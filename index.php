<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/Lesson.class.php';
require_once __DIR__.'/ScheduleList.class.php';
require_once __DIR__.'/LessonGoogleCalEvent.class.php';
require_once __DIR__.'/scrape.php';

session_start();

//Creates a new Google Client object
$client = new Google_Client();

//Sets the client authentification code from a json file
$client->setAuthConfig('client_secret.json');

//Adds the Google calendar scope, that i want to acces in the API
$client->addScope(Google_Service_Calendar::CALENDAR);

$lectioCalendarExists = false;
$calendarId;

//Checks if the user's access token is stored in the session
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    
    //The access token is set in the client object
    $client->setAccessToken($_SESSION['access_token']);
  
    //Creates a Google_Service_Calendar object from the client object
    $service = new Google_Service_Calendar($client);

    //Gets a calendar list
    $calendarList = $service->calendarList->listCalendarList();
    
    //Checks if a calendar named 'LectioSkema' exists
    //Loops until a break occurs
    while(true) {
        foreach ($calendarList->getItems() as $calendarListEntry) {

            //If the calendarListEntry
            if($calendarListEntry->getSummary()=="LectioSkema"){
                echo "LectioSkema already exists in Google Calendar";
                $calendarId = $calendarListEntry->getId();
                $lectioCalendarExists = true;
            }
            
        }

        //Gets the next page token
        $pageToken = $calendarList->getNextPageToken();

        //If more calendarList pages exist ($pageToken == true)
        if ($pageToken) {
            //An optional parameter is set containing the page token
            $optParams = array('pageToken' => $pageToken);

            //A new calendarList is created with $optParams as input parameter
            //$optParams contains the next page token
            $calendarList = $service->calendarList->listCalendarList($optParams);
        } 
        else {
            break;
        }
    }  
    
    
    //If the calendar does not exist
    if(!$lectioCalendarExists){
        //Creates a new calendar objects
        $calendar = new Google_Service_Calendar_Calendar(); 
        
        //sets the calendar's summary
        $calendar->setSummary('LectioSkema');
        //Sets the calendar's time zone
        $calendar->setTimeZone('Europe/Copenhagen');

        //Creates the calendar
        $createdCalendar = $service->calendars->insert($calendar);

        //Echoes the calendar id for the new calendar
        $calendarId = $createdCalendar->getId();
    }
    
    
 

    
    deleteWeek($service,$calendarId);
    //scrapeLectio('102018');
    //sendToGoogleCal($_SESSION['scheduleGoogle'],$service,$calendarId);
    //var_dump($_SESSION['scheduleGoogle']->scheduleList);
    
    
} 
else {
    //If no auth token is stored in the SESSION variable, the browser gets redirected to oauth2callback.php
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/LectioScrape/oauth2callback.php';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}



/**
* Sends the schedule to Google calendar
* @param $scheduleList is an object created from the LessonGoogleCalEvent class
*/
function sendToGoogleCal($scheduleList,$service,$calendarId){
    foreach($scheduleList->scheduleList as $list) {
        //Creates new event
        $event = new Google_Service_Calendar_Event($list->eventParams);


        //Inserts the event to the calendar
        $event = $service->events->insert($calendarId, $event);

        if($event){
            echo 'event created successfully';
        }
    }
}


/**
* Delete every event in the calendar for a specified week
* @param $service Google_Service_Calendar object
* @param $calendarId Google calendarId
*/
function deleteWeek($service,$calendarId){
    //Specify minimum and maximum time to search for
    $timeMin = '2018-03-03T00:00:00+01:00';
    $timeMax = '2018-03-11T23:59:00+01:00';
    
    //Saves the timeMin and timeMax parameters in an array
    $optParams = array('timeMin' => $timeMin, 'timeMax'=>$timeMax);
    
    //creates the events list
    $events = $service->events->listEvents($calendarId,$optParams);
    
    //Deletes every event in the events list
    //Loops until a break occurs
    while(true) {
        foreach ($events->getItems() as $event) {
            //Gets the event id
            $eventId = $event->getId();
            //Deletes the event
            $service->events->delete($calendarId, $eventId);
        }
        
        //Gets the next page token
        $pageToken = $events->getNextPageToken();
        
        //If more calendarList pages exist ($pageToken == true)
        if ($pageToken) {
            
            //The page token is added to the optParams
            $optParams = array('pageToken' => $pageToken,'timeMin' => $timeMin, 'timeMax'=>$timeMax);
            
            //A new list of events is created
            $events = $service->events->listEvents($calendarId, $optParams);
        } 
        else {
            //Break free from while loop if no more pages exist 
            break;
        }
        
    }
}


?>