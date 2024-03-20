<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// Check if the user is already logged in
if (isset($_COOKIE['username'])) {
    header('Location: index.php');
    exit();
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {

    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $exchangeName = 'user_auth';
    $routingKey = 'user_management';

    // Get form data
    $make = $_POST['make'] ?? null;
    $model = $_POST['model'] ?? null;
    $year = $_POST['year'] ?? null;

    // Prepare the data to be sent to RabbitMQ
    $registrationData = [
        'type' => 'register_vehicle',
        'make' => $make,
        'model' => $model,
        'year' => $year,
        'username' => $_COOKIE['username'] ?? null,
    ];

    $response = $client->send_request($registrationData, $exchangeName, $routingKey);
    echo json_encode(['success' => $response]);
    exit();
}
?>

<html>
<head>
    <title>Vehicle Registration</title>
    <link href="css/index.css" rel="stylesheet"> 
</head>
<body>

    <header>
        <h1 class="site-title">EagerDrivers</h1> 
    </header>

    <div class="registration-container">
        <div class="registration-box">
            <h1 class="Welcome">Register Your Vehicle</h1>
            <form id="registrationForm" method="post" onsubmit="return false;">
                <div class="form-group">
                    <label for="make">Make:</label>
                    <input type="text" id="make" name="make">
                </div>
                <div class="form-group">
                    <label for="model">Model:</label>
                    <input type="text" id="model" name="model">
                </div>
                <div class="form-group">
                    <label for="year">Year:</label>
                    <input type="text" id="year" name="year">
                </div>
                <button type="button" class="register-button" onclick="SendRegistrationRequest()">Register</button>
                <input type="hidden" name="register" value="1">
            </form>
        </div>
        <div id="registrationResponse" class="response-message"></div>
    </div>

<script>
    function SendRegistrationRequest() {
        const make = document.getElementById("make").value;
        const model = document.getElementById("model").value;
        const year = document.getElementById("year").value;

        const requestData = {
            make: make,
            model: model,
            year: year,
        };

        fetch('<?php echo $_SERVER["PHP_SELF"]; ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json()) // Parse response as JSON
        .then(data => {
            console.log(data.success.message);
            if (data.success.message) {
                document.getElementById("registrationResponse").innerHTML = 'Vehicle registered successfully.';
            } else {
                document.getElementById("registrationResponse").innerHTML = 'Failed to register vehicle.';
            }
        })
        .catch(error => {
            console.error('Error sending registration request:', error);
            document.getElementById("registrationResponse").innerHTML = "Error: Failed to process request.";
        });
    }
</script>

</body>
</html>
