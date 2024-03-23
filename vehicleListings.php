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
        <title>Vehicle Listings</title>
        <link href="css/vehicleListings.css" rel="stylesheet"> 
        <!-- <link href="css/header.css" rel="stylesheet"> -->
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <?php
        include 'header.php';
    ?>
    <body>

    </body>
</html>