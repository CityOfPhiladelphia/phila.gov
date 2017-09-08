<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Phila_Calendar_Options {
	var $current_post_types = array();

	public function __construct(){
		add_action( 'admin_menu', array( $this, 'plugin_custom_options' ), 10 );
		add_action( 'admin_init', array( $this, 'register_settings' ), 10 );
	}

	public function plugin_custom_options()
	{
		add_options_page( 
			'Phila Calendar',
			'Phila Calendar',
			'manage_options',
			'phc_options',
			array( $this, 'content_custom_options' ) 
		);
	}
 
	function register_settings(){
		register_setting( 
			'phc_plugin_options',
			'phc_force_simple_calendar_admin',
			array( $this, 'check_checkbox_true' )
		);

		add_settings_section( 
			'phc_options_section',
			'Main Settings',
			false,
			'phc_options'
		);

		add_settings_field( 
			'phc_force_simple_calendar_admin',
			'Force Simple Calendar to be modified only by admin',
			array( $this, 'phc_checkboxes' ),
			'phc_options',
			'phc_options_section'
		);
	}

	function check_checkbox_true( $value ) {
		return ( $value == 1 ) ? 1 : 0;
	}

	 function phc_checkboxes( $args = array() ) {
		$checked = get_option( "phc_force_simple_calendar_admin", array() );
		?>
		<label>
			<input type="checkbox" name="phc_force_simple_calendar_admin" id="phc_force_simple_calendar_admin" value="1" <?php checked( $checked, 1 ); ?> />
			Yes
		</label>
		<?php
	}

	public function content_custom_options() {
	?>
		<div class="wrap">
			<h1>Phila calendar configuration</h1>
			<form method="post" action="options.php">
			<?php
				settings_fields( 'phc_plugin_options' );
 
				do_settings_sections( 'phc_options' );
				submit_button();
			?>
			</form>
		</div>
	<?php
	}
}