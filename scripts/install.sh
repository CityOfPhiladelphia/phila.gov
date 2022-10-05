#!/bin/bash

_dir="$(dirname "$0")"

export DEBIAN_FRONTEND=noninteractive

sudo apt-get update

echo "Install npm"
cd /home/ubuntu/app/wp/wp-content/themes/phila.gov-theme
npm install
npm cache verify
cd /home/ubuntu/app

if [ "$PHILA_TEST" ]; then
  $_dir/unison.sh
  $_dir/gen-cert.sh
  $_dir/wp-config.sh
  $_dir/db-config.sh
  $_dir/local-db.sh
fi
