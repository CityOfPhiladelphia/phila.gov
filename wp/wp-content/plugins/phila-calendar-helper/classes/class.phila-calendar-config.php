<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Phila_Calendar_Configuration{
	var $name = "Phila Calendar Helper";

	public function __construct(){
		add_action( 'plugins_loaded', array( $this, 'validate_plugin_on_loaded' ) );
	}

	public function show_requirements_failed_error(){
		$name = $this->name;
		add_action( 'admin_notices', function() use ( $name ) {
			echo '<div class="error"><p>' .
					sprintf( __( 'The %s plugin requires the Simple Calendar core plugin to be installed and activated.', PHILA_CALENDAR_DOMAIN ), $name ) .
					'</p></div>';
		} );
	}

	public function validate_plugin_on_loaded(){
		if( ! $this->check_requirements() )
		{
			$this->show_requirements_failed_error();
		}else{
			new Phila_Calendar_Options();
			new Phila_Calendar_Main();
		}
	}

	public function check_requirements(){
		if ( ! class_exists( 'SimpleCalendar\Plugin' ) ) {
			return false;
		}

		return true;
	}
}