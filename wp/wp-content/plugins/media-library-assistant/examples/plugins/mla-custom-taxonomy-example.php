<?php
/*
Plugin Name: MLA Custom Taxonomy Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Defines custom taxonomies for support topic opened on 4/15/2016 by "direys"
Author: David Lingren
Version: 1.03
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
 * Class MLA Custom Taxonomy Example Defines custom taxonomies
 *
 * Created for support topic "How do I provide a front-end search of my media items using Custom Fields?"
 * opened on 4/15/2016 by "direys".
 *
 * @package MLA Custom Taxonomy Example
 * @since 1.00
 */
class MLACustomTaxonomyExample {
	/**
	 * Registers Species, Rooms and Finishes custom taxonomies
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function build_taxonomies( ) {
		$labels = array(
			'name' => 'Species',
			'singular_name' => 'Species',
			'search_items' => 'Search Species',
			'all_items' => 'All Species',
			'parent_item' => 'Parent Species',
			'parent_item_colon' => 'Parent Species:',
			'edit_item' => 'Edit Species',
			'update_item' => 'Update Species',
			'add_new_item' => 'Add New Species',
			'new_item_name' => 'New Species',
			'menu_name' => 'Species',
		);

		register_taxonomy(
			'species',
			array( 'attachment' ),
			array(
			  'hierarchical' => true,
			  'labels' => $labels,
			  'show_ui' => true,
			  'query_var' => true,
			  'rewrite' => true,
			  'update_count_callback' => '_update_generic_term_count'
			)
		);

		$labels = array(
			'name' => 'Rooms',
			'singular_name' => 'Room',
			'search_items' => 'Search Rooms',
			'all_items' => 'All Rooms',
			'parent_item' => 'Parent Room',
			'parent_item_colon' => 'Parent Room:',
			'edit_item' => 'Edit Room',
			'update_item' => 'Update Room',
			'add_new_item' => 'Add New Room',
			'new_item_name' => 'New Room',
			'menu_name' => 'Room',
		);

		register_taxonomy(
			'room',
			array( 'attachment' ),
			array(
			  'hierarchical' => false,
			  'labels' => $labels,
			  'show_ui' => true,
			  'query_var' => true,
			  'rewrite' => true,
			  'update_count_callback' => '_update_generic_term_count'
			)
		);

		$labels = array(
			'name' => 'Finishes',
			'singular_name' => 'Finish',
			'search_items' => 'Search Finishes',
			'all_items' => 'All Finishes',
			'parent_item' => 'Parent Finish',
			'parent_item_colon' => 'Parent Finish:',
			'edit_item' => 'Edit Finish',
			'update_item' => 'Update Finish',
			'add_new_item' => 'Add New Finish',
			'new_item_name' => 'New Finish',
			'menu_name' => 'Finish',
		);

		register_taxonomy(
			'finish',
			array( 'attachment' ),
			array(
			  'hierarchical' => false,
			  'labels' => $labels,
			  'show_ui' => true,
			  'query_var' => true,
			  'rewrite' => true,
			  'update_count_callback' => '_update_generic_term_count'
			)
		);
	} // build_taxonomies
} // Class MLACustomTaxonomyExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLACustomTaxonomyExample::build_taxonomies');
?>