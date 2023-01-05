<?php
session_start(); 

if (isset($_SESSION['username'])) {
    unset($_SESSION['username']);
    unset($_SESSION['session_expiry']);
    $_SESSION = array();
    session_unset();
    session_destroy();

    if (isset($_GET["logout"]) && $_GET["logout"] == true) { // session timeout
        header("Location: login.php?msg=sessionexpired");
        exit();
    } else { // user logout
        header("Location: login.php?msg=logout");
        exit();
    }
}
?>