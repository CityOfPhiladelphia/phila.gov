<?php // Frontend stuff..

function disable_gutenberg_wp_enqueue_scripts() {
	
	global $wp_query;
	
	$post_id = isset($wp_query->post->ID) ? $wp_query->post->ID : null;
	
	$options = get_option('disable_gutenberg_options');
	
	$enable = isset($options['styles-enable']) ? $options['styles-enable'] : false;
	
	if (!$enable && !disable_gutenberg_whitelist($post_id)) {
		
		wp_dequeue_style('wp-block-library');
		wp_dequeue_style('wp-block-library-theme');
		
	}
	
}
