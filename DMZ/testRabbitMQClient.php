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
$request = [
  'type' => "getRecall", 
  'make' => "Honda",     
  'model' => "Civic",    
  'year' => "2012",      
];

$response = $client->send_request($request, $exchangeName, $routingKey);

echo "Client received response:" . PHP_EOL;
if (isset($response['response']) && count($response['response']) > 0) {
  echo "Total Recalls Found: " . count($response['response']) . PHP_EOL;
  foreach ($response['response']['results'] as $index => $recall) {
      echo "Recall #" . ($index + 1) . ":" . PHP_EOL;
      echo "  Manufacturer: " . $recall['Manufacturer'] . PHP_EOL;
      echo "  NHTSA Campaign Number: " . $recall['NHTSACampaignNumber'] . PHP_EOL;
      echo "  Report Received Date: " . $recall['ReportReceivedDate'] . PHP_EOL;
      echo "  Component: " . $recall['Component'] . PHP_EOL;
      echo "  Summary: " . $recall['Summary'] . PHP_EOL;
      echo "  Consequence: " . $recall['Consequence'] . PHP_EOL;
      echo "  Remedy: " . $recall['Remedy'] . PHP_EOL;
      echo "  Notes: " . $recall['Notes'] . PHP_EOL;
      echo "  Model Year: " . $recall['ModelYear'] . ", Make: " . $recall['Make'] . ", Model: " . $recall['Model'] . PHP_EOL;
      echo "----------------------------------------------" . PHP_EOL;
  }
} else {
  echo "No recall information is available at the moment." . PHP_EOL;
}


echo "\n\n";


