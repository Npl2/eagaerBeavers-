<?php
  use PhpAmqpLib\Connection\AMQPStreamConnection;
  use PhpAmqpLib\Message\AMQPMessage;
  require 'vendor/autoload.php';
  require_once('path.inc');
  require_once('get_host_info.inc');
  require_once('rabbitMQLib.inc');
  require_once 'logError.php';
  
  if (!isset($_COOKIE['username'])) {
      header('Location: index.php');
      exit();
  }


  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $exchangeName = 'user_auth';
    $routingKey = 'user_management';

    $data = json_decode(file_get_contents('php://input'), true);

    $make = $data['make'] ?? null;
    $model = $data['model'] ?? null;
    $year = $data['year'] ?? null;

    $registrationData = [
        'type' => 'register_car',
        'make' => $make,
        'model' => $model,
        'year' => $year,
        'username' => $_COOKIE['username'] ?? null,
        'on_sale' => false, 
    ];

  $response = $client->send_request($registrationData, $exchangeName, $routingKey);

  if ($response && $response['message'] == "Car registration successful.") {
  
    echo json_encode(['success' => $response]);

  } else {
      echo json_encode(['success' => $response]);
  }

  exit();
}

require_once 'logError.php';

?>

<html>
  <head>
    <title>Vehicle Registration</title>
    <!-- <link href="css/header.css" rel="stylesheet"> -->
    <link href="css/registerVehicle.css" rel="stylesheet"> 
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>
  <body>

    <?php include 'header.php'; ?>

    <!-- <div class="registration-container">
        <div class="registration-box">
            <h1 class="Welcome">Register Your Vehicle</h1>
            <form id="registrationForm" method="post" onsubmit="return false;">
                <div class="form-group">
                    <label for="make">Make:</label>
                    <input type="text" id="mk" name="make">
                </div>
                <div class="form-group">
                    <label for="model">Model:</label>
                    <input type="text" id="md" name="model">
                </div>
                <div class="form-group">
                    <label for="year">Year:</label>
                    <input type="text" id="yr" name="year">
                </div>
                <button type="button" class="register-button" onclick="SendRegistrationRequest()">Register</button>
                <input type="hidden" name="register" value="1">
            </form>
        </div>
        <div id="registrationResponse" class="response-message"></div>
    </div> -->

    <div class="h-screen flex items-center justify-center">
        <form id="registrationForm" method="post" onsubmit="return false;" class="p-5 w-full md:w-80 lg:w-96 md:shadow-md md:rounded">
            <h4 class="font-bold capitalize text-center text-lg">
                Register Your Vehicle
            </h4>
            <div class="mt-1 border"></div>
            <div class="mt-5 mb-3">
                <label class="font-bold" for="mk">Make:</label>
                <input class="mt-2 p-1 block w-full border rounded" type="text" name="make" id="mk" />
            </div>
            <div class="mb-3">
                <label class="font-bold" for="md">Model:</label>
                <input class="mt-2 p-1 block w-full border rounded" type="text" name="model" id="md" />
            </div>
            <div class="mb-3">
                <label class="font-bold" for="yr">Year</label>
                <input class="mt-2 p-1 block w-full border rounded" type="text" name="year" id="yr" />
            </div>
            <div>
                <button type="submit" class="register-button p-1 block w-full bg-blue-500 hover:bg-blue-600 transition ease-in-out duration-200 text-white text-xs rounded" onclick="SendRegistrationRequest()">
                    Register
                </button>
                <input type="hidden" name="register" value="1">
            </div>
            <div id="registrationResponse" class="response-message mt-8 p-1 font-bold text-xs bg-gray-200 uppercase text-center"></div>
        </form>
    </div>
    
    <script>
      function SendRegistrationRequest() {
          const make = document.getElementById("mk").value;
          const model = document.getElementById("md").value;
          const year = document.getElementById("yr").value;

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
          .then(response => response.json())
          .then(data => {
              console.log(data.success.message);
              if (data.success) {
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

<?php include 'responsiveNavScript.php'; ?>
  </body>
</html>