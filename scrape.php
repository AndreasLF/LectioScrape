<?php
//includes simple_html_dom library
require_once __DIR__.'/simple_html_dom.php';
require_once __DIR__.'/Lesson.class.php';
require_once __DIR__.'/ScheduleList.class.php';
require_once __DIR__.'/LessonGoogleCalEvent.class.php';



var_dump(scrapeLectio("102018")['schedule']->scheduleList);
//sendScheduleToDatabase($_SESSION['schedule']->scheduleList);

/**
* This function scrapes the schedule for one week on lectio.dk
*
* @param string $weekNumber is the week you want to scrape
*
* @return array containing the schedule for one week in two formats - schedule (MySQL-ready) and scheduleGoogle (Google Cal-ready) 
*/
function scrapeLectio($weekNumber){
    
    $weekNumberStr = (string)$weekNumber;
    $schoolID = "681";
    $studentID = "14742506655";
    $lectioURL = "http://www.lectio.dk/lectio/".$schoolID."/SkemaNy.aspx?type=elev&elevid=".$studentID."&week=".$weekNumberStr;


    //creates html-DOM from the URL
    $html = file_get_html($lectioURL);

    $schedule = new ScheduleList(10);
    $scheduleGoogle = new ScheduleList(10);

    foreach($html->find('.s2skemabrik') as $element){
        $data = $element->getAttribute('data-additionalinfo');

        //Peforms af regular expression to check if the class has a date. If not it wont be processed
        if(!(preg_match('/(\d\d|\d)\/(\d\d|\d)-(\d\d\d\d)/', $data, $dateArr)==0)){

            $lesson = new Lesson($data);
            $lessonGoogle = new LessonGoogleCalEvent($lesson);

            $schedule->addLesson($lesson);
            $scheduleGoogle->addLesson($lessonGoogle);
        }
    }

    return array('schedule' => $schedule,'scheduleGoogle' => $scheduleGoogle);    
}



/**
* Sends the schedule to the MySQL database
* @param $scheduleList is list of lessons to upload.
*/
function sendScheduleToDatabase($scheduleList){
    //Includes connection.php - connects to database
    require_once __DIR__.'/connection.php';

    foreach($scheduleList as $lesson){
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








?>