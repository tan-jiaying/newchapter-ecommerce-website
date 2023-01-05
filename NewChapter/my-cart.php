<?php
session_start();
if (isset($_POST['checkout_item'])) {
    unset($_POST['checkout_item']);
}

if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        case "updateQuantity":
            foreach ($_SESSION["cart_item"] as $k => $v) {
                if ($_GET["title"] == $k)
                    $_SESSION["cart_item"][$k]["quantity"] = $_POST["quantity"];
            }
            break;
        case "empty":
            break;
    }
}
?>

<html>

<head>
    <title>NewChapter | My Cart</title>
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
        <div class="title-container">M Y&emsp;C A R T</div>
        <div class="cart-div">
            <!--Progress Bar-->
            <div class="progress-bar-div">
                <ul class="progress-bar">
                    <li class="active">Cart</li>
                    <li>Checkout</li>
                    <li>Payment</li>
                    <li>Complete</li>
                </ul>
            </div>
            <br>
            <form action="checkout.php" method="post">
                <table class="my-cart-tbl" style="margin-top: 100px" cellpadding="5" cellspacing="1">
                    <tbody>
                        <tr>
                            <th style="width:5%"></th>
                            <th style="text-align:center;width:10%">Book</th>
                            <th style="text-align:center;width:45%">Title</th>
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
                    <?php
                    if (empty($_SESSION["cart_item"])) {
                        unset($_SESSION["cart_item"]);
                        header('Location: books.php');
                    } else if (isset($_SESSION["cart_item"])) {
                        $total_quantity = 0;
                        $total_price = 0;
                    ?>

                        <table class="my-cart-tbl" cellpadding="5" cellspacing="1">
                            <tbody>
                                <?php
                                $index = 0;
                                foreach ($_SESSION["cart_item"] as $item) {
                                    $item_price = $item["quantity"] * $item["price"];
                                    $img = "C:xampphtdocsNewChapteruploads";
                                    $img_str = str_replace($img, "", stripslashes($item["image"]));
                                    $result = $db_handle->runQuery("SELECT inStock FROM books WHERE title='" . $item["title"] . "'");
                                    $stock = $result[0]["inStock"];
                                ?>

                                    <tr style="background-color: white;">
                                        <td>
                                            <!-- <input type="hidden" name="checkout_item" value="no"> -->
                                            <input onchange="checkCheckbox()" class="cart-checkbox" type="checkbox" name="checkout_item[][title]" unchecked value="<?php echo $item["title"]; ?>" />
                                        </td>

                                        <td width="80"><?php echo "<img class='my-cart-book-img' src='uploads/" . $img_str . "'>"; ?></td>
                                        <td style="width:48%"><?php echo $item["title"]; ?></td>
                                        <td class="cart-quantity">
                                            <input type="number" min="1" max="<?php echo $stock; ?>" name="checkout_item[][quantity]" style="display:inline-block" value="<?php echo $item['quantity']; ?>" />
                                        </td>
                                        <td><?php echo "RM " . $item["price"]; ?></td>
                                        <td><?php echo "RM " . $item_price ?></td>
                                        <td>
                                            <!-- Get method to remove item in Cart -->
                                            <a href="my-cart.php?action=remove&title=<?php echo $item["title"]; ?>" class="btnRemoveAction">
                                                <i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <tr style="height: 5px"></tr>

                                <?php
                                    $total_quantity += $item["quantity"];
                                    $total_price += ($item["price"] * $item["quantity"]);
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php
                    }
                    ?>
                </div>

                <!--Books and Checkout Button-->
                <div style="height:50px;width:100%"></div>

                <a href="books.php">
                    <div class="cart-nav-button"><i class="fa-solid fa-angle-left"></i>&emsp;Books</div>
                </a>
                <button id="checkout-btn" class="cart-nav-button" style="float: right" disabled>Checkout&emsp;<i class="fa-solid fa-angle-right"></i></button>
            </form>

        </div>
    </div>
    <div style="height:100px;width:100%"></div>
    <script>
        document.getElementById("shopping-cart-div").style.display = "none";
        localStorage.setItem('shoppingCart', "hidden");

        function checkCheckbox() {
            count = 0;
            var checkedBook = document.getElementsByClassName("cart-checkbox");
            for (var i = 0; i < checkedBook.length; i++) {
                if (checkedBook[i].checked == true) {
                    count += 1;
                }
            }
            if (count == 0) {
                document.querySelector('#checkout-btn').disabled = true;
            } else {
                document.querySelector('#checkout-btn').disabled = false;

            }
        }
    </script>
    <?php
    include "footer.php";
    ?>
</body>

</html>