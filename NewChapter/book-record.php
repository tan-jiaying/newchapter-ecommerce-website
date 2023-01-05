<?php
include "connect-db.php";
connectDB(); // connect to database 

include "admin-header.php";

// retreive bookID from book-list.php
$ID = $_GET['bookID'];

// retreive book from database 
$query = "SELECT * FROM books WHERE bookID = $ID";
$result = mysqli_query($handler, $query);
$book = mysqli_fetch_assoc($result);

// assign variables to each book detail 
if ($ID < 10) {
    $bookID = "B00" . $book['bookID'];
} else if ($ID < 100) {
    $bookID = "B0" . $book['bookID'];
} else {
    $bookID = "B" . $book['bookID'];
}

$title = $book['title'];
$description = $book['description'];
$author = $book['author'];
$publisher = $book['publisher'];
$genre = $book['genre'];
$year = $book['year'];
$language = $book['language'];
$price = $book['price'];
$isbn = $book['isbn'];
$stock = $book['inStock'];
$new = $book['newArrival'];
$clearance = $book['clearanceBook'];
$premium = $book['premiumPick'];

$others = array();
if ($new == '1') {
    array_push($others, "New Arrival");
}
if ($clearance == '1') {
    array_push($others, "Clearance Book");
}
if ($premium == '1') {
    array_push($others, "Premium Pick");
}
if (sizeof($others) == 0) {
    $others = "None";
} else {
    $others = implode(",", $others);
}

$imageFile = $book['imageFile'];
$imageDirectory = $book['imageDirectory'];

// retrieved comma-separated values from string
$genre_array = explode(", ", $book['genre']);

