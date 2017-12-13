<?php
/**
 * Manages synchronization between a parent post and its children.
 *
 * @package   Smart_Media_Categories_Admin
 * @author    David Lingren <dlingren@comcast.net>
 * @license   GPL-2.0+
 * @link      @TODO http://example.com
 * @copyright 2014 David Lingren
 */

/**
 * This support class provides functions to manage syncronization 
 * of taxonomy terms between a parent post and its attached children.
 *
 * In the current version all of the support functions are static, and there is
 * no need to create a new instance of the class.
 *
 * @package Smart_Media_Categories_Admin
 * @author  David Lingren <dlingren@comcast.net>
 */
class SMC_Sync_Support {
	/**
	 * Find taxonomies common to Posts and Attachments
	 *
	 * @since    1.0.6
	 *
	 * @param	string	$post_type Optional; Post Type of parent objects
	 *
	 * @return	array	active taxonomy slugs
	 */
	public static function get_active_taxonomies( $post_type = NULL ) {
		$atachment_taxonomies = get_object_taxonomies( 'attachment', 'objects' );
		
		if ( is_null( $post_type ) ) {
			$post_taxonomies = SMC_Settings_Support::smc_taxonomies();
		} else {
			$post_taxonomies = get_object_taxonomies( $post_type, 'names' );
		}
		
		$active_taxonomies = array();
		
		foreach ( $atachment_taxonomies as $tax_name => $tax_object ) {
			if ( $tax_object->show_ui && in_array( $tax_name, $post_taxonomies ) ) {
				$active_taxonomies[ $tax_name ] = $tax_object;
			}
		}
		
		return $active_taxonomies;
	}
	
