<?php
/**
 * Manages the Settings/Media Library Assistant IPTC EXIF tab
 *
 * @package Media Library Assistant
 * @since 2.40
 */

/**
 * Class MLA (Media Library Assistant) Settings IPTC EXIF implements the
 * Settings/Media Library Assistant IPTC EXIF tab
 *
 * @package Media Library Assistant
 * @since 2.40
 */
class MLASettings_IPTCEXIF {
	/**
	 * Object name for localizing JavaScript - MLA IPTC EXIF List Table
	 *
	 * @since 2.60
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_EDIT_IPTC_EXIF_OBJECT = 'mla_inline_edit_settings_vars';

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
		if ( empty( $_REQUEST['mla_tab'] ) || 'iptc_exif' !== $_REQUEST['mla_tab'] ) {
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
			'page' => 'mla-settings-menu-iptc_exif',
			'mla_tab' => 'iptc_exif',
			'screen' => 'settings_page_mla-settings-menu-iptc_exif',
			'ajax_action' => MLASettings::JAVASCRIPT_INLINE_MAPPING_IPTC_EXIF_SLUG,
			'fieldsId' => '#mla-display-settings-iptc-exif-tab',
			'totalItems' => $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE `post_type` = 'attachment' AND ( `post_mime_type` LIKE 'image/%' OR `post_mime_type` LIKE 'application/%pdf%' )" )
		);

		wp_enqueue_script( MLASettings::JAVASCRIPT_INLINE_MAPPING_IPTC_EXIF_SLUG,
			MLA_PLUGIN_URL . "js/mla-inline-mapping-scripts{$suffix}.js", 
			array( 'jquery' ), MLACore::CURRENT_MLA_VERSION, false );

		wp_localize_script( MLASettings::JAVASCRIPT_INLINE_MAPPING_IPTC_EXIF_SLUG,
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
			'tab' => 'iptc_exif',
			'fields' => array( 'type', 'name', 'rule_name', 'type', 'iptc_value', 'exif_value', 'iptc_first', 'keep_existing', 'active', 'delimiters', 'parent_options', 'parent', 'format', 'option' ),
			'checkboxes' => array( 'no_null' ),
			'ajax_action' => MLASettings::JAVASCRIPT_INLINE_EDIT_IPTC_EXIF_SLUG,
		);

		wp_enqueue_script( MLASettings::JAVASCRIPT_INLINE_EDIT_IPTC_EXIF_SLUG,
			MLA_PLUGIN_URL . "js/mla-inline-edit-settings-scripts{$suffix}.js", 
			array( 'wp-lists', 'suggest', 'jquery' ), MLACore::CURRENT_MLA_VERSION, false );

		wp_localize_script( MLASettings::JAVASCRIPT_INLINE_EDIT_IPTC_EXIF_SLUG,
			self::JAVASCRIPT_INLINE_EDIT_IPTC_EXIF_OBJECT, $script_variables );
	}

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
		$changed = false;

		// See if the entire tab is disabled
		if ( ! isset( $_REQUEST[ MLA_OPTION_PREFIX . MLACoreOptions::MLA_ALLOW_IPTC_EXIF_MAPPING ] ) ) {
			unset( $_REQUEST[ MLA_OPTION_PREFIX . 'enable_iptc_exif_mapping' ] );
			unset( $_REQUEST[ MLA_OPTION_PREFIX . 'enable_iptc_exif_update' ] );
		}

		// Process any page-level options
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'iptc_exif' == $value['tab'] ) {
				$old_value = MLACore::mla_get_option( $key );
				$option_messages .= MLASettings::mla_update_option_row( $key, $value );
				$changed |= $old_value !== MLACore::mla_get_option( $key );
			}
		}

		// Uncomment this for debugging.
		//$message_list = $option_messages . '<br>';

		if ( $changed ) {
			$message_list .= __( 'IPTC/EXIF mapping settings updated.', 'media-library-assistant' ) . "\r\n";
		} else {
			$message_list .= __( 'IPTC/EXIF no mapping changes detected.', 'media-library-assistant' ) . "\r\n";
		}

		return array( 'message' => $message_list, 'body' => '' );
	} // _save_iptc_exif_settings

	/**
	 * Process IPTC EXIF rule(s) against all image attachments
 	 *
	 * @since 2.60
	 *
	 * @param array | NULL $settings specific iptc_exif_mapping values 
	 * @param integer $offset for chunk mapping 
	 * @param integer $length for chunk mapping
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _process_iptc_exif_mapping( $settings = NULL, $offset = 0, $length = 0 ) {
		global $wpdb;

		if ( NULL == $settings ) {
			$settings = MLACore::mla_get_option( 'iptc_exif_mapping' );
			$source = 'iptc_exif_mapping';
		} else {
			$source = 'iptc_exif_execute';
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

		$query = array( 'orderby' => 'none', 'post_parent' => 'all', 'post_mime_type' => 'image,application/*pdf*' );

		if ( $length > 0 ) {
			$query['numberposts'] = $length;
			$query['offset'] = $offset;
		}

		do_action( 'mla_begin_mapping', $source, NULL );
		$posts = MLAShortcodes::mla_get_shortcode_attachments( 0, $query );

		$examine_count = 0;
		$update_count = 0;
		foreach ( $posts as $key => $post ) {
			$updates = MLAOptions::mla_evaluate_iptc_exif_mapping( $post, 'iptc_exif_mapping', $settings );
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
	} // _process_iptc_exif_mapping

	/**
	 * Add a IPTC EXIF custom field rule from values in $_REQUEST
 	 *
	 * @since 2.60
	 * @uses $_REQUEST for field-level values
	 *
	 * @return string Message(s) reflecting the results of the operation
	 */
	private static function _add_iptc_exif_rule() {
		$mla_iptc_exif_rule = isset( $_REQUEST['mla_iptc_exif_rule'] ) ? $_REQUEST['mla_iptc_exif_rule'] : array();

		// Validate new rule name
		if ( !empty( $mla_iptc_exif_rule['new_field'] ) ) {
			$new_name = $mla_iptc_exif_rule['new_field'];
		} elseif ( !empty( $mla_iptc_exif_rule['new_name'] ) && ( 'none' !== $mla_iptc_exif_rule['new_name'] ) ) {
			$new_name = $mla_iptc_exif_rule['new_name'];
		} else {
			return __( 'ERROR', 'media-library-assistant' ) . __( ': No custom field name selected/entered', 'media-library-assistant' );
		}

		if ( MLA_IPTC_EXIF_Query::mla_find_iptc_exif_rule_ID( $new_name ) ) {
			return __( 'ERROR', 'media-library-assistant' ) . __( ': Rule already exists for the new name', 'media-library-assistant' );
		}

		// Convert checkbox/dropdown controls to booleans
		$mla_iptc_exif_rule['iptc_first'] = '1' === $mla_iptc_exif_rule['iptc_first'];
		$mla_iptc_exif_rule['keep_existing'] = '1' === $mla_iptc_exif_rule['keep_existing'];
		$mla_iptc_exif_rule['no_null'] = isset( $mla_iptc_exif_rule['no_null'] );
		$mla_iptc_exif_rule['active'] = '1' === $mla_iptc_exif_rule['status'];

		$new_rule = array(
			'post_ID' => 0,
			'type' => 'custom',
			'key' => $new_name,
			'rule_name' => $new_name,
			'name' => $new_name,
			'hierarchical' => false,
			'iptc_value' => $mla_iptc_exif_rule['iptc_value'],
			'exif_value' => $mla_iptc_exif_rule['exif_value'],
			'iptc_first' => $mla_iptc_exif_rule['iptc_first'],
			'keep_existing' => $mla_iptc_exif_rule['keep_existing'],
			'format' => $mla_iptc_exif_rule['format'],
			'option' => $mla_iptc_exif_rule['option'],
			'no_null' => $mla_iptc_exif_rule['no_null'],
			'delimiters' => '',
			'parent' => 0,
			'active' => $mla_iptc_exif_rule['active'],
			'read_only' => false,
			'changed' => true,
			'deleted' => false,
		);

		if ( MLA_IPTC_EXIF_Query::mla_add_iptc_exif_rule( $new_rule ) ) {
			return __( 'Rule added', 'media-library-assistant' );
		}

		return __( 'ERROR', 'media-library-assistant' ) . __( ': Rule addition failed', 'media-library-assistant' );
	} // _add_iptc_exif_rule

