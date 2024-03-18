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

    // Checks if register was successful
    if ($response && $response['message'] === true) {
        // Registration succeeded, show popup
        echo '<script>showPopup();</script>';
    } else {
        echo "Howdy Sur yerr";
    }

    return $response; 
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

<!-- Fix later, unsure why the fixed position is causing layout problems
    <footer>
        <p>&copy; 2024 EagerDrivers. All rights reserved.</p>
    </footer>
-->

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
            .then(response => response)
            .then(data => {
                console.log(data);
                handleregisterResponse(data); // Make sure this function is defined to handle the response
            })
            .catch(error => {
                console.error('Error sending register request:', error);
            });
        }

        function handleregisterResponse(response) {
            try {
                document.getElementById("textResponse").innerHTML = "Response: " + response;
                
                // Check if register was successful
                if (response && response.status == 200) {
                    // Registration succeeded, show popup
                    showPopup();
                } else {
                    console.log('Registration failed:', response.error);
                }
            } catch (error) {
                console.error('Error parsing register response:', error);
                document.getElementById("textResponse").innerHTML = "Error: Failed to parse response.";
            }
        }   

        function showPopup() {
            document.getElementById('popupContainer').classList.remove('hidden');
        }

        function hidePopup() {
            document.getElementById('popupContainer').classList.add('hidden');
        }
    </script>
</body>
</html>
