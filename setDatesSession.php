<?php
/*This takes the start and end date passe with GET and saves it in the session*/

//Starts the session
session_start();

//Sets the startDate and endDate in session
$_SESSION['startDate'] = $_GET['startDate']; 
$_SESSION['endDate'] = $_GET['endDate'];

//Redirects to uploadToGoogleCal.php
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/LectioScrape/uploadToGoogleCal.php';
header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));

?>