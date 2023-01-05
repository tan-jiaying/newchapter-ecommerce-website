<?php
session_start();
include "connect-db.php";
connectDB(); // connect to database 

include "header.php";

if (isset($_POST["payment_method"])) {
    $_SESSION["payment_method"] = $_POST["payment_method"];
}
if (isset($_POST["address"])) {
    $_SESSION["address"] = $_POST["address"];
}
if (isset($_POST["city"])) {
    $_SESSION["city"] = $_POST["city"];
}
if (isset($_POST["state"])) {
    $_SESSION["state"] = $_POST["state"];
}
if (isset($_POST["postcode"])) {
    $_SESSION["postcode"] = $_POST["postcode"];
}
if (isset($_POST["date"])) {
    $_SESSION["date"] = $_POST["date"];
}
if (isset($_POST["time"])) {
    $_SESSION["time"] = $_POST["time"];
}
if (isset($_POST["pickup_location"])) {
    $_SESSION["pickup_location"] = $_POST["pickup_location"];
}
if (isset($_SESSION["pickup_location"])) {
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
}


if (isset($_GET['action'])) {
    if ($_GET['action'] == 'submit') {
        // perform form validation 
        $errors = 0; // error count

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
                $_SESSION["city"] = mysqli_real_escape_string($handler, trim($_POST['city']));
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

        // validate contact number 
        $pattern = "/^(\+?6?01)[02-46-9]-*[0-9]{7}$|^(\+?6?01)[1]-*[0-9]{8}$/";
        if (isset($_POST['phone'])) {
            if (empty($_POST['phone'])) {
                $mobileError = "Please enter Contact Number";
            } else if (!preg_match($pattern, $_POST['phone'])) {
                $mobileError = "Contact number must be of format +601[02346789]-XXXXXXX or +6011-XXXXXXXX";
                $errors += 1;
            } else {
                $_SESSION["phone"] = mysqli_real_escape_string($handler, trim($_POST['phone']));
            }
        }

        // validate card name 
        $pattern = "/^[A-Za-z\s\.\'@-]*$/";
        if (empty($_POST['card_name'])) {
            $cardnameError = "Please enter Name on Card";
        } else if (!preg_match($pattern, $_POST['card_name'])) {
            $cardnameError = "Name on Card can only contain alphabets, white spaces, and special characters -.'@";
            $errors += 1;
        }

        // validate card number 
        $pattern = "/^[0-9]{16}$/";
        if (empty($_POST['card_num'])) {
            $cardnumError = "Please enter Card Number";
        } else if (!preg_match($pattern, $_POST['card_num'])) {
            $cardnumError = "Card Number can only be 16-digit numbers";
            $errors += 1;
        }

        // validate CVV 
        $pattern = "/^[0-9]{3}$/";
        if (empty($_POST['cvv'])) {
            $cvvError = "Please enter CVV";
        } else if (!preg_match($pattern, $_POST['cvv'])) {
            $cvvError = "CVV must be 3 digits long";
            $errors += 1;
        }

        // if no errors found 
        if ($errors == 0) {
            $userByID = $db_handle->runQuery("SELECT * FROM users WHERE username='" . $_SESSION["username"] . "'");
            //getting the userID of the user
            $userArray = array($userByID[0]["userID"] => array(
                'userID' => $userByID[0]["userID"]
            ));

            $status = "Incomplete";
            $checkout_item = $_SESSION["checkout_item"];

            foreach ($checkout_item as $value) {
                if (isset($value["title"])) {
                    $bookByID = $db_handle->runQuery("SELECT bookID FROM books WHERE title='" . $value["title"] . "'");
                    $stockByID = $db_handle->runQuery("SELECT inStock FROM books WHERE title='" . $value["title"] . "'");
                    $bookID = $bookByID[0]["bookID"];
                    $inStock = $stockByID[0]["inStock"];
                    if ($bookID < 10) {
                        $bookCode = "B00" . $bookID;
                    } else if ($bookID < 100) {
                        $bookCode = "B0" . $bookID;
                    } else {
                        $bookCode = "B" . $bookID;
                    }
                    $isChecked = true;
                } else if (isset($value["quantity"]) and $isChecked) {
                    for ($k = 0; $k < $value["quantity"]; $k++) {
                        $inStock -= 1;
                        echo $inStock;
                        echo $bookID;
                        // update book stock in book table
                        mysqli_query($handler, "UPDATE books SET inStock = $inStock WHERE bookID = $bookID");
                        $bookIDs .= $bookCode . ', ';
                    }
                    $isChecked = false;
                }
            }

            $bookIDs = substr($bookIDs, 0, (strlen($bookIDs) - 2));
            $userID = $userByID[0]["userID"];
            if ($userID < 10) {
                $userID = "U00" . $userID;
            } else if ($userID < 100) {
                $userID = "U0" . $userID;
            } else {
                $userID = "U" . $userID;
            }
            $orderTotal = $_SESSION["total_checkout_price"];
            $paymentMethod = $_SESSION["payment_method"];
            date_default_timezone_set('Asia/Singapore');
            $orderDate = date('Y-m-d');
            $deliveryOption = $_SESSION["delivery_option"];
            if (isset($_SESSION["address"])) {
                $deliveryStreet = $_SESSION["address"];
            } else {
                $deliveryStreet = NULL;
            }
            if (isset($_SESSION["city"])) {
                $deliveryCity = $_SESSION["city"];
            } else {
                $deliveryCity = NULL;
            }
            if (isset($_SESSION["state"])) {
                $deliveryState = $_SESSION["state"];
            } else {
                $deliveryState = NULL;
            }
            if (isset($_SESSION["postcode"])) {
                $deliveryPostcode = $_SESSION["postcode"];
            } else {
                $deliveryPostcode = NULL;
            }
            if (isset($_SESSION["date"])) {
                $deliveryDate = date('Y-m-d', strtotime($_SESSION["date"]));
            } else {
                $deliveryDate = NULL;
            }
            if (isset($_SESSION["time"])) {
                $deliveryTime = $_SESSION["time"];
            } else {
                $deliveryTime = NULL;
            }

            // insert order into table
            $query = "INSERT INTO orders (status, bookIDs, userID, orderTotal, paymentMethod, orderDate, deliveryOption, deliveryStreet, deliveryCity, deliveryState, deliveryPostcode, deliveryDate, deliveryTime)
                        VALUES ('$status', '$bookIDs', '$userID', '$orderTotal', '$paymentMethod', '$orderDate', '$deliveryOption', '$deliveryStreet', '$deliveryCity', '$deliveryState', '$deliveryPostcode', '$deliveryDate', '$deliveryTime')";
            mysqli_query($handler, $query);
            $_SESSION['last_id'] = mysqli_insert_id($handler);

            header("Location: purchase-summary.php?action=submit");
            exit();
        }
    }
}
?>

