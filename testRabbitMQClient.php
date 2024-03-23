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
    'type' => 'add_car_review',
    'make'=>'Honda',
    'model'=>'Civic',
    'year'=>'2016',
    'username'=>'nath',
    'review_text'=> 'It is good but not that sleek, have a tone of issue the exhaust is just mehhh!!'
];

$response = $client->send_request($request, $exchangeName, $routingKey);

if ($response && $response['message'] == "Car review added successfully") {
  echo "Car review added Sucessfully";
} else {
    echo $response['message'];
}


echo "\n\n";

echo $argv[0] . " END" . PHP_EOL;
