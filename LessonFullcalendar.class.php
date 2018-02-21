<?php


/**
* LessonFullcalendar
* This creates an object ready for fullcalendar
*
* @author Andreas Fiehn 
*/
class LessonFullcalendar{
    
    public $calendarEvent;
    
    
    /**
    * Constructs the Event object ready for Fullcalendar
    * @param $lessonObject is the object from which the calendar event is created from
    */
    function __construct($lessonObject){
        $this->calendarEvent = array(
        'title'=>$this->setTitle($lessonObject),
        'allday'=>$this->setStartEvent($lessonObject)['allDay'],
        'description'=>$this->setDescription($lessonObject),
        'borderColor'=>'#429ef4',
        'color'=>'#429ef4',
        'textColor'=>'#000000',
        'start'=>$this->setStartEvent($lessonObject)['start'],
        'end'=>$this->setEndEvent($lessonObject)['end']
        );
    }
    
    
    /**
    * Returns the lesson's summary
    * @param $lessonObject is the lesson object created from the Lesson class
    * @return string
    */
    private function setTitle($lessonObject){
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
    * @return string
    */
    private function setStartEvent($lessonObject){
        
        if($lessonObject->startTime){
            return array(
                'start' => $lessonObject->date.'T'.$lessonObject->startTime,
                'allDay' => false
                );
        }
        else {
            return array(
                'start' => $lessonObject->date,
                'allDay' => true
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
                'end' => $lessonObject->date.'T'.$lessonObject->endTime,
                'allDay' => false
                );
        }
        else {
            return array(
                'end' => $lessonObject->date,
                'allDay' => true
                );
        } 
    }
    
    
}




?>