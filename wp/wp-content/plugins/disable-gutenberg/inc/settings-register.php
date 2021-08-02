<?php // Register Settings

if (!defined('ABSPATH')) exit;

function disable_gutenberg_register_settings() {
	
	// register_setting( $option_group, $option_name, $sanitize_callback );
	register_setting('disable_gutenberg_options', 'disable_gutenberg_options', 'disable_gutenberg_validate_options');
	
	// add_settings_section( $id, $title, $callback, $page ); 
	add_settings_section('settings_1', 'Complete Disable',       'disable_gutenberg_settings_section_1', 'disable_gutenberg_options');
	add_settings_section('settings_2', 'Disable for User Roles', 'disable_gutenberg_settings_section_2', 'disable_gutenberg_options');
	add_settings_section('settings_3', 'Disable for Post Types', 'disable_gutenberg_settings_section_3', 'disable_gutenberg_options');
	add_settings_section('settings_4', 'Disable for Templates',  'disable_gutenberg_settings_section_4', 'disable_gutenberg_options');
	add_settings_section('settings_5', 'Disable for Post IDs',   'disable_gutenberg_settings_section_5', 'disable_gutenberg_options');
	add_settings_section('settings_6', 'Whitelist',              'disable_gutenberg_settings_section_6', 'disable_gutenberg_options');
	add_settings_section('settings_7', 'More Tools',             'disable_gutenberg_settings_section_7', 'disable_gutenberg_options');
	
	// add_settings_field( $id, $title, $callback, $page, $section, $args );
	add_settings_field('disable-all', __('Complete Disable', 'disable-gutenberg'), 'disable_gutenberg_callback_checkbox', 'disable_gutenberg_options', 'settings_1', array('id' => 'disable-all', 'label' => esc_html__('Disable Gutenberg everywhere', 'disable-gutenberg')));
	
	foreach(disable_gutenberg_get_user_roles() as $type) {
		
		extract($type); // name label
		
		add_settings_field('user-role_'. $name, esc_html__('Disable for', 'disable-gutenberg') .' '. $label, 'disable_gutenberg_callback_checkbox', 'disable_gutenberg_options', 'settings_2', array('id' => 'user-role_'. $name, 'label' => esc_html__('User Role =', 'disable-gutenberg') .' '. $name));
		
	}
	
	foreach(disable_gutenberg_get_post_types() as $type) {
		
		extract($type); // name label
		
		add_settings_field('post-type_'. $name, esc_html__('Disable for', 'disable-gutenberg') .' '. $label, 'disable_gutenberg_callback_checkbox', 'disable_gutenberg_options', 'settings_3', array('id' => 'post-type_'. $name, 'label' => esc_html__('Post Type =', 'disable-gutenberg') .' '. $name));
		
	}
	
	add_settings_field('templates', __('Disable Templates', 'disable-gutenberg'), 'disable_gutenberg_callback_text', 'disable_gutenberg_options', 'settings_4', array('id' => 'templates', 'label' => esc_html__('Separate multiple templates with commas', 'disable-gutenberg')));
	add_settings_field('post-ids',  __('Disable Post IDs',  'disable-gutenberg'), 'disable_gutenberg_callback_text', 'disable_gutenberg_options', 'settings_5', array('id' => 'post-ids',  'label' => esc_html__('Separate multiple post IDs with commas',  'disable-gutenberg')));
	
	add_settings_field('whitelist-id',    __('Whitelist Post IDs',    'disable-gutenberg'), 'disable_gutenberg_callback_text', 'disable_gutenberg_options', 'settings_6', array('id' => 'whitelist-id',    'label' => esc_html__('Post IDs that always should use the Block Editor', 'disable-gutenberg')));
	add_settings_field('whitelist-slug',  __('Whitelist Post Slugs',  'disable-gutenberg'), 'disable_gutenberg_callback_text', 'disable_gutenberg_options', 'settings_6', array('id' => 'whitelist-slug',  'label' => esc_html__('Post slugs that always should use the Block Editor', 'disable-gutenberg')));
	add_settings_field('whitelist-title', __('Whitelist Post Titles', 'disable-gutenberg'), 'disable_gutenberg_callback_text', 'disable_gutenberg_options', 'settings_6', array('id' => 'whitelist-title', 'label' => esc_html__('Post titles that always should use the Block Editor', 'disable-gutenberg')));
	
	add_settings_field('classic-widgets', __('Classic Widgets',     'disable-gutenberg'), 'disable_gutenberg_callback_checkbox', 'disable_gutenberg_options', 'settings_7', array('id' => 'classic-widgets', 'label' => esc_html__('Disable Block Widgets and enable Classic Widgets', 'disable-gutenberg')));
	add_settings_field('disable-nag',     __('Disable Nag',         'disable-gutenberg'), 'disable_gutenberg_callback_checkbox', 'disable_gutenberg_options', 'settings_7', array('id' => 'disable-nag',     'label' => esc_html__('Disable "Try Gutenberg" nag', 'disable-gutenberg')));
	add_settings_field('styles-enable',   __('Enable Frontend',     'disable-gutenberg'), 'disable_gutenberg_callback_checkbox', 'disable_gutenberg_options', 'settings_7', array('id' => 'styles-enable',   'label' => esc_html__('Enable frontend Gutenberg styles', 'disable-gutenberg')));
	add_settings_field('whitelist',       __('Whitelist Options',   'disable-gutenberg'), 'disable_gutenberg_callback_checkbox', 'disable_gutenberg_options', 'settings_7', array('id' => 'whitelist',       'label' => esc_html__('Display Whitelist settings', 'disable-gutenberg')));
	add_settings_field('hide-menu',       __('Plugin Menu Item',    'disable-gutenberg'), 'disable_gutenberg_callback_checkbox', 'disable_gutenberg_options', 'settings_7', array('id' => 'hide-menu',       'label' => esc_html__('Hide this plugin&rsquo;s menu item', 'disable-gutenberg')));
	add_settings_field('hide-gut',        __('Gutenberg Menu Item', 'disable-gutenberg'), 'disable_gutenberg_callback_checkbox', 'disable_gutenberg_options', 'settings_7', array('id' => 'hide-gut',        'label' => esc_html__('Hide Gutenberg plugin&rsquo;s menu item (for WP &lt; 5.0)', 'disable-gutenberg')));
	add_settings_field('links-enable',    __('Display Edit Links',  'disable-gutenberg'), 'disable_gutenberg_callback_checkbox', 'disable_gutenberg_options', 'settings_7', array('id' => 'links-enable',    'label' => esc_html__('Display "Add New (Classic)" menu link and Classic/Block edit links', 'disable-gutenberg')));
	add_settings_field('acf-enable',      __('ACF Support',         'disable-gutenberg'), 'disable_gutenberg_callback_checkbox', 'disable_gutenberg_options', 'settings_7', array('id' => 'acf-enable',      'label' => esc_html__('Enable Custom Fields Meta Box (ACF disables by default),', 'disable-gutenberg') .' <a target="_blank" rel="noopener noreferrer" href="https://m0n.co/acf">'. esc_html__('learn more', 'disable-gutenberg') .'</a>'));
	add_settings_field('reset_options',   __('Reset Options',       'disable-gutenberg'), 'disable_gutenberg_callback_reset',    'disable_gutenberg_options', 'settings_7', array('id' => 'reset_options',   'label' => esc_html__('Restore default plugin options', 'disable-gutenberg')));
	add_settings_field('rate_plugin',     __('Rate Plugin',         'disable-gutenberg'), 'disable_gutenberg_callback_rate',     'disable_gutenberg_options', 'settings_7', array('id' => 'rate_plugin',     'label' => esc_html__('Show support with a 5-star rating&nbsp;&raquo;', 'disable-gutenberg')));
	
}

