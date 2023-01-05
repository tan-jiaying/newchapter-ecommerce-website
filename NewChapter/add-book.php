<?php
include "connect-db.php";
connectDB(); // connect to database 

include "admin-header.php";

// check if form is submitted 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // perform form validation 
    $errors = 0; // error count

    // validate title 
    $pattern = "/^[A-Za-z0-9\s\-_,\.;:()\'\&@]+$/";
    if (empty($_POST['title'])) {
        $titleError = "Please enter a book title";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['title'])) {
        $titleError = "Title can only contain alphabets, numbers, white spaces, and special characters -_,.:;()'&@";
        $errors += 1;
    } else {
        $title = mysqli_real_escape_string($handler, trim($_POST['title']));

        // create image file only when $title is set 
        // because $title is used in image filename
        if (empty($_FILES['upload-file']['name'])) { // no file uploaded
            $imageError = "No file uploaded";
            $errors += 1;
        } else { // check file extension
            $allowed = array('jpg', 'jpeg', 'png', 'gif');
            $filename = $_FILES['upload-file']['name'];
            $extension = pathInfo($filename, PATHINFO_EXTENSION);
            if (!in_array($extension, $allowed)) {
                $imageError = "Only extensions .jpg, .jpeg, .png, .gif are allowed. Please upload another file";
                $errors += 1;
            } else {
                // create directory path for uploaded image file 
                $uploadsDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
                $targetFileName = $title . '.png';
                $targetFileDirectory = $uploadsDir . DIRECTORY_SEPARATOR . $title . '.png';

                // store image file in the directory created
                if (array_key_exists('upload-file', $_FILES)) {
                    $uploadInfo = $_FILES['upload-file'];

                    switch ($uploadInfo['error']) {
                        case UPLOAD_ERR_OK: // file uploaded
                            mime_content_type($uploadInfo['tmp_name']);
                            move_uploaded_file($uploadInfo['tmp_name'], $targetFileDirectory);
                            break;
                        case UPLOAD_ERR_NO_FILE: // no file uploaded
                            echo 'No file was uploaded.';
                            break;
                    }
                }

                $targetFileName = mysqli_real_escape_string($handler, $targetFileName);
                $targetFileDirectory = mysqli_real_escape_string($handler, $targetFileDirectory);
            }
        }
    }

    // validate description
    if (empty($_POST['description'])) {
        $descriptionError = "Please enter a description";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['description'])) {
        $descriptionError = "Description can only contain alphabets, numbers, white spaces, and special characters -_,.:;()'&@";
        $errors += 1;
    } else {
        $description = mysqli_real_escape_string($handler, trim($_POST['description']));
    }

    // validate author 
    $pattern = "/^[A-Za-z\s\.\'@-]*$/";
    if (empty($_POST['author'])) {
        $authorError = "Please enter the author's name";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['author'])) {
        $authorError = "Author's name can only contain alphabets, white spaces, and special characters -.'@";
        $errors += 1;
    } else {
        $author = mysqli_real_escape_string($handler, trim($_POST['author']));
    }

    // validate publisher 
    $pattern = "/^[A-Za-z\s\.\'@-]*$/";
    if (empty($_POST['publisher'])) {
        $publisherError = "Please enter the publisher's name";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['publisher'])) {
        $publisherError = "Publisher's name can only contain alphabets, white spaces, and special characters -.'@";
        $errors += 1;
    } else {
        $publisher = mysqli_real_escape_string($handler, trim($_POST['publisher']));
    }

    // check if at least one checkbox is checked for genre
    if (
        !isset($_POST['action']) && !isset($_POST['fantasy']) && !isset($_POST['adventure']) && !isset($_POST['history']) && !isset($_POST['animation']) && !isset($_POST['horror']) && !isset($_POST['biography']) && !isset($_POST['mystery'])
        && !isset($_POST['comedy']) && !isset($_POST['romance']) && !isset($_POST['crime']) && !isset($_POST['sci-fi']) && !isset($_POST['cooking']) && !isset($_POST['sport']) && !isset($_POST['documentary']) && !isset($_POST['travel'])
    ) {
        $genreError = "At least one checkbox must be checked";
        $errors += 1;
    }

    // validate year 
    if (empty($_POST['year'])) {
        $yearError = "Please enter the publication year";
        $errors += 1;
    } else if (strlen((string)$_POST['year']) != 4) {
        $yearError = "Publication year must be exactly 4 digits";
        $errors += 1;
    } else {
        $pattern = "/^(18|19|20)\d{2}$/";
        if (!preg_match($pattern, $_POST['year'])) {
            $yearError = "Publication year must be from 1800 onwards";
            $errors += 1;
        } else {
            $year = $_POST['year'];
        }
    }

    // validate price 
    if (empty($_POST['price'])) {
        $priceError = "Please enter the price";
        $errors += 1;
    } else if ($_POST['price'] <= 0 || $_POST['price'] >= 100) {
        $priceError = "Price must be greater than 0 and lesser than 100";
        $errors += 1;
    } else {
        $price = $_POST['price'];
    }

    // validate isbn 
    $pattern = "/^978-\d{1}-\d{2}-\d{6}-\d{1}$/";
    if (empty($_POST['isbn'])) {
        $isbnError = "Please enter the ISBN";
        $errors += 1;
    } else if (!preg_match($pattern, $_POST['isbn'])) {
        $isbnError = "ISBN must be of format 978-X-XX-XXXXXX-X, where X are numbers";
        $errors += 1;
    } else {
        $isbn = mysqli_real_escape_string($handler, trim($_POST['isbn']));
    }

    // validate stock 
    if (empty($_POST['stock'])) {
        $stockError = "Please enter the number of stocks";
        $errors += 1;
    } else if ($_POST['stock'] < 0) {
        $stockError = "Number of stocks must be more than 0";
        $errors += 1;
    } else {
        $stock = $_POST['stock'];
    }

    // check image file
    if (empty($_FILES['upload-file']['name'])) { // no file uploaded
        $imageError = "No file uploaded";
        $errors += 1;
    } else { // check file extension
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['upload-file']['name'];
        $extension = pathInfo($filename, PATHINFO_EXTENSION);
        if (!in_array($extension, $allowed)) {
            $imageError = "Only extensions .jpg, .jpeg, .png, .gif are allowed. Please upload another file";
            $errors += 1;
        }
    }


    // if no errors found 
    if ($errors == 0) {
        $language = $_POST['language'];

        // connect checkbox values of genre into a string 
        $genres = array();
        if (isset($_POST['action'])) {
            array_push($genres, $_POST['action']);
        }
        if (isset($_POST['adventure'])) {
            array_push($genres, $_POST['adventure']);
        }
        if (isset($_POST['animation'])) {
            array_push($genres, $_POST['animation']);
        }
        if (isset($_POST['biography'])) {
            array_push($genres, $_POST['biography']);
        }
        if (isset($_POST['comedy'])) {
            array_push($genres, $_POST['comedy']);
        }
        if (isset($_POST['crime'])) {
            array_push($genres, $_POST['crime']);
        }
        if (isset($_POST['cooking'])) {
            array_push($genres, $_POST['cooking']);
        }
        if (isset($_POST['documentary'])) {
            array_push($genres, $_POST['documentary']);
        }
        if (isset($_POST['fantasy'])) {
            array_push($genres, $_POST['fantasy']);
        }
        if (isset($_POST['history'])) {
            array_push($genres, $_POST['history']);
        }
        if (isset($_POST['horror'])) {
            array_push($genres, $_POST['horror']);
        }
        if (isset($_POST['mystery'])) {
            array_push($genres, $_POST['mystery']);
        }
        if (isset($_POST['romance'])) {
            array_push($genres, $_POST['romance']);
        }
        if (isset($_POST['sci-fi'])) {
            array_push($genres, $_POST['sci-fi']);
        }
        if (isset($_POST['sport'])) {
            array_push($genres, $_POST['sport']);
        }
        if (isset($_POST['travel'])) {
            array_push($genres, $_POST['travel']);
        }
        $genres = implode(", ", $genres);

        // store new arrival, clearance book, and premium pick as 1 or 0
        $new = $_POST['new'];
        if (isset($new)) {
            $new = 1;
        } else {
            $new = 0;
        }

        $clearance = $_POST['clearance'];
        if (isset($clearance)) {
            $clearance = 1;
        } else {
            $clearance = 0;
        }

        $premium = $_POST['premium'];
        if (isset($premium)) {
            $premium = 1;
        } else {
            $premium = 0;
        }

        // insert inputs into book table 
        $query = "INSERT INTO books (title, description, author, publisher, genre, year, language, price, 
                    isbn, inStock, newArrival, clearanceBook, premiumPick, imageFile, imageDirectory)
                    VALUES ('$title', '$description', '$author', '$publisher', '$genres', '$year', '$language', '$price', 
                    '$isbn', '$stock', '$new', '$clearance', '$premium', '$targetFileName', '$targetFileDirectory')";
        mysqli_query($handler, $query);

        // redirect admin to book list page 
        header("Location: book-list.php?msg=addbook");
        exit();
    }
}
?>

