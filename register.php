<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $exchangeName = 'user_auth';
    $routingKey = 'user_management';

    $data = json_decode(file_get_contents('php://input'), true);

    // Prepare the data to be sent to RabbitMQ
    $registerData = [
        'type' => 'signup',
        'username' => $data['username'] ?? null,
        'password' => $data['password'] ?? null,
        
    ];

    $response = $client->send_request($registerData, $exchangeName, $routingKey);


    //return $response; 
    echo json_encode(['success' => $response]);
    exit();
}

?>
<html>
<head>
    <title>Register Page</title>
    <link href="css/index.css" rel="stylesheet"> 
</head>
<body>

    <header>
        <h1 class="site-title">EagerDrivers</h1> 
    </header>

    <div class="login-container">

        <div class="login-box">
            <h1 class="login-heading">Register Account</h1>
            <div class="line"></div>
            <form id="loginForm" onsubmit="return false;">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="un" name="username">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="pw" name="password">
                </div>
                <button type="button" class="login-button" onclick="SendregisterRequest()">Register</button>
            </form>
            <p class="register-link">Already have an account? <a href="index.php">Login</a></p>
        </div>
        <div id="textResponse" class="response-message"></div>
    </div>

<script>
    function SendregisterRequest() {
        const username = document.getElementById("un").value;
        const password = document.getElementById("pw").value;

        const requestData = {
            username: username,
            password: password,
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
                alert("Successfully registered.");
                window.location.href = 'index.php'; // Redirect directly
            } else {
                document.getElementById("textResponse").innerHTML = 'Register failed';
            }
        })
        .catch(error => {
            console.error('Error sending register request:', error);
            document.getElementById("textResponse").innerHTML = "Error: Failed to process request.";
        });
    }
</script>
</body>
</html>
