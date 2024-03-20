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
?>

<?php
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $exchangeName = 'user_auth';
    $routingKey = 'user_management';

    $loginRequest = [
        'type' => "list_blog_posts",
    ];

    $response = $client->send_request($loginRequest, $exchangeName, $routingKey);

    $discussions = [];
    if ($response && $response['message'] == "Blog posts fetched successfully") {
        $discussions = $response['data'];
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Discussion Forum</title>
        <link href="css/forum.css" rel="stylesheet"> 
        <link href="css/header.css" rel="stylesheet">
    </head>
    <body>
        <?php include 'header.php'; ?>
        <div class="discussionBox">
            <div class="sideBar-topics">
                <h2>Categories</h2>
                <ul>
                    <li><a href="#">Cars</a></li>
                    <li><a href="#">Makes</a></li>
                    <li><a href="#">Models</a></li>
                    <li><a href="createDiscussion.php">Create Discussion Post</a></li>
                </ul>
            </div>
            <div class="user-discussion">
                <h1>Welcome to the Discussion Forum</h1>
                <?php foreach ($discussions as $discussion): ?>
                    <div class="discussion">
                        <h2 <?php echo isset($discussion['_id']['$oid']) ? 'hidden' : ''; ?>><?php echo isset($discussion['_id']['$oid']) ? $discussion['_id']['$oid'] : "ID: Not Available"; ?></h2>
                        <h2><a href="displayDiscussion.php?discussion_id=<?php echo $discussion['_id']['$oid']; ?>"><?php echo $discussion['title']; ?></a></h2>
                        <h2>Author: <?php echo $discussion['author']; ?></h2><br>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </body>
</html>