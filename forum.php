<?php
if (!isset($_COOKIE['username'])) {
    header('Location: index.php');
    exit();
}
?>
<?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $client = new rabbitMQClient("testRabbitMQ.ini", "testServer"); //maybe change "testServer" 
        $exchangeName = 'discussion'; //create the proper exchanges and routing keys tomorrow morning
        $routingKey = 'get_discussions';

        $requestData = [
            'type' => 'get_discussions',
        ];

        $response = $client->send_request($requestData, $exchangeName, $routingKey);

        if ($response !== false) {
            
            $discussions = json_decode($response, true);

            if (!empty($discussions)) {
            } else {
                echo "Error: Failed to retrieve discussions.";
            }
            } else {
                echo "Error: Failed to send request to RabbitMQ.";
            }
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
                    <h2><?php echo $discussion['title']; ?></h2>
                    <p><?php echo $discussion['content']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>