<?php
include "connect-db.php";
connectDB(); // connect to database

include "admin-header.php";

// callback message from other pages 
if (isset($_GET['msg'])) {
    $adminMessage = $_GET['msg'];
    echo $adminMessage;
    unset($_GET['msg']);
}

// retrive orders from database 
$query = "SELECT * from orders";
$result = mysqli_query($handler, $query);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

// calculate total sales
$sales = 0;
if (count($orders) != 0) {
    foreach ($orders as $order) {
        $sales += (int)$order["orderTotal"];
    }
}

// calculate total books sold 
$bookCount = 0;
if (count($orders) != 0) {
    foreach ($orders as $order) {
        $bookIDs = explode(",", $order["bookIDs"]);
        $bookCount +=  sizeof($bookIDs);
    }
}

// retrieve customers from database
$query = "SELECT * from users WHERE role=2";
$result = mysqli_query($handler, $query);
$customers = mysqli_fetch_all($result, MYSQLI_ASSOC);

// calculate total number of customers
$customerCount = 0;
if (count($customers) != 0) {
    $customerCount = mysqli_num_rows($result);
}

// retrieve donations from database
$query = "SELECT * from donations";
$result = mysqli_query($handler, $query);
$donations = mysqli_fetch_all($result, MYSQLI_ASSOC);

// calculate total number of donations
$donationCount = 0;
if (count($donations) != 0) {
    $donationCount = mysqli_num_rows($result);
}
?>

<html>

<head>
    <title>NewChapter | Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://kit.fontawesome.com/ffaaf7e5e9.js" crossorigin="anonymous"></script>
</head>

<body id="scroll-bar">
    <div class="left-panel" style="height: 900px">
        <div style="height:100px; width:100%"></div>

        <a href="dashboard.php">
            <div class="admin-nav" style="background-color:#006000">Dashboard<i class="fa-solid fa-chart-line"></i></div>
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
            <div class="admin-nav">Donations<i class="fa-solid fa-hand-holding-dollar"></i></div>
        </a>
        <a href="logout.php">
            <div class="admin-nav admin-logout">Log Out<i class="fa-solid fa-right-from-bracket"></i></div>
        </a>
    </div>

    <div class="right-panel">
        <div class="title-container">D A S H B O A R D</div>
        <p class="admin-p">You are logged in as <strong>admin</strong>
        <p>

        <div class="summary">
            <!--Sales-->
            <div class="summary-box">
                <i class="fa-solid fa-sack-dollar"></i>
                <div class="summary-text">
                    SALES<br>
                    <?php echo "RM " . $sales; ?>
                </div>
            </div>

            <!--Books Sold-->
            <div class="summary-box">
                <i class="fa-solid fa-book"></i>
                <div class="summary-text">
                    BOOKS SOLD<br>
                    <?php echo $bookCount; ?>
                </div>
            </div>

            <!--Customers-->
            <div class="summary-box">
                <i class="fa-solid fa-person"></i>
                <div class="summary-text">
                    CUSTOMERS<br>
                    <?php echo $customerCount; ?>
                </div>
            </div>

            <!--Donations-->
            <div class="summary-box">
                <i class="fa-solid fa-hand-holding-medical"></i>
                <div class="summary-text">
                    DONATIONS<br>
                    <?php echo $donationCount; ?>
                </div>
            </div>
        </div>

        <!--Pending Orders-->
        <div>
            <table class="admin-table" style="margin-top:25px">
                <tbody id="scroll-bar">
                    <tr>
                        <th colspan="6" width='950'>Pending Orders</th>
                    </tr>
                </tbody>
                <tbody style="max-height:200px" id="scroll-bar">
                    <?php
                    if (count($orders) == 0) {
                        $display = "<tr><td>No orders available.</td><td></td><td></td><td></td><td></td><td></td></tr>";
                    } else {
                        $orderStatus_array = array();
                        foreach ($orders as $order) {
                            array_push($orderStatus_array, $order["status"]);
                        }

                        if (in_array("Incomplete", $orderStatus_array)) { // there are pending orders
                            $display = "<tr>
                                                    <td width='100'><b>Order ID</b></td>
                                                    <td width='400'><b>Book ID(s)</b></td>
                                                    <td width='100'><b>Customer ID</b></td>
                                                    <td width='150'><b>Order Date</b></td>
                                                    <td width='150'><b>Delivery Date</b></td>
                                                    <td width='50'></td>
                                                </tr>\n";

                            foreach ($orders as $order) {
                                if ($order['orderID'] < 10) {
                                    $orderID = "O00" . $order['orderID'];
                                } else if ($order['orderID'] < 100) {
                                    $orderID = "O0" . $order['orderID'];
                                } else {
                                    $orderID = "O" . $order['orderID'];
                                }
                                if ($order["status"] == "Incomplete") {
                                    $display .= "<tr><td>" . $orderID . "</td><td>" . $order['bookIDs'] . "</td><td>" . $order['userID']
                                        . "</td><td>" . $order['orderDate'] . "</td><td>" . $order['deliveryDate'] .
                                        "</td><td>
                                                <a href='order-record.php?orderID=" . $order['orderID'] . "'><button>View</button></a>
                                            </td></tr>\n";
                                }
                            }
                        } else { // no pending orders
                            $display = "<tr><td>No pending orders.</td><td></td><td></td><td></td><td></td></tr>";
                        }
                    }

                    echo $display;
                    ?>
                </tbody>
            </table>
        </div>

        <!--Pending Donations-->
        <div>
            <table class="admin-table" style="margin-top:25px" id="scroll-bar">
                <tbody id="scroll-bar">
                    <tr>
                        <th colspan="6" width='950'>Pending Donations</th>
                    </tr>
                </tbody>
                <tbody style="max-height:200px" id="scroll-bar">
                    <?php
                    if (count($donations) == 0) {
                        $display = "<tr><td>No donations available.</td><td></td><td></td><td></td><td></td></tr>";
                    } else {
                        $donationStatus_array = array();
                        foreach ($donations as $donation) {
                            array_push($donationStatus_array, $donation["status"]);
                        }

                        if (in_array("Incomplete", $donationStatus_array)) { // there are pending donations
                            $display = "<tr>
                                                    <td width='200px'><b>Donation ID</b></td>
                                                    <td width='200px'><b>Customer ID</b></td>
                                                    <td width='200px'><b>Donation Method</b></td>
                                                    <td width='200px'><b>Donation Date</b></td>
                                                    <td width='150px'></td>
                                                </tr>\n";

                            foreach ($donations as $donation) {
                                if ($donation['donationID'] < 10) {
                                    $donationID = "D00" . $donation['donationID'];
                                } else if ($donation['donationID'] < 100) {
                                    $donationID = "D0" . $donation['donationID'];
                                } else {
                                    $donationID = "D" . $donation['donationID'];
                                }
                                if ($donation["status"] == "Incomplete") {
                                    $display .= "<tr><td>" . $donationID . "</td><td>" . $donation['userID'] . "</td><td>" . $donation['donationMethod'] . "</td><td>" . $donation['donationDate'] .
                                        "</td><td>
                                                            <a href='donation-record.php?donationID=" . $donation['donationID'] . "'><button>View</button></a>
                                                        </td></tr>\n";
                                }
                            }
                        } else { // no pending donations
                            $display = "<tr><td>No pending donations.</td><td></td><td></td><td></td><td></td></tr>";
                        }
                    }

                    echo $display;
                    ?>
                </tbody>
            </table>
        </div>
    </div>




</body>

</html>