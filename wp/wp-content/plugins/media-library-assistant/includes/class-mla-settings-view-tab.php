<?php
/**
 * Manages the Settings/Media Library Assistant Views tab
 *
 * @package Media Library Assistant
 * @since 2.40
 */

/**
 * Class MLA (Media Library Assistant) Settings View implements the
 * Settings/Media Library Assistant Views tab
 *
 * @package Media Library Assistant
 * @since 2.40
 */
class MLASettings_View {
	/**
	 * Object name for localizing JavaScript - MLA View List Table
	 *
	 * @since 1.40
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_EDIT_VIEW_OBJECT = 'mla_inline_edit_settings_vars';

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
		if ( empty( $_REQUEST['mla_tab'] ) || 'view' !== $_REQUEST['mla_tab'] ) {
			return;
		}

		/*
		 * Initialize common script variables
		 */
		$script_variables = array(
			'error' => __( 'Error while making the changes.', 'media-library-assistant' ),
			'ntdeltitle' => __( 'Remove From Bulk Edit', 'media-library-assistant' ),
			'notitle' => '(' . __( 'no slug', 'media-library-assistant' ) . ')',
			'comma' => _x( ',', 'tag_delimiter', 'media-library-assistant' ),
			'useSpinnerClass' => false,
			'ajax_nonce' => wp_create_nonce( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ),
			'tab' => 'view',
			'fields' => array( 'original_slug', 'slug', 'singular', 'plural', 'specification', 'menu_order' ),
			'checkboxes' => array( 'post_mime_type', 'table_view' ),
			'ajax_action' => MLASettings::JAVASCRIPT_INLINE_EDIT_VIEW_SLUG,
		);

		if ( version_compare( get_bloginfo( 'version' ), '4.2', '>=' ) ) {
			$script_variables['useSpinnerClass'] = true;
		}

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( MLASettings::JAVASCRIPT_INLINE_EDIT_VIEW_SLUG,
			MLA_PLUGIN_URL . "js/mla-inline-edit-settings-scripts{$suffix}.js", 
			array( 'wp-lists', 'suggest', 'jquery' ), MLACore::CURRENT_MLA_VERSION, false );

