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
        'color'=>$this->getEventColor($lessonObject),
        'textColor'=>'#ffffff',
        'start'=>$this->setStartEvent($lessonObject)['start'],
        'end'=>$this->setEndEvent($lessonObject)['end'],
        'lessonURL'=>$this->setURL($lessonObject),
        );
    }
    
    
    /**
    * Returns the lesson's summary
    * @param $lessonObject is the lesson object created from the Lesson class
    * @return string
    */
    private function setTitle($lessonObject){
        if($lessonObject->description && $lessonObject->class){
            if($lessonObject->status){
                return $lessonObject->status. "!\n". $lessonObject->description . "\n(".$lessonObject->class.")";
            }
            else{
                 return $lessonObject->description . "\n(".$lessonObject->class.")";
            }
        }
        else if($lessonObject->class){
            if($lessonObject->status){
                return $lessonObject->status. "!\n". $lessonObject->class;
            }
            else{
                return $lessonObject->class;
            }
        }
        else{
            if($lessonObject->status){
                return $lessonObject->status. "!\n". $lessonObject->description;
            }
            else{
                return $lessonObject->description;
            }
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
    
    
    /**
    * Returns the lesson URL
    * @param $lessonObject is the lesson object created from the Lesson class
    * @return string
    */
    private function setURL($lessonObject){
        return $lessonObject->lessonURL;
    }
    
    /**
    * Returns the event color
    * @param $lessonObject is the lesson object created from the Lesson class
    * @return string
    */
    private function getEventColor($lessonObject){
        $colorsArray = array(
            'red'=>'#d32f2f',
            'pink'=>'#c2185b',
            'purple'=>'#7b1fa2',
            'deep purple'=>'#512da8',
            'indigo'=>'#303f9f',
            'blue'=>'#1976d2',
            'light blue'=>'#0288d1',
            'cyan'=>'#0097a7',
            'teal'=>'#00796b',
            'green'=>'#388e3c',
            'light green'=>'#689f38',
            'lime'=>'#afb42b',
            'yellow'=>'#fbc02d',
            'amber'=>'#ffa000',
            'orange'=>'#f57c00',
            'deep orange'=>'#e64a19',
            'brown'=>'#5d4037',
            'gray'=>'#616161',
            'blue gray'=>'#455a64'
        );
        
        
         if($lessonObject->status == 'Ændret'){
            return $colorsArray['green'];
        }
        else if($lessonObject->status == 'Aflyst'){
            return $colorsArray['red'];
        }
        else{

            if($lessonObject->startTime == NULL && $lessonObject->endTime == NULL){
                return $colorsArray['indigo'];
            }
            else if($lessonObject->class == "Matematik"){
                return $colorsArray['blue'];
            }
            else if($lessonObject->class == "Dansk"){
                return $colorsArray['orange'];
            }
            else if($lessonObject->class == "Fysik"){
                return $colorsArray['teal'];
            }
            else if($lessonObject->class == "Kemi"){
                return $colorsArray['light green'];
            }
            else if($lessonObject->class == "Samfundsfag"){
                return $colorsArray['gray'];
            }
            else if($lessonObject->class == "Informationsteknologi"){
                return $colorsArray['purple'];
            }
            else if($lessonObject->class == "Programmering"){
                return $colorsArray['purple'];
            }
            else if($lessonObject->class == "Teknik"){
                return $colorsArray['pink'];
            }
            else{
                return $colorsArray['blue gray'];
            }
        }
        
        
        
        return '#d32f2f';
    }
    
    
    
    
}




?>