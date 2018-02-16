<?php


/**
* LessonGoogleCalEvent
* This creates an object readable by the Google Calendar API from the Lesson object
*
* @author Andreas Fiehn 
*/
class LessonGoogleCalEvent{
    /** @var array $eventParams */
    //These are the Google Calendar event parameters, which can be passed to the Google Calendar API
    public $eventParams;
    
    /**
    * Constructs the Google event object
    * @param $lessonObject is the object from which the calendar event is created from
    */
    function __construct($lessonObject){
        $this->eventParams = array(
        'summary' => $this->setSummary($lessonObject),
        'description' => $this->setDescription($lessonObject),
        'start' => $this->setStartEvent($lessonObject),
        'end' => $this->setEndEvent($lessonObject)
        );
    }
    
    
    /**
    * Returns the lesson's summary
    * @param $lessonObject is the lesson object created from the Lesson class
    * @return string
    */
    private function setSummary($lessonObject){
        if($lessonObject->class){
            return $lessonObject->class;     
        }
        else{
            return $lessonObject->description;
        }
       
        
    }
    
    /**
    * Returns the lesson description
    * @param $lessonObject is the lesson object created from the Lesson class
    * @return string
    */
    private function setDescription($lessonObject){
        
        $descriptionArray = array();
        
        
        if($lessonObject->status){
            $descriptionArray[] = $lessonObject->status;
        }
        
        if($lessonObject->description){
            $descriptionArray[] = $lessonObject->description;
        }
        
        if($lessonObject->teacher){
            $descriptionArray[] = "Lærer: " . $lessonObject->teacher;
        }
        
        if($lessonObject->room){
            $descriptionArray[] = "Lokale: " . $lessonObject->room;
        }
        
        if($lessonObject->homework){
            $descriptionArray[] = "Lektier: " . $lessonObject->homework;
        }
        
        if($lessonObject->note){
            $descriptionArray[] = "Note: " . $lessonObject->note;
        }
        
        
        $descriptionString = "";
    
        foreach($descriptionArray as $desc){
            $descriptionString = $descriptionString . $desc . "<br>";
            
        }
        
        return $descriptionString;
    }
    
    /**
    * Returns the start time and timezone of the lesson 
    * @param $lessonObject is the lesson object created from the Lesson class
    * @return array
    */
    private function setStartEvent($lessonObject){
        
        if($lessonObject->startTime){
            return array(
            'dateTime' => $lessonObject->date.'T'.$lessonObject->startTime,
            'timeZone' => 'Europe/Copenhagen',
            );
        }
        else {
            return array(
            'date' => $lessonObject->date,
            'timeZone' => 'Europe/Copenhagen',
            );
        }
            
    }
    
     /**
    *  Returns the start time and timezone of the lesson 
    * @param $lessonObject is the lesson object created from the Lesson class
    * @return string
    */
    private function setEndEvent($lessonObject){
        
         if($lessonObject->endTime){
            return array(
            'dateTime' => $lessonObject->date.'T'.$lessonObject->endTime,
            'timeZone' => 'Europe/Copenhagen',
            );
        }
        else {
            return array(
            'date' => $lessonObject->date,
            'timeZone' => 'Europe/Copenhagen',
            );
        } 
    }
    
    
}




?>