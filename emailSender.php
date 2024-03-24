<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['username'])) {
$username = $_COOKIE['username']; 

$clientUser = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$exchangeName = 'user_auth';
$routingKey = 'user_management';

$clientCar = new rabbitMQClient("testRabbitMQ.ini", "carAPI");
$exchangeNameCar = 'car_api_exchange';
$routingKeyCar = 'car_api_queue';

// Function to send recall email notifications
function sendRecallEmail($email, $recallDetails) {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'hp548@njit.edu';
    $mail->Password = 'mkfh blnh ohoo jfbk'; 
    $mail->Port = 587;

    $mail->setFrom('hp548@njit.edu', 'Recall Notification System');
    $mail->addAddress($email);

    $mail->isHTML(true);

    $mail->Subject = 'New Vehicle Recall Notification';
    $mail->Body    = $recallDetails;
    $mail->AltBody = strip_tags($recallDetails);

    $mail->send();
}


$requestVehicles = [
    'type' => 'get_user_car_regs',
    'username' => $username
];
$responseVehicles = $clientUser->send_request($requestVehicles, $exchangeName, $routingKey);

$requestTodos = [
    'type' => 'get_recall_todos_by_username',
    'username' => $username
];
$responseTodos = $clientUser->send_request($requestTodos, $exchangeName, $routingKey);
$existingRecalls = array_column($responseTodos['data'], 'nhtsaCampaignNumber');

foreach ($responseVehicles['data'] as $vehicle) {
    $recallRequest = [
        'type' => 'getRecall',
        'make' => $vehicle['make'],
        'model' => $vehicle['model'],
        'year' => $vehicle['year']
    ];
    $recallResponse = $clientCar->send_request($recallRequest, $exchangeNameCar, $routingKeyCar);
    foreach ($recallResponse['response']['results'] as $recall) {
        if (!in_array($recall['NHTSACampaignNumber'], $existingRecalls)) {
            
            $recallDetails = "A new recall has been issued for your vehicle: Make: {$vehicle['make']}, Model: {$vehicle['model']}, Year: {$vehicle['year']}, NHTSA Campaign Number: {$recall['NHTSACampaignNumber']}";
            sendRecallEmail($username . "@njit.edu", $recallDetails);
        }
    }
}
echo "Recall check and email sending process complete for $username.";
}

?>
