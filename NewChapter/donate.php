<?php
session_start();
include "connect-db.php";
connectDB(); // connect to database 

include "header.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_GET['action'] == 'mail' || $_GET['action'] == 'dropby') {
        $errors = 0;
        // validate book list 1
        $pattern = "/^[A-Za-z0-9\s\-_,\.;:()\'@]+$/";
        if (isset($_POST['donation_list'])) {
            if (empty($_POST['donation_list'])) {
                $bookListError = "Please enter at least 1 book title";
                $errors += 1;
            } else if (!preg_match($pattern, $_POST['donation_list'])) {
                $bookListError = "Book title(s) can only contain alphabets, numbers, white spaces, and special characters -_,.:;()'@";
                $errors += 1;
            } else {
                $_SESSION["donation_list"] = mysqli_real_escape_string($handler, trim($_POST['donation_list']));
            }
        }
        // validate book list 2
        $pattern = "/^[A-Za-z0-9\s\-_,\.;:()\'@]+$/";
        if (isset($_POST['donation_list_2'])) {
            if (empty($_POST['donation_list_2'])) {
                $bookList2Error = "Please enter at least 1 book title";
                $errors += 1;
            } else if (!preg_match($pattern, $_POST['donation_list_2'])) {
                $bookList2Error = "Book title(s) can only contain alphabets, numbers, white spaces, and special characters -_,.:;()'@";
                $errors += 1;
            } else {
                $_SESSION["donation_list"] = mysqli_real_escape_string($handler, trim($_POST['donation_list_2']));
            }
        }

        if ($_GET['action'] == "mail") {
            $_SESSION["donation_method"] = "Mail";
        } else if ($_GET['action'] == "dropby") {
            $_SESSION["donation_method"] = "Drop By";
        } else if ($_GET['action'] == "collect") {
            $_SESSION["donation_method"] = "Collect";
        }

        $_SESSION["pickup_location"] = $_POST["pickup_location"];
        $_SESSION["donation_date"] = $_POST["date"];
        $_SESSION["donation_time"] = $_POST["time"];

        if ($_SESSION["pickup_location"] == "Subang Jaya") {
            $_SESSION["address"] = "12, Jalan USJ 11/4b, USJ 11";
            $_SESSION["city"] = "Subang Jaya";
            $_SESSION["state"] = "Selangor";
            $_SESSION["postcode"] = 47620;
        } else if ($_SESSION["pickup_location"] == "Kota Damansara") {
            $_SESSION["address"] = "37, Jalan PJU 5/10, Seksyen 13, Kota Damansara";
            $_SESSION["city"] = "Petaling Jaya";
            $_SESSION["state"] = "Selangor";
            $_SESSION["postcode"] = 47810;
        } else if ($_SESSION["pickup_location"] == "Suria KLCC") {
            $_SESSION["address"] = "Lot 107, Level 1, Kuala Lumpur City Centre";
            $_SESSION["city"] = "Kuala Lumpur";
            $_SESSION["state"] = "Selangor";
            $_SESSION["postcode"] = 50088;
        }

        if ($errors == 0) {
            header("Location: donate.php?action=submit");
            exit();
        }
    } else if ($_GET['action'] == 'collect') {
        $errors = 0;
        // validate book list 3
        $pattern = "/^[A-Za-z0-9\s\-_,\.;:()\'@]+$/";
        if (isset($_POST['donation_list_3'])) {
            if (empty($_POST['donation_list_3'])) {
                $bookList3Error = "Please enter at least 1 book title";
                $errors += 1;
            } else if (!preg_match($pattern, $_POST['donation_list_3'])) {
                $bookList3Error = "Book title(s) can only contain alphabets, numbers, white spaces, and special characters -_,.:;()'@";
                $errors += 1;
            } else {
                $_SESSION["donation_list"] = mysqli_real_escape_string($handler, trim($_POST['donation_list_3']));
            }
        }

        // validate street
        $pattern = "/^[A-Za-z0-9\s,.\'\/]*$/";
        if (isset($_POST['address'])) {
            if (empty($_POST['address'])) {
                $streetError = "Please enter Street";
                $errors += 1;
            } else if (!preg_match($pattern, $_POST['address'])) {
                $streetError = "Street can only contain alphabets, numbers, white spaces, and special characters ,.'/";
                $errors += 1;
            } else {
                $_SESSION["address"] = mysqli_real_escape_string($handler, trim($_POST['address']));
            }
        }

        // validate city
        $pattern = "/^[A-Za-z\s\.\'@-]*$/";
        if (isset($_POST['city'])) {
            if (empty($_POST['city'])) {
                $cityError = "Please enter City";
                $errors += 1;
            } else if (!preg_match($pattern, $_POST['city'])) {
                $cityError = "City can only contain alphabets, white spaces, and special characters -.'@";
                $errors += 1;
            } else {
                $_SESSION["city"] = mysqli_real_escape_string($handler, trim($_POST['city']));;
            }
        }

        // validate state
        if (isset($_POST['state'])) {
            if (empty($_POST['state'])) {
                $stateError = "Please enter State";
                $errors += 1;
            } else if (!preg_match($pattern, $_POST['state'])) {
                $stateError = "State can only contain alphabets, white spaces, and special characters -.'@";
                $errors += 1;
            } else {
                $_SESSION["state"] = mysqli_real_escape_string($handler, trim($_POST['state']));
            }
        }

        // validate postcode
        $pattern = "/^[0-9]{5}$/";
        if (isset($_POST['postcode'])) {
            if (empty($_POST['postcode'])) {
                $postcodeError = "Please enter Postcode";
                $errors += 1;
            } else if (!preg_match($pattern, $_POST['postcode'])) {
                $postcodeError = "Postcode must contain exactly 5 digits";
                $errors += 1;
            } else {
                $_SESSION["postcode"] = $_POST["postcode"];
            }
        }
        if ($errors == 0) {
            header("Location: donate.php?action=submit");
            exit();
        }
    }
}
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'submit') {
        echo "submitting";
        // insert order into table
        $status = "Incomplete";
        $userByID = $db_handle->runQuery("SELECT * FROM users WHERE username='" . $_SESSION["username"] . "'");
        //getting the userID of the user
        $userArray = array($userByID[0]["userID"] => array(
            'userID' => $userByID[0]["userID"]
        ));
        $userID = $userByID[0]["userID"];
        if ($userID < 10) {
            $userID = "U00" . $userID;
        } else if ($userID < 100) {
            $userID = "U0" . $userID;
        } else {
            $userID = "U" . $userID;
        }
        $bookTitles = $_SESSION["donation_list"];
        $donationMethod = $_SESSION["donation_method"];
        $donationStreet = $_SESSION["address"];
        $donationCity = $_SESSION["city"];
        $donationState = $_SESSION["state"];
        $donationPostcode = $_SESSION["postcode"];
        $donationDate = $_SESSION["donation_date"];
        $donationTime = $_SESSION["donation_time"];

        $query = "INSERT INTO donations (status, userID, bookTitles, donationMethod, donationStreet, donationCity, donationState, donationPostcode, donationDate, donationTime)
                    VALUES ('$status', '$userID', '$bookTitles', '$donationMethod', '$donationStreet', '$donationCity', '$donationState', '$donationPostcode', '$donationDate', '$donationTime')";
        mysqli_query($handler, $query);

        header('Location: donate.php?action=submitted');
        exit();
    } else if ($_GET['action'] == 'submitted') {
        echo "<div id='order-confirmation'><i class='fa-regular fa-circle-check'></i><br>";
        echo "<h3 style='margin-top:20px'>Thanks for your donation!<h3></div>";
    }
}

