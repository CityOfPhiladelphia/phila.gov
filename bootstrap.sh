#!/bin/bash

export DEBIAN_FRONTEND=noninteractive
apt-get update
# Don't apt-get upgrade http://stackoverflow.com/a/15093460/589391

# Need to lower mysql connections before install to limit memory use
mkdir -p /etc/mysql/conf.d
echo -e '[mysqld]\nmax_connections = 20' > /etc/mysql/conf.d/low_connect.cnf

apt-get install -y mysql-server-5.6 nginx php5-cli php5-fpm php5-mysql

echo 'Configuring mysql...'
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS wp"
# mysql -uroot -e "GRANT ALL PRIVILEGES ON wp.* TO username@hostname IDENTIFIED BY 'userpassword'"

echo 'Configuring nginx...'
cat > /etc/nginx/sites-available/default << 'EOF'
server {
    listen 80;

    root /vagrant/wp;
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
# python -m SimpleHTTPServer &

echo 'Installing composer...'
curl -sS https://getcomposer.org/download/1.0.0-alpha8/composer.phar > /usr/local/bin/composer
chmod 755 /usr/local/bin/composer

echo 'Install php components with composer...'
cd /vagrant
su vagrant -c 'composer install'

echo 'Wordpress should be up and running at http://localhost:8080'
