<?php
/**
 * Manages the Settings/Media Library Assistant Documentation tab
 *
 * @package Media Library Assistant
 * @since 2.40
 */

/**
 * Class MLA (Media Library Assistant) Settings Documentation implements the
 * Settings/Media Library Assistant Documentation tab
 *
 * @package Media Library Assistant
 * @since 2.40
 */
class MLASettings_Documentation {
	/**
	 * Slug for localizing and enqueueing JavaScript
	 *
	 * @since 2.40
	 * @var	string
	 */
	const JAVASCRIPT_DOCUMENTATION_TAB_SLUG = 'mla-documentation-tab-scripts';

	/**
	 * Object name for localizing JavaScript
	 *
	 * @since 2.40
	 * @var	string
	 */
	const JAVASCRIPT_DOCUMENTATION_TAB_OBJECT = 'mla_documentation_tab_vars';

	/**
	 * Load the tab's Javascript files
	 *
	 * @since 2.40
	 *
	 * @param string $page_hook Name of the page being loaded
	 */
	public static function mla_admin_enqueue_scripts( $page_hook ) {
		global $wpdb, $wp_locale;

		// Without a tab value that matches ours, there's nothing to do
		if ( empty( $_REQUEST['mla_tab'] ) || 'documentation' !== $_REQUEST['mla_tab'] ) {
			return;
		}

		// Initialize script variables
		$script_variables = array(
		);

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( self::JAVASCRIPT_DOCUMENTATION_TAB_SLUG,
			MLA_PLUGIN_URL . "js/mla-settings-shortcodes-tab-scripts{$suffix}.js", 
			array( 'jquery' ), MLACore::CURRENT_MLA_VERSION, false );

		wp_localize_script( self::JAVASCRIPT_DOCUMENTATION_TAB_SLUG,
			self::JAVASCRIPT_DOCUMENTATION_TAB_OBJECT, $script_variables );
	}

