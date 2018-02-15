<?php
require_once __DIR__.'/vendor/autoload.php';

session_start();

//Creates a new Google Client object
$client = new Google_Client();

//Sets the client authentification code from a json file
$client->setAuthConfig('client_secret.json');

//Adds the Google calendar scope, that i want to acces in the API
$client->addScope(Google_Service_Calendar::CALENDAR);

//Checks if the user's access token is stored in the session
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    
    //The access token is set in the client object
    $client->setAccessToken($_SESSION['access_token']);
  
    //Creates a Google_Service_Calendar object from the client object
    $service = new Google_Service_Calendar($client);

    //Gets a calendar list
    $calendarList = $service->calendarList->listCalendarList();
    
    //Loops until a break occurs
    while(true) {
        foreach ($calendarList->getItems() as $calendarListEntry) {

            //If the calendarListEntry
            if($calendarListEntry->getSummary()=="LectioSkema"){
                echo "LectioSkema already exists in Google Calendar";
            }

            break;

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
            echo "LectioSkema does not exist in Google Calendar";
        break;
      }
    }  
} 
else {
    //If no auth token is stored in the SESSION variable, the browser gets redirected to oauth2callback.php
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/LectioScrape/oauth2callback.php';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}