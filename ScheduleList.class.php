<?php
/**
* ScheduleList
* This creates an object containing a list of lessons in a week
*
* @author Andreas Fiehn
*/
class ScheduleList{
    public $weekNumber;
    public $scheduleList; 
    
    /**
    * This constructs the object by setting the $weekNumber and declaring $scheduleList an array
    * @param Int $weekNo is the week number
    */
    function __construct($weekNo){
        $this->weekNumber = $weekNo;
        $this->scheduleList = array();
    }
    
    /**
    * This adds a lesson to the list of lessons
    * @param Object $scheduleObject is the lesson object that is passed to the array.
    */
    public function addLesson($scheduleObject){
        $this->scheduleList[] = $scheduleObject;
    }
}
?>