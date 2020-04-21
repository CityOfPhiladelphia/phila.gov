<?php 
/*
	Plugin Name: Disable Gutenberg
	Plugin URI: https://perishablepress.com/disable-gutenberg/
	Description: Disables Gutenberg Block Editor and restores the Classic Editor and original Edit Post screen. Provides options to enable on specific post types, user roles, and more.
	Tags: editor, classic editor, block editor, block-editor, gutenberg, disable, blocks, posts, post types
	Author: Jeff Starr
	Author URI: https://plugin-planet.com/
	Donate link: https://monzillamedia.com/donate.html
	Contributors: specialk
	Requires at least: 4.9
	Tested up to: 5.4
	Stable tag: 2.1
	Version: 2.1
	Requires PHP: 5.6.20
	Text Domain: disable-gutenberg
	Domain Path: /languages
	License: GPL v2 or later
*/

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 
	2 of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	with this program. If not, visit: https://www.gnu.org/licenses/
	
	Copyright 2020 Monzilla Media. All rights reserved.
*/

if (!defined('ABSPATH')) die();

if (!class_exists('DisableGutenberg')) {
	
	class DisableGutenberg {
		
		function __construct() {
			
			$this->constants();
			$this->includes();
			
			add_action('admin_init',          array($this, 'check_version'));
			add_action('plugins_loaded',      array($this, 'load_i18n'));
			add_filter('plugin_action_links', array($this, 'action_links'), 10, 2);
			add_filter('plugin_row_meta',     array($this, 'plugin_links'), 10, 2);
			
			add_action('admin_enqueue_scripts', 'disable_gutenberg_admin_enqueue_scripts');
			add_action('admin_print_scripts',   'disable_gutenberg_admin_print_scripts');
			add_action('admin_notices',         'disable_gutenberg_admin_notice');
			add_action('admin_init',            'disable_gutenberg_register_settings');
			add_action('admin_init',            'disable_gutenberg_reset_options');
			add_action('admin_menu',            'disable_gutenberg_menu_pages');
			add_action('admin_menu',            'disable_gutenberg_menu_items', 999);
			add_action('admin_init',            'disable_gutenberg_acf_enable_meta');
			add_action('admin_init',            'disable_gutenberg_privacy_notice');
			add_filter('admin_init',            'disable_gutenberg_disable_nag');
			add_filter('admin_init',            'disable_gutenberg_init');
			
			add_filter('wp_enqueue_scripts', 'disable_gutenberg_wp_enqueue_scripts', 100);
			
		}
		
		function constants() {
			
			if (!defined('DISABLE_GUTENBERG_VERSION')) define('DISABLE_GUTENBERG_VERSION', '2.1');
			if (!defined('DISABLE_GUTENBERG_REQUIRE')) define('DISABLE_GUTENBERG_REQUIRE', '4.9');
			if (!defined('DISABLE_GUTENBERG_AUTHOR'))  define('DISABLE_GUTENBERG_AUTHOR',  'Jeff Starr');
			if (!defined('DISABLE_GUTENBERG_NAME'))    define('DISABLE_GUTENBERG_NAME',    __('Disable Gutenberg', 'disable-gutenberg'));
			if (!defined('DISABLE_GUTENBERG_HOME'))    define('DISABLE_GUTENBERG_HOME',    esc_url('https://perishablepress.com/disable-gutenberg/'));
			if (!defined('DISABLE_GUTENBERG_URL'))     define('DISABLE_GUTENBERG_URL',     plugin_dir_url(__FILE__));
			if (!defined('DISABLE_GUTENBERG_DIR'))     define('DISABLE_GUTENBERG_DIR',     plugin_dir_path(__FILE__));
			if (!defined('DISABLE_GUTENBERG_FILE'))    define('DISABLE_GUTENBERG_FILE',    plugin_basename(__FILE__));
			if (!defined('DISABLE_GUTENBERG_SLUG'))    define('DISABLE_GUTENBERG_SLUG',    basename(dirname(__FILE__)));
			
		}
		
		function includes() {
			
			require_once DISABLE_GUTENBERG_DIR .'inc/classic-editor.php';
			require_once DISABLE_GUTENBERG_DIR .'inc/plugin-core.php';
			require_once DISABLE_GUTENBERG_DIR .'inc/plugin-frontend.php';
			
			if (is_admin()) {
				
				require_once DISABLE_GUTENBERG_DIR .'inc/resources-enqueue.php';
				require_once DISABLE_GUTENBERG_DIR .'inc/settings-display.php';
				require_once DISABLE_GUTENBERG_DIR .'inc/settings-register.php';
				require_once DISABLE_GUTENBERG_DIR .'inc/settings-reset.php';
				
				if (version_compare($GLOBALS['wp_version'], '5.0-beta', '>')) {
					
					require_once DISABLE_GUTENBERG_DIR .'inc/plugin-features.php';
					
				}
				
			}
			
		}
		
		function options() {
			
			$options = array(
				
				'disable-all'     => 1,
				'disable-nag'     => 1,
				'hide-menu'       => 0,
				'hide-gut'        => 0,
				'templates'       => '',
				'post-ids'        => '',
				'acf-enable'      => 0,
				'links-enable'    => 0,
				'whitelist-id'    => '',
				'whitelist-slug'  => '',
				'whitelist-title' => '',
				'whitelist'       => 0,
				'styles-enable'   => 0,
				
			);
			
			$types = disable_gutenberg_get_post_types();
			
			foreach($types as $type) {
				
				extract($type); // name label
				
				$options['post-type_'. $name] = 1;
				
			}
			
			$roles = disable_gutenberg_get_user_roles();
			
			foreach($roles as $type) {
				
				extract($type); // name label
				
				$options['user-role_'. $name] = 1;
				
			}
			
			return apply_filters('disable_gutenberg_options', $options);
			
		}
		
		function action_links($links, $file) {
			
			if (($file === DISABLE_GUTENBERG_FILE) && (current_user_can('manage_options'))) {
				
				$settings = '<a href="'. admin_url('options-general.php?page=disable-gutenberg') .'">'. esc_html__('Settings', 'disable-gutenberg') .'</a>';
				
				array_unshift($links, $settings);
				
			}
			
			return $links;
			
		}
		
		function plugin_links($links, $file) {
			
			if ($file === DISABLE_GUTENBERG_FILE) {
				
				$home_href  = 'https://perishablepress.com/disable-gutenberg/';
				$home_title = esc_attr__('Plugin Homepage', 'disable-gutenberg');
				$home_text  = esc_html__('Homepage', 'disable-gutenberg');
				
				$links[] = '<a target="_blank" rel="noopener noreferrer" href="'. $home_href .'" title="'. $home_title .'">'. $home_text .'</a>';
				
				$rate_href  = 'https://wordpress.org/support/plugin/'. DISABLE_GUTENBERG_SLUG .'/reviews/?rate=5#new-post';
				$rate_title = esc_attr__('Click here to rate and review this plugin on WordPress.org', 'disable-gutenberg');
				$rate_text  = esc_html__('Rate this plugin', 'disable-gutenberg') .'&nbsp;&raquo;';
				
				$links[] = '<a target="_blank" rel="noopener noreferrer" href="'. $rate_href .'" title="'. $rate_title .'">'. $rate_text .'</a>';
				
			}
			
			return $links;
			
		}
		
		function check_version() {
			
			$wp_version = get_bloginfo('version');
			
			if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
				
				if (version_compare($wp_version, DISABLE_GUTENBERG_REQUIRE, '<')) {
					
					if (is_plugin_active(DISABLE_GUTENBERG_FILE)) {
						
						deactivate_plugins(DISABLE_GUTENBERG_FILE);
						
						$msg  = '<strong>'. DISABLE_GUTENBERG_NAME .'</strong> '. esc_html__('requires WordPress ', 'disable-gutenberg') . DISABLE_GUTENBERG_REQUIRE;
						$msg .= esc_html__(' or higher, and has been deactivated! ', 'disable-gutenberg');
						$msg .= esc_html__('Please return to the', 'disable-gutenberg') .' <a href="'. admin_url() .'">';
						$msg .= esc_html__('WP Admin Area', 'disable-gutenberg') .'</a> '. esc_html__('to upgrade WordPress and try again.', 'disable-gutenberg');
						
						wp_die($msg);
						
					}
					
				}
				
			}
			
		}
		
		function load_i18n() {
			
			load_plugin_textdomain('disable-gutenberg', false, DISABLE_GUTENBERG_DIR .'languages/');
			
		}
		
		function __clone() {
			
			_doing_it_wrong(__FUNCTION__, esc_html__('Sorry, pal!', 'disable-gutenberg'), DISABLE_GUTENBERG_VERSION);
			
		}
		
		function __wakeup() {
			
			_doing_it_wrong(__FUNCTION__, esc_html__('Sorry, pal!', 'disable-gutenberg'), DISABLE_GUTENBERG_VERSION);
			
		}
		
	}
	
	global $DisableGutenberg;
	
	$DisableGutenberg = new DisableGutenberg(); 
	
}
