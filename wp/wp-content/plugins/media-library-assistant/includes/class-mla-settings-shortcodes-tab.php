<?php
/**
 * Manages the Settings/Media Library Assistant Shortcodes tab
 *
 * @package Media Library Assistant
 * @since 2.40
 */

/**
 * Class MLA (Media Library Assistant) Settings Shortcodes implements the
 * Settings/Media Library Assistant Shortcodes tab
 *
 * @package Media Library Assistant
 * @since 2.40
 */
class MLASettings_Shortcodes {
	/**
	 * Slug for localizing and enqueueing JavaScript
	 *
	 * @since 2.40
	 * @var	string
	 */
	const JAVASCRIPT_SHORTCODES_TAB_SLUG = 'mla-shortcodes-tab-scripts';

	/**
	 * Object name for localizing JavaScript
	 *
	 * @since 2.40
	 * @var	string
	 */
	const JAVASCRIPT_SHORTCODES_TAB_OBJECT = 'mla_shortcodes_tab_vars';

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
		if ( empty( $_REQUEST['mla_tab'] ) || 'shortcodes' !== $_REQUEST['mla_tab'] ) {
			return;
		}

		// Initialize script variables
		$script_variables = array(
			'definitions' => MLATemplate_Support::$mla_template_definitions,
		);

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( self::JAVASCRIPT_SHORTCODES_TAB_SLUG,
			MLA_PLUGIN_URL . "js/mla-settings-shortcodes-tab-scripts{$suffix}.js", 
			array( 'jquery' ), MLACore::CURRENT_MLA_VERSION, false );

