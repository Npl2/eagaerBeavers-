<?php
$carId = $_GET['carId'] ?? '';
$carDetails = []; 
$bids = [];

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$exchangeName = 'user_auth';
$routingKey = 'user_management';

$request = [
    'type' => 'get_car_details_and_bids',
    'carId' => $carId,
];

$response = $client->send_request($request, $exchangeName, $routingKey);
if ($response && isset($response['data'])) {
    $carDetails = $response['data']['carDetails'];
    $bids = $response['data']['bids'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Detail</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 w-screen">
    <?php include 'header.php'; ?>
    <div class="container w-full lg:w-1/3 md:w-96 mx-auto px-4 py-8">
        <div class="bg-white shadow-md rounded-lg p-8">
            <h2 class="text-2xl font-bold mb-4">Car Details</h2>
            <p><strong>Make:</strong> <?= htmlspecialchars($carDetails['make']) ?></p>
            <p><strong>Model:</strong> <?= htmlspecialchars($carDetails['model']) ?></p>
            <p><strong>Year:</strong> <?= htmlspecialchars($carDetails['year']) ?></p>
            <p><strong>Price:</strong> $<?= htmlspecialchars(number_format($carDetails['sale_price'], 2)) ?></p>
            <h3 class="mt-8 text-xl font-bold mb-1">Bids</h3>
            <?php if (!empty($bids)) : ?>
                <ul>
                    <?php foreach ($bids as $bid) : ?>
                        <li><?= htmlspecialchars($bid['username']) ?>: $<?= htmlspecialchars(number_format($bid['bidAmount'], 2)) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No bids yet.</p>
            <?php endif; ?>
            <h3 class="text-xl font-bold mt-4">Place Your Bid</h3>
            <form action="place_bid.php" method="post">
                <input type="hidden" name="carId" value="<?= htmlspecialchars($carId) ?>">
                <input type="number" name="bidAmount" placeholder="Your bid amount" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                <div class="text-center">
                    <button type="submit" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Place Bid
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'responsiveNavScript.php'; ?>
</body>
</html>