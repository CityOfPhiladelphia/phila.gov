#!/bin/bash
#
# Install private plugins

_dir="$(dirname "$0")"
plugins="wpfront-user-role-editor-personal-pro-2.12.2.zip"

for plugin in $plugins; do
  wp plugin install --quiet --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")"
done
