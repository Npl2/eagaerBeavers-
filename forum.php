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
        <!-- <link href="css/forum.css" rel="stylesheet">  -->
        <!-- <link href="css/header.css" rel="stylesheet"> -->
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body>
        <?php include 'header.php'; ?>
        <div class="p-12 w-full flex items-center justify-center">
        <div class="flex w-8/12">
            <div class="mr-5 p-5 w-4/12 bg-white shadow">
                <h4 class="font-bold text-xl">Options</h4>
                <ul class="mt-5">
                    <li class="py-2 font-semibold hover:bg-black hover:text-white hover:px-2 hover:rounded transition-all duration-200 ease-in-out">
                        <a href="#">Create Discussions Post</a>
                    </li>
                </ul>
            </div>
            <div class="p-5 w-full">
                <h4 class="capitalize text-4xl font-bold">
                    Welcome to the Discussions Forum
                </h4>

                <?php foreach ($discussions as $discussion) : ?>
                    <div class="mt-5">
                        <div class="mb-5 p-10 cursor-pointer hover:bg-black hover:text-white transition-all duration-200 ease-in-out flex flex-col item-center justiy-center bg-white rounded-md shadow">
                            <h2 <?php echo isset($discussion['_id']['$oid']) ? 'hidden' : ''; ?>><?php echo isset($discussion['_id']['$oid']) ? $discussion['_id']['$oid'] : "ID: Not Available"; ?></h2>

                            <h3 class="mb-1 capitalize font-semibold text-2xl">
                                <a href="displayDiscussion.php?discussion_id=<?php echo $discussion['_id']['$oid']; ?>"><?php echo $discussion['title']; ?></a>
                            </h3>
                            <p class="italic">Author: <?php echo $discussion['author']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    </body>
</html>