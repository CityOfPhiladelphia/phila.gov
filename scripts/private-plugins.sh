#!/bin/bash
#
# Install private plugins

source /home/ubuntu/.ssh/environment
# wps-hide-login.1.5.7.zip <- hide login plugin

_dir="$(dirname "$0")"
plugins="mb-admin-columns-1.6.0.zip
mb-rest-api-1.4.0.zip
mb-revision-1.3.3.zip
mb-settings-page-2.1.4.zip
mb-term-meta-1.2.10.zip
meta-box-columns-1.2.14.zip
meta-box-conditional-logic-1.6.14.zip
meta-box-group-1.3.12.zip
meta-box-include-exclude-1.0.11.zip
meta-box-tabs-1.1.9.zip
meta-box-tooltip-1.1.4.zip
miniorange-saml-20-single-sign-on.4.9.08.zip
sitepress-multilingual-cms.4.5.2.zip
wpfront-user-role-editor-personal-pro-2.14.5.zip
wp-nested-pages-phila-3.1.15.zip
wpml-media-translation.2.7.0.zip
wpml-string-translation.3.2.0.zip
wpml-translation-management.2.10.8.zip"

cd /home/ubuntu/app
for plugin in $plugins; do
  #wp plugin install --quiet --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")" > /dev/null
  echo "--- Installing $plugin ---"
  wp plugin install --force --activate "$("$_dir/s3url.sh" "$PHILA_PLUGIN_BUCKET" "$plugin")"
done