	/**
	 * Update a IPTC EXIF rule from full-screen Edit Rule values in $_REQUEST
 	 *
	 * @since 2.60
	 * @uses $_REQUEST for field-level values
	 *
	 * @param integer $post_id ID value of rule to update
	 * @param	array	&$template Display templates.
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _update_iptc_exif_rule( $post_id, &$template ) {
		$error_message = '';
		$mla_iptc_exif_rule = isset( $_REQUEST['mla_iptc_exif_rule'] ) ? stripslashes_deep( $_REQUEST['mla_iptc_exif_rule'] ) : array();

		// Validate rule name change
		if ( !empty( $mla_iptc_exif_rule['new_field'] ) ) {
			$new_name = $mla_iptc_exif_rule['new_field'];
		} elseif ( !empty( $mla_iptc_exif_rule['new_name'] ) && ( 'none' !== $mla_iptc_exif_rule['new_name'] ) ) {
			$new_name = $mla_iptc_exif_rule['new_name'];
		} else {
			$new_name = '';
		}

		if ( !empty( $new_name) ) {
			if ( MLA_IPTC_EXIF_Query::mla_find_iptc_exif_rule_ID( $new_name ) ) {
				$error_message = __( 'ERROR', 'media-library-assistant' ) . __( ': Rule already exists for the new name', 'media-library-assistant' );
				$new_name = '';
			}
		} elseif ( $mla_iptc_exif_rule['name'] !== $mla_iptc_exif_rule['rule_name'] ) {
			$error_message =  __( 'ERROR', 'media-library-assistant' ) . __( ': Invalid rule name must be changed', 'media-library-assistant' );
		}

		// Convert checkbox/dropdown controls to booleans
		$mla_iptc_exif_rule['hierarchical'] = '1' === $mla_iptc_exif_rule['hierarchical'];
		$mla_iptc_exif_rule['iptc_first'] = '1' === $mla_iptc_exif_rule['iptc_first'];
		$mla_iptc_exif_rule['keep_existing'] = '1' === $mla_iptc_exif_rule['keep_existing'];
		$mla_iptc_exif_rule['no_null'] = isset( $mla_iptc_exif_rule['no_null'] );
		$mla_iptc_exif_rule['active'] = '1' === $mla_iptc_exif_rule['status'];

		$new_rule = array(
			'post_ID' => $mla_iptc_exif_rule['post_ID'],
			'type' => $mla_iptc_exif_rule['type'],
			'key' => $new_name ? $new_name : $mla_iptc_exif_rule['key'],
			'rule_name' => $new_name ? $new_name : $mla_iptc_exif_rule['rule_name'],
			'name' => $new_name ? $new_name : $mla_iptc_exif_rule['name'],
			'hierarchical' => $mla_iptc_exif_rule['hierarchical'],
			'iptc_value' => $mla_iptc_exif_rule['iptc_value'],
			'exif_value' => $mla_iptc_exif_rule['exif_value'],
			'iptc_first' => $mla_iptc_exif_rule['iptc_first'],
			'keep_existing' => $mla_iptc_exif_rule['keep_existing'],
			'format' => $mla_iptc_exif_rule['format'],
			'option' => $mla_iptc_exif_rule['option'],
			'no_null' => $mla_iptc_exif_rule['no_null'],
			'delimiters' => $mla_iptc_exif_rule['delimiters'],
			'parent' => !empty( $mla_iptc_exif_rule['parent'] ) && ( '-1' !== $mla_iptc_exif_rule['parent'] )  ? absint( $mla_iptc_exif_rule['parent'] ) : 0,
			'active' => $mla_iptc_exif_rule['active'],
			'read_only' => false,
			'changed' => true,
			'deleted' => false,

		);

		if ( empty( $error_message ) ) {
			if ( false === MLA_IPTC_EXIF_Query::mla_replace_iptc_exif_rule( $new_rule ) ) {
				$error_message =  __( 'ERROR', 'media-library-assistant' ) . __( ': Rule update failed', 'media-library-assistant' );
			}
		}

		if ( empty( $error_message ) ) {
			return array( 'message' => __( 'Rule updated', 'media-library-assistant' ), 'body' => '' );
		}

		$page_content = self::_compose_edit_iptc_exif_rule_tab( $new_rule, $template );
		$page_content['message'] = $error_message;
		return $page_content;
	} // _update_iptc_exif_rule

	/**
	 * Delete a IPTC EXIF rule
 	 *
	 * @since 2.60
	 *
	 * @param integer $post_id ID value of rule to delete
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _delete_iptc_exif_rule( $post_id ) {
		$rule = MLA_IPTC_EXIF_Query::mla_find_iptc_exif_rule( $post_id );
		if ( false === $rule ) {
			return "ERROR: _delete_iptc_exif_rule( {$post_id} ) rule not found.";
		}

		MLA_IPTC_EXIF_Query::mla_update_iptc_exif_rule( $post_id, 'deleted', true );
		return sprintf( __( 'Custom Field Rule "%1$s" deleted.', 'media-library-assistant' ), $rule['rule_name'] );
	} // _delete_iptc_exif_rule

	/**
	 * Update a IPTC EXIF rule from Bulk Edit action values in $_REQUEST
 	 *
	 * @since 2.60
	 * @uses $_REQUEST for field-level values
	 *
	 * @param integer $post_id ID value of rule to update
	 * @return string status/error message
	 */
	private static function _bulk_update_iptc_exif_rule( $post_id ) {
		$rule = MLA_IPTC_EXIF_Query::mla_find_iptc_exif_rule( $post_id );
		if ( false === $rule ) {
			return "ERROR: _bulk_update_iptc_exif_rule( {$post_id} ) rule not found.";
		}

		// Convert dropdown controls to field values
		if ( '-1' !== $_REQUEST['iptc_first'] ) {
			$rule['iptc_first'] = '1' === $_REQUEST['iptc_first'];
		}

		if ( '-1' !== $_REQUEST['keep_existing'] ) {
			$rule['keep_existing'] = '1' === $_REQUEST['keep_existing'];
		}

		if ( '-1' !== $_REQUEST['format'] ) {
			$rule['format'] = $_REQUEST['format'];
		}

		if ( '-1' !== $_REQUEST['option'] ) {
			$rule['option'] = $_REQUEST['option'];
		}

		if ( '-1' !== $_REQUEST['no_null'] ) {
			$rule['no_null'] = '1' === $_REQUEST['no_null'];
		}

		if ( '-1' !== $_REQUEST['active'] ) {
			$rule['active'] = '1' === $_REQUEST['active'];
		}

		$rule['changed'] = true;
		$rule['deleted'] = false;

		if ( false === MLA_IPTC_EXIF_Query::mla_replace_iptc_exif_rule( $rule ) ) {
			return  __( 'ERROR', 'media-library-assistant' ) . __( ': Rule update failed', 'media-library-assistant' );
		}

		return __( 'Rule updated', 'media-library-assistant' );
	} // _bulk_update_iptc_exif_rule

	/**
	 * Compose the Edit IPTC EXIF Rule tab content for the Settings/IPTC EXIF subpage
	 *
	 * @since 2.60
	 *
	 * @param	array	$item Data values for the item.
	 * @param	array	&$template Display templates.
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_edit_iptc_exif_rule_tab( $item, &$template ) {
		// An old bug left multiple rules for the same custom field; only one can be active
		if ( $item['name'] === $item['rule_name'] ) {
			$display_name = $item['name'];
		} else {
			$display_name = $item['rule_name'] . ' / ' . $item['name'];
		}

		$page_values = array(
			'Edit Rule' => __( 'Edit Rule', 'media-library-assistant' ) . ': ',
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-iptc_exif&mla_tab=iptc_exif',
			'post_ID' => $item['post_ID'],
			'type' => $item['type'],
			'key' => $item['key'],
			'rule_name' => $item['rule_name'],
			'hierarchical' => '0',
			'name' => $item['name'],
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'Name' => __( 'Name', 'media-library-assistant' ),
			'display_name' => esc_html( $display_name ),
			'new_names' => '',
			'Enter Name' => __( 'This is the name of the custom field to which the rule applies.<br>Only one rule is allowed for each custom field.', 'media-library-assistant' ),
			'Change Name' => __( 'Change Name', 'media-library-assistant' ),
			'Cancel Name Change' => __( 'Cancel Name Change', 'media-library-assistant' ),
			'Enter new field' => __( 'Enter new field', 'media-library-assistant' ),
			'Cancel new field' => __( 'Cancel new field', 'media-library-assistant' ),
			'IPTC Value' => __( 'IPTC Value', 'media-library-assistant' ),
			'iptc_field_options' => MLAOptions::mla_compose_iptc_option_list( $item['iptc_value'] ),
			'EXIF/Template Value' => __( 'EXIF/Template Value', 'media-library-assistant' ),
			'exif_size' => MLACoreOptions::MLA_EXIF_SIZE,
			'exif_text' => esc_attr( $item['exif_value'] ),
			'Enter EXIF/Template' => __( 'EXIF element name or Content Template', 'media-library-assistant' ),
			'Priority' => __( 'Priority', 'media-library-assistant' ),
			'iptc_selected' => '', // Set below
			'IPTC' => __( 'IPTC', 'media-library-assistant' ),
			'exif_selected' => '', // Set below
			'EXIF' => __( 'EXIF', 'media-library-assistant' ),
			'Existing Text' => __( 'Existing Text', 'media-library-assistant' ),
			'keep_selected' => '', // Set below
			'Keep' => __( 'Keep', 'media-library-assistant' ),
			'replace_selected' => '', // Set below
			'Replace' => __( 'Replace', 'media-library-assistant' ),
			// Taxonomy values
			'taxonomy_class' => 'hidden', // Set below
			'Delimiters' => __( 'Delimiters', 'media-library-assistant' ),
			'delimiters_size' => 4,
			'delimiters_text' => $item['delimiters'],
			'Parent' => __( 'Parent', 'media-library-assistant' ),
			'parent_class' => 'hidden', // Set below
			'parent_select' => '', // Set below
			// Custom Field values
			'custom_class' => 'hidden', // Set below
			'Format' => __( 'Format', 'media-library-assistant' ),
			'native_format' => '', // Set below
			'Native' => __( 'Native', 'media-library-assistant' ),
			'commas_format' => '', // Set below
			'Commas' => __( 'Commas', 'media-library-assistant' ),
			'raw_format' => '', // Set below
			'Raw' => __( 'Raw', 'media-library-assistant' ),
			'Option' => __( 'Option', 'media-library-assistant' ),
			'text_option' => '', // Set below
			'Text' => __( 'Text', 'media-library-assistant' ),
			'single_option' => '', // Set below
			'Single' => __( 'Single', 'media-library-assistant' ),
			'export_option' => '', // Set below
			'Export' => __( 'Export', 'media-library-assistant' ),
			'array_option' => '', // Set below
			'Array' => __( 'Array', 'media-library-assistant' ),
			'multi_option' => '', // Set below
			'Multi' => __( 'Multi', 'media-library-assistant' ),
			'no_null_checked' => '', // Set below
			'Delete NULL' => __( 'Delete NULL values', 'media-library-assistant' ),
			'Check Delete NULL' => __( 'Do not store empty custom field values', 'media-library-assistant' ),
			// Common values
			'Status' => __( 'Status', 'media-library-assistant' ),
			'active_selected' => $item['active'] ? 'selected=selected' : '',
			'Active' => __( 'Active', 'media-library-assistant' ),
			'inactive_selected' => $item['active'] ? '' : 'selected=selected',
			'Inactive' => __( 'Inactive', 'media-library-assistant' ),
			'cancel' => 'mla-edit-iptc-exif-cancel',
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
			'submit' => 'mla-edit-iptc-exif-submit',
			'Update' => __( 'Update', 'media-library-assistant' ),
		);

		if ( $item['iptc_first'] ) {
			$page_values['iptc_selected'] = 'selected="selected"';
		} else {
			$page_values['exif_selected'] = 'selected="selected"';
		}

		if ( $item['keep_existing'] ) {
			$page_values['keep_selected'] = 'selected="selected"';
		} else {
			$page_values['replace_selected'] = 'selected="selected"';
		}

		switch ( $item['type'] ) {
			case 'standard':
				$page_values['Edit Rule'] .= __( 'Standard field mapping', 'media-library-assistant' );
				break;
			case 'taxonomy':
				$page_values['Edit Rule'] .= __( 'Taxonomy term mapping', 'media-library-assistant' );
				$page_values['taxonomy_class'] = '';

				if ( $item['hierarchical'] ) {
					$page_values['hierarchical'] = '1';
					$page_values['parent_class'] = '';
					$select_values = array (
						'options' => MLAOptions::mla_compose_parent_option_list( $item['key'], $item['parent'] )
					);
					$page_values['parent_select'] = MLAData::mla_parse_template( $template['parent-select'], $select_values );
				} else {
					$page_values['hierarchical'] = '0';
				}
				break;
			case 'custom':
				$page_values['Edit Rule'] .= __( 'Custom field mapping', 'media-library-assistant' );
				$page_values['custom_class'] = '';
				$page_values['new_names'] = MLAOptions::mla_compose_custom_field_option_list( '', MLA_IPTC_EXIF_Query::mla_iptc_exif_rule_names() );
				switch( $item['format'] ) {
					case 'commas':
						$page_values['commas_format'] = 'selected="selected"';
						break;
					case 'raw':
						$page_values['raw_format'] = 'selected="selected"';
						break;
					default:
						$page_values['native_format'] = 'selected="selected"';
				} // format

				switch( $item['option'] ) {
					case 'single':
						$page_values['single_option'] = 'selected="selected"';
						break;
					case 'export':
						$page_values['export_option'] = 'selected="selected"';
						break;
					case 'array':
						$page_values['array_option'] = 'selected="selected"';
						break;
					case 'multi':
						$page_values['multi_option'] = 'selected="selected"';
						break;
					default:
						$page_values['text_option'] = 'selected="selected"';
				} // option

				if ( $item['no_null'] ) {
					$page_values['no_null_checked'] = 'checked="checked"';
				}
				break;
			default:
				$page_values['Edit Rule'] .= '(unknown type)';
		}

		return array(
			'message' => '',
			'body' => MLAData::mla_parse_template( $template['single-item-edit'], $page_values )
		);
	}

	/**
	 * Purge one or more custom field values for Bulk action
	 *
	 * @since 2.60
	 *
	 * @param array $rule_ids ID value of rule(s), to get field names
	 *
	 * @return array Message(s) reflecting the results of the operation
	 */
	private static function _purge_custom_field_values( $rule_ids ) {
		$message = '';
		$source_rules = MLA_IPTC_EXIF_Query::mla_convert_iptc_exif_rules( $rule_ids );
		foreach ( $source_rules['custom'] as $rule_name => $rule ) {
			$result = MLASettings::mla_delete_custom_field( $rule );
			$message .=  sprintf( __( 'Custom Field Rule "%1$s": %2$s', 'media-library-assistant' ), $rule['name'], $result );
		}

		return $message;
	}

