#!/bin/bash

set -e

_dir="$(dirname "$0")"

source /home/ubuntu/.ssh/environment
source "$_dir/lib/mo"

echo 'Running wp-config.sh'
"$_dir/wp-config.sh"

echo 'Running build tasks'
cd /home/ubuntu/app/wp/wp-content/themes/phila.gov-theme
sudo npm update
if [ "$PHILA_TEST" ]; then
  echo 'Running test machine tasks'
  sudo npm rebuild node-sass
  sudo npm run dev:build
else
  echo 'Running prod tasks'
  sudo npm run build
  sudo npm run postbuild
fi
cd /home/ubuntu/app

echo 'Modifying php configs'
sudo ed -s /etc/php/7.0/fpm/pool.d/www.conf <<'EOF'
g/^pm\.max_children/s/5/10
w
EOF
sudo ed -s /etc/php/7.0/fpm/php.ini <<'EOF'
g/^post_max_size/s/8/100
g/^upload_max_filesize/s/2/100
w
EOF

echo 'Reloading php-fpm'
sudo service php7.0-fpm reload

echo 'Refreshing WordPress'
wp rewrite flush
wp core update-db
mkdir -p /home/ubuntu/app/wp/wp-content/uploads
sudo chmod 777 -R /home/ubuntu/app/wp/wp-content/uploads

echo 'Rendering nginx confs'
# Render nginx confs into /etc with mo
sudo rm -rf /etc/nginx
shopt -s globstar
# Defaults
[ ! "$ROBOTS_DISALLOW" ] && export ROBOTS_DISALLOW=/
for f in nginx/**; do
  [ ! -f "$f" ] && continue
  sudo mkdir -p "$(dirname "/etc/$f")"
  mo "$f" | sudo tee "/etc/$f" > /dev/null
done

echo 'Testing nginx config'
sudo nginx -t

echo 'Purging nginx cache'
sudo rm -rf /var/run/nginx-cache
sudo rm -rf /home/ubuntu/app/wp/wp-content/cache

sudo service nginx reload
