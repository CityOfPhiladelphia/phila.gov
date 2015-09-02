#!/bin/bash

set -e

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

echo 'Reloading php-fpm...'
# https://bugs.launchpad.net/ubuntu/+source/php5/+bug/1242376
sudo kill -USR2 `cat /var/run/php5-fpm.pid`

echo 'Running WP CLI deploy steps...'
wp rewrite flush
wp core update-db
