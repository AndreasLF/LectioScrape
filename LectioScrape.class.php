<?php


/**
* LectioScrape
* This creates a lectioScrape object containing the schedule for one week
*
* @author Andreas Fiehn
*/

class LectioScrape{
    
    /** @var string $weekNumber*/
    public $weekNumber;
    
    /** @var string $year*/
    public $year;
    
    /** @var array containing lesson objects */
    private $scheduleMySql; 
    
    /** @var array containing lessonGoogleCalEvent objects */
    public $scheduleGoogle;
    
    /** @var array containing lessonFullcalendar objects */
    public $scheduleFullcalendar;
    
    /** @var mysqli object*/
    private $mysqli;
    
        
    
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

        //Sets the weekNumber and year
        $this->weekNumber = $this->getWeekNumberFromDate($date)['weekNumber'];
        $this->year = $this->getWeekNumberFromDate($date)['year'];
        
        //Scrapes the schedule
        $schedule = $this->scrapeLectio($date);
        
        //Sets the schedule parameters
        $this->scheduleMySql = $schedule['schedule'];
        $this->scheduleGoogle = $schedule['scheduleGoogle'];
        $this->scheduleFullcalendar = $schedule['scheduleFullcalendar'];
    }
    

    /**
    * This function scrapes the schedule for one week on lectio.dk
    *
    * @param string $date is a date inside the week you want to scrape. The date is in ISO8601 format
    *
    * @return array containing the schedule for one week in three formats - schedule (MySQL-ready), scheduleGoogle (Google Cal-ready), scheduleFullcalendar (Fullcalendar-ready)
    */
    private function scrapeLectio($date){

        //gets the week number from the date and saves it in $date. $date is now an array containing weekNumber and year
        $date = $this->getWeekNumberFromDate($date);

        //Sets the weekNumber and year properties
        $this->weekNumber = $date['weekNumber'];
        $this->year = $date['year'];
        
        //Sets the url's weekID, schoolID and studentID
        $weekID = $date['weekNumber'].$date['year'];
        $schoolID = "681";
        $studentID = "14742506655";
        
        //Creates the schedule url
        $lectioURL = "http://www.lectio.dk/lectio/".$schoolID."/SkemaNy.aspx?type=elev&elevid=".$studentID."&week=".$weekID;


        //creates html-DOM from the URL (uses simple_html_dom library)
        $html = file_get_html($lectioURL);

        //Define schedule variables
        $schedule;
        $scheduleGoogle;
        $scheduleFullcalendar;

        //Loop foreach element in the html dom with the class s2skemabrik
        foreach($html->find('.s2skemabrik') as $element){
            //Gets the data-additionalinfo attribute from the element
            $data = $element->getAttribute('data-additionalinfo');
            
            //If a href attribute exists this is saved as the url. Else the lectioUrl will be the url
            if($href = $element->href){
                $url = "https://www.lectio.dk" . $href;
            }
            else{
                $url = $lectioURL;
            }
            

            //Peforms a regular expression to check if the data-additionalinfo contains a date in the desired format. If not it wont be processed
            if(!(preg_match('/(\d\d|\d)\/(\d\d|\d)-(\d\d\d\d)/', $data, $dateArr)==0)){
                
                //Lesson object are created
                $lesson = new Lesson($data,$url);
                $lessonGoogle = new LessonGoogleCalEvent($lesson);
                $lessonFullcalendar = new LessonFullcalendar($lesson);

                //The objects are added to the schedule arrays
                $schedule[] = $lesson;
                $scheduleGoogle[] = $lessonGoogle->getEventParams();
                $scheduleFullcalendar[] = $lessonFullcalendar->getCalendarEvent();
            }
        }

        //An array containing the three schedule arrays is returned
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

        //Returns the date range array
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

        //Creates ned datetime object from the date
        $dateTimeObject = new DateTime($date);
        //The weeknumber is saved
        $weekNumber = $dateTimeObject->format("W");
        //The year is saved
        $year = $dateTimeObject->format("Y");
        //Returns array containing week number and year
        return array('weekNumber' => $weekNumber,'year' => $year); 
    }

    
    
    /**
    * Sends the schedule to the MySQL database
    *
    * @return boolean true on succes, false on failure
    */
    public function sendToDatabase(){
        
        //Checks if the mysqli property is set
        if(!isset($this->mysqli)){
            //Creates a new mysqli obect
            $this->mysqli = new mysqli("localhost","LectioDB","password","lectio"); 
        }
        
        //Creates the weekID
        $weekID = $this->weekNumber.$this->year;
        
        //Deletes the week to prevent duplicates
        $r = $this->deleteFromDatabase($weekID);
        
        //If the week was not deleted from the database. False is returned
        if(!($r)){
            return false;
        }
        
        //Loops for each lesson in the scheduleMySql parameter
        foreach($this->scheduleMySql as $lesson){ 
            
            //Creates a prepared statement for the database
            $stmt = $this->mysqli->prepare("INSERT INTO skema(ID,Week, Status, Description, Date, StartTime, EndTime, Class, Teacher, Room, Homework, Note) VALUES (NULL,?,?,?,?,?,?,?,?,?,?,?)");
            
            //If the prepared statement fails to be defined, false is returned
            if(!($stmt)){
                return false;
            }
            
            //Binds parameters to the prepared statement. Every parameter is of type String
            $result = $stmt->bind_param("sssssssssss",$weekID,$lesson->status,$lesson->description,$lesson->date,$lesson->startTime,$lesson->endTime,$lesson->class,$lesson->teacher,$lesson->room,$lesson->homework,$lesson->note); 

            //If bind_param fails, false is returned
            if(!($result)){
                return false;
            }
            
            //Executes the prepared statement
            $result = $stmt->execute(); 

            //If execute fails, false is returned
            if(!($result)){
                return false;
            }
           
            //Closes the prepared statement 
            $stmt->close(); 
            
        }   
        
        //true is returned on succes
        return true;
    }
    
    
    /**
    * Deletes a week from the database
    *
    * @param string $weekID is the week to delete and year to delete
    *
    * @return boolean true on succes, false on failure
    */
    private function deleteFromDatabase($weekID){
        
        //Creates a prepared statement for the database
        $stmt = $this->mysqli->prepare("DELETE FROM `skema` WHERE `Week`= ?");
            
        
        //If the prepared statement fails to be defined, false is returned
        if(!($stmt)){
            return false;
        }

        //Binds parameters to the prepared statement. Every parameter is of type String
        $result = $stmt->bind_param("s",$weekID); 
        
        //If bind_param fails, false is returned
        if(!($result)){
            return false;
        }
        
    
        //Executes the prepared statement. Returns a boolean - true on succes and false on failure.
        $result = $stmt->execute();
        
        //If execute fails, false is returned
        if(!($result)){
            return false;
        }
        
        //Closes the prepared statement
        $stmt->close();
        
        //return true on succes
        return true;
    }

    
    
    
}











?>
