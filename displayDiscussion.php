<?php
    use PhpAmqpLib\Connection\AMQPStreamConnection;
    use PhpAmqpLib\Message\AMQPMessage;
    require 'vendor/autoload.php';
    require_once('path.inc');
    require_once('get_host_info.inc');
    require_once('rabbitMQLib.inc');

    if (!isset($_GET['discussion_id'])) {
        header('Location: index.php');
        exit();
    }

    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $exchangeName = 'user_auth';
    $routingKey = 'user_management';

    $discussionId = $_GET['discussion_id'];
    $loginRequest = [
        'type' => 'get_blog_post_with_comments',
        'postId' => $discussionId
    ];

    $response = $client->send_request($loginRequest, $exchangeName, $routingKey);

    if ($response && $response['message'] == "Blog post with comments fetched successfully" && $response['data']['success']) {
        $post = $response['data']['post'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post['title']; ?></title>
    <link href="css/displayDiscussion.css" rel="stylesheet"> 
    <link href="css/header.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="discussionBox">
        <div class="user-discussion">
            <h1><?php echo $post['title']; ?></h1>
            <p>Author: <?php echo $post['author']; ?></p>
            <p>Content: <?php echo $post['content']; ?></p>
            <p>Posted on: <?php echo date('Y-m-d H:i:s', intval($post['created_at']['$date']['$numberLong']) / 1000); ?></p>
            <h2>Comments:</h2>
            <?php foreach ($post['comments'] as $comment): ?>
                <div class="comment">
                    <p>Username: <?php echo $comment['username']; ?></p>
                    <p>Comment: <?php echo $comment['comment']; ?></p>
                    <p>Posted on: <?php echo date('Y-m-d H:i:s', intval($comment['created_at']['$date']['$numberLong']) / 1000); ?></p>
                </div>
            <?php endforeach; ?>
            <hr>
            <form action="addComment.php" method="post">
                <input type="hidden" name="postId" value="<?php echo $discussionId; ?>">
                <br>
                <input type="hidden" id="username" name="username" value="<?php echo $_COOKIE['username']; ?>" required><br>
                <label for="comment">Comment:</label><br>
                <textarea id="comment" name="comment" rows="4" cols="50" required></textarea><br>
                <input type="submit" value="Add Comment">
            </form>
        </div>
    </div>
</body>
</html>


<?php
} else {
    echo "Error: Request failed.";
}
?>
