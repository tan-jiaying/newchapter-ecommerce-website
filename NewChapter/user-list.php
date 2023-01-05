<?php
include "connect-db.php";
connectDB(); // connect to database 

// retieve customers from database 
$query = "SELECT * FROM users";
$result = mysqli_query($handler, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<html>

<head>
    <title>NewChapter | User List</title>
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
            <div class="admin-nav" style="background-color:#006000">Users<i class="fa-solid fa-users"></i></div>
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
        <div class="title-container">U S E R S</div>
        <?php
        // callback message from other pages 
        if (isset($_GET['msg'])) {
            $adminMessage = $_GET['msg'];

            // display message accordingly 
            if ($adminMessage == "edituser") {
                echo '<p class="admin-p" id="admin-msg">User record has been edited.</p><br>';
            } else if ($adminMessage == "deleteuser") {
                echo '<p class="admin-p" id="admin-msg">User record has been deleted.</p><br>';
            }

            unset($_GET['msg']);
        }
        ?>
        <input class="adminSearchInput" type="text" id="userSearchInput" onkeyup="search()" placeholder="Search user ...">
        <br><br>

        <!--Show all users in a table-->
        <table class='admin-table' style="margin-top:25px;">
            <tbody id='scroll-bar'>
                <tr>
                    <th width='150px'>User ID</th>
                    <th width='275px'>First Name</th>
                    <th width='275px'>Last Name</th>
                    <th width='150px'>Role</th>
                    <th width='100px'>View</th>
                </tr>
            </tbody>
            <!-- </table> -->
            <?php
            if (count($users) == 0) {
                $display = "<p>No users to display.</p>";
            } else {
                $display = "<table class='admin-table' id='user-list' >\n";
                $display .= "<tbody id='scroll-bar'>\n";
                foreach ($users as $user) {
                    if ($user['userID'] < 10) {
                        $userID = "U00" . $user['userID'];
                    } else if ($user['userID'] < 100) {
                        $userID = "U0" . $user['userID'];
                    } else {
                        $userID = "U" . $user['userID'];
                    }

                    if ($user['role'] == 1) {
                        $role = "Admin";
                    } else {
                        $role = "Customer";
                    }
                    $display .= "<tr><td width='150px'>" . $userID . "</td><td width='275px'>" . $user['fname'] . "</td><td width='275px'>" . $user['lname'] . "</td><td width='150px'>" . $role .
                        "</td><td width='100px'>
                                            <a href='user-record.php?userID=" . $user['userID'] . "'><button>View</button></a>
                                        </td></tr>\n";
                }
                $display .= "</tbody>";
                $display .= "</table>";
                $display .= "<p class='admin-p' id='count'>" . count($users) . " user(s)</p>";
            }

            echo $display;
            ?>

            <script>
                // function to display search results 
                function search() {
                    // initialize variables
                    var searchInput = document.getElementById("userSearchInput");
                    var filter = searchInput.value.toUpperCase();
                    var table = document.getElementById("user-list");
                    var tr = table.getElementsByTagName("tr");
                    var count = document.getElementById("count");
                    var customerID, fname, lname, role;
                    var countTemp = 0;

                    // check if match is found
                    for (i = 0; i < tr.length; i++) {
                        var td_userID = tr[i].getElementsByTagName("td")[0];
                        var td_fname = tr[i].getElementsByTagName("td")[1];
                        var td_lname = tr[i].getElementsByTagName("td")[2];
                        var td_role = tr[i].getElementsByTagName("td")[3];

                        if (td_userID || td_fname || td_lname, td_role) {
                            userID = td_userID.textContent || td_userID.innerText;
                            fname = td_fname.textContent || td_fname.innerText;
                            lname = td_lname.textContent || td_lname.innerText;
                            role = td_role.textContent || td_role.innerText;

                            if (userID.toUpperCase().indexOf(filter) > -1) { // matched user ID
                                tr[i].style.display = "";
                                countTemp++;
                            } else if (fname.toUpperCase().indexOf(filter) > -1) { // matched first name
                                tr[i].style.display = "";
                                countTemp++;
                            } else if (lname.toUpperCase().indexOf(filter) > -1) { // matched last name
                                tr[i].style.display = "";
                                countTemp++;
                            } else if (role.toUpperCase().indexOf(filter) > -1) { // matched role
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
                        count.innerHTML = countTemp + " user(s)";
                    }
                }
            </script>
    </div>

</body>

</html>