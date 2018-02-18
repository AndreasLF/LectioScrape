    <?php
        
    //This opens a new connection to the MySQL server. mysqli_connect returns an object containing the connection data which is stored in a variable
    $connection = mysqli_connect("localhost","LectioDB","password"); 
        
    //Charset is set to utf8
    $connection->set_charset('utf8');

    //If the object stored in $connection does not contain anything
    if(!$connection){  
        //Exits php and returns an error message. mysqli_error returns a string containing a description of the last error that ocurred on the database
        exit("Connection to mySQL 'logindatabase' failed " . mysqli_error($connection));  
    }
    

    //This selects a database on the server. mysqli_select_db returns a boolean (either true or false) which is stored in $selectDB
    $selectDB = mysqli_select_db($connection,"lectio"); 
    
    
    if(!$selectDB){

        //Exits php and returns an error message. mysqli_error returns a string containing a description of the last error that ocurred on the database
        exit("Connection to SQL database 'logindatabase' failed " . mysqli_error($connection)); 
    }
    ?>
