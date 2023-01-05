<?php
include "connect-db.php";
connectDB(); // connect to database 

include "admin-header.php";

// retreive orderID from order-list.php
$ID = $_GET['orderID'];

// retreive order from database 
$query = "SELECT * FROM orders WHERE orderID = $ID";
$result = mysqli_query($handler, $query);
$order = mysqli_fetch_assoc($result);

// assign variables to each book detail 
if ($ID < 10) {
    $orderID = "O00" . $order['orderID'];
} else if ($ID < 100) {
    $orderID = "O0" . $order['orderID'];
} else {
    $orderID = "O" . $order['orderID'];
}

$status = $order['status'];
$bookIDs = $order['bookIDs'];
$userID = $order['userID'];
$orderTotal = $order['orderTotal'];
$paymentMethod = $order['paymentMethod'];
$orderDate = $order['orderDate'];
$deliveryOption = $order['deliveryOption'];
$deliveryStreet =  $order['deliveryStreet'];
$deliveryCity = $order['deliveryCity'];
$deliveryState = $order['deliveryState'];
$deliveryPostcode = $order['deliveryPostcode'];
$deliveryDate = $order['deliveryDate'];
$deliveryTime = $order['deliveryTime'];

// check if form is submitted 
if (isset($_POST['update'])) {
    // perform form validation 
    $errors = 0; // error count

    // validate book IDs 
    $pattern = "/^(([B][0-9]{3}(,\s)?)*)+$/";
    if (empty($_POST['book-ids'])) {
        $bookIDsError = "Please enter the Book ID(s) for this order";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['book-ids'])) {
        $bookIDsError = "Book ID(s) must be of format BXXX, BXXX, ... where XXX are numbers";
        $errors += 1;
    } else { // check if book ID exists in database
        // retrieve all existing book IDs from database
        $query = "SELECT bookID FROM books";
        $result = mysqli_query($handler, $query);
        $bookIDs_db = array();
        while ($row = mysqli_fetch_array($result)) {
            array_push($bookIDs_db, $row[0]);
        }

        $bookIDs_order = explode(", ", $_POST['book-ids']);
        $exist = array();
        foreach ($bookIDs_order as $item) {
            $item_test = ltrim($item, $item[0]); // remove letter B
            $item_test = (string)(int)$item_test; // remove leading zeros

            // check if book ID exists in database 
            if (!in_array($item_test, $bookIDs_db)) {
                $bookIDsError = "Book ID " . $item . " does not exist in the database";
                $errors += 1;
                $exist[] = "false";
            }
        }
        if (empty($exist)) { // all book ID(s) exist in database
            $bookIDs = mysqli_real_escape_string($handler, trim($_POST['book-ids']));
        }
    }

    // validate user ID
    $pattern = "/^[U][0-9]{3}$/";
    if (empty($_POST['user-id'])) {
        $userIDError = "Please enter the User ID for this order";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['user-id'])) {
        $userIDError = "User ID must be of format UXXX where XXX are numbers";
        $errors += 1;
    } else { // check if user ID exists in database
        // retrieve all existing userr IDs from database
        $query = "SELECT userID FROM users WHERE role=2"; // customers only
        $result = mysqli_query($handler, $query);
        $userIDs_db = array();
        while ($row = mysqli_fetch_array($result)) {
            array_push($userIDs_db, $row[0]);
        }

        $userID_order = $_POST['user-id'];
        $exist = array();
        $userID_test = ltrim($userID_order, $userID_order[0]); // remove letter U
        $userID_test = (string)(int)$userID_test; // remove leading zeros

        // check if user ID exists in database
        if (!in_array($userID_test, $userIDs_db)) {
            $userIDError = "User ID does not exist in the database";
            $errors += 1;
        } else {
            $userID = mysqli_real_escape_string($handler, trim($_POST['user-id']));
        }
    }

    // validate order total 
    if (empty($_POST['order-total'])) {
        $orderTotalError = "Please enter the order total";
        $errors += 1;
    } else if ($_POST['order-total'] < 0) {
        $orderTotalError = "Order total cannot be less than 0";
        $errors += 1;
    } else {
        $orderTotal = $_POST['order-total'];
    }

    // validate order date
    $today = date("Y-m-d");
    if (empty($_POST['order-date'])) {
        $orderDateError = "Please enter the order date";
        $errors += 1;
    } else if ($_POST['order-date'] > $today) {
        $orderDateError = "Order date cannot be a future date";
        $errors += 1;
    } else {
        $orderDate = $_POST['order-date'];
    }

    // validate delivery street
    $pattern = "/^[A-Za-z0-9\s,.\'\/]*$/";
    if (empty($_POST['delivery-street'])) {
        $streetError = "Please enter the delivery street";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['delivery-street'])) {
        $streetError = "Delivery street can only contain alphabets, numbers, white spaces, and special characters ,.'/";
        $errors += 1;
    } else {
        $deliveryStreet = mysqli_real_escape_string($handler, trim($_POST['delivery-street']));
    }

    // validate delivery city
    $pattern = "/^[A-Za-z\s]*$/";
    if (empty($_POST['delivery-city'])) {
        $cityError = "Please enter the delivery city";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['delivery-city'])) {
        $cityError = "Delivery city can only contain alphabets and white spaces";
        $errors += 1;
    } else {
        $deliveryCity = mysqli_real_escape_string($handler, trim($_POST['delivery-city']));
    }

    // validate delivery state
    if (empty($_POST['delivery-state'])) {
        $stateError = "Please enter the delivery state";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['delivery-state'])) {
        $stateError = "Delivery state can only contain alphabets and white spaces";
        $errors += 1;
    } else {
        $deliveryState = mysqli_real_escape_string($handler, trim($_POST['delivery-state']));
    }

    // validate delivery postcode
    if (empty($_POST['delivery-postcode'])) {
        $postcodeError = "Please enter the delivery postcode";
        $errors += 1;
    } else if (strlen((string)$_POST['delivery-postcode']) != 5) {
        $postcodeError = "Delivery postcode must contain exactly 5 digits";
        $errors += 1;
    } else {
        $deliveryPostcode = $_POST['delivery-postcode'];
    }

    // validate delivery date
    if (empty($_POST['delivery-date'])) {
        $deliveryDateError = "Please enter the delivery date";
        $errors += 1;
    } else if ($_POST['delivery-date'] < $_POST['order-date']) { // check if delivery date < order date
        $deliveryDateError = "Delivery date cannot be before the order date";
        $errors += 1;
    } else {
        if ($_POST['status'] == "Complete") { // status = complete
            if ($_POST['delivery-date'] > $today) {
                $deliveryDateError = "Order status = Complete, delivery date cannot be a future date";
                $errors += 1;
            } else {
                $deliveryDate = $_POST['delivery-date'];
            }
        } else if ($_POST['status'] == "Incomplete") { // status = incomplete
            if ($_POST['delivery-date'] < $today) {
                $deliveryDateError = "Order status = Incomplete, delivery date cannot be a past date";
                $errors += 1;
            } else {
                $deliveryDate = $_POST['delivery-date'];
            }
        }
    }

    // validate delivery time
    if (empty($_POST['delivery-time'])) {
        $deliveryTimeError = "Please enter the delivery time";
        $errors += 1;
    } else {
        $deliveryTime_test = (string)$_POST['delivery-time'];
        $deliveryTime_test = str_replace(":", "", $deliveryTime_test); // remove all :
        if (strlen($deliveryTime_test) > 4) {
            $deliveryTime_test = substr($deliveryTime_test, 0, -2); // remove seconds
        }
        $deliveryTime_test = (int)$deliveryTime_test;

        if ($deliveryTime_test < 800 || $deliveryTime_test > 1800) {
            $deliveryTimeError = "Delivery time must be within 8:00 AM to 6:00 PM";
            $errors += 1;
        } else {
            $deliveryTime = $_POST['delivery-time'];
        }
    }

    // if no errors found
    if ($errors == 0) {
        $status = mysqli_real_escape_string($handler, trim($_POST['status']));
        $paymentMethod = mysqli_real_escape_string($handler, trim($_POST['payment-method']));
        $deliveryOption = mysqli_real_escape_string($handler, trim($_POST['delivery-option']));

        // update order table
        $query = "UPDATE orders SET
                    status = '" . $status . "', 
                    bookIDs = '" . $bookIDs . "', 
                    userID = '" . $userID . "',
                    orderTotal = '" . $orderTotal . "', 
                    paymentMethod = '" . $paymentMethod . "', 
                    orderDate = '" . $orderDate . "', 
                    deliveryOption = '" . $deliveryOption . "', 
                    deliveryStreet = '" . $deliveryStreet . "', 
                    deliveryCity = '" . $deliveryCity . "',
                    deliveryState = '" . $deliveryState . "',
                    deliveryPostcode = '" . $deliveryPostcode . "',
                    deliveryDate = '" . $deliveryDate . "',
                    deliveryTime = '" . $deliveryTime . "'
                WHERE orderID =" . $order['orderID'];
        mysqli_query($handler, $query);

        // redirect admin to order list page
        header("Location: order-list.php?msg=editorder");
        exit();
    }
} else if (isset($_POST['delete'])) { // delete button clicked
    // delete order record from database
    $query = "DELETE FROM orders WHERE orderID =" . $order['orderID'];
    mysqli_query($handler, $query);
    header("Location: order-list.php?msg=deleteorder");
    exit();
}
?>

