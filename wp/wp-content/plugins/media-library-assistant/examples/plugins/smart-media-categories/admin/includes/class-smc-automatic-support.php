<?php
/**
 * Support functions for the SMC automatic actions.
 *
 * @package   Smart_Media_Categories_Admin
 * @author    David Lingren <dlingren@comcast.net>
 * @license   GPL-2.0+
 * @link      @TODO http://example.com
 * @copyright 2014 David Lingren
 */

/**
 * This support class provides functions to implement the "automatic actions"
 *
 * In the current version all of the support functions are static, and there is
 * no need to create a new instance of the class.
 *
 * @package Smart_Media_Categories_Admin
 * @author  David Lingren <dlingren@comcast.net>
 */
class SMC_Automatic_Support {
	/**
	 * Option definitions
	 *
	 * Defined NULL and initialized at runtime for I18N support.
	 *
	 * @since    1.0.6
	 *
	 * @var      array
	 */
	// protected static $option_definitions = NULL;

	/**
	 * Initialize the hooks required for automatic actions
	 *
	 * @since    1.0.6
	 *
	 * @return	void
	 */
	public static function initialize() {
		//error_log( __LINE__ . ' SMC_Automatic_Support::initialize() $_REQUEST = ' . var_export( $_REQUEST, true), 0 );

		add_filter( 'wp_handle_upload_prefilter', 'SMC_Automatic_Support::filter_wp_handle_upload_prefilter', 0x7FFFFFFE, 1 );
		add_filter( 'wp_handle_upload', 'SMC_Automatic_Support::filter_wp_handle_upload_filter', 0x7FFFFFFE, 2 );

		add_action( 'pre_post_update', 'SMC_Automatic_Support::action_pre_post_update', 0x7FFFFFFE, 2 );
		add_action( 'edit_attachment', 'SMC_Automatic_Support::action_edit_attachment', 0x7FFFFFFE, 1 );
		add_action( 'add_attachment', 'SMC_Automatic_Support::action_add_attachment', 0x7FFFFFFE, 1 );
		add_action( 'post_updated', 'SMC_Automatic_Support::action_post_updated', 0x7FFFFFFE, 3 );

		add_action( 'add_post_meta', 'SMC_Automatic_Support::action_add_post_meta', 0x7FFFFFFE, 3 );
		add_action( 'added_post_meta', 'SMC_Automatic_Support::action_added_post_meta', 0x7FFFFFFE, 4 );
		add_action( 'update_post_meta', 'SMC_Automatic_Support::action_update_post_meta', 0x7FFFFFFE, 4 );
		add_action( 'updated_post_meta', 'SMC_Automatic_Support::action_updated_post_meta', 0x7FFFFFFE, 4 );
		add_action( 'delete_post_meta', 'SMC_Automatic_Support::action_delete_post_meta', 0x7FFFFFFE, 4 );
		add_action( 'deleted_post_meta', 'SMC_Automatic_Support::action_deleted_post_meta', 0x7FFFFFFE, 4 );

		add_action( 'set_object_terms', 'SMC_Automatic_Support::action_set_object_terms', 0x7FFFFFFE, 6 );
		
		add_filter( 'wp_update_attachment_metadata', 'SMC_Automatic_Support::filter_wp_update_attachment_metadata', 0x7FFFFFFE, 2 );
	}
	
	/**
	 * Examine or alter the filename before the file is made permanent
 	 *
	 * Called from /wp-admin/includes/file.php, function _wp_handle_upload.
	 *
	 * @since 1.0.6
	 *
	 * @param	array	$file An array of data for a single file.
	 *
	 * @return	array	updated file parameters
	 */
	public static function filter_wp_handle_upload_prefilter( $file ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::filter_wp_handle_upload_prefilter $file = ' . var_export( $file, true), 0 );

		return $file;
 	} // filter_wp_handle_upload_prefilter

	/**
	 * Filter the data array for the uploaded file.
 	 *
	 * Called from /wp-admin/includes/file.php, function _wp_handle_upload.
	 *
	 * @since 1.0.6
	 *
	 * @param	array	{
	 *     Array of upload data.
	 *
	 *     @type string $file Filename of the newly-uploaded file.
	 *     @type string $url  URL of the uploaded file.
	 *     @type string $type File type.
	 * }
	 * @param	string	The type of upload action. Values include 'upload' or 'sideload'.
	 *
	 * @return	array	updated file parameters
	 */
	public static function filter_wp_handle_upload_filter( $upload, $context ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::filter_wp_handle_upload_filter $upload = ' . var_export( $upload, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::filter_wp_handle_upload_filter $context = ' . var_export( $context, true), 0 );

