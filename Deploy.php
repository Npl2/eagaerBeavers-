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
function listAvailableVersions($directoryPath) {
    $zipFiles = [];
    $dir = new DirectoryIterator($directoryPath);

    foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot() && $fileinfo->getExtension() === 'zip') {
            $zipFiles[] = $fileinfo->getFilename();
        }
    }

    if (count($zipFiles) === 0) {
        echo "No zip files found in the directory.\n";
        return [];
    }

    echo "Available Versions:\n";
    foreach ($zipFiles as $index => $file) {
        echo ($index + 1) . ". $file\n";
    }

    return $zipFiles;
}
function getUserVersionChoice($zipFiles) {
    echo "Enter the number of the version you wish to restore: ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $choice = trim($line);
    fclose($handle);

    if (!is_numeric($choice) || $choice < 1 || $choice > count($zipFiles)) {
        echo "Invalid selection. Please run the process again.\n";
        exit;
    }

    return $zipFiles[$choice - 1];
}

function unzipOnRemote($connection, $remoteFilePath, $destination) {
    $unzipCommand = "unzip -o " . escapeshellarg($remoteFilePath) . " -d " . escapeshellarg($destination);
    $unzipStream = ssh2_exec($connection, $unzipCommand);
    stream_set_blocking($unzipStream, true);
    if (stream_get_contents($unzipStream) === false) {
        echo "Failed to unzip file on remote server.\n";
    } else {
        echo "File unzipped successfully on remote server.\n";
    }
    fclose($unzipStream);
}


function handleFailure($connection, $unzipDestination, $remoteDir) {
    echo "Failure detected. Starting recovery process...\n";

    // List available versions for user to choose
    $zipFiles = listAvailableVersions("/home/webserver/zip");
    if (count($zipFiles) > 0) {
        $selectedFile = getUserVersionChoice($zipFiles);
        $selectedFilePath = "/home/webserver/zip/" . $selectedFile;

        // Deleting contents of the destination before restoring
        echo "Deleting contents of the destination...\n";
        deleteRemoteDirectoryContents($connection, $unzipDestination, $remoteDir);

        // Restoring selected version
        if (ssh2_scp_send($connection, $selectedFilePath, $remoteDir . "/" . basename($selectedFilePath))) {
            unzipOnRemote($connection, $remoteDir . "/" . basename($selectedFilePath), $unzipDestination);
            echo "Version restored successfully.\n";

        } else {
            echo "Failed to restore the selected version.\n";
        }
    }
}


function deleteRemoteDirectoryContents($connection, $remoteDir) {
    $safeRemoteDir = escapeshellarg($remoteDir);
    $deleteCommand = "find $safeRemoteDir -mindepth 1 -delete";
    $deleteStream = ssh2_exec($connection, $deleteCommand);
    stream_set_blocking($deleteStream, true);

    // Fetching error output
    $errorStream = ssh2_fetch_stream($deleteStream, SSH2_STREAM_STDERR);
    stream_set_blocking($errorStream, true);
    $errorOutput = stream_get_contents($errorStream);

    if ($errorOutput) {
        echo "Failed to delete contents of the directory on the remote server: $errorOutput\n";
    } else {
        echo "Contents deleted successfully.\n";
    }
}



$versionFile = '/home/webserver/currentVersion.txt';
$currentVersion = 1;  

// Check if the version file exists and read the current version from it
if (file_exists($versionFile)) {
    $fileContent = file_get_contents($versionFile);
    if ($fileContent !== false) {
        $currentVersion = (int)$fileContent + 1;  // Increment the version number
    }
}


file_put_contents($versionFile, $currentVersion);

echo "Current Version: $currentVersion\n";

