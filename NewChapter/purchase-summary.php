<?php
session_start();
include "connect-db.php";
connectDB(); // connect to database 

if ($_GET['action'] == "submit") {
    $_SESSION["purchase_summary"] = $_SESSION["cart_item"];
    if (isset($_SESSION["checkout_item"])) {
        $checkout_item = $_SESSION["checkout_item"];
        foreach ($checkout_item as $value) {
            if (isset($value["title"])) {
                foreach ($_SESSION["cart_item"] as $k => $v) {
                    if ($value["title"] == $k) {
                        unset($_SESSION["cart_item"][$k]);
                    }
                    // if no more item in cart, empty the session
                    if (empty($_SESSION["cart_item"])) {
                        unset($_SESSION["cart_item"]);
                    }
                }
            }
        }
    }
    header('Location: purchase-summary.php?action=submitted');
    exit();
} else if ($_GET['action'] == "submitted") {
    echo "<div id='order-confirmation'><i class='fa-regular fa-circle-check'></i><br>";
    echo "<h3 style='margin-top:20px'>Thanks for your order!<h3></div>";
}

include "header.php";
?>

<html>

<head>
    <title>NewChapter | Purchase Summary</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/progress-bar.css" type="text/css">
    <script src="https://kit.fontawesome.com/ffaaf7e5e9.js" crossorigin="anonymous"></script>
</head>

