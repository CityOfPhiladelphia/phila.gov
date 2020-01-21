#!/bin/bash
#
# Install private plugins

source /home/ubuntu/.ssh/environment

_dir="$(dirname "$0")"
plugins="two-factor-authentication-premium.1.6.2.zip
mb-admin-columns-1.4.2.zip
mb-revision-1.3.2.zip
mb-settings-page-1.3.4.zip
mb-term-meta-1.2.5.zip
meta-box-columns-1.2.5.zip
meta-box-conditional-logic-1.6.4.zip
meta-box-group-1.3.4.zip
meta-box-include-exclude-1.0.10.zip
meta-box-tabs-1.1.4.zip
meta-box-tooltip-1.1.1.zip
wpfront-user-role-editor-personal-pro-2.14.1.zip"

cd /home/ubuntu/app
for plugin in $plugins; do
  #wp plugin install --quiet --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")" > /dev/null
  echo "--- Installing $plugin ---"
  wp plugin install --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")"
done
