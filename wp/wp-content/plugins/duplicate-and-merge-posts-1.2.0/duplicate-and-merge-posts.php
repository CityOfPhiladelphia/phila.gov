<?php
/*
Plugin Name: Duplicate and Merge Posts
Plugin URI: http://www.exygy.com
Description: Duplicate posts, edit them and then merge them back to the original post
Version: 1.0.5
Author: Exygy, DavidWells, Pllum
Author URI: http://www.exygy.com
License: GPL2
*/

if (!class_exists('Duplicate_And_Merge_Plugin')) {

	final class Duplicate_And_Merge_Plugin {

		/**
		 * START -> PHP VERSION CHECKS
		 */
		/**
		 * Admin notices, collected and displayed on proper action
		 *
		 * @var array
		 */
		public static $notices = array();

		/**
		 * Whether the current PHP version meets the minimum requirements
		 *
		 * @return bool
		 */
		public static function is_valid_php_version() {
			return version_compare( PHP_VERSION, '5.3', '>=' );
		}

		/**
		 * Invoked when the PHP version check fails. Load up the translations and
		 * add the error message to the admin notices
		 */
		static function fail_php_version() {
			add_action( 'plugins_loaded', array( __CLASS__, 'load_text_domain_init' ) );
			$plugin_url = admin_url( 'plugins.php' );
			self::notice( __( 'Duplicate, Edit and Merge Posts plugin requires PHP version 5.3+ to run. Your version '.PHP_VERSION.' is not high enough.<br><u>Please contact your hosting provider</u> to upgrade your PHP Version.<br>The plugin is NOT Running. You can disable this warning message by <a href="'.$plugin_url.'">deactivating the plugin</a>', 'dem' ) );
		}

		/**
		 * Handle notice messages according to the appropriate context (WP-CLI or the WP Admin)
		 *
		 * @param string $message
		 * @param bool $is_error
		 * @return void
		 */
		public static function notice( $message, $is_error = true ) {
			if ( defined( 'WP_CLI' ) ) {
				$message = strip_tags( $message );
				if ( $is_error ) {
					WP_CLI::warning( $message );
				} else {
					WP_CLI::success( $message );
				}
			} else {
				// Trigger admin notices
				add_action( 'all_admin_notices', array( __CLASS__, 'admin_notices' ) );

				self::$notices[] = compact( 'message', 'is_error' );
			}
		}

		/**
		 * Show an error or other message in the WP Admin
		 *
		 * @action all_admin_notices
		 * @return void
		 */
		public static function admin_notices() {
			foreach ( self::$notices as $notice ) {
				$class_name   = empty( $notice['is_error'] ) ? 'updated' : 'error';
				$html_message = sprintf( '<div class="%s">%s</div>', esc_attr( $class_name ), wpautop( $notice['message'] ) );
				echo wp_kses_post( $html_message );
			}
		}
		/**
		 * END -> PHP VERSION CHECKS
		 */

		/**
		* Main Duplicate_And_Merge_Plugin Instance
		*/
		public function __construct() {
			self::define_constants();
			self::includes();
			self::load_text_domain_init();
		}

		/*
		* Setup plugin constants
		*
		*/
		private static function define_constants() {

			define('DEM_CURRENT_VERSION', '1.0.5' );
			define('DEM_URLPATH', plugins_url( '/' , __FILE__ ) );
			define('DEM_PATH', WP_PLUGIN_DIR.'/'.plugin_basename( dirname(__FILE__) ).'/' );
			define('DEM_SLUG', plugin_basename( dirname(__FILE__) ) );
			define('DEM_FILE', __FILE__ );

		}

		/* Include required plugin files */
		private static function includes() {

			$settings = get_option('dem_main_settings');
			require_once 'admin/duplicate-post.php';
			// Check if current user is allowed to submit review to current post


			// Message that is sent to Admins
			// add_filter("duplicate_post_notification_message", function($message, $post, $new_post, $current_user){
			//   return implode( array(
			//     "An update has been posted for ",
			//     "<a href='".get_permalink($post->ID)."'>'".$post->post_title."'</a>.",
			//     "by ".$current_user->display_name,
			//     "<br><br> To review the update follow the link ",
			//     "<a href='".get_permalink($new_post->ID)."'>'".$new_post->post_title."'</a>. ",
			//     "<a href='".admin_url("edit.php").'?page=show-diff&post='.$new_post->ID."'>Click here</a> to view changes side-by-side."
			//   ) );
			// },10,4);

			$emails = explode("\n", $settings['notify_emails']);

			// TODO move admin emails to class
			DuplicatePost::_init(array(
			  "duplicate_post_title_prefix" => "[Duplicated] ",
			  "duplicate_post_show_adminbar" => true,
			  "duplicate_post_show_row" => true,
			  "duplicate_post_show_submitbox" => true,
			  // "duplicate_post_add_nofollow_noindex" => true,
			  "duplicate_post_global_admins" => $emails
			));


			switch (is_admin()) :
				case true :
					/* loads admin files */
					require_once 'admin/settings.php';


					BREAK;

				case false :
					/* load front-end files */
					//include_once('public/blah.php');


					BREAK;
			endswitch;
		}

		/**
		*  Loads the correct .mo file for this plugin
		*/
		private static function load_text_domain_init() {
			add_action( 'init' , array( __CLASS__ , 'load_text_domain' ) );
		}

		public static function load_text_domain() {
			load_plugin_textdomain( 'dem' , false , DEM_SLUG . '/lang/' );
		}


	}

	/* Initiate Plugin */
	if ( Duplicate_And_Merge_Plugin::is_valid_php_version() ) {
		// Get Inbound Now Running
		$GLOBALS['Duplicate_And_Merge_Plugin'] = new Duplicate_And_Merge_Plugin;
	} else {
		// Show Fail
		Duplicate_And_Merge_Plugin::fail_php_version();
	}


}