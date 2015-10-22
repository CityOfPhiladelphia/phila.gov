#!/bin/bash

rm wp-config.php

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

wp core config --dbname=$DB_NAME --dbuser=$DB_USER ${DB_PASS+"--dbpass=$DB_PASS"} ${DB_HOST+"--dbhost=$DB_HOST"} --skip-check $SKIP_SALTS --extra-php <<PHP
$SALTS

/** For Composer-driven autoload. See http://composer.rarst.net/recipe/site-stack */
require __DIR__ . '/vendor/autoload.php';

/** WP_SITEURL overrides DB to set WP core address */
define('WP_SITEURL', '$WP_SITEURL');

/** WP_HOME overrides DB to set public site address */
define('WP_HOME', '$WP_HOME');

/** Directory splitting for Composer */
define('WP_CONTENT_DIR', __DIR__ . '/wp-content');

/** For AWS and S3 usage */
define('AWS_ACCESS_KEY_ID', '$AWS_ACCESS_KEY_ID');
define('AWS_SECRET_ACCESS_KEY', '$AWS_SECRET_ACCESS_KEY');

/** For Swiftype search */
define('SWIFTYPE_ENGINE', '$SWIFTYPE_ENGINE');

/** https://wordpress.org/support/topic/problem-after-the-recent-update */
define('FS_METHOD', 'direct');
PHP

mv wp/wp-config.php .
