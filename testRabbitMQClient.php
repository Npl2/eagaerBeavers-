#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$exchangeName = 'user_auth';
$routingKey = 'user_management';

$loginRequest = [
    'type' => 'get_blog_post_with_comments',
    'postId' => "65f961ef6054d3b6ab071f43"
];

$response = $client->send_request($loginRequest, $exchangeName, $routingKey);

echo "Client received response: " . PHP_EOL;
if ($response && $response['message'] == "Blog post with comments fetched successfully" && $response['data']['success']) {
    $post = $response['data']['post'];
    echo "ID: " . $post['_id']['$oid'] . PHP_EOL;
    echo "Author: " . $post['author'] . PHP_EOL;
    echo "Title: " . $post['title'] . PHP_EOL;
    echo "Content: " . $post['content'] . PHP_EOL;
    $timestampInSeconds = intval($post['created_at']['$date']['$numberLong']) / 1000;
    $date = date('Y-m-d H:i:s', $timestampInSeconds);
    echo "Posted on: " . $date . PHP_EOL;
    echo "Comments:" . PHP_EOL;
    foreach ($post['comments'] as $comment) {
        echo "    Comment ID: " . $comment['_id']['$oid'] . PHP_EOL;
        echo "    Username: " . $comment['username'] . PHP_EOL;
        echo "    Comment: " . $comment['comment'] . PHP_EOL;
        $commentTimestampInSeconds = intval($comment['created_at']['$date']['$numberLong']) / 1000;
        $commentDate = date('Y-m-d H:i:s', $commentTimestampInSeconds);
        echo "    Posted on: " . $commentDate . PHP_EOL;
        echo "    ---------------" . PHP_EOL;
    }

    
} else {
    echo "Request Failed" . PHP_EOL;
}

echo "\n\n";

echo $argv[0] . " END" . PHP_EOL;
