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



if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $username = $_COOKIE['username']; 
    $make = $_GET['make'];
    $model = $_GET['model'];
    

    $year = $_GET['year'];
    $component = $_GET['component'];
    $summary = $_GET['summary'];
    $consequence = $_GET['consequence'];
    $remedy = $_GET['remedy'];
    $notes = $_GET['notes'];

 
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