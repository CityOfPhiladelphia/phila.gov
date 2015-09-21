#!/bin/bash

set -e

echo 'Running composer install...'
composer config -g repositories.private composer $COMPOSER_REPOSITORY_URL
composer install

echo 'Modifying php.ini...'
sudo ed /etc/php5/fpm/php.ini <<'EOF'
g/post_max_size/s/8/100
g/upload_max_filesize/s/2/100
g/expose_php/s/On/Off
w
EOF

echo 'Reloading php-fpm...'
# https://bugs.launchpad.net/ubuntu/+source/php5/+bug/1242376
sudo kill -USR2 `cat /var/run/php5-fpm.pid`

echo 'Running WP CLI deploy steps...'
wp rewrite flush
wp core update-db

echo 'Giving web server write access to uploads...'
mkdir -p wp-content/uploads
sudo chmod 777 wp-content/uploads


# Render nginx confs in /etc with env vars
echo 'Rendering nginx confs...'
sudo rm -rf /etc/nginx
shopt -s globstar
for f in nginx/**; do
  [ ! -f "$f" ] && continue
  sudo mkdir -p `dirname "/etc/$f"`
  # Replace <ENV_VARS> in files and place them in /etc/nginx
  perl -X -p -i -e 's/<(\w+)>/defined $ENV{$1} ? $ENV{$1} : $&/eg' < "$f" | sudo tee "/etc/$f" > /dev/null
done

# Test config
sudo nginx -t

# Purge fastcgi cache
sudo rm -rf /var/run/nginx-cache/*

sudo service nginx reload
