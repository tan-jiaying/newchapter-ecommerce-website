<?php
include "connect-db.php";
connectDB(); // connect to database 

// retreive userID from user-list.php
$ID = $_GET['userID'];

// retreive user from database 
$query = "SELECT * FROM users WHERE userID = $ID";
$result = mysqli_query($handler, $query);
$user = mysqli_fetch_assoc($result);

include "admin-header.php";

// assign variables to each customer detail 
if ($ID < 10) {
    $userID = "U00" . $user['userID'];
} else if ($ID < 100) {
    $userID = "U0" . $user['userID'];
} else {
    $userID = "U" . $user['userID'];
}

$fname = $user['fname'];
$lname = $user['lname'];
$mobile = $user['contactNum'];
$email = $user['email'];
$role = $user['role'];

// check if form is submitted 
if (isset($_POST['update'])) {
    // perform form validation 
    $errors = 0; // error count

    // validate first name
    $pattern = "/^[A-Za-z\s\.\'@-]*$/";
    if (empty($_POST['fname'])) {
        $fnameError = "Please enter first name";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['fname'])) {
        $fnameError = "First name can only contain alphabets, white spaces, and special characters -.'@";
        $errors += 1;
    } else {
        $fname = mysqli_real_escape_string($handler, trim($_POST['fname']));
    }

    // validate last name
    if (empty($_POST['lname'])) {
        $lnameError = "Please enter last name";
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
        $mobileError = "Please enter contact number";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['mobile'])) {
        $mobileError = "Contact number must be of format +601[02346789]-XXXXXXX or +6011-XXXXXXXX";
        $errors += 1;
    } else {
        $mobile = mysqli_real_escape_string($handler, trim($_POST['mobile']));
    }

    // validate email
    $pattern = "/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/";
    // retieve existing emails from database 
    $query = "SELECT email FROM users";
    $result = mysqli_query($handler, $query);
    $emails = array();
    while ($row = mysqli_fetch_array($result)) {
        array_push($emails, $row[0]);
    }
    if (empty($_POST['email'])) {
        $emailError = "Please enter email";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['email'])) {
        $emailError = "Invalid email format";
        $errors += 1;
    } else if (in_array($_POST['email'], $emails) && ($_POST['email'] != $email)) { // check if email already exists 
        $emailError = "Email is already taken, please use another email";
        $errors += 1;
    } else {
        $email = mysqli_real_escape_string($handler, trim($_POST['email']));
    }

    // validate role 
    if (empty($_POST['role'])) {
        $roleError = "Please enter role";
        $errors += 1;
    } else if ($_POST['role'] != 1 && $_POST['role'] != 2) {
        $roleError = "Role must be either 1 (admin) or 2 (customer)";
        $errors += 1;
    } else {
        $role = $_POST['role'];
    }


    // if no errors found 
    if ($errors == 0) {
        // update customer table
        $query = "UPDATE users SET 
                    fname = '" . $fname . "', 
                    lname = '" . $lname . "', 
                    contactNum = '" . $mobile . "', 
                    email = '" . $email . "',
                    role = '" . $role . "'
                WHERE userID =" . $user['userID'];
        mysqli_query($handler, $query);

        // redirect admin to user list page
        header("Location: user-list.php?msg=edituser");
        exit();
    }
} else if (isset($_POST['delete'])) { // delete button clicked
    // delete customer record from database
    $query = "DELETE FROM users WHERE userID =" . $user['userID'];
    mysqli_query($handler, $query);
    header("Location: user-list.php?msg=deleteuser");
    exit();
}
?>

<html>

<head>
    <title>NewChapter | User Record</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/form-control.css" type="text/css">
</head>