<html>

<head>
    <title>NewChapter | Payment & Delivery</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/progress-bar.css" type="text/css">
    <script src="https://kit.fontawesome.com/ffaaf7e5e9.js" crossorigin="anonymous"></script>
</head>

<body id="scroll-bar">
    <div class="wrapper">
        <!--Form for Payment and Shipping Info-->
        <form id="pay-ship-form" action="payment-delivery.php?action=submit" method="post">
            <div class="title-container">P A Y M E N T&emsp;&&emsp;D E L I V E R Y</div>
            <!--Progress Bar-->
            <div class="cart-div">
                <div class="progress-bar-div">
                    <ul class="progress-bar">
                        <li class="previous">Cart</li>
                        <li class="previous">Checkout</li>
                        <li class="active">Payment</li>
                        <li>Complete</li>
                    </ul>
                </div>
                <br>

                <!--Delivery Information-->
                <div class="delivery-div">
                    <h3>Delivery Information</h3>
                    <a style="text-decoration:none" href="payment-delivery.php?action=self">
                        <div id="self-pick-up" value="selfPickup" class="donation-btn" style="text-align: left">
                            <i class="fa-solid fa-angle-right"></i>
                            <b>Self Pick-up</b><br>
                            Collect your books at any of the preferred locations.
                        </div>
                    </a>
                    <div id="delivery-details-self" style="display:none">
                        <h4>Select Self Pick-up Location</h4>
                        <select name="pickup_location" id="pickup-location" required>
                            <option value="" disabled selected hidden>Please select the preferred branch</option>
                            <option value="Subang Jaya" <?php if (isset($_POST['pickup_location']) && $_POST['pickup_location'] == "Subang Jaya") echo 'selected="selected"'; ?>>New Chapter Bookstore @ Subang Jaya</option>
                            <option value="Kota Damansara" <?php if (isset($_POST['pickup_location']) && $_POST['pickup_location'] == "Kota Damansara") echo 'selected="selected"'; ?>>New Chapter Bookstore @ Kota Damansara</option>
                            <option value="Suria KLCC" <?php if (isset($_POST['pickup_location']) && $_POST['pickup_location'] == "Suria KLCC") echo 'selected="selected"'; ?>>New Chapter Bookstore @ Suria KLCC</option>
                        </select>

                        <div class="form-control">
                            <label class="form-label" for="date">Pick-up Date</label>
                            <input class="register-info" type="date" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" value="<?php if (isset($_POST['date'])) echo $_POST['date'] ?>" required>
                            <?php
                            ?>
                        </div>
                        <div class="form-control">
                            <label class="form-label" for="time">Pick-up Time</label>
                            <input class="register-info" type="time" id="time" name="time" min="08:00" max="18:00" value="<?php if (isset($_POST['time'])) echo $_POST['time'] ?>" required>
                        </div>
                    </div>
                    <br>
                    <a style="text-decoration:none" href="payment-delivery.php?action=standard">
                        <div id="standard-courier" value="standard" class="donation-btn" style="text-align: left">
                            <i class="fa-solid fa-angle-right"></i>
                            <b>Standard Courier</b><br>
                            Additional delivery rates may apply.
                        </div>
                    </a>

                    <div id="delivery-details" style="display:none">
                        <h4>Enter Delivery Address</h4>
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
                            <label class="form-label" for="date">Delivery Date</label>
                            <input class="register-info" type="date" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" value="<?php if (isset($_POST['date'])) echo $_POST['date'] ?>" required>
                        </div>
                        <div class="form-control">
                            <label class="form-label" for="time">Delivery Time</label>
                            <input class="register-info" type="time" id="time" name="time" min="08:00" max="18:00" value="<?php if (isset($_POST['time'])) echo $_POST['time'] ?>" required>
                        </div>
                    </div>
                </div>
                <script>
                    function deliveryOption(option) {
                        if (option == "standard") {
                            document.getElementById("standard-courier").style.border = "3px solid green";
                            document.getElementById("self-pick-up").style.border = "2px solid lightgray";
                            var fields = document.getElementById("delivery-details").getElementsByTagName('*');
                            for (var i = 0; i < fields.length; i++) {
                                fields[i].disabled = false;
                            }
                            var fields = document.getElementById("delivery-details-self").getElementsByTagName('*');
                            for (var i = 0; i < fields.length; i++) {
                                fields[i].disabled = true;
                            }
                            document.getElementById("delivery-details").style.display = "block";
                            document.getElementById("delivery-details-self").style.display = "none";
                        } else if (option == "self") {
                            document.getElementById("self-pick-up").style.border = "3px solid green";
                            document.getElementById("standard-courier").style.border = "2px solid lightgray";
                            var fields = document.getElementById("delivery-details").getElementsByTagName('*');
                            for (var i = 0; i < fields.length; i++) {
                                fields[i].disabled = true;
                            }
                            var fields = document.getElementById("delivery-details-self").getElementsByTagName('*');
                            for (var i = 0; i < fields.length; i++) {
                                fields[i].disabled = false;
                            }
                            document.getElementById("delivery-details").style.display = "none";
                            document.getElementById("delivery-details-self").style.display = "block";
                        }
                    }
                    if (localStorage.getItem('deliveryOption') == "Standard Courier") {
                        deliveryOption("standard");
                    } else if (localStorage.getItem('deliveryOption') == "Self Pick-up") {
                        deliveryOption("self");
                    }
                </script>

                <?php
                $delivery_fees = 0;
                $total_checkout_price = $_SESSION["total_price"];
                if (isset($_GET["action"])) {
                    if ($_GET["action"] == "standard") {
                        $total_checkout_price = $_SESSION["total_price"] + 5;
                        $delivery_fees = 5;
                        $_SESSION["total_checkout_price"] = $total_checkout_price;
                        $_SESSION["delivery_option"] = "Standard Courier";
                        echo '<script type="text/javascript">',
                        'deliveryOption("standard");',
                        'localStorage.setItem("deliveryOption", "Standard Courier");',
                        '</script>';
                    } else if ($_GET["action"] == "self") {
                        $total_checkout_price = $_SESSION["total_price"];
                        $_SESSION["total_checkout_price"] = $total_checkout_price;
                        $_SESSION["delivery_option"] = "Self Pick-up";
                        echo '<script type="text/javascript">',
                        'deliveryOption("self");',
                        'localStorage.setItem("deliveryOption", "Self Pick-up");',
                        '</script>';
                    }
                }
                ?>

                <!--Payment Information-->
                <div class="payment-div">
                    <h3>Payment Information</h3>
                    <h4>Order Total: &emsp;&emsp;&nbsp;RM <?php echo number_format((float)$_SESSION["total_price"], 2, '.', ''); ?></h4>
                    <?php
                    if (isset($_SESSION["delivery_option"])) {
                        if ($_SESSION["delivery_option"] == "Standard Courier") {
                            $delivery_fees = 5;
                        } else {
                            $delivery_fees = 0;
                        }
                    } else {
                        $delivery_fees;
                    }
                    ?>
                    <h4>Delivery Fees: &emsp;&nbsp;RM <?php echo number_format((float)$delivery_fees, 2, '.', ''); ?></h4>
                    <h4>Amount Payable: RM <?php echo number_format((float)$_SESSION["total_checkout_price"], 2, '.', ''); ?></h4>

                    <b>Payment Method:</b><br>
                    <input type="radio" onclick="paymentMethod('credit-card')" id="credit-card" name="payment_method" 
                        value="Credit Card" <?php if (isset($_POST['payment_method'])) {
                        if ($_POST['payment_method'] == "Credit Card") echo "checked='checked'";} ?>>
                    <label for="credit-card">Credit Card</label><br>
                    <input type="radio" onclick="paymentMethod('e-wallet')" id="touch-n-go" name="payment_method" 
                        value="Touch n Go eWallet" <?php if (isset($_POST['payment_method'])) {
                        if ($_POST['payment_method'] == "Touch n Go eWallet") echo "checked='checked'";} ?>>
                    <label for="touch-n-go">Touch n Go eWallet</label><br>
                    <input type="radio" onclick="paymentMethod('e-wallet')" id="grabpay" name="payment_method" 
                        value="GrabPay" <?php if (isset($_POST['payment_method'])) {
                        if ($_POST['payment_method'] == "GrabPay") echo "checked='checked'";} ?>>
                    <label for="grabpay">GrabPay</label><br><br>

                    <div id="e-wallet-method" style="display:none">
                        <div class="form-control">
                            <label class="form-label" for="phone">Contact Number (+60)</label>
                            <input class="register-info" type="text" id="phone" name="phone" value="<?php if (isset($_POST['phone'])) echo $_POST['phone'] ?>" required>
                            <?php
                            if (isset($mobileError)) {
                                echo '<div class="form-error">';
                                echo $mobileError;
                                echo "</div>";
                                unset($mobileError);
                            }
                            ?>
                        </div>
                    </div>
                    <div id="credit-card-method" style="display:none">
                        <div class="form-control">
                            <label class="form-label" for="card-name">Name On Card</label>
                            <input class="register-info" type="text" id="card-name" name="card_name" maxlength="50" value="<?php if (isset($_POST['card_name'])) echo $_POST['card_name'] ?>" required>
                            <?php
                            if (isset($cardnameError)) {
                                echo '<div class="form-error">';
                                echo $cardnameError;
                                echo "</div>";
                                unset($cardnameError);
                            }
                            ?>
                        </div>
                        <div class="form-control">
                            <label class="form-label" for="card-num">Card Number</label>
                            <input class="register-info" type="number" id="card-num" name="card_num" length="16" value="<?php if (isset($_POST['card_num'])) echo $_POST['card_num'] ?>" required>
                            <?php
                            if (isset($cardnumError)) {
                                echo '<div class="form-error">';
                                echo $cardnumError;
                                echo "</div>";
                                unset($cardnumError);
                            }
                            ?>
                        </div>
                        <div class="form-control">
                            <label class="form-label" for="expiry-date">Expiration Date</label>
                            <input class="register-info" type="month" id="expiry-date" name="expiry_date" min="<?php echo date('Y-m'); ?>" value="<?php if (isset($_POST['expiry_date'])) echo $_POST['expiry_date'] ?>" required>
                            <?php
                            if (isset($expiryError)) {
                                echo '<div class="form-error">';
                                echo $expiryError;
                                echo "</div>";
                                unset($expiryError);
                            }
                            ?>
                        </div>
                        <div class="form-control">
                            <label class="form-label" for="cvv">CVV</label>
                            <input class="register-info" type="number" id="cvv" name="cvv" value="<?php if (isset($_POST['cvv'])) echo $_POST['cvv'] ?>" required>
                            <?php
                            if (isset($cvvError)) {
                                echo '<div class="form-error">';
                                echo $cvvError;
                                echo "</div>";
                                unset($cvvError);
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <!--Confirm Button-->
                <div style="height:50px;width:100%"></div>
                <a><button class="cart-nav-button" style="float:right;" type="submit">Confirm&emsp;<i class="fa-solid fa-angle-right"></i></button></a>
                <div style="height:50px;width:100%"></div>
            </div>
        </form>

    </div>
    <div style="height:100px;width:100%"></div>
    <script>
        if (localStorage.getItem('paymentMethod') == "e-wallet") {
            paymentMethod("e-wallet");
        } else if (localStorage.getItem('paymentMethod') == "credit") {
            paymentMethod("credit");
        }

        function paymentMethod(method) {
            if (method == "e-wallet") {
                var field1 = document.getElementById("e-wallet-method").getElementsByTagName('*');
                for (var i = 0; i < field1.length; i++) {
                    field1[i].disabled = false;
                }
                var field2 = document.getElementById("credit-card-method").getElementsByTagName('*');
                for (var i = 0; i < field2.length; i++) {
                    field2[i].disabled = true;
                }
                document.getElementById("credit-card-method").style.display = "none";
                document.getElementById("e-wallet-method").style.display = "block";
                localStorage.setItem('paymentMethod', "e-wallet");
            } else {
                var field1 = document.getElementById("e-wallet-method").getElementsByTagName('*');
                for (var i = 0; i < field1.length; i++) {
                    field1[i].disabled = true;
                }
                var field2 = document.getElementById("credit-card-method").getElementsByTagName('*');
                for (var i = 0; i < field2.length; i++) {
                    field2[i].disabled = false;
                }
                document.getElementById("e-wallet-method").style.display = "none";
                document.getElementById("credit-card-method").style.display = "block";
                localStorage.setItem('paymentMethod', "credit");
            }
        }

        document.addEventListener("DOMContentLoaded", function(event) {
            var scrollpos = localStorage.getItem('scrollpos');
            if (scrollpos) window.scrollTo(0, scrollpos);
        });

        window.onbeforeunload = function(e) {
            localStorage.setItem('scrollpos', window.scrollY);
        };
    </script>
    <?php
    include "footer.php";
    ?>
</body>

</html>