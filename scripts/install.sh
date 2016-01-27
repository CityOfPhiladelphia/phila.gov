#!/bin/bash

export DEBIAN_FRONTEND=noninteractive
sudo add-apt-repository -y ppa:ondrej/php
sudo apt-get update
sudo -E apt-get install -y jq nginx php7.0-cli php7.0-curl php7.0-fpm php7.0-gd php7.0-mysql

echo "Installing wp-cli"
curl -sS https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar | sudo tee /usr/local/bin/wp > /dev/null
sudo chmod 755 /usr/local/bin/wp

echo 'Copying .env to ~/.ssh/environment'
cp .env ~/.ssh/environment

echo 'Permitting user env vars for ssh deploys'
echo 'PermitUserEnvironment yes' | sudo tee -a /etc/ssh/sshd_config > /dev/null
sudo service ssh restart