function unzipFile($connection, $zipFilePath, $remoteDir, $currentVersion) {
    // Prompting the local user for the directory where the file should be unzipped
    echo "Enter the full path of the directory where you want to unzip the file: ";
    $unzipDestination = trim(fgets(STDIN));
    $remoteDirPath = $unzipDestination; 

    // Preparing and executing the unzip command on the remote server
    $unzipCommand = "unzip -o " . escapeshellarg($remoteDir . "/IT-490 Version $currentVersion.zip") . " -d " . escapeshellarg($unzipDestination);
    $unzipStream = ssh2_exec($connection, $unzipCommand);
    stream_set_blocking($unzipStream, true);
    
    if (stream_get_contents($unzipStream) !== false) {
        echo "Zip file unzipped successfully in " . $unzipDestination . ".\n";
        
        // Directory and file pattern for checking renaming
        $directory = escapeshellarg($unzipDestination);
        $filenamePattern = "/eagaerBeavers"; // Adjust based on how files are named before being renamed
    
        // Polling interval and max attempts
        $pollInterval = 10; // seconds
        $maxAttempts = 60; // This gives 10 minutes to wait
    
        $fileFound = false;
        $attempt = 0;
    
        while (!$fileFound && $attempt < $maxAttempts) {
            // SSH command to check if the file or directory has been renamed to include 'pass' or 'fail'
            $checkCommand = "find $directory -type f -or -type d";
            $checkStream = ssh2_exec($connection, $checkCommand);
            stream_set_blocking($checkStream, true);
            $result = stream_get_contents($checkStream);
            fclose($checkStream);
        
            if (strpos($result, 'pass') !== false) {
                echo "The package or directory has been successfully verified as a pass by the remote machine.\n";
                $fileFound = true;
            } elseif (strpos($result, 'fail') !== false) {
                echo "The package or directory has failed verification by the remote machine, rolling back.\n";
        
                // Extract directory name
                preg_match("/\d+\s+\d+\s+\d+\s+\d+:\d+\s+([^\s]+)/", $result, $matches);
                if (!empty($matches)) {
                    $failedItem = $matches[1];  // Name of the file or directory that contains 'fail'
                    if (is_dir("$directory/$failedItem")) {
                        // Delete directory if it's a directory
                        $deleteCommand = "rm -rf '$directory/$failedItem'";
                        $deleteStream = ssh2_exec($connection, $deleteCommand);
                        stream_set_blocking($deleteStream, true);
                        if (ssh2_fetch_stream($deleteStream, SSH2_STREAM_STDERR)) {
                            echo "Error deleting directory: $directory/$failedItem\n";
                        } else {
                            echo "Directory deleted successfully: $directory/$failedItem\n";
                        }
                        fclose($deleteStream);
                    }
                }
                // Directly call the failure handling function when "fail" is detected
                handleFailure($connection, $unzipDestination, $remoteDir);
            } else {
                echo "No relevant file or directory changes yet, checking again in $pollInterval seconds.\n";
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



// Path to the directory you want to zip
echo "Enter the full path of the source directory: ";
$directoryPath = trim(fgets(STDIN));

// Path for the resulting zip file with versioning
$zipFilePath = "/home/webserver/zip/IT-490 Version $currentVersion.zip";  // Use $currentVersion for the zip file path

$zip = new ZipArchive();
// Attempt to open the zip file, create it if it doesn't exist
if ($zip->open($zipFilePath, ZipArchive::CREATE) !== TRUE) {
    exit("Cannot open <$zipFilePath>\n");
}

// Add files to the zip file
addFilesToZip($zip, $directoryPath);

// Close the zip file
if ($zip->close()) {
    echo "Zip file created successfully as IT-490 Version $currentVersion.zip.\n";  // Use $currentVersion in the message
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
    $remoteDir = '/home/webserver/Deploy';
    

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

if (ssh2_scp_send($connection, $zipFilePath, $remoteDir . "/IT-490 Version $currentVersion.zip")) {
    echo "Zip file transferred successfully as Version $currentVersion.\n";
    unzipFile($connection, $zipFilePath, $remoteDir, $currentVersion);
} else {
    echo "Failed to transfer zip file.\n";
}


?>
