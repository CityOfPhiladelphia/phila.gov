<?php

add_filter( 'map_meta_cap', 'wpcmsb_map_meta_cap', 10, 4 );

function wpcmsb_map_meta_cap( $caps, $cap, $user_id, $args ) {
	$meta_caps = array(
		'wpcmsb_edit_cms_block' => wpcmsb_ADMIN_READ_WRITE_CAPABILITY,
		'wpcmsb_edit_cms_blocks' => wpcmsb_ADMIN_READ_WRITE_CAPABILITY,
		'wpcmsb_read_cms_blocks' => wpcmsb_ADMIN_READ_CAPABILITY,
		'wpcmsb_delete_cms_block' => wpcmsb_ADMIN_READ_WRITE_CAPABILITY );

	$meta_caps = apply_filters( 'wpcmsb_map_meta_cap', $meta_caps );

	$caps = array_diff( $caps, array_keys( $meta_caps ) );

	if ( isset( $meta_caps[$cap] ) )
		$caps[] = $meta_caps[$cap];

	return $caps;
}

?>