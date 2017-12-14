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
	 * Slug for localizing and enqueueing JavaScript - MLA Upload List Table
	 *
	 * @since 1.40
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_EDIT_UPLOAD_SLUG = 'mla-inline-edit-upload-scripts';

	/**
	 * Slug for localizing and enqueueing JavaScript - MLA Custom Fields List Table
	 *
	 * @since 2.50
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_EDIT_CUSTOM_SLUG = 'mla-inline-edit-custom-scripts';

	/**
	 * Slug for localizing and enqueueing JavaScript - MLA Custom tab
	 *
	 * @since 2.00
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_MAPPING_CUSTOM_SLUG = 'mla-inline-mapping-custom-scripts';

	/**
	 * Slug for localizing and enqueueing JavaScript - MLA IPTC/EXIF List Table
	 *
	 * @since 2.60
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_EDIT_IPTC_EXIF_SLUG = 'mla-inline-edit-iptc-exif-scripts';

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
		MLASettings::_localize_tablist();

		//add_action( 'admin_page_access_denied', 'MLASettings::mla_admin_page_access_denied_action' );
		add_action( 'admin_init', 'MLASettings::mla_admin_init_action' );
		add_action( 'admin_menu', 'MLASettings::mla_admin_menu_action' );
		add_action( 'admin_enqueue_scripts', 'MLASettings::mla_admin_enqueue_scripts_action' );
		add_filter( 'set-screen-option', 'MLASettings::mla_set_screen_option_filter', 10, 3 ); // $status, $option, $value
		add_filter( 'screen_options_show_screen', 'MLASettings::mla_screen_options_show_screen_filter', 10, 2 ); // $show_screen, $this
		self::_version_upgrade();

		if( defined('DOING_AJAX') && DOING_AJAX && isset( $_REQUEST['action'] ) ) {
			// Ajax handlers
			switch ( $_REQUEST['action'] ) {
				case self::JAVASCRIPT_INLINE_EDIT_UPLOAD_SLUG:
					require_once( MLA_PLUGIN_PATH . 'includes/class-mla-settings-upload-tab.php' );
					break;
				case self::JAVASCRIPT_INLINE_EDIT_VIEW_SLUG:
					require_once( MLA_PLUGIN_PATH . 'includes/class-mla-settings-view-tab.php' );
					break;
				case self::JAVASCRIPT_INLINE_EDIT_CUSTOM_SLUG:
				case self::JAVASCRIPT_INLINE_MAPPING_CUSTOM_SLUG:
					require_once( MLA_PLUGIN_PATH . 'includes/class-mla-settings-custom-fields-tab.php' );
					break;
				case self::JAVASCRIPT_INLINE_EDIT_IPTC_EXIF_SLUG:
				case self::JAVASCRIPT_INLINE_MAPPING_IPTC_EXIF_SLUG:
					require_once( MLA_PLUGIN_PATH . 'includes/class-mla-settings-iptc-exif-tab.php' );
					break;
			}
		} elseif ( isset( $_REQUEST['page'] ) && is_string( $_REQUEST['page'] ) && ( 'mla-settings-menu-' == substr( $_REQUEST['page'], 0, 18 ) ) ) {
			// Settings/Media Library Assistant current tab
			switch( substr( $_REQUEST['page'], 18 ) ) {
				case 'upload':
					require_once( MLA_PLUGIN_PATH . 'includes/class-mla-settings-upload-tab.php' );
					break;
				case 'view':
					require_once( MLA_PLUGIN_PATH . 'includes/class-mla-settings-view-tab.php' );
					break;
				case 'shortcodes':
					require_once( MLA_PLUGIN_PATH . 'includes/class-mla-settings-shortcodes-tab.php' );
					break;
				case 'custom_field':
					require_once( MLA_PLUGIN_PATH . 'includes/class-mla-settings-custom-fields-tab.php' );
					break;
				case 'iptc_exif':
					require_once( MLA_PLUGIN_PATH . 'includes/class-mla-settings-iptc-exif-tab.php' );
					break;
				case 'documentation':
					require_once( MLA_PLUGIN_PATH . 'includes/class-mla-settings-documentation-tab.php' );
					break;
			}
		}
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
			
			if ( !empty( $option_value['custom'] ) ) {
				$new_values = array();
	
				foreach ( $option_value['custom'] as $key => $value ) {
					$value['format'] = isset( $value['format'] ) ? $value['format'] : 'native';
					$value['option'] = isset( $value['option'] ) ? $value['option'] : 'text';
					$value['no_null'] = isset( $value['no_null'] ) ? $value['no_null'] : false;
					$new_values[ $key ] = $value;
				}
	
				$option_value['custom'] = $new_values;
				MLACore::mla_update_option( 'iptc_exif_mapping', $option_value );
			}
		} // version is less than 2.13

		MLACore::mla_update_option( MLACoreOptions::MLA_VERSION_OPTION, MLACore::CURRENT_MLA_VERSION );
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
		add_action( 'wp_ajax_' . self::JAVASCRIPT_INLINE_EDIT_VIEW_SLUG, 'MLASettings_View::mla_inline_edit_view_action' );
		add_action( 'wp_ajax_' . self::JAVASCRIPT_INLINE_EDIT_UPLOAD_SLUG, 'MLASettings_Upload::mla_inline_edit_upload_action' );
		add_action( 'wp_ajax_' . self::JAVASCRIPT_INLINE_EDIT_CUSTOM_SLUG, 'MLASettings_CustomFields::mla_inline_edit_custom_action' );
		add_action( 'wp_ajax_' . self::JAVASCRIPT_INLINE_MAPPING_CUSTOM_SLUG, 'MLASettings_CustomFields::mla_inline_mapping_custom_action' );
		add_action( 'wp_ajax_' . self::JAVASCRIPT_INLINE_EDIT_IPTC_EXIF_SLUG, 'MLASettings_IPTCEXIF::mla_inline_edit_iptc_exif_action' );
		add_action( 'wp_ajax_' . self::JAVASCRIPT_INLINE_MAPPING_IPTC_EXIF_SLUG, 'MLASettings_IPTCEXIF::mla_inline_mapping_iptc_exif_action' );
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
			wp_register_style( MLACore::STYLESHEET_SLUG, MLA_PLUGIN_URL . 'css/mla-style-rtl.css', false, MLACore::CURRENT_MLA_VERSION );
		} else {
			wp_register_style( MLACore::STYLESHEET_SLUG, MLA_PLUGIN_URL . 'css/mla-style.css', false, MLACore::CURRENT_MLA_VERSION );
		}

		wp_enqueue_style( MLACore::STYLESHEET_SLUG );
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

		$tab = self::_get_options_tablist( $tab ) ? '-' . $tab : '-general';
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
			elseif ( 'shortcodes' == $_REQUEST['mla_tab'] ) {
				$option = 'per_page';

				$args = array(
					 'label' => __( 'Shortcode templates per page', 'media-library-assistant' ),
					'default' => 10,
					'option' => 'mla_shortcode_templates_per_page' 
				);

				add_screen_option( $option, $args );
			} // shortcodes
			elseif ( 'custom_field' == $_REQUEST['mla_tab'] ) {
				$option = 'per_page';

				$args = array(
					 'label' => __( 'Rules per page', 'media-library-assistant' ),
					'default' => 10,
					'option' => 'mla_custom_field_rules_per_page' 
				);

				add_screen_option( $option, $args );
			} // custom_field
			elseif ( 'iptc_exif' == $_REQUEST['mla_tab'] ) {
				$option = 'per_page';

				$args = array(
					 'label' => __( 'Rules per page', 'media-library-assistant' ),
					'default' => 10,
					'option' => 'mla_iptc_exif_rules_per_page' 
				);

				add_screen_option( $option, $args );
			} // iptc_exif
			elseif ( 'documentation' == $_REQUEST['mla_tab'] ) {
				if ( isset( $_REQUEST['mla-example-display'] ) || isset( $_REQUEST['mla-example-search'] ) ) {
					$option = 'per_page';

					$args = array(
						 'label' => __( 'Plugins per page', 'media-library-assistant' ),
						'default' => 10,
						'option' => 'mla_example_plugins_per_page' 
					);

					add_screen_option( $option, $args );
				}
			} // documentation
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

		// Do we have options/help information for this tab?
		$screen_suffix = substr( $screen->id, strlen( 'settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG ) ) ;
		if ( ! in_array( $screen_suffix, array( '-view', '-upload', '-shortcodes', '-custom_field', '-iptc_exif', '-documentation' ) ) ) {
			return;
		}

		$file_suffix = self::$current_page_hook;

		/*
		 * Override the screen suffix if we are going to display something other than the attachment table
		 */
		if ( isset( $_REQUEST['mla-optional-uploads-display'] ) || isset( $_REQUEST['mla-optional-uploads-search'] ) ) {
			$file_suffix .= '-optional';
		} elseif ( isset( $_REQUEST['mla-example-display'] ) || isset( $_REQUEST['mla-example-search'] ) ) {
			$file_suffix = str_replace( '-documentation', '-example', $file_suffix );
		} elseif ( isset( $_REQUEST['mla_admin_action'] ) ) {
			switch ( $_REQUEST['mla_admin_action'] ) {
				case MLACore::MLA_ADMIN_SINGLE_EDIT_DISPLAY:
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
				MLACore::mla_debug_add( sprintf( _x( '%1$s: %2$s discarding "%3$s"; no title/order', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'mla_add_help_tab_action', $id ), MLACore::MLA_DEBUG_CATEGORY_ANY );
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
	 * @return	mixed	New value if this is our option, otherwise original status
	 */
	public static function mla_set_screen_option_filter( $status, $option, $value ) {
		if ( in_array( $option, array ( 'mla_views_per_page', 'mla_uploads_per_page', 'mla_types_per_page', 'mla_shortcode_templates_per_page', 'mla_custom_field_rules_per_page', 'mla_iptc_exif_rules_per_page', 'mla_example_plugins_per_page' ) ) ) {
			return $value;
		}

		MLACore::mla_debug_add( __LINE__ . " MLASettings::mla_set_screen_option_filter( {$option} ) status = " . var_export( $status, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
		MLACore::mla_debug_add( __LINE__ . " MLASettings::mla_set_screen_option_filter( {$option} ) value = " . var_export( $value, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );

		return $status;
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
		if ( $file == 'media-library-assistant/index.php' && current_user_can( 'manage_options' ) ) {
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
					MLACore::mla_debug_add( sprintf( _x( '%1$s: %2$s unknown type = "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), '_save_settings(1)', var_export( $value, true ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
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
					MLACore::mla_debug_add( sprintf( _x( '%1$s: %2$s unknown type = "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), '_save_settings(2)', var_export( $value, true ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
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
					'Go to Bottom' => __( 'Go to Bottom', 'media-library-assistant' ),
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
				MLACore::mla_debug_add( sprintf( _x( '%1$s: %2$s unknown type = "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'mla_render_settings_page', var_export( $value, true ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
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
	 * The array must be populated at runtime in MLASettings::_localize_tablist();
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
	private static function _localize_tablist() {
		self::$mla_tablist = array(
			'general' => array( 'title' => __ ( 'General', 'media-library-assistant' ), 'render' => array( 'MLASettings', '_compose_general_tab' ) ),
			'view' => array( 'title' => __ ( 'Views', 'media-library-assistant' ), 'render' => array( 'MLASettings_View', 'mla_compose_view_tab' ) ),
			'upload' => array( 'title' => __ ( 'Uploads', 'media-library-assistant' ), 'render' => array( 'MLASettings_Upload', 'mla_compose_upload_tab' ) ),
			'shortcodes' => array( 'title' => __ ( 'Shortcodes', 'media-library-assistant' ), 'render' => array( 'MLASettings_Shortcodes', 'mla_compose_shortcodes_tab' ) ),
			'custom_field' => array( 'title' => __ ( 'Custom Fields', 'media-library-assistant' ), 'render' => array( 'MLASettings_CustomFields', 'mla_compose_custom_field_tab' ) ),
			'iptc_exif' => array( 'title' => __ ( 'IPTC/EXIF', 'media-library-assistant' ), 'render' => array( 'MLASettings_IPTCEXIF', 'mla_compose_iptc_exif_tab' ) ),
			'documentation' => array( 'title' => __ ( 'Documentation', 'media-library-assistant' ), 'render' => array( 'MLASettings_Documentation', 'mla_compose_documentation_tab' ) ),
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
	private static function _get_options_tablist( $tab = NULL ) {
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
		foreach ( self::_get_options_tablist() as $key => $item ) {
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
			'Go to Bottom' => __( 'Go to Bottom', 'media-library-assistant' ),
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
			array( 'name' => 'mla_gallery', 'description' => __( 'enhanced version of the WordPress [gallery] shortcode.', 'media-library-assistant' ) . sprintf( ' %1$s <a href="%2$s">%3$s</a>.',  __( 'For complete documentation', 'media-library-assistant' ), admin_url( 'options-general.php?page=' . MLACoreOptions::MLA_SETTINGS_SLUG . '-documentation&amp;mla_tab=documentation#mla_gallery' ), __( 'click here', 'media-library-assistant' ) ) ),
			array( 'name' => 'mla_tag_cloud', 'description' => __( 'enhanced version of the WordPress Tag Cloud.', 'media-library-assistant' ) . sprintf( ' %1$s <a href="%2$s">%3$s</a>.',  __( 'For complete documentation', 'media-library-assistant' ), admin_url( 'options-general.php?page=' . MLACoreOptions::MLA_SETTINGS_SLUG . '-documentation&amp;mla_tab=documentation#mla_tag_cloud' ), __( 'click here', 'media-library-assistant' ) ) ),
			array( 'name' => 'mla_term_list', 'description' => __( 'provides flat or hierarchical lists, dropdown controls and checkbox lists of taxonomy terms.', 'media-library-assistant' ) . sprintf( ' %1$s <a href="%2$s">%3$s</a>.',  __( 'For complete documentation', 'media-library-assistant' ), admin_url( 'options-general.php?page=' . MLACoreOptions::MLA_SETTINGS_SLUG . '-documentation&amp;mla_tab=documentation#mla_term_list' ), __( 'click here', 'media-library-assistant' ) ) ),
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
	public static function mla_current_bulk_action( )	{
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
			/* translators: 1: Documentation hyperlink */
			'You can find' => sprintf( __( 'You can find more information about the MLA Reporting/MLA_DEBUG_LEVEL values in the %1$s section of the Documentation tab.', 'media-library-assistant' ), '<a href="[+settingsURL+]?page=mla-settings-menu-documentation&amp;mla_tab=documentation#mla_debug_tab" title="' . __( 'MLA Debug Tab documentation', 'media-library-assistant' ) . '" target="_blank">' . __( 'MLA Debug Tab', 'media-library-assistant' ) . '</a>' ),
			'settingsURL' => admin_url('options-general.php'),
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
		$current_tab = self::_get_options_tablist( $current_tab_slug );
		$page_values = array(
			'Support Our Work' => __( 'Support Our Work', 'media-library-assistant' ),
			'Donate' => __( 'Donate', 'media-library-assistant' ),
			'version' => 'v' . MLACore::CURRENT_MLA_VERSION,
			'development' => $development_version,
			'messages' => '',
			'tablist' => self::_compose_settings_tabs( $current_tab_slug ),
			'tab_content' => '',
			'Media Library Assistant' => __( 'Media Library Assistant', 'media-library-assistant' ),
			'Settings' => __( 'Settings', 'media-library-assistant' )
		);
//error_log( __LINE__ . " mla_render_settings_page( {$current_tab_slug} ) REQUEST = " . var_export( $_REQUEST, true ), 0 );

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
				$messages_class = 'updated error';
				$dismiss_button = '';
			} else {
				$messages_class = 'updated notice is-dismissible';
				$dismiss_button = "  <button class=\"notice-dismiss\" type=\"button\"><span class=\"screen-reader-text\">[+dismiss_text+].</span></button>\n";
			}

			$page_values['messages'] = MLAData::mla_parse_template( self::$page_template_array['messages'], array(
				 'mla_messages_class' => $messages_class ,
				 'messages' => $page_content['message'],
				 'dismiss_button' => $dismiss_button,
				 'dismiss_text' => __( 'Dismiss this notice', 'media-library-assistant' ),
			) );
		}

		$page_values['tab_content'] = $page_content['body'];
		echo MLAData::mla_parse_template( self::$page_template_array['page'], $page_values );
	} // mla_render_settings_page

	/**
	 * Delete a custom field from the wp_postmeta table
 	 *
	 * @since 1.10
	 *
	 * @param	array specific custom_field_mapping rule
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	public static function mla_delete_custom_field( $value ) {
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
	} // mla_delete_custom_field

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
			MLACore::mla_debug_add( sprintf( _x( '%1$s: _export_settings $error_info = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), var_export( $error_info, true ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );

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
			MLACore::mla_debug_add( sprintf( _x( '%1$s: _import_settings $error_info = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), var_export( $error_info, true ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );

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