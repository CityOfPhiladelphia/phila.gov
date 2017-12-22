<?php
/**
 * Manages the Settings/Media Library Assistant Custom Fields tab
 *
 * @package Media Library Assistant
 * @since 2.40
 */

/**
 * Class MLA (Media Library Assistant) Settings Custom Fields implements the
 * Settings/Media Library Assistant Custom Fields tab
 *
 * @package Media Library Assistant
 * @since 2.40
 */
class MLASettings_CustomFields {
	/**
	 * Object name for localizing JavaScript - MLA Custom Fields List Table
	 *
	 * @since 2.50
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_EDIT_CUSTOM_OBJECT = 'mla_inline_edit_settings_vars';

	/**
	 * Load the tab's Javascript files
	 *
	 * @since 2.40
	 *
	 * @param string $page_hook Name of the page being loaded
	 */
	public static function mla_admin_enqueue_scripts( $page_hook ) {
		global $wpdb,  $wp_locale;

		// Without a tab value that matches ours, there's nothing to do
		if ( empty( $_REQUEST['mla_tab'] ) || 'custom_field' !== $_REQUEST['mla_tab'] ) {
			return;
		}

		if ( $wp_locale->is_rtl() ) {
			wp_register_style( MLACore::STYLESHEET_SLUG, MLA_PLUGIN_URL . 'css/mla-style-rtl.css', false, MLACore::CURRENT_MLA_VERSION );
		} else {
			wp_register_style( MLACore::STYLESHEET_SLUG, MLA_PLUGIN_URL . 'css/mla-style.css', false, MLACore::CURRENT_MLA_VERSION );
		}

		wp_enqueue_style( MLACore::STYLESHEET_SLUG );

		$use_spinner_class = version_compare( get_bloginfo( 'version' ), '4.2', '>=' );
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		/*
		 * Initialize variables for mapping scripts
		 */
		$script_variables = array(
			'error' => __( 'Error while making the changes.', 'media-library-assistant' ),
			'ntdeltitle' => __( 'Remove From Bulk Edit', 'media-library-assistant' ),
			'notitle' => '(' . __( 'no slug', 'media-library-assistant' ) . ')',
			'comma' => _x( ',', 'tag_delimiter', 'media-library-assistant' ),
			'useSpinnerClass' => $use_spinner_class,
			'ajax_nonce' => wp_create_nonce( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ),
			'bulkChunkSize' => MLACore::mla_get_option( MLACoreOptions::MLA_BULK_CHUNK_SIZE ),
			'bulkWaiting' => __( 'Waiting', 'media-library-assistant' ),
			'bulkRunning' => __( 'Running', 'media-library-assistant' ),
			'bulkComplete' => __( 'Complete', 'media-library-assistant' ),
			'bulkUnchanged' => __( 'Unchanged', 'media-library-assistant' ),
			'bulkSuccess' => __( 'Succeeded', 'media-library-assistant' ),
			'bulkFailure' => __( 'Failed', 'media-library-assistant' ),
			'bulkSkip' => __( 'Skipped', 'media-library-assistant' ),
			'bulkRedone' => __( 'Reprocessed', 'media-library-assistant' ),
			'bulkPaused' => __( 'PAUSED', 'media-library-assistant' ),
			'page' => 'mla-settings-menu-custom_field',
			'mla_tab' => 'custom_field',
			'screen' => 'settings_page_mla-settings-menu-custom_field',
			'ajax_action' => MLASettings::JAVASCRIPT_INLINE_MAPPING_CUSTOM_SLUG,
			'fieldsId' => '#mla-display-settings-custom-field-tab',
			'totalItems' => $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE `post_type` = 'attachment'" )
		);

		wp_enqueue_script( MLASettings::JAVASCRIPT_INLINE_MAPPING_CUSTOM_SLUG,
			MLA_PLUGIN_URL . "js/mla-inline-mapping-scripts{$suffix}.js", 
			array( 'jquery' ), MLACore::CURRENT_MLA_VERSION, false );

		wp_localize_script( MLASettings::JAVASCRIPT_INLINE_MAPPING_CUSTOM_SLUG,
			MLASettings::JAVASCRIPT_INLINE_MAPPING_OBJECT, $script_variables );
			
		/*
		 * Initialize variables for inline edit scripts
		 */
		$script_variables = array(
			'error' => __( 'Error while making the changes.', 'media-library-assistant' ),
			'ntdeltitle' => __( 'Remove From Bulk Edit', 'media-library-assistant' ),
			'notitle' => '(' . __( 'no slug', 'media-library-assistant' ) . ')',
			'comma' => _x( ',', 'tag_delimiter', 'media-library-assistant' ),
			'useSpinnerClass' => $use_spinner_class,
			'ajax_nonce' => wp_create_nonce( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ),
			'tab' => 'custom_field',
			'fields' => array( 'name', 'rule_name', 'data_source', 'meta_name', 'format', 'option', 'keep_existing', 'active' ),
			'checkboxes' => array( 'no_null', 'mla_column', 'quick_edit', 'bulk_edit' ),
			'ajax_action' => MLASettings::JAVASCRIPT_INLINE_EDIT_CUSTOM_SLUG,
		);

		wp_enqueue_script( MLASettings::JAVASCRIPT_INLINE_EDIT_CUSTOM_SLUG,
			MLA_PLUGIN_URL . "js/mla-inline-edit-settings-scripts{$suffix}.js", 
			array( 'wp-lists', 'suggest', 'jquery' ), MLACore::CURRENT_MLA_VERSION, false );

		wp_localize_script( MLASettings::JAVASCRIPT_INLINE_EDIT_CUSTOM_SLUG,
			self::JAVASCRIPT_INLINE_EDIT_CUSTOM_OBJECT, $script_variables );
	}

	/**
	 * Save custom field settings to the options table
 	 *
	 * @since 1.10
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _save_custom_field_settings() {
		$message_list = '';
		$option_messages = '';
		$changed = false;

		// See if the entire tab is disabled
		if ( ! isset( $_REQUEST[ MLA_OPTION_PREFIX . MLACoreOptions::MLA_ALLOW_CUSTOM_FIELD_MAPPING ] ) ) {
			unset( $_REQUEST[ MLA_OPTION_PREFIX . 'enable_custom_field_mapping' ] );
			unset( $_REQUEST[ MLA_OPTION_PREFIX . 'enable_custom_field_update' ] );
		}

		// Process any page-level options
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'custom_field' == $value['tab'] ) {
				$old_value = MLACore::mla_get_option( $key );
				$option_messages .= MLASettings::mla_update_option_row( $key, $value );
				$changed |= $old_value !== MLACore::mla_get_option( $key );
			}
		}

		// Uncomment this for debugging.
		//$message_list = $option_messages . '<br>';
			
		if ( $changed ) {
			$message_list .= __( 'Custom field mapping settings updated.', 'media-library-assistant' ) . "\r\n";
		} else {
			$message_list .= __( 'Custom field no mapping changes detected.', 'media-library-assistant' ) . "\r\n";
		}

		return array( 'message' => $message_list, 'body' => '' );
	} // _save_custom_field_settings

	/**
	 * Process custom field rules against all image attachments
 	 *
	 * @since 2.50
	 *
	 * @param array | NULL	specific custom_field_mapping values 
	 * @param integer			offset for chunk mapping 
	 * @param integer			length for chunk mapping
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _process_custom_field_mapping( $settings = NULL, $offset = 0, $length = 0 ) {
		global $wpdb;

		if ( NULL == $settings ) {
			$source = 'custom_fields';
			$settings = MLACore::mla_get_option( 'custom_field_mapping' );
		} else {
			$source = 'custom_rule';
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
	 * Add a custom field rule from values in $_REQUEST
 	 *
	 * @since 2.50
	 * @uses $_REQUEST for field-level values
	 *
	 * @return string Message(s) reflecting the results of the operation
	 */
	private static function _add_custom_field_rule() {
		$mla_custom_field = isset( $_REQUEST['mla_custom_field'] ) ? $_REQUEST['mla_custom_field'] : array();

		// Validate new rule name
		if ( !empty( $mla_custom_field['new_field'] ) ) {
			$new_name = $mla_custom_field['new_field'];
		} elseif ( !empty( $mla_custom_field['new_name'] ) && ( 'none' !== $mla_custom_field['new_name'] ) ) {
			$new_name = $mla_custom_field['new_name'];
		} else {
			return __( 'ERROR', 'media-library-assistant' ) . __( ': No custom field name selected/entered', 'media-library-assistant' );
		}

		if ( MLA_Custom_Field_Query::mla_find_custom_field_rule_ID( $new_name ) ) {
			return __( 'ERROR', 'media-library-assistant' ) . __( ': Rule already exists for the new name', 'media-library-assistant' );
		}

		// Convert checkbox/dropdown controls to booleans
		$mla_custom_field['mla_column'] = isset( $mla_custom_field['mla_column'] );
		$mla_custom_field['quick_edit'] = isset( $mla_custom_field['quick_edit'] );
		$mla_custom_field['bulk_edit'] = isset( $mla_custom_field['bulk_edit'] );
		$mla_custom_field['keep_existing'] = '1' === $mla_custom_field['keep_existing'];
		$mla_custom_field['no_null'] = isset( $mla_custom_field['no_null'] );
		$mla_custom_field['active'] = '1' === $mla_custom_field['status'];

		$new_rule = array(
			'post_ID' => 0,
			'rule_name' => $new_name,
			'name' => $new_name,
			'data_source' => $mla_custom_field['data_source'],
			'meta_name' => $mla_custom_field['meta_name'],
			'format' => $mla_custom_field['format'],
			'option' => $mla_custom_field['option'],
			'keep_existing' => $mla_custom_field['keep_existing'],
			'no_null' => $mla_custom_field['no_null'],
			'mla_column' => $mla_custom_field['mla_column'],
			'quick_edit' => $mla_custom_field['quick_edit'],
			'bulk_edit' => $mla_custom_field['bulk_edit'],
			'active' => $mla_custom_field['active'],
			'read_only' => false,
			'changed' => true,
			'deleted' => false,
		);

		if ( MLA_Custom_Field_Query::mla_add_custom_field_rule( $new_rule ) ) {
			return __( 'Rule added', 'media-library-assistant' );
		}

		return __( 'ERROR', 'media-library-assistant' ) . __( ': Rule addition failed', 'media-library-assistant' );
	} // _add_custom_field_rule

