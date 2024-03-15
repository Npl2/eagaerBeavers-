<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

header('Content-Type: application/json');

if (isset($_COOKIE['username'])) {
    echo json_encode(['redirect' => 'forum.php']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $data = json_decode(file_get_contents('php://input'), true);

    $loginData = [
        'type' => 'login',
        'username' => $data['username'] ?? '',
        'password' => $data['password'] ?? '',
    ];

    $response = $client->send_request($loginData);

    if ($response && $response['message'] == true) {
        setcookie('username', $data['username'], time() + 3600, "/");
        echo json_encode(['success' => true, 'message' => 'Login successful', 'redirect' => 'forum.php']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Login failed']);
    }
    exit();
}
?>

<html>
<head>
    <title>Login Page</title>
    <link href="css/index.css" rel="stylesheet"> 
</head>
<body>

    <header>
        <h1 class="site-title">EagerDrivers</h1> 
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
                <button type="button" class="login-button" onclick="SendLoginRequest()">Login</button>
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

        const requestData = { username: username, password: password };

        fetch('<?php echo $_SERVER["PHP_SELF"]; ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.redirect) {
                // Server requested a redirect (user already logged in or just logged in successfully)
                window.location.href = data.redirect;
            } else if (data.success) {
                alert(data.message);
                window.location.href = data.redirect;
            } else {
                document.getElementById("textResponse").innerHTML = "Login failed: " + data.message;
            }
        })
        .catch(error => {
            console.error('Error sending login request:', error);
            document.getElementById("textResponse").innerHTML = "Error: Failed to process request.";
        });
    }


        
                function handleLoginResponse(response) {
            try {
                document.getElementById("textResponse").innerHTML = "Response: " + response;
                
                // Check if login was successful
                if (response['message'] == "Login successful") {
                    console.log('Login succeeded!');
                } else {
                    console.log('Login failed:', response.error);
                }
            } catch (error) {
                console.error('Error parsing login response:', error);
                document.getElementById("textResponse").innerHTML = "Error: Failed to parse response.";
            }
        }   
    </script>
</body>
</html>