<?php
function connectDB() {
    global $handler; // to be accessed from different php files 

    // connect to database 
    $handler = mysqli_connect("localhost", "root", "", "newchapterdb");

    // display error message if connection failed 
    if (mysqli_connect_errno()) {
        echo "Connection failed: ". mysqli_connect_error();
        exit();
    }
}
?>