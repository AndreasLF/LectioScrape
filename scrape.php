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
        echo $lessonStaus;
            
    }
    //Checks if the class has been cancelled by performing a regex
    else if(preg_match('/^Aflyst!/', $data)){
        
        //LessonStaus is changed
        $lessonStatus = 'Aflyst';
        echo $lessonStaus;
    }
    
    
    //Dato
    preg_match('/(\d\d|\d)\/(\d\d|\d)-(\d\d\d\d)/', $data, $dato);
    print_r($dato);
    var_dump($dato);
        

    //Tid
    preg_match('/(\d\d:\d\d)\stil\s(\d\d:\d\d)/', $data, $time);
    print_r($time);
    var_dump($time);   
        
   
    //Class
    preg_match('/Hold:\s(.*?)\sLærer/', $data, $class);
    print_r($class);
    var_dump($class);
   
    //Teacher
    if(preg_match('/Lærer:\s(.*?)\s\((.*?)\)\sLokale/', $data, $teacher)){
        print_r($teacher);
        var_dump($teacher);         
    }else if(preg_match('/Lærere:\s(.*?)\sLokale/', $data, $teacherTemp)){
        //preg_match('/([^,\s]+)/', $teacherTemp[1], $teacher);
        $teacher=preg_split("/[,\s]+/",$teacherTemp[1]);
            
        print_r($teacher);
        var_dump($teacher);
    } 
    
}
    

?>