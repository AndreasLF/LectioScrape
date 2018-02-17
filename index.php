<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/Lesson.class.php';
require_once __DIR__.'/ScheduleList.class.php';
require_once __DIR__.'/LessonGoogleCalEvent.class.php';
//require_once __DIR__.'/scrape.php';

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
    
    
    
    
    sendToGoogleCal($_SESSION['scheduleGoogle']);
    
    
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

function sendToGoogleCal($scheduleList){
    foreach($scheduleList->eventParams as $eventParams) {
        //Creates new event
        $event = new Google_Service_Calendar_Event($scheduleEvent->eventParams);


        //Inserts the event to the calendar
        $event = $service->events->insert($calendarId, $event);

        if($event){
            echo 'event created successfully';
        }
    }
}


?>