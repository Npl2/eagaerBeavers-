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

?>

<html>
  <head>
    <title>Vehicle Registration</title>
    <link href="css/header.css" rel="stylesheet">
    <link href="css/registerVehicle.css" rel="stylesheet"> 
  </head>
  <body>

    <?php include 'header.php'; ?>

    <div class="registration-container">
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

  </body>
</html>