<body id="scroll-bar">
    <div class="wrapper">
        <div class="title-container">P U R C H A S E&emsp;S U M M A R Y</div>
        <div class="cart-div">
            <!--Progress Bar-->

            <div class="progress-bar-div">
                <ul class="progress-bar">
                    <li class="previous">Cart</li>
                    <li class="previous">Checkout</li>
                    <li class="previous">Payment</li>
                    <li class="active">Complete</li>
                </ul>
            </div>
            <div style="height:100px;width:100%"></div>

            <!--Books Ordered-->
            <h2>
                <center>
                    ORDER #
                    <?php
                    $lastID = $_SESSION['last_id'];
                    if ($lastID < 10) {
                        $lastID = "O00" . $lastID;
                    } else if ($lastID < 100) {
                        $lastID = "O0" . $lastID;
                    } else {
                        $lastID = "O" . $lastID;
                    }
                    echo $lastID;
                    ?>
                </center>
            </h2>
            <h3>Book(s) Ordered</h3>
            <table class="my-cart-tbl" cellpadding="5" cellspacing="1">
                <tbody>
                    <tr>
                        <th style="text-align:center;width:10%">Book</th>
                        <th style="text-align:center;width:50%">Title</th>
                        <th style="text-align:center;width:10%">Qty</th>
                        <th style="width:10%">Unit Price</th>
                        <th style="width:10%">Sub-total</th>
                        <th style="width:5%"></th>
                    </tr>
                </tbody>
            </table>
            <div style="height:5px;width:100%"></div>
            <!--Products in Shopping Cart-->
            <div class="cart-table-div" id="scroll-bar">
                <table class="my-cart-tbl" cellpadding="5" cellspacing="1">
                    <tbody>
                        <?php
                        $total_quantity = 0;
                        $total_price = 0;
                        $isChecked = false;
                        $checkout_item = $_SESSION["checkout_item"];

                        if (is_array($checkout_item)) {
                            foreach ($_SESSION["purchase_summary"] as $item) {
                                foreach ($checkout_item as $value) {
                                    if (isset($value["title"])) {
                                        if ($value["title"] == $item["title"]) {
                                            $img = "C:xampphtdocsNewChapteruploads";
                                            $img_str = str_replace($img, "", stripslashes($item["image"]));
                        ?>
                                            <tr style="background-color: white;">
                                                <td width="80"><?php echo "<img class='my-cart-book-img' src='uploads/" . $img_str . "'>"; ?></td>
                                                <td style="width:54%"><?php echo $item["title"]; ?></td>
                                            <?php
                                            $isChecked = true;
                                        }
                                    } else if (isset($value["quantity"]) and $isChecked) {
                                        $item_price = $value["quantity"] * $item["price"];
                                            ?>
                                            <td class="cart-quantity"><?php echo $value["quantity"]; ?></td>
                                            <td><?php echo "RM " . $item["price"]; ?></td>
                                            <td><?php echo "RM " . $item_price ?></td>
                                            <td></td>
                                            </tr>
                                            <tr style="height: 5px"></tr>
                            <?php
                                        $isChecked = false;
                                        $total_quantity += $value["quantity"];
                                        $total_price += ($item["price"] * $value["quantity"]);
                                    }
                                }
                            }
                        }
                        $_SESSION["total_price"] = $total_price;
                            ?>
                    </tbody>
                </table>
            </div>
            <table class="my-cart-tbl" cellpadding="5" cellspacing="1">
                <tbody>
                    <tr>
                        <td style="text-align:right;width:60%"></td>
                        <td style="text-align:center;width:10%"></td>
                        <td style="text-align:left;width:25%">Delivery Fees: <b><?php if ($_SESSION["delivery_option"] == "Standard Courier") {
                                                                                    echo "RM 5.00";
                                                                                } else {
                                                                                    echo "RM 0.00";
                                                                                } ?></b></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;width:60%">Number of Books:</td>
                        <td style="text-align:center;width:10%" name="total_quantity"><b><?php echo $total_quantity; ?></b></td>
                        <td style="text-align:left;width:25%">Order Total: <b><?php echo "RM " . number_format($_SESSION["total_checkout_price"], 2); ?></b></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <!--Delivery Information-->
            <div class="delivery-info" style="padding: 30px 30px;">
                <h3 style="margin-top:0">Delivery Information</h3>
                <?php if ($_SESSION["delivery_option"] == "Standard Courier") {
                    echo "<b>Delivery Option: </b>Standard Courier<br>";
                    echo "<b>Delivery Fees: </b>RM 5.00<br><br>";
                    echo "<b>Address: </b><br>" . $_SESSION["address"] . ",<br>" . $_SESSION["postcode"] . 
                    " " . $_SESSION["city"] . ", " . $_SESSION["state"] . "<br><br>";
                    echo "<b>Delivery Time: </b>" . date('h:i a', strtotime($_SESSION["time"])) . "<br>";
                    echo "<b>Delivery Date: </b>" . date('d/m/Y', strtotime($_SESSION["date"])) . "<br>";
                } else if ($_SESSION["delivery_option"] == "Self Pick-up") {
                    echo "<b>Delivery Option: </b>Self Pick-up<br>";
                    echo "<b>Self Pick-up Location: </b><br>A New Chapter Bookstore @ " . $_SESSION["pickup_location"];
                    echo "<br><b>Pick-up Time: </b>" . date('h:i a', strtotime($_SESSION["time"])) . "<br>";
                    echo "<b>Pick-up Date: </b>" . date('d/m/Y', strtotime($_SESSION["date"])) . "<br>";
                } ?>
            </div>
            <!--Payment Information-->
            <div class="delivery-info" style="padding: 30px 30px;">
                <h3 style="margin-top:0">Payment Information</h3>
                <b>Payment Method: </b><?php echo $_SESSION["payment_method"];?>
                <b><br>Order Total: </b><?php echo "RM " . number_format($_SESSION["total_checkout_price"], 2); ?><br>
                <b>Amount Paid: </b><?php echo "RM " . number_format($_SESSION["total_checkout_price"], 2); ?><br>
                <b><br>Order Date: </b><?php
                                        date_default_timezone_set('Asia/Singapore');
                                        $orderDate = date('d-m-Y');
                                        echo $orderDate;
                                        ?><br>
            </div>

            <!--Home Button-->
            <div style="height:50px;width:100%">
                <a href="index.php?msg=complete"><button class="cart-nav-button" style="float:right">Home&emsp;<i class="fa-solid fa-angle-right"></i></button></a>
            </div>

        </div>
        <div style="height:100px;width:100%"></div>
    </div>
    <script>
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
    include "footer.php";
    ?>
</body>

</html>