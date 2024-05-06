<?php

$new_ip_address = '172.28.222.207';

$config_file = '/home/webserver/IT-490/eagaerBeavers-/testRabbitMQ.ini';
$lines = file($config_file);

foreach ($lines as &$line) {
    // Check if the line contains BROKER_HOST
    if (strpos($line, 'BROKER_HOST') !== false) {
        $line = preg_replace('/BROKER_HOST\s=\s\K[^;]+/', $new_ip_address, $line);

        $line .= "\n";
    }
}

file_put_contents($config_file, implode('', $lines));

echo "BROKER_HOST values updated successfully.";

?>
