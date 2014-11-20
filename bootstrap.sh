#!/bin/bash

export DEBIAN_FRONTEND=noninteractive
apt-get update
# Don't apt-get upgrade http://stackoverflow.com/a/15093460/589391
apt-get install -y mysql-server-5.6 nginx php5-cli php5-fpm

echo 'Configuring mysql...'
mysql -uroot -e "CREATE DATABASE wp"
# mysql -uroot -e "GRANT ALL PRIVILEGES ON wp.* TO username@hostname IDENTIFIED BY 'userpassword'"

echo 'Configuring nginx...'
cat > /etc/nginx/sites-available/default << 'EOF'
server {
    listen 80;

    root /vagrant;
    index index.php index.html index.htm;

    server_name localhost;

    location / {
        try_files $uri $uri/ /index.php?q=$uri&$args;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }

    location = /favicon.ico {
        log_not_found off;
        access_log off;
    }

    location = /robots.txt {
        log_not_found off;
        access_log off;
    }
}
EOF
/etc/init.d/nginx restart

# TODO cd to static files directory and
# python -m SimpleHTTPServer
