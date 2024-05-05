#!/bin/bash

function parse_ini {
    awk -F "=" '/[.*]/{gsub(/[|]/,"",$1); a=$1} NF>1{print a,$1,$2}' "$1"
}

declare -A reference_ini
declare -A target_ini

parse_ini '/home/webserver/ipAddress.ini'

while IFS= read -r line; do
    section=$(echo "$line" | awk '{print $1}')
    key=$(echo "$line" | awk '{print $2}')
    value=$(echo "$line" | awk '{print $3}')

    reference_ini["$section:$key"]="$value"
done < <(parse_ini '/home/webserver/ipAddress.ini')

parse_ini '/home/webserver/IT-490/eagaerBeavers-/testRabbitMQ.ini'

while IFS= read -r line; do
    section=$(echo "$line" | awk '{print $1}')
    key=$(echo "$line" | awk '{print $2}')
    value=$(echo "$line" | awk '{print $3}')

    target_ini["$section:$key"]="$value"
done < <(parse_ini '/home/webserver/IT-490/eagaerBeavers-/testRabbitMQ.ini')

for key in "${!reference_ini[@]}"; do
    if [[ ! "${target_ini[$key]}" ]]; then
        target_ini["$key"]="${reference_ini[$key]}"
    fi
done


updated_ini_content=""
for key in "${!target_ini[@]}"; do
    section=$(echo "$key" | awk -F ':' '{print $1}')
    key=$(echo "$key" | awk -F ':' '{print $2}')
    value="${target_ini[$section:$key]}"
    updated_ini_content+="$section $key=$value"$'\n'
done

echo "$updated_ini_content" > target.ini

echo "INI files compared and updated."