<?php
require_once __DIR__.'/LectioScrape.class.php';

$eventList = array();

$start = $_GET['start'];
$lectioSchedule = new LectioScrape($start);

$eventArray;


foreach($lectioSchedule->scheduleFullcalendar as $event){
    
    $eventArray[] = $event->calendarEvent;
    
}




header('Content-Type: application/json');
//header('Content-Type: application/json');
echo json_encode($eventArray);

?>