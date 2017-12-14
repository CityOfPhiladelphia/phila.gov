<?php
/**
 * Media Library Assistant Uninstall
 *
 * Uninstalling (deleting) Media Library Assistant deletes option settings.
 *
 * @package Media Library Assistant
 * @since 2.25
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Provides path information to the plugin root in file system format, including the trailing slash.
 */
define( 'MLA_PLUGIN_PATH', rtrim( dirname( __FILE__ ), '/\\' ) . '/' );

if ( ! defined( 'MLA_BACKUP_DIR' ) ) {
	/**
	 * Provides the absolute path to the MLA backup directory, including the trailing slash.
	 * This constant can be overriden by defining it in the wp_config.php file.
	 */
	$content_dir = ( defined('WP_CONTENT_DIR') ) ? WP_CONTENT_DIR : ABSPATH . 'wp-content';
	define( 'MLA_BACKUP_DIR', $content_dir . '/mla-backup/' );
	unset( $content_dir );
}

if ( ! defined('MLA_OPTION_PREFIX') ) {
	/**
	 * Gives a unique prefix for plugin options; can be set in wp-config.php
	 */
	define('MLA_OPTION_PREFIX', 'mla_');
}

/*
 * Load the MLA Options table to get the option settings list
 */
require_once( MLA_PLUGIN_PATH . 'includes/class-mla-core-options.php' );
MLACoreOptions::mla_localize_option_definitions_array();

/**
 * Class MLA (Media Library Assistant) Uninstall deletes the data associated with the MLA plugin
 *
 * @package Media Library Assistant
 * @since 2.25
 */
class MLAUninstall {
	/**
	 * Delete option settings and/or backup directory, if the appropriate MLA General options are set
	 *
	 * @since 2.25
	 */
	public static function process_uninstall( ) {
		$delete_option_settings = 'checked' === get_option( MLA_OPTION_PREFIX . MLACoreOptions::MLA_DELETE_OPTION_SETTINGS, false );
		$delete_option_backups = 'checked' === get_option( MLA_OPTION_PREFIX . MLACoreOptions::MLA_DELETE_OPTION_BACKUPS, false );

		/*
		 * Delete saved settings
		 */
		if ( $delete_option_settings ) {
			foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
				if ( in_array( $value['type'], array( 'header', 'subheader' ) ) ) {
					continue;
				} else {
					$result = delete_option( MLA_OPTION_PREFIX .$key );
				}
			}
		} // $delete_option_settings
		
		/*
		 * Delete backup files and directory  (best efforts)
		 */
		if ( $delete_option_backups && file_exists( MLA_BACKUP_DIR ) ) {
			$files = @scandir( MLA_BACKUP_DIR, SCANDIR_SORT_NONE );
			if ( is_array( $files ) ) {
				foreach ( $files as $file ) {
					if ( 0 === strpos( $file, '.' ) ) {
						continue;
					}
					
					@unlink( MLA_BACKUP_DIR . $file );
				}
			} // is_array
	
			@rmdir( MLA_BACKUP_DIR );
		} // $delete_option_backups
	} // process_uninstall
} // class MLAUninstall
MLAUninstall::process_uninstall();
?>