<?php
/**
 * Provides an example of the filters provided by the "Edit Media additional meta boxes" feature
 *
 * In this example the format of the "Inserted in" meta box is simplified.
 * All of the action takes place in the "mla_inserted_in_meta_box" filter.
 *
 * @package MLA Meta Box Hooks Example
 * @version 1.00
 */

/*
Plugin Name: MLA Meta Box Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides an example of the filters provided by the "Edit Media additional meta boxes" feature
Author: David Lingren
Version: 1.00
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2014 David Lingren

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
 * Class MLA Meta Box Hooks Example hooks all of the filters provided by the "Edit Media additional meta boxes" feature
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Meta Box Hooks Example
 * @since 1.00
 */
class MLAMetaboxHooksExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * Installs filters and actions that handle the MLA hooks for uploading and mapping.
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * The filters are only useful in the admin section; exit if in the "front-end" posts/pages. 
		 */
		if ( ! is_admin() )
			return;

		/*
		 * add_filter parameters:
		 * $tag - name of the hook you're filtering; defined by [mla_gallery]
		 * $function_to_add - function to be called when [mla_gallery] applies the filter
		 * $priority - default 10; lower runs earlier, higher runs later
		 * $accepted_args - number of arguments your function accepts
		 *
		 * Comment out the filters you don't need; save them for future use
		 */
		add_filter( 'mla_edit_media_support', 'MLAMetaboxHooksExample::mla_edit_media_support_filter', 10, 1 );
		add_filter( 'mla_edit_media_meta_boxes', 'MLAMetaboxHooksExample::mla_edit_media_meta_boxes_filter', 10, 1 );

		add_filter( 'mla_parent_info_meta_box', 'MLAMetaboxHooksExample::mla_parent_info_meta_box_filter', 10, 3 );
		add_filter( 'mla_menu_order_meta_box', 'MLAMetaboxHooksExample::mla_menu_order_meta_box_filter', 10, 2 );

		add_filter( 'mla_image_metadata_meta_box', 'MLAMetaboxHooksExample::mla_image_metadata_meta_box_filter', 10, 3 );
		add_filter( 'mla_image_metadata_meta_box_html', 'MLAMetaboxHooksExample::mla_image_metadata_meta_box_html_filter', 10, 4 );

		add_filter( 'mla_featured_in_meta_box', 'MLAMetaboxHooksExample::mla_featured_in_meta_box_filter', 10, 3 );
		add_filter( 'mla_featured_in_meta_box_html', 'MLAMetaboxHooksExample::mla_featured_in_meta_box_html_filter', 10, 4 );

		add_filter( 'mla_inserted_in_meta_box', 'MLAMetaboxHooksExample::mla_inserted_in_meta_box_filter', 10, 3 );
		add_filter( 'mla_inserted_in_meta_box_html', 'MLAMetaboxHooksExample::mla_inserted_in_meta_box_html_filter', 10, 4 );

		add_filter( 'mla_gallery_in_meta_box', 'MLAMetaboxHooksExample::mla_gallery_in_meta_box_filter', 10, 3 );
		add_filter( 'mla_gallery_in_meta_box_html', 'MLAMetaboxHooksExample::mla_gallery_in_meta_box_html_filter', 10, 4 );

		add_filter( 'mla_mla_gallery_in_meta_box', 'MLAMetaboxHooksExample::mla_mla_gallery_in_meta_box_filter', 10, 3 );
		add_filter( 'mla_mla_gallery_in_meta_box_html', 'MLAMetaboxHooksExample::mla_mla_gallery_in_meta_box_html_filter', 10, 4 );
	}

	/**
	 * Save the active meta boxes
	 *
	 * Default array elements (index and value) are:
	 * 'mla-parent-info', 'mla-menu-order', 'mla-image-metadata',
	 * 'mla-featured-in', 'mla-inserted-in',
	 * 'mla-gallery-in', 'mla-mla-gallery-in'
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $default_meta_boxes = array();
	private static $active_meta_boxes = array();

	/**
	 * MLA Edit Media Add Support Filter
	 *
	 * This filter gives you an opportunity to suppress the addition of Custom Fields to the Edit Media screen.
	 *
	 * @since 1.00
	 *
	 * @param	array	( [0] => 'custom-fields' )
	 *
	 * @return	array	updated add_post_type_support() array
	 */
	public static function mla_edit_media_support_filter( $add_support ) {
		/*
		 * Uncomment the error_log statements in any of the filters to see what's passed in
		 */
		//error_log( 'MLAMetaboxHooksExample::mla_edit_media_support_filter $add_support = ' . var_export( $add_support, true ), 0 );

		// to suppress Custom Fields, return an empty array, i.e., return array();
		return $add_support;
	} // mla_edit_media_support_filter

	/**
	 * MLA Edit Media Meta Boxes Filter
	 *
	 * This filter gives you an opportunity to record the original list of meta box slugs.
	 * You can also remove elements from the array to suppress one or more meta boxes.
	 *
	 * @since 1.00
	 *
	 * @param	array	the file name, type and location
	 * @param	array	the IPTC, EXIF and WordPress image_metadata
	 *
	 * @return	array	updated file name and other information
	 */
	public static function mla_edit_media_meta_boxes_filter( $active_boxes ) {
		/*
		 * Uncomment the error_log statements in any of the filters to see what's passed in
		 */
		//error_log( 'MLAMetaboxHooksExample::mla_edit_media_meta_boxes_filter $active_boxes = ' . var_export( $active_boxes, true ), 0 );

		/*
		 * Save the information for use in the later filters
		 */
		self::$default_meta_boxes = $active_boxes;
		// to suppress a box, remove it from the array, e.g., unset( $active_boxes['mla-menu-order'] );
		self::$active_meta_boxes = $active_boxes;

		return $active_boxes;
	} // mla_edit_media_meta_boxes_filter

	/**
	 * MLA Parent Info Meta Box Filter
	 *
	 * This filter gives you an opportunity to modify the text portion of the "Parent Info" meta box.
	 *
	 * @since 1.00
	 *
	 * @param	string	the default parent information
	 * @param	array	the attachment references information for this post
	 * @param	object	the current post
	 *
	 * @return	array	updated parent information
	 */
	public static function mla_parent_info_meta_box_filter( $parent_info, $references, $post ) {
		//error_log( 'MLAMetaboxHooksExample::mla_parent_info_meta_box_filter $parent_info = ' . var_export( $parent_info, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_parent_info_meta_box_filter $references = ' . var_export( $references, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_parent_info_meta_box_filter $post = ' . var_export( $post, true ), 0 );

		return $parent_info;
	} // mla_parent_info_meta_box_filter

	/**
	 * MLA Menu Order Meta Box Filter
	 *
	 * This filter gives you an opportunity to modify the "Menu Order" meta box.
	 *
	 * @since 1.00
	 *
	 * @param	string	the default menu order
	 * @param	object	the current post
	 *
	 * @return	array	updated menu order
	 */
	public static function mla_menu_order_meta_box_filter( $menu_order, $post ) {
		//error_log( 'MLAMetaboxHooksExample::mla_menu_order_meta_box_filter $menu_order = ' . var_export( $menu_order, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_menu_order_meta_box_filter $post = ' . var_export( $post, true ), 0 );

		return $menu_order;
	} // mla_menu_order_meta_box_filter

	/**
	 * MLA Attachment Metadata Meta Box Filter
	 *
	 * This filter gives you an opportunity to modify the "Attachment Metadata" meta box.
	 *
	 * @since 1.00
	 *
	 * @param	array	( [value] => default text, [rows] => textbox rows, [cols] => textbox columns )
	 * @param	array	the attachment metadata for this post (all of it); image metadata, if any, is in 
	 * 					$metadata['mla_wp_attachment_metadata']
	 * @param	object	the current post
	 *
	 * @return	array	updated parent information
	 */
	public static function mla_image_metadata_meta_box_filter( $value, $metadata, $post ) {
		//error_log( 'MLAMetaboxHooksExample::mla_image_metadata_meta_box_filter $value = ' . var_export( $value, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_image_metadata_meta_box_filter $metadata = ' . var_export( $metadata, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_image_metadata_meta_box_filter $post = ' . var_export( $post, true ), 0 );

		return $value;
	} // mla_image_metadata_meta_box_filter

	/**
	 * MLA Attachment Metadata Meta Box HTML Filter
	 *
	 * This filter gives you an opportunity to modify the "Attachment Metadata" meta box HTML content.
	 *
	 * @since 1.00
	 *
	 * @param	string	Meta box contents markup
	 * @param	array	( [value] => default text, [rows] => textbox rows, [cols] => textbox columns )
	 * @param	array	the attachment metadata for this post (all of it); image metadata, if any, is in 
	 * 					$metadata['mla_wp_attachment_metadata']
	 * @param	object	the current post
	 *
	 * @return	array	updated meta box contents markup
	 */
	public static function mla_image_metadata_meta_box_html_filter( $html, $value, $metadata, $post ) {
		//error_log( 'MLAMetaboxHooksExample::mla_image_metadata_meta_box_html_filter $html = ' . var_export( $html, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_image_metadata_meta_box_html_filter $value = ' . var_export( $value, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_image_metadata_meta_box_html_filter $metadata = ' . var_export( $metadata, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_image_metadata_meta_box_html_filter $post = ' . var_export( $post, true ), 0 );

		return $html;
	} // mla_image_metadata_meta_box_html_filter

	/**
	 * MLA Featured in Meta Box Filter
	 *
	 * This filter gives you an opportunity to modify the "Featured in" meta box.
	 *
	 * @since 1.00
	 *
	 * @param	array	( [features] => default text, [rows] => textbox rows, [cols] => textbox columns )
	 * @param	array	the attachment references information for this post
	 * @param	object	the current post
	 *
	 * @return	array	updated text, rows, columns
	 */
	public static function mla_featured_in_meta_box_filter( $features, $references, $post ) {
		//error_log( 'MLAMetaboxHooksExample::mla_featured_in_meta_box_filter $features = ' . var_export( $features, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_featured_in_meta_box_filter $references = ' . var_export( $references, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_featured_in_meta_box_filter $post = ' . var_export( $post, true ), 0 );

		return $features;
	} // mla_featured_in_meta_box_filter

	/**
	 * MLA Featured in Meta Box HTML Filter
	 *
	 * This filter gives you an opportunity to modify the "Featured in" meta box HTML content.
	 *
	 * @since 1.00
	 *
	 * @param	string	Meta box contents markup
	 * @param	array	( [features] => default text, [rows] => textbox rows, [cols] => textbox columns )
	 * @param	array	the attachment references information for this post
	 * @param	object	the current post
	 *
	 * @return	array	updated meta box contents markup
	 */
	public static function mla_featured_in_meta_box_html_filter( $html, $features, $references, $post ) {
		//error_log( 'MLAMetaboxHooksExample::mla_featured_in_meta_box_html_filter $html = ' . var_export( $html, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_featured_in_meta_box_html_filter $features = ' . var_export( $features, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_featured_in_meta_box_html_filter $references = ' . var_export( $references, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_featured_in_meta_box_html_filter $post = ' . var_export( $post, true ), 0 );

		return $html;
	} // mla_featured_in_meta_box_html_filter

	/**
	 * MLA Inserted in Meta Box Filter
	 *
	 * This filter gives you an opportunity to modify the "Inserted in" meta box.
	 *
	 * @since 1.00
	 *
	 * @param	array	( [inserts] => default text, [rows] => textbox rows, [cols] => textbox columns )
	 * @param	array	the attachment references information for this post
	 * @param	object	the current post
	 *
	 * @return	array	updated text, rows, columns
	 */
	public static function mla_inserted_in_meta_box_filter( $inserts, $references, $post ) {
		//error_log( 'MLAMetaboxHooksExample::mla_inserted_in_meta_box_filter $inserts = ' . var_export( $inserts, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_inserted_in_meta_box_filter $references = ' . var_export( $references, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_inserted_in_meta_box_filter $post = ' . var_export( $post, true ), 0 );

		// Comment out this return statement to fall through to the example code that simplifies the display
		return $inserts;

		$new_inserts = array();
		$upload_dir = wp_upload_dir();
		$file_url = $upload_dir['baseurl'] . '/' . $references['base_file'];
		$new_inserts[] = $references['file'];
		$new_inserts[] = '(' . $file_url . ')';

		// Index on post ID to eliminate duplicates
		$base_references = array();
		foreach( $references['inserts'] as $key => $file_references ) {
			foreach ( $file_references as $reference ) {
				$base_references[ $reference->ID ] = $reference;
			}
		}

		foreach ( $base_references as $reference ) {
			$new_inserts[] = $reference->post_title;
			$new_inserts[] = '(' . get_permalink( $reference->ID, false ) . ')';
		}

		// Uncomment these lines to display the arguments in the meta box
		//$new_inserts[] = '----- debug information -----';
		//$new_inserts[] = '$inserts = ' . var_export( $inserts, true );
		//$new_inserts[] = '$references = ' . var_export( $references, true );
		//$new_inserts[] = '$post = ' . var_export( $post, true );

		$inserts['inserts'] = implode( "\n", $new_inserts );
		return $inserts;
	} // mla_inserted_in_meta_box_filter

	/**
	 * MLA Inserted in Meta Box HTML Filter
	 *
	 * This filter gives you an opportunity to modify the "Inserted in" meta box HTML content.
	 *
	 * @since 1.00
	 *
	 * @param	string	Meta box contents markup
	 * @param	array	( [inserts] => default text, [rows] => textbox rows, [cols] => textbox columns )
	 * @param	array	the attachment references information for this post
	 * @param	object	the current post
	 *
	 * @return	array	updated meta box contents markup
	 */
	public static function mla_inserted_in_meta_box_html_filter( $html, $inserts, $references, $post ) {
		//error_log( 'MLAMetaboxHooksExample::mla_inserted_in_meta_box_html_filter $html = ' . var_export( $html, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_inserted_in_meta_box_html_filter $inserts = ' . var_export( $inserts, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_inserted_in_meta_box_html_filter $references = ' . var_export( $references, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_inserted_in_meta_box_html_filter $post = ' . var_export( $post, true ), 0 );

		return $html;
	} // mla_inserted_in_meta_box_html_filter

	/**
	 * MLA Gallery in Meta Box Filter
	 *
	 * This filter gives you an opportunity to modify the "Gallery in" meta box.
	 *
	 * @since 1.00
	 *
	 * @param	array	( [galleries] => default text, [rows] => textbox rows, [cols] => textbox columns )
	 * @param	array	the attachment references information for this post
	 * @param	object	the current post
	 *
	 * @return	array	updated text, rows, columns
	 */
	public static function mla_gallery_in_meta_box_filter( $galleries, $references, $post ) {
		//error_log( 'MLAMetaboxHooksExample::mla_gallery_in_meta_box_filter $galleries = ' . var_export( $galleries, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_gallery_in_meta_box_filter $references = ' . var_export( $references, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_gallery_in_meta_box_filter $post = ' . var_export( $post, true ), 0 );

		return $galleries;
	} // mla_gallery_in_meta_box_filter

	/**
	 * MLA Gallery in Meta Box HTML Filter
	 *
	 * This filter gives you an opportunity to modify the "Gallery in" meta box HTML content.
	 *
	 * @since 1.00
	 *
	 * @param	string	Meta box contents markup
	 * @param	array	( [galleries] => default text, [rows] => textbox rows, [cols] => textbox columns )
	 * @param	array	the attachment references information for this post
	 * @param	object	the current post
	 *
	 * @return	array	updated meta box contents markup
	 */
	public static function mla_gallery_in_meta_box_html_filter( $html, $galleries, $references, $post ) {
		//error_log( 'MLAMetaboxHooksExample::mla_gallery_in_meta_box_html_filter $html = ' . var_export( $html, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_gallery_in_meta_box_html_filter $galleries = ' . var_export( $galleries, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_gallery_in_meta_box_html_filter $references = ' . var_export( $references, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_gallery_in_meta_box_html_filter $post = ' . var_export( $post, true ), 0 );

		return $html;
	} // mla_gallery_in_meta_box_html_filter

	/**
	 * MLA MLA Gallery in Meta Box Filter
	 *
	 * This filter gives you an opportunity to modify the "MLA Gallery in" meta box.
	 *
	 * @since 1.00
	 *
	 * @param	array	( [galleries] => default text, [rows] => textbox rows, [cols] => textbox columns )
	 * @param	array	the attachment references information for this post
	 * @param	object	the current post
	 *
	 * @return	array	updated text, rows, columns
	 */
	public static function mla_mla_gallery_in_meta_box_filter( $galleries, $references, $post ) {
		//error_log( 'MLAMetaboxHooksExample::mla_mla_gallery_in_meta_box_filter $galleries = ' . var_export( $galleries, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_mla_gallery_in_meta_box_filter $references = ' . var_export( $references, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_mla_gallery_in_meta_box_filter $post = ' . var_export( $post, true ), 0 );

		return $galleries;
	} // mla_mla_gallery_in_meta_box_filter

	/**
	 * MLA MLA Gallery in Meta Box HTML Filter
	 *
	 * This filter gives you an opportunity to modify the "MLA Gallery in" meta box HTML content.
	 *
	 * @since 1.00
	 *
	 * @param	string	Meta box contents markup
	 * @param	array	( [galleries] => default text, [rows] => textbox rows, [cols] => textbox columns )
	 * @param	array	the attachment references information for this post
	 * @param	object	the current post
	 *
	 * @return	array	updated meta box contents markup
	 */
	public static function mla_mla_gallery_in_meta_box_html_filter( $html, $galleries, $references, $post ) {
		//error_log( 'MLAMetaboxHooksExample::mla_mla_gallery_in_meta_box_html_filter $html = ' . var_export( $html, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_mla_gallery_in_meta_box_html_filter $galleries = ' . var_export( $galleries, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_mla_gallery_in_meta_box_html_filter $references = ' . var_export( $references, true ), 0 );
		//error_log( 'MLAMetaboxHooksExample::mla_mla_gallery_in_meta_box_html_filter $post = ' . var_export( $post, true ), 0 );

		return $html;
	} // mla_mla_gallery_in_meta_box_html_filter
} //MLAMetaboxHooksExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAMetaboxHooksExample::initialize');
?>