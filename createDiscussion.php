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
        <link href="css/createDiscussion.css" rel="stylesheet"> 
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
                </ul>
            </div>
            <div class="user-discussion">
                <h1>Create a Discussion</h1>
                
                <form id="createDiscussionForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="discussionTitle">Discussion Title:</label>
                        <input type="text" id="discussionTitle" name="discussionTitle" placeholder="Enter the title of your discussion" required>
                    </div>
                    <div class="form-group">
                        <label for="discussionContent">Discussion Content:</label>
                        <textarea id="discussionContent" name="discussionContent" rows="4" placeholder="Enter the content of your discussion" required></textarea>
                    </div>
                    <!--
                    <div class="form-group">
                        <label for="discussionType">Discussion Type:</label>
                        <select id="discussionType" name="discussionType">
                            <option value="car">Car</option>
                            <option value="make">Make</option>
                            <option value="model">Model</option>
                        </select>
                    </div>
                    -->
                    <button class="createDiscussionButton" type="submit">Create Discussion</button>
                </form>
            </div>
        </div>
    </body>
</html>