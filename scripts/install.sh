#!/bin/bash

export DEBIAN_FRONTEND=noninteractive
sudo apt-get update
sudo -E apt-get install -y nginx php5-cli php5-curl php5-fpm php5-gd php5-mysql

echo "Installing wp-cli"
curl -sS https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar | sudo tee /usr/local/bin/wp > /dev/null
sudo chmod 755 /usr/local/bin/wp
