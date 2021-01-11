#!/bin/bash

set -e

_dir="$(dirname "$0")"

source /home/ubuntu/.ssh/environment
source "$_dir/lib/mo"

echo 'Running wp-config.sh'
"$_dir/wp-config.sh"

sudo chmod 755 $_dir/db-config.sh

echo 'Running db-config.sh'
"$_dir/db-config.sh"

echo 'Running build tasks'
cd /home/ubuntu/app/wp/wp-content/themes/phila.gov-theme
npm install
if [ "$PHILA_TEST" ]; then
  echo 'Running test machine tasks'
  npm rebuild node-sass
  npm run dev:build
else
  echo 'Running prod tasks'
  npm run build
  npm run postbuild
fi
cd /home/ubuntu/app

echo 'Modifying php configs'
sudo ed -s /etc/php/7.2/fpm/pool.d/www.conf <<'EOF'
g/^pm\.max_children/s/10/20
w
EOF
sudo ed -s /etc/php/7.2/fpm/php.ini <<'EOF'
g/^post_max_size/s/8/1000
g/^upload_max_filesize/s/2/100
g/^memory_limit/s/128/1024
g/^max_execution_time/s/30/300
g/^max_input_vars/s/100000/1000000
w
EOF

if [ ! "$PHILA_TEST" ]; then
echo 'Installing private plugins'
"$_dir/private-plugins.sh"
fi

echo 'Reloading php-fpm'
sudo service php7.2-fpm reload

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
