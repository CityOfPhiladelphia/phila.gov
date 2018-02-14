<?php

if ( ! class_exists('Duplicate_Edit_And_Merge_Settings') )
{
	class Duplicate_Edit_And_Merge_Settings {
		private $settings;
		private $pageHook;

		public function __construct() {
			require_once dirname( __FILE__ ) . '/class.settings-api.php';
			$this->settings = cnSettingsAPI::getInstance();

			add_action( 'admin_menu', array( &$this , 'loadSettingsPage' ) );
			add_action( 'plugins_loaded', array( &$this , 'init') );

		}

		public function init() {
			/*
			 * Register the settings tabs shown on the Settings admin page tabs, sections and fields.
			 * Init the registered settings.
			 * NOTE: The init method must be run after registering the tabs, sections and fields.
			 */
			add_filter( 'cn_register_settings_tabs' , array( &$this , 'tabs' ) );
			add_filter( 'cn_register_settings_sections' , array( &$this , 'sections' ) );
			add_filter( 'cn_register_settings_fields' , array( &$this , 'fields' ) );
			$this->settings->init();
		}

		public function loadSettingsPage() {
			$this->pageHook = add_options_page( 'Duplicate & Merge Settings', 'Duplicate & Merge', 'manage_options', 'settings_dem', array( &$this , 'showPage' ) );
			/* add settings page */
			//$this->pageHook = add_menu_page( 'Dupe, Edit, Merge', 'Dupe, Edit, Merge', 'manage_options', 'settings_dem', array( &$this , 'showPage' ) );
		}

		public function register_my_custom_menu_page(){
		   // add_menu_page( 'custom menu title', 'custom menu', 'manage_options', 'myplugin/myplugin-admin.php', '', plugins_url( 'myplugin/images/icon.png' ), 6 );
		}

		public function tabs( $tabs ) {
			// Register the core tab banks.
			$tabs[] = array(
				'id' => 'basic' ,
				'position' => 10 ,
				'title' => __( 'Settings' , 'dem' ) ,
				'page_hook' => $this->pageHook
			);
			/*
			$tabs[] = array(
				'id' => 'other' ,
				'position' => 20 ,
				'title' => __( 'Other' , 'dem' ) ,
				'page_hook' => $this->pageHook
			);

			$tabs[] = array(
				'id' => 'advanced' ,
				'position' => 30 ,
				'title' => __( 'Advanced' , 'dem' ) ,
				'page_hook' => $this->pageHook
			);
			*/

			return $tabs;
		}

		public function sections( $sections ) {
			$sections[] = array(
				'tab' => 'basic' ,
				'id' => 'dem_main_settings' ,
				'position' => 10 ,
				'title' => __( 'Main Settings' , 'dem' ) ,
				'callback' => false,
				'page_hook' => $this->pageHook
			);
			//print_r($this->pageHook);
			/*
			$sections[] = array(
				'tab' => 'basic' ,
				'id' => 'basic_two' ,
				'position' => 20 ,
				'title' => __( 'Test Section Two' , 'dem' ) ,
				'callback' => create_function( '', "_e( 'Test Section Two Description.' , 'dem' );" ) ,
				'page_hook' => $this->pageHook
			);
			*/

			return $sections;
		}
		static function get_registered_post_types() {
		    global $wp_post_types;

		    return array_keys( $wp_post_types );

		}
		public function fields( $fields ) {
			global $wp_roles;
     		$roles = $wp_roles->get_names();
     		$args = array(
     		   'public'   => true,

     		);

     		$output = 'names'; // names or objects, note names is the default


     		$post_types = get_post_types( $args, $output );
     		//$post_types = self::get_registered_post_types();
     		unset($post_types['attachment']);
     		//print_r($roles);

			$fields[] = array(
				'plugin_id' => 'dem',
				'id' => 'notify_emails',
				'position' => 1,
				'page_hook' => 'settings_page_settings_dem',
				'tab' => 'basic',
				'section' => 'dem_main_settings',
				'title' => __('Admin Emails', 'dem'),
				'desc' => __('Enter notification emails one per line', 'dem'),
				'help' => __(''),
				'type' => 'textarea',
				'size' => 'large',
				'default' => 'your@email.com'
			);

			$fields[] = array(
				'plugin_id' => 'dem',
				'id' => 'edit_access',
				'position' => 4,
				'page_hook' => 'settings_page_settings_dem',
				'tab' => 'basic',
				'section' => 'dem_main_settings',
				'title' => __('Duplicate/Edit Access Level:', 'dem'),
				'desc' => __('What user types will <u>have access</u> to duplicate and edit posts (NOT MERGE)', 'dem'),
				'help' => __(''),
				'type' => 'multicheckbox',
				'options' => $roles,
				'default' => array( 'administrator' , 'editor', 'author' )
			);

			$fields[] = array(
				'plugin_id' => 'dem',
				'id' => 'merge_access',
				'position' => 6,
				'page_hook' => 'settings_page_settings_dem',
				'tab' => 'basic',
				'section' => 'dem_main_settings',
				'title' => __('Merge Access Level:', 'dem'),
				'desc' => __('What user types will <u>have access</u> to merge duplicated posts back to the original?', 'dem'),
				'help' => __(''),
				'type' => 'multicheckbox',
				'options' => $roles,
				'default' => array( 'administrator' , 'editor' )
			);

			$fields[] = array(
				'plugin_id' => 'dem',
				'id' => 'exclude_post_types',
				'position' => 22,
				'page_hook' => 'settings_page_settings_dem',
				'tab' => 'basic',
				'section' => 'dem_main_settings',
				'title' => __('Exclude Post Types:', 'dem'),
				'desc' => __('What types of posts do you want to exclude and <b><u>NOT</u></b> allow anyone to duplicate', 'dem'),
				'help' => __(''),
				'type' => 'multicheckbox',
				'options' => $post_types
			);

			return $fields;
		}

		public function showPage() {
			echo '<div class="wrap">';

			$args = array(
				'page_icon' => '',
				'page_title' => 'Duplicate and Merge Settings',
				'tab_icon' => 'options-general'
				);

			$this->settings->form( $this->pageHook , $args );

			echo '</div>';
		}
	}

	global $Duplicate_Edit_And_Merge_Settings;
	$Duplicate_Edit_And_Merge_Settings = new Duplicate_Edit_And_Merge_Settings();
}
?>