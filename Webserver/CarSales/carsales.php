<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are filled
    if (isset($_POST["year"]) && isset($_POST["make"]) && isset($_POST["model"])) {
        $year = $_POST["year"];
        $make = $_POST["make"];
        $model = $_POST["model"];

        // Store the posted car details in a database or file
        // For demonstration purposes, let's just echo the details
        echo "Car posted for sale:<br>";
        echo "Year: $year<br>";
        echo "Make: $make<br>";
        echo "Model: $model<br>";
    } else {
        echo "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Car for Sale</title>
</head>
<body>
    <h2>Post Your Car for Sale</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="year">Year:</label><br>
        <input type="text" id="year" name="year"><br><br>

        <label for="make">Make:</label><br>
        <input type="text" id="make" name="make"><br><br>

        <label for="model">Model:</label><br>
        <input type="text" id="model" name="model"><br><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
