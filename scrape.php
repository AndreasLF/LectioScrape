<?php
//includes simple_html_dom library
include('simple_html_dom.php'); 

//creates html-DOM from the URL
$html = file_get_html('https://www.lectio.dk/lectio/681/SkemaNy.aspx?type=elev&elevid=14742506655'); 

$lessonStatus = "";

foreach($html->find('.s2skemabrik') as $element){
    $data = $element->getAttribute('data-additionalinfo');
   
    
    //Peforms af regular expression to check if the class has been changed
    if(preg_match('/^Ændret!/',$data)){
             
        //LessonStatus changed
        $lessonStatus = 'Ændret';
            
    }
    //Checks if the class has been cancelled by performing a regex
    else if(preg_match('/^Aflyst!/', $data)){
        
        //LessonStaus is changed
        $lessonStatus = 'Aflyst';
    }
   
    
}
    

?>