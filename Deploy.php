<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

function addFilesToZip($zip, $dir, $baseDir = '') {
    $dirHandle = opendir($dir);
    if ($dirHandle) {
        while (($file = readdir($dirHandle)) !== false) {
            if ($file != '.' && $file != '..') {
                if (file_exists("$dir/$file")) {
                    // Check if it's a directory or a file
                    if (is_dir("$dir/$file")) {
                        // Recursively add directories
                        addFilesToZip($zip, "$dir/$file", "$baseDir$file/");
                    } else {
                        // Add files
                        $zip->addFile("$dir/$file", $baseDir.$file);
                    }
                }
            }
        }
        closedir($dirHandle);
    }
}

function getVersionNumber() {
    $versionFile = '/home/npl2/version.txt';
    $version = 1;
    if (file_exists($versionFile)) {
        $version = (int)file_get_contents($versionFile) + 1;
    }
    file_put_contents($versionFile, $version);
    return $version;
}


// Path to the directory you want to zip
$directoryPath = '/home/npl2/IT-490';

// Get current version number
$version = getVersionNumber();

// Path for the resulting zip file with versioning
$zipFilePath = "/home/npl2/zip/IT-490 Version $version.zip";

$zip = new ZipArchive();
// Attempt to open the zip file, create it if it doesn't exist
if ($zip->open($zipFilePath, ZipArchive::CREATE) !== TRUE) {
    exit("Cannot open <$zipFilePath>\n");
}

// Add files to the zip file
addFilesToZip($zip, $directoryPath);

// Close the zip file
if ($zip->close()) {
    echo "Zip file created successfully as IT-490 Version $version.zip.\n";
} else {
    echo "Failed to create zip file.\n";
}

// SSH details for transferring the zip file
do {
    // Requesting input from the user for SSH details
    echo "Enter SSH Host IP: ";
    $sshHost = trim(fgets(STDIN)); // Remote host IP

    echo "Enter SSH Port (default 22): ";
    $sshPortInput = trim(fgets(STDIN));
    $sshPort = !empty($sshPortInput) ? $sshPortInput : 22; // SSH port number, defaults to 22 if empty

    echo "Enter SSH Username: ";
    $sshUsername = trim(fgets(STDIN)); // SSH username

    echo "Enter SSH Password: ";
    $sshPassword = trim(fgets(STDIN)); // SSH password

    // The remote directory remains constant
    $remoteDir = '/home/npl2/Deploy';

    // Display entered information (excluding password for security reasons)
    echo "You have entered:\n";
    echo "SSH Host IP: $sshHost\n";
    echo "SSH Port: $sshPort\n";
    echo "SSH Username: $sshUsername\n";
    echo "Remote directory: $remoteDir\n";

    echo "Is this information correct? (yes/no): ";
    $confirmation = trim(fgets(STDIN));

} while (strtolower($confirmation) !== 'yes');

// Establish an SSH connection and authenticate
$connection = ssh2_connect($sshHost, $sshPort);
if (!$connection || !ssh2_auth_password($connection, $sshUsername, $sshPassword)) {
    exit('SSH Connection or Authentication Failed...');
}

// Add further code to proceed with operations after successful confirmation
echo "SSH Connection established successfully.\n";

// Check if remote directory exists, create it if it does not
$stream = ssh2_exec($connection, "mkdir -p $remoteDir");
stream_set_blocking($stream, true); // Set blocking to ensure command completes
if (stream_get_contents($stream) === false) {
    echo "Failed to create remote directory or it already exists.\n";
} else {
    echo "Remote directory verified or created.\n";
}
fclose($stream);

if (ssh2_scp_send($connection, $zipFilePath, $remoteDir . "/IT-490 Version $version.zip")) {
    echo "Zip file transferred successfully as Version $version.\n";
    unzipFile($connection, $zipFilePath, $remoteDir, $version);
} else {
    echo "Failed to transfer zip file.\n";
}
function unzipFile($connection, $zipFilePath, $remoteDir, $version) {
    // Prompting the local user for the directory where the file should be unzipped
    echo "Enter the full path of the directory where you want to unzip the file: ";
    $unzipDestination = trim(fgets(STDIN));

    // Preparing and executing the unzip command on the remote server
    $unzipCommand = "unzip -o " . escapeshellarg($remoteDir . "/IT-490 Version $version.zip") . " -d " . escapeshellarg($unzipDestination);
    $unzipStream = ssh2_exec($connection, $unzipCommand);
    stream_set_blocking($unzipStream, true);
    
    if (stream_get_contents($unzipStream) !== false) {
        echo "Zip file unzipped successfully in " . $unzipDestination . ".\n";
        
        // Directory and file pattern for checking renaming
        $directory = escapeshellarg($unzipDestination);
        $filenamePattern = "eagaerBeavers-*"; // Adjust based on how files are named before being renamed
    
        // Polling interval and max attempts
        $pollInterval = 10; // seconds
        $maxAttempts = 60; // This gives 10 minutes to wait
    
        $fileFound = false;
        $attempt = 0;
    
        while (!$fileFound && $attempt < $maxAttempts) {
            // SSH command to check if the file has been renamed to include 'pass' or 'fail'
            $checkCommand = "ls $directory/$filenamePattern 2>/dev/null";
            $checkStream = ssh2_exec($connection, $checkCommand);
            stream_set_blocking($checkStream, true);
            $result = stream_get_contents($checkStream);
            fclose($checkStream);
    
            if (strpos($result, 'pass') !== false) {
                echo "The package has been successfully verified as a pass by the remote machine.\n";
                $fileFound = true;
            } elseif (strpos($result, 'fail') !== false) {
                echo "The package has failed verification by the remote machine.\n";
                $fileFound = true;
            } else {
                echo "File not yet renamed, checking again in $pollInterval seconds.\n";
                sleep($pollInterval);
                $attempt++;
            }
        }
    
        if (!$fileFound) {
            echo "Verification process exceeded the time limit without a pass or fail confirmation.\n";
        }
    } else {
        echo "Failed to unzip zip file at the specified location.\n";
    }
    
    // Always ensure streams are closed to free resources
    if (isset($unzipStream)) {
        fclose($unzipStream);
    }
    
    // Disconnect from the remote server
    ssh2_disconnect($connection);
    echo "SSH connection closed.\n";
}
?>
