<?php

require_once('API_test.php');


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['type'])) {
    $type = $_POST['type'];

    
    $client = new rabbitMQClient("testRabbitMQ.ini","testServer");

   
    $request = array();
    $request['type'] = "getYearsByMake";
    $request['make'] = $type;

    
    $response = $client->send_request($request);

    
    if ($response['returnCode'] == '200') {
        echo $response['response']; 
    } else {
        echo "Error ({$response['returnCode']}): {$response['message']}"; 
    }
} else {
    echo "Error: Type not provided."; 
}
?>
