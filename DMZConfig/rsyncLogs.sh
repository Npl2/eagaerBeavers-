#!/bin/bash

sync_logs() {
    local ssh_command="$1"
    local source_file="$2"
    local destination="$3"

    rsync -avz -e "$ssh_command" "$source_file" "$destination"
}

ssh_command="ssh -i /home/dmz/.ssh/dmzErrorLog"

source_file="/var/log/syslog"

destination1="npl2@backendPROD:/home/npl2/errorLogs/dmzErrorLogs.log"
destination2="webserver@frontendPROD:/home/webserver/errorLogs/dmzErrorLogs.log"

while true; do
    sync_logs "$ssh_command" "$source_file" "$destination1"
    sync_logs "$ssh_command" "$source_file" "$destination2"
    sleep 30
done

