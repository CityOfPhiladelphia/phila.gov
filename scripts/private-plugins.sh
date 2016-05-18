#!/bin/bash
#
# Install private plugins

_dir="$(dirname "$0")"
plugins="meta-box-group-1.0.7.zip meta-box-include-exclude-1.0.3.zip wpfront-user-role-editor-personal-pro-2.12.5.zip"

for plugin in $plugins; do
  wp plugin install --quiet --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")" > /dev/null
done
