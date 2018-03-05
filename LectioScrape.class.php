<?php



/**
* LectioScrape
*
* This creates a lectioScrape object containing the schedule for one week
* @author Andreas Fiehn
*/

class LectioScrape{
    
    /** @var string $weekNumber*/
    public $weekNumber;
    
    /** @var string $year*/
    public $year;
    
    /** @var list containing lesson objects */
    public $scheduleMySql; 
    
    /** @var list containing lessonGoogleCalEvent objects */
    public $scheduleGoogle;
    
    /** @var list containing lessonFullcalendar objects */
    public $scheduleFullcalendar;
        
    
    /**
    * Constructs the LectioScrape object
    *
    * @param string $date is a date inside the week you want to scrape. The date is in ISO8601 format
    */
    function __construct($date){
        //Includes simple_html_dom library
        require_once __DIR__.'/simple_html_dom.php';
    
        //Includes classes
        require_once __DIR__.'/Lesson.class.php';
        require_once __DIR__.'/LessonGoogleCalEvent.class.php';
        require_once __DIR__.'/LessonFullcalendar.class.php';

        $this->weekNumber = $this->getWeekNumberFromDate($date)['weekNumber'];
        $this->year = $this->getWeekNumberFromDate($date)['year'];
        
        $schedule = $this->scrapeLectio($date);
        
        $this->scheduleMySql = $schedule['schedule'];
        $this->scheduleGoogle = $schedule['scheduleGoogle'];
        $this->scheduleFullcalendar = $schedule['scheduleFullcalendar'];
    }
    

    /**
    * This function scrapes the schedule for one week on lectio.dk
    *
    * @param string $weekNumber is the week you want to scrape
    *
    * @return array containing the schedule for one week in two formats - schedule (MySQL-ready) and scheduleGoogle (Google Cal-ready) 
    */
    private function scrapeLectio($date){

        $date = $this->getWeekNumberFromDate($date);

        $this->weekNumber = $date['weekNumber'];
        $this->year = $date['year'];
        
        $weekID = $date['weekNumber'].$date['year'];
        $schoolID = "681";
        $studentID = "14742506655";
        $lectioURL = "http://www.lectio.dk/lectio/".$schoolID."/SkemaNy.aspx?type=elev&elevid=".$studentID."&week=".$weekID;


        //creates html-DOM from the URL
        $html = file_get_html($lectioURL);

        $schedule;
        $scheduleGoogle;
        $scheduleFullcalendar;

        foreach($html->find('.s2skemabrik') as $element){
            $data = $element->getAttribute('data-additionalinfo');
            
            if($href = $element->href){
                $url = "https://www.lectio.dk" . $element->href;
            }
            else{
                $url = $lectioURL;
            }
            

            //Peforms af regular expression to check if the class has a date. If not it wont be processed
            if(!(preg_match('/(\d\d|\d)\/(\d\d|\d)-(\d\d\d\d)/', $data, $dateArr)==0)){

                $lesson = new Lesson($data,$url);
                $lessonGoogle = new LessonGoogleCalEvent($lesson);
                $lessonFullcalendar = new LessonFullcalendar($lesson);

                $schedule[] = $lesson;
                $scheduleGoogle[] = $lessonGoogle;
                $scheduleFullcalendar[] = $lessonFullcalendar;
            }
        }

        return array('schedule' => $schedule,'scheduleGoogle' => $scheduleGoogle, 'scheduleFullcalendar' => $scheduleFullcalendar);    
    }

    

    /**
    * Gets the start and end date by specifying year and week number
    *
    * @param int $year is the year
    * @param int $weekNumber is the weeknumber
    * 
    * @return array containing weekStart and weekEnd 
    */
    private function getWeekStartAndEndDate($year,$weekNumber){

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
    private function getWeekNumberFromDate($date){

        $dateTimeObject = new DateTime($date);
        $weekNumber = $dateTimeObject->format("W");
        $year = $dateTimeObject->format("Y");
        return array('weekNumber' => $weekNumber,'year' => $year); 
    }

    
    
    /**
    * Sends the schedule to the MySQL database
    */
    public function sendToDatabase(){
        //Includes connection.php - connects to database
        require_once __DIR__.'/connection.php';
        
        //Creates the weekID
        $weekID = $this->weekNumber.$this->year;
        
        //Deletes the week to prevent duplicates
        $this->deleteFromDatabase($connection,$weekID);
        
        foreach($this->scheduleMySql as $lesson){ 
            
            //Creates a prepared statement for the database
            $stmt = mysqli_prepare($connection,"INSERT INTO skema(ID,Week, Status, Description, Date, StartTime, EndTime, Class, Teacher, Room, Homework, Note) VALUES (NULL,?,?,?,?,?,?,?,?,?,?,?)");
            
            //Binds parameters to the prepared statement. Every parameter is of type String
            $stmt->bind_param("sssssssssss",$weekID,$lesson->status,$lesson->description,$lesson->date,$lesson->startTime,$lesson->endTime,$lesson->class,$lesson->teacher,$lesson->room,$lesson->homework,$lesson->note); 

            //Executes the prepared statement. Returns a boolean - true on succes and false on failure.
            $result = $stmt->execute(); 

            //Creates error message if MySQL query was unsuccesful
            if (!$result){ 
                exit("<br>ERROR executing: $query"."<br>".mysqli_error($connection)."<br>");
            }
           

            $stmt->close(); //Closes the prepared statement 
        }   
    }
    
    
    /**
    * Deletes a week from the database
    *
    * @param $connection is the MySQL connection object to delete the schedule from
    * @param string $weekID is the week to delete and year to delete
    */
    private function deleteFromDatabase($connection,$weekID){
        
        //Includes connection.php - connects to database
        require_once __DIR__.'/connection.php';
        
        //Creates a prepared statement for the database
        $stmt = mysqli_prepare($connection,"DELETE FROM `skema` WHERE `Week`= ?");
            
        //Binds parameters to the prepared statement. Every parameter is of type String
        $stmt->bind_param("s",$weekID); 
    
        //Executes the prepared statement. Returns a boolean - true on succes and false on failure.
        $result = $stmt->execute();   
        
        //Creates error message if MySQL query was unsuccesful
        if (!$result){ 
            exit("<br>ERROR executing: $query"."<br>".mysqli_error($connection)."<br>");
        }
    }

    
    
    
}











?>