		wp_localize_script( self::JAVASCRIPT_SHORTCODES_TAB_SLUG,
			self::JAVASCRIPT_SHORTCODES_TAB_OBJECT, $script_variables );
	}

	/**
	 * Process a shortcode template add action.
	 *
	 * @since 2.40
 	 *
	 * @param array $value New template values.
	 * @return string Action status/error messages.
	 */
	public static function mla_add_template( $value ) {
		$value = stripslashes_deep( $value );
		$value['default'] = false;
		$value['changed'] = true;
		$value['deleted'] = false;

		if ( 'any' === $value['type'] || 'any' === $value['shortcode'] ) {
			/* translators: 1: ERROR tag 2: template type */
			return sprintf( __( '%1$s: %2$s type or shortcode not specified.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), __( 'Template', 'media-library-assistant' ) );
		}
		
		$message_list = '';
		$new_name = sanitize_title( $value['name'] );
		$default_name = 'add-template-default-name';
		$label = ( 'style' == $value['type'] ) ? __( 'style template', 'media-library-assistant' ) : __( 'markup template', 'media-library-assistant' );

		// Handle name validation, check for duplicates
		if ( '' == $new_name ) {
			/* translators: 1: ERROR tag 2: template type 3: old template name */
			$message_list = sprintf( __( '%1$s: Blank %2$s name, reverting to "%3$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $label, $default_name );
			$new_name = $default_name;
		} elseif ( 'blank' == $new_name ) {
			/* translators: 1: ERROR tag 2: template type 3: new template name 4: old template name */
			$message_list = sprintf( __( '%1$s: Reserved %2$s name "%3$s", reverting to "%4$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $label, $new_name, $default_name );
			$new_name = $default_name;
		}

		if ( MLA_Template_Query::mla_find_shortcode_template_ID( $value['type'], $new_name ) ) {
			// Generate a unique name
			$index = 1;
			while( MLA_Template_Query::mla_find_shortcode_template_ID( $value['type'], $new_name . '-' . $index ) ) {
				$index++;
			}

			$default_name = $new_name . '-' . $index;
			
			if ( strlen( $message_list ) ) {
				$message_list .= '<br>';
			}
			
			/* translators: 1: ERROR tag 2: template type 3: new template name 4: old template name */
			$message_list .= sprintf( __( '%1$s: Duplicate new %2$s name "%3$s", reverting to "%4$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $label, $new_name, $default_name );
			$new_name = $default_name;
		} // duplicate name

		$value['name'] = $new_name;
		
		// Find section content
		$sections = array();
		$prefix = $value['type'] . '-' . $value['shortcode'] . '-';
		$allowed_sections = MLATemplate_Support::$mla_template_definitions[ $value['type'] ][ $value['shortcode'] ]['sections'];
		foreach( $value['sections'] as $section_slug => $text ) {
			if ( empty( $text ) || ( false === strpos( $section_slug, $prefix ) ) ) {
				continue;
			}
			
			$key = substr( $section_slug, strlen( $prefix ) );
			if ( array_key_exists( $key, $allowed_sections ) ) {
				$sections[ $key ] = $text;
			}
		}

		  if ( strlen( $message_list ) ) {
			  $message_list .= '<br>';
		  }
			
		if ( empty( $sections ) ) {
			/* translators: 1: ERROR tag 2: template type 3: new template name */
			$message_list .= sprintf( __( '%1$s: New %2$s "%3$s" has no content; not added.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $label, $new_name );
		} else {
			$value['sections'] = $sections;
			MLA_Template_Query::mla_add_shortcode_template( $value );
			/* translators: 1: field type, 2: new template name */
			$message_list .= sprintf( __( '%1$s "%2$s" added.', 'media-library-assistant' ), __( 'Template', 'media-library-assistant' ), $new_name );
		}

		return $message_list;
	}

	/**
	 * Process a shortcode template copy action.
	 *
	 * @since 2.40
 	 *
	 * @param integer $ID Template ID.
	 * @return array New template values, action status/error messages.
	 */
	public static function mla_copy_template( $ID ) {
		$value = MLA_Template_Query::mla_find_shortcode_template( $ID );
		$old_name = $value['name'];
		$new_name = $old_name . '-copy';

		if ( MLA_Template_Query::mla_find_shortcode_template_ID( $value['type'], $new_name ) ) {
			$index = 1;
			while( MLA_Template_Query::mla_find_shortcode_template_ID( $value['type'], $new_name . '-' . $index ) ) {
				$index++;
			}

			$new_name = $new_name . '-' . $index;
		}

		$value['name'] = $new_name;
		MLA_Template_Query::mla_add_shortcode_template( $value );

		/* translators: 1: field type, 2: old template name, 3: new template name */
		$value['message'] =  sprintf( __( '%1$s "%2$s" copied to "%3$s".', 'media-library-assistant' ), __( 'Template', 'media-library-assistant' ), $old_name, $new_name );
		return $value;
	}

	/**
	 * Process a shortcode template update action.
	 *
	 * @since 2.40
 	 *
	 * @param array $value New template values.
	 * @return string Action status/error messages.
	 */
	public static function mla_update_template( $value ) {
		$ID = $value['post_ID'];
		$value = stripslashes_deep( $value );
		$old_value = MLA_Template_Query::mla_find_shortcode_template( $ID );
		$template_changed = false;
		$message_list = '';
		$error_list = '';

		$old_name = $old_value['name'];
		$new_name = sanitize_title( $value['name'] );
		$label = ( 'style' == $value['type'] ) ? __( 'style template name', 'media-library-assistant' ) : __( 'markup template name', 'media-library-assistant' );

		// Handle name changes, check for duplicates
		if ( '' == $new_name ) {
			/* translators: 1: ERROR tag 2: template type 3: old template name */
			$error_list .= '<br>' . sprintf( __( '%1$s: Blank %2$s, reverting to "%3$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $label, $old_name );
			$new_name = $old_name;
		}

		if ( $new_name != $old_name ) {
			if ( MLA_Template_Query::mla_find_shortcode_template_ID( $value['type'], $new_name ) ) {
				/* translators: 1: ERROR tag 2: template type 3: new template name 4: old template name */
				$error_list .= '<br>' . sprintf( __( '%1$s: Duplicate new %2$s "%3$s", reverting to "%4$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $label, $new_name, $old_name );
				$new_name = $old_name;
			} elseif ( 'blank' == $new_name ) {
				/* translators: 1: ERROR tag 2: template type 3: new template name 4: old template name */
				$error_list .= '<br>' . sprintf( __( '%1$s: Reserved %2$s "%3$s", reverting to "%4$s".', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $label, $new_name, $old_name );
				$new_name = $old_name;
			} else {
				/* translators: 1: template type 2: old template name 3: new template name */
				$message_list .= '<br>' . sprintf( _x( 'Changing %1$s from "%2$s" to "%3$s"', 'message_list', 'media-library-assistant' ), $label, $old_name, $new_name );
				$template_changed = true;
			}
		} // name changed

		// Handle section content changes
		foreach( MLATemplate_Support::$mla_template_definitions[ $value['type'] ][ $value['shortcode'] ]['sections'] as $section_name => $definition ) {
			$old_section = isset( $old_value['sections'][ $section_name ] ) ? $old_value['sections'][ $section_name ] : '';
			if ( $value['sections'][ $section_name ] !== $old_section ) {
				$template_changed = true;
			}

			if ( empty( $value['sections'][ $section_name ] ) ) {
				unset( $value['sections'][ $section_name ] );
			}
		}

		if ( $template_changed ) {
			$value['default'] = false;
			$value['changed'] = true;
			$value['deleted'] = false;
			MLA_Template_Query::mla_replace_shortcode_template( $value );
			/* translators: 1: field type, 2: new template name */
			$message_list .= sprintf( __( '%1$s "%2$s" updated.', 'media-library-assistant' ), __( 'Template', 'media-library-assistant' ), $new_name ) . "\r\n";
		} else {
			/* translators: 1: field type, 2: template name */
			$message_list .= sprintf( __( '%1$s "%2$s" no changes detected.', 'media-library-assistant' ), __( 'Template', 'media-library-assistant' ), $new_name ) . "\r\n";
		}

		return $message_list . $error_list;
	}

	/**
	 * Process a shortcode template delete action.
	 *
	 * @since 2.40
 	 *
	 * @param integer $ID Template ID.
	 * @return string Action status/error messages.
	 */
	public static function mla_delete_template( $ID ) {
		$value = MLA_Template_Query::mla_find_shortcode_template( $ID );
		$value['deleted'] = true;
		MLA_Template_Query::mla_replace_shortcode_template( $value );
		/* translators: 1: field type */
		return sprintf( __( '%1$s "%2$s" deleted.', 'media-library-assistant' ), __( 'Template', 'media-library-assistant' ), $value['name'] ) . "\r\n";
	}

	/**
	 * Save Shortcodes settings to the options table
 	 *
	 * @since 2.40
	 *
	 * @uses $_REQUEST
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _save_shortcodes_settings( ) {
		$settings_changed = false;
		$message_list = '';
		$error_list = '';

		// Start with any page-level options
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
				$message = MLASettings::mla_update_option_row( $key, $value );
				if ( $this_setting_changed ) {
					$settings_changed = true;
					$message_list .= $message;
				}
			} // mla_gallery option
		} // foreach mla_options

		if ( $settings_changed ) {
			/* translators: 1: field type */
			$message = sprintf( __( '%1$s settings saved.', 'media-library-assistant' ), __( 'Shortcodes', 'media-library-assistant' ) ) . "\r\n";
		} else {
			/* translators: 1: field type */
			$message = sprintf( __( '%1$s no changes detected.', 'media-library-assistant' ), __( 'Shortcodes', 'media-library-assistant' ) ) . "\r\n";
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
	} // _save_shortcodes_settings

	/**
	 * Compose the Add Template tab content for the Settings/Shortcodes subpage
	 *
	 * @since 2.40
	 *
	 * @param array &$template Display templates.
	 * @return array 'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_add_template_tab( &$template ) {
		// Compose the dropdown controls
		$shortcode_options = '';
		foreach( MLATemplate_Support::$mla_template_definitions['style'] as $shortcode => $definition ) {
			$shortcode_options .= 	"\t\t\t<option value=\"" . $shortcode . '">' . $definition['label'] . "</option>\r\n";
		}

		$page_values = array (
			'Select a type' => '&mdash; ' . __( 'select template type', 'media-library-assistant' ) . ' &mdash;',
			'Select a shortcode' => '&mdash; ' .__( 'select template shortcode', 'media-library-assistant' ) . ' &mdash;',
			'shortcode_options' => $shortcode_options,
			'controls_help' =>  __( 'Select a template type and shortcode to generate the section areas.', 'media-library-assistant' ),
		);

		$controls = MLAData::mla_parse_template( $template['single-item-controls'], $page_values );

		// Compose the template sections
		$sections = array();
		foreach( MLATemplate_Support::$mla_template_definitions as $type => $type_definitions ) {
			foreach( $type_definitions as $shortcode => $shortcode_definition ) {
				foreach( $shortcode_definition['sections'] as $section_name => $definition ) {
					$definition['type'] = $type;
					$definition['shortcode'] = $shortcode;
					$definition['slug'] = $section_name;
					$sections[ $type . $shortcode . sprintf("%'.02d", $definition['order'] ) ] = $definition;
				}
			}
		}
		ksort( $sections, SORT_REGULAR );

		$section_list = '';
		foreach ( $sections as $section ) {
			$page_values = array (
				'class' => 'mla_section mla_' . $section['type'] . ' mla_' . $section['shortcode'],
				'style' => ' style="display: none"',
				'section_slug' => $section['type'] . '-' . $section['shortcode'] . '-' . $section['slug'],
				'section_name' => $section['label'],
				'section_rows' => $section['rows'],
				'readonly' => '',
				'section_value' => '',
				'section_help' => $section['help'],
			);

			$section_list .= MLAData::mla_parse_template( $template['single-item-section'], $page_values );
		}

		$page_values = array(
			'Edit Template' => __( 'Add Template', 'media-library-assistant' ),
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-shortcodes&mla_tab=shortcodes',
			'ID' => 0,
			'type' => 'any',
			'shortcode' => 'any',
			'name' => '',
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'controls' => $controls,
			'Name' => __( 'Name', 'media-library-assistant' ),
			'The name is' => __( 'The name/&#8220;slug&#8221; is the URL-friendly, unique key for the template. It must be all lowercase and contain only letters, numbers and hyphens (-).', 'media-library-assistant' ),
			'section_list' => $section_list,
			'cancel' => 'mla-add-template-cancel',
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
			'submit' =>'mla-add-template-submit',
			'Update' => __( 'Add Template', 'media-library-assistant' ),
			'copy_style' => 'style="display: none"',
			'copy_href' => '#',
			'Copy' => __( 'Copy', 'media-library-assistant' ),
		);

		return array(
			'message' => '',
			'body' => MLAData::mla_parse_template( $template['single-item-edit'], $page_values )
		);
	}

	/**
	 * Compose the Edit Template tab content for the Settings/Shortcodes subpage
	 *
	 * @since 2.40
	 *
	 * @param	array	$item Data values for the item.
	 * @param	array	&$template Display templates.
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_edit_template_tab( $item, &$template ) {
		$sections = array();
		foreach( MLATemplate_Support::$mla_template_definitions[ $item['type'] ][ $item['shortcode'] ]['sections'] as $section_name => $definition ) {
			$definition['slug'] = $section_name;
			$definition['value'] = isset( $item['sections'][ $section_name ] ) ? $item['sections'][ $section_name ] : '';
			$sections[ $definition['order'] ] = $definition;
		}
		ksort( $sections, SORT_NUMERIC );

		$section_list = '';
		foreach ( $sections as $section ) {
			$page_values = array (
				'class' => 'mla_section mla_' . $item['type'] . ' mla_' . $item['shortcode'],
				'style' => '',
				'section_slug' => $section['slug'],
				'section_name' => $section['label'],
				'section_rows' => $section['rows'],
				'readonly' => $item['default'] ? 'readonly="readonly"' : '',
				'section_value' => $section['value'],
				'section_help' => $section['help'],
			);

			$section_list .= MLAData::mla_parse_template( $template['single-item-section'], $page_values );
		}
		
		// Compose copy_href, for default templates
		$view_args = array(
			'page' => MLACoreOptions::MLA_SETTINGS_SLUG . '-shortcodes',
			'mla_tab' => 'shortcodes',
			'mla_item_ID' => $item['post_ID']
		);
		$copy_href = add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_COPY, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) );

		$page_values = array(
			'Edit Template' => $item['default'] ? __( 'View Template', 'media-library-assistant' ) : __( 'Edit Template', 'media-library-assistant' ),
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-shortcodes&mla_tab=shortcodes',
			'ID' => $item['post_ID'],
			'type' => $item['type'],
			'shortcode' => $item['shortcode'],
			'name' => $item['name'],
			'readonly' => $item['default'] ? 'readonly="readonly"' : '',
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'controls' => '', //MLAData::mla_parse_template( $template['single-item-controls'], array() ),
			'Name' => __( 'Name', 'media-library-assistant' ),
			'The name is' => __( 'The name/&#8220;slug&#8221; is the URL-friendly, unique key for the template. It must be all lowercase and contain only letters, numbers and hyphens (-).', 'media-library-assistant' ),
			'section_list' => $section_list,
			'cancel' => $item['default'] ? 'mla-edit-template-close' : 'mla-edit-template-cancel',
			'Cancel' => $item['default'] ? __( 'Close', 'media-library-assistant' ) : __( 'Cancel', 'media-library-assistant' ),
			'submit' => 'mla-edit-template-submit',
			'submit_style' => $item['default'] ? 'style="display: none"' : '',
			'Update' => __( 'Update', 'media-library-assistant' ),
			'copy_style' => $item['default'] ? '' : 'style="display: none"',
			'copy_href' => $copy_href,
			'Copy' => __( 'Copy', 'media-library-assistant' ),
		);

		return array(
			'message' => '',
			'body' => MLAData::mla_parse_template( $template['single-item-edit'], $page_values )
		);
	}

	/**
	 * Compose the Shortcodes tab content for the Settings subpage
	 *
	 * @since 2.40
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	public static function mla_compose_shortcodes_tab( ) {
		$page_content = array( 'message' => '', 'body' => '' );
		$page_template_array = MLACore::mla_load_template( 'admin-display-settings-shortcodes-tab.tpl' );
		if ( ! is_array( $page_template_array ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			$page_content['message'] = sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLASettings_Shortcodes::mla_compose_shortcodes_tab', var_export( $page_template_array, true ) );
			return $page_content;
		}

		// Initialize page messages and content, check for Save Changes, Add/Update/Cancel Template
		if ( !empty( $_REQUEST['mla-shortcodes-options-save'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_save_shortcodes_settings( );
		} elseif ( !empty( $_REQUEST['mla-add-new-template-submit'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_compose_add_template_tab( $page_template_array );
		} elseif ( !empty( $_REQUEST['mla-add-template-submit'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content['message'] = MLASettings_Shortcodes::mla_add_template( $_REQUEST['mla_template_item'] );
			MLA_Template_Query::mla_put_shortcode_template_items();
		} elseif ( !empty( $_REQUEST['mla-edit-template-submit'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content['message'] = MLASettings_Shortcodes::mla_update_template( $_REQUEST['mla_template_item'] );
			MLA_Template_Query::mla_put_shortcode_template_items();
		} elseif ( !empty( $_REQUEST['mla-add-template-cancel'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content['message'] = __( 'Add Template cancelled.', 'media-library-assistant' );
		} elseif ( !empty( $_REQUEST['mla-edit-template-cancel'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content['message'] = __( 'Edit Template cancelled.', 'media-library-assistant' );
		}

		if ( !empty( $page_content['body'] ) ) {
			return $page_content;
		}

		/*
		 * Process bulk actions (delete, copy) that affect an array of items
		 */
		$bulk_action = MLASettings::mla_current_bulk_action();
		if ( $bulk_action && ( $bulk_action != 'none' ) ) {
			if ( isset( $_REQUEST['cb_mla_item_ID'] ) ) {
				foreach ( $_REQUEST['cb_mla_item_ID'] as $post_ID ) {
					switch ( $bulk_action ) {
						case 'delete':
							$item_content = MLASettings_Shortcodes::mla_delete_template( $post_ID );
							break;
						case 'copy':
							$content = MLASettings_Shortcodes::mla_copy_template( $post_ID );
							$item_content = $content['message'];
							break;
						default:
							$item_content = array(
								/* translators: 1: bulk_action, e.g., delete, edit, restore, trash */
								 'message' => sprintf( __( 'Unknown bulk action %1$s', 'media-library-assistant' ), $bulk_action ),
								'body' => '' 
							);
					} // switch $bulk_action

					$page_content['message'] .= $item_content . '<br>';
				} // foreach cb_attachment

				MLA_Template_Query::mla_put_shortcode_template_items();
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

			$page_content = array( 'message' => '', 'body' => '' );

			switch ( $_REQUEST['mla_admin_action'] ) {
				case MLACore::MLA_ADMIN_SINGLE_COPY:
					$content =  MLASettings_Shortcodes::mla_copy_template( $_REQUEST['mla_item_ID'] );
					MLA_Template_Query::mla_put_shortcode_template_items();
					$item = MLA_Template_Query::mla_find_shortcode_template_ID( $content['type'], $content['name'] );
					$item = MLA_Template_Query::mla_find_shortcode_template( $item );
					$page_content = self::_compose_edit_template_tab( $item, $page_template_array );
					break;
				case MLACore::MLA_ADMIN_SINGLE_EDIT_DISPLAY:
					$item = MLA_Template_Query::mla_find_shortcode_template( $_REQUEST['mla_item_ID'] );
					$page_content = self::_compose_edit_template_tab( $item, $page_template_array );
					break;
				case MLACore::MLA_ADMIN_SINGLE_DELETE:
					$page_content['message'] = MLASettings_Shortcodes::mla_delete_template( $_REQUEST['mla_item_ID'] );
					MLA_Template_Query::mla_put_shortcode_template_items();
					break;
				default:
					$page_content['message'] = sprintf( __( 'Unknown mla_admin_action - "%1$s"', 'media-library-assistant' ), $_REQUEST['mla_admin_action'] );
					break;
			} // switch ($_REQUEST['mla_admin_action'])
		} // (!empty($_REQUEST['mla_admin_action'])

		if ( !empty( $page_content['body'] ) ) {
			return $page_content;
		}

		/*
		 * Display the Shortcodes tab and the Template table
		 */
		$_SERVER['REQUEST_URI'] = remove_query_arg( array(
			'mla_admin_action',
			'mla_template_item',
			'mla_item_ID',
			'_wpnonce',
			'_wp_http_referer',
			'action',
			'action2',
			'cb_mla_item_ID',
			'mla-edit-template-cancel',
			'mla-edit-template-submit',
			'mla-shortcodes-options-save',
		), $_SERVER['REQUEST_URI'] );

		// Create an instance of our package class
		$MLATemplateListTable = new MLA_Template_List_Table();

		//	Fetch, prepare, sort, and filter our data
		$MLATemplateListTable->prepare_items();

		/*
		 * Build default template selection lists
		 */
		MLACoreOptions::$mla_option_definitions['default_style']['options'][] = 'none';
		MLACoreOptions::$mla_option_definitions['default_style']['texts'][] = '&mdash; ' . __( 'None', 'media-library-assistant' ) . ' &mdash;';
		MLACoreOptions::$mla_option_definitions['default_style']['options'][] = 'theme';
		MLACoreOptions::$mla_option_definitions['default_style']['texts'][] = '&mdash; ' . __( 'Theme', 'media-library-assistant' ) . ' &mdash;';

		$templates = MLATemplate_Support::mla_get_style_templates( 'gallery' );
		ksort($templates);
		foreach ($templates as $key => $value ) {
			MLACoreOptions::$mla_option_definitions['default_style']['options'][] = $key;
			MLACoreOptions::$mla_option_definitions['default_style']['texts'][] = $key;
		}

		$templates = MLATemplate_Support::mla_get_markup_templates( 'gallery' );
		ksort($templates);
		foreach ($templates as $key => $value ) {
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

		// Start with any page-level options
		$options_list = '';
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'mla_gallery' == $value['tab'] ) {
				$options_list .= MLASettings::mla_compose_option_row( $key, $value );
			}
		}

		// WPML requires that lang be the first argument after page
		$view_arguments = MLA_Template_List_Table::mla_submenu_arguments();
		$form_language = isset( $view_arguments['lang'] ) ? '&lang=' . $view_arguments['lang'] : '';
		$form_arguments = '?page=mla-settings-menu-shortcodes' . $form_language . '&mla_tab=shortcodes';

		// We need to remember all the view arguments
		$view_args = '';
		foreach ( $view_arguments as $key => $value ) {
			/*
			 * Search box elements are already set up in the above "search-box"
			 * 'lang' has already been added to the form action attribute
			 */
			if ( in_array( $key, array( 's', 'lang' ) ) ) {
				continue;
			}

			if ( is_array( $value ) ) {
				foreach ( $value as $element_key => $element_value )
					$view_args .= "\t" . sprintf( '<input type="hidden" name="%1$s[%2$s]" value="%3$s" />', $key, $element_key, esc_attr( $element_value ) ) . "\n";
			} else {
				$view_args .= "\t" . sprintf( '<input type="hidden" name="%1$s" value="%2$s" />', $key, esc_attr( $value ) ) . "\n";
			}
		}

		$page_values = array(
			'MLA Shortcode Options' => __( 'MLA Shortcode Options', 'media-library-assistant' ),
			'In this tab' => __( 'In this tab you can view the default style and markup templates. You can also define additional templates and use the <code>mla_style</code> and <code>mla_markup</code> parameters to apply them in your [mla_gallery] shortcodes.', 'media-library-assistant' ),
			/* translators: 1: Documentation hyperlink */
			'You can find' => sprintf( __( 'You can find more information about shortcode templates and how MLA and WordPress use them in the %1$s section of the Documentation or by clicking the <strong>"Help"</strong> tab in the upper-right corner of this screen.', 'media-library-assistant' ), '<a href="[+settingsURL+]?page=mla-settings-menu-documentation&amp;mla_tab=documentation#mla_gallery_templates" title="' . __( 'Style and Markup Templates documentation', 'media-library-assistant' ) . '">' . __( 'Style and Markup Templates', 'media-library-assistant' ) . '</a>' ),
			'settingsURL' => admin_url('options-general.php'),
			'form_url' => admin_url( 'options-general.php' ) . $form_arguments,
			'view_args' => $view_args,
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'_wp_http_referer' => wp_referer_field( false ),
			'Add New Template' => __( 'Add New Template', 'media-library-assistant' ),
			'Search Templates' => __( 'Search Templates', 'media-library-assistant' ),
			's' => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
			'results' => ! empty( $_REQUEST['s'] ) ? '<span class="alignright" style="margin-top: .5em; font-weight: bold">' . __( 'Search results for', 'media-library-assistant' ) . ':&nbsp;</span>' : '',
			'options_list' => $options_list,
			'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
		);

		$page_content['body'] = MLAData::mla_parse_template( $page_template_array['before-table'], $page_values );

		//	 Now we can render the completed list table
		ob_start();
		$MLATemplateListTable->views();
		$MLATemplateListTable->display();
		$page_content['body'] .= ob_get_contents();
		ob_end_clean();

		$page_content['body'] .= MLAData::mla_parse_template( $page_template_array['after-table'], $page_values );

		return $page_content;
	}
} // class MLASettings_Shortcodes

/* 
 * The WP_List_Table class isn't automatically available to plugins
 */
if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class MLA (Media Library Assistant) Template List Table displays the
 * shortcode templates submenu table
 *
 * Extends the core WP_List_Table class.
 *
 * @package Media Library Assistant
 * @since 2.40
 */
class MLA_Template_List_Table extends WP_List_Table {
	/**
	 * Calls the parent constructor to set some default values.
	 *
	 * @since 2.40
	 *
	 * @return	void
	 */
	function __construct( ) {
		//Set parent defaults
		parent::__construct( array(
			'singular' => 'template', //singular name of the listed records
			'plural' => 'templates', //plural name of the listed records
			'ajax' => false, //does this table support ajax?
			'screen' => 'settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-shortcodes'
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
	 * All of the columns are added to this array by MLA_Template_List_Table::_localize_default_columns_array.
	 *
	 * @since 2.40
	 * @access private
	 * @var	array $default_columns {
	 * 	       @type string $$column_slug Column title.
	 * }
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
	 * @since 2.40
	 * @access private
	 * @var	array $default_hidden_columns {
	 * 	       @type string $$index Column slug.
	 * }
	 */
	private static $default_hidden_columns	= array(
		// 'name',
		// 'type',
		// 'shortcode',
		// 'description'
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
	 * @since 2.40
	 * @access private
	 * @var	array $default_sortable_columns {
	 *         @type array $$column_slug {
	 *                 @type string $orderby_name Database column or other sorting slug.
	 *                 @type boolean $descending Optional. True to make the initial orderby DESC.
	 *         }
	 * }
	 */
	private static $default_sortable_columns = array(
		'name' => array('name',false),
		'type' => array('type',false),
		'shortcode' => array('shortcode',false),
		'description' => array('description',false),
        );

	/**
	 * Access the default list of hidden columns
	 *
	 * @since 2.40
	 *
	 * @return	array	default list of hidden columns
	 */
	private static function _default_hidden_columns( ) {
		return self::$default_hidden_columns;
	}

	/**
	 * Return the names and orderby values of the sortable columns
	 *
	 * @since 2.40
	 *
	 * @return	array	column_slug => array( orderby value, initial_descending_sort ) for sortable columns
	 */
	public static function mla_get_sortable_columns( ) {
		return self::$default_sortable_columns;
	}

	/**
	 * Process $_REQUEST, building $submenu_arguments
	 *
	 * @since 2.40
	 *
	 * @param boolean $include_filters Optional. Include the "click filter" values in the results. Default true.
	 * @return array non-empty view, search, filter and sort arguments
	 */
	public static function mla_submenu_arguments( $include_filters = true ) {
		static $submenu_arguments = NULL, $has_filters = NULL;

		if ( is_array( $submenu_arguments ) && ( $has_filters == $include_filters ) ) {
			return $submenu_arguments;
		}

		$submenu_arguments = array();
		$has_filters = $include_filters;

		// View arguments
		if ( isset( $_REQUEST['mla_template_view'] ) ) {
			$submenu_arguments['mla_template_view'] = $_REQUEST['mla_template_view'];
		}

		// Search box arguments
		if ( !empty( $_REQUEST['s'] ) ) {
			$submenu_arguments['s'] = urlencode( stripslashes( $_REQUEST['s'] ) );
		}

		// Filter arguments (from table header)
		if ( isset( $_REQUEST['mla_template_status'] ) && ( 'any' != $_REQUEST['mla_template_status'] ) ) {
			$submenu_arguments['mla_template_status'] = $_REQUEST['mla_template_status'];
		}

		// Sort arguments (from column header)
		if ( isset( $_REQUEST['order'] ) ) {
			$submenu_arguments['order'] = $_REQUEST['order'];
		}

		if ( isset( $_REQUEST['orderby'] ) ) {
			$submenu_arguments['orderby'] = $_REQUEST['orderby'];
		}

		return $submenu_arguments;
	}

	/**
	 * Handler for filter 'get_user_option_managesettings_page_mla-settings-menu-shortcodescolumnshidden'
	 *
	 * Required because the screen.php get_hidden_columns function only uses
	 * the get_user_option result. Set when the file is loaded because the object
	 * is not created in time for the call from screen.php.
	 *
	 * @since 2.40
	 *
	 * @param	mixed	false or array with current list of hidden columns, if any
	 * @param	string	'managesettings_page_mla-settings-menu-shortcodescolumnshidden'
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
	 * @since 2.40
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
	 * @since 2.40
	 */
	private static function _localize_default_columns_array( ) {
		if ( empty( self::$default_columns ) ) {
			// Build the default columns array at runtime to accomodate calls to the localization functions
			self::$default_columns = array(
				'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
				'name' => _x( 'Name', 'list_table_column', 'media-library-assistant' ),
				'type' => _x( 'Type', 'list_table_column', 'media-library-assistant' ),
				'shortcode' => _x( 'Shortcode', 'list_table_column', 'media-library-assistant' ),
				'description' => _x( 'Description', 'list_table_column', 'media-library-assistant' ),
			);
		}
	}

	/**
	 * Print optional in-line styles for the Shortcodes submenu table
	 *
	 * @since 2.40
	 */
	public static function mla_admin_print_styles_action() {
		/*
		 * Suppress display of the hidden columns selection list (disabled),
		 * adjust width of the Type column
		 */
		echo "  <style type='text/css'>\r\n";
		//echo "    form#adv-settings div.metabox-prefs,\r\n";
		//echo "    form#adv-settings fieldset.metabox-prefs {\r\n";
		//echo "      display: none;\r\n";
		//echo "    }\r\n\r\n";
		echo "    table.template_plugins th.column-type,\r\n";
		echo "    table.template_plugins td.column-type {\r\n";
		echo "      width: 8em;\r\n";
		echo "    }\r\n";
		echo "  </style>\r\n";
	}

	/**
	 * Called in the admin_init action because the list_table object isn't
	 * created in time to affect the "screen options" setup.
	 *
	 * @since 2.40
	 *
	 * @return	void
	 */
	public static function mla_admin_init( ) {
		if ( isset( $_REQUEST['mla_tab'] ) && $_REQUEST['mla_tab'] == 'shortcodes' ) {
			add_filter( 'get_user_option_managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-shortcodescolumnshidden', 'MLA_Template_List_Table::mla_manage_hidden_columns_filter', 10, 3 );
			add_filter( 'manage_settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-shortcodes_columns', 'MLA_Template_List_Table::mla_manage_columns_filter', 10, 0 );
		}
	}

	/**
	 * Get the name of the default primary column.
	 *
	 * @since 2.40
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
	 * @since 2.40
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
	 * @since 2.40
	 * 
	 * @param	object	An MLA shortcode_template object
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
	 * @since 2.40
	 * 
	 * @param	object	An MLA shortcode_template object
	 * @param	string	Current column name
	 *
	 * @return	array	Names and URLs of row-level actions
	 */
	private function _build_rollover_actions( $item, $column ) {
		$actions = array();

		/*
		 * Compose view arguments
		 */

		$view_args = array_merge( array(
			'page' => MLACoreOptions::MLA_SETTINGS_SLUG . '-shortcodes',
			'mla_tab' => 'shortcodes',
			'mla_item_ID' => urlencode( $item->post_ID )
		), MLA_Template_List_Table::mla_submenu_arguments() );

		if ( isset( $_REQUEST['paged'] ) ) {
			$view_args['paged'] = $_REQUEST['paged'];
		}

		if ( $item->default ) {
			$actions['view'] = '<a href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_EDIT_DISPLAY, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'View this item', 'media-library-assistant' ) . '">' . __( 'View', 'media-library-assistant' ) . '</a>';
		} else {
			$actions['edit'] = '<a href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_EDIT_DISPLAY, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Edit this item', 'media-library-assistant' ) . '">' . __( 'Edit', 'media-library-assistant' ) . '</a>';
		}
		
		$actions['copy'] = '<a href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_COPY, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Make a copy', 'media-library-assistant' ) . '">' . __( 'Copy', 'media-library-assistant' ) . '</a>';

		if ( ! $item->default ) {
			$actions['delete'] = '<a class="delete-tag" href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_DELETE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Delete this item Permanently', 'media-library-assistant' ) . '">' . __( 'Delete Permanently', 'media-library-assistant' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * Supply the content for the Name column
	 *
	 * @since 2.40
	 * 
	 * @param	object	An MLA shortcode_template object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_name( $item ) {
		$row_actions = self::_build_rollover_actions( $item, 'name' );
		$slug = esc_attr( $item->name );
		$default = $item->default ? '<br>(' . __( 'default', 'media-library-assistant' ) . ')' : '';
		return sprintf( '%1$s%2$s<br>%3$s', $slug, $default, $this->row_actions( $row_actions ) );
	}

	/**
	 * Supply the content for the Type column
	 *
	 * @since 2.40
	 * 
	 * @param	object	An MLA shortcode_template object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_type( $item ) {
		return esc_attr( 'style' === $item->type ? _x( 'Style', 'table_view_singular', 'media_library-assistant' ) : _x( 'Markup', 'table_view_singular', 'media_library-assistant' ) );
	}

	/**
	 * Supply the content for the Shortcode column
	 *
	 * @since 2.40
	 * 
	 * @param	object	An MLA shortcode_template object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_shortcode( $item ) {
		return esc_attr( MLATemplate_Support::$mla_template_definitions[ $item->type ][ $item->shortcode ]['label'] );
	}

	/**
	 * Supply the content for the Description column
	 *
	 * @since 2.40
	 * 
	 * @param	object	An MLA shortcode_template object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_description( $item ) {
		if ( isset( $item->sections['description'] ) ) {
			return esc_attr( $item->sections['description'] );
		}

		return '';
	}

	/**
	 * Display the pagination, adding view, search and filter arguments
	 *
	 * @since 2.40
	 * 
	 * @param	string	'top' | 'bottom'
	 */
	function pagination( $which ) {
		$save_uri = $_SERVER['REQUEST_URI'];
		$_SERVER['REQUEST_URI'] = add_query_arg( MLA_Template_List_Table::mla_submenu_arguments(), $save_uri );
		parent::pagination( $which );
		$_SERVER['REQUEST_URI'] = $save_uri;
	}

	/**
	 * This method dictates the table's columns and titles
	 *
	 * @since 2.40
	 * 
	 * @return	array	Column information: 'slugs'=>'Visible Titles'
	 */
	function get_columns( ) {
		return $columns = MLA_Template_List_Table::mla_manage_columns_filter();
	}

	/**
	 * Returns the list of currently hidden columns from a user option or
	 * from default values if the option is not set
	 *
	 * @since 2.40
	 * 
	 * @return	array	Column information,e.g., array(0 => 'ID_parent, 1 => 'title_name')
	 */
	function get_hidden_columns( ) {
		$columns = get_user_option( 'managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-shortcodescolumnshidden' );

		if ( is_array( $columns ) ) {
			return $columns;
		}

		return self::_default_hidden_columns();
	}

	/**
	 * Returns an array where the  key is the column that needs to be sortable
	 * and the value is db column to sort by.
	 *
	 * @since 2.40
	 * 
	 * @return	array	Sortable column information,e.g.,
	 * 					'slugs'=>array('data_values',boolean)
	 */
	function get_sortable_columns( ) {
		return self::$default_sortable_columns;
	}

	/**
	 * Print column headers, adding view, search and filter arguments
	 *
	 * @since 2.40
	 *
	 * @param bool $with_id Whether to set the id attribute or not
	 */
	function print_column_headers( $with_id = true ) {
		$save_uri = $_SERVER['REQUEST_URI'];
		$_SERVER['REQUEST_URI'] = add_query_arg( MLA_Template_List_Table::mla_submenu_arguments(), $save_uri );
		parent::print_column_headers( $with_id );
		$_SERVER['REQUEST_URI'] = $save_uri;
	}

	/**
	 * Returns HTML markup for one view that can be used with this table
	 *
	 * @since 2.40
	 *
	 * @param	string	View slug
	 * @param	array	count and labels for the View
	 * @param	string	Slug for current view 
	 * 
	 * @return	string | false	HTML for link to display the view, false if count = zero
	 */
	function _get_view( $view_slug, $template_item, $current_view ) {
		static $base_url = NULL;

		$class = ( $view_slug == $current_view ) ? ' class="current"' : '';

		/*
		 * Calculate the common values once per page load
		 */
		if ( is_null( $base_url ) ) {
			/*
			 * Remember the view filters
			 */
			$base_url = wp_nonce_url( 'options-general.php?page=' . MLACoreOptions::MLA_SETTINGS_SLUG . '-shortcodes&mla_tab=shortcodes', MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

			if ( isset( $_REQUEST['s'] ) ) {
				$base_url = add_query_arg( array( 's' => $_REQUEST['s'] ), $base_url );
			}
		}

		$singular = sprintf('%s <span class="count">(%%s)</span>', $template_item['singular'] );
		$plural = sprintf('%s <span class="count">(%%s)</span>', $template_item['plural'] );
		$nooped_plural = _n_noop( $singular, $plural, 'media-library-assistant' );
		return "<a href='" . add_query_arg( array( 'mla_template_view' => $view_slug ), $base_url )
			. "'$class>" . sprintf( translate_nooped_plural( $nooped_plural, $template_item['count'], 'media-library-assistant' ), number_format_i18n( $template_item['count'] ) ) . '</a>';
	} // _get_view

	/**
	 * Returns an associative array listing all the views that can be used with this table.
	 * These are listed across the top of the page and managed by WordPress.
	 *
	 * @since 2.40
	 * 
	 * @return	array	View information,e.g., array ( id => link )
	 */
	function get_views( ) {
		/*
		 * Find current view
		 */
		$current_view = isset( $_REQUEST['mla_template_view'] ) ? $_REQUEST['mla_template_view'] : 'all';

		/*
		 * Generate the list of views, retaining keyword search criterion
		 */
		//$s = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';
		$s = '';
		$template_items = MLA_Template_Query::mla_tabulate_template_items( $s );
		$view_links = array();
		foreach ( $template_items as $slug => $item )
			$view_links[ $slug ] = self::_get_view( $slug, $item, $current_view );

		return $view_links;
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 2.40
	 * 
	 * @return	array	Contains all the bulk actions: 'slugs'=>'Visible Titles'
	 */
	function get_bulk_actions( ) {
		return array(
			'delete' => __( 'Delete', 'media-library-assistant' ),
			'copy' => __( 'Copy', 'media-library-assistant' ),
		);
	}

	/**
	 * Get dropdown box of template status values, i.e., Default/Custom.
	 *
	 * @since 2.40
	 *
	 * @param string $selected Optional. Currently selected status. Default 'any'.
	 * @return string HTML markup for dropdown box.
	 */
	public static function mla_get_template_status_dropdown( $selected = 'any' ) {
		$dropdown  = '<select name="mla_template_status" class="postform" id="name">' . "\n";

		$selected_attribute = ( $selected == 'any' ) ? ' selected="selected"' : '';
		$dropdown .= "\t" . sprintf( '<option value="any"%1$s>%2$s</option>', $selected_attribute, _wp_specialchars( __( 'Any Status', 'media-library-assistant' ) ) ) . "\n";

		$selected_attribute = ( $selected == 'default' ) ? ' selected="selected"' : '';
		$dropdown .= "\t" . sprintf( '<option value="default"%1$s>%2$s</option>', $selected_attribute, _wp_specialchars( __( 'Default', 'media-library-assistant' ) ) ) . "\n";

		$selected_attribute = ( $selected == 'custom' ) ? ' selected="selected"' : '';
		$dropdown .= "\t" . sprintf( '<option value="custom"%1$s>%2$s</option>', $selected_attribute, _wp_specialchars( __( 'Custom', 'media-library-assistant' ) ) ) . "\n";

		$dropdown .= '</select>';

		return $dropdown;
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * Modeled after class-wp-posts-list-table.php in wp-admin/includes.
	 *
	 * @since 2.40
	 * 
	 * @param	string	'top' or 'bottom', i.e., above or below the table rows
	 *
	 * @return	void
	 */
	function extra_tablenav( $which ) {
		/*
		 * Decide which actions to show
		 */
		if ( 'top' == $which ) {
			$actions = array( 'mla_template_status', 'mla_filter' );
		} else {
			$actions = array();
		}

		if ( empty( $actions ) ) {
			return;
		}

		echo ( '<div class="alignleft actions">' );

		foreach ( $actions as $action ) {
			switch ( $action ) {
				case 'mla_template_status':
					echo self::mla_get_template_status_dropdown( isset( $_REQUEST['mla_template_status'] ) ? $_REQUEST['mla_template_status'] : 'any' );
					break;
				case 'mla_filter':
					submit_button( __( 'Filter', 'media-library-assistant' ), 'secondary', 'mla_filter', false, array( 'id' => 'template-query-submit' ) );
					break;
				default:
					// ignore anything else
			}
		}

		echo ( '</div>' );
	}

	/**
	 * Prepares the list of items for displaying
	 *
	 * This is where you prepare your data for display. This method will usually
	 * be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args().
	 *
	 * @since 2.40
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
		$total_items = MLA_Template_Query::mla_count_template_items( $_REQUEST );
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
		$this->items = MLA_Template_Query::mla_query_template_items( $_REQUEST, ( ( $current_page - 1 ) * $per_page ), $per_page );
	}

	/**
	 * Generates (echoes) content for a single row of the table
	 *
	 * @since 2.40
	 *
	 * @param object the current item
	 *
	 * @return void Echoes the row HTML
	 */
	function single_row( $item ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		echo '<tr id="template-' . $item->post_ID . '"' . $row_class . '>';
		echo parent::single_row_columns( $item );
		echo '</tr>';
	}
} // class MLA_Template_List_Table

/**
 * Class MLA (Media Library Assistant) Template Query implements the
 * searchable database of shortcode templates
 *
 * @package Media Library Assistant
 * @since 2.40
 */
class MLA_Template_Query {

	/**
	 * Callback to sort array by a 'name' key.
	 *
	 * @since 2.40
	 *
	 * @param array $a The first array.
	 * @param array $b The second array.
	 * @return integer The comparison result.
	 */
	private static function _sort_uname_callback( $a, $b ) {
		return strnatcasecmp( $a['name'], $b['name'] );
	}

	/**
	 * In-memory representation of the template items
	 *
	 * @since 2.40
	 *
	 * @var array $_shortcode_template_items {
	 *         Items by ID. Key $$ID is an index number starting with 1.
	 *
	 *         @type array $$ID {
	 *             Template elements.
	 *
	 *             @type integer $post_ID Template ID; equal to $$ID.
	 *             @type string $type Template type; style or markup.
	 *             @type string $shortcode Shortcode slug this template applies to.
	 *             @type boolean $default True if a default template, false if a custom template.
	 *             @type string $name Template name/slug.
	 *             @type array $sections {
	 *                 Template content by section. Key $$section_name is the section name/slug.
	 *
	 *                 @type string $$section_name HTML markup/CSS styles for the template section.
	 *             }
	 *             @type boolean $changed True if the template has changed since loading.
	 *             @type boolean $deleted True if the template has been deleted since loading.
	 * @var	array	ID => ( post_ID, type, shortcode, default, name, description, changed, deleted )
	 */
	private static $_shortcode_template_items = NULL;

	/**
	 * Highest existing template ID value
	 *
	 * @since 2.40
	 *
	 * @var	integer
	 */
	private static $_shortcode_template_highest_ID = 0;

	/**
	 * Assemble the in-memory representation of the templates items
	 *
	 * @since 2.40
	 *
	 * @param boolean $force_refresh Optional. Force a reload of items. Default false.
	 * @return boolean Success (true) or failure (false) of the operation
	 */
	private static function _get_shortcode_template_items( $force_refresh = false ) {
		if ( false == $force_refresh && NULL != self::$_shortcode_template_items ) {
			return true;
		}

		$template_items = array();
		foreach( MLATemplate_Support::$mla_template_definitions['style'] as $shortcode => $definition ) {
			$templates = MLATemplate_Support::mla_get_style_templates( $shortcode );
			foreach( $templates as $template_name => $template ) {
				$template_items[] = array(
					'post_ID' => 0,
					'type' => 'style',
					'shortcode' => $shortcode,
					'default' => in_array( $template_name, $definition['default_names'] ),
					'name' => $template_name,
					'sections' => $template,
					'changed' => false,
					'deleted' => false,
				);
			}
		}

		foreach( MLATemplate_Support::$mla_template_definitions['markup'] as $shortcode => $definition ) {
			$templates = MLATemplate_Support::mla_get_markup_templates( $shortcode );
			foreach( $templates as $template_name => $template ) {
				$template_items[] = array(
					'post_ID' => 0,
					'type' => 'markup',
					'shortcode' => $shortcode,
					'default' => in_array( $template_name, $definition['default_names'] ),
					'name' => $template_name,
					'sections' => $template,
					'changed' => false,
					'deleted' => false,
				);
			}
		}

		uasort( $template_items, 'MLA_Template_Query::_sort_uname_callback' );
		self::$_shortcode_template_items = array();
		self::$_shortcode_template_highest_ID = 0;

		// Load and number the entries
		foreach ( $template_items as $value ) {
			$ID = ++self::$_shortcode_template_highest_ID;
			$value['post_ID'] = $ID;
			self::$_shortcode_template_items[ $ID ] = $value;
		}

		return true;
	}

	/**
	 * Flush the in-memory representation of the templates items to option values
	 *
	 * @since 2.40
	 */
	public static function mla_put_shortcode_template_items() {
		$style_templates = array();
		$style_changed = false;
		$markup_templates = array();
		$markup_changed = false;

		if ( NULL === self::$_shortcode_template_items ) {
			return;
		}

		foreach( self::$_shortcode_template_items as $ID => $value ) {
			if ( $value['default'] ) {
				continue;
			}

			$new_template = $value['sections'];

			if ( 'style' === $value['type'] ) {
				if ( $value['deleted'] ) {
					$style_changed = true;
					continue;
				}

				// Encode shortcode assignment in template content
				$new_template['styles'] = sprintf( "<!-- mla_shortcode_slug=\"%1\$s\" -->\r\n%2\$s", $value['shortcode'], $new_template['styles'] );
				$style_templates[ $value['name'] ] = $new_template;
				$style_changed |= $value['changed'];
			} else {
				if ( $value['deleted'] ) {
					$markup_changed = true;
					continue;
				}

				// Encode shortcode assignment in template content
				if ( isset( $new_template['arguments'] ) ) {
					$new_template['arguments'] = sprintf( "mla_shortcode_slug=\"%1\$s\"\r\n%2\$s", $value['shortcode'], $new_template['arguments'] );
				} else {
					$new_template['arguments'] = sprintf( "mla_shortcode_slug=\"%1\$s\"\r\n", $value['shortcode'] );
				}

				$markup_templates[ $value['name'] ] = $new_template;
				$markup_changed |= $value['changed'];
			}
		}

		if ( $style_changed ) {
			$results = MLATemplate_Support::mla_put_style_templates( $style_templates );
		}

		if ( $markup_changed ) {
			$results = MLATemplate_Support::mla_put_markup_templates( $markup_templates );
		}

		if ( $style_changed || $markup_changed ) {
			self::_get_shortcode_template_items( true );
		}
	}

	/**
	 * Sanitize and expand query arguments from request variables
	 *
	 * @since 2.40
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		Optional number of rows (default 0) to skip over to reach desired page
	 * @param	int		Optional number of rows on each page (0 = all rows, default)
	 *
	 * @return	array	revised arguments suitable for query
	 */
	private static function _prepare_template_items_query( $raw_request, $offset = 0, $count = 0 ) {
		/*
		 * Go through the $raw_request, take only the arguments that are used in the query and
		 * sanitize or validate them.
		 */
		if ( ! is_array( $raw_request ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			MLACore::mla_debug_add( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLA_Template_List_Table::_prepare_template_items_query', var_export( $raw_request, true ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			return NULL;
		}

		$clean_request = array (
			'mla_template_view' => 'all',
			'mla_template_status' => 'any',
			'orderby' => 'name',
			'order' => 'ASC',
			's' => ''
		);

		foreach ( $raw_request as $key => $value ) {
			switch ( $key ) {
				case 'mla_template_view':
				case 'mla_template_status':
					$clean_request[ $key ] = $value;
					break;
				case 'orderby':
					if ( 'none' == $value ) {
						$clean_request[ $key ] = $value;
					} else {
						if ( array_key_exists( $value, MLA_Template_List_Table::mla_get_sortable_columns() ) ) {
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
	 * @since 2.40
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 *
	 * @return	array	query results; array of MLA post_mime_type objects
	 */
	private static function _execute_template_items_query( $request ) {
		if ( ! self::_get_shortcode_template_items() ) {
			return array ();
		}

		/*
		 * Sort and filter the list
		 */
		$keywords = isset( $request['s'] ) ? $request['s'] : '';
		preg_match_all('/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $keywords, $matches);
		$keywords = array_map( 'MLAQuery::mla_search_terms_tidy', $matches[0]);
		$view = isset( $request['mla_template_view'] ) ? $request['mla_template_view'] : 'all';
		$status = isset( $request['mla_template_status'] ) ? $request['mla_template_status'] : 'any';
		$index = 0;
		$sorted_items = array();

		foreach ( self::$_shortcode_template_items as $ID => $value ) {
			if ( ! empty( $keywords ) ) {
				$found = false;
				foreach ( $keywords as $keyword ) {
					$found |= false !== stripos( $value['name'], $keyword );
					if ( isset( $value['sections']['description'] ) ) {
						$found |= false !== stripos( $value['sections']['description'], $keyword );
					}
					// TODO: Content search
				}

				if ( ! $found ) {
					continue;
				}
			}

			switch( $view ) {
				case 'style':
					$found = 'style' === $value['type'];
					break;
				case 'markup':
					$found = 'markup' === $value['type'];
					break;
				case 'gallery':
					$found = 'gallery' === $value['shortcode'];
					break;
				case 'tag-cloud':
					$found = 'tag-cloud' === $value['shortcode'];
					break;
				case 'term-list':
					$found = 'term-list' === $value['shortcode'];
					break;
				default:
					$found = true;
			}// $view

			if ( ! $found ) {
				continue;
			}

			switch( $status ) {
				case 'default':
					$found = $value['default'];
					break;
				case 'custom':
					$found = ! $value['default'];
					break;
				default:
					$found = true;
			}// $view

			if ( ! $found ) {
				continue;
			}

			switch ( $request['orderby'] ) {
				case 'name':
					$sorted_items[ ( empty( $value['name'] ) ? chr(1) : $value['name'] ) . $ID ] = (object) $value;
					break;
				case 'type':
					$sorted_items[ ( empty( $value['type'] ) ? chr(1) : $value['type'] ) . $ID ] = (object) $value;
					break;
				case 'shortcode':
					$sorted_items[ ( empty( $value['shortcode'] ) ? chr(1) : $value['shortcode'] ) . $ID ] = (object) $value;
					break;
				case 'description':
					$sorted_items[ ( empty( $value['sections']['description'] ) ? chr(1) : $value['sections']['description'] ) . $ID ] = (object) $value;
					break;
				default:
					$sorted_items[ $slug ] = (object) $value;
					break;
			} //orderby
		}
		ksort( $sorted_items );

		if ( 'DESC' == $request['order'] ) {
			$sorted_items = array_reverse( $sorted_items, true );
		}

		/*
		 * Paginate the sorted list
		 */
		$results = array();
		$offset = isset( $request['offset'] ) ? $request['offset'] : 0;
		$count = isset( $request['posts_per_page'] ) ? $request['posts_per_page'] : -1;
		foreach ( $sorted_items as $value ) {
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
	 * Get the total number of MLA shortcode_template objects
	 *
	 * @since 2.40
	 *
	 * @param	array	Query variables, e.g., from $_REQUEST
	 *
	 * @return	integer	Number of MLA shortcode_template objects
	 */
	public static function mla_count_template_items( $request ) {
		$request = self::_prepare_template_items_query( $request );
		$results = self::_execute_template_items_query( $request );
		return count( $results );
	}

	/**
	 * Retrieve MLA shortcode_template objects for list table display
	 *
	 * @since 2.40
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		number of rows to skip over to reach desired page
	 * @param	int		number of rows on each page
	 *
	 * @return	array	MLA shortcode_template objects
	 */
	public static function mla_query_template_items( $request, $offset, $count ) {
		$request = self::_prepare_template_items_query( $request, $offset, $count );
		$results = self::_execute_template_items_query( $request );
		return $results;
	}

	/**
	 * Find a Shortcode Template ID given its type and name
	 *
	 * @since 2.40
 	 *
	 * @param string $type MLA Shortcode Template type; style or markup.
	 * @param string $name MLA Shortcode Template name.
	 * @return integer Template ID if the template exists else zero (0).
	 */
	public static function mla_find_shortcode_template_ID( $type, $name ) {
		if ( ! self::_get_shortcode_template_items() ) {
			return false;
		}

		foreach( self::$_shortcode_template_items as $ID => $template ) {
			if ( $type == $template['type'] && $name == $template['name'] ) {
				return $ID;
			}
		}

		return 0;
	}

	/**
	 * Find an Shortcode Template given its ID
	 *
	 * @since 2.40
 	 *
	 * @param	integer	$ID MLA Shortcode Template ID
 	 *
	 * @return	array	MLA shortcode_template array
	 * @return	boolean	false; MLA shortcode_template does not exist
	 */
	public static function mla_find_shortcode_template( $ID ) {
		if ( ! self::_get_shortcode_template_items() ) {
			return false;
		}

		if ( isset( self::$_shortcode_template_items[ $ID ] ) ) {
			return self::$_shortcode_template_items[ $ID ];
		}

		return false;
	}

	/**
	 * Update a Shortcode Template field given its ID and key.
	 *
	 * @since 2.40
 	 *
	 * @param integer $ID MLA Shortcode Template ID.
	 * @param string $key MLA Shortcode Template property.
	 * @param string $value MLA Shortcode Template new value.
	 * @return boolean true if object exists else false.
	 */
	public static function mla_update_shortcode_template( $ID, $key, $value ) {
		if ( ! self::_get_shortcode_template_items() ) {
			return false;
		}

		if ( isset( self::$_shortcode_template_items[ $ID ] ) ) {
			self::$_shortcode_template_items[ $ID ][ $key ] = $value;
			return true;
		}

		return false;
	}

	/**
	 * Replace a Shortcode Template given its value array.
	 *
	 * @since 2.40
 	 *
	 * @param array $value MLA Shortcode Template new value.
	 * @return boolean true if object exists else false.
	 */
	public static function mla_replace_shortcode_template( $value ) {
		if ( ! self::_get_shortcode_template_items() ) {
			return false;
		}

		if ( isset( self::$_shortcode_template_items[ $value['post_ID'] ] ) ) {
			self::$_shortcode_template_items[ $value['post_ID'] ] = $value;
			return true;
		}

		return false;
	}

	/**
	 * Insert a Shortcode Template given its value array.
	 *
	 * @since 2.40
 	 *
	 * @param array $value MLA Shortcode Template new value.
	 */
	public static function mla_add_shortcode_template( $value ) {
		if ( ! self::_get_shortcode_template_items() ) {
			return false;
		}

		$value['post_ID'] = ++self::$_shortcode_template_highest_ID;
		$value['default'] = false;
		$value['changed'] = true;
		$value['deleted'] = false;

		self::$_shortcode_template_items[ $value['post_ID'] ] = $value;
	}

	/**
	 * Tabulate MLA shortcode_template objects by view for list table display
	 *
	 * @since 2.40
	 *
	 * @param	string	keyword search criterion, optional
	 *
	 * @return	array	( 'singular' label, 'plural' label, 'count' of items )
	 */
	public static function mla_tabulate_template_items( $s = '' ) {
		if ( empty( $s ) ) {
			$request = array( 'mla_template_view' => 'all' );
		} else {
			$request = array( 's' => $s );
		}

		$items = self::mla_query_template_items( $request, 0, 0 );

		$template_items = array(
			'all' => array(
				'singular' => _x( 'All', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'All', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'style' => array(
				'singular' => _x( 'Style', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Style', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'markup' => array(
				'singular' => _x( 'Markup', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Markup', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'gallery' => array(
				'singular' => _x( 'Gallery', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Gallery', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'tag-cloud' => array(
				'singular' => _x( 'Tag Cloud', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Tag Cloud', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'term-list' => array(
				'singular' => _x( 'Term List', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Term List', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
		);

		foreach ( $items as $value ) {
			$template_items['all']['count']++;

			switch ( $value->type ) {
				case 'style':
					$template_items[ 'style' ]['count']++;
					break;
				case 'markup':
					$template_items[ 'markup' ]['count']++;
					break;
				default:
					break;
			}

			switch ( $value->shortcode ) {
				case 'gallery':
					$template_items[ 'gallery' ]['count']++;
					break;
				case 'tag-cloud':
					$template_items[ 'tag-cloud' ]['count']++;
					break;
				case 'term-list':
					$template_items[ 'term-list' ]['count']++;
					break;
				default:
					break;
			}
		}

		return $template_items;
	}
} // class MLA_Template_Query

/*
 * Actions are added here, when the source file is loaded, because the MLA_Template_List_Table
 * object is created too late to be useful.
 */
add_action( 'admin_enqueue_scripts', 'MLASettings_Shortcodes::mla_admin_enqueue_scripts' );
add_action( 'admin_init', 'MLA_Template_List_Table::mla_admin_init' );
?>