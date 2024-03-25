<?php
    use PhpAmqpLib\Connection\AMQPStreamConnection;
    use PhpAmqpLib\Message\AMQPMessage;
    require 'vendor/autoload.php';
    require_once('path.inc');
    require_once('get_host_info.inc');
    require_once('rabbitMQLib.inc');
    
if (!isset($_COOKIE['username'])) {
    echo "Please log in.";
    exit();
}

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$exchangeName = 'user_auth';
$routingKey = 'user_management';

$request = [
    'type' => 'get_user_car_regs',
    'username' => $_COOKIE['username'],
];

$response = $client->send_request($request, $exchangeName, $routingKey);

if ($response && $response['message'] === "Car registrations fetched successfully") {
    $carRegs = $response['data']; 
    foreach ($carRegs as $index => $carReg) {
        echo '<div class="vehicle-info">';
        echo "<div class='container mx-auto px-4 py-8'>";
        echo "<h2 class='text-3xl font-bold mb-6'>Vehicle Information:</h2>";
        echo "<div class='bg-white shadow-md rounded-md p-6 mb-8'>";
        echo "<p class='mb-2'><strong>Make:</strong> {$carReg['make']}</p>";
        echo "<p class='mb-2'><strong>Model:</strong> {$carReg['model']}</p>";
        echo "<p class='mb-2'><strong>Year:</strong> {$carReg['year']}</p>";
        echo "</div>";
        echo "</div>";
        echo '<div id="recall-info-' . $index . '">'; 
        echo '</div>';
        echo '</div>';
        echo '<script>';
        echo 'setTimeout(function() { getRecalls("' . $carReg['make'] . '", "' . $carReg['model'] . '", "' . $carReg['year'] . '", "' . $index . '"); }, 0);'; // 2000 milliseconds delay
        echo '</script>';
    }
} else {
    echo "Failed to fetch car registrations.";
}
?>
