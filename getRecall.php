<?php
    use PhpAmqpLib\Connection\AMQPStreamConnection;
    use PhpAmqpLib\Message\AMQPMessage;
    require 'vendor/autoload.php';
    require_once('path.inc');
    require_once('get_host_info.inc');
    require_once('rabbitMQLib.inc');

if (!isset($_POST['make'], $_POST['model'], $_POST['year'])) {
    echo "Insufficient information to fetch recalls for this vehicle.";
    exit();
}

$recallRequest = [
    'type' => 'getRecall',
    'make' => $_POST['make'],
    'model' => $_POST['model'],
    'year' => $_POST['year'],
];

$clientCar = new rabbitMQClient("testRabbitMQ.ini", "carAPI");
$exchangeNameCar = 'car_api_exchange';
$routingKeyCar = 'car_api_queue';

$recallResponse = $clientCar->send_request($recallRequest, $exchangeNameCar, $routingKeyCar);

if ($recallResponse && isset($recallResponse['response']['results'])) {
    echo "<h2>Recalls:</h2>";
    foreach ($recallResponse['response']['results'] as $index => $recall) {
        echo "<h3>Recall #" . ($index + 1) . ":</h3>";
        echo "<p><strong>Manufacturer:</strong> {$recall['Manufacturer']}</p>";
        echo "<p><strong>NHTSA Campaign Number:</strong> {$recall['NHTSACampaignNumber']}</p>";
        echo "<p><strong>Report Received Date:</strong> {$recall['ReportReceivedDate']}</p>";
        echo "<p><strong>Component:</strong> {$recall['Component']}</p>";
        echo "<p><strong>Summary:</strong> {$recall['Summary']}</p>";
        echo "<p><strong>Consequence:</strong> {$recall['Consequence']}</p>";
        echo "<p><strong>Remedy:</strong> {$recall['Remedy']}</p>";
        echo "<p><strong>Notes:</strong> {$recall['Notes']}</p>";
        echo "----------------------------------------------";
    }
} else {
    echo "<p>No recall information available for this vehicle.</p>";
}
?>
