<?php

add_action( 'wpcmsb_upgrade', 'wpcmsb_convert_to_cpt', 10, 2 );

function wpcmsb_convert_to_cpt( $new_ver, $old_ver ) {
	global $wpdb;

	if ( ! version_compare( $old_ver, '3.0-dev', '<' ) )
		return;

	$old_rows = array();

	$table_name = $wpdb->prefix . "cms_block_7";

	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) ) {
		$old_rows = $wpdb->get_results( "SELECT * FROM $table_name" );
	} elseif ( ( $opt = get_option( 'wpcmsb' ) ) && ! empty( $opt['cms_blocks'] ) ) {
		foreach ( (array) $opt['cms_blocks'] as $key => $value ) {
			$old_rows[] = (object) array_merge( $value, array( 'cf7_unit_id' => $key ) );
		}
	}

	foreach ( (array) $old_rows as $row ) {
		$q = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_old_cf7_unit_id'"
			. $wpdb->prepare( " AND meta_value = %d", $row->cf7_unit_id );

		if ( $wpdb->get_var( $q ) )
			continue;

		$postarr = array(
			'post_type' => 'wpcmsb_cms_block',
			'post_status' => 'publish',
			'post_title' => maybe_unserialize( $row->title ) );

		$post_id = wp_insert_post( $postarr );

		if ( $post_id ) {
			update_post_meta( $post_id, '_old_cf7_unit_id', $row->cf7_unit_id );

			$metas = array( 'form', 'messages');

			foreach ( $metas as $meta ) {
				update_post_meta( $post_id, '_' . $meta,
					wpcmsb_normalize_newline_deep( maybe_unserialize( $row->{$meta} ) ) );
			}
		}
	}
}

add_action( 'wpcmsb_upgrade', 'wpcmsb_prepend_underscore', 10, 2 );

function wpcmsb_prepend_underscore( $new_ver, $old_ver ) {
	if ( version_compare( $old_ver, '3.0-dev', '<' ) )
		return;

	if ( ! version_compare( $old_ver, '3.3-dev', '<' ) )
		return;

	$posts = wpcmsb_cmsblock::find( array(
		'post_status' => 'any',
		'posts_per_page' => -1 ) );

	foreach ( $posts as $post ) {
		$props = $post->get_properties();

		foreach ( $props as $prop => $value ) {
			if ( metadata_exists( 'post', $post->id(), '_' . $prop ) )
				continue;

			update_post_meta( $post->id(), '_' . $prop, $value );
			delete_post_meta( $post->id(), $prop );
		}
	}
}

?>