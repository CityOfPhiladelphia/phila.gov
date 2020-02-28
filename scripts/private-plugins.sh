#!/bin/bash
#
# Install private plugins

source /home/ubuntu/.ssh/environment

_dir="$(dirname "$0")"
plugins="two-factor-authentication-premium.1.7.2.zip
mb-admin-columns-1.4.3.zip
mb-revision-1.3.3.zip
mb-settings-page-2.1.0.zip
mb-term-meta-1.2.9.zip
meta-box-columns-1.2.6.zip
meta-box-conditional-logic-1.6.11.zip
meta-box-group-1.3.8.zip
meta-box-include-exclude-1.0.10.zip
meta-box-tabs-1.1.5.zip
meta-box-tooltip-1.1.2.zip
wpfront-user-role-editor-personal-pro-2.14.3.zip"

cd /home/ubuntu/app
for plugin in $plugins; do
  #wp plugin install --quiet --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")" > /dev/null
  echo "--- Installing $plugin ---"
  wp plugin install --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")"
done
