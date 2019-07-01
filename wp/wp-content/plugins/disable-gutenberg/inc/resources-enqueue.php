<?php // Enqueue Resources

if (!defined('ABSPATH')) exit;

function disable_gutenberg_admin_enqueue_scripts() {
	
	$screen = get_current_screen();
	
	if (!is_object($screen)) $screen = new stdClass();
	
	if (!property_exists($screen, 'id')) return;
	
	if ($screen->id === 'settings_page_disable-gutenberg') {
		
		wp_enqueue_style('wp-jquery-ui-dialog');
		
		wp_enqueue_style('disable-gutenberg-font-icons', DISABLE_GUTENBERG_URL .'css/font-icons.css', array(), DISABLE_GUTENBERG_VERSION);
		
		wp_enqueue_style('disable-gutenberg-settings', DISABLE_GUTENBERG_URL .'css/settings.css', array(), DISABLE_GUTENBERG_VERSION);
		
		$js_deps = array('jquery', 'jquery-ui-core', 'jquery-ui-dialog');
		
		wp_enqueue_script('disable-gutenberg-settings', DISABLE_GUTENBERG_URL .'js/settings.js', $js_deps, DISABLE_GUTENBERG_VERSION);
		
	}
	
}

function disable_gutenberg_admin_print_scripts() { ?>
		
	<script type="text/javascript">
		var 
		alert_reset_options_title   = '<?php _e('Confirm Reset',            'disable-gutenberg'); ?>',
		alert_reset_options_message = '<?php _e('Restore default options?', 'disable-gutenberg'); ?>',
		alert_reset_options_true    = '<?php _e('Yes, make it so.',         'disable-gutenberg'); ?>',
		alert_reset_options_false   = '<?php _e('No, abort mission.',       'disable-gutenberg'); ?>';
	</script>
	
<?php 
}