	/**
	 * Compose the IPTC EXIF tab content for the Settings subpage
	 *
	 * @since 1.10
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	public static function mla_compose_iptc_exif_tab( ) {
		$page_content = array( 'message' => '', 'body' => '' );
		$page_template_array = MLACore::mla_load_template( 'admin-display-settings-iptc-exif-tab.tpl' );
		if ( ! is_array( $page_template_array ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			$page_content['message'] = sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLASettings_IPTCEXIF::mla_compose_iptc_exif_tab', var_export( $page_template_array, true ) );
			return $page_content;
		}

		// Initialize page messages and content, check for page-level Save Changes, Add/Update/Cancel Rule
		if ( !empty( $_REQUEST['mla-iptc-exif-options-save'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_save_iptc_exif_settings( );
		} elseif ( !empty( $_REQUEST['mla-add-iptc-exif-submit'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content['message'] = MLASettings_IPTCEXIF::_add_iptc_exif_rule();
			MLA_IPTC_EXIF_Query::mla_put_iptc_exif_rules();
		} elseif ( !empty( $_REQUEST['mla-edit-iptc-exif-submit'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = MLASettings_IPTCEXIF::_update_iptc_exif_rule( $_REQUEST['mla_iptc_exif_rule']['post_ID'], $page_template_array );
			MLA_IPTC_EXIF_Query::mla_put_iptc_exif_rules();
		} elseif ( !empty( $_REQUEST['mla-edit-iptc-exif-cancel'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content['message'] = __( 'Edit IPTC EXIF Rule cancelled.', 'media-library-assistant' );
		}

		if ( !empty( $page_content['body'] ) ) {
			return $page_content;
		}

		// Process bulk actions that affect an array of items
		$bulk_action = MLASettings::mla_current_bulk_action();
		if ( $bulk_action && ( $bulk_action != 'none' ) ) {
			if ( array_key_exists( $bulk_action, MLA_IPTC_EXIF_List_Table::mla_get_bulk_actions() ) ) {
				if ( isset( $_REQUEST['cb_mla_item_ID'] ) ) {
					if ( 'execute' === $bulk_action ) {
						$page_content['message'] = sprintf( __( 'Unknown bulk action %1$s', 'media-library-assistant' ), $bulk_action );
					} elseif ( 'purge' === $bulk_action ) {
						$page_content['message'] = MLASettings_IPTCEXIF::_purge_custom_field_values( $_REQUEST['cb_mla_item_ID'] );
					} else {
						foreach ( $_REQUEST['cb_mla_item_ID'] as $post_ID ) {
							switch ( $bulk_action ) {
								case 'edit':
									$item_content = MLASettings_IPTCEXIF::_bulk_update_iptc_exif_rule( $post_ID );
									break;
								case 'delete':
									$item_content = MLASettings_IPTCEXIF::_delete_iptc_exif_rule( $post_ID );
									break;
								default:
									$item_content = 'Bad action'; // UNREACHABLE
							} // switch $bulk_action

							$page_content['message'] .= $item_content . '<br>';
						} // foreach cb_attachment

						MLA_IPTC_EXIF_Query::mla_put_iptc_exif_rules();
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

		/*
		 * Process row-level actions that affect a single item
		 */
		if ( !empty( $_REQUEST['mla_admin_action'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

			$page_content = array( 'message' => '', 'body' => '' );

			switch ( $_REQUEST['mla_admin_action'] ) {
				case MLACore::MLA_ADMIN_SINGLE_EDIT_DISPLAY:
					$item = MLA_IPTC_EXIF_Query::mla_find_iptc_exif_rule( $_REQUEST['mla_item_ID'] );
					$page_content = self::_compose_edit_iptc_exif_rule_tab( $item, $page_template_array );
					break;
				case MLACore::MLA_ADMIN_SINGLE_PURGE:
					$page_content['message'] = MLASettings_IPTCEXIF::_purge_custom_field_values( $_REQUEST['mla_item_ID'] );
					break;
				case MLACore::MLA_ADMIN_SINGLE_DELETE:
					$page_content['message'] = MLASettings_IPTCEXIF::_delete_iptc_exif_rule( $_REQUEST['mla_item_ID'] );
					MLA_IPTC_EXIF_Query::mla_put_iptc_exif_rules();
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
		if ( 'checked' != MLACore::mla_get_option( MLACoreOptions::MLA_ALLOW_IPTC_EXIF_MAPPING ) ) {
			// Fill in the page-level option
			$options_list = '';
			foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
				if ( MLACoreOptions::MLA_ALLOW_IPTC_EXIF_MAPPING == $key ) {
					$options_list .= MLASettings::mla_compose_option_row( $key, $value );
				}
			}

			$page_values = array(
				'Support is disabled' => __( 'IPTC/EXIF Mapping Support is disabled', 'media-library-assistant' ),
				'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-iptc_exif&mla_tab=iptc_exif',
				'options_list' => $options_list,
				'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
				'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			);

			$page_content['body'] .= MLAData::mla_parse_template( $page_template_array['iptc-exif-disabled'], $page_values );
			return $page_content;
		}

		// Display the IPTC EXIF tab and the IPTC EXIF rule table
		$_SERVER['REQUEST_URI'] = remove_query_arg( array(
			'mla_admin_action',
			'mla_iptc_exif_item',
			'mla_item_ID',
			'_wpnonce',
			'_wp_http_referer',
			'action',
			'action2',
			'cb_mla_item_ID',
			'mla-edit-iptc-exif-cancel',
			'mla-edit-iptc-exif-submit',
			'mla-iptc-exif-options-save',
		), $_SERVER['REQUEST_URI'] );

		// Create an instance of our package class
		$MLAIPTCEXIFListTable = new MLA_IPTC_EXIF_List_Table();

		//	Fetch, prepare, sort, and filter our data
		$MLAIPTCEXIFListTable->prepare_items();

		// Start with any page-level options
		$options_list = '';
		foreach ( MLACoreOptions::$mla_option_definitions as $key => $value ) {
			if ( 'iptc_exif' == $value['tab'] ) {
				$options_list .= MLASettings::mla_compose_option_row( $key, $value );
			}
		}

		// WPML requires that lang be the first argument after page
		$view_arguments = MLA_IPTC_EXIF_List_Table::mla_submenu_arguments();
		$form_language = isset( $view_arguments['lang'] ) ? '&lang=' . $view_arguments['lang'] : '';
		$form_arguments = '?page=mla-settings-menu-iptc_exif' . $form_language . '&mla_tab=iptc_exif';

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
			$page_content['message'] = sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLASettings_CustomFields::mla_compose_iptc_exif_tab', var_export( $progress_template_array, true ) );
			return $page_content;
		}

		$page_values = array(
			'Mapping Progress' => __( 'IPTC &amp; EXIF Mapping Progress', 'media-library-assistant' ),
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
			'refresh_href' => '?page=mla-settings-menu-iptc_exif&mla_tab=iptc_exif',
		);

		$progress_div = MLAData::mla_parse_template( $progress_template_array['mla-progress-div'], $page_values );

		$page_values = array(
			'mla-progress-div' => $progress_div,
			'IPTC EXIF Options' => __( 'IPTC &amp; EXIF Processing Options', 'media-library-assistant' ),
			/* translators: 1: Documentation hyperlink */
			'In this tab' => __( 'In this tab you can define the rules for mapping IPTC (International Press Telecommunications Council) and EXIF (EXchangeable Image File) metadata to WordPress standard attachment fields, taxonomy terms and custom fields.', 'media-library-assistant' ),
			/* translators: 1: Documentation hyperlink */
			'You can find' => __( 'You can find more information about using the controls in this tab to define mapping rules and apply them by clicking the "Help" control in the upper-right corner of the screen.', 'media-library-assistant' ),
			'settingsURL' => admin_url('options-general.php'),
			'form_url' => admin_url( 'options-general.php' ) . $form_arguments,
			'view_args' => $view_args,
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'results' => ! empty( $_REQUEST['s'] ) ? '<span class="alignright" style="margin-top: .5em; font-weight: bold">' . __( 'Search results for', 'media-library-assistant' ) . ':&nbsp;</span>' : '',
			'Search Rules Text' => __( 'Search Rules Text', 'media-library-assistant' ),
			's' => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
			'Search Rules' => __( 'Search Rules', 'media-library-assistant' ),
			'options_list' => $options_list,
			'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
			'Map All' => __( 'Execute All Rules', 'media-library-assistant' ),
			'Add New Rule' => __( 'Add New Custom Field Rule', 'media-library-assistant' ),
			'Quick Edit' => __( 'Quick Edit', 'media-library-assistant' ),
			'Bulk Edit' => __( 'Bulk Edit', 'media-library-assistant' ),
			'Name' => __( 'Name', 'media-library-assistant' ),
			'new_names' => MLAOptions::mla_compose_custom_field_option_list( 'none', MLA_IPTC_EXIF_Query::mla_iptc_exif_rule_names() ),
			'Enter new field' => __( 'Enter new field', 'media-library-assistant' ),
			'Cancel new field' => __( 'Cancel new field', 'media-library-assistant' ),
			'IPTC Value' => __( 'IPTC Value', 'media-library-assistant' ),
			'iptc_field_options' => MLAOptions::mla_compose_iptc_option_list( 'none' ),
			'EXIF/Template Value' => __( 'EXIF/Template Value', 'media-library-assistant' ),
			'exif_size' => MLACoreOptions::MLA_EXIF_SIZE,
			'exif_text' => '',
			'Enter EXIF/Template' => __( 'EXIF element name or Content Template', 'media-library-assistant' ),
			'Priority' => __( 'Priority', 'media-library-assistant' ),
			'iptc_selected' => 'selected="selected"',
			'IPTC' => __( 'IPTC', 'media-library-assistant' ),
			'exif_selected' => '',
			'EXIF' => __( 'EXIF', 'media-library-assistant' ),
			'Existing Text' => __( 'Existing Text', 'media-library-assistant' ),
			'keep_selected' => '',
			'Keep' => __( 'Keep', 'media-library-assistant' ),
			'replace_selected' => '',
			'Replace' => __( 'Replace', 'media-library-assistant' ),
			'Delimiters' => __( 'Delimiters', 'media-library-assistant' ),
			'delimiters_size' => 4,
			'Parent' => __( 'Parent', 'media-library-assistant' ),
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
			// Inline edit areas
			'No Change' => __( 'No Change', 'media-library-assistant' ),
			'Yes' => __( 'Yes', 'media-library-assistant' ),
			'No' => __( 'No', 'media-library-assistant' ),
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
			'Update' => __( 'Update', 'media-library-assistant' ),
		);

		$page_content['body'] = MLAData::mla_parse_template( $page_template_array['before-table'], $page_values );

		//	 Now we can render the completed list table
		ob_start();
		$MLAIPTCEXIFListTable->views();
		$MLAIPTCEXIFListTable->display();
		$page_content['body'] .= ob_get_contents();
		ob_end_clean();

		$page_content['body'] .= MLAData::mla_parse_template( $page_template_array['after-table'], $page_values );

		return $page_content;
	} // mla_compose_iptc_exif_tab

	/**
	 * Ajax handler for IPTC EXIF tab inline mapping
	 *
	 * @since 2.00
	 *
	 * @return	void	echo json response object, then die()
	 */
	public static function mla_inline_mapping_iptc_exif_action() {
		MLACore::mla_debug_add( 'MLASettings_IPTCEXIF::mla_inline_mapping_custom_action $_REQUEST = ' . var_export( $_REQUEST, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
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
		if ( ! empty( $_REQUEST['bulk_action'] ) && ( 'iptc-exif-options-map' == $_REQUEST['bulk_action'] ) ) {
			$page_content = self::_process_iptc_exif_mapping( NULL, $offset, $length );
		}
		elseif ( ! empty( $_REQUEST['bulk_action'] ) ) {
			$source_rules = NULL;

			if ( 'mapping-options-bulk-execute' == $_REQUEST['bulk_action'] ) {
				$source_rules = MLA_IPTC_EXIF_Query::mla_convert_iptc_exif_rules( $_REQUEST['ids'] );
			}
			elseif ( 0 === strpos( $_REQUEST['bulk_action'], MLACore::MLA_ADMIN_SINGLE_MAP ) ) {
				$match_count = preg_match( '/\[(.*)\]/', $_REQUEST['bulk_action'], $matches );
				if ( $match_count ) {
					$post_id = absint( $matches[1] );
					$source_rules = MLA_IPTC_EXIF_Query::mla_convert_iptc_exif_rules( $post_id );
				}
			}

			if ( is_array( $source_rules ) ) {
				$no_rules = true;			
				foreach ( $source_rules as $type => $rules ) {
					foreach( $rules as $key => $rule ) {
						$no_rules = false;
						$source_rules[ $type ][ $key ]['active'] = true; // Always execute for rollover action
					}
				}

				if ( $no_rules ) {
					$page_content['message'] = __( 'Nothing to execute', 'media-library-assistant' );
				} else {
					$page_content = self::_process_iptc_exif_mapping( $source_rules, $offset, $length );
				}
			}
		} // found bulk_action

		$chunk_results = array( 
			'message' => $page_content['message'],
			'processed' => $page_content['processed'],
			'unchanged' => $page_content['unchanged'],
			'success' => $page_content['success'],
			'refresh' => isset( $page_content['refresh'] ) && true == $page_content['refresh'],
		);

		MLACore::mla_debug_add( 'MLASettings::mla_inline_mapping_custom_action $chunk_results = ' . var_export( $chunk_results, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		wp_send_json_success( $chunk_results );
	} // mla_inline_mapping_iptc_exif_action

	/**
	 * Ajax handler for IPTC EXIF inline editing (quick edit)
	 *
	 * Adapted from wp_ajax_inline_save in /wp-admin/includes/ajax-actions.php
	 *
	 * @since 2.60
	 *
	 * @return	void	echo HTML <tr> markup for updated row or error message, then die()
	 */
	public static function mla_inline_edit_iptc_exif_action() {
		set_current_screen( $_REQUEST['screen'] );

		check_ajax_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

		$error_message = '';
		if ( empty( $_REQUEST['post_ID'] ) ) {
			$error_message = __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Rule ID not found', 'media-library-assistant' );
		} else {
			$rule = MLA_IPTC_EXIF_Query::mla_find_iptc_exif_rule( $_REQUEST['post_ID'] );
			if ( false === $rule ) {
				$error_message = __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Rule not found', 'media-library-assistant' );
			}
		}

		if ( !empty( $error_message ) ) {
			echo $error_message;
			die();
		}

		$rule['iptc_value'] = $_REQUEST['iptc_value'];
		$rule['exif_value'] = $_REQUEST['exif_value'];
		$rule['iptc_first'] = '1' === $_REQUEST['iptc_first'];
		$rule['keep_existing'] = '1' === $_REQUEST['keep_existing'];
		$rule['delimiters'] = !empty( $_REQUEST['delimiters'] ) ? $_REQUEST['delimiters'] : '';
		$rule['parent'] = !empty( $_REQUEST['parent'] ) ? absint( $_REQUEST['parent'] ) : 0;
		$rule['format'] = $_REQUEST['format'];
		$rule['option'] = $_REQUEST['option'];
		$rule['no_null'] = isset( $_REQUEST['no_null'] ) && '1' === $_REQUEST['no_null'];
		$rule['active'] = '1' === $_REQUEST['active'];
		$rule['changed'] = true;
		$rule['deleted'] = false;
		$rule = stripslashes_deep( $rule );

		if ( false === MLA_IPTC_EXIF_Query::mla_replace_iptc_exif_rule( $rule ) ) {
			echo __( 'ERROR', 'media-library-assistant' ) . __( ': Rule update failed', 'media-library-assistant' );
			die();
		}

		MLA_IPTC_EXIF_Query::mla_put_iptc_exif_rules();

		//	Create an instance of our package class and echo the new HTML
		$MLAListCustomTable = new MLA_IPTC_EXIF_List_Table();
		$MLAListCustomTable->single_row( (object) $rule );
		die(); // this is required to return a proper result
	} // mla_inline_edit_iptc_exif_action
} // class MLASettings_IPTCEXIF

/* 
 * The WP_List_Table class isn't automatically available to plugins
 */
if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class MLA (Media Library Assistant) IPTC EXIF List Table implements the "IPTC EXIF"
 * admin settings submenu table
 *
 * Extends the core WP_List_Table class.
 *
 * @package Media Library Assistant
 * @since 2.60
 */
class MLA_IPTC_EXIF_List_Table extends WP_List_Table {
	/**
	 * Initializes some properties from $_REQUEST variables, then
	 * calls the parent constructor to set some default configs.
	 *
	 * @since 2.60
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
			'singular' => 'iptc_exif', //singular name of the listed records
			'plural' => 'iptc_exif', //plural name of the listed records
			'ajax' => true, //does this table support ajax?
			'screen' => 'settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-iptc_exif'
		) );

		// NOTE: There are two add_action calls at the end of this source file.
	}

	/**
	 * Table column definitions
	 *
	 * This array defines table columns and titles where the key is the column slug (and class)
	 * and the value is the column's title text.
	 * 
	 * All of the columns are added to this array by MLA_IPTC_EXIF_List_Table::_localize_default_columns_array.
	 *
	 * @since 2.60
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
	 * @since 2.60
	 *
	 * @var	array
	 */
	private static $default_hidden_columns	= array(
		// 'name',
		'rule_name',
		// 'iptc_value',
		// 'exif_value',
		// 'priority',
		// 'existing_text',
		// 'status',
		'delimiters',
		'parent',
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
	 * @since 2.60
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
		'iptc_value' => array('iptc_value',false),
		'exif_value' => array('exif_value',false),
		'priority' => array('priority',false),
		'existing_text' => array('existing_text',false),
		'status' => array('status',false),
		'delimiters' => array('delimiters',false),
		'parent' => array('parent',false),
		'delete_null' => array('delete_null',false),
		'format' => array('format',false),
		'option' => array('option',false),
		);

	/**
	 * Access the default list of hidden columns
	 *
	 * @since 2.60
	 *
	 * @return	array	default list of hidden columns
	 */
	private static function _default_hidden_columns( ) {
		return self::$default_hidden_columns;
	}

	/**
	 * Return the names and orderby values of the sortable columns
	 *
	 * @since 2.60
	 *
	 * @return	array	column_slug => array( orderby value, initial_descending_sort ) for sortable columns
	 */
	public static function mla_get_sortable_columns( ) {
		return self::$default_sortable_columns;
	}

	/**
	 * Process $_REQUEST, building $submenu_arguments
	 *
	 * @since 2.60
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
		if ( isset( $_REQUEST['mla_iptc_exif_view'] ) ) {
			$submenu_arguments['mla_iptc_exif_view'] = $_REQUEST['mla_iptc_exif_view'];
		}

		// Search box arguments
		if ( !empty( $_REQUEST['s'] ) ) {
			$submenu_arguments['s'] = urlencode( stripslashes( $_REQUEST['s'] ) );
		}

		// Filter arguments (from table header)
		if ( isset( $_REQUEST['mla_iptc_exif_status'] ) && ( 'any' != $_REQUEST['mla_iptc_exif_status'] ) ) {
			$submenu_arguments['mla_iptc_exif_status'] = $_REQUEST['mla_iptc_exif_status'];
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
	 * Handler for filter 'get_user_option_managesettings_page_mla-settings-menu-iptc_exifcolumnshidden'
	 *
	 * Required because the screen.php get_hidden_columns function only uses
	 * the get_user_option result. Set when the file is loaded because the object
	 * is not created in time for the call from screen.php.
	 *
	 * @since 2.60
	 *
	 * @param mixed	false or array with current list of hidden columns, if any
	 * @param string	'managesettings_page_mla-settings-menu-iptc_exifcolumnshidden'
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
	 * @since 2.60
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
	 * @since 2.60
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
				'iptc_value' => _x( 'IPTC Value', 'list_table_column', 'media-library-assistant' ),
				'exif_value' => _x( 'EXIF/Template Value', 'list_table_column', 'media-library-assistant' ),
				'priority' => _x( 'Priority ', 'list_table_column', 'media-library-assistant' ),
				'existing_text'  => _x( 'Existing Text', 'list_table_column', 'media-library-assistant' ),
				'status' => _x( 'Status', 'list_table_column', 'media-library-assistant' ),
				'delimiters' => _x( 'Delimiter(s)', 'list_table_column', 'media-library-assistant' ),
				'parent' => _x( 'Parent', 'list_table_column', 'media-library-assistant' ),
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
	 * @since 2.60
	 *
	 * @return	void
	 */
	public static function mla_admin_init( ) {
		if ( isset( $_REQUEST['mla_tab'] ) && $_REQUEST['mla_tab'] == 'iptc_exif' ) {
			add_filter( 'get_user_option_managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-iptc_exifcolumnshidden', 'MLA_IPTC_EXIF_List_Table::mla_manage_hidden_columns_filter', 10, 3 );
			add_filter( 'manage_settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-iptc_exif_columns', 'MLA_IPTC_EXIF_List_Table::mla_manage_columns_filter', 10, 0 );
		}
	}

	/**
	 * Checks the current user's permissions
	 *
	 * @since 2.60
	 *
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can('manage_options');
	}

	/**
	 * Get the name of the default primary column.
	 *
	 * @since 2.60
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
	 * @since 2.60
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
	 * @since 2.60
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
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
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
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
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
			'page' => MLACoreOptions::MLA_SETTINGS_SLUG . '-iptc_exif',
			'mla_tab' => 'iptc_exif',
			'mla_item_ID' => urlencode( $item->post_ID )
		), MLA_IPTC_EXIF_List_Table::mla_submenu_arguments() );

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
		MLACore::MLA_ADMIN_SINGLE_MAP . '[' . $item->post_ID . ']" href="#" title="' . __( 'Map All Attachments', 'media-library-assistant' ) . '">' . __( 'Execute', 'media-library-assistant' ) . '</a>';

			if ( 'custom' === $item->type ) {
				$actions['purge'] = '<a class="purge"' . ' href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_PURGE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Purge IPTC EXIF values', 'media-library-assistant' ) . '">' . __( 'Purge Values', 'media-library-assistant' ) . '</a>';
			}
		}

		$actions['delete'] = '<a class="delete-tag"' . ' href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_DELETE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Delete this item Permanently', 'media-library-assistant' ) . '">' . __( 'Delete Permanently', 'media-library-assistant' ) . '</a>';

		return $actions;
	}

	/**
	 * Add hidden fields with the data for use in the inline editor
	 *
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
	 *
	 * @return	string	HTML <div> with row data
	 */
	private function _build_inline_data( $item ) {
		$inline_data = "\r\n" . '<div class="hidden" id="inline_' . $item->post_ID . "\">\r\n";
		$inline_data .= '	<div class="type">' . esc_attr( $item->type ) . "</div>\r\n";
		$inline_data .= '	<div class="name">' . esc_attr( $item->name ) . "</div>\r\n";
		$inline_data .= '	<div class="slug">' . esc_attr( $item->key ) . "</div>\r\n";
		$inline_data .= '	<div class="rule_name">' . esc_attr( $item->rule_name ) . "</div>\r\n";
		$inline_data .= '	<div class="hierarchical">' . esc_attr( $item->hierarchical ) . "</div>\r\n";
		$inline_data .= '	<div class="iptc_value">' . esc_attr( $item->iptc_value ) . "</div>\r\n";
		$inline_data .= '	<div class="exif_value">' . esc_attr( $item->exif_value ) . "</div>\r\n";
		$inline_data .= '	<div class="iptc_first">' . esc_attr( $item->iptc_first ) . "</div>\r\n";
		$inline_data .= '	<div class="keep_existing">' . esc_attr( $item->keep_existing ) . "</div>\r\n";
		$inline_data .= '	<div class="active">' . esc_attr( $item->active ) . "</div>\r\n";

		$inline_data .= '	<div class="delimiters">' . esc_attr( $item->delimiters ) . "</div>\r\n";
		$inline_data .= '	<div class="parent">' . esc_attr( $item->parent ) . "</div>\r\n";

		if ( $item->hierarchical ) {
			$inline_data .= '	<div class="parent_options">' . MLAOptions::mla_compose_parent_option_list( $item->key, $item->parent ) . "</div>\r\n";
		}

		$inline_data .= '	<div class="format">' . esc_attr( $item->format ) . "</div>\r\n";
		$inline_data .= '	<div class="option">' . esc_attr( $item->option ) . "</div>\r\n";
		$inline_data .= '	<div class="no_null">' . esc_attr( $item->no_null ) . "</div>\r\n";
		$inline_data .= "</div>\r\n";
		return $inline_data;
	}

	/**
	 * Populate the Name column
	 *
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
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
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_rule_name( $item ) {
		return ( $item->name !== $item->rule_name ) ? esc_html( $item->rule_name ) : '';
	}

	/**
	 * Populate the IPTC Value column
	 *
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_iptc_value( $item ) {
		$iptc_text = $item->iptc_value;
		if ( array_key_exists( $item->iptc_value, MLAData::$mla_iptc_records ) ) {
			$iptc_text .= ':<br>' . esc_html( MLAData::$mla_iptc_records[ $item->iptc_value ] );
		}

		return $iptc_text;
	}

	/**
	 * Populate the EXIF/Template Value column
	 *
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_exif_value( $item ) {
		return esc_html( $item->exif_value );
	}

	/**
	 * Populate the Priority column
	 *
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_priority( $item ) {
		if ( $item->iptc_first ) {
			return __( 'IPTC', 'media-library-assistant' );
		}

		return __( 'EXIF', 'media-library-assistant' );
	}

	/**
	 * Populate the Existing Text column
	 *
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
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
	 * Populate the Status column
	 *
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
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
	 * Populate the Delimiters column
	 *
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_delimiters( $item ) {
		return esc_html( $item->delimiters );
	}

	/**
	 * Populate the Parent column
	 *
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_parent( $item ) {
		if ( 0 < absint( $item->parent ) ) {
			$term = get_term( $item->parent, $item->key );
			return esc_html( $term->name );
		}

		return '';
	}

	/**
	 * Populate the Delete NULL column
	 *
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
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
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_format( $item ) {
		return $item->format;
	}

	/**
	 * Populate the Option column
	 *
	 * @since 2.60
	 * 
	 * @param object	An MLA iptc_exif_rule object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_option( $item ) {
		return $item->option;
	}

	/**
	 * Display the pagination, adding view, search and filter arguments
	 *
	 * @since 2.60
	 * 
	 * @param string	'top' | 'bottom'
	 */
	function pagination( $which ) {
		$save_uri = $_SERVER['REQUEST_URI'];
		$_SERVER['REQUEST_URI'] = add_query_arg( MLA_IPTC_EXIF_List_Table::mla_submenu_arguments(), $save_uri );
		parent::pagination( $which );
		$_SERVER['REQUEST_URI'] = $save_uri;
	}

	/**
	 * This method dictates the table's columns and titles
	 *
	 * @since 2.60
	 * 
	 * @return	array	Column information: 'slugs'=>'Visible Titles'
	 */
	function get_columns( ) {
		return $columns = MLA_IPTC_EXIF_List_Table::mla_manage_columns_filter();
	}

	/**
	 * Returns the list of currently hidden columns from a user option or
	 * from default values if the option is not set
	 *
	 * @since 2.60
	 * 
	 * @return	array	Column information,e.g., array(0 => 'ID_parent, 1 => 'title_name')
	 */
	function get_hidden_columns( ) {
		$columns = get_user_option( 'managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-iptc_exifcolumnshidden' );

		if ( is_array( $columns ) ) {
			return $columns;
		}

		return self::_default_hidden_columns();
	}

	/**
	 * Returns an array where the  key is the column that needs to be sortable
	 * and the value is db column to sort by.
	 *
	 * @since 2.60
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
	 * @since 2.60
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
			$base_url = 'options-general.php?page=' . MLACoreOptions::MLA_SETTINGS_SLUG . '-iptc_exif&mla_tab=iptc_exif';

			if ( isset( $_REQUEST['s'] ) ) {
				//$base_url = add_query_arg( array( 's' => $_REQUEST['s'] ), $base_url );
			}
		}

		$singular = sprintf('%s <span class="count">(%%s)</span>', $custom_field_item['singular'] );
		$plural = sprintf('%s <span class="count">(%%s)</span>', $custom_field_item['plural'] );
		$nooped_plural = _n_noop( $singular, $plural, 'media-library-assistant' );
		return "<a href='" . add_query_arg( array( 'mla_iptc_exif_view' => $view_slug ), $base_url )
			. "'$class>" . sprintf( translate_nooped_plural( $nooped_plural, $custom_field_item['count'], 'media-library-assistant' ), number_format_i18n( $custom_field_item['count'] ) ) . '</a>';
	} // _get_view

	/**
	 * Returns an associative array listing all the views that can be used with this table.
	 * These are listed across the top of the page and managed by WordPress.
	 *
	 * @since 2.60
	 * 
	 * @return	array	View information,e.g., array ( id => link )
	 */
	function get_views( ) {
		// Find current view
		$current_view = isset( $_REQUEST['mla_iptc_exif_view'] ) ? $_REQUEST['mla_iptc_exif_view'] : 'all';

		// Generate the list of views, retaining keyword search criterion
		$s = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';
		$iptc_exif_items = MLA_IPTC_EXIF_Query::mla_tabulate_iptc_exif_items( $s );
		$view_links = array();
		foreach ( $iptc_exif_items as $slug => $item )
			$view_links[ $slug ] = self::_get_view( $slug, $item, $current_view );

		return $view_links;
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 2.60
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
	 * @since 2.60
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
	 * @since 2.60
	 *
	 * @param string $selected Optional. Currently selected status. Default 'any'.
	 * @return string HTML markup for dropdown box.
	 */
	public static function mla_get_custom_field_status_dropdown( $selected = 'any' ) {
		$dropdown  = '<select name="mla_iptc_exif_status" class="postform" id="name">' . "\n";

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
			$actions = array( 'mla_iptc_exif_status', 'mla_filter' );
		} else {
			$actions = array();
		}

		if ( empty( $actions ) ) {
			return;
		}

		echo ( '<div class="alignleft actions">' );

		foreach ( $actions as $action ) {
			switch ( $action ) {
				case 'mla_iptc_exif_status':
					echo self::mla_get_custom_field_status_dropdown( isset( $_REQUEST['mla_iptc_exif_status'] ) ? $_REQUEST['mla_iptc_exif_status'] : 'any' );
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
	 * @since 2.60
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
		$total_items = MLA_IPTC_EXIF_Query::mla_count_iptc_exif_rules( $_REQUEST );
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
		$this->items = MLA_IPTC_EXIF_Query::mla_query_iptc_exif_rules( $_REQUEST, ( ( $current_page - 1 ) * $per_page ), $per_page );
	}

	/**
	 * Generates (echoes) content for a single row of the table
	 *
	 * @since 2.60
	 *
	 * @param object the current item
	 *
	 * @return void Echoes the row HTML
	 */
	function single_row( $item ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		echo '<tr id="iptc_exif-' . $item->post_ID . '"' . $row_class . '>';
		echo parent::single_row_columns( $item );
		echo '</tr>';
	}
} // class MLA_IPTC_EXIF_List_Table

/**
 * Class MLA (Media Library Assistant) IPTC EXIF Query implements the
 * searchable database of IPTC EXIF mapping rules.
 *
 * @package Media Library Assistant
 * @since 2.60
 */
class MLA_IPTC_EXIF_Query {

	/**
	 * Callback to sort array by a 'name' key.
	 *
	 * @since 2.60
	 *
	 * @param array $a The first array.
	 * @param array $b The second array.
	 * @return integer The comparison result.
	 */
	private static function _sort_uname_callback( $a, $b ) {
		return strnatcasecmp( $a['name'], $b['name'] );
	}

	/**
	 * In-memory representation of the IPTC EXIF mapping rules
	 *
	 * @since 2.60
	 *
	 * @var array $_iptc_exif_rules {
	 *         Items by ID. Key $$ID is an index number starting with 1.
	 *
	 *         @type array $$ID {
	 *             Rule elements.
	 *
	 *             @type integer $post_ID Rule ID; equal to $$ID.
	 *             @type string $type Rule type, ‘standard’, ‘taxonomy’ or ‘custom’.
	 *             @type string $key Field or taxonomy slug, custom field name the rule applies to.
	 *             @type string $rule_name Rule name, to accomodate an old custom fields bug.
	 *             @type string $name Field or taxonomy name the rule applies to.
	 *             @type boolean $hierarchical True if taxonomy is hierarchical.
	 *             @type string $iptc_value IPTC tag, e.g., ‘2#025’ or 'none'.
	 *             @type string $exif_value EXIF field name or Content Template begining "template:".
	 *             @type boolean $iptc_first True if IPTC value takes priority over EXIF value.
	 *             @type boolean $keep_existing Retain existing value(s), do not replace them.
	 *             @type string $format Output format, 'native', 'commas' or 'raw'.
	 *             @type string $option Output option, 'text', 'single', 'array' or 'multi'.
	 *             @type boolean $no_null Delete empty (NULL) values.
	 *             @type string $delimiters Term separator(s) for taxonomy rules.
	 *             @type integer $parent Parent term_id for taxonomy rules.
	 *             @type boolean $active True if rule should be applied during mapping.
	 *             @type boolean $read_only True if rule_name !== name, to prevent editing of "old bug" rules.
	 *             @type boolean $changed True if the rule has changed since loading.
	 *             @type boolean $deleted True if the rule has been deleted since loading.
	 *         }
	 */
	private static $_iptc_exif_rules = NULL;

	/**
	 * Highest existing IPTC EXIF rule ID value
	 *
	 * @since 2.60
	 *
	 * @var	integer
	 */
	private static $_iptc_exif_rule_highest_ID = 0;

	/**
	 * Assemble the in-memory representation of the IPTC EXIF rules
	 *
	 * @since 2.60
	 *
	 * @param boolean $force_refresh Optional. Force a reload of rules. Default false.
	 * @return boolean Success (true) or failure (false) of the operation
	 */
	private static function _get_iptc_exif_rules( $force_refresh = false ) {
		if ( false == $force_refresh && NULL != self::$_iptc_exif_rules ) {
			return true;
		}

		self::$_iptc_exif_rules = array();
		self::$_iptc_exif_rule_highest_ID = 0;

		$current_values = MLACore::mla_get_option( 'iptc_exif_mapping' );
		if (empty( $current_values ) ) {
			$current_values = array ( 'standard' => array(), 'taxonomy' => array(), 'custom' => array() );
		}

		// One rule for each standard value, which MUST be present
		$default_values = MLACore::mla_get_option( 'iptc_exif_mapping', true );
		foreach( $default_values['standard'] as $key => $value ) {
			if ( isset( $current_values['standard'][ $key ] ) ) {
				$current_value = $current_values['standard'][ $key ];
			} else {
				$current_value = $value;
			}

			self::$_iptc_exif_rules[ ++self::$_iptc_exif_rule_highest_ID ] = array(
				'post_ID' => self::$_iptc_exif_rule_highest_ID,
				'type' => 'standard',
				'key' => $key,
				'rule_name' => $current_value['name'],
				'name' => $current_value['name'],
				'hierarchical' => false,
				'iptc_value' => $current_value['iptc_value'],
				'exif_value' => $current_value['exif_value'],
				'iptc_first' => $current_value['iptc_first'],
				'keep_existing' => $current_value['keep_existing'],
				'format' => 'native',
				'option' => 'text',
				'no_null' => false,
				'delimiters' => '',
				'parent' => 0,
				'active' => isset( $current_value['active'] ) ? $current_value['active'] : false,
				'read_only' => false,
				'changed' => false,
				'deleted' => false,
			);
		}

		// One rule for each registered and supported taxonomy
		$taxonomies = get_taxonomies( array ( 'show_ui' => true ), 'objects' );
		foreach ( $taxonomies as $key => $value ) {
			if ( ! MLACore::mla_taxonomy_support( $key, 'support' ) ) {
				continue;
			}

			$current_value = array(
				'post_ID' => ++self::$_iptc_exif_rule_highest_ID,
				'type' => 'taxonomy',
				'key' => $key,
				'rule_name' => $value->labels->name,
				'name' => $value->labels->name,
				'hierarchical' => $value->hierarchical,
				'format' => 'native',
				'option' => 'text',
				'no_null' => false,
				'read_only' => false,
				'changed' => false,
				'deleted' => false,
			);

			if ( isset( $current_values['taxonomy'][ $key ] ) ) {
				$existing_values = $current_values['taxonomy'][ $key ];
				unset( $current_values['taxonomy'][ $key ] );

				$current_value = array_merge( $current_value, array(
					'iptc_value' => $existing_values['iptc_value'],
					'exif_value' => $existing_values['exif_value'],
					'iptc_first' => $existing_values['iptc_first'],
					'keep_existing' => $existing_values['keep_existing'],
					'delimiters' => $existing_values['delimiters'],
					'parent' => $existing_values['parent'],
					'active' => isset( $existing_values['active'] ) ? $existing_values['active'] : true,
				) );
			} else {
				$current_value = array_merge( $current_value, array(
					'iptc_value' => 'none',
					'exif_value' => '',
					'iptc_first' => true,
					'keep_existing' => true,
					'delimiters' => '',
					'parent' => 0,
					'active' => true,
				) );
			}

			self::$_iptc_exif_rules[ self::$_iptc_exif_rule_highest_ID ] = $current_value;
		}

		// Preserve existing rules for non-supported taxonomies as "inactive"
		$taxonomy_rules_changed = false;
		foreach ( $current_values['taxonomy'] as $key => $value ) {
			$value['post_ID'] = ++self::$_iptc_exif_rule_highest_ID;
			$value['type'] = 'taxonomy';
			$value['key'] = $key;
			$value['rule_name'] = $value['name'];
			$value['format'] = 'native';
			$value['option'] = 'text';
			$value['no_null'] = false;
			$value['read_only'] = false;
			$value['deleted'] = false;

			if ( isset( $value['active'] ) && $value['active'] ) {
				$value['active'] = false;
				$value['changed'] = true;
				$taxonomy_rules_changed = true;
			} else {
				$value['active'] = false;
			}

			self::$_iptc_exif_rules[ self::$_iptc_exif_rule_highest_ID ] = $value;
		}

		// One rule for each existing custom field rule, case insensitive "natural order"
		if ( !empty( $current_values['custom'] ) ) {
			$sorted_keys = array();
			foreach ( $current_values['custom'] as $rule_name => $current_value ) {
				$sorted_keys[ $current_value['name'] ] = $current_value['name'];
			}
			natcasesort( $sorted_keys );
	
			$sorted_names = array();
			foreach ( $sorted_keys as $rule_name ) {
				$sorted_names[ $rule_name ] = array();
			}
	
			// Allow for multiple rules mapping the same name (an old bug)
			foreach ( $current_values['custom'] as $rule_name => $current_value ) {
				$sorted_names[ $current_value['name'] ][] = $rule_name;
			}
	
			foreach ( $sorted_names as $sorted_keys ) {
				foreach ( $sorted_keys as $rule_name ) {
					$current_value = $current_values['custom'][ $rule_name ];
					self::$_iptc_exif_rules[ ++self::$_iptc_exif_rule_highest_ID ] = array(
						'post_ID' => self::$_iptc_exif_rule_highest_ID,
						'type' => 'custom',
						'key' => $rule_name,
						'rule_name' => $rule_name,
						'name' => $current_value['name'],
						'hierarchical' => false,
						'iptc_value' => $current_value['iptc_value'],
						'exif_value' => $current_value['exif_value'],
						'iptc_first' => $current_value['iptc_first'],
						'keep_existing' => $current_value['keep_existing'],
						'format' => $current_value['format'],
						'option' => $current_value['option'],
						'no_null' => $current_value['no_null'],
						'delimiters' => '',
						'parent' => 0,
						'active' => isset( $current_value['active'] ) ? $current_value['active'] : true,
						'read_only' => $rule_name !== $current_value['name'],
						'changed' => false,
						'deleted' => false,
					);
	
					if ( self::$_iptc_exif_rules[ self::$_iptc_exif_rule_highest_ID ]['read_only'] ) {
						self::$_iptc_exif_rules[ self::$_iptc_exif_rule_highest_ID ]['active'] = false;
					}
				} // foreach rule
			} // foreach name
		} // custom rules exist

		// Flush the rules if we have inactivated any non-supported taxonomy rules
		if ( $taxonomy_rules_changed ) {
			MLA_IPTC_EXIF_Query::mla_put_iptc_exif_rules();
		}
		
		return true;
	}

	/**
	 * Flush the in-memory representation of the IPTC EXIF rules to the option value
	 *
	 * @since 2.60
	 */
	public static function mla_put_iptc_exif_rules() {
		if ( NULL === self::$_iptc_exif_rules ) {
			return;
		}

		$iptc_exif_rules = array();
		$rules_changed = false;

		foreach( self::$_iptc_exif_rules as $ID => $current_value ) {
			if ( $current_value['deleted'] ) {
				$rules_changed = true;
				continue;
			}

			$new_value = array(
				'name' => $current_value['name'],
				'iptc_value' => $current_value['iptc_value'],
				'exif_value' => $current_value['exif_value'],
				'iptc_first' => $current_value['iptc_first'],
				'keep_existing' => $current_value['keep_existing'],
				'active' => $current_value['active'],
			);

			switch( $current_value['type'] ) {
				case 'taxonomy':
					$new_value['hierarchical'] = $current_value['hierarchical'];
					$new_value['parent'] = $current_value['parent'];
					$new_value['delimiters'] = $current_value['delimiters'];
					break;
				case 'custom':
					$new_value['format'] = $current_value['format'];
					$new_value['option'] = $current_value['option'];
					$new_value['no_null'] = $current_value['no_null'];
					break;
				default:
					break;
			}

			$iptc_exif_rules[ $current_value['type'] ][ $current_value['key'] ] = $new_value;
			$rules_changed |= $current_value['changed'];
		}

		if ( $rules_changed ) {
			$settings_changed = MLACore::mla_update_option( 'iptc_exif_mapping', $iptc_exif_rules );
			self::_get_iptc_exif_rules( true );
		}
	}

	/**
	 * Sanitize and expand query arguments from request variables
	 *
	 * @since 2.60
	 *
	 * @param array	query parameters from web page, usually found in $_REQUEST
	 * @param int		Optional number of rows (default 0) to skip over to reach desired page
	 * @param int		Optional number of rows on each page (0 = all rows, default)
	 *
	 * @return	array	revised arguments suitable for query
	 */
	private static function _prepare_iptc_exif_rules_query( $raw_request, $offset = 0, $count = 0 ) {
		/*
		 * Go through the $raw_request, take only the arguments that are used in the query and
		 * sanitize or validate them.
		 */
		if ( ! is_array( $raw_request ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			MLACore::mla_debug_add( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLA_IPTC_EXIF_Query::_prepare_iptc_exif_rules_query', var_export( $raw_request, true ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			return NULL;
		}

		$clean_request = array (
			'mla_iptc_exif_view' => 'all',
			'mla_iptc_exif_status' => 'any',
			'orderby' => 'none',
			'order' => 'ASC',
			's' => ''
		);

		foreach ( $raw_request as $key => $value ) {
			switch ( $key ) {
				case 'mla_iptc_exif_view':
				case 'mla_iptc_exif_status':
					$clean_request[ $key ] = $value;
					break;
				case 'orderby':
					if ( 'none' == $value ) {
						$clean_request[ $key ] = $value;
					} else {
						if ( array_key_exists( $value, MLA_IPTC_EXIF_List_Table::mla_get_sortable_columns() ) ) {
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
	 * @since 2.60
	 *
	 * @param array	query parameters from web page, usually found in $_REQUEST
	 *
	 * @return	array	query results; array of MLA post_mime_type objects
	 */
	private static function _execute_iptc_exif_rules_query( $request ) {
		if ( ! self::_get_iptc_exif_rules() ) {
			return array ();
		}

		/*
		 * Sort and filter the list
		 */
		$keywords = isset( $request['s'] ) ? $request['s'] : '';
		preg_match_all('/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $keywords, $matches);
		$keywords = array_map( 'MLAQuery::mla_search_terms_tidy', $matches[0]);
		$view = isset( $request['mla_iptc_exif_view'] ) ? $request['mla_iptc_exif_view'] : 'all';
		$status = isset( $request['mla_iptc_exif_status'] ) ? $request['mla_iptc_exif_status'] : 'any';
		$index = 0;
		$sortable_items = array();

		foreach ( self::$_iptc_exif_rules as $ID => $value ) {
			if ( ! empty( $keywords ) ) {
				$iptc_text = array_key_exists( $value['iptc_value'], MLAData::$mla_iptc_records ) ? MLAData::$mla_iptc_records[ $value['iptc_value'] ] : $value['iptc_value'];

				$found = false;
				foreach ( $keywords as $keyword ) {
					$found |= false !== stripos( $value['rule_name'], $keyword );
					$found |= false !== stripos( $value['name'], $keyword );
					$found |= false !== stripos( $iptc_text, $keyword );
					$found |= false !== stripos( $value['iptc_value'], $keyword );
					$found |= false !== stripos( $value['exif_value'], $keyword );
				}

				if ( ! $found ) {
					continue;
				}
			}

			switch( $view ) {
				case 'standard':
					$found = 'standard' === $value['type'];
					break;
				case 'taxonomy':
					$found = 'taxonomy' === $value['type'];
					break;
				case 'custom':
					$found = 'custom' === $value['type'];
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
				case 'iptc_value':
					$sortable_items[ ( ( 'none' === $value['iptc_value'] ) ? chr(1) : $value['iptc_value'] ) . $ID ] = (object) $value;
					break;
				case 'exif_value':
					$sortable_items[ ( empty( $value['exif_value'] ) ? chr(1) : $value['exif_value'] ) . $ID ] = (object) $value;
					break;
				case 'priority':
					$sortable_items[ ( $value['iptc_first'] ? chr(2) : chr(1) ) . $ID ] = (object) $value;
					break;
				case 'existing_text':
					$sortable_items[ ( $value['keep_existing'] ? __( 'Keep', 'media-library-assistant' ) : __( 'Replace', 'media-library-assistant' ) ) . $ID ] = (object) $value;
					break;
				case 'status':
					$sortable_items[ ( $value['active'] ? __( 'Active', 'media-library-assistant' ) : __( 'Inactive', 'media-library-assistant' ) ) . $ID ] = (object) $value;
					break;
				case 'delimiters':
					$sortable_items[ ( empty( $value['delimiters'] ) ? chr(1) : $value['delimiters'] ) . $ID ] = (object) $value;
					break;
				case 'parent':
					if ( empty( $value['parent'] ) ) {
						$parent = chr(1);
					} else {
						$term = get_term( $value['parent'], $value['key'] );
						$parent = $term->name;
					}

					$sortable_items[ $parent . $ID ] = (object) $value;
					break;
				case 'delete_null':
					$sortable_items[ ( $value['no_null'] ? __( 'Yes', 'media-library-assistant' ) : __( 'No', 'media-library-assistant' ) ) . $ID ] = (object) $value;
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
	 * Get the total number of MLA iptc_exif_rule objects
	 *
	 * @since 2.60
	 *
	 * @param array	Query variables, e.g., from $_REQUEST
	 *
	 * @return	integer	Number of MLA iptc_exif_rule objects
	 */
	public static function mla_count_iptc_exif_rules( $request ) {
		$request = self::_prepare_iptc_exif_rules_query( $request );
		$results = self::_execute_iptc_exif_rules_query( $request );
		return count( $results );
	}

	/**
	 * Retrieve MLA iptc_exif_rule objects for list table display
	 *
	 * @since 2.60
	 *
	 * @param array	query parameters from web page, usually found in $_REQUEST
	 * @param int		number of rows to skip over to reach desired page
	 * @param int		number of rows on each page
	 *
	 * @return	array	MLA iptc_exif_rule objects
	 */
	public static function mla_query_iptc_exif_rules( $request, $offset, $count ) {
		$request = self::_prepare_iptc_exif_rules_query( $request, $offset, $count );
		$results = self::_execute_iptc_exif_rules_query( $request );
		return $results;
	}

	/**
	 * Find a IPTC EXIF Rule ID given its rule name
	 *
	 * @since 2.60
 	 *
	 * @param string $rule_name MLA IPTC EXIF Rule name.
	 * @return integer Rule ID if the rule exists else zero (0).
	 */
	public static function mla_find_iptc_exif_rule_ID( $rule_name ) {
		if ( ! self::_get_iptc_exif_rules() ) {
			return false;
		}

		foreach( self::$_iptc_exif_rules as $ID => $rule ) {
			if ( $rule_name == $rule['rule_name'] ) {
				return $ID;
			}
		}

		return 0;
	}

	/**
	 * Return the IPTC EXIF custom field rule names
	 *
	 * @since 2.60
 	 *
	 * @return	array	MLA iptc_exif_rule name => name
	 */
	public static function mla_iptc_exif_rule_names() {
		$names = array();

		if ( ! self::_get_iptc_exif_rules() ) {
			return $names;
		}

		foreach( self::$_iptc_exif_rules as $ID => $rule ) {
			if ( 'custom' === $rule['type'] ) {
				$names[ $rule['name'] ]['name'] = $rule['name'];
			}
		}

		return $names;
	}

	/**
	 * Find a IPTC EXIF Rule given its ID
	 *
	 * @since 2.60
 	 *
	 * @param integer	$ID MLA IPTC EXIF Rule ID
 	 *
	 * @return	array	MLA iptc_exif_rule array
	 * @return	boolean	false; MLA iptc_exif_rule does not exist
	 */
	public static function mla_find_iptc_exif_rule( $ID ) {
		if ( ! self::_get_iptc_exif_rules() ) {
			return false;
		}

		if ( isset( self::$_iptc_exif_rules[ $ID ] ) ) {
			return self::$_iptc_exif_rules[ $ID ];
		}

		return false;
	}

	/**
	 * Convert a IPTC EXIF Rule to an old-style mapping rule, given its ID
	 *
	 * @since 2.60
 	 *
	 * @param integer|array $rule_ids MLA IPTC EXIF Rule ID(s)
 	 *
	 * @return array MLA iptc_exif_mapping values ( 'standard' => array(), 'taxonomy' => array(), 'custom' => array() )
	 * @return boolean false; MLA iptc_exif_rules do not exist
	 */
	public static function mla_convert_iptc_exif_rules( $rule_ids ) {
		if ( ! self::_get_iptc_exif_rules() ) {
			return false;
		}

		if ( is_scalar( $rule_ids ) ) {
			$rule_ids = array( $rule_ids );
		}

		$rules = array( 'standard' => array(), 'taxonomy' => array(), 'custom' => array() );
		foreach( $rule_ids as $id ) {
			$id = absint( $id );
			if ( isset( self::$_iptc_exif_rules[ $id ] ) ) {
				$new_rule = self::$_iptc_exif_rules[ $id ];
				$old_rule = array(
					'name' => $new_rule['name'],
					'iptc_value' => $new_rule['iptc_value'],
					'exif_value' => $new_rule['exif_value'],
					'iptc_first' => $new_rule['iptc_first'],
					'keep_existing' => $new_rule['keep_existing'],
					'active' => $new_rule['active'],
				);

				switch( $new_rule['type'] ) {
					case 'taxonomy':
						$old_rule['hierarchical'] = $new_rule['hierarchical'];
						$old_rule['parent'] = $new_rule['parent'];
						$old_rule['delimiters'] = $new_rule['delimiters'];
						break;
					case 'custom':
						$old_rule['format'] = $new_rule['format'];
						$old_rule['option'] = $new_rule['option'];
						$old_rule['no_null'] = $new_rule['no_null'];
						break;
					default:
						break;
				}

				// Convert to "checkbox", i.e. isset() == true
				if ( $new_rule['no_null'] ) {
					$old_rule['no_null'] = $new_rule['no_null'];
				}

				$rules[ $new_rule['type'] ][ $new_rule['key'] ] = $old_rule;
			}
		}

		return $rules;
	}

	/**
	 * Update a IPTC EXIF Rule property given its ID and key.
	 *
	 * @since 2.60
 	 *
	 * @param integer $ID MLA IPTC EXIF Rule ID.
	 * @param string $key MLA IPTC EXIF Rule property.
	 * @param string $value MLA IPTC EXIF Rule new value.
	 * @return boolean true if object exists else false.
	 */
	public static function mla_update_iptc_exif_rule( $ID, $key, $value ) {
		if ( ! self::_get_iptc_exif_rules() ) {
			return false;
		}

		if ( isset( self::$_iptc_exif_rules[ $ID ] ) ) {
			self::$_iptc_exif_rules[ $ID ][ $key ] = $value;
			return true;
		}

		return false;
	}

	/**
	 * Replace a IPTC EXIF Rule given its value array.
	 *
	 * @since 2.60
 	 *
	 * @param array $value MLA IPTC EXIF Rule new value.
	 * @return boolean true if object exists else false.
	 */
	public static function mla_replace_iptc_exif_rule( $value ) {
		if ( ! self::_get_iptc_exif_rules() ) {
			return false;
		}

		if ( isset( self::$_iptc_exif_rules[ $value['post_ID'] ] ) ) {
			self::$_iptc_exif_rules[ $value['post_ID'] ] = $value;
			return true;
		}

		return false;
	}

	/**
	 * Insert a IPTC EXIF Rule given its value array.
	 *
	 * @since 2.60
 	 *
	 * @param array $value MLA IPTC EXIF Rule new value.
	 * @return boolean true if addition succeeds else false.
	 */
	public static function mla_add_iptc_exif_rule( $value ) {
		if ( ! self::_get_iptc_exif_rules() ) {
			return false;
		}

		$value['post_ID'] = ++self::$_iptc_exif_rule_highest_ID;
		$value['read_only'] = $value['rule_name'] !== $value['name'];
		$value['changed'] = true;
		$value['deleted'] = false;

		self::$_iptc_exif_rules[ $value['post_ID'] ] = $value;
		return true;
	}

	/**
	 * Tabulate MLA iptc_exif_rule objects by view for list table display
	 *
	 * @since 2.60
	 *
	 * @param string	keyword search criterion, optional
	 *
	 * @return	array	( 'singular' label, 'plural' label, 'count' of items )
	 */
	public static function mla_tabulate_iptc_exif_items( $s = '' ) {
		if ( empty( $s ) ) {
			$request = array( 'mla_iptc_exif_view' => 'all' );
		} else {
			$request = array( 's' => $s );
		}

		$items = self::mla_query_iptc_exif_rules( $request, 0, 0 );

		$template_items = array(
			'all' => array(
				'singular' => _x( 'All', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'All', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'standard' => array(
				'singular' => _x( 'Standard', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Standard', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'taxonomy' => array(
				'singular' => _x( 'Taxonomy', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Taxonomy', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'custom' => array(
				'singular' => _x( 'Custom', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Custom', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'read_only' => array(
				'singular' => _x( 'Read Only', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Read Only', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
		);

		foreach ( $items as $value ) {
			$template_items['all']['count']++;

			switch( $value->type ) {
				case 'standard':
					$template_items[ 'standard' ]['count']++;
					break;
				case 'taxonomy':
					$template_items[ 'taxonomy' ]['count']++;
					break;
				case 'custom':
					$template_items[ 'custom' ]['count']++;
					break;
				default:
					break;
			}

			if ( $value->read_only ) {
					$template_items[ 'read_only' ]['count']++;
			}
		}

		return $template_items;
	}
} // class MLA_IPTC_EXIF_Query

/*
 * Actions are added here, when the source file is loaded, because the mla_compose_iptc_exif_tab
 * function is called too late to be useful.
 */
add_action( 'admin_enqueue_scripts', 'MLASettings_IPTCEXIF::mla_admin_enqueue_scripts' );
add_action( 'admin_init', 'MLA_IPTC_EXIF_List_Table::mla_admin_init' );
?>