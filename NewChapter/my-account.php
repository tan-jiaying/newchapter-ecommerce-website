<?php
include "connect-db.php";
connectDB(); // connect to database 

include "header.php";

//Get method for Changing account details/password 
if (!empty($_GET["action"])) {
    $username = $_SESSION["username"];
    switch ($_GET["action"]) {
        case "chgAccountDetails":
            // perform form validation 
            $errors = 0; // error count

            // validate first name 
            $pattern = "/^[A-Za-z\s\.\'@-]*$/";
            if (isset($_POST['fname'])) {
                if (empty($_POST['fname'])) {
                    $fnameError = "Please enter First Name";
                    $errors += 1;
                } else if (!preg_match($pattern, $_POST['fname'])) {
                    $fnameError = "First name can only contain alphabets, white spaces, and special characters -.'@";
                    $errors += 1;
                } else {
                    $fname = mysqli_real_escape_string($handler, trim($_POST['fname']));
                }
            }
            // validate last name
            if (isset($_POST['lname'])) {
                if (empty($_POST['lname'])) {
                    $lnameError = "Please enter Last Name";
                    $errors += 1;
                } else if (!preg_match($pattern, $_POST['lname'])) {
                    $lnameError = "Last name can only contain alphabets, white spaces, and special characters -.'@";
                    $errors += 1;
                } else {
                    $lname = mysqli_real_escape_string($handler, trim($_POST['lname']));
                }
            }
            // validate contact number 
            if (isset($_POST['mobile'])) {
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
            }
            // validate email
            if (isset($_POST['email'])) {
                $pattern = "/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/";
                // retieve existing emails from database 
                $query = "SELECT email FROM users";
                $result = mysqli_query($handler, $query);
                $emails = array();
                while ($row = mysqli_fetch_array($result)) {
                    array_push($emails, $row[0]);
                }
                if (empty($_POST['email'])) {
                    $mobileError = "Please enter E-mail Address";
                    $errors += 1;
                } else if (!preg_match($pattern, $_POST['email'])) {
                    $emailError = "Invalid email format";
                    $errors += 1;
                } else {
                    $email = mysqli_real_escape_string($handler, trim($_POST['email']));
                }
            }
            if ($errors == 0) {
                // insert inputs into users table
                $query = "UPDATE users SET fname='$fname', lname='$lname', contactNum='$mobile', email='$email' WHERE username='$username'";
                mysqli_query($handler, $query);

                // display alert message
                echo '<script>alert("Account details have been updated")</script>';
            }
            break;
        case "chgPassword":
            // perform form validation 
            $errors = 0; // error count

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

            if ($errors == 0) {
                // insert inputs into users table
                $query = "UPDATE users SET password='$password' WHERE username='$username'";
                mysqli_query($handler, $query);
                // display alert message
                echo '<script>alert("Password has been updated")</script>';
            }
            break;
        case "empty":
            break;
    }
}
?>
<html>

<head>
    <title>NewChapter | My Account</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css">
</head>
<style>
    .form-error {
        margin: 0px 10px;
    }
</style>

<body id="scroll-bar">
    <?php
    $user_detail = $db_handle->runQuery("SELECT * FROM users WHERE username='" . $_SESSION["username"] . "'");
    ?>
    <div class="wrapper">
        <div class="title-container">M Y&emsp;A C C O U N T</div>
        <div class="cart-div">
            <h1>Account Details</h1>
            <!-- Form for user to update profile-->
            <form action="my-account.php?action=chgAccountDetails" id="update-detail-form" method="post">
                <table class="update-form-table">
                    <tr>
                        <b>
                            <td><b>Username:</b></td>
                            <td><?php echo $user_detail[0]["username"]; ?></td>
                    </tr>
                    <tr>
                        <td><b>First Name:</b></td>
                        <td>
                            <div class="form-control">
                                <input class="update-info" type="text" id="fname" name="fname" value="<?php if (isset($_POST['fname'])) echo $_POST['fname'];
                                                                                                else echo $user_detail[0]["fname"]; ?>" required>
                                <?php
                                if (isset($fnameError)) {
                                    echo '<div class="form-error">';
                                    echo $fnameError;
                                    echo "</div>";
                                    unset($fnameError);
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Last Name:</b></td>
                        <td>
                            <div class="form-control">
                                <input class="update-info" type="text" id="lname" name="lname" value="<?php if (isset($_POST['lname'])) echo $_POST['lname'];
                                                                                                else echo $user_detail[0]["lname"]; ?>" required>
                                <?php
                                if (isset($lnameError)) {
                                    echo '<div class="form-error">';
                                    echo $lnameError;
                                    echo "</div>";
                                    unset($lnameError);
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Contact Number:</b></td>
                        <td>
                            <div class="form-control">
                                <input class="update-info" type="text" id="mobile" name="mobile" value="<?php if (isset($_POST['mobile'])) echo $_POST['mobile'];
                                                                                                else echo $user_detail[0]["contactNum"]; ?>" required>
                                <?php
                                if (isset($mobileError)) {
                                    echo '<div class="form-error">';
                                    echo $mobileError;
                                    echo "</div>";
                                    unset($mobileError);
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><b>E-mail:</b></td>
                        <td>
                            <div class="form-control">
                                <input class="update-info" type="email" id="email" name="email" value="<?php if (isset($_POST['email'])) echo $_POST['email'];
                                                                                                else echo $user_detail[0]["email"]; ?>" required>
                                <?php
                                if (isset($emailError)) {
                                    echo '<div class="form-error">';
                                    echo $emailError;
                                    echo "</div>";
                                    unset($emailError);
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input class="update-btn" type="submit" value="Update Account Details"></td>
                    </tr>
                </table>
            </form>
            <form action="my-account.php?action=chgPassword" id="update-password-form" method="post">
                <table class="update-form-table">
                    <tr>
                        <td><b>New Password:</b></td>
                        <td>
                            <div class="form-control">
                                <input class="update-info" type="password" id="password" name="password" 
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
                        </td>
                    </tr>
                    <tr>
                        <td><b>Confirm New Password:</b></td>
                        <td>
                            <div class="form-control">
                                <input class="update-info" type="password" id="confirm_password" name="confirm_password" value="<?php if (isset($_POST['confirm_password'])) echo $_POST['confirm_password'] ?>" required>
                                <?php
                                if (isset($passwordConfirmError)) {
                                    echo '<div class="form-error">';
                                    echo $passwordConfirmError;
                                    echo "</div>";
                                    unset($passwordConfirmError);
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input class="update-btn" type="submit" value="Update Password"></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <div style="height:100px;width:100%"></div>
    <?php
    include "footer.php";
    ?>
</body>

</html>