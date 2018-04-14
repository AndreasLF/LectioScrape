<?php
/*This script serves a Full calendar json feed*/

//Includes the LectioScrape class if not already included
require_once __DIR__.'/LectioScrape.class.php';

//Gets the start date passed with GET
$start = $_GET['start'];

//Creates a new LectioScrape object
$lectioSchedule = new LectioScrape($start);

//Sets the content type
header('Content-Type: application/json');

//Echoes the scheduleFullcalendar array as a JSON string
echo json_encode($lectioSchedule->scheduleFullcalendar);

?>