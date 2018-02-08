<?php
//includes simple_html_dom library
require('simple_html_dom.php');

//Includes connection.php - connects to database
include('connection.php');



/*
$query = "INSERT INTO `skema`(`ID`, `Week`, `Status`, `Description`, `Date`, `StartTime`, `EndTime`, `Class`, `Teacher`, `Room`, `Homework`, `Note`) VALUES (null,'062018','$lessonStatus','$descriptionString','$dateDay."-".$dateMonth."-".$dateYear','$startTimeString','$startTimeString','$classString','$teacherString','$roomString','$homeworkString','$noteString')";
*/

$mm = "03";
$dd = "20";
$yyyy = "2018";
    
$date = $yyyy.$mm.$dd;
$dateInt = (int)$date ;   


$startTimeHours = "14";
$startTimeMinutes = "30";
$startTime = $startTimeHours.$startTimeMinutes."00";
$startTimeInt = (int)$startTime;

$endTimeHours = "15";
$endTimeMinutes = "30";
$endTime = $endTimeHours.$endTimeMinutes."00";
$endTimeInt = (int)$endTime;


 
$query = "INSERT INTO `skema`(`ID`, `Week`, `Date`,`StartTime`, `EndTime`) VALUES (null,'062018',$dateInt,$startTimeInt,$endTimeInt)";

$result = mysqli_query($connection, $query); //mysqli performs a query on the database. It returns true, false or an object containing information about the query

//creates html-DOM from the URL
$html = file_get_html('https://www.lectio.dk/lectio/681/SkemaNy.aspx?type=elev&elevid=14742506655');

foreach($html->find('.s2skemabrik') as $element){
    $data = $element->getAttribute('data-additionalinfo');
       
    //Peforms af regular expression to check if the class has been changed
    if(!(preg_match('/(\d\d|\d)\/(\d\d|\d)-(\d\d\d\d)/', $data, $dateArr)==0)){
       
        echo "------------------------------------------------------------------------ <br>";
        echo $data . "<br>";
        echo "------------------------------------------------------------------------ <br>";
        
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
        
        
         //Peforms af regular expression to check if the class has been changed
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
            
            $startTimeString = $time[1];
            $endTimeString = $time[2];
            
            print_r($time);
            var_dump($time);
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
        

        
        $mm = "03";
        
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
        
        $dd = "20";
        $yyyy = "2018";

        $date = $yyyy.$mm.$dd;
        $dateInt = (int)$date ;   

        
        $startTimeString = $startTimeString . "00";
        $endTimeString = $endTimeString . "00";

        echo $startTimeString;
        $query = "INSERT INTO `skema`(`ID`, `Week`, `Date`,`StartTime`, `EndTime`) VALUES (null,'062018',$dateInt,$startTimeString,$endTimeString)";

        $result = mysqli_query($connection, $query); //mysqli performs a query on the database. It returns true, false or an object containing information about the query
        

            echo mysqli_error($result);
   
        
        else{ //If $result is false (mysqli_query was unsuccesful)
            echo "<br>ERROR executing: $query"."<br>".mysqli_error($connection)."<br><br>"; //An error message is created and echoed to screen
        
    }
}



?>