#!/bin/bash

set -e

# Render nginx confs in /etc with env vars
echo 'Rendering nginx confs...'
rm -rf /etc/nginx
shopt -s globstar
for f in nginx/**; do
  [ ! -f "$f" ] && continue
  mkdir -p `dirname "/etc/$f"`
  perl -X -p -i -e 's/<(\w+)>/defined $ENV{$1} ? $ENV{$1} : $&/eg' < "$f" > "/etc/$f"
done

# Test config
nginx -t

# Purge fastcgi cache
rm -rf /var/run/nginx-cache/*

service nginx reload

# Reload php-fpm
# https://bugs.launchpad.net/ubuntu/+source/php5/+bug/1242376
kill -USR2 `cat /var/run/php5-fpm.pid`
