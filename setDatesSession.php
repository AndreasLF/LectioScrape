<?php

session_start();

$_SESSION['startDate'] = $_GET['startDate']; 
$_SESSION['endDate'] = $_GET['endDate'];


$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/LectioScrape/uploadToGoogleCal.php';
header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));

?>