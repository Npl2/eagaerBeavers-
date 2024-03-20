<?php
    use PhpAmqpLib\Connection\AMQPStreamConnection;
    use PhpAmqpLib\Message\AMQPMessage;
    require 'vendor/autoload.php';
    require_once('path.inc');
    require_once('get_host_info.inc');
    require_once('rabbitMQLib.inc');

    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $exchangeName = 'user_auth';
    $routingKey = 'user_management';

    $request = [
        'type' => 'get_user_car_regs',
        'username' => $_COOKIE['username'] ?? null,
    ];

    $response = $client->send_request($request, $exchangeName, $routingKey);

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Car Registrations</title>
        <link href="css/header.css" rel="stylesheet">
        <link href="css/displayRegVehicle.css" rel="stylesheet"> 
    </head>
    <body>

    <?php include 'header.php'; ?>

    <div class="registration-container">
        <h1>Registered Vehicles</h1>
        <div class="vehicle-list">
            <?php
            if ($response && $response['message'] == "Car registrations fetched successfully") {
                foreach ($response['data'] as $carReg) {
                    echo "<p>Car Registration: " . $carReg['car_reg'] . " - Make: " . $carReg['make'] . ", Model: " . $carReg['model'] . ", Year: " . $carReg['year'] . ($carReg['on_sale'] ? " - On Sale" : "") . "</p>";
                }
            } else {
                echo "<p>Failed to fetch car registrations.</p>";
            }
            ?>
        </div>
    </div>

</body>
</html>