	/**
	 * Count the number of "Synced" and "Unsynced" posts
	 *
	 * @since    1.0.2
	 *
	 * @param	array	Optional; ('smc_status', 'post_parents', 'fields')
	 *
	 * @return	array	( 'sync' => Synced posts, 'unsync' => Unsynced posts )
	 */
	public static function get_posts_per_view( $attr = NULL ) {
		global $wpdb;
		static $save_attr = NULL, $posts_per_view = NULL;

		/*
		 * Make sure $attr is an array, even if it's empty
		 */
		if ( empty( $attr ) ) {
			$attr = array();
		} elseif ( is_string( $attr ) ) {
			$attr = shortcode_parse_atts( $attr );
		}

		/*
		 * Create the PHP variables we need
		 */
		extract( shortcode_atts( array(
			'post_type' => 'post',
			'smc_status' => NULL, // 'sync', 'unsync'
			'post_parents' => NULL, // array of IDs
			'fields' => 'ids', // 'all'
		), $attr ) );
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view $attr = ' . var_export( $attr, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view $save_attr = ' . var_export( $save_attr, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view $smc_status = ' . var_export( $smc_status, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view $post_parents = ' . var_export( $post_parents, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view $fields = ' . var_export( $fields, true ), 0 );

//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view $posts_per_view = ' . var_export( $posts_per_view, true ), 0 );
		if ( $attr == $save_attr && NULL !== $posts_per_view ) {
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view returning cached array', 0 );
			return $posts_per_view;
		}

		$save_attr = $attr;
		$posts_per_view = NULL;
		
		// Only taxonomies used for both posts AND attachments are tested
		$active_taxonomies = SMC_Sync_Support::get_active_taxonomies( $post_type );

		// Build an array of SQL clauses to find Parent/Child relationships
		$query = array();
		$query_parameters = array();

		$query[] = "SELECT p2.ID AS `post_parent`, p.ID FROM {$wpdb->posts} AS p";
		
		// INNER JOIN removes posts with no attachments
		$query[] = "INNER JOIN {$wpdb->posts} as p2";
		$query[] = "ON (p.post_parent = p2.ID)";

		$query[] = "WHERE p2.post_type = '{$post_type}'";
		$query[] = "AND p2.post_status != 'trash'";
		
		if ( ! empty( $post_parents ) ) {
			$placeholders = array();
			foreach ( $post_parents as $post_parent ) {
				$placeholders[] = '%s';
				$query_parameters[] = $post_parent;
			}
			
			$query[] = 'AND ( p.post_parent IN (' . join( ',', $placeholders ) . ') )';
		}

		$query[] = "AND p.post_type = 'attachment'";
		$query[] = "AND p.post_status = 'inherit'";

		$query =  join(' ', $query);
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view $query = ' . var_export( $query, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view $query_parameters = ' . var_export( $query_parameters, true ), 0 );
		if ( ! empty( $query_parameters ) ) {
			$results = $wpdb->get_results( $wpdb->prepare( $query, $query_parameters ) );
		} else {
			$results = $wpdb->get_results( $query );
		}
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view parent/child $results = ' . var_export( $results, true ), 0 );

		if ( is_array( $results ) ) {
			$assignments = array();
			foreach ( $results as $result ) {
				$assignments[ $result->post_parent ][ $result->ID ] = array();
				$assignments[ $result->post_parent ][ 'ttids' ] = array();
				if ( 'all' == $fields ) {
					$assignments[ $result->post_parent ][ 'terms' ] = array();
				}
			}
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view parent/child $assignments = ' . var_export( $assignments, true ), 0 );
		} else {
			return array( 'sync' => 0, 'unsync' => 0 );
		}
		
		// Build an array of SQL clauses to find Child terms
		$query = array();
		$query_parameters = array();

		$query[] = "SELECT p2.ID AS `post_parent`, p.ID, tr.term_taxonomy_id FROM {$wpdb->posts} AS p";
		
		// INNER JOIN removes posts with no attachments
		$query[] = "INNER JOIN {$wpdb->posts} as p2";
		$query[] = "ON (p.post_parent = p2.ID)";

		$query[] = "LEFT JOIN {$wpdb->term_relationships} as tr";
		$query[] = "ON (p.ID = tr.object_id)";

		$query[] = "LEFT JOIN {$wpdb->term_taxonomy} as tt";
		$query[] = "ON (tr.term_taxonomy_id = tt.term_taxonomy_id)";

		$query[] = "WHERE p2.post_type = '{$post_type}'";
		$query[] = "AND p2.post_status != 'trash'";
		
		$placeholders = array();
		foreach ( $active_taxonomies as $tax_name => $tax_object ) {
			$placeholders[] = '%s';
			$query_parameters[] = $tax_name;
		}

		$query[] = 'AND ( tt.taxonomy IN (' . join( ',', $placeholders ) . ') )';
		if ( ! empty( $post_parents ) ) {
			$placeholders = array();
			foreach ( $post_parents as $post_parent ) {
				$placeholders[] = '%s';
				$query_parameters[] = $post_parent;
			}
			
			$query[] = 'AND ( p.post_parent IN (' . join( ',', $placeholders ) . ') )';
		}

		$query[] = "AND p.post_type = 'attachment'";
		$query[] = "AND p.post_status = 'inherit'";
		$query[] = "ORDER BY p.post_parent, p.ID, tr.term_taxonomy_id";

		$query =  join(' ', $query);
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view $query = ' . var_export( $query, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view $query_parameters = ' . var_export( $query_parameters, true ), 0 );
		if ( ! empty( $query_parameters ) ) {
			$results = $wpdb->get_results( $wpdb->prepare( $query, $query_parameters ) );
		} else {
			$results = $wpdb->get_results( $query );
		}
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view child term $results = ' . var_export( $results, true ), 0 );

		if ( is_array( $results ) ) {
			foreach ( $results as $result ) {
				$assignments[ $result->post_parent ][ $result->ID ][ $result->term_taxonomy_id ] = (integer) $result->term_taxonomy_id;
			}
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view child term $assignments = ' . var_export( $assignments, true ), 0 );
		} else {
			return array( 'sync' => 0, 'unsync' => 0 );
		}
		
		// Build an array of SQL clauses to find Parent terms
		$query = array();
		$query_parameters = array();

		if ( 'all' == $fields ) {
			$query[] = "SELECT DISTINCT p2.ID, tr.term_taxonomy_id, tt.taxonomy, t.term_id, t.slug FROM {$wpdb->posts} AS p";
		} else {
			$query[] = "SELECT DISTINCT p2.ID, tr.term_taxonomy_id FROM {$wpdb->posts} AS p";
		}
		
		// INNER JOIN removes posts with no attachments
		$query[] = "INNER JOIN {$wpdb->posts} as p2";
		$query[] = "ON (p.post_parent = p2.ID)";

		$query[] = "LEFT JOIN {$wpdb->term_relationships} as tr";
		$query[] = "ON (p2.ID = tr.object_id)";

		$query[] = "LEFT JOIN {$wpdb->term_taxonomy} as tt";
		$query[] = "ON (tr.term_taxonomy_id = tt.term_taxonomy_id)";

		if ( 'all' == $fields ) {
			$query[] = "LEFT JOIN {$wpdb->terms} as t";
			$query[] = "ON (tt.term_id = t.term_id)";
		}
		
		$query[] = "WHERE p2.post_type = '{$post_type}'";
		$query[] = "AND p2.post_status != 'trash'";
		
		$placeholders = array();
		foreach ( $active_taxonomies as $tax_name => $tax_object ) {
			$placeholders[] = '%s';
			$query_parameters[] = $tax_name;
		}

		$query[] = 'AND ( tt.taxonomy IN (' . join( ',', $placeholders ) . ') )';

		if ( ! empty( $post_parents ) ) {
			$placeholders = array();
			foreach ( $post_parents as $post_parent ) {
				$placeholders[] = '%s';
				$query_parameters[] = $post_parent;
			}
			
			$query[] = 'AND ( p.post_parent IN (' . join( ',', $placeholders ) . ') )';
		}

		$query[] = "AND p.post_type = 'attachment'";
		$query[] = "AND p.post_status = 'inherit'";

		//$query[] = "GROUP BY p2.ID, tr.term_taxonomy_id";
		$query[] = "ORDER BY p2.ID, tr.term_taxonomy_id";

		$query =  join(' ', $query);
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view $query = ' . var_export( $query, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view $query_parameters = ' . var_export( $query_parameters, true ), 0 );
		if ( ! empty( $query_parameters ) ) {
			$results = $wpdb->get_results( $wpdb->prepare( $query, $query_parameters ) );
		} else {
			$results = $wpdb->get_results( $query );
		}
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view $results = ' . var_export( $results, true ), 0 );

