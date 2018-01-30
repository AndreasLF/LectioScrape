<?php
//includes simple_html_dom library
include('simple_html_dom.php'); 

//creates html-DOM from the URL
$html = file_get_html('https://www.lectio.dk/lectio/681/SkemaNy.aspx?type=elev&elevid=14742506655'); 

echo $html;


?>