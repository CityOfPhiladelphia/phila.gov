#!/bin/bash
#
# Install private plugins

source /home/ubuntu/.ssh/environment

_dir="$(dirname "$0")"
plugins="mb-admin-columns-1.3.0.zip
mb-revision-1.1.1.zip
meta-box-columns-1.2.3.zip
meta-box-conditional-logic-1.5.5.zip
meta-box-group-1.2.13.zip
meta-box-include-exclude-1.0.9.zip
meta-box-tabs-1.0.3.zip
meta-box-tooltip-1.1.1.zip
wpfront-user-role-editor-personal-pro-2.14.1.zip"

cd /home/ubuntu/app
for plugin in $plugins; do
  #wp plugin install --quiet --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")" > /dev/null
  echo "--- Installing $plugin ---"
  wp plugin install --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")"
done
