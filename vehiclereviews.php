<?php
  use PhpAmqpLib\Connection\AMQPStreamConnection;
  use PhpAmqpLib\Message\AMQPMessage;
  require 'vendor/autoload.php';
  require_once('path.inc');
  require_once('get_host_info.inc');
  require_once('rabbitMQLib.inc');

  if (!isset($_COOKIE['username'])) {
      header('Location: index.php');
      exit();
  }


  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $exchangeName = 'user_auth';
    $routingKey = 'user_management';

    $make = $_POST['vehicle_make'] ?? null;
    $model = $_POST['vehicle_model'] ?? null;
    $year = $_POST['vehicle_year'] ?? null;
    $reviewText = $_POST['review_text'] ?? NULL;

    $registrationData = [
        'type' => 'add_car_review',
        'make' => $make,
        'model' => $model,
        'year' => $year,
        'username' => $_COOKIE['username'] ?? null,
        'review_text'=> $reviewText
    ];

  $response = $client->send_request($registrationData, $exchangeName, $routingKey);

  if ($response && $response['message'] == "Car review added successfully") {
  
    echo "<script>alert('Reviews has been added');</script>";

  } else {
    echo "<script>alert('Reviews has not been added');</script>";
  }

  exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Reviews</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="max-w-md mx-auto my-10">
        <form action="vehiclereviews.php" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="vehicle_make">
                    Vehicle Make
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="vehicle_make" name="vehicle_make" type="text" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="vehicle_model">
                    Vehicle Model
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="vehicle_model" name="vehicle_model" type="text" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="vehicle_year">
                    Vehicle Year
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="vehicle_year" name="vehicle_year" type="number" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="review_text">
                    Review
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="review_text" name="review_text" rows="4" required></textarea>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Submit Review
                </button>
            </div>
        </form>
    </div>
</body>
</html>
