#!/bin/bash

export DEBIAN_FRONTEND=noninteractive

sudo apt-get update
sudo apt-get -yq upgrade

sudo rm -rf /home/ubuntu/app/wp/wp-content/themes/phila.gov-theme/
sudo rm -rf /home/ubuntu/app/wp/wp-content/plugins/

source /home/ubuntu/.bashrc