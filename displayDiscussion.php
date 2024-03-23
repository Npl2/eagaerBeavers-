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
    <!-- <link href="css/displayDiscussion.css" rel="stylesheet">  -->
    <!-- <link href="css/header.css" rel="stylesheet"> -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="h-screen flex items-center justify-center">
        <div class="p-5 w-1/2 shadow-md rounded">
            <h4 class="font-bold capitalize text-center text-lg">
                Create Discussion
            </h4>

            <div class="mt-5 p-3 border border-1 border-gray-200 rounded">
                <?php echo '<p>' . $post['title'] . '</p>' ?>
                <?php echo '<p>' . $post['content'] . '</p>' ?>
                <p class="mt-2 uppercase text-[0.55rem] font-bold text-right">
                    From: ABena <span class="font-normal">|</span> Date: <?= date('Y-m-d H:i:s', intval($post['created_at']['$date']['$numberLong']) / 1000) ?>
                </p>
            </div>
            <div class="h-20 text-[0.65rem] overflow-y-auto">
                <?php foreach ($post['comments'] as $comment) : ?>
                    <div class="mt-1 p-1">
                        <p>
                            <?= $comment['comment']; ?>
                        </p>
                        <p class="mt-1 uppercase text-[0.55rem] font-bold text-right">
                            From: ABena <span class="font-normal">|</span> Date: <?= date('Y-m-d H:i:s', intval($comment['created_at']['$date']['$numberLong']) / 1000) ?>
                        </p>
                    </div>
                <?php endforeach ?>
            </div>
            <form action="addComment.php" method="post">
                <input type="hidden" name="postId" value="<?= $discussionId; ?>">
                <input type="hidden" id="username" name="username" value="<?= $_COOKIE['username']; ?>" required>
                <textarea class="h-10 p-1 w-full text-xs italic rounded" name="comment" id="comment" placeholder="Type a comment"></textarea>
                <div class="text-right text-xs">
                    <button type="submit" class="w-12 p-1 font-bold text-white bg-blue-500 rounded">
                        Post
                    </button>
                </div>
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
