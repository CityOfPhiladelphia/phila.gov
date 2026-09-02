<?php
namespace FileBird;

defined( 'ABSPATH' ) || exit;

use FileBird\Classes\Review;
use FileBird\Classes\Schedule as FilebirdSchedule;
use FileBird\Install;
/**
 * Plugin activate/deactivate logic
 */
class Plugin {
	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
	}

	public static function prepareRun() {
		$current_version = get_option( 'fbv_version' );
		if ( version_compare( NJFB_VERSION, $current_version, '>' ) ) {
			self::activate();
			update_option( 'fbv_version', NJFB_VERSION );
			Review::update_time_display();
		}
	}

	/** Plugin activated hook */
	public static function activate() {
		$first_time_active = get_option( 'fbv_first_time_active' );
		if ( $first_time_active === false ) {
			update_option( 'fbv_is_new_user', 1 );
			update_option( 'fbv_first_time_active', 1 );
		}
		Install::create_tables();
		FilebirdSchedule::clearSchedule();
	}

	/** Plugin deactivate hook */
	public static function deactivate() {
		FilebirdSchedule::clearSchedule();
	}
}
