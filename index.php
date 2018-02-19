<?php
session_start();


require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/Lesson.class.php';
require_once __DIR__.'/ScheduleList.class.php';
require_once __DIR__.'/LessonGoogleCalEvent.class.php';
require_once __DIR__.'/scrape.php';



//Creates a new Google Client object
$client = new Google_Client();

//Sets the client authentification code from a json file
$client->setAuthConfig('client_secret.json');

//Adds the Google calendar scope, that i want to acces in the API
$client->addScope(Google_Service_Calendar::CALENDAR);

$lectioCalendarExists = false;
$calendarId;

//$weekArr = getWeekStartAndEndDate(2018,10);
//echo $weekArr['weekStart'] . "<br>" . $weekArr['weekEnd'];

echo getWeekNumberFromDate('2018-02-19T00:00:00');

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
    }
    
    
 

    
    
    
    //deleteWeek($service,$calendarId);
    //scrapeLectio('102018');
    //sendToGoogleCal($_SESSION['scheduleGoogle'],$service,$calendarId);
    //var_dump($_SESSION['scheduleGoogle']->scheduleList);
    
    
} 
else {
    //If no auth token is stored in the SESSION variable, the browser gets redirected to oauth2callback.php
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/LectioScrape/oauth2callback.php';
    //header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}




/**
* Sends the schedule to Google calendar
* 
* @param $scheduleList is an object created from the LessonGoogleCalEvent class
* @param $service Google_Service_Calendar object
* @param $calendarId Google calendarId
*/
function sendToGoogleCal($scheduleList,$service,$calendarId){
    foreach($scheduleList->scheduleList as $list) {
        //Creates new event
        $event = new Google_Service_Calendar_Event($list->eventParams);


        //Inserts the event to the calendar
        $event = $service->events->insert($calendarId, $event);

        if($event){
            echo 'event created successfully<br>';
        }
    }
}


/**
* Delete every event in the calendar between (including) $startDate and $endDate
*
* @param string $startDate is the start date in the following format: 'YYYY-MM-DD'
* @param string $endDate is the end date in the following format: 'YYYY-MM-DD'
* @param $service Google_Service_Calendar object
* @param $calendarId Google calendarId
*/
function deleteEvents($startDate,$endDate,$service,$calendarId){
    //Specify minimum and maximum time to search for
    $timeMin = $startDate . 'T00:00:00+01:00';
    $timeMax = $endDate . 'T23:59:59+01:00';
    
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


/**
* Gets the start and end date by specifying year and week number
*
* @param int $year is the year
* @param int $weekNumber is the weeknumber
* 
* @return array containing weekStart and weekEnd 
*/

function getWeekStartAndEndDate($year,$weekNumber){
    
    //Creates a new dateTime object
    $dateTimeObject = new DateTime();
    
    //Sets the date by using the ISO 8601 standard, specifying year and week number
    $dateTimeObject->setISODate($year,$weekNumber);
    
    //Saves start date in an array
    $dateRangeArray['weekStart'] = $dateTimeObject->format('Y-m-d');
    
    //Add 6 days to the dateTime object
    $dateTimeObject->modify('+6 days');
    
    //Saves the new date in an array as the end date
     $dateRangeArray['weekEnd'] = $dateTimeObject->format('Y-m-d');
    
    return $dateRangeArray;
}



/**
* Gets the week number by specifying a date in the week
*
* @param string $date is the date
*
* @return string week number
*/
function getWeekNumberFromDate($date){
    
    $dateTimeObject = new DateTime($date);
    $weekNumber = $dateTimeObject->format("W");
    return $weekNumber; 
}



?>