		if ( is_array( $results ) ) {
			foreach ( $results as $result ) {
				$assignments[ $result->ID ][ 'ttids' ][ $result->term_taxonomy_id ] = (integer) $result->term_taxonomy_id;
				if ( 'all' == $fields ) {
					$assignments[ $result->ID ][ 'terms' ][ $result->taxonomy ][ $result->term_taxonomy_id ] = array( 'term_id' => (integer) $result->term_id, 'slug' => $result->slug );
				}
			}
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view all $assignments = ' . var_export( $assignments, true ), 0 );
		} else {
			return array( 'sync' => 0, 'unsync' => 0 );
		}

		// Compute sync status
		foreach ( $assignments as $parent_id => $assignment ) {
//error_log( __LINE__ ."' SMC_Sync_Support::get_posts_per_view (parent {$parent_id}) assignment = " . var_export( $assignment, true ), 0 );
			$parent_terms = $assignment['ttids'];
			unset( $assignment['ttids'] );
			unset( $assignment['terms'] );
			$assignments[ $parent_id ]['smc_sync'] = true;
			foreach ( $assignment as $child_id => $child_terms ) {
				$smc_sync = $parent_terms == $child_terms;
				$assignments[ $parent_id ][ $child_id ]['smc_sync'] = $smc_sync;
				$assignments[ $parent_id ]['smc_sync'] &= $smc_sync;
			}
		}
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view final $assignments = ' . var_export( $assignments, true ), 0 );