function disable_gutenberg_validate_options($input) {
	
	$types = disable_gutenberg_get_post_types();
	
	foreach ($types as $type) {
		
		extract($type); // name label
		
		if (is_array($input) && in_array($name, $input)) {
			
			if (!isset($input['post-type_'. $name])) $input['post-type_'. $name] = null;
			$input['post-type_'. $name] = ($input['post-type_'. $name] == 1 ? 1 : 0);
			
		}
		
	}
	
	$roles = disable_gutenberg_get_user_roles();
	
	foreach ($roles as $type) {
		
		extract($type); // name label
		
		if (is_array($input) && in_array($name, $input)) {
			
			if (!isset($input['user-role_'. $name])) $input['user-role_'. $name] = null;
			$input['user-role_'. $name] = ($input['user-role_'. $name] == 1 ? 1 : 0);
			
		}
		
	}
	
	if (isset($input['templates'])) $input['templates'] = sanitize_text_field(trim($input['templates']));
	if (isset($input['post-ids']))  $input['post-ids']  = sanitize_text_field(trim($input['post-ids']));
	
	if (isset($input['whitelist-id']))    $input['whitelist-id']    = sanitize_text_field(trim($input['whitelist-id']));
	if (isset($input['whitelist-slug']))  $input['whitelist-slug']  = sanitize_text_field(trim($input['whitelist-slug']));
	if (isset($input['whitelist-title'])) $input['whitelist-title'] = sanitize_text_field(trim($input['whitelist-title']));
	
	if (!isset($input['disable-all'])) $input['disable-all'] = null;
	$input['disable-all'] = ($input['disable-all'] == 1 ? 1 : 0);
	
	if (!isset($input['classic-widgets'])) $input['classic-widgets'] = null;
	$input['classic-widgets'] = ($input['classic-widgets'] == 1 ? 1 : 0);
	
	if (!isset($input['disable-nag'])) $input['disable-nag'] = null;
	$input['disable-nag'] = ($input['disable-nag'] == 1 ? 1 : 0);
	
	if (!isset($input['styles-enable'])) $input['styles-enable'] = null;
	$input['styles-enable'] = ($input['styles-enable'] == 1 ? 1 : 0);
	
	if (!isset($input['whitelist'])) $input['whitelist'] = null;
	$input['whitelist'] = ($input['whitelist'] == 1 ? 1 : 0);
	
	if (!isset($input['hide-menu'])) $input['hide-menu'] = null;
	$input['hide-menu'] = ($input['hide-menu'] == 1 ? 1 : 0);
	
	if (!isset($input['hide-gut'])) $input['hide-gut'] = null;
	$input['hide-gut'] = ($input['hide-gut'] == 1 ? 1 : 0);
	
	if (!isset($input['links-enable'])) $input['links-enable'] = null;
	$input['links-enable'] = ($input['links-enable'] == 1 ? 1 : 0);
	
	if (!isset($input['acf-enable'])) $input['acf-enable'] = null;
	$input['acf-enable'] = ($input['acf-enable'] == 1 ? 1 : 0);
	
	return $input;
	
}

