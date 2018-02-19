<?php
$eventList = array();


$start = $_GET['start'];
$end = $_GET['end'];

$eventList[] =   array(
    'title'=>$end,
    'allday'=>false,
    'borderColor'=>'#429ef4',
    'color'=>'#429ef4',
    'textColor'=>'#000000',
    'start'=>'2018-02-19T08:15:00',
    'end'=>'2018-02-19T09:15:00');

$eventList[] =   array(
    'title'=>'TEST2',
    'allday'=>false,
    'borderColor'=>'#f20966',
    'color'=>'#f20966',
    'textColor'=>'#000000',
    'start'=>'2018-02-19T09:20:00',
    'end'=>'2018-02-19T10:20:00');

$eventList[] =   array(
    'title'=>'TEST3',
    'allday'=>true,
    'borderColor'=>'#f20966',
    'color'=>'#f20966',
    'textColor'=>'#000000',
    'start'=>'2018-02-19');

header('Content-Type: application/json');
//header('Content-Type: application/json');
echo json_encode($eventList);
?>