<?php
/**
 * The base configurations of the WordPress.
 *
 * For all getenv() vars set fastcgi_param values in nginx.
 *
 * @package WordPress
 */

/** For Composer-driven autoload. See http://composer.rarst.net/recipe/site-stack */
require __DIR__ . '/vendor/autoload.php';

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', getenv('DB_NAME') ?: 'wp');

/** MySQL database username */
define('DB_USER', getenv('DB_USER') ?: 'root');

/** MySQL database password */
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');

/** MySQL hostname */
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', getenv('AUTH_KEY') ?: 'put your unique phrase here');
define('SECURE_AUTH_KEY', getenv('SECURE_AUTH_KEY') ?: 'put your unique phrase here');
define('LOGGED_IN_KEY', getenv('LOGGED_IN_KEY') ?: 'put your unique phrase here');
define('NONCE_KEY', getenv('NONCE_KEY') ?: 'put your unique phrase here');
define('AUTH_SALT', getenv('AUTH_SALT') ?: 'put your unique phrase here');
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT') ?: 'put your unique phrase here');
define('LOGGED_IN_SALT', getenv('LOGGED_IN_SALT') ?: 'put your unique phrase here');
define('NONCE_SALT', getenv('NONCE_SALT') ?: 'put your unique phrase here');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', getenv('WP_DEBUG') ?: true);

/** 
 * Advanced options
 *
 * See http://codex.wordpress.org/Editing_wp-config.php#Advanced_Options
 */

/** WP_SITEURL overrides DB to set WP core address */
define('WP_SITEURL', getenv('WP_SITEURL') ?: 'http://localhost:19102');

/** WP_HOME overrides DB to set public site address */
/** To be changed to port 19107 once static is generated there */
define('WP_HOME', getenv('WP_HOME') ?: 'http://localhost:19102');

/** Directory splitting for Composer */
define('WP_CONTENT_DIR', __DIR__ . '/wp-content');

/** For AWS and S3 usage */
define('AWS_ACCESS_KEY_ID', getenv('AWS_ACCESS_KEY_ID') ?: 'noaccesskeydefined');
define('AWS_SECRET_ACCESS_KEY', getenv('AWS_SECRET_ACCESS_KEY') ?: 'nosecretkeydefined');

/** https://wordpress.org/support/topic/problem-after-the-recent-update */
define('FS_METHOD', 'direct');


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