function disable_gutenberg_settings_section_1() {
	
	echo '<p class="g7g-display">'. esc_html__('Enable this setting to completely disable Gutenberg (and restore the Classic Editor). Or, disable this setting to display more options.', 'disable-gutenberg') .'</p>';
	
}

function disable_gutenberg_settings_section_2() {
	
	echo '<p>'. esc_html__('Select the user roles for which Gutenberg should be disabled.', 'disable-gutenberg') .'</p>';
	
}

function disable_gutenberg_settings_section_3() {
	
	echo '<p>'. esc_html__('Select the post types for which Gutenberg should be disabled.', 'disable-gutenberg') .'</p>';
	
}

function disable_gutenberg_settings_section_4() {
	
	echo '<p>'. esc_html__('Select the theme template files for which Gutenberg should be disabled (e.g., custom-page.php).', 'disable-gutenberg') .'</p>';
	
}

function disable_gutenberg_settings_section_5() {
	
	echo '<p>'. esc_html__('Select the post IDs for which Gutenberg should be disabled.', 'disable-gutenberg') .'</p>';
	
}

function disable_gutenberg_settings_section_6() {
	
	echo '<p class="g7g-display g7g-whitelist">'. esc_html__('Select the posts that always should use the Gutenberg Block Editor. Separate multiple values with commas.', 'disable-gutenberg') .'</p>';
	
}

