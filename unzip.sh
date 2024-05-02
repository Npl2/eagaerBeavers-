# This could be a shell script named unzip_script.sh
# Make sure this script is called after the file is received successfully

#!/bin/bash
ZIP_FILE="/path/to/destination/directory/IT-490 Version X.zip" # Adjust the version or pass it dynamically
DESTINATION_DIR="/path/to/unzip/directory"

unzip $ZIP_FILE -d $DESTINATION_DIR
echo "Unzip successful."