// check if form is submitted 
if (isset($_POST['update'])) {
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
        $new_title = mysqli_real_escape_string($handler, trim($_POST['title']));

        // create new image file only when $title is set 
        // because $new_title is used in image filename
        if (!empty($_FILES['upload-file']['name'])) { // new file uploaded
            // check file extension
            $allowed = array('jpg', 'jpeg', 'png', 'gif');
            $filename = $_FILES['upload-file']['name'];
            $extension = pathInfo($filename, PATHINFO_EXTENSION);

            if (!in_array($extension, $allowed)) {
                $imageError = "Only extensions .jpg, .jpeg, .png, .gif are allowed. Please upload another file";
                $errors += 1;
            } else {
                // delete old file directory
                unlink($book['imageDirectory']);

                // create directory path for new uploaded image file 
                $uploadsDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
                $targetFileName = $new_title . '.png';
                $targetFileDirectory = $uploadsDir . DIRECTORY_SEPARATOR . $new_title . '.png';

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
    if (!empty($_FILES['upload-file']['name'])) { // new file uploaded
        // check file extension
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
        $language = mysqli_real_escape_string($handler, trim($_POST['language']));

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

        // update book table 
        $query = "UPDATE books SET
                    title = '" . $new_title . "', 
                    description = '" . $description . "', 
                    author = '" . $author . "',
                    publisher = '" . $publisher . "', 
                    genre = '" . $genres . "', 
                    year = '" . $year . "', 
                    language = '" . $language . "', 
                    price = '" . $price . "', 
                    isbn = '" . $isbn . "',
                    inStock = '" . $stock . "',
                    newArrival = '" . $new . "',
                    clearanceBook = '" . $clearance . "',
                    premiumPick = '" . $premium . "'
                WHERE bookID =" . $book['bookID'];
        mysqli_query($handler, $query);

        if (!empty($_FILES['upload-file']['name'])) {
            $query = "UPDATE books SET
                        imageFile = '" . $targetFileName . "', 
                        imageDirectory = '" . $targetFileDirectory . "'
                    WHERE bookID =" . $book['bookID'];
            mysqli_query($handler, $query);
        }

        // redirect admin to book list page 
        header("Location: book-list.php?msg=editbook");
        exit();
    }
} else if (isset($_POST['delete'])) { // delete button clicked
    // remove image file from uploads folder 
    unlink($book['imageDirectory']);

    // delete book record from database
    $query = "DELETE FROM books WHERE bookID =" . $book['bookID'];
    mysqli_query($handler, $query);
    header("Location: book-list.php?msg=deletebook");
    exit();
}
?>

<html>

<head>
    <title>NewChapter | Book Record</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/form-control.css" type="text/css">
</head>

<body id="scroll-bar">
    <div class="left-panel" style="height: 1080px">
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
        <div class="title-container">B O O K&emsp;R E C O R D&emsp;#<?php echo $bookID ?></div>
        <div class="summary">
            <div id="edit-div">
                <!--Form for admin to update book details-->
                <form id="update-form" method="post" action="book-record.php?bookID=<?php echo $ID; ?>" enctype="multipart/form-data">
                    <div class="form-control">
                        <label for="title">Title:</label>
                        <input class="book-update-info" type="text" id="title" name="title" value="<?php if (isset($_POST['title'])) echo $_POST['title'];
                                                                                                    else echo $title; ?>" required>
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
                        <textarea class="book-update-info" id="description" style="height:120px" name="description" rows="4" cols="50" required><?php if (isset($_POST['description'])) echo $_POST['description'];
                                                                                                                            else echo $description; ?></textarea>
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
                        <input class="book-update-info" type="text" id="author" name="author" value="<?php if (isset($_POST['author'])) echo $_POST['author'];
                                                                                                        else echo $author; ?>" required>
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
                        <input class="book-update-info" type="text" id="publisher" name="publisher" value="<?php if (isset($_POST['publisher'])) echo $_POST['publisher'];
                                                                                                            else echo $publisher; ?>" required>
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
                        <input type="checkbox" id="action" name="action" value="Action" <?php
                                                                                        if (isset($_POST['update'])) {
                                                                                            if (isset($_POST['action'])) {
                                                                                                echo 'checked="checked"';
                                                                                            }
                                                                                        } else {
                                                                                            if (in_array("Action", $genre_array)) {
                                                                                                echo 'checked="checked"';
                                                                                            }
                                                                                        }
                                                                                        ?>>
                        <label for="action">Action</label>
                        <input type="checkbox" id="fantasy" name="fantasy" value="Fantasy" <?php
                                                                                            if (isset($_POST['update'])) {
                                                                                                if (isset($_POST['fantasy'])) {
                                                                                                    echo 'checked="checked"';
                                                                                                }
                                                                                            } else {
                                                                                                if (in_array("Fantasy", $genre_array)) {
                                                                                                    echo 'checked="checked"';
                                                                                                }
                                                                                            }
                                                                                            ?>>
                        <label for="fantasy">Fantasy</label><br>
                        <input type="checkbox" id="adventure" name="adventure" value="Adventure" <?php
                                                                                                    if (isset($_POST['update'])) {
                                                                                                        if (isset($_POST['adventure'])) {
                                                                                                            echo 'checked="checked"';
                                                                                                        }
                                                                                                    } else {
                                                                                                        if (in_array("Adventure", $genre_array)) {
                                                                                                            echo 'checked="checked"';
                                                                                                        }
                                                                                                    }
                                                                                                    ?>>
                        <label for="adventure">Adventure</label>
                        <input type="checkbox" id="history" name="history" value="History" <?php
                                                                                            if (isset($_POST['update'])) {
                                                                                                if (isset($_POST['history'])) {
                                                                                                    echo 'checked="checked"';
                                                                                                }
                                                                                            } else {
                                                                                                if (in_array("History", $genre_array)) {
                                                                                                    echo 'checked="checked"';
                                                                                                }
                                                                                            }
                                                                                            ?>>
                        <label for="history">History</label><br>
                        <input type="checkbox" id="animation" name="animation" value="Animation" <?php
                                                                                                    if (isset($_POST['update'])) {
                                                                                                        if (isset($_POST['animation'])) {
                                                                                                            echo 'checked="checked"';
                                                                                                        }
                                                                                                    } else {
                                                                                                        if (in_array("Animation", $genre_array)) {
                                                                                                            echo 'checked="checked"';
                                                                                                        }
                                                                                                    }
                                                                                                    ?>>
                        <label for="animation">Animation</label>
                        <input type="checkbox" id="horror" name="horror" value="Horror" <?php
                                                                                        if (isset($_POST['horror'])) {
                                                                                            if (isset($_POST['action'])) {
                                                                                                echo 'checked="checked"';
                                                                                            }
                                                                                        } else {
                                                                                            if (in_array("Horror", $genre_array)) {
                                                                                                echo 'checked="checked"';
                                                                                            }
                                                                                        }
                                                                                        ?>>
                        <label for="horror">Horror</label><br>
                        <input type="checkbox" id="biography" name="biography" value="Biography" <?php
                                                                                                    if (isset($_POST['update'])) {
                                                                                                        if (isset($_POST['biography'])) {
                                                                                                            echo 'checked="checked"';
                                                                                                        }
                                                                                                    } else {
                                                                                                        if (in_array("Biography", $genre_array)) {
                                                                                                            echo 'checked="checked"';
                                                                                                        }
                                                                                                    }
                                                                                                    ?>>
                        <label for="biography">Biography</label>
                        <input type="checkbox" id="mystery" name="mystery" value="Mystery" <?php
                                                                                            if (isset($_POST['update'])) {
                                                                                                if (isset($_POST['mystery'])) {
                                                                                                    echo 'checked="checked"';
                                                                                                }
                                                                                            } else {
                                                                                                if (in_array("Mystery", $genre_array)) {
                                                                                                    echo 'checked="checked"';
                                                                                                }
                                                                                            }
                                                                                            ?>>
                        <label for="mystery">Mystery</label><br>
                        <input type="checkbox" id="comedy" name="comedy" value="Comedy" <?php
                                                                                        if (isset($_POST['update'])) {
                                                                                            if (isset($_POST['comedy'])) {
                                                                                                echo 'checked="checked"';
                                                                                            }
                                                                                        } else {
                                                                                            if (in_array("Comedy", $genre_array)) {
                                                                                                echo 'checked="checked"';
                                                                                            }
                                                                                        }
                                                                                        ?>>
                        <label for="comedy">Comedy</label>
                        <input type="checkbox" id="romance" name="romance" value="Romance" <?php
                                                                                            if (isset($_POST['update'])) {
                                                                                                if (isset($_POST['romance'])) {
                                                                                                    echo 'checked="checked"';
                                                                                                }
                                                                                            } else {
                                                                                                if (in_array("Romance", $genre_array)) {
                                                                                                    echo 'checked="checked"';
                                                                                                }
                                                                                            }
                                                                                            ?>>
                        <label for="romance">Romance</label><br>
                        <input type="checkbox" id="crime" name="crime" value="Crime" <?php
                                                                                        if (isset($_POST['update'])) {
                                                                                            if (isset($_POST['crime'])) {
                                                                                                echo 'checked="checked"';
                                                                                            }
                                                                                        } else {
                                                                                            if (in_array("Crime", $genre_array)) {
                                                                                                echo 'checked="checked"';
                                                                                            }
                                                                                        }
                                                                                        ?>>
                        <label for="crime">Crime</label>
                        <input type="checkbox" id="sci-fi" name="sci-fi" value="Sci-fi" <?php
                                                                                        if (isset($_POST['update'])) {
                                                                                            if (isset($_POST['sci-fi'])) {
                                                                                                echo 'checked="checked"';
                                                                                            }
                                                                                        } else {
                                                                                            if (in_array("Sci-fi", $genre_array)) {
                                                                                                echo 'checked="checked"';
                                                                                            }
                                                                                        }
                                                                                        ?>>
                        <label for="sci-fi">Sci-fi</label><br>
                        <input type="checkbox" id="cooking" name="cooking" value="Cooking" <?php
                                                                                            if (isset($_POST['update'])) {
                                                                                                if (isset($_POST['cooking'])) {
                                                                                                    echo 'checked="checked"';
                                                                                                }
                                                                                            } else {
                                                                                                if (in_array("Cooking", $genre_array)) {
                                                                                                    echo 'checked="checked"';
                                                                                                }
                                                                                            }
                                                                                            ?>>
                        <label for="cooking">Cooking</label>
                        <input type="checkbox" id="sport" name="sport" value="Sport" <?php
                                                                                        if (isset($_POST['update'])) {
                                                                                            if (isset($_POST['sport'])) {
                                                                                                echo 'checked="checked"';
                                                                                            }
                                                                                        } else {
                                                                                            if (in_array("Sport", $genre_array)) {
                                                                                                echo 'checked="checked"';
                                                                                            }
                                                                                        }
                                                                                        ?>>
                        <label for="sport">Sport</label><br>
                        <input type="checkbox" id="documentary" name="documentary" value="Documentary" <?php
                                                                                                        if (isset($_POST['update'])) {
                                                                                                            if (isset($_POST['documentary'])) {
                                                                                                                echo 'checked="checked"';
                                                                                                            }
                                                                                                        } else {
                                                                                                            if (in_array("Documentary", $genre_array)) {
                                                                                                                echo 'checked="checked"';
                                                                                                            }
                                                                                                        }
                                                                                                        ?>>
                        <label for="documentary">Documentary</label>
                        <input type="checkbox" id="travel" name="travel" value="Travel" <?php
                                                                                        if (isset($_POST['update'])) {
                                                                                            if (isset($_POST['travel'])) {
                                                                                                echo 'checked="checked"';
                                                                                            }
                                                                                        } else {
                                                                                            if (in_array("Travel", $genre_array)) {
                                                                                                echo 'checked="checked"';
                                                                                            }
                                                                                        }
                                                                                        ?>>
                        <label for="travel">Travel</label>
                    </div>
                    <br>

                    <div class="form-control">
                        <label for="year">Publication Year:</label>
                        <input class="book-update-info" type="number" id="year" name="year" value="<?php if (isset($_POST['year'])) echo $_POST['year'];
                                                                                                    else echo $year; ?>" required>
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
                        <?php
                        // set default value in drop down list
                        if (isset($_POST['update'])) {
                            if (isset($_POST['language']) && $_POST['language'] == "English") {
                                echo "<option value='English' selected>English</option>\n
                                            <option value='Chinese'>Chinese</option>\n
                                            <option value='Bahasa Melayu'>Bahasa Melayu</option>\n";
                            } else if (isset($_POST['language']) && $_POST['language'] == "Chinese") {
                                echo "<option value='English'>English</option>\n
                                            <option value='Chinese' selected>Chinese</option>\n
                                            <option value='Bahasa Melayu'>Bahasa Melayu</option>\n";
                            } else {
                                echo "<option value='English'>English</option>\n
                                            <option value='Chinese'Chinese</option>\n
                                            <option value='Bahasa Melayu' selected>Bahasa Melayu</option>\n";
                            }
                        } else {
                            if ($language == "English") {
                                echo "<option value='English' selected>English</option>\n
                                            <option value='Chinese'>Chinese</option>\n
                                            <option value='Bahasa Melayu'>Bahasa Melayu</option>\n";
                            } else if ($language == "Chinese") {
                                echo "<option value='English'>English</option>\n
                                            <option value='Chinese' selected>Chinese</option>\n
                                            <option value='Bahasa Melayu'>Bahasa Melayu</option>\n";
                            } else {
                                echo "<option value='English'>English</option>\n
                                            <option value='Chinese'Chinese</option>\n
                                            <option value='Bahasa Melayu' selected>Bahasa Melayu</option>\n";
                            }
                        }
                        ?>
                    </select><br><br>

                    <div class="form-control">
                        <label for="price">Price:</label>
                        <input class="book-update-info" type="number" id="price" name="price" value="<?php if (isset($_POST['price'])) echo $_POST['price'];
                                                                                                        else echo $price; ?>" required>
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
                        <input class="book-update-info" type="text" id="isbn" name="isbn" value="<?php if (isset($_POST['isbn'])) echo $_POST['isbn'];
                                                                                                    else echo $isbn; ?>" required>
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
                        <input class="book-update-info" type="number" id="stock" name="stock" value="<?php if (isset($_POST['stock'])) echo $_POST['stock'];
                                                                                                        else echo $stock; ?>" required>
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
                    <input type="checkbox" id="new" name="new" value="New Arrival" <?php
                                                                                    if (isset($_POST['update'])) {
                                                                                        if (isset($_POST['new'])) {
                                                                                            echo 'checked="checked"';
                                                                                        }
                                                                                    } else {
                                                                                        if ($new == "1") {
                                                                                            echo 'checked="checked"';
                                                                                        }
                                                                                    }
                                                                                    ?>>
                    <label for="new">New Arrival</label><br>
                    <input type="checkbox" id="clearance" name="clearance" value="Clearance Book" <?php
                                                                                                    if (isset($_POST['update'])) {
                                                                                                        if (isset($_POST['clearance'])) {
                                                                                                            echo 'checked="checked"';
                                                                                                        }
                                                                                                    } else {
                                                                                                        if ($clearance == "1") {
                                                                                                            echo 'checked="checked"';
                                                                                                        }
                                                                                                    }
                                                                                                    ?>>
                    <label for="clearance">Clearance Book</label><br>
                    <input type="checkbox" id="premium" name="premium" value="Premium Pick" <?php
                                                                                            if (isset($_POST['update'])) {
                                                                                                if (isset($_POST['premium'])) {
                                                                                                    echo 'checked="checked"';
                                                                                                }
                                                                                            } else {
                                                                                                if ($premium == "1") {
                                                                                                    echo 'checked="checked"';
                                                                                                }
                                                                                            }
                                                                                            ?>>
                    <label for="premium">Premium Pick</label><br><br>

                    <!--Book Cover Image-->
                    <label for="upload-file">Cover Image: </label><?php echo $imageFile; ?>
                    <button id="upload-btn" type="button" onclick="showUploadDiv()">Upload New File</button><br>
                    <?php
                    if (isset($_POST['update'])) {
                        if (empty($_FILES['upload-file']['name'])) { // no new file uploaded
                            echo '<div class="form-control" id="upload-div" style="display: none">
                                            <input type="file" name="upload-file" id="upload-file">
                                        </div>';
                        } else {
                            echo '<div class="form-control" id="upload-div">
                                            <input type="file" name="upload-file" id="upload-file">';
                            if (isset($imageError)) {
                                echo '<br><div class="admin-form-error">';
                                echo $imageError;
                                echo "</div>";
                                unset($imageError);
                            }
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="form-control" id="upload-div" style="display: none">
                                        <input type="file" name="upload-file" id="upload-file">
                                    </div>';
                    }
                    ?>
                    <br><br>

                    <input class="book-update-btn" type="submit" name="update" value="Update Record">
                    <input class="delete-btn" type="submit" name="delete" value="Delete Record">
                </form>
            </div>
            <a href="book-list.php"><button type="button">Close</button></a>

            <script>
                // show or hide Choose File input when Upload New File button is clicked
                function showUploadDiv() {
                    var uploadDiv = document.getElementById("upload-div");
                    if (uploadDiv.style.display !== "none") {
                        uploadDiv.style.display = "none";
                    } else {
                        uploadDiv.style.display = "block";
                    }
                }
            </script>
        </div>
    </div>

</body>

</html>

<?
include "footer.php";
?>