#!/bin/bash

/usr/local/bin/aws s3 cp s3://phila-prod-environment/environment /home/ubuntu/.ssh/environment

export DEBIAN_FRONTEND=noninteractive

sudo apt-get update
sudo apt-get -y -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" dist-upgrade
