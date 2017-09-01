#!/bin/bash
#
# Install private plugins

source /home/ubuntu/.ssh/environment

_dir="$(dirname "$0")"
plugins="mb-admin-columns-1.2.zip mb-revision-1.0.1.zip meta-box-columns-1.0.2.zip meta-box-group-1.2.11.zip meta-box-include-exclude-1.0.9.zip meta-box-conditional-logic-1.4.1.zip wpfront-user-role-editor-personal-pro-2.13.1.zip meta-box-updater-0.1.0.zip"

cd /home/ubuntu/app
for plugin in $plugins; do
  #wp plugin install --quiet --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")" > /dev/null
  echo "--- Installing $plugin ---"
  wp plugin install --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")"
done
