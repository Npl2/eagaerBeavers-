<?php

// Define the new IP address you want to use
$new_ip_address = '172.28.222.208';

// Read the contents of the configuration file
$config_file = '/home/npl2/IT-490/eagaerBeavers-/testRabbitMQ.ini';
$lines = file($config_file);

// Loop through each line and replace BROKER_HOST if found
foreach ($lines as &$line) {
    // Check if the line contains BROKER_HOST
    if (strpos($line, 'BROKER_HOST') !== false) {
        // Replace the IP address with the new one
        $line = preg_replace('/BROKER_HOST\s=\s\K[^;]+/', $new_ip_address, $line);

        // Add a new line character after replacing the IP address
        $line .= "\n";
    }
}

// Write the modified content back to the file
file_put_contents($config_file, implode('', $lines));

echo "BROKER_HOST values updated successfully.";

?>
