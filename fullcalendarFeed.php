<?php
require_once __DIR__.'/LectioScrape.class.php';

$eventList = array();

$end = $_GET['end'];
$lectioSchedule = new LectioScrape($end);

$eventArray;


foreach($lectioSchedule->scheduleFullcalendar as $event){
    
    $eventArray[] = $event->calendarEvent;
    
}




header('Content-Type: application/json');
//header('Content-Type: application/json');
echo json_encode($eventArray);

?>