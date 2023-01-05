<?php 
// check if session has expired and return result to ajax call
if (isset($_GET['check'])) {
    session_start();
    if (time() > $_SESSION['session_expiry']) { // session has expired
        echo true;
    } else {
        echo false;
    }
}
?>

