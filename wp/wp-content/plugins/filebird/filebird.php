<?php
/**
 * Plugin Name: FileBird Lite
 * Plugin URI: https://ninjateam.org/wordpress-media-library-folders/
 * Description: Organize thousands of WordPress media files into folders/ categories at ease.
 * Version: 5.6.4
 * Author: Ninja Team
 * Author URI: https://ninjateam.org
 * Text Domain: filebird
 * Domain Path: /i18n/languages/
 *
 * @package FileBirdPlugin
 */
namespace FileBird;

defined( 'ABSPATH' ) || exit;

if ( function_exists( 'FileBird\\init' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/Fallback.php';
	if ( isset( $_GET['activate'] ) ) { // phpcs:ignore
		unset( $_GET['activate'] ); // phpcs:ignore
	}
	add_action(
         'admin_init',
        function() {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
        );
	return;
}

if ( ! defined( 'NJFB_PREFIX' ) ) {
	define( 'NJFB_PREFIX', 'filebird' );
}

if ( ! defined( 'NJFB_VERSION' ) ) {
	define( 'NJFB_VERSION', '5.6.4' );
}

if ( ! defined( 'NJFB_PLUGIN_FILE' ) ) {
	define( 'NJFB_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'NJFB_PLUGIN_URL' ) ) {
	define( 'NJFB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'NJFB_PLUGIN_PATH' ) ) {
	define( 'NJFB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'NJFB_PLUGIN_BASE_NAME' ) ) {
	define( 'NJFB_PLUGIN_BASE_NAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'NJFB_REST_URL' ) ) {
	define( 'NJFB_REST_URL', 'filebird/v1' );
}

if ( ! defined( 'NJFB_REST_PUBLIC_URL' ) ) {
	define( 'NJFB_REST_PUBLIC_URL', 'filebird/public/v1' );
}

if ( ! defined( 'NJFB_UPLOAD_DIR' ) ) {
	define( 'NJFB_UPLOAD_DIR', 'filebird-uploads' );
}

if ( file_exists( NJFB_PLUGIN_PATH . '/vendor/autoload.php' ) ) {
	require_once NJFB_PLUGIN_PATH . '/vendor/autoload.php';
}

spl_autoload_register(
	function ( $class ) {
		$prefix   = __NAMESPACE__; // project-specific namespace prefix
		$base_dir = __DIR__ . '/includes'; // base directory for the namespace prefix

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) { // does the class use the namespace prefix?
			return; // no, move to the next registered autoloader
		}

		$relative_class_name = substr( $class, $len );

		// replace the namespace prefix with the base directory, replace namespace
		// separators with directory separators in the relative class name, append
		// with .php
		$file = $base_dir . str_replace( '\\', '/', $relative_class_name ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

if ( ! function_exists( 'FileBird\\init' ) ) {
	function init() {
		Plugin::prepareRun();
		I18n::loadPluginTextdomain();

		new Classes\Feedback();
		new Classes\Review();
		new Classes\Svg();
		Page\Settings::getInstance();

		Classes\Convert::getInstance();
		Controller\Folder::getInstance();

		new Support\SupportController();
		new Classes\Schedule();
		new Blocks\BlockController();

		if ( function_exists( 'register_block_type' ) ) {
			require plugin_dir_path( __FILE__ ) . 'blocks/filebird-gallery/dist/init.php';
		}

		require_once plugin_dir_path( __FILE__ ) . 'includes/YC.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/Recommended/Recommended.php';
	}
}
add_action( 'plugins_loaded', 'FileBird\\init' );

register_activation_hook( __FILE__, array( 'FileBird\\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'FileBird\\Plugin', 'deactivate' ) );
