<?php
include "connect-db.php";
connectDB(); // connect to database 

// retieve orders from database 
$query = "SELECT * FROM orders";
$result = mysqli_query($handler, $query);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<html>

<head>
    <title>NewChapter | Order List</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://kit.fontawesome.com/ffaaf7e5e9.js" crossorigin="anonymous"></script>
</head>

<body id="scroll-bar">
    <?php
    include "admin-header.php";
    ?>

    <div class="left-panel" style="height: 800px">
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
        <div class="title-container">O R D E R S</div>
        <?php
        // callback message from other pages 
        if (isset($_GET['msg'])) {
            $adminMessage = $_GET['msg'];

            // display message accordingly 
            if ($adminMessage == "editorder") {
                echo '<p class="admin-p" id="admin-msg">Order record has been edited.</p><br>';
            } else if ($adminMessage == "deleteorder") {
                echo '<p class="admin-p" id="admin-msg">Order record has been deleted.</p><br>';
            }

            unset($_GET['msg']);
        }
        ?>
        <input class="adminSearchInput" type="text" id="orderSearchInput" onkeyup="search()" placeholder="Search order ...">
        <br><br>

        <!--Show all orders in a table-->
        <?php
        if (count($orders) == 0) {
            $display = "<p>No orders to display.</p>";
        } else {
            $display = "<table class='admin-table' style='margin-top:25px;' id='order-list'>\n";
            $display .= "<tbody id='scroll-bar'>\n";
            $display .= "<tr>
                                        <th width='100'>Order ID</th>
                                        <th width='400'>Book ID(s)</th>
                                        <th width='100'>User ID</th>
                                        <th width='200'>Order Date</th>
                                        <th width='100'>Status</th>
                                        <th width='50'></th>
                                    </tr>\n
                                    </tbody>
                                    <tbody id='scroll-bar'>";
            foreach ($orders as $order) {
                if ($order['orderID'] < 10) {
                    $orderID = "O00" . $order['orderID'];
                } else if ($order['orderID'] < 100) {
                    $orderID = "O0" . $order['orderID'];
                } else {
                    $orderID = "O" . $order['orderID'];
                }
                $display .= "<tr><td width='100'>" . $orderID . "</td><td width='400'>" . $order['bookIDs'] . "</td><td width='100'>" . $order['userID'] . "</td><td width='200'>" . $order['orderDate'] . "</td><td width='100'>" . $order['status'] .
                    "</td><td width='50'>
                                            <a href='order-record.php?orderID=" . $order['orderID'] . "'><button>View</button></a>
                                        </td></tr>\n";
            }
            $display .= "</tbody></table>";
            $display .= "<p class='admin-p' id='count'>" . count($orders) . " order(s)</p>";
        }

        echo $display;
        ?>

        <script>
            // function to display search results
            function search() {
                // initialize variables
                var searchInput = document.getElementById("orderSearchInput");
                var filter = searchInput.value.toUpperCase();
                var table = document.getElementById("order-list");
                var tr = table.getElementsByTagName("tr");
                var count = document.getElementById("count");
                var orderID, bookID, userID, orderDate, status;
                var countTemp = 0;

                // check if match is found
                for (i = 0; i < tr.length; i++) {
                    var td_orderID = tr[i].getElementsByTagName("td")[0];
                    var td_bookID = tr[i].getElementsByTagName("td")[1];
                    var td_userID = tr[i].getElementsByTagName("td")[2];
                    var td_orderDate = tr[i].getElementsByTagName("td")[3];
                    var td_status = tr[i].getElementsByTagName("td")[4];

                    if (td_orderID || td_bookID || td_userID || td_orderDate || td_status) {
                        orderID = td_orderID.textContent || td_orderID.innerText;
                        bookID = td_bookID.textContent || td_bookID.innerText;
                        userID = td_userID.textContent || td_userID.innerText;
                        orderDate = td_orderDate.textContent || td_orderDate.innerText;
                        status = td_status.textContent || td_status.innerText;

                        if (orderID.toUpperCase().indexOf(filter) > -1) { // matched order ID
                            tr[i].style.display = "";
                            countTemp++;
                        } else if (bookID.toUpperCase().indexOf(filter) > -1) { // matched book ID
                            tr[i].style.display = "";
                            countTemp++;
                        } else if (userID.toUpperCase().indexOf(filter) > -1) { // matched user ID
                            tr[i].style.display = "";
                            countTemp++;
                        } else if (orderDate.toUpperCase().indexOf(filter) > -1) { // matched order date
                            tr[i].style.display = "";
                            countTemp++;
                        } else if (status.toUpperCase().indexOf(filter) > -1) { // matched status
                            tr[i].style.display = "";
                            countTemp++;
                        } else { // no match found
                            tr[i].style.display = "none";
                        }
                    }
                }

                // count number of matches
                if (countTemp == 0) {
                    table.style.display = "none";
                    count.innerHTML = "No match found.";
                } else {
                    table.style.display = "";
                    count.innerHTML = countTemp + " order(s)";
                }
            }
        </script>
    </div>
</body>

</html>