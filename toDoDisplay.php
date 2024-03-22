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

    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $exchangeName = 'user_auth';
    $routingKey = 'user_management';

    $request = [
        'type' => 'get_recall_todos_by_username',
        'username' => $_COOKIE['username'], 
    ];

    $response = $client->send_request($request, $exchangeName, $routingKey);

?>
<html>
  <head>
    <title>Vehicle Registration</title>
    <link href="css/header.css" rel="stylesheet">
    <link href="css/toDoDisplay.css" rel="stylesheet"> 
  </head>
  <body>
    <?php include 'header.php'; ?>

    <div class="container">

        <?php if ($response && $response['message'] == "Recall todos fetched successfully"): ?>
            <h2>Recall todos for user <?php echo $_COOKIE['username']; ?>:</h2>
            <ul>
                <?php foreach ($response['data'] as $todo): ?>
                    <li>
                        <strong>ID:</strong> <?php echo $todo['_id']['$oid']; ?><br>
                        <strong>Task:</strong> <?php echo $todo['task']; ?><br>
                        <strong>Status:</strong> <?php echo $todo['status']; ?><br>
                        <?php
                            $date = new DateTime();
                            $timestamp = $todo['created_at']['$date']['$numberLong'] / 1000;
                            $date->setTimestamp($timestamp);
                            $formattedDate = $date->format('Y-m-d H:i:s');
                        ?>
                        <strong>Created At:</strong> <?php echo $formattedDate; ?><br>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No recall todos found for user <?php echo $_COOKIE['username']; ?>.</p>
        <?php endif; ?>
    </div>
  </body>
</html>