<html>

<head>
    <title>NewChapter | Add Book</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/form-control.css" type="text/css">
</head>

<body id="scroll-bar">
    <div class="left-panel" style="height: 1050px">
        <div style="height:100px; width:100%"></div>

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
        <div class="title-container">A D D&emsp;B O O K</div>
        <div class="summary">
            <!-- Form for admin to add book details-->
            <form id="add-form" action="add-book.php" method="post" enctype="multipart/form-data">
                <div class="form-control">
                    <label for="title">Title:</label>
                    <input class="add-info" type="text" id="title" name="title" maxlength="50" placeholder="The Hobbit" value="<?php if (isset($_POST['title'])) echo $_POST['title'] ?>" required>
                    <?php
                    if (isset($titleError)) {
                        echo '<div class="admin-form-error">';
                        echo $titleError;
                        echo "</div>";
                        unset($titleError);
                    }
                    ?>
                </div>
                <br>

                <div class="form-control">
                    <label for="description" style="vertical-align: top">Description:</label>
                    <textarea class="add-info" id="description" style="height:120px" name="description" rows="4" cols="50" maxlength="5000" placeholder="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur." required><?php if (isset($_POST['description'])) echo $_POST['description'] ?></textarea>
                    <?php
                    if (isset($descriptionError)) {
                        echo '<div class="admin-form-error">';
                        echo $descriptionError;
                        echo "</div>";
                        unset($descriptionError);
                    }
                    ?>
                </div>
                <br>

                <div class="form-control">
                    <label for="author">Author:</label>
                    <input class="add-info" type="text" id="author" name="author" maxlength="50" placeholder="J. R. R. Tolkien" value="<?php if (isset($_POST['author'])) echo $_POST['author'] ?>" required>
                    <?php
                    if (isset($authorError)) {
                        echo '<div class="admin-form-error">';
                        echo $authorError;
                        echo "</div>";
                        unset($authorError);
                    }
                    ?>
                </div>
                <br>

                <div class="form-control">
                    <label for="publisher">Publisher:</label>
                    <input class="add-info" type="text" id="publisher" name="publisher" maxlength="50" placeholder="HarperCollins Publisher" value="<?php if (isset($_POST['publisher'])) echo $_POST['publisher'] ?>" required>
                    <?php
                    if (isset($publisherError)) {
                        echo '<div class="admin-form-error">';
                        echo $publisherError;
                        echo "</div>";
                        unset($publisherError);
                    }
                    ?>
                </div>
                <br>

                <div class="form-control">
                    Genre:&emsp;
                    <?php
                    if (isset($genreError)) {
                        echo '<div class="admin-form-error">';
                        echo $genreError;
                        echo "</div>";
                        unset($genreError);
                    }
                    ?>
                    <br>
                    <input type="checkbox" id="action" name="action" value="Action" <?php if (isset($_POST['action'])) echo 'checked="checked"'; ?>>
                    <label for="action">Action</label>
                    <input type="checkbox" id="fantasy" name="fantasy" value="Fantasy" <?php if (isset($_POST['fantasy'])) echo 'checked="checked"'; ?>>
                    <label for="fantasy">Fantasy</label><br>
                    <input type="checkbox" id="adventure" name="adventure" value="Adventure" <?php if (isset($_POST['adventure'])) echo 'checked="checked"'; ?>>
                    <label for="adventure">Adventure</label>
                    <input type="checkbox" id="history" name="history" value="History" <?php if (isset($_POST['history'])) echo 'checked="checked"'; ?>>
                    <label for="history">History</label><br>
                    <input type="checkbox" id="animation" name="animation" value="Animation" <?php if (isset($_POST['animation'])) echo 'checked="checked"'; ?>>
                    <label for="animation">Animation</label>
                    <input type="checkbox" id="horror" name="horror" value="Horror" <?php if (isset($_POST['horror'])) echo 'checked="checked"'; ?>>
                    <label for="horror">Horror</label><br>
                    <input type="checkbox" id="biography" name="biography" value="Biography" <?php if (isset($_POST['biography'])) echo 'checked="checked"'; ?>>
                    <label for="biography">Biography</label>
                    <input type="checkbox" id="mystery" name="mystery" value="Mystery" <?php if (isset($_POST['mystery'])) echo 'checked="checked"'; ?>>
                    <label for="mystery">Mystery</label><br>
                    <input type="checkbox" id="comedy" name="comedy" value="Comedy" <?php if (isset($_POST['comedy'])) echo 'checked="checked"'; ?>>
                    <label for="comedy">Comedy</label>
                    <input type="checkbox" id="romance" name="romance" value="Romance" <?php if (isset($_POST['romance'])) echo 'checked="checked"'; ?>>
                    <label for="romance">Romance</label><br>
                    <input type="checkbox" id="crime" name="crime" value="Crime" <?php if (isset($_POST['crime'])) echo 'checked="checked"'; ?>>
                    <label for="crime">Crime</label>
                    <input type="checkbox" id="sci-fi" name="sci-fi" value="Sci-fi" <?php if (isset($_POST['sci-fi'])) echo 'checked="checked"'; ?>>
                    <label for="sci-fi">Sci-fi</label><br>
                    <input type="checkbox" id="cooking" name="cooking" value="Cooking" <?php if (isset($_POST['cooking'])) echo 'checked="checked"'; ?>>
                    <label for="cooking">Cooking</label>
                    <input type="checkbox" id="sport" name="sport" value="Sport" <?php if (isset($_POST['sport'])) echo 'checked="checked"'; ?>>
                    <label for="sport">Sport</label><br>
                    <input type="checkbox" id="documentary" name="documentary" value="Documentary" <?php if (isset($_POST['documentary'])) echo 'checked="checked"'; ?>>
                    <label for="documentary">Documentary</label>
                    <input type="checkbox" id="travel" name="travel" value="Travel" <?php if (isset($_POST['travel'])) echo 'checked="checked"'; ?>>
                    <label for="travel">Travel</label>
                </div>
                <br>

                <div class="form-control">
                    <label for="year">Publication Year:</label>
                    <input class="add-info" type="number" id="year" name="year" placeholder="1937" value="<?php if (isset($_POST['year'])) echo $_POST['year'] ?>" required>
                    <?php
                    if (isset($yearError)) {
                        echo '<div class="admin-form-error">';
                        echo $yearError;
                        echo "</div>";
                        unset($yearError);
                    }
                    ?>
                </div>
                <br>

                <label for="language">Language:</label>
                <select name="language" id="language">
                    <option value="English" <?php if (isset($_POST['language']) && $_POST['language'] == "English") echo 'selected="selected"'; ?>>English</option>
                    <option value="Chinese" <?php if (isset($_POST['language']) && $_POST['language'] == "Chinese") echo 'selected="selected"'; ?>>Chinese</option>
                    <option value="Bahasa Melayu" <?php if (isset($_POST['language']) && $_POST['language'] == "Bahasa Melayu") echo 'selected="selected"'; ?>>Bahasa Melayu</option>
                </select><br><br>

                <div class="form-control">
                    <label for="price">Price:</label>
                    <input class="add-info" type="number" id="price" name="price" placeholder="13" value="<?php if (isset($_POST['price'])) echo $_POST['price'] ?>" required>
                    <?php
                    if (isset($priceError)) {
                        echo '<div class="admin-form-error">';
                        echo $priceError;
                        echo "</div>";
                        unset($priceError);
                    }
                    ?>
                </div>
                <br>

                <div class="form-control">
                    <label for="isbn">ISBN:</label>
                    <input class="add-info" type="text" id="isbn" name="isbn" maxlength="17" placeholder="978-X-XX-XXXXXX-X" value="<?php if (isset($_POST['isbn'])) echo $_POST['isbn'] ?>" required>
                    <?php
                    if (isset($isbnError)) {
                        echo '<div class="admin-form-error">';
                        echo $isbnError;
                        echo "</div>";
                        unset($isbnError);
                    }
                    ?>
                </div>
                <br>

                <div class="form-control">
                    <label for="stock">In Stock:</label>
                    <input class="add-info" type="number" id="stock" name="stock" placeholder="2" value="<?php if (isset($_POST['stock'])) echo $_POST['stock'] ?>" required>
                    <?php
                    if (isset($stockError)) {
                        echo '<div class="admin-form-error">';
                        echo $stockError;
                        echo "</div>";
                        unset($stockError);
                    }
                    ?>
                </div>
                <br>

                Other Categories:<br>
                <input type="checkbox" id="new" name="new" value="New Arrival" <?php if (isset($_POST['new'])) echo 'checked="checked"'; ?>>
                <label for="new">New Arrival</label><br>
                <input type="checkbox" id="clearance" name="clearance" value="Clearance Book" <?php if (isset($_POST['clearance'])) echo 'checked="checked"'; ?>>
                <label for="clearance">Clearance Book</label><br>
                <input type="checkbox" id="premium" name="premium" value="Premium Pick" <?php if (isset($_POST['premium'])) echo 'checked="checked"'; ?>>
                <label for="premium">Premium Pick</label><br><br>

                <!--Book Cover Image-->
                <div class="form-control">
                    <label for="upload-file">Cover Image:</label>
                    <input type="file" name="upload-file" id="upload-file">
                    <?php
                    if (isset($imageError)) {
                        echo '<br><div class="admin-form-error">';
                        echo $imageError;
                        echo "</div>";
                        unset($imageError);
                    }
                    ?>
                </div>
                <br><br>

                <input class="add-btn" type="submit" name="add" value="Add Record">
                <a href="book-list.php"><button type="button">Close</button></a>
            </form>
        </div>
    </div>

</body>

</html>