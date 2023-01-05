<?php
include "connect-db.php";
connectDB(); // connect to database 

// retrieve donations from database 
$query = "SELECT * FROM donations";
$result = mysqli_query($handler, $query);
$donations = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<html>

<head>
    <title>NewChapter | Donation List</title>
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
            <div class="admin-nav">Orders<i class="fa-solid fa-cart-shopping"></i></div>
        </a>
        <a href="donation-list.php">
            <div class="admin-nav" style="background-color:#006000">Donations<i class="fa-solid fa-hand-holding-dollar"></i></div>
        </a>
        <a href="logout.php">
            <div class="admin-nav admin-logout">Log Out<i class="fa-solid fa-right-from-bracket"></i></div>
        </a>
    </div>

    <div class="right-panel">
        <div class="title-container">D O N A T I O N S</div>
        <?php
        // callback message from other pages 
        if (isset($_GET['msg'])) {
            $adminMessage = $_GET['msg'];

            // display message accordingly 
            if ($adminMessage == "editdonation") {
                echo '<p class="admin-p" id="admin-msg">Donation record has been edited.</p><br>';
            } else if ($adminMessage == "deletedonation") {
                echo '<p class="admin-p" id="admin-msg">Donation record has been deleted.</p><br>';
            }

            unset($_GET['msg']);
        }
        ?>
        <input class="adminSearchInput" type="text" id="donationSearchInput" onkeyup="search()" placeholder="Search donation ...">
        <br><br>

        <!--Show all donations in a table-->
        <div>
            <?php
            if (count($donations) == 0) {
                $display = "<p>No donations to display.</p>";
            } else {
                $display = "<table class='admin-table' style='margin-top:25px' id='donation-list'>\n";
                $display .= "<tbody id='scroll-bar'>\n";
                $display .= "<tr>
                                            <th width='150'>Donation ID</th>
                                            <th width='150'>User ID</th>
                                            <th width='200'>Donation Method</th>
                                            <th width='200'>Donation Date</th>
                                            <th width='150'>Status</th>
                                            <th width='100'></th>
                                        </tr>\n
                                        </tbody>\n
                                        <tbody id='scroll-bar'>";
                foreach ($donations as $donation) {
                    if ($donation['donationID'] < 10) {
                        $donationID = "D00" . $donation['donationID'];
                    } else if ($donation['donationID'] < 100) {
                        $donationID = "D0" . $donation['donationID'];
                    } else {
                        $donationID = "D" . $donation['donationID'];
                    }
                    $display .= "<tr><td width='150'>" . $donationID . "</td><td width='150'>" . $donation['userID'] . "</td><td width='200'>" . $donation['donationMethod'] . "</td><td width='200'>" . $donation['donationDate'] . "</td><td width='150'>" . $donation['status'] .
                        "</td><td width='100'>
                                                <a href='donation-record.php?donationID=" . $donation['donationID'] . "'><button>View</button></a>
                                            </td></tr>\n";
                }
                $display .= "</tbody></table>";
                $display .= "<p class='admin-p' id='count'>" . count($donations) . " donation(s)</p>";
            }

            echo $display;
            ?>

            <script>
                // function to display search results
                function search() {
                    // initialize variables
                    var searchInput = document.getElementById("donationSearchInput");
                    var filter = searchInput.value.toUpperCase();
                    var table = document.getElementById("donation-list");
                    var tr = table.getElementsByTagName("tr");
                    var count = document.getElementById("count");
                    var donationID, userID, donationMethod, donationDate, status;
                    var countTemp = 0;

                    // check if match is found
                    for (i = 0; i < tr.length; i++) {
                        var td_donationID = tr[i].getElementsByTagName("td")[0];
                        var td_userID = tr[i].getElementsByTagName("td")[1];
                        var td_donationMethod = tr[i].getElementsByTagName("td")[2];
                        var td_donationDate = tr[i].getElementsByTagName("td")[3];
                        var td_status = tr[i].getElementsByTagName("td")[4];

                        if (td_donationID || td_userID || td_donationMethod || td_donationDate || td_status) {
                            donationID = td_donationID.textContent || td_donationID.innerText;
                            userID = td_userID.textContent || td_userID.innerText;
                            donationMethod = td_donationMethod.textContent || td_donationMethod.innerText;
                            donationDate = td_donationDate.textContent || td_donationDate.innerText;
                            status = td_status.textContent || td_status.innerText;

                            if (donationID.toUpperCase().indexOf(filter) > -1) { // matched donation ID
                                tr[i].style.display = "";
                                countTemp++;
                            } else if (userID.toUpperCase().indexOf(filter) > -1) { // matched user ID
                                tr[i].style.display = "";
                                countTemp++;
                            } else if (donationMethod.toUpperCase().indexOf(filter) > -1) { // matched donation method
                                tr[i].style.display = "";
                                countTemp++;
                            } else if (donationDate.toUpperCase().indexOf(filter) > -1) { // matched donation date
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
                        count.innerHTML = countTemp + " donation(s)";
                    }
                }
            </script>
        </div>
    </div>

</body>

</html>