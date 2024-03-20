<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
// Check if the user is already logged in
if (isset($_COOKIE['username'])) {
    header('Location: forum.php');
    exit();
}
// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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









<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register Vehicle</title>
    <link rel="stylesheet" href="/car_registration.css" />
  </head>
  <body>
    <nav>
      <div class="navContainer">
        <h4><a href="index.html" id="link">EAGER_DRIVERS</a></h4>
        <ul>
          <li><a href="cars_for_sale.html" id="link">Cars for sale</a></li>
          <li><a href="car_maintenance.html" id="link">Car maintenance</a></li>
          <li><a href="vehicle_reviews.html" id="link">Vehicle reviews</a></li>
          <li><a href="login.html" id="link">Login</a></li>
          <li>
            <a href="car_registration.html" id="link">Car Registration</a>
          </li>
        </ul>
      </div>
    </nav>

    <div class="container">
      <div class="bodyWrapper">
        <main>
          <form class="formContent">
            <p style="text-align: center; font-size: 2rem; margin-bottom: 3rem">
              Register Vehicle
            </p>
            <div class="fullName">
              <div>
                <label for="firstName">First name</label>
                <input type="text" name="firstName" id="firstName" />
              </div>
              <div>
                <label for="lastName">Last name</label>
                <input type="text" name="lastName" id="lastName" />
              </div>
            </div>
            <div class="contactDetails">
              <div class="address">
                <label for="address">Address</label>
                <input type="text" name="address" id="address" />
              </div>
              <div class="contact">
                <label for="contact">Contact</label>
                <input type="text" name="contact" id="contact" />
              </div>
            </div>
            <div class="vehicleDetails">
              <div>
                <label for="vehicleMake">Make of your vehicle</label>
                <select name="vehicleMake" id="vehicleMake">
                  <option value="toyota">Toyota</option>
                  <option value="ford">Ford</option>
                  <option value="Honda">Honda</option>
                  <option value="chevrolet">Chevrolet</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div>
                <label for="vehicleModel">Model</label>
                <input type="text" name="vehicleModel" id="vehicleModel" />
              </div>
            </div>
            <div class="vehicleDateInfo">
              <div>
                <label for="vehicleYear">Year</label>
                <input type="text" name="vehicleYear" id="vehicleYear" />
              </div>
              <div>
                <label for="vin">VIN</label>
                <input type="text" name="vin" id="vin" />
              </div>
            </div>
            <div class="submitBtn">
              <input type="submit" name="submit" value="Submit" id="submit" />
            </div>
          </form>
        </main>
      </div>
      <div class="wallper"></div>
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

    document.querySelector("#submit").addEventListener("click", SendRegistrationRequest)

</script>
  </body>
</html>
