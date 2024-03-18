#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('mongoClient.php');
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

function setupMessaging($channel) {
    $exchangeName = 'user_auth';
    $queueNameUser = 'testQueue';
    $routingKeyUser = 'user_management';

    // Declare the exchange
    $channel->exchange_declare($exchangeName, 'direct', false, true, false);

    // Declare and bind the user management queue
    $channel->queue_declare($queueNameUser, false, true, false, false);
    $channel->queue_bind($queueNameUser, $exchangeName, $routingKeyUser);
}


/*
function createQueues(){
  $connection = new AMQPStreamConnection('172.28.222.209', 5672, 'test', 'test', 'testHost');
  $channel = $connection->channel();

  $channel->queue_declare('frontend_login_queue', false, true, false, false);

  $channel->close();
  $connection->close();
}
*/
/*
function connectRabbitMQ(){
  return new AMQPStreamConnection('172.28.222.209', 5672, 'test', 'test', 'testHost');
}
*/
function doLogin($username, $password) {
  $mongoClientDB = new MongoClientDB();
  $user = $mongoClientDB->findUserByUsername($username);

  if ($user !== null && password_verify($password, $user['password'])) {
      echo "Login successful";
      return true;
  } else {
      echo "Login failed";
      //http_response_code(500);
      return false;
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

  /* new code
  $connection = connectRabbitMQ();
  $channel = $connection->channel();
  $channel->queue_decalre('frontend_login_queue', false, true, false, false);
  */

  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return array('message' => doLogin($request['username'],$request['password']));
    case "signup":
      return array('message' => insertUser($request['username'],$request['password']));
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$connection = new AMQPStreamConnection('172.28.222.209', 5672, 'test', 'test', 'testHost');
$channel = $connection->channel();

setupMessaging($channel);

echo "User Management Server ready to receive messages".PHP_EOL;

$callback = function($msg) use ($channel) {
  $request = json_decode($msg->body, true);
  $response = requestProcessor($request);
  
  // Prepare the response message
  $responseMsg = new AMQPMessage(
      json_encode($response),
      array('correlation_id' => $msg->get('correlation_id'))
  );
  
  // Publish the response message to the queue specified in the reply_to header
  $channel->basic_publish(
      $responseMsg, 
      '', 
      $msg->get('reply_to')
  );

  echo 'Processed request and sent response: ', json_encode($response), "\n";
};

$channel->basic_consume('testQueue', '', false, true, false, false, $callback);

while($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>

