#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$exchangeName = 'user_auth';
$routingKey = 'user_management';


// recall todos by username
$request = [
    'type' => 'get_recall_todos_by_username',
    'username' => 'user',
];

$response = $client->send_request($request, $exchangeName, $routingKey);

if ($response && $response['message'] == "Recall todos fetched successfully") {
    echo "Recall todos for user " . $request['username'] . ":\n";
    foreach ($response['data'] as $todo) {
        $id = (string) $todo['_id']['$oid'];
        $date = new DateTime();
        $timestamp = $todo['created_at']['$date']['$numberLong'] / 1000;
        $date->setTimestamp($timestamp);
        $formattedDate = $date->format('Y-m-d H:i:s');

        echo "ID: " . $id . "\n";
        echo "Task: " . $todo['task'] . "\n";
        echo "Status: " . $todo['status'] . "\n";
        echo "Created At: " . $formattedDate . "\n";
        echo "---------------------------------\n";
    }
} else {
    echo "Failed to fetch recall todos for username: " . $request['username'] . "\n";
}


echo "\n\n";

echo $argv[0] . " END" . PHP_EOL;
