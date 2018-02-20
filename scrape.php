<?php
//includes simple_html_dom library
require_once __DIR__.'/simple_html_dom.php';
require_once __DIR__.'/Lesson.class.php';
require_once __DIR__.'/ScheduleList.class.php';
require_once __DIR__.'/LessonGoogleCalEvent.class.php';



//var_dump(scrapeLectio("102018")['schedule']->scheduleList);
//var_dump(scrapeLectio("102018")['scheduleGoogle']->scheduleList);
//sendScheduleToDatabase($_SESSION['schedule']->scheduleList);

var_dump(scrapeLectio('T23:59:59+01:00')['schedule']);


/**
* This function scrapes the schedule for one week on lectio.dk
*
* @param string $weekNumber is the week you want to scrape
*
* @return array containing the schedule for one week in two formats - schedule (MySQL-ready) and scheduleGoogle (Google Cal-ready) 
*/
function scrapeLectio($date){
    
    $date = getWeekNumberFromDate($date);
    
    $weekID = $date['weekNumber'].$date['year'];
    $schoolID = "681";
    $studentID = "14742506655";
    $lectioURL = "http://www.lectio.dk/lectio/".$schoolID."/SkemaNy.aspx?type=elev&elevid=".$studentID."&week=".$weekID;


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



/**
* Gets the start and end date by specifying year and week number
*
* @param int $year is the year
* @param int $weekNumber is the weeknumber
* 
* @return array containing weekStart and weekEnd 
*/
function getWeekStartAndEndDate($year,$weekNumber){
    
    //Creates a new dateTime object
    $dateTimeObject = new DateTime();
    
    //Sets the date by using the ISO 8601 standard, specifying year and week number
    $dateTimeObject->setISODate($year,$weekNumber);
    
    //Saves start date in an array
    $dateRangeArray['weekStart'] = $dateTimeObject->format('Y-m-d');
    
    //Add 6 days to the dateTime object
    $dateTimeObject->modify('+6 days');
    
    //Saves the new date in an array as the end date
     $dateRangeArray['weekEnd'] = $dateTimeObject->format('Y-m-d');
    
    return $dateRangeArray;
}



/**
* Gets the week number by specifying a date in the week
*
* @param string $date is the date
*
* @return array containing weekNumber and year
*/
function getWeekNumberFromDate($date){
    
    $dateTimeObject = new DateTime($date);
    $weekNumber = $dateTimeObject->format("W");
    $year = $dateTimeObject->format("Y");
    return array('weekNumber' => $weekNumber,'year' => $year); 
}







?>