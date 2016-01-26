#!/bin/bash


set -e

_dir="$(dirname "$0")"

source "$_dir/lib/mo"

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

echo 'Installing private plugins'
"$_dir/private-plugins.sh"

echo 'Reloading php-fpm'
sudo service php7.0-fpm reload

echo 'Refreshing WordPress'
wp rewrite flush
wp core update-db
mkdir -p wp/wp-content/uploads
sudo chmod 777 -R wp/wp-content/uploads

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

sudo service nginx reload

echo 'Writing crontab'
crontab - <<EOF
APP_DIR=$APP_DIR
PATH=$PATH
SLACK_HOOK=$SLACK_HOOK

20 11 * * * $HOME/$APP_DIR/scripts/update-plugins-slack.sh
EOF
