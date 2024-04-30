<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Source directory containing the files to be zipped
$sourceDir = '/home/npl2/IT-490';
// Destination zip file path (including the name of the new zip file to create)
$zipFilePath = '/home/npl2/zip/IT-490.zip';

$zip = new ZipArchive();
// Attempt to open the zip file, create it if it doesn't exist
if ($zip->open($zipFilePath, ZipArchive::CREATE) !== TRUE) {
    exit("Cannot open <$zipFilePath>\n");
}

// Add files to the zip file
$iterator = new DirectoryIterator($sourceDir);
foreach ($iterator as $fileinfo) {
    if ($fileinfo->isFile()) {
        $zip->addFile($fileinfo->getPathname(), $fileinfo->getFilename());
    }
}
// Close the zip archive
$zip->close();
echo "Zip file created successfully at $zipFilePath.\n";

// SSH details for transferring the zip file
$sshHost = '172.28.212.226'; // Remote host IP
$sshPort = 22; // SSH port number
$sshUsername = 'npl2'; // SSH username
$sshPassword = 'Creative@Kirby23'; // SSH password
$remoteDir = '/home/npl2/Deploy'; // Remote directory to transfer the zip file

// Establish an SSH connection and authenticate
$connection = ssh2_connect($sshHost, $sshPort);
if (!$connection || !ssh2_auth_password($connection, $sshUsername, $sshPassword)) {
    exit('SSH Connection or Authentication Failed...');
}

// Send the zip file via SCP to the remote directory
if (ssh2_scp_send($connection, $zipFilePath, $remoteDir . '/IT-490.zip')) {
    echo "Zip file transferred successfully.\n";
} else {
    echo "Failed to transfer zip file.\n";
}

// Close the SSH connection
ssh2_exec($connection, 'exit');
?>
