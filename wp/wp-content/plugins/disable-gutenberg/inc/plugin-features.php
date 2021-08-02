<?php // Plugin Features

if (!defined('ABSPATH')) exit;

// THANKS to Classic Editor for the inspiration for these "feature" functions.

function disable_gutenberg_add_submenus() {
	
	if (disable_gutenberg()) return;
	
	if (!disable_gutenberg_enable_features()) return;
	
	$types = array();
	
	$types = apply_filters('disable_gutenberg_submenu_types', $types);
	
	foreach (get_post_types(array('show_ui' => true)) as $type) {
		
		$type_obj = get_post_type_object($type);
		
		if (!$type_obj->show_in_menu || !post_type_supports($type, 'editor')) continue;
		
		if ($type_obj->show_in_menu === true) {
			
			if ('post' === $type) {
				
				$parent_slug = 'edit.php';
				
			} elseif ('page' === $type) {
				
				$parent_slug = 'edit.php?post_type=page';
				
			} elseif (in_array($type, $types)) {
				
				$parent_slug = 'edit.php?post_type='. $type;
				
			} else {
				
				continue;
				
			}
			
		} else {
			
			$parent_slug = $type_obj->show_in_menu;
			
		}
		
		$item_name = $type_obj->labels->add_new .' '. __('(Classic)', 'disable-gutenberg');
		
		add_submenu_page($parent_slug, $type_obj->labels->add_new, $item_name, $type_obj->cap->edit_posts, 'post-new.php?post_type='. $type .'&classic-editor');
		
	}
	
}
add_action('admin_menu', 'disable_gutenberg_add_submenus');



function disable_gutenberg_page_row_actions($actions, $post) {
	
	if (!disable_gutenberg_enable_features()) return $actions;
	
	if (disable_gutenberg_whitelist($post->ID)) return $actions;
	
	if (!array_key_exists('edit', $actions)) return $actions;
	
	if (array_key_exists('classic', $actions)) unset($actions['classic']);
	
	if ('trash' === $post->post_status || !post_type_supports($post->post_type, 'editor')) return $actions;
	
	$title = _draft_or_post_title($post->ID);
	
	$edit_url = get_edit_post_link($post->ID, 'raw');
	
	if (!$edit_url) return $actions;
	
	$url = remove_query_arg(array('block-editor', 'classic-editor'), $edit_url);
	
	//
	
	$block_url = add_query_arg('block-editor', '', $url);
	
	$block_text = __('Block Edit', 'disable-gutenberg');
	
	$block_label = sprintf(__('Edit &#8220;%s&#8221; in the Block Editor', 'disable-gutenberg'), $title);
	
	$block_action = sprintf('<a href="%s" aria-label="%s" title="%s">%s</a>', esc_url($block_url), esc_attr($block_label), esc_attr($block_label), esc_html($block_text));
	
	//
	
	$classic_url = add_query_arg('classic-editor', '', $url);
	
	$classic_text = __('Classic Edit', 'disable-gutenberg');
	
	$classic_label = sprintf(__('Edit &#8220;%s&#8221; in the Classic Editor', 'disable-gutenberg'), $title);
	
	$classic_action = sprintf('<a href="%s" aria-label="%s" title="%s">%s</a>', esc_url($classic_url), esc_attr($classic_label), esc_attr($classic_label), esc_html($classic_text));
	
	//
	
	$edit_offset = array_search('edit', array_keys($actions), true);
	
	array_splice($actions, $edit_offset, 1, $block_action);
	
	array_unshift($actions, $classic_action);
	
	return $actions;
	
}
add_filter('page_row_actions', 'disable_gutenberg_page_row_actions', 15, 2);
add_filter('post_row_actions', 'disable_gutenberg_page_row_actions', 15, 2);



function disable_gutenberg_get_edit_post_link($url) {
	
	if (!isset($_REQUEST['classic-editor']) && !disable_gutenberg()) return $url;
	
	$query = array();
	
	$parts = parse_url($url);
	
	if (isset($parts['query'])) parse_str($parts['query'], $query);

	$post_id = isset($query['post']) ? $query['post'] : false;
	
	if (disable_gutenberg_whitelist($post_id)) return $url;
	
	$url = add_query_arg('classic-editor', '', $url);
	
	return $url;
	
}
add_filter('get_edit_post_link', 'disable_gutenberg_get_edit_post_link');



function disable_gutenberg_redirect_post_location($location) {
	
	if (isset($_REQUEST['classic-editor']) || (isset($_POST['_wp_http_referer']) && strpos($_POST['_wp_http_referer'], '&classic-editor') !== false)) {
		
		if (disable_gutenberg()) {
			
			$location = add_query_arg('classic-editor', '', $location);
			
		} else {
			
			$location = remove_query_arg('classic-editor', $location);
			
		}
		
	}
	
	return $location;
	
}
add_filter('redirect_post_location', 'disable_gutenberg_redirect_post_location');



function disable_gutenberg_edit_form_top() {
	
	if (!isset($_GET['classic-editor']) && !disable_gutenberg()) return;
	
	?>
	
	<input type="hidden" name="classic-editor" value="1">
	
	<?php
	
}
add_action('edit_form_top', 'disable_gutenberg_edit_form_top');



function disable_gutenberg_enable_features() {
	
	$options = disable_gutenberg_get_options();
	
	$enable = (isset($options['links-enable']) && !empty($options['links-enable'])) ? true : false;
	
	return $enable;
	
}