function disable_gutenberg_settings_section_7() {
	
	echo '<p class="g7g-display"><span class="fa fa-pad-more fa-gear"></span><strong><a class="g7g-toggle" href="#more-tools" title="'. esc_attr__('Toggle More Tools', 'disable-gutenberg') .'">'. esc_html__('Click here', 'disable-gutenberg') .'</a></strong> '. esc_html__('to display more tools and options. Note: these options remain in effect even when hidden on this page.', 'disable-gutenberg') .'</p>';
	
}

function disable_gutenberg_callback_text($args) {
	
	$options = disable_gutenberg_get_options();
	
	$id    = isset($args['id'])    ? $args['id']    : '';
	$label = isset($args['label']) ? $args['label'] : '';
	$value = isset($options[$id])  ? $options[$id]  : '';
	
	$name = 'disable_gutenberg_options['. $id .']';
	
	echo '<input id="'. esc_attr($name) .'" name="'. esc_attr($name) .'" type="text" size="40" class="regular-text" value="'. esc_attr($value) .'">';
	echo '<label for="'. esc_attr($name) .'">'. esc_html($label) .'</label>';
	
}

function disable_gutenberg_callback_textarea($args) {
	
	$options = disable_gutenberg_get_options();
	
	$allowed_tags = wp_kses_allowed_html('post');
	
	$id    = isset($args['id'])    ? $args['id']    : '';
	$label = isset($args['label']) ? $args['label'] : '';
	$value = isset($options[$id])  ? $options[$id]  : '';
	
	$name = 'disable_gutenberg_options['. $id .']';
	
	echo '<textarea id="'. esc_attr($name) .'" name="'. esc_attr($name) .'" rows="3" cols="50" class="large-text code">'. wp_kses(stripslashes_deep($value), $allowed_tags) .'</textarea>';
	echo '<label for="'. esc_attr($name) .'">'. esc_html($label) .'</label>';
	
}

function disable_gutenberg_callback_checkbox($args) {
	
	$options = disable_gutenberg_get_options();
	
	$id    = isset($args['id'])    ? $args['id']    : '';
	$label = isset($args['label']) ? $args['label'] : '';
	$value = isset($options[$id])  ? $options[$id]  : '';
	
	$name = 'disable_gutenberg_options['. $id .']';
	
	echo '<input id="'. esc_attr($name) .'" name="'. esc_attr($name) .'" type="checkbox" '. checked($value, 1, false) .' value="1"> ';
	echo '<label for="'. esc_attr($name) .'" class="inline-block">'. $label .'</label>';
	
}

function disable_gutenberg_callback_reset($args) {
	
	$nonce = wp_create_nonce('disable_gutenberg_reset_options');
	
	$href  = add_query_arg(array('reset-options-verify' => $nonce), admin_url('options-general.php?page=disable-gutenberg'));
	
	$label = isset($args['label']) ? $args['label'] : esc_html__('Restore default plugin options', 'disable-gutenberg');
	
	echo '<a class="disable-gutenberg-reset-options" href="'. esc_url($href) .'">'. esc_html($label) .'</a>';
	
}

function disable_gutenberg_callback_rate($args) {
	
	$href  = 'https://wordpress.org/support/plugin/'. DISABLE_GUTENBERG_SLUG .'/reviews/?rate=5#new-post';
	$title = esc_attr__('Please give a 5-star rating! A huge THANK YOU for your support!', 'disable-gutenberg');
	$text  = isset($args['label']) ? $args['label'] : esc_html__('Show support with a 5-star rating&nbsp;&raquo;', 'disable-gutenberg');
	
	echo '<a target="_blank" rel="noopener noreferrer" class="disable-gutenberg-rate-plugin" href="'. $href .'" title="'. $title .'">'. $text .'</a>';
	
}
