#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$exchangeName = 'user_auth';
$routingKey = 'user_management';


$request = [
    'type' => 'list_all_car_regs'
];


$response = $client->send_request($request, $exchangeName, $routingKey);
if ($response && $response['message'] == "All car registrations fetched successfully") {
    echo "Cars On Sale:" . PHP_EOL;
    foreach ($response['data'] as $carReg) {
        if ($carReg['on_sale']) {
            echo "User: " . $carReg['username'] . " - Make: " . $carReg['make'] . ", Model: " . $carReg['model'] . ", Year: " . $carReg['year'] . " - On Sale" . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "Cars Not On Sale:" . PHP_EOL;
    foreach ($response['data'] as $carReg) {
        if (!$carReg['on_sale']) {
            echo "User: " . $carReg['username'] . " - Make: " . $carReg['make'] . ", Model: " . $carReg['model'] . ", Year: " . $carReg['year'] . PHP_EOL;
        }
    }
} else {
    echo "Failed to list all car registrations" . PHP_EOL;
}
echo "\n\n";

echo $argv[0] . " END" . PHP_EOL;
