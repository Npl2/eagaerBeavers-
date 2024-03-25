<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$exchangeName = 'user_auth';
$routingKey = 'user_management';

$requestType = 'list_cars_on_sale'; 
$searchCriteria = []; 

if (!empty($_GET['make']) || !empty($_GET['model']) || !empty($_GET['year'])) {
    $requestType = 'search_cars_on_sale'; 
    if (!empty($_GET['make'])) {
        $searchCriteria['make'] = $_GET['make'];
    }
    if (!empty($_GET['model'])) {
        $searchCriteria['model'] = $_GET['model'];
    }
    if (!empty($_GET['year'])) {
        $searchCriteria['year'] = (int)$_GET['year'];
    }
}

$request = [
    'type' => $requestType,
] + $searchCriteria;

$response = $client->send_request($request, $exchangeName, $routingKey);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Listings</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.4/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<?php include 'header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-md rounded-lg p-8">
        <h2 class="text-2xl font-bold mb-4">Search Cars on Sale</h2>
        <form method="GET" action="search_cars.php" class="space-y-4">
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label for="make" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Make</label>
                    <input type="text" name="make" id="make" class="appearance-none block w-full bg-gray-200 text-gray-700 border rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white" placeholder="Toyota">
                </div>
                <div class="w-full md:w-1/3 px-3">
                    <label for="model" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Model</label>
                    <input type="text" name="model" id="model" class="appearance-none block w-full bg-gray-200 text-gray-700 border rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white" placeholder="Corolla">
                </div>
                <div class="w-full md:w-1/3 px-3">
                    <label for="year" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Year</label>
                    <input type="number" name="year" id="year" class="appearance-none block w-full bg-gray-200 text-gray-700 border rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white" placeholder="2020">
                </div>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Search
            </button>
        </form>
    </div>

    <!-- Results Section -->
<?php if (isset($response) && isset($response['data']) && !empty($response['data'])): ?>
    <div class="mt-8">
        <h3 class="text-2xl font-bold mb-4">Search Results</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($response['data'] as $car): ?>
                <a href="car_detail.php?carId=<?= urlencode($car['_id']['$oid']) ?>" class="block bg-white shadow-md rounded-lg overflow-hidden hover:shadow-xl transition duration-300">
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-2"><?= htmlspecialchars($car['make']) ?> <?= htmlspecialchars($car['model']) ?></h4>
                        <p class="text-gray-700 mb-2"><?= htmlspecialchars($car['year']) ?></p>
                        <p class="text-gray-700 mb-2">Price: <?= htmlspecialchars(number_format($car['sale_price'], 2)) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <p class="mt-8 text-center text-lg text-gray-600">No results found or failed to fetch cars on sale.</p>
<?php endif; ?>

</div>

</body>
</html>