	/**
	 * Update a custom field rule from full-screen Edit Rule values in $_REQUEST
 	 *
	 * @since 2.50
	 * @uses $_REQUEST for field-level values
	 *
	 * @param integer $post_id ID value of rule to update
	 * @param	array	&$template Display templates.
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _update_custom_field_rule( $post_id, &$template ) {
		$error_message = '';
		$mla_custom_field = isset( $_REQUEST['mla_custom_field'] ) ? stripslashes_deep( $_REQUEST['mla_custom_field'] ) : array();

		// Validate rule name change
		if ( !empty( $mla_custom_field['new_field'] ) ) {
			$new_name = $mla_custom_field['new_field'];
		} elseif ( !empty( $mla_custom_field['new_name'] ) && ( 'none' !== $mla_custom_field['new_name'] ) ) {
			$new_name = $mla_custom_field['new_name'];
		} else {
			$new_name = '';
		}

		if ( !empty( $new_name) ) {
			if ( MLA_Custom_Field_Query::mla_find_custom_field_rule_ID( $new_name ) ) {
				$error_message = __( 'ERROR', 'media-library-assistant' ) . __( ': Rule already exists for the new name', 'media-library-assistant' );
				$new_name = '';
			}
		} elseif ( $mla_custom_field['name'] !== $mla_custom_field['rule_name'] ) {
			$error_message =  __( 'ERROR', 'media-library-assistant' ) . __( ': Invalid rule name must be changed', 'media-library-assistant' );
		}

		// Convert checkbox/dropdown controls to booleans
		$mla_custom_field['mla_column'] = isset( $mla_custom_field['mla_column'] );
		$mla_custom_field['quick_edit'] = isset( $mla_custom_field['quick_edit'] );
		$mla_custom_field['bulk_edit'] = isset( $mla_custom_field['bulk_edit'] );
		$mla_custom_field['keep_existing'] = '1' === $mla_custom_field['keep_existing'];
		$mla_custom_field['no_null'] = isset( $mla_custom_field['no_null'] );
		$mla_custom_field['active'] = '1' === $mla_custom_field['status'];
		$mla_custom_field['read_only'] = $mla_custom_field['name'] !== $mla_custom_field['rule_name'];

		$new_rule = array(
			'post_ID' => $mla_custom_field['post_ID'],
			'rule_name' => $new_name ? $new_name : $mla_custom_field['rule_name'],
			'name' => $new_name ? $new_name : $mla_custom_field['name'],
			'data_source' => $mla_custom_field['data_source'],
			'meta_name' => $mla_custom_field['meta_name'],
			'format' => $mla_custom_field['format'],
			'option' => $mla_custom_field['option'],
			'keep_existing' => $mla_custom_field['keep_existing'],
			'no_null' => $mla_custom_field['no_null'],
			'mla_column' => $mla_custom_field['mla_column'],
			'quick_edit' => $mla_custom_field['quick_edit'],
			'bulk_edit' => $mla_custom_field['bulk_edit'],
			'active' => $mla_custom_field['active'],
			'read_only' => $mla_custom_field['read_only'],
			'changed' => true,
			'deleted' => false,
		);

		if ( empty( $error_message ) ) {
			if ( false === MLA_Custom_Field_Query::mla_replace_custom_field_rule( $new_rule ) ) {
				$error_message =  __( 'ERROR', 'media-library-assistant' ) . __( ': Rule update failed', 'media-library-assistant' );
			}
		}

		if ( empty( $error_message ) ) {
			return array( 'message' => __( 'Rule updated', 'media-library-assistant' ), 'body' => '' );
		}

		$page_content = self::_compose_edit_custom_field_rule_tab( $new_rule, $template );
		$page_content['message'] = $error_message;
		return $page_content;
	} // _update_custom_field_rule

	/**
	 * Delete a custom field rule
 	 *
	 * @since 2.50
	 *
	 * @param integer $post_id ID value of rule to delete
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _delete_custom_field_rule( $post_id ) {
		$rule = MLA_Custom_Field_Query::mla_find_custom_field_rule( $post_id );
		if ( false === $rule ) {
			return "ERROR: _delete_custom_field_rule( {$post_id} ) rule not found.";
		}

		MLA_Custom_Field_Query::mla_update_custom_field_rule( $post_id, 'deleted', true );
		return sprintf( __( 'Custom Field Rule "%1$s" deleted.', 'media-library-assistant' ), $rule['rule_name'] );
	} // _delete_custom_field_rule

	/**
	 * Update a custom field rule from Bulk Edit action values in $_REQUEST
 	 *
	 * @since 2.50
	 * @uses $_REQUEST for field-level values
	 *
	 * @param integer $post_id ID value of rule to update
	 * @return string status/error message
	 */
	private static function _bulk_update_custom_field_rule( $post_id ) {
		$rule = MLA_Custom_Field_Query::mla_find_custom_field_rule( $post_id );
		if ( false === $rule ) {
			return "ERROR: _bulk_update_custom_field_rule( {$post_id} ) rule not found.";
		}

		// Convert dropdown controls to field values
		if ( '-1' !== $_REQUEST['format'] ) {
			$rule['format'] = $_REQUEST['format'];
		}
		
		if ( '-1' !== $_REQUEST['option'] ) {
			$rule['option'] = $_REQUEST['option'];
		}
		
		if ( '-1' !== $_REQUEST['keep_existing'] ) {
			$rule['keep_existing'] = '1' === $_REQUEST['keep_existing'];
		}
		
		if ( '-1' !== $_REQUEST['no_null'] ) {
			$rule['no_null'] = '1' === $_REQUEST['no_null'];
		}
		
		if ( '-1' !== $_REQUEST['mla_column'] ) {
			$rule['mla_column'] = '1' === $_REQUEST['mla_column'];
		}
		
		if ( '-1' !== $_REQUEST['quick_edit'] ) {
			$rule['quick_edit'] = '1' === $_REQUEST['quick_edit'];
		}
		
		if ( '-1' !== $_REQUEST['bulk_edit'] ) {
			$rule['bulk_edit'] = '1' === $_REQUEST['bulk_edit'];
		}
		
		if ( '-1' !== $_REQUEST['active'] ) {
			$rule['active'] = '1' === $_REQUEST['active'];
		}
		
		$rule['changed'] = true;
		$rule['deleted'] = false;

		if ( false === MLA_Custom_Field_Query::mla_replace_custom_field_rule( $rule ) ) {
			return  __( 'ERROR', 'media-library-assistant' ) . __( ': Rule update failed', 'media-library-assistant' );
		}

		return __( 'Rule updated', 'media-library-assistant' );
	} // _bulk_update_custom_field_rule

	/**
	 * Compose the Edit Custom Field Rule tab content for the Settings/Custom Field subpage
	 *
	 * @since 2.50
	 *
	 * @param	array	$item Data values for the item.
	 * @param	array	&$template Display templates.
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_edit_custom_field_rule_tab( $item, &$template ) {
		// An old bug left multiple rules for the same custom field; only one can be active
		if ( $item['name'] === $item['rule_name'] ) {
			$display_name = $item['name'];
		} else {
			$display_name = $item['rule_name'] . ' / ' . $item['name'];
		}

		$page_values = array(
			'Edit Rule' => __( 'Edit Rule', 'media-library-assistant' ),
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-custom_field&mla_tab=custom_field',
			'post_ID' => $item['post_ID'],
			'name' => $item['name'],
			'rule_name' => $item['rule_name'],
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'Name' => __( 'Name', 'media-library-assistant' ),
			'display_name' => $display_name,
			'new_names' => MLAOptions::mla_compose_custom_field_option_list( '', MLA_Custom_Field_Query::mla_custom_field_rule_names() ),
			'Enter Name' => __( 'This is the name of the custom field to which the rule applies.<br>Only one rule is allowed for each custom field.', 'media-library-assistant' ),
			'Change Name' => __( 'Change Name', 'media-library-assistant' ),
			'Cancel Name Change' => __( 'Cancel Name Change', 'media-library-assistant' ),
			'Enter new field' => __( 'Enter new field', 'media-library-assistant' ),
			'Cancel new field' => __( 'Cancel new field', 'media-library-assistant' ),
			'Data Source' => __( 'Data Source', 'media-library-assistant' ),
			'data_sources' => MLAOptions::mla_compose_data_source_option_list( $item['data_source'] ),
			'Meta/Template' => __( 'Meta/Template', 'media-library-assistant' ),
			'meta_name' => $item['meta_name'],
			'Enter Meta/Template' => __( 'WordPress attachment metadata element or Content Template', 'media-library-assistant' ),
			'mla_column' => $item['mla_column'] ? 'checked=checked' : '',
			'MLA Column' => __( 'MLA Column', 'media-library-assistant' ),
			'Check MLA Column' => __( 'Display as Media/Assistant column', 'media-library-assistant' ),
			'quick_edit' => $item['quick_edit'] ? 'checked=checked' : '',
			'Quick Edit' => __( 'Quick Edit', 'media-library-assistant' ),
			'Check Quick Edit' => __( 'Add to Media/Assistant Quick Edit area', 'media-library-assistant' ),
			'bulk_edit' => $item['bulk_edit'] ? 'checked=checked' : '',
			'Bulk Edit' => __( 'Bulk Edit', 'media-library-assistant' ),
			'Check Bulk Edit' => __( 'Add to Media/Assistant Bulk Edit area', 'media-library-assistant' ),
			'Existing Text' => __( 'Existing Text', 'media-library-assistant' ),
			'keep_selected' => $item['keep_existing'] ? 'selected=selected' : '',
			'Keep' => __( 'Keep', 'media-library-assistant' ),
			'replace_selected' => $item['keep_existing'] ? '' : 'selected=selected',
			'Replace' => __( 'Replace', 'media-library-assistant' ),
			'Format' => __( 'Format', 'media-library-assistant' ),
			'native_format' => ( 'native' === $item['format'] ) ? 'selected=selected' : '',
			'Native' => __( 'Native', 'media-library-assistant' ),
			'commas_format' => ( 'commas' === $item['format'] ) ? 'selected=selected' : '',
			'Commas' => __( 'Commas', 'media-library-assistant' ),
			'raw_format' => ( 'raw' === $item['format'] ) ? 'selected=selected' : '',
			'Raw' => __( 'Raw', 'media-library-assistant' ),
			'Option' => __( 'Option', 'media-library-assistant' ),
			'text_option' => ( 'text' === $item['option'] ) ? 'selected=selected' : '',
			'Text' => __( 'Text', 'media-library-assistant' ),
			'single_option' => ( 'single' === $item['option'] ) ? 'selected=selected' : '',
			'Single' => __( 'Single', 'media-library-assistant' ),
			'export_option' => ( 'export' === $item['option'] ) ? 'selected=selected' : '',
			'Export' => __( 'Export', 'media-library-assistant' ),
			'array_option' => ( 'array' === $item['option'] ) ? 'selected=selected' : '',
			'Array' => __( 'Array', 'media-library-assistant' ),
			'multi_option' => ( 'multi' === $item['option'] ) ? 'selected=selected' : '',
			'Multi' => __( 'Multi', 'media-library-assistant' ),
			'no_null' => $item['no_null'] ? 'checked=checked' : '',
			'Delete NULL' => __( 'Delete NULL Values', 'media-library-assistant' ),
			'Check Delete NULL' => __( 'Do not store empty custom field values', 'media-library-assistant' ),
			'Status' => __( 'Status', 'media-library-assistant' ),
			'active_selected' => $item['active'] ? 'selected=selected' : '',
			'Active' => __( 'Active', 'media-library-assistant' ),
			'inactive_selected' => $item['active'] ? '' : 'selected=selected',
			'Inactive' => __( 'Inactive', 'media-library-assistant' ),
			'cancel' => 'mla-edit-custom-field-cancel',
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
			'submit' => 'mla-edit-custom-field-submit',
			'Update' => __( 'Update', 'media-library-assistant' ),
		);

		return array(
			'message' => '',
			'body' => MLAData::mla_parse_template( $template['single-item-edit'], $page_values )
		);
	}

	/**
	 * Purge one or more custom field values for Bulk action
	 *
	 * @since 2.50
	 *
	 * @param array $rule_ids ID value of rule(s), to get field names
	 *
	 * @return array Message(s) reflecting the results of the operation
	 */
	private static function _purge_custom_field_values( $rule_ids ) {
		$message = '';
		$source_rules = MLA_Custom_Field_Query::mla_convert_custom_field_rules( $rule_ids );
		foreach ( $source_rules as $rule_name => $rule ) {
			$result = MLASettings::mla_delete_custom_field( $rule );
			$message .=  sprintf( __( 'Custom Field Rule "%1$s": %2$s', 'media-library-assistant' ), $rule['name'], $result );
		}

		return $message;
	}

