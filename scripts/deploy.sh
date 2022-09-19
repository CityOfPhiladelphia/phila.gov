#!/bin/bash

set -e

_dir="$(dirname "$0")"

source /home/ubuntu/.ssh/environment
source "$_dir/lib/mo"

echo 'Running wp-config.sh'
"$_dir/wp-config.sh"

echo 'Running build tasks'
cd /home/ubuntu/app/wp/wp-content/themes/phila.gov-theme
npm install
if [ "$PHILA_TEST" ]; then
  echo 'Running test machine tasks'
  npm run dev:build
else
  echo 'Running prod tasks'
  npm run build
fi
cd /home/ubuntu/app

echo 'Modifying php configs'
sudo ed -s /etc/php/7.4/fpm/pool.d/www.conf <<'EOF'
g/^pm\.max_children/s/5/20
w
EOF
sudo ed -s /etc/php/7.4/fpm/php.ini <<'EOF'
g/^post_max_size/s/8/1000
g/^upload_max_filesize/s/100/500
g/^memory_limit/s/128/1024
g/^max_execution_time/s/500/600
g/^max_input_vars/s/2000000/3000000
w
EOF

if [ ! "$PHILA_TEST" ]; then
echo 'Installing private plugins'
"$_dir/private-plugins.sh"
fi

echo 'Reloading php-fpm'
sudo service php7.4-fpm reload

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