	/**
	 * Display (read-only) an Example Plugin
	 *
	 * @since 2.40
 	 *
	 * @param integer MLA Example Plugin ID
 	 *
	 * @return array 'message' => status/error messages, 'body' => tab content
	 */
	private static function _display_example_plugin( $ID ) {
		global $wp_filesystem;

		$page_content = array(
			'message' => '',
			'body' => '' 
		);

		$plugin = MLA_Example_List_Table::mla_find_example_plugin( $ID );
		if ( !$plugin ) {
			/* translators: 1: plugin name */
			$page_content['message'] = sprintf( __( 'Example plugin "%1$s" not found', 'media-library-assistant' ), $ID );
			return $page_content;
		}

		$source_path = MLA_PLUGIN_PATH . 'examples/plugins/' . $plugin->file;
		$file_contents = @file_get_contents( $source_path, false );
		if ( false === $file_contents ) {
			$error_info = error_get_last();
			if ( false !== ( $tail = strpos( $error_info['message'], '</a>]: ' ) ) ) {
				$php_errormsg = ':<br>' . substr( $error_info['message'], $tail + 7 );
			} else {
				$php_errormsg = '.';
			}

			/* translators: 1: ERROR tag 2: file type 3: file name 4: error message*/
			$page_content['message'] = sprintf( __( '%1$s: Reading the %2$s file ( %3$s ) "%4$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), __( 'plugin', 'media-library-assistant' ), $plugin->file, $php_errormsg );
			$file_contents = '';
		}

		// Compose tab content
		$page_template_array = MLACore::mla_load_template( 'admin-display-settings-example-tab.tpl' );
		$page_values = array (
			'View Plugin' => __( 'View Plugin', 'media-library-assistant' ),
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-documentation&mla_tab=documentation&mla-example-search=Search',
			'plugin_text' => $file_contents,
			'Close' => __( 'Close', 'media-library-assistant' ),
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'_wp_http_referer' => wp_referer_field( false )
		);

		$page_content['body'] = MLAData::mla_parse_template( $page_template_array['view-plugin'], $page_values );

		return $page_content;
	}

	/**
	 * Compose the Example Plugin tab content for the Settings/Documentation subpage
	 *
	 * @since 2.32
	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_example_tab() {
		$page_template_array = MLACore::mla_load_template( 'admin-display-settings-example-tab.tpl' );

		/*
		 * Display the Example Plugin Table
		 */
		$_SERVER['REQUEST_URI'] = add_query_arg( array( 'mla-example-display' => 'true' ), remove_query_arg( array(
			'mla_admin_action',
			'mla_item_slug',
			'mla_item_ID',
			'_wpnonce',
			'_wp_http_referer',
			'action',
			'action2',
			'cb_attachment',
			'mla-example-search'
		), $_SERVER['REQUEST_URI'] ) );

		//	Create an instance of our package class
		$MLAListExampleTable = new MLA_Example_List_Table();

		//	Fetch, prepare, sort, and filter our data
		$MLAListExampleTable->prepare_items();

		$page_content = array(
			'message' => '',
			'body' => '' 
		);

		$page_values = array(
			'results' => ! empty( $_REQUEST['s'] ) ? ' - ' . __( 'Displaying search results for', 'media-library-assistant' ) . ': "' . $_REQUEST['s'] . '"' : '',
			'In this tab' => __( 'In this tab you can browse the list of MLA example plugins, install or update them in the Plugins/Installed Plugins area and see which examples you have already installed. <strong>To activate, deactivate or delete</strong> the plugins you must go to the Plugins/Installed Plugins admin submenu.' ),
			/* translators: 1: Documentation hyperlink */
			'You can find' => sprintf( __( 'You can find more information about using the example plugins in the %1$s section of the Documentation or by clicking the <strong>"Help"</strong> tab in the upper-right corner of this screen.', 'media-library-assistant' ), '<a href="[+settingsURL+]?page=mla-settings-menu-documentation&amp;mla_tab=documentation#mla_example_plugins" title="' . __( 'Example plugin documentation', 'media-library-assistant' ) . '">' . __( 'The Example Plugins', 'media-library-assistant' ) . '</a>' ),
			'views' => '',
			'settingsURL' => admin_url('options-general.php'),
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-documentation&mla_tab=documentation',
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'Example Plugins' => __( 'Example Plugins', 'media-library-assistant' ),
			'Search Example Plugins' => __( 'Search Example Plugins', 'media-library-assistant' ),
			's' => isset( $_REQUEST['s'] ) ? esc_attr( stripslashes( $_REQUEST['s'] ) ) : '',
			'Search Plugins' => __( 'Search Plugins', 'media-library-assistant' ),
			'Search help' => __( 'Searches Name, Description, File Name and Tags', 'media-library-assistant' ),
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
		);

		ob_start();
		$MLAListExampleTable->views();
		$page_values['views'] = ob_get_contents();
		ob_end_clean();

		$page_content['body'] = MLAData::mla_parse_template( $page_template_array['before-example-table'], $page_values );

		//	 Now we can render the completed list table
		ob_start();
		$MLAListExampleTable->display();
		$page_content['body'] .= ob_get_contents();
		ob_end_clean();

		$page_content['body'] .= MLAData::mla_parse_template( $page_template_array['after-example-table'], $page_values );

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
	public static function mla_compose_documentation_tab( ) {
		/*
		 * Display or Cancel the Example Plugins submenu, if requested
		 */
		if ( !empty( $_REQUEST['mla-example-search'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_compose_example_tab();
		} elseif ( !empty( $_REQUEST['mla-example-cancel'] ) ) {
			$page_content = array(
				'message' => '',
				'body' => '' 
			);
		} elseif ( !empty( $_REQUEST['mla-example-display'] ) ) {
			if ( 'true' != $_REQUEST['mla-example-display'] ) {
				check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			}
			$page_content = self::_compose_example_tab();
		} else {
			$page_content = array(
				'message' => '',
				'body' => '' 
			);
		}

		/*
		 * Process bulk actions that affect an array of items
		 */
		$bulk_action = MLASettings::mla_current_bulk_action();
		if ( $bulk_action && ( $bulk_action != 'none' ) ) {
			$bulk_message = '';
			if ( isset( $_REQUEST['cb_mla_item_ID'] ) ) {
				foreach ( $_REQUEST['cb_mla_item_ID'] as $ID ) {
					switch ( $bulk_action ) {
						case 'install':
							$item_content = MLA_Example_List_Table::mla_install_example_plugin( $ID );
							break;
						case 'update':
							$item_content = MLA_Example_List_Table::mla_update_example_plugin( $ID );
							break;
						default:
							/* translators: 1: bulk_action, e.g., delete, edit, restore, trash */
							$item_content = sprintf( __( 'Unknown bulk action %1$s', 'media-library-assistant' ), $bulk_action );
						break 2;  // Exit the switch and the foreach
;
					} // switch ($_REQUEST['mla_admin_action'])

					$bulk_message .= $item_content . '<br>';
				} // foreach $ID
			} // isset cb_attachment
			else {
				/* translators: 1: action name, e.g., edit */
				$bulk_message = sprintf( __( 'Bulk Action %1$s - no items selected.', 'media-library-assistant' ), $bulk_action );
			}

			$page_content = self::_compose_example_tab();
			$page_content['message'] = $bulk_message;
		} // $bulk_action

		/*
		 * Process row-level actions that affect a single item
		 */
		if ( !empty( $_REQUEST['mla_admin_action'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$action_content = array( 'message' => '' );
			if ( empty( $_REQUEST['mla_item_ID'] ) ) {
				/* translators: 1: bulk_action, e.g., single_item_delete, single_item_edit */
				$action_content['message'] = sprintf( __( 'Empty mla_item_ID - "%1$s"', 'media-library-assistant' ), $_REQUEST['mla_admin_action'] );
			} else {
				switch ( $_REQUEST['mla_admin_action'] ) {
					case MLACore::MLA_ADMIN_SINGLE_EDIT_INSTALL:
						$action_content = MLA_Example_List_Table::mla_install_example_plugin( $_REQUEST['mla_item_ID'] );
						break;
					case MLACore::MLA_ADMIN_SINGLE_EDIT_UPDATE:
						$action_content = MLA_Example_List_Table::mla_update_example_plugin( $_REQUEST['mla_item_ID'] );
						break;
					case MLACore::MLA_ADMIN_SINGLE_EDIT_DISPLAY:
						$action_content = MLASettings_Documentation::_display_example_plugin( $_REQUEST['mla_item_ID'] );
						if ( !empty( $action_content['body'] ) ) {
							return $action_content;
						}

						$action_content = $action_content['message'];
						break;
					default:
						/* translators: 1: bulk_action, e.g., single_item_delete, single_item_edit */
						$action_content = sprintf( __( 'Unknown mla_admin_action - "%1$s"', 'media-library-assistant' ), $_REQUEST['mla_admin_action'] );
						break;
				} // switch ($_REQUEST['mla_admin_action'])
			}

			$page_content = self::_compose_example_tab();
			$page_content['message'] = $action_content;
		} // (!empty($_REQUEST['mla_admin_action'])

		if ( !empty( $page_content['body'] ) ) {
			return $page_content;
		}

		$page_template = MLACore::mla_load_template( 'documentation-settings-tab.tpl' );
		if ( ! is_array( $page_template ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			MLACore::mla_debug_add( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLASettings::_compose_documentation_tab', var_export( $page_template, true ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			return '';
		}

		/*
		 * Display the Documentation tab
		 */
		$page_values = array(
			'example_url' => wp_nonce_url( '?page=mla-settings-menu-documentation&mla_tab=documentation&mla-example-search=Search', MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ),
			'translate_url' => MLA_PLUGIN_URL . 'languages/MLA Internationalization Guide.pdf',
//			'phpDocs_url' => MLA_PLUGIN_URL . 'phpDocs/index.html',
			'phpDocs_url' => 'http://fairtradejudaica.org/wp-content/uploads/' . 'phpDocs/index.html',
		);

		$page_content['body'] = MLAData::mla_parse_template( $page_template['documentation-tab'], $page_values );
		return $page_content;
	}

} // class MLASettings_Documentation

/* 
 * The WP_List_Table class isn't automatically available to plugins
 */
if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/* 
 * The WP_Upgrader classes aren't automatically available to plugins
 */
if ( !class_exists( 'WP_Upgrader' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
}

if ( !class_exists( 'WP_Upgrader_Skin' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader-skins.php' );
}

/**
 * Class MLA (Media Library Assistant) Example List Table implements the
 * searchable database of example plugins for the "Documentation" admin settings tab
 *
 * Extends the core WP_List_Table class.
 *
 * @package Media Library Assistant
 * @since 2.32
 */
class MLA_Example_List_Table extends WP_List_Table {
	/**
	 * Calls the parent constructor to set some default values.
	 *
	 * @since 2.32
	 *
	 * @return	void
	 */
	function __construct( ) {
		//Set parent defaults
		parent::__construct( array(
			'singular' => 'example_plugin', //singular name of the listed records
			'plural' => 'example_plugins', //plural name of the listed records
			'ajax' => false, //does this table support ajax?
			'screen' => 'settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-example'
		) );

		/*
		 * NOTE: There is one add_action call at the end of this source file.
		 */
	}

	/**
	 * Table column definitions
	 *
	 * This array defines table columns and titles where the key is the column slug (and class)
	 * and the value is the column's title text.
	 * 
	 * All of the columns are added to this array by MLA_Example_List_Table::_localize_default_columns_array.
	 *
	 * @since 2.32
	 *
	 * @var	array
	 */
	private static $default_columns = array();

	/**
	 * Default values for hidden columns
	 *
	 * This array is used when the user-level option is not set, i.e.,
	 * the user has not altered the selection of hidden columns.
	 *
	 * The value on the right-hand side must match the column slug, e.g.,
	 * array(0 => 'ID_parent, 1 => 'title_name').
	 * 
	 * @since 2.32
	 *
	 * @var	array
	 */
	private static $default_hidden_columns	= array(
		// 'name',
		// 'version',
		// 'installed_version',
		// 'description'
		// 'file'
		// 'tags'
	);

	/**
	 * Sortable column definitions
	 *
	 * This array defines the table columns that can be sorted. The array key
	 * is the column slug that needs to be sortable, and the value is database column
	 * to sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * The array value also contains a boolean which is 'true' if the initial sort order
	 * for the column is DESC/Descending.
	 *
	 * @since 2.32
	 *
	 * @var	array
	 */
	private static $default_sortable_columns = array(
		'name' => array('name',false),
		'version' => array('version',false),
		'installed_version' => array('installed_version',true),
		'description' => array('description',false),
		'file' => array('file',false),
		'tags' => array('tags',false),
        );

	/**
	 * Access the default list of hidden columns
	 *
	 * @since 2.32
	 *
	 * @return	array	default list of hidden columns
	 */
	private static function _default_hidden_columns( ) {
		return self::$default_hidden_columns;
	}

	/**
	 * Return the names and orderby values of the sortable columns
	 *
	 * @since 2.32
	 *
	 * @return	array	column_slug => array( orderby value, initial_descending_sort ) for sortable columns
	 */
	public static function mla_get_sortable_columns( ) {
		return self::$default_sortable_columns;
	}

	/**
	 * Handler for filter 'get_user_option_managesettings_page_mla-settings-menu-examplecolumnshidden'
	 *
	 * Required because the screen.php get_hidden_columns function only uses
	 * the get_user_option result. Set when the file is loaded because the object
	 * is not created in time for the call from screen.php.
	 *
	 * @since 2.32
	 *
	 * @param	mixed	false or array with current list of hidden columns, if any
	 * @param	string	'managesettings_page_mla-settings-menu-examplecolumnshidden'
	 * @param	object	WP_User object, if logged in
	 *
	 * @return	array	updated list of hidden columns
	 */
	public static function mla_manage_hidden_columns_filter( $result, $option, $user_data ) {
		if ( false !== $result ) {
			return $result;
		}

		return self::_default_hidden_columns();
	}

	/**
	 * Handler for filter 'manage_settings_page_mla-settings-menu_columns'
	 *
	 * This required filter dictates the table's columns and titles. Set when the
	 * file is loaded because the list_table object isn't created in time
	 * to affect the "screen options" setup.
	 *
	 * @since 2.32
	 *
	 * @return	array	list of table columns
	 */
	public static function mla_manage_columns_filter( ) {
		self::_localize_default_columns_array();
		return self::$default_columns;
	}

	/**
	 * Builds the $default_columns array with translated source texts.
	 *
	 * @since 2.32
	 *
	 * @return	void
	 */
	private static function _localize_default_columns_array( ) {
		if ( empty( self::$default_columns ) ) {
			// Build the default columns array at runtime to accomodate calls to the localization functions
			self::$default_columns = array(
				'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
				'name' => _x( 'Name', 'list_table_column', 'media-library-assistant' ),
				'version' => _x( 'Current Version', 'list_table_column', 'media-library-assistant' ),
				'installed_version' => _x( 'Installed Version', 'list_table_column', 'media-library-assistant' ),
				'description' => _x( 'Description', 'list_table_column', 'media-library-assistant' ),
				'file'  => _x( 'File Name', 'list_table_column', 'media-library-assistant' ),
				'tags'  => _x( 'Tags', 'list_table_column', 'media-library-assistant' ),
			);
		}
	}

	/**
	 * Print optional in-line styles for Example Plugins submenu table
	 *
	 * @since 2.32
	 */
	public static function mla_admin_print_styles_action() {
		/*
		 * Suppress display of the hidden columns selection list (disabled),
		 * adjust width of the Version column
		 */
		echo "  <style type='text/css'>\r\n";
		//echo "    form#adv-settings div.metabox-prefs,\r\n";
		//echo "    form#adv-settings fieldset.metabox-prefs {\r\n";
		//echo "      display: none;\r\n";
		//echo "    }\r\n\r\n";
		echo "    table.example_plugins th.column-version,\r\n";
		echo "    table.example_plugins td.column-version {\r\n";
		echo "      width: 6em;\r\n";
		echo "    }\r\n\r\n";
		echo "    table.example_plugins th.column-installed_version,\r\n";
		echo "    table.example_plugins td.column-installed_version {\r\n";
		echo "      width: 7em;\r\n";
		echo "    }\r\n";
		echo "  </style>\r\n";
	}

	/**
	 * Called in the admin_init action because the list_table object isn't
	 * created in time to affect the "screen options" setup.
	 *
	 * @since 2.32
	 *
	 * @return	void
	 */
	public static function mla_admin_init( ) {
//error_log( __LINE__ . ' mla_admin_init request = ' . var_export( $_REQUEST, true ), 0 );
		if ( isset( $_REQUEST['mla-example-cancel'] ) ) {
			unset( $_REQUEST['mla-example-display'] );
		}

		if ( isset( $_REQUEST['mla-example-display'] ) || isset( $_REQUEST['mla-example-search'] ) ) {
			add_filter( 'get_user_option_managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-documentationcolumnshidden', 'MLA_Example_List_Table::mla_manage_hidden_columns_filter', 10, 3 );
			add_filter( 'manage_settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-documentation_columns', 'MLA_Example_List_Table::mla_manage_columns_filter', 10, 0 );
			add_action( 'admin_print_styles', 'MLA_Example_List_Table::mla_admin_print_styles_action' );
		}
	}

	/**
	 * Get the name of the default primary column.
	 *
	 * @since 2.32
	 * @access protected
	 *
	 * @return string Name of the default primary column
	 */
	protected function get_default_primary_column_name() {
		return 'name';
	}

	/**
	 * Supply a column value if no column-specific function has been defined
	 *
	 * Called when the parent class can't find a method specifically built for a
	 * given column. All columns should have a specific method, so this function
	 * returns a troubleshooting message.
	 *
	 * @since 2.32
	 *
	 * @param	array	A singular item (one full row's worth of data)
	 * @param	array	The name/slug of the column to be processed
	 * @return	string	Text or HTML to be placed inside the column
	 */
	function column_default( $item, $column_name ) {
		//Show the whole array for troubleshooting purposes
		/* translators: 1: column_name 2: column_values */
		return sprintf( __( 'column_default: %1$s, %2$s', 'media-library-assistant' ), $column_name, print_r( $item, true ) );
	}

	/**
	 * Displays checkboxes for using bulk actions. The 'cb' column
	 * is given special treatment when columns are processed.
	 *
	 * @since 2.32
	 * 
	 * @param	object	An MLA example_plugin object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="cb_mla_item_ID[]" value="%1$s" />',
		/*%1$s*/ $item->post_ID
		);
	}

	/**
	 * Add rollover actions to a table column
	 *
	 * @since 2.32
	 * 
	 * @param	object	An MLA example_plugin object
	 * @param	string	Current column name
	 *
	 * @return	array	Names and URLs of row-level actions
	 */
	private function _build_rollover_actions( $item, $column ) {
		$actions = array();

		/*
		 * Compose view arguments
		 */

		$view_args = array(
			'page' => MLACoreOptions::MLA_SETTINGS_SLUG . '-documentation',
			'mla_tab' => 'documentation',
			'mla-example-display' => 'true',
			'mla_item_ID' => urlencode( $item->post_ID )
		);

		if ( isset( $_REQUEST['paged'] ) ) {
			$view_args['paged'] = $_REQUEST['paged'];
		}

		if ( isset( $_REQUEST['order'] ) ) {
			$view_args['order'] = $_REQUEST['order'];
		}

		if ( isset( $_REQUEST['orderby'] ) ) {
			$view_args['orderby'] = $_REQUEST['orderby'];
		}

		if ( empty( $item->installed_version ) ) {
			if ( current_user_can( 'install_plugins' ) ) {
				$actions['install'] = '<a href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_EDIT_INSTALL, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Install this plugin', 'media-library-assistant' ) . '">' . __( 'Install', 'media-library-assistant' ) . '</a>';
			}
		} else {
			if ( current_user_can( 'update_plugins' ) ) {
				$actions['update'] = '<a href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_EDIT_UPDATE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Update this plugin', 'media-library-assistant' ) . '">' . __( 'Update', 'media-library-assistant' ) . '</a>';
			}
		}

		if ( current_user_can( 'upload_files' ) && ( false === strpos( $item->file, '/' ) ) ) {
			$args = array(
				'page' => MLACore::ADMIN_PAGE_SLUG,
				'mla_download_file' => urlencode( MLA_PLUGIN_PATH . 'examples/plugins/' . $item->file ),
				'mla_download_type' => 'text/plain'
			);
			$actions['download'] = '<a href="' . add_query_arg( $args, wp_nonce_url( 'upload.php', MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Download', 'media-library-assistant' ) . ' &#8220;' . esc_attr( $item->file ) . '&#8221;">' . __( 'Download', 'media-library-assistant' ) . '</a>';
		}

		$actions['view'] = '<a href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_EDIT_DISPLAY, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'View this item', 'media-library-assistant' ) . '">' . __( 'View', 'media-library-assistant' ) . '</a>';

		return $actions;
	}

	/**
	 * Supply the content for the Name column
	 *
	 * @since 2.32
	 * 
	 * @param	object	An MLA example_plugin object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_name( $item ) {
		$row_actions = self::_build_rollover_actions( $item, 'name' );
		$slug = esc_attr( $item->name );
		return sprintf( '%1$s<br>%2$s', /*%1$s*/ $slug, /*%2$s*/ $this->row_actions( $row_actions ) );
	}

	/**
	 * Supply the content for the Version column
	 *
	 * @since 2.32
	 * 
	 * @param	object	An MLA example_plugin object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_version( $item ) {
		return esc_attr( $item->version );
	}

	/**
	 * Supply the content for the Installed Version column
	 *
	 * @since 2.32
	 * 
	 * @param	object	An MLA example_plugin object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_installed_version( $item ) {
		return esc_attr( $item->installed_version ) . '<br>' . esc_attr( $item->status );
	}

	/**
	 * Supply the content for the Description column
	 *
	 * @since 2.32
	 * 
	 * @param	object	An MLA example_plugin object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_description( $item ) {
		return esc_attr( $item->description );
	}

	/**
	 * Supply the content for the File Name column
	 *
	 * @since 2.32
	 * 
	 * @param	object	An MLA example_plugin object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_file( $item ) {
		return esc_attr( $item->file );
	}

	/**
	 * Supply the content for the Tags column
	 *
	 * @since 2.32
	 * 
	 * @param	object	An MLA example_plugin object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_tags( $item ) {
		return esc_attr( $item->tags );
	}

	/**
	 * This method dictates the table's columns and titles
	 *
	 * @since 2.32
	 * 
	 * @return	array	Column information: 'slugs'=>'Visible Titles'
	 */
	function get_columns( ) {
		return $columns = MLA_Example_List_Table::mla_manage_columns_filter();
	}

	/**
	 * Returns the list of currently hidden columns from a user option or
	 * from default values if the option is not set
	 *
	 * @since 2.32
	 * 
	 * @return	array	Column information,e.g., array(0 => 'ID_parent, 1 => 'title_name')
	 */
	function get_hidden_columns( ) {
		$columns = get_user_option( 'managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-documentationcolumnshidden' );

		if ( is_array( $columns ) ) {
			return $columns;
		}

		return self::_default_hidden_columns();
	}

	/**
	 * Returns an array where the  key is the column that needs to be sortable
	 * and the value is db column to sort by.
	 *
	 * @since 2.32
	 * 
	 * @return	array	Sortable column information,e.g.,
	 * 					'slugs'=>array('data_values',boolean)
	 */
	function get_sortable_columns( ) {
		return self::$default_sortable_columns;
	}

	/**
	 * Returns HTML markup for one view that can be used with this table
	 *
	 * @since 2.32
	 *
	 * @param	string	View slug
	 * @param	array	count and labels for the View
	 * @param	string	Slug for current view 
	 * 
	 * @return	string | false	HTML for link to display the view, false if count = zero
	 */
	function _get_view( $view_slug, $example_item, $current_view ) {
		static $base_url = NULL;

		$class = ( $view_slug == $current_view ) ? ' class="current"' : '';

		/*
		 * Calculate the common values once per page load
		 */
		if ( is_null( $base_url ) ) {
			/*
			 * Remember the view filters
			 */
			$base_url = wp_nonce_url( 'options-general.php?page=' . MLACoreOptions::MLA_SETTINGS_SLUG . '-documentation&mla_tab=documentation&mla-example-search=Search', MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

			if ( isset( $_REQUEST['s'] ) ) {
				$base_url = add_query_arg( array( 's' => $_REQUEST['s'] ), $base_url );
			}
		}

		$singular = sprintf('%s <span class="count">(%%s)</span>', $example_item['singular'] );
		$plural = sprintf('%s <span class="count">(%%s)</span>', $example_item['plural'] );
		$nooped_plural = _n_noop( $singular, $plural, 'media-library-assistant' );
		return "<a href='" . add_query_arg( array( 'mla_example_view' => $view_slug ), $base_url )
			. "'$class>" . sprintf( translate_nooped_plural( $nooped_plural, $example_item['count'], 'media-library-assistant' ), number_format_i18n( $example_item['count'] ) ) . '</a>';
	} // _get_view

	/**
	 * Returns an associative array listing all the views that can be used with this table.
	 * These are listed across the top of the page and managed by WordPress.
	 *
	 * @since 2.32
	 * 
	 * @return	array	View information,e.g., array ( id => link )
	 */
	function get_views( ) {
		/*
		 * Find current view
		 */
		$current_view = isset( $_REQUEST['mla_example_view'] ) ? $_REQUEST['mla_example_view'] : 'all';

		/*
		 * Generate the list of views, retaining keyword search criterion
		 */
		$s = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';
		$example_items = self::_tabulate_example_items( $s );
		$view_links = array();
		foreach ( $example_items as $slug => $item )
			$view_links[ $slug ] = self::_get_view( $slug, $item, $current_view );

		return $view_links;
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 2.32
	 * 
	 * @return	array	Contains all the bulk actions: 'slugs'=>'Visible Titles'
	 */
	function get_bulk_actions( ) {
		return array(
			'install' => __( 'Install', 'media-library-assistant' ),
			'update' => __( 'Update', 'media-library-assistant' ),
		);
	}

	/**
	 * Prepares the list of items for displaying
	 *
	 * This is where you prepare your data for display. This method will usually
	 * be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args().
	 *
	 * @since 2.32
	 *
	 * @return	void
	 */
	function prepare_items( ) {
		$this->_column_headers = array(
			$this->get_columns(),
			$this->get_hidden_columns(),
			$this->get_sortable_columns() 
		);

		/*
		 * REQUIRED for pagination.
		 */
		$total_items = self::_count_example_items( $_REQUEST );
		$user = get_current_user_id();
		$screen = get_current_screen();
		$option = $screen->get_option( 'per_page', 'option' );
		if ( is_string( $option ) ) {
			$per_page = get_user_meta( $user, $option, true );
		} else {
			$per_page = 10;
		}

		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = $screen->get_option( 'per_page', 'default' );
		}

		/*
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page' => $per_page, 
			'total_pages' => ceil( $total_items / $per_page )
		) );

		$current_page = $this->get_pagenum();

		/*
		 * REQUIRED. Assign sorted and paginated data to the items property, where 
		 * it can be used by the rest of the class.
		 */
		$this->items = self::_query_example_items( $_REQUEST, ( ( $current_page - 1 ) * $per_page ), $per_page );
	}

	/**
	 * Generates (echoes) content for a single row of the table
	 *
	 * @since 2.32
	 *
	 * @param object the current item
	 *
	 * @return void Echoes the row HTML
	 */
	function single_row( $item ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		echo '<tr id="example-' . $item->post_ID . '"' . $row_class . '>';
		echo parent::single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Install or update an Example Plugin
	 *
	 * @since 2.32
 	 *
	 * @param	integer	MLA Example Plugin ID
	 * @param	boolean true to update an already-installed plugin, false to install new plugin
 	 *
	 * @return	string	empty or error message
	 */
	private static function _process_example_plugin( $ID, $update = false ) {
		global $wp_filesystem;

		$plugin = MLA_Example_List_Table::mla_find_example_plugin( $ID );
		if ( !$plugin ) {
			/* translators: 1: plugin name */
			return sprintf( __( 'Example plugin "%1$s" not found', 'media-library-assistant' ), $ID );
		}

		$source_parts = explode( '/', $plugin->file );
		if ( $source_is_dir = 1 < count( $source_parts ) ) {
			$source_dir = $source_parts[0];
			$source_file = $source_parts[1];
		} else {
			$source_dir = str_replace( '.php', '', $source_parts[0] );
			$source_file = $source_parts[0];
		}

		$result = validate_plugin( $source_file );
		$target_is_file = ( 0 === $result );

		$source_root = MLA_PLUGIN_PATH . 'examples/plugins/' . $source_dir;
		$target_root = WP_PLUGIN_DIR . '/' . $source_dir;

		$upgrader = new WP_Upgrader( new MLA_Upgrader_Skin() );
		$upgrader->init();
		$result = $upgrader->fs_connect( array( WP_PLUGIN_DIR, $target_root ) );
		if ( is_wp_error($result) ) {
			/* translators: 1: plugin name, 2: WP_Error message */
			return sprintf( __( 'Example plugin "%1$s" fs_connect failed; no action taken. Error: %2$s', 'media-library-assistant' ), $plugin->name, $result->get_error_message() );
		}

		if ( true !== $result ) {
			/* translators: 1: plugin name */
			return sprintf( __( 'Example plugin "%1$s" fs_connect failed; no action taken', 'media-library-assistant' ), $plugin->name );
		}

		/*
		 * Convert a single-file plugin to (temporary) directory-based plugin
		 */
		if ( !$source_is_dir ) {
			global $wp_filesystem;

			$result = $wp_filesystem->mkdir( $source_root, FS_CHMOD_DIR );
			$result = $wp_filesystem->copy( $source_root . '.php', $source_root . '/' . $source_file );
		}

		$upgrader_args = array(
			'source' => $source_root,
			'destination' => $target_root,
			'clear_destination' => $update,
			'clear_working' => !$source_is_dir,
			'abort_if_destination_exists' => !$update,
			'hook_extra' => array()
		);

		$result = $upgrader->install_package( $upgrader_args );
		if ( is_wp_error($result) ) {
			/* translators: 1: plugin name, 2: WP_Error message */
			return sprintf( __( 'Example plugin "%1$s" install_package failed; no action taken. Error: %2$s', 'media-library-assistant' ), $plugin->name, $result->get_error_message() );
		}

		self::_update_example_plugin( $ID, 'installed_version', $plugin->version );
		if ( !$update ) {
			self::_update_example_plugin( $ID, 'status', 'Inactive' );
		}

		if ( $target_is_file ) {
			if ( ! $wp_filesystem->delete( WP_PLUGIN_DIR . '/' . $source_file, false, 'f' ) ) {
				/* translators: 1: plugin name, 2: WP_Error message */
				return sprintf( __( 'Example plugin "%1$s" remove old single file failed.', 'media-library-assistant' ), $plugin->name );
				return new WP_Error( 'remove_old_failed', $this->strings['remove_old_failed'] );
			}
		}

		return '';
	}

	/**
	 * Process an Example Plugin Install action
	 *
	 * @since 2.32
 	 *
	 * @param	integer	MLA Example Plugin ID
 	 *
	 * @return	string	status/error messages
	 */
	public static function mla_install_example_plugin( $ID ) {
		$plugin = MLA_Example_List_Table::mla_find_example_plugin( $ID );
		if ( !$plugin ) {
			/* translators: 1: plugin name */
			return sprintf( __( 'Example plugin "%1$s" not found', 'media-library-assistant' ), $ID );
		}

		if ( !empty( $plugin->installed_version ) ) {
			/* translators: 1: plugin name */
			return sprintf( __( 'Example plugin "%1$s" already installed; no action taken', 'media-library-assistant' ), $plugin->name );
		}

		$result = self::_process_example_plugin( $ID, false );
		if ( empty( $result) ) {
			/* translators: 1: plugin name */
			return sprintf( __( 'Example plugin "%1$s" installed', 'media-library-assistant' ), $plugin->name );
		}

		return $result;
	}

	/**
	 * Process an Example Plugin Update action
	 *
	 * @since 2.32
 	 *
	 * @param	integer	MLA Example Plugin ID
 	 *
	 * @return	string	status/error messages
	 */
	public static function mla_update_example_plugin( $ID ) {
		$plugin = MLA_Example_List_Table::mla_find_example_plugin( $ID );
		if ( !$plugin ) {
			/* translators: 1: plugin name */
			return sprintf( __( 'Example plugin "%1$s" not found', 'media-library-assistant' ), $ID );
		}

		if ( empty( $plugin->installed_version ) ) {
			/* translators: 1: plugin name */
			return sprintf( __( 'Example plugin "%1$s" not installed; no action taken', 'media-library-assistant' ), $plugin->name );
		}

		$result = self::_process_example_plugin( $ID, true );
		if ( empty( $result) ) {
			/* translators: 1: plugin name */
			return sprintf( __( 'Example plugin "%1$s" updated', 'media-library-assistant' ), $plugin->name );
		}

		return $result;
	}

	/**
	 * Callback to sort array by a 'Name' key.
	 *
	 * @since 2.32
	 *
	 * @param	array	The first array
	 * @param	array	The second array
 	 *
	 * @return	integer	The comparison result
	 */
	private static function _sort_uname_callback( $a, $b ) {
		return strnatcasecmp( $a['Name'], $b['Name'] );
	}

	/**
	 * In-memory representation of the Example Plugins
	 *
	 * @since 2.32
	 *
	 * @var	array	ID => ( post_ID, name, version, description, file, tags )
	 */
	private static $_example_plugin_items = NULL;

	/**
	 * Highest existing Post MIME Type ID value
	 *
	 * @since 2.32
	 *
	 * @var	integer
	 */
	private static $_example_plugin_highest_ID = 0;

	/**
	 * Assemble the in-memory representation of the Example Plugins
	 *
	 * @since 2.32
	 *
	 * @param	boolean	Force a reload/recalculation of types
	 *
	 * @return	boolean	Success (true) or failure (false) of the operation
	 */
	private static function _get_example_plugin_items( $force_refresh = false ) {
		if ( false == $force_refresh && NULL != self::$_example_plugin_items ) {
			return true;
		}

		/*
		 * Begin code adapted from /wp-admin/includes/plugin.php function get_plugins()
		 */
		$wp_plugins = array ();
		$plugin_root = MLA_PLUGIN_PATH . 'examples/plugins';

		// Files in media-library-assistant/examples directory
		$plugins_dir = @ opendir( $plugin_root);
		$plugin_files = array();
		if ( $plugins_dir ) {
			while ( ( $file = readdir( $plugins_dir ) ) !== false ) {
				if ( substr( $file, 0, 1 ) == '.' ) {
					continue;
				}

				if ( is_dir( $plugin_root . '/' . $file ) ) {
					$plugins_subdir = @ opendir( $plugin_root . '/' . $file );
					if ( $plugins_subdir ) {
						while ( ( $subfile = readdir( $plugins_subdir ) ) !== false ) {
							if ( substr( $subfile, 0, 1 ) == '.' )
								continue;
							if ( substr( $subfile, -4 ) == '.php' )
								$plugin_files[] = "$file/$subfile";
						}
						closedir( $plugins_subdir );
					}
				} else {
					if ( substr( $file, -4 ) == '.php' ) {
						$plugin_files[] = $file;
					}
				}
			}
			closedir( $plugins_dir );
		}

		if ( empty($plugin_files) ) {
			self::$_example_plugin_items = $wp_plugins;
			return true;
		}

		foreach ( $plugin_files as $plugin_file ) {
			if ( !is_readable( "$plugin_root/$plugin_file" ) )
				continue;

			$default_headers = array(
				'Name' => 'Plugin Name',
				'Version' => 'Version',
				'Description' => 'Description',
				'Tags' => 'Tags',
			);

			$plugin_data = get_file_data( "$plugin_root/$plugin_file", $default_headers, 'mla_example_plugins' );
			if ( empty ( $plugin_data['Name'] ) )
				continue;

			$wp_plugins[plugin_basename( $plugin_file )] = $plugin_data;
		}

		uasort( $wp_plugins, 'self::_sort_uname_callback' );

		/*
		 * End code adapted from /wp-admin/includes/plugin.php function get_plugins()
		 */

		/*
		 * Compose the array
		 */
		$example_plugins = array();
		foreach( $wp_plugins as $file => $metadata ) {
			$plugin_status = validate_plugin( $file );
			if( 0 === $plugin_status ) {
				$plugin_file = $file;
				$plugin_status = get_plugin_data( WP_PLUGIN_DIR . '/' . $file );
				$plugin_version = $plugin_status['Version'];
			} else {
				// Look for a directory-based target for a single-file source
				if ( false === strpos( $file, '/' ) ) {
					$source_dir = str_replace( '.php', '', $file );
					$plugin_status = validate_plugin( "{$source_dir}/{$file}" );
					if( 0 === $plugin_status ) {
						$plugin_file = "{$source_dir}/{$file}";
						$plugin_status = get_plugin_data( WP_PLUGIN_DIR . "/{$plugin_file}" );
						$plugin_version = $plugin_status['Version'];
					} else {
						$plugin_version = '';
					}
				} else {
					$plugin_version = '';
				}
			}

			if ( !empty( $plugin_version ) ) {
				if ( is_plugin_active_for_network( $plugin_file ) ) {
					$plugin_status = __ ( 'Network' );
				} elseif ( is_plugin_active( $plugin_file ) ) {
					$plugin_status = __ ( 'Active' );
				} else {
					$plugin_status = __ ( 'Inactive' );
				}
			} else {
				$plugin_status = '';
			}

			$slug = sanitize_title( str_replace( '.php', '', $file ) );
			$example_plugins[ $slug ] = array( 
				'name' => $metadata['Name'],
				'version' => $metadata['Version'],
				'installed_version' => $plugin_version,
				'status' => $plugin_status,
				'description' => $metadata['Description'],
				'file' => $file,
				'tags' => $metadata['Tags'],
			);
		}

		self::$_example_plugin_items = array();
		self::$_example_plugin_highest_ID = 0;

		/*
		 * Load and number the entries
		 */
		foreach ( $example_plugins as $slug => $value ) {
			self::$_example_plugin_items[ $slug ] = $value;
			self::$_example_plugin_items[ $slug ]['post_ID'] = ++self::$_example_plugin_highest_ID;
		}

		return true;
	}

	/**
	 * Sanitize and expand query arguments from request variables
	 *
	 * @since 2.32
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		Optional number of rows (default 0) to skip over to reach desired page
	 * @param	int		Optional number of rows on each page (0 = all rows, default)
	 *
	 * @return	array	revised arguments suitable for query
	 */
	private static function _prepare_example_items_query( $raw_request, $offset = 0, $count = 0 ) {
		/*
		 * Go through the $raw_request, take only the arguments that are used in the query and
		 * sanitize or validate them.
		 */
		if ( ! is_array( $raw_request ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			MLACore::mla_debug_add( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLAMime::_prepare_view_items_query', var_export( $raw_request, true ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			return NULL;
		}

		$clean_request = array (
			'mla_example_view' => 'all',
			'orderby' => 'name',
			'order' => 'ASC',
			's' => ''
		);

		foreach ( $raw_request as $key => $value ) {
			switch ( $key ) {
				case 'mla_example_view':
					$clean_request[ $key ] = $value;
					break;
				case 'orderby':
					if ( 'none' == $value ) {
						$clean_request[ $key ] = $value;
					} else {
						if ( array_key_exists( $value, MLA_Example_List_Table::mla_get_sortable_columns() ) ) {
							$clean_request[ $key ] = $value;
						}
					}
					break;
				case 'order':
					switch ( $value = strtoupper ($value ) ) {
						case 'ASC':
						case 'DESC':
							$clean_request[ $key ] = $value;
							break;
						default:
							$clean_request[ $key ] = 'ASC';
					}
					break;
				/*
				 * ['s'] - Search items by one or more keywords
				 */
				case 's':
					$clean_request[ $key ] = stripslashes( trim( $value ) );
					break;
				default:
					// ignore anything else in $_REQUEST
			} // switch $key
		} // foreach $raw_request

		/*
		 * Ignore incoming paged value; use offset and count instead
		 */
		if ( ( (int) $count ) > 0 ) {
			$clean_request['offset'] = $offset;
			$clean_request['posts_per_page'] = $count;
		}

		return $clean_request;
	}

	/**
	 * Query the plugin_examples items
	 *
	 * @since 2.32
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 *
	 * @return	array	query results; array of MLA post_mime_type objects
	 */
	private static function _execute_example_items_query( $request ) {
		if ( ! self::_get_example_plugin_items() ) {
			return array ();
		}

		/*
		 * Sort and filter the list
		 */
		$keywords = isset( $request['s'] ) ? $request['s'] : '';
		preg_match_all('/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $keywords, $matches);
		$keywords = array_map( 'MLAQuery::mla_search_terms_tidy', $matches[0]);
		$view = isset( $request['mla_example_view'] ) ? $request['mla_example_view'] : 'all';
		$index = 0;
		$sorted_types = array();

		foreach ( self::$_example_plugin_items as $slug => $value ) {
			$index++;
			if ( ! empty( $keywords ) ) {
				$found = false;
				foreach ( $keywords as $keyword ) {
					//$found |= false !== stripos( $slug, $keyword );
					$found |= false !== stripos( $value['name'], $keyword );
					$found |= false !== stripos( $value['description'], $keyword );
					$found |= false !== stripos( $value['file'], $keyword );
					$found |= false !== stripos( $value['tags'], $keyword );
				}

				if ( ! $found ) {
					continue;
				}
			}

			switch( $view ) {
				case 'installed':
					$found = '' !== $value['status'];
					break;
				case 'active':
					$found = 'Active' === $value['status'];
					break;
				case 'inactive':
					$found = 'Inactive' === $value['status'];
					break;
				case 'network':
					$found = 'Network' === $value['status'];
					break;
				case 'uninstalled':
					$found = '' === $value['status'];
					break;
				default:
					$found = true;
			}// $view

			if ( ! $found ) {
				continue;
			}

			$value['post_ID'] = $index;
			switch ( $request['orderby'] ) {
				case 'name':
					$sorted_types[ ( empty( $value['name'] ) ? chr(1) : $value['name'] ) . $index ] = (object) $value;
					break;
				case 'version':
					$sorted_types[ ( empty( $value['version'] ) ? chr(1) : $value['version'] ) . $index ] = (object) $value;
					break;
				case 'installed_version':
					$sorted_types[ ( empty( $value['installed_version'] ) ? chr(1) : $value['installed_version'] ) . $index ] = (object) $value;
					break;
				case 'description':
					$sorted_types[ ( empty( $value['description'] ) ? chr(1) : $value['description'] ) . $index ] = (object) $value;
					break;
				case 'file':
					$sorted_types[ ( empty( $value['file'] ) ? chr(1) : $value['file'] ) . $index ] = (object) $value;
					break;
				case 'tags':
					$sorted_types[ ( empty( $value['tags'] ) ? chr(1) : $value['tags'] ) . $index ] = (object) $value;
					break;
				default:
					$sorted_types[ $slug ] = (object) $value;
					break;
			} //orderby
		}
		ksort( $sorted_types );

		if ( 'DESC' == $request['order'] ) {
			$sorted_types = array_reverse( $sorted_types, true );
		}

		/*
		 * Paginate the sorted list
		 */
		$results = array();
		$offset = isset( $request['offset'] ) ? $request['offset'] : 0;
		$count = isset( $request['posts_per_page'] ) ? $request['posts_per_page'] : -1;
		foreach ( $sorted_types as $value ) {
			if ( $offset ) {
				$offset--;
			} elseif ( $count-- ) {
				$results[] = $value;
			} else {
				break;
			}
		}

		return $results;
	}

	/**
	 * Get the total number of MLA example_plugin objects
	 *
	 * @since 2.32
	 *
	 * @param	array	Query variables, e.g., from $_REQUEST
	 *
	 * @return	integer	Number of MLA example_plugin objects
	 */
	private static function _count_example_items( $request ) {
		$request = self::_prepare_example_items_query( $request );
		$results = self::_execute_example_items_query( $request );
		return count( $results );
	}

	/**
	 * Retrieve MLA example_plugin objects for list table display
	 *
	 * @since 2.32
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		number of rows to skip over to reach desired page
	 * @param	int		number of rows on each page
	 *
	 * @return	array	MLA example_plugin objects
	 */
	private static function _query_example_items( $request, $offset, $count ) {
		$request = self::_prepare_example_items_query( $request, $offset, $count );
		$results = self::_execute_example_items_query( $request );
		return $results;
	}

	/**
	 * Find an Example Plugin given its ID
	 *
	 * @since 2.32
 	 *
	 * @param	integer	MLA Example Plugin ID
 	 *
	 * @return	mixed	MLA example_plugin object if it exists else false
	 */
	public static function mla_find_example_plugin( $ID ) {
		if ( ! self::_get_example_plugin_items() ) {
			return false;
		}

		$plugin_ID = (string) $ID;
		foreach ( self::$_example_plugin_items as $slug => $value ) {
			if ( $plugin_ID == $value['post_ID'] ) {
				return (object) $value;
			}
		}

		return false;
	}

	/**
	 * Update an Example Plugin given its ID
	 *
	 * @since 2.32
 	 *
	 * @param	integer	MLA Example Plugin ID
	 * @param	string	MLA Example Plugin property
	 * @param	string	MLA Example Plugin new value
 	 *
	 * @return	boolean	true if object exists else false
	 */
	private static function _update_example_plugin( $ID, $key, $value ) {
		if ( ! self::_get_example_plugin_items() ) {
			return false;
		}

		$plugin_ID = (string) $ID;
		foreach ( self::$_example_plugin_items as $slug => $item ) {
			if ( $plugin_ID == $item['post_ID'] ) {
				self::$_example_plugin_items[ $slug ][ $key ] = $value;
				return true;
			}
		}

		return false;
	}

	/**
	 * Tabulate MLA example_plugin objects by view for list table display
	 *
	 * @since 2.32
	 *
	 * @param	string	keyword search criterion, optional
	 *
	 * @return	array	( 'singular' label, 'plural' label, 'count' of items )
	 */
	private static function _tabulate_example_items( $s = '' ) {
		if ( empty( $s ) ) {
			$request = array( 'mla_example_view' => 'all' );
		} else {
			$request = array( 's' => $s );
		}

		$items = self::_query_example_items( $request, 0, 0 );

		$example_items = array(
			'all' => array(
				'singular' => _x( 'All', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'All', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'installed' => array(
				'singular' => _x( 'Installed', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Installed', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'active' => array(
				'singular' => _x( 'Active', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Active', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'inactive' => array(
				'singular' => _x( 'Inactive', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Inactive', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'network' => array(
				'singular' => _x( 'Network', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Network', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'uninstalled' => array(
				'singular' => _x( 'Uninstalled', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Uninstalled', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
		);

		foreach ( $items as $value ) {
			$example_items['all']['count']++;

			switch ( $value->status ) {
				case 'Active':
					$example_items[ 'active' ]['count']++;
					$example_items[ 'installed' ]['count']++;
					break;
				case 'Inactive':
					$example_items[ 'inactive' ]['count']++;
					$example_items[ 'installed' ]['count']++;
					break;
				case 'Network':
					$example_items[ 'network' ]['count']++;
					$example_items[ 'installed' ]['count']++;
					break;
				default:
					$example_items[ 'uninstalled' ]['count']++;
					break;
			}
		}

		return $example_items;
	}
} // class MLA_Example_List_Table

/**
 * Skin for the MLA_Example_List_Table Install and Update functions.
 *
 * Extends the core WP_Upgrader_Skin class.
 *
 * @package Media Library Assistant
 * @since 2.32
 */
class MLA_Upgrader_Skin extends WP_Upgrader_Skin {
	/**
	 * Messages sent to MLA_Upgrader_Skin::feedback()
	 *
	 * @since 2.32
	 *
	 * @var	array
	 */
	public $feedback = array();

	/**
	 * Receive feedback from the WP_Upgrader::install() process
	 *
	 * @since 2.32
	 *
	 * @param string $string
	 */
	public function feedback($string) {
		if ( isset( $this->upgrader->strings[$string] ) )
			$string = $this->upgrader->strings[$string];

		if ( strpos($string, '%') !== false ) {
			$args = func_get_args();
			$args = array_splice($args, 1);
			if ( $args ) {
				$args = array_map( 'strip_tags', $args );
				$args = array_map( 'esc_html', $args );
				$string = vsprintf($string, $args);
			}
		}

		if ( empty($string) ) {
			return;
		}

		$this->feedback[] = $string;
	}
}

/*
 * Actions are added here, when the source file is loaded, because the MLA_Example_List_Table
 * object is created too late to be useful.
 */
//add_action( 'admin_enqueue_scripts', 'MLASettings_Documentation::mla_admin_enqueue_scripts' );
add_action( 'admin_init', 'MLA_Example_List_Table::mla_admin_init' );
?>