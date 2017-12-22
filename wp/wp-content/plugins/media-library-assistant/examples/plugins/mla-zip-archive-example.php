<?php
/**
 * Provides an example of hooking the filters provided by the MLA_List_Table class
 *
 * In this example, a Bulk Action is created that downloads one or more files as a ZIP archive.
 *
 * @package MLA Download ZIP Example
 * @version 1.00
 */

/*
Plugin Name: MLA Download ZIP Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides an example of hooking the filters provided by the MLA_List_Table class
Author: David Lingren
Version: 1.00
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2014 David Lingren

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You can get a copy of the GNU General Public License by writing to the
	Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

/**
 * Class MLA Download ZIP Example hooks some of the filters provided by the MLA_List_Table class
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Download ZIP Example
 * @since 1.00
 */
class MLADownloadZIPExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * The filters are only useful for the admin section; exit in the front-end posts/pages
		 */
		if ( ! is_admin() )
			return;

		/*
		 * Make sure we have ZIP support
		 */
		if ( ! class_exists( 'ZipArchive' ) )
			return;

		/*
		 * add_filter parameters:
		 * $tag - name of the hook you're filtering; defined by [mla_gallery]
		 * $function_to_add - function to be called when [mla_gallery] applies the filter
		 * $priority - default 10; lower runs earlier, higher runs later
		 * $accepted_args - number of arguments your function accepts
		 */

 		add_action( 'admin_init', 'MLADownloadZIPExample::admin_init_action', 10 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-list-table.php
		  */
		add_filter( 'mla_list_table_get_bulk_actions', 'MLADownloadZIPExample::mla_list_table_get_bulk_actions', 10, 1 );
	}

	/**
	 * Process the 'download-zip' bulk action
	 *
	 * We must take control here so we can successfully issue a wp_redirect for the download.
	 *
	 * @since 1.00
	 */
	public static function admin_init_action() {
		//error_log( 'MLADownloadZIPExample::admin_init_action $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );

		$bulk_action = '';
		if ( isset( $_REQUEST['action'] ) && 'download-zip' == $_REQUEST['action']) {
			$bulk_action = 'download-zip';
		} elseif ( isset( $_REQUEST['action2'] ) && 'download-zip' == $_REQUEST['action2']) {
			$bulk_action = 'download-zip';
		}

		if ( 'download-zip' !== $bulk_action ) {
			return;
		}

		if ( empty( $_REQUEST['cb_attachment'] ) ) {
			return;
		}

		/*
		 * Create unique local names to handle the case where the same file name
		 * appears in multiple year/month/ directories.
		 */
		$file_names = array();
		foreach ( $_REQUEST['cb_attachment'] as $index => $post_id ) {
			$file_name = get_attached_file( $post_id );
			$path_info = pathinfo( $file_name  );
			$local_name = $path_info['basename'];
			$suffix = 0;
			while( array_key_exists( $local_name, $file_names ) ) {
				$suffix++;
				$local_name = $path_info['filename'] . $suffix . '.' . $path_info['extension'];
			}

			$file_names[ $local_name ] = $file_name;
		}

		/*
		 * Create the ZIP archive
		 */
		$upload_dir = wp_upload_dir();
		$prefix = ( defined( MLA_OPTION_PREFIX ) ) ? MLA_OPTION_PREFIX : 'mla_';
		$date = date("Ymd_B");
		$archive_name = $upload_dir['basedir'] . '/' . "{$prefix}_options_{$date}.zip";

		if ( file_exists( $archive_name ) ) {
			@unlink( $archive_name );
		}

		$zip = new ZipArchive();
		if ( true !== $zip->open( $archive_name, ZIPARCHIVE::CREATE ) ) {
			/* translators: 1: ZIP archive file name */
			$_REQUEST['mla_admin_message'] = sprintf( __( 'ERROR: The ZIP archive ( %1$s ) could not be created.', 'mla-zip-archive-example' ), $archive_name );
			return;
		}

		foreach( $file_names as $local_name => $file_name ) {
			if ( true !== $zip->addFile( $file_name, $local_name ) ) {
				/* translators: 1: ZIP archive file name */
				$_REQUEST['mla_admin_message'] = sprintf( __( 'ERROR: The file ( %1$s ) could not be added to the ZIP archive.', 'mla-zip-archive-example' ), $file_name );
				return;
			}
		}

		if ( true !== $zip->close() ) {
			/* translators: 1: ZIP archive file name */
			$_REQUEST['mla_admin_message'] = sprintf( __( 'ERROR: The ZIP archive ( %1$s ) could not be closed.', 'mla-zip-archive-example' ), $archive_name );
			return;
		}

		$download_args = array( 'page' => MLA::ADMIN_PAGE_SLUG, 'mla_download_file' => urlencode( $archive_name ), 'mla_download_type' => 'application/zip', 'mla_download_disposition' => 'delete' );

		wp_redirect( add_query_arg( $download_args, wp_nonce_url( 'upload.php', MLA::MLA_ADMIN_NONCE ) ), 302 );
		exit;
	} // admin_init_action

	/**
	 * Filter the MLA_List_Table bulk actions
	 *
	 * This MLA-specific filter gives you an opportunity to filter the list of bulk actions;
	 * a good alternative to the 'bulk_actions-media_page_mla-menu' filter.
	 *
	 * @since 1.01
	 *
	 * @param	array	$actions	An array of bulk actions.
	 *								Format: 'slug' => 'Label'
	 *
	 * @return	array	updated array of actions.
	 */
	public static function mla_list_table_get_bulk_actions( $actions ) {
		//error_log( 'MLADownloadZIPExample::mla_list_table_get_bulk_actions $actions = ' . var_export( $actions, true ), 0 );

		$actions[ 'download-zip' ] = __( 'Download', 'mla-zip-archive-example' );
		return $actions;
	} // mla_list_table_get_bulk_actions
} // Class MLADownloadZIPExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLADownloadZIPExample::initialize');
?>