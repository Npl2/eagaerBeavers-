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

function unzipFile($zipFilePath, $extractPath) {
    $zip = new ZipArchive;
    if ($zip->open($zipFilePath) === TRUE) {
        $zip->extractTo($extractPath);
        $zip->close();
        echo "Unzip successful.\n";
    } else {
        echo "Failed to unzip file.\n";
    }
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
} else {
    echo "Failed to transfer zip file.\n";
}

// Close the SSH connection
ssh2_exec($connection, 'exit');
?>
