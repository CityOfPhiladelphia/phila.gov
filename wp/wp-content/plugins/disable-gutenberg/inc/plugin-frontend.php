<?php // Frontend stuff..

function disable_gutenberg_wp_enqueue_scripts() {
	
	global $wp_query;
	
	if (is_admin()) return;
	
	$post_id = isset($wp_query->post->ID) ? $wp_query->post->ID : null;
	
	$options = get_option('disable_gutenberg_options');
	
	$enable = isset($options['styles-enable']) ? $options['styles-enable'] : false;
	
	if (!$enable && !disable_gutenberg_whitelist($post_id)) {
		
		// blocks
		wp_dequeue_style('wp-block-library');
		wp_dequeue_style('wp-block-library-theme');
		
		// theme.json
		wp_dequeue_style('global-styles');
		
		// svg
		remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
		remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
		
	}
	
}