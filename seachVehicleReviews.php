<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$exchangeName = 'user_auth';
$routingKey = 'user_management';

$requestType = 'list_all_reviews'; 
$searchCriteria = [];

if (!empty($_GET['make']) || !empty($_GET['model']) || !empty($_GET['year'])) {
    $requestType = 'search_car_reviews';
    if (!empty($_GET['make'])) {
        $searchCriteria['make'] = $_GET['make'];
    }
    if (!empty($_GET['model'])) {
        $searchCriteria['model'] = $_GET['model'];
    }
    if (!empty($_GET['year'])) {
        $searchCriteria['year'] = $_GET['year'];
    }
}

$request = [
    'type' => $requestType,
] + $searchCriteria;

$response = $client->send_request($request, $exchangeName, $routingKey);

require_once 'logError.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Reviews</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<?php include 'header.php'; ?>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-md rounded-lg p-8">
            <h2 class="text-2xl font-bold mb-4">Search Car Reviews</h2>
            <form method="GET" action="seachVehicleReviews.php" class="space-y-4">
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
            <a href="vehiclereviews.php" class="w-32 block mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Add Review
            </a>
        </div>
        <h3 class="mt-8 text-2xl font-bold mb-4">Search Results:</h3>
        <!-- Results Section -->
        <?php if (isset($response) && isset($response['data']) && !empty($response['data'])) : ?>
            <div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
                <?php foreach ($response['data'] as $review) : ?>
                    <div class="bg-white shadow-md rounded-lg mb-4 p-6">
                        <h4 class="text-xl font-bold mb-2"><?= htmlspecialchars($review['car_make']) ?> <?= htmlspecialchars($review['car_model']) ?>, <?= htmlspecialchars($review['car_year']) ?></h4>
                        <p class="text-gray-700 mb-2"><?= htmlspecialchars($review['review_text']) ?></p>
                        <!-- Add additional review details here if available -->
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="mt-8 text-center text-lg text-gray-600">No results found or failed to fetch car reviews.</p>
        <?php endif; ?>
    </div>

</body>
</html>
