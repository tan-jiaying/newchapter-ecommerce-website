<?php
include "connect-db.php";
connectDB(); // connect to database 

// check if form is submitted 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // perform form validation 
    $errors = 0; // error count

    // validate first name 
    $pattern = "/^[A-Za-z\s\.\'@-]*$/";
    if (empty($_POST['fname'])) {
        $fnameError = "Please enter First Name";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['fname'])) {
        $fnameError = "First name can only contain alphabets, white spaces, and special characters -.'@";
        $errors += 1;
    } else {
        $fname = mysqli_real_escape_string($handler, trim($_POST['fname']));
    }

    // validate last name
    if (empty($_POST['lname'])) {
        $lnameError = "Please enter Last Name";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['lname'])) {
        $lnameError = "Last name can only contain alphabets, white spaces, and special characters -.'@";
        $errors += 1;
    } else {
        $lname = mysqli_real_escape_string($handler, trim($_POST['lname']));
    }

    // validate contact number 
    $pattern = "/^(\+?6?01)[02-46-9]-*[0-9]{7}$|^(\+?6?01)[1]-*[0-9]{8}$/";
    if (empty($_POST['mobile'])) {
        $mobileError = "Please enter Contact Number";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['mobile'])) {
        $mobileError = "Contact number must be of format +601[02346789]-XXXXXXX or +6011-XXXXXXXX";
        $errors += 1;
    } else {
        $mobile = mysqli_real_escape_string($handler, trim($_POST['mobile']));
    }

    // validate email
    $pattern = "/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/";
    // retrieve existing emails from database 
    $query = "SELECT email FROM users";
    $result = mysqli_query($handler, $query);
    $emails = array();
    while ($row = mysqli_fetch_array($result)) {
        array_push($emails, $row[0]);
    }
    if (empty($_POST['email'])) {
        $emailError = "Please enter E-mail Address";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['email'])) {
        $emailError = "Invalid E-mail Format";
        $errors += 1;
    } else if (in_array($_POST['email'], $emails)) { // check if email already exists 
        $emailError = "Email is already taken, please use another E-mail";
        $errors += 1;
    } else {
        $email = mysqli_real_escape_string($handler, trim($_POST['email']));
    }

    // validate username 
    // retrieve existing usernames from database 
    $query = "SELECT username FROM users";
    $result = mysqli_query($handler, $query);
    $usernames = array();
    while ($row = mysqli_fetch_array($result)) {
        array_push($usernames, $row[0]);
    }
    $pattern = "/^[a-z0-9\.-_]{6,}$/";
    if (empty($_POST['username'])) {
        $usernameError = "Please enter username";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['username'])) {
        $usernameError = "Username must be at least 6 characters long and can only contain lowercase letters, numbers, and special characters .-_";
        $errors += 1;
    } else if (in_array($_POST['username'], $usernames)) { // check if username already exists
        $usernameError = "Username is already taken, please use another username";
        $errors += 1;
    } else {
        $username = mysqli_real_escape_string($handler, trim($_POST['username']));
    }

    // validate password 
    $pattern = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-~]).{8,}$/";
    if (empty($_POST['password'])) {
        $passwordError = "Please enter password";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['password'])) {
        $passwordError = "Password must contain a minumum of 8 characters, at least one uppercase letter, one lowercase letter, one number, and one special character";
        $errors += 1;
    } else if (empty($_POST['confirm_password'])) { // validate confirm password
        $passwordConfirmError = "Please confirm password";
        $errors += 1;
    } else if ($_POST['password'] != $_POST['confirm_password']) {
        $passwordConfirmError = "Passwords do not match";
        $errors += 1;
    } else { // hash password
        $password = mysqli_real_escape_string($handler, trim($_POST['password']));
        $password = password_hash($password, PASSWORD_DEFAULT);
    }

    // if no errors found 
    if ($errors == 0) {
        $role = 2; // customer
        // insert inputs into users table
        $query = "INSERT INTO users (fname, lname, contactNum, username, email, password, role)
                    VALUES ('$fname', '$lname', '$mobile', '$username', '$email', '$password', '$role')";
        mysqli_query($handler, $query);

        // redirect user to login page
        header("Location: login.php?msg=registered");
        exit();
    }
}
?>

