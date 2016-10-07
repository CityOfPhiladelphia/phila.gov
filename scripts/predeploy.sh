#!/bin/bash

/usr/local/bin/aws s3 cp s3://phila-prod-environment/environment /home/ubuntu/.ssh/environment
sudo apt-get update
sudo apt-get upgrade -y
