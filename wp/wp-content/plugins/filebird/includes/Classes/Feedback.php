<?php
namespace FileBird\Classes;

defined( 'ABSPATH' ) || exit;

class Feedback {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_filebird_feedback' ) );
	}

	public function enqueue_filebird_feedback() {
		if ( ! in_array( get_current_screen()->id, array( 'plugins', 'plugins-network' ), true ) ) {
			return;
		}

		add_action( 'admin_footer', array( $this, 'form_feedback' ) );
	}

	public function form_feedback() {
		echo '<div id="fbv-feedback"></div>';
	}
}