		wp_localize_script( MLASettings::JAVASCRIPT_INLINE_EDIT_VIEW_SLUG,
			self::JAVASCRIPT_INLINE_EDIT_VIEW_OBJECT, $script_variables );
	}

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
				$message_list .= MLASettings::mla_update_option_row( $key, $value );
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
			'action' => MLACore::MLA_ADMIN_SINGLE_EDIT_UPDATE,
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
	public static function mla_compose_view_tab( ) {
		$page_template_array = MLACore::mla_load_template( 'admin-display-settings-view-tab.tpl' );
		if ( ! is_array( $page_template_array ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			MLACore::mla_debug_add( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLASettings_View::mla_compose_view_tab', var_export( $page_template_array, true ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
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
		$bulk_action = MLASettings::mla_current_bulk_action();
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
				case MLACore::MLA_ADMIN_SINGLE_EDIT_DISPLAY:
					$view = MLAMime::mla_get_post_mime_type( $_REQUEST['mla_item_slug'] );
					$page_content = self::_compose_edit_view_tab( $view, $page_template_array['single-item-edit'] );
					break;
				case MLACore::MLA_ADMIN_SINGLE_EDIT_UPDATE:
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
				if ( MLACoreOptions::MLA_ENABLE_POST_MIME_TYPES == $key ) {
					$options_list .= MLASettings::mla_compose_option_row( $key, $value );
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

		/*
		 * Start with any page-level options
		 */
		$options_list = '';
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'view' == $value['tab'] ) {
				$options_list .= MLASettings::mla_compose_option_row( $key, $value );
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
		$MLAListViewTable->views();
		$MLAListViewTable->display();
		$page_content['body'] .= ob_get_contents();
		ob_end_clean();

		$page_content['body'] .= MLAData::mla_parse_template( $page_template_array['after-table'], $page_values );

		return $page_content;
	}

	/**
	 * Ajax handler for Post MIME Types inline editing (quick edit)
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
} // MLASettings_View

/* 
 * The WP_List_Table class isn't automatically available to plugins
 */
if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class MLA (Media Library Assistant) View List Table implements the "Views"
 * admin settings submenu table
 *
 * Extends the core WP_List_Table class.
 *
 * @package Media Library Assistant
 * @since 1.40
 */
class MLA_View_List_Table extends WP_List_Table {
	/**
	 * Initializes some properties from $_REQUEST variables, then
	 * calls the parent constructor to set some default configs.
	 *
	 * @since 1.40
	 *
	 * @return	void
	 */
	function __construct( ) {
		// MLA does not use this
		$this->modes = array(
			'list' => __( 'List View' ),
		);

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'post_mime_type', //singular name of the listed records
			'plural' => 'post_mime_types', //plural name of the listed records
			'ajax' => true, //does this table support ajax?
			'screen' => 'settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-view'
		) );

		/*
		 * NOTE: There is one add_action call at the end of this source file.
		 */
	}

	/**
	 * Default values for hidden columns
	 *
	 * This array is used when the user-level option is not set, i.e.,
	 * the user has not altered the selection of hidden columns.
	 *
	 * The value on the right-hand side must match the column slug, e.g.,
	 * array(0 => 'ID_parent, 1 => 'title_name').
	 * 
	 * @since 1.40
	 *
	 * @var	array
	 */
	private static $default_hidden_columns	= array(
		// 'name',
		// 'specification',
		// 'post_mime_type',
		// 'table_view',
		'singular',
		// 'plural',
		'menu_order',
		'description'
	);

	/**
	 * Access the default list of hidden columns
	 *
	 * @since 1.40
	 *
	 * @return	array	default list of hidden columns
	 */
	private static function _default_hidden_columns( ) {
		return self::$default_hidden_columns;
	}

	/**
	 * Handler for filter 'get_user_option_managesettings_page_mla-settings-menu-viewcolumnshidden'
	 *
	 * Required because the screen.php get_hidden_columns function only uses
	 * the get_user_option result. Set when the file is loaded because the object
	 * is not created in time for the call from screen.php.
	 *
	 * @since 1.40
	 *
	 * @param	mixed	false or array with current list of hidden columns, if any
	 * @param	string	'managesettings_page_mla-settings-menu-viewcolumnshidden'
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
	 * @since 1.40
	 *
	 * @return	array	list of table columns
	 */
	public static function mla_manage_columns_filter( ) {
		return MLAMime::$default_view_columns;
	}

	/**
	 * Called in the admin_init action because the list_table object isn't
	 * created in time to affect the "screen options" setup.
	 *
	 * @since 1.40
	 *
	 * @return	void
	 */
	public static function mla_admin_init( ) {
		if ( isset( $_REQUEST['mla_tab'] ) && $_REQUEST['mla_tab'] == 'view' ) {
			add_filter( 'get_user_option_managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-viewcolumnshidden', 'MLA_View_List_Table::mla_manage_hidden_columns_filter', 10, 3 );
			add_filter( 'manage_settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-view_columns', 'MLA_View_List_Table::mla_manage_columns_filter', 10, 0 );
		}
	}

	/**
	 * Checks the current user's permissions
	 *
	 * @since 2.14
	 *
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can('manage_options');
	}

	/**
	 * Get the name of the default primary column.
	 *
	 * @since 2.14
	 * @access protected
	 *
	 * @return string Name of the default primary column
	 */
	protected function get_default_primary_column_name() {
		return 'name';
	}

	/**
	 * Generate and display row actions links.
	 *
	 * @since 2.14
	 * @access protected
	 *
	 * @param object $item        Attachment being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string Row actions output for media attachments.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary === $column_name ) {
			$actions = $this->row_actions( $this->_build_rollover_actions( $item, $column_name ) );
			$actions .= $this->_build_inline_data( $item );
			return $actions;
		}

		return '';
	}

	/**
	 * Supply a column value if no column-specific function has been defined
	 *
	 * Called when the parent class can't find a method specifically built for a
	 * given column. All columns should have a specific method, so this function
	 * returns a troubleshooting message.
	 *
	 * @since 1.40
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
	 * @since 1.40
	 * 
	 * @param	object	An MLA post_mime_type object
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
	 * @since 1.40
	 * 
	 * @param	object	An MLA post_mime_type object
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
			'page' => MLACoreOptions::MLA_SETTINGS_SLUG . '-view',
			'mla_tab' => 'view',
			'mla_item_slug' => urlencode( $item->slug )
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

		/*
		 * Get the standard and custom types
		 */
		$mla_types = MLACore::mla_get_option( MLACoreOptions::MLA_POST_MIME_TYPES, true );
		if ( ! is_array( $mla_types ) ) {
			$mla_types = array ();
		}

		$custom_types = MLACore::mla_get_option( MLACoreOptions::MLA_POST_MIME_TYPES, false, true );
		if ( ! is_array( $custom_types ) ) {
			$custom_types = array ();
		}

		$actions['edit'] = '<a href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_EDIT_DISPLAY, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Edit this item', 'media-library-assistant' ) . '">' . __( 'Edit', 'media-library-assistant' ) . '</a>';

		$actions['inline hide-if-no-js'] = '<a class="editinline" href="#" title="' . __( 'Edit this item inline', 'media-library-assistant' ) . '">' . __( 'Quick Edit', 'media-library-assistant' ) . '</a>';

			if ( isset( $custom_types[ $item->slug ] ) ) {
				if ( isset( $mla_types[ $item->slug ] ) ) {
					$actions['delete'] = '<a class="delete-tag"' . ' href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_DELETE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Revert to standard item', 'media-library-assistant' ) . '">' . __( 'Revert to Standard', 'media-library-assistant' ) . '</a>';
				} else {
					$actions['delete'] = '<a class="delete-tag"' . ' href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_DELETE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Delete this item Permanently', 'media-library-assistant' ) . '">' . __( 'Delete Permanently', 'media-library-assistant' ) . '</a>';
				}
			} // custom type

		return $actions;
	}

	/**
	 * Add hidden fields with the data for use in the inline editor
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA post_mime_type object
	 *
	 * @return	string	HTML <div> with row data
	 */
	private function _build_inline_data( $item ) {
		$inline_data = "\r\n" . '<div class="hidden" id="inline_' . $item->post_ID . "\">\r\n";
		$inline_data .= '	<div class="original_slug">' . esc_attr( $item->slug ) . "</div>\r\n";
		$inline_data .= '	<div class="slug">' . esc_attr( $item->slug ) . "</div>\r\n";
		$inline_data .= '	<div class="singular">' . esc_attr( $item->singular ) . "</div>\r\n";
		$inline_data .= '	<div class="plural">' . esc_attr( $item->plural ) . "</div>\r\n";
		$inline_data .= '	<div class="specification">' . esc_attr( $item->specification ) . "</div>\r\n";
		$inline_data .= '	<div class="post_mime_type">' . esc_attr( $item->post_mime_type ) . "</div>\r\n";
		$inline_data .= '	<div class="table_view">' . esc_attr( $item->table_view ) . "</div>\r\n";
		$inline_data .= '	<div class="menu_order">' . esc_attr( $item->menu_order ) . "</div>\r\n";
		$inline_data .= '	<div class="description">' . esc_attr( $item->description ) . "</div>\r\n";
		$inline_data .= "</div>\r\n";
		return $inline_data;
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA post_mime_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_name( $item ) {
		if ( MLATest::$wp_4dot3_plus ) {
			return esc_attr( $item->slug );
		}

		$row_actions = self::_build_rollover_actions( $item, 'name' );
		$slug = esc_attr( $item->slug );
		return sprintf( '%1$s<br>%2$s%3$s', /*%1$s*/ $slug, /*%2$s*/ $this->row_actions( $row_actions ), /*%3$s*/ $this->_build_inline_data( $item ) );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA post_mime_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_specification( $item ) {
		return esc_attr( $item->specification );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA post_mime_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_post_mime_type( $item ) {
		if ( $item->post_mime_type ) {
			return __( 'Yes', 'media-library-assistant' );
		} else {
			return __( 'No', 'media-library-assistant' );
		}
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA post_mime_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_table_view( $item ) {
		if ( $item->table_view ) {
			return __( 'Yes', 'media-library-assistant' );
		} else {
			return __( 'No', 'media-library-assistant' );
		}
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA post_mime_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_singular( $item ) {
		return esc_attr( $item->singular );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA post_mime_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_plural( $item ) {
		return esc_attr( $item->plural );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA post_mime_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_menu_order( $item ) {
		return (string) $item->menu_order;
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA post_mime_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_description( $item ) {
		return esc_attr( $item->description );
	}

	/**
	 * This method dictates the table's columns and titles
	 *
	 * @since 1.40
	 * 
	 * @return	array	Column information: 'slugs'=>'Visible Titles'
	 */
	function get_columns( ) {
		return $columns = MLA_View_List_Table::mla_manage_columns_filter();
	}

	/**
	 * Returns the list of currently hidden columns from a user option or
	 * from default values if the option is not set
	 *
	 * @since 1.40
	 * 
	 * @return	array	Column information,e.g., array(0 => 'ID_parent, 1 => 'title_name')
	 */
	function get_hidden_columns( ) {
		$columns = get_user_option( 'managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-viewcolumnshidden' );

		if ( is_array( $columns ) ) {
			return $columns;
		}

		return self::_default_hidden_columns();
	}

	/**
	 * Returns an array where the  key is the column that needs to be sortable
	 * and the value is db column to sort by.
	 *
	 * @since 1.40
	 * 
	 * @return	array	Sortable column information,e.g.,
	 * 					'slugs'=>array('data_values',boolean)
	 */
	function get_sortable_columns( ) {
		return MLAMime::$default_sortable_view_columns;
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 1.40
	 * 
	 * @return	array	Contains all the bulk actions: 'slugs'=>'Visible Titles'
	 */
	function get_bulk_actions( ) {
		$actions = array();

		$actions['edit'] = __( 'Edit', 'media-library-assistant' );
		$actions['delete'] = __( 'Delete Permanently', 'media-library-assistant' );

		return $actions;
	}

	/**
	 * Prepares the list of items for displaying
	 *
	 * This is where you prepare your data for display. This method will usually
	 * be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args().
	 *
	 * @since 1.40
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
		$total_items = MLAMime::mla_count_view_items( $_REQUEST );
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
		$this->items = MLAMime::mla_query_view_items( $_REQUEST, ( ( $current_page - 1 ) * $per_page ), $per_page );
	}

	/**
	 * Generates (echoes) content for a single row of the table
	 *
	 * @since 1.40
	 *
	 * @param object the current item
	 *
	 * @return void Echoes the row HTML
	 */
	function single_row( $item ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		echo '<tr id="view-' . $item->post_ID . '"' . $row_class . '>';
		echo parent::single_row_columns( $item );
		echo '</tr>';
	}
} // class MLA_View_List_Table

/*
 * Actions are added here, when the source file is loaded, because the MLA_View_List_Table
 * object is created too late to be useful.
 */

/*
 * Actions are added here, when the source file is loaded, because the MLA_Template_List_Table
 * object is created too late to be useful.
 */
add_action( 'admin_enqueue_scripts', 'MLASettings_View::mla_admin_enqueue_scripts' );
add_action( 'admin_init', 'MLA_View_List_Table::mla_admin_init' );
?>