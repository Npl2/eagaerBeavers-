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
    $registerData = [
        'type' => 'signup',
        'username' => $data['username'] ?? null,
        'password' => $data['password'] ?? null,
        
    ];

    $response = $client->send_request($registerData);

    // Check if register was successful
    if ($response && $response['status'] == 'success') {
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
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <h1 class="text-3xl mt-10 mb-4 text-center">Register page</h1>
    <div class="max-w-md mx-auto bg-white shadow-md rounded px-8 py-6">
        <form id="registerForm" onsubmit="return false;">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username:</label>
                <input type="text" id="un" name="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                <input type="password" id="pw" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" onclick="SendregisterRequest()">Register</button>
        </form>
        <p class="mt-4 text-sm text-center">Already have an account? <a href="index.php" class="text-blue-500">Login</a></p>
    </div>
    <div id="textResponse" class="mt-4"></div>

    <!-- Popup container -->
    <div id="popupContainer" class="fixed top-0 left-0 w-full h-full flex justify-center items-center bg-black bg-opacity-50 hidden">
        <!-- Popup content -->
        <div class="bg-white rounded-lg p-8 max-w-md">
            <h2 class="text-lg font-semibold mb-4">Registration Successful!</h2>
            <p>Your registration was successful.</p>
            <!-- Close button -->
            <button id="closePopup" class="mt-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" onclick="hidePopup()">Close</button>
        </div>
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
