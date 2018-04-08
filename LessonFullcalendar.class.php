<?php


/**
* LessonFullcalendar
* This creates an object ready for fullcalendar
*
* @author Andreas Fiehn 
*/
class LessonFullcalendar{
    
    /** @var array containing calendar event in Fullcalendar format*/
    private $calendarEvent;
    
    /** @var string */
    private $title;

    /** @var string */
    private $description;
    
    /** @var array */
    private $startEvent;
    
    /** @var array */
    private $endEvent;
    
    /** @var string */
    private $url;
    
    /** @var string */
    private $color;
    
    
    /**
    * Constructs the Event object ready for Fullcalendar
    * @param $lessonObject is the object from which the calendar event is created
    */
    function __construct($lessonObject){
        //Sets the calendar parameters
        $this->setTitle($lessonObject);
        $this->setStartEvent($lessonObject);
        $this->setDescription($lessonObject);
        $this->setEventColor($lessonObject);
        $this->setStartEvent($lessonObject);
        $this->setEndEvent($lessonObject);
        $this->setURL($lessonObject);
        
        //Creates the calendar event
        $this->setCalendarEvent();
    }
    
    
    /**
    * This gets the fullcalendar event
    *
    * @return array 
    */
    public function getCalendarEvent(){
        return $this->calendarEvent;
    }
    
    
    /**
    * This sets the calendarEvent property
    *
    */
    private function setCalendarEvent(){
        $this->calendarEvent = array(
            'title'=>$this->title,
            'allday'=>$this->startEvent['allDay'],
            'description'=>$this->description,
            'color'=>$this->color,
            'textColor'=>'#ffffff',
            'start'=>$this->startEvent['start'],
            'end'=>$this->endEvent['end'],
            'lessonURL'=>$this->url
        );
    }
    
    
    /**
    * Sets the lesson's summary
    * @param $lessonObject is the lesson object created from the Lesson class
    */
    private function setTitle($lessonObject){
        if($lessonObject->description && $lessonObject->class){
            if($lessonObject->status){
                $this->title = $lessonObject->status. "!\n". $lessonObject->description . "\n(".$lessonObject->class.")";
            }
            else{
                 $this->title = $lessonObject->description . "\n(".$lessonObject->class.")";
            }
        }
        else if($lessonObject->class){
            if($lessonObject->status){
                return $lessonObject->status. "!\n". $lessonObject->class;
            }
            else{
                $this->title = $lessonObject->class;
            }
        }
        else{
            if($lessonObject->status){
                $this->title = $lessonObject->status. "!\n". $lessonObject->description;
            }
            else{
                $this->title = $lessonObject->description;
            }
        }
       
        
    }
    
    /**
    * Set the lesson description
    * @param $lessonObject is the lesson object created from the Lesson class
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
        
        $this->description = $descriptionString;
    }
    
    /**
    * Set the start time and timezone of the lesson 
    * @param $lessonObject is the lesson object created from the Lesson class
    */
    private function setStartEvent($lessonObject){
        
        if($lessonObject->startTime){
            $this->startEvent = array(
                'start' => $lessonObject->date.'T'.$lessonObject->startTime,
                'allDay' => false
                );
        }
        else {
            $this->startEvent = array(
                'start' => $lessonObject->date,
                'allDay' => true
                );
        }
            
    }
    
    /**
    * Sets the start time and timezone of the lesson 
    * @param $lessonObject is the lesson object created from the Lesson class
    */
    private function setEndEvent($lessonObject){
        
         if($lessonObject->endTime){
            $this->endEvent = array(
                'end' => $lessonObject->date.'T'.$lessonObject->endTime,
                'allDay' => false
                );
        }
        else {
            $this->endEvent = array(
                'end' => $lessonObject->date,
                'allDay' => true
                );
        } 
    }
    
    
    /**
    * Sets the lesson URL
    * @param $lessonObject is the lesson object created from the Lesson class
    */
    private function setURL($lessonObject){
        $this->url = $lessonObject->lessonURL;
    }
    
    /**
    * Sets the event color
    * @param $lessonObject is the lesson object created from the Lesson class
    */
    private function setEventColor($lessonObject){
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
            $this->color = $colorsArray['green'];
        }
        else if($lessonObject->status == 'Aflyst'){
            $this->color = $colorsArray['red'];
        }
        else{

            if($lessonObject->startTime == NULL && $lessonObject->endTime == NULL){
                $this->color = $colorsArray['indigo'];
            }
            else if($lessonObject->class == "Matematik"){
                $this->color = $colorsArray['blue'];
            }
            else if($lessonObject->class == "Dansk"){
                $this->color = $colorsArray['orange'];
            }
            else if($lessonObject->class == "Fysik"){
                $this->color = $colorsArray['teal'];
            }
            else if($lessonObject->class == "Kemi"){
                $this->color = $colorsArray['light green'];
            }
            else if($lessonObject->class == "Samfundsfag"){
                $this->color = $colorsArray['gray'];
            }
            else if($lessonObject->class == "Informationsteknologi"){
                $this->color = $colorsArray['purple'];
            }
            else if($lessonObject->class == "Programmering"){
                $this->color = $colorsArray['purple'];
            }
            else if($lessonObject->class == "Teknik"){
                $this->color = $colorsArray['pink'];
            }
            else{
                $this->color = $colorsArray['blue gray'];
            }
        }
    }
}




?>