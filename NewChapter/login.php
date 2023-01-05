<?php
include "connect-db.php";
connectDB(); // connect to database 
include "header.php";

// check if form is submitted 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // perform form validation 
    $errors = 0; // error count

    // validate username and password
    // retieve existing usernames from database 
    $query = "SELECT username FROM users";
    $result = mysqli_query($handler, $query);
    $usernames = array();
    while ($row = mysqli_fetch_array($result)) {
        array_push($usernames, $row[0]);
    }
    if (empty($_POST['username'])) {
        $usernameError = "Please enter username";
        $errors += 1;
    } elseif (!in_array($_POST['username'], $usernames)) { // check if username exists
        $usernameError = "Username does not exist, please enter a registered username";
        $errors += 1;
    } else { // validate password
        // retieve password of provided username from database 
        $username = mysqli_real_escape_string($handler, trim($_POST['username']));
        $password = mysqli_real_escape_string($handler, trim($_POST['password']));

        $query = "SELECT password FROM users WHERE username='$username'";
        $result = mysqli_query($handler, $query);
        while ($row = mysqli_fetch_array($result)) {
            $hash = $row[0];
        }

        // check if password is correct 
        if (!password_verify("$password", "$hash")) {
            $passwordError = "Incorrect password, please try again";
            $errors += 1;
        }
    }

    // if no errors found 
    if ($errors == 0) {
        if (!isset($_SESSION)) session_start();
        $_SESSION['username'] = $username;

        if (!isset($_SESSION['session_expiry']) || time() < $_SESSION['session_expiry']) {
            $_SESSION['session_expiry'] = time() + (60 * 60); // 1 hour
        }

        // retieve role of user 
        $query = "SELECT role FROM users WHERE username='$username'";
        $result = mysqli_query($handler, $query);
        while ($row = mysqli_fetch_array($result)) {
            $role = $row[0];
        }

        // redirect user according to role 
        if ($role == 1) { // admin
            header("Location: dashboard.php");
            exit();
        }
        if ($role == 2) { // customer
            header("Location: index.php");
            exit();
        }
    }
}
?>

<html>

<head>
    <title>NewChapter | Log In</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/form-control.css" type="text/css">
</head>

<body id="scroll-bar">
    <div class="wrapper">
        <div class="title-container">L O G&emsp;I N</div>
        <div class="register-div">
            <a href="index.php"><i class="fa-solid fa-xmark"></i></a>
            <form class="register-form" action="login.php" method="post" name="login">
                <div style="height:120px;width:100%"></div>
                <?php
                // callback message from other pages 
                if (isset($_GET['msg'])) {
                    $adminMessage = $_GET['msg'];

                    // display message accordingly 
                    if ($adminMessage == "registered") {
                        echo '<p class="login-msg" id="admin-msg">You have registered successfully!</p><br>';
                    } else if ($adminMessage == "logout") {
                        echo '<p class="login-msg" id="admin-msg">You have logged out successfully!</p><br>';
                    } else if ($adminMessage == "adminnotloggedin") {
                        echo '<p class="login-msg" id="admin-msg">Login access required for admin interface</p><br>';
                    } else if ($adminMessage == "sessionexpired") {
                        echo '<p class="login-msg" id="admin-msg">Your session has expired, please log in again</p><br>';
                    } else if ($adminMessage == "loginrequired") {
                        echo '<p class="login-msg" id="admin-msg">You must be logged in to checkout</p><br>';
                    }

                    unset($_GET['msg']);
                    unset($adminMessage);
                }
                ?>
                <h2>Welcome!</h2>
                <!-- Input fields for login form -->
                <div class="form-control">
                    <label class="form-label">Username</label>
                    <input class="register-info" type="text" id="username" name="username" 
                    value="<?php if (isset($_POST['username'])) echo $_POST['username'] ?>" required>
                    <?php
                    if (isset($usernameError)) {
                        echo '<div class="form-error">';
                        echo $usernameError;
                        echo "</div>";
                        unset($usernameError);
                    }
                    ?>
                </div>
                <br>

                <div class="form-control">
                    <label class="form-label">Password</label>
                    <input class="register-info" type="password" id="password" name="password" 
                    value="<?php if (isset($_POST['password'])) echo $_POST['password'] ?>" required>
                    <?php
                    if (isset($passwordError)) {
                        echo '<div class="form-error">';
                        echo $passwordError;
                        echo "</div>";
                        unset($passwordError);
                    }
                    ?>
                </div>
                <br>

                <button type="submit" class="register-button2" value="submit">LOG IN</button><br><br>
                <div class="register-to-login">Don't have an account? <a href="register.php">Sign up now!</a></div>
            </form>

            <div class="login-img"></div>
        </div>
    </div>
    <div style="height:70px;width:100%"></div>
</body>

</html>

<?php
include "footer.php";
?>