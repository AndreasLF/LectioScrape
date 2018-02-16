<?php
//includes simple_html_dom library
require_once __DIR__.'/simple_html_dom.php';
require_once __DIR__.'/Lesson.class.php';
require_once __DIR__.'/ScheduleList.class.php';

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







?>