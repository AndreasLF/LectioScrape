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
        'end'=>$this->setEndEvent($lessonObject)['end'],
        'lessonURL'=>$this->setURL($lessonObject)
        );
    }
    
    
    /**
    * Returns the lesson's summary
    * @param $lessonObject is the lesson object created from the Lesson class
    * @return string
    */
    private function setTitle($lessonObject){
        if($lessonObject->description){
            if($lessonObject->class){             
                return $lessonObject->description . "\n(".$lessonObject->class.")";
            }
            return $lessonObject->description;
            
        }
        else{
            return $lessonObject->class;
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
            $descriptionArray[] = "<b>Lærer: </b>" . $lessonObject->teacher;
        }
        
        if($lessonObject->room){
            $descriptionArray[] = "<b>Lokale: </b>" . $lessonObject->room;
        }
        
        if($lessonObject->homework){
            $descriptionArray[] = "<b>Lektier: </b>" . $lessonObject->homework;
        }
        
         if($lessonObject->additionalContent){
            $descriptionArray[] = "<b>Øvrigt indhold: </b>" . $lessonObject->additionalContent;
        }
        
        if($lessonObject->note){
            $descriptionArray[] = "<b>Note: </b>" . $lessonObject->note;
        }
        
        if($lessonObject->students){
            $descriptionArray[] = "<b>Elever: </b>" . $lessonObject->students;
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
    
    private function setURL($lessonObject){
        return $lessonObject->lessonURL;
    }
    
    
}




?>