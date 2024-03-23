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
        <!-- <link href="css/header.css" rel="stylesheet"> -->
        <!-- <link href="css/displayRegVehicle.css" rel="stylesheet">  -->
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body>

    <?php include 'header.php'; ?>

    <div class="h-screen flex items-center justify-center">
        <div class="p-5 w-4/5 shadow-md rounded">
            <h4 class="font-bold capitalize text-center text-lg">
                Registered Vehicles
            </h4>

            <?php
            if ($response && $response['message'] == "Car registrations fetched successfully") {
                echo '<div class="grid grid-cols-4 gap-4 text-sm">';

                foreach ($response['data'] as $carReg) {
                    echo '
                    <div class="p-2 h-44 flex items-center justify-center shadow">
                      <div>
                        <p class="mb-2 font-medium capitalize">
                          Car Registration: <span class="font-normal">' . $carReg['car_reg'] . '</span>
                        </p>
                        <p class="mb-2 font-medium capitalize">
                          Make: <span class="font-normal">' . $carReg['make'] . '</span>
                        </p>
                        <p class="mb-2 font-medium capitalize">
                          Model: <span class="font-normal">' . $carReg['model'] . '</span>
                        </p>
                        <p class="mb-2 capitalize">
                          Year: <span class="font-normal">' . $carReg['year'] . '</span>
                        </p>
                        <p class="font-medium capitalize">
                          On Sale: ' . ($carReg['on_sale'] ? '<span
                          class="p-1 font-normal uppercase text-white bg-green-500 rounded-lg"
                          >Yes</span
                        >' : '<span
                        class="p-1 font-normal uppercase text-white bg-red-500 rounded-lg"
                        >No</span
                      >') . '  
                        </p>
                      </div>
                    </div>
                    ';
                }
                echo '</div>';
            } else {
                echo '<p class="bg-red-500 text-white">Failed to fetch car registrations.</p>';
            }
            ?>
        </div>
    </div>

</body>
</html>
