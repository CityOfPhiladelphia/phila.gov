<?php
/**
 * Provides two examples of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * - A custom field mapping rule is used to trigger export to an XML file.
 * - The Title and ALT Text values are cleaned up, replacing dashes, underscores and periods with spaces.
 *
 * All of the action takes place in the "mla_mapping_updates" filter,
 * a supporting function "_export_this_item" and the "mla_end_mapping" action.
 *
 * @package MLA Mapping Hooks Example
 * @version 1.03
 */

/*
Plugin Name: MLA Mapping Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides an example of the filters provided by the IPTC/EXIF and Custom Field mapping features
Author: David Lingren
Version: 1.03
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
 * Class MLA Mapping Hooks Example hooks all of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Mapping Hooks Example
 * @since 1.00
 */
class MLAMappingHooksExample {
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
		add_filter( 'mla_upload_prefilter', 'MLAMappingHooksExample::mla_upload_prefilter', 10, 2 );
		add_filter( 'mla_upload_filter', 'MLAMappingHooksExample::mla_upload_filter', 10, 2 );

		add_action( 'mla_add_attachment', 'MLAMappingHooksExample::mla_add_attachment', 10, 1 );

		add_filter( 'mla_update_attachment_metadata_options', 'MLAMappingHooksExample::mla_update_attachment_metadata_options', 10, 3 );
		add_filter( 'mla_update_attachment_metadata_prefilter', 'MLAMappingHooksExample::mla_update_attachment_metadata_prefilter', 10, 3 );
		add_filter( 'mla_update_attachment_metadata_postfilter', 'MLAMappingHooksExample::mla_update_attachment_metadata_postfilter', 10, 3 );

		add_action( 'mla_begin_mapping', 'MLAMappingHooksExample::mla_begin_mapping', 10, 2 );
		add_filter( 'mla_mapping_settings', 'MLAMappingHooksExample::mla_mapping_settings', 10, 4 );
		add_filter( 'mla_mapping_rule', 'MLAMappingHooksExample::mla_mapping_rule', 10, 4 );
		add_filter( 'mla_mapping_custom_value', 'MLAMappingHooksExample::mla_mapping_custom_value', 10, 5 );
		add_filter( 'mla_mapping_iptc_value', 'MLAMappingHooksExample::mla_mapping_iptc_value', 10, 5 );
		add_filter( 'mla_mapping_exif_value', 'MLAMappingHooksExample::mla_mapping_exif_value', 10, 5 );
		add_filter( 'mla_mapping_new_text', 'MLAMappingHooksExample::mla_mapping_new_text', 10, 5 );
		add_filter( 'mla_mapping_updates', 'MLAMappingHooksExample::mla_mapping_updates', 10, 5 );
		add_action( 'mla_end_mapping', 'MLAMappingHooksExample::mla_end_mapping', 10, 0 );

