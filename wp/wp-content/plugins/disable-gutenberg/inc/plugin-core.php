<?php // Plugin Core

if (!defined('ABSPATH')) exit;

function disable_gutenberg_get_options() {
	
	global $DisableGutenberg;
	
	$default = $DisableGutenberg->options();
	
	return get_option('disable_gutenberg_options', $default);
	
}

function disable_gutenberg_init() {
	
	if (disable_gutenberg()) disable_gutenberg_remove();
	
	disable_gutenberg_restore_widgets();
	
}

function disable_gutenberg($post_id = false) {
	
	if (is_plugin_active('classic-editor/classic-editor.php')) return false;
	
	if (disable_gutenberg_whitelist_id($post_id)) return false;
	
	if (disable_gutenberg_whitelist_slug($post_id)) return false;
	
	if (disable_gutenberg_whitelist_title($post_id)) return false;
	
	if (isset($_GET['block-editor'])) return false;
	
	if (isset($_GET['classic-editor'])) return true;
	
	if (isset($_POST['classic-editor'])) return true;
	
	if (disable_gutenberg_disable_all()) return true;
	
	if (disable_gutenberg_disable_user_role()) return true;
	
	if (disable_gutenberg_disable_post_type()) return true;
	
	if (disable_gutenberg_disable_templates()) return true;
	
	if (disable_gutenberg_disable_ids($post_id)) return true;
	
	return false;
	
}

function disable_gutenberg_get_current_user_roles($user = null) {
	
	if (is_user_logged_in()) {
		
		$user = $user ? new WP_User($user) : wp_get_current_user();
		
		return $user->roles ? (array) $user->roles : false;
		
	}
	
	return false;
	
}

function disable_gutenberg_get_user_roles() {
	
	if (!function_exists('get_editable_roles')) require_once ABSPATH .'wp-admin/includes/user.php';
	
	$roles = get_editable_roles();
	
	$types = array();
	
	foreach ($roles as $key => $value) {
		
		$label = isset($value['name']) ? $value['name'] : null;
		
		$types[$key]['name']  = $key;
		$types[$key]['label'] = $label;
		
	}
	
	return apply_filters('disable_gutenberg_get_user_roles', $types);
	
}

function disable_gutenberg_get_enabled_user_roles() {
	
	$options = disable_gutenberg_get_options();
	
	$array = array();
	
	foreach ($options as $key => $value) {
		
		preg_match('/^user-role_(.*)$/i', $key, $matches);
		
		if ($value && isset($matches[1]) && !empty($matches[1])) $array[] = $matches[1];
		
	}
	
	return apply_filters('disable_gutenberg_get_enabled_user_roles', $array);
	
}

function disable_gutenberg_get_post_types() {
	
	$post_types = get_post_types(array(), 'objects');
	
	$unset = array('attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block');
	
	$unset = apply_filters('disable_gutenberg_post_types_unset', $unset);
	
	$types = array();
	
	foreach($post_types as $key => $post_type) {
		
		$types[$key]['name']  = $post_type->name;
		$types[$key]['label'] = $post_type->label;
		
		if (in_array($post_type->name, $unset)) unset($types[$key]);
		
	}
	
	return apply_filters('disable_gutenberg_get_post_types', $types);
	
}

function disable_gutenberg_get_enabled_post_types() {
	
	$options = disable_gutenberg_get_options();
	
	$array = array();
	
	foreach ($options as $key => $value) {
		
		preg_match('/^post-type_(.*)$/i', $key, $matches);
		
		if ($value && isset($matches[1]) && !empty($matches[1])) $array[] = $matches[1];
		
	}
	
	return apply_filters('disable_gutenberg_get_enabled_post_types', $array);
	
}

function disable_gutenberg_disable_all() {
	
	$options = disable_gutenberg_get_options();
	
	$disable_all = (isset($options['disable-all']) && !empty($options['disable-all'])) ? true : false;
	
	if ($disable_all) return true;
	
	return false;
	
}

function disable_gutenberg_disable_user_role() {
	
	foreach (disable_gutenberg_get_enabled_user_roles() as $role) {
		
		$roles = disable_gutenberg_get_current_user_roles();
		
		if (is_array($roles) && in_array($role, $roles)) return true;
		
	}
	
	return false;
	
}

function disable_gutenberg_disable_post_type() {
	
	global $pagenow, $typenow;
	
	$post_type = null;
	
	if ($pagenow === 'edit.php') {
		
		// if (empty($typenow)) $typenow = 'post';
		
		$post_type = $typenow;
		
	} elseif ($pagenow === 'post.php') {
		
		$post_id = isset($_GET['post']) ? $_GET['post'] : null;
		
		$post_type = $post_id ? get_post_type($post_id) : null;
		
	} elseif ($pagenow === 'post-new.php') {
		
		if (empty($typenow)) $typenow = 'post';
		
		$post_type = isset($_GET['post_type']) ? $_GET['post_type'] : null;
		
		if (empty($post_type)) $post_type = $typenow;
		
	}
	
	if ($post_type) {
		
		foreach (disable_gutenberg_get_enabled_post_types() as $type) {
			
			if ($post_type === $type) return true;
			
		}
		
	}
	
	return false;
	
}