<body id="scroll-bar">
    <div class="left-panel"  style="height: 750px">
        <div style="height:100px;width:100%"></div>

        <a href="dashboard.php">
            <div class="admin-nav">Dashboard<i class="fa-solid fa-chart-line"></i></div>
        </a>
        <a href="user-list.php">
            <div class="admin-nav" style="background-color:#006000">Users<i class="fa-solid fa-users"></i></div>
        </a>
        <a href="book-list.php">
            <div class="admin-nav">Books<i class="fa-solid fa-book"></i></div>
        </a>
        <a href="order-list.php">
            <div class="admin-nav">Orders<i class="fa-solid fa-cart-shopping"></i></div>
        </a>
        <a href="donation-list.php">
            <div class="admin-nav">Donations<i class="fa-solid fa-hand-holding-dollar"></i></div>
        </a>
        <a href="logout.php">
            <div class="admin-nav admin-logout">Log Out<i class="fa-solid fa-right-from-bracket"></i></div>
        </a>
    </div>

    <div class="right-panel">
        <div class="title-container">U S E R&emsp;R E C O R D&emsp;#<?php echo $userID ?></div>
        <div class="summary">
            <div id="edit-div">
                <!--Form for admin to update user details-->
                <!--Note: admin not allowed to edit user credentials, namely username and password-->
                <form id="update-form" method="post" action="user-record.php?userID=<?php echo $ID; ?>">
                    <div class="form-control">
                        <label for="fname">First Name:</label>
                        <input class="user-update-info" type="text" id="fname" name="fname" value="<?php if (isset($_POST['fname'])) echo $_POST['fname'];
                                                                                                    else echo $fname; ?>" required>
                        <?php
                        if (isset($fnameError)) {
                            echo '<div class="admin-form-error">';
                            echo $fnameError;
                            echo "</div>";
                            unset($fnameError);
                        }
                        ?>
                    </div>
                    <br>

                    <div class="form-control">
                        <label for="lname">Last Name:</label>
                        <input class="user-update-info" type="text" id="lname" name="lname" value="<?php if (isset($_POST['lname'])) echo $_POST['lname'];
                                                                                                    else echo $lname; ?>" required>
                        <?php
                        if (isset($lnameError)) {
                            echo '<div class="admin-form-error">';
                            echo $lnameError;
                            echo "</div>";
                            unset($lnameError);
                        }
                        ?>
                    </div>
                    <br>

                    <div class="form-control">
                        <label for="mobile">Contact Number:</label>
                        <input class="user-update-info" type="text" id="mobile" name="mobile" value="<?php if (isset($_POST['mobile'])) echo $_POST['mobile'];
                                                                                                        else echo $mobile; ?>" required>
                        <?php
                        if (isset($mobileError)) {
                            echo '<div class="admin-form-error">';
                            echo $mobileError;
                            echo "</div>";
                            unset($mobileError);
                        }
                        ?>
                    </div>
                    <br>

                    <div class="form-control">
                        <label for="email">E-mail:</label>
                        <input class="user-update-info" type="email" id="email" name="email" value="<?php if (isset($_POST['email'])) echo $_POST['email'];
                                                                                                    else echo $email; ?>" required>
                        <?php
                        if (isset($emailError)) {
                            echo '<div class="admin-form-error">';
                            echo $emailError;
                            echo "</div>";
                            unset($emailError);
                        }
                        ?>
                    </div>
                    <br>

                    <div class="form-control">
                        <label for="role">Role:</label>
                        <input class="user-update-info" type="number" id="role" name="role" value="<?php if (isset($_POST['role'])) echo $_POST['role'];
                                                                                                    else echo $role; ?>" required>
                        <?php
                        if (isset($roleError)) {
                            echo '<div class="admin-form-error">';
                            echo $roleError;
                            echo "</div>";
                            unset($roleError);
                        }
                        ?>
                        <br><small>*1 = admin, 2 = customer</small>
                    </div>
                    <br><br>

                    <input class="user-update-btn" type="submit" name="update" value="Update Record">
                    <input class="delete-btn" type="submit" name="delete" value="Delete Record">
                </form>
            </div>
            <a href="user-list.php"><button type="button">Close</button></a>
        </div>
    </div>

</body>

</html>