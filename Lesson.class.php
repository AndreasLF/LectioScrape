<?php
/**
* Lesson
* Creates a lesson object containing information about the lesson
*
* @author Andreas Fiehn
*/
class Lesson{
    public $lessonURL;
    
    public $status;
    public $description;
    public $date;
    public $startTime;
    public $endTime;
    public $class;
    public $teacher;
    public $room;
    public $homework;
    public $additionalContent;
    public $note;
    public $students;
    
    
    /** 
    * This method constructs the object from the lesson class
    * @param String $data contains the lesson data as a string. The lesson data is fetched from Lectio and is the content of the data-additionalinfo class.
    * @param String $url is the lessons web page url
    */
    function __construct($data,$url){
        
     
        $this->lessonURL = $url;
            
        //Sets every property by refering to functions
        $this->setStatusAndDescription($data);
        $this->setDate($data);
        $this->setTime($data);
        $this->setClass($data);
        $this->setTeacher($data);
        $this->setRoom($data);
        $this->setHomework($data);
        $this->setAdditionalContent($data);
        $this->setNote($data);
        $this->setStudents($data);
    }
    
    
    /**
    * This method sets the lesson's status and description, by performing regular expressions
    * @param String $data contains the lesson data as a string. The lesson data is fetched from Lectio and is the content of the data-additionalinfo class.
    */
    private function setStatusAndDescription($data){
        //Checks if the lesson has been changed by performing a regex
        if(preg_match('/^Ændret!/',$data)){
             
            $this->status = 'Ændret';
            
            //If there is a string between "Ændret!" and the date. Here the class descritption will show up if there is any
            if(preg_match('/^Ændret!\s(.*?)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $matches)){
                
                $this->description = $matches[1];
             }
            
        }
        //Checks if the lesson has been cancelled by performing a regex
        else if(preg_match('/^Aflyst!/', $data)){
            $this->status = 'Aflyst';
            
            //If there is a string between "Aflyst!" and the date. Here the class descritption will show up if there is any.
            if(preg_match('/^Aflyst!\s(.*?)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $matches)){
            
                $this->description = $matches[1];
            }
        }
        //Else if there is a string before the date. Here the class description will show up if there is any.
        else if(preg_match('/(.*?)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $matches)){
            
            $this->status = NULL;
            $this->description = $matches[1];
        }
        else{
            $this->description = NULL; 
        }
    }
    
    
    /**
    * By performing a regex the date of the lesson is retrieved and and saved.
    * @param String $data contains the lesson data as a string. The lesson data is fetched from Lectio and is the content of the data-additionalinfo class.
    */
    private function setDate($data){
        //Perform a regex on $data. Searches for a date pattern
        if(preg_match('/(\d\d|\d)\/(\d\d|\d)-(\d\d\d\d)/', $data, $matches)){
            
            
            //If the retrieved day only contains 1 digit a '0' is added to ensure, that the format complies MySQL's date format.
            if(strlen($matches[1])==1){
                $dd = "0".$matches[1];
            }
            else{
                $dd = $matches[1];
            }
            
            
            //If the retrieved month only contains 1 digit a '0' is added to ensure, that the format complies MySQL's date format.
            if(strlen($matches[2])==1){
                $mm = "0".$matches[2];
            }
            else{
                $mm = $matches[2];
            }

            $yyyy = $matches[3];

            //The date property is updated to contain the date in the correct format
            $this->date = $yyyy."-".$mm."-".$dd;  
        }
        else{
            $this->date = NULL;
        }
    }
    
    
    /**
    * This method saves the start and end time for the lesson
    * @param String $data contains the lesson data as a string. The lesson data is fetched from Lectio and is the content of the data-additionalinfo class.
    */
    private function setTime($data){
        if(preg_match('/(\d\d:\d\d)\stil\s(\d\d:\d\d)/', $data, $matches)){
            
            //Splits the matches at every ':'. The result is returned as an array.
            $startTimeArray = preg_split("/[:]+/",$matches[1]);
            $endTimeArray = preg_split("/[:]+/",$matches[2]);
            
            //Concatenates the results and adds '00' to the end to make sure the format is correct
            $this->startTime = $startTimeArray[0].":".$startTimeArray[1].":"."00";
            $this->endTime= $endTimeArray[0].":".$endTimeArray[1].":"."00";
        }
        else{
            $this->startTime = NULL;
            $this->endTime = NULL;
        }
    }
    
    /**
    * Saves the class information on the lesson
    * @param String $data contains the lesson data as a string. The lesson data is fetched from Lectio and is the content of the data-additionalinfo class.
    */
    private function setClass($data){
        //Searches for the content between 'Hold: ' and ' Lærer'. The '?' makes it non-greedy, which means it will search for the shortest string between the two.
        if(preg_match('/Hold:\s(.*?)\sLærer/', $data, $matches)){
            $this->class = $this->convertClass($matches[1]);
        }
        else{
            $this->class = NULL;
        }
    }
    
    
    /**
    * Converts the classId to a class name
    * @param String $classId is the class id
    */
    private function convertClass($classId){
        if(preg_match('/DA/', $classId)){
            return "Dansk";
        } 
        else if(preg_match('/MA|Ma/', $classId)){
            return "Matematik";
        }
        else if(preg_match('/FY|Fy/', $classId)){
            return "Fysik";
        } 
        else if(preg_match('/TK/', $classId)){
            return "Teknik";
        } 
        else if(preg_match('/EN|En/', $classId)){
            return "Engelsk";
        } 
        else if(preg_match('/KE|Ke/', $classId)){
            return "Kemi";
        } 
        else if(preg_match('/Itk/', $classId)){
            return "Informationsteknologi";
        } 
        else if(preg_match('/pro/', $classId)){
            return "Programmering";
        } 
        else if(preg_match('/Ti/', $classId)){
            return "Teknologi";
        } 
        else if(preg_match('/th/', $classId)){
            return "Teknologihistorie";
        } 
        else if(preg_match('/Sa/', $classId)){
            return "Samfundsfag";
        } 
        else if(preg_match('/SO|SOf/', $classId)){
            return "Studieområde";
        } 
        else if(preg_match('/Andenaktiv/', $classId)){
            return "Anden aktivitet";
        } 
        else{
            return $classId;
        }
        
    }
    
    
    /**
    * Saves the teacher on the lesson
    * @param String $data contains the lesson data as a string. The lesson data is fetched from Lectio and is the content of the data-additionalinfo class.
    */
    private function setTeacher($data){
        //If only one teacher is on the schedule, his name will be shown following by his initials. By performing a regex i search for this pattern
        if(preg_match('/Lærer:\s(.*?)\s\((.*?)\)\sLokale/', $data, $matches)){
            
            $this->teacher = $matches[1];    
        }
        //If more than one teacher is on the schedule only the initials will be show seperated by commas. I serach for all the content between 'Lærere: ' and ' Lokale'
        else if(preg_match('/Lærere:\s(.*?)\sLokale/', $data, $matches)){
            
            //Splits the matches at every ','
            $teacherTemp=preg_split("/[,\s]+/",$matches[1]);
            
            $this->teacher = $matches[1];
        }
        else{
            $this->teacher = NULL;
        }
    }
    
    
    /**
    * Saves the room information on the lesson
    * @param String $data contains the lesson data as a string. The lesson data is fetched from Lectio and is the content of the data-additionalinfo class.
    */
    private function setRoom($data){
        
        //Gets all the content after 'Lokale: ' or 'Lokaler: '
        if(preg_match('/Lokale\S+\s(.*)/', $data, $matches)){
            
            $this->room = $matches[1];    
            
            //Sorts the string if there is a note or homework
            if(preg_match('/(.*?)\sNote/', $matches[1],$matchesSorted)){
                $this->room = $matchesSorted[1];
            }
            if(preg_match('/(.*?)\sLektier/', $matches[1],$matchesSorted)){
                $this->room = $matchesSorted[1];
            }
        }
        else {
            $this->room = NULL;
        }
    }
    
    
   
    
        
    /**
    * Saves the homework on the lesson
    * @param String $data contains the lesson data as a string. The lesson data is fetched from Lectio and is the content of the data-additionalinfo class.
    */
    private function setHomework($data) {
        if(preg_match('/Lektier:\s(.*)/', $data, $matches)){
            $this->homework = $matches[1];
            
            if(preg_match('/Lektier:\s(.*)\sØvrigt\sindhold:/', $data, $matches)){
                $this->homework = $matches[1];
            }
            else if(preg_match('/Lektier:\s(.*)\sNote/', $data, $matches)){
                $this->homework = $matches[1];
            }
        }
        else {
            $this->homework = NULL;
        }
    }
    
    
    
    /**
    * Saves the additional content for the lesson
    * @param String $data contains the lesson data as a string. The lesson data is fetched from Lectio and is the content of the data-additionalinfo class.
    */  
    private function setAdditionalContent($data){
        if(preg_match('/Øvrigt\sindhold:\s(.*)/', $data, $matches)){
            $this->additionalContent = $matches[1];
            
            if(preg_match('/Øvrigt\sindhold:\s(.*)\sNote:/', $data, $matches)){
                $this->additionalContent = $matches[1];
            }
            
        }
        else {
            $this->additionalContent = NULL;
        }
    }
    
    
    /**
    * Saves the notes on the lesson
    * @param String $data contains the lesson data as a string. The lesson data is fetched from Lectio and is the content of the data-additionalinfo class.
    */    
    private function setNote($data){
        if(preg_match('/Note:\s(.*)/', $data, $matches)){
            $this->note = $matches[1]; 
            if(preg_match('/Øvrigt\sindhold:\s(.*)\sElever:/', $data, $matches)){
                $this->additionalContent = $matches[1];
            }
        }
        else{
            $this->note = NULL;
        }
    }
    
    /**
    * Saves the students on the lesson
    * @param String $data contains the lesson data as a string. The lesson data is fetched from Lectio and is the content of the data-additionalinfo class.
    */  
    private function setStudents($data){
        if(preg_match('/Elever:\s(.*)/', $data, $matches)){
            $this->students = $matches[1]; 
        }
        else{
            $this->students = NULL;
        }
    }
}
?>