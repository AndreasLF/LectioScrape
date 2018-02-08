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
       
        echo "------------------------------------------------------------------------ <br>";
        echo $data . "<br>";
        echo "------------------------------------------------------------------------ <br>";
        
        
        $lesson = new Lesson($data);
        
        //Saves the lesson's staus
        $lessonStatus = "";
        
        //declaring variables
        $descriptionString = "";
        $dateDay = $dateArr[1];
        $dateMonth = $dateArr[2];
        $dateYear = $dateArr[3];
        $startTimeString = "";
        $endTimeString = "";
        $classString = "";
        $roomString = "";
        $teacherString = "";
        $homeworkString = "";
        $noteString = "";
        
        
         //Peforms a regular expression to check if the class has been changed
        if(preg_match('/^Ændret!/',$data)){
             
             //Change lesson status
            $lessonStatus = 'Ændret';
            
            //If there is a string between "Ændret!" and the date. Here the class descritption will show up if there is any
            if(preg_match('/^Ændret!\s(.*)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $description)){
            
            $descriptionString = $description[1];
            print_r($description);
            var_dump($description);
             }
            
        }
        //Checks if the class has been cancelled by performing a regex
        else if(preg_match('/^Aflyst!/', $data)){
            $lessonStatus = 'Aflyst';
            
            //If there is a string between "Aflyst!" and the date. Here the class descritption will show up if there is any.
            if(preg_match('/^Aflyst!\s(.*)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $description)){
            
            $descriptionString = $description[1];
                
            print_r($description);
            var_dump($description);
            }
        }
        //Else if there is a string before the date. Here the class description will show up if there is any.
        else if(preg_match('/(.*)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $description)){
            
            $descriptionString = $description[1];
            
            print_r($description);
            var_dump($description);
        }
        
        
        //Date
        preg_match('/(\d\d|\d)\/(\d\d|\d)-(\d\d\d\d)/', $data, $dato);
        print_r($dato);
        var_dump($dato);
        
        
        
        //Time
        if(preg_match('/(\d\d:\d\d)\stil\s(\d\d:\d\d)/', $data, $time)){
            
            $startTimeArray = preg_split("/[:]+/",$time[1]);
            $endTimeArray = preg_split("/[:]+/",$time[2]);
            
            $startTimeString = $startTimeArray[0].$startTimeArray[1];
            $endTimeString = $endTimeArray[0].$endTimeArray[1];
            
            print_r($time);
            var_dump($time);
            
            var_dump($startTimeString);
            var_dump($endTimeString);
        }
        else{
            $startTimeString = "0000";
            $endTimeString = "0000";
        }
        
   
        
        //Class
        if(preg_match('/Hold:\s(.*?)\sLærer/', $data, $class)){
            
            $classString = $class[1];
            
            print_r($class);
            var_dump($class);
        }
        
       
        
        //Teacher
        if(preg_match('/Lærer:\s(.*?)\s\((.*?)\)\sLokale/', $data, $teacher)){
            
            $teacherString = $teacher[1];
            
            print_r($teacher);
            var_dump($teacher);    
        }else if(preg_match('/Lærere:\s(.*?)\sLokale/', $data, $teacherTemp)){
            $teacher=preg_split("/[,\s]+/",$teacherTemp[1]);
            
            $teacherString = $teacher[0];
            
            print_r($teacher);
            var_dump($teacher);
        } 
        
        
        
        
        //Room
        if(preg_match('/Lokale\S+\s(.*)/', $data, $room)){
            
        $roomString = $room[1];    
            
        print_r($room);
        var_dump($room);
            
            //Sorts the string if there is a note or homework
            if(preg_match('/(.*?)\sNote/', $room[1],$roomSorted)){
                $roomString = $roomSorted[1];
                print_r($roomSorted);
                var_dump($roomSorted);
            }
            if(preg_match('/(.*?)\sLektier/', $room[1],$roomSorted)){
                $roomString = $roomSorted[1];
                print_r($roomSorted);
                var_dump($roomSorted);
            }
        }
        
        
        
        //Lektier
        if(preg_match('/Lektier:\s-\s(.*)/', $data, $homework)){
            $homeworkString = $homework[1];
            print_r($homework);
            var_dump($homework);
        }
        
        
        //Note
        
        if(preg_match('/Note:\s(.*)/', $data, $note)){
            $noteString = $note[1];
            print_r($note);
            var_dump($note);      
        }
        
        
        
        echo "=================================================================<br>";
        echo "Status: " . $lessonStatus . "<br>";
        echo "Description: " . $descriptionString . "<br>";
        echo "Date: " . $dateDay . "-" . $dateMonth . "-" . $dateYear . "<br>";
        echo "Start time: " . $startTimeString . "<br>";
        echo "End time: " . $endTimeString . "<br>";
        echo "Class: " . $classString . "<br>";
        echo "Teacher: " . $teacherString . "<br>";
        echo "Room: " . $roomString . "<br>";
        echo "Homework: " . $homeworkString . "<br>";
        echo "Note: " . $noteString . "<br>";
        echo "=================================================================<br>";
        
        
        if(strlen($dateDay)==1){
            $dd = "0".$dateDay;
        }
        else{
            $dd = $dateDay;
        }
        if(strlen($dateMonth)==1){
            $mm = "0".$dateMonth;
        }
        else{
            $mm = $dateMonth;
        }
        
        
        $yyyy = $dateYear;

        $date = $yyyy.$mm.$dd;  
        
        
        $startTimeString = $startTimeString . "00";
        $endTimeString = $endTimeString . "00";

        $query = "INSERT INTO `skema`(`ID`, `Week`, `Status`, `Description`, `Date`, `StartTime`, `EndTime`, `Class`, `Teacher`, `Room`, `Homework`, `Note`) VALUES (null,$weekNumber,'$lessonStatus','$descriptionString',$date,$startTimeString,$endTimeString,'$classString','$teacherString','$roomString','$homeworkString','$noteString')";

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
    private $status;
    private $description;
    private $date;
    private $startTime;
    private $endTime;
    private $class;
    private $teacher;
    private $room;
    private $homework;
    private $note;
    
    
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
            $status = 'Ændret';
            
            //If there is a string between "Ændret!" and the date. Here the class descritption will show up if there is any
            if(preg_match('/^Ændret!\s(.*)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $matches)){
            
            $description = $matches[1];
             }
            
        }
        //Checks if the class has been cancelled by performing a regex
        else if(preg_match('/^Aflyst!/', $data)){
            $status = 'Aflyst';
            
            //If there is a string between "Aflyst!" and the date. Here the class descritption will show up if there is any.
            if(preg_match('/^Aflyst!\s(.*)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $matches)){
            
            $description = $matches[1];
            }
        }
        //Else if there is a string before the date. Here the class description will show up if there is any.
        else if(preg_match('/(.*)\s((\d\d|\d)\/(\d\d|\d)-(\d\d\d\d))/', $data, $matches)){
            
            $status = null;
            $description = $matches[1];
        }
        else{
            $description = null; 
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

            $date = $yyyy.$mm.$dd;  
        }
        else{
            $date = null;
        }
    }
    
    
    private function setTime($data){
        if(preg_match('/(\d\d:\d\d)\stil\s(\d\d:\d\d)/', $data, $matches)){
            
            $startTimeArray = preg_split("/[:]+/",$matches[1]);
            $endTimeArray = preg_split("/[:]+/",$matches[2]);
            
            $startTime = $startTimeArray[0].$startTimeArray[1]."00";
            $endTime= $endTimeArray[0].$endTimeArray[1]."00";
        }
        else{
            $startTime = null;
            $endTime = null;
        }
    }
    
    private function setClass($data){
        if(preg_match('/Hold:\s(.*?)\sLærer/', $data, $matches)){
            $class = $matches[1];
        }
        else{
            $class = null;
        }
    }
    
    private function setTeacher($data){
        if(preg_match('/Lærer:\s(.*?)\s\((.*?)\)\sLokale/', $data, $matches)){
            
            $teacher = $matches[1];    
        }
        else if(preg_match('/Lærere:\s(.*?)\sLokale/', $data, $mathces)){
            $teacherTemp=preg_split("/[,\s]+/",$matches[1]);
            
            $teacher = $teacherTemp[0];
        }
        else{
            $teacher = null;
        }
    }
    
    
    //Performs a regex and saves the room data
    private function setRoom($data){
        if(preg_match('/Lokale\S+\s(.*)/', $data, $matches)){
            
            $room = $matches[1];    
            
            //Sorts the string if there is a note or homework
            if(preg_match('/(.*?)\sNote/', $room[1],$mathcesSorted)){
                $room = $matchesSorted[1];
            }
            if(preg_match('/(.*?)\sLektier/', $room[1],$matchesSorted)){
                $room = $matchesSorted[1];
            }
        }
        else {
            $room = null;
        }
    }
    
    //Performs a regular expression and saves the homework
    private function setHomework($data) {
        if(preg_match('/Lektier:\s-\s(.*)/', $data, $matches)){
            $homework = $matches[1];
        }
        else {
            $homework = null;
        }
    }
    
    //Performs a regular expression and stores the note
    private function setNote($data){
        if(preg_match('/Note:\s(.*)/', $data, $matches)){
            $note = $matches[1]; 
        }
        else{
            $note = null;
        }
    }  
}





?>