<html>

<head>
    <title>NewChapter | Register</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/form-control.css" type="text/css">
</head>

<body id="scroll-bar">
    <?php
    include "header.php";
    ?>
    <div class="wrapper">
        <div class="title-container">R E G I S T E R</div>
        <div class="register-div">
            <a href="index.php"><i class="fa-solid fa-xmark"></i></a>
            <form class="register-form" action="register.php" method="post" name="register">
                <h2>Let's Get You Started!</h2>
                <!-- Input fields for registration form -->
                <div class="form-control">
                    <label class="form-label">First Name</label>
                    <input class="register-info" type="text" id="fname" name="fname" value="<?php if (isset($_POST['fname'])) echo $_POST['fname'] ?>" required>
                    <?php
                    if (isset($fnameError)) {
                        echo '<div class="form-error">';
                        echo $fnameError;
                        echo "</div>";
                        unset($fnameError);
                    }
                    ?>
                </div>
                <br>

                <div class="form-control">
                    <label class="form-label">Last Name</label>
                    <input class="register-info" type="text" id="lname" name="lname" value="<?php if (isset($_POST['lname'])) echo $_POST['lname'] ?>" required>
                    <?php
                    if (isset($lnameError)) {
                        echo '<div class="form-error">';
                        echo $lnameError;
                        echo "</div>";
                        unset($lnameError);
                    }
                    ?>
                </div>
                <br>

                <div class="form-control">
                    <label class="form-label">Contact Number (+60)</label>
                    <input class="register-info" type="text" id="mobile" name="mobile" value="<?php if (isset($_POST['mobile'])) echo $_POST['mobile'] ?>" required>
                    <?php
                    if (isset($mobileError)) {
                        echo '<div class="form-error">';
                        echo $mobileError;
                        echo "</div>";
                        unset($mobileError);
                    }
                    ?>
                </div>
                <br>

                <div class="form-control">
                    <label class="form-label">E-mail</label>
                    <input class="register-info" type="email" id="email" name="email" value="<?php if (isset($_POST['email'])) echo $_POST['email'] ?>" required>
                    <?php
                    echo isset($emailError);
                    if (isset($emailError)) {
                        echo '<div class="form-error">';
                        echo $emailError;
                        echo "</div>";
                        unset($emailError);
                    }
                    ?>
                </div>
                <br>

                <div class="form-control">
                    <label class="form-label">Username</label>
                    <input class="register-info" type="text" id="username" name="username" value="<?php if (isset($_POST['username'])) echo $_POST['username'] ?>" required>
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
                    <input class="register-info" type="password" id="password" name="password" value="<?php if (isset($_POST['password'])) echo $_POST['password'] ?>" required>
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

                <div class="form-control">
                    <label class="form-label">Confirm Password</label>
                    <input class="register-info" type="password" id="confirm_password" name="confirm_password" value="<?php if (isset($_POST['confirm_password'])) echo $_POST['confirm_password'] ?>" required>
                    <?php
                    if (isset($passwordConfirmError)) {
                        echo '<div class="form-error">';
                        echo $passwordConfirmError;
                        echo "</div>";
                        unset($passwordConfirmError);
                    }
                    ?>
                </div>
                <br>

                <button type="submit" class="register-button2" value="submit">REGISTER</button><br><br>
                <div class="register-to-login">Already have an account? <a href="login.php">Log in now</a></div>
            </form>
            <div class="register-img"></div>
        </div>

    </div>
    <div style="height:70px; width:100%"></div>
</body>

</html>

<?php
include "footer.php";
?>