<?php
require_once __DIR__.'/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->addScope(Google_Service_Calendar::CALENDAR);

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
  
    $service = new Google_Service_Calendar($client);

    $calendarList = $service->calendarList->listCalendarList();
    
    while(true) {
    foreach ($calendarList->getItems() as $calendarListEntry) {
        
        if($calendarListEntry->getSummary()=="LectioSkema"){
            echo "LectioSkema already exists in Google Calendar";
        }
        break;
            
    }
        
    $pageToken = $calendarList->getNextPageToken();
        
    if ($pageToken) {
        $optParams = array('pageToken' => $pageToken);
        $calendarList = $service->calendarList->listCalendarList($optParams);
    } 
    else {
    echo "LectioSkema does not exist in Google Calendar";
    break;
  }
}
  
    
   

    
} else {
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/LectioScrape/oauth2callback.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}