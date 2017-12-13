<?php
/**
 * Manages the Settings/Media Library Assistant Uploads tab
 *
 * @package Media Library Assistant
 * @since 2.40
 */

/**
 * Class MLA (Media Library Assistant) Settings Upload implements the
 * Settings/Media Library Assistant Uploads tab
 *
 * @package Media Library Assistant
 * @since 2.40
 */
class MLASettings_Upload {
	/**
	 * Object name for localizing JavaScript - MLA Upload List Table
	 *
	 * @since 1.40
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_EDIT_UPLOAD_OBJECT = 'mla_inline_edit_settings_vars';

	/**
	 * Load the tab's Javascript files
	 *
	 * @since 2.40
	 *
	 * @param string $page_hook Name of the page being loaded
	 */
	public static function mla_admin_enqueue_scripts( $page_hook ) {
		global $wpdb;

		// Without a tab value that matches ours, there's nothing to do
		if ( empty( $_REQUEST['mla_tab'] ) || 'upload' !== $_REQUEST['mla_tab'] ) {
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
			'tab' => 'upload',
			'fields' => array( 'original_slug', 'slug', 'mime_type', 'icon_type', 'core_type', 'mla_type', 'source', 'standard_source' ),
			'checkboxes' => array( 'disabled' ),
			'ajax_action' => MLASettings::JAVASCRIPT_INLINE_EDIT_UPLOAD_SLUG,
		);

		if ( version_compare( get_bloginfo( 'version' ), '4.2', '>=' ) ) {
			$script_variables['useSpinnerClass'] = true;
		}

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( MLASettings::JAVASCRIPT_INLINE_EDIT_UPLOAD_SLUG,
			MLA_PLUGIN_URL . "js/mla-inline-edit-settings-scripts{$suffix}.js", 
			array( 'wp-lists', 'suggest', 'jquery' ), MLACore::CURRENT_MLA_VERSION, false );

		wp_localize_script( MLASettings::JAVASCRIPT_INLINE_EDIT_UPLOAD_SLUG,
			self::JAVASCRIPT_INLINE_EDIT_UPLOAD_OBJECT, $script_variables );
	}

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
				$message_list .= MLASettings::mla_update_option_row( $key, $value );
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
	private static function _get_icon_type_dropdown( $templates, $name, $selection = '.none.' ) {
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
			'action' => MLACore::MLA_ADMIN_SINGLE_EDIT_UPDATE,
			'original_slug' => $item['slug'],
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'Extension' => __( 'Extension', 'media-library-assistant' ),
			'The extension is' => __( 'The &#8220;extension&#8221; is the file extension for this type, and a unique key for the item. It must be all lowercase and contain only letters and numbers.', 'media-library-assistant' ),
			'MIME Type' => __( 'MIME Type', 'media-library-assistant' ),
			'The MIME Type' => __( 'The MIME Type must be all lowercase and contain only letters, numbers, periods (.), slashes (/) and hyphens (-). It <strong>must be a valid MIME</strong> type, e.g., &#8220;image&#8221; or &#8220;image/jpeg&#8221;.', 'media-library-assistant' ),
			'Icon Type' => __( 'Icon Type', 'media-library-assistant' ),
			'icon_types' => self::_get_icon_type_dropdown( $templates, 'mla_upload_item[icon_type]', $item['icon_type'] ),
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
	} // _compose_edit_upload_tab

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
		echo "    form#adv-settings div.metabox-prefs,\r\n";
		echo "    form#adv-settings fieldset.metabox-prefs {\r\n";
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
			'Search Types' => __( 'Search Types', 'media-library-assistant' ),
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
	} // _compose_optional_upload_tab

	/**
	 * Process an Optional Upload MIME Type selection
	 *
	 * @since 1.40
 	 *
	 * @param	integer	MLA Optional Upload MIME Type ID
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
	} // _process_optional_upload_mime

	/**
	 * Compose the File Upload MIME Types tab content for the Settings subpage
	 *
	 * @since 1.40
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	public static function mla_compose_upload_tab( ) {
		$page_template_array = MLACore::mla_load_template( 'admin-display-settings-upload-tab.tpl' );
		if ( ! is_array( $page_template_array ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			MLACore::mla_debug_add( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLASettings_Upload::mla_compose_upload_tab', var_export( $page_template_array, true ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			return '';
		}

		/*
		 * Untangle confusion between searching, canceling and selecting
		 * on the Optional Uploads screen
		 */
		$bulk_action = MLASettings::mla_current_bulk_action();
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
				case MLACore::MLA_ADMIN_SINGLE_EDIT_DISPLAY:
					$view = MLAMime::mla_get_upload_mime( $_REQUEST['mla_item_slug'] );
					$page_content = self::_compose_edit_upload_tab( $view, $page_template_array );
					break;
				case MLACore::MLA_ADMIN_SINGLE_EDIT_UPDATE:
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
				if ( MLACoreOptions::MLA_ENABLE_UPLOAD_MIMES == $key ) {
					$options_list .= MLASettings::mla_compose_option_row( $key, $value );
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
				$options_list .= MLASettings::mla_compose_option_row( $key, $value );
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
			'icon_types' => self::_get_icon_type_dropdown( $page_template_array, 'mla_upload_item[icon_type]' ),
			'inline_icon_types' => self::_get_icon_type_dropdown( $page_template_array, 'icon_type' ),
			'bulk_icon_types' => self::_get_icon_type_dropdown( $page_template_array, 'icon_type', '.nochange.' ),
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
	} // mla_compose_upload_tab

	/**
	 * Ajax handler for Upload MIME Types inline editing (quick edit)
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
	} // mla_inline_edit_upload_action
} // MLASettings_Upload

/* 
 * The WP_List_Table class isn't automatically available to plugins
 */
