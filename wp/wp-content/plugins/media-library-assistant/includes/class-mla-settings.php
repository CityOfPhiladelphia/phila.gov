<?php
/**
 * Manages the settings page to edit the plugin option settings
 *
 * @package Media Library Assistant
 * @since 0.1
 */

/**
 * Class MLA (Media Library Assistant) Settings provides the settings page to edit the plugin option settings
 *
 * @package Media Library Assistant
 * @since 0.1
 */
class MLASettings {
	/**
	 * Slug for localizing and enqueueing JavaScript - MLA View List Table
	 *
	 * @since 1.40
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_EDIT_VIEW_SLUG = 'mla-inline-edit-view-scripts';

	/**
	 * Object name for localizing JavaScript - MLA View List Table
	 *
	 * @since 1.40
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_EDIT_VIEW_OBJECT = 'mla_inline_edit_view_vars';

	/**
	 * Slug for localizing and enqueueing JavaScript - MLA Upload List Table
	 *
	 * @since 1.40
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_EDIT_UPLOAD_SLUG = 'mla-inline-edit-upload-scripts';

	/**
	 * Object name for localizing JavaScript - MLA Upload List Table
	 *
	 * @since 1.40
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_EDIT_UPLOAD_OBJECT = 'mla_inline_edit_upload_vars';

	/**
	 * Slug for localizing and enqueueing JavaScript - MLA Custom tab
	 *
	 * @since 2.00
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_MAPPING_CUSTOM_SLUG = 'mla-inline-mapping-custom-scripts';

	/**
	 * Slug for localizing and enqueueing JavaScript - MLA IPTC/EXIF tab
	 *
	 * @since 2.00
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_MAPPING_IPTC_EXIF_SLUG = 'mla-inline-mapping-iptc-exif-scripts';

	/**
	 * Object name for localizing JavaScript - MLA Custom and IPTC/EXIF tabs
	 *
	 * @since 2.00
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_MAPPING_OBJECT = 'mla_inline_mapping_vars';

	/**
	 * Holds screen id to match help text to corresponding screen
	 *
	 * @since 1.40
	 *
	 * @var	array
	 */
	private static $current_page_hook = '';

	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 0.1
	 *
	 * @return	void
	 */
	public static function initialize( ) {
		//add_action( 'admin_page_access_denied', 'MLASettings::mla_admin_page_access_denied_action' );
		add_action( 'admin_init', 'MLASettings::mla_admin_init_action' );
		add_action( 'admin_menu', 'MLASettings::mla_admin_menu_action' );
		add_action( 'admin_enqueue_scripts', 'MLASettings::mla_admin_enqueue_scripts_action' );
		add_filter( 'set-screen-option', 'MLASettings::mla_set_screen_option_filter', 10, 3 ); // $status, $option, $value
		add_filter( 'screen_options_show_screen', 'MLASettings::mla_screen_options_show_screen_filter', 10, 2 ); // $show_screen, $this
		self::_version_upgrade();
	}

	/**
	 * Database and option update check, for installing new versions
	 *
	 * @since 0.30
	 *
	 * @return	void
	 */
	private static function _version_upgrade( ) {
		$current_version = MLACore::mla_get_option( MLACoreOptions::MLA_VERSION_OPTION );

		if ( version_compare( '.30', $current_version, '>' ) ) {
			/*
			 * Convert attachment_category and _tag to taxonomy_support;
			 * change the default if either option is unchecked
			 */
			$category_option = MLACore::mla_get_option( 'attachment_category' );
			$tag_option = MLACore::mla_get_option( 'attachment_tag' );
			if ( ! ( ( 'checked' == $category_option ) && ( 'checked' == $tag_option ) ) ) {
				$tax_option = MLACore::mla_get_option( MLACoreOptions::MLA_TAXONOMY_SUPPORT );
				if ( 'checked' != $category_option ) {
					if ( isset( $tax_option['tax_support']['attachment_category'] ) ) {
						unset( $tax_option['tax_support']['attachment_category'] );
					}
				}

				if ( 'checked' != $tag_option )  {
					if ( isset( $tax_option['tax_support']['attachment_tag'] ) ) {
						unset( $tax_option['tax_support']['attachment_tag'] );
					}
				}

				MLAOptions::mla_taxonomy_option_handler( 'update', 'taxonomy_support', MLACoreOptions::$mla_option_definitions['taxonomy_support'], $tax_option );
			} // one or both options unchecked

		MLACore::mla_delete_option( 'attachment_category' );
		MLACore::mla_delete_option( 'attachment_tag' );
		} // version is less than .30

		if ( version_compare( '1.13', $current_version, '>' ) ) {
			/*
			 * Add quick_edit and bulk_edit values to custom field mapping rules
			 */
			$new_values = array();

			foreach ( MLACore::mla_get_option( 'custom_field_mapping' ) as $key => $value ) {
				$value['quick_edit'] = ( isset( $value['quick_edit'] ) && $value['quick_edit'] ) ? true : false;
				$value['bulk_edit'] = ( isset( $value['bulk_edit'] ) && $value['bulk_edit'] ) ? true : false;
				$new_values[ $key ] = $value;
			}

			MLACore::mla_update_option( 'custom_field_mapping', $new_values );
		} // version is less than 1.13

		if ( version_compare( '1.30', $current_version, '>' ) ) {
			/*
			 * Add metadata values to custom field mapping rules
			 */
			$new_values = array();

			foreach ( MLACore::mla_get_option( 'custom_field_mapping' ) as $key => $value ) {
				$value['meta_name'] = isset( $value['meta_name'] ) ? $value['meta_name'] : '';
				$value['meta_single'] = ( isset( $value['meta_single'] ) && $value['meta_single'] ) ? true : false;
				$value['meta_export'] = ( isset( $value['meta_export'] ) && $value['meta_export'] ) ? true : false;
				$new_values[ $key ] = $value;
			}

			MLACore::mla_update_option( 'custom_field_mapping', $new_values );
		} // version is less than 1.30

		if ( version_compare( '1.40', $current_version, '>' ) ) {
			/*
			 * Add metadata values to custom field mapping rules
			 */
			$new_values = array();

			foreach ( MLACore::mla_get_option( 'custom_field_mapping' ) as $key => $value ) {
				$value['no_null'] = ( isset( $value['no_null'] ) && $value['no_null'] ) ? true : false;

				if ( isset( $value['meta_single'] ) && $value['meta_single'] ) {
					$value['option'] = 'single';
				} elseif ( isset( $value['meta_export'] ) && $value['meta_export'] ) {
					$value['option'] = 'export';
				} else {
					$value['option'] = 'text';
				}

				unset( $value['meta_single'] );
				unset( $value['meta_export'] );

				$new_values[ $key ] = $value;
			}

			MLACore::mla_update_option( 'custom_field_mapping', $new_values );
		} // version is less than 1.40

		if ( version_compare( '1.60', $current_version, '>' ) ) {
			/*
			 * Add delimiters values to taxonomy mapping rules
			 */
			$option_value = MLACore::mla_get_option( 'iptc_exif_mapping' );
			$new_values = array();

			foreach ( $option_value['taxonomy'] as $key => $value ) {
				$value['delimiters'] = isset( $value['delimiters'] ) ? $value['delimiters'] : '';
				$new_values[ $key ] = $value;
			}

			$option_value['taxonomy'] = $new_values;
			MLACore::mla_update_option( 'iptc_exif_mapping', $option_value );
		} // version is less than 1.60

		if ( version_compare( '1.72', $current_version, '>' ) ) {
			/*
			 * Strip default descriptions from the options table
			 */
			MLAMime::mla_update_upload_mime();
		} // version is less than 1.72

		if ( version_compare( '2.13', $current_version, '>' ) ) {
			/*
			 * Add format, option and no_null to IPTC/EXIF custom mapping rules
			 */
			$option_value = MLACore::mla_get_option( 'iptc_exif_mapping' );
			$new_values = array();

			foreach ( $option_value['custom'] as $key => $value ) {
				$value['format'] = isset( $value['format'] ) ? $value['format'] : 'native';
				$value['option'] = isset( $value['option'] ) ? $value['option'] : 'text';
				$value['no_null'] = isset( $value['no_null'] ) ? $value['no_null'] : false;
				$new_values[ $key ] = $value;
			}

			$option_value['custom'] = $new_values;
			MLACore::mla_update_option( 'iptc_exif_mapping', $option_value );
		} // version is less than 2.13

		MLACore::mla_update_option( MLACoreOptions::MLA_VERSION_OPTION, MLA::CURRENT_MLA_VERSION );
	}

	/**
	 * Perform one-time actions on plugin activation
	 *
	 * @since 0.40
	 *
	 * @return	void
	 */
	public static function mla_activation_hook( ) {
		/*
		 * Disable the uninstall file while the plugin is active
		 */
		if ( file_exists( MLA_PLUGIN_PATH . 'uninstall.php' ) ) {
			@rename ( MLA_PLUGIN_PATH . 'uninstall.php' , MLA_PLUGIN_PATH . 'mla-uninstall.php' );
		}
	}

	/**
	 * Perform one-time actions on plugin deactivation
	 *
	 * @since 0.40
	 *
	 * @return	void
	 */
	public static function mla_deactivation_hook( ) {
		$delete_option_settings = 'checked' === MLACore::mla_get_option( MLACoreOptions::MLA_DELETE_OPTION_SETTINGS );
		$delete_option_backups = 'checked' === MLACore::mla_get_option( MLACoreOptions::MLA_DELETE_OPTION_BACKUPS );
		
		/*
		 * We only need the uninstall file if one or both options are true,
		 * otherwise disable it to prevent a false "Delete files and data" warning
		 */
		if ( $delete_option_backups || $delete_option_settings ) {
			if ( file_exists( MLA_PLUGIN_PATH . 'mla-uninstall.php' ) ) {
				@rename ( MLA_PLUGIN_PATH . 'mla-uninstall.php' , MLA_PLUGIN_PATH . 'uninstall.php' );
			}
		} else {
			if ( file_exists( MLA_PLUGIN_PATH . 'uninstall.php' ) ) {
				@rename ( MLA_PLUGIN_PATH . 'uninstall.php' , MLA_PLUGIN_PATH . 'mla-uninstall.php' );
			}
		}
	}

	/**
	 * Debug logging for "You do not have sufficient permissions to access this page."
	 *
	 * @since 1.40
	 *
	 * @return	void
	 * /
	public static function mla_admin_page_access_denied_action() {
		global $pagenow;
		global $menu;
		global $submenu;
		global $_wp_menu_nopriv;
		global $_wp_submenu_nopriv;
		global $plugin_page;
		global $_registered_pages;

		error_log( 'DEBUG: mla_admin_page_access_denied_action xdebug_get_function_stack = ' . var_export( xdebug_get_function_stack(), true), 0 );		
		error_log( 'DEBUG: mla_admin_page_access_denied_action $_SERVER[REQUEST_URI] = ' .  var_export( $_SERVER['REQUEST_URI'], true), 0 );
		error_log( 'DEBUG: mla_admin_page_access_denied_action $_REQUEST = ' .  var_export( $_REQUEST, true), 0 );
		error_log( 'DEBUG: mla_admin_page_access_denied_action $pagenow = ' .  var_export( $pagenow, true), 0 );
		error_log( 'DEBUG: mla_admin_page_access_denied_action $parent = ' .  var_export( get_admin_page_parent(), true), 0 );
		error_log( 'DEBUG: mla_admin_page_access_denied_action $menu = ' .  var_export( $menu, true), 0 );
		error_log( 'DEBUG: mla_admin_page_access_denied_action $submenu = ' .  var_export( $submenu, true), 0 );
		error_log( 'DEBUG: mla_admin_page_access_denied_action $_wp_menu_nopriv = ' .  var_export( $_wp_menu_nopriv, true), 0 );
		error_log( 'DEBUG: mla_admin_page_access_denied_action $_wp_submenu_nopriv = ' .  var_export( $_wp_submenu_nopriv, true), 0 );
		error_log( 'DEBUG: mla_admin_page_access_denied_action $plugin_page = ' .  var_export( $plugin_page, true), 0 );
		error_log( 'DEBUG: mla_admin_page_access_denied_action $_registered_pages = ' .  var_export( $_registered_pages, true), 0 );
	}
	// */

	/**
	 * Load the plugin's Ajax handler
	 *
	 * @since 1.40
	 *
	 * @return	void
	 */
	public static function mla_admin_init_action() {
		add_action( 'wp_ajax_' . self::JAVASCRIPT_INLINE_EDIT_VIEW_SLUG, 'MLASettings::mla_inline_edit_view_action' );
		add_action( 'wp_ajax_' . self::JAVASCRIPT_INLINE_EDIT_UPLOAD_SLUG, 'MLASettings::mla_inline_edit_upload_action' );
		add_action( 'wp_ajax_' . self::JAVASCRIPT_INLINE_MAPPING_CUSTOM_SLUG, 'MLASettings::mla_inline_mapping_custom_action' );
		add_action( 'wp_ajax_' . self::JAVASCRIPT_INLINE_MAPPING_IPTC_EXIF_SLUG, 'MLASettings::mla_inline_mapping_iptc_exif_action' );
	}

	/**
	 * Load the plugin's Style Sheet and Javascript files
	 *
	 * @since 1.40
	 *
	 * @param	string	Name of the page being loaded
	 *
	 * @return	void
	 */
	public static function mla_admin_enqueue_scripts_action( $page_hook ) {
		global $wpdb, $wp_locale;

		/*
		 * Without a tab value, there's nothing to do
		 */
		if ( ( self::$current_page_hook != $page_hook ) || empty( $_REQUEST['mla_tab'] ) ) {
			return;
		}

		if ( $wp_locale->is_rtl() ) {
			wp_register_style( MLA::STYLESHEET_SLUG, MLA_PLUGIN_URL . 'css/mla-style-rtl.css', false, MLA::CURRENT_MLA_VERSION );
		} else {
			wp_register_style( MLA::STYLESHEET_SLUG, MLA_PLUGIN_URL . 'css/mla-style.css', false, MLA::CURRENT_MLA_VERSION );
		}

		wp_enqueue_style( MLA::STYLESHEET_SLUG );

		/*
		 * Initialize common script variables
		 */
		$script_variables = array(
			'error' => __( 'Error while making the changes.', 'media-library-assistant' ),
			'ntdeltitle' => __( 'Remove From Bulk Edit', 'media-library-assistant' ),
			'notitle' => '(' . __( 'no slug', 'media-library-assistant' ) . ')',
			'comma' => _x( ',', 'tag_delimiter', 'media-library-assistant' ),
			'useSpinnerClass' => false,
			'ajax_nonce' => wp_create_nonce( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) 
		);

		if ( version_compare( get_bloginfo( 'version' ), '4.2', '>=' ) ) {
			$script_variables['useSpinnerClass'] = true;
		}

		$mapping_variables = array(
			'bulkChunkSize' => MLACore::mla_get_option( MLACoreOptions::MLA_BULK_CHUNK_SIZE ),
			'bulkWaiting' => __( 'Waiting', 'media-library-assistant' ),
			'bulkRunning' => __( 'Running', 'media-library-assistant' ),
			'bulkComplete' => __( 'Complete', 'media-library-assistant' ),
			'bulkUnchanged' => __( 'Unchanged', 'media-library-assistant' ),
			'bulkSuccess' => __( 'Succeeded', 'media-library-assistant' ),
			'bulkFailure' => __( 'Failed', 'media-library-assistant' ),
			'bulkSkip' => __( 'Skipped', 'media-library-assistant' ),
			'bulkRedone' => __( 'Reprocessed', 'media-library-assistant' ),
			'bulkCanceled' => __( 'CANCELED', 'media-library-assistant' ),
		);


		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		/*
		 * Select tab-specific scripts and variables
		 */		
		switch ( $_REQUEST['mla_tab'] ) {
			case 'view':
				wp_enqueue_script( self::JAVASCRIPT_INLINE_EDIT_VIEW_SLUG,
					MLA_PLUGIN_URL . "js/mla-inline-edit-view-scripts{$suffix}.js", 
					array( 'wp-lists', 'suggest', 'jquery' ), MLA::CURRENT_MLA_VERSION, false );

				$script_variables['fields'] = array( 'original_slug', 'slug', 'singular', 'plural', 'specification', 'menu_order' );
				$script_variables['checkboxes'] = array( 'post_mime_type', 'table_view' );
				$script_variables['ajax_action'] = self::JAVASCRIPT_INLINE_EDIT_VIEW_SLUG;

				wp_localize_script( self::JAVASCRIPT_INLINE_EDIT_VIEW_SLUG,
					self::JAVASCRIPT_INLINE_EDIT_VIEW_OBJECT, $script_variables );
				return;
			case 'upload':
				wp_enqueue_script( self::JAVASCRIPT_INLINE_EDIT_UPLOAD_SLUG,
					MLA_PLUGIN_URL . "js/mla-inline-edit-upload-scripts{$suffix}.js", 
					array( 'wp-lists', 'suggest', 'jquery' ), MLA::CURRENT_MLA_VERSION, false );

				$script_variables['fields'] = array( 'original_slug', 'slug', 'mime_type', 'icon_type', 'core_type', 'mla_type', 'source', 'standard_source' );
				$script_variables['checkboxes'] = array( 'disabled' );
				$script_variables['ajax_action'] = self::JAVASCRIPT_INLINE_EDIT_UPLOAD_SLUG;

				wp_localize_script( self::JAVASCRIPT_INLINE_EDIT_UPLOAD_SLUG,
					self::JAVASCRIPT_INLINE_EDIT_UPLOAD_OBJECT, $script_variables );
				return;
			case 'custom_field':
				wp_enqueue_script( self::JAVASCRIPT_INLINE_MAPPING_CUSTOM_SLUG,
					MLA_PLUGIN_URL . "js/mla-inline-mapping-scripts{$suffix}.js", 
					array( 'jquery' ), MLA::CURRENT_MLA_VERSION, false );

				$tab_variables = array(
					'page' => 'mla-settings-menu-custom_field',
					'mla_tab' => 'custom_field',
					'screen' => 'settings_page_mla-settings-menu-custom_field',
					'ajax_action' => self::JAVASCRIPT_INLINE_MAPPING_CUSTOM_SLUG,
					'fieldsId' => '#mla-display-settings-custom-field-tab',
					'totalItems' => $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE `post_type` = 'attachment'" )
				);
				
				$script_variables = array_merge( $script_variables, $mapping_variables, $tab_variables );

				wp_localize_script( self::JAVASCRIPT_INLINE_MAPPING_CUSTOM_SLUG,
					self::JAVASCRIPT_INLINE_MAPPING_OBJECT, $script_variables );
				return;
			case 'iptc_exif':
				wp_enqueue_script( self::JAVASCRIPT_INLINE_MAPPING_IPTC_EXIF_SLUG,
					MLA_PLUGIN_URL . "js/mla-inline-mapping-scripts{$suffix}.js", 
					array( 'jquery' ), MLA::CURRENT_MLA_VERSION, false );

				$tab_variables = array(
					'page' => 'mla-settings-menu-iptc_exif',
					'mla_tab' => 'iptc_exif',
					'screen' => 'settings_page_mla-settings-menu-iptc_exif',
					'ajax_action' => self::JAVASCRIPT_INLINE_MAPPING_IPTC_EXIF_SLUG,
					'fieldsId' => '#mla-display-settings-iptc-exif-tab',
					'totalItems' => $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE `post_type` = 'attachment' AND ( `post_mime_type` LIKE 'image/%' OR `post_mime_type` LIKE 'application/%pdf%' )" ),
				);
				
				$script_variables = array_merge( $script_variables, $mapping_variables, $tab_variables );

				wp_localize_script( self::JAVASCRIPT_INLINE_MAPPING_IPTC_EXIF_SLUG,
					self::JAVASCRIPT_INLINE_MAPPING_OBJECT, $script_variables );
				return;
		}
	}

	/**
	 * Add settings page in the "Settings" section,
	 * add screen options and help tabs,
	 * add settings link in the Plugins section entry for MLA.
	 *
	 * @since 0.1
	 *
	 * @return	void
	 */
	public static function mla_admin_menu_action( ) {
		/*
		 * We need a tab-specific page ID to manage the screen options on the Views and Uploads tabs.
		 * Use the URL suffix, if present. If the URL doesn't have a tab suffix, use '-general'.
		 * This hack is required to pass the WordPress "referer" validation.
		 */
		 if ( isset( $_REQUEST['page'] ) && is_string( $_REQUEST['page'] ) && ( 'mla-settings-menu-' == substr( $_REQUEST['page'], 0, 18 ) ) ) {
			$tab = substr( $_REQUEST['page'], 18 );
		 } else {
			$tab = 'general';
		 }

		$tab = self::mla_get_options_tablist( $tab ) ? '-' . $tab : '-general';
		self::$current_page_hook = add_submenu_page( 'options-general.php', __( 'Media Library Assistant', 'media-library-assistant' ) . ' ' . __( 'Settings', 'media-library-assistant' ), __( 'Media Library Assistant', 'media-library-assistant' ), 'manage_options', MLACoreOptions::MLA_SETTINGS_SLUG . $tab, 'MLASettings::mla_render_settings_page' );
		add_action( 'load-' . self::$current_page_hook, 'MLASettings::mla_add_menu_options_action' );
		add_action( 'load-' . self::$current_page_hook, 'MLASettings::mla_add_help_tab_action' );
		add_filter( 'plugin_action_links', 'MLASettings::mla_add_plugin_settings_link_filter', 10, 2 );
	}

	/**
	 * Add the "XX Entries per page" filter to the Screen Options tab
	 *
	 * @since 1.40
	 *
	 * @return	void
	 */
	public static function mla_add_menu_options_action( ) {
		if ( isset( $_REQUEST['mla_tab'] ) ) {
			if ( 'view' == $_REQUEST['mla_tab'] ) {
				$option = 'per_page';

				$args = array(
					 'label' => __( 'Views per page', 'media-library-assistant' ),
					'default' => 10,
					'option' => 'mla_views_per_page' 
				);

				add_screen_option( $option, $args );
			} // view
			elseif ( isset( $_REQUEST['mla-optional-uploads-display'] ) || isset( $_REQUEST['mla-optional-uploads-search'] ) ) {
				$option = 'per_page';

				$args = array(
					 'label' => __( 'Types per page', 'media-library-assistant' ),
					'default' => 10,
					'option' => 'mla_types_per_page' 
				);

				add_screen_option( $option, $args );
			} // optional upload
			elseif ( 'upload' == $_REQUEST['mla_tab'] ) {
				$option = 'per_page';

				$args = array(
					 'label' => __( 'Upload types per page', 'media-library-assistant' ),
					'default' => 10,
					'option' => 'mla_uploads_per_page' 
				);

				add_screen_option( $option, $args );
			} // upload
		} // isset mla_tab
	}

