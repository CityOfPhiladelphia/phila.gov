#!/bin/bash

_dir="$(dirname "$0")"

export DEBIAN_FRONTEND=noninteractive

# For php7
sudo add-apt-repository -y ppa:ondrej/php

# For nodejs 4.x
echo 'deb https://deb.nodesource.com/node_4.x trusty main' | sudo tee /etc/apt/sources.list.d/nodesource.list > /dev/null
curl -s https://deb.nodesource.com/gpgkey/nodesource.gpg.key | sudo apt-key add -

sudo apt-get update
sudo -E apt-get install -y jq nodejs nginx php7.0-cli php7.0-curl php7.0-fpm php7.0-gd php7.0-mbstring php7.0-mysql php-xml

echo "Installing grunt"
sudo npm install -g grunt-cli

echo "Installing wp-cli"
curl -sS https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar | sudo tee /usr/local/bin/wp > /dev/null
sudo chmod 755 /usr/local/bin/wp

echo "Allowing hq access"
echo 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDde/ohFVNJyZgI3KXXt9uwipFNpu0oVFxZzeht/WAu9PRuiqOh9PUCHT+Ca/R1y70ltrO8OqGjOkmbmxBt99nvncxF8LPjQUG1rUePDOqOGNWs/d56rhLAsVM7cWXZG7xdLkJt8c5VrtXHsbRzaJ28RRwqT6C/x+ZPtx7POl/x1t8gNGeagAbbS3hq5O77ymHe4lukgcz4K5TuU8y36fH0Qp1Doe3exCDaN2DdBIz/OXYQru6vO5yRWvxMUTkxjh7GO1qsn0efWEfknrZekINNck/wP1hbvbP9xjwntyhOjxLAQrwYF6nH1iNY6J+hW/0qYrFdZqpC7dL+cmm+XVUH hq' >> ~/.ssh/authorized_keys

$_dir/wp-config.sh

if [ "$PHILA_TEST" ]; then
  $_dir/unison.sh
  $_dir/gen-cert.sh
  $_dir/local-db.sh
fi
