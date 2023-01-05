<?php
// include "connect-db.php";
// connectDB(); // connect to database 
?>

<html>

<head>
    <title>NewChapter | Books</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body id="scroll-bar">
    <?php
    include "header.php";
    $sort_by = "";
    $product_array = $db_handle->runQuery("SELECT * FROM books ORDER BY title ASC");

    if (!empty($_GET["action"])) {
        switch ($_GET["action"]) {
            case "sort":
                if (isset($_POST["sort"])) {
                    $sort_by = $_POST["sort"];
                }
                if ($sort_by == "alphabetical") {
                    $product_array = $db_handle->runQuery("SELECT * FROM books ORDER BY title ASC");
                } else if ($sort_by == "price-l2h") {
                    $product_array = $db_handle->runQuery("SELECT * FROM books ORDER BY price ASC");
                } else if ($sort_by == "price-h2l") {
                    $product_array = $db_handle->runQuery("SELECT * FROM books ORDER BY price DESC");
                } else if ($sort_by == "year") {
                    $product_array = $db_handle->runQuery("SELECT * FROM books ORDER BY year ASC");
                } else if ($sort_by == "language") {
                    $product_array = $db_handle->runQuery("SELECT * FROM books ORDER BY language ASC");
                }
                break;
            case "filter":
                $genres = [];
                if (isset($_POST["action"])) {
                    array_push($genres, $_POST["action"]);
                }
                if (isset($_POST["adventure"])) {
                    array_push($genres, $_POST["adventure"]);
                }
                if (isset($_POST["animation"])) {
                    array_push($genres, $_POST["animation"]);
                }
                if (isset($_POST["biography"])) {
                    array_push($genres, $_POST["biography"]);
                }
                if (isset($_POST["children"])) {
                    array_push($genres, $_POST["children"]);
                }
                if (isset($_POST["comedy"])) {
                    array_push($genres, $_POST["comedy"]);
                }
                if (isset($_POST["cooking"])) {
                    array_push($genres, $_POST["cooking"]);
                }
                if (isset($_POST["crime"])) {
                    array_push($genres, $_POST["crime"]);
                }
                if (isset($_POST["fantasy"])) {
                    array_push($genres, $_POST["fantasy"]);
                }
                if (isset($_POST["history"])) {
                    array_push($genres, $_POST["history"]);
                }
                if (isset($_POST["horror"])) {
                    array_push($genres, $_POST["horror"]);
                }
                if (isset($_POST["mystery"])) {
                    array_push($genres, $_POST["mystery"]);
                }
                if (isset($_POST["romance"])) {
                    array_push($genres, $_POST["romance"]);
                }
                if (isset($_POST["sci-fi"])) {
                    array_push($genres, $_POST["sci-fi"]);
                }
                if (isset($_POST["sport"])) {
                    array_push($genres, $_POST["sport"]);
                }
                if (isset($_POST["travel"])) {
                    array_push($genres, $_POST["travel"]);
                }

                $filter_array = [];
                $count = 0;
                $query = 'SELECT * FROM books WHERE ';
                if (isset($_POST['filter_category'])) {
                    $filter_category = $_POST['filter_category'];
                    $query .= $filter_category . "='1'";
                    $count += 1;
                    if ($filter_category == "newArrival") {
                        array_push($filter_array, "New Arrivals");
                    } else if ($filter_category == "premiumPick") {
                        array_push($filter_array, "Premium Picks");
                    } else if ($filter_category == "clearanceBook") {
                        array_push($filter_array, "Clearance Books");
                    }
                }

                if (count($genres) > 0) {
                    if ($count >= 1) {
                        $genre_query = " AND (";
                    } else {
                        $genre_query = " (";
                    }
                    foreach ($genres as $value) {
                        $genre_query .= 'genre LIKE ';
                        $genre_query .= "'%" . $value . "%'";
                        $genre_query .= ' OR ';
                        array_push($filter_array, $value);
                    }
                    $offset = strlen($genre_query) - 4;
                    $genre_query = substr($genre_query, 0, $offset);
                    $genre_query .= ')';
                    $query .= $genre_query;
                    $count += 1;
                }

                if (!empty($_POST['min-price'])) {
                    if ($count >= 1) {
                        $query .= " AND ";
                    }
                    $min_price = $_POST['min-price'];
                    $query .= "price >= " . $min_price;
                    $count += 1;
                }

                if (!empty($_POST['max-price'])) {
                    if ($count >= 1) {
                        $query .= " AND ";
                    }
                    $max_price = $_POST['max-price'];
                    $query .= "price <= " . $max_price;
                    $count += 1;
                }

                if ($count > 0) {
                    $product_array = $db_handle->runQuery($query);
                } else {
                    $product_array = $db_handle->runQuery("SELECT * FROM books ORDER BY title ASC");
                }

                break;
            case "empty":
                break;
        }
    }
    ?>
    <div class="wrapper">
        <div class="title-container">B O O K S</div>
        <!-- Filter Section-->
        <input class="search-bar" onkeyup="searchBooks()" type="text" id="searchInput" placeholder="Search book, author, genre, year, ISBN...">
        <div class="search-bar-icon">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
    </div>
    <div style="height:30px;width:100%"></div>

    <div class="wrapper3">
        <div class="filter-section">
            <form action="books.php?action=filter" method="post">
                <div class="filter-category">
                    <b>Shop by Genre &nbsp;</b><i class="fa-solid fa-tags"></i><br>
                    <span style="font-size: 14px;color:darkslategray"><br>(Select up to 4 genres)</span>
                    <!-- <i class="fa-solid fa-angle-up"></i><br> -->
                </div>
                <div class="filter-category" id="genre-category">
                    <input class="genre-check" type="checkbox" id="action" name="action" value="Action">
                    <label for="action">Action</label><br>
                    <input class="genre-check" type="checkbox" id="adventure" name="adventure" value="Adventure">
                    <label for="action">Adventure</label><br>
                    <input class="genre-check" type="checkbox" id="animation" name="animation" value="Animation">
                    <label for="animation">Animation</label><br>
                    <input class="genre-check" type="checkbox" id="biography" name="biography" value="Biography">
                    <label for="biography">Biography</label><br>
                    <input class="genre-check" type="checkbox" id="children" name="children" value="Children">
                    <label for="sport">Children</label><br>
                    <input class="genre-check" type="checkbox" id="comedy" name="comedy" value="Comedy">
                    <label for="comedy">Comedy</label><br>
                    <input class="genre-check" type="checkbox" id="cooking" name="cooking" value="Cooking">
                    <label for="cooking">Cooking</label><br>
                    <input class="genre-check" type="checkbox" id="crime" name="crime" value="Crime">
                    <label for="crime">Crime</label><br>
                    <input class="genre-check" type="checkbox" id="fantasy" name="fantasy" value="Fantasy">
                    <label for="fantasy">Fantasy</label><br>
                    <input class="genre-check" type="checkbox" id="history" name="history" value="History">
                    <label for="history">History</label><br>
                    <input class="genre-check" type="checkbox" id="horror" name="horror" value="Horror">
                    <label for="horror">Horror</label><br>
                    <input class="genre-check" type="checkbox" id="mystery" name="mystery" value="Mystery">
                    <label for="mystery">Mystery</label><br>
                    <input class="genre-check" type="checkbox" id="romance" name="romance" value="Romance">
                    <label for="romance">Romance</label><br>
                    <input class="genre-check" type="checkbox" id="sci-fi" name="sci-fi" value="Sci-fi">
                    <label for="sci-fi">Sci-Fi</label><br>
                    <input class="genre-check" type="checkbox" id="sport" name="sport" value="Sport">
                    <label for="sport">Sport</label><br>
                    <input class="genre-check" type="checkbox" id="travel" name="travel" value="Travel">
                    <label for="travel">Travel</label><br>
                </div>

                <div class="filter-category">
                    <input type="radio" id="new_arrivals" name="filter_category" value="newArrival">
                    <b>New Arrivals &nbsp;</b><i class="fa-solid fa-clock-rotate-left"></i>
                </div>

                <div class="filter-category">
                    <input type="radio" id="clearance" name="filter_category" value="clearanceBook">
                    <b>Clearance Books &nbsp;</b><i class="fa-solid fa-hourglass-end"></i>
                </div>

                <div class="filter-category">
                    <input type="radio" id="premium" name="filter_category" value="premiumPick">
                    <b>Premium Picks &nbsp;</b><i class="fa-solid fa-star"></i>
                </div>

                <div class="filter-category">
                    <b>Price &nbsp;</b><i class="fa-solid fa-coins"></i><br><br>
                    <div class="price" style="display:inline-block">
                        Min<br>
                        <input type="number" id="min-price" name="min-price">
                    </div>
                    <div class="price" style="display:inline-block">
                        Max<br>
                        <input type="number" id="max-price" name="max-price">
                    </div>
                </div>

                <br>
                <input id='apply-button' type="submit" value="Apply"/>
            </form>
            <a href="books.php"><button id="reset-button">Reset</button></a>
        </div>

        <!-- Book Catalogue-->
        <div class="book-catalogue">
            <div class="filter">
                Applied filters(s):
                <?php
                if (!empty($filter_array)) {
                    foreach ($filter_array as $value) {
                        echo '<div class="selected-filter">';
                        echo $value;
                        echo '</div>';
                    }
                } else {
                    echo '<div class="selected-filter">No filters applied</div>';
                }
                ?>
            </div>
            <div class="filter-right">
                <i class="filter-icon fa-solid fa-arrow-down-wide-short"></i>
                <form action="books.php?action=sort" method="post">
                    <select onchange="this.form.submit()" name="sort" id="sort">
                        <option value="" disabled selected hidden>Sort By</option>
                        <option <?= $sort_by == 'alphabetical' ? ' selected="selected"' : ''; ?> value="alphabetical">Alphabetical</option>
                        <option <?= $sort_by == 'price-l2h' ? ' selected="selected"' : ''; ?> value="price-l2h">Price: Low to High</option>
                        <option <?= $sort_by == 'price-h2l' ? ' selected="selected"' : ''; ?> value="price-h2l">Price: High to Low</option>
                        <option <?= $sort_by == 'language' ? ' selected="selected"' : ''; ?> value="language">Language</option>
                        <option <?= $sort_by == 'year' ? ' selected="selected"' : ''; ?> value="year">Year</option>
                    </select>
                </form>
            </div>
            <div class="book-display" id="scroll-bar">
                <div id="no-books-display" style="display:none">
                    <div style="height:100px;width:100%"></div>
                    <img src="img/no-books-found.png"><br>No search results found.
                </div>
                <div id='book-display' style="display:block">
                    <?php
                    if (!empty($product_array)) {
                        foreach ($product_array as $k => $v) {
                            if ($product_array[$k]["inStock"] > 0) {
                                $img = "C:xampphtdocsNewChapteruploads";
                                $img_str = str_replace($img, "", stripslashes($product_array[$k]["imageDirectory"]));
                    ?>
                                <div class="book-box" id="book-box-title">
                                    <!-- mixture of Get and post method. Get method for adding and removing items  -->
                                    <form method="post" action="books.php?action=add&title=<?php echo $product_array[$k]["title"]; ?>">
                                        <div class="product-tile-footer">
                                            <?php echo "<a href='book-description.php?book=" . $product_array[$k]["title"] . "'>
                                            <img class='book-box-img' src='uploads/" . $img_str . "'><br>"; ?>
                                            <p class="img_link">View Book Details</p></a>

                                            <div class="book-description">
                                                <div style='height:18px;text-align:center'><b><?php echo "<span class='book-title'>" . 
                                                $product_array[$k]["title"] . "</span>"; ?></b></div><br><br>
                                                <span class="book-other-info" hidden>
                                                    <?php
                                                    echo $product_array[$k]["author"];
                                                    echo $product_array[$k]["publisher"];
                                                    echo $product_array[$k]["isbn"];
                                                    echo $product_array[$k]["genre"];
                                                    echo $product_array[$k]["year"];
                                                    echo $product_array[$k]["language"];
                                                    ?>
                                                </span>
                                                <span style='color:gray'>Price:</span> RM <?php echo $product_array[$k]["price"]; ?></span><br>
                                                <span style='color:gray' name="stock">Stock:</span> <?php echo $product_array[$k]["inStock"]; ?></span>
                                            </div>
                                            <div class="cart-action">
                                                <input type="number" min="0" class="product-quantity" name="quantity" value="1" 
                                                max="<?php echo $product_array[$k]["inStock"]; ?>" />
                                                <!-- Post method for adding to Cart  -->
                                                <input id='add-to-cart' type="submit" value="Add to Cart" class="btnAddAction" />
                                            </div>
                                        </div>
                                    </form>
                                </div>
                        <?php
                            }
                        }
                    } else {
                        ?>
                        <div style="width:100%;font-size: 20px;font-weight: bold;text-align: center;display:block">
                            <div style="height:100px;width:100%"></div>
                            <img style="width:20%" src="img/no-books-found.png"><br>No search results found.
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>

    </div>
    </div>
    <script>
        var checks = document.querySelectorAll(".genre-check");
        var max = 4;
        for (var i = 0; i < checks.length; i++)
            checks[i].onclick = selectiveCheck;

        function selectiveCheck(event) {
            var checkedChecks = document.querySelectorAll(".genre-check:checked");
            console.log(checkedChecks.length);
            if (checkedChecks.length >= max + 1)
                return false;
        }

        function searchBooks() {
            var input, filter, ul_title, ul_l2h, ul_h2l, ul_year, li_title, li_l2h, li_h2l, li_year, a, i, txtValue, count;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            ul = document.getElementById("book-display");
            li_title = ul.getElementsByClassName("book-box");
            count = 0;

            for (i = 0; i < li_title.length; i++) {
                a = li_title[i].getElementsByClassName("book-title")[0];
                b = li_title[i].getElementsByClassName("book-other-info")[0];
                txtValue = a.textContent || a.innerText;
                txtValueB = b.textContent || b.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1 || txtValueB.toUpperCase().indexOf(filter) > -1) {
                    li_title[i].style.display = "";
                    count += 1;
                } else {
                    li_title[i].style.display = "none";
                }
            }

            if (count == 0) {
                document.getElementById("no-books-display").style.display = "block";
            } else {
                document.getElementById("no-books-display").style.display = "none";
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
    <div style="height:580px;width:100%"></div>
    <?php

    include "footer.php";
    ?>
</body>

</html>