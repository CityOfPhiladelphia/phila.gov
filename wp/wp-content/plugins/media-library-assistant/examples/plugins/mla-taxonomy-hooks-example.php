<?php
/**
 * Provides an example of the filters provided by the MLA registration code for the Att. Cateogires and Att. Tags taxonomies
 *
 * Created for support topic "attachment_category rewrite"
 * opened on  9/8/2017 by "alx359":
 * https://wordpress.org/support/topic/attachment_category-rewrite/
 *
 * @package MLA Taxonomy Hooks Example
 * @version 1.00
 */

/*
Plugin Name: MLA Taxonomy Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Documents the hooks provided for registering the Att. Categories and Att. Tags taxonomies
Author: David Lingren
Version: 1.00
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2017 David Lingren

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
 * Class MLA Taxonomy Hooks Example hooks all of the filters provided
 * by the "Custom Taxonomy Actions and Filters (Hooks)"
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding
 * everything else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Taxonomy Hooks Example
 * @since 1.00
 */
class MLATaxonomyHooksExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * Installs filters and actions that handle the MLA hooks for the
	 * "Field-level substitution parameters"
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {

		// Defined in /media-library-assistant/includes/class-mla-objects.php
		add_filter( 'mla_attachment_category_types', 'MLATaxonomyHooksExample::mla_attachment_category_types', 10, 1 );
		add_filter( 'mla_attachment_category_labels', 'MLATaxonomyHooksExample::mla_attachment_category_labels', 10, 1 );
		add_filter( 'mla_attachment_category_arguments', 'MLATaxonomyHooksExample::mla_attachment_category_arguments', 10, 1 );

		add_filter( 'mla_attachment_tag_types', 'MLATaxonomyHooksExample::mla_attachment_tag_types', 10, 1 );
		add_filter( 'mla_attachment_tag_labels', 'MLATaxonomyHooksExample::mla_attachment_tag_labels', 10, 1 );
		add_filter( 'mla_attachment_tag_arguments', 'MLATaxonomyHooksExample::mla_attachment_tag_arguments', 10, 1 );
	} // initialize

	/**
	 * MLA Att. Categories Object Type Filter
	 *
	 * Gives you an opportunity to modify the object types for which the taxonomy will be registered.
	 *
	 * @since 1.00
	 *
	 * @param	array	$object_type The object type(s) for which the taxonomy will be registered. Default array( 'attachment' ).
	 */
	public static function mla_attachment_category_types( $object_type ) {
		//error_log( __LINE__ . " MLATaxonomyHooksExample::mla_attachment_category_types object_type = " . var_export( $object_type, true ), 0 );

		return $object_type;
	} // mla_attachment_category_types

	/**
	 * MLA Att. Categories Labels Filter
	 *
	 * Gives you an opportunity to modify the labels used to define the taxonomy in the user interface.
	 *
	 * @since 1.00
	 *
	 * @param	array	$labels The labels used to define the taxonomy in the user interface.
	 */
	public static function mla_attachment_category_labels( $labels ) {
		//error_log( __LINE__ . " MLATaxonomyHooksExample::mla_attachment_category_labels labels = " . var_export( $labels, true ), 0 );

		return $labels;
	} // mla_attachment_category_labels

	/**
	 * MLA Att. Categories Arguments Filter
	 *
	 * Gives you an opportunity to modify the arguments used to define the taxonomy.
	 *
	 * @since 1.00
	 *
	 * @param	array	$args The arguments used to define the taxonomy.
	 */
	public static function mla_attachment_category_arguments( $args ) {
		//error_log( __LINE__ . " MLATaxonomyHooksExample::mla_attachment_category_arguments args = " . var_export( $args, true ), 0 );

		//$args['rewrite'] = array( 'slug' => 'genre' ); // Example of a custom "base" value for the taxonomy URLs
		
		return $args;
	} // mla_attachment_category_arguments

	/**
	 * MLA Att. Tags Object Type Filter
	 *
	 * Gives you an opportunity to modify the object types for which the taxonomy will be registered.
	 *
	 * @since 1.00
	 *
	 * @param	array	$object_type The object type(s) for which the taxonomy will be registered. Default array( 'attachment' ).
	 */
	public static function mla_attachment_tag_types( $object_type ) {
		//error_log( __LINE__ . " MLATaxonomyHooksExample::mla_attachment_tag_types object_type = " . var_export( $object_type, true ), 0 );

		return $object_type;
	} // mla_attachment_tag_types

	/**
	 * MLA Att. Tags Labels Filter
	 *
	 * Gives you an opportunity to modify the labels used to define the taxonomy in the user interface.
	 *
	 * @since 1.00
	 *
	 * @param	array	$labels The labels used to define the taxonomy in the user interface.
	 */
	public static function mla_attachment_tag_labels( $labels ) {
		//error_log( __LINE__ . " MLATaxonomyHooksExample::mla_attachment_tag_labels labels = " . var_export( $labels, true ), 0 );

		return $labels;
	} // mla_attachment_tag_labels

	/**
	 * MLA Att. Tags Arguments Filter
	 *
	 * Gives you an opportunity to modify the arguments used to define the taxonomy.
	 *
	 * @since 1.00
	 *
	 * @param	array	$args The arguments used to define the taxonomy.
	 */
	public static function mla_attachment_tag_arguments( $args ) {
		//error_log( __LINE__ . " MLATaxonomyHooksExample::mla_attachment_tag_arguments args = " . var_export( $args, true ), 0 );

		return $args;
	} // mla_attachment_tag_arguments
} //MLATaxonomyHooksExample

// Install the filters at an early opportunity, e.g., 'plugins_loaded' or 'init'
add_action('plugins_loaded', 'MLATaxonomyHooksExample::initialize');
?>