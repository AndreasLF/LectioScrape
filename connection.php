    <?php
        
    $connection = mysqli_connect("localhost","LectioDB","password"); //This opens a new connection to the MySQL server. mysqli_connect returns an object containing the connection data which is stored in a variable
        
    if(!$connection){ //If the object stored in $connection does not contain anything 
        exit("Connection to mySQL 'logindatabase' failed " . mysqli_error($connection)); //Exits php and returns an error message. mysqli_error returns a string containing a description of the last error that ocurred on the database 
    }
    

    $selectDB = mysqli_select_db($connection,"lectio"); //This selects a database on the server. mysqli_select_db returns a boolean (either true or false) which is stored in $selectDB
    
    
    if(!$selectDB){
        /*If selectDB is false the folowing error message will be printed*/
        exit("Connection to SQL database 'logindatabase' failed " . mysqli_error($connection)); //Exits php and returns an error message. mysqli_error returns a string containing a description of the last error that ocurred on the database
    }
    ?>
