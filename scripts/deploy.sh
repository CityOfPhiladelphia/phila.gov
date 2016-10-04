#!/bin/bash

set -e

_dir="$(dirname "$0")"

source "$_dir/lib/mo"

echo 'Running grunt tasks'
cd /home/ubuntu/app/wp/wp-content/themes/phila.gov-theme
npm install
grunt
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

echo 'Installing private plugins'
"$_dir/private-plugins.sh"

echo 'Reloading php-fpm'
sudo service php7.0-fpm reload
