#!/bin/bash
#
# Install private plugins

source /home/ubuntu/.ssh/environment
# wps-hide-login.1.5.7.zip <- hide login plugin

_dir="$(dirname "$0")"
plugins="wpfront-user-role-editor-personal-pro-2.14.5.zip"

cd /home/ubuntu/app
for plugin in $plugins; do
  #wp plugin install --quiet --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")" > /dev/null
  echo "--- Installing $plugin ---"
  wp plugin install --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")"
done