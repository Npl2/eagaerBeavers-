<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $data = json_decode(file_get_contents('php://input'), true);

    // Prepare the data to be sent to RabbitMQ
    $loginData = [
        'type' => 'login',
        'username' => $data['username'] ?? null,
        'password' => $data['password'] ?? null,
        
    ];

    $response = $client->send_request($loginData);
    echo "Howdy Sur yerr";
    return $response; 
}

?>
<html>
<head>
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-4">
        <h1 class="text-3xl mb-4">Login page</h1>
        <div class="max-w-md mx-auto bg-white shadow-md rounded px-8 py-6">
            <form id="loginForm" onsubmit="return false;">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username:</label>
                    <input type="text" id="un" name="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                    <input type="password" id="pw" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" onclick="SendLoginRequest()">Login</button>
            </form>
            <p class="mt-4 text-sm">Don't have an account? <a href="register.php" class="text-blue-500">Register</a></p>
        </div>
        <div id="textResponse" class="mt-4"></div>
    </div>
    <div id="textResponse">
    </div>
    <script>
        function SendLoginRequest() {
            const username = document.getElementById("un").value;
            const password = document.getElementById("pw").value;
            //const email = document.getElementById("mail").value;
            //const firstname = document.getElementById("fn").value;
            //const lastname = document.getElementById("ln").value;

            const requestData = {
                username: username,
                password: password,
                //email: email,
                //firstname: firstname,
                //lastname: lastname
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
                handleLoginResponse(data); // Make sure this function is defined to handle the response
            })
            .catch(error => {
                console.error('Error sending login request:', error);
            });
        }


        function handleLoginResponse(response) {
            try {
                document.getElementById("textResponse").innerHTML = "Response: " + response;
                
                // Check if login was successful
                if (response.status==200) {
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