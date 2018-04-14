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
    private $eventParams;
    
    /** @var string $sumamry */
    private $summary;
    /** @var string $description */
    private $description;
    /** @var array $startEvent */
    private $startEvent;
    /** @var array $endEvent */
    private $endEvent;
    /** @var string $colorId */
    private $colorId;
    
    
    /**
    * Constructs the Google event object
    * @param $lessonObject is the object from which the calendar event is created from
    */
    function __construct($lessonObject){
        //sets calendar parameters
        $this->setSummary($lessonObject);
        $this->setDescription($lessonObject);
        $this->setStartEvent($lessonObject);
        $this->setEndEvent($lessonObject);
        $this->setColorId($lessonObject);
            
        //Creates the eventParams array
        $this->setEventParams(); 
    }
    
    
    /**
    * This gets the google calendar event parameters
    *
    * @return array
    */
    public function getEventParams(){
        return $this->eventParams;
    }
    
    /**
    * Sets the google calendar event parameters 
    */
    private function setEventParams(){
        $this->eventParams = array(
        'summary' => $this->summary,
        'description' => $this->description,
        'start' => $this->startEvent,
        'end' => $this->endEvent,
        'colorId' => $this->colorId
        );
    }
    
    /**
    * Returns the lesson's summary
    * @param $lessonObject is the lesson object created from the Lesson class
    * @return string
    */
    private function setSummary($lessonObject){
        //If lessonObject contains class
        if($lessonObject->class){
            //If a status exists
            if($lessonObject->status){
                $this->summary = $lessonObject->status . "! " . $lessonObject->class;
            }
            else{
                $this->summary = $lessonObject->class;  
            }  
        }
        else{
            if($lessonObject->status){
                $this->summary = $lessonObject->status . "! " . $lessonObject->description;
            }
            else{
                $this->summary = $lessonObject->description;  
            } 
        }
       
        
    }
    
    /**
    * Returns the lesson description
    * @param $lessonObject is the lesson object created from the Lesson class
    * @return string
    */
    private function setDescription($lessonObject){
        //Creates description array
        $descriptionArray = array();
        
        //If a lessonObject property exists it wil be added to the array
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
        
        //Description string is defined
        $descriptionString = "";
    
        //For each element in description array, the description is added to the string
        foreach($descriptionArray as $desc){
            $descriptionString = $descriptionString . $desc . "<br>";
            
        }
        
        //Sets the description property
        $this->description = $descriptionString;
    }
    
    /**
    * Returns the start time and timezone of the lesson 
    * @param $lessonObject is the lesson object created from the Lesson class
    * @return array
    */
    private function setStartEvent($lessonObject){
        //If lessonObject does not have a startTime the event will last all day
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
    
     /**
    *  Returns the start time and timezone of the lesson 
    * @param $lessonObject is the lesson object created from the Lesson class
    * @return string
    */
    private function setEndEvent($lessonObject){
        //If lessonObject does not have an endTime the event will last all day
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
    
    /**
    * Returns the color id for the event 
    * @param $lessonObject is the lesson object created from the Lesson class
    * @return string
    */
    private function setColorId($lessonObject){
        //Defines the colors in an array (google calendar color id)
        $colorsArray = array(
        'blue' => '1',
        'green'=>'2',
        'purple'=>'3',
        'red'=>'4',
        'yellow'=>'5',
        'orange' => '6',
        'turquoise'=>'7',
        'gray'=>'8',
        'bold blue'=>'9',
        'bold green'=>'10',
        'bold red' => '11',
        );
        
        //Checks for information and sets the color corresponding to the information
        
        if($lessonObject->status == 'Ændret'){
            $this->colorId = $colorsArray['bold green'];
        }
        else if($lessonObject->status == 'Aflyst'){
            $this->colorId = $colorsArray['bold red'];

        }
        else{
           
            if($lessonObject->startTime == NULL && $lessonObject->endTime == NULL){
                $this->colorId = $colorsArray['bold blue'];
            }
            else if($lessonObject->class == "Matematik"){
                $this->colorId = $colorsArray['turquoise'];
            }
            else if($lessonObject->class == "Dansk"){
                $this->colorId = $colorsArray['orange'];
            }
            else if($lessonObject->class == "Fysik"){
                $this->colorId = $colorsArray['red'];
            }
            else if($lessonObject->class == "Kemi"){
                $this->colorId = $colorsArray['yellow'];
            }
            else if($lessonObject->class == "Samfundsfag"){
                $this->colorId = $colorsArray['gray'];
            }
            else if($lessonObject->class == "Informationsteknologi"){
                $this->colorId = $colorsArray['purple'];
            }
            else if($lessonObject->class == "Programmering"){
                $this->colorId = $colorsArray['purple'];
            }
            else if($lessonObject->class == "Teknik"){
                $this->colorId = $colorsArray['green'];
            }
            else{
                $this->colorId = $colorsArray['blue'];
            }
      
        }
     
    }
    
}




?>