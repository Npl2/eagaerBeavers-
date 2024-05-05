#!/bin/bash

sync_logs() {
    local ssh_command="$1"
    local source_file="$2"
    local destination="$3"

    rsync -avz -e "$ssh_command" "$source_file" "$destination"
}

ssh_command="ssh -i /home/webserver/.ssh/frontendErrorLog"

source_file="/var/log/apache2/sample_error.log"

destination1="npl2@backendQA:/home/npl2/errorLogs/frontendErrorLogs.log"
destination2="dmz@dmzQA:/home/dmz/errorLogs/frontendErrorLogs.log"

while true; do
    sync_logs "$ssh_command" "$source_file" "$destination1"
    sync_logs "$ssh_command" "$source_file" "$destination2"
    sleep 30
done

