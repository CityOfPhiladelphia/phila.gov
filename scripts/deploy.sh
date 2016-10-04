#!/bin/bash

set -e

_dir="$(dirname "$0")"

source "$_dir/lib/mo"

echo 'Running grunt tasks'
cd /home/ubuntu/app/wp/wp-content/themes/phila.gov-theme
npm install
grunt
cd /home/ubuntu/app
