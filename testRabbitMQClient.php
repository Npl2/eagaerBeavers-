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

$request = array();
$request['type'] = "getYearsByMake";
$request['make'] = "Ford";

$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
if ($response['returnCode'] == '200') {
    print_r($response['response']); // Handle successful response
} else {
    // Handle error response
    echo "Error ({$response['returnCode']}): {$response['message']}\n";
}
echo "\n\n";


