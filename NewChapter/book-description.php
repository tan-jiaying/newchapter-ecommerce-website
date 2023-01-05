<html>

<head>
    <title>NewChapter | Book Description</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css">
</head>

<body id="scroll-bar">
    <?php
    include "header.php";
    $book_title = $_GET["book"];
    $book_detail = $db_handle->runQuery("SELECT * FROM books WHERE title='" . $book_title . "'");
    // generating image
    $img = "C:xampphtdocsNewChapteruploads";
    $img_str = str_replace($img, "", stripslashes($book_detail[0]["imageDirectory"]));
    ?>
    <div class="wrapper">
        <div class="title-container">B O O K&emsp;D E S C R I P T I O N</div>
        <?php echo "<img style='height: 300px; display: inline-block' src='uploads/" . $img_str . "'>"; ?>
        <!--Main Details-->
        <div class="book-details">
            <form method="post" action="book-description.php?action=add&title=<?php echo $book_detail[0]["title"]; ?>&book=<?php echo $book_detail[0]["title"]; ?>">
                <h2><?php echo $book_detail[0]["title"]; ?></h2>
                <p><?php echo $book_detail[0]["description"]; ?></p>
                <div>
                    <div style="display: inline-block; padding: 15px 30px 15px 0">
                        <span style="color: #777">Written by: </span><br>
                        <?php echo $book_detail[0]["author"]; ?>
                    </div>
                    <div style="display: inline-block; padding: 15px 30px">
                        <span style="color: #777">Publisher: </span><br>
                        <?php echo $book_detail[0]["publisher"]; ?>
                    </div>
                    <div style="display: inline-block; padding: 15px 30px">
                        <span style="color: #777">Year: </span><br>
                        <?php echo $book_detail[0]["year"]; ?>
                    </div>
                </div>
                <div class="details-bottom">
                    <p style="display: inline-block; color: #2FAC24;"><b>RM <?php echo $book_detail[0]["price"]; ?></b></p>
                    <div class="cart-action">
                        <input class="bd-quantity" type="number" min="0" name="quantity" value="1" />
                        <!-- Post method for adding to Cart  -->
                        <input type="submit" value="Add to Cart" class="btn-add-action" />
                    </div>
                </div>
            </form>
        </div>

        <!--Other Details-->
        <div class="other-details">
            <h3>Other Details</h3>
            <table class="other-details-table">
                <tr class="other-details-row">
                    <td><b>Language</b></td>
                    <td id="language"><?php echo $book_detail[0]["language"]; ?></td>
                </tr>
                <tr class="other-details-row">
                    <td><b>Genre</b></td>
                    <td id="genre"><?php echo $book_detail[0]["genre"]; ?></td>
                </tr>
                <tr class="other-details-row">
                    <td><b>ISBN</b></td>
                    <td id="isbn"><?php echo $book_detail[0]["isbn"]; ?></td>
                </tr>
            </table>
        </div>
    </div>
    <div style="height:100px;width:100%"></div>
    <?php
    include "footer.php";
    ?>
</body>

</html>