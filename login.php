<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require 'vendor/autoload.php';

// Create a connection to RabbitMQ


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connection = new AMQPStreamConnection('172.28.222.209', 5672, 'kerlos77', 'kerlos77','testHost');
    $channel = $connection->channel();

    // Declare a queue for receiving messages
    $channel->queue_declare('FE2BE', true, false, false, false);
    $data = json_decode(file_get_contents('php://input'), true);

    // Prepare the data to be sent to RabbitMQ
    $loginData = [
        'username' => $data['username'] ?? null,
        'password' => $data['password'] ?? null,
        // Include additional info as needed
    ];
    $msgBody = json_encode($loginData);
    $msg = new AMQPMessage($msgBody, ['content_type' => 'application/json', 'delivery_mode' => 2]);

    // Publish the message to the queue
    $channel->basic_publish($msg, '', $requestQueue);

  
    $response = ['success' => true, 'message' => 'Login request sent.'];
    echo json_encode($response);

    // Clean up
    $channel->close();
    $connection->close();
    exit;
}

echo "-={[Back-end] Waiting for Front-end messages. To exit press CTRL+C}=-\n";

?>