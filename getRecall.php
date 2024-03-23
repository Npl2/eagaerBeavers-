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
    echo "<div class='container mx-auto px-4 py-8'>";
    echo "<h2 class='text-3xl font-bold mb-6'>Recalls:</h2>";
    foreach ($recallResponse['response']['results'] as $index => $recall) {
        echo "<div class='bg-white rounded-lg shadow-md mb-6'>";
        echo "<div class='p-6'>";
        echo "<h3 class='text-xl font-bold mb-2'>Recall #" . ($index + 1) . ":</h3>";
        echo "<p><strong>Manufacturer:</strong> {$recall['Manufacturer']}</p>";
        echo "<p><strong>NHTSA Campaign Number:</strong> {$recall['NHTSACampaignNumber']}</p>";
        echo "<p><strong>Report Received Date:</strong> {$recall['ReportReceivedDate']}</p>";
        echo "<p><strong>Component:</strong> {$recall['Component']}</p>";
        echo "<p><strong>Summary:</strong> {$recall['Summary']}</p>";
        echo "<p><strong>Consequence:</strong> {$recall['Consequence']}</p>";
        echo "<p><strong>Remedy:</strong> {$recall['Remedy']}</p>";
        echo "<p><strong>Notes:</strong> {$recall['Notes']}</p>";
    
        // Use a form for each recall
        echo "<form action='insertRecallToDo.php' method='get' class='mt-4'>";
        echo "<input type='hidden' name='manufacturer' value='{$recall['Manufacturer']}'>";
        echo "<input type='hidden' name='model' value='{$recall['Model']}'>";
        echo "<input type='hidden' name='year' value='{$recall['ModelYear']}'>";
        echo "<input type='hidden' name='make' value='{$recall['Make']}'>";
        echo "<input type='hidden' name='component' value='{$recall['Component']}'>";
        echo "<input type='hidden' name='summary' value='{$recall['Summary']}'>";
        echo "<input type='hidden' name='consequence' value='{$recall['Consequence']}'>";
        echo "<input type='hidden' name='remedy' value='{$recall['Remedy']}'>";
        echo "<input type='hidden' name='notes' value='{$recall['Notes']}'>";
        echo "<button type='submit' class='bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition duration-300'>Add to TODO</button>";
        echo "</form>";
        echo "</div>"; 
        echo "</div>"; 
    }
    echo "</div>"; 
} else {
echo "<div class='container mx-auto flex justify-center items-center h-full'>";
echo "<p class='text-gray-600 text-lg'>No recall information available for this vehicle.</p>";
echo "</div>";

}

?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
    $('.add-todo-btn').click(function() {
        var manufacturer = $(this).data('manufacturer');
        var model = $(this).data('model');
        var year = $(this).data('year');
        var component = $(this).data('component');
        var summary = $(this).data('summary');
        var consequence = $(this).data('consequence');
        var remedy = $(this).data('remedy');
        var notes = $(this).data('notes');

        $.ajax({
            url: 'insertRecallToDo.php',
            method: 'GET',
            data: {
                manufacturer: manufacturer,
                model: model,
                year: year,
                recalls: [{
                    Component: component,
                    Summary: summary,
                    Consequence: consequence,
                    Remedy: remedy,
                    Notes: notes,
                }]
            },
            success: function(response) {
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
});

</script>