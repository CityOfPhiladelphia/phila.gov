<?php
/**
 * Corrects Product Image/Product Gallery issues, synchronizes taxonomy terms
 *
 * Adds a Tools/Woo Fixit submenu with buttons to perform the operations.
 *
 * @package WooCommerce Fixit
 * @version 1.11
 */

/*
Plugin Name: WooCommerce Fixit
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Corrects Product Image/Product Gallery issues
Author: David Lingren
Version: 1.11
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
 * Class Woo Fixit implements a Tools submenu page with several image-fixing tools.
 *
 * @package WooCommerce Fixit
 * @since 1.00
 */
class Woo_Fixit {
	/**
	 * Current version number
	 *
	 * @since 1.00
	 *
	 * @var	string
	 */
	const CURRENT_VERSION = '1.11';

	/**
	 * Slug prefix for registering and enqueueing submenu pages, style sheets and scripts
	 *
	 * @since 1.00
	 *
	 * @var	string
	 */
	const SLUG_PREFIX = 'woofixit-';

	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		add_action( 'admin_init', 'Woo_Fixit::admin_init_action' );
		add_action( 'admin_menu', 'Woo_Fixit::admin_menu_action' );
	}

	/**
	 * Admin Init Action
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function admin_init_action() {
	}

	/**
	 * Add submenu page in the "Tools" section
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function admin_menu_action( ) {
		$current_page_hook = add_submenu_page( 'tools.php', 'WooCommerce Fixit Tools', 'Woo Fixit', 'manage_options', self::SLUG_PREFIX . 'tools', 'Woo_Fixit::render_tools_page' );
		add_filter( 'plugin_action_links', 'Woo_Fixit::add_plugin_links_filter', 10, 2 );
	}

	/**
	 * Add the "Tools" link to the Plugins section entry
	 *
	 * @since 1.00
	 *
	 * @param	array 	array of links for the Plugin, e.g., "Activate"
	 * @param	string 	Directory and name of the plugin Index file
	 *
	 * @return	array	Updated array of links for the Plugin
	 */
	public static function add_plugin_links_filter( $links, $file ) {
		if ( $file == 'woofixit.php' ) {
			$tools_link = sprintf( '<a href="%s">%s</a>', admin_url( 'tools.php?page=' . self::SLUG_PREFIX . 'tools' ), 'Tools' );
			array_unshift( $links, $tools_link );
		}

		return $links;
	}

	/**
	 * Render (echo) the "WooCommerce Fixit" submenu in the Tools section
	 *
	 * @since 1.00
	 *
	 * @return	void Echoes HTML markup for the submenu page
	 */
	public static function render_tools_page() {
//error_log( 'Woo_Fixit::render_tools_page() $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );
		if ( !current_user_can( 'manage_options' ) ) {
			echo "WooCommerce Fixit - Error</h2>\n";
			wp_die( 'You do not have permission to manage plugin settings.' );
		}

		$setting_actions = array(
			'help' => array( 'handler' => '', 'comment' => 'Enter first/last Product ID values above to restrict tool application range. You can find ID values by hovering over the "Name" column in the WooCommerce/Products submenu table.' ),
			'warning' => array( 'handler' => '', 'comment' => '<strong>These tools make permanent updates to your database.</strong> Make a backup before you use the tools so you can restore your old values if you don&rsquo;t like the results.' ),

			'c0' => array( 'handler' => '', 'comment' => '<h3>Operations on ALL Media Library Images</h3>' ),
			'Clear Title' => array( 'handler' => '_clear_title',
				'comment' => '<strong>Delete ALL</strong> item Title fields.' ),
			'c1' => array( 'handler' => '', 'comment' => '<hr>' ),
			'Fill Title' => array( 'handler' => '_fill_title',
				'comment' => 'Fill empty item Title field with re-formatted file name.' ),
			'Replace Title' => array( 'handler' => '_replace_title',
				'comment' => '<strong>Replace ALL</strong> item Title fields with re-formatted file name.' ),

			'c2' => array( 'handler' => '', 'comment' => '<h3>Operations on Product Image/Product Gallery Images</h3>' ),
			'Clear ALT Text' => array( 'handler' => '_clear_alt_text',
				'comment' => '<strong>Delete ALL</strong> ALT Text fields for Product Image/Product Gallery items.' ),
			'c3' => array( 'handler' => '', 'comment' => '<hr>' ),
			'Fill ALT Text' => array( 'handler' => '_fill_alt_text',
				'comment' => 'Fill empty ALT Text field with first top-level Product Category.' ),
			'Replace ALT Text' => array( 'handler' => '_replace_alt_text',
				'comment' => '<strong>Replace ALL</strong> ALT Text field with first top-level Product Category.' ),

			'c4' => array( 'handler' => '', 'comment' => '<h3>Operations on the Featured Image and Product Gallery</h3>' ),
			'Remove Feature' => array( 'handler' => '_remove_feature',
				'comment' => 'Remove Product/Featured Image from the Product Gallery.' ),
			'Restore Feature' => array( 'handler' => '_restore_feature',
				'comment' => 'Restore Product/Featured Image to the Product Gallery.' ),
			'Reverse Gallery' => array( 'handler' => '_reverse_gallery',
				'comment' => 'Reverse the image order in the Product Gallery.' ),

			'c5' => array( 'handler' => '', 'comment' => '<h3>Operations on the Product Image and Product Tags Taxonomy</h3>' ),
			'Clear Product Tags' => array( 'handler' => '_clear_product_tags',
				'comment' => '<strong>Delete ALL</strong> Product Tags assignments where a Product Image exists.' ),
			'Fill Product Tags' => array( 'handler' => '_fill_product_tags',
				'comment' => 'Fill empty Product Tags assignments from Product Image Att. Tags where a Product Image exists.' ),
			'Add Product Tags' => array( 'handler' => '_fill_product_tags',
				'comment' => 'Append Product Tags assignments to <strong>ALL Products</strong> from Product Image Att. Tags where a Product Image exists.' ),
			'Replace Product Tags' => array( 'handler' => '_replace_product_tags',
				'comment' => '<strong>Replace ALL</strong> Product Tags assignments from Product Image Att. Tags where a Product Image exists.' ),

			'c6' => array( 'handler' => '', 'comment' => '<h3>Operations on Products, using the Product Image, Product Categories and Att. Tags</h3>' ),
			'Clear Product Cats' => array( 'handler' => '_clear_product_categories',
				'comment' => '<strong>Delete ALL Products&rsquo;</strong> Product Categories assignments where a Product Image exists.' ),
			'Fill Product Cats' => array( 'handler' => '_fill_product_categories',
				'comment' => 'Fill empty Product Categories assignments from Product Image Att. Tags,<br>where the Product Image Att. Tag matches an existing Product Category.' ),
			'Add Product Cats' => array( 'handler' => '_append_product_categories',
				'comment' => 'Append Product Categories assignments to <strong>ALL Products</strong> from Product Image Att. Tags,<br>where the Product Image Att. Tag matches an existing Product Category.' ),
			'Replace Product Cats' => array( 'handler' => '_replace_product_categories',
				'comment' => '<strong>Replace ALL</strong> Product Categories assignments from Product Image Att. Tags,<br>where the Product Image Att. Tag matches an existing Product Category.' ),

			'c7' => array( 'handler' => '', 'comment' => '<h3>Operations on the Att. Categories Taxonomy</h3>' ),
			'Clear Att. Cats' => array( 'handler' => '_clear_attachment_categories',
				'comment' => '<strong>Delete ALL</strong> Att. Categories assignments.' ),
			'Fill Att. Cats' => array( 'handler' => '_fill_attachment_categories',
				'comment' => 'Fill empty Att. Categories assignments from Att. Tags, where the Att. Tag matches an existing Att. Category.' ),
			'Add Att. Cats' => array( 'handler' => '_append_attachment_categories',
				'comment' => 'Append Att. Categories assignments to <strong>ALL items</strong> from Att. Tags,<br>where the Att. Tag matches an existing Att. Category.' ),
			'Replace Att. Cats' => array( 'handler' => '_replace_attachment_categories',
				'comment' => '<strong>Replace ALL</strong> Att. Categories assignments from Att. Tags, where the Att. Tag matches an existing Att. Category.' ),
 		);

		echo '<div class="wrap">' . "\n";
		echo "\t\t" . '<div id="icon-tools" class="icon32"><br/></div>' . "\n";
		echo "\t\t" . '<h2>WooCommerce Fixit Tools v' . self::CURRENT_VERSION . '</h2>' . "\n";

		if ( isset( $_REQUEST[ self::SLUG_PREFIX . 'action' ] ) ) {
			$label = $_REQUEST[ self::SLUG_PREFIX . 'action' ];
			if( isset( $setting_actions[ $label ] ) ) {
				$action = $setting_actions[ $label ]['handler'];
				if ( ! empty( $action ) ) {
					echo self::$action();
				} else {
					echo "\t\t<br>ERROR: no handler for action: \"{$label}\"\n";
				}
			} else {
				echo "\t\t<br>ERROR: unknown action: \"{$label}\"\n";
			}
		}

		echo "\t\t" . '<div style="width:700px">' . "\n";
		echo "\t\t" . '<form action="/wp-admin/tools.php?page=' . self::SLUG_PREFIX . 'tools' . '" method="post" class="' . self::SLUG_PREFIX . 'tools-form-class" id="' . self::SLUG_PREFIX . 'tools-form-id">' . "\n";
		echo "\t\t" . '  <p class="submit" style="padding-bottom: 0;">' . "\n";
		echo "\t\t" . '    <table>' . "\n";

		echo "\t\t" . '      <tr valign="top"><th style="text-align: right;" scope="row">First Product</th><td style="text-align: left;">' . "\n";
		echo "\t\t" . '        <input name="' . self::SLUG_PREFIX . 'lower" type="text" size="5" value="">' . "\n";
		echo "\t\t" . '      </td></tr>' . "\n";

		echo "\t\t" . '      <tr valign="top"><th style="text-align: right;" scope="row">Last Product</th><td style="text-align: left;">' . "\n";
		echo "\t\t" . '        <input name="' . self::SLUG_PREFIX . 'upper" type="text" size="5" value="">' . "\n";
		echo "\t\t" . '      </td></tr>' . "\n";

		foreach ( $setting_actions as $label => $action ) {
			if ( empty( $action['handler'] ) ) {
				echo "\t\t" . '      <tr><td colspan=2 style="padding: 2px 0px;">' . $action['comment'] . "</td></tr>\n";
			} else {
				echo "\t\t" . '      <tr><td width="150px">' . "\n";
				echo "\t\t" . '        <input name="' . self::SLUG_PREFIX . 'action" type="submit" class="button-primary" style="width: 140px;" value="' . $label . '" />&nbsp;&nbsp;' . "\n";
				echo "\t\t" . '      </td><td>' . "\n";
				echo "\t\t" . '        ' . $action['comment'] . "\n";
				echo "\t\t" . '      </td></tr>' . "\n";
			}
		}

		echo "\t\t" . '    </table>' . "\n";
		echo "\t\t" . '  </p>' . "\n";
		echo "\t\t" . '</form>' . "\n";
		echo "\t\t" . '</div>' . "\n";
		echo "\t\t" . '</div><!-- wrap -->' . "\n";
	}

	/**
	 * Array of Products giving Product Image and Product Gallery attachments:
	 * product_id => array( '_thumbnail_id' => image_id, '_product_image_gallery' => gallery_ids (comma-delimited string)
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $product_attachments = array();

	/**
	 * Array of Attachments giving Product assignments:
	 * attachment_id => array( '_thumbnail_id' => array( thumbnail_ids ), '_product_image_gallery' => array( gallery_ids )
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $attachment_products = array();

	/**
	 * Compile array of Product Image and Product Gallery attachments
 	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	private static function _build_product_attachments() {
		global $wpdb;

		$query = sprintf( 'SELECT m.* FROM %1$s as m INNER JOIN %2$s as p ON m.post_id = p.ID WHERE ( p.post_type = \'product\' ) AND ( m.meta_key IN ( \'_product_image_gallery\', \'_thumbnail_id\' ) ) GROUP BY m.post_id, m.meta_id ORDER BY m.post_id', $wpdb->postmeta, $wpdb->posts );
		$results = $wpdb->get_results( $query );
//error_log( 'Woo_Fixit::_build_product_attachments() $results = ' . var_export( $results, true ), 0 );

		if ( ! empty( $_REQUEST[ self::SLUG_PREFIX . 'lower' ] ) ) {
			$lower_bound = (integer) $_REQUEST[ self::SLUG_PREFIX . 'lower' ];
		} else {
			$lower_bound = 0;
		}

		if ( ! empty( $_REQUEST[ self::SLUG_PREFIX . 'upper' ] ) ) {
			$upper_bound = (integer) $_REQUEST[ self::SLUG_PREFIX . 'upper' ];
		} elseif ( $lower_bound ) {
			$upper_bound = $lower_bound;
		} else {
			$upper_bound = 0x7FFFFFFF;
		}

		self::$product_attachments = array();
		self::$attachment_products = array();

		foreach ( $results as $result ) {
			if ( ( $lower_bound > $result->post_id ) || ( $upper_bound < $result->post_id ) ) {
				continue;
			}

			self::$product_attachments[ $result->post_id ][ $result->meta_key ] = trim( $result->meta_value );

			if ( '_thumbnail_id' == $result->meta_key ) {
				$key = (integer) $result->meta_value;
				if ( isset( self::$attachment_products[ $key ] ) ) {
					self::$attachment_products[ $key ]['_thumbnail_id'][] = (integer) $result->post_id;
				} else {
					self::$attachment_products[ $key ]['_thumbnail_id'] = array( (integer) $result->post_id );
				}
			} else {
				foreach( explode( ',', $result->meta_value ) as $key ) {
					$key = (integer) trim( $key);
					if ( isset( self::$attachment_products[ $key ] ) ) {
						self::$attachment_products[ $key ]['_product_image_gallery'][] = (integer) $result->post_id;
					} else {
						self::$attachment_products[ $key ]['_product_image_gallery'] = array( (integer) $result->post_id );
					}
				}
			}
		}
//error_log( 'Woo_Fixit::_build_product_attachments() self::$product_attachments = ' . var_export( self::$product_attachments, true ), 0 );
//error_log( 'Woo_Fixit::_build_product_attachments() self::$attachment_products = ' . var_export( self::$attachment_products, true ), 0 );
	} // _build_product_attachments

	/**
	 * Replace ALL item Title fields with empty value
 	 *
	 * @since 1.00
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _clear_title() {
		global $wpdb;

		$results = $wpdb->query( "UPDATE {$wpdb->posts} SET post_title = '' WHERE post_type = 'attachment'" );
		return "<br>_clear_title() performed {$results} update(s).\n";
	} // _clear_title

	/**
	 * Fill empty item Title field with re-formatted file name
 	 *
	 * @since 1.00
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _fill_title() {
		global $wpdb;

		$query = sprintf( 'SELECT m.post_id, m.meta_value FROM %1$s as m INNER JOIN %2$s as p ON m.post_id = p.ID WHERE ( p.post_title = \'\' ) AND ( p.post_type = \'attachment\' ) AND ( m.meta_key IN ( \'_wp_attached_file\' ) )', $wpdb->postmeta, $wpdb->posts );
		$results = $wpdb->get_results( $query );

		$update_count = 0;
		$select_bits = '';
		$where_bits = array();
		$chunk_count = 0;
		foreach( $results as $result ) {
			$path_info = pathinfo( $result->meta_value );  
			$new_title = str_replace( array( '-', '_', '.' ), ' ', $path_info['filename'] );
			$select_bits .= " WHEN ID = {$result->post_id} THEN '{$new_title}'";
			$where_bits[] = $result->post_id;

			/*
			 * Run an update when the chunk is full
			 */
			if ( 25 <= ++$chunk_count ) {
				$where_bits = implode( ',', $where_bits );
				$update_query = "UPDATE {$wpdb->posts} SET post_title = CASE{$select_bits} ELSE post_title END WHERE ID IN ( {$where_bits} )";
				$query_result = $wpdb->query( $update_query );
				$update_count += $chunk_count;
				$select_bits = '';
				$where_bits = array();
				$chunk_count = 0;
			}
		}

		/*
		 * Run a final update if the chunk is partially filled
		 */
		if ( $chunk_count ) {
			$where_bits = implode( ',', $where_bits );
			$update_query = "UPDATE {$wpdb->posts} SET post_title = CASE{$select_bits} ELSE post_title END WHERE ID IN ( {$where_bits} )";
			$query_result = $wpdb->query( $update_query );
			$update_count += $chunk_count;
		}

		return "<br>_fill_title() performed {$update_count} update(s).\n";
	} // _fill_title

	/**
	 * Replace ALL item Title fields with re-formatted file name
 	 *
	 * @since 1.00
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _replace_title() {
		global $wpdb;

		$query = sprintf( 'SELECT m.post_id, m.meta_value FROM %1$s as m INNER JOIN %2$s as p ON m.post_id = p.ID WHERE ( p.post_mime_type LIKE \'%3$s\' ) AND ( p.post_type = \'attachment\' ) AND ( m.meta_key IN ( \'_wp_attached_file\' ) )', $wpdb->postmeta, $wpdb->posts, 'image/%' );
		$results = $wpdb->get_results( $query );

		$update_count = 0;
		$select_bits = '';
		$where_bits = array();
		$chunk_count = 0;
		foreach( $results as $result ) {
			$path_info = pathinfo( $result->meta_value );  
			$new_title = str_replace( array( '-', '_', '.' ), ' ', $path_info['filename'] );
			$select_bits .= " WHEN ID = {$result->post_id} THEN '{$new_title}'";
			$where_bits[] = $result->post_id;

			/*
			 * Run an update when the chunk is full
			 */
			if ( 25 <= ++$chunk_count ) {
				$where_bits = implode( ',', $where_bits );
				$update_query = "UPDATE {$wpdb->posts} SET post_title = CASE{$select_bits} ELSE post_title END WHERE ID IN ( {$where_bits} )";
				$query_result = $wpdb->query( $update_query );
				$update_count += $chunk_count;
				$select_bits = '';
				$where_bits = array();
				$chunk_count = 0;
			}
		}

		/*
		 * Run a final update if the chunk is partially filled
		 */
		if ( $chunk_count ) {
			$where_bits = implode( ',', $where_bits );
			$update_query = "UPDATE {$wpdb->posts} SET post_title = CASE{$select_bits} ELSE post_title END WHERE ID IN ( {$where_bits} )";
			$query_result = $wpdb->query( $update_query );
			$update_count += $chunk_count;
		}

		return "<br>_replace_title() performed {$update_count} update(s).\n";
	} // _replace_title

	/**
	 * Empty ALT Text field in all Product Image/Product Gallery items
 	 *
	 * @since 1.00
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _clear_alt_text() {
		global $wpdb;

		self::_build_product_attachments();
		ksort( self::$attachment_products );
		$update_count = 0;
		foreach ( array_chunk( self::$attachment_products, 25, true ) as $chunk ) {
			$keys = implode( ',', array_keys( $chunk ) );
			$delete_query = "DELETE FROM {$wpdb->postmeta} WHERE ( post_id IN ( {$keys} ) ) AND ( meta_key = '_wp_attachment_image_alt' )";
			$query_result = $wpdb->query( $delete_query );
			$update_count += $query_result;
		}

		return "<br>_clear_alt_text() performed {$update_count} delete(s).\n";
	} // _clear_alt_text

	/**
	 * Fill empty ALT Text field with first top-level Product Category
 	 *
	 * @since 1.00
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _fill_alt_text() {
		global $wpdb;

		self::_build_product_attachments();
		$delete_count = 0;
		$insert_count = 0;
		foreach ( array_chunk( self::$product_attachments, 25, true ) as $chunk ) {
			$terms = wp_get_object_terms( array_keys( $chunk ), 'product_cat', array( 'orderby' => 'name', 'fields' => 'all_with_object_id' ) );

			/*
			 * Build an array of "first product category" names
			 */
			$product_terms = array();
			foreach ( $terms as $term ) {
				if ( isset( $product_terms[ $term->object_id ] ) ) {
					/*
					 * The first top-level term wins
					 */
					if ( 0 == $product_terms[ $term->object_id ]['parent'] ) {
						continue;
					} elseif ( ( (integer) $term->parent ) < $product_terms[ $term->object_id ]['parent'] ) {
						$product_terms[ $term->object_id ] = array( 'name' => $term->name, 'parent' => (integer) $term->parent );
					}
				} else {
					$product_terms[ $term->object_id ] = array( 'name' => $term->name, 'parent' => (integer) $term->parent );
				}
			}

			/*
			 * Assign the names to each attachment
			 */				 
			$attachment_values = array();
			foreach ( $chunk as $key => $value ) {
				if ( empty( $product_terms[ $key ] ) ) {
					continue;
				}

				if ( ! empty( $value['_thumbnail_id'] ) ) {
					$attachment_values[ $value['_thumbnail_id'] ] = $product_terms[ $key ]['name'];
				}

				if ( ! empty( $value['_product_image_gallery'] ) ) {
					$ids = explode( ',', $value['_product_image_gallery'] );
					foreach( $ids as $id ) {
						$attachment_values[ $id ] = $product_terms[ $key ]['name'];
					}
				}
			}

			/*
			 * Find the existing ALT Text values and remove them from the update
			 */
			$keys = implode( ',', array_keys( $attachment_values ) );
			$select_query = "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE ( post_id IN ( {$keys} ) ) AND ( meta_key = '_wp_attachment_image_alt' )";
			$empty_values = array();
			foreach( $wpdb->get_results( $select_query ) as $existing_value ) {
				$trim =  trim( $existing_value->meta_value );
				if ( empty( $trim ) ) {
					$empty_values[] = $existing_value->post_id;
					continue;
				}
				unset( $attachment_values[ (integer) $existing_value->post_id ] );
			}

			/*
			 * Delete empty ALT Text values
			 */
			if ( ! empty( $empty_values ) ) {
				$keys = implode( ',', $empty_values );
				$delete_query = "DELETE FROM {$wpdb->postmeta} WHERE ( post_id IN ( {$keys} ) ) AND ( meta_key = '_wp_attachment_image_alt' )";
				$query_result = $wpdb->query( $delete_query );
				$delete_count += $query_result;
			}

			/*
			 * Insert the new values
			 */
			foreach ( $attachment_values as $attachment => $text ) {
				$insert_query = "INSERT INTO {$wpdb->postmeta} ( `post_id`,`meta_key`,`meta_value` )
VALUES ( {$attachment},'_wp_attachment_image_alt','{$text}' )";
				$query_result = $wpdb->query( $insert_query );
				$insert_count += $query_result;
			}
		}

		return "<br>_fill_alt_text() performed {$delete_count} delete(s), {$insert_count} inserts(s).\n";
	} // _fill_alt_text

	/**
	 * Replace ALL ALT Text field with first top-level Product Category
 	 *
	 * @since 1.00
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _replace_alt_text() {
		global $wpdb;

		self::_build_product_attachments();
		$delete_count = 0;
		$insert_count = 0;
		foreach ( array_chunk( self::$product_attachments, 25, true ) as $chunk ) {
			$terms = wp_get_object_terms( array_keys( $chunk ), 'product_cat', array( 'orderby' => 'name', 'fields' => 'all_with_object_id' ) );

			/*
			 * Build an array of "first product category" names
			 */
			$product_terms = array();
			foreach ( $terms as $term ) {
				if ( isset( $product_terms[ $term->object_id ] ) ) {
					/*
					 * The first top-level term wins
					 */
					if ( 0 == $product_terms[ $term->object_id ]['parent'] ) {
						continue;
					} elseif ( ( (integer) $term->parent ) < $product_terms[ $term->object_id ]['parent'] ) {
						$product_terms[ $term->object_id ] = array( 'name' => $term->name, 'parent' => (integer) $term->parent );
					}
				} else {
					$product_terms[ $term->object_id ] = array( 'name' => $term->name, 'parent' => (integer) $term->parent );
				}
			}

			/*
			 * Assign the names to each attachment
			 */				 
			$attachment_values = array();
			foreach ( $chunk as $key => $value ) {
				if ( isset( $value['_thumbnail_id'] ) ) {
					$attachment_values[ $value['_thumbnail_id'] ] = $product_terms[ $key ]['name'];
				}

				if ( ! empty( $value['_product_image_gallery'] ) ) {
					$ids = explode( ',', $value['_product_image_gallery'] );
					foreach( $ids as $id ) {
						$attachment_values[ $id ] = $product_terms[ $key ]['name'];
					}
				}
			}

			/*
			 * Remove the old ALT Text values
			 */
			$keys = implode( ',', array_keys( $attachment_values ) );
			$delete_query = "DELETE FROM {$wpdb->postmeta} WHERE ( post_id IN ( {$keys} ) ) AND ( meta_key = '_wp_attachment_image_alt' )";
			$query_result = $wpdb->query( $delete_query );
			$delete_count += $query_result;

			/*
			 * Insert the new values
			 */
			foreach ( $attachment_values as $attachment => $text ) {
				$insert_query = "INSERT INTO {$wpdb->postmeta} ( `post_id`,`meta_key`,`meta_value` )
VALUES ( {$attachment},'_wp_attachment_image_alt','{$text}' )";
				$query_result = $wpdb->query( $insert_query );
				$insert_count += $query_result;
			}
		}

		return "<br>_replace_alt_text() performed {$delete_count} delete(s), {$insert_count} inserts(s).\n";
	} // _replace_alt_text

	/**
	 * Remove Product/Featured Image from the Product Gallery
 	 *
	 * @since 1.10
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _remove_feature() {
		global $wpdb;

		self::_build_product_attachments();

		$update_count = 0;
		$select_bits = '';
		$where_bits = array();
		$chunk_count = 0;
		foreach( self::$product_attachments as $post_id => $result ) {
			if ( empty( $result['_thumbnail_id'] ) ) {
				continue;
			}

			$feature = (integer) $result['_thumbnail_id'];
			$gallery = array();
			$feature_found = false;

			if ( ! empty( $result['_product_image_gallery'] ) ) {
				foreach ( explode( ',', $result['_product_image_gallery'] ) as $item ) {
					if ( $feature == (integer) $item ) {
						$feature_found = true;
					} else {
						$gallery[] = $item;
					}
				} // foreach gallery item
			}

			if ( $feature_found ) {
				$new_gallery = implode( ',', $gallery );
				$select_bits .= " WHEN post_id = {$post_id} THEN '{$new_gallery}'";
				$where_bits[] = $post_id;

				/*
				 * Run an update when the chunk is full
				 */
				if ( 25 <= ++$chunk_count ) {
					$where_bits = implode( ',', $where_bits );
					$update_query = "UPDATE {$wpdb->postmeta} SET meta_value = CASE{$select_bits} ELSE meta_value END WHERE post_id IN ( {$where_bits} ) AND meta_key = '_product_image_gallery'";
					$query_result = $wpdb->query( $update_query );
					$update_count += $chunk_count;
					$select_bits = '';
					$where_bits = array();
					$chunk_count = 0;
				}
			} // feature removed
		} // foreach product

		/*
		 * Run a final update if the chunk is partially filled
		 */
		if ( $chunk_count ) {
			$where_bits = implode( ',', $where_bits );
			$update_query = "UPDATE {$wpdb->postmeta} SET meta_value = CASE{$select_bits} ELSE meta_value END WHERE post_id IN ( {$where_bits} ) AND meta_key = '_product_image_gallery'";
			$query_result = $wpdb->query( $update_query );
			$update_count += $chunk_count;
		}

		return "<br>_remove_feature() performed {$update_count} update(s).\n";
	} // _remove_feature

	/**
	 * Restore Product/Featured Image to the Product Gallery
 	 *
	 * @since 1.10
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _restore_feature() {
		global $wpdb;

		self::_build_product_attachments();

		$update_count = 0;
		$select_bits = '';
		$where_bits = array();
		$chunk_count = 0;
		foreach( self::$product_attachments as $post_id => $result ) {
			if ( empty( $result['_thumbnail_id'] ) ) {
				continue;
			}

			$feature = (integer) $result['_thumbnail_id'];
			$gallery = array();
			$feature_found = false;
			if ( ! empty( $result['_product_image_gallery'] ) ) {
				foreach ( explode( ',', $result['_product_image_gallery'] ) as $item ) {
					if ( $feature == (integer) $item ) {
						$feature_found = true;
					} else {
						$gallery[] = $item;
					}
				} // foreach gallery item
			}

			if ( ! $feature_found ) {
				if ( count( $gallery ) ) {
					$new_gallery = implode( ',', $gallery ) . ',' . $feature;
				} else {
					$new_gallery = (string) $feature;
				}

				$select_bits .= " WHEN post_id = {$post_id} THEN '{$new_gallery}'";
				$where_bits[] = $post_id;

				/*
				 * Run an update when the chunk is full
				 */
				if ( 25 <= ++$chunk_count ) {
					$where_bits = implode( ',', $where_bits );
					$update_query = "UPDATE {$wpdb->postmeta} SET meta_value = CASE{$select_bits} ELSE meta_value END WHERE post_id IN ( {$where_bits} ) AND meta_key = '_product_image_gallery'";
					$query_result = $wpdb->query( $update_query );
					$update_count += $chunk_count;
					$select_bits = '';
					$where_bits = array();
					$chunk_count = 0;
				}
			} // feature restored
		} // foreach product

		/*
		 * Run a final update if the chunk is partially filled
		 */
		if ( $chunk_count ) {
			$where_bits = implode( ',', $where_bits );
			$update_query = "UPDATE {$wpdb->postmeta} SET meta_value = CASE{$select_bits} ELSE meta_value END WHERE post_id IN ( {$where_bits} ) AND meta_key = '_product_image_gallery'";
			$query_result = $wpdb->query( $update_query );
			$update_count += $chunk_count;
		}

		return "<br>_restore_feature() performed {$update_count} update(s).\n";
	} // _restore_feature

	/**
	 * Reverse the image order in the Product Gallery
 	 *
	 * @since 1.10
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _reverse_gallery() {
		global $wpdb;

		self::_build_product_attachments();

		$update_count = 0;
		$select_bits = '';
		$where_bits = array();
		$chunk_count = 0;
		foreach( self::$product_attachments as $post_id => $result ) {
			if ( empty( $result['_product_image_gallery'] ) ) {
				$gallery = array();
			} else {
				$gallery = explode( ',', $result['_product_image_gallery'] );
			}

			if ( 1 < count( $gallery ) ) {
				$new_gallery = implode( ',', array_reverse( $gallery ) );
				$select_bits .= " WHEN post_id = {$post_id} THEN '{$new_gallery}'";
				$where_bits[] = $post_id;

				/*
				 * Run an update when the chunk is full
				 */
				if ( 25 <= ++$chunk_count ) {
					$where_bits = implode( ',', $where_bits );
					$update_query = "UPDATE {$wpdb->postmeta} SET meta_value = CASE{$select_bits} ELSE meta_value END WHERE post_id IN ( {$where_bits} ) AND meta_key = '_product_image_gallery'";
					$query_result = $wpdb->query( $update_query );
					$update_count += $chunk_count;
					$select_bits = '';
					$where_bits = array();
					$chunk_count = 0;
				}
			} // gallery reversed
		} // foreach product

		/*
		 * Run a final update if the chunk is partially filled
		 */
		if ( $chunk_count ) {
			$where_bits = implode( ',', $where_bits );
			$update_query = "UPDATE {$wpdb->postmeta} SET meta_value = CASE{$select_bits} ELSE meta_value END WHERE post_id IN ( {$where_bits} ) AND meta_key = '_product_image_gallery'";
			$query_result = $wpdb->query( $update_query );
			$update_count += $chunk_count;
		}

		return "<br>_reverse_gallery() performed {$update_count} update(s).\n";
	} // _reverse_gallery

	/**
	 * Delete ALL Products' Product Tags assignments where a Product Image exists
 	 *
	 * @since 1.11
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _clear_product_tags() {
		return self::_update_product_tags( 'clear' );
	} // _clear_product_tags

	/**
	 * Fill empty Product Tags assignments from Product Image Att. Tags,
	 * where the Product Image exists
 	 *
	 * @since 1.11
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _fill_product_tags() {
		return self::_update_product_tags( 'fill' );
	} // _fill_product_tags

	/**
	 * Append Product Tags assignments to ALL Products from Product Image Att. Tags,
	 * where the Product Image exists
 	 *
	 * @since 1.11
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _append_product_tags() {
		return self::_update_product_tags( 'append' );
	} // _append_product_tags

	/**
	 * Replace ALL Product Tags assignments from Product Image Att. Tags,
	 * where the Product Image exists
 	 *
	 * @since 1.11
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _replace_product_tags() {
		return self::_update_product_tags( 'replace' );
	} // _replace_product_tags

	/**
	 * Common code for the Product Category operations
 	 *
	 * @since 1.11
	 *
	 * @param	string	Action to be taken - clear, fill, append, replace
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _update_product_tags( $action ) {
		global $wpdb;

		self::_build_product_attachments();

		$update_count = 0;
		$delete_count = 0;
		$terms_added = 0;
		$terms_removed = 0;

		foreach ( array_chunk( self::$product_attachments, 25, true ) as $chunk ) {
			// Find the Products that have Product Images
			$products = array();
			foreach ( $chunk as $product_id => $attachments ) {
				if ( ! empty( $attachments['_thumbnail_id'] ) ) {
					$id = absint( $attachments['_thumbnail_id'] );
					$products[ $product_id ] = $id;
				}
			}

			if ( count( $products ) == 0 ) {
				continue;
			}

			switch ( $action ) {
				case 'clear':
				case 'fill':
					// Select the attachments that have product_tag term assignments
					$ids = implode( ',', array_keys( $products ) );
					$query = sprintf( 'SELECT DISTINCT tr.object_id FROM %1$s as tr INNER JOIN %2$s as tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE ( tr.object_id IN ( %3$s ) AND tt.taxonomy = \'product_tag\' ) ', $wpdb->term_relationships, $wpdb->term_taxonomy, $ids );
					$assignments = $wpdb->get_col( $query );

					if ( 'clear' == $action ) {
						foreach ( $assignments as $assignment ) {
							wp_delete_object_term_relationships( $assignment, 'product_tag' ); 
							$update_count++;
						} // each assignment
					} else {
						// Find the products that have no assignments
						$assignments = array_diff_key( $products, array_flip( $assignments ) );

						foreach ( $assignments as $product_id => $assignment ) {
							$attachment_tags = wp_get_object_terms( $assignment, 'attachment_tag', array( 'orderby' => 'none', 'fields' => 'names' ) );

							if ( ! empty( $attachment_tags ) ) {
								$term_taxonomy_ids = wp_set_object_terms( $product_id, $attachment_tags, 'product_tag' );
								$terms_added += count( $term_taxonomy_ids );
								$update_count++;
							}
						} // each assignment
					}
					break;
				case 'append':
				case 'replace':
					foreach ( $products as $product_id => $assignment ) {
						$attachment_tags = wp_get_object_terms( $assignment, 'attachment_tag', array( 'orderby' => 'none', 'fields' => 'names' ) );

						if ( 'append' == $action ) {
							if ( ! empty( $attachment_tags ) ) {
								$old_term_taxonomy_ids = wp_get_object_terms( $product_id, 'product_tag', array( 'orderby' => 'none', 'fields' => 'tt_ids' ) );
								$term_taxonomy_ids = wp_set_object_terms( $product_id, $attachment_tags, 'product_tag', true );
								$new_terms = count( array_diff( $term_taxonomy_ids, $old_term_taxonomy_ids ) );
								if ( 0 < $new_terms ) {
									$terms_added += $new_terms;
									$update_count++;
								}
							} // common terms
						} else {
							if ( ! empty( $attachment_tags ) ) {
								$term_taxonomy_ids = wp_set_object_terms( $product_id, $attachment_tags, 'product_tag', false );
								$new_terms = count( $term_taxonomy_ids );
								if ( 0 < $new_terms ) {
									$terms_added += $new_terms;
									$update_count++;
								}
							} else {
								$term_taxonomy_ids = wp_get_object_terms( $product_id, 'product_tag', array( 'orderby' => 'none', 'fields' => 'tt_ids' ) );

								$old_terms = count( $term_taxonomy_ids );
								if ( 0 < $old_terms ) {
									$terms_removed += $old_terms;
									$delete_count++;
									$term_taxonomy_ids = wp_set_object_terms( $product_id, NULL, 'product_tag', false );
								}
							} // no common terms
						}
					} // each assignment
			} // action
		} // each chunk

		switch ( $action ) {
			case 'clear':
				return "<br>_clear_product_categories() cleared {$update_count} Product(s).\n";
				break;
			case 'fill':
				return "<br>_fill_product_categories() added {$terms_added} term(s) to {$update_count} Product(s).\n";
				break;
			case 'append':
				return "<br>_append_product_categories() added {$terms_added} term(s) to {$update_count} Product(s).\n";
				break;
			case 'replace':
				return "<br>_replace_product_categories() replaced {$terms_added} term(s) in {$update_count} Product(s), and deleted {$terms_removed} term(s) from {$delete_count} Product(s).\n";
		}

		return "<br>Unknown _update_product_categories action: {$action}";
	} // _update_product_categories

	/**
	 * Delete ALL Products' Product Categories assignments where a Product Image exists
 	 *
	 * @since 1.11
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _clear_product_categories() {
		return self::_update_product_categories( 'clear' );
	} // _clear_product_categories

	/**
	 * Fill empty Product Categories assignments from Product Image Att. Tags,
	 * where the Product Image Att. Tag matches an existing Product Category
 	 *
	 * @since 1.11
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _fill_product_categories() {
		return self::_update_product_categories( 'fill' );
	} // _fill_product_categories

	/**
	 * Append Product Categories assignments to ALL Products from Product Image Att. Tags,
	 * where the Product Image Att. Tag matches an existing Product Category
 	 *
	 * @since 1.11
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _append_product_categories() {
		return self::_update_product_categories( 'append' );
	} // _append_product_categories

	/**
	 * Replace ALL Product Categories assignments from Product Image Att. Tags,
	 * where the Product Image Att. Tag matches an existing Product Category
 	 *
	 * @since 1.11
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _replace_product_categories() {
		return self::_update_product_categories( 'replace' );
	} // _replace_product_categories

	/**
	 * Common code for the Product Category operations
 	 *
	 * @since 1.11
	 *
	 * @param	string	Action to be taken - clear, fill, append, replace
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _update_product_categories( $action ) {
		global $wpdb;

		self::_build_product_attachments();

		if ( 'clear' != $action ) {
			// Get the array of the Product Category term objects for comparison
			$product_categories = get_terms( 'product_cat', array( 'orderby' => 'none', 'hide_empty' => 0, 'fields' => 'id=>name' ) );
		} else {
			$product_categories = array();
		}

		$update_count = 0;
		$delete_count = 0;
		$terms_added = 0;
		$terms_removed = 0;

		foreach ( array_chunk( self::$product_attachments, 25, true ) as $chunk ) {
			// Find the Products that have Product Images
			$products = array();
			foreach ( $chunk as $product_id => $attachments ) {
				if ( ! empty( $attachments['_thumbnail_id'] ) ) {
					$id = absint( $attachments['_thumbnail_id'] );
					$products[ $product_id ] = $id;
				}
			}

			if ( count( $products ) == 0 ) {
				continue;
			}

			switch ( $action ) {
				case 'clear':
				case 'fill':
					// Select the attachments that have product_cat term assignments
					$ids = implode( ',', array_keys( $products ) );
					$query = sprintf( 'SELECT DISTINCT tr.object_id FROM %1$s as tr INNER JOIN %2$s as tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE ( tr.object_id IN ( %3$s ) AND tt.taxonomy = \'product_cat\' ) ', $wpdb->term_relationships, $wpdb->term_taxonomy, $ids );
					$assignments = $wpdb->get_col( $query );

					if ( 'clear' == $action ) {
						foreach ( $assignments as $assignment ) {
							wp_delete_object_term_relationships( $assignment, 'product_cat' ); 
							$update_count++;
						} // each assignment
					} else {
						// Find the products that have no assignments
						$assignments = array_diff_key( $products, array_flip( $assignments ) );

						foreach ( $assignments as $product_id => $assignment ) {
							$attachment_tags = wp_get_object_terms( $assignment, 'attachment_tag', array( 'orderby' => 'none', 'fields' => 'names' ) );
							$common_terms = array_keys( array_intersect( $product_categories, $attachment_tags ) );

							if ( ! empty( $common_terms ) ) {
								$term_taxonomy_ids = wp_set_object_terms( $product_id, $common_terms, 'product_cat' );
								$terms_added += count( $term_taxonomy_ids );
								$update_count++;
							} // common terms
						} // each assignment
					}
					break;
				case 'append':
				case 'replace':
					foreach ( $products as $product_id => $assignment ) {
						$attachment_tags = wp_get_object_terms( $assignment, 'attachment_tag', array( 'orderby' => 'none', 'fields' => 'names' ) );
						$common_terms = array_keys( array_intersect( $product_categories, $attachment_tags ) );

						if ( 'append' == $action ) {
							if ( ! empty( $common_terms ) ) {
								$old_term_taxonomy_ids = wp_get_object_terms( $product_id, 'product_cat', array( 'orderby' => 'none', 'fields' => 'tt_ids' ) );
								$term_taxonomy_ids = wp_set_object_terms( $product_id, $common_terms, 'product_cat', true );
								$new_terms = count( array_diff( $term_taxonomy_ids, $old_term_taxonomy_ids ) );
								if ( 0 < $new_terms ) {
									$terms_added += $new_terms;
									$update_count++;
								}
							} // common terms
						} else {
							if ( ! empty( $common_terms ) ) {
								$term_taxonomy_ids = wp_set_object_terms( $product_id, $common_terms, 'product_cat', false );
								$new_terms = count( $term_taxonomy_ids );
								if ( 0 < $new_terms ) {
									$terms_added += $new_terms;
									$update_count++;
								}
							} else {
								$term_taxonomy_ids = wp_get_object_terms( $product_id, 'product_cat', array( 'orderby' => 'none', 'fields' => 'tt_ids' ) );

								$old_terms = count( $term_taxonomy_ids );
								if ( 0 < $old_terms ) {
									$terms_removed += $old_terms;
									$delete_count++;
									$term_taxonomy_ids = wp_set_object_terms( $product_id, NULL, 'product_cat', false );
								}
							} // no common terms
						}
					} // each assignment
			} // action
		} // each chunk

		switch ( $action ) {
			case 'clear':
				return "<br>_clear_product_categories() cleared {$update_count} Product(s).\n";
				break;
			case 'fill':
				return "<br>_fill_product_categories() added {$terms_added} term(s) to {$update_count} Product(s).\n";
				break;
			case 'append':
				return "<br>_append_product_categories() added {$terms_added} term(s) to {$update_count} Product(s).\n";
				break;
			case 'replace':
				return "<br>_replace_product_categories() replaced {$terms_added} term(s) in {$update_count} Product(s), and deleted {$terms_removed} term(s) from {$delete_count} Product(s).\n";
		}

		return "<br>Unknown _update_product_categories action: {$action}";
	} // _update_product_categories

	/**
	 * Delete ALL Att. Categories assignments
 	 *
	 * @since 1.11
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _clear_attachment_categories() {
		global $wpdb;

		$update_count = 0;
		$offset = 0;
		$limit = 25;

		do {
			// Select a chunk of attachment IDs
			$query = sprintf( 'SELECT p.ID FROM %1$s as p WHERE ( p.post_type = \'attachment\' ) ORDER BY p.ID LIMIT %2$d, %3$d', $wpdb->posts, $offset, $limit );
			$results = $wpdb->get_col( $query );

			if ( is_array( $results ) && count( $results ) > 0 ) {
				// Select the attachments that have attachment_category term assignments
				$ids = implode( ',', $results );
				$query = sprintf( 'SELECT DISTINCT tr.object_id FROM %1$s as tr INNER JOIN %2$s as tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE ( tr.object_id IN ( %3$s ) AND tt.taxonomy = \'attachment_category\' ) ', $wpdb->term_relationships, $wpdb->term_taxonomy, $ids );
				$assignments = $wpdb->get_col( $query );

				foreach ( $assignments as $assignment ) {
					wp_delete_object_term_relationships( $assignment, 'attachment_category' ); 
					$update_count++;
				}
			} else {
				$results = array();
			}

			$offset += $limit;
		} while ( count( $results ) == $limit );

		return "<br>_clear_attachment_categories() cleared {$update_count} items(s).\n";
	} // _clear_attachment_categories

	/**
	 * Fill empty Att. Categories assignments from Att. Tags,
	 * where the Att. Tag matches an existing Att. Category
 	 *
	 * @since 1.11
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _fill_attachment_categories() {
		global $wpdb;

		// Get the array of the Att. Category term objects for comparison
		$attachment_categories = get_terms( 'attachment_category', array( 'orderby' => 'none', 'hide_empty' => 0, 'fields' => 'id=>name' ) );

		$update_count = 0;
		$terms_added = 0;
		$offset = 0;
		$limit = 25;

		do {
			// Select a chunk of attachment IDs
			$query = sprintf( 'SELECT p.ID FROM %1$s as p WHERE ( p.post_type = \'attachment\' ) ORDER BY p.ID LIMIT %2$d, %3$d', $wpdb->posts, $offset, $limit );
			$results = $wpdb->get_col( $query );

			if ( is_array( $results ) && count( $results ) > 0 ) {
				// Select the attachments that have attachment_category term assignments
				$ids = implode( ',', $results );
				$query = sprintf( 'SELECT DISTINCT tr.object_id FROM %1$s as tr INNER JOIN %2$s as tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE ( tr.object_id IN ( %3$s ) AND tt.taxonomy = \'attachment_category\' ) ', $wpdb->term_relationships, $wpdb->term_taxonomy, $ids );
				$assignments = $wpdb->get_col( $query );

				// find the attachments that have no assignments
				$assignments = array_diff( $results, $assignments );

				foreach ( $assignments as $assignment ) {
					$attachment_tags = wp_get_object_terms( $assignment, 'attachment_tag', array( 'orderby' => 'none', 'fields' => 'names' ) );
					$common_terms = array_keys( array_intersect( $attachment_categories, $attachment_tags ) );
					if ( ! empty( $common_terms ) ) {
						$term_taxonomy_ids = wp_set_object_terms( $assignment, $common_terms, 'attachment_category' );
						$terms_added += count( $term_taxonomy_ids );
						$update_count++;
					}
				}
			} else {
				$results = array();
			}

			$offset += $limit;
		} while ( count( $results ) == $limit );

		return "<br>_fill_attachment_categories() added {$terms_added} term(s) to {$update_count} item(s).\n";
	} // _fill_attachment_categories

	/**
	 * Append Att. Categories assignments to ALL items from Att. Tags,
	 * where the Att. Tag matches an existing Att. Category
 	 *
	 * @since 1.11
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _append_attachment_categories() {
		global $wpdb;

		// Get the array of the Att. Category term objects for comparison
		$attachment_categories = get_terms( 'attachment_category', array( 'orderby' => 'none', 'hide_empty' => 0, 'fields' => 'id=>name' ) );

		$update_count = 0;
		$terms_added = 0;
		$offset = 0;
		$limit = 25;

		do {
			// Select a chunk of attachment IDs
			$query = sprintf( 'SELECT p.ID FROM %1$s as p WHERE ( p.post_type = \'attachment\' ) ORDER BY p.ID LIMIT %2$d, %3$d', $wpdb->posts, $offset, $limit );
			$results = $wpdb->get_col( $query );

			if ( is_array( $results ) && count( $results ) > 0 ) {
				// Select the attachments that have attachment_tag term assignments
				$ids = implode( ',', $results );
				$query = sprintf( 'SELECT DISTINCT tr.object_id FROM %1$s as tr INNER JOIN %2$s as tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE ( tr.object_id IN ( %3$s ) AND tt.taxonomy = \'attachment_tag\' ) ', $wpdb->term_relationships, $wpdb->term_taxonomy, $ids );
				$assignments = $wpdb->get_col( $query );

				foreach ( $assignments as $assignment ) {
					$attachment_tags = wp_get_object_terms( $assignment, 'attachment_tag', array( 'orderby' => 'none', 'fields' => 'names' ) );
					$common_terms = array_keys( array_intersect( $attachment_categories, $attachment_tags ) );
					if ( ! empty( $common_terms ) ) {
						$old_term_taxonomy_ids = wp_get_object_terms( $assignment, 'attachment_category', array( 'orderby' => 'none', 'fields' => 'tt_ids' ) );
						$term_taxonomy_ids = wp_set_object_terms( $assignment, $common_terms, 'attachment_category', true );

						$new_terms = count( array_diff( $term_taxonomy_ids, $old_term_taxonomy_ids ) );
						if ( 0 < $new_terms ) {
							$terms_added += $new_terms;
							$update_count++;
						}
					}
				}
			} else {
				$results = array();
			}

			$offset += $limit;
		} while ( count( $results ) == $limit );

		return "<br>_append_attachment_categories() added {$terms_added} term(s) to {$update_count} item(s).\n";
	} // _append_attachment_categories

	/**
	 * Replace ALL Att. Categories assignments from Att. Tags,
	 * where the Att. Tag matches an existing Att. Category
 	 *
	 * @since 1.11
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _replace_attachment_categories() {
		global $wpdb;

		// Get the array of the Att. Category term objects for comparison
		$attachment_categories = get_terms( 'attachment_category', array( 'orderby' => 'none', 'hide_empty' => 0, 'fields' => 'id=>name' ) );

		$update_count = 0;
		$delete_count = 0;
		$terms_added = 0;
		$terms_removed = 0;

		$offset = 0;
		$limit = 25;

		do {
			// Select a chunk of attachment IDs
			$query = sprintf( 'SELECT p.ID FROM %1$s as p WHERE ( p.post_type = \'attachment\' ) ORDER BY p.ID LIMIT %2$d, %3$d', $wpdb->posts, $offset, $limit );
			$results = $wpdb->get_col( $query );

			if ( is_array( $results ) && count( $results ) > 0 ) {
				foreach ( $results as $assignment ) {
					$attachment_tags = wp_get_object_terms( $assignment, 'attachment_tag', array( 'orderby' => 'none', 'fields' => 'names' ) );
					$common_terms = array_keys( array_intersect( $attachment_categories, $attachment_tags ) );

					if ( ! empty( $common_terms ) ) {
						$term_taxonomy_ids = wp_set_object_terms( $assignment, $common_terms, 'attachment_category' );
						$new_terms = count( $term_taxonomy_ids );
						if ( 0 < $new_terms ) {
							$terms_added += $new_terms;
							$update_count++;
						}
					} else {
						$term_taxonomy_ids = wp_get_object_terms( $assignment, 'attachment_category', array( 'orderby' => 'none', 'fields' => 'tt_ids' ) );
						$old_terms = count( $term_taxonomy_ids );
						if ( 0 < $old_terms ) {
							$terms_removed += $old_terms;
							$delete_count++;
							$term_taxonomy_ids = wp_set_object_terms( $assignment, NULL, 'attachment_category' );
						}
					} // no common terms
				}
			} else {
				$results = array();
			}

			$offset += $limit;
		} while ( count( $results ) == $limit );

		return "<br>_replace_attachment_categories() replaced {$terms_added} term(s) in {$update_count} item(s), and deleted {$terms_removed} term(s) from {$delete_count} item(s).\n";
	} // _replace_attachment_categories
} //Woo_Fixit

/*
 * Install the submenu at an early opportunity
 */
add_action('init', 'Woo_Fixit::initialize');
?>