	/**
	 * Compose the Custom Field tab content for the Settings subpage
	 *
	 * @since 1.10
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	public static function mla_compose_custom_field_tab( ) {
		$page_content = array( 'message' => '', 'body' => '' );
		$page_template_array = MLACore::mla_load_template( 'admin-display-settings-custom-fields-tab.tpl' );
		if ( ! is_array( $page_template_array ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			$page_content['message'] = sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLASettings_CustomFields::mla_compose_custom_field_tab', var_export( $page_template_array, true ) );
			return $page_content;
		}

		// Initialize page messages and content, check for page-level Save Changes, Add/Update/Cancel Rule
		if ( !empty( $_REQUEST['mla-custom-field-options-save'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_save_custom_field_settings( );
		} elseif ( !empty( $_REQUEST['mla-add-custom-field-submit'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content['message'] = MLASettings_CustomFields::_add_custom_field_rule();
			MLA_Custom_Field_Query::mla_put_custom_field_rules();
		} elseif ( !empty( $_REQUEST['mla-edit-custom-field-submit'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = MLASettings_CustomFields::_update_custom_field_rule( $_REQUEST['mla_custom_field']['post_ID'], $page_template_array );
			MLA_Custom_Field_Query::mla_put_custom_field_rules();
		} elseif ( !empty( $_REQUEST['mla-edit-custom-field-cancel'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content['message'] = __( 'Edit Custom Field Rule cancelled.', 'media-library-assistant' );
		}

		if ( !empty( $page_content['body'] ) ) {
			return $page_content;
		}

		// Process bulk actions that affect an array of items
		$bulk_action = MLASettings::mla_current_bulk_action();
		if ( $bulk_action && ( $bulk_action != 'none' ) ) {
			if ( array_key_exists( $bulk_action, MLA_Custom_Fields_List_Table::mla_get_bulk_actions() ) ) {
				if ( isset( $_REQUEST['cb_mla_item_ID'] ) ) {
					if ( 'execute' === $bulk_action ) {
						$page_content['message'] = sprintf( __( 'Unknown bulk action %1$s', 'media-library-assistant' ), $bulk_action );
					} elseif ( 'purge' === $bulk_action ) {
						$page_content['message'] = MLASettings_CustomFields::_purge_custom_field_values( $_REQUEST['cb_mla_item_ID'] );
					} else {
						foreach ( $_REQUEST['cb_mla_item_ID'] as $post_ID ) {
							switch ( $bulk_action ) {
								case 'edit':
									$item_content = MLASettings_CustomFields::_bulk_update_custom_field_rule( $post_ID );
									break;
								case 'delete':
									$item_content = MLASettings_CustomFields::_delete_custom_field_rule( $post_ID );
									break;
								default:
									$item_content = 'Bad action'; // UNREACHABLE
							} // switch $bulk_action
	
							$page_content['message'] .= $item_content . '<br>';
						} // foreach cb_attachment
	
						MLA_Custom_Field_Query::mla_put_custom_field_rules();
					} // edit, delete
				} // isset cb_attachment
				else {
					/* translators: 1: action name, e.g., edit */
					$page_content['message'] = sprintf( __( 'Bulk Action %1$s - no items selected.', 'media-library-assistant' ), $bulk_action );
				}
			} else {
				/* translators: 1: bulk_action, e.g., delete, edit, execute, purge */
				$page_content['message'] = sprintf( __( 'Unknown bulk action %1$s', 'media-library-assistant' ), $bulk_action );
			}
		} // $bulk_action

		// Process row-level actions that affect a single item
		if ( !empty( $_REQUEST['mla_admin_action'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

			$page_content = array( 'message' => '', 'body' => '' );

			switch ( $_REQUEST['mla_admin_action'] ) {
				case MLACore::MLA_ADMIN_SINGLE_EDIT_DISPLAY:
					$item = MLA_Custom_Field_Query::mla_find_custom_field_rule( $_REQUEST['mla_item_ID'] );
					$page_content = self::_compose_edit_custom_field_rule_tab( $item, $page_template_array );
					break;
				case MLACore::MLA_ADMIN_SINGLE_CUSTOM_FIELD_PURGE:
					$page_content['message'] = MLASettings_CustomFields::_purge_custom_field_values( $_REQUEST['mla_item_ID'] );
					break;
				case MLACore::MLA_ADMIN_SINGLE_DELETE:
					$page_content['message'] = MLASettings_CustomFields::_delete_custom_field_rule( $_REQUEST['mla_item_ID'] );
					MLA_Custom_Field_Query::mla_put_custom_field_rules();
					break;
				default:
					$page_content['message'] = sprintf( __( 'Unknown mla_admin_action - "%1$s"', 'media-library-assistant' ), $_REQUEST['mla_admin_action'] );
					break;
			} // switch ($_REQUEST['mla_admin_action'])
		} // (!empty($_REQUEST['mla_admin_action'])

		if ( !empty( $page_content['body'] ) ) {
			return $page_content;
		}

		// Check for disabled status
		if ( 'checked' != MLACore::mla_get_option( MLACoreOptions::MLA_ALLOW_CUSTOM_FIELD_MAPPING ) ) {
			// Fill in the page-level option
			$options_list = '';
			foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
				if ( MLACoreOptions::MLA_ALLOW_CUSTOM_FIELD_MAPPING == $key ) {
					$options_list .= MLASettings::mla_compose_option_row( $key, $value );
				}
			}

			$page_values = array(
				'Support is disabled' => __( 'Custom Field Mapping Support is disabled', 'media-library-assistant' ),
				'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-custom_field&mla_tab=custom_field',
				'options_list' => $options_list,
				'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
				'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			);

			$page_content['body'] .= MLAData::mla_parse_template( $page_template_array['custom-field-disabled'], $page_values );
			return $page_content;
		}

		// Display the Custom Fields tab and the custom fields rule table
		$_SERVER['REQUEST_URI'] = remove_query_arg( array(
			'mla_admin_action',
			'mla_custom_field_item',
			'mla_item_ID',
			'_wpnonce',
			'_wp_http_referer',
			'action',
			'action2',
			'cb_mla_item_ID',
			'mla-edit-custom-field-cancel',
			'mla-edit-custom-field-submit',
			'mla-custom-field-options-save',
		), $_SERVER['REQUEST_URI'] );

		// Create an instance of our package class
		$MLACustomFieldsListTable = new MLA_Custom_Fields_List_Table();

		//	Fetch, prepare, sort, and filter our data
		$MLACustomFieldsListTable->prepare_items();

		// Start with any page-level options
		$options_list = '';
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'custom_field' == $value['tab'] ) {
				$options_list .= MLASettings::mla_compose_option_row( $key, $value );
			}
		}

		// WPML requires that lang be the first argument after page
		$view_arguments = MLA_Custom_Fields_List_Table::mla_submenu_arguments();
		$form_language = isset( $view_arguments['lang'] ) ? '&lang=' . $view_arguments['lang'] : '';
		$form_arguments = '?page=mla-settings-menu-custom_field' . $form_language . '&mla_tab=custom_field';

		// We need to remember all the view arguments
		$view_args = '';
		foreach ( $view_arguments as $key => $value ) {
			// 'lang' has already been added to the form action attribute
			if ( in_array( $key, array( 'lang' ) ) ) {
				continue;
			}

			if ( is_array( $value ) ) {
				foreach ( $value as $element_key => $element_value )
					$view_args .= "\t" . sprintf( '<input type="hidden" name="%1$s[%2$s]" value="%3$s" />', $key, $element_key, esc_attr( $element_value ) ) . "\n";
			} else {
				$view_args .= "\t" . sprintf( '<input type="hidden" name="%1$s" value="%2$s" />', $key, esc_attr( $value ) ) . "\n";
			}
		}

		$progress_template_array = MLACore::mla_load_template( 'admin-display-settings-progress-div.tpl' );
		if ( ! is_array( $progress_template_array ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			$page_content['message'] = sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLASettings_CustomFields::mla_compose_custom_field_tab', var_export( $progress_template_array, true ) );
			return $page_content;
		}

		$page_values = array(
			'Mapping Progress' => __( 'Custom Field Mapping Progress', 'media-library-assistant' ),
			'DO NOT' => __( 'DO NOT DO THE FOLLOWING (they will cause mapping to fail)', 'media-library-assistant' ),
			'DO NOT Close' => __( 'Close the window', 'media-library-assistant' ),
			'DO NOT Reload' => __( 'Reload the page', 'media-library-assistant' ),
			'DO NOT Click' => __( 'Click the browser&rsquo;s Stop, Back or forward buttons', 'media-library-assistant' ),
			'Progress' => __( 'Progress', 'media-library-assistant' ),
			'Pause' => __( 'Pause', 'media-library-assistant' ),
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
			'Resume' => __( 'Resume', 'media-library-assistant' ),
			'Close' => __( 'Close', 'media-library-assistant' ),
			'Refresh' => __( 'Refresh', 'media-library-assistant' ),
			'refresh_href' => '?page=mla-settings-menu-custom_field&mla_tab=custom_field',
		);

		$progress_div = MLAData::mla_parse_template( $progress_template_array['mla-progress-div'], $page_values );

		$page_values = array(
			'mla-progress-div' => $progress_div,
			'Custom Field Options' => __( 'Custom Field and Attachment Metadata Processing Options', 'media-library-assistant' ),
			/* translators: 1: Documentation hyperlink */
			'In this tab' => __( 'In this tab you can define the rules for mapping several types of image metadata to WordPress custom fields. You can also use this screen to define rules for adding or updating fields within the WordPress-supplied "Attachment Metadata", stored in the "_wp_attachment_metadata" custom field.', 'media-library-assistant' ),
			/* translators: 1: Documentation hyperlink */
			'You can find' => __( 'You can find more information about using the controls in this tab to define mapping rules and apply them by clicking the "Help" control in the upper-right corner of the screen.', 'media-library-assistant' ),
			'settingsURL' => admin_url('options-general.php'),
			'form_url' => admin_url( 'options-general.php' ) . $form_arguments,
			'view_args' => $view_args,
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'results' => ! empty( $_REQUEST['s'] ) ? '<span class="alignright" style="margin-top: .5em; font-weight: bold">' . __( 'Search results for', 'media-library-assistant' ) . ':&nbsp;</span>' : '',
			// '_wp_http_referer' => wp_referer_field( false ),
			'Search Rules Text' => __( 'Search Rules Text', 'media-library-assistant' ),
			's' => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
			'Search Rules' => __( 'Search Rules', 'media-library-assistant' ),
			'options_list' => $options_list,
			'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
			'Map All' => __( 'Execute All Rules', 'media-library-assistant' ),
			'Add New Rule' => __( 'Add New Custom Field Rule', 'media-library-assistant' ),
			'Name' => __( 'Name', 'media-library-assistant' ),
			'new_names' => MLAOptions::mla_compose_custom_field_option_list( 'none', MLA_Custom_Field_Query::mla_custom_field_rule_names() ),
			'Enter new field' => __( 'Enter new field', 'media-library-assistant' ),
			'Cancel new field' => __( 'Cancel new field', 'media-library-assistant' ),
			'Data Source' => __( 'Data Source', 'media-library-assistant' ),
			'data_sources' => MLAOptions::mla_compose_data_source_option_list( 'none' ),
			'Meta/Template' => __( 'Meta/Template', 'media-library-assistant' ),
			'meta_name' => '', // initial value
			'Enter Meta/Template' => __( 'WordPress attachment metadata element or Content Template', 'media-library-assistant' ),
			'mla_column' => '', // or checked=checked
			'MLA Column' => __( 'MLA Column', 'media-library-assistant' ),
			'Check MLA Column' => __( 'Display as Media/Assistant column', 'media-library-assistant' ),
			'quick_edit' => '', // or checked=checked
			'Quick Edit' => __( 'Quick Edit', 'media-library-assistant' ),
			'Check Quick Edit' => __( 'Add to Media/Assistant Quick Edit area', 'media-library-assistant' ),
			'bulk_edit' => '', // or checked=checked
			'Bulk Edit' => __( 'Bulk Edit', 'media-library-assistant' ),
			'Check Bulk Edit' => __( 'Add to Media/Assistant Bulk Edit area', 'media-library-assistant' ),
			'Existing Text' => __( 'Existing Text', 'media-library-assistant' ),
			'keep_selected' => '',
			'Keep' => __( 'Keep', 'media-library-assistant' ),
			'replace_selected' => '',
			'Replace' => __( 'Replace', 'media-library-assistant' ),
			'Format' => __( 'Format', 'media-library-assistant' ),
			'native_format' => '',
			'Native' => __( 'Native', 'media-library-assistant' ),
			'commas_format' => '',
			'Commas' => __( 'Commas', 'media-library-assistant' ),
			'raw_format' => '',
			'Raw' => __( 'Raw', 'media-library-assistant' ),
			'Option' => __( 'Option', 'media-library-assistant' ),
			'text_option' => '',
			'Text' => __( 'Text', 'media-library-assistant' ),
			'single_option' => '',
			'Single' => __( 'Single', 'media-library-assistant' ),
			'export_option' => '',
			'Export' => __( 'Export', 'media-library-assistant' ),
			'array_option' => '',
			'Array' => __( 'Array', 'media-library-assistant' ),
			'multi_option' => '',
			'Multi' => __( 'Multi', 'media-library-assistant' ),
			'no_null' => '', // or checked=checked
			'Delete NULL' => __( 'Delete NULL Values', 'media-library-assistant' ),
			'Check Delete NULL' => __( 'Do not store empty custom field values', 'media-library-assistant' ),
			'Status' => __( 'Status', 'media-library-assistant' ),
			'active_selected' => '',
			'Active' => __( 'Active', 'media-library-assistant' ),
			'inactive_selected' => '',
			'Inactive' => __( 'Inactive', 'media-library-assistant' ),
			'Add Rule' => __( 'Add Rule', 'media-library-assistant' ),
			'No Change' => __( 'No Change', 'media-library-assistant' ),
			'Yes' => __( 'Yes', 'media-library-assistant' ),
			'No' => __( 'No', 'media-library-assistant' ),
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
			'Update' => __( 'Update', 'media-library-assistant' ),
		);

		$page_content['body'] = MLAData::mla_parse_template( $page_template_array['before-table'], $page_values );

		//	 Now we can render the completed list table
		ob_start();
		$MLACustomFieldsListTable->views();
		$MLACustomFieldsListTable->display();
		$page_content['body'] .= ob_get_contents();
		ob_end_clean();

		$page_content['body'] .= MLAData::mla_parse_template( $page_template_array['after-table'], $page_values );

		return $page_content;
	} // mla_compose_custom_field_tab

	/**
	 * Ajax handler for Custom Fields tab inline mapping
	 *
	 * @since 2.00
	 *
	 * @return	void	echo json response object, then die()
	 */
	public static function mla_inline_mapping_custom_action() {
		MLACore::mla_debug_add( 'MLASettings_CustomFields::mla_inline_mapping_custom_action $_REQUEST = ' . var_export( $_REQUEST, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		set_current_screen( $_REQUEST['screen'] );
		check_ajax_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

		// Find the current chunk
		$offset = isset( $_REQUEST['offset'] ) ? $_REQUEST['offset'] : 0;
		$length = isset( $_REQUEST['length'] ) ? $_REQUEST['length'] : 0;

		$page_content = array(
			'message' => 'ERROR: No action taken',
			'body' => '',
			'processed' => 0,
			'unchanged' => 0,
			'success' =>  0
		);

		// Look for "Execute All Rules", Bulk Action Execute, then the "Execute" rollover action
		if ( ! empty( $_REQUEST['bulk_action'] ) && ( 'custom-field-options-map' == $_REQUEST['bulk_action'] ) ) {
			$page_content = self::_process_custom_field_mapping( NULL, $offset, $length );
		}
		elseif ( ! empty( $_REQUEST['bulk_action'] ) && ( 'mapping-options-bulk-execute' == $_REQUEST['bulk_action'] ) ) {
			$source_rules = MLA_Custom_Field_Query::mla_convert_custom_field_rules( $_REQUEST['ids'] );
			
			$rules = array();
			foreach ( $source_rules as $rule_name => $rule ) {
				if ( 'none' === $rule['data_source'] ) {
					continue;
				}
	
				$rule['active'] = true; // Always execute for bulk actions
				$rules[ $rule_name ] = $rule;
			}
	
			if ( empty( $rules ) ) {
				$page_content['message'] = __( 'Nothing to execute', 'media-library-assistant' );
			} else {
				$page_content = self::_process_custom_field_mapping( $rules, $offset, $length );
			}
		}
		elseif ( ! empty( $_REQUEST['bulk_action'] ) && ( 0 === strpos( $_REQUEST['bulk_action'], MLACore::MLA_ADMIN_SINGLE_CUSTOM_FIELD_MAP ) ) ) {
			$match_count = preg_match( '/\[(.*)\]/', $_REQUEST['bulk_action'], $matches );
			if ( $match_count ) {
				$post_id = absint( $matches[1] );
				$source_rules = MLA_Custom_Field_Query::mla_convert_custom_field_rules( $post_id );
			
				$rules = array();
				foreach ( $source_rules as $rule_name => $rule ) {
					if ( 'none' === $rule['data_source'] ) {
						continue;
					}
		
					$rule['active'] = true; // Always execute for rollover action
					$rules[ $rule_name ] = $rule;
				}
		
				if ( empty( $rules ) ) {
					$page_content['message'] = __( 'Nothing to execute', 'media-library-assistant' );
				} else {
					$page_content = self::_process_custom_field_mapping( $rules, $offset, $length );
				}
			}
		}

		$chunk_results = array( 
			'message' => $page_content['message'],
			'processed' => $page_content['processed'],
			'unchanged' => $page_content['unchanged'],
			'success' => $page_content['success'],
			'refresh' => isset( $page_content['refresh'] ) && true == $page_content['refresh'],
		);

		MLACore::mla_debug_add( 'MLASettings::mla_inline_mapping_custom_action $chunk_results = ' . var_export( $chunk_results, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		wp_send_json_success( $chunk_results );
	} // mla_inline_mapping_custom_action

	/**
	 * Ajax handler for Custom Fields inline editing (quick edit)
	 *
	 * Adapted from wp_ajax_inline_save in /wp-admin/includes/ajax-actions.php
	 *
	 * @since 2.50
	 *
	 * @return	void	echo HTML <tr> markup for updated row or error message, then die()
	 */
	public static function mla_inline_edit_custom_action() {
		set_current_screen( $_REQUEST['screen'] );
		check_ajax_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

		$error_message = '';
		if ( empty( $_REQUEST['post_ID'] ) ) {
			$error_message = __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Rule ID not found', 'media-library-assistant' );
		} else {
			$rule = MLA_Custom_Field_Query::mla_find_custom_field_rule( $_REQUEST['post_ID'] );
			if ( false === $rule ) {
				$error_message = __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Rule not found', 'media-library-assistant' );
			}
		}
		
		if ( !empty( $error_message ) ) {
			echo $error_message;
			die();
		}

		$rule['data_source'] = $_REQUEST['data_source'];
		$rule['meta_name'] = $_REQUEST['meta_name'];
		$rule['format'] = $_REQUEST['format'];
		$rule['option'] = $_REQUEST['option'];
		$rule['keep_existing'] = '1' === $_REQUEST['keep_existing'];
		$rule['no_null'] = isset( $_REQUEST['no_null'] ) && '1' === $_REQUEST['no_null'];
		$rule['mla_column'] = isset( $_REQUEST['mla_column'] ) && '1' === $_REQUEST['mla_column'];
		$rule['quick_edit'] = isset( $_REQUEST['quick_edit'] ) && '1' === $_REQUEST['quick_edit'];
		$rule['bulk_edit'] = isset( $_REQUEST['bulk_edit'] ) && '1' === $_REQUEST['bulk_edit'];
		$rule['active'] = '1' === $_REQUEST['status'];
		$rule['changed'] = true;
		$rule['deleted'] = false;
		$rule = stripslashes_deep( $rule );

		if ( false === MLA_Custom_Field_Query::mla_replace_custom_field_rule( $rule ) ) {
			echo __( 'ERROR', 'media-library-assistant' ) . __( ': Rule update failed', 'media-library-assistant' );
			die();
		}

		MLA_Custom_Field_Query::mla_put_custom_field_rules();

		//	Create an instance of our package class and echo the new HTML
		$MLAListCustomTable = new MLA_Custom_Fields_List_Table();
		$MLAListCustomTable->single_row( (object) $rule );
		die(); // this is required to return a proper result
	} // mla_inline_edit_custom_action
} // class MLASettings_CustomFields

/* 
 * The WP_List_Table class isn't automatically available to plugins
 */
if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class MLA (Media Library Assistant) Custom Fields List Table implements the "Custom Fields"
 * admin settings submenu table
 *
 * Extends the core WP_List_Table class.
 *
 * @package Media Library Assistant
 * @since 2.50
 */
class MLA_Custom_Fields_List_Table extends WP_List_Table {
	/**
	 * Initializes some properties from $_REQUEST variables, then
	 * calls the parent constructor to set some default configs.
	 *
	 * @since 2.50
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
			'singular' => 'custom_field', //singular name of the listed records
			'plural' => 'custom_fields', //plural name of the listed records
			'ajax' => true, //does this table support ajax?
			'screen' => 'settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-custom_field'
		) );

		// NOTE: There is one add_action call at the end of this source file.
	}

	/**
	 * Table column definitions
	 *
	 * This array defines table columns and titles where the key is the column slug (and class)
	 * and the value is the column's title text.
	 * 
	 * All of the columns are added to this array by MLA_Custom_Fields_List_Table::_localize_default_columns_array.
	 *
	 * @since 2.50
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
	 * @since 2.50
	 *
	 * @var	array
	 */
	private static $default_hidden_columns	= array(
		// 'name',
		'rule_name',
		// 'data_source',
		// 'meta_name',
		// 'visibility',
		// 'status',
		'existing_text',
		'delete_null',
		'format',
		'option'
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
	 * @since 2.50
	 * @access private
	 * @var	array $default_sortable_columns {
	 *         @type array $$column_slug {
	 *                 @type string $orderby_name Database column or other sorting slug.
	 *                 @type boolean $descending Optional. True to make the initial orderby DESC.
	 *         }
	 * }
	 */
	private static $default_sortable_columns = array(
		'name' => array('name',true),
		'rule_name' => array('rule_name',true),
		'data_source' => array('data_source',false),
		// 'meta_name',
		// 'visibility',
		'status' => array('status',false),
		'existing_text' => array('existing_text',false),
		'delete_null' => array('delete_null',false),
		'format' => array('format',false),
		'option' => array('option',false),
		);

	/**
	 * Access the default list of hidden columns
	 *
	 * @since 2.50
	 *
	 * @return	array	default list of hidden columns
	 */
	private static function _default_hidden_columns( ) {
		return self::$default_hidden_columns;
	}

	/**
	 * Return the names and orderby values of the sortable columns
	 *
	 * @since 2.50
	 *
	 * @return	array	column_slug => array( orderby value, initial_descending_sort ) for sortable columns
	 */
	public static function mla_get_sortable_columns( ) {
		return self::$default_sortable_columns;
	}

	/**
	 * Process $_REQUEST, building $submenu_arguments
	 *
	 * @since 2.50
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
		if ( isset( $_REQUEST['mla_custom_field_view'] ) ) {
			$submenu_arguments['mla_custom_field_view'] = $_REQUEST['mla_custom_field_view'];
		}

		// Search box arguments
		if ( !empty( $_REQUEST['s'] ) ) {
			$submenu_arguments['s'] = urlencode( stripslashes( $_REQUEST['s'] ) );
		}

		// Filter arguments (from table header)
		if ( isset( $_REQUEST['mla_custom_field_status'] ) && ( 'any' != $_REQUEST['mla_custom_field_status'] ) ) {
			$submenu_arguments['mla_custom_field_status'] = $_REQUEST['mla_custom_field_status'];
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
	 * Handler for filter 'get_user_option_managesettings_page_mla-settings-menu-custom_fieldcolumnshidden'
	 *
	 * Required because the screen.php get_hidden_columns function only uses
	 * the get_user_option result. Set when the file is loaded because the object
	 * is not created in time for the call from screen.php.
	 *
	 * @since 2.50
	 *
	 * @param mixed	false or array with current list of hidden columns, if any
	 * @param string	'managesettings_page_mla-settings-menu-custom_fieldcolumnshidden'
	 * @param object	WP_User object, if logged in
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
	 * @since 2.50
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
	 * @since 2.50
	 *
	 * @return	void
	 */
	private static function _localize_default_columns_array( ) {
		if ( empty( self::$default_columns ) ) {
			// Build the default columns array at runtime to accomodate calls to the localization functions
			self::$default_columns = array(
				'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
				'name' => _x( 'Name', 'list_table_column', 'media-library-assistant' ),
				'rule_name' => _x( 'Bad Name', 'list_table_column', 'media-library-assistant' ),
				'data_source' => _x( 'Source', 'list_table_column', 'media-library-assistant' ),
				'meta_name' => _x( 'Meta/Template', 'list_table_column', 'media-library-assistant' ),
				'visibility' => _x( 'Visibility', 'list_table_column', 'media-library-assistant' ),
				'status' => _x( 'Status', 'list_table_column', 'media-library-assistant' ),
				'existing_text'  => _x( 'Existing Text', 'list_table_column', 'media-library-assistant' ),
				'delete_null'  => _x( 'Delete NULL', 'list_table_column', 'media-library-assistant' ),
				'format'  => _x( 'Format', 'list_table_column', 'media-library-assistant' ),
				'option'  => _x( 'Option', 'list_table_column', 'media-library-assistant' ),
			);
		}
	}

	/**
	 * Called in the admin_init action because the list_table object isn't
	 * created in time to affect the "screen options" setup.
	 *
	 * @since 2.50
	 *
	 * @return	void
	 */
	public static function mla_admin_init( ) {
		if ( isset( $_REQUEST['mla_tab'] ) && $_REQUEST['mla_tab'] == 'custom_field' ) {
			add_filter( 'get_user_option_managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-custom_fieldcolumnshidden', 'MLA_Custom_Fields_List_Table::mla_manage_hidden_columns_filter', 10, 3 );
			add_filter( 'manage_settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-custom_field_columns', 'MLA_Custom_Fields_List_Table::mla_manage_columns_filter', 10, 0 );
		}
	}

	/**
	 * Checks the current user's permissions
	 *
	 * @since 2.50
	 *
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can('manage_options');
	}

	/**
	 * Get the name of the default primary column.
	 *
	 * @since 2.50
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
	 * @since 2.50
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
	 * @since 2.50
	 *
	 * @param array	A singular item (one full row's worth of data)
	 * @param array	The name/slug of the column to be processed
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
	 * @since 2.50
	 * 
	 * @param object	An MLA custom_field_rule object
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
	 * @since 2.50
	 * 
	 * @param object	An MLA custom_field_rule object
	 * @param string	Current column name
	 *
	 * @return	array	Names and URLs of row-level actions
	 */
	private function _build_rollover_actions( $item, $column ) {
		$actions = array();

		/*
		 * Compose view arguments
		 */

		$view_args = array_merge( array(
			'page' => MLACoreOptions::MLA_SETTINGS_SLUG . '-custom_field',
			'mla_tab' => 'custom_field',
			'mla_item_ID' => urlencode( $item->post_ID )
		), MLA_Custom_Fields_List_Table::mla_submenu_arguments() );

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

		if ( !$item->read_only ) {
			$actions['inline hide-if-no-js'] = '<a class="editinline" href="#" title="' . __( 'Edit this item inline', 'media-library-assistant' ) . '">' . __( 'Quick Edit', 'media-library-assistant' ) . '</a>';

			$actions['execute hide-if-no-js'] = '<a class="execute" id="' . 
		MLACore::MLA_ADMIN_SINGLE_CUSTOM_FIELD_MAP . '[' . $item->post_ID . ']" href="#" title="' . __( 'Map All Attachments', 'media-library-assistant' ) . '">' . __( 'Execute', 'media-library-assistant' ) . '</a>';

			$actions['purge'] = '<a class="purge"' . ' href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_CUSTOM_FIELD_PURGE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Purge custom field values', 'media-library-assistant' ) . '">' . __( 'Purge Values', 'media-library-assistant' ) . '</a>';
		}

		$actions['delete'] = '<a class="delete-tag"' . ' href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_DELETE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Delete this item Permanently', 'media-library-assistant' ) . '">' . __( 'Delete Permanently', 'media-library-assistant' ) . '</a>';

		return $actions;
	}

	/**
	 * Add hidden fields with the data for use in the inline editor
	 *
	 * @since 2.50
	 * 
	 * @param object	An MLA custom_field_rule object
	 *
	 * @return	string	HTML <div> with row data
	 */
	private function _build_inline_data( $item ) {
		$inline_data = "\r\n" . '<div class="hidden" id="inline_' . $item->post_ID . "\">\r\n";
		$inline_data .= '	<div class="name">' . esc_attr( $item->name ) . "</div>\r\n";
		$inline_data .= '	<div class="slug">' . esc_attr( $item->name ) . "</div>\r\n";
		$inline_data .= '	<div class="rule_name">' . esc_attr( $item->rule_name ) . "</div>\r\n";
		$inline_data .= '	<div class="data_source">' . esc_attr( $item->data_source ) . "</div>\r\n";
		$inline_data .= '	<div class="meta_name">' . esc_attr( $item->meta_name ) . "</div>\r\n";
		$inline_data .= '	<div class="format">' . esc_attr( $item->format ) . "</div>\r\n";
		$inline_data .= '	<div class="option">' . esc_attr( $item->option ) . "</div>\r\n";
		$inline_data .= '	<div class="keep_existing">' . esc_attr( $item->keep_existing ) . "</div>\r\n";
		$inline_data .= '	<div class="no_null">' . esc_attr( $item->no_null ) . "</div>\r\n";
		$inline_data .= '	<div class="mla_column">' . esc_attr( $item->mla_column ) . "</div>\r\n";
		$inline_data .= '	<div class="quick_edit">' . esc_attr( $item->quick_edit ) . "</div>\r\n";
		$inline_data .= '	<div class="bulk_edit">' . esc_attr( $item->bulk_edit ) . "</div>\r\n";
		$inline_data .= '	<div class="active">' . esc_attr( $item->active ) . "</div>\r\n";
		$inline_data .= "</div>\r\n";
		return $inline_data;
	}

	/**
	 * Populate the Name column
	 *
	 * @since 2.50
	 * 
	 * @param object	An MLA custom_field_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_name( $item ) {
		if ( MLATest::$wp_4dot3_plus ) {
			return esc_html( $item->name );
		}

		$row_actions = self::_build_rollover_actions( $item, 'name' );
		return sprintf( '%1$s<br>%2$s%3$s', /*%1$s*/ esc_html( $item->name ), /*%2$s*/ $this->row_actions( $row_actions ), /*%3$s*/ $this->_build_inline_data( $item ) );
	}

	/**
	 * Populate the Bad Name column
	 *
	 * @since 2.50
	 * 
	 * @param object	An MLA custom_field_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_rule_name( $item ) {
		return ( $item->name !== $item->rule_name ) ? esc_html( $item->rule_name ) : '';
	}

	/**
	 * Populate the Source column
	 *
	 * @since 2.50
	 * 
	 * @param object	An MLA custom_field_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_data_source( $item ) {
		return esc_html( $item->data_source );
	}

	/**
	 * Populate the Meta/Template column
	 *
	 * @since 2.50
	 * 
	 * @param object	An MLA custom_field_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_meta_name( $item ) {
		return esc_html( $item->meta_name );
	}

	/**
	 * Populate the Visibility column
	 *
	 * @since 2.50
	 * 
	 * @param object	An MLA custom_field_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_visibility( $item ) {
		$visibility = '';

		if ( $item->mla_column ) {
			$visibility .= __( 'MLA Column', 'media-library-assistant' ) . '<br />';
		}

		if ( $item->quick_edit ) {
			$visibility .= __( 'Quick Edit', 'media-library-assistant' ) . '<br />';
		}

		if ( $item->bulk_edit ) {
			$visibility .= __( 'Bulk Edit', 'media-library-assistant' ) . '<br />';
		}

		if ( $length = strlen( $visibility ) ) {
			$visibility = substr( $visibility, 0, $length - strlen( '<br />' ) );
		} else {
			$visibility = '(none)';
		}

		return $visibility;
	}

	/**
	 * Populate the Status column
	 *
	 * @since 2.50
	 * 
	 * @param object	An MLA custom_field_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_status( $item ) {
		if ( $item->active ) {
			return __( 'Active', 'media-library-assistant' );
		} else {
			return __( 'Inactive', 'media-library-assistant' );
		}
	}

	/**
	 * Populate the Existing Text column
	 *
	 * @since 2.50
	 * 
	 * @param object	An MLA custom_field_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_existing_text( $item ) {
		if ( $item->keep_existing ) {
			return __( 'Keep', 'media-library-assistant' );
		} else {
			return __( 'Replace', 'media-library-assistant' );
		}
	}

	/**
	 * Populate the Delete NULL column
	 *
	 * @since 2.50
	 * 
	 * @param object	An MLA custom_field_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_delete_null( $item ) {
		if ( $item->no_null ) {
			return __( 'Yes', 'media-library-assistant' );
		} else {
			return __( 'No', 'media-library-assistant' );
		}
	}

	/**
	 * Populate the Format column
	 *
	 * @since 2.50
	 * 
	 * @param object	An MLA custom_field_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_format( $item ) {
		return $item->format;
	}

	/**
	 * Populate the Option column
	 *
	 * @since 2.50
	 * 
	 * @param object	An MLA custom_field_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_option( $item ) {
		return $item->option;
	}

	/**
	 * Display the pagination, adding view, search and filter arguments
	 *
	 * @since 2.50
	 * 
	 * @param string	'top' | 'bottom'
	 */
	function pagination( $which ) {
		$save_uri = $_SERVER['REQUEST_URI'];
		$_SERVER['REQUEST_URI'] = add_query_arg( MLA_Custom_Fields_List_Table::mla_submenu_arguments(), $save_uri );
		parent::pagination( $which );
		$_SERVER['REQUEST_URI'] = $save_uri;
	}

	/**
	 * This method dictates the table's columns and titles
	 *
	 * @since 2.50
	 * 
	 * @return	array	Column information: 'slugs'=>'Visible Titles'
	 */
	function get_columns( ) {
		return $columns = MLA_Custom_Fields_List_Table::mla_manage_columns_filter();
	}

	/**
	 * Returns the list of currently hidden columns from a user option or
	 * from default values if the option is not set
	 *
	 * @since 2.50
	 * 
	 * @return	array	Column information,e.g., array(0 => 'ID_parent, 1 => 'title_name')
	 */
	function get_hidden_columns( ) {
		$columns = get_user_option( 'managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-custom_fieldcolumnshidden' );

		if ( is_array( $columns ) ) {
			return $columns;
		}

		return self::_default_hidden_columns();
	}

	/**
	 * Returns an array where the key is the column that needs to be sortable
	 * and the value is db column to sort by.
	 *
	 * @since 2.50
	 * 
	 * @return	array	Sortable column information,e.g.,
	 * 					'slugs'=>array('data_values', boolean initial_descending_sort)
	 */
	function get_sortable_columns( ) {
		return self::$default_sortable_columns;
	}

	/**
	 * Returns HTML markup for one view that can be used with this table
	 *
	 * @since 2.50
	 *
	 * @param string $view_slug View slug
	 * @param array $custom_field_item count and labels for the View
	 * @param string $current_view Slug for current view 
	 * 
	 * @return	string | false	HTML for link to display the view, false if count = zero
	 */
	function _get_view( $view_slug, $custom_field_item, $current_view ) {
		static $base_url = NULL;

		$class = ( $view_slug == $current_view ) ? ' class="current"' : '';

		/*
		 * Calculate the common values once per page load
		 */
		if ( is_null( $base_url ) ) {
			/*
			 * Remember the view filters
			 */
			$base_url = 'options-general.php?page=' . MLACoreOptions::MLA_SETTINGS_SLUG . '-custom_field&mla_tab=custom_field';

			if ( isset( $_REQUEST['s'] ) ) {
				//$base_url = add_query_arg( array( 's' => $_REQUEST['s'] ), $base_url );
			}
		}

		$singular = sprintf('%s <span class="count">(%%s)</span>', $custom_field_item['singular'] );
		$plural = sprintf('%s <span class="count">(%%s)</span>', $custom_field_item['plural'] );
		$nooped_plural = _n_noop( $singular, $plural, 'media-library-assistant' );
		return "<a href='" . add_query_arg( array( 'mla_custom_field_view' => $view_slug ), $base_url )
			. "'$class>" . sprintf( translate_nooped_plural( $nooped_plural, $custom_field_item['count'], 'media-library-assistant' ), number_format_i18n( $custom_field_item['count'] ) ) . '</a>';
	} // _get_view

	/**
	 * Returns an associative array listing all the views that can be used with this table.
	 * These are listed across the top of the page and managed by WordPress.
	 *
	 * @since 2.50
	 * 
	 * @return	array	View information,e.g., array ( id => link )
	 */
	function get_views( ) {
		// Find current view
		$current_view = isset( $_REQUEST['mla_custom_field_view'] ) ? $_REQUEST['mla_custom_field_view'] : 'all';

		// Generate the list of views, retaining keyword search criterion
		$s = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';
		$custom_field_items = MLA_Custom_Field_Query::mla_tabulate_custom_field_items( $s );
		$view_links = array();
		foreach ( $custom_field_items as $slug => $item )
			$view_links[ $slug ] = self::_get_view( $slug, $item, $current_view );

		return $view_links;
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 2.50
	 * 
	 * @return	array	Contains all the bulk actions: 'slugs'=>'Visible Titles'
	 */
	function get_bulk_actions( ) {
		return self::mla_get_bulk_actions();
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 2.50
	 * 
	 * @return	array	Contains all the bulk actions: 'slugs'=>'Visible Titles'
	 */
	public static function mla_get_bulk_actions( ) {
		$actions = array();

		$actions['edit'] = __( 'Edit', 'media-library-assistant' );
		$actions['delete'] = __( 'Delete Permanently', 'media-library-assistant' );
		$actions['execute'] = __( 'Execute', 'media-library-assistant' );
		$actions['purge'] = __( 'Purge Values', 'media-library-assistant' );

		return $actions;
	}

	/**
	 * Get dropdown box of rule status values, i.e., Active/Inactive.
	 *
	 * @since 2.50
	 *
	 * @param string $selected Optional. Currently selected status. Default 'any'.
	 * @return string HTML markup for dropdown box.
	 */
	public static function mla_get_custom_field_status_dropdown( $selected = 'any' ) {
		$dropdown  = '<select name="mla_custom_field_status" class="postform" id="name">' . "\n";

		$selected_attribute = ( $selected == 'any' ) ? ' selected="selected"' : '';
		$dropdown .= "\t" . sprintf( '<option value="any"%1$s>%2$s</option>', $selected_attribute, _wp_specialchars( __( 'Any Status', 'media-library-assistant' ) ) ) . "\n";

		$selected_attribute = ( $selected == 'active' ) ? ' selected="selected"' : '';
		$dropdown .= "\t" . sprintf( '<option value="active"%1$s>%2$s</option>', $selected_attribute, _wp_specialchars( __( 'Active', 'media-library-assistant' ) ) ) . "\n";

		$selected_attribute = ( $selected == 'inactive' ) ? ' selected="selected"' : '';
		$dropdown .= "\t" . sprintf( '<option value="inactive"%1$s>%2$s</option>', $selected_attribute, _wp_specialchars( __( 'Inactive', 'media-library-assistant' ) ) ) . "\n";

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
			$actions = array( 'mla_custom_field_status', 'mla_filter' );
		} else {
			$actions = array();
		}

		if ( empty( $actions ) ) {
			return;
		}

		echo ( '<div class="alignleft actions">' );

		foreach ( $actions as $action ) {
			switch ( $action ) {
				case 'mla_custom_field_status':
					echo self::mla_get_custom_field_status_dropdown( isset( $_REQUEST['mla_custom_field_status'] ) ? $_REQUEST['mla_custom_field_status'] : 'any' );
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
	 * @since 2.50
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
		$total_items = MLA_Custom_Field_Query::mla_count_custom_field_rules( $_REQUEST );
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
		$this->items = MLA_Custom_Field_Query::mla_query_custom_field_rules( $_REQUEST, ( ( $current_page - 1 ) * $per_page ), $per_page );
	}

	/**
	 * Generates (echoes) content for a single row of the table
	 *
	 * @since 2.50
	 *
	 * @param object the current item
	 *
	 * @return void Echoes the row HTML
	 */
	function single_row( $item ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		echo '<tr id="custom_field-' . $item->post_ID . '"' . $row_class . '>';
		echo parent::single_row_columns( $item );
		echo '</tr>';
	}
} // class MLA_Custom_Fields_List_Table

/**
 * Class MLA (Media Library Assistant) Custom Field Query implements the
 * searchable database of custom field mapping rules.
 *
 * @package Media Library Assistant
 * @since 2.50
 */
class MLA_Custom_Field_Query {

	/**
	 * Callback to sort array by a 'name' key.
	 *
	 * @since 2.50
	 *
	 * @param array $a The first array.
	 * @param array $b The second array.
	 * @return integer The comparison result.
	 */
	private static function _sort_uname_callback( $a, $b ) {
		return strnatcasecmp( $a['name'], $b['name'] );
	}

	/**
	 * In-memory representation of the custom field mapping rules
	 *
	 * @since 2.50
	 *
	 * @var array $_custom_field_rules {
	 *         Items by ID. Key $$ID is an index number starting with 1.
	 *
	 *         @type array $$ID {
	 *             Rule elements.
	 *
	 *             @type integer $post_ID Rule ID; equal to $$ID.
	 *             @type string $rule_name Rule name, to accomodate an old bug.
	 *             @type string $name Custom field name the rule applies to.
	 *             @type string $data_source Data source name, 'none', 'meta' or 'template'.
	 *             @type string $meta_name if ( $data_source = 'meta' ) attachment metadata element name,
	 *                                     if ( $data_source = 'template ) template value w/o "template:"
	 *             @type string $format Output format, 'native', 'commas' or 'raw'.
	 *             @type string $option Output option, 'text', 'single'. 'array' or 'multi'.
	 *             @type boolean $keep_existing Retain existing value(s), do not replace them.
	 *             @type boolean $no_null Delete empty (NULL) values.
	 *             @type boolean $mla_column Display the field as a Media/Assistant submenu table column.
	 *             @type boolean $quick_edit Add the field to the Media/Assistant submenu table Quick Edit area.
	 *             @type boolean $bulk_edit Add the field to the Media/Assistant submenu table Bulk Edit area.
	 *             @type boolean $inline_edit OBSOLETE - no longer used.
	 *             @type boolean $active True if rule should be applied during mapping.
	 *             @type boolean $read_only True if rule_name !== name, to prevent editing of "old bug" rules.
	 *             @type boolean $changed True if the rule has changed since loading.
	 *             @type boolean $deleted True if the rule has been deleted since loading.
	 *         }
	 */
	private static $_custom_field_rules = NULL;

	/**
	 * Highest existing custom field rule ID value
	 *
	 * @since 2.50
	 *
	 * @var	integer
	 */
	private static $_custom_field_rule_highest_ID = 0;

	/**
	 * Assemble the in-memory representation of the custom field rules
	 *
	 * @since 2.50
	 *
	 * @param boolean $force_refresh Optional. Force a reload of rules. Default false.
	 * @return boolean Success (true) or failure (false) of the operation
	 */
	private static function _get_custom_field_rules( $force_refresh = false ) {
		if ( false == $force_refresh && NULL != self::$_custom_field_rules ) {
			return true;
		}

		self::$_custom_field_rules = array();
		self::$_custom_field_rule_highest_ID = 0;

		$current_values = MLACore::mla_get_option( 'custom_field_mapping' );
		if (empty( $current_values ) ) {
			return true;
		}

		/*
		 * One row for each existing rule, case insensitive "natural order"
		 */
		$sorted_keys = array();
		foreach ( $current_values as $rule_name => $current_value ) {
			$sorted_keys[ $current_value['name'] ] = $current_value['name'];
		}
		natcasesort( $sorted_keys );

		$sorted_names = array();
		foreach ( $sorted_keys as $rule_name ) {
			$sorted_names[ $rule_name ] = array();
		}

		/*
		 * Allow for multiple rules mapping the same name (an old bug)
		 */						
		foreach ( $current_values as $rule_name => $current_value ) {
			$sorted_names[ $current_value['name'] ][] = $rule_name;
		}

		foreach ( $sorted_names as $sorted_keys ) {
			foreach ( $sorted_keys as $rule_name ) {
				$current_value = $current_values[ $rule_name ];
				self::$_custom_field_rules[ ++self::$_custom_field_rule_highest_ID ] = array(
					'post_ID' => self::$_custom_field_rule_highest_ID,
					'rule_name' => $rule_name,
					'name' => $current_value['name'],
					'data_source' =>  $current_value['data_source'],
					'meta_name' => $current_value['meta_name'],
					'format' => $current_value['format'],
					'option' => $current_value['option'],
					'keep_existing' => $current_value['keep_existing'],
					'no_null' => $current_value['no_null'],
					'mla_column' => $current_value['mla_column'],
					'quick_edit' => $current_value['quick_edit'],
					'bulk_edit' => $current_value['bulk_edit'],
					'active' => isset( $current_value['active'] ) ? $current_value['active'] : true,
					'read_only' => $rule_name !== $current_value['name'],
					'changed' => false,
					'deleted' => false,
				);

				if ( self::$_custom_field_rules[ self::$_custom_field_rule_highest_ID ]['read_only'] ) {
				 self::$_custom_field_rules[ self::$_custom_field_rule_highest_ID ]['active'] = false;
				}
			} // foreach rule
		} // foreach name

		return true;
	}

	/**
	 * Flush the in-memory representation of the custom field rules to the option value
	 *
	 * @since 2.50
	 */
	public static function mla_put_custom_field_rules() {
		if ( NULL === self::$_custom_field_rules ) {
			return;
		}

		$custom_field_rules = array();
		$rules_changed = false;

		foreach( self::$_custom_field_rules as $ID => $current_value ) {
			if ( $current_value['deleted'] ) {
				$rules_changed = true;
				continue;
			}

			$custom_field_rules[ $current_value['rule_name'] ] = array(
				'name' => $current_value['name'],
				'data_source' =>  $current_value['data_source'],
				'meta_name' => $current_value['meta_name'],
				'format' => $current_value['format'],
				'option' => $current_value['option'],
				'keep_existing' => $current_value['keep_existing'],
				'no_null' => $current_value['no_null'],
				'mla_column' => $current_value['mla_column'],
				'quick_edit' => $current_value['quick_edit'],
				'bulk_edit' => $current_value['bulk_edit'],
				'active' => $current_value['active'],
			);

			$rules_changed |= $current_value['changed'];
		}

		if ( $rules_changed ) {
			$settings_changed = MLACore::mla_update_option( 'custom_field_mapping', $custom_field_rules );
			self::_get_custom_field_rules( true );
		}
	}

	/**
	 * Sanitize and expand query arguments from request variables
	 *
	 * @since 2.50
	 *
	 * @param array	query parameters from web page, usually found in $_REQUEST
	 * @param int		Optional number of rows (default 0) to skip over to reach desired page
	 * @param int		Optional number of rows on each page (0 = all rows, default)
	 *
	 * @return	array	revised arguments suitable for query
	 */
	private static function _prepare_custom_field_rules_query( $raw_request, $offset = 0, $count = 0 ) {
		/*
		 * Go through the $raw_request, take only the arguments that are used in the query and
		 * sanitize or validate them.
		 */
		if ( ! is_array( $raw_request ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			MLACore::mla_debug_add( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLA_Custom_Field_Query::_prepare_custom_field_rules_query', var_export( $raw_request, true ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			return NULL;
		}

		$clean_request = array (
			'mla_custom_field_view' => 'all',
			'mla_custom_field_status' => 'any',
			'orderby' => 'name',
			'order' => 'ASC',
			's' => ''
		);

		foreach ( $raw_request as $key => $value ) {
			switch ( $key ) {
				case 'mla_custom_field_view':
				case 'mla_custom_field_status':
					$clean_request[ $key ] = $value;
					break;
				case 'orderby':
					if ( 'none' == $value ) {
						$clean_request[ $key ] = $value;
					} else {
						if ( array_key_exists( $value, MLA_Custom_Fields_List_Table::mla_get_sortable_columns() ) ) {
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
	 * @since 2.50
	 *
	 * @param array	query parameters from web page, usually found in $_REQUEST
	 *
	 * @return	array	query results; array of MLA post_mime_type objects
	 */
	private static function _execute_custom_field_rules_query( $request ) {
		if ( ! self::_get_custom_field_rules() ) {
			return array ();
		}

		/*
		 * Sort and filter the list
		 */
		$keywords = isset( $request['s'] ) ? $request['s'] : '';
		preg_match_all('/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $keywords, $matches);
		$keywords = array_map( 'MLAQuery::mla_search_terms_tidy', $matches[0]);
		$view = isset( $request['mla_custom_field_view'] ) ? $request['mla_custom_field_view'] : 'all';
		$status = isset( $request['mla_custom_field_status'] ) ? $request['mla_custom_field_status'] : 'any';
		$index = 0;
		$sortable_items = array();

		foreach ( self::$_custom_field_rules as $ID => $value ) {
			if ( ! empty( $keywords ) ) {
				$found = false;
				foreach ( $keywords as $keyword ) {
					$found |= false !== stripos( $value['rule_name'], $keyword );
					$found |= false !== stripos( $value['name'], $keyword );
					$found |= false !== stripos( $value['data_source'], $keyword );
					$found |= false !== stripos( $value['meta_name'], $keyword );
				}

				if ( ! $found ) {
					continue;
				}
			}

			switch( $view ) {
				case 'mla_column':
					$found = $value['mla_column'];
					break;
				case 'quick_edit':
					$found = $value['quick_edit'];
					break;
				case 'bulk_edit':
					$found = $value['bulk_edit'];
					break;
				case 'read_only':
					$found = $value['read_only'];
					break;
				default:
					$found = true;
			}// $view

			if ( ! $found ) {
				continue;
			}

			switch( $status ) {
				case 'active':
					$found = $value['active'];
					break;
				case 'inactive':
					$found = ! $value['active'];
					break;
				default:
					$found = true;
			}// $view

			if ( ! $found ) {
				continue;
			}

			switch ( $request['orderby'] ) {
				case 'name':
					$sortable_items[ ( empty( $value['name'] ) ? chr(1) : $value['name'] ) . $ID ] = (object) $value;
					break;
				case 'rule_name':
					$sortable_items[ ( empty( $value['rule_name'] ) ? chr(1) : $value['rule_name'] ) . $ID ] = (object) $value;
					break;
				case 'data_source':
					$sortable_items[ ( empty( $value['data_source'] ) ? chr(1) : $value['data_source'] ) . $ID ] = (object) $value;
					break;
				case 'status':
					$sortable_items[ ( $value['active'] ? chr(1) : chr(2) ) . $ID ] = (object) $value;
					break;
				case 'existing_text':
					$sortable_items[ ( $value['keep_existing'] ? chr(1) : chr(2) ) . $ID ] = (object) $value;
					break;
				case 'delete_null':
					$sortable_items[ ( $value['no_null'] ? chr(1) : chr(2) ) . $ID ] = (object) $value;
					break;
				case 'format':
					$sortable_items[ ( empty( $value['format'] ) ? chr(1) : $value['format'] ) . $ID ] = (object) $value;
					break;
				case 'option':
					$sortable_items[ ( empty( $value['option'] ) ? chr(1) : $value['option'] ) . $ID ] = (object) $value;
					break;
				default:
					$sortable_items[ absint( $ID ) ] = (object) $value;
					break;
			} //orderby
		}

		$sorted_items = array();
		$sorted_keys = array_keys( $sortable_items );
		natcasesort( $sorted_keys );
		foreach ( $sorted_keys as $key ) {
			$sorted_items[] = $sortable_items[ $key ];
		}

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
	 * Get the total number of MLA custom_field_rule objects
	 *
	 * @since 2.50
	 *
	 * @param array	Query variables, e.g., from $_REQUEST
	 *
	 * @return	integer	Number of MLA custom_field_rule objects
	 */
	public static function mla_count_custom_field_rules( $request ) {
		$request = self::_prepare_custom_field_rules_query( $request );
		$results = self::_execute_custom_field_rules_query( $request );
		return count( $results );
	}

	/**
	 * Retrieve MLA custom_field_rule objects for list table display
	 *
	 * @since 2.50
	 *
	 * @param array	query parameters from web page, usually found in $_REQUEST
	 * @param int		number of rows to skip over to reach desired page
	 * @param int		number of rows on each page
	 *
	 * @return	array	MLA custom_field_rule objects
	 */
	public static function mla_query_custom_field_rules( $request, $offset, $count ) {
		$request = self::_prepare_custom_field_rules_query( $request, $offset, $count );
		$results = self::_execute_custom_field_rules_query( $request );
		return $results;
	}

	/**
	 * Find a Custom Field Rule ID given its rule name
	 *
	 * @since 2.50
 	 *
	 * @param string $rule_name MLA Custom Field Rule name.
	 * @return integer Rule ID if the rule exists else zero (0).
	 */
	public static function mla_find_custom_field_rule_ID( $rule_name ) {
		if ( ! self::_get_custom_field_rules() ) {
			return false;
		}

		foreach( self::$_custom_field_rules as $ID => $rule ) {
			if ( $rule_name == $rule['rule_name'] ) {
				return $ID;
			}
		}

		return 0;
	}

	/**
	 * Return the custom field rule names
	 *
	 * @since 2.50
 	 *
	 * @return	array	MLA custom_field_rule name => name
	 */
	public static function mla_custom_field_rule_names() {
		$names = array();

		if ( ! self::_get_custom_field_rules() ) {
			return $names;
		}

		foreach( self::$_custom_field_rules as $ID => $rule ) {
			$names[ $rule['name'] ]['name'] = $rule['name'];
		}

		return $names;
	}

	/**
	 * Find a Custom Field Rule given its ID
	 *
	 * @since 2.50
 	 *
	 * @param integer	$ID MLA Custom Field Rule ID
 	 *
	 * @return	array	MLA custom_field_rule array
	 * @return	boolean	false; MLA custom_field_rule does not exist
	 */
	public static function mla_find_custom_field_rule( $ID ) {
		if ( ! self::_get_custom_field_rules() ) {
			return false;
		}

		if ( isset( self::$_custom_field_rules[ $ID ] ) ) {
			return self::$_custom_field_rules[ $ID ];
		}

		return false;
	}

	/**
	 * Convert a Custom Field Rule to an old-style mapping rule, given its ID
	 *
	 * @since 2.50
 	 *
	 * @param integer|array $rule_ids | array( $IDs )  MLA Custom Field Rule ID(s)
 	 *
	 * @return array MLA custom_field_mapping values (can be empty)
	 * @return boolean false; MLA custom_field_rules do not exist
	 */
	public static function mla_convert_custom_field_rules( $rule_ids ) {
		if ( ! self::_get_custom_field_rules() ) {
			return false;
		}

		if ( is_scalar( $rule_ids ) ) {
			$rule_ids = array( $rule_ids );
		}

		$rules = array();
		foreach( $rule_ids as $id ) {
			$id = absint( $id );
			if ( isset( self::$_custom_field_rules[ $id ] ) ) {
				$new_rule = self::$_custom_field_rules[ $id ];
				$old_rule = array(
					'name' => $new_rule['name'],
					'data_source' => $new_rule['data_source'],
					'keep_existing' => $new_rule['keep_existing'],
					'format' => $new_rule['format'],
					'meta_name' => $new_rule['meta_name'],
					'option' => $new_rule['option'],
					'active' => $new_rule['active'],
				);

				// Convert to "checkbox", i.e. isset() == true
				if ( $new_rule['no_null'] ) {
					$old_rule['no_null'] = $new_rule['no_null'];
				}

				$rules[ $new_rule['rule_name'] ] = $old_rule;
			}
		}

		return $rules;
	}

	/**
	 * Update a Custom Field Rule property given its ID and key.
	 *
	 * @since 2.50
 	 *
	 * @param integer $ID MLA Custom Field Rule ID.
	 * @param string $key MLA Custom Field Rule property.
	 * @param string $value MLA Custom Field Rule new value.
	 * @return boolean true if object exists else false.
	 */
	public static function mla_update_custom_field_rule( $ID, $key, $value ) {
		if ( ! self::_get_custom_field_rules() ) {
			return false;
		}

		if ( isset( self::$_custom_field_rules[ $ID ] ) ) {
			self::$_custom_field_rules[ $ID ][ $key ] = $value;
			return true;
		}

		return false;
	}

	/**
	 * Replace a Custom Field Rule given its value array.
	 *
	 * @since 2.50
 	 *
	 * @param array $value MLA Custom Field Rule new value.
	 * @return boolean true if object exists else false.
	 */
	public static function mla_replace_custom_field_rule( $value ) {
		if ( ! self::_get_custom_field_rules() ) {
			return false;
		}

		if ( isset( self::$_custom_field_rules[ $value['post_ID'] ] ) ) {
			self::$_custom_field_rules[ $value['post_ID'] ] = $value;
			return true;
		}

		return false;
	}

	/**
	 * Insert a Custom Field Rule given its value array.
	 *
	 * @since 2.50
 	 *
	 * @param array $value MLA Custom Field Rule new value.
	 * @return boolean true if addition succeeds else false.
	 */
	public static function mla_add_custom_field_rule( $value ) {
		if ( ! self::_get_custom_field_rules() ) {
			return false;
		}

		$value['post_ID'] = ++self::$_custom_field_rule_highest_ID;
		$value['read_only'] = $value['rule_name'] !== $value['name'];
		$value['changed'] = true;
		$value['deleted'] = false;

		self::$_custom_field_rules[ $value['post_ID'] ] = $value;
		return true;
	}

	/**
	 * Tabulate MLA custom_field_rule objects by view for list table display
	 *
	 * @since 2.50
	 *
	 * @param string	keyword search criterion, optional
	 *
	 * @return	array	( 'singular' label, 'plural' label, 'count' of items )
	 */
	public static function mla_tabulate_custom_field_items( $s = '' ) {
		if ( empty( $s ) ) {
			$request = array( 'mla_custom_field_view' => 'all' );
		} else {
			$request = array( 's' => $s );
		}

		$items = self::mla_query_custom_field_rules( $request, 0, 0 );

		$template_items = array(
			'all' => array(
				'singular' => _x( 'All', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'All', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'mla_column' => array(
				'singular' => _x( 'MLA Column', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'MLA Column', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'quick_edit' => array(
				'singular' => _x( 'Quick Edit', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Quick Edit', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'bulk_edit' => array(
				'singular' => _x( 'Bulk Edit', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Bulk Edit', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'read_only' => array(
				'singular' => _x( 'Read Only', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Read Only', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
		);

		foreach ( $items as $value ) {
			$template_items['all']['count']++;

			if ( $value->mla_column ) {
					$template_items[ 'mla_column' ]['count']++;
			}

			if ( $value->quick_edit ) {
					$template_items[ 'quick_edit' ]['count']++;
			}

			if ( $value->bulk_edit ) {
					$template_items[ 'bulk_edit' ]['count']++;
			}

			if ( $value->read_only ) {
					$template_items[ 'read_only' ]['count']++;
			}
		}

		return $template_items;
	}
} // class MLA_Custom_Field_Query

/*
 * Actions are added here, when the source file is loaded, because the mla_compose_custom_field_tab
 * function is called too late to be useful.
 */
add_action( 'admin_enqueue_scripts', 'MLASettings_CustomFields::mla_admin_enqueue_scripts' );
add_action( 'admin_init', 'MLA_Custom_Fields_List_Table::mla_admin_init' );
?>