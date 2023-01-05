<?php
include "connect-db.php";
connectDB(); // connect to database

include "admin-header.php";

// retreive donationID from donation-list.php
$ID = $_GET['donationID'];

// retreive donation from database 
$query = "SELECT * FROM donations WHERE donationID = $ID";
$result = mysqli_query($handler, $query);
$donation = mysqli_fetch_assoc($result);

// assign variables to each donation detail 
if ($ID < 10) {
    $donationID = "D00" . $donation['donationID'];
} else if ($ID < 100) {
    $donationID = "D0" . $donation['donationID'];
} else {
    $donationID = "D" . $donation['donationID'];
}

$status = $donation['status'];
$userID = $donation['userID'];
$bookTitles = $donation['bookTitles'];
$donationMethod = $donation['donationMethod'];
$donationStreet = $donation['donationStreet'];
$donationCity = $donation['donationCity'];
$donationState = $donation['donationState'];
$donationPostcode = $donation['donationPostcode'];
$donationDate = $donation['donationDate'];
$donationTime = $donation['donationTime'];

// check if form is submitted 
if (isset($_POST['update'])) {
    // perform form validation 
    $errors = 0; // error count

    // validate user ID
    $pattern = "/^[U][0-9]{3}$/";
    if (empty($_POST['user-id'])) {
        $userIDError = "Please enter the User ID for this donation";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['user-id'])) {
        $userIDError = "User ID must be of format UXXX where XXX are numbers";
        $errors += 1;
    } else { // check if user ID exists in database
        // retrieve all existing user IDs from database
        $query = "SELECT userID FROM users WHERE role=2"; // customers only
        $result = mysqli_query($handler, $query);
        $userIDs_db = array();
        while ($row = mysqli_fetch_array($result)) {
            array_push($userIDs_db, $row[0]);
        }

        $userID_donation = $_POST['user-id'];
        $exist = array();
        $userID_test = ltrim($userID_donation, $userID_donation[0]); // remove letter U
        $userID_test = (string)(int)$userID_test; // remove leading zeros

        // check if user ID exists in database
        if (!in_array($userID_test, $userIDs_db)) {
            $userIDError = "User ID does not exist in the database";
            $errors += 1;
        } else {
            $userID = mysqli_real_escape_string($handler, trim($_POST['user-id']));
        }
    }

    // validate book titles
    $pattern = "/^[A-Za-z0-9\s\-_,\.;:()\'&@]+$/";
    if (empty($_POST['book-titles'])) {
        $bookTitlesError = "Please enter the book title(s) for this donation";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['book-titles'])) {
        $bookTitlesError = "Book title(s) can only contain alphabets, numbers, white spaces, and special characters -_,.:;()'&@";
        $errors += 1;
    } else {
        $bookTitles = mysqli_real_escape_string($handler, trim($_POST['book-titles']));
    }

    // validate donation street
    $pattern = "/^[A-Za-z0-9\s,.\'\/]*$/";
    if (empty($_POST['donation-street'])) {
        $streetError = "Please enter the donation street";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['donation-street'])) {
        $streetError = "Donation street can only contain alphabets, numbers, white spaces, and special characters ,.'/";
        $errors += 1;
    } else {
        $donationStreet = mysqli_real_escape_string($handler, trim($_POST['donation-street']));
    }

    // validate donation city
    $pattern = "/^[A-Za-z\s]*$/";
    if (empty($_POST['donation-city'])) {
        $cityError = "Please enter the donation city";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['donation-city'])) {
        $cityError = "Donation city can only contain alphabets and white spaces";
        $errors += 1;
    } else {
        $donationCity = mysqli_real_escape_string($handler, trim($_POST['donation-city']));
    }

    // validate donation state
    if (empty($_POST['donation-state'])) {
        $stateError = "Please enter the donation state";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['donation-state'])) {
        $stateError = "Donation state can only contain alphabets and white spaces";
        $errors += 1;
    } else {
        $donationState = mysqli_real_escape_string($handler, trim($_POST['donation-state']));
    }

    // validate donation postcode
    if (empty($_POST['donation-postcode'])) {
        $postcodeError = "Please enter the donation postcode";
        $errors += 1;
    } else if (strlen((string)$_POST['donation-postcode']) != 5) {
        $postcodeError = "Donation postcode must contain exactly 5 digits";
        $errors += 1;
    } else {
        $donationPostcode = $_POST['donation-postcode'];
    }

    // validate donation date 
    $today = date("Y-m-d");
    if (empty($_POST['donation-date'])) {
        $dateError = "Please enter the donation date";
        $errors += 1;
    } else {
        if ($_POST['status'] == "Complete") { // status = complete
            if ($_POST['donation-date'] > $today) {
                $dateError = "Donation status = Complete, donation date cannot be a future date";
                $errors += 1;
            } else {
                $donationDate = $_POST['donation-date'];
            }
        } else if ($_POST['status'] == "Incomplete") { // status = incomplete
            if ($_POST['donation-date'] < $today) {
                $dateError = "Order status = Incomplete, donation date cannot be a past date";
                $errors += 1;
            } else {
                $donationDate = $_POST['donation-date'];
            }
        }
    }

    // validate donation time
    if (empty($_POST['donation-time'])) {
        $timeError = "Please enter the donation time";
        $errors += 1;
    } else {
        $donationTime_test = (string)$_POST['donation-time'];
        $donationTime_test = str_replace(":", "", $donationTime_test); // remove all :
        if (strlen($donationTime_test) > 4) {
            $donationTime_test = substr($donationTime_test, 0, -2); // remove seconds
        }
        $donationTime_test = (int)$donationTime_test;

        if ($donationTime_test < 800 || $donationTime_test > 1800) {
            $timeError = "Donation time must be within 8:00 AM to 6:00 PM";
            $errors += 1;
        } else {
            $donationTime = $_POST['donation-time'];
        }
    }

    // if no errors found
    if ($errors == 0) {
        $status = mysqli_real_escape_string($handler, trim($_POST['status']));
        $donationMethod = mysqli_real_escape_string($handler, trim($_POST['donation-method']));

        // update donation table
        $query = "UPDATE donations SET
                    status = '" . $status . "', 
                    userID = '" . $userID . "', 
                    bookTitles = '" . $bookTitles . "',
                    donationMethod = '" . $donationMethod . "', 
                    donationStreet = '" . $donationStreet . "', 
                    donationCity = '" . $donationCity . "', 
                    donationState = '" . $donationState . "', 
                    donationPostcode = '" . $donationPostcode . "', 
                    donationDate = '" . $donationDate . "',
                    donationTime = '" . $donationTime . "'
                WHERE donationID =" . $donation['donationID'];
        mysqli_query($handler, $query);

        // redirect admin to donation list page 
        header("Location: donation-list.php?msg=editdonation");
        exit();
    }
} else if (isset($_POST['delete'])) { // delete button clicked
    // delete donation record from database
    $query = "DELETE FROM donations WHERE donationID =" . $donation['donationID'];
    mysqli_query($handler, $query);
    header("Location: donation-list.php?msg=deletedonation");
    exit();
}
?>