function disable_gutenberg_disable_templates() {
	
	$excluded = array();
	
	$disable = false;
	
	$template = null;
	
	$post_id = isset($_GET['post']) ? intval($_GET['post']) : null;
	
	$post_id = (empty($post_id) && isset($_POST['post_ID'])) ? $_POST['post_ID'] : $post_id;
	
	if (is_admin() && !empty($post_id)) {
		
		$options = disable_gutenberg_get_options();
		
		$excluded = isset($options['templates']) ? $options['templates'] : null;
		
		$excluded = array_map('trim', explode(',', $excluded));
		
		if (isset($_POST) && !empty($_POST)) {
			
			$template = isset($_POST['page_template']) ? $_POST['page_template'] : null;
			
		} else {
			
			$template = get_page_template_slug($post_id);
			
		}
		
	}
	
	if (!empty($excluded) && !empty($template)) {
		
		$disable = in_array($template, $excluded, true);
		
	}
	
	return $disable;
	
}

function disable_gutenberg_disable_ids($post_id = false) {
	
	$excluded = array();
	
	$post_id = disable_gutenberg_get_post_id($post_id);
	
	if (is_admin() && !empty($post_id)) {
		
		$options = disable_gutenberg_get_options();
		
		$excluded = isset($options['post-ids']) ? $options['post-ids'] : null;
		
		$excluded = array_map('trim', explode(',', $excluded));
		
	}
	
	return in_array($post_id, $excluded);
	
}

function disable_gutenberg_disable_nag() {
	
	$options = disable_gutenberg_get_options();
	
	$disable_nag = (isset($options['disable-nag']) && !empty($options['disable-nag'])) ? true : false;
	
	if ($disable_nag) remove_filter('try_gutenberg_panel', 'wp_try_gutenberg_panel'); // until WP 5.0 beta
	
}

function disable_gutenberg_menu_items() {
	
	$options = disable_gutenberg_get_options();
	
	$hide_plugin    = (isset($options['hide-menu']) && !empty($options['hide-menu'])) ? true : false;
	$hide_gutenberg = (isset($options['hide-gut'])  && !empty($options['hide-gut']))  ? true : false;
	
	if ($hide_plugin)    remove_submenu_page('options-general.php', 'disable-gutenberg');
	if ($hide_gutenberg) remove_menu_page('gutenberg');
	
}

function disable_gutenberg_acf_enable_meta() {
	
	$options = disable_gutenberg_get_options();
	
	$enable = isset($options['acf-enable']) ? $options['acf-enable'] : false;
	
	if ($enable) add_filter('acf/settings/remove_wp_meta_box', '__return_false');
	
}

function disable_gutenberg_privacy_notice() {
	
	global $pagenow;
	
	if ($pagenow !== 'post.php') return;
	
	if (!disable_gutenberg()) return;
	
	remove_action('admin_notices', array('WP_Privacy_Policy_Content', 'notice'));
	
	add_action('edit_form_after_title', array('WP_Privacy_Policy_Content', 'notice'));
	
}

function disable_gutenberg_whitelist($post_id = false) {
	
	if (disable_gutenberg_whitelist_id($post_id)) return true;
	
	if (disable_gutenberg_whitelist_slug($post_id)) return true;
	
	if (disable_gutenberg_whitelist_title($post_id)) return true;
	
	return false;
	
}

function disable_gutenberg_whitelist_id($post_id = false) {
	
	$whitelist = array();
	
	$post_id = disable_gutenberg_get_post_id($post_id);
	
	if (!empty($post_id)) {
		
		$options = disable_gutenberg_get_options();
		
		$whitelist = isset($options['whitelist-id']) ? $options['whitelist-id'] : null;
		
		$whitelist = disable_gutenberg_explode($whitelist);
		
	}
	
	return in_array($post_id, $whitelist);
	
}

function disable_gutenberg_whitelist_slug($post_id = false) {
	
	$whitelist = array();
	
	$slug = false;
	
	$post_id = disable_gutenberg_get_post_id($post_id);
	
	$status = get_post_status($post_id);
	
	if (!empty($post_id) && $status === 'publish') {
		
		$post = get_post($post_id);
		
		$slug = $post->post_name;
		
		$options = disable_gutenberg_get_options();
		
		$whitelist = isset($options['whitelist-slug']) ? $options['whitelist-slug'] : null;
		
		$whitelist = disable_gutenberg_explode($whitelist);
		
	}
	
	return in_array($slug, $whitelist);
	
}

function disable_gutenberg_whitelist_title($post_id = false) {
	
	$whitelist = array();
	
	$title = false;
	
	$post_id = disable_gutenberg_get_post_id($post_id);
	
	if (!empty($post_id)) {
		
		$title = strtolower(get_the_title($post_id));
		
		$options = disable_gutenberg_get_options();
		
		$whitelist = isset($options['whitelist-title']) ? $options['whitelist-title'] : null;
		
		$whitelist = disable_gutenberg_explode($whitelist);
		
	}
	
	return (!empty($title) && in_array($title, $whitelist));
	
}

function disable_gutenberg_explode($string) {
	
	$explode = array_map('trim', explode(',', $string));
	
	$array = array();
	
	foreach ($explode as $item) {
		
		$array[] = strtolower($item);
		
	}
	
	return $array;
	
}

function disable_gutenberg_get_post_id($post_id = false) {
	
	if (empty($post_id)) {
		
		$post_id = isset($_GET['post']) ? intval($_GET['post']) : null;
		
	}
	
	return $post_id;
	
}

function disable_gutenberg_restore_widgets() {
	
	$restore = false;
	
	$options = disable_gutenberg_get_options();
	
	if (isset($options['classic-widgets'])) {
		
		$restore = (!empty($options['classic-widgets'])) ? true : false;
		
	}
	
	if ($restore) {
		
		add_filter('gutenberg_use_widgets_block_editor', '__return_false');
		add_filter('use_widgets_block_editor', '__return_false');
		
	}
	
}
