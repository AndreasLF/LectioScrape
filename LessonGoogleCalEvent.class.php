<?php

class LessonGoogleCalEvent{
    public $summary;
    public $description;
    public $startEvent;
    public $endEvent;
    
    
    function __construct($lessonObject){
        $this->setSummary($lessonObject);
        $this->setDescription($lessonObject);
        $this->setStartEvent($lessonObject);
        $this->setEndEvent($lessonObject);
    }
    
    
    private function setSummary($lessonObject){
        if($lessonObject->class){
            $this->summary = $lessonObject->class;     
        }
        else{
            $this->summary = $lessonObject->description;
        }
       
        
    }
    
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
        
        
        $descriptionString;
    
        foreach($descriptionArray as $desc){
            $description = $desc . "<br>";
            
        }
        
        $this->description = $description;
    }
    
    private function setStartEvent($lessonObject){
        
        if($lessonObject->startTime){
            $this->startEvent = array(
            'dateTime' => $lessonObject->date.'T'.$lessonObject->startTime,
            'timeZone' => 'Europe/Copenhagen',
            );
        }
        else {
            $this->startEvent = array(
            'date' => $lessonObject->date,
            'timeZone' => 'Europe/Copenhagen',
            );
        }
            
    }
    
    private function setEndEvent($lessonObject){
        
         if($lessonObject->endTime){
            $this->endEvent = array(
            'dateTime' => $lessonObject->date.'T'.$lessonObject->endTime,
            'timeZone' => 'Europe/Copenhagen',
            );
        }
        else {
            $this->endEvent = array(
            'date' => $lessonObject->date,
            'timeZone' => 'Europe/Copenhagen',
            );
        } 
    }
    
    
}




?>