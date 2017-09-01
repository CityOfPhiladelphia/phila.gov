<?php

require_once wpcmsb_PLUGIN_DIR . '/includes/functions.php';
require_once wpcmsb_PLUGIN_DIR . '/includes/formatting.php';
require_once wpcmsb_PLUGIN_DIR . '/includes/shortcodes.php';
require_once wpcmsb_PLUGIN_DIR . '/includes/capabilities.php';
require_once wpcmsb_PLUGIN_DIR . '/includes/cms-block-template.php';
require_once wpcmsb_PLUGIN_DIR . '/includes/cms-block.php';
require_once wpcmsb_PLUGIN_DIR . '/includes/upgrade.php';
require_once wpcmsb_PLUGIN_DIR . '/includes/integration.php';


require_once wpcmsb_PLUGIN_DIR . '/includes/options.php';
require_once wpcmsb_PLUGIN_DIR . '/includes/viewport.php';
require_once wpcmsb_PLUGIN_DIR . '/includes/login.php';
require_once wpcmsb_PLUGIN_DIR . '/includes/show-template.php';
require_once wpcmsb_PLUGIN_DIR . '/includes/less.php';
require_once wpcmsb_PLUGIN_DIR . '/includes/tools.php';

if ( is_admin() ) {
	require_once wpcmsb_PLUGIN_DIR . '/admin/admin.php';
} else {
	require_once wpcmsb_PLUGIN_DIR . '/includes/controller.php';
}

add_action( 'plugins_loaded', 'wpcmsb' );

function wpcmsb() {
	wpcmsb_load_textdomain();

	/* Shortcodes */
	add_shortcode( 'cms-block', 'wpcmsb_cms_block_tag_func' );
	add_shortcode( 'block', 'wpcmsb_cms_block_tag_func' );
	//var_dump ( ' Entro al Settings' );
}

add_action( 'init', 'wpcmsb_init' );

function wpcmsb_init() {
	wpcmsb_get_request_uri();
	wpcmsb_register_post_types();

	do_action( 'wpcmsb_init' );
}

add_action( 'admin_init', 'wpcmsb_upgrade' );

function wpcmsb_upgrade() {
	$opt = get_option( 'wpcmsb' );

	if ( ! is_array( $opt ) )
		$opt = array();

	$old_ver = isset( $opt['version'] ) ? (string) $opt['version'] : '0';
	$new_ver = wpcmsb_VERSION;

	if ( $old_ver == $new_ver )
		return;

	do_action( 'wpcmsb_upgrade', $new_ver, $old_ver );

	$opt['version'] = $new_ver;

	update_option( 'wpcmsb', $opt );
}

/* Install and default settings */

add_action( 'activate_' . wpcmsb_PLUGIN_BASENAME, 'wpcmsb_install' );

function wpcmsb_install() {
	if ( $opt = get_option( 'wpcmsb' ) )
		return;

	wpcmsb_load_textdomain();
	wpcmsb_register_post_types();
	wpcmsb_upgrade();

	if ( get_posts( array( 'post_type' => 'wpcmsb_cms_block' ) ) )
		return;

	$cms_block = wpcmsb_cmsblock::get_template( array(
		'title' => sprintf( __( 'popup', 'cms-block' ), 1 ) ) );

	$cms_block->save();
}
