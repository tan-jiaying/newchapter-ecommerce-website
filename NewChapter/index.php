<?php
session_start();
?>

<html>

<head>
    <title>NewChapter | Home</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body id="scroll-bar">
    <?php
    include "header.php";
    ?>
    <!--Slide Show-->
    <div class="slide">
        <div class="slide-content">
            <h1>GIVE THESE BOOKS A SECOND CHANCE</h1>
            <!-- <h1 class="slide-text">Give these books a second chance</h1> -->
            <a href="books.php"><button id="shop-now-button">SHOP NOW</button></a>
        </div>
        <img class="slide-img" src="img/home-image/slide1.svg">
    </div>
    <div class="wrapper2">
        <!--New Arrivals-->
        <div style="height:50px;width:100%"></div>
        <h4 class="main-page-book-type">New Arrivals</h4>
        <div id="slideLeft" class="slide-icon"><i class="fa-solid fa-circle-left"></i></div>
        <div class="book-category" id="book-category-1">
            <?php
            $product_array = $db_handle->runQuery("SELECT * FROM books WHERE newArrival='" . 1 . "'");
            if (!empty($product_array)) {
                foreach ($product_array as $k => $v) {
                    if ($product_array[$k]["inStock"] > 0) {
                        $img = "C:xampphtdocsNewChapteruploads";
                        $img_str = str_replace($img, "", stripslashes($product_array[$k]["imageDirectory"]));

            ?>
                        <div class="book-box" id="book-box-title">
                            <!-- mixture of Get and post method. Get method for adding and removing items  -->
                            <div class="product-tile-footer">
                                <?php echo "<a href='book-description.php?book=" . $product_array[$k]["title"] . "'><img class='book-box-img' src='uploads/" . $img_str . "'><br>"; ?>
                                <p class="img_link">View Book Details</p></a>

                                <div class="book-description" style="padding-bottom:25px">
                                    <div style='height:18px;text-align:center;'><b><?php echo "<span class='book-title'>" . $product_array[$k]["title"] . "</span>"; ?></b></div><br><br>
                                    <span style='color:gray'>Price:</span> RM <?php echo $product_array[$k]["price"]; ?></span><br>
                                    <span style='color:gray'>Stock:</span> <?php echo $product_array[$k]["inStock"]; ?></span>
                                </div>
                            </div>
                            </form>
                        </div>
            <?php
                    }
                }
            }
            ?>
        </div>
        <div id="slideRight" class="slide-icon"><i class="fa-solid fa-circle-right"></i></div>

        <div style="height:30px;width: 100%"></div>

        <!--Clearance Books-->
        <h4 class="main-page-book-type">Clearance Books</h4>
        <div id="slideLeft2" class="slide-icon"><i class="fa-solid fa-circle-left"></i></div>
        <div class="book-category" id="book-category-2">
            <?php

            $product_array = $db_handle->runQuery("SELECT * FROM books WHERE clearanceBook='" . 1 . "'");
            if (!empty($product_array)) {
                foreach ($product_array as $k => $v) {
                    $img = "C:xampphtdocsNewChapteruploads";
                    $img_str = str_replace($img, "", stripslashes($product_array[$k]["imageDirectory"]));

            ?>
                    <div class="book-box" id="book-box-title">
                        <!-- mixture of Get and post method. Get method for adding and removing items  -->
                        <div class="product-tile-footer">
                            <?php echo "<a href='book-description.php?book=" . $product_array[$k]["title"] . "'><img class='book-box-img' src='uploads/" . $img_str . "'><br>"; ?>
                            <p class="img_link">View Book Details</p></a>

                            <div class="book-description" style="padding-bottom:25px">
                                <div style='height:18px;text-align:center'><b><?php echo "<span class='book-title'>" . $product_array[$k]["title"] . "</span>"; ?></b></div><br><br>
                                <span style='color:gray'>Price:</span> RM <?php echo $product_array[$k]["price"]; ?></span><br>
                                <span style='color:gray'>Stock:</span> <?php echo $product_array[$k]["inStock"]; ?></span>
                            </div>
                        </div>
                        </form>
                    </div>
            <?php
                }
            }
            ?>
        </div>
        <div id="slideRight2" class="slide-icon"><i class="fa-solid fa-circle-right"></i></div>

        <div style="height:30px;width: 100%"></div>

        <!--Premium Picks-->
        <h4 class="main-page-book-type">Premium Picks</h4>
        <div id="slideLeft3" class="slide-icon"><i class="fa-solid fa-circle-left"></i></div>
        <div class="book-category" id="book-category-3">

            <?php
            $product_array = $db_handle->runQuery("SELECT * FROM books WHERE premiumPick='" . 1 . "'");
            if (!empty($product_array)) {
                foreach ($product_array as $k => $v) {
                    $img = "C:xampphtdocsNewChapteruploads";
                    $img_str = str_replace($img, "", stripslashes($product_array[$k]["imageDirectory"]));

            ?>
                    <div class="book-box" id="book-box-title">
                        <!-- mixture of Get and post method. Get method for adding and removing items  -->
                        <div class="product-tile-footer">
                            <?php echo "<a href='book-description.php?book=" . $product_array[$k]["title"] . "'><img class='book-box-img' src='uploads/" . $img_str . "'><br>"; ?>
                            <p class="img_link">View Book Details</p></a>

                            <div class="book-description" style="padding-bottom:25px">
                                <div style='height:18px;text-align:center'><b><?php echo "<span class='book-title'>" . $product_array[$k]["title"] . "</span>"; ?></b></div><br><br>
                                <span style='color:gray'>Price:</span> RM <?php echo $product_array[$k]["price"]; ?></span><br>
                                <span style='color:gray'>Stock:</span> <?php echo $product_array[$k]["inStock"]; ?></span>
                            </div>
                        </div>
                        </form>
                    </div>
            <?php
                }
            }
            ?>
        </div>
        <div id="slideRight3" class="slide-icon"><i class="fa-solid fa-circle-right"></i></div>

    </div>
    <div style="height:100px;width: 100%"></div>
    <script>
        const buttonRight = document.getElementById('slideRight');
        const buttonLeft = document.getElementById('slideLeft');

        buttonRight.onclick = function() {
            document.getElementById('book-category-1').scrollLeft += 200;
        };
        buttonLeft.onclick = function() {
            document.getElementById('book-category-1').scrollLeft -= 200;
        };

        const buttonRight2 = document.getElementById('slideRight2');
        const buttonLeft2 = document.getElementById('slideLeft2');

        buttonRight2.onclick = function() {
            document.getElementById('book-category-2').scrollLeft += 200;
        };
        buttonLeft2.onclick = function() {
            document.getElementById('book-category-2').scrollLeft -= 200;
        };

        const buttonRight3 = document.getElementById('slideRight3');
        const buttonLeft3 = document.getElementById('slideLeft3');

        buttonRight3.onclick = function() {
            document.getElementById('book-category-3').scrollLeft += 200;
        };
        buttonLeft3.onclick = function() {
            document.getElementById('book-category-3').scrollLeft -= 200;
        };

        document.addEventListener("DOMContentLoaded", function(event) {
            var scrollpos = localStorage.getItem('scrollpos');
            if (scrollpos) window.scrollTo(0, scrollpos);
        });

        window.onbeforeunload = function(e) {
            localStorage.setItem('scrollpos', window.scrollY);
        };

        localStorage.removeItem("deliveryOption");
        localStorage.removeItem("paymentMethod");
    </script>
    <?php
    include "footer.php";
    ?>
</body>

</html>