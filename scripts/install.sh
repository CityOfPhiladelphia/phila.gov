#!/bin/bash

export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get install -y mysql-server-5.6 nginx php5-cli php5-curl php5-fpm php5-gd php5-mysql unzip

echo "Installing composer"
curl -sS https://getcomposer.org/download/1.0.0-alpha10/composer.phar > /usr/local/bin/composer
chmod 755 /usr/local/bin/composer

echo "Installing wp-cli"
curl -sS https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar > /usr/local/bin/wp
chmod 755 /usr/local/bin/wp

echo 'Installing AWS CLI'
cd /tmp
wget https://s3.amazonaws.com/aws-cli/awscli-bundle.zip
unzip -o awscli-bundle.zip
./awscli-bundle/install -i /usr/local/aws -b /usr/local/bin/aws
cd -

echo "Configuring AWS CLI"
mkdir -p ~/.aws
cat > ~/.aws/config <<EOF
[default]
aws_access_key_id = $AWS_ID
aws_secret_access_key = $AWS_SECRET
output = text
region = us-east-1
EOF

echo "Generating SSL certificate"
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/self-signed.key \
  -out /etc/ssl/certs/self-signed.crt \
  -subj "/C=US/ST=Pennsylvania/L=Philadelphia/O=City of Philadelphia/OU=Office of Innovation and Technology/CN=$JOIA_HOSTNAME"

`dirname "$0"`/db.sh
