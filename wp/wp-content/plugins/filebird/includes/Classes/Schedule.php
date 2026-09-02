<?php
namespace FileBird\Classes;

defined( 'ABSPATH' ) || exit;

class Schedule {
	public function __construct() {
		add_action( 'filebird_remove_zip_files', array( $this, 'actionRemoveZipFiles' ) );
	}

	public static function registerSchedule() {
		if ( ! wp_next_scheduled( 'filebird_remove_zip_files' ) ) {
			wp_schedule_event( time(), 'daily', 'filebird_remove_zip_files' );
		}
	}

	public static function clearSchedule() {
		wp_clear_scheduled_hook( 'filebird_remove_zip_files' );
	}

	public function actionRemoveZipFiles() {
		$saved_downloads = get_option( 'filebird_saved_downloads', array() );
		if ( ! is_array( $saved_downloads ) ) {
			$saved_downloads = array();
		}
		foreach ( $saved_downloads as $time => $path ) {
			if ( ( time() - $time ) >= ( 24 * 60 * 60 ) ) {
				$wp_dir = wp_upload_dir();
				if ( file_exists( $wp_dir['basedir'] . $path ) ) {
					unlink( $wp_dir['basedir'] . $path );
				}
				unset( $saved_downloads[ $time ] );
			}
		}
		update_option( 'filebird_saved_downloads', $saved_downloads );
	}
}
