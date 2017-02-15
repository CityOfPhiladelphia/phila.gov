#!/bin/bash
#
# Install private plugins

source /home/ubuntu/.ssh/environment

_dir="$(dirname "$0")"
plugins="meta-box-columns-1.0.0.zip meta-box-group-1.1.6.zip meta-box-include-exclude-1.0.5.zip meta-box-conditional-logic-1.3.4.zip wpfront-user-role-editor-personal-pro-2.13.zip"

cd /home/ubuntu/app
for plugin in $plugins; do
  #wp plugin install --quiet --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")" > /dev/null
  echo "--- Installing $plugin ---"
  wp plugin install --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")"
done