	/**
	 * Add contextual help tabs to all the MLA pages
	 *
	 * @since 1.40
	 *
	 * @return	void
	 */
	public static function mla_add_help_tab_action( ) {
		$screen = get_current_screen();

		/*
		 * Is this our page and the Views or Uploads tab?
		 */
		if ( ! in_array( $screen->id, array( 'settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-view', 'settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-upload' ) ) ) {
			return;
		}

		$file_suffix = self::$current_page_hook;

		/*
		 * Override the screen suffix if we are going to display something other than the attachment table
		 */
		if ( isset( $_REQUEST['mla-optional-uploads-display'] ) || isset( $_REQUEST['mla-optional-uploads-search'] ) ) {
			$file_suffix .= '-optional';
		} elseif ( isset( $_REQUEST['mla_admin_action'] ) ) {
			switch ( $_REQUEST['mla_admin_action'] ) {
				case MLA::MLA_ADMIN_SINGLE_EDIT_DISPLAY:
					$file_suffix .= '-edit';
					break;
			} // switch
		} // isset( $_REQUEST['mla_admin_action'] )

		$template_array = MLACore::mla_load_template( 'help-for-' . $file_suffix . '.tpl' );
		if ( empty( $template_array ) ) {
			return;
		}

		if ( !empty( $template_array['sidebar'] ) ) {
			$page_values = array( 'settingsURL' => admin_url('options-general.php') );
			$content = MLAData::mla_parse_template( $template_array['sidebar'], $page_values );
			$screen->set_help_sidebar( $content );
			unset( $template_array['sidebar'] );
		}

		/*
		 * Provide explicit control over tab order
		 */
		$tab_array = array();

		foreach ( $template_array as $id => $content ) {
			$match_count = preg_match( '#\<!-- title="(.+)" order="(.+)" --\>#', $content, $matches, PREG_OFFSET_CAPTURE );

			if ( $match_count > 0 ) {
				$page_values = array( 'settingsURL' => admin_url('options-general.php') );
				$content = MLAData::mla_parse_template( $content, $page_values );
				$tab_array[ $matches[ 2 ][ 0 ] ] = array(
					 'id' => $id,
					'title' => $matches[ 1 ][ 0 ],
					'content' => $content 
				);
			} else {
				/* translators: 1: ERROR tag 2: function name 3: template key */
				error_log( sprintf( _x( '%1$s: %2$s discarding "%3$s"; no title/order', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'mla_add_help_tab_action', $id ), 0 );
			}
		}

		ksort( $tab_array, SORT_NUMERIC );
		foreach ( $tab_array as $indx => $value ) {
			$screen->add_help_tab( $value );
		}
	}

	/**
	 * Only show screen options on the View and Upload tabs
	 *
	 * @since 1.40
	 *
	 * @param	boolean	True to display "Screen Options", false to suppress them
	 * @param	string	Name of the page being loaded
	 *
	 * @return	boolean	True to display "Screen Options", false to suppress them
	 */
	public static function mla_screen_options_show_screen_filter( $show_screen, $this_screen ) {
		if ( self::$current_page_hook == $this_screen->base ) {
			if ( isset( $_REQUEST['mla_tab'] ) && in_array( $_REQUEST['mla_tab'], array( 'view', 'upload' ) ) ) {
				return true;
			}
		}

		return $show_screen;
	}

	/**
	 * Save the "Views/Uploads per page" option set by this user
	 *
	 * @since 1.40
	 *
	 * @param	mixed	false or value returned by previous filter
	 * @param	string	Name of the option being changed
	 * @param	string	New value of the option
	 *
	 * @return	string|void	New value if this is our option, otherwise nothing
	 */
	public static function mla_set_screen_option_filter( $status, $option, $value ) {
		if ( 'mla_views_per_page' == $option || 'mla_uploads_per_page' == $option || 'mla_types_per_page' == $option ) {
			return $value;
		} elseif ( $status ) {
			return $status;
		}
	}

	/**
	 * Ajax handler for Post MIME Types inline editing (quick and bulk edit)
	 *
	 * Adapted from wp_ajax_inline_save in /wp-admin/includes/ajax-actions.php
	 *
	 * @since 1.40
	 *
	 * @return	void	echo HTML <tr> markup for updated row or error message, then die()
	 */
	public static function mla_inline_edit_view_action() {
		set_current_screen( $_REQUEST['screen'] );

		check_ajax_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

		if ( empty( $_REQUEST['original_slug'] ) ) {
			echo __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'No view slug found', 'media-library-assistant' );
			die();
		}

		$request = array( 'original_slug' => $_REQUEST['original_slug'] );
		$request['slug'] = $_REQUEST['slug'];
		$request['specification'] = $_REQUEST['specification'];
		$request['singular'] = $_REQUEST['singular'];
		$request['plural'] = $_REQUEST['plural'];
		$request['post_mime_type'] = isset( $_REQUEST['post_mime_type'] ) && ( '1' == $_REQUEST['post_mime_type'] );
		$request['table_view'] = isset( $_REQUEST['table_view'] ) && ( '1' == $_REQUEST['table_view'] );
		$request['menu_order'] = $_REQUEST['menu_order'];
		$results = MLAMime::mla_update_post_mime_type( $request );

		if ( false === strpos( $results['message'], __( 'ERROR', 'media-library-assistant' ) ) ) {
			$new_item = (object) MLAMime::mla_get_post_mime_type( $_REQUEST['slug'] );
		} else {
			$new_item = (object) MLAMime::mla_get_post_mime_type( $_REQUEST['original_slug'] );
		}

		$new_item->post_ID = $_REQUEST['post_ID'];

		//	Create an instance of our package class and echo the new HTML
		$MLAListViewTable = new MLA_View_List_Table();
		$MLAListViewTable->single_row( $new_item );
		die(); // this is required to return a proper result
	}

	/**
	 * Ajax handler for Upload MIME Types inline editing (quick and bulk edit)
	 *
	 * Adapted from wp_ajax_inline_save in /wp-admin/includes/ajax-actions.php
	 *
	 * @since 1.40
	 *
	 * @return	void	echo HTML <tr> markup for updated row or error message, then die()
	 */
	public static function mla_inline_edit_upload_action() {
		set_current_screen( $_REQUEST['screen'] );

		check_ajax_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

		if ( empty( $_REQUEST['original_slug'] ) ) {
			echo __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'No upload slug found', 'media-library-assistant' );
			die();
		}

		$request = array( 'original_slug' => $_REQUEST['original_slug'] );
		$request['slug'] = $_REQUEST['slug'];
		$request['mime_type'] = $_REQUEST['mime_type'];
		$request['icon_type'] = $_REQUEST['icon_type'];
		$request['disabled'] = isset( $_REQUEST['disabled'] ) && ( '1' == $_REQUEST['disabled'] );
		$results = MLAMime::mla_update_upload_mime( $request );

		if ( false === strpos( $results['message'], __( 'ERROR', 'media-library-assistant' ) ) ) {
			$new_item = (object) MLAMime::mla_get_upload_mime( $_REQUEST['slug'] );
		} else {
			$new_item = (object) MLAMime::mla_get_upload_mime( $_REQUEST['original_slug'] );
		}
		$new_item->post_ID = $_REQUEST['post_ID'];