if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class MLA (Media Library Assistant) Upload List Table implements the "Upload"
 * admin settings tab
 *
 * Extends the core WP_List_Table class.
 *
 * @package Media Library Assistant
 * @since 1.40
 */
class MLA_Upload_List_Table extends WP_List_Table {
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
			'singular' => 'upload_type', //singular name of the listed records
			'plural' => 'upload_types', //plural name of the listed records
			'ajax' => true, //does this table support ajax?
			'screen' => 'settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-upload'
		) );

		// NOTE: There is one add_action call at the end of this source file.
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
		// 'mime_type',
		'icon_type',
		// 'source',
		// 'status',
		'core_type',
		'mla_type',
		'standard_source',
		'core_icon_type',
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
	 * Handler for filter 'get_user_option_managesettings_page_mla-settings-menu-uploadcolumnshidden'
	 *
	 * Required because the screen.php get_hidden_columns function only uses
	 * the get_user_option result. Set when the file is loaded because the object
	 * is not created in time for the call from screen.php.
	 *
	 * @since 1.40
	 *
	 * @param	mixed	false or array with current list of hidden columns, if any
	 * @param	string	'managesettings_page_mla-settings-menu-uploadcolumnshidden'
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
		/*
		 * For WP 4.3+ icon will be merged with the Extension/name column
		 */
		if ( MLATest::$wp_4dot3_plus ) {
			unset( MLAMime::$default_upload_columns['icon'] );
		}

		return MLAMime::$default_upload_columns;
	}

	/**
	 * Print optional in-lne styles for Uploads submenu table
	 *
	 * @since 2.14
	 */
	public static function mla_admin_print_styles_action() {
		if ( MLATest::$wp_4dot3_plus ) {
			echo "<style type='text/css'>\n";

			// Any icon_type will do
			$image_info = MLAMime::mla_get_icon_type_size( 'image' );

			/*
			 * Primary column including icon and some margin
			 */
			$icon_width = ( $image_info['width'] + 10 ) . 'px';
			$icon_height = ( $image_info['height'] + 5 ) . 'px';

			echo "  table.upload_types td.column-primary {\n";
			echo "    position: relative;\n";
			echo "  }\n";
			echo "  table.upload_types div.upload_types-icon {\n";
			echo "    position: absolute;\n";
			echo "    top: 8px;\n";
			echo "    left: 10px;\n";
			echo "  }\n";
			echo "  table.upload_types div.upload_types-info {\n";
			echo "    margin-left: {$icon_width};\n";
			echo "    min-height: {$icon_height};\n";
			echo "  }\n";

			echo "</style>\n";
		}
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
 		if ( isset( $_REQUEST['mla-optional-uploads-display'] ) || isset( $_REQUEST['mla-optional-uploads-search'] ) ) {
			return;
		}

		if ( isset( $_REQUEST['mla_tab'] ) && $_REQUEST['mla_tab'] == 'upload' ) {
			add_filter( 'get_user_option_managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-uploadcolumnshidden', 'MLA_Upload_list_Table::mla_manage_hidden_columns_filter', 10, 3 );
			add_filter( 'manage_settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-upload_columns', 'MLA_Upload_list_Table::mla_manage_columns_filter', 10, 0 );
			add_action( 'admin_print_styles', 'MLA_Upload_List_Table::mla_admin_print_styles_action' );
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
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="cb_mla_item_ID[]" value="%1$s" />',
		/*%1$s*/ $item->post_ID
		);
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_icon( $item ) {
		return MLAMime::mla_get_icon_type_image( $item->icon_type );
	}

	/**
	 * Add rollover actions to a table column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
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
			'page' => MLACoreOptions::MLA_SETTINGS_SLUG . '-upload',
			'mla_tab' => 'upload',
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

		$actions['edit'] = '<a href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_EDIT_DISPLAY, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Edit this item', 'media-library-assistant' ) . '">' . __( 'Edit', 'media-library-assistant' ) . '</a>';

		$actions['inline hide-if-no-js'] = '<a class="editinline" href="#" title="' . __( 'Edit this item inline', 'media-library-assistant' ) . '">' . __( 'Quick Edit', 'media-library-assistant' ) . '</a>';

		if ( 'custom' == $item->source ) {
			if ( empty( $item->standard_source ) ) {
				$actions['delete'] = '<a class="delete-tag"' . ' href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_DELETE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Delete this item Permanently', 'media-library-assistant' ) . '">' . __( 'Delete Permanently', 'media-library-assistant' ) . '</a>';
			} else {
				$actions['delete'] = '<a class="delete-tag"' . ' href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_DELETE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Revert to standard item', 'media-library-assistant' ) . '">' . __( 'Revert to Standard', 'media-library-assistant' ) . '</a>';
			}
		}

		return $actions;
	}

	/**
	 * Add hidden fields with the data for use in the inline editor
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 *
	 * @return	string	HTML <div> with row data
	 */
	private function _build_inline_data( $item ) {
		$inline_data = "\r\n" . '<div class="hidden" id="inline_' . $item->post_ID . "\">\r\n";
		$inline_data .= '	<div class="original_slug">' . esc_attr( $item->slug ) . "</div>\r\n";
		$inline_data .= '	<div class="slug">' . esc_attr( $item->slug ) . "</div>\r\n";
		$inline_data .= '	<div class="mime_type">' . esc_attr( $item->mime_type ) . "</div>\r\n";
		$inline_data .= '	<div class="icon_type">' . esc_attr( $item->icon_type ) . "</div>\r\n";
		$inline_data .= '	<div class="core_type">' . esc_attr( $item->core_type ) . "</div>\r\n";
		$inline_data .= '	<div class="mla_type">' . esc_attr( $item->mla_type ) . "</div>\r\n";
		$inline_data .= '	<div class="source">' . esc_attr( $item->source ) . "</div>\r\n";
		$inline_data .= '	<div class="standard_source">' . esc_attr( $item->standard_source ) . "</div>\r\n";
		$inline_data .= '	<div class="disabled">' . esc_attr( $item->disabled ) . "</div>\r\n";
		$inline_data .= '	<div class="description">' . esc_attr( $item->description ) . "</div>\r\n";
		$inline_data .= '	<div class="wp_icon_type">' . esc_attr( $item->wp_icon_type ) . "</div>\r\n";
		$inline_data .= '	<div class="mla_icon_type">' . esc_attr( $item->mla_icon_type ) . "</div>\r\n";
		$inline_data .= '	<div class="core_icon_type">' . esc_attr( $item->core_icon_type ) . "</div>\r\n";
		$inline_data .= "</div>\r\n";
		return $inline_data;
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_name( $item ) {
		if ( MLATest::$wp_4dot3_plus ) {
			$content = "<div class=\"upload_types-icon\">\n";
			$content .= self::column_icon( $item );
			$content .= "\n</div>\n";
			$content .= '<div class="upload_types-info">' . esc_attr( $item->slug ) . "</div>\n";
			return $content;
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
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_mime_type( $item ) {
		return esc_attr( $item->mime_type );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_icon_type( $item ) {
		return esc_attr( $item->icon_type );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_source( $item ) {
		return esc_attr( $item->source );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_status( $item ) {
		if ( $item->disabled ) {
			return __( 'Inactive', 'media-library-assistant' );
		} else {
			return __( 'Active', 'media-library-assistant' );
		}
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_core_type( $item ) {
		return esc_attr( $item->core_type );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_mla_type( $item ) {
		return esc_attr( $item->mla_type );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_standard_source( $item ) {
		return (string) $item->standard_source;
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_core_icon_type( $item ) {
		return esc_attr( $item->core_icon_type );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
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
		return MLA_Upload_list_Table::mla_manage_columns_filter();
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
		$columns = get_user_option( 'managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-uploadcolumnshidden' );

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
		return MLAMime::$default_sortable_upload_columns;
	}

	/**
	 * Returns HTML markup for one view that can be used with this table
	 *
	 * @since 1.40
	 *
	 * @param	string	View slug
	 * @param	array	count and labels for the View
	 * @param	string	Slug for current view 
	 * 
	 * @return	string | false	HTML for link to display the view, false if count = zero
	 */
	function _get_view( $view_slug, $upload_item, $current_view ) {
		static $base_url = NULL;

		$class = ( $view_slug == $current_view ) ? ' class="current"' : '';

		/*
		 * Calculate the common values once per page load
		 */
		if ( is_null( $base_url ) ) {
			/*
			 * Remember the view filters
			 */
			$base_url = 'options-general.php?page=' . MLACoreOptions::MLA_SETTINGS_SLUG . '-upload&mla_tab=upload';

			if ( isset( $_REQUEST['s'] ) ) {
				$base_url = add_query_arg( array( 's' => $_REQUEST['s'] ), $base_url );
			}
		}

		$singular = sprintf('%s <span class="count">(%%s)</span>', $upload_item['singular'] );
		$plural = sprintf('%s <span class="count">(%%s)</span>', $upload_item['plural'] );
		$nooped_plural = _n_noop( $singular, $plural, 'media-library-assistant' );
		return "<a href='" . add_query_arg( array( 'mla_upload_view' => $view_slug ), $base_url )
			. "'$class>" . sprintf( translate_nooped_plural( $nooped_plural, $upload_item['count'], 'media-library-assistant' ), number_format_i18n( $upload_item['count'] ) ) . '</a>';
	} // _get_view

	/**
	 * Returns an associative array listing all the views that can be used with this table.
	 * These are listed across the top of the page and managed by WordPress.
	 *
	 * @since 1.40
	 * 
	 * @return	array	View information,e.g., array ( id => link )
	 */
	function get_views( ) {
		// Find current view
		$current_view = isset( $_REQUEST['mla_upload_view'] ) ? $_REQUEST['mla_upload_view'] : 'all';

		// Generate the list of views, retaining keyword search criterion
		$s = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';
		$upload_items = MLAMime::mla_tabulate_upload_items( $s );
		$view_links = array();
		foreach ( $upload_items as $slug => $item )
			$view_links[ $slug ] = self::_get_view( $slug, $item, $current_view );

		return $view_links;
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
		$actions['delete'] = __( 'Delete/Revert Custom', 'media-library-assistant' );

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

		$total_items = MLAMime::mla_count_upload_items( $_REQUEST );
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

		// Register our pagination options & calculations.
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page' => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );

		$current_page = $this->get_pagenum();

		// Assign sorted and paginated data to the items property
		$this->items = MLAMime::mla_query_upload_items( $_REQUEST, ( ( $current_page - 1 ) * $per_page ), $per_page );
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

		echo '<tr id="upload-' . $item->post_ID . '"' . $row_class . '>';
		echo parent::single_row_columns( $item );
		echo '</tr>';
	}
} // class MLA_Upload_List_Table

/**
 * Class MLA (Media Library Assistant) Upload Optional List Table implements the
 * searchable database of exension/type associations for the "Uploads" admin settings tab
 *
 * Extends the core WP_List_Table class.
 *
 * @package Media Library Assistant
 * @since 1.40
 */
class MLA_Upload_Optional_List_Table extends WP_List_Table {
	/**
	 * Initializes some properties from $_REQUEST variables, then
	 * calls the parent constructor to set some default configs.
	 *
	 * @since 1.40
	 *
	 * @return	void
	 */
	function __construct( ) {
		//Set parent defaults
		parent::__construct( array(
			'singular' => 'optional_upload_type', //singular name of the listed records
			'plural' => 'optional_upload_types', //plural name of the listed records
			'ajax' => false, //does this table support ajax?
			'screen' => 'settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-upload'
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
		// 'mime_type',
		// 'core_type',
		// 'mla_type',
		// 'description'
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
	 * Handler for filter 'get_user_option_managesettings_page_mla-settings-menu-uploadcolumnshidden'
	 *
	 * Required because the screen.php get_hidden_columns function only uses
	 * the get_user_option result. Set when the file is loaded because the object
	 * is not created in time for the call from screen.php.
	 *
	 * @since 1.40
	 *
	 * @param	mixed	false or array with current list of hidden columns, if any
	 * @param	string	'managesettings_page_mla-settings-menu-uploadcolumnshidden'
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
		return MLAMime::$default_upload_optional_columns;
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
		if ( isset( $_REQUEST['mla-optional-uploads-display'] ) || isset( $_REQUEST['mla-optional-uploads-search'] ) ) {
			add_filter( 'get_user_option_managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-uploadcolumnshidden', 'MLA_Upload_Optional_List_Table::mla_manage_hidden_columns_filter', 10, 3 );
			add_filter( 'manage_settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-upload_columns', 'MLA_Upload_Optional_List_Table::mla_manage_columns_filter', 10, 0 );
		}
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
		/*%1$s*/ $item->ID
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
			'page' => MLACoreOptions::MLA_SETTINGS_SLUG . '-upload',
			'mla_tab' => 'upload',
			'mla_item_ID' => urlencode( $item->ID )
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

		$actions['select'] = '<a href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_EDIT_UPDATE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Select this entry', 'media-library-assistant' ) . '">' . __( 'Select', 'media-library-assistant' ) . '</a>';

		return $actions;
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
		$row_actions = self::_build_rollover_actions( $item, 'name' );
		$slug = esc_attr( $item->slug );
		return sprintf( '%1$s<br>%2$s', /*%1$s*/ $slug, /*%2$s*/ $this->row_actions( $row_actions ) );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA post_mime_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_mime_type( $item ) {
		return esc_attr( $item->mime_type );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA post_mime_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_core_type( $item ) {
		return esc_attr( $item->core_type );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA post_mime_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_mla_type( $item ) {
		return esc_attr( $item->mla_type );
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
		return $columns = MLA_Upload_Optional_List_Table::mla_manage_columns_filter();
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
		$columns = get_user_option( 'managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-uploadcolumnshidden' );

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
		return MLAMime::$default_upload_optional_sortable_columns;
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

		$actions['select'] = __( 'Select these entries', 'media-library-assistant' );

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
		$total_items = MLAMime::mla_count_optional_upload_items( $_REQUEST );
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
		$this->items = MLAMime::mla_query_optional_upload_items( $_REQUEST, ( ( $current_page - 1 ) * $per_page ), $per_page );
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

		echo '<tr id="optional-upload-' . $item->ID . '"' . $row_class . '>';
		echo parent::single_row_columns( $item );
		echo '</tr>';
	}
} // class MLA_Upload_Optional_List_Table

/*
 * Actions are added here, when the source file is loaded, because the "_list_Table"
 * objects are created too late to be useful.
 */
add_action( 'admin_enqueue_scripts', 'MLASettings_Upload::mla_admin_enqueue_scripts' );
add_action( 'admin_init', 'MLA_Upload_list_Table::mla_admin_init' );
add_action( 'admin_init', 'MLA_Upload_Optional_List_Table::mla_admin_init' );
?>