		add_filter( 'mla_get_options_tablist', 'MLAMappingHooksExample::mla_get_options_tablist', 10, 3 );
	}

	/**
	 * Save the original image metadata when a file is first uploaded
	 *
	 * Array elements are:
	 * 		'post_id' => 0,
	 *		'mla_iptc_metadata' => array(),
	 *		'mla_exif_metadata' => array(),
	 *		'wp_image_metadata' => array(),
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $image_metadata = array();

	/**
	 * MLA Mapping Upload Prefilter
	 *
	 * This filter gives you an opportunity to record the original IPTC, EXIF and
	 * WordPress image_metadata before the file is stored in the Media Library.
	 * You can also modify the file name that will be used in the Media Library.
	 *
	 * Many plugins and image editing functions alter or destroy this information,
	 * so this may be your last change to preserve it.
	 *
	 * @since 1.00
	 *
	 * @param	array	the file name, type and location
	 * @param	array	the IPTC, EXIF and WordPress image_metadata
	 *
	 * @return	array	updated file name and other information
	 */
	public static function mla_upload_prefilter( $file, $image_metadata ) {
		/*
		 * Uncomment the error_log statements in any of the filters to see what's passed in
		 */
		//error_log( __LINE__ . ' MLAMappingHooksExample::mla_upload_prefilter_filter $file = ' . var_export( $file, true ), 0 );
		//error_log( __LINE__ . ' MLAMappingHooksExample::mla_upload_prefilter_filter $image_metadata = ' . var_export( $image_metadata, true ), 0 );

		/*
		 * Save the information for use in the later filters
		 */
		self::$image_metadata = $image_metadata;
		self::$image_metadata['preload_file'] = $file;

		return $file;
	} // mla_upload_prefilter_filter

	/**
	 * MLA Mapping Upload Filter
	 *
	 * This filter gives you an opportunity to record some additional metadata
	 * for audio and video media after the file is stored in the Media Library.
	 *
	 * Many plugins and other functions alter or destroy this information,
	 * so this may be your last change to preserve it.
	 *
	 * @since 1.00
	 *
	 * @param	array	the file name, type and location
	 * @param	array	the ID3 metadata for audio and video files
	 *
	 * @return	array	updated file name, type and location
	 */
	public static function mla_upload_filter( $file, $id3_data ) {
		//error_log( __LINE__ . ' MLAMappingHooksExample::mla_upload_filter_filter $file = ' . var_export( $file, true ), 0 );
		//error_log( __LINE__ . ' MLAMappingHooksExample::mla_upload_filter_filter $id3_data = ' . var_export( $id3_data, true ), 0 );

		/*
		 * Save the information for use in the later filters
		 */
		self::$image_metadata['postload_file'] = $file;
		self::$image_metadata['id3_metadata'] = $id3_data;

		return $file;
	} // mla_upload_filter_filter

	/**
	 * MLA Add Attachment Action
	 *
	 * This filter is called at the end of the wp_insert_attachment() function,
	 * after the file is in place and the post object has been created in the database.
	 *
	 * By this time, other plugins have probably run their own 'add_attachment' filters
	 * and done their work/damage to metadata, etc.
	 *
	 * @since 1.00
	 *
	 * @param	integer	The Post ID of the new attachment
	 *
	 * @return	void
	 */
	public static function mla_add_attachment( $post_id ) {
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_add_attachment_action( {$post_id} )", 0 );

		/*
		 * Save the information for use in the later filters
		 */
		self::$image_metadata['post_id'] = $post_id;
	} // mla_add_attachment_action

	/**
	 * MLA Update Attachment Metadata Options
	 *
	 * This filter lets you inspect or change the processing options that will
	 * control the MLA mapping rules in the update_attachment_metadata filter.
	 *
	 * The options are:
	 *		is_upload - true if this is part of the original file upload process
	 *		enable_iptc_exif_mapping - true to apply IPTC/EXIF mapping to file uploads
	 *		enable_custom_field_mapping - true to apply custom field mapping to file uploads
	 *		enable_iptc_exif_update - true to apply IPTC/EXIF mapping to updates
	 *		enable_custom_field_update - true to apply custom field mapping to updates
	 *
	 * @since 1.00
	 *
	 * @param	array	Processing options, e.g., 'is_upload'
	 * @param	array	attachment metadata
	 * @param	integer	The Post ID of the new/updated attachment
	 *
	 * @return	array	updated processing options
	 */
	public static function mla_update_attachment_metadata_options( $options, $data, $post_id ) {
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_update_attachment_metadata_options_filter( {$post_id} ) options = " . var_export( $options, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_update_attachment_metadata_options_filter( {$post_id} ) data = " . var_export( $data, true ), 0 );

		return $options;
	} // mla_update_attachment_metadata_options_filter

	/**
	 * MLA Update Attachment Metadata Prefilter
	 *
	 * This filter is called at the end of the wp_update_attachment_metadata() function,
	 * BEFORE any MLA mapping rules are applied. The prefilter gives you an
	 * opportunity to record or update the metadata before the mapping.
	 *
	 * The wp_update_attachment_metadata() function is called at the end of the file upload process and at
	 * several later points, such as when an image attachment is edited or by
	 * plugins that alter the attachment file.
	 *
	 * @since 1.00
	 *
	 * @param	array	attachment metadata
	 * @param	integer	The Post ID of the new/updated attachment
	 * @param	array	Processing options, e.g., 'is_upload'
	 *
	 * @return	array	updated attachment metadata
	 */
	public static function mla_update_attachment_metadata_prefilter( $data, $post_id, $options ) {
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_update_attachment_metadata_prefilter_filter( {$post_id} ) data = " . var_export( $data, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_update_attachment_metadata_prefilter_filter( {$post_id} ) options = " . var_export( $options, true ), 0 );

		return $data;
	} // mla_update_attachment_metadata_prefilter_filter

	/**
	 * MLA Update Attachment Metadata Postfilter
	 *
	 * This filter is called AFTER MLA mapping rules are applied during
	 * wp_update_attachment_metadata() processing. The postfilter gives you
	 * an opportunity to record or update the metadata after the mapping.
	 *
	 * @since 1.00
	 *
	 * @param	array	attachment metadata
	 * @param	integer	The Post ID of the new/updated attachment
	 * @param	array	Processing options, e.g., 'is_upload'
	 *
	 * @return	array	updated attachment metadata
	 */
	public static function mla_update_attachment_metadata_postfilter( $data, $post_id, $options ) {
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_update_attachment_metadata_postfilter_filter( {$post_id} ) data = " . var_export( $data, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_update_attachment_metadata_postfilter_filter( {$post_id} ) options = " . var_export( $options, true ), 0 );

		return $data;
	} // mla_update_attachment_metadata_postfilter_filter

	/**
	 * MLA Begin Mapping Action
	 *
	 * This action is called once, before any mapping rules are executed for any item(s).
	 *
	 * @since 1.01
	 *
	 * @param	string 	what kind of mapping action is starting:
	 *					single_custom, single_iptc_exif, bulk_custom, bulk_iptc_exif,
	 *					create_metadata, update_metadata, custom_fields, custom_rule,
	 *					iptc_exif_standard, iptc_exif_taxonomy, iptc_exif_custom,
	 *					iptc_exif_custom_rule
	 * @param	mixed	Attachment ID or NULL, depending on scope
	 *
	 * @return	void	updated mapping rules
	 */
	public static function mla_begin_mapping( $source, $post_id = NULL ) {
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_begin_mapping_action( {$source}, {$post_id} )\n", 0 );
	} // mla_begin_mapping_action

	/**
	 * MLA Mapping Settings Filter
	 *
	 * This filter is called before any mapping rules are executed.
	 * You can add, change or delete rules from the array.
	 *
	 * @since 1.00
	 *
	 * @param	array 	mapping rules
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against, e.g., custom_field_mapping or single_attachment_mapping
	 * @param	array 	attachment_metadata, default NULL
	 *
	 * @return	array	updated mapping rules
	 */
	public static function mla_mapping_settings( $settings, $post_id, $category, $attachment_metadata ) {
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_settings_filter( {$post_id}, {$category} ) settings = " . var_export( $settings, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_settings_filter( {$post_id}, {$category} ) attachment_metadata = " . var_export( $attachment_metadata, true ), 0 );

		/*
		 * $category gives the context in which the rule is applied:
		 *		'custom_field_mapping' - mapping custom fields for ALL attachments
		 *		'single_attachment_mapping' - mapping custom fields for ONE attachments
		 *
		 *		'iptc_exif_mapping' - mapping ALL IPTC/EXIF rules
		 *		'iptc_exif_standard_mapping' - mapping standard field rules
		 *		'iptc_exif_taxonomy_mapping' - mapping taxonomy term rules
		 *		'iptc_exif_custom_mapping' - mapping IPTC/EXIF custom field rules
		 *
		 * NOTE: 'iptc_exif_mapping' will never be passed to the 'mla_mapping_rule' filter.
		 * There, one of the three more specific values will be passed.
		 */

		/*
		 * For Custom Field Mapping, $settings is an array indexed by
		 * the custom field name.
		 * Each array element is a mapping rule; an array containing:
		 *		'name' => custom field name
		 *		'data_source' => 'none', 'meta', 'template' or data source name
		 *		'keep_existing' => boolean; true to preserve existing content
		 *		'format' => 'native', 'commas', 'raw'
		 *		'mla_column' => boolean; not used
		 *		'quick_edit' => boolean; not used
		 *		'bulk_edit' => boolean; not used
		 *		'meta_name' => attachment metadata element name or content template
		 *		'option' => 'text', 'single', 'export', 'array', 'multi'
		 *		'no_null' => boolean; true to delete empty custom field values
		 *
		 * For IPTC/EXIF Mapping, $settings is an array indexed by
		 * the mapping category; 'standard', 'taxonomy' and 'custom'.
		 * Each category is an array of rules, with slightly different formats.
		 *
		 * Each 'standard' category array element is a rule (array) containing:
		 *		'name' => field slug; 'post_title', 'post_name', 'image_alt', 'post_excerpt', 'post_content'
		 *		'iptc_value' => IPTC Identifier or friendly name
		 *		'exif_value' => EXIF element name
		 *		'iptc_first' => boolean; true to prefer IPTC value over EXIF value
		 *		'keep_existing' => boolean; true to preserve existing content
		 *
		 * Each 'taxonomy' category array element is a rule (array) containing:
		 *		'name' => taxonomy slug, e.g., 'post_tag', 'attachment_category'
		 *		'hierarchical' => boolean; true for hierarchical taxonomies
		 *		'iptc_value' => IPTC Identifier or friendly name
		 *		'exif_value' => EXIF element name
		 *		'iptc_first' => boolean; true to prefer IPTC value over EXIF value
		 *		'keep_existing' => boolean; true to preserve existing content
		 *		'parent' => zero for none or the term_id of the parent 
		 *		'delimiters' => term separator(s), e.g., ',;'
		 *
		 * Each 'custom' category array element is a rule (array) containing:
		 *		'name' => custom field name
		 *		'iptc_value' => IPTC Identifier or friendly name
		 *		'exif_value' => EXIF element name
		 *		'iptc_first' => boolean; true to prefer IPTC value over EXIF value
		 *		'keep_existing' => boolean; true to preserve existing content
		 */
		return $settings;
	} // mla_mapping_settings_filter

	/**
	 * MLA Mapping Rule Filter
	 *
	 * This filter is called once for each mapping rule, before the rule
	 * is evaluated. You can change the rule parameters, or prevent rule
	 * evaluation by returning $setting_value['data_source'] = 'none'; 
	 *
	 * @since 1.00
	 *
	 * @param	array 	custom_field_mapping rule
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against: custom_field_mapping or single_attachment_mapping
	 * @param	array 	attachment_metadata, default NULL
	 *
	 * @return	array	updated custom_field_mapping rule
	 */
	public static function mla_mapping_rule( $setting_value, $post_id, $category, $attachment_metadata ) {
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_rule_filter( {$post_id}, {$category} ) setting_value = " . var_export( $setting_value, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_rule_filter( {$post_id}, {$category} ) attachment_metadata = " . var_export( $attachment_metadata, true ), 0 );

		/*
		 * $setting_value is an array containing a mapping rule; see above
		 * To stop this rule's evaluation and mapping, return NULL
		 */
		return $setting_value;
	} // mla_mapping_rule_filter

	/**
	 * MLA Mapping Custom Field Value Filter
	 *
	 * This filter is called once for each custom field mapping rule, after the rule
	 * is evaluated. You can change the new value produced by the rule.
	 *
	 * @since 1.00
	 *
	 * @param	mixed 	value returned by the rule
	 * @param	array 	custom_field_mapping rule
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against: custom_field_mapping or single_attachment_mapping
	 * @param	array 	attachment_metadata, default NULL
	 *
	 * @return	array	updated rule value
	 */
	public static function mla_mapping_custom_value( $new_text, $setting_value, $post_id, $category, $attachment_metadata ) {
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_custom_value_filter( {$post_id}, {$category} ) new_text = " . var_export( $new_text, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_custom_value_filter( {$post_id}, {$category} ) setting_value = " . var_export( $setting_value, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_custom_value_filter( {$post_id}, {$category} ) attachment_metadata = " . var_export( $attachment_metadata, true ), 0 );

		/*
		 * You can use MLAOptions::mla_get_data_source() to get anything available;
		 * for example:
		 * /
		$my_setting = array(
			'data_source' => 'size_names',
			'option' => 'array'
		); // */
		//$size_names = MLAOptions::mla_get_data_source($post_id, $category, $my_setting, $attachment_metadata);
		//error_log( __LINE__ . ' MLAMappingHooksExample::mla_mapping_custom_value_filter $size_names = ' . var_export( $size_names, true ), 0 );

		/*
		 * For "empty" values, return ' '.
		 */
		return $new_text;
	} // mla_mapping_custom_value_filter

	/**
	 * MLA Mapping IPTC Value Filter
	 *
	 * This filter is called once for each IPTC/EXIF mapping rule, after the IPTC 
	 * portion of the rule is evaluated. You can change the new value produced by
	 * the rule.
	 *
	 * @since 1.00
	 *
	 * @param	mixed 	IPTC value returned by the rule
	 * @param	array 	custom_field_mapping rule
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against: iptc_exif_standard_mapping, iptc_exif_taxonomy_mapping or iptc_exif_custom_mapping
	 * @param	array 	attachment_metadata, default NULL
	 *
	 * @return	array	updated rule IPTC value
	 */
	public static function mla_mapping_iptc_value( $iptc_value, $setting_value, $post_id, $category, $attachment_metadata ) {
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_iptc_value_filter( {$post_id}, {$category} ) iptc_value = " . var_export( $iptc_value, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_iptc_value_filter( {$post_id}, {$category} ) setting_value = " . var_export( $setting_value, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_iptc_value_filter( {$post_id}, {$category} ) attachment_metadata = " . var_export( $attachment_metadata, true ), 0 );

		/*
		 * You can use MLAOptions::mla_get_data_source() to get anything available;
		 * for example:
		 * /
		$my_setting = array(
			'data_source' => 'template',
			'meta_name' => '([+iptc:keywords+])',
			'option' => 'array'
		); // */
		//$keywords = MLAOptions::mla_get_data_source($post_id, $category, $my_setting, $attachment_metadata);
		//error_log( __LINE__ . ' MLAMappingHooksExample::mla_mapping_iptc_value_filter $keywords = ' . var_export( $keywords, true ), 0 );

		/*
		 * For "empty" values, return ''.
		 */
		return $iptc_value;
	} // mla_mapping_iptc_value_filter

	/**
	 * MLA Mapping EXIF Value Filter
	 *
	 * This filter is called once for each IPTC/EXIF mapping rule, after the EXIF 
	 * portion of the rule is evaluated. You can change the new value produced by
	 * the rule.
	 *
	 * @since 1.00
	 *
	 * @param	mixed 	EXIF/Template value returned by the rule
	 * @param	array 	custom_field_mapping rule
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against: iptc_exif_standard_mapping, iptc_exif_taxonomy_mapping or iptc_exif_custom_mapping
	 * @param	array 	attachment_metadata, default NULL
	 *
	 * @return	array	updated rule EXIF/Template value
	 */
	public static function mla_mapping_exif_value( $exif_value, $setting_value, $post_id, $category, $attachment_metadata ) {
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_exif_value_filter( {$post_id}, {$category} ) exif_value = " . var_export( $exif_value, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_exif_value_filter( {$post_id}, {$category} ) setting_value = " . var_export( $setting_value, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_exif_value_filter( {$post_id}, {$category} ) attachment_metadata = " . var_export( $attachment_metadata, true ), 0 );

		/*
		 * You can use MLAOptions::mla_get_data_source() to get anything available;
		 * for example:
		 * /
		$my_setting = array(
			'data_source' => 'template',
			'meta_name' => '([+exif:Copyright+])',
			'option' => 'array'
		); // */
		//$copyright = MLAOptions::mla_get_data_source($post_id, $category, $my_setting, $attachment_metadata);
		//error_log( __LINE__ . ' MLAMappingHooksExample::mla_mapping_exif_value_filter $copyright = ' . var_export( $copyright, true ), 0 );

		/*
		 * For "empty" 'text' values, return ''.
		 * For "empty" 'array' values, return NULL.
		 */
		return $exif_value;
	} // mla_mapping_exif_value_filter

	/**
	 * MLA Mapping New Text Filter
	 *
	 * This filter is called once for each IPTC/EXIF mapping rule, after the selection
	 * between the IPTC and EXIF values has been made. You can change the new value
	 * produced by the rule.
	 *
	 * @since 1.02
	 *
	 * @param	mixed 	string or array value returned by the rule
	 * @param	array 	custom_field_mapping rule
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against: iptc_exif_standard_mapping, iptc_exif_taxonomy_mapping or iptc_exif_custom_mapping
	 * @param	array 	attachment_metadata, default NULL
	 *
	 * @return	array	updated rule EXIF/Template value
	 */
	public static function mla_mapping_new_text( $new_text, $setting_value, $post_id, $category, $attachment_metadata ) {
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_new_text_filter( {$post_id}, {$category} ) new_text = " . var_export( $new_text, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_new_text_filter( {$post_id}, {$category} ) setting_value = " . var_export( $setting_value, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_new_text_filter( {$post_id}, {$category} ) attachment_metadata = " . var_export( $attachment_metadata, true ), 0 );

		return  $new_text;
	} // mla_mapping_new_text_filter

	/**
	 * MLA Mapping Updates Filter
	 *
	 * This filter is called AFTER all mapping rules are applied.
	 * You can add, change or remove updates for the attachment's
	 * standard fields, taxonomies and/or custom fields.
	 *
	 * @since 1.00
	 *
	 * @param	array	updates for the attachment's standard fields, taxonomies and/or custom fields
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against: custom_field_mapping or single_attachment_mapping
	 * @param	array 	mapping rules
	 * @param	array 	attachment_metadata, default NULL
	 *
	 * @return	array	updated attachment's updates
	 */
	public static function mla_mapping_updates( $updates, $post_id, $category, $settings, $attachment_metadata ) {
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_updates_filter( {$post_id}, {$category} ) updates = " . var_export( $updates, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_updates_filter( {$post_id}, {$category} ) settings = " . var_export( $settings, true ), 0 );
		//error_log( __LINE__ . " MLAMappingHooksExample::mla_mapping_updates_filter( {$post_id}, {$category} ) attachment_metadata = " . var_export( $attachment_metadata, true ), 0 );

		/*
		 * Look for the "file export" rule; call the export function if there's a match.
		 * Remove the space between "*" and "/" in the next line to activate this code.
		 */
		foreach ($settings as $key => $setting ) {
			if ( 'export' == sanitize_title( $key ) && 'none' == $setting['data_source'] && false !== strpos( $setting['meta_name'], '.xml' ) ) {
				self::_export_this_item( $post_id, $setting['meta_name'] );
			}
		} // */

		/*
		 * To stop this rule's updates, return an empty array, i.e., return array();
		 */
		return $updates;

		/*
		 * Comment out the above return statement to fall through to this example,
		 * which spruces up the post Title and copies the Title to the ALT Text.
		 */

		/*
		 * If $updates[ 'post_title' ] is set, some mapping rule
		 * has been set up, so we respect the result. If not,
		 * use whatever the current Title value is.
		 */
		if ( isset( $updates[ 'post_title' ] ) ) {
			$old_value = $updates[ 'post_title' ];
		} else {
			$post = get_post( $post_id );
			$old_value = $post->post_title;
		}

		/*
		 * Clean up the Title value. If the cleanup has changed the value,
		 * put the new value in the $updates array.
		 */
		$new_title = str_replace( array( '-', '_', '.' ), ' ', $old_value );
		if ( $old_value != $new_title ) {
			$updates[ 'post_title' ] = $new_title;
		}

		// Only replace ALT Text if Image Metadata is present
		$old_value = get_metadata( 'post', $post_id, '_wp_attachment_metadata', true );
		if ( ! empty( $old_value ) ) {
			// Find the current ALT Text value
			if ( isset( $updates[ 'image_alt' ] ) ) {
				$old_value = $updates[ 'image_alt' ];
			} else {
				$old_value = get_metadata( 'post', $post_id, '_wp_attachment_image_alt', true );
			}

			// Replace the ALT Text value with the clean Title
			if ( $old_value != $new_title ) {
				$updates[ 'image_alt' ] = $new_title;
			}
		}

		/*
		 * To stop this rule's updates, return an empty array, i.e., return array();
		 */
		return $updates;
	} // mla_mapping_updates_filter

	/**
	 * Share the file handle between _export_this_item and mla_end_mapping_action
	 *
	 * @since 1.01
	 *
	 * @var	integer	Export file handle
	 */
	private static $file_handle = NULL;

	/**
	 * Export data for one or more items to an XML file
	 *
	 * This function is called from the mla_mapping_updates_filter(),
	 * just above, when the "export" rule is defined. It writes information
	 * about the item and its tags to an XML file.
	 *
	 * @since 1.01
	 *
	 * @param	integer	The ID value of the current item/attachment
	 * @param	string	The name of the output file, e.g., "data.xml"
	 *
	 * @return	void
	 */
	private static function _export_this_item( $post_id, $file ) {
		static $filename = NULL;

		// create/open the file on first call
		if ( NULL == $filename ) {
			$filename = MLA_BACKUP_DIR . $file;

			// Make sure the directory exists and is writable
			if ( ! file_exists( MLA_BACKUP_DIR ) && ! @mkdir( MLA_BACKUP_DIR ) ) {
				return; // Does not exist and cannot create it
			} elseif ( ! is_writable( MLA_BACKUP_DIR ) && ! @chmod( MLA_BACKUP_DIR , '0777') ) {
				return; // Is not writable and cannot make it so
			}

			// Every directory should have an empty index.php file for security
			if ( ! file_exists( MLA_BACKUP_DIR . 'index.php') ) {
				@touch( MLA_BACKUP_DIR . 'index.php');
			}

			// Open the file for write access	
			self::$file_handle = @fopen( $filename, 'w' );
			if ( ! self::$file_handle ) {
				self::$file_handle = NULL;
				return; // Cannot open a writable file
			}

			// Write the file header
			if ( false === @fwrite( self::$file_handle, "<items>\n" ) ) {
				@fclose( self::$file_handle );
				self::$file_handle = NULL;
				return;
			}
		} // First call

		if ( NULL == self::$file_handle ) {
			return; // Don't have a writable file
		}

		/*
		 * Get the post information and assigned terms. You can use any taxonomy (slug) you want:
		 * Categories -> category					Tags -> post_tag
		 * Att. Categories -> attachment_category	Att. Tags -> attachment_tag
		 */
		$post = get_post( $post_id );
		$terms = wp_get_post_terms( $post_id, 'post_tag' );

		// Compose the item, line by line
		$item = array();
		$item[] = "\t<item>";
		$item[] = "\t\t<id>" . absint( $post_id ) . '</id>';
		$item[] = "\t\t<title>" . $post->post_title . '</title>';
		$item[] = "\t\t<url>" . get_attachment_link( $post_id ) . '</url>';
		//$item[] = "\t\t<file>" . wp_get_attachment_url( $post_id ) . '</file>';
		$item[] = "\t\t<post_date>" . $post->post_date . '</post_date>';

		foreach( $terms as $term ) {
			$item[] = "\t\t<tag>" . $term->name . '</tag>';
		}

		$item[] = "\t</item>";

		// Write the item to the file
		$item = implode( "\n", $item );
		if ( false === @fwrite( self::$file_handle, $item ) ) {
			@fclose( self::$file_handle );
			self::$file_handle = NULL;
			return;
		}
	} // _export_this_item

	/**
	 * MLA End Mapping Action
	 *
	 * This action is called once, after all mapping rules are executed for all item(s).
	 *
	 * @since 1.01
	 *
	 * @return	void
	 */
	public static function mla_end_mapping() {
		//error_log( __LINE__ . ' MLAMappingHooksExample::mla_end_mapping_action', 0 );

		// If the Export file is open, write the trailer and close it
		if ( NULL !== self::$file_handle ) {
			@fwrite( self::$file_handle, "</items>\n" );
			@fclose( self::$file_handle );
			self::$file_handle = NULL;
			error_log( __LINE__ . ' MLAMappingHooksExample::mla_end_mapping_action Export file closed', 0 );
		}
	} // mla_end_mapping_action

	/**
	 * MLA Settings Tab List Filter
	 *
	 * This filter is before the Settings/Media Library Assistant screen is displayed.
	 * You can remove one or more tabs from the default list to prevent their display and use.
	 *
	 * @since 1.00
	 *
	 * @param	array|false	The entire tablist ( $tab = NULL ), a single tab entry or false if not found/not allowed.
	 * @param	array		The entire tablist
	 * @param	string|NULL	tab slug for single-element return or NULL to return entire tablist
	 *
	 * @return	array	updated tablist
	 */
	public static function mla_get_options_tablist( $results, $mla_tablist, $tab ) {
		//error_log( __LINE__ . ' MLAMappingHooksExample::mla_get_options_tablist_filter $results = ' . var_export( $results, true ), 0 );
		//error_log( __LINE__ . ' MLAMappingHooksExample::mla_get_options_tablist_filter $mla_tablist = ' . var_export( $mla_tablist, true ), 0 );
		//error_log( __LINE__ . ' MLAMappingHooksExample::mla_get_options_tablist_filter $tab = ' . var_export( $tab, true ), 0 );

		/*
		 * Return an updated $mla_tablist ( $tab = NULL ), an updated single element or false
		 */
		return $results;

		/*
		 * Comment out the above return statement to fall through to the example, which removes the "Uploads" tab.
		 */
		if ( NULL == $tab ) {
			unset( $results['upload'] );
		}

		return $results;
	} // mla_get_options_tablist_filter
} //MLAMappingHooksExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAMappingHooksExample::initialize');
?>