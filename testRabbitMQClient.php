#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}

$login = array();
$login['type'] = "login";
$login['username'] = "NEWHARSH_123";
$login['password'] = "1212121";
// $request['email'] = "kerlos12@gmail.com";
// $request['firstName'] = 'kerlos';
// $request['lastName'] = 'Awadalla';

$response = $client->send_request($login);
echo $response;
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
if ($response['message'] ==  true){
  echo("Sucessfull");
}
else{
  echo("Request Failed");
}
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