<html>

<head>
    <title>NewChapter | Order Record</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/form-control.css" type="text/css">
</head>

<body id="scroll-bar">
    <div class="left-panel" style="height: 800px">
        <div style="height:100px; width:100%"></div>

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
            <div class="admin-nav" style="background-color:#006000">Orders<i class="fa-solid fa-cart-shopping"></i></div>
        </a>
        <a href="donation-list.php">
            <div class="admin-nav">Donations<i class="fa-solid fa-hand-holding-dollar"></i></div>
        </a>
        <a href="logout.php">
            <div class="admin-nav admin-logout">Log Out<i class="fa-solid fa-right-from-bracket"></i></div>
        </a>
    </div>

    <div class="right-panel">
        <div class="title-container">O R D E R&emsp;R E C O R D&emsp;#<?php echo $orderID ?></div>
        <div class="summary">
            <div id="edit-div">
                <!-- Form for admin to update order details-->
                <form id="update-form" method="post" action="order-record.php?orderID=<?php echo $ID; ?>">
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
                        <label for="book-ids">Book ID(s):</label>
                        <input class="order-update-info" type="text" id="book-ids" name="book-ids" value="<?php if (isset($_POST['book-ids'])) echo $_POST['book-ids'];
                                                                                                            else echo $bookIDs; ?>" required>
                        <?php
                        if (isset($bookIDsError)) {
                            echo '<div class="admin-form-error">';
                            echo $bookIDsError;
                            echo "</div>";
                            unset($bookIDsError);
                        }
                        ?>
                    </div>
                    <br>

                    <div class="form-control">
                        <label for="user-id">User ID:</label>
                        <input class="order-update-info" type="text" id="user-id" name="user-id" value="<?php if (isset($_POST['user-id'])) echo $_POST['user-id'];
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
                        <label for="order-total">Order Total:</label>
                        <input class="order-update-info" type="number" id="order-total" name="order-total" value="<?php if (isset($_POST['order-total'])) echo $_POST['order-total'];
                                                                                                                    else echo $orderTotal; ?>" required>
                        <?php
                        if (isset($orderTotalError)) {
                            echo '<div class="admin-form-error">';
                            echo $orderTotalError;
                            echo "</div>";
                            unset($orderTotalError);
                        }
                        ?>
                    </div>
                    <br>

                    <label for="payment-method">Payment Method:</label>
                    <select name="payment-method" id="payment-method">
                            <?php
                                // set default value in drop down list 
                                if (isset($_POST['update'])) {
                                    if (isset($_POST['payment-method']) && $_POST['payment-method'] == "Credit Card") {
                                        echo "<option value='Credit Card' selected>Credit Card</option>
                                        <option value='Touch n Go eWallet'>Touch n Go eWallet</option>
                                        <option value='GrabPay'>GrabPay</option>";
                                    } elseif (isset($_POST['payment-method']) && $_POST['payment-method'] == "Touch n Go eWallet") {
                                        echo "<option value='Credit Card'>Credit Card</option>
                                        <option value='Touch n Go eWallet' selected>Touch n Go eWallet</option>
                                        <option value='GrabPay'>GrabPay</option>";
                                    } else {
                                        echo "<option value='Credit Card'>Credit Card</option>
                                        <option value='Touch n Go eWallet'>Touch n Go eWallet</option>
                                        <option value='GrabPay' selected>GrabPay</option>";
                                    }
                                } else {
                                    if ($paymentMethod == "Credit Card") {
                                        echo "<option value='Credit Card' selected>Credit Card</option>
                                        <option value='Touch n Go eWallet'>Touch n Go eWallet</option>
                                        <option value='GrabPay'>GrabPay</option>";
                                    } elseif ($paymentMethod == "Touch n Go eWallet") {
                                        echo "<option value='Credit Card'>Credit Card</option>
                                        <option value='Touch n Go eWallet' selected>Touch n Go eWallet</option>
                                        <option value='GrabPay'>GrabPay</option>";
                                    } else {
                                        echo "<option value='Credit Card'>Credit Card</option>
                                        <option value='Touch n Go eWallet'>Touch n Go eWallet</option>
                                        <option value='GrabPay' selected>GrabPay</option>";
                                    }
                                }
                            ?>
                        </select><br><br>

                    <div class="form-control">
                        <label for="order-date">Order Date:</label>
                        <input class="order-update-info" type="date" id="order-date" name="order-date" value="<?php if (isset($_POST['order-date'])) echo $_POST['order-date'];
                                                                                                                else echo $orderDate; ?>" required>
                        <?php
                        if (isset($orderDateError)) {
                            echo '<div class="admin-form-error">';
                            echo $orderDateError;
                            echo "</div>";
                            unset($orderDateError);
                        }
                        ?>
                    </div>
                    <br>

                    <label for="deivery-option">Delivery Option:</label>
                    <select name="delivery-option" id="delivery-option">
                        <?php
                        // set default value in drop down list 
                        if (isset($_POST['update'])) {
                            if (isset($_POST['delivery-option']) && $_POST['delivery-option'] == "Standard Courier") {
                                echo "<option value='Standard Courier' selected>Standard Courier</option>
                                            <option value='Self Pick-up'>Self Pick-up</option>";
                            } else {
                                echo "<option value='Standard Courier'>Standard Courier</option>
                                            <option value='Self Pick-up' selected>Self Pick-up</option>";
                            }
                        } else {
                            if ($deliveryOption = "Standard Courier") {
                                echo "<option value='Standard Courier' selected>Standard Courier</option>
                                            <option value='Self Pick-up'>Self Pick-up</option>";
                            } else {
                                echo "<option value='Standard Courier'>Standard Courier</option>
                                            <option value='Self Pick-up' selected>Self Pick-up</option>";
                            }
                        }
                        ?>
                    </select><br><br>

                    <div class="form-control">
                        <label for="delivery-street" style="vertical-align: top">Delivery Street:</label>
                        <textarea class="order-update-info" id="delivery-street" name="delivery-street" rows="3" cols="50" required><?php if (isset($_POST['delivery-street'])) echo $_POST['delivery-street'];
                                                                                                                                    else echo $deliveryStreet; ?></textarea>
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
                        <label for="delivery-city">Delivery City:</label>
                        <input class="order-update-info" type="text" id="delivery-city" name="delivery-city" value="<?php if (isset($_POST['delivery-city'])) echo $_POST['delivery-city'];
                                                                                                                    else echo $deliveryCity; ?>" required>
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
                        <label for="delivery-state">Delivery State:</label>
                        <input class="order-update-info" type="text" id="delivery-state" name="delivery-state" value="<?php if (isset($_POST['delivery-state'])) echo $_POST['delivery-state'];
                                                                                                                        else echo $deliveryState; ?>" required>
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
                        <label for="delivery-postcode">Delivery Postcode:</label>
                        <input class="order-update-info" type="number" id="delivery-postcode" name="delivery-postcode" value="<?php if (isset($_POST['delivery-postcode'])) echo $_POST['delivery-postcode'];
                                                                                                                                else echo $deliveryPostcode; ?>" required>
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
                        <label for="delivery-date">Delivery Date:</label>
                        <input class="order-update-info" type="date" id="delivery-date" name="delivery-date" value="<?php if (isset($_POST['delivery-date'])) echo $_POST['delivery-date'];
                                                                                                                    else echo $deliveryDate ?>" required>
                        <?php
                        if (isset($deliveryDateError)) {
                            echo '<div class="admin-form-error">';
                            echo $deliveryDateError;
                            echo "</div>";
                            unset($deliveryDateError);
                        }
                        ?>
                    </div>
                    <br>

                    <div class="form-control">
                        <label for="delivery-time">Delivery Time:</label>
                        <input class="order-update-info" type="time" id="delivery-time" name="delivery-time" value="<?php if (isset($_POST['delivery-time'])) echo $_POST['delivery-time'];
                                                                                                                    else echo $deliveryTime; ?>" required>
                        <?php
                        if (isset($deliveryTimeError)) {
                            echo '<div class="admin-form-error">';
                            echo $deliveryTimeError;
                            echo "</div>";
                            unset($deliveryTimeError);
                        }
                        ?>
                    </div>
                    <br>

                    <input class="update-order-btn" type="submit" name="update" value="Update Record">
                    <input class="delete-btn" type="submit" name="delete" value="Delete Record">
                </form>
            </div>
            <a href="order-list.php"><button type="button">Close</button></a>

        </div>
    </div>
</body>

</html>