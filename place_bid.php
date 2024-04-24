<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$user = $_COOKIE['username'];

$carId = $_POST['carId'] ?? '';
$bidAmount = filter_input(INPUT_POST, 'bidAmount', FILTER_VALIDATE_FLOAT);

if (!$carId || !$bidAmount) {
    header('Location: car_detail.php?carId=' . urlencode($carId) . '&error=Invalid input');
    exit;
}

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$request = [
    'type' => 'place_bid',
    'carId' => $carId,
    'username' => $user,
    'bidAmount' => $bidAmount,
];

$response = $client->send_request($request, 'user_auth', 'user_management');

if ($response && $response['message'] == "Bid placed successfully.") {
    // Redirect back to car detail with success message
    header('Location: car_detail.php?carId=' . urlencode($carId) . '&success=Bid placed successfully');
} else {
    // Redirect back with error message
    header('Location: car_detail.php?carId=' . urlencode($carId) . '&error=Failed to place bid');
}

require_once 'logError.php';
require_once 'logError.php';
