<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/header.css" rel="stylesheet">
    <title>Recall Todo Insertion</title>
</head>
<body class="bg-gray-100">
<?php include 'header.php'; ?>
<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


if (!isset($_COOKIE['username'])) {
    echo "<p>Please log in.</p>";
    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $username = $_COOKIE['username']; 
    $make = $_GET['make'];
    $model = $_GET['model'];
    

    $year = $_GET['year'];
    $component = $_GET['component'];
    $summary = $_GET['summary'];
    $consequence = $_GET['consequence'];
    $remedy = $_GET['remedy'];
    $notes = $_GET['notes'];

 
    $recallsTodo = [
        [
            'Component' => $component,
            'Summary' => $summary,
            'Consequence' => $consequence,
            'Remedy' => $remedy,
            'Notes' => $notes
        ]
    ];


    $request = [
        'type' => 'insert_recall_todos',
        'username' => $username,
        'make' => $make,
        'model' => $model,
        'year' => $year,
        'recalls' => $recallsTodo,
    ];


    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $exchangeName = 'user_auth';
    $routingKey = 'user_management';
    
 
    $response = $client->send_request($request, $exchangeName, $routingKey);

    if ($response && $response['message'] == "Recall todos inserted successfully.") {
        echo "<p>Recall todo successfully inserted for $make $model ($year).</p>";
    } else {
        echo "<p>Failed to insert recall todo for $make $model ($year).</p>";
    }
} else {
    echo "<p>Invalid request method.</p>";
}
?>

</body>
</html>
