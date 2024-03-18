
<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
/*
if (!isset($_COOKIE['username'])) {
    header('Location: index.php');
    exit();
}
*/
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/header.css" rel="stylesheet">
    <title>Get Years by Make</title>
</head>
<?php include 'header.php'; ?>
<body>
    <h1>Get Years by Make</h1>
    <form id="typeForm" method="POST">
        <label for="type">Enter Type:</label>
        <input type="text" id="type" name="type" required>
        <button type="submit">Submit</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#typeForm').submit(function(event) {
                event.preventDefault(); // Prevent the form from submitting normally

                var type = $('#type').val(); // Get the type entered by the user

                // Make AJAX request to the PHP script
                $.ajax({
                    url: '',
                    method: 'POST',
                    data: {
                        type: type
                    },
                    success: function(response) {
                        $('#response').html(response); // Display response from PHP script
                    },
                    error: function() {
                        $('#response').html('<p>Error: Unable to retrieve data.</p>'); // Display error message
                    }
                });
            });
        });
    </script>
    <?php

        // Check if type is set in POST request
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['type'])) {
            $type = $_POST['type'];

            // Create RabbitMQ client
            $client = new rabbitMQClient("testRabbitMQ.ini","testServer");

            // Prepare request data
            $request = array();
            $request['type'] = "getYearsByMake";
            $request['make'] = $type;

            // Send request to RabbitMQ server
            $response = $client->send_request($request);


            // Handle response
            if ($response['returnCode'] == '200') {
                // Parse the response and display information
                $data = json_decode($response['response'], true);
                /*
                echo "<h2>Vehicle Information</h2>";
                echo "<p>Make: " . $data['make'] . "</p>";
                echo "<p>Model: " . $data['model'] . "</p>";
                */
                // Add more fields as needed
            } else {
                echo "Error ({$response['returnCode']}): {$response['message']}"; // Output error message
            }
        } else {
            echo "Error: Type not provided."; // Output error message if type is not provided
        }

        
    ?>

</body>
</html>
