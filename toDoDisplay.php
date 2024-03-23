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

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$exchangeName = 'user_auth';
$routingKey = 'user_management';

$request = [
    'type' => 'get_recall_todos_by_username',
    'username' => $_COOKIE['username'], 
];

$response = $client->send_request($request, $exchangeName, $routingKey);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Recall Todos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/header.css" rel="stylesheet">
    <link href="css/toDoDisplay.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
    <?php if ($response && $response['message'] == "Recall todos fetched successfully"): ?>
        <h2>Recall todos for user <?php echo $_COOKIE['username']; ?>:</h2>
        <h3>Pending Todos:</h3>
        <ul class="list-group">
            <?php foreach ($response['data'] as $todo): ?>
                <?php if ($todo['status'] == 'pending'): ?>
                    <li class="list-group-item">
                        <strong>Make:</strong> <?php echo $todo['make']; ?><br>
                        <strong>Model:</strong> <?php echo $todo['model']; ?><br>
                        <strong>Year:</strong> <?php echo $todo['year']; ?><br>
                        <?php
                        // Splitting task into sections
                        $sections = explode(".", $todo['task']);
                        ?>

                        <strong>Recall Notice:</strong>
                        <ul>
                            <?php foreach ($sections as $section): ?>
                                <?php if (strpos($section, "Recall Notice:") !== false): ?>
                                    <li><?php echo $section; ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>

                        <strong>Summary:</strong>
                        <ul>
                            <?php foreach ($sections as $section): ?>
                                <?php if (strpos($section, "Summary:") !== false): ?>
                                    <li><?php echo $section; ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>

                        <strong>Consequence:</strong>
                        <ul>
                            <?php foreach ($sections as $section): ?>
                                <?php if (strpos($section, "Consequence:") !== false): ?>
                                    <li><?php echo $section; ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>

                        <strong>Remedy:</strong>
                        <ul>
                            <?php foreach ($sections as $section): ?>
                                <?php if (strpos($section, "Remedy:") !== false): ?>
                                    <li><?php echo $section; ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>

                        <strong>Notes:</strong>
                        <ul>
                            <?php foreach ($sections as $section): ?>
                                <?php if (strpos($section, "Notes:") !== false): ?>
                                    <li><?php echo $section; ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>

                        <strong>Status:</strong> <?php echo $todo['status']; ?><br>
                        <?php
                        $date = new DateTime();
                        $timestamp = $todo['created_at']['$date']['$numberLong'] / 1000;
                        $date->setTimestamp($timestamp);
                        $formattedDate = $date->format('Y-m-d H:i:s');
                        ?>
                        <strong>Created At:</strong> <?php echo $formattedDate; ?><br>
                        
                        <!-- Form to update status -->
                        <form action="updateTodo.php" method="post">
                            <input type="hidden" name="todoId" value="<?php echo $todo['_id']['$oid']; ?>">
                            <input type="hidden" name="newStatus" value="completed">
                            <input type="submit" value="Mark as Completed" class="btn btn-primary">
                        </form>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <hr>
        <h3>Completed Todos:</h3>
        <ul class="list-group">
            <?php foreach ($response['data'] as $todo): ?>
                <?php if ($todo['status'] == 'completed'): ?>
                    <li class="list-group-item">
                        <strong>ID:</strong> <?php echo $todo['_id']['$oid']; ?><br>
                        <strong>Task:</strong> <?php echo $todo['task']; ?><br>
                        <strong>Status:</strong> <?php echo $todo['status']; ?><br>
                        <?php
                        $date = new DateTime();
                        $timestamp = $todo['created_at']['$date']['$numberLong'] / 1000;
                        $date->setTimestamp($timestamp);
                        $formattedDate = $date->format('Y-m-d H:i:s');
                        ?>
                        <strong>Created At:</strong> <?php echo $formattedDate; ?><br>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No recall todos found for user <?php echo $_COOKIE['username']; ?>.</p>
    <?php endif; ?>
</div>

</body>
</html>
