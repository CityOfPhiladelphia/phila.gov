<?php
/**
 * Plugin Name: MB Relationships
 * Plugin URI:  https://metabox.io/plugins/mb-relationships/
 * Description: Create many-to-many relationships between posts, users, terms, etc.
 * Version:     1.10.11
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * License:     GPL2+
 * Text Domain: mb-relationships
 * Domain Path: /languages/
 *
 * @package    Meta Box
 * @subpackage MB Relationships
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'MBR_Loader' ) ) {
	require __DIR__ . '/inc/loader.php';
	$loader = new MBR_Loader();

	// Create relationships table only when plugin is activated.
	register_activation_hook( __FILE__, array( $loader, 'activate' ) );

	// Hook to 'init' with priority 5 to make sure all actions are registered before Meta Box runs.
	add_action( 'init', array( $loader, 'init' ), 5 );
}
