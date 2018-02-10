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

foreach($html->find('.s2skemabrik') as $element){
    $data = $element->getAttribute('data-additionalinfo');
       
    //Peforms af regular expression to check if the class has a date. If not it wont be processed
    if(!(preg_match('/(\d\d|\d)\/(\d\d|\d)-(\d\d\d\d)/', $data, $dateArr)==0)){
        
        $lesson = new Lesson($data);
        
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
        

        $query = "INSERT INTO `skema`(`ID`, `Week`, `Status`, `Description`, `Date`, `StartTime`, `EndTime`, `Class`, `Teacher`, `Room`, `Homework`, `Note`) VALUES (null,$weekNumber,'$lesson->status','$lesson->description','$lesson->date','$lesson->startTime','$lesson->endTime','$lesson->class','$lesson->teacher','$lesson->room','$lesson->homework','$lesson->note')";

        $result = mysqli_query($connection, $query); //mysqli performs a query on the database. It returns true, false or an object containing information about the query
        
        if ($result){ //If $result is true (mysqli_query was successful)
            echo "Data uploaded successfully<br><br>";
            
        }
        else{ //If $result is false (mysqli_query was unsuccesful)
            echo "<br>ERROR executing: $query"."<br>".mysqli_error($connection)."<br><br>"; //An error message is created and echoed to screen
        }
        
    }
}



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
    
    
    function __construct($data){
        $this->setStatusAndDescription($data);
        $this->setDate($data);
        $this->setTime($data);
        $this->setClass($data);
        $this->setTeacher($data);
        $this->setRoom($data);
        $this->setHomework($data);
        $this->setNote($data);
    }
    
    
    private function setStatusAndDescription($data){
        if(preg_match('/^Ændret!/',$data)){
             
             //Change lesson status
            $this->status = 'Ændret';
            
            //If there is a string between "Ændret!" and the date. Here the class descritption will show up if there is any
            if(preg_match('/^Ændret!\s(.*)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $matches)){
            
            $this->description = $matches[1];
             }
            
        }
        //Checks if the class has been cancelled by performing a regex
        else if(preg_match('/^Aflyst!/', $data)){
            $this->status = 'Aflyst';
            
            //If there is a string between "Aflyst!" and the date. Here the class descritption will show up if there is any.
            if(preg_match('/^Aflyst!\s(.*)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $matches)){
            
            $this->description = $matches[1];
            }
        }
        //Else if there is a string before the date. Here the class description will show up if there is any.
        else if(preg_match('/(.*)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $matches)){
            
            $this->status = null;
            $this->description = $matches[1];
        }
        else{
            $this->description = null; 
        }
    }
    
    private function setDate($data){
        if(preg_match('/(\d\d|\d)\/(\d\d|\d)-(\d\d\d\d)/', $data, $matches)){
            
            
            if(strlen($matches[1])==1){
                $dd = "0".$matches[1];
            }
            else{
                $dd = $matches[1];
            }
            
            
            if(strlen($matches[2])==1){
                $mm = "0".$matches[2];
            }
            else{
                $mm = $matches[2];
            }

            $yyyy = $matches[3];

            $this->date = $yyyy.$mm.$dd;  
        }
        else{
            $this->date = null;
        }
    }
    
    
    private function setTime($data){
        if(preg_match('/(\d\d:\d\d)\stil\s(\d\d:\d\d)/', $data, $matches)){
            
            $startTimeArray = preg_split("/[:]+/",$matches[1]);
            $endTimeArray = preg_split("/[:]+/",$matches[2]);
            
            $this->startTime = $startTimeArray[0].$startTimeArray[1]."00";
            $this->endTime= $endTimeArray[0].$endTimeArray[1]."00";
        }
        else{
            $this->startTime = null;
            $this->endTime = null;
        }
    }
    
    private function setClass($data){
        if(preg_match('/Hold:\s(.*?)\sLærer/', $data, $matches)){
            $this->class = $matches[1];
        }
        else{
            $this->class = null;
        }
    }
    
    private function setTeacher($data){
        if(preg_match('/Lærer:\s(.*?)\s\((.*?)\)\sLokale/', $data, $matches)){
            
            $this->teacher = $matches[1];    
        }
        else if(preg_match('/Lærere:\s(.*?)\sLokale/', $data, $matches)){
            $teacherTemp=preg_split("/[,\s]+/",$matches[1]);
            
            $this->teacher = $teacherTemp[0];
        }
        else{
            $this->teacher = null;
        }
    }
    
    
    //Performs a regex and saves the room data
    private function setRoom($data){
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
            $this->room = null;
        }
    }
    
    //Performs a regular expression and saves the homework
    private function setHomework($data) {
        if(preg_match('/Lektier:\s-\s(.*)/', $data, $matches)){
            $this->homework = $matches[1];
        }
        else {
            $this->homework = null;
        }
    }
    
    //Performs a regular expression and stores the note
    private function setNote($data){
        if(preg_match('/Note:\s(.*)/', $data, $matches)){
            $this->note = $matches[1]; 
        }
        else{
            $this->note = null;
        }
    }  
}





?>