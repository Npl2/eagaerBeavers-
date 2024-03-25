<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if (!isset($_POST['todoId'], $_POST['newStatus'])) {
    echo "Required information is missing.";
    exit();
}

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$exchangeName = 'user_auth';
$routingKey = 'user_management';

$requestUpdateRecallTodoStatus = [
    'type' => 'update_recall_todo_status',
    'todoId' => $_POST['todoId'],
    'newStatus' => $_POST['newStatus']
];

$responseUpdateRecallTodoStatus = $client->send_request($requestUpdateRecallTodoStatus, $exchangeName, $routingKey);

if ($responseUpdateRecallTodoStatus && $responseUpdateRecallTodoStatus['message'] == "Recall todo status updated successfully.") {
    header('Location: toDoDisplay.php?status=success'); // Make sure to change 'todoList.php' to your actual todo list page if different
} else {
    header('Location: toDoDisplay.php?status=error');
}
?>