<html>

<head>
    <title>NewChapter | Donation Record</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/form-control.css" type="text/css">
</head>

<body id="scroll-bar">
    <div class="left-panel" style="height:800px">
        <div style="height:100px;width:100%"></div>

        <a href="dashboard.php">
            <div class="admin-nav">Dashboard<i class="fa-solid fa-chart-line"></i></div>
        </a>
        <a href="user-list.php">
            <div class="admin-nav">Users<i class="fa-solid fa-users"></i></div>
        </a>
        <a href="book-list.php">
            <div class="admin-nav">Books<i class="fa-solid fa-book"></i></div>
        </a>
        <a href="order-list.php">
            <div class="admin-nav">Orders<i class="fa-solid fa-cart-shopping"></i></div>
        </a>
        <a href="donation-list.php">
            <div class="admin-nav" style="background-color:#006000">Donations<i class="fa-solid fa-hand-holding-dollar"></i></div>
        </a>
        <a href="logout.php">
            <div class="admin-nav admin-logout">Log Out<i class="fa-solid fa-right-from-bracket"></i></div>
        </a>
    </div>

    <div class="right-panel">
        <div class="title-container">D O N A T I O N&emsp;R E C O R D&emsp;#<?php echo $donationID ?></div>
        <div class="summary">
            <div id="edit-div">
                <!-- Form for admin to update donation details-->
                <form id="update-form" method="post" action="donation-record.php?donationID=<?php echo $ID; ?>">
                    <label for="status">Status:</label>
                    <select name="status" id="status">
                        <?php
                        // set default value in drop down list 
                        if (isset($_POST['update'])) {
                            if (isset($_POST['status']) && $_POST['status'] == "Incomplete") {
                                echo "<option value='Incomplete' selected>Incomplete</option>
                                            <option value='Complete'>Complete</option>";
                            } else {
                                echo "<option value='Incomplete'>Incomplete</option>
                                            <option value='Complete' selected>Complete</option>";
                            }
                        } else {
                            if ($status == "Incomplete") {
                                echo "<option value='Incomplete' selected>Incomplete</option>
                                            <option value='Complete'>Complete</option>";
                            } else {
                                echo "<option value='Incomplete'>Incomplete</option>
                                            <option value='Complete' selected>Complete</option>";
                            }
                        }
                        ?>
                    </select><br><br>

                    <div class="form-control">
                        <label for="user-id">User ID:</label>
                        <input class="donation-update-info" type="text" id="user-id" name="user-id" value="<?php if (isset($_POST['user-id'])) echo $_POST['user-id'];
                                                                                                            else echo $userID; ?>" required>
                        <?php
                        if (isset($userIDError)) {
                            echo '<div class="admin-form-error">';
                            echo $userIDError;
                            echo "</div>";
                            unset($userIDError);
                        }
                        ?>
                    </div>
                    <br>

                    <div class="form-control">
                        <label for="book-titles" style="vertical-align: top">Book Title(s):</label>
                        <textarea class="donation-update-info" id="book-titles" name="book-titles" rows="3" cols="50" required><?php if (isset($_POST['book-titles'])) echo $_POST['book-titles'];
                                                                                                                                else echo $bookTitles; ?></textarea>
                        <?php
                        if (isset($bookTitlesError)) {
                            echo '<div class="admin-form-error">';
                            echo $bookTitlesError;
                            echo "</div>";
                            unset($bookTitlesError);
                        }
                        ?>
                    </div>
                    <br>

                    <label for="donation-method">Donation Method:</label>
                    <select name="donation-method" id="donation-method">
                        <?php
                        // set default value in drop down list 
                        if (isset($_POST['update'])) {
                            if (isset($_POST['donation-method']) && $_POST['donation-method'] == "Mail") {
                                echo "<option value='Mail' selected>Mail</option>
                                            <option value='Drop By'>Drop By</option>
                                            <option value='Collect'>Collect</option>";
                            } else if (isset($_POST['donation-method']) && $_POST['donation-method'] == "Drop By") {
                                echo "<option value='Mail'>Mail</option>
                                            <option value='Drop By' selected>Drop By</option>
                                            <option value='Collect'>Collect</option>";
                            } else {
                                echo "<option value='Mail'>Mail</option>
                                            <option value='Drop By'>Drop By</option>
                                            <option value='Collect' selected>Collect</option>";
                            }
                        } else {
                            if ($donationMethod == "Mail") {
                                echo "<option value='Mail' selected>Mail</option>
                                            <option value='Drop By'>Drop By</option>
                                            <option value='Collect'>Collect</option>";
                            } else if ($donationMethod == "Drop By") {
                                echo "<option value='Mail'>Mail</option>
                                            <option value='Drop By' selected>Drop By</option>
                                            <option value='Collect'>Collect</option>";
                            } else {
                                echo "<option value='Mail'>Mail</option>
                                            <option value='Drop By'>Drop By</option>
                                            <option value='Collect' selected>Collect</option>";
                            }
                        }
                        ?>
                    </select><br><br>

                    <div class="form-control">
                        <label for="donation-street" style="vertical-align: top">Donation Street:</label>
                        <textarea class="donation-update-info" id="donation-street" name="donation-street" rows="3" cols="50" required><?php if (isset($_POST['donation-street'])) echo $_POST['donation-street'];
                                                                                                                                        else echo $donationStreet; ?></textarea>
                        <?php
                        if (isset($streetError)) {
                            echo '<div class="admin-form-error">';
                            echo $streetError;
                            echo "</div>";
                            unset($streetError);
                        }
                        ?>
                    </div>
                    <br>

                    <div class="form-control">
                        <label for="donation-city">Donation City:</label>
                        <input class="donation-update-info" type="text" id="donation-city" name="donation-city" value="<?php if (isset($_POST['donation-city'])) echo $_POST['donation-city'];
                                                                                                                        else echo $donationCity; ?>" required>
                        <?php
                        if (isset($cityError)) {
                            echo '<div class="admin-form-error">';
                            echo $cityError;
                            echo "</div>";
                            unset($cityError);
                        }
                        ?>
                    </div>
                    <br>

                    <div class="form-control">
                        <label for="donation-state">Donation State:</label>
                        <input class="donation-update-info" type="text" id="donation-state" name="donation-state" value="<?php if (isset($_POST['donation-state'])) echo $_POST['donation-state'];
                                                                                                                            else echo $donationState; ?>" required>
                        <?php
                        if (isset($stateError)) {
                            echo '<div class="admin-form-error">';
                            echo $stateError;
                            echo "</div>";
                            unset($stateError);
                        }
                        ?>
                    </div>
                    <br>

                    <div class="form-control">
                        <label for="donation-postcode">Donation Postcode:</label>
                        <input class="donation-update-info" type="number" id="donation-postcode" name="donation-postcode" value="<?php if (isset($_POST['donation-postcode'])) echo $_POST['donation-postcode'];
                                                                                                                                    else echo $donationPostcode; ?>" required>
                        <?php
                        if (isset($postcodeError)) {
                            echo '<div class="admin-form-error">';
                            echo $postcodeError;
                            echo "</div>";
                            unset($postcodeError);
                        }
                        ?>
                    </div>
                    <br>

                    <div class="form-control">
                        <label for="donation-date">Donation Date:</label>
                        <input class="donation-update-info" type="date" id="donation-date" name="donation-date" value="<?php if (isset($_POST['donation-date'])) echo $_POST['donation-date'];
                                                                                                                        else echo $donationDate; ?>" required>
                        <?php
                        if (isset($dateError)) {
                            echo '<div class="admin-form-error">';
                            echo $dateError;
                            echo "</div>";
                            unset($dateError);
                        }
                        ?>
                    </div>
                    <br>

                    <div class="form-control">
                        <label for="donation-time">Donation Time:</label>
                        <input class="donation-update-info" type="time" step="60" id="donation-time" name="donation-time" value="<?php if (isset($_POST['donation-time'])) echo $_POST['donation-time'];
                                                                                                                                    else echo $donationTime; ?>" required>
                        <?php
                        if (isset($timeError)) {
                            echo '<div class="admin-form-error">';
                            echo $timeError;
                            echo "</div>";
                            unset($timeError);
                        }
                        ?>
                    </div>
                    <br>

                    <input class="donation-update-btn" type="submit" name="update" value="Update Record">
                    <input class="delete-btn" type="submit" name="delete" value="Delete Record">
                </form>
            </div>
            <a href="donation-list.php"><button type="button">Close</button></a>
        </div>
    </div>
</body>

</html>