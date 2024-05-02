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
    <div class="p-5 md:p-8 lg:p-12 w-full flex items-center justify-center">
        <div class="flex flex-col md:flex-row lg:w-8/12">
            <div class="lg:mr-5 p-5 lg:w-4/12 bg-white md:shadow">
                <h4 class="hidden md:block font-medium lg:font-bold text-lg lg:text-xl">Options</h4>
                <ul class="mt-5 bg-blue-700 md:bg-transparent">
                    <li class="py-2 text-center md:text-lect text-white md:text-black font-semibold hover:bg-black hover:text-white hover:px-2 hover:rounded transition-all duration-200 ease-in-out">
                        <a href="createDiscussion.php">Create Discussions Post</a>
                    </li>
                </ul>
            </div>
            <div class="p-5 w-full">
                <h4 class="capitalize text-center md:text-left text-lg lg:text-4xl font-normal lg:font-bold">
                    Welcome to the Discussions Forum
                </h4>
                <?php foreach ($discussions as $discussion) : ?>
                    <div class="mt-5">
                        <div class="mb-5 p-3 md:p-5 md:p-10 cursor-pointer hover:bg-black hover:text-white transition-all duration-200 ease-in-out flex flex-col item-center justiy-center bg-white rounded-md shadow">
                            <h2 <?php echo isset($discussion['_id']['$oid']) ? 'hidden' : ''; ?>><?php echo isset($discussion['_id']['$oid']) ? $discussion['_id']['$oid'] : "ID: Not Available"; ?></h2>
                            <h3 class="mb-1 capitalize font-semibold  text-base md:text-xl lg:text-2xl">
                                <a href="displayDiscussion.php?discussion_id=<?php echo $discussion['_id']['$oid']; ?>"><?php echo $discussion['title']; ?></a>
                            </h3>
                            <p class="italic text-sm md:text-normal">Author: <?php echo $discussion['author']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php include 'responsiveNavScript.php'; ?>
    </body>
</html>