<?php
/**
 * Adds "Copy" action to Media/Assistant submenu Bulk Actions dropdown
 *
 * In this example, a "Copy" bulk action lets you make a copy of one or more existing items
 * as a new Media Library item or items.
 *
 * This example plugin uses eight of the many filters available in the Media/Assistant submenu
 * screen and illustrates a technique you can use to customize the submenu table actions.
 *
 * Created for support topic "Option to copy an image"
 * opened on 11/2/2016 by "argosmedia".
 * https://wordpress.org/support/topic/option-to-copy-an-image/
 *
 * @package MLA Copy Item Example
 * @version 1.00
 */

/*
Plugin Name: MLA Copy Item Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Adds "Copy" action to Media/Assistant submenu Bulk Actions dropdown
Author: David Lingren
Version: 1.00
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2016 David Lingren

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You can get a copy of the GNU General Public License by writing to the
	Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

/**
 * Class MLA Copy Item Example adds a "Copy" bulk action and makes copies of existing items.
 *
 * @package MLA Copy Item Example
 * @since 1.00
 */
class MLACopyItemExample {
	/**
	 * Uniquely identifies the Copy bulk action
	 *
	 * @since 1.00
	 *
	 * @var	string
	 */
	const MLA_COPY_ACTION = 'mla-copy-item-example';

	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 */
	public static function initialize() {
		// The filters are only useful in the admin section
		if ( !is_admin() )
			return;

		/*
		 * Defined in /wp-admin/admin-header.php
		 */
 		add_action( 'admin_enqueue_scripts', 'MLACopyItemExample::admin_enqueue_scripts', 10, 1 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-main.php
		  */
		add_filter( 'mla_list_table_help_template', 'MLACopyItemExample::mla_list_table_help_template', 10, 3 );
		add_filter( 'mla_list_table_begin_bulk_action', 'MLACopyItemExample::mla_list_table_begin_bulk_action', 10, 2 );
		add_filter( 'mla_list_table_custom_bulk_action', 'MLACopyItemExample::mla_list_table_custom_bulk_action', 10, 3 );
		add_filter( 'mla_list_table_end_bulk_action', 'MLACopyItemExample::mla_list_table_end_bulk_action', 10, 2 );
		add_filter( 'mla_list_table_inline_parse', 'MLACopyItemExample::mla_list_table_inline_parse', 10, 3 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-list-table.php
		  */
		add_filter( 'mla_list_table_get_bulk_actions', 'MLACopyItemExample::mla_list_table_get_bulk_actions', 10, 1 );
		add_filter( 'mla_list_table_submenu_arguments', 'MLACopyItemExample::mla_list_table_submenu_arguments', 10, 2 );
	}

	/**
	 * Load the plugin's Style Sheet and Javascript files
	 *
	 * @since 2.13
	 *
	 * @param	string	Name of the page being loaded
	 *
	 * @return	void
	 */
	public static function admin_enqueue_scripts( $page_hook ) {
		global $wp_locale;

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		if ( 'media_page_mla-menu' != $page_hook ) {
			return;
		}

		if ( $wp_locale->is_rtl() ) {
			wp_register_style( 'mla-copy-item', plugin_dir_url( __FILE__ ) . 'mla-copy-item-rtl.css', false, MLACore::CURRENT_MLA_VERSION );
		} else {
			wp_register_style( 'mla-copy-item', plugin_dir_url( __FILE__ ) . 'mla-copy-item.css', false, MLACore::CURRENT_MLA_VERSION );
		}

		wp_enqueue_style( 'mla-copy-item' );

		wp_enqueue_script( 'mla-copy-item-scripts', plugin_dir_url( __FILE__ ) . "mla-copy-item-scripts{$suffix}.js", 
			array( 'jquery' ), MLACore::CURRENT_MLA_VERSION, false );

		$script_variables = array(
			'error' => __( 'Error while saving the thumbnails.', 'media-library-assistant' ),
			'ntdelTitle' => __( 'Remove From', 'media-library-assistant' ) . ' ' . 'Copy Items',
			'noTitle' => __( '(no title)', 'media-library-assistant' ),
			'bulkTitle' => 'Copy Items',
			'comma' => _x( ',', 'tag_delimiter', 'media-library-assistant' ),
		);

		wp_localize_script( 'mla-copy-item-scripts', 'mla_copy_item_support_vars', $script_variables );
	}

	/**
	 * Options for the thumbnail generation bulk action
	 *
	 * @since 2.13
	 *
	 * @var	array
	 */
	private static $bulk_action_options = array();

	/**
	 * Items returned by custom bulk action(s)
	 *
	 * @since 2.13
	 *
	 * @var	array
	 */
	private static $bulk_action_includes = array();

	/**
	 * Load the MLA_List_Table dropdown help menu template
	 *
	 * Add the thumbnail generation options documentation.
	 *
	 * @since 2.13
	 *
	 * @param	array	$template_array NULL, to indicate no replacement template.
	 * @param	string	$file_name the complete name of the default template file.
	 * @param	string	$file_suffix the $screen->id or hook suffix part of the  template file name.
	 */
	public static function mla_list_table_help_template( $template_array, $file_name, $file_suffix ) {
		if ( 'media_page_mla-menu' != $file_suffix ) {
			return $template_array;
		}

		// Retain other filters' additions
		if ( empty( $template_array ) ) {
			$template_array = MLACore::mla_load_template( $file_name );
		}
		
		$help_array = MLACore::mla_load_template( plugin_dir_path( __FILE__ ) . 'help-for-mla-copy-item-example.tpl', 'path' );

		if ( isset( $help_array['sidebar'] ) ) {
			if ( isset( $template_array['sidebar'] ) ) {
				$template_array['sidebar'] .= $help_array['sidebar'];
			} else {
				$template_array['sidebar'] = $help_array['sidebar'];
			}
			
			unset( $help_array['sidebar'] );
		}

		return array_merge( $template_array, $help_array );
	}

	/**
	 * Begin an MLA_List_Table bulk action
	 *
	 * Prepare the thumbnail generation options.
	 *
	 * @since 2.13
	 *
	 * @param	array	$item_content	NULL, to indicate no handler.
	 * @param	string	$bulk_action	the requested action.
	 */
	public static function mla_list_table_begin_bulk_action( $item_content, $bulk_action ) {
		if ( self::MLA_COPY_ACTION != $bulk_action ) {
			return $item_content;
		}

		self::$bulk_action_options = array();
		$request_options = isset( $_REQUEST['mla_copy_item_options'] ) ? $_REQUEST['mla_copy_item_options'] : array();

		foreach ( $request_options as $key => $value ) {
			if ( ! empty( $value ) ) {
				self::$bulk_action_options[ $key ] = $value;
			}
		}

		// Convert checkboxes to booleans
		self::$bulk_action_options['map_custom'] = isset( $request_options['map_custom'] );
		self::$bulk_action_options['map_iptc_exif'] = isset( $request_options['map_iptc_exif'] );
		self::$bulk_action_options['copy_terms'] = isset( $request_options['copy_terms'] );
		self::$bulk_action_options['copy_custom'] = isset( $request_options['copy_custom'] );
		self::$bulk_action_options['copy_item'] = isset( $request_options['copy_item'] );

		// Remember the MLA option settings as overide them as necessary
		self::$bulk_action_options['enable_custom_field_mapping'] = 'checked' == MLACore::mla_get_option( 'enable_custom_field_mapping' );
		self::$bulk_action_options['enable_custom_field_update'] = 'checked' == MLACore::mla_get_option( 'enable_custom_field_update' );
		self::$bulk_action_options['enable_iptc_exif_mapping'] = 'checked' == MLACore::mla_get_option( 'enable_iptc_exif_mapping' );
		self::$bulk_action_options['enable_iptc_exif_update'] = 'checked' == MLACore::mla_get_option( 'enable_iptc_exif_update' );

		if ( self::$bulk_action_options['map_custom'] ) {
			MLACore::mla_update_option( 'enable_custom_field_mapping', 'checked' );
			MLACore::mla_update_option( 'enable_custom_field_update', 'checked' );
		} else {
			// Default setting is "unchecked"
			MLACore::mla_delete_option( 'enable_custom_field_mapping' );
			MLACore::mla_delete_option( 'enable_custom_field_update' );
		}
		
		if ( self::$bulk_action_options['map_iptc_exif'] ) {
			MLACore::mla_update_option( 'enable_iptc_exif_mapping', 'checked' );
			MLACore::mla_update_option( 'enable_iptc_exif_update', 'checked' );
		} else {
			// Default setting is "unchecked"
			MLACore::mla_delete_option( 'enable_iptc_exif_mapping' );
			MLACore::mla_delete_option( 'enable_iptc_exif_update' );
		}
		
		return $item_content;
	} // mla_list_table_begin_bulk_action

	/**
	 * Process an MLA_List_Table custom bulk action
	 *
	 * Creates new items from the "Bulk Thumbnail" list.
	 *
	 * @since 2.13
	 *
	 * @param	array	$item_content	NULL, to indicate no handler.
	 * @param	string	$bulk_action	the requested action.
	 * @param	integer	$post_id		the affected attachment.
	 *
	 * @return	object	updated $item_content. NULL if no handler, otherwise
	 *					( 'message' => error or status message(s), 'body' => '' )
	 */
	public static function mla_list_table_custom_bulk_action( $item_content, $bulk_action, $post_id ) {
		if ( self::MLA_COPY_ACTION != $bulk_action ) {
			return $item_content;
		}

		$item_prefix = sprintf( 'Item %1$d', $post_id ) . ', ';

		// Validate the item's file existance and type
		$file = get_attached_file( $post_id );
		if ( empty( $file ) ) {
			return array( 'message' => sprintf( 'ERROR: %1$sno attached file.', $item_prefix ) );
		}

		// Parse the file name for the new item
		$pathinfo = pathinfo( $file );

		// Copy the original file because media_handle_sideload destroys it
		$tmp_name = wp_tempnam();
		$copy_result = @copy( $file, $tmp_name );
		if ( false === $copy_result ) {
			return array( 'message' => sprintf( 'ERROR: %1$sfile copy failed.', $item_prefix ) );
		}
		
		// array based on $_FILE as seen in PHP file uploads
		$file_array = array(
			'name' => $pathinfo['basename'],
			'tmp_name' => $tmp_name,
		);		

		if ( self::$bulk_action_options['copy_item'] ) {
			// Copy selected data from the source item
			$post = get_post( $post_id );
			$post_parent = $post->post_parent;
			$post_title = $post->post_title;
			$post_data = array(
				'post_author' => $post->post_author,
				'post_content' => $post->post_content,
				'post_excerpt' => $post->post_excerpt,
				'menu_order' => $post->menu_order,
			);
		} else {
			$post_parent = 0;
			$post_title = NULL;
			$post_data = array();
		}
		
		$item_id = media_handle_sideload( $file_array, $post_parent, $post_title, $post_data );
		if ( is_wp_error( $item_id ) ) {
			$text = implode( ',', $item_id->get_error_messages() );
			return array( 'message' => sprintf( 'ERROR: %1$smedia_handle_sideload failed; %2$s.', $item_prefix, $text ) );
		}

		if ( self::$bulk_action_options['copy_item'] ) {
			// Now we can copy ALT Text
			$alt_text = get_metadata( 'post', $post_id, '_wp_attachment_image_alt', true );

			if ( !empty( $alt_text ) ) {
				$result =  update_metadata( 'post', $item_id, '_wp_attachment_image_alt', $alt_text );
			}
		}

		if ( self::$bulk_action_options['copy_custom'] ) {
			// Look for custom fields
			foreach ( get_metadata( 'post', $post_id ) as $meta_key => $meta_value ) {
				if ( ! ( 0 === strpos( $meta_key, '_' ) ) ) {
					if ( 1 === count( $meta_value ) ) {
						$meta_value= current( $meta_value );
					}
					
					$result =  update_metadata( 'post', $item_id, $meta_key, $meta_value );
				}
			}
		}

		if ( self::$bulk_action_options['copy_terms'] ) {
			$taxonomies = get_taxonomies( array( 'object_type' => array( 'attachment' ) ), 'names' );
			$terms = wp_get_object_terms( $post_id, $taxonomies, array() );

			$new_terms = array();
			foreach ( $terms as $term ) {
				$new_terms[ $term->taxonomy][] = $term->term_id;
			}

			foreach( $new_terms as $taxonomy => $term_ids ) {
				wp_set_object_terms( $item_id, $term_ids, $taxonomy, false );
			}
		}

		MLACopyItemExample::$bulk_action_includes[] = $item_id;
		return array( 'message' => sprintf( '%1$scopied to new item %2$s.', $item_prefix, $item_id ) );
	} // mla_list_table_custom_bulk_action

	/**
	 * End an MLA_List_Table bulk action
	 *
	 * Add the query arguments required for the "Generated Thumbnails" filter.
	 *
	 * @since 2.13
	 *
	 * @param	array	$item_content	NULL, to indicate no handler.
	 * @param	string	$bulk_action	the requested action.
	 */
	public static function mla_list_table_end_bulk_action( $item_content, $bulk_action ) {
		if ( self::MLA_COPY_ACTION != $bulk_action ) {
			return $item_content;
		}

		// Restore the MLA option settings; default setting is "unchecked"
		if ( self::$bulk_action_options['map_custom'] ) {
			if ( !self::$bulk_action_options['enable_custom_field_mapping'] ) {
				MLACore::mla_delete_option( 'enable_custom_field_mapping' );
			}

			if ( !self::$bulk_action_options['enable_custom_field_update'] ) {
				MLACore::mla_delete_option( 'enable_custom_field_update' );
			}
		} else {
			if ( self::$bulk_action_options['enable_custom_field_mapping'] ) {
				MLACore::mla_update_option( 'enable_custom_field_mapping', 'checked' );
			}

			if ( self::$bulk_action_options['enable_custom_field_update'] ) {
				MLACore::mla_update_option( 'enable_custom_field_update', 'checked' );
			}
		}

		if ( self::$bulk_action_options['map_iptc_exif'] ) {
			if ( !self::$bulk_action_options['enable_iptc_exif_mapping'] ) {
				MLACore::mla_delete_option( 'enable_iptc_exif_mapping' );
			}

			if ( !self::$bulk_action_options['enable_iptc_exif_update'] ) {
				MLACore::mla_delete_option( 'enable_iptc_exif_update' );
			}
		} else {
			if ( self::$bulk_action_options['enable_iptc_exif_mapping'] ) {
				MLACore::mla_update_option( 'enable_iptc_exif_mapping', 'checked' );
			}

			if ( self::$bulk_action_options['enable_iptc_exif_update'] ) {
				MLACore::mla_update_option( 'enable_iptc_exif_update', 'checked' );
			}
		}

		if ( ! empty( MLACopyItemExample::$bulk_action_includes ) ) {
			MLA::mla_clear_filter_by( array( 'ids' ) );

			// Reset the current view to "All" to ensure that thumbnails are displayed
			unset( $_REQUEST['post_mime_type'] );
			unset( $_POST['post_mime_type'] );
			unset( $_GET['post_mime_type'] );
			unset( $_REQUEST['meta_query'] );
			unset( $_GET['meta_query'] );
			unset( $_REQUEST['meta_slug'] );
			unset( $_GET['meta_slug'] );

			// Clear the "extra_nav" controls and the Search Media box
			unset( $_REQUEST['m'] );
			unset( $_POST['m'] );
			unset( $_GET['m'] );
			unset( $_REQUEST['mla_filter_term'] );
			unset( $_POST['mla_filter_term'] );
			unset( $_GET['mla_filter_term'] );
			unset( $_REQUEST['s'] );
			unset( $_POST['s'] );
			unset( $_GET['s'] );

			// Clear the pagination control
			unset( $_REQUEST['paged'] );
			unset( $_POST['paged'] );
			unset( $_GET['paged'] );

			$_REQUEST['ids'] = MLACopyItemExample::$bulk_action_includes;
			$_REQUEST['heading_suffix'] = __( 'Copied Items', 'media-library-assistant' );
		}

		return $item_content;
	} // mla_list_table_end_bulk_action

	/**
	 * Add Copy Item to the Bulk Actions dropdown controls
	 *
	 * @since 1.00
	 *
	 * @param	array	$actions	An array of bulk actions.
	 *								Format: 'slug' => 'Label'
	 */
	public static function mla_list_table_get_bulk_actions( $actions ) {
		$actions[self::MLA_COPY_ACTION] = 'Copy';
		
		return $actions;
	} // mla_list_table_get_bulk_actions

	/**
	 * MLA_List_Table inline edit parse
	 *
	 * @since 2.13
	 *
	 * Adds Bulk Translate form and the Language dropdown
	 * markup used for the Quick and Bulk Edit forms.
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for the Quick and Bulk Edit forms
	 */
	public static function mla_list_table_inline_parse( $html_markup, $item_template, $item_values ) {

		/*
		 * Add the Thumbnail Generation Markup
		 */
		$page_template_array = MLACore::mla_load_template( plugin_dir_path( __FILE__ ) . 'mla-copy-item-example.tpl', 'path' );
		if ( ! is_array( $page_template_array ) ) {
			MLACore::mla_debug_add( 'ERROR: mla-copy-item-example.tpl path = ' . var_export( plugin_dir_path( __FILE__ ) . 'mla-copy-item-example.tpl', true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			MLACore::mla_debug_add( 'ERROR: mla-copy-item-example.tpl non-array result = ' . var_export( $page_template_array, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			return $html_markup;
		}

		$map_iptc_exif = ( ( 'checked' == MLACore::mla_get_option( 'enable_iptc_exif_mapping' ) ) ||
							( 'checked' == MLACore::mla_get_option( 'enable_iptc_exif_update' ) ) );

		$map_custom = ( ( 'checked' == MLACore::mla_get_option( 'enable_custom_field_mapping' ) ) ||
							( 'checked' == MLACore::mla_get_option( 'enable_custom_field_update' ) ) );

		$page_values = array(
			'colspan' => $item_values['colspan'],
			'map-custom-checked' => $map_custom ? 'checked="checked"' : '',
			'map-iptc-exif-checked' => $map_iptc_exif ? 'checked="checked"' : '',
			'copy-terms-checked' => '',
			'copy-custom-checked' => '',
			'copy-item-checked' => '',
		);
		$parse_value = MLAData::mla_parse_template( $page_template_array['page'], $page_values );

		return $html_markup . "\n" . $parse_value;
	} // mla_list_table_inline_parse

	/**
	 * Filter the "sticky" submenu URL parameters
	 *
	 * Maintains the list of "Generated Thumbnails" items in the URLs for sorting the table display.
	 *
	 * @since 2.13
	 *
	 * @param	array	$submenu_arguments	Current view, pagination and sort parameters.
	 * @param	object	$include_filters	True to include "filter-by" parameters, e.g., year/month dropdown.
	 *
	 * @return	array	updated submenu_arguments.
	 */
	public static function mla_list_table_submenu_arguments( $submenu_arguments, $include_filters ) {
		if ( $include_filters && ( ! empty( MLACopyItemExample::$bulk_action_includes ) ) ) {
			$submenu_arguments['ids'] = implode( ',', MLACopyItemExample::$bulk_action_includes );
			$submenu_arguments['heading_suffix'] = 'Copied Items';
		}

		return $submenu_arguments;
	} // mla_list_table_submenu_arguments
} // Class MLACopyItemExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLACopyItemExample::initialize');
?>