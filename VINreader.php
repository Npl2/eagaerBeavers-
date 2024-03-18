<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function handleAjaxRequest($make, $year) {
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $exchangeName = 'car_api_exchange';
    $routingKey = 'car_request';

    $request = array(
        'type' => 'getMakes', 
        'make' => $make,
        'year' => $year,
    );

    $response = $client->send_request($request,$exchangeName, $routingKey);

    if ($response && isset($response['returnCode']) && $response['returnCode'] == '200') {
        echo json_encode($response['data']);
    } else {
        echo json_encode(array('error' => 'No data found or error occurred.'));
    }

    exit();
}

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $make = isset($_POST['make']) ? $_POST['make'] : null;
    $year = isset($_POST['year']) ? $_POST['year'] : null;
    handleAjaxRequest($make, $year);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Get Years by Make</title>
    <!-- Your CSS links here -->
</head>
<body>
    <h1>Get Years by Make</h1>
    <form id="typeForm">
        <label for="make">Make:</label>
        <input type="text" id="make" name="make">
        <label for="year">Year:</label>
        <input type="text" id="year" name="year">
        <button type="submit">Submit</button>
    </form>

    <div id="responseTable"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#typeForm').submit(function(event) {
                event.preventDefault();

                var make = $('#make').val();
                var year = $('#year').val();

                $.ajax({
                    url: '',
                    method: 'POST',
                    data: { make: make, year: year },
                    dataType: 'json',
                    success: function(response) {
                        if(response.error) {
                            $('#responseTable').html('<p>' + response.error + '</p>');
                        } else {
                            var table = '<table><tr><th>ID</th><th>Name</th></tr>';
                            $.each(response, function(index, item) {
                                table += '<tr><td>' + item.id + '</td><td>' + item.name + '</td></tr>';
                            });
                            table += '</table>';

                            $('#responseTable').html(table);
                        }
                    },
                    error: function() {
                        $('#responseTable').html('<p>Error: Unable to retrieve data.</p>');
                    }
                });
            });
        });
    </script>
</body>
</html>
