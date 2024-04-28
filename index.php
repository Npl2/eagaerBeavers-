<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


if (isset($_COOKIE['username'])) {
    header('Location: forum.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $exchangeName = 'user_auth';
    $routingKey = 'user_management';

    $data = json_decode(file_get_contents('php://input'), true);

    // Prepare the data to be sent to RabbitMQ
    $loginData = [
        'type' => 'login',
        'username' => $data['username'] ?? null,
        'password' => $data['password'] ?? null,
    ];
    
    $response = $client->send_request($loginData, $exchangeName, $routingKey);
    if($response && $response['message'] === true){
        setcookie('username', $data['username'], time() + 3600, "/");

    }
    echo json_encode(['success' => $response]);
    exit();
}

?>

<html>
<head>
    <title>Login Page</title>
    <link href="css/index.css" rel="stylesheet"> 
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>

<header class="bg-blue-800">
      <nav class="m-auto w-11/12 flex items-center justify-between">
        <h1 class="py-5 font-bold text-white text-3xl">EagerDrivers</h1>
        <div class="flex items-center text-white">
          <div class="flex items-center">
            <a
              class="p-1 bg-white text-blue-800 font-bold rounded"
              href="register.php"
            >
              Register</a
            >
          </div>
        </div>
      </nav>
    </header>

    <div class="login-container">
        <div class="login-box">
            <h1 class="Welcome">Welcome Back!</h1>
            <p class="login-description">Please enter your credentials to log in</p>
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
                <button type="button" class="login-button bg-[#007bff]" onclick="SendLoginRequest()">Login</button>
            </form>
            <p class="register-link">Don't have an account? <a href="register.php">Register</a></p>
        </div>
        <div id="textResponse" class="response-message"></div>
    </div>

<!-- Fix later, unsure why the fixed position is causing layout problems
    <footer>
        <p>&copy; 2024 EagerDrivers. All rights reserved.</p>
    </footer>
-->
<script>
    function SendLoginRequest() {
        const username = document.getElementById("un").value;
        const password = document.getElementById("pw").value;

        const requestData = {
            username: username,
            password: password,
        };
        console.log(requestData);
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
                alert("Successfully logged in.");
                window.location.href = 'forum.php'; // Redirect directly
            } else {
                document.getElementById("textResponse").innerHTML = 'Login failed';
            }
        })
        .catch(error => {
            console.error('Error sending login request:', error);
            document.getElementById("textResponse").innerHTML = "Error: Failed to process request.";
        });
    }
</script>

</body>
</html>