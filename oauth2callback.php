<?php
//Includes the google API
require_once __DIR__.'/vendor/autoload.php';

//Starts the session
session_start();

//Creates a new client object
$client = new Google_Client();
//Sets the Auth config file, which includes the client's secret key
$client->setAuthConfigFile('client_secret.json');
//Sets the redirect URI
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/LectioScrape/oauth2callback.php');
//Adds the scope. I need access to the calendar
$client->addScope(Google_Service_Calendar::CALENDAR);


//If the $_GET variable does not contain a code from Google the browser is redirected to a login page where they can grant access
if (! isset($_GET['code'])) {
    //The authentification URL is created
    $auth_url = $client->createAuthUrl();
    //The browser is redirected
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} 
else {
    //If $_GET['code'] exists, an access token is created and stored in the session
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    
    //The user gets redirected to the uploadToGoogleCal script
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/LectioScrape/uploadToGoogleCal.php';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}