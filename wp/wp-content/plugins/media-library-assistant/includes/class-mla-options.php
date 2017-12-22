<?php
/**
 * Manages the plugin option settings
 *
 * @package Media Library Assistant
 * @since 1.00
 */

/**
 * Class MLA (Media Library Assistant) Options manages the plugin option settings
 * and provides functions to get and put them from/to WordPress option variables
 *
 * Separated from class MLASettings in version 1.00
 *
 * @package Media Library Assistant
 * @since 1.00
 */
class MLAOptions {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize( ) {
 		if ( ( 'checked' == MLACore::mla_get_option( 'enable_iptc_exif_mapping' ) ) ||
			( 'checked' == MLACore::mla_get_option( 'enable_custom_field_mapping' ) ) ||
 			( 'checked' == MLACore::mla_get_option( 'enable_iptc_exif_update' ) ) ||
			( 'checked' == MLACore::mla_get_option( 'enable_custom_field_update' ) ) ) {
			add_filter( 'wp_handle_upload_prefilter', 'MLAOptions::mla_wp_handle_upload_prefilter_filter', 1, 1 );
			add_filter( 'wp_handle_upload', 'MLAOptions::mla_wp_handle_upload_filter', 1, 1 );

			add_action( 'add_attachment', 'MLAOptions::mla_add_attachment_action', 0x7FFFFFFF, 1 );
			add_filter( 'wp_update_attachment_metadata', 'MLAOptions::mla_update_attachment_metadata_filter', 0x7FFFFFFF, 2 );

			MLACore::mla_debug_add( __LINE__ . " MLAOptions::initialize() hooks set", MLACore::MLA_DEBUG_CATEGORY_REST );
		}
	}

	/**
	 * Option Setting templates
	 *
	 * @since 0.80
	 *
	 * @var	array
	 */
	private static $mla_option_templates = NULL;

	/**
	 * Load option settings templates to $mla_option_templates
	 *
	 * @since 0.80
	 *
	 * @return	void
	 */
	private static function _load_option_templates() {
		if ( is_null( MLAOptions::$mla_option_templates ) ) {
			MLAOptions::$mla_option_templates = MLACore::mla_load_template( 'mla-option-templates.tpl' );

			if ( is_null( MLAOptions::$mla_option_templates ) ) {
				MLACore::mla_debug_add( '<strong>mla_debug _load_option_templates()</strong> ' . __( 'error loading tpls/mla-option-templates.tpl', 'media-library-assistant' ) );
				MLAOptions::$mla_option_templates = array();
			} elseif ( !MLAOptions::$mla_option_templates ) {
				MLACore::mla_debug_add( '<strong>mla_debug _load_option_templates()</strong> ' . __( 'tpls/mla-option-templates.tpl not found', 'media-library-assistant' ) );
				MLAOptions::$mla_option_templates = array();
			}
		} // is_null()
	}

	/**
	 * Return the stored value or default value of a defined MLA option
	 *
	 * Compatibility shim for MLACore::mla_get_option
	 *
	 * @since 0.1
	 *
	 * @param	string 	Name of the desired option
	 * @param	boolean	True to ignore current setting and return default values
	 * @param	boolean	True to ignore default values and return only stored values
	 * @param	array	Custom option definitions
	 * 
	 *
	 * @return	mixed	Value(s) for the option or false if the option is not a defined MLA option
	 */
	public static function mla_get_option( $option, $get_default = false, $get_stored = false, $option_table = NULL ) {
		return MLACore::mla_get_option( $option, $get_default, $get_stored, $option_table ); 
	}

	/**
	 * Add or update the stored value of a defined MLA option
	 *
	 * Compatibility shim for MLACore::mla_update_option
	 *
	 * @since 0.1
	 *
	 * @param	string 	Name of the desired option
	 * @param	mixed 	New value for the desired option
	 * @param	array	Custom option definitions
	 *
	 * @return	boolean	True if the value was changed or false if the update failed
	 */
	public static function mla_update_option( $option, $newvalue, $option_table = NULL ) {
		return MLACore::mla_update_option( $option, $newvalue, $option_table ); 
	}

	/**
	 * Delete the stored value of a defined MLA option
	 *
	 * Compatibility shim for MLACore::mla_delete_option
	 *
	 * @since 0.1
	 *
	 * @param	string 	Name of the desired option
	 * @param	array	Custom option definitions
	 *
	 * @return	boolean	True if the option was deleted, otherwise false
	 */
	public static function mla_delete_option( $option, $option_table = NULL ) {
		return MLACore::mla_delete_option( $option, $option_table ); 
	}

	/**
	 * Determine MLA support for a taxonomy, handling the special case where the
	 * settings are being updated or reset.
 	 *
	 * Compatibility shim for MLACore::mla_taxonomy_support
	 *
	 * @since 0.30
	 *
	 * @param	string	Taxonomy name, e.g., attachment_category
	 * @param	string	Optional. 'support' (default), 'quick-edit' or 'filter'
	 *
	 * @return	boolean|string
	 *			true if the taxonomy is supported in this way else false.
	 *			string if $tax_name is '' and $support_type is 'filter', returns the taxonomy to filter by.
	 */
	public static function mla_taxonomy_support( $tax_name, $support_type = 'support' ) {
		return MLACore::mla_taxonomy_support( $tax_name, $support_type ); 
	} // mla_taxonomy_support

	/**
	 * Returns an array of taxonomy names assigned to $support_type
 	 *
	 * Compatibility shim for MLACore::mla_taxonomy_support
	 *
	 * @since 1.90
	 *
	 * @param	string	Optional. 'support' (default), 'quick-edit', 'flat-checklist', 'term-search' or 'filter'
	 *
	 * @return	array	taxonomies assigned to $support_type; can be empty.
	 */
	public static function mla_supported_taxonomies($support_type = 'support') {
		return MLACore::mla_supported_taxonomies( $tax_name, $support_type ); 
	} // mla_supported_taxonomies