		return $upload;
 	} // filter_wp_handle_upload_filter

	/**
	 *  Fires immediately before an existing post/attachment is updated in the database
 	 *
	 * Called from /wp-includes/post.php, function wp_insert_post
	 *
	 * @since 1.0.6
	 *
	 * @param	integer	ID of updated attachment
	 * @param	array	Array of unslashed post data
	 *
	 * @return	void
	 */
	public static function action_pre_post_update( $post_id, $data ) {
		//error_log( __LINE__ . " SMC_Automatic_Support::action_pre_post_update( {$post_id} ) \$data = " . var_export( $data, true), 0 );
		//error_log( __LINE__ . " SMC_Automatic_Support::action_pre_post_update( {$post_id} ) \$post = " . var_export(get_post( $post_id ), true), 0 );

		if ( (boolean) SMC_Settings_Support::get_option( 'update_post_terms' ) ) {
			SMC_Automatic_support::rule_update_post_terms( $post_id, NULL, NULL, NULL, 'before' );
		}

		if ( (boolean) SMC_Settings_Support::get_option( 'attach_orphan' ) ) {
			SMC_Automatic_support::rule_attach_orphan( $data['post_parent'], $post_id, true );
		}

		if ( (boolean) SMC_Settings_Support::get_option( 'reattach_item' ) ) {
			SMC_Automatic_support::rule_reattach_item( $data['post_parent'], $post_id, true );
		}
 	} // action_pre_post_update

	/**
	 * Fires once an existing attachment has been updated
 	 *
	 * Called from /wp-includes/post.php, function wp_insert_post
	 *
	 * @since 1.0.6
	 *
	 * @param	integer	ID of updated attachment
	 *
	 * @return	void
	 */
	public static function action_edit_attachment( $post_id ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_edit_attachment $post_id = ' . var_export( $post_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_edit_attachment $post = ' . var_export( get_post( $post_id ), true), 0 );

		if ( (boolean) SMC_Settings_Support::get_option( 'attach_orphan' ) ) {
			SMC_Automatic_support::rule_attach_orphan( NULL, $post_id, false );
		}

		if ( (boolean) SMC_Settings_Support::get_option( 'reattach_item' ) ) {
			SMC_Automatic_support::rule_reattach_item( NULL, $post_id, false );
		}
 	} // action_edit_attachment

	/**
	 * Fires once an attachment has been added
 	 *
	 * Called from /wp-includes/post.php, function wp_insert_post
	 *
	 * @since 1.0.6
	 *
	 * @param	integer	ID of just-inserted attachment
	 *
	 * @return	void
	 */
	public static function action_add_attachment( $post_id ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_add_attachment $post_id = ' . var_export( $post_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_add_attachment $post = ' . var_export( get_post( $post_id ), true), 0 );
		
		if ( (boolean) SMC_Settings_Support::get_option( 'upload_item' ) ) {
			SMC_Automatic_support::rule_upload_item( $post_id );
		}
 	} // action_add_attachment

	/**
	 * Fires once an existing post has been updated.
 	 *
	 * Called from /wp-includes/post.php, function wp_insert_post
	 *
	 * @since 1.0.6
	 *
	 * @param	integer	Post ID.
	 * @param	WP_Post	Post object following the update.
	 * @param	WP_Post	Post object before the update.
	 *
	 * @return	void
	 */
	public static function action_post_updated( $post_id, $post_after, $post_before ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_post_updated $post_id = ' . var_export( $post_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_post_updated $post_after = ' . var_export( $post_after, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_post_updated $post_before = ' . var_export( $post_before, true), 0 );

		if ( (boolean) SMC_Settings_Support::get_option( 'insert_orphan' ) ) {
			$inserted_items = SMC_Automatic_support::compare_inserted_items( $post_after, $post_before );
//error_log( __LINE__ . ' SMC_Automatic_Support::action_post_updated orphan $inserted_items = ' . var_export( $inserted_items, true), 0 );
			SMC_Automatic_support::rule_insert_orphan( $post_after, $post_before, $inserted_items );
		}

		if ( (boolean) SMC_Settings_Support::get_option( 'insert_attached' ) ) {
			if ( empty( $inserted_items ) ) {
				$inserted_items = SMC_Automatic_support::compare_inserted_items( $post_after, $post_before );
//error_log( __LINE__ . ' SMC_Automatic_Support::action_post_updated attached $inserted_items = ' . var_export( $inserted_items, true), 0 );
			}

			SMC_Automatic_support::rule_insert_attached( $post_after, $post_before, $inserted_items );
		}

		if ( (boolean) SMC_Settings_Support::get_option( 'update_post_terms' ) ) {
			SMC_Automatic_support::rule_update_post_terms( $post_id, NULL, NULL, NULL, 'after' );
		}
 	} // action_post_updated

	/**
	 * Fires immediately before meta of a specific type is added
 	 *
	 * Called from /wp-includes/meta.php, function add_metadata
	 *
	 * @since 1.0.6
	 *
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 *
	 * @return	void
	 */
	public static function action_add_post_meta( $object_id, $meta_key, $meta_value ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_add_post_meta $object_id = ' . var_export( $object_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_add_post_meta $meta_key = ' . var_export( $meta_key, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_add_post_meta $meta_value = ' . var_export( $meta_value, true), 0 );

		if ( ( '_thumbnail_id' == $meta_key ) && (boolean) SMC_Settings_Support::get_option( 'remove_feature' ) ) {
			SMC_Automatic_support::rule_remove_feature( $object_id, $meta_value, true );
		}
 	} // action_add_post_meta

	/**
	 * Fires immediately after meta of a specific type is added
 	 *
	 * Called from /wp-includes/meta.php, function add_metadata
	 *
	 * @since 1.0.6
	 *
	 * @param int    $mid        The meta ID after successful update.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 *
	 * @return	void
	 */
	public static function action_added_post_meta( $mid, $object_id, $meta_key, $meta_value ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_added_post_meta $mid = ' . var_export( $mid, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_added_post_meta $object_id = ' . var_export( $object_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_added_post_meta $meta_key = ' . var_export( $meta_key, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_added_post_meta $meta_value = ' . var_export( $meta_value, true), 0 );

		if ( '_thumbnail_id' == $meta_key ) {
			if ( (boolean) SMC_Settings_Support::get_option( 'set_feature' ) ) {
				SMC_Automatic_support::rule_set_feature( $object_id, $meta_value );
			}

			if ( (boolean) SMC_Settings_Support::get_option( 'reattach_feature' ) ) {
				SMC_Automatic_support::rule_reattach_feature( $object_id, $meta_value );
			}

			if ( (boolean) SMC_Settings_Support::get_option( 'remove_feature' ) ) {
				SMC_Automatic_support::rule_remove_feature( $object_id, $meta_value, false );
			}
		}
 	} // action_added_post_meta

	/**
	 * Fires immediately before updating metadata of a specific type.
 	 *
	 * Called from /wp-includes/meta.php, function update_metadata
	 *
	 * @since 1.0.6
	 *
	 * @param int    $meta_id    ID of metadata entry to update.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 *
	 * @return	void
	 */
	public static function action_update_post_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_update_post_meta $meta_id = ' . var_export( $meta_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_update_post_meta $object_id = ' . var_export( $object_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_update_post_meta $meta_key = ' . var_export( $meta_key, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_update_post_meta $meta_value = ' . var_export( $meta_value, true), 0 );

		if ( ( '_thumbnail_id' == $meta_key ) && (boolean) SMC_Settings_Support::get_option( 'remove_feature' ) ) {
			SMC_Automatic_support::rule_remove_feature( $object_id, $meta_value, true );
		}
 	} // action_update_post_meta

	/**
	 * Fires immediately after updating metadata of a specific type.
 	 *
	 * Called from /wp-includes/meta.php, function update_metadata
	 *
	 * @since 1.0.6
	 *
	 * @param int    $meta_id    ID of updated metadata entry.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 *
	 * @return	void
	 */
	public static function action_updated_post_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_updated_post_meta $meta_id = ' . var_export( $meta_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_updated_post_meta $object_id = ' . var_export( $object_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_updated_post_meta $meta_key = ' . var_export( $meta_key, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_updated_post_meta $meta_value = ' . var_export( $meta_value, true), 0 );

		if ( '_thumbnail_id' == $meta_key ) {
			if ( (boolean) SMC_Settings_Support::get_option( 'set_feature' ) ) {
				SMC_Automatic_support::rule_set_feature( $object_id, $meta_value );
			}

			if ( (boolean) SMC_Settings_Support::get_option( 'reattach_feature' ) ) {
				SMC_Automatic_support::rule_reattach_feature( $object_id, $meta_value );
			}

			if ( (boolean) SMC_Settings_Support::get_option( 'remove_feature' ) ) {
				SMC_Automatic_support::rule_remove_feature( $object_id, $meta_value, false );
			}
		}
 	} // action_updated_post_meta

	/**
	 * Fires immediately before deleting metadata of a specific type.
 	 *
	 * Called from /wp-includes/meta.php, function delete_metadata
	 *
	 * @since 1.0.6
	 *
	 * @param array  $meta_ids   An array of metadata entry IDs to delete.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 *
	 * @return	void
	 */
	public static function action_delete_post_meta( $meta_ids, $object_id, $meta_key, $meta_value ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_delete_post_meta $meta_ids = ' . var_export( $meta_ids, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_delete_post_meta $object_id = ' . var_export( $object_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_delete_post_meta $meta_key = ' . var_export( $meta_key, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_delete_post_meta $meta_value = ' . var_export( $meta_value, true), 0 );
 	} // action_delete_post_meta

	/**
	 * Fires immediately after updating metadata of a specific type.
 	 *
	 * Called from /wp-includes/meta.php, function delete_metadata
	 *
	 * @since 1.0.6
	 *
	 * @param array  $meta_ids   An array of deleted metadata entry IDs.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 *
	 * @return	void
	 */
	public static function action_deleted_post_meta( $meta_ids, $object_id, $meta_key, $meta_value ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_deleted_post_meta $meta_ids = ' . var_export( $meta_ids, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_deleted_post_meta $object_id = ' . var_export( $object_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_deleted_post_meta $meta_key = ' . var_export( $meta_key, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_deleted_post_meta $meta_value = ' . var_export( $meta_value, true), 0 );
 	} // action_updated_post_meta

	/**
	 * Fires after an object's terms have been set.
	 *
	 * Called from /wp-includes/taxonomy.php, function wp_set_object_terms
	 *
	 * @since 1.0.6
	 *
	 * @param int    $object_id  Object ID.
	 * @param array  $terms      An array of object terms.
	 * @param array  $tt_ids     An array of term taxonomy IDs.
	 * @param string $taxonomy   Taxonomy slug.
	 * @param bool   $append     Whether to append new terms to the old terms.
	 * @param array  $old_tt_ids Old array of term taxonomy IDs.
	 */
	public static function action_set_object_terms( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_set_object_terms $object_id = ' . var_export( $object_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_set_object_terms $terms = ' . var_export( $terms, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_set_object_terms $tt_ids = ' . var_export( $tt_ids, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_set_object_terms $taxonomy = ' . var_export( $taxonomy, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_set_object_terms $append = ' . var_export( $append, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::action_set_object_terms $old_tt_ids = ' . var_export( $old_tt_ids, true), 0 );

		if ( (boolean) SMC_Settings_Support::get_option( 'update_post_terms' ) ) {
			SMC_Automatic_support::rule_update_post_terms( $object_id, $taxonomy, $tt_ids, $old_tt_ids, 'during' );
		}
 	} // action_set_object_terms
	
	/**
	 * Filter the updated attachment meta data
	 *
	 * Called from /wp-includes/post.php, function wp_update_attachment_metadata
	 *
	 * This filter could test the $post_id variable set by action_add_attachment
	 * to separate new additions from metadata updates.
	 * 
	 * @since 1.0.6
	 *
	 * @param	array	Attachment metadata for just-inserted attachment
	 * @param	integer	ID of just-inserted attachment
	 *
	 * @return	array	Updated attachment metadata
	 */
	public static function filter_wp_update_attachment_metadata( $data, $post_id ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::filter_wp_update_attachment_metadata $data = ' . var_export( $data, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::filter_wp_update_attachment_metadata $post_id = ' . var_export( $post_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::filter_wp_update_attachment_metadata $post = ' . var_export( get_post( $post_id ), true), 0 );

		return $data;
	} // filter_wp_update_attachment_metadata

	/**
	 * Implements item uploaded to a post rule
	 *
	 * 1. When an item is uploaded to a Post (post_type = 'post'), the item inherits the parent's terms.
	 *
	 * @since 1.0.6
	 *
	 * @param	integer	attachment/child ID.
	 *
	 * @return	void
	 */
	private static function rule_upload_item( $post_id ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_upload_item $post_id = ' . var_export( $post_id, true), 0 );

		$child = get_post( $post_id );

		// Is it a child?
		if ( ( 'attachment' == $child->post_type ) && ( 0 < $child->post_parent ) ) {
			$parent = get_post( $child->post_parent );

			// Is it a child of a supported Post Type?
			if ( SMC_Settings_Support::is_smc_post_type( $parent->post_type ) ) {
				$results = SMC_Sync_Support::sync_children_to_parent( $child->post_parent, $post_id );
			} // Post child
		} // attached item
 	} // rule_upload_item

	/**
	 * Returns attachment IDs for items in post content
	 *
	 * @since 1.0.7
	 *
	 * @param	string	Post content.
	 *
	 * @return	array	items in content; ( [ID] => 'base file' )
	 */
	private static function find_inserted_items( $content ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::find_inserted_items $content = ' . var_export( $content, true), 0 );
		global $wpdb;
		static $upload_dir = NULL, $base_url = NULL;
		
		$results = array();

		if ( is_null( $upload_dir ) ) {
			$upload_dir = wp_upload_dir();
//error_log( __LINE__ . ' SMC_Automatic_Support::find_inserted_items $upload_dir = ' . var_export( $upload_dir, true), 0 );
			$base_url = $upload_dir['url'] . '/';
		}

		$match_count = preg_match_all( '/<img src="([^"]*)"/', $content, $matches, PREG_OFFSET_CAPTURE );
//error_log( __LINE__ . ' SMC_Automatic_Support::find_inserted_items $match_count = ' . var_export( $match_count, true), 0 );
//error_log( __LINE__ . ' SMC_Automatic_Support::find_inserted_items $matches = ' . var_export( $matches, true), 0 );
		if ( ( $match_count == false ) || ( $match_count == 0 ) ) {
			return $results;
		}

		foreach ( $matches[1] as $match ) {
//error_log( __LINE__ . ' SMC_Automatic_Support::find_inserted_items $match = ' . var_export( $match, true), 0 );
			// Is it in our uploads directory?
			if ( false === strpos( $match[0], $base_url ) ) {
				continue;
			}
			
			$base_file = str_replace( $base_url, '', $match[0] );
//error_log( __LINE__ . ' SMC_Automatic_Support::find_inserted_items $base_file = ' . var_export( $base_file, true), 0 );
			// Strip out intermediate sizes
			$match_count = preg_match( '/(-\d+x\d+)\./', $base_file, $matches );
//error_log( __LINE__ . ' SMC_Automatic_Support::find_inserted_items base_file $match_count = ' . var_export( $match_count, true), 0 );
//error_log( __LINE__ . ' SMC_Automatic_Support::find_inserted_items base_file $matches = ' . var_export( $matches, true), 0 );
			if ( 1 == $match_count ) {
				$base_file = str_replace( $matches[1], '', $base_file );
//error_log( __LINE__ . ' SMC_Automatic_Support::find_inserted_items new $base_file = ' . var_export( $base_file, true), 0 );
			}

			// look for an attachment match
			$sql = "SELECT post_id FROM $wpdb->postmeta
			WHERE meta_key = '_wp_attached_file' AND meta_value = '{$base_file}' LIMIT 1";
			$assignments = $wpdb->get_col( $sql );
//error_log( __LINE__ . ' SMC_Automatic_Support::find_inserted_items $assignments = ' . var_export( $assignments, true), 0 );
			if ( isset( $assignments[0] ) ) {
				$results[ absint( $assignments[0] ) ] = $base_file;
			}
		} // each URL
		
		asort( $results );
//error_log( __LINE__ . ' SMC_Automatic_Support::find_inserted_items $results = ' . var_export( $results, true), 0 );
		return $results;
 	} // find_inserted_items

	/**
	 * Finds the "inserted items" differences before/after a post update
	 *
	 * @since 1.0.7
	 *
	 * @param WP_Post $post_after   Post object following the update.
	 * @param WP_Post $post_before  Post object before the update.
	 *
	 * @return	array	( 'inserts_changed' => false, 'inserts_before' => array(), 'inserts_after' => array(), 'inserts_added' => array() )
	 */
	private static function compare_inserted_items( $post_after, $post_before ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::compare_inserted_items $post_after = ' . var_export( $post_after, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::compare_inserted_items $post_before = ' . var_export( $post_before, true), 0 );
		$results = array( 'inserts_changed' => false, 'inserts_before' => array(), 'inserts_after' => array(), 'inserts_added' => array() );

		if ( isset( $post_before->post_content ) ) {
			$results['inserts_before'] = SMC_Automatic_Support::find_inserted_items( $post_before->post_content );
		}

		if ( isset( $post_after->post_content ) ) {
			$results['inserts_after'] = SMC_Automatic_Support::find_inserted_items( $post_after->post_content );
		}

		$results['inserts_changed'] = $results['inserts_before'] != $results['inserts_after'];
		if ( $results['inserts_changed'] ) {
			$results['inserts_added'] = array_diff_assoc( $results['inserts_after'], $results['inserts_before'] );
		}
		
//error_log( __LINE__ . ' SMC_Automatic_Support::compare_inserted_items $results = ' . var_export( $results, true), 0 );
		return $results;
 	} // compare_inserted_items

	/**
	 * Implements insert an orphan in a post rule
	 *
	 * 2. Inserting an orphan in a Post will attach it to the Post and assign its terms.
	 *
	 * @since 1.0.6
	 *
	 * @param	WP_Post	Post object following the update.
	 * @param	WP_Post	Post object before the update.
	 * @param	array	( 'inserts_changed' => false, 'inserts_before' => array(), 'inserts_after' => array(), 'inserts_added' => array() )
	 *
	 * @return	void
	 */
	private static function rule_insert_orphan( $post_after, $post_before, $inserted_items ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_insert_orphan $post_after = ' . var_export( $post_after, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_insert_orphan $post_before = ' . var_export( $post_before, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_insert_orphan $inserted_items = ' . var_export( $inserted_items, true), 0 );
		global $wpdb;
		
		// Make sure its a supported Post Type and published Post
		if ( ( !SMC_Settings_Support::is_smc_post_type( $post_after->post_type ) ) || ( 'publish' != $post_after->post_status ) ) {
			return;
		}
		
		// If this is the "Publish" action, evaluate all items as new inserts
		if ( 'publish' != $post_before->post_status ) {
			$inserted_items['inserts_added'] = $inserted_items['inserts_after'];
		}
		
		$parent_id = $post_after->ID;
		
		// Evaluate new inserts
		$orphans = array();
		foreach ( $inserted_items['inserts_added'] as $insert_id => $insert ) {
			$item = get_post( $insert_id );
			
			// Make sure its an Orphan
			if ( 0 != $item->post_parent ) {
				continue;
			}

			$orphans[] = $insert_id;
			
			//Attach the child to the new Parent
			$sql = "UPDATE $wpdb->posts SET post_parent = {$parent_id}
			WHERE ID = {$insert_id}";
			$results = $wpdb->query( $sql );
//error_log( "rule_insert_orphan SET post_parent({$insert_id}) $results = " . var_export( $results, true), 0 );
			clean_attachment_cache( $insert_id );
		} // each insert
		
	
		// Sync the new children to the parent
		if ( ! empty( $orphans ) ) {
			$results = SMC_Sync_Support::sync_children_to_parent( $parent_id, $orphans );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_insert_orphan sync $results = ' . var_export( $results, true), 0 );
		}
 	} // rule_insert_orphan

	/**
	 * Implements move inserted item to a post rule
	 *
	 * 3. Inserting an item already attached to a different Post (or Page or Custom
	 * Post Type) will change the item's post_parent, delete its terms and assign
	 * the terms assigned to the new parent Post.
	 *
	 * @since 1.0.6
	 *
	 * @param	WP_Post	Post object following the update.
	 * @param	WP_Post	Post object before the update.
	 * @param	array	( 'inserts_changed' => false, 'inserts_before' => array(), 'inserts_after' => array(), 'inserts_added' => array() )
	 *
	 * @return	void
	 */
	private static function rule_insert_attached( $post_after, $post_before, $inserted_items ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_insert_attached $post_after = ' . var_export( $post_after, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_insert_attached $post_before = ' . var_export( $post_before, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_insert_attached $inserted_items = ' . var_export( $inserted_items, true), 0 );
		global $wpdb;

		// Make sure its a supported Post Type and published Post
		if ( ( !SMC_Settings_Support::is_smc_post_type( $post_after->post_type ) ) || ( 'publish' != $post_after->post_status ) ) {
			return;
		}
		
		// If this is the "Publish" action, evaluate all items as new inserts
		if ( 'publish' != $post_before->post_status ) {
			$inserted_items['inserts_added'] = $inserted_items['inserts_after'];
		}
		
		$parent_id = $post_after->ID;
		
		// Evaluate new inserts
		$new_kids = array();
		foreach ( $inserted_items['inserts_added'] as $insert_id => $insert ) {
			$item = get_post( $insert_id );
			
			// Make sure its changing parents
			if ( ( 0 == $item->post_parent ) || ( $parent_id == $item->post_parent ) ) {
				continue;
			}

			$new_kids[] = $insert_id;
			
			//Attach the child to the new Parent
			$sql = "UPDATE $wpdb->posts SET post_parent = {$parent_id}
			WHERE ID = {$insert_id}";
			$results = $wpdb->query( $sql );
//error_log( "rule_insert_attached SET post_parent({$insert_id}) $results = " . var_export( $results, true), 0 );
			clean_attachment_cache( $insert_id );
		} // each insert
		
	
		// Sync the new children to the parent
		if ( ! empty( $new_kids ) ) {
			$results = SMC_Sync_Support::sync_children_to_parent( $parent_id, $new_kids );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_insert_attached sync $results = ' . var_export( $results, true), 0 );
		}
 	} // rule_insert_attached

	/**
	 * Implements update a post's terms rule
	 *
	 * 4. When a Post's terms are updated, the Post's children inherit the current terms of the parent.
	 *
	 * @since 1.0.6
	 *
	 * @param	integer	Post ID.
	 * @param	array	An array of new term taxonomy IDs.
	 * @param	string	Taxonomy slug.
	 * @param	array	Old array of term taxonomy IDs.
	 * @param	string	Update phase; 'before', 'during', 'after'.
	 *
	 * @return	void
	 */
	private static function rule_update_post_terms( $post_id, $taxonomy, $tt_ids, $old_tt_ids, $phase ) {
//error_log( __LINE__ . " SMC_Automatic_Support::rule_update_post_terms( {$post_id}, {$taxonomy}, {$phase} ) \$tt_ids = " . var_export( $tt_ids, true), 0 );
//error_log( __LINE__ . " SMC_Automatic_Support::rule_update_post_terms( {$post_id}, {$taxonomy}, {$phase} ) \$old_tt_ids = " . var_export( $old_tt_ids, true), 0 );
		static $update_post = NULL, $update_type = NULL, $active_taxonomies = NULL, $terms_changed = false;
		
		switch ( $phase ) {
			case 'before':
				$terms_changed = false;

				// Make sure the object is a supported Post Type
				$post = get_post( $post_id );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_update_post_terms $post = ' . var_export( $post, true), 0 );
				if ( !SMC_Settings_Support::is_smc_post_type( $post->post_type ) ) {
					$update_post = NULL;
					$update_type = NULL;
					return;
				}
				
				$update_post = $post_id;
				$update_type = $post->post_type;
				if ( is_null( $active_taxonomies ) ) {
					$active_taxonomies = SMC_Sync_Support::get_active_taxonomies( $update_type );
				}
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_update_post_terms $active_taxonomies = ' . var_export( $active_taxonomies, true), 0 );
				break;
			case 'during':
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_update_post_terms during $update_post = ' . var_export( $update_post, true), 0 );
				// Make sure it's the right Post
				if ( is_null( $update_post ) || ( $update_post != $post_id ) ) {
					return;
				}
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_update_post_terms during $taxonomy = ' . var_export( $taxonomy, true), 0 );
				
				// Make sure it's an active taxonomy
				if ( ! array_key_exists( $taxonomy, $active_taxonomies ) ) {
					return;
				}
				
				// tt_ids are strings on input, old_tt_ids are integers
				$tt_ids = array_map( absint, $tt_ids );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_update_post_terms mapped $tt_ids = ' . var_export( $tt_ids, true), 0 );
				if ( $tt_ids != $old_tt_ids ) {
					$terms_changed = true;
				}
				
//error_log( __LINE__ . " SMC_Automatic_Support::rule_update_post_terms ({$taxonomy}) \$terms_changed = " . var_export( $terms_changed, true), 0 );
				break;
			case 'after':
				// Make sure terms have changed and it's the right Post
				if ( $terms_changed && $update_post == $post_id ) {
					$all_assignments = SMC_Sync_Support::get_posts_per_view( array( 'post_type' => $update_type, 'smc_status' => 'unsync', 'post_parents' => array( $post_id ), 'fields' => 'all' ) );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_update_post_terms $all_assignments = ' . var_export( $all_assignments, true ), 0 );
					$results = SMC_Sync_Support::sync_all( $all_assignments );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_update_post_terms $results = ' . var_export( $results, true ), 0 );
				}

				$update_post = NULL;
				$terms_changed = false;
				break;
		} // phase
 	} // rule_update_post_terms

	/**
	 * Implements set featured image rule
	 *
	 * 5. When an orphan is set as a Featured Image of a Post it is attached to the Post and inherits the Post's terms.  
	 *
	 * @since 1.0.6
	 *
	 * @param	integer	post/parent ID.
	 * @param	integer	attachment/child ID.
	 *
	 * @return	void
	 */
	private static function rule_set_feature( $parent_id, $child_id ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_set_feature $parent_id = ' . var_export( $parent_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_set_feature $child_id = ' . var_export( $child_id, true), 0 );
		global $wpdb;
		
		// Make sure the item is an Orphan
		$child = get_post( $child_id );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_set_feature $child = ' . var_export( $child, true), 0 );
		if ( 0 != $child->post_parent ) {
			return;
		}
		
		// Make sure the new parent is a supported Post Type
		$parent = get_post( $parent_id );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_set_feature $parent = ' . var_export( $parent, true), 0 );
		if ( !SMC_Settings_Support::is_smc_post_type( $parent->post_type ) ) {
			return;
		}
		
		//Attach the child to the new Parent
		$sql = "UPDATE $wpdb->posts SET post_parent = {$parent_id}
		WHERE ID = {$child_id}";
		$results = $wpdb->query( $sql );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_set_feature attach $results = ' . var_export( $results, true), 0 );
		clean_attachment_cache( $child_id );

		// Sync the child to the parent
		$results = SMC_Sync_Support::sync_children_to_parent( $parent_id, $child_id );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_set_feature sync $results = ' . var_export( $results, true), 0 );
 	} // rule_set_feature

	/**
	 * Implements move featured image rule
	 *
	 * 6. If the item was previously attached to a different Post, Page or Custom Post Type, it is detached from the previous parent, reattached to the current Post and inherits the current Post's terms.
	 *
	 * @since 1.0.6
	 *
	 * @param	integer	post/parent ID.
	 * @param	integer	attachment/child ID.
	 *
	 * @return	void
	 */
	private static function rule_reattach_feature( $parent_id, $child_id ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_reattach_feature $parent_id = ' . var_export( $parent_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_reattach_feature $child_id = ' . var_export( $child_id, true), 0 );
		global $wpdb;
		
		// Make sure the item is a Child, and the parent is different
		$child = get_post( $child_id );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_reattach_feature $child = ' . var_export( $child, true), 0 );
		if ( ( 0 == $child->post_parent ) || ( $parent_id == $child->post_parent ) ) {
			return;
		}

		// Make sure the new parent is a supported Post Type
		$parent = get_post( $parent_id );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_reattach_feature $parent = ' . var_export( $parent, true), 0 );
		if ( !SMC_Settings_Support::is_smc_post_type( $parent->post_type ) ) {
			return;
		}

		//Attach the child to the new Parent
		$sql = "UPDATE $wpdb->posts SET post_parent = {$parent_id}
		WHERE ID = {$child_id}";
		$results = $wpdb->query( $sql );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_reattach_feature $results = ' . var_export( $results, true), 0 );
		clean_attachment_cache( $child_id );

		// Sync the child to the parent
		$results = SMC_Sync_Support::sync_children_to_parent( $parent_id, $child_id );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_reattach_feature sync $results = ' . var_export( $results, true), 0 );
 	} // rule_reattach_feature

	/**
	 * Implements remove old post's featured image rule
	 *
	 * 7. If the item was the Featured Image of a different Post, it is removed as the Featured Image of that Post.
	 *
	 * @since 1.0.6
	 *
	 * @param	integer	post/parent ID.
	 * @param	integer	attachment/child ID.
	 * @param	boolean	optional, default false; is before update.
	 *
	 * @return	void
	 */
	private static function rule_remove_feature( $parent_id, $child_id, $before_update = false ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_remove_feature $parent_id = ' . var_export( $parent_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_remove_feature $child_id = ' . var_export( $child_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_remove_feature $before_update = ' . var_export( $before_update, true), 0 );
		global $wpdb;
		static $old_ids = NULL;
		
		// Make sure the new parent is a supported Post Type
		$parent = get_post( $parent_id );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_remove_feature $parent = ' . var_export( $parent, true), 0 );
		if ( !SMC_Settings_Support::is_smc_post_type( $parent->post_type ) ) {
			return;
		}

		// Find all the old Featured Image assignments to a Post
		if ( $before_update ) {
			$old_ids = array();

			// Find old assignments
			$sql = "SELECT post_id FROM $wpdb->postmeta
			WHERE meta_key = '_thumbnail_id' AND meta_value = '{$child_id}'";
			$old_assignments = $wpdb->get_col( $sql );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_remove_feature $old_assignments = ' . var_export( $old_assignments, true), 0 );

			// Remove current parent from old assignments
			$index = array_search( $parent_id, $old_ids );
			if ( false !== $index ) {
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_remove_feature $index = ' . var_export( $index, true), 0 );
				unset( $old_ids[ $index ] );
			}
				
			if ( ! empty( $old_assignments ) ) {
				// Find old Post assignments
				$old_assignments = implode( ',', $old_assignments );
				$sql = "SELECT ID FROM {$wpdb->posts}
				WHERE ID IN ( {$old_assignments} ) AND post_type = {$parent->post_type}";
				$old_ids = $wpdb->get_col( $sql );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_remove_feature $old_ids = ' . var_export( $old_ids, true), 0 );
			}
		} else {
			if ( ! empty( $old_ids ) ) {
				// Delete old Post assignments
				foreach( $old_ids as $index ) {
					wp_cache_delete( $index, 'post_meta' );
				}

				$old_ids = implode( ',', $old_ids );
				$sql = "DELETE FROM $wpdb->postmeta
				WHERE post_id IN ( {$old_ids} ) AND meta_key = '_thumbnail_id'";
				$results = $wpdb->query( $sql );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_remove_feature $results = ' . var_export( $results, true), 0 );
			}
			
			$old_ids = array();
		}
 	} // rule_remove_feature

	/**
	 * Implements orphan attached to a post rule
	 *
	 * 8. When an orphan is attached to a Post, it will inherit the Post's terms.
	 *
	 * @since 1.0.6
	 *
	 * @param	integer	new post/parent ID.
	 * @param	integer	attachment/child ID.
	 * @param	boolean	optional, default false; is before update.
	 *
	 * @return	void
	 */
	private static function rule_attach_orphan( $parent_id, $child_id, $before_update = false ) {
		//error_log( __LINE__ . " SMC_Automatic_Support::rule_attach_orphan( {$parent_id}, {$child_id} ) \$before_update = " . var_export( $before_update, true), 0 );
		static $attaching_orphan = false, $new_parent = 0;
			
		if ( $before_update ) {
			// Quit if the new "parent" is "unattached"
			if ( 0 == $parent_id ) {
				return;
			}
			
			// Make sure the item is an Orphan
			$child = get_post( $child_id );
//error_log( __LINE__ . " SMC_Automatic_Support::rule_attach_orphan( {$parent_id}, {$child_id} ) \$child = " . var_export( $child, true), 0 );
			if ( ( 'attachment' != $child->post_type ) || ( 0 != $child->post_parent ) ) {
				return;
			}

			// Make sure the new parent is a supported Post Type
			$parent = get_post( $parent_id );
//error_log( __LINE__ . " SMC_Automatic_Support::rule_attach_orphan( {$parent_id}, {$child_id} ) \$parent = " . var_export( $parent, true), 0 );
			if ( !SMC_Settings_Support::is_smc_post_type( $parent->post_type ) ) {
				return;
			}

			$attaching_orphan = true;
			$new_parent = $parent_id;
			return;
		} elseif ( ! $attaching_orphan ) {
			return;
		}
		
		// Sync the child to the new parent
		$results = SMC_Sync_Support::sync_children_to_parent( $new_parent, $child_id );
//error_log( __LINE__ . " SMC_Automatic_Support::rule_attach_orphan( {$new_parent}, {$child_id} ) \$results = " . var_export( $results, true), 0 );
		$attaching_orphan = false;
		$new_parent = 0;
 	} // rule_attach_orphan

	/**
	 * Implements item reattached to a new post rule
	 *
	 * 9. When an items new parent is a Post, it will inherit the Post's terms.
	 *
	 * @since 1.0.6
	 *
	 * @param	integer	new post/parent ID.
	 * @param	integer	attachment/child ID.
	 * @param	boolean	optional, default false; is before update.
	 *
	 * @return	void
	 */
	private static function rule_reattach_item( $parent_id, $child_id, $before_update = false ) {
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_reattach_item $parent_id = ' . var_export( $parent_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_reattach_item $child_id = ' . var_export( $child_id, true), 0 );
		//error_log( __LINE__ . ' SMC_Automatic_Support::rule_reattach_item $before_update = ' . var_export( $before_update, true), 0 );
		static $changing_parent = false, $new_parent = 0;
			
		if ( $before_update ) {
			// Make sure the item has a new, different parent
			$child = get_post( $child_id );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_reattach_item $child = ' . var_export( $child, true), 0 );
			if ( ( 'attachment' != $child->post_type ) || ( 0 == $child->post_parent ) || ( $parent_id == $child->post_parent ) ) {
				return;
			}

			// Make sure the new parent is a supported Post Type
			$parent = get_post( $parent_id );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_reattach_item $parent = ' . var_export( $parent, true), 0 );
			if ( !SMC_Settings_Support::is_smc_post_type( $parent->post_type ) ) {
				return;
			}

			$changing_parent = true;
			$new_parent = $parent_id;
			return;
		} elseif ( ! $changing_parent ) {
			return;
		}
		
		// Sync the child to the new parent
		$results = SMC_Sync_Support::sync_children_to_parent( $new_parent, $child_id );
//error_log( __LINE__ . ' SMC_Automatic_Support::rule_reattach_item sync $results = ' . var_export( $results, true), 0 );
		$changing_parent = false;
		$new_parent = 0;
 	} // rule_reattach_item
} // class SMC_Automatic_Support
?>