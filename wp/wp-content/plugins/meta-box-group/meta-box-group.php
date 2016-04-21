<?php
/**
 * Plugin Name: Meta Box Group
 * Plugin URI: https://metabox.io/plugins/meta-box-group/
 * Description: Add-on for meta box plugin, allows you to add field type 'group' which put child fields into 1 group which are displayed/accessed easier and can be cloneable.
 * Version: 1.0.7
 * Author: Rilwis
 * Author URI: http://www.deluxeblogtips.com
 * License: GPL2+
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

/**
 * Extension main class.
 */
class RWMB_Group
{
	/**
	 * Indicate that the meta box is saved or not.
	 * This variable is used inside group field to show child fields.
	 *
	 * @var bool
	 */
	static $saved = false;

	/**
	 * Add hooks to meta box.
	 */
	public function __construct()
	{
		add_action( 'plugins_loaded', array( $this, 'load_files' ) );

		add_action( 'rwmb_before', array( $this, 'set_saved' ) );
		add_action( 'rwmb_after', array( $this, 'unset_saved' ) );
	}

	/**
	 * Load field group class.
	 */
	public function load_files()
	{
		if ( class_exists( 'RWMB_Field' ) && ! class_exists( 'RWMB_Group_Field' ) )
		{
			require_once plugin_dir_path( __FILE__ ) . 'class-rwmb-group-field.php';
		}
	}

	/**
	 * Check if current meta box is saved.
	 * This variable is used inside group field to show child fields.
	 *
	 * @param object $obj Meta Box object
	 */
	public function set_saved( $obj )
	{
		self::$saved = $obj->is_saved();
	}

	/**
	 * Unset 'saved' variable, to be ready for next meta box.
	 */
	public function unset_saved()
	{
		self::$saved = false;
	}
}

new RWMB_Group;
