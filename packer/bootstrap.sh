#!/bin/bash

set -e

apt-get update
apt-get install -y nginx php5-cli php5-curl php5-fpm php5-mysql

echo 'Modifying php.ini...'
ed /etc/php5/fpm/php.ini <<'EOF'
g/post_max_size/s/8/100
g/upload_max_filesize/s/2/100
g/expose_php/s/On/Off
w
EOF

echo 'Installing composer...'
curl -sS https://getcomposer.org/download/1.0.0-alpha10/composer.phar > /usr/local/bin/composer
chmod 755 /usr/local/bin/composer

echo 'Installing wp-cli...'
curl -sS https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar > /usr/local/bin/wp
chmod 755 /usr/local/bin/wp

echo 'Enabling env vars in ssh deploys...'
echo 'PermitUserEnvironment yes' >> /etc/ssh/sshd_config
service ssh restart
