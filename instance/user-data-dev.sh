#!/bin/bash

set -e

export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get install -y mysql-server-5.6 nginx php5-cli php5-curl php5-fpm php5-gd php5-mysql

echo 'Installing composer'
curl -sS https://getcomposer.org/download/1.0.0-alpha10/composer.phar > /usr/local/bin/composer
chmod 755 /usr/local/bin/composer

echo 'Installing wp-cli'
curl -sS https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar > /usr/local/bin/wp
chmod 755 /usr/local/bin/wp

echo 'Setting env vars for ssh deploys'
echo 'PermitUserEnvironment yes' >> /etc/ssh/sshd_config
service ssh restart
