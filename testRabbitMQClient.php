#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}

$exchangeName = 'car_api_exchange';
$routingKey = 'car_request';
$request = array();
$request['type'] = "getMakes";
// Any of the make or year must not be empty

$request['year'] = "2018";

$response = $client->send_request($request,$exchangeName, $routingKey);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
if ($response['returnCode'] == '200') {
    print_r($response['response']); // Handle successful response
} else {
    // Handle error response
    echo "Error ({$response['returnCode']}): {$response['message']}\n";
}
echo "\n\n";


