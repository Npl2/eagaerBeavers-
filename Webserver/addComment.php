<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if (!isset($_POST['postId'], $_POST['username'], $_POST['comment'])) {
    header('Location: index.php');
    exit();
}

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$exchangeName = 'user_auth';
$routingKey = 'user_management';

$loginRequest = [
    'type' => 'add_comment_post',
    'postId' => $_POST['postId'],
    'username' => $_POST['username'],
    'comment' => $_POST['comment']
];

$response = $client->send_request($loginRequest, $exchangeName, $routingKey);

if ($response && $response['success']) {
    header('Location: displayDiscussion.php?discussion_id=' . $_POST['postId'] . '&success=true');
    exit();
} else {
    header('Location: displayDiscussion.php?discussion_id=' . $_POST['postId'] . '&error=true');
    exit();
}
?>
