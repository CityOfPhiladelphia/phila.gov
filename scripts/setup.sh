#!/bin/bash

# Spin up new machine on AWS with something like the following:
# $ aws ec2 run-instances --user-data file://scripts/setup.sh --key-name philagov2 \
# > --instance-type t2.micro --associate-public-ip-address --image-id ami-d05e75b8 \
# > --subnet-id subnet-54412b0d

set -e

export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get install -y nginx php5-cli php5-curl php5-fpm php5-gd php5-mysql

echo 'Installing composer'
curl -sS https://getcomposer.org/download/1.0.0-alpha10/composer.phar > /usr/local/bin/composer
chmod 755 /usr/local/bin/composer

echo 'Installing wp-cli'
curl -sS https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar > /usr/local/bin/wp
chmod 755 /usr/local/bin/wp

echo 'Setting env vars for ssh deploys'
echo 'PermitUserEnvironment yes' >> /etc/ssh/sshd_config
service ssh restart
