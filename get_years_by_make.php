<?php

require_once('API_test.php');

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
        echo $response['response']; // Output response
    } else {
        echo "Error ({$response['returnCode']}): {$response['message']}"; // Output error message
    }
} else {
    echo "Error: Type not provided."; // Output error message if type is not provided
}
?>
