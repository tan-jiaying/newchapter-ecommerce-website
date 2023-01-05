<?php
ob_start();
session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    // redirect user to login page
    header("Location: login.php?msg=adminnotloggedin");
    exit();
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
    <script src="https://kit.fontawesome.com/ffaaf7e5e9.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navigation" style="height:100px">
        <div class="nav-container" style="height:100px">
            <div class="nav-top-row">
                <a href="index.php">
                    <div class="nav-logo">
                        <img class="nav-logo-img" src="img/header-image/logo.svg" alt="New Chapter">
                        <div class="nav-logo-text"><b>&emsp;New Chapter</b></div>
                    </div>
                </a>
                <button class='account-button'><i class='fa-solid fa-user'></i>&emsp;<?php echo $_SESSION['username']; ?></button>
            </div>
        </div>
    </nav>
    <script>
        setInterval(checkSession, 30000); // check session every 30 seconds

        // function to automatically log user out when session has expired
        function checkSession() {
            let check = true;
            $.ajax({
                url: "session-timeout.php",
                method: "get",
                data: {
                    check: check,
                },
                success: function(timeout) {
                    if (timeout == 1) {
                        window.location.href = "logout.php?logout=true";
                    }
                }
            });
        }
    </script>
</body>

</html>