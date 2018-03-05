<?php
//Require the LectioScrape class
require_once __DIR__.'/LectioScrape.class.php';

//Creates a new LectioScrape object with the date passed from $_GET as input parameter
$schedule = new LectioScrape($_GET['startDate'].'T01:00:00');

//Sends the schedule to the database
$schedule->sendToDatabase();

//Redirects to the front page
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/LectioScrape/index.html';
header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));

?>