		//	Create an instance of our package class and echo the new HTML
		$MLAListUploadTable = new MLA_Upload_List_Table();
		$MLAListUploadTable->single_row( $new_item );
		die(); // this is required to return a proper result
	}

	/**
	 * Ajax handler for Custom Fields tab inline mapping
	 *
	 * @since 2.00
	 *
	 * @return	void	echo json response object, then die()
	 */
	public static function mla_inline_mapping_custom_action() {
		set_current_screen( $_REQUEST['screen'] );
		check_ajax_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

		/*
		 * Convert the ajax bulk_action back to the older Submit button equivalent
		 */
		if ( ! empty( $_REQUEST['bulk_action'] ) ) {
			if ( 'custom-field-options-map' == $_REQUEST['bulk_action'] ) {
				$_REQUEST['custom-field-options-map'] = __( 'Map All Rules, All Attachments Now', 'media-library-assistant' );
			} else {
				$match_count = preg_match( '/custom_field_mapping\[(.*)\]\[(.*)\]\[(.*)\]/', $_REQUEST['bulk_action'], $matches );
				if ( $match_count ) {
					$_REQUEST['custom_field_mapping'][ $matches[1] ][ $matches[2] ][ $matches[3] ] = __( 'Map All Attachments', 'media-library-assistant' );
				}
			}
		}

		/*
		 * Check for action or submit buttons.
		 */

		if ( isset( $_REQUEST['custom_field_mapping'] ) && is_array( $_REQUEST['custom_field_mapping'] ) ) {
			/*
			 * Find the current chunk
			 */
			$offset = isset( $_REQUEST['offset'] ) ? $_REQUEST['offset'] : 0;
			$length = isset( $_REQUEST['length'] ) ? $_REQUEST['length'] : 0;

			/*
			 * Check for page-level submit button to map attachments.
			 */
			if ( !empty( $_REQUEST['custom-field-options-map'] ) ) {
				$page_content = self::_process_custom_field_mapping( NULL, $offset, $length );
			} else {
				$page_content = array(
					'message' => '',
					'body' => '',
					'processed' => 0,
					'unchanged' => 0,
					'success' =>  0
				);

				/*
				 * Check for single-rule action buttons
				 */
				foreach ( $_REQUEST['custom_field_mapping'] as $key => $value ) {
					$value = stripslashes_deep( $value );

					if ( isset( $value['action'] ) ) {
						$settings = array( $key => $value );
						foreach ( $value['action'] as $action => $label ) {
							switch( $action ) {
								case 'map_now':
									$page_content = self::_process_custom_field_mapping( $settings, $offset, $length );
									break;
								case 'add_rule_map':
									if ( 'none' == $value['name'] ) {
										$page_content['message'] = __( 'Custom field no mapping rule changes detected.', 'media-library-assistant' );
										break;
									}
									// fallthru
								case 'add_field_map':
									if ( '' == $value['name'] ) {
										$page_content['message'] = __( 'Custom field no mapping rule changes detected.', 'media-library-assistant' );
										break;
									}

									if ( 0 == $offset ) {
										$page_content = self::_save_custom_field_settings( $settings );
										if ( false !== strpos( $page_content['message'], __( 'ERROR', 'media-library-assistant' ) ) ) {
											$page_content['processed'] = 0;
											$page_content['unchanged'] = 0;
											$page_content['success'] = 0;
											break;
										}
									}

									$current_values = MLACore::mla_get_option( 'custom_field_mapping' );
									$settings = array( $value['name'] => $current_values[$value['name']] );
									$map_content = self::_process_custom_field_mapping( $settings, $offset, $length );
									$page_content['message'] .= '<br>&nbsp;<br>' . $map_content['message'];
									$page_content['processed'] = $map_content['processed'];
									$page_content['unchanged'] = $map_content['unchanged'];
									$page_content['success'] = $map_content['success'];
									$page_content['refresh'] = true;
									break;
								default:
									// ignore everything else
							} //switch action
						} // foreach action
					} /// isset action
				} // foreach rule
			} // specific rule check
		} // isset custom_field_mapping
		else {
			$page_content = array(
				'message' => '',
				'body' => '',
				'processed' => 0,
				'unchanged' => 0,
				'success' =>  0
			);
		}

		$chunk_results = array( 
			'message' => $page_content['message'],
			'processed' => $page_content['processed'],
			'unchanged' => $page_content['unchanged'],
			'success' => $page_content['success'],
			'refresh' => isset( $page_content['refresh'] ) && true == $page_content['refresh'],
		);

		wp_send_json_success( $chunk_results );
	}

	/**
	 * Ajax handler for IPTC/EXIF tab inline mapping
	 *
	 * @since 2.00
	 *
	 * @return	void	echo json response object, then die()
	 */
	public static function mla_inline_mapping_iptc_exif_action() {
		set_current_screen( $_REQUEST['screen'] );
		check_ajax_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

		/*
		 * Convert the ajax bulk_action back to the older Submit button equivalent
		 */
		if ( ! empty( $_REQUEST['bulk_action'] ) ) {
			switch ( $_REQUEST['bulk_action'] ) {
				case 'iptc-exif-options-process-standard':
				$_REQUEST['iptc-exif-options-process-standard'] = __( 'Map All Attachments, Standard Fields Now', 'media-library-assistant' );
					break;
				case 'iptc-exif-options-process-taxonomy':
				$_REQUEST['iptc-exif-options-process-taxonomy'] = __( 'Map All Attachments, Taxonomy Terms Now', 'media-library-assistant' );
					break;
				case 'iptc-exif-options-process-custom':
				$_REQUEST['iptc-exif-options-process-custom'] = __( 'Map All Attachments, Custom Fields Now', 'media-library-assistant' );
					break;
				default:
					$match_count = preg_match( '/iptc_exif_mapping\[custom\]\[(.*)\]\[(.*)\]\[(.*)\]/', $_REQUEST['bulk_action'], $matches );
					if ( $match_count ) {
						$_REQUEST['iptc_exif_mapping']['custom'][ $matches[1] ][ $matches[2] ][ $matches[3] ] = __( 'Map All Attachments', 'media-library-assistant' );
					}
			}
		}

		/*
		 * Check for action or submit buttons.
		 */
		if ( isset( $_REQUEST['iptc_exif_mapping'] ) && is_array( $_REQUEST['iptc_exif_mapping'] ) ) {
			/*
			 * Find the current chunk
			 */
			$offset = isset( $_REQUEST['offset'] ) ? $_REQUEST['offset'] : 0;
			$length = isset( $_REQUEST['length'] ) ? $_REQUEST['length'] : 0;

			/*
			 * Check for page-level submit button to map attachments.
			 */
			if ( !empty( $_REQUEST['iptc-exif-options-process-standard'] ) ) {
				$page_content = self::_process_iptc_exif_standard( $offset, $length );
			} elseif ( !empty( $_REQUEST['iptc-exif-options-process-taxonomy'] ) ) {
				$page_content = self::_process_iptc_exif_taxonomy( $offset, $length );
			} elseif ( !empty( $_REQUEST['iptc-exif-options-process-custom'] ) ) {
				$page_content = self::_process_iptc_exif_custom( NULL, $offset, $length );
			} else {
				$page_content = array(
					'message' => '',
					'body' => '',
					'processed' => 0,
					'unchanged' => 0,
					'success' =>  0
				);

				/*
				 * Check for single-rule action buttons
				 */
				foreach ( $_REQUEST['iptc_exif_mapping']['custom'] as $key => $value ) {
					$value = stripslashes_deep( $value );

					if ( isset( $value['action'] ) ) {
						$settings = array( 'custom' => array( $key => $value ) );
						foreach ( $value['action'] as $action => $label ) {
							switch( $action ) {
								case 'map_now':
									$page_content = self::_process_iptc_exif_custom( $settings, $offset, $length );
									break;
								case 'add_rule_map':
									if ( 'none' == $value['name'] ) {
										$page_content['message'] = __( 'IPTC/EXIF no mapping changes detected.', 'media-library-assistant' );
										break;
									}
									// fallthru
								case 'add_field_map':
									if ( '' == $value['name'] ) {
										$page_content['message'] = __( 'IPTC/EXIF no mapping changes detected.', 'media-library-assistant' );
										break;
									}

									if ( 0 == $offset ) {
										$page_content = self::_save_iptc_exif_custom_settings( $settings );
										if ( false !== strpos( $page_content['message'], __( 'ERROR', 'media-library-assistant' ) ) ) {
											$page_content['processed'] = 0;
											$page_content['unchanged'] = 0;
											$page_content['success'] = 0;
											break;
										}
									}

									$current_values = MLACore::mla_get_option( 'iptc_exif_mapping' );
									$settings = array( 'custom' => array( $value['name'] => $current_values['custom'][$value['name']] ) );
									$map_content = self::_process_iptc_exif_custom( $settings, $offset, $length );
									$page_content['message'] .= '<br>&nbsp;<br>' . $map_content['message'];
									$page_content['processed'] = $map_content['processed'];
									$page_content['unchanged'] = $map_content['unchanged'];
									$page_content['success'] = $map_content['success'];
									$page_content['refresh'] = true;
									break;
								default:
									// ignore everything else
							} //switch action
						} // foreach action
					} /// isset action
				} // foreach rule
			}
		} // isset custom_field_mapping
		else {
			$page_content = array(
				'message' => '',
				'body' => '',
				'processed' => 0,
				'unchanged' => 0,
				'success' =>  0
			);
		}

		$chunk_results = array( 
			'message' => $page_content['message'],
			'processed' => $page_content['processed'],
			'unchanged' => $page_content['unchanged'],
			'success' => $page_content['success'],
			'refresh' => isset( $page_content['refresh'] ) && true == $page_content['refresh'],
		);

		wp_send_json_success( $chunk_results );
	}

	/**
	 * Add the "Settings" link to the MLA entry in the Plugins section
	 *
	 * @since 0.1
	 *
	 * @param	array 	array of links for the Plugin, e.g., "Activate"
	 * @param	string 	Directory and name of the plugin Index file
	 *
	 * @return	array	Updated array of links for the Plugin
	 */
	public static function mla_add_plugin_settings_link_filter( $links, $file ) {
		if ( $file == 'media-library-assistant/index.php' ) {
			$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=' . MLACoreOptions::MLA_SETTINGS_SLUG . '-general' ), __( 'Settings', 'media-library-assistant' ) );
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Update or delete a single MLA option value
	 *
	 * @since 0.80
	 * @uses $_REQUEST
 	 *
	 * @param	string	HTML id/name attribute and option database key (OMIT MLA_OPTION_PREFIX)
	 * @param	array	Option parameters, e.g., 'type', 'std'
	 * @param	array	Custom option definitions
	 *
	 * @return	string	HTML markup for the option's table row
	 */
	public static function mla_update_option_row( $key, $value, $option_table = NULL ) {
		$default = MLACore::mla_get_option( $key, true, false, $option_table );
		
		/*
		 * Checkbox logic is done in the switch statements below,
		 * custom logic is done in the handler.
		 */
		if ( ( 'checkbox' != $value['type'] ) && ( 'custom' != $value['type'] ) ) {
			if ( isset( $_REQUEST[ MLA_OPTION_PREFIX . $key ] ) ) {
				$current = $_REQUEST[ MLA_OPTION_PREFIX . $key ];
			} else {
				$current = $default;
			}

			if ( $current == $default ) {
				unset( $_REQUEST[ MLA_OPTION_PREFIX . $key ] );
			}
		}
		
		if ( isset( $_REQUEST[ MLA_OPTION_PREFIX . $key ] ) ) {
			$message = '<br>update_option(' . $key . ")\r\n";
			switch ( $value['type'] ) {
				case 'checkbox':
					if ( 'checked' == $default ) {
						MLACore::mla_delete_option( $key, $option_table );
					} else {
						$message = '<br>check_option(' . $key . ')';
						MLACore::mla_update_option( $key, 'checked', $option_table );
					}
					break;
				case 'header':
				case 'subheader':
					$message = '';
					break;
				case 'radio':
					MLACore::mla_update_option( $key, $_REQUEST[ MLA_OPTION_PREFIX . $key ], $option_table );
					break;
				case 'select':
					MLACore::mla_update_option( $key, $_REQUEST[ MLA_OPTION_PREFIX . $key ], $option_table );
					break;
				case 'text':
					MLACore::mla_update_option( $key, stripslashes( trim( $_REQUEST[ MLA_OPTION_PREFIX . $key ], $option_table ) ) );
					break;
				case 'textarea':
					MLACore::mla_update_option( $key, stripslashes( trim( $_REQUEST[ MLA_OPTION_PREFIX . $key ], $option_table ) ) );
					break;
				case 'custom':
					$message = call_user_func( array( 'MLAOptions', $value['update'] ), 'update', $key, $value, $_REQUEST );
					break;
				case 'hidden':
					break;
				default:
					/* translators: 1: ERROR tag 2: function name 3: option type, e.g., radio, select, text */
					error_log( sprintf( _x( '%1$s: %2$s unknown type = "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), '_save_settings(1)', var_export( $value, true ) ), 0 );
			} // $value['type']
		}  // isset $key
		else {
			$message = '<br>delete_option(' . $key . ')';
			switch ( $value['type'] ) {
				case 'checkbox':
					if ( 'checked' == $default ) {
						$message = '<br>uncheck_option(' . $key . ')';
						MLACore::mla_update_option( $key, 'unchecked', $option_table );
					} else {
						MLACore::mla_delete_option( $key, $option_table );
					}
					break;
				case 'header':
				case 'subheader':
					$message = '';
					break;
				case 'radio':
					MLACore::mla_delete_option( $key, $option_table );
					break;
				case 'select':
					MLACore::mla_delete_option( $key, $option_table );
					break;
				case 'text':
					MLACore::mla_delete_option( $key, $option_table );
					break;
				case 'textarea':
					MLACore::mla_delete_option( $key, $option_table );
					break;
				case 'custom':
					$message = call_user_func( array( 'MLAOptions', $value['delete'] ), 'delete', $key, $value, $_REQUEST );
					break;
				case 'hidden':
					break;
				default:
					/* translators: 1: ERROR tag 2: function name 3: option type, e.g., radio, select, text */
					error_log( sprintf( _x( '%1$s: %2$s unknown type = "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), '_save_settings(2)', var_export( $value, true ) ), 0 );
			} // $value['type']
		}  // ! isset $key

		return $message;
	}

	/**
	 * Compose the table row for a single MLA option
	 *
	 * @since 0.80
	 * @uses $page_template_array contains option and option-item templates
 	 *
	 * @param	string	HTML id/name attribute and option database key (OMIT MLA_OPTION_PREFIX)
	 * @param	array	Option parameters, e.g., 'type', 'std'
	 * @param	array	Custom option definitions
	 *
	 * @return	string	HTML markup for the option's table row
	 */
	public static function mla_compose_option_row( $key, $value, $option_table = NULL ) {
		switch ( $value['type'] ) {
			case 'checkbox':
				$option_values = array(
					'key' => MLA_OPTION_PREFIX . $key,
					'checked' => '',
					'value' => $value['name'],
					'help' => $value['help'] 
				);

				if ( 'checked' == MLACore::mla_get_option( $key, false, false, $option_table ) ) {
					$option_values['checked'] = 'checked="checked"';
				}

				return MLAData::mla_parse_template( self::$page_template_array['checkbox'], $option_values );
			case 'header':
			case 'subheader':
				$option_values = array(
					'Go to Top' => __( 'Go to Top', 'media-library-assistant' ),
					'key' => MLA_OPTION_PREFIX . $key,
					'value' => $value['name'] 
				);

				return MLAData::mla_parse_template( self::$page_template_array[ $value['type'] ], $option_values );
			case 'radio':
				$radio_options = '';
				foreach ( $value['options'] as $optid => $option ) {
					$option_values = array(
						'key' => MLA_OPTION_PREFIX . $key,
						'option' => $option,
						'checked' => '',
						'value' => $value['texts'][$optid] 
					);

					if ( $option == MLACore::mla_get_option( $key, false, false, $option_table ) ) {
						$option_values['checked'] = 'checked="checked"';
					}

					$radio_options .= MLAData::mla_parse_template( self::$page_template_array['radio-option'], $option_values );
				}

				$option_values = array(
					'value' => $value['name'],
					'options' => $radio_options,
					'help' => $value['help'] 
				);

				return MLAData::mla_parse_template( self::$page_template_array['radio'], $option_values );
			case 'select':
				$select_options = '';
				foreach ( $value['options'] as $optid => $option ) {
					$option_values = array(
						'selected' => '',
						'value' => $option,
						'text' => $value['texts'][$optid]
					);

					if ( $option == MLACore::mla_get_option( $key, false, false, $option_table ) ) {
						$option_values['selected'] = 'selected="selected"';
					}

					$select_options .= MLAData::mla_parse_template( self::$page_template_array['select-option'], $option_values );
				}

				$option_values = array(
					'key' => MLA_OPTION_PREFIX . $key,
					'value' => $value['name'],
					'options' => $select_options,
					'help' => $value['help'] 
				);

				return MLAData::mla_parse_template( self::$page_template_array['select'], $option_values );
			case 'text':
				$option_values = array(
					'key' => MLA_OPTION_PREFIX . $key,
					'value' => $value['name'],
					'help' => $value['help'],
					'size' => '40',
					'text' => '' 
				);

				if ( !empty( $value['size'] ) ) {
					$option_values['size'] = $value['size'];
				}

				$option_values['text'] = MLACore::mla_get_option( $key, false, false, $option_table );

				return MLAData::mla_parse_template( self::$page_template_array['text'], $option_values );
			case 'textarea':
				$option_values = array(
					'key' => MLA_OPTION_PREFIX . $key,
					'value' => $value['name'],
					'options' => $select_options,
					'help' => $value['help'],
					'cols' => '90',
					'rows' => '5',
					'text' => '' 
				);

				if ( !empty( $value['cols'] ) ) {
					$option_values['cols'] = $value['cols'];
				}

				if ( !empty( $value['rows'] ) ) {
					$option_values['rows'] = $value['rows'];
				}

				$option_values['text'] = stripslashes( MLACore::mla_get_option( $key, false, false, $option_table ) );

				return MLAData::mla_parse_template( self::$page_template_array['textarea'], $option_values );
			case 'custom':
				if ( isset( $value['render'] ) ) {
					return call_user_func( array( 'MLAOptions', $value['render'] ), 'render', $key, $value );
				}

				break;
			case 'hidden':
				break;
			default:
				/* translators: 1: ERROR tag 2: function name 3: option type, e.g., radio, select, text */
				error_log( sprintf( _x( '%1$s: %2$s unknown type = "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'mla_render_settings_page', var_export( $value, true ) ), 0 );
		} //switch

		return '';
	}

	/**
	 * Template file for the Settings page(s) and parts
	 *
	 * This array contains all of the template parts for the Settings page(s). The array is built once
	 * each page load and cached for subsequent use.
	 *
	 * @since 0.80
	 *
	 * @var	array
	 */
	public static $page_template_array = NULL;

	/**
	 * Definitions for Settings page tab ids, titles and handlers
	 * Each tab is defined by an array with the following elements:
	 *
	 * array key => HTML id/name attribute and option database key (OMIT MLA_OPTION_PREFIX)
	 *
	 * title => tab label / heading text
	 * render => rendering function for tab messages and content. Usage:
	 *     $tab_content = ['render']( );
	 *
	 * The array must be populated at runtime in MLASettings::mla_localize_tablist();
	 * localization calls cannot be placed in the "public static" array definition itself.
	 *
	 * @since 0.80
	 *
	 * @var	array
	 */
	private static $mla_tablist = array();

	/**
	 * Localize $mla_tablist array
	 *
	 * Localization must be done at runtime; these calls cannot be placed in the
	 * "public static" array definition itself. Called from MLATest::initialize.
	 *
	 * @since 1.70
	 *
	 * @return	void
	 */
	public static function mla_localize_tablist() {
		self::$mla_tablist = array(
			'general' => array( 'title' => __ ( 'General', 'media-library-assistant' ), 'render' => array( 'MLASettings', '_compose_general_tab' ) ),
			'view' => array( 'title' => __ ( 'Views', 'media-library-assistant' ), 'render' => array( 'MLASettings', '_compose_view_tab' ) ),
			'upload' => array( 'title' => __ ( 'Uploads', 'media-library-assistant' ), 'render' => array( 'MLASettings', '_compose_upload_tab' ) ),
			'mla_gallery' => array( 'title' => __ ( 'MLA Gallery', 'media-library-assistant' ), 'render' => array( 'MLASettings', '_compose_mla_gallery_tab' ) ),
			'custom_field' => array( 'title' => __ ( 'Custom Fields', 'media-library-assistant' ), 'render' => array( 'MLASettings', '_compose_custom_field_tab' ) ),
			'iptc_exif' => array( 'title' => 'IPTC/EXIF', 'render' => array( 'MLASettings', '_compose_iptc_exif_tab' ) ),
			'documentation' => array( 'title' => __ ( 'Documentation', 'media-library-assistant' ), 'render' => array( 'MLASettings', '_compose_documentation_tab' ) ),
			'debug' => array( 'title' => __ ( 'Debug', 'media-library-assistant' ), 'render' => array( 'MLASettings', '_compose_debug_tab' ) ),
		);
	}

	/**
	 * Retrieve the list of options tabs or a specific tab value
	 *
	 * @since 1.82
	 *
	 * @param	string	Tab slug, to retrieve a single entry
	 *
	 * @return	array|false	The entire tablist ( $tab = NULL ), a single tab entry or false if not found/not allowed
	 */
	private static function mla_get_options_tablist( $tab = NULL ) {
		if ( is_string( $tab ) ) {
			if ( isset( self::$mla_tablist[ $tab ] ) ) {
				$results = self::$mla_tablist[ $tab ];

				if ( ( 'debug' == $tab ) && ( 0 == ( MLA_DEBUG_LEVEL & 1 ) ) ) {
					$results = false;
				}
			} else {
				$results = false;
			}
		} else {
			$results = self::$mla_tablist;

			if ( 0 == ( MLA_DEBUG_LEVEL & 1 ) ) {
				unset ( $results['debug'] );
			}
		}

		return apply_filters( 'mla_get_options_tablist', $results, self::$mla_tablist, $tab );
	}

	/**
	 * Compose the navigation tabs for the Settings subpage
	 *
	 * @since 0.80
	 * @uses $page_template_array contains tablist and tablist-item templates
 	 *
	 * @param	string	Optional data-tab-id value for the active tab, default 'general'
	 *
	 * @return	string	HTML markup for the Settings subpage navigation tabs
	 */
	private static function _compose_settings_tabs( $active_tab = 'general' ) {
		$tablist_item = self::$page_template_array['tablist-item'];
		$tabs = '';
		foreach ( self::mla_get_options_tablist() as $key => $item ) {
			$item_values = array(
				'data-tab-id' => $key,
				'nav-tab-active' => ( $active_tab == $key ) ? 'nav-tab-active' : '',
				'settings-page' => MLACoreOptions::MLA_SETTINGS_SLUG . '-' . $key,
				'title' => $item['title']
			);

			$tabs .= MLAData::mla_parse_template( $tablist_item, $item_values );
		} // foreach $item

		$tablist_values = array( 'tablist' => $tabs );
		return MLAData::mla_parse_template( self::$page_template_array['tablist'], $tablist_values );
	}

	/**
	 * Compose the General tab content for the Settings subpage
	 *
	 * @since 0.80
	 * @uses $page_template_array contains tab content template(s)
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_general_tab( ) {
		/*
		 * Check for submit buttons to change or reset settings.
		 * Initialize page messages and content.
		 */
		if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_save_general_settings( );
		} elseif ( !empty( $_REQUEST['mla-general-options-export'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_export_settings( );
		} elseif ( !empty( $_REQUEST['mla-general-options-import'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_import_settings( );
		} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_reset_general_settings( );
		} else {
			$page_content = array(
				 'message' => '',
				'body' => '' 
			);
		}

		if ( !empty( $page_content['body'] ) ) {
			return $page_content;
		}

		$page_values = array(
			'General Processing Options' => __( 'General Processing Options', 'media-library-assistant' ),
			/* translators: 1: - 4: page subheader values */
			'In this tab' => sprintf( __( 'In this tab you can find a number of options for controlling the plugin&rsquo;s operation. Scroll down to find options for %1$s, %2$s, %3$s and %4$s. Be sure to click "Save Changes" at the bottom of the tab to save any changes you make.', 'media-library-assistant' ), '<strong>' . __( 'Where-used Reporting', 'media-library-assistant' ) . '</strong>', '<strong>' . __( 'Taxonomy Support', 'media-library-assistant' ) . '</strong>', '<strong>' . __( 'Media/Assistant Table Defaults', 'media-library-assistant' ) . '</strong>', '<strong>' . __( 'Media Manager Enhancements', 'media-library-assistant' ) . '</strong>' ),
			'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
			'Export ALL Settings' => __( 'Export ALL Settings', 'media-library-assistant' ),
			'Delete General options' => __( 'Delete General options and restore default settings', 'media-library-assistant' ),
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'_wp_http_referer' => wp_referer_field( false ),
			'Go to Top' => __( 'Go to Top', 'media-library-assistant' ),
			'Support Our Work' => __( 'Support Our Work', 'media-library-assistant' ),
			'Donate to FTJ' => __( 'Donate to FTJ', 'media-library-assistant' ),
			'Donate' => __( 'Donate', 'media-library-assistant' ),
			/* translators: 1: donation hyperlink */
			'This plugin was' => sprintf( __( 'This plugin was inspired by my work on the WordPress web site for our nonprofit, Fair Trade Judaica. If you find the Media Library Assistant plugin useful and would like to support a great cause, consider a %1$s to our work. Thank you!', 'media-library-assistant' ), '<a href="http://fairtradejudaica.org/make-a-difference/donate/" title="' . __( 'Donate to FTJ', 'media-library-assistant' ) . '" target="_blank" style="font-weight:bold">' . __( 'tax-deductible donation', 'media-library-assistant' ) . '</a>' ),
			'shortcode_list' => '',
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-general&mla_tab=general',
			'options_list' => '',
			'import_settings' => '',
		);

		/*
		 * $custom_fields documents the name and description of custom fields
		 */
		$custom_fields = array( 
			// array("name" => "field_name", "description" => "field description.")
		);

		/* 
		 * $shortcodes documents the name and description of plugin shortcodes
		 */
		$shortcodes = array( 
			// array("name" => "shortcode", "description" => "This shortcode...")
			// array( 'name' => 'mla_attachment_list', 'description' => __( 'renders a complete list of all attachments and references to them.', 'media-library-assistant' ) ),
			array( 'name' => 'mla_gallery', 'description' => __( 'enhanced version of the WordPress [gallery] shortcode.', 'media-library-assistant' ) . sprintf( ' %1$s <a href="%2$s">%3$s</a>.',  __( 'For complete documentation', 'media-library-assistant' ), admin_url( 'options-general.php?page=' . MLACoreOptions::MLA_SETTINGS_SLUG . '-documentation&amp;mla_tab=documentation#mla_gallery' ), __( 'click here', 'media-library-assistant' ) ) ),
			array( 'name' => 'mla_tag_cloud', 'description' => __( 'enhanced version of the WordPress Tag Cloud.', 'media-library-assistant' ) . sprintf( ' %1$s <a href="%2$s">%3$s</a>.',  __( 'For complete documentation', 'media-library-assistant' ), admin_url( 'options-general.php?page=' . MLACoreOptions::MLA_SETTINGS_SLUG . '-documentation&amp;mla_tab=documentation#mla_tag_cloud' ), __( 'click here', 'media-library-assistant' ) ) )
		);

		$shortcode_list = '';
		foreach ( $shortcodes as $shortcode ) {
			$shortcode_values = array ( 'name' => $shortcode['name'], 'description' => $shortcode['description'] );
			$shortcode_list .= MLAData::mla_parse_template( self::$page_template_array['shortcode-item'], $shortcode_values );
		}

		if ( ! empty( $shortcode_list ) ) {
			$shortcode_values = array (
				'shortcode_list' => $shortcode_list,
				'Shortcodes made available' => __( 'Shortcodes made available by this plugin', 'media-library-assistant' )
			);
			$page_values['shortcode_list'] = MLAData::mla_parse_template( self::$page_template_array['shortcode-list'], $shortcode_values );
		}

		/*
		 * Fill in the current list of Media/Assistant table sortable columns, sorted by their labels.
		 * Make sure the current choice still exists or revert to default.
		 */
		$columns = array();
		foreach ( MLAQuery::mla_get_sortable_columns( ) as $key => $value ) {
			if ( ! array_key_exists( $value[1], $columns ) ) {
				$columns[ $value[1] ] = $value[0];
			}
		}

		uksort( $columns, 'strnatcasecmp' );
		$options = array_merge( array('None' => 'none'), $columns );
		$current = MLACore::mla_get_option( MLACoreOptions::MLA_DEFAULT_ORDERBY );
		MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_DEFAULT_ORDERBY ]['options'] = array();
		MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_DEFAULT_ORDERBY ]['texts'] = array();
		$found_current = false;
		foreach ($options as $key => $value ) {
			MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_DEFAULT_ORDERBY ]['options'][] = $value;
			MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_DEFAULT_ORDERBY ]['texts'][] = $key;
			if ( $current == $value ) {
				$found_current = true;
			}
		}

		if ( ! $found_current ) {
			MLACore::mla_delete_option( MLACoreOptions::MLA_DEFAULT_ORDERBY );
		}

		/*
		 * Validate the Media Manager sort order or revert to default
		 */
		$options = array_merge( array('&mdash; ' . __( 'Media Manager Default', 'media-library-assistant' ) . ' &mdash;' => 'default', 'None' => 'none'), $columns );
		$current = MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_ORDERBY );
		MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_MEDIA_MODAL_ORDERBY ]['options'] = array();
		MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_MEDIA_MODAL_ORDERBY ]['texts'] = array();
		$found_current = false;
		foreach ($options as $key => $value ) {
			MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_MEDIA_MODAL_ORDERBY ]['options'][] = $value;
			MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_MEDIA_MODAL_ORDERBY ]['texts'][] = $key;
			if ( $current == $value ) {
				$found_current = true;
			}
		}

		if ( ! $found_current ) {
			MLACore::mla_delete_option( MLACoreOptions::MLA_MEDIA_MODAL_ORDERBY );
		}

		$options_list = '';
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'general' == $value['tab'] ) {
				$options_list .= self::mla_compose_option_row( $key, $value );
			}
		}

		$page_values['options_list'] = $options_list;
		$page_values['import_settings'] = self::_compose_import_settings();
		$page_content['body'] = MLAData::mla_parse_template( self::$page_template_array['general-tab'], $page_values );
		return $page_content;
	}

	/**
	 * Get the current action selected from the bulk actions dropdown
	 *
	 * @since 1.40
	 *
	 * @return string|false The action name or False if no action was selected
	 */
	private static function _current_bulk_action( )	{
		$action = false;

		if ( isset( $_REQUEST['action'] ) ) {
			if ( -1 != $_REQUEST['action'] ) {
				return $_REQUEST['action'];
			}

			$action = 'none';
		} // isset action

		if ( isset( $_REQUEST['action2'] ) ) {
			if ( -1 != $_REQUEST['action2'] ) {
				return $_REQUEST['action2'];
			}

			$action = 'none';
		} // isset action2

		return $action;
	}

	/**
	 * Compose the Edit View tab content for the Settings subpage
	 *
	 * @since 1.40
	 *
	 * @param	array	data values for the item
	 * @param	string	Display template
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_edit_view_tab( $view, $template ) {
		$page_values = array(
			'Edit View' => __( 'Edit View', 'media-library-assistant' ),
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-view&mla_tab=view',
			'action' => MLA::MLA_ADMIN_SINGLE_EDIT_UPDATE,
			'original_slug' => $view['slug'],
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'Slug' => __( 'Slug', 'media-library-assistant' ),
			'The slug is' => __( 'The &#8220;slug&#8221; is the URL-friendly, unique key for the view. It must be all lowercase and contain only letters, numbers, periods (.), slashes (/) and hyphens (-). For &#8220;<strong>Post MIME Type</strong>&#8221; views, the slug is also the MIME type specification and <strong>must be a valid MIME</strong> type, e.g., &#8220;image&#8221; or &#8220;image/jpeg&#8221;.', 'media-library-assistant' ),
			'Singular Label' => __( 'Singular Label', 'media-library-assistant' ),
			'Plural Label' => __( 'Plural Label', 'media-library-assistant' ),
			'The labels' => __( 'The labels, e.g., &#8220;Image&#8221; and &#8220;Images&#8221; are used for column headers and other display purposes.', 'media-library-assistant' ),
			'Specification' => __( 'Specification', 'media-library-assistant' ),
			'If the specification' => __( 'If the MIME type specification differs from the slug, enter it here. You may include multiple MIME types, e.g., &#8220;audio,video&#8221; and/or wildcard specs, e.g.,  &#8220;*/*ms*&#8221;. This field will be ignored if the Post MIME Type box is checked.', 'media-library-assistant' ),
			'Post MIME Type' => __( 'Post MIME Type', 'media-library-assistant' ),
			'Check Post MIME' => __( 'Check this box if you want to add this entry to the list of MIME types returned by wp_get_mime_types().', 'media-library-assistant' ),
			'Table View' => __( 'Table View', 'media-library-assistant' ),
			'Check Table View' => __( 'Check this box if you want to add this entry to the list of Media/Assistant table views.', 'media-library-assistant' ),
			'Menu Order' => __( 'Menu Order', 'media-library-assistant' ),
			'You can choose' => __( 'You can choose your own table view order by entering a number (1 for first, etc.) in this field.', 'media-library-assistant' ),
			'Description' => __( 'Description', 'media-library-assistant' ),
			'The description can' => __( 'The description can contain any documentation or notes you need to understand or use the item.', 'media-library-assistant' ),
			'Update' => __( 'Update', 'media-library-assistant' ),
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
		);

		foreach ( $view as $key => $value ) {
			switch ( $key ) {
				case 'post_mime_type':
				case 'table_view':
					$page_values[ $key ] = $value ? 'checked="checked"' : '';
					break;
				default:
					$page_values[ $key ] = $value;
			}
		}

		return array(
			'message' => '',
			'body' => MLAData::mla_parse_template( $template, $page_values )
		);
	}

	/**
	 * Compose the Post MIME Type Views tab content for the Settings subpage
	 *
	 * @since 1.40
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_view_tab( ) {
		$page_template_array = MLACore::mla_load_template( 'admin-display-settings-view-tab.tpl' );
		if ( ! is_array( $page_template_array ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			error_log( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLASettings::_compose_view_tab', var_export( $page_template_array, true ) ), 0 );
			return '';
		}

		/*
		 * Convert checkbox values, if present
		 */
		if ( isset( $_REQUEST['mla_view_item'] ) ) {
			$_REQUEST['mla_view_item']['post_mime_type'] = isset( $_REQUEST['mla_view_item']['post_mime_type'] );
			$_REQUEST['mla_view_item']['table_view'] = isset( $_REQUEST['mla_view_item']['table_view'] );
		}

		/*
		 * Set default values, check for Add New Post MIME Type View button
		 */
		$add_form_values = array (
			'slug' => '',
			'singular' => '',
			'plural' => '',
			'specification' => '',
			'post_mime_type' => 'checked="checked"',
			'table_view' => 'checked="checked"',
			'menu_order' => '',
			'description' => ''
			);

		if ( !empty( $_REQUEST['mla-view-options-save'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_save_view_settings( );
		} elseif ( !empty( $_REQUEST['mla-add-view-submit'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = MLAMime::mla_add_post_mime_type( $_REQUEST['mla_view_item'] );
			if ( false !== strpos( $page_content['message'], __( 'ERROR', 'media-library-assistant' ) ) ) {
				$add_form_values = $_REQUEST['mla_view_item'];
				$add_form_values['post_mime_type'] = $add_form_values['post_mime_type'] ? 'checked="checked"' : '';
				$add_form_values['table_view'] = $add_form_values['table_view'] ? 'checked="checked"' : '';
			}
		} else {
			$page_content = array(
				'message' => '',
				'body' => '' 
			);
		}

		/*
		 * Process bulk actions that affect an array of items
		 */
		$bulk_action = self::_current_bulk_action();
		if ( $bulk_action && ( $bulk_action != 'none' ) ) {
			if ( isset( $_REQUEST['cb_mla_item_ID'] ) ) {
				/*
				 * Convert post-ID to slug; separate loop required because delete changes post_IDs
				 */
				$slugs = array();
				foreach ( $_REQUEST['cb_mla_item_ID'] as $post_ID )
					$slugs[] = MLAMime::mla_get_post_mime_type_slug( $post_ID );

				foreach ( $slugs as $slug ) {
					switch ( $bulk_action ) {
						case 'delete':
							$item_content = MLAMime::mla_delete_post_mime_type( $slug );
							break;
						case 'edit':
							$request = array( 'slug' => $slug );
							if ( '-1' != $_REQUEST['post_mime_type'] ) {
								$request['post_mime_type'] = '1' == $_REQUEST['post_mime_type'];
							}
							if ( '-1' != $_REQUEST['table_view'] ) {
								$request['table_view'] = '1' == $_REQUEST['table_view'];
							}
							if ( !empty( $_REQUEST['menu_order'] ) ) {
								$request['menu_order'] = $_REQUEST['menu_order'];
							}
							$item_content = MLAMime::mla_update_post_mime_type( $request );
							break;
						default:
							$item_content = array(
								/* translators: 1: bulk_action, e.g., delete, edit, restore, trash */
								 'message' => sprintf( __( 'Unknown bulk action %1$s', 'media-library-assistant' ), $bulk_action ),
								'body' => '' 
							);
					} // switch $bulk_action

					$page_content['message'] .= $item_content['message'] . '<br>';
				} // foreach cb_attachment
			} // isset cb_attachment
			else {
				/* translators: 1: action name, e.g., edit */
				$page_content['message'] = sprintf( __( 'Bulk Action %1$s - no items selected.', 'media-library-assistant' ), $bulk_action );
			}
		} // $bulk_action

		/*
		 * Process row-level actions that affect a single item
		 */
		if ( !empty( $_REQUEST['mla_admin_action'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

			switch ( $_REQUEST['mla_admin_action'] ) {
				case MLACore::MLA_ADMIN_SINGLE_DELETE:
					$page_content = MLAMime::mla_delete_post_mime_type( $_REQUEST['mla_item_slug'] );
					break;
				case MLA::MLA_ADMIN_SINGLE_EDIT_DISPLAY:
					$view = MLAMime::mla_get_post_mime_type( $_REQUEST['mla_item_slug'] );
					$page_content = self::_compose_edit_view_tab( $view, $page_template_array['single-item-edit'] );
					break;
				case MLA::MLA_ADMIN_SINGLE_EDIT_UPDATE:
					if ( !empty( $_REQUEST['update'] ) ) {
						$page_content = MLAMime::mla_update_post_mime_type( $_REQUEST['mla_view_item'] );
						if ( false !== strpos( $page_content['message'], __( 'ERROR', 'media-library-assistant' ) ) ) {
							$message = $page_content['message'];
							$page_content = self::_compose_edit_view_tab( $_REQUEST['mla_view_item'], $page_template_array['single-item-edit'] );
							$page_content['message'] = $message;
						}
			} else {
						$page_content = array(
							/* translators: 1: view name/slug */
							'message' => sprintf( __( 'Edit view "%1$s" cancelled.', 'media-library-assistant' ), $_REQUEST['mla_view_item']['original_slug'] ),
							'body' => '' 
						);
					}
					break;
				default:
					$page_content = array(
						/* translators: 1: bulk_action, e.g., single_item_delete, single_item_edit */
						 'message' => sprintf( __( 'Unknown mla_admin_action - "%1$s"', 'media-library-assistant' ), $_REQUEST['mla_admin_action'] ),
						'body' => '' 
					);
					break;
			} // switch ($_REQUEST['mla_admin_action'])
		} // (!empty($_REQUEST['mla_admin_action'])

		if ( !empty( $page_content['body'] ) ) {
			return $page_content;
		}

		/*
		 * Check for disabled status
		 */
		if ( 'checked' != MLACore::mla_get_option( MLACoreOptions::MLA_ENABLE_POST_MIME_TYPES ) ) {
			/*
			 * Fill in with any page-level options
			 */
			$options_list = '';
			foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
				if ( 'view' == $value['tab'] ) {
					$options_list .= self::mla_compose_option_row( $key, $value );
				}
			}

			$page_values = array(
				'Support is disabled' => __( 'View and Post MIME Type Support is disabled', 'media-library-assistant' ),
				'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-view&mla_tab=view',
				'options_list' => $options_list,
				'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
				'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			);

			$page_content['body'] .= MLAData::mla_parse_template( $page_template_array['view-disabled'], $page_values );
			return $page_content;
		}

		/*
		 * Display the View Table
		 */
		$_SERVER['REQUEST_URI'] = remove_query_arg( array(
			'mla_admin_action',
			'mla_item_slug',
			'mla_item_ID',
			'_wpnonce',
			'_wp_http_referer',
			'action',
			'action2',
			'cb_mla_item_ID',
			'mla-optional-uploads-search',
			'mla-optional-uploads-display'
		), $_SERVER['REQUEST_URI'] );

		//	Create an instance of our package class
		$MLAListViewTable = new MLA_View_List_Table();

		//	Fetch, prepare, sort, and filter our data
		$MLAListViewTable->prepare_items();
		$MLAListViewTable->views();

		/*
		 * Start with any page-level options
		 */
		$options_list = '';
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'view' == $value['tab'] ) {
				$options_list .= self::mla_compose_option_row( $key, $value );
			}
		}

		$page_values = array(
			'Library Views Processing' => __( 'Library Views/Post MIME Type Processing', 'media-library-assistant' ),
			'In this tab' => __( 'In this tab you can manage the list of "Post MIME Types", which are used by WordPress to define the views for the <em><strong>Media/Library</strong></em> screen and the <em><strong>Media Manager/Add Media</strong></em> "media items" drop down list. MLA&rsquo;s <em><strong>Media/Assistant</strong></em> screen uses an enhanced version of the list, <em>Table Views</em>, to support views with multiple MIME Types (e.g., "audio,video") and wildcard specifications (e.g. "*/*ms*").', 'media-library-assistant' ),
			/* translators: 1: Documentation hyperlink */
			'You can find' => sprintf( __( 'You can find more information about library views, Post MIME types and how MLA and WordPress use them in the %1$s section of the Documentation or by clicking the <strong>"Help"</strong> tab in the upper-right corner of this screen.', 'media-library-assistant' ), '<a href="[+settingsURL+]?page=mla-settings-menu-documentation&amp;mla_tab=documentation#mla_views" title="' . __( 'Library View Processing documentation', 'media-library-assistant' ) . '">' . __( 'Library Views/Post MIME Type Processing', 'media-library-assistant' ) . '</a>' ),
			'settingsURL' => admin_url('options-general.php'),
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-view&mla_tab=view',
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'results' => ! empty( $_REQUEST['s'] ) ? '<h2 class="alignleft">' . __( 'Displaying search results for', 'media-library-assistant' ) . ': "' . $_REQUEST['s'] . '"</h2>' : '',
			'Search Views' => __( 'Search Views', 'media-library-assistant' ),
			's' => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
			'options_list' => $options_list,
			'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
			/* translators: %s: add new View */
			'Add New View' => sprintf( __( 'Add New %1$s', 'media-library-assistant' ), __( 'View', 'media-library-assistant' ) ),
			'Slug' => __( 'Slug', 'media-library-assistant' ),
			'The slug is' => __( 'The &#8220;slug&#8221; is the URL-friendly, unique key for the view. It must be all lowercase and contain only letters, numbers, periods (.), slashes (/) and hyphens (-). For &#8220;<strong>Post MIME Type</strong>&#8221; views, the slug is also the MIME type specification and <strong>must be a valid MIME</strong> type, e.g., &#8220;image&#8221; or &#8220;image/jpeg&#8221;.', 'media-library-assistant' ),
			'Singular Label' => __( 'Singular Label', 'media-library-assistant' ),
			'Plural Label' => __( 'Plural Label', 'media-library-assistant' ),
			'The labels' => __( 'The labels, e.g., &#8220;Image&#8221; and &#8220;Images&#8221; are used for column headers and other display purposes.', 'media-library-assistant' ),
			'Specification' => __( 'Specification', 'media-library-assistant' ),
			'If the specification' => __( 'If the MIME type specification differs from the slug, enter it here. You may include multiple MIME types, e.g., &#8220;audio,video&#8221; and/or wildcard specs, e.g.,  &#8220;*/*ms*&#8221;. This field will be ignored if the Post MIME Type box is checked.', 'media-library-assistant' ),
			'Post MIME Type' => __( 'Post MIME Type', 'media-library-assistant' ),
			'Check Post MIME' => __( 'Check this box if you want to add this entry to the list of MIME types returned by wp_get_mime_types().', 'media-library-assistant' ),
			'Table View' => __( 'Table View', 'media-library-assistant' ),
			'Check Table View' => __( 'Check this box if you want to add this entry to the list of Media/Assistant table views.', 'media-library-assistant' ),
			'Menu Order' => __( 'Menu Order', 'media-library-assistant' ),
			'You can choose' => __( 'You can choose your own table view order by entering a number (1 for first, etc.) in this field.', 'media-library-assistant' ),
			'Description' => __( 'Description', 'media-library-assistant' ),
			'The description can' => __( 'The description can contain any documentation or notes you need to understand or use the item.', 'media-library-assistant' ),
			'Add View' => __( 'Add View', 'media-library-assistant' ),
			'colspan' => $MLAListViewTable->get_column_count(),
			'Quick Edit' => __( '<strong>Quick Edit</strong>', 'media-library-assistant' ),
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
			'Update' => __( 'Update', 'media-library-assistant' ),
			'Bulk Edit' => __( 'Bulk Edit', 'media-library-assistant' ),
			'No Change' => __( 'No Change', 'media-library-assistant' ),
			'No' => __( 'No', 'media-library-assistant' ),
			'Yes' => __( 'Yes', 'media-library-assistant' ),
		);

		foreach ( $add_form_values as $key => $value ) {
			$page_values[ $key ] = $value;
		}
		$page_content['body'] = MLAData::mla_parse_template( $page_template_array['before-table'], $page_values );

		//	 Now we can render the completed list table
		ob_start();
		$MLAListViewTable->display();
		$page_content['body'] .= ob_get_contents();
		ob_end_clean();

		$page_content['body'] .= MLAData::mla_parse_template( $page_template_array['after-table'], $page_values );

		return $page_content;
	}

	/**
	 * Get an HTML select element representing a list of icon types
	 *
	 * @since 1.40
	 *
	 * @param	array	Display template array
	 * @param	string	HTML name attribute value
	 * @param	string	currently selected Icon Type
	 *
	 * @return string HTML select element or empty string on failure.
	 */
	public static function mla_get_icon_type_dropdown( $templates, $name, $selection = '.none.' ) {
		$option_template = $templates['icon-type-select-option'];
		if ( '.nochange.' == $selection ) {
			$option_values = array (
				'selected' => 'selected="selected"',
				'value' => '.none.',
				'text' => '&mdash; ' . __( 'No Change', 'media-library-assistant' ) . ' &mdash;'
			);
		} else {
			$option_values = array (
				'selected' => ( '.none.' == $selection ) ? 'selected="selected"' : '',
				'value' => '.none.',
				'text' => '&mdash; ' . __( 'None (select a value)', 'media-library-assistant' ) . ' &mdash;'
			);
		}

		$options = MLAData::mla_parse_template( $option_template, $option_values );

		$icon_types = MLAMime::mla_get_current_icon_types(); 
		foreach ( $icon_types as $icon_type ) {
			$option_values = array (
				'selected' => ( $icon_type == $selection ) ? 'selected="selected"' : '',
				'value' => $icon_type,
				'text' => $icon_type
			);

			$options .= MLAData::mla_parse_template( $option_template, $option_values );					
		} // foreach icon_type

		return MLAData::mla_parse_template( $templates['icon-type-select'], array( 'name' => $name, 'options' => $options ) );
	}

	/**
	 * Compose the Edit Upload type tab content for the Settings subpage
	 *
	 * @since 1.40
	 *
	 * @param	array	data values for the item
	 * @param	string	Display template array
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_edit_upload_tab( $item, &$templates ) {
		$page_values = array(
			'Edit Upload MIME' => __( 'Edit Upload MIME Type', 'media-library-assistant' ),
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-upload&mla_tab=upload',
			'action' => MLA::MLA_ADMIN_SINGLE_EDIT_UPDATE,
			'original_slug' => $item['slug'],
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'Extension' => __( 'Extension', 'media-library-assistant' ),
			'The extension is' => __( 'The &#8220;extension&#8221; is the file extension for this type, and a unique key for the item. It must be all lowercase and contain only letters and numbers.', 'media-library-assistant' ),
			'MIME Type' => __( 'MIME Type', 'media-library-assistant' ),
			'The MIME Type' => __( 'The MIME Type must be all lowercase and contain only letters, numbers, periods (.), slashes (/) and hyphens (-). It <strong>must be a valid MIME</strong> type, e.g., &#8220;image&#8221; or &#8220;image/jpeg&#8221;.', 'media-library-assistant' ),
			'Icon Type' => __( 'Icon Type', 'media-library-assistant' ),
			'icon_types' => self::mla_get_icon_type_dropdown( $templates, 'mla_upload_item[icon_type]', $item['icon_type'] ),
			'The Icon Type' => __( 'The Icon Type selects a thumbnail image displayed for non-image file types, such as PDF documents.', 'media-library-assistant' ),
			'Inactive' => __( 'Inactive', 'media-library-assistant' ),
			'Check this box' => __( 'Check this box if you want to remove this entry from the list of Upload MIME Types returned by get_allowed_mime_types().', 'media-library-assistant' ),
			'Description' => __( 'Description', 'media-library-assistant' ),
			'The description can' => __( 'The description can contain any documentation or notes you need to understand or use the item.', 'media-library-assistant' ),
			'Update' => __( 'Update', 'media-library-assistant' ),
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
		);

		foreach ( $item as $key => $value ) {
			switch ( $key ) {
				case 'disabled':
					$page_values[ $key ] = $value ? 'checked="checked"' : '';
					break;
				default:
					$page_values[ $key ] = $value;
			}
		}

		return array(
			'message' => '',
			'body' => MLAData::mla_parse_template( $templates['single-item-edit'], $page_values )
		);
	}

	/**
	 * Compose the Optional File Upload MIME Types tab content for the Settings subpage
	 *
	 * @since 1.40
	 *
	 * @param	string	Display templates
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_optional_upload_tab( $page_template_array ) {
		/*
		 * Display the Optional Upload MIME Types Table
		 */
		$_SERVER['REQUEST_URI'] = add_query_arg( array( 'mla-optional-uploads-display' => 'true' ), remove_query_arg( array(
			'mla_admin_action',
			'mla_item_slug',
			'mla_item_ID',
			'_wpnonce',
			'_wp_http_referer',
			'action',
			'action2',
			'cb_attachment',
			'mla-optional-uploads-search'
		), $_SERVER['REQUEST_URI'] ) );

			/*
			 * Suppress display of the hidden columns selection list
			 */
			echo "  <style type='text/css'>\r\n";
			echo "    form#adv-settings div.metabox-prefs {\r\n";
			echo "      display: none;\r\n";
			echo "    }\r\n";
			echo "  </style>\r\n";

		//	Create an instance of our package class
		$MLAListUploadTable = new MLA_Upload_Optional_List_Table();

		//	Fetch, prepare, sort, and filter our data
		$MLAListUploadTable->prepare_items();

		$page_content = array(
			'message' => '',
			'body' => '' 
		);

		$page_values = array(
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-upload&mla_tab=upload',
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'Known File Extension' => __( 'Known File Extension/MIME Type Associations', 'media-library-assistant' ),
			'results' => ! empty( $_REQUEST['s'] ) ? '<h2 class="alignleft">' . __( 'Displaying search results for', 'media-library-assistant' ) . ': "' . $_REQUEST['s'] . '"</h2>' : '',
			'Search Known MIME' => __( 'Search Known MIME Types', 'media-library-assistant' ),
			's' => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
			'To search by' => __( 'To search by extension, use ".", e.g., ".doc"', 'media-library-assistant' ),
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
		);

		$page_content['body'] = MLAData::mla_parse_template( $page_template_array['before-optional-uploads-table'], $page_values );

		//	 Now we can render the completed list table
		ob_start();
//		$MLAListUploadTable->views();
		$MLAListUploadTable->display();
		$page_content['body'] .= ob_get_contents();
		ob_end_clean();

		$page_content['body'] .= MLAData::mla_parse_template( $page_template_array['after-optional-uploads-table'], $page_values );

		return $page_content;
	}

	/**
	 * Process an Optional Upload MIME Type selection
	 *
	 * @since 1.40
 	 *
	 * @param	intger	MLA Optional Upload MIME Type ID
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _process_optional_upload_mime( $ID ) {
		$optional_type = MLAMime::mla_get_optional_upload_mime( $ID );
		$optional_type['disabled'] = false;

		if ( false === $upload_type = MLAMime::mla_get_upload_mime( $optional_type['slug'] ) ) {
			$optional_type['icon_type'] = '.none.';
			return MLAMime::mla_add_upload_mime( $optional_type );
		}

		$optional_type['original_slug'] = $optional_type['slug'];
		return MLAMime::mla_update_upload_mime( $optional_type );
	}

	/**
	 * Compose the File Upload MIME Types tab content for the Settings subpage
	 *
	 * @since 1.40
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_upload_tab( ) {
		$page_template_array = MLACore::mla_load_template( 'admin-display-settings-upload-tab.tpl' );
		if ( ! is_array( $page_template_array ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			error_log( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLASettings::_compose_upload_tab', var_export( $page_template_array, true ) ), 0 );
			return '';
		}

		/*
		 * Untangle confusion between searching, canceling and selecting on the Optional Uploads screen
		 */
		$bulk_action = self::_current_bulk_action();
		if ( isset( $_REQUEST['mla-optional-uploads-cancel'] ) || $bulk_action && ( $bulk_action == 'select' ) ) {
			unset( $_REQUEST['mla-optional-uploads-search'] );
			unset( $_REQUEST['s'] );
		}

		/*
		 * Convert checkbox values, if present
		 */
		if ( isset( $_REQUEST['mla_upload_item'] ) ) {
			$_REQUEST['mla_upload_item']['disabled'] = isset( $_REQUEST['mla_upload_item']['disabled'] );
		}

		/*
		 * Set default values, check for Add New Upload MIME Type button
		 */
		$add_form_values = array (
			'slug' => '',
			'mime_type' => '',
			'icon_type' => '.none.',
			'disabled' => '',
			'description' => ''
			);

		if ( !empty( $_REQUEST['mla-upload-options-save'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_save_upload_settings( );
		} elseif ( !empty( $_REQUEST['mla-optional-uploads-search'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_compose_optional_upload_tab( $page_template_array );
		} elseif ( !empty( $_REQUEST['mla-optional-uploads-cancel'] ) ) {
			$page_content = array(
				 'message' => '',
				'body' => '' 
			);
		} elseif ( !empty( $_REQUEST['mla-optional-uploads-display'] ) ) {
			if ( 'true' != $_REQUEST['mla-optional-uploads-display'] ) {
				check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
				unset( $_REQUEST['s'] );
			}
			$page_content = self::_compose_optional_upload_tab( $page_template_array );
		} elseif ( !empty( $_REQUEST['mla-add-upload-submit'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = MLAMime::mla_add_upload_mime( $_REQUEST['mla_upload_item'] );
			if ( false !== strpos( $page_content['message'], __( 'ERROR', 'media-library-assistant' ) ) ) {
				$add_form_values = $_REQUEST['mla_upload_item'];
				$add_form_values['disabled'] = $add_form_values['disabled'] ? 'checked="checked"' : '';
			}
		} else {
			$page_content = array(
				 'message' => '',
				'body' => '' 
			);
		}

		/*
		 * Process bulk actions that affect an array of items
		 */
		if ( $bulk_action && ( $bulk_action != 'none' ) ) {
			if ( isset( $_REQUEST['cb_mla_item_ID'] ) ) {
				if ( 'select' == $bulk_action ) {
					foreach ( $_REQUEST['cb_mla_item_ID'] as $ID ) {
						$item_content = MLASettings::_process_optional_upload_mime( $ID );
						$page_content['message'] .= $item_content['message'] . '<br>';
					}
				} else {
					/*
					 * Convert post-ID to slug; separate loop required because delete changes post_IDs
					 */
					$slugs = array();
					foreach ( $_REQUEST['cb_mla_item_ID'] as $post_ID )
						$slugs[] = MLAMime::mla_get_upload_mime_slug( $post_ID );

					foreach ( $slugs as $slug ) {
						switch ( $bulk_action ) {
							case 'delete':
								$item_content = MLAMime::mla_delete_upload_mime( $slug );
								break;
							case 'edit':
								$request = array( 'slug' => $slug );
								if ( '-1' != $_REQUEST['disabled'] ) {
									$request['disabled'] = '1' == $_REQUEST['disabled'];
								}
								if ( '.none.' != $_REQUEST['icon_type'] ) {
									$request['icon_type'] = $_REQUEST['icon_type'];
								}
								$item_content = MLAMime::mla_update_upload_mime( $request );
								break;
							default:
								$item_content = array(
									/* translators: 1: bulk_action, e.g., delete, edit, restore, trash */
									 'message' => sprintf( __( 'Unknown bulk action %1$s', 'media-library-assistant' ), $bulk_action ),
									'body' => '' 
								);
						} // switch $bulk_action

						$page_content['message'] .= $item_content['message'] . '<br>';
					} // foreach cb_attachment
				} // != select
			} // isset cb_attachment
			else {
				/* translators: 1: action name, e.g., edit */
				$page_content['message'] = sprintf( __( 'Bulk Action %1$s - no items selected.', 'media-library-assistant' ), $bulk_action );
			}
		} // $bulk_action

		/*
		 * Process row-level actions that affect a single item
		 */
		if ( !empty( $_REQUEST['mla_admin_action'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

			switch ( $_REQUEST['mla_admin_action'] ) {
				case MLACore::MLA_ADMIN_SINGLE_DELETE:
					$page_content = MLAMime::mla_delete_upload_mime( $_REQUEST['mla_item_slug'] );
					break;
				case MLA::MLA_ADMIN_SINGLE_EDIT_DISPLAY:
					$view = MLAMime::mla_get_upload_mime( $_REQUEST['mla_item_slug'] );
					$page_content = self::_compose_edit_upload_tab( $view, $page_template_array );
					break;
				case MLA::MLA_ADMIN_SINGLE_EDIT_UPDATE:
					if ( !empty( $_REQUEST['update'] ) ) {
						$page_content = MLAMime::mla_update_upload_mime( $_REQUEST['mla_upload_item'] );
						if ( false !== strpos( $page_content['message'], __( 'ERROR', 'media-library-assistant' ) ) ) {
							$message = $page_content['message'];
							$page_content = self::_compose_edit_upload_tab( $_REQUEST['mla_upload_item'], $page_template_array );
							$page_content['message'] = $message;
						}
					} elseif ( !empty( $_REQUEST['mla_item_ID'] ) ) {
						$page_content = self::_process_optional_upload_mime( $_REQUEST['mla_item_ID'] );
					} else {
						$page_content = array(
							/* translators: 1: view name/slug */
							'message' => sprintf( __( 'Edit view "%1$s" cancelled.', 'media-library-assistant' ), $_REQUEST['mla_upload_item']['original_slug'] ),
							'body' => '' 
						);
					}
					break;
				default:
					$page_content = array(
						/* translators: 1: bulk_action, e.g., single_item_delete, single_item_edit */
						 'message' => sprintf( __( 'Unknown mla_admin_action - "%1$s"', 'media-library-assistant' ), $_REQUEST['mla_admin_action'] ),
						'body' => '' 
					);
					break;
			} // switch ($_REQUEST['mla_admin_action'])
		} // (!empty($_REQUEST['mla_admin_action'])

		if ( !empty( $page_content['body'] ) ) {
			return $page_content;
		}

		/*
		 * Check for disabled status
		 */
		if ( 'checked' != MLACore::mla_get_option( MLACoreOptions::MLA_ENABLE_UPLOAD_MIMES ) ) {
			/*
			 * Fill in with any page-level options
			 */
			$options_list = '';
			foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
				if ( 'upload' == $value['tab'] ) {
					$options_list .= self::mla_compose_option_row( $key, $value );
				}
			}

			$page_values = array(
				'Support is disabled' => __( 'Upload MIME Type Support is disabled', 'media-library-assistant' ),
				'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-upload&mla_tab=upload',
				'options_list' => $options_list,
				'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
				'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			);

			$page_content['body'] .= MLAData::mla_parse_template( $page_template_array['upload-disabled'], $page_values );
			return $page_content;
		}

		/*
		 * Display the Upload MIME Types Table
		 */
		$_SERVER['REQUEST_URI'] = remove_query_arg( array(
			'mla_admin_action',
			'mla_item_slug',
			'mla_item_ID',
			'_wpnonce',
			'_wp_http_referer',
			'action',
			'action2',
			'cb_mla_item_ID',
			'mla-optional-uploads-search',
		), $_SERVER['REQUEST_URI'] );

		//	Create an instance of our package class
		$MLAListUploadTable = new MLA_Upload_List_Table();

		//	Fetch, prepare, sort, and filter our data
		$MLAListUploadTable->prepare_items();

		/*
		 * Start with any page-level options
		 */
		$options_list = '';
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'upload' == $value['tab'] ) {
				$options_list .= self::mla_compose_option_row( $key, $value );
			}
		}

		$page_values = array(
			'File Extension Processing' => __( 'File Extension and MIME Type Processing', 'media-library-assistant' ),
			'In this tab' => __( 'In this tab you can manage the list of file extension/MIME Type associations, which are used by WordPress to decide what kind of files can be uploaded to the Media Library and to fill in the <strong><em>post_mime_type</em></strong> value. To upload a file, the file extension must be in this list and be active.', 'media-library-assistant' ),
			/* translators: 1: Documentation hyperlink */
			'You can find' => sprintf( __( 'You can find more information about file extensions, MIME types and how WordPress uses them in the %1$s section of the Documentation or by clicking the <strong>"Help"</strong> tab in the upper-right corner of this screen.', 'media-library-assistant' ), '<a href="[+settingsURL+]?page=mla-settings-menu-documentation&amp;mla_tab=documentation#mla_uploads" title="' . __( 'File Extension Processing documentation', 'media-library-assistant' ) . '">' . __( 'File Extension and MIME Type Processing', 'media-library-assistant' ) . '</a>' ),
			'settingsURL' => admin_url('options-general.php'),
			'Search Uploads' => __( 'Search Uploads', 'media-library-assistant' ),
			'To search by' => __( 'To search by extension, use ".", e.g., ".doc"', 'media-library-assistant' ),
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-upload&mla_tab=upload',
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'options_list' => $options_list,
			'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
			/* translators: %s: add new Upload MIME Type */
			'Add New Upload' => sprintf( __( 'Add New %1$s', 'media-library-assistant' ), __( 'Upload MIME Type', 'media-library-assistant' ) ),
			'To search database' => __( 'To search the database of over 1,500 known extension/type associations, click "Search Known Types" below the form.', 'media-library-assistant' ),
			'Extension' => __( 'Extension', 'media-library-assistant' ),
			'The extension is' => __( 'The &#8220;extension&#8221; is the file extension for this type, and unique key for the item. It must be all lowercase and contain only letters and numbers.', 'media-library-assistant' ),
			'MIME Type' => __( 'MIME Type', 'media-library-assistant' ),
			'The MIME Type' => __( 'The MIME Type must be all lowercase and contain only letters, numbers, periods (.), slashes (/) and hyphens (-). It <strong>must be a valid MIME</strong> type, e.g., &#8220;image&#8221; or &#8220;image/jpeg&#8221;.', 'media-library-assistant' ),
			'Icon Type' => __( 'Icon Type', 'media-library-assistant' ),
			'The Icon Type' => __( 'The Icon Type selects a thumbnail image displayed for non-image file types, such as PDF documents.', 'media-library-assistant' ),
			'Inactive' => __( 'Inactive', 'media-library-assistant' ),
			'Check this box' => __( 'Check this box if you want to remove this entry from the list of Upload MIME Types returned by get_allowed_mime_types().', 'media-library-assistant' ),
			'Description' => __( 'Description', 'media-library-assistant' ),
			'The description can' => __( 'The description can contain any documentation or notes you need to understand or use the item.', 'media-library-assistant' ),
			'Add Upload MIME' => __( 'Add Upload MIME Type', 'media-library-assistant' ),
			'search_url' => wp_nonce_url( '?page=mla-settings-menu-upload&mla_tab=upload&mla-optional-uploads-search=Search', MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ),
			'Search Known Types' => __( 'Search Known Types', 'media-library-assistant' ),
			'colspan' => $MLAListUploadTable->get_column_count(),
			'Quick Edit' => __( '<strong>Quick Edit</strong>', 'media-library-assistant' ),
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
			'Update' => __( 'Update', 'media-library-assistant' ),
			'Bulk Edit' => __( 'Bulk Edit', 'media-library-assistant' ),
			'Status' => __( 'Status', 'media-library-assistant' ),
			'No Change' => __( 'No Change', 'media-library-assistant' ),
			'Active' => __( 'Active', 'media-library-assistant' ),
			'results' => ! empty( $_REQUEST['s'] ) ? '<h2 class="alignleft">' . __( 'Displaying search results for', 'media-library-assistant' ) . ': "' . $_REQUEST['s'] . '"</h2>' : '',
			's' => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
			'icon_types' => self::mla_get_icon_type_dropdown( $page_template_array, 'mla_upload_item[icon_type]' ),
			'inline_icon_types' => self::mla_get_icon_type_dropdown( $page_template_array, 'icon_type' ),
			'bulk_icon_types' => self::mla_get_icon_type_dropdown( $page_template_array, 'icon_type', '.nochange.' ),
		);

		foreach ( $add_form_values as $key => $value ) {
			$page_values[ $key ] = $value;
		}
		$page_content['body'] = MLAData::mla_parse_template( $page_template_array['before-table'], $page_values );

		//	 Now we can render the completed list table
		ob_start();
		$MLAListUploadTable->views();
		$MLAListUploadTable->display();
		$page_content['body'] .= ob_get_contents();
		ob_end_clean();

		$page_content['body'] .= MLAData::mla_parse_template( $page_template_array['after-table'], $page_values );

		return $page_content;
	}

	/**
	 * Compose the MLA Gallery tab content for the Settings subpage
	 *
	 * @since 0.80
	 * @uses $page_template_array contains tab content template(s)
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_mla_gallery_tab( ) {
		/*
		 * Check for submit buttons to change or reset settings.
		 * Initialize page messages and content.
		 */
		if ( !empty( $_REQUEST['mla-gallery-options-save'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_save_gallery_settings( );
		} else {
			$page_content = array(
				'message' => '',
				'body' => '' 
			);
		}

		if ( !empty( $page_content['body'] ) ) {
			return $page_content;
		}

		$page_values = array(
			'MLA Gallery Options' => __( 'MLA Gallery Options', 'media-library-assistant' ),
			'Go to Style Templates' => __( 'Go to Style Templates', 'media-library-assistant' ),
			'Go to Markup Templates' => __( 'Go to Markup Templates', 'media-library-assistant' ),
			'In this tab' => __( 'In this tab you can view the default style and markup templates. You can also define additional templates and use the <code>mla_style</code> and <code>mla_markup</code> parameters to apply them in your <code>[mla_gallery]</code> shortcodes. <strong>NOTE:</strong> template additions and changes will not be made permanent until you click "Save Changes" at the bottom of this page.', 'media-library-assistant' ),
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-mla_gallery&mla_tab=mla_gallery',
			'options_list' => '',
			'Go to Top' => __( 'Go to Top', 'media-library-assistant' ),
			'Style Templates' => __( 'Style Templates', 'media-library-assistant' ),
			'style_options_list' => '',
			'Markup Templates' => __( 'Markup Templates', 'media-library-assistant' ),
			'markup_options_list' => '',
			'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'_wp_http_referer' => wp_referer_field( false )
		);

		/*
		 * Build default template selection lists; leave out the [mla_tag_cloud] templates
		 */
		MLACoreOptions::$mla_option_definitions['default_style']['options'][] = 'none';
		MLACoreOptions::$mla_option_definitions['default_style']['texts'][] = '&mdash; ' . __( 'None', 'media-library-assistant' ) . ' &mdash;';
		MLACoreOptions::$mla_option_definitions['default_style']['options'][] = 'theme';
		MLACoreOptions::$mla_option_definitions['default_style']['texts'][] = '&mdash; ' . __( 'Theme', 'media-library-assistant' ) . ' &mdash;';

		$templates = MLAOptions::mla_get_style_templates();
		ksort($templates);
		foreach ($templates as $key => $value ) {
			if ( 'tag-cloud' == $key ) {
				continue;
			}

			MLACoreOptions::$mla_option_definitions['default_style']['options'][] = $key;
			MLACoreOptions::$mla_option_definitions['default_style']['texts'][] = $key;
		}

		$templates = MLAOptions::mla_get_markup_templates();
		ksort($templates);
		foreach ($templates as $key => $value ) {
			if ( in_array( $key, array( 'tag-cloud', 'tag-cloud-dl', 'tag-cloud-ul' ) ) ) {
				continue;
			}

			MLACoreOptions::$mla_option_definitions['default_markup']['options'][] = $key;
			MLACoreOptions::$mla_option_definitions['default_markup']['texts'][] = $key;
		}

		/*
		 * Check for MLA Viewer Support requirements,
		 * starting with Imagick check
		 */
		if ( ! class_exists( 'Imagick' ) ) {
			$not_supported_warning = '<br>&nbsp;&nbsp;' . __( 'Imagick support is not installed.', 'media-library-assistant' );
		} else {
			$not_supported_warning = '';
		}

		$ghostscript_path = MLACore::mla_get_option( 'ghostscript_path' );
		if ( ! MLAShortcode_Support::mla_ghostscript_present( $ghostscript_path, true ) ) {
			$not_supported_warning .= '<br>&nbsp;&nbsp;' . __( 'Ghostscript support is not installed.', 'media-library-assistant' );
		}

		if ( ! empty( $not_supported_warning ) ) {
			MLACoreOptions::$mla_option_definitions['enable_mla_viewer']['help'] = '<strong>' . __( 'WARNING:', 'media-library-assistant' ) . __( ' MLA Viewer support may not be available', 'media-library-assistant' ) . ':</strong>' . $not_supported_warning;
		}

		/*
		 * Start with any page-level options
		 */
		$options_list = '';
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'mla_gallery' == $value['tab'] ) {
				$options_list .= self::mla_compose_option_row( $key, $value );
			}
		}

		$page_values['options_list'] = $options_list;

		/*
		 * Add style templates; defaults go first
		 */
		$default_styles = array( 'default', 'tag-cloud' );
		$style_options_list = '';
		$templates = MLAOptions::mla_get_style_templates();

		foreach ( $default_styles as $default ) {
			$name = $default;
			$value =$templates[$default];
			if ( ! empty( $value ) ) {
				$template_values = array (
					'help' => __( 'This default template cannot be altered or deleted, but you can copy the styles.', 'media-library-assistant' )
				);
				$control_cells = MLAData::mla_parse_template( self::$page_template_array['mla-gallery-default'], $template_values );

				$template_values = array (
					'Name' => __( 'Name', 'media-library-assistant' ),
					'name_name' => "mla_style_templates_name[{$default}]",
					'name_id' => "mla_style_templates_name_{$default}",
					'readonly' => 'readonly="readonly"',
					'name_text' => $default,
					'control_cells' => $control_cells,
					'Styles' => __( 'Styles', 'media-library-assistant' ),
					'value_name' => "mla_style_templates_value[{$default}]",
					'value_id' => "mla_style_templates_value_{$default}",
					'value_text' => esc_textarea( $value ),
					'value_help' => __( 'List of substitution parameters, e.g., [+selector+], on Documentation tab.', 'media-library-assistant' )
				);

				$style_options_list .= MLAData::mla_parse_template( self::$page_template_array['mla-gallery-style'], $template_values );
			} // $value
		} // foreach default

		foreach ( $templates as $name => $value ) {
			$slug = sanitize_title( $name );

			if ( in_array( $name, $default_styles ) ) {
				continue; // already handled above
			}

			$template_values = array (
				'name' => 'mla_style_templates_delete[' . $slug . ']',
				'id' => 'mla_style_templates_delete_' . $slug,
				'value' => __( 'Delete this template', 'media-library-assistant' ),
				'help' => __( 'Check the box to delete this template when you press Update at the bottom of the page.', 'media-library-assistant' )
			);
			$control_cells = MLAData::mla_parse_template( self::$page_template_array['mla-gallery-delete'], $template_values );

			$template_values = array (
				'Name' => __( 'Name', 'media-library-assistant' ),
				'name_name' => 'mla_style_templates_name[' . $slug . ']',
				'name_id' => 'mla_style_templates_name_' . $slug,
				'readonly' => '',
				'name_text' => $slug,
				'control_cells' => $control_cells,
				'Styles' => __( 'Styles', 'media-library-assistant' ),
				'value_name' => 'mla_style_templates_value[' . $slug . ']',
				'value_id' => 'mla_style_templates_value_' . $slug,
				'value_text' => esc_textarea( $value ),
				'value_help' => __( 'List of substitution parameters, e.g., [+selector+], on Documentation tab.', 'media-library-assistant' )
			);

			$style_options_list .= MLAData::mla_parse_template( self::$page_template_array['mla-gallery-style'], $template_values );
		} // foreach $templates

		/*
		 * Add blank style template for additions
		 */
		if ( ! empty( $value ) ) {
			$template_values = array (
				'help' => __( 'Fill in a name and styles to add a new template.', 'media-library-assistant' )
			);
			$control_cells = MLAData::mla_parse_template( self::$page_template_array['mla-gallery-default'], $template_values );

			$template_values = array (
				'Name' => __( 'Name', 'media-library-assistant' ),
				'name_name' => 'mla_style_templates_name[blank]',
				'name_id' => 'mla_style_templates_name_blank',
				'readonly' => '',
				'name_text' => '',
				'control_cells' => $control_cells,
				'Styles' => __( 'Styles', 'media-library-assistant' ),
				'value_name' => 'mla_style_templates_value[blank]',
				'value_id' => 'mla_style_templates_value_blank',
				'value_text' => '',
				'value_help' => __( 'List of substitution parameters, e.g., [+selector+], on Documentation tab.', 'media-library-assistant' )
			);

			$style_options_list .= MLAData::mla_parse_template( self::$page_template_array['mla-gallery-style'], $template_values );
		} // $value

		$page_values['style_options_list'] = $style_options_list;

		/*
		 * Add markup templates; defaults go first
		 */
		$default_markups = array( 'default', 'tag-cloud', 'tag-cloud-ul', 'tag-cloud-dl' );
		$markup_options_list = '';
		$templates = MLAOptions::mla_get_markup_templates();

		foreach ( $default_markups as $default ) {
			$name = $default;
			$value =$templates[$default];
			if ( ! empty( $value ) ) {
				$template_values = array (
					'help' => __( 'This default template cannot be altered or deleted, but you can copy the markup.', 'media-library-assistant' )
				);
				$control_cells = MLAData::mla_parse_template( self::$page_template_array['mla-gallery-default'], $template_values );

				$template_values = array (
					'Name' => __( 'Name', 'media-library-assistant' ),
					'name_name' => "mla_markup_templates_name[{$default}]",
					'name_id' => "mla_markup_templates_name_{$default}",
					'readonly' => 'readonly="readonly"',
					'name_text' => $default,
					'control_cells' => $control_cells,

					'Arguments' => __( 'Arguments', 'media-library-assistant' ),
					'arguments_name' => "mla_markup_templates_arguments[{$default}]",
					'arguments_id' => "mla_markup_templates_arguments_{$default}",
					'arguments_text' => isset( $value['arguments'] ) ? esc_textarea( $value['arguments'] ) : '',
					'arguments_help' => __( 'Default shortcode parameter values.', 'media-library-assistant' ),

					'Open' => __( 'Open', 'media-library-assistant' ),
					'open_name' => "mla_markup_templates_open[{$default}]",
					'open_id' => "mla_markup_templates_open_{$default}",
					'open_text' => isset( $value['open'] ) ? esc_textarea( $value['open'] ) : '',
					'open_help' => __( 'Markup for the beginning of the gallery. List of parameters, e.g., [+selector+], on Documentation tab.', 'media-library-assistant' ),

					'Row' => __( 'Row', 'media-library-assistant' ),
					'row_open_name' => "mla_markup_templates_row_open[{$default}]",
					'row_open_id' => "mla_markup_templates_row_open_{$default}",
					'row_open_text' => isset( $value['row-open'] ) ? esc_textarea( $value['row-open'] ) : '',
					'row_open_help' =>  __( 'Markup for the beginning of each row in the gallery.', 'media-library-assistant' ),

					'Item' => __( 'Item', 'media-library-assistant' ),
					'item_name' => "mla_markup_templates_item[{$default}]",
					'item_id' => "mla_markup_templates_item_{$default}",
					'item_text' => isset( $value['item'] ) ? esc_textarea( $value['item'] ) : '',
					'item_help' =>  __( 'Markup for each item/cell of the gallery.', 'media-library-assistant' ),

					'Close' => __( 'Close', 'media-library-assistant' ),
					'row_close_name' => "mla_markup_templates_row_close[{$default}]",
					'row_close_id' => "mla_markup_templates_row_close_{$default}",
					'row_close_text' => isset( $value['row-close'] ) ? esc_textarea( $value['row-close'] ) : '',
					'row_close_help' =>  __( 'Markup for the end of each row in the gallery.', 'media-library-assistant' ),

					'close_name' => "mla_markup_templates_close[{$default}]",
					'close_id' => "mla_markup_templates_close_{$default}",
					'close_text' => isset( $value['close'] ) ? esc_textarea( $value['close'] ) : '',
					'close_help' =>  __( 'Markup for the end of the gallery.', 'media-library-assistant' )
				);

				$markup_options_list .= MLAData::mla_parse_template( self::$page_template_array['mla-gallery-markup'], $template_values );
			} // $value
		} // foreach default

		foreach ( $templates as $name => $value ) {
			$slug = sanitize_title( $name );

			if ( in_array( $name, $default_markups ) ) {
				continue; // already handled above
			}

			$template_values = array (
				'name' => 'mla_markup_templates_delete[' . $slug . ']',
				'id' => 'mla_markup_templates_delete_' . $slug,
				'value' => __( 'Delete this template', 'media-library-assistant' ),
				'help' => __( 'Check the box to delete this template when you press Update at the bottom of the page.', 'media-library-assistant' )
			);
			$control_cells = MLAData::mla_parse_template( self::$page_template_array['mla-gallery-delete'], $template_values );

			$template_values = array (
				'Name' => __( 'Name', 'media-library-assistant' ),
				'name_name' => "mla_markup_templates_name[{$slug}]",
				'name_id' => "mla_markup_templates_name_{$slug}",
				'readonly' => '',
				'name_text' => $slug,
				'control_cells' => $control_cells,

				'Arguments' => __( 'Arguments', 'media-library-assistant' ),
				'arguments_name' => "mla_markup_templates_arguments[{$slug}]",
				'arguments_id' => "mla_markup_templates_arguments_{$slug}",
				'arguments_text' => isset( $value['arguments'] ) ? esc_textarea( $value['arguments'] ) : '',
				'arguments_help' => __( 'Default shortcode parameter values.', 'media-library-assistant' ),

				'Open' => __( 'Open', 'media-library-assistant' ),
				'open_name' => "mla_markup_templates_open[{$slug}]",
				'open_id' => "mla_markup_templates_open_{$slug}",
				'open_text' => isset( $value['open'] ) ? esc_textarea( $value['open'] ) : '',
				'open_help' =>  __( 'Markup for the beginning of the gallery. List of parameters, e.g., [+selector+], on Documentation tab.', 'media-library-assistant' ),

				'Row' => __( 'Row', 'media-library-assistant' ),
				'row_open_name' => "mla_markup_templates_row_open[{$slug}]",
				'row_open_id' => "mla_markup_templates_row_open_{$slug}",
				'row_open_text' => isset( $value['row-open'] ) ? esc_textarea( $value['row-open'] ) : '',
				'row_open_help' =>  __( 'Markup for the beginning of each row.', 'media-library-assistant' ),

				'Item' => __( 'Item', 'media-library-assistant' ),
				'item_name' => "mla_markup_templates_item[{$slug}]",
				'item_id' => "mla_markup_templates_item_{$slug}",
				'item_text' => isset( $value['item'] ) ? esc_textarea( $value['item'] ) : '',
				'item_help' =>  __( 'Markup for each item/cell.', 'media-library-assistant' ),

				'Close' => __( 'Close', 'media-library-assistant' ),
				'row_close_name' => "mla_markup_templates_row_close[{$slug}]",
				'row_close_id' => "mla_markup_templates_row_close_{$slug}",
				'row_close_text' => isset( $value['row-close'] ) ? esc_textarea( $value['row-close'] ) : '',
				'row_close_help' =>  __( 'Markup for the end of each row.', 'media-library-assistant' ),

				'close_name' => "mla_markup_templates_close[{$slug}]",
				'close_id' => "mla_markup_templates_close_{$slug}",
				'close_text' => isset( $value['close'] ) ? esc_textarea( $value['close'] ) : '',
				'close_help' =>  __( 'Markup for the end of the gallery.', 'media-library-assistant' )
			);

			$markup_options_list .= MLAData::mla_parse_template( self::$page_template_array['mla-gallery-markup'], $template_values );
		} // foreach $templates

		/*
		 * Add blank markup template for additions
		 */
		if ( ! empty( $value ) ) {
			$template_values = array (
				'help' => __( 'Fill in a name and markup to add a new template.', 'media-library-assistant' )
			);
			$control_cells = MLAData::mla_parse_template( self::$page_template_array['mla-gallery-default'], $template_values );

			$template_values = array (
				'Name' => __( 'Name', 'media-library-assistant' ),
				'name_name' => 'mla_markup_templates_name[blank]',
				'name_id' => 'mla_markup_templates_name_blank',
				'readonly' => '',
				'name_text' => '',
				'control_cells' => $control_cells,

				'Arguments' => __( 'Arguments', 'media-library-assistant' ),
				'arguments_name' => 'mla_markup_templates_arguments[blank]',
				'arguments_id' => 'mla_markup_templates_arguments_blank',
				'arguments_text' => '',
				'arguments_help' => __( 'Default shortcode parameter values.', 'media-library-assistant' ),

				'Open' => __( 'Open', 'media-library-assistant' ),
				'open_name' => 'mla_markup_templates_open[blank]',
				'open_id' => 'mla_markup_templates_open_blank',
				'open_text' => '',
				'open_help' => __( 'Markup for the beginning of the gallery. List of parameters, e.g., [+selector+], on Documentation tab.', 'media-library-assistant' ),

				'Row' => __( 'Row', 'media-library-assistant' ),
				'row_open_name' => 'mla_markup_templates_row_open[blank]',
				'row_open_id' => 'mla_markup_templates_row_open_blank',
				'row_open_text' => '',
				'row_open_help' => __( 'Markup for the beginning of each row in the gallery.', 'media-library-assistant' ),

				'Item' => __( 'Item', 'media-library-assistant' ),
				'item_name' => 'mla_markup_templates_item[blank]',
				'item_id' => 'mla_markup_templates_item_blank',
				'item_text' => '',
				'item_help' => __( 'Markup for each item/cell of the gallery.', 'media-library-assistant' ),

				'Close' => __( 'Close', 'media-library-assistant' ),
				'row_close_name' => 'mla_markup_templates_row_close[blank]',
				'row_close_id' => 'mla_markup_templates_row_close_blank',
				'row_close_text' => '',
				'row_close_help' => __( 'Markup for the end of each row in the gallery.', 'media-library-assistant' ),

				'close_name' => 'mla_markup_templates_close[blank]',
				'close_id' => 'mla_markup_templates_close_blank',
				'close_text' => '',
				'close_help' => __( 'Markup for the end of the gallery.', 'media-library-assistant' )

			);

			$markup_options_list .= MLAData::mla_parse_template( self::$page_template_array['mla-gallery-markup'], $template_values );
		} // $value

		$page_values['markup_options_list'] = $markup_options_list;

		$page_content['body'] = MLAData::mla_parse_template( self::$page_template_array['mla-gallery-tab'], $page_values );
		return $page_content;
	}

	/**
	 * Compose the Custom Field tab content for the Settings subpage
	 *
	 * @since 1.10
	 * @uses $page_template_array contains tab content template(s)
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_custom_field_tab( ) {
		/*
		 * Check for action or submit buttons.
		 * Initialize page messages and content.
		 */
		if ( isset( $_REQUEST['custom_field_mapping'] ) && is_array( $_REQUEST['custom_field_mapping'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

			/*
			 * Check for page-level submit buttons to change settings or map attachments.
			 * Initialize page messages and content.
			 */
			if ( !empty( $_REQUEST['custom-field-options-save'] ) ) {
				$page_content = self::_save_custom_field_settings( );
			} elseif ( !empty( $_REQUEST['custom-field-options-map'] ) ) {
				$page_content = self::_process_custom_field_mapping( );
			} else {
				$page_content = array(
					'message' => '',
					'body' => '' 
				);

				/*
				 * Check for single-rule action buttons
				 */
				foreach ( $_REQUEST['custom_field_mapping'] as $key => $value ) {
					$value = stripslashes_deep( $value );

					if ( isset( $value['action'] ) ) {
						$settings = array( $key => $value );
						foreach ( $value['action'] as $action => $label ) {
							switch( $action ) {
								case 'delete_field':
									$delete_result = self::_delete_custom_field( $value );
								case 'delete_rule':
								case 'add_rule':
								case 'add_field':
								case 'update_rule':
									$page_content = self::_save_custom_field_settings( $settings );
									if ( isset( $delete_result ) ) {
										$page_content['message'] = $delete_result . $page_content['message'];
									}
									break;
								case 'map_now':
									$page_content = self::_process_custom_field_mapping( $settings );
									break;
								case 'add_rule_map':
								case 'add_field_map':
									$page_content = self::_save_custom_field_settings( $settings );
									if ( false === strpos( $page_content['message'], __( 'ERROR', 'media-library-assistant' ) ) ) {
										$current_values = MLACore::mla_get_option( 'custom_field_mapping' );
										$settings = array( $value['name'] => $current_values[$value['name']] );
										$map_content = self::_process_custom_field_mapping( $settings );
										$page_content['message'] .= '<br>&nbsp;<br>' . $map_content['message'];
									}
									break;
								default:
									// ignore everything else
							} //switch action
						} // foreach action
					} /// isset action
				} // foreach rule
			} // specific rule check
		} // isset custom_field_mapping
		else {
			$page_content = array(
				'message' => '',
				'body' => '' 
			);
		}

		if ( !empty( $page_content['body'] ) ) {
			return $page_content;
		}

		$page_values = array(
			'Mapping Progress' => __( 'Custom Field Mapping Progress', 'media-library-assistant' ),
			'DO NOT' => __( 'DO NOT DO THE FOLLOWING (they will cause mapping to fail)', 'media-library-assistant' ),
			'DO NOT Close' => __( 'Close the window', 'media-library-assistant' ),
			'DO NOT Reload' => __( 'Reload the page', 'media-library-assistant' ),
			'DO NOT Click' => __( 'Click the browser&rsquo;s Stop, Back or forward buttons', 'media-library-assistant' ),
			'Progress' => __( 'Progress', 'media-library-assistant' ),
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
			'Resume' => __( 'Resume', 'media-library-assistant' ),
			'Close' => __( 'Close', 'media-library-assistant' ),
			'Refresh' => __( 'Refresh', 'media-library-assistant' ),
			'refresh_href' => '?page=mla-settings-menu-custom_field&mla_tab=custom_field',
		);

		$progress_div = MLAData::mla_parse_template( self::$page_template_array['mla-progress-div'], $page_values );

		$page_values = array(
			'mla-progress-div' => $progress_div,
			'Custom Field Options' => __( 'Custom Field and Attachment Metadata Processing Options', 'media-library-assistant' ),
			/* translators: 1: Documentation hyperlink */
			'In this tab' => sprintf( __( 'In this tab you can define the rules for mapping several types of image metadata to WordPress custom fields. You can also use this screen to define rules for adding or updating fields within the WordPress-supplied "Attachment Metadata", stored in the "_wp_attachment_metadata" custom field. See the %1$s section of the Documentation for details.', 'media-library-assistant' ), '<a href="[+settingsURL+]?page=mla-settings-menu-documentation&amp;mla_tab=documentation#attachment_metadata_mapping" title="' . __( 'Updating Attachment Metadata Documentation', 'media-library-assistant' ) . '">' . __( 'Adding or changing Attachment Metadata', 'media-library-assistant' ) . '</a>' ),
			/* translators: 1: Documentation hyperlink */
			'You can find' => sprintf( __( 'You can find more information about using the controls in this tab to define mapping rules and apply them in the %1$s section of the Documentation.', 'media-library-assistant' ), '<a href="[+settingsURL+]?page=mla-settings-menu-documentation&amp;mla_tab=documentation#mla_custom_field_mapping" title="' . __( 'Custom Field Options documentation', 'media-library-assistant' ) . '">' . __( 'Custom Field and Attachment Metadata Processing Options', 'media-library-assistant' ) . '</a>' ),
			'settingsURL' => admin_url('options-general.php'),
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-custom_field&mla_tab=custom_field',
			'options_list' => '',
			'Custom field mapping' => __( 'Custom field mapping', 'media-library-assistant' ),
			'custom_options_list' => '',
			'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
			'Map All Rules' => __( 'Map All Rules, All Attachments Now', 'media-library-assistant' ),
			/* translators: 1: "Save Changes" */
			'Click Save Changes' => sprintf( __( 'Click %1$s to update the "Enable custom field mapping..." checkbox and/or all rule changes and additions at once. <strong>No rule mapping will be performed.</strong>', 'media-library-assistant' ), '<strong>' . __( 'Save Changes', 'media-library-assistant' ) . '</strong>' ),
			/* translators: 1: "Map All Rules..." */
			'Click Map All' => sprintf( __( 'Click %1$s to apply all the rules at once (rule changes will be applied but not saved).', 'media-library-assistant' ), '<strong>' . __( 'Map All Rules, All Attachments Now', 'media-library-assistant' ) . '</strong>' ),
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'_wp_http_referer' => wp_referer_field( false )
		);

		/*
		 * Start with any page-level options
		 */
		$options_list = '';
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'custom_field' == $value['tab'] ) {
				$options_list .= self::mla_compose_option_row( $key, $value );
			}
		}

		$page_values['options_list'] = $options_list;

		/*
		 * Add mapping options
		 */
		$page_values['custom_options_list'] = MLAOptions::mla_custom_field_option_handler( 'render', 'custom_field_mapping', MLACoreOptions::$mla_option_definitions['custom_field_mapping'] );

		$page_content['body'] = MLAData::mla_parse_template( self::$page_template_array['custom-field-tab'], $page_values );
		return $page_content;
	}

	/**
	 * Compose the IPTC/EXIF tab content for the Settings subpage
	 *
	 * @since 1.00
	 * @uses $page_template_array contains tab content template(s)
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_iptc_exif_tab( ) {
		/*
		 * Initialize page messages and content.
		 * Check for submit buttons to change or reset settings.
		 */
		$page_content = array(
			'message' => '',
			'body' => '' 
		);

		if ( isset( $_REQUEST['iptc_exif_mapping'] ) && is_array( $_REQUEST['iptc_exif_mapping'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

			if ( !empty( $_REQUEST['iptc-exif-options-save'] ) ) {
				$page_content = self::_save_iptc_exif_settings( );
			} elseif ( !empty( $_REQUEST['iptc-exif-options-process-standard'] ) ) {
				$page_content = self::_process_iptc_exif_standard( );
			} elseif ( !empty( $_REQUEST['iptc-exif-options-process-taxonomy'] ) ) {
				$page_content = self::_process_iptc_exif_taxonomy( );
			} elseif ( !empty( $_REQUEST['iptc-exif-options-process-custom'] ) ) {
				$page_content = self::_process_iptc_exif_custom( );
			} else {
				/*
				 * Check for single-rule action buttons
				 */
				foreach ( $_REQUEST['iptc_exif_mapping']['custom'] as $key => $value ) {
					$value = stripslashes_deep( $value );

					if ( isset( $value['action'] ) ) {
						$settings = array( 'custom' => array( $key => $value ) );
						foreach ( $value['action'] as $action => $label ) {
							switch( $action ) {
								case 'delete_field':
									$delete_result = self::_delete_custom_field( $value );
								case 'delete_rule':
								case 'add_rule':
								case 'add_field':
								case 'update_rule':
									$page_content = self::_save_iptc_exif_custom_settings( $settings );
									if ( isset( $delete_result ) ) {
										$page_content['message'] = $delete_result . $page_content['message'];
									}
									break;
								case 'map_now':
									$page_content = self::_process_iptc_exif_custom( $settings );
									break;
								case 'add_rule_map':
								case 'add_field_map':
									$page_content = self::_save_iptc_exif_custom_settings( $settings );
									if ( false === strpos( $page_content['message'], __( 'ERROR', 'media-library-assistant' ) ) ) {
										$current_values = MLACore::mla_get_option( 'iptc_exif_mapping' );
										$settings = array( 'custom' => array( $value['name'] => $current_values['custom'][$value['name']] ) );
										$map_content = self::_process_iptc_exif_custom( $settings );
										$page_content['message'] .= '<br>&nbsp;<br>' . $map_content['message'];
									}
									break;
								default:
									// ignore everything else
							} //switch action
						} // foreach action
					} /// isset action
				} // foreach rule
			}

			if ( !empty( $page_content['body'] ) ) {
				return $page_content;
			}
		}

		$page_values = array(
			'Mapping Progress' => __( 'IPTC &amp; EXIF Mapping Progress', 'media-library-assistant' ),
			'DO NOT' => __( 'DO NOT DO THE FOLLOWING (they will cause mapping to fail)', 'media-library-assistant' ),
			'DO NOT Close' => __( 'Close the window', 'media-library-assistant' ),
			'DO NOT Reload' => __( 'Reload the page', 'media-library-assistant' ),
			'DO NOT Click' => __( 'Click the browser&rsquo;s Stop, Back or forward buttons', 'media-library-assistant' ),
			'Progress' => __( 'Progress', 'media-library-assistant' ),
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
			'Resume' => __( 'Resume', 'media-library-assistant' ),
			'Close' => __( 'Close', 'media-library-assistant' ),
			'Refresh' => __( 'Refresh', 'media-library-assistant' ),
			'refresh_href' => '?page=mla-settings-menu-iptc_exif&mla_tab=iptc_exif',
		);

		$progress_div = MLAData::mla_parse_template( self::$page_template_array['mla-progress-div'], $page_values );

		$page_values = array(
			'mla-progress-div' => $progress_div,
			'IPTX/EXIF Options' => __( 'IPTC &amp; EXIF Processing Options', 'media-library-assistant' ),
			'In this tab' => __( 'In this tab you can define the rules for mapping IPTC (International Press Telecommunications Council) and EXIF (EXchangeable Image File) metadata to WordPress standard attachment fields, taxonomy terms and custom fields. <strong>NOTE:</strong> settings changes will not be made permanent until you click "Save Changes" at the bottom of this page.', 'media-library-assistant' ),
			/* translators: 1: Documentation hyperlink */
			'You can find' => sprintf( __( 'You can find more information about using the controls in this tab to define mapping rules and apply them in the %1$s section of the Documentation.', 'media-library-assistant' ), '<a href="[+settingsURL+]?page=mla-settings-menu-documentation&amp;mla_tab=documentation#mla_iptc_exif_mapping" title="' . __( 'IPTC/EXIF Options documentation', 'media-library-assistant' ) . '">' . __( 'IPTC &amp; EXIF Processing Options', 'media-library-assistant' ) . '</a>' ),
			'settingsURL' => admin_url('options-general.php'),
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-iptc_exif&mla_tab=iptc_exif',
			'options_list' => '',
			'Standard field mapping' => __( 'Standard field mapping', 'media-library-assistant' ),
			'Map Standard Fields' => __( 'Map All Attachments, Standard Fields Now', 'media-library-assistant' ),
			'standard_options_list' => '',
			'Taxonomy term mapping' => __( 'Taxonomy term mapping', 'media-library-assistant' ),
			'Map Taxonomy Terms' => __( 'Map All Attachments, Taxonomy Terms Now', 'media-library-assistant' ),
			'taxonomy_options_list' => '',
			'Custom field mapping' => __( 'Custom field mapping', 'media-library-assistant' ),
			'Map Custom Fields' => __( 'Map All Attachments, Custom Fields Now', 'media-library-assistant' ),
			'custom_options_list' => '',
			'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
			/* translators: 1: "Save Changes" */
			'Click Save Changes' => sprintf( __( 'Click %1$s to update the "Enable IPTC/EXIF mapping..." checkbox and/or all rule changes and additions at once. <strong>No rule mapping will be performed.</strong>', 'media-library-assistant' ), '<strong>' . __( 'Save Changes', 'media-library-assistant' ) . '</strong>' ),
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'_wp_http_referer' => wp_referer_field( false )
		);

		/*
		 * Start with any page-level options
		 */
		$options_list = '';
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'iptc_exif' == $value['tab'] ) {
				$options_list .= self::mla_compose_option_row( $key, $value );
			}
		}

		$page_values['options_list'] = $options_list;

		/*
		 * Add mapping options
		 */
		$page_values['standard_options_list'] = MLAOptions::mla_iptc_exif_option_handler( 'render', 'iptc_exif_standard_mapping', MLACoreOptions::$mla_option_definitions['iptc_exif_standard_mapping'] );

		$page_values['taxonomy_options_list'] = MLAOptions::mla_iptc_exif_option_handler( 'render', 'iptc_exif_taxonomy_mapping', MLACoreOptions::$mla_option_definitions['iptc_exif_taxonomy_mapping'] );

		$page_values['custom_options_list'] = MLAOptions::mla_iptc_exif_option_handler( 'render', 'iptc_exif_custom_mapping', MLACoreOptions::$mla_option_definitions['iptc_exif_custom_mapping'] );

		$page_content['body'] = MLAData::mla_parse_template( self::$page_template_array['iptc-exif-tab'], $page_values );
		return $page_content;
	}

	/**
	 * Compose the Documentation tab content for the Settings subpage
	 *
	 * @since 0.80
	 * @uses $page_template_array contains tab content template(s)
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_documentation_tab( ) {
		$page_template = MLACore::mla_load_template( 'documentation-settings-tab.tpl' );
		$page_values = array(
			'translate_url' => MLA_PLUGIN_URL . 'languages/MLA Internationalization Guide.pdf',
			'phpDocs_url' => MLA_PLUGIN_URL . 'phpDocs/index.html',
			'examples_url' => MLA_PLUGIN_URL . 'examples/'
		);

		return array(
			'message' => '',
			'body' => MLAData::mla_parse_template( $page_template['documentation-tab'], $page_values ) 
		);
	}

	/**
	 * Save Debug settings to the options table
 	 *
	 * @since 2.10
	 *
	 * @uses $_REQUEST
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _save_debug_settings( ) {
		$message_list = '';

		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'debug' == $value['tab'] ) {
				$message_list .= self::mla_update_option_row( $key, $value );
			} // view option
		} // foreach mla_options

		$page_content = array(
			'message' => __( 'Debug settings saved.', 'media-library-assistant' ) . "\r\n",
			'body' => '' 
		);

		/*
		 * Uncomment this for debugging.
		 */
		// $page_content['message'] .= $message_list;

		return $page_content;
	} // _save_debug_settings

	/**
	 * Compose the Debug tab Debug Settings content for one setting
	 *
	 * @since 2.14
	 *
	 * @param	string	$label Display name for the setting
	 * @param	string	$value Current value for the setting
 	 *
	 * @return	string	HTML table row markup for the label setting pair
	 */
	private static function _compose_settings_row( $label, $value ) {
		$row = '<tr valign="top"><th scope="row" style="text-align:right;">' . "\n";
        $row .= $label . "\n";
        $row .= '</th><td style="text-align:left;">' . "\n";
        $row .= $value . "\n";
        $row .= '</td></tr>' . "\n";

		return $row;        
	} // _compose_settings_row

	/**
	 * Compose the Debug tab content for the Settings subpage
	 *
	 * @since 2.10
	 * @uses $page_template_array contains tab content template(s)
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_debug_tab( ) {
		$page_content = array(
			'message' => '',
			'body' => '' 
		);

		$page_values = array();

		/*
		 * Saving the options can change the log file name, so do it first
		 */
		if ( !empty( $_REQUEST['mla-debug-options-save'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_save_debug_settings();
		}

		/*
		 * Find the appropriate error log file
		 */
		$error_log_name = MLACore::mla_get_option( MLACoreOptions::MLA_DEBUG_FILE );
		if ( empty( $error_log_name ) ) {
			$error_log_name =  ini_get( 'error_log' );
		} else {
			$first = substr( $error_log_name, 0, 1 );
			if ( ( '/' != $first ) && ( '\\' != $first ) ) {
				$error_log_name = '/' . $error_log_name;
			}

			$error_log_name = WP_CONTENT_DIR . $error_log_name;
		}

		$error_log_exists = file_exists ( $error_log_name );

		/*
		 * Check for other page-level actions
		 */
		if ( isset( $_REQUEST['mla_reset_log'] ) && 'true' == $_REQUEST['mla_reset_log'] ) {
			$file_error = false;
			$file_handle = @fopen( $error_log_name, 'w' );

			if ( $file_handle ) {
				$file_error = ( false === @ftruncate( $file_handle, 0 ) );
				@fclose( $file_handle );
			} else {
				$file_error = true;
			}

			if ( $file_error ) {
				$error_info = error_get_last();
				if ( false !== ( $tail = strpos( $error_info['message'], '</a>]: ' ) ) ) {
					$php_errormsg = ':<br>' . substr( $error_info['message'], $tail + 7 );
				} else {
					$php_errormsg = '.';
				}

				/* translators: 1: ERROR tag 2: file type 3: file name 4: error message*/
				$page_content['message'] = sprintf( __( '%1$s: Reseting the %2$s file ( %3$s ) "%4$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), __( 'Error Log', 'media-library-assistant' ), $error_log_name, $php_errormsg );
			} else {
				$error_log_exists = file_exists ( $error_log_name );
			}
		}

		/*
		 * Start with any page-level options
		 */
		$options_list = '';
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'debug' == $value['tab'] ) {
				$options_list .= self::mla_compose_option_row( $key, $value );
			}
		}

		/*
		 * Gather Debug Settings
		 */
		$display_limit = MLACore::mla_get_option( MLACoreOptions::MLA_DEBUG_DISPLAY_LIMIT );
		$debug_file = MLACore::mla_get_option( MLACoreOptions::MLA_DEBUG_FILE );
		$replace_php = MLACore::mla_get_option( MLACoreOptions::MLA_DEBUG_REPLACE_PHP_LOG );
		$php_reporting = MLACore::mla_get_option( MLACoreOptions::MLA_DEBUG_REPLACE_PHP_REPORTING );
		$mla_reporting = MLACore::mla_get_option( MLACoreOptions::MLA_DEBUG_REPLACE_LEVEL );

		if ( $error_log_exists ) {
			/*
			 * Add debug content
			 */
			$display_limit = absint( MLACore::mla_get_option( MLACoreOptions::MLA_DEBUG_DISPLAY_LIMIT ) );
			$error_log_size = filesize( $error_log_name ); 

			if ( 0 < $display_limit ) {
				if ( $display_limit < $error_log_size ) {
					$error_log_contents = @file_get_contents( $error_log_name, false, NULL, ( $error_log_size - $display_limit ), $display_limit );
				} else {
					$error_log_contents = @file_get_contents( $error_log_name, false );
				}
			} else {
				$error_log_contents = @file_get_contents( $error_log_name, false );
			}

			if ( false === $error_log_contents ) {
				$error_info = error_get_last();
				if ( false !== ( $tail = strpos( $error_info['message'], '</a>]: ' ) ) ) {
					$php_errormsg = ':<br>' . substr( $error_info['message'], $tail + 7 );
				} else {
					$php_errormsg = '.';
				}

				/* translators: 1: ERROR tag 2: file type 3: file name 4: error message*/
				$page_content['message'] = sprintf( __( '%1$s: Reading the %2$s file ( %3$s ) "%4$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), __( 'Error Log', 'media-library-assistant' ), $error_log_name, $php_errormsg );
				$error_log_contents = '';
			} else {
				if ( 0 < $display_limit ) {
					$error_log_contents = substr( $error_log_contents, 0 - $display_limit );
				}
			}
		} else {
			if ( empty( $page_content['message'] ) ) {
				/* translators: 1: file name */
				$page_content['message'] = sprintf( __( 'Error log file (%1$s) not found; click Reset to create it.', 'media-library-assistant' ), $error_log_name );
			}

			$error_log_size = 0;
			$error_log_contents = '';
		} // file_exists

		if ( current_user_can( 'upload_files' ) ) {
			if ( $error_log_exists ) {
				$args = array(
					'page' => MLACore::ADMIN_PAGE_SLUG,
					'mla_download_file' => urlencode( $error_log_name ),
					'mla_download_type' => 'text/plain'
				);
				$download_link = '<a class="button-secondary" href="' . add_query_arg( $args, wp_nonce_url( 'upload.php', MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Download', 'media-library-assistant' ) . ' &#8220;' . __( 'Error Log', 'media-library-assistant' ) . '&#8221;">' . __( 'Download', 'media-library-assistant' ) . '</a>';
			} else {
				$download_link = '';
			}

			$args = array(
				'page' => 'mla-settings-menu-debug',
				'mla_tab' => 'debug',
				'mla_reset_log' => 'true'
			);
			$reset_link = '<a class="button-secondary" href="' . add_query_arg( $args, wp_nonce_url( 'options-general.php', MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Reset', 'media-library-assistant' ) . ' &#8220;' . __( 'Error Log', 'media-library-assistant' ) . '&#8221;">' . __( 'Reset', 'media-library-assistant' ) . '</a>';
		}

		$settings_list  = self::_compose_settings_row( 'Display Limit', $display_limit );
		$settings_list .= self::_compose_settings_row( 'Debug File', $debug_file );
		$settings_list .= self::_compose_settings_row( 'Replace PHP log', $replace_php );
		$settings_list .= self::_compose_settings_row( 'PHP Reporting', $php_reporting );
		$settings_list .= self::_compose_settings_row( 'MLA Reporting', $mla_reporting );
		$settings_list .= self::_compose_settings_row( 'MLA_DEBUG_LEVEL', sprintf( '0x%1$04X', MLA_DEBUG_LEVEL ) );
		$settings_list .= self::_compose_settings_row( 'PHP error_reporting', MLACore::$original_php_reporting );
		$settings_list .= self::_compose_settings_row( 'Old PHP error_log', MLACore::$original_php_log );
		$settings_list .= self::_compose_settings_row( 'New PHP error_log', ini_get( 'error_log' ) );
		$settings_list .= self::_compose_settings_row( 'WP_DEBUG', WP_DEBUG ? 'true' : 'false' );
		$settings_list .= self::_compose_settings_row( 'WP_DEBUG_LOG', WP_DEBUG_LOG ? 'true' : 'false' );
		$settings_list .= self::_compose_settings_row( 'WP_DEBUG_DISPLAY', WP_DEBUG_DISPLAY ? 'true' : 'false' );
		$settings_list .= self::_compose_settings_row( 'WP_CONTENT_DIR', WP_CONTENT_DIR );

		/*
		 * Compose tab content
		 */
		$page_values = array (
			'Debug Options' => __( 'Debug Options', 'media-library-assistant' ),
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-debug&mla_tab=debug',
			'options_list' => $options_list,
			'Debug Settings' => __( 'Debug Settings', 'media-library-assistant' ),
			'settings_list' => $settings_list,
			'Error Log' => __( 'Error Log', 'media-library-assistant' ),
			'Error Log Name' => $error_log_name,
			'Error Log Size' => number_format( (float) $error_log_size ),
			'error_log_text' => $error_log_contents,
			'download_link' => $download_link,
			'reset_link' => $reset_link,
			'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
			/* translators: 1: "Save Changes" */
			'Click Save Changes' => sprintf( __( 'Click %1$s to update the %2$s.', 'media-library-assistant' ), '<strong>' . __( 'Save Changes', 'media-library-assistant' ) . '</strong>', __( 'Debug Options', 'media-library-assistant' ) ),
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'_wp_http_referer' => wp_referer_field( false )
		);

		$page_content['body'] = MLAData::mla_parse_template( self::$page_template_array['debug-tab'], $page_values );
		return $page_content;
	}

	/**
	 * Render (echo) the "Media Library Assistant" subpage in the Settings section
	 *
	 * @since 0.1
	 *
	 * @return	void Echoes HTML markup for the Settings subpage
	 */
	public static function mla_render_settings_page( ) {
		if ( !current_user_can( 'manage_options' ) ) {
			echo __( 'Media Library Assistant', 'media-library-assistant' ) . ' - ' . __( 'ERROR', 'media-library-assistant' ) . "</h2>\r\n";
			wp_die( __( 'You do not have permission to manage plugin settings.', 'media-library-assistant' ) );
		}

		/*
		 * Load template array and initialize page-level values.
		 */
		$development_version =  MLA::MLA_DEVELOPMENT_VERSION;
		$development_version =  ( ! empty( $development_version ) ) ? ' (' . $development_version . ')' : '';
		self::$page_template_array = MLACore::mla_load_template( 'admin-display-settings-page.tpl' );
		$current_tab_slug = isset( $_REQUEST['mla_tab'] ) ? $_REQUEST['mla_tab']: 'general';
		$current_tab = self::mla_get_options_tablist( $current_tab_slug );
		$page_values = array(
			'Support Our Work' => __( 'Support Our Work', 'media-library-assistant' ),
			'Donate' => __( 'Donate', 'media-library-assistant' ),
			'version' => 'v' . MLA::CURRENT_MLA_VERSION,
			'development' => $development_version,
			'messages' => '',
			'tablist' => self::_compose_settings_tabs( $current_tab_slug ),
			'tab_content' => '',
			'Media Library Assistant' => __( 'Media Library Assistant', 'media-library-assistant' ),
			'Settings' => __( 'Settings', 'media-library-assistant' )
		);

		/*
		 * Compose tab content
		 */
		if ( $current_tab ) {
			if ( isset( $current_tab['render'] ) ) {
				$handler = $current_tab['render'];
				$page_content = call_user_func( $handler );
			} else {
				$page_content = array( 'message' => __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Cannot render content tab', 'media-library-assistant' ), 'body' => '' );
			}
		} else {
			$page_content = array( 'message' => __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Unknown content tab', 'media-library-assistant' ), 'body' => '' );
		}

		if ( ! empty( $page_content['message'] ) ) {
			if ( false !== strpos( $page_content['message'], __( 'ERROR', 'media-library-assistant' ) ) ) {
				$messages_class = 'mla_errors';
			} else {
				$messages_class = 'mla_messages';
			}

			$page_values['messages'] = MLAData::mla_parse_template( self::$page_template_array['messages'], array(
				 'messages' => $page_content['message'],
				 'mla_messages_class' => $messages_class 
			) );
		}

		$page_values['tab_content'] = $page_content['body'];
		echo MLAData::mla_parse_template( self::$page_template_array['page'], $page_values );
	} // mla_render_settings_page

	/**
	 * Save MLA Gallery settings to the options table
 	 *
	 * @since 0.80
	 *
	 * @uses $_REQUEST
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _save_gallery_settings( ) {
		$settings_changed = false;
		$message_list = '';
		$error_list = '';

		/*
		 * Start with any page-level options
		 */
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'mla_gallery' == $value['tab'] ) {
				$this_setting_changed = false;
				$old_value = MLACore::mla_get_option( $key );

				if (  'select' == $value['type'] ) {
					if ( $old_value != $_REQUEST[ MLA_OPTION_PREFIX . $key ] ) {
						$this_setting_changed = true;
					}
				} elseif ( 'text' == $value['type'] ) {
					if ( '' == $_REQUEST[ MLA_OPTION_PREFIX . $key ] ) {
						$_REQUEST[ MLA_OPTION_PREFIX . $key ] = $value['std'];
					}

					if ( $old_value != $_REQUEST[ MLA_OPTION_PREFIX . $key ] ) {
						$this_setting_changed = true;
					}
				} elseif ( 'checkbox' == $value['type'] ) {
					if ( isset( $_REQUEST[ MLA_OPTION_PREFIX . $key ] ) ) {
						$this_setting_changed = "checked" != $old_value;
					} else {
						$this_setting_changed = "checked" == $old_value;
					}
				}
				
				/*
				 * Always update to scrub default settings
				 */
				$message = self::mla_update_option_row( $key, $value );
				if ( $this_setting_changed ) {
					$settings_changed = true;
					$message_list .= $message;
				}
			} // mla_gallery option
		} // foreach mla_options

		/*
		 * Get the current style contents for comparison
		 */
		$old_templates = MLAOptions::mla_get_style_templates();
		$new_templates = array();
		$new_names = $_REQUEST['mla_style_templates_name'];
		$new_values = stripslashes_deep( $_REQUEST['mla_style_templates_value'] );
		$new_deletes = isset( $_REQUEST['mla_style_templates_delete'] ) ? $_REQUEST['mla_style_templates_delete']: array();

		/*
		 * Build new style template array, noting changes
		 */
		$default_styles = array( 'default', 'tag-cloud' );
		$templates_changed = false;
		foreach ( $new_names as $name => $new_name ) {
			if ( in_array( $name, $default_styles ) ) {
				continue;
			}

			if ( array_key_exists( $name, $new_deletes ) ) {
				/* translators: 1: template type 2: template name */
				$message_list .= '<br>' . sprintf( _x( 'Deleting %1$s "%2$s".', 'message_list', 'media-library-assistant' ), __( 'Style Template', 'media-library-assistant' ), $name );
				$templates_changed = true;
				continue;
			}

			$new_slug = sanitize_title( $new_name );
			if ( 'blank' == $name ) {
				if ( '' == $new_slug ) {
					continue;
				} elseif ( 'blank' == $new_slug ) {
					/* translators: 1: ERROR tag 2: template name 3: template type */
					$error_list .= '<br>' . sprintf( __( '%1$s: Reserved name "%2$s", new %3$s discarded.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $new_slug, __( 'Style Template', 'media-library-assistant' ) );
					continue;
				}

				if ( array_key_exists( $new_slug, $old_templates ) ) {
					/* translators: 1: ERROR tag 2: template name 3: template type */
					$error_list .= '<br>' . sprintf( __( '%1$s: Duplicate name "%2$s", new %3$s discarded.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $new_slug, __( 'Style Template', 'media-library-assistant' ) );
					continue;
				} else {
					/* translators: 1: template type 2: template name */
					$message_list .= '<br>' . sprintf( _x( 'Adding new %1$s "%2$s".', 'message_list', 'media-library-assistant' ), __( 'Style Template', 'media-library-assistant' ), $new_slug );
					$templates_changed = true;
				}
			} // 'blank' - reserved name

			/*
			 * Handle name changes, check for duplicates
			 */
			if ( '' == $new_slug ) {
				/* translators: 1: ERROR tag 2: element name 3: old value */
				$error_list .= '<br>' . sprintf( __( '%1$s: Blank %2$s, reverting to "%3$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), __( 'style template name', 'media-library-assistant' ), $name );
				$new_slug = $name;
			}

			if ( $new_slug != $name ) {
				if ( array_key_exists( $new_slug, $old_templates ) ) {
					/* translators: 1: ERROR tag 2: element name 3: new value 4: old value */
					$error_list .= '<br>' . sprintf( __( '%1$s: Duplicate new %2$s "%3$s", reverting to "%4$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), __( 'style template name', 'media-library-assistant' ), $new_slug, $name );
					$new_slug = $name;
				} elseif ( 'blank' != $name ) {
					/* translators: 1: element name 2: old_value 3: new_value */
					$message_list .= '<br>' . sprintf( _x( 'Changing %1$s from "%2$s" to "%3$s"', 'message_list', 'media-library-assistant' ), __( 'style template name', 'media-library-assistant' ), $name, $new_slug );
					$templates_changed = true;
				}
			} // name changed

			if ( ( 'blank' != $name ) && ( $new_values[ $name ] != $old_templates[ $name ] ) ) {
				/* translators: 1: template type 2: template name */
				$message_list .= '<br>' . sprintf( _x( 'Updating contents of %1$s "%2$s".', 'message_list', 'media-library-assistant' ), __( 'Style Template', 'media-library-assistant' ), $new_slug );
				$templates_changed = true;
			}

			$new_templates[ $new_slug ] = $new_values[ $name ];
		} // foreach $name

		if ( $templates_changed ) {
			$settings_changed = true;
			if ( false == MLAOptions::mla_put_style_templates( $new_templates ) ) {
				/* translators: 1: ERROR tag 2: template type */
				$error_list .= '<br>' . sprintf( __( '%1$s: Update of %2$s failed.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), __( 'Style Template', 'media-library-assistant' ) );
			}
		}

		/*
		 * Get the current markup contents for comparison
		 */
		$old_templates = MLAOptions::mla_get_markup_templates();
//error_log( __LINE__ . ' _save_gallery_settings $old_templates = ' . var_export( $old_templates, true ), 0 );
		$new_templates = array();
		$new_names = $_REQUEST['mla_markup_templates_name'];
		$new_values['arguments'] = stripslashes_deep( $_REQUEST['mla_markup_templates_arguments'] );
		$new_values['open'] = stripslashes_deep( $_REQUEST['mla_markup_templates_open'] );
		$new_values['row-open'] = stripslashes_deep( $_REQUEST['mla_markup_templates_row_open'] );
		$new_values['item'] = stripslashes_deep( $_REQUEST['mla_markup_templates_item'] );
		$new_values['row-close'] = stripslashes_deep( $_REQUEST['mla_markup_templates_row_close'] );
		$new_values['close'] = stripslashes_deep( $_REQUEST['mla_markup_templates_close'] );
		$new_deletes = isset( $_REQUEST['mla_markup_templates_delete'] ) ? $_REQUEST['mla_markup_templates_delete']: array();
//error_log( __LINE__ . ' _save_gallery_settings $new_values = ' . var_export( $new_values, true ), 0 );

		/*
		 * Build new markup template array, noting changes
		 */
		$default_markups = array( 'default', 'tag-cloud', 'tag-cloud-ul', 'tag-cloud-dl' );
		$templates_changed = false;
		foreach ( $new_names as $name => $new_name ) {
			if ( in_array( $name, $default_markups ) ) {
				continue;
			}

			if ( array_key_exists( $name, $new_deletes ) ) {
				/* translators: 1: template type 2: template name */
				$message_list .= '<br>' . sprintf( _x( 'Deleting %1$s "%2$s".', 'message_list', 'media-library-assistant' ), __( 'markup template', 'media-library-assistant' ), $name );
				$templates_changed = true;
				continue;
			}

			$new_slug = sanitize_title( $new_name );
			if ( 'blank' == $name ) {
				if ( '' == $new_slug ) {
					continue;
				}

				if ( 'blank' == $new_slug ) {
					/* translators: 1: ERROR tag 2: template name 3: template type */
					$error_list .= '<br>' . sprintf( __( '%1$s: Reserved name "%2$s", new %3$s discarded.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $new_slug, __( 'markup template', 'media-library-assistant' ) );
					continue;
				}

				if ( array_key_exists( $new_slug, $old_templates ) ) {
					/* translators: 1: ERROR tag 2: template name 3: template type */
					$error_list .= '<br>' . sprintf( __( '%1$s: Duplicate name "%2$s", new %3$s discarded.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $new_slug, __( 'markup template', 'media-library-assistant' ) );
					continue;
				} else {
					/* translators: 1: template type 2: template name */
					$message_list .= '<br>' . sprintf( _x( 'Adding new %1$s "%2$s".', 'message_list', 'media-library-assistant' ), __( 'markup template', 'media-library-assistant' ), $new_slug );
					$templates_changed = true;
				}
			} // 'blank' - reserved name

			/*
			 * Handle name changes, check for duplicates
			 */
			if ( '' == $new_slug ) {
				/* translators: 1: ERROR tag 2: element name 3: old value */
				$error_list .= '<br>' . sprintf( __( '%1$s: Blank %2$s, reverting to "%3$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), __( 'markup template name', 'media-library-assistant' ), $name );
				$new_slug = $name;
			}

			if ( $new_slug != $name ) {
				if ( array_key_exists( $new_slug, $old_templates ) ) {
					/* translators: 1: ERROR tag 2: element name 3: new value 4: old value */
					$error_list .= '<br>' . sprintf( __( '%1$s: Duplicate new %2$s "%3$s", reverting to "%4$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), __( 'markup template name', 'media-library-assistant' ), $new_slug, $name );
					$new_slug = $name;
				} elseif ( 'blank' != $name ) {
					/* translators: 1: element name 2: old_value 3: new_value */
					$message_list .= '<br>' . sprintf( _x( 'Changing %1$s from "%2$s" to "%3$s"', 'message_list', 'media-library-assistant' ), __( 'markup template name', 'media-library-assistant' ), $name, $new_slug );
					$templates_changed = true;
				}
			} // name changed

			if ( 'blank' != $name ) {
				if ( $new_values['arguments'][ $name ] != $old_templates[ $name ]['arguments'] ) {
					/* translators: 1: template name */
					$message_list .= '<br>' . sprintf( _x( 'Updating arguments markup for "%1$s".', 'message_list', 'media-library-assistant' ), $new_slug );
					$templates_changed = true;
				}

				if ( $new_values['open'][ $name ] != $old_templates[ $name ]['open'] ) {
					/* translators: 1: template name */
					$message_list .= '<br>' . sprintf( _x( 'Updating open markup for "%1$s".', 'message_list', 'media-library-assistant' ), $new_slug );
					$templates_changed = true;
				}

				if ( $new_values['row-open'][ $name ] != $old_templates[ $name ]['row-open'] ) {
					/* translators: 1: template name */
					$message_list .= '<br>' . sprintf( _x( 'Updating row open markup for "%1$s".', 'message_list', 'media-library-assistant' ), $new_slug );
					$templates_changed = true;
				}

				if ( $new_values['item'][ $name ] != $old_templates[ $name ]['item'] ) {
					/* translators: 1: template name */
					$message_list .= '<br>' . sprintf( _x( 'Updating item markup for "%1$s".', 'message_list', 'media-library-assistant' ), $new_slug );
					$templates_changed = true;
				}

				if ( $new_values['row-close'][ $name ] != $old_templates[ $name ]['row-close'] ) {
					/* translators: 1: template name */
					$message_list .= '<br>' . sprintf( _x( 'Updating row close markup for "%1$s".', 'message_list', 'media-library-assistant' ), $new_slug );
					$templates_changed = true;
				}

				if ( $new_values['close'][ $name ] != $old_templates[ $name ]['close'] ) {
					/* translators: 1: template name */
					$message_list .= '<br>' . sprintf( _x( 'Updating close markup for "%1$s".', 'message_list', 'media-library-assistant' ), $new_slug );
					$templates_changed = true;
				}
			} // ! 'blank'

			$new_templates[ $new_slug ]['arguments'] = $new_values['arguments'][ $name ];
			$new_templates[ $new_slug ]['open'] = $new_values['open'][ $name ];
			$new_templates[ $new_slug ]['row-open'] = $new_values['row-open'][ $name ];
			$new_templates[ $new_slug ]['item'] = $new_values['item'][ $name ];
			$new_templates[ $new_slug ]['row-close'] = $new_values['row-close'][ $name ];
			$new_templates[ $new_slug ]['close'] = $new_values['close'][ $name ];
		} // foreach $name

		if ( $templates_changed ) {
			$settings_changed = true;
			if ( false == MLAOptions::mla_put_markup_templates( $new_templates ) ) {
				/* translators: 1: ERROR tag 2: template type */
				$error_list .= '<br>' . sprintf( __( '%1$s: Update of %2$s failed.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), __( 'markup template', 'media-library-assistant' ) );
			}
		}

		if ( $settings_changed ) {
			/* translators: 1: field type */
			$message = sprintf( __( '%1$s settings saved.', 'media-library-assistant' ), __( 'MLA Gallery', 'media-library-assistant' ) ) . "\r\n";
		} else {
			/* translators: 1: field type */
			$message = sprintf( __( '%1$s no changes detected.', 'media-library-assistant' ), __( 'MLA Gallery', 'media-library-assistant' ) ) . "\r\n";
		}

		$page_content = array(
			'message' => $message . $error_list,
			'body' => '' 
		);

		/*
		 * Uncomment this for debugging.
		 */
		// $page_content['message'] .= $message_list;

		return $page_content;
	} // _save_gallery_settings

	/**
	 * Save View settings to the options table
 	 *
	 * @since 1.40
	 *
	 * @uses $_REQUEST
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _save_view_settings( ) {
		$message_list = '';

		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'view' == $value['tab'] ) {
				$message_list .= self::mla_update_option_row( $key, $value );
			} // view option
		} // foreach mla_options

		$page_content = array(
			'message' => __( 'View settings saved.', 'media-library-assistant' ) . "\r\n",
			'body' => '' 
		);

		/*
		 * Uncomment this for debugging.
		 */
		// $page_content['message'] .= $message_list;

		return $page_content;
	} // _save_view_settings

	/**
	 * Save Upload settings to the options table
 	 *
	 * @since 1.40
	 *
	 * @uses $_REQUEST
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _save_upload_settings( ) {
		$message_list = '';

		if ( ! isset( $_REQUEST[ MLA_OPTION_PREFIX . MLACoreOptions::MLA_ENABLE_UPLOAD_MIMES ] ) )		
			unset( $_REQUEST[ MLA_OPTION_PREFIX . MLACoreOptions::MLA_ENABLE_MLA_ICONS ] );

		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'upload' == $value['tab'] ) {
				$message_list .= self::mla_update_option_row( $key, $value );
			} // upload option
		} // foreach mla_options

		$page_content = array(
			'message' => __( 'Upload MIME Type settings saved.', 'media-library-assistant' ) . "\r\n",
			'body' => '' 
		);

		/*
		 * Uncomment this for debugging.
		 */
		// $page_content['message'] .= $message_list;

		return $page_content;
	} // _save_upload_settings

	/**
	 * Process custom field settings against all image attachments
	 * without saving the settings to the mla_option
 	 *
	 * @since 1.10
	 * @uses $_REQUEST if passed a NULL parameter
	 *
	 * @param	array | NULL	specific custom_field_mapping values 
	 * @param	integer			offset for chunk mapping 
	 * @param	integer			length for chunk mapping
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _process_custom_field_mapping( $settings = NULL, $offset = 0, $length = 0 ) {
		global $wpdb;
		if ( NULL == $settings ) {
			$source = 'custom_fields';
			$settings = ( isset( $_REQUEST['custom_field_mapping'] ) ) ? stripslashes_deep( $_REQUEST['custom_field_mapping'] ) : array();
			if ( isset( $settings[ MLACoreOptions::MLA_NEW_CUSTOM_FIELD ] ) ) {
				unset( $settings[ MLACoreOptions::MLA_NEW_CUSTOM_FIELD ] );
			}
			if ( isset( $settings[ MLACoreOptions::MLA_NEW_CUSTOM_RULE ] ) ) {
				unset( $settings[ MLACoreOptions::MLA_NEW_CUSTOM_RULE ] );
			}
		} else {
			$source = 'custom_rule';
			$settings = stripslashes_deep( $settings );
		}

		if ( empty( $settings ) ) {
			return array(
				'message' => __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'No custom field mapping rules to process.', 'media-library-assistant' ),
				'body' => '' ,
				'processed' => 0,
				'unchanged' => 0,
				'success' =>  0
			);
		}

		if ( $length > 0 ) {
			$limits = "LIMIT {$offset}, {$length}";
		} else {
			$limits = '';
		}

		$examine_count = 0;
		$update_count = 0;
		$post_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE `post_type` = 'attachment' {$limits}" );

		do_action( 'mla_begin_mapping', $source, NULL );
		foreach ( $post_ids as $key => $post_id ) {
			$updates = MLAOptions::mla_evaluate_custom_field_mapping( (integer) $post_id, 'custom_field_mapping', $settings );
			$examine_count += 1;
			if ( ! empty( $updates ) && isset( $updates['custom_updates'] ) ) {
				$results = MLAData::mla_update_item_postmeta( (integer) $post_id, $updates['custom_updates'] );
				if ( ! empty( $results ) ) {
					$update_count += 1;
				}
			}
		} // foreach post
		do_action( 'mla_end_mapping' );

		if ( $update_count ) {
			/* translators: 1: field type 2: examined count 3: updated count */
			$message = sprintf( __( '%1$s mapping completed; %2$d attachment(s) examined, %3$d updated.' ), __( 'Custom field', 'media-library-assistant' ), $examine_count, $update_count ) . "\r\n";
		} else {
			/* translators: 1: field type 2: examined count */
			$message = sprintf( __( '%1$s mapping completed; %2$d attachment(s) examined, no changes detected.' ), __( 'Custom field', 'media-library-assistant' ), $examine_count ) . "\r\n";
		}

		return array(
			'message' => $message,
			'body' => '',
			'processed' => $examine_count,
			'unchanged' => $examine_count - $update_count,
			'success' =>  $update_count
		);
	} // _process_custom_field_mapping

	/**
	 * Delete a custom field from the wp_postmeta table
 	 *
	 * @since 1.10
	 *
	 * @param	array specific custom_field_mapping rule
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _delete_custom_field( $value ) {
		global $wpdb;

		$post_meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM {$wpdb->postmeta} LEFT JOIN {$wpdb->posts} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id ) WHERE {$wpdb->postmeta}.meta_key = '%s' AND {$wpdb->posts}.post_type = 'attachment'", $value['name'] ));
		foreach ( $post_meta_ids as $mid )
			delete_metadata_by_mid( 'post', $mid );

		$count = count( $post_meta_ids );
		if ( $count ) {
			/* translators: 1: number of attachments */
			$count_text = sprintf( _n( '%s attachment', '%s attachments', $count, 'media-library-assistant' ), $count );
			/* translators: 1: singular/plural number of attachments */
			return sprintf( __( 'Deleted custom field value from %1$s.', 'media-library-assistant' ) . '<br>', $count_text );
		}

		return __( 'No attachments contained this custom field.', 'media-library-assistant' ) . '<br>';
	} // _delete_custom_field

	/**
	 * Save custom field settings to the options table
 	 *
	 * @since 1.10
	 * @uses $_REQUEST if passed a NULL parameter
	 *
	 * @param	array | NULL	specific custom_field_mapping values 
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _save_custom_field_settings( $new_values = NULL ) {
		$message_list = '';
		$option_messages = '';

		if ( NULL == $new_values ) {
			/*
			 * Start with any page-level options
			 */
			foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
				if ( 'custom_field' == $value['tab'] ) {
					$option_messages .= self::mla_update_option_row( $key, $value );
				}
			}

			/*
			 * Add mapping options
			 */
			$new_values = ( isset( $_REQUEST['custom_field_mapping'] ) ) ? $_REQUEST['custom_field_mapping'] : array();
		} // NULL

		/*
		 * Uncomment this for debugging.
		 */
		// $message_list = $option_messages . '<br>';

		return array(
			'message' => $message_list . MLAOptions::mla_custom_field_option_handler( 'update', 'custom_field_mapping', MLACoreOptions::$mla_option_definitions['custom_field_mapping'], $new_values ),
			'body' => '' 
		);
	} // _save_custom_field_settings

	/**
	 * Process IPTC/EXIF standard field settings against all image attachments
	 * without saving the settings to the mla_option
 	 *
	 * @since 1.00
	 *
	 * @uses $_REQUEST
	 *
	 * @param	integer			offset for chunk mapping 
	 * @param	integer			length for chunk mapping
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _process_iptc_exif_standard( $offset = 0, $length = 0 ) {
		if ( ! isset( $_REQUEST['iptc_exif_mapping']['standard'] ) ) {
			return array(
				/* translators: 1: ERROR tag 2: field type */
				'message' => sprintf( __( '%1$s: No %2$s settings to process.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), __( 'Standard field', 'media-library-assistant' ) ),
				'body' => '',
				'processed' => 0,
				'unchanged' => 0,
				'success' => 0,
			);
		}

		$examine_count = 0;
		$update_count = 0;
		$query = array( 'orderby' => 'none', 'post_parent' => 'all', 'post_mime_type' => 'image,application/*pdf*' );

		if ( $length > 0 ) {
			$query['numberposts'] = $length;
			$query['offset'] = $offset;
		}

		do_action( 'mla_begin_mapping', 'iptc_exif_standard', NULL );
		$posts = MLAShortcodes::mla_get_shortcode_attachments( 0, $query );

		if ( is_string( $posts ) ) {
			return array(
				'message' => $posts,
				'body' => '' 
			);
		}

		foreach ( $posts as $key => $post ) {
			$updates = MLAOptions::mla_evaluate_iptc_exif_mapping( $post, 'iptc_exif_standard_mapping', $_REQUEST['iptc_exif_mapping'] );

			$examine_count += 1;
			if ( ! empty( $updates ) ) {
				$results = MLAData::mla_update_single_item( $post->ID, $updates );
				if ( stripos( $results['message'], __( 'updated.', 'media-library-assistant' ) ) ) {
					$update_count += 1;
				}
			}
		} // foreach post
		do_action( 'mla_end_mapping' );

		if ( $update_count ) {
			/* translators: 1: field type 2: examined count 3: updated count */
			$message = sprintf( __( '%1$s mapping completed; %2$d attachment(s) examined, %3$d updated.' ), 'IPTC/EXIF ' . __( 'Standard field', 'media-library-assistant' ), $examine_count, $update_count ) . "\r\n";
		} else {
			/* translators: 1: field type 2: examined count */
			$message = sprintf( __( '%1$s mapping completed; %2$d attachment(s) examined, no changes detected.' ), 'IPTC/EXIF ' . __( 'Standard field', 'media-library-assistant' ), $examine_count ) . "\r\n";
		}

		return array(
			'message' => $message,
			'body' => '',
			'processed' => $examine_count,
			'unchanged' => $examine_count - $update_count,
			'success' => $update_count
		);
	} // _process_iptc_exif_standard

	/**
	 * Process IPTC/EXIF taxonomy term settings against all image attachments
	 * without saving the settings to the mla_option
 	 *
	 * @since 1.00
	 *
	 * @uses $_REQUEST
	 *
	 * @param	integer			offset for chunk mapping 
	 * @param	integer			length for chunk mapping
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _process_iptc_exif_taxonomy( $offset = 0, $length = 0 ) {
		if ( ! isset( $_REQUEST['iptc_exif_mapping']['taxonomy'] ) ) {
			return array(
				/* translators: 1: ERROR tag 2: field type */
				'message' => sprintf( __( '%1$s: No %2$s settings to process.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), __( 'Taxonomy term', 'media-library-assistant' ) ),
				'body' => '',
				'processed' => 0,
				'unchanged' => 0,
				'success' => 0,
			);
		}

		$examine_count = 0;
		$update_count = 0;
		$query = array( 'orderby' => 'none', 'post_parent' => 'all', 'post_mime_type' => 'image,application/*pdf*' );

		if ( $length > 0 ) {
			$query['numberposts'] = $length;
			$query['offset'] = $offset;
		}

		do_action( 'mla_begin_mapping', 'iptc_exif_taxonomy', NULL );
		$posts = MLAShortcodes::mla_get_shortcode_attachments( 0, $query );

		if ( is_string( $posts ) ) {
			return array(
				'message' => $posts,
				'body' => '' 
			);
		}

		foreach ( $posts as $key => $post ) {
			$updates = MLAOptions::mla_evaluate_iptc_exif_mapping( $post, 'iptc_exif_taxonomy_mapping', $_REQUEST['iptc_exif_mapping'] );

			$examine_count += 1;
			if ( ! empty( $updates ) ) {
				$results = MLAData::mla_update_single_item( $post->ID, array(), $updates['taxonomy_updates']['inputs'], $updates['taxonomy_updates']['actions'] );
				if ( stripos( $results['message'], __( 'updated.', 'media-library-assistant' ) ) ) {
					$update_count += 1;
				}
			}
		} // foreach post
		do_action( 'mla_end_mapping' );

		if ( $update_count ) {
			/* translators: 1: field type 2: examined count 3: updated count */
			$message = sprintf( __( '%1$s mapping completed; %2$d attachment(s) examined, %3$d updated.' ), 'IPTC/EXIF ' . __( 'Taxonomy term', 'media-library-assistant' ), $examine_count, $update_count ) . "\r\n";
		} else {
			/* translators: 1: field type 2: examined count */
			$message = sprintf( __( '%1$s mapping completed; %2$d attachment(s) examined, no changes detected.' ), 'IPTC/EXIF ' . __( 'Taxonomy term', 'media-library-assistant' ), $examine_count ) . "\r\n";
		}

		return array(
			'message' => $message,
			'body' => '',
			'processed' => $examine_count,
			'unchanged' => $examine_count - $update_count,
			'success' => $update_count
		);
	} // _process_iptc_exif_taxonomy

	/**
	 * Process IPTC/EXIF custom field settings against all image attachments
	 * without saving the settings to the mla_option
 	 *
	 * @since 1.00
	 *
	 * @uses $_REQUEST if passed a NULL parameter
	 *
	 * @param	array | NULL	specific iptc_exif_custom_mapping values 
	 * @param	integer			offset for chunk mapping 
	 * @param	integer			length for chunk mapping
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _process_iptc_exif_custom( $settings = NULL, $offset = 0, $length = 0 ) {
		if ( NULL == $settings ) {
			$source = 'iptc_exif_custom';
			$settings = ( isset( $_REQUEST['iptc_exif_mapping'] ) ) ? stripslashes_deep( $_REQUEST['iptc_exif_mapping'] ) : array();
			if ( isset( $settings['custom'][ MLACoreOptions::MLA_NEW_CUSTOM_FIELD ] ) ) {
				unset( $settings['custom'][ MLACoreOptions::MLA_NEW_CUSTOM_FIELD ] );
			}
			if ( isset( $settings['custom'][ MLACoreOptions::MLA_NEW_CUSTOM_RULE ] ) ) {
				unset( $settings['custom'][ MLACoreOptions::MLA_NEW_CUSTOM_RULE ] );
			}
		} else {
			$source = 'iptc_exif_custom_rule';
			$settings = stripslashes_deep( $settings );
		}

		if ( empty( $settings['custom'] ) ) {
			return array(
				/* translators: 1: ERROR tag 2: field type */
				'message' => sprintf( __( '%1$s: No %2$s settings to process.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), __( 'Custom field', 'media-library-assistant' ) ),
				'body' => '',
				'processed' => 0,
				'unchanged' => 0,
				'success' => 0,
			);
		}

		$examine_count = 0;
		$update_count = 0;
		$query = array( 'orderby' => 'none', 'post_parent' => 'all', 'post_mime_type' => 'image,application/*pdf*' );

		if ( $length > 0 ) {
			$query['numberposts'] = $length;
			$query['offset'] = $offset;
		}

		do_action( 'mla_begin_mapping', $source, NULL );
		$posts = MLAShortcodes::mla_get_shortcode_attachments( 0, $query );

		if ( is_string( $posts ) ) {
			return array(
				'message' => $posts,
				'body' => '' 
			);
		}

		foreach ( $posts as $key => $post ) {
			$updates = MLAOptions::mla_evaluate_iptc_exif_mapping( $post, 'iptc_exif_custom_mapping', $settings );

			$examine_count += 1;
			if ( ! empty( $updates ) ) {
				$results = MLAData::mla_update_single_item( $post->ID, $updates );
				if ( stripos( $results['message'], __( 'updated.', 'media-library-assistant' ) ) ) {
					$update_count += 1;
				}
			}
		} // foreach post
		do_action( 'mla_end_mapping' );

		if ( $update_count ) {
			/* translators: 1: field type 2: examined count 3: updated count */
			$message = sprintf( __( '%1$s mapping completed; %2$d attachment(s) examined, %3$d updated.' ), 'IPTC/EXIF ' . __( 'Custom field', 'media-library-assistant' ), $examine_count, $update_count ) . "\r\n";
		} else {
			/* translators: 1: field type 2: examined count */
			$message = sprintf( __( '%1$s mapping completed; %2$d attachment(s) examined, no changes detected.' ), 'IPTC/EXIF ' . __( 'Custom field', 'media-library-assistant' ), $examine_count ) . "\r\n";
		}

		return array(
			'message' => $message,
			'body' => '',
			'processed' => $examine_count,
			'unchanged' => $examine_count - $update_count,
			'success' => $update_count
		);
	} // _process_iptc_exif_custom

	/**
	 * Save IPTC/EXIF custom field settings to the options table
 	 *
	 * @since 1.30
	 *
	 * @param	array	specific iptc_exif_custom_mapping values 
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _save_iptc_exif_custom_settings( $new_values ) {
		return array(
			'message' => MLAOptions::mla_iptc_exif_option_handler( 'update', 'iptc_exif_custom_mapping', MLACoreOptions::$mla_option_definitions['iptc_exif_mapping'], $new_values ),
			'body' => '' 
		);
	} // _save_iptc_exif_custom_settings

	/**
	 * Save IPTC/EXIF settings to the options table
 	 *
	 * @since 1.00
	 *
	 * @uses $_REQUEST
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _save_iptc_exif_settings( ) {
		$message_list = '';
		$option_messages = '';

		/*
		 * Start with any page-level options
		 */
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'iptc_exif' == $value['tab'] ) {
				$option_messages .= self::mla_update_option_row( $key, $value );
			}
		}

		/*
		 * Uncomment this for debugging.
		 */
		//$message_list = $option_messages . '<br>';

		/*
		 * Add mapping options
		 */
		$new_values = ( isset( $_REQUEST['iptc_exif_mapping'] ) ) ? $_REQUEST['iptc_exif_mapping'] : array( 'standard' => array(), 'taxonomy' => array(), 'custom' => array() );

		return array(
			'message' => $message_list . MLAOptions::mla_iptc_exif_option_handler( 'update', 'iptc_exif_mapping', MLACoreOptions::$mla_option_definitions['iptc_exif_mapping'], $new_values ),
			'body' => '' 
		);
	} // _save_iptc_exif_settings

	/**
	 * Save General settings to the options table
 	 *
	 * @since 0.1
	 *
	 * @uses $_REQUEST
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _save_general_settings( ) {
		$message_list = '';

		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'general' == $value['tab'] ) {
				switch ( $key ) {
					case MLACoreOptions::MLA_FEATURED_IN_TUNING:
						MLACore::$process_featured_in = ( 'disabled' != $_REQUEST[ MLA_OPTION_PREFIX . $key ] );
						break;
					case MLACoreOptions::MLA_INSERTED_IN_TUNING:
						MLACore::$process_inserted_in = ( 'disabled' != $_REQUEST[ MLA_OPTION_PREFIX . $key ] );
						break;
					case MLACoreOptions::MLA_GALLERY_IN_TUNING:
						MLACore::$process_gallery_in = ( 'disabled' != $_REQUEST[ MLA_OPTION_PREFIX . $key ] );

						if ( 'refresh' == $_REQUEST[ MLA_OPTION_PREFIX . $key ] ) {
							MLAQuery::mla_flush_mla_galleries( MLACoreOptions::MLA_GALLERY_IN_TUNING );
							/* translators: 1: reference type, e.g., Gallery in */
							$message_list .= "<br>" . sprintf( _x( '%1$s - references updated.', 'message_list', 'media-library-assistant' ), __( 'Gallery in', 'media-library-assistant' ) ) . "\r\n";
							$_REQUEST[ MLA_OPTION_PREFIX . $key ] = 'cached';
						}
						break;
					case MLACoreOptions::MLA_MLA_GALLERY_IN_TUNING:
						MLACore::$process_mla_gallery_in = ( 'disabled' != $_REQUEST[ MLA_OPTION_PREFIX . $key ] );

						if ( 'refresh' == $_REQUEST[ MLA_OPTION_PREFIX . $key ] ) {
							MLAQuery::mla_flush_mla_galleries( MLACoreOptions::MLA_MLA_GALLERY_IN_TUNING );
							/* translators: 1: reference type, e.g., Gallery in */
							$message_list .= "<br>" . sprintf( _x( '%1$s - references updated.', 'message_list', 'media-library-assistant' ), __( 'MLA Gallery in', 'media-library-assistant' ) ) . "\r\n";
							$_REQUEST[ MLA_OPTION_PREFIX . $key ] = 'cached';
						}
						break;
					case MLACoreOptions::MLA_TAXONOMY_SUPPORT:
						/*
						 * Replace missing "checkbox" arguments with empty arrays,
						 * denoting that all of the boxes are unchecked.
						 */
						if ( ! isset( $_REQUEST['tax_support'] ) ) {
							$_REQUEST['tax_support'] = array();
						}
						if ( ! isset( $_REQUEST['tax_quick_edit'] ) ) {
							$_REQUEST['tax_quick_edit'] = array();
						}
						if ( ! isset( $_REQUEST['tax_term_search'] ) ) {
							$_REQUEST['tax_term_search'] = array();
						}
						if ( ! isset( $_REQUEST['tax_flat_checklist'] ) ) {
							$_REQUEST['tax_flat_checklist'] = array();
						}
						if ( ! isset( $_REQUEST['tax_checked_on_top'] ) ) {
							$_REQUEST['tax_checked_on_top'] = array();
						}
						break;
					case MLACoreOptions::MLA_SEARCH_MEDIA_FILTER_DEFAULTS:
						/*
						 * Replace missing "checkbox" arguments with empty arrays,
						 * denoting that all of the boxes are unchecked.
						 */
						if ( ! isset( $_REQUEST['search_fields'] ) ) {
							$_REQUEST['search_fields'] = array();
						}
						break;
					default:
						//	ignore everything else
				} // switch

				$message_list .= self::mla_update_option_row( $key, $value );
			} // general option
		} // foreach mla_options

		$page_content = array(
			'message' => __( 'General settings saved.', 'media-library-assistant' ) . "\r\n",
			'body' => '' 
		);

		/*
		 * Uncomment this for debugging.
		 */
		//$page_content['message'] .= $message_list;

		return $page_content;
	} // _save_general_settings

	/**
	 * Delete saved settings, restoring default values
 	 *
	 * @since 0.1
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _reset_general_settings( ) {
		$message_list = '';

		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'general' == $value['tab'] ) {
				if ( 'custom' == $value['type'] && isset( $value['reset'] ) ) {
					$message = call_user_func( array( 'MLAOptions', $value['reset'] ), 'reset', $key, $value, $_REQUEST );
				} elseif ( ('header' == $value['type']) || ('hidden' == $value['type']) ) {
					$message = '';
				} else {
					MLACore::mla_delete_option( $key );
					/* translators: 1: option name */
					$message = '<br>' . sprintf( _x( 'delete_option "%1$s"', 'message_list', 'media-library-assistant'), $key );
				}

				$message_list .= $message;
			}
		}

		$page_content = array(
			'message' => __( 'General settings reset to default values.', 'media-library-assistant' ) . "\r\n",
			'body' => '' 
		);

		/*
		 * Uncomment this for debugging.
		 */
		// $page_content['message'] .= $message_list;

		return $page_content;
	} // _reset_general_settings

	/**
	 * Compose HTML markup for the import settings if any settings files exist
 	 *
	 * @since 1.50
	 *
	 * @return	string	HTML markup for the Import All Settings button and dropdown list, if any
	 */
	private static function _compose_import_settings( ) {
		if ( ! file_exists( MLA_BACKUP_DIR ) ) {
			return '';
		}

		$prefix = ( ( defined( MLA_OPTION_PREFIX ) ) ? MLA_OPTION_PREFIX : 'mla_' ) . '_options_';
		$prefix_length = strlen( $prefix );
		$backup_files = array();	
		$files = scandir( MLA_BACKUP_DIR, 1 ); // sort descending
		foreach ( $files as $file ) {
			if ( 0 === strpos( $file, $prefix ) ) {
				$tail = substr( $file, $prefix_length, strlen( $file ) - ( $prefix_length + 4 ) );
				$text = sprintf( '%1$s/%2$s/%3$s %4$s', substr( $tail, 0, 4 ), substr( $tail, 4, 2 ), substr( $tail, 6, 2 ), substr( $tail, 9 ) );
				$backup_files [ $text ] = $file;
			}
		}

		if ( empty( $backup_files ) ) {
			return '';
		}

		$option_values = array(
			'value' => 'none',
			'text' => '&mdash; ' . __( 'select settings', 'media-library-assistant' ) . ' &mdash;',
			'selected' => 'selected="selected"'
		);

		$select_options = MLAData::mla_parse_template( self::$page_template_array['select-option'], $option_values );
		foreach ( $backup_files as $text => $file ) {
			$option_values = array(
				'value' => esc_attr( $file ),
				'text' => esc_html( $text ),
				'selected' => ''
			);

			$select_options .= MLAData::mla_parse_template( self::$page_template_array['select-option'], $option_values );
		}

		$option_values = array(
			'key' => 'mla-import-settings-file',
			'options' => $select_options
		);

		return '<input name="mla-general-options-import" type="submit" class="button-primary" value="' . __( 'Import ALL Settings', 'media-library-assistant' ) . '" />' . MLAData::mla_parse_template( self::$page_template_array['select-only'], $option_values );
	} // _compose_import_settings

	/**
	 * Serialize option settings and write them to a file
 	 *
	 * Options with a default value, i.e., not stored in the database are NOT written to the file.
	 *
	 * @since 1.50
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _export_settings( ) {
		$message_list = '';
		$settings = array();
		$stored_count = 0;
		
		/*
		 * These are WordPress options, not MLA options
		 */
		foreach( array( 'image_default_align', 'image_default_link_type', 'image_default_size' ) as $key ) {
			$stored_value = get_option( $key );
			if ( empty( $stored_value ) ) {
				$stored_value = 'default';
			}

			if ( 'default' !== $stored_value ) {
				$settings[ $key ] = $stored_value;
				$stored_count++;
				$message = "<br>{$key} " . _x( 'exported', 'message_list', 'media-library-assistant' );
			} else {
				$message = "<br>{$key} " . _x( 'skipped', 'message_list', 'media-library-assistant' );
			}

			$message_list .= $message;
		}

		/*
		 * Accumulate the settings into an array, then serialize it for writing to the file.
		 */
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			$stored_value = MLACore::mla_get_option( $key, false, true );
			if ( false !== $stored_value ) {
				$settings[ $key ] = $stored_value;
				$stored_count++;
				$message = "<br>{$key} " . _x( 'exported', 'message_list', 'media-library-assistant' );
			} else {
				$message = "<br>{$key} " . _x( 'skipped', 'message_list', 'media-library-assistant' );
			}

			$message_list .= $message;
		}

		$settings = serialize( $settings );
		$page_content = array( 'message' => __( 'ALL settings exported.', 'media-library-assistant' ), 'body' => '' );

		/*
		 * Make sure the directory exists and is writable, then create the file
		 */
		$prefix = ( defined( MLA_OPTION_PREFIX ) ) ? MLA_OPTION_PREFIX : 'mla_';
		$date = date("Ymd_B");
		$filename = MLA_BACKUP_DIR . "{$prefix}_options_{$date}.txt";

		if ( ! file_exists( MLA_BACKUP_DIR ) && ! @mkdir( MLA_BACKUP_DIR ) ) {
			/* translators: 1: ERROR tag 2: backup directory name */
			$page_content['message'] = sprintf( __( '%1$s: The settings directory ( %2$s ) cannot be created.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), MLA_BACKUP_DIR );
			return $page_content;
		} elseif ( ! is_writable( MLA_BACKUP_DIR ) && ! @chmod( MLA_BACKUP_DIR , '0777') ) {
			/* translators: 1: ERROR tag 2: backup directory name */
			$page_content['message'] = sprintf( __( '%1$s: The settings directory ( %2$s ) is not writable.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), MLA_BACKUP_DIR );
			return $page_content;
		}

		if ( ! file_exists( MLA_BACKUP_DIR . 'index.php') ) {
			@ touch( MLA_BACKUP_DIR . 'index.php');
		}

		$file_handle = @fopen( $filename, 'w' );
		if ( ! $file_handle ) {
			/* translators: 1: ERROR tag 2: backup file name */
			$page_content['message'] = sprintf( __( '%1$s: The settings file ( %2$s ) could not be opened.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $filename );
			return $page_content;
			}

		if (false === @fwrite($file_handle, $settings)) {
			$error_info = error_get_last();
			/* translators: 1: ERROR tag 2: PHP error information */
			error_log( sprintf( _x( '%1$s: _export_settings $error_info = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), var_export( $error_info, true ) ), 0 );

			if ( false !== ( $tail = strpos( $error_info['message'], '</a>]: ' ) ) ) {
				$php_errormsg = ':<br>' . substr( $error_info['message'], $tail + 7 );
			} else {
				$php_errormsg = '.';
			}

			/* translators: 1: ERROR tag 2: backup file name 3: error message*/
			$page_content['message'] = sprintf( __( '%1$s: Writing the settings file ( %2$s ) "%3$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $filename, $php_errormsg );
		}

		fclose($file_handle);

		/* translators: 1: number of option settings */
		$page_content['message'] = sprintf( __( 'Settings exported; %1$s settings recorded.', 'media-library-assistant' ), $stored_count );

		/*
		 * Uncomment this for debugging.
		 */
		//$page_content['message'] .= $message_list;

		return $page_content;
	} // _export_settings

	/**
	 * Read a serialized file of option settings and write them to the database
 	 *
	 * @since 1.50
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _import_settings( ) {
		$page_content = array( 'message' => __( 'No settings imported.', 'media-library-assistant' ), 'body' => '' );
		$message_list = '';

		if ( isset( $_REQUEST['mla-import-settings-file'] ) ) {
			$filename = $_REQUEST['mla-import-settings-file'];

			if ( 'none' != $filename ) {
				$filename = MLA_BACKUP_DIR . $filename;
			} else {
				$page_content['message'] = __( 'Please select an import settings file from the dropdown list.', 'media-library-assistant' );
				return $page_content;
			}
		} else {
			$page_content['message'] = __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'The import settings dropdown selection is missing.', 'media-library-assistant' );
			return $page_content;
		}

		$settings = @file_get_contents( $filename, false );
		if ( false === $settings ) {
			$error_info = error_get_last();
			/* translators: 1: ERROR tag 2: PHP error information */
			error_log( sprintf( _x( '%1$s: _import_settings $error_info = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), var_export( $error_info, true ) ), 0 );

			if ( false !== ( $tail = strpos( $error_info['message'], '</a>]: ' ) ) ) {
				$php_errormsg = ':<br>' . substr( $error_info['message'], $tail + 7 );
			} else {
				$php_errormsg = '.';
			}

			/* translators: 1: ERROR tag 2: backup file name 3: error message*/
			$page_content['message'] = sprintf( __( '%1$s: Reading the settings file ( %2$s ) "%3$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $filename, $php_errormsg );
			return $page_content;
		}

		$settings = unserialize( $settings );
		$updated_count = 0;
		$unchanged_count = 0;
		foreach ( $settings as $key => $value ) {
			/*
			 * These are WordPress options, not MLA options
			 */
			if ( in_array( $key, array( 'image_default_align', 'image_default_link_type', 'image_default_size' ) ) ) {
				$stored_value = get_option( $key );
				if ( empty( $stored_value ) ) {
					$stored_value = 'default';
				}

				if ( $stored_value !== $value ) {
					$updated_count++;
					$message_list .= "<br>{$key} " . _x( 'updated', 'message_list', 'media-library-assistant' );
				} else {
					$unchanged_count++;
					$message_list .= "<br>{$key} " . _x( 'unchanged', 'message_list', 'media-library-assistant' );
				}
				
				if ( 'default' === $value ) {
					$value = '';
				}

				update_option( $key, $value );
				continue;
			}

			if ( MLACore::mla_update_option( $key, $value ) ) {
				$updated_count++;
				$message_list .= "<br>{$key} " . _x( 'updated', 'message_list', 'media-library-assistant' );
			} else {
				$unchanged_count++;
				$message_list .= "<br>{$key} " . _x( 'unchanged', 'message_list', 'media-library-assistant' );
			}
		}

		/* translators: 1: number of option settings updated 2: number of option settings unchanged */
		$page_content['message'] = sprintf( __( 'Settings imported; %1$s updated, %2$s unchanged.', 'media-library-assistant' ), $updated_count, $unchanged_count );

		/*
		 * Uncomment this for debugging.
		 */
		//$page_content['message'] .= $message_list;

		return $page_content;
	} // _import_settings
} // class MLASettings
?>