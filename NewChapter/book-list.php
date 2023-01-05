<?php
include "connect-db.php";
connectDB(); // connect to database 

// retreive books from database 
$query = "SELECT * FROM books";
$result = mysqli_query($handler, $query);
$books = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<html>

<head>
    <title>NewChapter | Book List</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://kit.fontawesome.com/ffaaf7e5e9.js" crossorigin="anonymous"></script>
</head>

<body id="scroll-bar">
    <?php
    include "admin-header.php";
    ?>

    <div class="left-panel"  style="height: 800px">
        <div style="height:100px;width:100%"></div>

        <a href="dashboard.php">
            <div class="admin-nav">Dashboard<i class="fa-solid fa-chart-line"></i></div>
        </a>
        <a href="user-list.php">
            <div class="admin-nav">Users<i class="fa-solid fa-users"></i></div>
        </a>
        <a href="book-list.php">
            <div class="admin-nav" style="background-color:#006000">Books<i class="fa-solid fa-book"></i></div>
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
        <div class="title-container">B O O K S</div>
        <?php
        // callback message from other pages 
        if (isset($_GET['msg'])) {
            $adminMessage = $_GET['msg'];

            // display message accordingly 
            if ($adminMessage == "addbook") {
                echo '<p class="admin-p" id="admin-msg">New book has been added.</p><br>';
            } else if ($adminMessage == "editbook") {
                echo '<p class="admin-p" id="admin-msg">Book record has been edited.</p><br>';
            } else if ($adminMessage == "deletebook") {
                echo '<p class="admin-p" id="admin-msg">Book record has been deleted.</p><br>';
            }

            unset($_GET['msg']);
        }
        ?>
        <input class="adminSearchInput" type="text" id="bookSearchInput" onkeyup="search()" placeholder="Search book ...">
        <a href="add-book.php"><button>+ NEW BOOK</button></a>
        <br><br>

        <!--Show all books in a table-->
        <div>
            <?php
            if (count($books) == 0) {
                $display = "<p>No books to display.</p>";
            } else {
                $display = "<table class='admin-table' style='margin-top: 25px' id='book-list'>\n";
                $display .= "<tbody id='scroll-bar'>\n";
                $display .= "<tr>
                                <th width='100'>Book ID</th>
                                <th width='400'>Title</th>
                                <th width='200'>Author</th>
                                <th width='200'>Publisher</th>
                                <th width='100'></th>
                            </tr>\n
                            </tbody>\n<tbody id='scroll-bar'>";
                foreach ($books as $book) {
                    if ($book['bookID'] < 10) {
                        $bookID = "B00" . $book['bookID'];
                    } else if ($book['bookID'] < 100) {
                        $bookID = "B0" . $book['bookID'];
                    } else {
                        $bookID = "B" . $book['bookID'];
                    }
                    $display .= "<tr><td width='100'>" . $bookID . "</td><td width='400'>" . $book['title'] . "</td><td width='200'>"
                        . $book['author'] . "</td><td width='200'>" . $book['publisher'] . "</td><td width='100'>
                                <a href='book-record.php?bookID=" . $book['bookID'] . "'><button>View</button></a>
                                </td></tr>\n";
                }
                $display .= "</tbody>";
                $display .= "</table>";
                $display .= "<p class='admin-p' id='count'>" . count($books) . " book(s)</p>";
            }

            echo $display;
            ?>

            <script>
                // function to display search results
                function search() {
                    // initialize variables
                    var searchInput = document.getElementById("bookSearchInput");
                    var filter = searchInput.value.toUpperCase();
                    var table = document.getElementById("book-list");
                    var tr = table.getElementsByTagName("tr");
                    var count = document.getElementById("count");
                    var bookID, title, author;
                    var countTemp = 0;

                    // check if match is found
                    for (i = 0; i < tr.length; i++) {
                        var td_bookID = tr[i].getElementsByTagName("td")[0];
                        var td_title = tr[i].getElementsByTagName("td")[1];
                        var td_author = tr[i].getElementsByTagName("td")[2];
                        var td_publisher = tr[i].getElementsByTagName("td")[3];

                        if (td_bookID || td_title || td_author || td_publisher) {
                            bookID = td_bookID.textContent || td_bookID.innerText;
                            title = td_title.textContent || td_title.innerText;
                            author = td_author.textContent || td_author.innerText;
                            publisher = td_publisher.textContent || td_publisher.innerText;

                            if (bookID.toUpperCase().indexOf(filter) > -1) { // matched book ID 
                                tr[i].style.display = "";
                                countTemp++;
                            } else if (title.toUpperCase().indexOf(filter) > -1) { // matched title
                                tr[i].style.display = "";
                                countTemp++;
                            } else if (author.toUpperCase().indexOf(filter) > -1) { // matched author
                                tr[i].style.display = "";
                                countTemp++;
                            } else if (publisher.toUpperCase().indexOf(filter) > -1) { // matched pubisher
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
                        count.innerHTML = countTemp + " book(s)";
                    }
                }
            </script>
        </div>
    </div>



</body>

</html>