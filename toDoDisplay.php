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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vehicle Recall Todos</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gray-100">
<?php include 'header.php'; ?>
    <div class="container w-full lg:w-1/2 mx-auto px-4 py-8">
        <?php if ($response && $response['message'] == "Recall todos fetched successfully") : ?>
            <div class="text-xl font-semibold mb-6">Recall Todos for User: <?= htmlspecialchars($_COOKIE['username']); ?></div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Pending Todos:</h3>
                <?php foreach ($response['data'] as $todo) : ?>
                    <?php if ($todo['status'] == 'pending') : ?>
                        <div class="bg-yellow-200 p-6 rounded-lg shadow mb-4 flex justify-between items-center">
                            <div>
                                <p><strong>Make:</strong> <?= htmlspecialchars($todo['make']); ?></p>
                                <p><strong>Model:</strong> <?= htmlspecialchars($todo['model']); ?></p>
                                <p><strong>Year:</strong> <?= htmlspecialchars($todo['year']); ?></p>
                                <p><strong>Component:</strong> <?= htmlspecialchars($todo['component']); ?></p>
                                <p><strong>Summary:</strong> <?= htmlspecialchars($todo['summary']); ?></p>
                                <p><strong>Remedy:</strong> <?= htmlspecialchars($todo['remedy']); ?></p>
                                <p><strong>Notes:</strong> <?= htmlspecialchars($todo['notes']); ?></p>
                                <?php
                                $date = new DateTime();
                                $timestamp = $todo['created_at']['$date']['$numberLong'] / 1000;
                                $date->setTimestamp($timestamp);
                                $formattedDate = $date->format('Y-m-d H:i:s');
                                ?>
                                <p><strong>Created At:</strong> <?= $formattedDate; ?></p>
                            </div>
                            <!-- Form to update status -->
                            <form action="updateTodo.php" method="post" class="flex items-center">
                                <input type="hidden" name="todoId" value="<?= $todo['_id']['oid']; ?>">
                                <input type="hidden" name="newStatus" value="completed">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" onchange="this.form.submit()" class="form-checkbox h-5 w-5 text-blue-600"><span class="ml-2 text-gray-700">Mark as Completed</span>
                                </label>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="mt-10">
                <h3 class="text-lg font-semibold mb-4">Completed Todos:</h3>
                <div class="space-y-4">
                    <?php foreach ($response['data'] as $todo) : ?>
                        <?php if ($todo['status'] == 'completed') : ?>
                            <div class="p-6 rounded-lg shadow bg-green-200">
                                <p><strong>ID:</strong> <?= htmlspecialchars($todo['_id']['oid']); ?></p>
                                <p><strong>Make:</strong> <?= htmlspecialchars($todo['make']); ?></p>
                                <p><strong>Model:</strong> <?= htmlspecialchars($todo['model']); ?></p>
                                <p><strong>Year:</strong> <?= htmlspecialchars($todo['year']); ?></p>
                                <p><strong>Component:</strong> <?= htmlspecialchars($todo['component']); ?></p>
                                <p><strong>Status:</strong> <?= htmlspecialchars($todo['status']); ?></p>
                                <?php
                                $date = new DateTime();
                                $timestamp = $todo['created_at']['$date']['$numberLong'] / 1000;
                                $date->setTimestamp($timestamp);
                                $formattedDate = $date->format('Y-m-d H:i:s');
                                ?>
                                <p><strong>Created At:</strong> <?= $formattedDate; ?></p>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else : ?>
            <p class="text-center text-lg">No recall todos found for user <?= htmlspecialchars($_COOKIE['username']); ?>.</p>
        <?php endif; ?>
    </div>
</body>
</html>