		switch ( $smc_status ) {
			case 'sync':
				$posts_per_view = array();
				foreach ( $assignments as $parent_id => $assignment ) {
					if ( $assignment['smc_sync'] ) {
						if ( 'ids' == $fields ) {
							$posts_per_view[] = $parent_id;
						} else {
							$posts_per_view[ $parent_id ] = $assignment;
						}
					}
				}
			
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view sync $posts_per_view = ' . var_export( $posts_per_view, true ), 0 );
				return $posts_per_view;
			case 'unsync':
				$posts_per_view = array();
				foreach ( $assignments as $parent_id => $assignment ) {
					if ( ! $assignment['smc_sync'] ) {
						if ( 'ids' == $fields ) {
							$posts_per_view[] = $parent_id;
						} else {
							$posts_per_view[ $parent_id ] = $assignment;
						}
					}
				}
			
//error_log( __LINE__ . ' SMC_Sync_Support::get_posts_per_view unsync $posts_per_view = ' . var_export( $posts_per_view, true ), 0 );
				return $posts_per_view;
		} // smc_status
		
		// Compute sync/unsync counts
		$sync_count = $unsync_count = 0;
		foreach ( $assignments as $parent_id => $assignment ) {
			$sync_count += $assignment['smc_sync'];
			$unsync_count += 1 - $assignment['smc_sync'];
		}

