#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require('car_api.php');

include '/home/dmz/ip.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

function setupMessaging($channel) {
    $exchangeName = 'car_api_exchange';
    $queueName = 'car_api_queue';
    $routingKey = 'car_request';

    $channel->exchange_declare($exchangeName, 'direct', false, true, false);

    $channel->queue_declare($queueName, false, true, false, false);
    $channel->queue_bind($queueName, $exchangeName, $routingKey);
}

function requestProcessor($request, $channel, $replyQueue, $correlationId) {
    $carApi = new CarApiFunctions();
    echo "received request".PHP_EOL;
    var_dump($request);

    if(!isset($request['type'])) {
        $response = array("returnCode" => '400', 'message'=>"ERROR: unsupported message type");
    } else {
        switch ($request['type'])
        {
          case "getYearsByMake":
            $response = $carApi->getYearsByMake($request['make']);
            if ($response !== null) {
              
              return array('returnCode' => '200', 'response' => $response);
            } else {
              return array('returnCode' => '500', 'message' => 'Internal Server Error');
            }
            break;
            case "getMakes":
              $response = $carApi->getMakes(
                  $request['page'] ?? null,
                  $request['limit'] ?? null,
                  $request['sort'] ?? null,
                  $request['direction'] ?? null,
                  $request['make'] ?? null,
                  $request['year'] ?? null
              );
              if ($response !== null) {
              
                return array('returnCode' => '200', 'response' => $response['data']);
              } else {
                return array('returnCode' => '500', 'message' => 'Internal Server Error');
              }
              case "getRecall":
                $response = $carApi->getRecall($request['make'], $request['model'], $request['year']);
                return $response !== null ? array('returnCode' => '200', 'response' => $response) : array('returnCode' => '500', 'message' => 'Internal Server Error'); 
              break;
            default:
                $response = array("returnCode" => '404', 'message'=>"Request type not found");
        }
    }
}

$connection = new AMQPStreamConnection($rabbitmq, 5672, 'test', 'test', 'testHost');
$channel = $connection->channel();

setupMessaging($channel);

echo "Car API Server ready to receive messages".PHP_EOL;

$callback = function($msg) use ($channel) {
    $request = json_decode($msg->body, true);
    $replyQueue = $msg->get('reply_to');
    $correlationId = $msg->get('correlation_id');

    $response = requestProcessor($request, $channel, $replyQueue, $correlationId);

  $responseMsg = new AMQPMessage(
    json_encode($response),
    array('correlation_id' => $msg->get('correlation_id'))
);

$channel->basic_publish(
    $responseMsg, 
    '', 
    $msg->get('reply_to')
);

echo 'Processed request and sent response: ', json_encode($response), "\n";
};

$channel->basic_consume('car_api_queue', '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>
