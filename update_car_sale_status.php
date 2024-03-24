<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


// Initialize RabbitMQ client
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$exchangeName = 'user_auth';
$routingKey = 'user_management';

// Check if carId and onSale values are set
if (isset($_POST['carId'], $_POST['onSale'])) {
    $carId = $_POST['carId'];
    $onSale = filter_var($_POST['onSale'], FILTER_VALIDATE_BOOLEAN);
    $salePrice = filter_input(INPUT_POST, 'salePrice', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);


    // Prepare the request array
    $request = [
        'type' => 'update_car_sale_status',
        'carId' => $carId,
        'onSale' => $onSale,
        'salePrice'=>$salePrice
    ];
    echo "<h2>I'm here </h2>";
    // Send the request to the RabbitMQ server
    $response = $client->send_request($request, $exchangeName, $routingKey);

    if ($response && $response['message'] === "Car sale status updated successfully.") {
        echo "<script>alert('Car Status updated')</script>";
         header('Location: displayRegVehicle.php');
     } else {
        echo "<script>alert('not updated')</script>";
        header('Location: displayRegVehicle.php');
     }
}
else{
    header('Location: displayRegVehicle.php');
}
exit;
?>
