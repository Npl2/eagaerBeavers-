#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$exchangeName = 'user_auth';
$routingKey = 'user_management';

$loginRequest = [
    'type' => "login",
    'username' => "NEWHARSH_123",
    'password' => "1212121",
];

$response = $client->send_request($loginRequest, $exchangeName, $routingKey);

echo "Client received response: " . PHP_EOL;
if ($response && $response['message'] === true) {
    echo "Successful";
} else {
    echo "Request Failed";
}
echo "\n\n";

echo $argv[0] . " END" . PHP_EOL;
