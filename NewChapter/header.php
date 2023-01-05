<?php
date_default_timezone_set('Asia/Singapore');
ob_start();
if (!isset($_SESSION)) session_start();
require_once("dbcontroller.php");
$db_handle = new DBController();

//Get method for adding/remove item to Cart
if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        case "add":
            if (!empty($_POST["quantity"])) {
                $productByCode = $db_handle->runQuery("SELECT * FROM books WHERE title='" . $_GET["title"] . "'");
                //get the first data only with index [0]
                $itemArray = array($productByCode[0]["title"] => array(
                    'title' => $productByCode[0]["title"],
                    'quantity' => $_POST["quantity"],
                    'price' => $productByCode[0]["price"],
                    'image' => $productByCode[0]["imageDirectory"],
                    'inStock' => $productByCode[0]["inStock"]
                ));

                if (!empty($_SESSION["cart_item"])) {
                    //checking new add item with current Cart
                    if (in_array($productByCode[0]["title"], array_keys($_SESSION["cart_item"]))) {
                        foreach ($_SESSION["cart_item"] as $k => $v) {
                            if ($productByCode[0]["title"] == $k) {
                                //if the quantity is empty, starting the quantity from Zero
                                if (empty($_SESSION["cart_item"][$k]["quantity"])) {
                                    $_SESSION["cart_item"][$k]["quantity"] = 0;
                                }
                                //if the item already in the Cart, add the quantity
                                if (($_SESSION["cart_item"][$k]["quantity"] + $_POST["quantity"]) <= $productByCode[0]["inStock"]) {
                                    $_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
                                } 
                            }
                        }
                    }
                    //if current item is not in the cart, add the item
                    else {
                        $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"], $itemArray);
                    }
                } else {
                    //if the session is empty, start the new session.
                    $_SESSION["cart_item"] = $itemArray;
                }
            }
            break;
        case "remove":
            if (!empty($_SESSION["cart_item"])) {
                foreach ($_SESSION["cart_item"] as $k => $v) {
                    if ($_GET["title"] == $k)
                        unset($_SESSION["cart_item"][$k]);
                    // if no more item in cart, empty the session
                    if (empty($_SESSION["cart_item"]))
                        unset($_SESSION["cart_item"]);
                }
            }
            break;
        case "empty":
            unset($_SESSION["cart_item"]);

            break;
    }
}
if (isset($_SESSION["cart_item"])) {
    $count = count($_SESSION["cart_item"]);
} else {
    $count = 0;
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
    <script src="https://kit.fontawesome.com/ffaaf7e5e9.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navigation">
        <div class="nav-container">
            <div class="nav-top-row">
                <a href="index.php">
                    <div class="nav-logo">
                        <img class="nav-logo-img" src="img/header-image/logo.svg" alt="New Chapter">
                        <div class="nav-logo-text"><b>&emsp;New Chapter</b></div>
                    </div>
                </a>

                <?php
                // display username if user is logged in 
                if (isset($_SESSION['username'])) {
                    echo "<button class='account-button' onclick='showAccountDiv()'>";
                    echo "<i class='fa-solid fa-user'></i>&emsp;";
                    echo $_SESSION['username'];
                    echo "</button>";
                } else {
                    echo "<button id='login-register-btn'><a class='login-button' href='login.php'>Login</a> | 
                    <a class='register-button' href='register.php'>Register</a></button>";
                }
                ?>
                <div class="shopping-cart-icon" onclick="showShoppingCart()">
                    <div class="shopping-cart-count" id="shopping-cart-count"><?php echo $count; ?></div>
                    <i class='fa-solid fa-cart-shopping'></i>
                </div>

                <!--Shopping Cart Drop Down-->
                <div id="shopping-cart-div" style="display:none">
                    <div class="my-cart-top"><b>My Cart</b><i id="shopping-cart-close" class="fa-solid fa-close" onclick="hideShoppingCart()"></i></div>
                    <div class="my-cart-content" id="scroll-bar">
                        <div class="empty-btn-div"><a id="btnEmpty" href="books.php?action=empty"><b>Empty Cart</b></a></div>
                        <?php
                        if (isset($_SESSION["cart_item"])) {
                            $total_quantity = 0;
                            $total_price = 0;
                        ?>
                            <table class="cart-tbl" cellpadding="5" cellspacing="1">
                                <tbody>
                                    <tr>
                                        <th></th>
                                        <th style="text-align:left;">Title</th>
                                        <!-- <th style="text-align:left;">ID</th> -->
                                        <th style="text-align:center;">Qty</th>
                                        <th style="text-align:center;">Unit Price</th>
                                        <th style="text-align:center;">Total Price</th>
                                        <th style="width:30px"></th>
                                    </tr>
                                    <?php
                                    foreach ($_SESSION["cart_item"] as $item) {
                                        $item_price = $item["quantity"] * $item["price"];
                                        $img = "C:xampphtdocsNewChapteruploads";
                                        $img_str = str_replace($img, "", stripslashes($item["image"]));
                                    ?>
                                        <tr style="background-color: white;">
                                            <td><?php echo "<img class='cart-book-img' src='uploads/" . $img_str . "'>"; ?></td>
                                            <td><?php echo $item["title"]; ?></td>
                                            <td><?php echo $item["quantity"]; ?></td>
                                            <td><?php echo "RM " . $item["price"]; ?></td>
                                            <td><?php echo "RM " . $item_price ?></td>
                                            <td>
                                                <!-- Get method to remove item in Cart -->
                                                <a href="books.php?action=remove&title=<?php echo $item["title"]; ?>" class="btnRemoveAction">
                                                    <i class="fa-solid fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <tr style="height: 5px"></tr>
                                    <?php
                                        $total_quantity += $item["quantity"];
                                        $total_price += ($item["price"] * $item["quantity"]);
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="2" align="right">Total:</td>
                                        <td align="right"><?php echo $total_quantity; ?></td>
                                        <td align="right" colspan="2"><strong><?php echo "RM " . number_format($total_price, 2); ?></strong></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php
                        } else {
                        ?>
                            <!-- When cart is empty  -->
                            <span><b>Your cart is empty!</b></span>
                        <?php
                        }
                        ?>
                    </div>
                    <a <?php if (empty($_SESSION["cart_item"])) { ?> onclick="alert('Please add at least one book!')" <?php } else { ?>href="my-cart.php" <?php } ?>><button id="checkout-button"><b>Checkout</b></button></a>
                </div>

                <!--Account Drop Down List-->
                <div id="account-div" style="display: none">
                    <div class="arrow-up"></div>
                    <a href="my-account.php">My Account</a>
                    <a href="logout.php">Log Out</a>
                </div>

                <div class="mobile-menu" onclick="mobileMenuToggle()">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div>
            </div>
            <!-- Navigation List -->
            <div class="nav-list-div">
                <a href="index.php">
                    <div class="nav-list">H O M E</div>
                </a>
                <a href="about.php">
                    <div class="nav-list">A B O U T<div class="active-line"></div>
                    </div>
                </a>
                <a href="books.php">
                    <div class="nav-list">B O O K S</div>
                </a>
                <a href="donate.php">
                    <div class="nav-list">D O N A T E</div>
                </a>
                <a href="contact.php">
                    <div class="nav-list">C O N T A C T</div>
                </a>
            </div>
            <!-- Mobile Navigation List -->
            <div id="mobile-nav-list-div" style="display:none">
                <a href="index.php">
                    <div class="nav-list">H O M E</div>
                </a>
                <a href="about.php">
                    <div class="nav-list">A B O U T<div class="active-line"></div>
                    </div>
                </a>
                <a href="books.php">
                    <div class="nav-list">B O O K S</div>
                </a>
                <a href="donate.php">
                    <div class="nav-list">D O N A T E</div>
                </a>
                <a href="contact.php">
                    <div class="nav-list">C O N T A C T</div>
                </a>
            </div>

        </div>
    </nav>
</body>
<script>
    if (document.getElementById("shopping-cart-count").innerHTML == "0") {
        document.getElementById("shopping-cart-count").style.display = "none";
    } else {
        document.getElementById("shopping-cart-count").style.display = "block";

    };

    if (localStorage.getItem('shoppingCart') == "shown") {
        showShoppingCart();
    } else if (localStorage.getItem('shoppingCart') == "hidden") {
        hideShoppingCart();
    }

    setInterval(checkSession, 30000); // check session every 30 seconds
    
    // function to automatically log user out when session has expired
    function checkSession() {
        let check = true;
        $.ajax({
            url: "session-timeout.php",
            method: "get",
            data: {
                check: check,
            },
            success: function(timeout) {
                if (timeout == 1) {
                    window.location.href = "logout.php?logout=true";
                }
            }
        });
    }

    function mobileMenuToggle() {
        var x = document.getElementById("mobile-nav-list-div");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }

    function showAccountDiv() {
        var accountDiv = document.getElementById("account-div");
        if (accountDiv.style.display === "none") {
            accountDiv.style.display = "block";
        } else {
            accountDiv.style.display = "none";
        }
    }

    function showShoppingCart() {
        var accountDiv = document.getElementById("shopping-cart-div");
        if (accountDiv.style.display === "none") {
            accountDiv.style.display = "block";
        }
        localStorage.setItem('shoppingCart', "shown");
    }

    function hideShoppingCart() {
        var accountDiv = document.getElementById("shopping-cart-div");
        if (accountDiv.style.display === "block") {
            accountDiv.style.display = "none";
        }
        localStorage.setItem('shoppingCart', "hidden");

    }
</script>

</html>