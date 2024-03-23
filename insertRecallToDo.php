<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


if (!isset($_COOKIE['username'])) {
    echo "Please log in.";
    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_COOKIE['username']; 
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $component = $_POST['component'];
    $summary = $_POST['summary'];
    $consequence = $_POST['consequence'];
    $remedy = $_POST['remedy'];
    $notes = $_POST['notes'];

 
    $recallsTodo = [
        [
            'Component' => $component,
            'Summary' => $summary,
            'Consequence' => $consequence,
            'Remedy' => $remedy,
            'Notes' => $notes
        ]
    ];


    $request = [
        'type' => 'insert_recall_todos',
        'username' => $username,
        'make' => $make,
        'model' => $model,
        'year' => $year,
        'recalls' => $recallsTodo,
    ];


    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $exchangeName = 'user_auth';
    $routingKey = 'user_management';
    
 
    $response = $client->send_request($request, $exchangeName, $routingKey);

    if ($response && $response['message'] == "Recall todos inserted successfully.") {
        echo "Recall todo successfully inserted for $make $model ($year).";
    } else {
        echo "Failed to insert recall todo for $make $model ($year).";
    }
} else {
    echo "Invalid request method.";
}
?>