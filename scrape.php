<?php
//includes simple_html_dom library
require('simple_html_dom.php');

//Includes connection.php - connects to database
include('connection.php');

$schoolID = "681";
$studentID = "14742506655";
$weekNumber = "102018";
$lectioURL = "http://www.lectio.dk/lectio/".$schoolID."/SkemaNy.aspx?type=elev&elevid=".$studentID."&week=".$weekNumber;


//creates html-DOM from the URL
$html = file_get_html($lectioURL);

$schedule = new ScheduleList(10);

foreach($html->find('.s2skemabrik') as $element){
    $data = $element->getAttribute('data-additionalinfo');
       
    //Peforms af regular expression to check if the class has a date. If not it wont be processed
    if(!(preg_match('/(\d\d|\d)\/(\d\d|\d)-(\d\d\d\d)/', $data, $dateArr)==0)){
        
        $lesson = new Lesson($data);
        
        $schedule->addLesson($lesson);
        
        
        /*
        echo "=================================================================<br>";
        echo $data . "<br>";
        echo "Status: " . $lesson->status . "<br>";
        echo "Description: " . $lesson->description . "<br>";
        echo "Date: " . $lesson->date . "<br>";
        echo "Start time: " . $lesson->startTime . "<br>";
        echo "End time: " . $lesson->endTime . "<br>";
        echo "Class: " . $lesson->class . "<br>";
        echo "Teacher: " . $lesson->teacher . "<br>";
        echo "Room: " . $lesson->room . "<br>";
        echo "Homework: " . $lesson->homework . "<br>";
        echo "Note: " . $lesson->note . "<br>";
        echo "=================================================================<br>";
        */
     
        
        $stmt = mysqli_prepare($connection,"INSERT INTO skema(ID,Week, Status, Description, Date, StartTime, EndTime, Class, Teacher, Room, Homework, Note) VALUES (NULL,?,?,?,?,?,?,?,?,?,?,?)");//Creates a prepared statement for the database
        
        $stmt->bind_param("sssssssssss",$weekNumber,$lesson->status,$lesson->description,$lesson->date,$lesson->startTime,$lesson->endTime,$lesson->class,$lesson->teacher,$lesson->room,$lesson->homework,$lesson->note); //Binds parameters to the prepared statement. Every parameter is of type String
        
        $result = $stmt->execute(); //Executes the prepared statement. Returns a boolean - true on succes and false on failure.
      
        
        if ($result){ //If $result is true (mysqli_query was successful)
            echo "Data uploaded successfully<br><br>";
            
        }
        else{ //If $result is false (mysqli_query was unsuccesful)
            echo "<br>ERROR executing: $query"."<br>".mysqli_error($connection)."<br><br>"; //An error message is created and echoed to screen
        }
        
        
        $stmt->close(); //Closes the prepared statement
        
        
    }
}

var_dump($schedule->scheduleList);


/**
* Lesson
* Creates a lesson object containing information about the lesson
*
* @author Andreas Fiehn
*/
class Lesson{
    public $status;
    public $description;
    public $date;
    public $startTime;
    public $endTime;
    public $class;
    public $teacher;
    public $room;
    public $homework;
    public $note;
    
    
    /** 
    * This method constructs the object from the lesson class
    * @param String $data contains the lesson data as a string. The lesson data is fetched from Lectio and is the content of the data-additionalinfo class.
    */
    function __construct($data){
        
        //Sets every property by refering to functions
        $this->setStatusAndDescription($data);
        $this->setDate($data);
        $this->setTime($data);
        $this->setClass($data);
        $this->setTeacher($data);
        $this->setRoom($data);
        $this->setHomework($data);
        $this->setNote($data);
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
            if(preg_match('/^Ændret!\s(.*)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $matches)){
                
                $this->description = $matches[1];
             }
            
        }
        //Checks if the lesson has been cancelled by performing a regex
        else if(preg_match('/^Aflyst!/', $data)){
            $this->status = 'Aflyst';
            
            //If there is a string between "Aflyst!" and the date. Here the class descritption will show up if there is any.
            if(preg_match('/^Aflyst!\s(.*)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $matches)){
            
                $this->description = $matches[1];
            }
        }
        //Else if there is a string before the date. Here the class description will show up if there is any.
        else if(preg_match('/(.*)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $matches)){
            
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
            $this->date = $yyyy.$mm.$dd;  
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
            $this->startTime = $startTimeArray[0].$startTimeArray[1]."00";
            $this->endTime= $endTimeArray[0].$endTimeArray[1]."00";
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
            $this->class = $matches[1];
        }
        else{
            $this->class = NULL;
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
        if(preg_match('/Lektier:\s-\s(.*)/', $data, $matches)){
            $this->homework = $matches[1];
        }
        else {
            $this->homework = NULL;
        }
    }
    
    /**
    * Saves the notes on the lesson
    * @param String $data contains the lesson data as a string. The lesson data is fetched from Lectio and is the content of the data-additionalinfo class.
    */    
    private function setNote($data){
        if(preg_match('/Note:\s(.*)/', $data, $matches)){
            $this->note = $matches[1]; 
        }
        else{
            $this->note = NULL;
        }
    }  
}

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