#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require('car_api.php');



function requestProcessor($request)
{
  $carApi = new CarApiFunctions();
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return array("returnCode" => '400', 'message'=>"ERROR: unsupported message type");
  }
  switch ($request['type'])
  {
    case "getYearsByMake":
      $response = $carApi->getYearsByMake($make);
      if ($response !== null) {
        return array('returnCode' => '200', 'response' => $response);
      } else {
        return array('returnCode' => '500', 'message' => 'Internal Server Error');
      }
      case "getMakes":
        $response = $carApi->getMakes(
            $request['page'] ?? null,
            $request['limit'] ?? null,
            $request['sort'] ?? null,
            $request['direction'] ?? null,
            $request['make'] ?? null,
            $request['year'] ?? null
        );
        if ($response['error']!=null){
          return array('returnCode' => '500', 'message' => $response['error']);
        }


        $dataresponse = $response['data'] ?? null;
        

        if ($dataresponse !== null) {
            return array('returnCode' => '200', 'response' => $dataresponse);
        } else {
            return array('returnCode' => '500', 'message' => 'Internal Server Error');
        }

    case "validate_session":
      $isValid = doValidate($request['sessionId']);
      if ($isValid) {
        return array('returnCode' => '200', 'message' => 'Session is valid');
      } else {
        return array('returnCode' => '403', 'message' => 'Session is invalid');
      }
    default:
      return array("returnCode" => '404', 'message'=>"Request type not found");
  }
}


$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

