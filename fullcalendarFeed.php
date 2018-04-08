<?php
/*This script serves a Full calendar json feed*/

//Includes the LectioScrape class if not already included
require_once __DIR__.'/LectioScrape.class.php';


$eventList = array();

//Gets the start date passed with GET
$start = $_GET['start'];

//Creates a new LectioScrape object
$lectioSchedule = new LectioScrape($start);

header('Content-Type: application/json');
//header('Content-Type: application/json');
echo json_encode($lectioSchedule->scheduleFullcalendar);

?>