#!/bin/bash

echo 'Writing wp-config.php'

# Don't let any existing configs get in the way
rm -f wp-config.php wp/wp-config.php

# DOMAIN is INSTANCE_HOSTNAME unless PUBLIC_HOSTNAME is set
DOMAIN=${PUBLIC_HOSTNAME:-"$INSTANCE_HOSTNAME"}

if [ "$WP_AUTH_KEY" ]; then
  SKIP_SALTS="--skip-salts"
  read -r -d '' SALTS <<EOF
define('AUTH_KEY',         '$WP_AUTH_KEY');
define('SECURE_AUTH_KEY',  '$WP_SECURE_AUTH_KEY');
define('LOGGED_IN_KEY',    '$WP_LOGGED_IN_KEY');
define('NONCE_KEY',        '$WP_NONCE_KEY');
define('AUTH_SALT',        '$WP_AUTH_SALT');
define('SECURE_AUTH_SALT', '$WP_SECURE_AUTH_SALT');
define('LOGGED_IN_SALT',   '$WP_LOGGED_IN_SALT');
define('NONCE_SALT',       '$WP_NONCE_SALT');
EOF
fi

wp core config --dbname=${DB_NAME:-'wp'} --dbuser=${DB_USER:-'root'} ${DB_PASS+"--dbpass=$DB_PASS"} ${DB_HOST+"--dbhost=$DB_HOST"} --skip-check $SKIP_SALTS --extra-php <<PHP
$SALTS

/** WP_SITEURL overrides DB to set WP core address */
define('WP_SITEURL', 'https://$DOMAIN');

/** WP_HOME overrides DB to set public site address */
define('WP_HOME', 'https://$DOMAIN');

/** For AWS and S3 usage */
define('AWS_ACCESS_KEY_ID', '$AWS_ID');
define('AWS_SECRET_ACCESS_KEY', '$AWS_SECRET');
define('AS3CF_BUCKET', '$PHILA_MEDIA_BUCKET');
define('AS3CF_DOMAIN', '$DOMAIN');

/** For Swiftype search */
define('SWIFTYPE_ENGINE', '$SWIFTYPE_ENGINE');

/** https://wordpress.org/support/topic/problem-after-the-recent-update */
define('FS_METHOD', 'direct');
PHP
