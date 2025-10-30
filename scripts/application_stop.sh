#!/bin/bash
# Stop and remove the container if it exists
if [ $(docker ps -a -q --filter="name=php-container") ]; then
    docker stop php-container
    docker rm php-container
fi
