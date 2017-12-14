<?php
/**
 * Media Library Assistant Plugin Loader
 *
 * Defines constants and loads all of the classes and functions required to run the plugin.
 * This file is only loaded if the naming conflict tests in index.php are passed.
 *
 * @package Media Library Assistant
 * @since 0.20
 */

defined( 'ABSPATH' ) or die();

if ( ! defined('MLA_OPTION_PREFIX') ) {
	/**
	 * Gives a unique prefix for plugin options; can be set in wp-config.php
	 */
	define('MLA_OPTION_PREFIX', 'mla_');
}

if ( ! defined('MLA_DEBUG_LEVEL') ) {
	/**
	 * Activates debug options; can be set in wp-config.php
	 */
	define('MLA_DEBUG_LEVEL', 1);
}

if ( ! defined('MLA_AJAX_EXCEPTIONS') ) {
	/**
	 * Activates full MLA load for specified AJAX actions; can be set in wp-config.php
	 */
	define('MLA_AJAX_EXCEPTIONS', '');
}

/**
 * Accumulates error messages from name conflict tests
 *
 * @since 1.14
 */
$mla_plugin_loader_error_messages = '';
 
/**
 * Displays version conflict error messages at the top of the Dashboard
 *
 * @since 1.14
 */
function mla_plugin_loader_reporting_action () {
	global $mla_plugin_loader_error_messages;

	echo '<div class="error"><p><strong>' . __( 'The Media Library Assistant cannot load.', 'media-library-assistant' ) . '</strong></p>'."\r\n";
	echo "<ul>{$mla_plugin_loader_error_messages}</ul>\r\n";
	echo '<p>' . __( 'You must resolve these conflicts before this plugin can safely load.', 'media-library-assistant' ) . '</p></div>'."\r\n";
}

/*
 * Basic library of run-time tests.
 */
require_once( MLA_PLUGIN_PATH . 'tests/class-mla-tests.php' );

$mla_plugin_loader_error_messages .= MLATest::min_php_version( '5.3' );
$mla_plugin_loader_error_messages .= MLATest::min_WordPress_version( '3.5.0' );

if ( ! empty( $mla_plugin_loader_error_messages ) ) {
	add_action( 'admin_notices', 'mla_plugin_loader_reporting_action' );
} else {
	// MLATest is loaded above
	add_action( 'init', 'MLATest::initialize', 0x7FFFFFFF );

	// Minimum support functions required by all other components
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-core.php' );
	add_action( 'plugins_loaded', 'MLACore::mla_plugins_loaded_action', 0x7FFFFFFF );
	add_action( 'init', 'MLACore::initialize', 0x7FFFFFFF );

	// Check for XMLPRC, WP REST API and front end requests
	if( !( defined('WP_ADMIN') && WP_ADMIN ) ) {
		$front_end_only = true;

		// XMLRPC requests need everything loaded to process uploads
		if ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ) {
			$front_end_only = false;
		}

		// WP REST API calls need everything loaded to process uploads
		if ( isset( $_SERVER['REQUEST_URI'] ) && 0 === strpos( $_SERVER['REQUEST_URI'], '/wp-json/' ) ) {
			$front_end_only = false; // TODO be more selective
		}

		// Front end posts/pages only need shortcode support; load the interface shims.
		if ( $front_end_only ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcodes.php' );
			add_action( 'init', 'MLAShortcodes::initialize', 0x7FFFFFFF );
			return;
		}
	}

	if( defined('DOING_AJAX') && DOING_AJAX ) {
		/*
		 * Ajax handlers
		 */
		require_once( MLA_PLUGIN_PATH . 'includes/class-mla-ajax.php' );
		add_action( 'init', 'MLA_Ajax::initialize', 0x7FFFFFFF );

		/*
		 * Quick and Bulk Edit requires full support for content templates, etc.
		 * IPTC/EXIF and Custom Field mapping require full support, too.
		 * NOTE: AJAX upload_attachment is no longer used - see /wp-admin/asynch-upload.php
		 */
		$ajax_exceptions = array( MLACore::JAVASCRIPT_INLINE_EDIT_SLUG, 'mla-inline-mapping-iptc-exif-scripts', 'mla-inline-mapping-custom-scripts', 'mla-polylang-quick-translate', 'mla-inline-edit-upload-scripts', 'mla-inline-edit-view-scripts', 'mla-inline-edit-custom-scripts', 'mla-inline-edit-iptc-exif-scripts', 'upload-attachment' );

 		$ajax_only = true;
		if ( MLA_AJAX_EXCEPTIONS ) {
			if ( 'always' === trim( strtolower( MLA_AJAX_EXCEPTIONS ) ) ) {
				$ajax_only = false;
			} else {
				$ajax_exceptions = array_merge( $ajax_exceptions, explode( ',', MLA_AJAX_EXCEPTIONS ) );
			}
		}

		if ( $ajax_only && isset( $_REQUEST['action'] ) ) {
			if ( in_array( $_REQUEST['action'], $ajax_exceptions ) ) {
				$ajax_only = false;
			} elseif ( 'mla-update-compat-fields' == $_REQUEST['action'] ) {
				global $sitepress;

				//Look for multi-lingual terms updates
				if ( is_object( $sitepress ) || class_exists( 'Polylang' ) ) {
					$ajax_only = false;
				}
			} elseif ( 'ajax-tag-search' == $_REQUEST['action'] ) {
				global $sitepress;

				//Look for WPML flat taxonomy autocomplete
				if ( is_object( $sitepress ) ) {
					$ajax_only = false;
				}
			}
		}

		MLA_Ajax::$ajax_only = $ajax_only; // for debug logging
		if ( $ajax_only ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-query.php' );
			add_action( 'init', 'MLAQuery::initialize', 0x7FFFFFFF );

			/*
			 * Other plugins such as "No Cache AJAX Widgets" might need shortcodes
			 */
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcodes.php' );
			add_action( 'init', 'MLAShortcodes::initialize', 0x7FFFFFFF );

			return;
		}
	}

	/*
	 * Template file and database access functions.
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-query.php' );
	add_action( 'init', 'MLAQuery::initialize', 0x7FFFFFFF );

	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data.php' );
	add_action( 'init', 'MLAData::initialize', 0x7FFFFFFF );

	/*
	 * Shortcode shim functions
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcodes.php' );
	add_action( 'init', 'MLAShortcodes::initialize', 0x7FFFFFFF );

	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcode-support.php' );

	/*
	 * Plugin settings management
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-options.php' );
	add_action( 'init', 'MLAOptions::initialize', 0x7FFFFFFF );
	 
	/*
	 * Plugin settings management page
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-settings.php' );
	add_action( 'init', 'MLASettings::initialize', 0x7FFFFFFF );

	/*
	 * Main program
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-main.php' );
	add_action( 'init', 'MLA::initialize', 0x7FFFFFFF );

	/*
	 * Edit Media screen additions, e.g., meta boxes
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-edit-media.php' );
	add_action( 'init', 'MLAEdit::initialize', 0x7FFFFFFF );

	/*
	 * Media Manager (Modal window) additions
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-media-modal.php' );
	add_action( 'init', 'MLAModal::initialize', 0x7FFFFFFF );

	/*
	 * Custom list table package that extends the core WP_List_Table class.
	 * Doesn't need an initialize function; has a constructor.
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-list-table.php' );
}
?>