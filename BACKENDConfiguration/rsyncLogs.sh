#!/bin/bash

# Define rsync command with variables
sync_logs() {
    local ssh_command="$1"
    local source_file="$2"
    local destination="$3"

    rsync -avz -e "$ssh_command" "$source_file" "$destination"
}

# Define SSH command
ssh_command="ssh -i /home/npl2/.ssh/backendErrorLog"

# Define source files and destinations
source_file_rabbit="/var/log/rabbitmq/rabbit@backendQA.log"
source_file_kern="/var/log/kern.log"
destination1="webserver@frontendQA:/home/webserver/errorLogs/"
destination2="dmz@dmzQA:/home/dmz/errorLogs/"

# Run rsync commands every 30 seconds
while true; do
    sync_logs "$ssh_command" "$source_file_rabbit" "$destination1/backendRabbitErrorLogs.log"
    sync_logs "$ssh_command" "$source_file_kern" "$destination1/backendRabbitErrorKernLogs.log"
    sync_logs "$ssh_command" "$source_file_rabbit" "$destination2/backendRabbitErrorLogs.log"
    sync_logs "$ssh_command" "$source_file_kern" "$destination2/backendRabbitErrorKernLogs.log"
    sleep 30
done