	/**
	 * Render and manage Attachment Display Settings options; alignment, link type and size
 	 *
	 * @since 1.71
	 * @uses MLASettings::$page_template_array contains select_option and select templates
	 *
	 * @param	string 	'render', 'update', 'delete', or 'reset'
	 * @param	string 	option name, e.g., 'image_default_align'
	 * @param	array 	option parameters
	 * @param	array 	Optional. null (default) for 'render' else option data, e.g., $_REQUEST
	 *
	 * @return	string	HTML table row markup for 'render' else message(s) reflecting the results of the operation.
	 */
	public static function mla_attachment_display_settings_option_handler( $action, $key, $value, $args = NULL ) {
		switch ( $action ) {
			case 'render':
				$current_value = get_option( $key );
				if ( empty( $current_value ) ) {
					$current_value = $value['std'];
				}

				$select_options = '';
				foreach ( $value['options'] as $optid => $option ) {
					$option_values = array(
						'selected' => '',
						'value' => $option,
						'text' => $value['texts'][$optid]
					);

					if ( $option == $current_value ) {
						$option_values['selected'] = 'selected="selected"';
					}

					$select_options .= MLAData::mla_parse_template( MLASettings::$page_template_array['select-option'], $option_values );
				}

				$option_values = array(
					'key' => MLA_OPTION_PREFIX . $key,
					'value' => esc_attr( $value['name'] ),
					'options' => $select_options,
					'help' => $value['help'] 
				);

				return MLAData::mla_parse_template( MLASettings::$page_template_array['select'], $option_values );
			case 'update':
			case 'delete':
				$msg = '<br>update_option(' . $key . ")\r\n";
				$new_value = $args[ MLA_OPTION_PREFIX . $key ];
				if ( $value['std'] == $new_value ) {
					$new_value = '';
				}

				update_option( $key, $new_value );
				return $msg;
			case 'reset':
				$msg = '<br>update_option(' . $key . ")\r\n";
				update_option( $key, '' );
				return $msg;
			default:
				/* translators: 1: ERROR tag 2: option name 3: action, e.g., update, delete, reset */
				return '<br>' . sprintf( __( '%1$s: Custom %2$s unknown action "%3$s"', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $key, $action ) . "\r\n";
		}
	} // mla_attachment_display_settings_option_handler

	/**
	 * Render and manage taxonomy support options, e.g., Categories and Post Tags
 	 *
	 * @since 0.30
	 * @uses $mla_option_templates contains taxonomy-row and taxonomy-table templates
	 *
	 * @param	string 	'render', 'update', 'delete', or 'reset'
	 * @param	string 	option name, e.g., 'tax_support', or 'tax_flat_checklist'
	 * @param	array 	option parameters
	 * @param	array 	Optional. null (default) for 'render' else option data, e.g., $_REQUEST
	 *
	 * @return	string	HTML table row markup for 'render' else message(s) reflecting the results of the operation.
	 */
	public static function mla_taxonomy_option_handler( $action, $key, $value, $args = NULL ) {
		MLAOptions::_load_option_templates();

		switch ( $action ) {
			case 'render':
				$taxonomies = get_taxonomies( array ( 'show_ui' => true ), 'objects' );
				$current_values = MLACore::mla_get_option( $key );
				$tax_support = isset( $current_values['tax_support'] ) ? $current_values['tax_support'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_support'];
				$tax_quick_edit = isset( $current_values['tax_quick_edit'] ) ? $current_values['tax_quick_edit'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_quick_edit'];
				$tax_term_search = isset( $current_values['tax_term_search'] ) ? $current_values['tax_term_search'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_term_search'];
				$tax_flat_checklist = isset( $current_values['tax_flat_checklist'] ) ? $current_values['tax_flat_checklist'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_flat_checklist'];
				$tax_checked_on_top = isset( $current_values['tax_checked_on_top'] ) ? $current_values['tax_checked_on_top'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_checked_on_top'];
				$tax_filter = isset( $current_values['tax_filter'] ) ? $current_values['tax_filter'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_filter'];
				$tax_metakey_sort = isset( $current_values['tax_metakey_sort'] ) ? $current_values['tax_metakey_sort'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_metakey_sort'];
				$tax_metakey = isset( $current_values['tax_metakey'] ) ? $current_values['tax_metakey'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_metakey'];

				/*
				 * Always display our own taxonomies, even if not registered.
				 * Otherwise there's no way to turn them back on.
				 */
				if ( ! array_key_exists( 'attachment_category', $taxonomies ) ) {
					$taxonomies['attachment_category'] = (object) array( 'labels' => (object) array( 'name' => __( 'Attachment Categories', 'media-library-assistant' ) ), 'hierarchical' => true );
					if ( isset( $tax_support['attachment_category'] ) ) {
						unset( $tax_support['attachment_category'] );
					}

					if ( isset( $tax_quick_edit['attachment_category'] ) ) {
						unset( $tax_quick_edit['attachment_category'] );
					}

					if ( $tax_filter == 'attachment_category' ) {
						$tax_filter = '';
					}
				}

				if ( ! array_key_exists( 'attachment_tag', $taxonomies ) ) {
					$taxonomies['attachment_tag'] = (object) array( 'labels' => (object) array( 'name' => __( 'Attachment Tags', 'media-library-assistant' ) ), 'hierarchical' => false );

					if ( isset( $tax_support['attachment_tag'] ) ) {
						unset( $tax_support['attachment_tag'] );
					}

					if ( isset( $tax_quick_edit['attachment_tag'] ) ) {
						unset( $tax_quick_edit['attachment_tag'] );
					}

					if ( $tax_filter == 'attachment_tag' ) {
						$tax_filter = '';
					}
				}

				$taxonomy_row = MLAOptions::$mla_option_templates['taxonomy-row'];
				$row = '';

				foreach ( $taxonomies as $tax_name => $tax_object ) {
					$option_values = array (
						'key' => $tax_name,
						'name' => $tax_object->labels->name,
						'support_checked' => array_key_exists( $tax_name, $tax_support ) ? 'checked=checked' : '',
						'quick_edit_checked' => array_key_exists( $tax_name, $tax_quick_edit ) ? 'checked=checked' : '',
						'term_search_checked' => array_key_exists( $tax_name, $tax_term_search ) ? 'checked=checked' : '',
						'flat_checklist_checked' => array_key_exists( $tax_name, $tax_flat_checklist ) ? 'checked=checked' : '',
						'flat_checklist_disabled' => '',
						'flat_checklist_value' => 'checked',
						'checked_on_top_checked' => array_key_exists( $tax_name, $tax_checked_on_top ) ? 'checked=checked' : '',
						'filter_checked' => ( $tax_name == $tax_filter ) ? 'checked=checked' : ''
					);

					if ( $tax_object->hierarchical ) {
						$option_values['flat_checklist_checked'] = 'checked=checked';
						$option_values['flat_checklist_disabled'] = 'disabled=disabled';
						$option_values['flat_checklist_value'] = 'disabled';
					}

					$row .= MLAData::mla_parse_template( $taxonomy_row, $option_values );
				}

				/*
				 * Add the "filter on custom field" row
				 */
				$selected = empty( $tax_metakey ) ? 'none' : $tax_metakey;
				$tax_metakey_options = MLAOptions::mla_compose_custom_field_option_list( $selected, array() );

				$option_values = array (
					'key' => MLACoreOptions::MLA_FILTER_METAKEY,
					'name' =>  '( ' . __( 'Custom Field', 'media-library-assistant' ) . ' )',
					'asc_checked' => ( 'ASC' == $tax_metakey_sort ) ? 'checked=checked' : '',
					'desc_checked' => ( 'DESC' == $tax_metakey_sort ) ? 'checked=checked' : '',
					'tax_metakey_options' => $tax_metakey_options,
					'tax_metakey_size' => '20',
					'tax_metakey_text' => $tax_metakey,
					'filter_checked' => ( MLACoreOptions::MLA_FILTER_METAKEY == $tax_filter ) ? 'checked=checked' : ''
				);

				$row .= MLAData::mla_parse_template( MLAOptions::$mla_option_templates['taxonomy-metakey-row'], $option_values );

				$option_values = array (
					'Support' => __( 'Support', 'media-library-assistant' ),
					'Inline Edit' => __( 'Inline Edit', 'media-library-assistant' ),
					'Term Search' => __( 'Term Search', 'media-library-assistant' ),
					'Checklist' => __( 'Checklist', 'media-library-assistant' ),
					'Checked On Top' => __( 'Checked On Top', 'media-library-assistant' ),
					'List Filter' => __( 'List Filter', 'media-library-assistant' ),
					'Taxonomy' => __( 'Taxonomy', 'media-library-assistant' ),
					'taxonomy_rows' => $row,
					'help' => $value['help']
				);

				return MLAData::mla_parse_template( MLAOptions::$mla_option_templates['taxonomy-table'], $option_values );
			case 'update':
			case 'delete':
				$tax_support = isset( $args['tax_support'] ) ? $args['tax_support'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_support'];
				$tax_quick_edit = isset( $args['tax_quick_edit'] ) ? $args['tax_quick_edit'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_quick_edit'];
				$tax_term_search = isset( $args['tax_term_search'] ) ? $args['tax_term_search'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_term_search'];
				$tax_flat_checklist = isset( $args['tax_flat_checklist'] ) ? $args['tax_flat_checklist'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_flat_checklist'];
				$tax_checked_on_top = isset( $args['tax_checked_on_top'] ) ? $args['tax_checked_on_top'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_checked_on_top'];
				$tax_filter = isset( $args['tax_filter'] ) ? $args['tax_filter'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_filter'];
				$tax_metakey_sort = isset( $args['tax_metakey_sort'] ) ? $args['tax_metakey_sort'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_metakey_sort'];
				$tax_metakey = isset( $args['tax_metakey'] ) ? stripslashes( $args['tax_metakey'] ) : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_metakey'];

				$msg = '';

				if ( !empty($tax_filter) && ! ( array_key_exists( $tax_filter, $tax_support ) || ( MLACoreOptions::MLA_FILTER_METAKEY == $tax_filter ) ) ) {
					/* translators: 1: taxonomy name */
					$msg .= '<br>' . sprintf( __( 'List Filter ignored; %1$s not supported.', 'media-library-assistant' ), $tax_filter ) . "\r\n";
					$tax_filter = '';
				}

				foreach ( $tax_quick_edit as $tax_name => $tax_value ) {
					if ( !array_key_exists( $tax_name, $tax_support ) ) {
						/* translators: 1: taxonomy name */
						$msg .= '<br>' . sprintf( __( 'Inline Edit ignored; %1$s not supported.', 'media-library-assistant' ), $tax_name ) . "\r\n";
						unset( $tax_quick_edit[ $tax_name ] );
					}
				}

				foreach ( $tax_term_search as $tax_name => $tax_value ) {
					if ( !array_key_exists( $tax_name, $tax_support ) ) {
						/* translators: 1: taxonomy name */
						$msg .= '<br>' . sprintf( __( 'Term Search ignored; %1$s not supported.', 'media-library-assistant' ), $tax_name ) . "\r\n";
						unset( $tax_term_search[ $tax_name ] );
					}
				}

				foreach ( $tax_flat_checklist as $tax_name => $tax_value ) {
					if ( 'disabled' == $tax_value ) {
						unset( $tax_flat_checklist[ $tax_name ] );
					} elseif ( !array_key_exists( $tax_name, $tax_support ) ) {
						/* translators: 1: taxonomy name */
						$msg .= '<br>' . sprintf( __( 'Checklist ignored; %1$s not supported.', 'media-library-assistant' ), $tax_name ) . "\r\n";
						unset( $tax_flat_checklist[ $tax_name ] );
					}
				}

				foreach ( $tax_checked_on_top as $tax_name => $tax_value ) {
					if ( !array_key_exists( $tax_name, $tax_support ) ) {
						/* translators: 1: taxonomy name */
						$msg .= '<br>' . sprintf( __( 'Checked On Top ignored; %1$s not supported.', 'media-library-assistant' ), $tax_name ) . "\r\n";
						unset( $tax_checked_on_top[ $tax_name ] );
					}
				}

				$value = array (
					'tax_support' => $tax_support,
					'tax_quick_edit' => $tax_quick_edit,
					'tax_term_search' => $tax_term_search,
					'tax_flat_checklist' => $tax_flat_checklist,
					'tax_checked_on_top' => $tax_checked_on_top,
					'tax_filter' => $tax_filter,
					'tax_metakey_sort' => $tax_metakey_sort,
					'tax_metakey' => $tax_metakey,
					);

				MLACore::mla_update_option( $key, $value );

				if ( empty( $msg ) ) {
					/* translators: 1: option name, e.g., taxonomy_support */
					$msg .= '<br>' . sprintf( __( 'Update custom %1$s', 'media-library-assistant' ), $key ) . "\r\n";
				}

				return $msg;
			case 'reset':
				MLACore::mla_delete_option( $key );
				/* translators: 1: option name, e.g., taxonomy_support */
				return '<br>' . sprintf( __( 'Reset custom %1$s', 'media-library-assistant' ), $key ) . "\r\n";
			default:
				/* translators: 1: ERROR tag 2: option name 3: action, e.g., update, delete, reset */
				return '<br>' . sprintf( __( '%1$s: Custom %2$s unknown action "%3$s"', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $key, $action ) . "\r\n";
		}
	} // mla_taxonomy_option_handler

	/**
	 * Render and manage Search box options, e.g., connector and search fields
 	 *
	 * @since 1.90
	 * @uses $mla_option_templates contains search-table template
	 *
	 * @param	string 	'render', 'update', 'delete', or 'reset'
	 * @param	string 	option name; 'search_connector' or 'search_fields'
	 * @param	array 	option parameters
	 * @param	array 	Optional. null (default) for 'render' else option data, e.g., $_REQUEST
	 *
	 * @return	string	HTML table row markup for 'render' else message(s) reflecting the results of the operation.
	 */
	public static function mla_search_option_handler( $action, $key, $value, $args = NULL ) {
		MLAOptions::_load_option_templates();

		switch ( $action ) {
			case 'render':
				$current_values = MLACore::mla_get_option( $key );
				$search_connector = isset( $current_values['search_connector'] ) ? $current_values['search_connector'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_SEARCH_MEDIA_FILTER_DEFAULTS ]['std']['search_connector'];
				$search_fields = isset( $current_values['search_fields'] ) ? $current_values['search_fields'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_SEARCH_MEDIA_FILTER_DEFAULTS ]['std']['search_fields'];

				$option_values = array (
					'and_checked' => ( 'AND' == $search_connector ) ? 'checked="checked"' : '',
					'AND' => __( 'and', 'media-library-assistant' ),
					'or_checked' => ( 'OR' == $search_connector ) ? 'checked="checked"' : '',
					'OR' => __( 'or', 'media-library-assistant' ),
					'title_checked' => ( in_array( 'title', $search_fields ) ) ? 'checked="checked"' : '',
					'Title' => __( 'Title', 'media-library-assistant' ),

					'name_checked' => ( in_array( 'name', $search_fields ) ) ? 'checked="checked"' : '',
					'Name' => __( 'Name', 'media-library-assistant' ),

					'alt_text_checked' => ( in_array( 'alt-text', $search_fields ) ) ? 'checked="checked"' : '',
					'ALT Text' => __( 'ALT Text', 'media-library-assistant' ),

					'excerpt_checked' => ( in_array( 'excerpt', $search_fields ) ) ? 'checked="checked"' : '',
					'Caption' => __( 'Caption', 'media-library-assistant' ),

					'content_checked' => ( in_array( 'content', $search_fields ) ) ? 'checked="checked"' : '',
					'Description' => __( 'Description', 'media-library-assistant' ),

					'file_checked' => ( in_array( 'file', $search_fields ) ) ? 'checked="checked"' : '',
					'File' => __( 'File', 'media-library-assistant' ),

					'terms_checked' => ( in_array( 'terms', $search_fields ) ) ? 'checked="checked"' : '',
					'Terms' => __( 'Terms', 'media-library-assistant' ),
					'help' => MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_SEARCH_MEDIA_FILTER_DEFAULTS ]['help']
				);

				return MLAData::mla_parse_template( MLAOptions::$mla_option_templates['search-table'], $option_values );
			case 'update':
			case 'delete':
				$search_connector = isset( $args['search_connector'] ) ? $args['search_connector'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_SEARCH_MEDIA_FILTER_DEFAULTS ]['std']['search_connector'];
				$search_fields = isset( $args['search_fields'] ) ? $args['search_fields'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_SEARCH_MEDIA_FILTER_DEFAULTS ]['std']['search_fields'];

				$msg = '';

				$value = array (
					'search_connector' => $search_connector,
					'search_fields' => $search_fields,
					);

				MLACore::mla_update_option( $key, $value );

				if ( empty( $msg ) ) {
					/* translators: 1: option name, e.g., taxonomy_support */
					$msg .= '<br>' . sprintf( __( 'Update custom %1$s', 'media-library-assistant' ), $key ) . "\r\n";
				}

				return $msg;
			case 'reset':
				MLACore::mla_delete_option( $key );
				/* translators: 1: option name, e.g., taxonomy_support */
				return '<br>' . sprintf( __( 'Reset custom %1$s', 'media-library-assistant' ), $key ) . "\r\n";
			default:
				/* translators: 1: ERROR tag 2: option name 3: action, e.g., update, delete, reset */
				return '<br>' . sprintf( __( '%1$s: Custom %2$s unknown action "%3$s"', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $key, $action ) . "\r\n";
		}
	} // mla_search_option_handler
	/**
	 * Examine or alter the filename before the file is made permanent
 	 *
	 * @since 1.70
	 *
	 * @param	array	file parameters ( 'name' )
	 *
	 * @return	array	updated file parameters
	 */
	public static function mla_wp_handle_upload_prefilter_filter( $file ) {
		/*
		 * This filter requires file access and processing, so only do the work
		 * if someone has hooked it.
		 */
		if ( has_filter( 'mla_upload_prefilter' ) ) {
			/* 
			 * The image.php file is not loaded for "front end" uploads
			 */
			if ( !function_exists( 'wp_read_image_metadata' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
			}

			$image_metadata =  MLAData::mla_fetch_attachment_image_metadata( 0, $file['tmp_name'] );
			$image_metadata['mla_exif_metadata']['FileName'] = $file['name'];
			$image_metadata['wp_image_metadata'] = wp_read_image_metadata( $file['tmp_name'] );
			$file = apply_filters( 'mla_upload_prefilter', $file, $image_metadata );
		}

		return $file;
 	} // mla_wp_handle_upload_prefilter_filter

	/**
	 * Called once for each file uploaded
 	 *
	 * @since 1.70
	 *
	 * @param	array	file parameters ( 'name' )
	 *
	 * @return	array	updated file parameters
	 */
	public static function mla_wp_handle_upload_filter( $file ) {
		/*
		 * This filter requires file access and processing, so only do the work
		 * if someone has hooked it.
		 */
		if ( has_filter( 'mla_upload_prefilter' ) ) {
			/* 
			 * The getid3.php file is not loaded for "front end" uploads
			 */
			if ( ! class_exists( 'getID3' ) ) {
				require( ABSPATH . WPINC . '/ID3/getid3.php' );
			}

			$id3 = new getID3();
			$id3_data = $id3->analyze( $file['file'] );
			$file = apply_filters( 'mla_upload_filter', $file, $id3_data );
		}

		return $file;
 	} // mla_wp_handle_upload_filter

	/**
	 * Attachment ID passed from mla_add_attachment_action to mla_update_attachment_metadata_filter
	 *
	 * Ensures that IPTC/EXIF and Custom Field mapping is only performed when the attachment is first
	 * added to the Media Library.
	 *
	 * @since 1.70
	 *
	 * @var	integer
	 */
	private static $add_attachment_id = 0;

	/**
	 * Set $add_attachment_id to just-inserted attachment
 	 *
	 * All of the actual processing is done later, in mla_update_attachment_metadata_filter.
	 *
	 * @since 1.00
	 *
	 * @param	integer	ID of just-inserted attachment
	 *
	 * @return	void
	 */
	public static function mla_add_attachment_action( $post_ID ) {
		MLACore::mla_debug_add( __LINE__ . " MLAOptions::mla_add_attachment_action( $post_ID )", MLACore::MLA_DEBUG_CATEGORY_METADATA );
		MLAOptions::$add_attachment_id = $post_ID;
		do_action('mla_add_attachment', $post_ID);
 	} // mla_add_attachment_action

	/**
	 * Update _wp_attachment_metadata for just-inserted attachment
 	 *
	 * @since 1.70
	 *
	 * @param	array	Attachment metadata updates
	 * @param	array	Attachment metadata, by reference; updated by this function
	 *
	 * @return	array	Attachment metadata updates, with "meta:" elements removed
	 */
	private static function _update_attachment_metadata( $updates, &$data ) {
		if ( is_array( $updates ) and isset( $updates['custom_updates'] ) ) {
			$attachment_meta_values = array();
			foreach ( $updates['custom_updates'] as $key => $value ) {
				if ( 'meta:' == substr( $key, 0, 5 ) ) {
					$meta_key = substr( $key, 5 );
					$attachment_meta_values[ $meta_key ] = $value;
					unset( $updates['custom_updates'][ $key ] );
				}
			} // foreach $updates

			if ( empty( $updates['custom_updates'] ) ) {
				unset( $updates['custom_updates'] );
			}

			if ( ! empty( $attachment_meta_values ) ) {
				$results = MLAData::mla_update_wp_attachment_metadata( $data, $attachment_meta_values );
			}
		} // custom_updates

		return $updates;
 	} // _update_attachment_metadata

	/**
	 * Perform IPTC/EXIF and Custom Field mapping on just-inserted attachment
 	 *
	 * This filter tests the $add_attachment_id variable set by the mla_add_attachment_action
	 * to ensure that mapping is only performed for new additions, not metadata updates.
	 *
	 * @since 1.10
	 *
	 * @param	array	Attachment metadata for just-inserted attachment
	 * @param	integer	ID of just-inserted attachment
	 *
	 * @return	array	Updated attachment metadata
	 */
	public static function mla_update_attachment_metadata_filter( $data, $post_id ) {
		$options = array ();
		$options['is_upload'] = MLAOptions::$add_attachment_id == $post_id;
		MLAOptions::$add_attachment_id = 0;

		$options['enable_iptc_exif_mapping'] = 'checked' == MLACore::mla_get_option( 'enable_iptc_exif_mapping' );
		$options['enable_custom_field_mapping'] = 'checked' == MLACore::mla_get_option( 'enable_custom_field_mapping' );
		$options['enable_iptc_exif_update'] = 'checked' == MLACore::mla_get_option( 'enable_iptc_exif_update' );
		$options['enable_custom_field_update'] = 'checked' == MLACore::mla_get_option( 'enable_custom_field_update' );

		$options = apply_filters( 'mla_update_attachment_metadata_options', $options, $data, $post_id );
		$data = apply_filters( 'mla_update_attachment_metadata_prefilter', $data, $post_id, $options );
		MLACore::mla_debug_add( __LINE__ . " MLAOptions::mla_update_attachment_metadata_filter( $post_id ) \$data = " . var_export( $data, true ), MLACore::MLA_DEBUG_CATEGORY_METADATA );
		MLACore::mla_debug_add( __LINE__ . " MLAOptions::mla_update_attachment_metadata_filter( $post_id ) \$options = " . var_export( $options, true ), MLACore::MLA_DEBUG_CATEGORY_METADATA );

		if ( $options['is_upload'] ) {
			if ( $options['enable_iptc_exif_mapping'] || $options['enable_custom_field_mapping'] ) {
				do_action( 'mla_begin_mapping', 'create_metadata', $post_id );
			}

			if ( $options['enable_iptc_exif_mapping'] ) {
				$item = get_post( $post_id );
				$updates = MLAOptions::mla_evaluate_iptc_exif_mapping( $item, 'iptc_exif_mapping', NULL, $data, true );
				MLACore::mla_debug_add( __LINE__ . " MLAOptions::mla_update_attachment_metadata_filter( $post_id ) \$updates = " . var_export( $updates, true ), MLACore::MLA_DEBUG_CATEGORY_METADATA );
				$updates = MLAOptions::_update_attachment_metadata( $updates, $data );
				MLACore::mla_debug_add( __LINE__ . " MLAOptions::mla_update_attachment_metadata_filter( $post_id ) \$updates = " . var_export( $updates, true ), MLACore::MLA_DEBUG_CATEGORY_METADATA );

				if ( !empty( $updates ) ) {
					$item_content = MLAData::mla_update_single_item( $post_id, $updates );
					MLACore::mla_debug_add( __LINE__ . " MLAOptions::mla_update_attachment_metadata_filter( $post_id ) \$item_content = " . var_export( $item_content, true ), MLACore::MLA_DEBUG_CATEGORY_METADATA );
				}
			}

			if ( $options['enable_custom_field_mapping'] ) {
				$updates = MLAOptions::mla_evaluate_custom_field_mapping( $post_id, 'single_attachment_mapping', NULL, $data );
				$updates = MLAOptions::_update_attachment_metadata( $updates, $data );

				if ( !empty( $updates ) ) {
					$item_content = MLAData::mla_update_single_item( $post_id, $updates );
				}
			}

			if ( $options['enable_iptc_exif_mapping'] || $options['enable_custom_field_mapping'] ) {
				do_action( 'mla_end_mapping' );
			}
		} else {
			if ( $options['enable_iptc_exif_update'] || $options['enable_custom_field_update'] ) {
				do_action( 'mla_begin_mapping', 'update_metadata', $post_id );
			}

			if ( $options['enable_iptc_exif_update'] ) {
				$item = get_post( $post_id );
				$updates = MLAOptions::mla_evaluate_iptc_exif_mapping( $item, 'iptc_exif_mapping', NULL, $data );
				$updates = MLAOptions::_update_attachment_metadata( $updates, $data );

				if ( !empty( $updates ) ) {
					$item_content = MLAData::mla_update_single_item( $post_id, $updates );
				}
			}

			if ( $options['enable_custom_field_update'] ) {
				$updates = MLAOptions::mla_evaluate_custom_field_mapping( $post_id, 'single_attachment_mapping', NULL, $data );
				$updates = MLAOptions::_update_attachment_metadata( $updates, $data );

				if ( !empty( $updates ) ) {
					$item_content = MLAData::mla_update_single_item( $post_id, $updates );
				}
			}

			if ( $options['enable_iptc_exif_update'] || $options['enable_custom_field_update'] ) {
				do_action( 'mla_end_mapping' );
			}
		}

		$data = apply_filters( 'mla_update_attachment_metadata_postfilter', $data, $post_id, $options );
		return $data;
	} // mla_update_attachment_metadata_filter

	/**
	 * Get IPTC/EXIF or custom field mapping data source; WP_ADMIN mode
	 *
	 * Compatibility shim for MLAData_Source::mla_get_data_source.
	 *
	 * @since 1.70
	 *
	 * @param	integer	post->ID of attachment
	 * @param	string 	category/scope to evaluate against: custom_field_mapping or single_attachment_mapping
	 * @param	array	data source specification ( name, *data_source, *keep_existing, *format, mla_column, quick_edit, bulk_edit, *meta_name, *option, no_null )
	 * @param	array 	(optional) _wp_attachment_metadata, default NULL (use current postmeta database value)
	 *
	 * @return	string|array	data source value
	 */
	public static function mla_get_data_source( $post_id, $category, $data_value, $attachment_metadata = NULL ) {
		if ( !class_exists( 'MLAData_Source' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-source.php' );
		}

		return MLAData_Source::mla_get_data_source( $post_id, $category, $data_value, $attachment_metadata );
	} // mla_get_data_source

	/**
	 * Identify custom field mapping data source; WP_ADMIN mode
	 *
	 * Compatibility shim for MLAData_Source::mla_is_data_source.
	 *
	 * @since 1.80
	 *
	 * @param	string 	candidate data source name
	 *
	 * @return	boolean	true if candidate name matches a data source
	 */
	public static function mla_is_data_source( $candidate_name ) {
		if ( !class_exists( 'MLAData_Source' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-source.php' );
		}

		return MLAData_Source::mla_is_data_source( $candidate_name );
	}

	/**
	 * Evaluate custom field mapping updates for a post
 	 *
	 * @since 1.10
	 *
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against: custom_field_mapping or single_attachment_mapping
	 * @param	array 	(optional) custom_field_mapping values, default NULL (use current option value)
	 * @param	array 	(optional) attachment_metadata, default NULL (use current postmeta database value)
	 *
	 * @return	array	Updates suitable for MLAData::mla_update_single_item, if any
	 */
	public static function mla_evaluate_custom_field_mapping( $post_id, $category, $settings = NULL, $attachment_metadata = NULL ) {
		if ( 'checked' != MLACore::mla_get_option( MLACoreOptions::MLA_ALLOW_CUSTOM_FIELD_MAPPING ) ) {
			return array();
		}

		if ( NULL == $settings ) {
			$settings = MLACore::mla_get_option( 'custom_field_mapping' );
		}

		$settings = apply_filters( 'mla_mapping_settings', $settings, $post_id, $category, $attachment_metadata );

		$custom_updates = array();
		foreach ( $settings as $setting_key => $setting_value ) {
			/*
			 * Convert checkbox value(s)
			 */
			$setting_value['no_null'] = isset( $setting_value['no_null'] ) && ( false !== $setting_value['no_null'] );

			$setting_value = apply_filters( 'mla_mapping_rule', $setting_value, $post_id, $category, $attachment_metadata );
			if ( NULL === $setting_value ) {
				continue;
			}

			if ( ( 'none' == $setting_value['data_source'] ) || ( isset( $setting_value['active'] ) && false == $setting_value['active'] ) ) {
				continue;
			}

			$new_text = MLAOptions::mla_get_data_source( $post_id, $category, $setting_value, $attachment_metadata );
			$new_text = apply_filters( 'mla_mapping_custom_value', $new_text, $setting_key, $post_id, $category, $attachment_metadata );

			if ( 'multi' == $setting_value['option'] ) {
				if ( ' ' == $new_text ) {
					$new_text = array(
						0x80000000 => $setting_value['option'],
						0x80000001 => $setting_value['keep_existing'],
						0x80000002 => $setting_value['no_null']
					);

					if ( ! $setting_value['no_null'] ) {
						$new_text [0x00000000] = ' ';
					}
				} elseif ( is_string( $new_text ) ) {
					$new_text = array(
						0x00000000 => $new_text,
						0x80000000 => $setting_value['option'],
						0x80000001 => $setting_value['keep_existing']
					);
				}

				$custom_updates[ $setting_value['name'] ] = $new_text;
			} else {
				if ( $setting_value['keep_existing'] ) {
					if ( 'meta:' == substr( $setting_value['name'], 0, 5 ) ) {
						$meta_key = substr( $setting_value['name'], 5 );

						if ( NULL === $attachment_metadata ) {
							$attachment_metadata = maybe_unserialize( get_metadata( 'post', $post->ID, '_wp_attachment_metadata', true ) );
						}

						if ( array( $attachment_metadata ) ) {
							$old_text = MLAData::mla_find_array_element( $meta_key, $attachment_metadata, 'array' );
						} else {
							$old_text = '';
						}
					} else { // } meta:
						if ( is_string( $old_text = get_metadata( 'post', $post_id, $setting_value['name'], true ) ) ) {
							$old_text = trim( $old_text );
						}
					}

					if ( ( ' ' != $new_text ) && empty( $old_text ) ) {
						$custom_updates[ $setting_value['name'] ] = $new_text;
					}
				} else { // } keep_existing
					if ( ' ' == $new_text && $setting_value['no_null'] ) {
						$new_text = NULL;
					}

					$custom_updates[ $setting_value['name'] ] = $new_text;
				}
			} // ! multi
		} // foreach new setting

		$updates = array();
		if ( ! empty( $custom_updates ) ) {
			$updates['custom_updates'] = $custom_updates;
		}

		$updates = apply_filters( 'mla_mapping_updates', $updates, $post_id, $category, $settings, $attachment_metadata );
		return $updates;
	} // mla_evaluate_custom_field_mapping

	/**
	 * Compose a Custom Field Options list with current selection
 	 *
	 * @since 1.10
	 * @uses $mla_option_templates contains row and table templates
	 *
	 * @param	string 	current selection or 'none' (default)
	 * @param	array 	optional list of terms to exclude from the list
	 *
	 * @return	string	HTML markup with select field options
	 */
	public static function mla_compose_custom_field_option_list( $selection = 'none', $blacklist = array() ) {
		MLAOptions::_load_option_templates();

		// Add the "None" option to the front of the list
		$option_template = MLAOptions::$mla_option_templates['custom-field-select-option'];
		$option_values = array (
			'selected' => ( 'none' == $selection ) ? 'selected="selected"' : '',
			'value' => 'none',
			'text' => '&mdash; ' . __( 'None (select a value)', 'media-library-assistant' ) . ' &mdash;'
		);
		$custom_field_options = MLAData::mla_parse_template( $option_template, $option_values );					

		// Add an option for each name without a rule, i.e., not in the blacklist
		$blacklist_names = array();
		foreach ( $blacklist as $value ) {
			$blacklist_names[] = $value['name'];
		}

		$custom_field_names = MLAOptions::_get_custom_field_names();
		foreach ( $custom_field_names as $value ) {
			if ( in_array( $value, $blacklist_names ) ) {
				continue;
			}

			$option_values = array (
				'selected' => ( $value == $selection ) ? 'selected="selected"' : '',
				'value' => esc_attr( $value ),
				'text' => esc_html( $value )
			);

			$custom_field_options .= MLAData::mla_parse_template( $option_template, $option_values );					
		} // foreach custom_field_name

		return $custom_field_options;
	} // mla_compose_custom_field_option_list

	/**
	 * Compose a (Custom Field) Data Source Options list with current selection
 	 *
	 * @since 1.10
	 * @uses $mla_option_templates contains row and table templates
	 *
	 * @param	string 	current selection or 'none' (default)
	 *
	 * @return	string	HTML markup with select field options
	 */
	public static function mla_compose_data_source_option_list( $selection = 'none' ) {
		MLAOptions::_load_option_templates();

		$option_template = MLAOptions::$mla_option_templates['custom-field-select-option'];

		$option_values = array (
			'selected' => ( 'none' == $selection ) ? 'selected="selected"' : '',
			'value' => 'none',
			'text' => '&mdash; ' . __( 'None (select a value)', 'media-library-assistant' ) . ' &mdash;'
		);
		$custom_field_options = MLAData::mla_parse_template( $option_template, $option_values );

		$option_values = array (
			'selected' => ( 'meta' == $selection ) ? 'selected="selected"' : '',
			'value' => 'meta',
			'text' => '&mdash; ' . __( 'Metadata (see below)', 'media-library-assistant' ) . ' &mdash;'
		);
		$custom_field_options .= MLAData::mla_parse_template( $option_template, $option_values );

		$option_values = array (
			'selected' => ( 'template' == $selection ) ? 'selected="selected"' : '',
			'value' => 'template',
			'text' => '&mdash; ' . __( 'Template (see below)', 'media-library-assistant' ) . ' &mdash;'
		);
		$custom_field_options .= MLAData::mla_parse_template( $option_template, $option_values );

		if ( !class_exists( 'MLAData_Source' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-source.php' );
		}

		$intermediate_sizes = get_intermediate_image_sizes();
		foreach ( MLAData_Source::$custom_field_data_sources as $value ) {
			$size_pos = strpos( $value, '[size]' );
			if ( $size_pos ) {
				$root_value = substr( $value, 0, $size_pos );
				foreach ( $intermediate_sizes as $size_name ) {
					$value = $root_value . '[' . $size_name . ']';
					$option_values = array (
						'selected' => ( $value == $selection ) ? 'selected="selected"' : '',
						'value' => esc_attr( $value ),
						'text' => $value
					);

					$custom_field_options .= MLAData::mla_parse_template( $option_template, $option_values );					
					} // foreach size_name
				continue;
			} else {
				$option_values = array (
					'selected' => ( $value == $selection ) ? 'selected="selected"' : '',
					'value' => esc_attr( $value ),
					'text' => $value
				);
			}

			$custom_field_options .= MLAData::mla_parse_template( $option_template, $option_values );					
		} // foreach custom_field_name

		return $custom_field_options;
	} // mla_compose_data_source_option_list

	/**
	 * Update custom field mappings
 	 *
	 * @since 1.10
	 *
	 * @param	array 	current custom_field_mapping values 
	 * @param	array	new values
	 *
	 * @return	array	( 'message' => HTML message(s) reflecting results, 'values' => updated custom_field_mapping values, 'changed' => true if any changes detected else false )
	 */
	private static function _update_custom_field_mapping( $current_values, $new_values ) {
		$error_list = '';
		$message_list = '';
		$settings_changed = false;
		$custom_field_names = MLAOptions::_get_custom_field_names();
		$new_values = stripslashes_deep( $new_values );

		foreach ( $new_values as $the_key => $new_value ) {
			$any_setting_changed = false;
			/*
			 * Replace index with field name
			 */
			$new_key = trim( $new_value['name'] );

			/*
			 * Check for the addition of a new rule or field
			 */
			if ( MLACoreOptions::MLA_NEW_CUSTOM_FIELD === $the_key ) {
				if ( empty( $new_key ) ) {
					continue;
				}

				if ( in_array( $new_key, $custom_field_names ) ) {
					/* translators: 1: ERROR tag 2: custom field name */
					$error_list .= '<br>' . sprintf( __( '%1$s: New field %2$s already exists.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), esc_html( $new_key ) ) . "\r\n";
					continue;
				}

				/* translators: 1: custom field name */
				$message_list .= '<br>' . sprintf( __( 'Adding new field %1$s.', 'media-library-assistant' ), esc_html( $new_key ) ) . "\r\n";
				$any_setting_changed = true;
			} elseif ( MLACoreOptions::MLA_NEW_CUSTOM_RULE === $the_key ) {
				if ( 'none' == $new_key ) {
					continue;
				}

				/* translators: 1: custom field name */
				$message_list .= '<br>' . sprintf( __( 'Adding new rule for %1$s.', 'media-library-assistant' ), esc_html( $new_key ) ) . "\r\n";
				$any_setting_changed = true;
			}

			if ( isset( $current_values[ $new_key ] ) ) {
				$old_values = $current_values[ $new_key ];
				$any_setting_changed = false;
			} else {
				$old_values = array(
					'name' => $new_key,
					'data_source' => 'none',
					'keep_existing' => true,
					'format' => 'native',
					'mla_column' => false,
					'quick_edit' => false,
					'bulk_edit' => false,
					'meta_name' => '',
					'option' => 'text',
					'no_null' => false
				);
			}

			if ( isset( $new_value['action'] ) ) {
				if ( array_key_exists( 'delete_rule', $new_value['action'] ) || array_key_exists( 'delete_field', $new_value['action'] ) ) {
					$settings_changed = true;
					/* translators: 1: custom field name */
					$message_list .= '<br>' . sprintf( __( 'Deleting rule for %1$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ) ) . "\r\n";
					unset( $current_values[ $new_key ] );
					continue;
				} // delete rule
			} // isset action

			/*
			 * For "meta:" fields, the UI options are not appropriate
			 */
			if ( 'meta:' == substr( $new_key, 0, 5 ) ) {
				unset( $new_value['mla_column'] );
				unset( $new_value['quick_edit'] );
				unset( $new_value['bulk_edit'] );
			}

			if ( $old_values['data_source'] != $new_value['data_source'] ) {
				$any_setting_changed = true;

				if ( in_array( $old_values['data_source'], array( 'meta', 'template' ) ) ) {
					$new_value['meta_name'] = '';
				}

				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Data Source', 'media-library-assistant' ), $old_values['data_source'], $new_value['data_source'] ) . "\r\n";
				$old_values['data_source'] = $new_value['data_source'];
			}

			if ( $new_value['keep_existing'] ) {
				$boolean_value = true;
				$boolean_text = __( 'Replace to Keep', 'media-library-assistant' );
			} else {
				$boolean_value = false;
				$boolean_text = __( 'Keep to Replace', 'media-library-assistant' );
			}
			if ( $old_values['keep_existing'] != $boolean_value ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 'to' new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s value from %3$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Existing Text', 'media-library-assistant' ), $boolean_text ) . "\r\n";
				$old_values['keep_existing'] = $boolean_value;
			}

			if ( $old_values['format'] != $new_value['format'] ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Format', 'media-library-assistant' ), $old_values['format'], $new_value['format'] ) . "\r\n";
				$old_values['format'] = $new_value['format'];
			}

			if ( isset( $new_value['mla_column'] ) ) {
				$boolean_value = true;
				$boolean_text = __( 'unchecked to checked', 'media-library-assistant' );
			} else {
				$boolean_value = false;
				$boolean_text = __( 'checked to unchecked', 'media-library-assistant' );
			}
			if ( $old_values['mla_column'] != $boolean_value ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 'to' new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s value from %3$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'MLA Column', 'media-library-assistant' ), $boolean_text ) . "\r\n";
				$old_values['mla_column'] = $boolean_value;
			}

			if ( isset( $new_value['quick_edit'] ) ) {
				$boolean_value = true;
				$boolean_text = __( 'unchecked to checked', 'media-library-assistant' );
			} else {
				$boolean_value = false;
				$boolean_text = __( 'checked to unchecked', 'media-library-assistant' );
			}
			if ( $old_values['quick_edit'] != $boolean_value ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 'to' new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s value from %3$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Quick Edit', 'media-library-assistant' ), $boolean_text ) . "\r\n";
				$old_values['quick_edit'] = $boolean_value;
			}

			if ( isset( $new_value['bulk_edit'] ) ) {
				$boolean_value = true;
				$boolean_text = __( 'unchecked to checked', 'media-library-assistant' );
			} else {
				$boolean_value = false;
				$boolean_text = __( 'checked to unchecked', 'media-library-assistant' );
			}
			if ( $old_values['bulk_edit'] != $boolean_value ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 'to' new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s value from %3$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Bulk Edit', 'media-library-assistant' ), $boolean_text ) . "\r\n";
				$old_values['bulk_edit'] = $boolean_value;
			}

			if ( $old_values['meta_name'] != $new_value['meta_name'] ) {
				$any_setting_changed = true;

				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Metavalue name', 'media-library-assistant' ), $old_values['meta_name'], $new_value['meta_name'] ) . "\r\n";
				$old_values['meta_name'] = $new_value['meta_name'];
			}

			if ( $old_values['option'] != $new_value['option'] ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Option', 'media-library-assistant' ), $old_values['option'], $new_value['option'] ) . "\r\n";
				$old_values['option'] = $new_value['option'];
			}

			if ( isset( $new_value['no_null'] ) ) {
				$boolean_value = true;
				$boolean_text = __( 'unchecked to checked', 'media-library-assistant' );
			} else {
				$boolean_value = false;
				$boolean_text = __( 'checked to unchecked', 'media-library-assistant' );
			}
			if ( $old_values['no_null'] != $boolean_value ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 'to' new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s value from %3$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Delete NULL values', 'media-library-assistant' ), $boolean_text ) . "\r\n";
				$old_values['no_null'] = $boolean_value;
			}

			if ( $any_setting_changed ) {
				$settings_changed = true;
				$current_values[ $new_key ] = $old_values;
			}
		} // foreach new value

		/*
		 * Uncomment this for debugging.
		 */
		// $error_list .= $message_list;

		return array( 'message' => $error_list, 'values' => $current_values, 'changed' => $settings_changed );
	} // _update_custom_field_mapping

	/**
	 * Render and manage custom field mapping options
 	 *
	 * @since 1.10
	 * @uses $mla_option_templates contains row and table templates
	 *
	 * @param	string 	'render', 'update', 'delete', or 'reset'
	 * @param	string 	option name, e.g., 'custom_field_mapping'
	 * @param	array 	option parameters
	 * @param	array 	Optional. null (default) for 'render' else option data, e.g., $_REQUEST
	 *
	 * @return	string	HTML table row markup for 'render' else message(s) reflecting the results of the operation.
	 */
	public static function mla_custom_field_option_handler( $action, $key, $value, $args = NULL ) {
error_log( __LINE__ . " MLAOptions::mla_custom_field_option_handler( $action, $key )", 0 );
return "MLAOptions::mla_custom_field_option_handler( $action, $key ) deprecated.";

		// Added in 2.63
		MLAOptions::_load_option_templates();

		$current_values = MLACore::mla_get_option( 'custom_field_mapping' );
	} // mla_custom_field_option_handler

	/**
	 * Build and search a cache of taxonomy and term name to term ID mappings
 	 *
	 * @since 2.01
	 *
	 * @param	string 	term name (not slug)
	 * @param	integer zero or term's parent term_id
	 * @param	string 	taxonomy slug
	 * @param	array 	term objects currently assigned to the item
	 *
	 * @return	integer	term_id for the term name
	 */
	private static function _get_term_id( $term_name, $term_parent, $taxonomy, &$post_terms ) {
		static $term_cache = array();

		// WordPress encodes special characters, e.g., "&" as HTML entities in term names
		$term_name = _wp_specialchars( $term_name );

		if ( isset( $term_cache[ $taxonomy ] ) && isset( $term_cache[ $taxonomy ][ $term_parent ] ) && isset( $term_cache[ $taxonomy ][ $term_parent ][ $term_name ] ) ) {
			return $term_cache[ $taxonomy ][ $term_parent ][ $term_name ];
		}

		if ( is_array( $post_terms ) ) {
			$term_id = 0;
			foreach( $post_terms as $post_term ) {
				$term_cache[ $taxonomy ][ $post_term->parent ][ $post_term->name ] = $post_term->term_id;
				if ( $term_name == $post_term->name && $term_parent == $post_term->parent ) {
					$term_id = $post_term->term_id;
				}
			}

			if ( 0 < $term_id ) {
				return $term_id;
			}
		}

		if ( 0 === $term_parent ) {
			$post_term = get_term_by( 'name', $term_name, $taxonomy );
			if ( false !== $post_term ) {
				$term_cache[ $taxonomy ][ $term_parent ][ $term_name ] = $post_term->term_id;
				return $post_term->term_id;
			}
		} else {
			$post_term = term_exists( $term_name, $taxonomy, $term_parent );
			if ( $post_term !== 0 && $post_term !== NULL ) {
				$term_cache[ $taxonomy ][ $term_parent ][ $term_name ] = absint( $post_term['term_id'] );
				return absint( $post_term['term_id'] );
			}
		}

		$post_term = wp_insert_term( $term_name, $taxonomy, array( 'parent' => $term_parent ) );
		if ( ( ! is_wp_error( $post_term ) ) && isset( $post_term['term_id'] ) ) {
			$term_cache[ $taxonomy ][ $term_parent ][ $term_name ] = $post_term['term_id'];
			return $post_term['term_id'];
		}

		return 0;
	} // _get_term_id

	/**
	 * Evaluate IPTC/EXIF mapping updates for a post
 	 *
	 * @since 1.00
	 *
	 * @param	object 	post object with current values
	 * @param	string 	category to evaluate against, e.g., iptc_exif_standard_mapping or iptc_exif_mapping
	 * @param	array 	(optional) iptc_exif_mapping values, default - current option value
	 * @param	array 	(optional) _wp_attachment_metadata, for MLAOptions::mla_update_attachment_metadata_filter
	 * @param	boolean	(optional) true if uploading a new item else false (default)
	 *
	 * @return	array	Updates suitable for MLAData::mla_update_single_item, if any
	 */
	public static function mla_evaluate_iptc_exif_mapping( $post, $category, $settings = NULL, $attachment_metadata = NULL, $is_upload = false ) {
		if ( 'checked' != MLACore::mla_get_option( MLACoreOptions::MLA_ALLOW_IPTC_EXIF_MAPPING ) ) {
			return array();
		}

		$image_metadata = MLAData::mla_fetch_attachment_image_metadata( $post->ID );

		/*
		 * Make the PDF/XMP metadata available as EXIF values so simple rules like "EXIF:Keywords" will work
		 */
		if ( empty( $image_metadata['mla_exif_metadata'] ) ) {
			if ( ! empty( $image_metadata['mla_xmp_metadata'] ) ) {
				$image_metadata['mla_exif_metadata'] = $image_metadata['mla_xmp_metadata'];
			} elseif ( ! empty( $image_metadata['mla_pdf_metadata'] ) ) {
				$image_metadata['mla_exif_metadata'] = $image_metadata['mla_pdf_metadata'];
			}
		}

		$updates = array();
		$update_all = ( 'iptc_exif_mapping' == $category );
		$data_source_category = $update_all ? 'single_attachment_mapping' : 'custom_field_mapping';

		if ( NULL == $settings ) {
			$settings = MLACore::mla_get_option( 'iptc_exif_mapping' );
		}

		$settings = apply_filters( 'mla_mapping_settings', $settings, $post->ID, $category, $attachment_metadata );

		MLACore::mla_debug_add( __LINE__ . " MLAOptions::mla_evaluate_iptc_exif_mapping( {$post->ID}, {$category}, {$data_source_category} ) \$settings = " . var_export( $settings, true ), MLACore::MLA_DEBUG_CATEGORY_METADATA );

		if ( $update_all || ( 'iptc_exif_standard_mapping' == $category ) ) {
			foreach ( $settings['standard'] as $setting_key => $setting_value ) {
				$setting_value = apply_filters( 'mla_mapping_rule', $setting_value, $post->ID, 'iptc_exif_standard_mapping', $attachment_metadata );
				if ( NULL === $setting_value ) {
					continue;
				}

				if ( isset( $setting_value['active'] ) && false == $setting_value['active'] ) {
					continue;
				}

				if ( 'none' == $setting_value['iptc_value'] ) {
					$iptc_value = '';
				} else {
					$iptc_value = MLAData::mla_iptc_metadata_value( $setting_value['iptc_value'], $image_metadata );
				}

				$iptc_value = apply_filters( 'mla_mapping_iptc_value', $iptc_value, $setting_key, $post->ID, 'iptc_exif_standard_mapping', $attachment_metadata );

				if ( 'template:[+empty+]' == $setting_value['exif_value'] ) {
					$exif_value =  NULL;
				} elseif ( 'template:' == substr( $setting_value['exif_value'], 0, 9 ) ) {
					$data_value = array(
						'name' => $setting_key,
						'data_source' => 'template',
						'meta_name' => substr( $setting_value['exif_value'], 9 ),
						'keep_existing' => $setting_value['keep_existing'],
						'format' => 'native',
						'option' => 'text' );

					$exif_value =  MLAOptions::mla_get_data_source( $post->ID, $data_source_category, $data_value, $attachment_metadata );
					if ( ' ' == $exif_value ) {
						$exif_value = '';
					}
				} else {
					$exif_value = MLAData::mla_exif_metadata_value( $setting_value['exif_value'], $image_metadata );
				}

				$exif_value = apply_filters( 'mla_mapping_exif_value', $exif_value, $setting_key, $post->ID, 'iptc_exif_standard_mapping', $attachment_metadata );

				$keep_existing = (boolean) $setting_value['keep_existing'];

				if ( $setting_value['iptc_first'] ) {
					if ( ! empty( $iptc_value ) ) {
						$new_text = $iptc_value;
					} else {
						$new_text = $exif_value;
					}
				} else {
					if ( ( ! empty( $exif_value ) ) || is_null( $exif_value ) ) {
						$new_text = $exif_value;
					} else {
						$new_text = $iptc_value;
					}
				}

				$new_text = apply_filters( 'mla_mapping_new_text', $new_text, $setting_key, $post->ID, 'iptc_exif_standard_mapping', $attachment_metadata );

				if ( is_array( $new_text ) ) {
					$new_text = implode( ',', $new_text );
				}

				// Handle 'template:[+empty+]'
				if ( is_null( $new_text ) ) {
					$updates[ $setting_key ] = '';
					continue;
				}

				/*
				 * See /wp-includes/formatting.php, function convert_chars()
				 *
				 * Metadata tags <<title>> and <<category>> are removed, <<br>> and <<hr>> are
				 * converted into correct XHTML and Unicode characters are converted to the
				 * valid range.
				 */
				$new_text = trim( convert_chars( $new_text ) );
				if ( !empty( $new_text ) ) {
					switch ( $setting_key ) {
						case 'post_title':
							if ( ( empty( $post->post_title ) || !$keep_existing ) &&
							( trim( $new_text ) && ! is_numeric( sanitize_title( $new_text ) ) ) )
								$updates[ $setting_key ] = $new_text;
							break;
						case 'post_name':
							$updates[ $setting_key ] = wp_unique_post_slug( sanitize_title( $new_text ), $post->ID, $post->post_status, $post->post_type, $post->post_parent);
							break;
						case 'image_alt':
							$old_text = get_metadata( 'post', $post->ID, '_wp_attachment_image_alt', true );
							if ( empty( $old_text ) || !$keep_existing ) {
								$updates[ $setting_key ] = $new_text;							}
							break;
						case 'post_excerpt':
							if ( empty( $post->post_excerpt ) || !$keep_existing ) {
								$updates[ $setting_key ] = $new_text;
							}
							break;
						case 'post_content':
							if ( empty( $post->post_content ) || !$keep_existing ) {
								$updates[ $setting_key ] = $new_text;
							}
							break;
						default:
							// ignore anything else
					} // $setting_key
				}
			} // foreach new setting
		} // update standard field mappings

		if ( $update_all || ( 'iptc_exif_taxonomy_mapping' == $category ) ) {
			$tax_inputs = array();
			$tax_actions =  array();

			foreach ( $settings['taxonomy'] as $setting_key => $setting_value ) {
				if ( ! MLACore::mla_taxonomy_support($setting_key, 'support') ) {
					continue;
				}

				/*
				 * Convert checkbox value(s)
				 */
				$hierarchical = $setting_value['hierarchical'] = (boolean) $setting_value['hierarchical'];

				$setting_value = apply_filters( 'mla_mapping_rule', $setting_value, $post->ID, 'iptc_exif_taxonomy_mapping', $attachment_metadata );
				if ( NULL === $setting_value ) {
					continue;
				}

				if ( isset( $setting_value['active'] ) && false == $setting_value['active'] ) {
					continue;
				}

				if ( 'none' == $setting_value['iptc_value'] ) {
					$iptc_value = '';
				} else {
					$iptc_value = MLAData::mla_iptc_metadata_value( $setting_value['iptc_value'], $image_metadata );
				}

				$iptc_value = apply_filters( 'mla_mapping_iptc_value', $iptc_value, $setting_key, $post->ID, 'iptc_exif_taxonomy_mapping', $attachment_metadata );

				if ( 'template:' == substr( $setting_value['exif_value'], 0, 9 ) ) {
					$data_value = array(
						'name' => $setting_key,
						'data_source' => 'template',
						'meta_name' => substr( $setting_value['exif_value'], 9 ),
						'keep_existing' => $setting_value['keep_existing'],
						'format' => 'native',
						'option' => 'array' );

					$exif_value =  MLAOptions::mla_get_data_source( $post->ID, $data_source_category, $data_value, $attachment_metadata );
					if ( ' ' == $exif_value ) {
						$exif_value = '';
					}
				} else {
					$exif_value = MLAData::mla_exif_metadata_value( $setting_value['exif_value'], $image_metadata );
				}

				$exif_value = apply_filters( 'mla_mapping_exif_value', $exif_value, $setting_key, $post->ID, 'iptc_exif_taxonomy_mapping', $attachment_metadata );

				$tax_action = ( $setting_value['keep_existing'] ) ? 'add' : 'replace';
				$tax_parent = ( isset( $setting_value['parent'] ) && (0 != (integer) $setting_value['parent'] ) ) ? (integer) $setting_value['parent'] : 0;

				if ( $setting_value['iptc_first'] ) {
					if ( ! empty( $iptc_value ) ) {
						$new_text = $iptc_value;
					} else {
						$new_text = $exif_value;
					}
				} else {
					if ( ! empty( $exif_value ) ) {
						$new_text = $exif_value;
					} else {
						$new_text = $iptc_value;
					}
				}

				/*
				 * Parse out individual terms
				 */
				if ( ! empty( $setting_value['delimiters'] ) ) {
					$text = $setting_value['delimiters'];
					$delimiters = array();
					while ( ! empty( $text ) ) {
						$delimiters[] = $text[0];
						$text = substr($text, 1);
					}
				} else {
					$delimiters = array( _x( ',', 'tag_delimiter', 'media-library-assistant' ) );
				}

				if ( is_scalar( $new_text ) ) {
					$new_text = array( $new_text );
				}

				foreach( $delimiters as $delimiter ) {
					$new_terms = array();
					foreach ( $new_text as $text ) {
							$fragments = explode( $delimiter, $text );
							foreach( $fragments as $fragment ) {
								if ( MLATest::$wp_3dot5 ) {
									$fragment = trim( stripslashes_deep( $fragment ) );
								} else {
									$fragment = trim( wp_unslash( $fragment ) );
								}

								if ( ! empty( $fragment ) ) {
									$new_terms[] = $fragment;
								}
							} // foreach fragment
					} // foreach $text
					$new_text = array_unique( $new_terms );
				} // foreach $delimiter

				$new_text = apply_filters( 'mla_mapping_new_text', $new_text, $setting_key, $post->ID, 'iptc_exif_taxonomy_mapping', $attachment_metadata );

				if ( empty( $new_text ) ) {
					continue;
				}

				$current_terms = array();
				if ( ! $is_upload ) {
					$post_terms = get_object_term_cache( $post->ID, $setting_key );
					if ( false === $post_terms ) {
						$post_terms = wp_get_object_terms( $post->ID, $setting_key );
						wp_cache_add( $post->ID, $post_terms, $setting_key . '_relationships' );
					}

					foreach( $post_terms as $new_term ) {
						if ( $hierarchical ) {
							$current_terms[ $new_term->term_id ] =  $new_term->term_id;
						} else {
							$current_terms[ $new_term->name ] =  $new_term->name;
						}
					}
				}

				/*
				 * Hierarchical taxonomies require term_id, flat require term names
				 */
				if ( $hierarchical ) {
					/*
					 * Convert text to term_id
					 */
					$new_terms = array();
					foreach ( $new_text as $new_term ) {
						if ( 0 < $new_term = MLAOptions::_get_term_id( $new_term, $tax_parent, $setting_key, $post_terms ) ) {
							$new_terms[] = $new_term;
						}
					} // foreach new_term
				} else {
					$new_terms = $new_text;
				}

				if ( 'replace' == $tax_action ) {
					/*
					 * If the new terms match the term cache, we can skip the update
					 */
					foreach ( $new_terms as $new_term ) {
						if ( isset( $current_terms[ $new_term ] ) ) {
							unset( $current_terms[ $new_term ] );
						} else {
							$current_terms[ $new_term ] = $new_term;
							break; // not a match; stop checking
						}
					}

					$do_update = ! empty( $current_terms );
				} else {
					/*
					 * We are adding terms; remove existing terms
					 */
					foreach ( $new_terms as $index => $new_term ) {
						if ( isset( $current_terms[ esc_attr( $new_term ) ] ) ) {
							unset( $new_terms[ $index ] );
						}
					}

					$do_update = ! empty( $new_terms );
				}

				if ( $do_update ) {
					$tax_inputs[ $setting_key ] = $new_terms;
					$tax_actions[ $setting_key ] = $tax_action;
				}
			} // foreach new setting

			if ( ! empty( $tax_inputs ) ) {
				$updates['taxonomy_updates'] = array ( 'inputs' => $tax_inputs, 'actions' => $tax_actions );
			}
		} // update taxonomy term mappings

		if ( ( $update_all || ( 'iptc_exif_custom_mapping' == $category ) ) && !empty( $settings['custom'] ) ) {
			$custom_updates = array();
			foreach ( $settings['custom'] as $setting_key => $setting_value ) {
				/*
				 * Convert checkbox value(s)
				 */
				$setting_value['no_null'] = isset( $setting_value['no_null'] );

				$setting_name = $setting_value['name'];
				$setting_value = apply_filters( 'mla_mapping_rule', $setting_value, $post->ID, 'iptc_exif_custom_mapping', $attachment_metadata );
				if ( NULL === $setting_value ) {
					continue;
				}

				if ( isset( $setting_value['active'] ) && false == $setting_value['active'] ) {
					continue;
				}

				if ( 'none' == $setting_value['iptc_value'] ) {
					$iptc_value = '';
				} else {
					$data_value = array(
						'name' => $setting_key,
						'data_source' => 'template',
						'meta_name' => '([+iptc:' . $setting_value['iptc_value'] . '+])',
						'keep_existing' => $setting_value['keep_existing'],
						'format' => $setting_value['format'],
						'option' => $setting_value['option'] );

					$iptc_value = MLAOptions::mla_get_data_source( $post->ID, $data_source_category, $data_value, $attachment_metadata );
					if ( ' ' == $iptc_value ) {
						$iptc_value = '';
					}
				}

				$iptc_value = apply_filters( 'mla_mapping_iptc_value', $iptc_value, $setting_key, $post->ID, 'iptc_exif_custom_mapping', $attachment_metadata );

				$exif_value = trim( $setting_value['exif_value'] );
				if ( ! empty( $exif_value ) ) {
					$data_value = array(
						'name' => $setting_key,
						'data_source' => 'template',
						'keep_existing' => $setting_value['keep_existing'],
						'format' => $setting_value['format'],
						'option' => $setting_value['option'] );

					if ( 'template:' == substr( $exif_value, 0, 9 ) ) {
						$data_value['meta_name'] = substr( $exif_value, 9 );
					} else {
						$data_value['meta_name'] = '([+exif:' . $exif_value . '+])';
					}

					$exif_value =  MLAOptions::mla_get_data_source( $post->ID, $data_source_category, $data_value, $attachment_metadata );
					if ( ' ' == $exif_value ) {
						$exif_value = '';
					}
				}

				$exif_value = apply_filters( 'mla_mapping_exif_value', $exif_value, $setting_key, $post->ID, 'iptc_exif_custom_mapping', $attachment_metadata );

				if ( $setting_value['iptc_first'] ) {
					if ( ! empty( $iptc_value ) ) {
						$new_text = $iptc_value;
					} else {
						$new_text = $exif_value;
					}
				} else {
					if ( ! empty( $exif_value ) ) {
						$new_text = $exif_value;
					} else {
						$new_text = $iptc_value;
					}
				}

				$new_text = apply_filters( 'mla_mapping_new_text', $new_text, $setting_key, $post->ID, 'iptc_exif_custom_mapping', $attachment_metadata );

				if ( $setting_value['keep_existing'] ) {
					if ( 'meta:' == substr( $setting_name, 0, 5 ) ) {
						$meta_key = substr( $setting_name, 5 );

						if ( NULL === $attachment_metadata ) {
							$attachment_metadata = maybe_unserialize( get_metadata( 'post', $post->ID, '_wp_attachment_metadata', true ) );
						}

						if ( array( $attachment_metadata ) ) {
							$old_value = MLAData::mla_find_array_element( $meta_key, $attachment_metadata, 'array' );
						} else {
							$old_value = '';
						}
					} else {
						if ( is_string( $old_value = get_metadata( 'post', $post->ID, $setting_name, true ) ) ) {
							$old_value = trim( $old_value );
						}
					}

					if ( ( ! empty( $new_text ) ) && empty( $old_value ) ) {
						$custom_updates[ $setting_name ] = $new_text;
					}
				} else { // } keep_existing
					if ( empty( $new_text ) && $setting_value['no_null'] ) {
						$new_text = NULL;
					}

					$custom_updates[ $setting_name ] = $new_text;
				}
			} // foreach new setting

			if ( ! empty( $custom_updates ) ) {
				$updates['custom_updates'] = $custom_updates;
			}
		} // update custom field mappings

		$updates = apply_filters( 'mla_mapping_updates', $updates, $post->ID, $category, $settings, $attachment_metadata );
		MLACore::mla_debug_add( __LINE__ . " MLAOptions::mla_evaluate_iptc_exif_mapping( {$post->ID}, {$category}, {$data_source_category} ) \$updates = " . var_export( $updates, true ), MLACore::MLA_DEBUG_CATEGORY_METADATA );

		return $updates;
	} // mla_evaluate_iptc_exif_mapping

	/**
	 * Compose an IPTC Options list with current selection
 	 *
	 * @since 1.00
	 * @uses $mla_option_templates contains row and table templates
	 *
	 * @param	string 	current selection or 'none' (default)
	 *
	 * @return	string	HTML markup with select field options
	 */
	public static function mla_compose_iptc_option_list( $selection = 'none' ) {
		MLAOptions::_load_option_templates();

		$option_template = MLAOptions::$mla_option_templates['iptc-exif-select-option'];
		$option_values = array (
			'selected' => ( 'none' == $selection ) ? 'selected="selected"' : '',
			'value' => 'none',
			'text' => '&mdash; ' . __( 'None (select a value)', 'media-library-assistant' ) . ' &mdash;'
		);

		$iptc_options = MLAData::mla_parse_template( $option_template, $option_values );					
		foreach ( MLAData::$mla_iptc_keys as $iptc_name => $iptc_code ) {
			$option_values = array (
				'selected' => ( $iptc_code == $selection ) ? 'selected="selected"' : '',
				'value' => $iptc_code,
				'text' => $iptc_code . ' ' . $iptc_name
			);

			$iptc_options .= MLAData::mla_parse_template( $option_template, $option_values );					
		} // foreach iptc_key

		return $iptc_options;
	} // mla_compose_iptc_option_list

	/**
	 * Compose an hierarchical taxonomy Parent options list with current selection
 	 *
	 * @since 1.00
	 * @uses $mla_option_templates contains row and table templates
	 *
	 * @param	string 	taxonomy slug
	 * @param	integer	current selection or 0 (zero, default)
	 *
	 * @return	string	HTML markup with select field options
	 */
	public static function mla_compose_parent_option_list( $taxonomy, $selection = 0 ) {
		$dropdown_options = array(
			'show_option_all' => '',
			'show_option_none' => '&mdash; ' . __( 'None (select a value)', 'media-library-assistant' ) . ' &mdash;',
			'orderby' => 'name',
			'order' => 'ASC',
			'show_count' => false,
			'hide_empty' => false,
			'child_of' => 0,
			'exclude' => '',
			// 'exclude_tree => '', 
			'echo' => true,
			'depth' => 0,
			'tab_index' => 0,
			'name' => 'mla_filter_term',
			'id' => 'name',
			'class' => 'postform',
			'selected' => ( 0 == $selection) ? -1 : $selection,
			'hierarchical' => true,
			'pad_counts' => false,
			'taxonomy' => $taxonomy,
			'hide_if_empty' => false 
		);

		ob_start();
		wp_dropdown_categories( $dropdown_options );
		$dropdown = ob_get_contents();
		ob_end_clean();

		$dropdown_options = substr( $dropdown, strpos( $dropdown, ' >' ) + 2 );
		$dropdown_options = substr( $dropdown_options, 0, strpos( $dropdown_options, '</select>' ) );
		$dropdown_options = str_replace( "value='-1' ", 'value="0"', $dropdown_options );

		return $dropdown_options;
	} // mla_compose_parent_option_list

	/**
	 * Update Standard field portion of IPTC/EXIF mappings
 	 *
	 * @since 1.00
	 *
	 * @param	array 	current iptc_exif_mapping values 
	 * @param	array	new values
	 *
	 * @return	array	( 'message' => HTML message(s) reflecting results, 'values' => updated iptc_exif_mapping values, 'changed' => true if any changes detected else false )
	 */
	private static function _update_iptc_exif_standard_mapping( $current_values, $new_values ) {
		$error_list = '';
		$message_list = '';
		$settings_changed = false;
		$new_values = stripslashes_deep( $new_values );

		foreach ( $new_values['standard'] as $new_key => $new_value ) {
			if ( isset( $current_values['standard'][ $new_key ] ) ) {
				$old_values = $current_values['standard'][ $new_key ];
				$any_setting_changed = false;
			} else {
				/* translators: 1: ERROR tag 2: custom field name */
				$error_list .= '<br>' . sprintf( __( '%1$s: No old values for %2$s.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), esc_html( $new_key ) ) . "\r\n";
				continue;
			}

			/*
			 * Field Title can change as a result of localization
			 */
			$new_value['name'] = MLACoreOptions::$mla_option_definitions['iptc_exif_mapping']['std']['standard'][ $new_key ]['name'];

			if ( $old_values['name'] != $new_value['name'] ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Field Title', 'media-library-assistant' ), esc_html( $old_values['name'] ), esc_html( $new_value['name'] ) ) . "\r\n";
				$old_values['name'] = $new_value['name'];
			}

			if ( $old_values['iptc_value'] != $new_value['iptc_value'] ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'IPTC Value', 'media-library-assistant' ), $old_values['iptc_value'], $new_value['iptc_value'] ) . "\r\n";
				$old_values['iptc_value'] = $new_value['iptc_value'];
			}

			if ( $old_values['exif_value'] != $new_value['exif_value'] ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'EXIF Value', 'media-library-assistant' ), $old_values['exif_value'], $new_value['exif_value'] ) . "\r\n";
				$old_values['exif_value'] = $new_value['exif_value'];
			}

			if ( $new_value['iptc_first'] ) {
				$boolean_value = true;
				$boolean_text = __( 'EXIF to IPTC', 'media-library-assistant' );
			} else {
				$boolean_value = false;
				$boolean_text = __( 'IPTC to EXIF', 'media-library-assistant' );
			}
			if ( $old_values['iptc_first'] != $boolean_value ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 'to' new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s value from %3$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Priority', 'media-library-assistant' ), $boolean_text ) . "\r\n";
				$old_values['iptc_first'] = $boolean_value;
			}

			if ( $new_value['keep_existing'] ) {
				$boolean_value = true;
				$boolean_text = __( 'Replace to Keep', 'media-library-assistant' );
			} else {
				$boolean_value = false;
				$boolean_text = __( 'Keep to Replace', 'media-library-assistant' );
			}
			if ( $old_values['keep_existing'] != $boolean_value ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 'to' new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s value from %3$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Existing Text', 'media-library-assistant' ), $boolean_text ) . "\r\n";
				$old_values['keep_existing'] = $boolean_value;
			}

			if ( $any_setting_changed ) {
				$settings_changed = true;
				$current_values['standard'][ $new_key ] = $old_values;
			}
		} // new standard value

		/*
		 * Uncomment this for debugging.
		 */
		 // $error_list .= $message_list;

		return array( 'message' => $error_list, 'values' => $current_values, 'changed' => $settings_changed );
	} // _update_iptc_exif_standard_mapping

	/**
	 * Update Taxonomy term portion of IPTC/EXIF mappings
 	 *
	 * @since 1.00
	 *
	 * @param	array 	current iptc_exif_mapping values 
	 * @param	array	new values
	 *
	 * @return	array	( 'message' => HTML message(s) reflecting results, 'values' => updated iptc_exif_mapping values, 'changed' => true if any changes detected else false )
	 */
	private static function _update_iptc_exif_taxonomy_mapping( $current_values, $new_values ) {
		$error_list = '';
		$message_list = '';
		$settings_changed = false;
		$new_values = stripslashes_deep( $new_values );

		/*
		 * Remove rules for taxonomies that no longer exist
		 */
		$taxonomies = get_taxonomies( array ( 'show_ui' => true ), 'objects' );
		foreach ( $current_values['taxonomy'] as $new_key => $new_value ) {
			if ( ! isset( $taxonomies[ $new_key ] ) ) {
				$settings_changed = true;
				/* translators: 1: custom field name */
				$message_list .= '<br>' . sprintf( __( 'Deleting rule for %1$s.', 'media-library-assistant' ), esc_html( $new_key ) ) . "\r\n";
				unset( $current_values['taxonomy'][ $new_key ] );
			}
		}

		foreach ( $new_values['taxonomy'] as $new_key => $new_value ) {
			if ( isset( $current_values['taxonomy'][ $new_key ] ) ) {
				$old_values = $current_values['taxonomy'][ $new_key ];
			} else {
				$old_values = array(
					'name' => $new_value['name'],
					'hierarchical' => $new_value['hierarchical'],
					'iptc_value' => 'none',
					'exif_value' => '',
					'iptc_first' => true,
					'keep_existing' => true,
					'delimiters' => '',
					'parent' => 0
				);
			}

			$any_setting_changed = false;
			if ( $old_values['iptc_value'] != $new_value['iptc_value'] ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'IPTC Value', 'media-library-assistant' ), $old_values['iptc_value'], $new_value['iptc_value'] ) . "\r\n";
				$old_values['iptc_value'] = $new_value['iptc_value'];
			}

			if ( $old_values['exif_value'] != $new_value['exif_value'] ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'EXIF Value', 'media-library-assistant' ), $old_values['exif_value'], $new_value['exif_value'] ) . "\r\n";
				$old_values['exif_value'] = $new_value['exif_value'];
			}

			if ( $new_value['iptc_first'] ) {
				$boolean_value = true;
				$boolean_text = __( 'EXIF to IPTC', 'media-library-assistant' );
			} else {
				$boolean_value = false;
				$boolean_text = __( 'IPTC to EXIF', 'media-library-assistant' );
			}
			if ( $old_values['iptc_first'] != $boolean_value ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 'to' new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s value from %3$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Priority', 'media-library-assistant' ), $boolean_text ) . "\r\n";
				$old_values['iptc_first'] = $boolean_value;
			}

			if ( $new_value['keep_existing'] ) {
				$boolean_value = true;
				$boolean_text = __( 'Replace to Keep', 'media-library-assistant' );
			} else {
				$boolean_value = false;
				$boolean_text = __( 'Keep to Replace', 'media-library-assistant' );
			}
			if ( $old_values['keep_existing'] != $boolean_value ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 'to' new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s value from %3$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Existing Text', 'media-library-assistant' ), $boolean_text ) . "\r\n";
				$old_values['keep_existing'] = $boolean_value;
			}

			if ( $old_values['delimiters'] != $new_value['delimiters'] ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Delimiter(s)', 'media-library-assistant' ), $old_values['delimiters'], $new_value['delimiters'] ) . "\r\n";
				$old_values['delimiters'] = $new_value['delimiters'];
			}

			if ( isset( $new_value['parent'] ) && ( $old_values['parent'] != $new_value['parent'] ) ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Parent', 'media-library-assistant' ), $old_values['parent'], $new_value['parent'] ) . "\r\n";
				$old_values['parent'] = $new_value['parent'];
			}

			if ( $any_setting_changed ) {
				$settings_changed = true;
				$current_values['taxonomy'][ $new_key ] = $old_values;
			}
		} // new taxonomy value

		/*
		 * Uncomment this for debugging.
		 */
		// $error_list .= $message_list;

		return array( 'message' => $error_list, 'values' => $current_values, 'changed' => $settings_changed );
	} // _update_iptc_exif_taxonomy_mapping

	/**
	 * Update Custom field portion of IPTC/EXIF mappings
 	 *
	 * @since 1.00
	 *
	 * @param	array 	current iptc_exif_mapping values 
	 * @param	array	new values
	 *
	 * @return	array	( 'message' => HTML message(s) reflecting results, 'values' => updated iptc_exif_mapping values, 'changed' => true if any changes detected else false )
	 */
	private static function _update_iptc_exif_custom_mapping( $current_values, $new_values ) {
		$error_list = '';
		$message_list = '';
		$settings_changed = false;
		$custom_field_names = MLAOptions::_get_custom_field_names();
		$new_values = stripslashes_deep( $new_values );

		if ( empty( $new_values['custom'] ) ) {
			$new_values['custom'] = array();
		}

		foreach ( $new_values['custom'] as $the_key => $new_value ) {
			$any_setting_changed = false;
			/*
			 * Replace index with field name
			 */
			$new_key = trim( $new_value['name'] );

			/*
			 * Check for the addition of a new field or new rule
			 */
			if ( MLACoreOptions::MLA_NEW_CUSTOM_FIELD === $the_key ) {
				if ( empty( $new_key ) ) {
					continue;
				}

				if ( in_array( $new_key, $custom_field_names ) ) {
					/* translators: 1: ERROR tag 2: custom field name */
					$error_list .= '<br>' . sprintf( __( '%1$s: New field %2$s already exists.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), esc_html( $new_key ) ) . "\r\n";
					continue;
				}

				/* translators: 1: custom field name */
				$message_list .= '<br>' . sprintf( __( 'Adding new field %1$s.', 'media-library-assistant' ), esc_html( $new_key ) ) . "\r\n";
				$any_setting_changed = true;
			} elseif ( MLACoreOptions::MLA_NEW_CUSTOM_RULE === $the_key ) {
				if ( 'none' == $new_key ) {
					continue;
				}

				/* translators: 1: custom field name */
				$message_list .= '<br>' . sprintf( __( 'Adding new rule for %1$s.', 'media-library-assistant' ), esc_html( $new_key ) ) . "\r\n";
				$any_setting_changed = true;
			}

			$new_value = $new_value;

			if ( isset( $current_values['custom'] ) && isset( $current_values['custom'][ $new_key ] ) ) {
				$old_values = $current_values['custom'][ $new_key ];
				$any_setting_changed = false;
			} else {
				$old_values = array(
					'name' => $new_key,
					'iptc_value' => 'none',
					'exif_value' => '',
					'iptc_first' => true,
					'keep_existing' => true,
					'format' => 'native',
					'option' => 'text',
					'no_null' => false
				);
			}

			if ( isset( $new_value['action'] ) ) {
				if ( array_key_exists( 'delete_rule', $new_value['action'] ) || array_key_exists( 'delete_field', $new_value['action'] ) ) {
					$settings_changed = true;
					/* translators: 1: custom field name */
					$message_list .= '<br>' . sprintf( __( 'Deleting rule for %1$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ) ) . "\r\n";
					unset( $current_values['custom'][ $new_key ] );
					$settings_changed = true;
					continue;
				} // delete rule
			} // isset action

			if ( $old_values['iptc_value'] != $new_value['iptc_value'] ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'IPTC Value', 'media-library-assistant' ), $old_values['iptc_value'], $new_value['iptc_value'] ) . "\r\n";
				$old_values['iptc_value'] = $new_value['iptc_value'];
			}

			if ( $old_values['exif_value'] != $new_value['exif_value'] ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'EXIF Value', 'media-library-assistant' ), $old_values['exif_value'], $new_value['exif_value'] ) . "\r\n";
				$old_values['exif_value'] = $new_value['exif_value'];
			}

			if ( $new_value['iptc_first'] ) {
				$boolean_value = true;
				$boolean_text = __( 'EXIF to IPTC', 'media-library-assistant' );
			} else {
				$boolean_value = false;
				$boolean_text = __( 'IPTC to EXIF', 'media-library-assistant' );
			}
			if ( $old_values['iptc_first'] != $boolean_value ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 'to' new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s value from %3$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Priority', 'media-library-assistant' ), $boolean_text ) . "\r\n";
				$old_values['iptc_first'] = $boolean_value;
			}

			if ( $new_value['keep_existing'] ) {
				$boolean_value = true;
				$boolean_text = __( 'Replace to Keep', 'media-library-assistant' );
			} else {
				$boolean_value = false;
				$boolean_text = __( 'Keep to Replace', 'media-library-assistant' );
			}
			if ( $old_values['keep_existing'] != $boolean_value ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 'to' new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s value from %3$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Existing Text', 'media-library-assistant' ), $boolean_text ) . "\r\n";
				$old_values['keep_existing'] = $boolean_value;
			}

			if ( $old_values['format'] != $new_value['format'] ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Format', 'media-library-assistant' ), $old_values['format'], $new_value['format'] ) . "\r\n";
				$old_values['format'] = $new_value['format'];
			}

			if ( $old_values['option'] != $new_value['option'] ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 4: new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s from %3$s to %4$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Option', 'media-library-assistant' ), $old_values['option'], $new_value['option'] ) . "\r\n";
				$old_values['option'] = $new_value['option'];
			}

			if ( isset( $new_value['no_null'] ) ) {
				$boolean_value = true;
				$boolean_text = __( 'unchecked to checked', 'media-library-assistant' );
			} else {
				$boolean_value = false;
				$boolean_text = __( 'checked to unchecked', 'media-library-assistant' );
			}
			if ( $old_values['no_null'] != $boolean_value ) {
				$any_setting_changed = true;
				/* translators: 1: custom field name 2: attribute 3: old value 'to' new value */
				$message_list .= '<br>' . sprintf( __( '%1$s changing %2$s value from %3$s.', 'media-library-assistant' ), esc_html( $old_values['name'] ), __( 'Delete NULL values', 'media-library-assistant' ), $boolean_text ) . "\r\n";
				$old_values['no_null'] = $boolean_value;
			}

			if ( $any_setting_changed ) {
				$settings_changed = true;
				$current_values['custom'][ $new_key ] = $old_values;
			}
		} // new custom value

		/*
		 * Uncomment this for debugging.
		 */
		// $error_list .= $message_list;

		return array( 'message' => $error_list, 'values' => $current_values, 'changed' => $settings_changed );
	} // _update_iptc_exif_custom_mapping

	/**
	 * Generate a list of all (post) Custom Field names
	 *
	 * The list will include any Custom Field and IPTC/EXIF rules that
	 * haven't been mapped to any attachments, yet.
 	 *
	 * @since 1.00
	 *
	 * @return	array	Custom field names from the postmeta table and MLA rules
	 */
	private static function _get_custom_field_names( ) {
		global $wpdb;

		$custom_field_mapping = MLACore::mla_get_option( 'custom_field_mapping' );
		$iptc_exif_mapping = MLACore::mla_get_option( 'iptc_exif_mapping' );
		$iptc_exif_mapping = !empty( $iptc_exif_mapping['custom'] ) ? $iptc_exif_mapping['custom'] : array();

		$limit = (int) apply_filters( 'postmeta_form_limit', 100 );
		$keys = $wpdb->get_col( "
			SELECT meta_key
			FROM $wpdb->postmeta
			GROUP BY meta_key
			HAVING meta_key NOT LIKE '\_%'
			ORDER BY meta_key
			LIMIT $limit" );

		// Add any names in mapping rules that don't exist in the database
		if ( $keys ) {
			foreach ( $custom_field_mapping as $value ) {
				if ( ! in_array( $value['name'], $keys ) ) {
					$keys[] = $value['name'];
				}
			}

			foreach ( $iptc_exif_mapping as $value ) {
				if ( ! in_array( $value['name'], $keys ) ) {
					$keys[] = $value['name'];
				}
			}

			natcasesort($keys);
		}

		return $keys;
	} // _get_custom_field_names

	/**
	 * Render and manage iptc/exif support options
 	 *
	 * @since 1.00
	 * @uses $mla_option_templates contains row and table templates
	 *
	 * @param	string 	'render', 'update', 'delete', or 'reset'
	 * @param	string 	option name, e.g., 'iptc_exif_mapping'
	 * @param	array 	option parameters
	 * @param	array 	Optional. null (default) for 'render' else option data, e.g., $_REQUEST
	 *
	 * @return	string	HTML table row markup for 'render' else message(s) reflecting the results of the operation.
	 */
	public static function mla_iptc_exif_option_handler( $action, $key, $value, $args = NULL ) {
error_log( __LINE__ . " MLAOptions::mla_iptc_exif_option_handler( $action, $key )", 0 );
return " MLAOptions::mla_iptc_exif_option_handler( $action, $key ) deprecated.";

		// Added in 2.63
		MLAOptions::_load_option_templates();

		$current_values = MLACore::mla_get_option( 'iptc_exif_mapping' );
	} // mla_iptc_exif_option_handler
} // class MLAOptions
?>