?>

<html>

<head>
    <title>NewChapter | Donation</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css">
    <script src="https://kit.fontawesome.com/ffaaf7e5e9.js" crossorigin="anonymous"></script>
</head>

<body id="scroll-bar">

    <div class="wrapper">
        <div class="title-container">D O N A T E</div>
        <div class="donation-container">
            <!-- <img src="img/donation-image/donation-bg.jpg"> -->
            <h2>Interested in donating your old books and making a difference?</h2>
            <h3>Here are a few options for you:</h3>
            <a style="text-decoration:none" href="donate.php?action=mail">
                <div id="donate-mail-btn" value="mail" class="donation-btn" style="color: darkblue">
                    <i class="fa-solid fa-angle-right donation"></i>
                    <b>Mail&emsp;<i class="fa-solid fa-paper-plane"></i></i></b><br>
                    You can directly mail your old books to us.
                </div>
            </a>

            <div id="donate-mail" style="display:none">
                <form action="donate.php?action=mail" method="post">
                    <h4>Title of Books for Donation</h4>
                    <div class="form-control">
                        <label class="form-label" for="donation_list">Book Titles</label>
                        <input class="register-info" type="text" id="donation_list" name="donation_list" maxlength="50" value="<?php if (isset($_POST['donation_list'])) echo $_POST['donation_list'] ?>" placeholder="Book 1, Book 2, Book 3, ...">
                        <?php
                        if (isset($bookListError)) {
                            echo '<div class="form-error">';
                            echo $bookListError;
                            echo "</div>";
                            unset($bookListError);
                        }
                        ?>
                    </div>
                    <h4>Select which branch you want to mail to:</h4>
                    <select name="pickup_location" id="pickup-location" required>
                        <option value="" disabled selected hidden>Please select the preferred branch</option>
                        <option value="Subang Jaya" <?php if (isset($_POST['pickup_location']) && $_POST['pickup_location'] == "Subang Jaya") echo 'selected="selected"'; ?>>New Chapter Bookstore @ Subang Jaya</option>
                        <option value="Kota Damansara" <?php if (isset($_POST['pickup_location']) && $_POST['pickup_location'] == "Kota Damansara") echo 'selected="selected"'; ?>>New Chapter Bookstore @ Kota Damansara</option>
                        <option value="Suria KLCC" <?php if (isset($_POST['pickup_location']) && $_POST['pickup_location'] == "Suria KLCC") echo 'selected="selected"'; ?>>New Chapter Bookstore @ Suria KLCC</option>
                    </select>
                    <div class="form-control">
                        <label class="form-label" for="date">Mail Date</label>
                        <input class="register-info" type="date" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" value="<?php if (isset($_POST['date'])) echo $_POST['date'] ?>" required>
                        <?php
                        ?>
                    </div>
                    <div class="form-control">
                        <label class="form-label" for="time">Mail Time</label>
                        <input class="register-info" type="time" id="time" name="time" min="08:00" max="18:00" value="<?php if (isset($_POST['time'])) echo $_POST['time'] ?>" required>
                    </div>
                    <button class="submit-donation" type="submit">Submit Donation</button>
                </form>
            </div>

            <br>
            <a style="text-decoration:none" href="donate.php?action=dropby">
                <div id="donate-dropby-btn" value="dropby" class="donation-btn" style="color: darkblue">
                    <i class="fa-solid fa-angle-right donation"></i>
                    <b>Drop By&emsp;<i class="fa-solid fa-inbox"></i></b><br>
                    Drop by our nearest store and pass your books to us.
                </div>
            </a>

            <div id="donate-dropby" style="display:none">
                <form action="donate.php?action=dropby" method="post">
                    <h4>Title of Books for Donation</h4>
                    <div class="form-control">
                        <label class="form-label" for="donation_list_2">Book Titles</label>
                        <input class="register-info" type="text" id="donation_list_2" name="donation_list_2" maxlength="50" value="<?php if (isset($_POST['donation_list_2'])) echo $_POST['donation_list_2'] ?>" placeholder="Book 1, Book 2, Book 3, ...">
                        <?php
                        if (isset($bookList2Error)) {
                            echo '<div class="form-error">';
                            echo $bookList2Error;
                            echo "</div>";
                            unset($bookList2Error);
                        }
                        ?>
                    </div>
                    <h4>Select which branch you want to drop by:</h4>
                    <select name="pickup_location" id="pickup-location" required>
                        <option value="" disabled selected hidden>Please select the preferred branch</option>
                        <option value="Subang Jaya" <?php if (isset($_POST['pickup_location']) && $_POST['pickup_location'] == "Subang Jaya") echo 'selected="selected"'; ?>>New Chapter Bookstore @ Subang Jaya</option>
                        <option value="Kota Damansara" <?php if (isset($_POST['pickup_location']) && $_POST['pickup_location'] == "Kota Damansara") echo 'selected="selected"'; ?>>New Chapter Bookstore @ Kota Damansara</option>
                        <option value="Suria KLCC" <?php if (isset($_POST['pickup_location']) && $_POST['pickup_location'] == "Suria KLCC") echo 'selected="selected"'; ?>>New Chapter Bookstore @ Suria KLCC</option>
                    </select>
                    <div class="form-control">
                        <label class="form-label" for="date">Drop by Date</label>
                        <input class="register-info" type="date" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" value="<?php if (isset($_POST['date'])) echo $_POST['date'] ?>" required>
                        <?php
                        ?>
                    </div>
                    <div class="form-control">
                        <label class="form-label" for="time">Drop by Time</label>
                        <input class="register-info" type="time" id="time" name="time" min="08:00" max="18:00" value="<?php if (isset($_POST['time'])) echo $_POST['time'] ?>" required>
                    </div>
                    <button class="submit-donation" type="submit">Submit Donation</button>
                </form>
            </div>

            <br>
            <a style="text-decoration:none" href="donate.php?action=collect">
                <div id="donate-collect-btn" value="collect" class="donation-btn" style="color: darkblue">
                    <i class="fa-solid fa-angle-right donation"></i>
                    <b>Collect&emsp;<i class="fa-solid fa-truck-fast"></i></b><br>
                    We can come to you to collect your books.
                </div>
            </a>

            <div id="donate-collect" style="display:none">
                <form action="donate.php?action=collect" method="post">
                    <h4>Title of Books for Donation</h4>
                    <div class="form-control">
                        <label class="form-label" for="donation_list_3">Book Titles</label>
                        <input class="register-info" type="text" id="donation_list_3" name="donation_list_3" maxlength="50" value="<?php if (isset($_POST['donation_list_3'])) echo $_POST['donation_list_3'] ?>" placeholder="Book 1, Book 2, Book 3, ...">
                        <?php
                        if (isset($bookList3Error)) {
                            echo '<div class="form-error">';
                            echo $bookList3Error;
                            echo "</div>";
                            unset($bookList3Error);
                        }
                        ?>
                    </div>
                    <h4>Enter Collection Address</h4>
                    <div class="form-control">
                        <label class="form-label" for="address">Street</label>
                        <input class="register-info" type="text" id="address" name="address" maxlength="50" value="<?php if (isset($_POST['address'])) echo $_POST['address'] ?>">
                        <?php
                        if (isset($streetError)) {
                            echo '<div class="form-error">';
                            echo $streetError;
                            echo "</div>";
                            unset($streetError);
                        }
                        ?>
                    </div>
                    <div class="form-control">
                        <label class="form-label" for="city">City</label>
                        <input class="register-info" type="text" id="city" name="city" maxlength="50" value="<?php if (isset($_POST['city'])) echo $_POST['city'] ?>">
                        <?php
                        if (isset($cityError)) {
                            echo '<div class="form-error">';
                            echo $cityError;
                            echo "</div>";
                            unset($cityError);
                        }
                        ?>
                    </div>
                    <div class="form-control">
                        <label class="form-label" for="state">State</label>
                        <input class="register-info" type="text" id="state" name="state" maxlength="50" value="<?php if (isset($_POST['state'])) echo $_POST['state'] ?>">
                        <?php
                        if (isset($stateError)) {
                            echo '<div class="form-error">';
                            echo $stateError;
                            echo "</div>";
                            unset($stateError);
                        }
                        ?>
                    </div>
                    <div class="form-control">
                        <label class="form-label" for="postcode">Postcode</label>
                        <input class="register-info" type="text" id="postcode" name="postcode" length="5" value="<?php if (isset($_POST['postcode'])) echo $_POST['postcode'] ?>">
                        <?php
                        if (isset($postcodeError)) {
                            echo '<div class="form-error">';
                            echo $postcodeError;
                            echo "</div>";
                            unset($postcodeError);
                        }
                        ?>
                    </div>
                    <div class="form-control">
                        <label class="form-label" for="date">Collection Date</label>
                        <input class="register-info" type="date" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" value="<?php if (isset($_POST['date'])) echo $_POST['date'] ?>" required>
                    </div>
                    <div class="form-control">
                        <label class="form-label" for="time">Collection Time</label>
                        <input class="register-info" type="time" id="time" name="time" min="08:00" max="18:00" value="<?php if (isset($_POST['time'])) echo $_POST['time'] ?>" required>
                    </div>
                    <button class="submit-donation" type="submit">Submit Donation</button>
                </form>
            </div>
            <br>
        </div>
    </div>
    <div style="height:100px;width:100%"></div>

    <script>
        function donateOption(option) {
            if (option == "mail") {
                document.getElementById("donate-mail-btn").style.border = "3px solid green";
                document.getElementById("donate-dropby-btn").style.border = "2px solid lightgray";
                document.getElementById("donate-collect-btn").style.border = "2px solid lightgray";
                var fields = document.getElementById("donate-mail").getElementsByTagName('*');
                for (var i = 0; i < fields.length; i++) {
                    fields[i].disabled = false;
                }
                var fields = document.getElementById("donate-dropby").getElementsByTagName('*');
                for (var i = 0; i < fields.length; i++) {
                    fields[i].disabled = true;
                }
                var fields = document.getElementById("donate-collect").getElementsByTagName('*');
                for (var i = 0; i < fields.length; i++) {
                    fields[i].disabled = true;
                }
                document.getElementById("donate-mail").style.display = "block";
                document.getElementById("donate-collect").style.display = "none";
                document.getElementById("donate-dropby").style.display = "none";
            } else if (option == "dropby") {
                document.getElementById("donate-dropby-btn").style.border = "3px solid green";
                document.getElementById("donate-collect-btn").style.border = "2px solid lightgray";
                document.getElementById("donate-mail-btn").style.border = "2px solid lightgray";
                var fields = document.getElementById("donate-dropby").getElementsByTagName('*');
                for (var i = 0; i < fields.length; i++) {
                    fields[i].disabled = false;
                }
                var fields = document.getElementById("donate-mail").getElementsByTagName('*');
                for (var i = 0; i < fields.length; i++) {
                    fields[i].disabled = true;
                }
                var fields = document.getElementById("donate-collect").getElementsByTagName('*');
                for (var i = 0; i < fields.length; i++) {
                    fields[i].disabled = true;
                }
                document.getElementById("donate-dropby").style.display = "block";
                document.getElementById("donate-collect").style.display = "none";
                document.getElementById("donate-mail").style.display = "none";
            } else if (option == "collect") {
                document.getElementById("donate-collect-btn").style.border = "3px solid green";
                document.getElementById("donate-dropby-btn").style.border = "2px solid lightgray";
                document.getElementById("donate-mail-btn").style.border = "2px solid lightgray";
                var fields = document.getElementById("donate-collect").getElementsByTagName('*');
                for (var i = 0; i < fields.length; i++) {
                    fields[i].disabled = false;
                }
                var fields = document.getElementById("donate-mail").getElementsByTagName('*');
                for (var i = 0; i < fields.length; i++) {
                    fields[i].disabled = true;
                }
                var fields = document.getElementById("donate-dropby").getElementsByTagName('*');
                for (var i = 0; i < fields.length; i++) {
                    fields[i].disabled = true;
                }
                document.getElementById("donate-collect").style.display = "block";
                document.getElementById("donate-dropby").style.display = "none";
                document.getElementById("donate-mail").style.display = "none";
            }
        }
        if (localStorage.getItem('donateOption') == "mail") {
            donateOption("mail");
        } else if (localStorage.getItem('donateOption') == "dropby") {
            donateOption("dropby");
        } else if (localStorage.getItem('donateOption') == "collect") {
            donateOption("collect");
        }

        document.addEventListener("DOMContentLoaded", function(event) {
            var scrollpos = localStorage.getItem('scrollpos');
            if (scrollpos) window.scrollTo(0, scrollpos);
        });

        window.onbeforeunload = function(e) {
            localStorage.setItem('scrollpos', window.scrollY);
        };

        setTimeout(() => {
            const box = document.getElementById('order-confirmation');
            box.classList.add("fadeout-animation");
        }, 2000); //
        setTimeout(() => {
            const box = document.getElementById('order-confirmation');
            box.style.display = 'none';
        }, 3000);
    </script>

    <?php
    if (isset($_GET["action"])) {
        echo '<script type="text/javascript">',
        'elements = document.getElementsByClassName("submit-donation");',
        'for(var x=0; x < elements.length; x++) { elements[x].style.display = "block"; }',
        '</script>';
        if ($_GET["action"] == "mail") {
            $_SESSION["donation_method"] = "Mail";
            echo '<script type="text/javascript">',
            'donateOption("mail");',
            'localStorage.setItem("donate", "mail");',
            '</script>';
        } else if ($_GET["action"] == "dropby") {
            $_SESSION["donation_method"] = "Drop By";
            echo '<script type="text/javascript">',
            'donateOption("dropby");',
            'localStorage.setItem("donate", "dropby");',
            '</script>';
        } else if ($_GET["action"] == "collect") {
            $_SESSION["donation_method"] = "Collect";
            echo '<script type="text/javascript">',
            'donateOption("collect");',
            'localStorage.setItem("donate", "collect");',
            '</script>';
        }
    }

    include "footer.php";
    ?>
</body>

</html>