<?php
session_start();

unset($_SESSION["checkout_item"]);
$_SESSION["checkout_item"] = $_POST["checkout_item"];

if (!isset($_SESSION["username"])) {
    header('Location: login.php?msg=loginrequired');
}
?>

<html>

<head>
    <title>NewChapter | Checkout</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/progress-bar.css" type="text/css">
    <script src="https://kit.fontawesome.com/ffaaf7e5e9.js" crossorigin="anonymous"></script>
</head>

<body id="scroll-bar">
    <?php
    include "header.php";
    ?>
    <div class="wrapper">
        <div class="title-container">C H E C K O U T</div>
        <div class="cart-div">

            <!--Progress Bar-->
            <div class="progress-bar-div">
                <ul class="progress-bar">
                    <li class="previous">Cart</li>
                    <li class="active">Checkout</li>
                    <li>Payment</li>
                    <li>Complete</li>
                </ul>
            </div>
            <br>
            <form action="payment-delivery.php" method="post">
                <table class="my-cart-tbl" style="margin-top: 100px" cellpadding="5" cellspacing="1">
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
                            if (isset($_POST["checkout_item"])) {
                                $checkout_item = $_POST["checkout_item"];
                            } else {
                                header("Location: my-cart.php");
                            }

                            if (is_array($checkout_item)) {
                                foreach ($_SESSION["cart_item"] as $item) {
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
                                ?>

                        </tbody>
                    </table>

                </div>
                <table class="my-cart-tbl" cellpadding="5" cellspacing="1">
                    <tbody>
                        <tr>
                            <td style="text-align:right;width:60%">Number of Books:</td>
                            <td style="text-align:center;width:10%" name="total_quantity"><b><?php echo $total_quantity; ?></b></td>
                            <td style="text-align:left;width:25%">Total Price: <b><?php echo "RM " . number_format($total_price, 2); ?></b></td>
                            <?php $_SESSION["total_price"] = $total_price; ?>
                        </tr>
                    </tbody>
                </table>
                <div style="height:50px;width:100%"></div>

                <!--Cart and Payment Button-->
                <div>
                    <a href="my-cart.php">
                        <div class="cart-nav-button"><i class="fa-solid fa-angle-left"></i>&emsp;Cart</div>
                    </a>
                    <button type="submit" class="cart-nav-button" style="float:right">Payment&emsp;<i class="fa-solid fa-angle-right"></i></button>
                </div>
            </form>
        </div>
    </div>
    <div style="height:100px;width:100%"></div>

    <?php
    include "footer.php";
    ?>
</body>

</html>