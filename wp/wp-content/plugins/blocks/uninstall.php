<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

function wpcmsb_delete_plugin() {
	global $wpdb;

	delete_option( 'wpcmsb' );

	$posts = get_posts( array(
		'numberposts' => -1,
		'post_type' => 'wpcmsb_cms_block',
		'post_status' => 'any' ) );

	foreach ( $posts as $post )
		wp_delete_post( $post->ID, true );

	$table_name = $wpdb->prefix . "cms_block_7";

	$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}

wpcmsb_delete_plugin();

?>