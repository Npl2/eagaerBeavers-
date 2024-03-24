<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$exchangeName = 'user_auth';
$routingKey = 'user_management';

// Determine the request type based on the presence of search criteria
$requestType = 'list_car_reviews'; // Default action
$searchCriteria = []; // Initialize search criteria

// Check if there are any search criteria provided
if (!empty($_GET['make']) || !empty($_GET['model']) || !empty($_GET['year'])) {
    $requestType = 'search_car_reviews'; // Change request type for searching
    if (!empty($_GET['make'])) {
        $searchCriteria['make'] = $_GET['make'];
    }
    if (!empty($_GET['model'])) {
        $searchCriteria['model'] = $_GET['model'];
    }
    if (!empty($_GET['year'])) {
        $searchCriteria['year'] = (int)$_GET['year']; // Cast to int for safety
    }
}

// Prepare the request
$request = [
    'type' => $requestType,
] + $searchCriteria;

// Send the request to RabbitMQ
$response = $client->send_request($request, $exchangeName, $routingKey);

?>