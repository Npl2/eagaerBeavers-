#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('mongoClient.php');

function doLogin($username, $password) {
  $mongoClientDB = new MongoClientDB();
  $user = $mongoClientDB->findUserByUsername($username);

  if ($user !== null && password_verify($password, $user['password'])) {
      echo "Login successful";
      return "Login successful";
  } else {
      echo "Login failed";
      //http_response_code(500);
      return "Login failed";
  }
}

function insertUser($username, $password){
  $mongoClientDB = new MongoClientDB();
  if (!$mongoClientDB->isDatabaseConnected()) {
      echo "Failed to connect to the database.";
      return false;
  }
  $result = $mongoClientDB->insertUser($username, $password);
  
  if ($result['success']) {
      echo "User successfully inserted.";
      
      return true;
  } else {
      echo "Failed to insert user: " . $result['message'];
      // http_response_code(500);
      return false;
  }
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return array("returnCode" => 1 ,'message' => doLogin($request['username'],$request['password']));
    case "signup":
      return insertUser($request['username'], $request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