		$posts_per_view['sync'] = $sync_count;
		$posts_per_view['unsync'] = $unsync_count; //( $total_count > $sync_count ) ? $total_count - $sync_count : 0;
		return $posts_per_view;
	}

	/**
	 * Assemble taxonomy and term assignments for parent and children
	 *
	 * @since    1.0.2
	 *
	 * @param	integer	ID of the parent post
	 * @param	array	IDs of children
	 *
	 * @return	array	( [object_id] => array( [taxonomy] => array( [term_taxonomy_id] => array( 'id' => term_id, 'slug' => term_slug )... 'smc_sync' => true/false )... 'smc_sync' => true/false )
	 */
	public static function get_terms( $parent_id, $children ) {
		global $wpdb;
		static $active_taxonomies = NULL, $current_type = NULL;
//error_log( __LINE__ . " SMC_Sync_Support::get_terms( $parent_id ) \$children = " . var_export( $children, true ), 0 );

		$parent = get_post( $parent_id );
		
		if ( NULL == $active_taxonomies || $current_type !== $parent->post_type ) {
			$current_type = $parent->post_type;
			$active_taxonomies = SMC_Sync_Support::get_active_taxonomies( $current_type );
		}
//error_log( __LINE__ . ' SMC_Sync_Support::get_terms $active_taxonomies( {$current_type} ) = ' . var_export( $active_taxonomies, true ), 0 );

		$posts = implode( ',', array_merge( array( $parent_id ), $children ) );
//error_log( __LINE__ . ' SMC_Sync_Support::get_terms $posts = ' . var_export( $posts, true ), 0 );

		$query = "SELECT object_id, tt.term_taxonomy_id, tt.term_id, slug, taxonomy FROM " . $wpdb->term_relationships . " as tr INNER JOIN " . $wpdb->term_taxonomy . " AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN " . $wpdb->terms . " AS t ON tt.term_id = t.term_id WHERE tr.object_id IN ( {$posts} )";
//error_log( __LINE__ . ' SMC_Sync_Support::get_terms $query = ' . var_export( $query, true ), 0 );
		
		$terms = $wpdb->get_results( $query, OBJECT );
//error_log( __LINE__ . ' SMC_Sync_Support::get_terms $terms = ' . var_export( $terms, true ), 0 );

		$results[ $parent_id ] = array();
		foreach ( $children as $child ) {
			$results[ $child ]['smc_sync'] = true;
		}
		
		$taxonomies = array();
		foreach( $terms as $term ) {
			if ( array_key_exists( $term->taxonomy, $active_taxonomies ) ) {
				$taxonomies[ $term->taxonomy ] = $term->taxonomy;
				$results[ $term->object_id ][ $term->taxonomy ][ $term->term_taxonomy_id ] = array( 'id' => (integer) $term->term_id, 'slug' => $term->slug );
			}
		}
//error_log( __LINE__ . ' SMC_Sync_Support::get_terms $taxonomies = ' . var_export( $taxonomies, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::get_terms $results = ' . var_export( $results, true ), 0 );

		// Add synchronization state information
		foreach ( $taxonomies as $taxonomy ) {
			if ( isset( $results[ $parent_id ][ $taxonomy ] ) ) {
				$parent_terms = $results[ $parent_id ][ $taxonomy ];
			} else {
				$parent_terms = $results[ $parent_id ][ $taxonomy ] = array();
			}
//error_log( __LINE__ ."' SMC_Sync_Support::get_terms ({$taxonomy}) \$parent_terms = " . var_export( $parent_terms, true ), 0 );
			
			foreach( $children as $child ) {
				if ( ! isset( $results[ $child ][ $taxonomy ] ) ) {
					$results[ $child ][ $taxonomy ] = array();
				}
				
				$results[ $child ][ $taxonomy ]['smc_sync'] = ( $parent_terms == $results[ $child ][ $taxonomy ] );

				// All taxonomies must be synched for the overall child to be synched
				if ( isset( $results[ $child ]['smc_sync'] ) ) {
					$results[ $child ]['smc_sync'] = $results[ $child ]['smc_sync'] && $results[ $child ][ $taxonomy ]['smc_sync'];
				} else {
					$results[ $child ]['smc_sync'] = $results[ $child ][ $taxonomy ]['smc_sync'];
				}
//error_log( __LINE__ ."' SMC_Sync_Support::get_terms ({$child}) \$results = " . var_export( $results, true ), 0 );
			}
		}
		
//error_log( __LINE__ . ' SMC_Sync_Support::get_terms $results = ' . var_export( $results, true ), 0 );
		return $results;
	} // get_terms

	/**
	 * Sync all children in an array of assignments
	 *
	 * @since    1.0.2
	 *
	 * @param	array	( [parent_id] = array ( [child_id] => array( ttids, smc_sync ) ) )
	 * @return	array	update counts; array ( 'parent_count', 'children_count' )
	 */
	public static function sync_all( $assignments ) {
//error_log( __LINE__ . ' SMC_Sync_Support::sync_all $assignments = ' . var_export( $assignments, true ), 0 );

		$parent_count = 0;
		$children_count = 0;
		$initial_tax_input = array();

		// Only taxonomies used for both posts AND attachments are tested
		$active_taxonomies = SMC_Sync_Support::get_active_taxonomies();
		foreach ( $active_taxonomies as $tax_name => $tax_object ) {
			$tax_action[ $tax_name ] = 'sync';
			$initial_tax_input[ $tax_name ] = array();
		}
//error_log( __LINE__ . ' SMC_Sync_Support::sync_all $active_taxonomies = ' . var_export( $active_taxonomies, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::sync_all $tax_action = ' . var_export( $tax_action, true ), 0 );
		
		foreach( $assignments as $parent_id => $assignment ) {
			$taxonomy_terms = $assignment[ 'terms' ];
			unset( 	$assignment['terms'] );
			unset( 	$assignment['ttids'] );
			unset( 	$assignment['smc_sync'] );
			
			$tax_input = $initial_tax_input;
			foreach( $taxonomy_terms as $taxonomy => $terms ) {
				foreach( $terms as $ttid => $term ) {
					if ( $active_taxonomies[ $taxonomy ]->hierarchical ) {
						$tax_input[ $taxonomy ][] = $term['term_id']; 
					} else {
						$tax_input[ $taxonomy ][] = $term['slug']; 
					}
				} // term
			
				if ( ! $active_taxonomies[ $taxonomy ]->hierarchical ) {
					$tax_input[ $taxonomy ] = implode( ',', $tax_input[ $taxonomy ] );
				}
			} // taxonomy
			
			$children = array();
			foreach ( $assignment as $child => $terms ) {
				$children[] = $child;
			}
//error_log( __LINE__ . ' SMC_Sync_Support::sync_all $parent_id = ' . var_export( $parent_id, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::sync_all $children = ' . var_export( $children, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::sync_all $tax_input = ' . var_export( $tax_input, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::sync_all $tax_action = ' . var_export( $tax_action, true ), 0 );

			$results = SMC_Sync_Support::sync_terms( $parent_id, $children, $tax_input, $tax_action );
//error_log( __LINE__ ."' SMC_Sync_Support::sync_all {$parent_id} results = " . var_export( $results, true ), 0 );
			if ( $results['updated'] ) {
				$parent_count++;
				$children_count += $results['updated'];
			}
		} // parent_id
		
		$results = compact( 'parent_count', 'children_count' );
//error_log( __LINE__ . ' SMC_Sync_Support::sync_all $results = ' . var_export( $results, true ), 0 );
		return $results;
	}

	/**
	 * Synchronize one or more children to the parent
	 *
	 * @since    1.0.6
	 *
	 * @param	integer			ID of the parent post
	 * @param	integer|array	ID of child/children to be synced
	 *
	 * @return	array	update counts; array ( 'parent_count', 'children_count' )
	 */
	public static function sync_children_to_parent( $parent_id, $children ) {
//error_log( __LINE__ . " SMC_Sync_Support::sync_children_to_parent( $parent_id ) \$children = " . var_export( $children, true ), 0 );
		$parent = get_post( $parent_id );
		$all_assignments = SMC_Sync_Support::get_posts_per_view( array( 'post_type' => $parent->post_type, 'smc_status' => 'unsync', 'post_parents' => array( $parent_id ), 'fields' => 'all' ) );
//error_log( __LINE__ . " SMC_Sync_Support::sync_children_to_parent( $parent_id ) \$all_assignments = " . var_export( $all_assignments, true ), 0 );

		// Convert single child to an array
		$children = (array) $children;
		
		// Select only the requested, unsynced children
		$all_assignments = $all_assignments[ $parent_id ];
//error_log( __LINE__ . " SMC_Sync_Support::sync_children_to_parent( $parent_id ) \$all_assignments = " . var_export( $all_assignments, true ), 0 );
		$new_assignment['ttids'] = $all_assignments['ttids'];
		$new_assignment['terms'] = $all_assignments['terms'];
		$new_assignment['smc_sync'] = $all_assignments['smc_sync'];
//error_log( __LINE__ . " SMC_Sync_Support::sync_children_to_parent( $parent_id ) \$new_assignment = " . var_export( $new_assignment, true ), 0 );

		$children_count = 0;
		foreach( $children as $child_id ) {
			// Is the child unsynchronized?
			if ( isset( $all_assignments[ $child_id ] ) ) {
				$new_assignment[ $child_id ] = $all_assignments[ $child_id ];
				$children_count++;
			}
		}
//error_log( __LINE__ . " SMC_Sync_Support::sync_children_to_parent( $children_count ) \$new_assignment = " . var_export( $new_assignment, true ), 0 );

		if ( $children_count ) {
			$child_assignments[ $parent_id ] = $new_assignment;
			return SMC_Sync_Support::sync_all( $child_assignments );
		}
				
		return array ( 'parent_count' => 0, 'children_count' => 0 );
	}

	/**
	 * Synchronize terms from parent to children
	 *
	 * @since    1.0.2
	 *
	 * @param	integer	ID of the parent post
	 * @param	array	IDs of children to be synced
	 * @param	array	taxonomy => terms to be assigned
	 * @param	array	taxonomy => sync/ignore to control assignment
	 *
	 * @return	array	( 'parent_changed' => true/false, 'updated' => count( $children_changed )
	 */
	public static function sync_terms( $parent_id, $children, $tax_inputs, $tax_actions ) {
		global $wpdb;
//error_log( __LINE__ . ' SMC_Sync_Support::sync_terms $parent_id = ' . var_export( $parent_id, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::sync_terms $children = ' . var_export( $children, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::sync_terms $tax_input = ' . var_export( $tax_inputs, true ), 0 );
//error_log( __LINE__ . ' SMC_Sync_Support::sync_terms $tax_action = ' . var_export( $tax_actions, true ), 0 );
		
		$term_assignments = SMC_Sync_Support::get_terms( $parent_id, $children );
//error_log( __LINE__ . ' SMC_Sync_Support::sync_terms $term_assignments = ' . var_export( $term_assignments, true ), 0 );
		
		$parent_changed = false;
		$children_changed = array();
		foreach ( $tax_actions as $taxonomy => $action ) {
			if ( 'ignore' == $action ) {
				continue;
			}

			$taxonomy_obj = get_taxonomy( $taxonomy );
//error_log( __LINE__ ."' SMC_Sync_Support::sync_terms \$taxonomy_obj = " . var_export( $taxonomy_obj, true ), 0 );
			if ( ! current_user_can( $taxonomy_obj->cap->assign_terms ) ) {
				continue;
			}
			
			/*
			 * Arrays are term-ids, strings are slugs
			 */
			$terms = $tax_inputs[ $taxonomy ];
			if ( is_array( $terms ) ) {
				$terms = array_filter( array_map( 'absint', $terms ) );
			} else {
				$comma = _x( ',', 'tag_delimiter', 'smart-media-categories' );
				if ( ',' !== $comma ) {
					$terms = str_replace( $comma, ',', $terms );
				}
				
				$terms = array_filter( array_map( 'trim', explode(',', $terms ) ) );
			}

			/*
			 * Get the parent terms, compare for changes, update as necessary
			 */
			$terms_before = array();
			if ( isset( $term_assignments[ $parent_id ][ $taxonomy ] ) ) {
				foreach( $term_assignments[ $parent_id ][ $taxonomy ] as $term_taxonomy_id => $term ) {
					if ( 'smc_sync' != $term_taxonomy_id ) {
						$terms_before[] = $term_taxonomy_id;
					}
				}
				sort( $terms_before );
			}
			
			$terms_after = wp_set_post_terms( $parent_id, $terms, $taxonomy );
			sort( $terms_after );
			if ( $terms_after != $terms_before ) {
				$parent_changed = true;
			}
			
//error_log( __LINE__ ."' SMC_Sync_Support::sync_terms \$terms[{$taxonomy}] = " . var_export( $terms, true ), 0 );
//error_log( __LINE__ ."' SMC_Sync_Support::sync_terms \$parent_changed = " . var_export( $parent_changed, true ), 0 );
			/*
			 * For each child, get the terms, compare for changes, update as necessary
			 */
			foreach( $children as $child ) {
				$terms_before = array();
				if ( isset( $term_assignments[ $child ][ $taxonomy ] ) ) {
					foreach( $term_assignments[ $child ][ $taxonomy ] as $term_taxonomy_id => $term ) {
						if ( 'smc_sync' != $term_taxonomy_id ) {
							$terms_before[] = $term_taxonomy_id;
						}
					}
					sort( $terms_before );
				}
				
				$terms_after = wp_set_post_terms( $child, $terms, $taxonomy );
				sort( $terms_after );
				if ( $terms_after != $terms_before ) {
					$children_changed[ $child ] = $child;
				}
//error_log( __LINE__ ."' SMC_Sync_Support::sync_terms \$terms_before[{$child}] = " . var_export( $terms_before, true ), 0 );
//error_log( __LINE__ ."' SMC_Sync_Support::sync_terms \$terms_after = " . var_export( $terms_after, true ), 0 );
//error_log( __LINE__ ."' SMC_Sync_Support::sync_terms \$children_changed = " . var_export( $children_changed, true ), 0 );
			}
		}
		
		$updated = count( $children_changed );
		return compact( 'parent_changed', 'updated' );
	}
}
?>