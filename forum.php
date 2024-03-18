<?php
if (!isset($_COOKIE['username'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Discussion Forum</title>
        <link href="css/forum.css" rel="stylesheet"> 
        <link href="css/header.css" rel="stylesheet">
    </head>
    <?php
            include 'header.php';
    ?>
    <body>
        <div class="discussionBox">
            <div class="sideBar-topics">
                <h2>Categories</h2>
                <ul>
                    <li><a href="#">Cars</a></li>
                    <li><a href="#">Makes</a></li>
                    <li><a href="#">Models</a></li>
                </ul>
            </div>
            <div class="user-discussion">
                <h1>Welcome to the Discussion Forum</h1>
                <p>This is a place for open discussion on various topics.</p>
            </div>
        </div>
    </body>
    </html>
        <?php
            include 'footer.php';
        ?>
    </body>
</html>