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
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if (isset($_POST['discussionTitle']) && isset($_POST['discussionContent'])) {
            
            $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
            $exchangeName = 'user_auth';
            $routingKey = 'user_management';

            $title = htmlspecialchars($_POST['discussionTitle']);
            $content = htmlspecialchars($_POST['discussionContent']);

            $loginRequest = [
                'type' => "add_blog_post",
                'username' => $_COOKIE['username'],
                'title' => $title,
                'content' => $content
            ];
            
            $response = $client->send_request($loginRequest, $exchangeName, $routingKey);

            if ($response && $response['message']) {
                echo "Successful";
                header('Location: forum.php');
            } else {
                echo "Request Failed";
            }

            header("Location: forum.php");
            exit();
        } else {
            echo "Error: Discussion title and content are required.";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Discussion - Discussion Forum</title>
        <!-- <link href="css/createDiscussion.css" rel="stylesheet">  -->
        <!-- <link href="css/header.css" rel="stylesheet"> -->
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100">
        <?php include 'header.php'; ?>
        <div class="discussionBox">
            <!-- <div class="sideBar-topics">
                <h2>Categories</h2>
                <ul>
                    <li><a href="#">Cars</a></li>
                    <li><a href="#">Makes</a></li>
                    <li><a href="#">Models</a></li>
                </ul>
            </div> -->
            

        <div class="h-screen flex items-center justify-center">
      <div class="p-5 w-96 shadow-md rounded">
        <h4 class="font-bold capitalize text-center text-lg">
          Create Discussion
        </h4>

        <form id="createDiscussionForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <div class="mt-5">
            <label for="discussionTitle">Discussion Title</label>
            <input
              class="mt-1 p-1 w-full border border-gray-200 rounded"
              type="text"
              name="discussionTitle"
              id="discussionTitle"
              placeholder="Enter discussion title"
              required
            />
          </div>
          <div class="mt-5">
            <label for="discussionContent">Discussion Title</label>
            <textarea
              class="w-full h-44 border border-gray-200"
              name="discussionContent"
              id="discussionContent"
              placeholder="Enter discussion content"
              required
            ></textarea>
          </div>
          <div>
            <button
              class="createDiscussionButton mt-1 w-full border-0 bg-blue-500 hover:bg-blue-600 transition-all duration-100 ease-in-out rounded font-bold text-white" type="submit"
            >
              Post
            </button>
          </div>
        </form>
      </div>
    </div>
    </body>
</html>