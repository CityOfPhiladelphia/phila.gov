#!/bin/bash

set -e

export DEBIAN_FRONTEND=noninteractive
apt-get update

apt-get install -y nginx php5-cli php5-curl php5-fpm php5-mysql

echo 'Configuring nginx...'
cat > /etc/nginx/nginx.conf << 'EOF'
user www-data;
worker_processes 4;
pid /run/nginx.pid;

events {
  worker_connections 768;
}

http {
  sendfile on;
  tcp_nopush on;
  tcp_nodelay on;
  keepalive_timeout 65;
  types_hash_max_size 2048;
  gzip on;

  include /etc/nginx/mime.types;
  default_type application/octet-stream;

  access_log /var/log/nginx/access.log;
  error_log /var/log/nginx/error.log;

  server {
    location /property/ {
      proxy_set_header Host cityofphiladelphia.github.io;
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
      proxy_pass https://cityofphiladelphia.github.io/property2/;
    }
  }
}
EOF

echo 'Installing composer...'
curl -sS https://getcomposer.org/download/1.0.0-alpha10/composer.phar > /usr/local/bin/composer
chmod 755 /usr/local/bin/composer

echo 'Installing wp-cli...'
curl -sS https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar > /usr/local/bin/wp
chmod 755 /usr/local/bin/wp
