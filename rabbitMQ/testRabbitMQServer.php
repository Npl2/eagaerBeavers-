#!/usr/bin/php
<?php
require_once ('./vendor/autoload.php');
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$callback = function ($message) {
    echo "Received message: ", $message->body, "\n";
    // Further processing of the message (e.g., interacting with the database)
};

// Connection parameters
$connection = new AMQPStreamConnection('172.28.222.209', 5672, 'test', 'test', 'testHost');

// Create a channel
$channel = $connection->channel();

// Declare a queue
$channel->queue_declare('testQueue', false, true, false, false);

// Consume messages from the queue
$channel->basic_consume('testQueue', '', false, true, false, false, $callback);

// Keep consuming messages until the channel is closed
while ($channel->is_consuming()) {
    $channel->wait();
}

// Close the channel and the connection
$channel->close();
$connection->close();

// Include the requestProcessor function and RabbitMQ server instantiation here
function requestProcessor($request)
{
    echo "received request \n";
    //var_dump($request);
    /*
    if(!isset($request['type']))
    {
        return "ERROR: unsupported message type";
    }
    switch ($request['type'])
    {
        case "login":
            return doLogin($request['username'],$request['password']);
        case "validate_session":
            return doValidate($request['sessionId']);
    }
    return array("returnCode" => '0', 'message'=>"Server received request and processed");
    */

    if (!isset($request['type'])) {
        return ['success' => false, 'message' => "Error: unsupported message type"];
    }

    // Assuming DBrequest function is defined elsewhere
    $response = DBrequest($request);

    return $response;
}

// Instantiate RabbitMQ server
$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");
$server->process_requests('requestProcessor');