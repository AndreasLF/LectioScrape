<?php
session_start();

//Requires the Google Client Library
require_once __DIR__.'/vendor/autoload.php';

//Includes classes
require_once __DIR__.'/Lesson.class.php';
require_once __DIR__.'/LessonGoogleCalEvent.class.php';
require_once __DIR__.'/LectioScrape.class.php';



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

            //If the calendarListEntry is LectioSkema
            if($calendarListEntry->getSummary()=="LectioSkema"){
                echo "LectioSkema already exists in Google Calendar <br>";
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
        
        echo "LectioSkema created successfully";
    }
    
    
} 
else {
    //If no auth token is stored in the SESSION variable, the browser gets redirected to oauth2callback.php
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/LectioScrape/oauth2callback.php';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

//Deletes the events from the week
deleteEvents($_SESSION['startDate'],$_SESSION['endDate'],$client,$service,$calendarId);

//New LectioScrape object is created
$schedule = new LectioScrape($_SESSION['startDate'].'T10:50:31');

//The schedule is sent to Google Cal
sendToGoogleCal($schedule->scheduleGoogle,$client,$service,$calendarId);

//Destroys the session
session_destroy();

$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/LectioScrape/index.html';
header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));








/**
* Sends the schedule to Google calendar
* 
* @param $scheduleList is a list of objects created from the LessonGoogleCalEvent class
* @param $client Google_Client object
* @param $service Google_Service_Calendar object
* @param $calendarId Google calendarId
*/
function sendToGoogleCal($scheduleList,$client,$service,$calendarId){
    //Batch setup
    $client->setUseBatch(true);
    $batch = new Google_Http_Batch($client);
    
    foreach($scheduleList as $eventParams) {
        //Creates new event
        $event = new Google_Service_Calendar_Event($eventParams);
        
        //Inserts the event to the calendar
        $event = $service->events->insert($calendarId, $event);
        
        //Adds to the batch
        $batch->add($event);
    }
    
    //Executes batch
    $batch->execute();
}


/**
* Delete every event in the calendar between (including) $startDate and $endDate
*
* @param string $startDate is the start date in the following format: 'YYYY-MM-DD'
* @param string $endDate is the end date in the following format: 'YYYY-MM-DD'
* @param $client Google_Client object
* @param $service Google_Service_Calendar object
* @param $calendarId Google calendarId
*/
function deleteEvents($startDate,$endDate,$client,$service,$calendarId){
    
    //Specify minimum and maximum time to search for
    $timeMin = $startDate . 'T00:00:00+01:00';
    $timeMax = $endDate . 'T00:00:00+01:00';
    
    //List of items
    $eventItemsList;
    
    
    //Saves the timeMin and timeMax parameters in an array
    $optParams = array('timeMin' => $timeMin, 'timeMax'=>$timeMax);
    
    //creates the events list
    $events = $service->events->listEvents($calendarId,$optParams);

    //Add items to the list
    $eventItemsList[] = $events->getItems();
    
    //If more calendarList pages exist they will be added to the eventsItemsList array
    //Loops until a break occurs
    while(true){
        //Gets the next page token
        $pageToken = $events->getNextPageToken();
        
        if ($pageToken) {
            
            //The page token is added to the optParams
            $optParams = array('pageToken' => $pageToken,'timeMin' => $timeMin, 'timeMax'=>$timeMax);

            //A new list of events is created
            $events = $service->events->listEvents($calendarId, $optParams);
            
            //Add items to the list
            $eventItemsList[] = $events->getItems();
            
        } 
        else {
            //Break free from while loop if no more pages exist 
            break;
        }
    }
    
    //Batch setup
    $client->setUseBatch(true);
    $batch = new Google_Http_Batch($client);
    
        
    //Deletes every event in the events list
   
   foreach($eventItemsList as $eventItems){
        foreach ($eventItems as $event) {
            //Gets the event id
            $eventId = $event->getId();
            //Deletes the event
            $eventDeletion = $service->events->delete($calendarId, $eventId);
            
            //Add event deletion to the batch
            $batch->add($eventDeletion);
        }
   }
    
    //Batch execution    
    $batch->execute();

}






?>
