<?php
/**
 * Database query support for MLA Ajax, Shortcode and Admin needs
 *
 * @package Media Library Assistant
 * @since 2.20
 */

/**
 * Class MLA (Media Library Assistant) Query provides database query support
 * for MLA Ajax, Shortcode and Admin needs
 *
 * @package Media Library Assistant
 * @since 2.20
 */
class MLAQuery {
	/**
	 * Provides a unique value for the ALT Text "Search Media" subquery
	 *
	 * The subquery is used to filter the Media/Assistant submenu table by
	 * ALT Text with the Search Media text box.
	 *
	 * @since 0.40
	 */
	const MLA_ALT_TEXT_SUBQUERY = 'alt_text_subquery';

	/**
	 * Provides a unique suffix for the custom field "orderby" subquery
	 *
	 * The subquery is used to sort the Media/Assistant submenu table on
	 * ALT Text and custom field columns.
	 *
	 * @since 2.15
	 */
	const MLA_ORDERBY_SUBQUERY = 'orderby_subquery';

	/**
	 * Provides a unique suffix for the "Table View custom:" SQL View
	 *
	 * The SQL View is used to filter the Media/Assistant submenu table on
	 * custom field Table Views.
	 *
	 * @since 2.15
	 */
	const MLA_TABLE_VIEW_SUBQUERY = 'table_view_subquery';

	/**
	 * WordPress version test for $wpdb->esc_like() Vs esc_sql()
	 *
	 * @since 2.13
	 *
	 * @var	boolean
	 */
	public static $wp_4dot0_plus = true;

	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 0.1
	 */
	public static function initialize() {
		self::$wp_4dot0_plus = version_compare( get_bloginfo('version'), '4.0', '>=' );

		/*
		 * Set up the Media/Assistant submenu table column definitions
		 */
		$taxonomies = get_taxonomies( array ( 'show_ui' => true ), 'names' );

		foreach ( $taxonomies as $tax_name ) {
			if ( MLACore::mla_taxonomy_support( $tax_name ) ) {
				$tax_object = get_taxonomy( $tax_name );
				self::$default_columns[ 't_' . $tax_name ] = esc_html( $tax_object->labels->name );
				self::$default_hidden_columns [] = 't_' . $tax_name;
				// self::$default_sortable_columns [] = none at this time
			} // supported taxonomy
		} // foreach $tax_name

		/*
		 * For WP 4.3+ icon will be merged with the first visible preferred column
		 */
		if ( MLATest::$wp_4dot3_plus ) {
			unset( self::$default_columns['icon'] );
		}

		self::$default_columns = array_merge( self::$default_columns, MLACore::mla_custom_field_support( 'default_columns' ) );
		self::$default_hidden_columns = array_merge( self::$default_hidden_columns, MLACore::mla_custom_field_support( 'default_hidden_columns' ) );
		self::$default_sortable_columns = array_merge( self::$default_sortable_columns, MLACore::mla_custom_field_support( 'default_sortable_columns' ) );
	}

	/**
	 * Find Featured Image and inserted image/link references to an attachment
	 * 
	 * Searches all post and page content to see if the attachment is used 
	 * as a Featured Image or inserted in the post as an image or link.
	 *
	 * @since 0.1
	 *
	 * @param	int	post ID of attachment
	 * @param	int	post ID of attachment's parent, if any
	 * @param	boolean	True to compute references, false to return empty values
	 *
	 * @return	array	Reference information; see $references array comments
	 */
	public static function mla_fetch_attachment_references( $ID, $parent, $add_references = true ) {
		/* 
		 * The MLAReferences class is only loaded when needed.
		 */
		if ( !class_exists( 'MLAReferences' ) ) {
			if ( -1 === $ID ) {
				return NULL;
			}
			
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-references.php' );
		}
		
		return MLAReferences::mla_fetch_attachment_references_handler( $ID, $parent, $add_references );
	}

	/**
	 * Add Featured Image and inserted image/link references to an array of attachments
	 * 
	 * Searches all post and page content to see if the attachmenta are used 
	 * as a Featured Image or inserted in the post as an image or link.
	 *
	 * @since 1.94
	 *
	 * @param	array	WP_Post objects, passed by reference
	 *
	 * @return	void	updates WP_Post objects with new mla_references property
	 */
	public static function mla_attachment_array_fetch_references( &$attachments ) {
		/* 
		 * The MLAReferences class is only loaded when needed.
		 */
		if ( !class_exists( 'MLAReferences' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-references.php' );
		}
		
		return MLAReferences::mla_attachment_array_fetch_references_handler( $attachments );
	}
	
	/**
	 * Invalidates the $mla_galleries or $galleries array and cached values
	 *
	 * @since 1.00
	 *
	 * @param	string name of the gallery's cache/option variable
	 *
	 * @return	void
	 */
	public static function mla_flush_mla_galleries( $option_name ) {
		delete_transient( MLA_OPTION_PREFIX . 't_' . $option_name );

		/* 
		 * If MLAReferences isn't loaded there is nothing else to do
		 */
		if ( class_exists( 'MLAReferences' ) ) {
			MLAReferences::mla_flush_mla_galleries_handler( $option_name );
		}
	}

	/**
	 * Builds the $default_columns array with translated source texts.
	 *
	 * Called from MLATest::initialize because the $default_columns information
	 * might be accessed from "front end" posts/pages.
	 *
	 * @since 1.71
	 */
	public static function mla_localize_default_columns_array( ) {
		/*
		 * Build the default columns array at runtime to accomodate calls to the
		 * localization functions
		 */
		self::$default_columns = array(
			'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
			'icon' => '',
			'ID_parent' => esc_html( _x( 'ID/Parent', 'list_table_column', 'media-library-assistant' ) ),
			'title_name' => esc_html( _x( 'Title/Name', 'list_table_column', 'media-library-assistant' ) ),
			'post_title' => esc_html( _x( 'Title', 'list_table_column', 'media-library-assistant' ) ),
			'post_name' => esc_html( _x( 'Name', 'list_table_column', 'media-library-assistant' ) ),
			'parent' => esc_html( _x( 'Parent ID', 'list_table_column', 'media-library-assistant' ) ),
			'menu_order' => esc_html( _x( 'Menu Order', 'list_table_column', 'media-library-assistant' ) ),
			'featured' => esc_html( _x( 'Featured in', 'list_table_column', 'media-library-assistant' ) ),
			'inserted' => esc_html( _x( 'Inserted in', 'list_table_column', 'media-library-assistant' ) ),
			'galleries' => esc_html( _x( 'Gallery in', 'list_table_column', 'media-library-assistant' ) ),
			'mla_galleries' => esc_html( _x( 'MLA Gallery in', 'list_table_column', 'media-library-assistant' ) ),
			'alt_text' => esc_html( _x( 'ALT Text', 'list_table_column', 'media-library-assistant' ) ),
			'caption' => esc_html( _x( 'Caption', 'list_table_column', 'media-library-assistant' ) ),
			'description' => esc_html( _x( 'Description', 'list_table_column', 'media-library-assistant' ) ),
			'post_mime_type' => esc_html( _x( 'MIME Type', 'list_table_column', 'media-library-assistant' ) ),
			'file_url' => esc_html( _x( 'File URL', 'list_table_column', 'media-library-assistant' ) ),
			'base_file' => esc_html( _x( 'Base File', 'list_table_column', 'media-library-assistant' ) ),
			'date' => esc_html( _x( 'Date', 'list_table_column', 'media-library-assistant' ) ),
			'modified' => esc_html( _x( 'Last Modified', 'list_table_column', 'media-library-assistant' ) ),
			'author' => esc_html( _x( 'Author', 'list_table_column', 'media-library-assistant' ) ),
			'attached_to' => esc_html( _x( 'Attached to', 'list_table_column', 'media-library-assistant' ) ),
			// taxonomy and custom field columns added below
		);
	}

	/*
	 * The $default_columns, $default_hidden_columns, and $default_sortable_columns
	 * arrays define the "Media/Assistant" table columns.
	 */

	/**
	 * Table column definitions
	 *
	 * This array defines table columns and titles where the key is the column slug (and class)
	 * and the value is the column's title text. If you need a checkbox for bulk actions,
	 * use the special slug "cb".
	 * 
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a column_cb() method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * All of the columns are added to this array by MLA_List_Table::mla_admin_init_action.
	 *
	 * @since 0.1
	 *
	 * @var	array
	 */
	public static $default_columns = array();

	/**
	 * Default values for hidden columns
	 *
	 * This array is used when the user-level option is not set, i.e.,
	 * the user has not altered the selection of hidden columns.
	 *
	 * The value on the right-hand side must match the column slug, e.g.,
	 * array(0 => 'ID_parent, 1 => 'title_name').
	 *
	 * Taxonomy and custom field columns are added to this array by
	 * MLA_List_Table::mla_admin_init_action.
	 * 
	 * @since 0.1
	 *
	 * @var	array
	 */
	public static $default_hidden_columns	= array(
		// 'ID_parent',
		// 'title_name',
		'post_title',
		'post_name',
		'parent',
		'menu_order',
		'featured',
		'inserted',
		'galleries',
		'mla_galleries',
		// 'alt_text',
		// 'caption',
		// 'description',
		'post_mime_type',
		'file_url',
		'base_file',
		// 'date',
		'modified',
		'author',
		// 'attached_to',
		// taxonomy columns added by mla_admin_init_action
		// custom field columns added by mla_admin_init_action
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
	 * Taxonomy and custom field columns are added to this array by
	 * MLA_List_Table::mla_admin_init_action.
	 *
	 * @since 0.1
	 *
	 * @var	array
	 */
	public static $default_sortable_columns = array(
		'ID_parent' => array('ID',true),
		'title_name' => array('title_name',false),
		'post_title' => array('post_title',false),
		'post_name' => array('post_name',false),
		'parent' => array('post_parent',false),
		'menu_order' => array('menu_order',false),
		// 'featured'   => array('featured',false),
		// 'inserted' => array('inserted',false),
		// 'galleries' => array('galleries',false),
		// 'mla_galleries' => array('mla_galleries',false),
		'alt_text' => array('_wp_attachment_image_alt',true),
		'caption' => array('post_excerpt',false),
		'description' => array('post_content',false),
		'post_mime_type' => array('post_mime_type',false),
		'file_url' => array('guid',false),
		'base_file' => array('_wp_attached_file',false),
		'date' => array('post_date',true),
		'modified' => array('post_modified',true),
		'author' => array('post_author',false),
		'attached_to' => array('post_parent',false),
		// sortable taxonomy columns, if any, added by mla_admin_init_action
		// sortable custom field columns, if any, added by mla_admin_init_action
        );

	/**
	 * Return the names and display values of the sortable columns
	 *
	 * @since 0.30
	 *
	 * @return	array	name => array( orderby value, heading ) for sortable columns
	 */
	public static function mla_get_sortable_columns( ) {
		$results = array() ;

		foreach ( self::$default_sortable_columns as $key => $value ) {
			$value[1] = self::$default_columns[ $key ];
			$results[ $key ] = $value;
		}

		return $results;
	}

	/**
	 * Cache the results of mla_count_list_table_items for reuse in mla_query_list_table_items
	 *
	 * @since 1.40
	 *
	 * @var	array
	 */
	private static $mla_list_table_items = NULL;

	/**
	 * Get the total number of attachment posts
	 *
	 * @since 0.30
	 *
	 * @param	array	Query variables, e.g., from $_REQUEST
	 * @param	int		(optional) number of rows to skip over to reach desired page
	 * @param	int		(optional) number of rows on each page
	 *
	 * @return	integer	Number of attachment posts
	 */
	public static function mla_count_list_table_items( $request, $offset = NULL, $count = NULL ) {
		if ( NULL !== $offset && NULL !== $count ) {
			$request = self::_prepare_list_table_query( $request, $offset, $count );
			$request = apply_filters( 'mla_list_table_query_final_terms', $request );

			self::$mla_list_table_items = apply_filters( 'mla_list_table_query_custom_items', NULL, $request );
			if ( is_null( self::$mla_list_table_items ) ) {
				self::$mla_list_table_items = self::_execute_list_table_query( $request );
			}

			return self::$mla_list_table_items->found_posts;
		}

		$request = self::_prepare_list_table_query( $request );
		$request = apply_filters( 'mla_list_table_query_final_terms', $request );

		$results = apply_filters( 'mla_list_table_query_custom_items', NULL, $request );
		if ( is_null( $results ) ) {
			$results = self::_execute_list_table_query( $request );
		}

		self::$mla_list_table_items = NULL;

		return $results->found_posts;
	}

	/**
	 * Retrieve attachment objects for list table display
	 *
	 * Supports prepare_items in class-mla-list-table.php.
	 * Modeled after wp_edit_attachments_query in wp-admin/post.php
	 *
	 * @since 0.1
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		number of rows to skip over to reach desired page
	 * @param	int		number of rows on each page
	 *
	 * @return	array	attachment objects (posts) including parent data, meta data and references
	 */
	public static function mla_query_list_table_items( $request, $offset, $count ) {
		if ( NULL == self::$mla_list_table_items ) {
			$request = self::_prepare_list_table_query( $request, $offset, $count );
			$request = apply_filters( 'mla_list_table_query_final_terms', $request );

			self::$mla_list_table_items = apply_filters( 'mla_list_table_query_custom_items', NULL, $request );
			if ( is_null( self::$mla_list_table_items ) ) {
				self::$mla_list_table_items = self::_execute_list_table_query( $request );
			}
		}

		$attachments = self::$mla_list_table_items->posts;
		foreach ( $attachments as $index => $attachment ) {
			/*
			 * Add parent data
			 */
			$parent_data = self::mla_fetch_attachment_parent_data( $attachment->post_parent );
			foreach ( $parent_data as $parent_key => $parent_value ) {
				$attachments[ $index ]->$parent_key = $parent_value;
			}

			/*
			 * Add meta data
			 */
			$meta_data = self::mla_fetch_attachment_metadata( $attachment->ID );
			foreach ( $meta_data as $meta_key => $meta_value ) {
				$attachments[ $index ]->$meta_key = $meta_value;
			}
		}

		/*
		 * Add references
		 */
		self::mla_attachment_array_fetch_references( $attachments );

		return $attachments;
	}

	/**
	 * Retrieve attachment objects for the WordPress Media Manager
	 *
	 * Supports month-year and taxonomy-term filters as well as the enhanced search box
	 *
	 * @since 1.20
	 *
	 * @param	array	query parameters from Media Manager
	 * @param	int		number of rows to skip over to reach desired page
	 * @param	int		number of rows on each page
	 *
	 * @return	object	WP_Query object with query results
	 */
	public static function mla_query_media_modal_items( $request, $offset, $count ) {
		$request = self::_prepare_list_table_query( $request, $offset, $count );
		$request = apply_filters( 'mla_media_modal_query_final_terms', $request );

		$results = apply_filters( 'mla_media_modal_query_custom_items', NULL, $request );
		return is_null( $results ) ? self::_execute_list_table_query( $request ) : $results;
	}

	/**
	 * Returns information about an attachment's parent, if found
	 *
	 * @since 0.1
	 *
	 * @param	int		post ID of attachment's parent, if any
	 *
	 * @return	array	Parent information; post_date, post_title and post_type
	 */
	public static function mla_fetch_attachment_parent_data( $parent_id ) {
		static $save_id = -1, $parent_data;

		if ( $save_id == $parent_id ) {
			return $parent_data;
		} elseif ( $parent_id == -1 ) {
			$save_id = -1;
			return NULL;
		}

		$parent_data = array();
		if ( $parent_id ) {
			$parent = get_post( $parent_id );

			if ( isset( $parent->post_name ) ) {
				$parent_data['parent_name'] = $parent->post_name;
			}

			if ( isset( $parent->post_type ) ) {
				$parent_data['parent_type'] = $parent->post_type;
			}

			if ( isset( $parent->post_title ) ) {
				$parent_data['parent_title'] = $parent->post_title;
			}

			if ( isset( $parent->post_date ) ) {
				$parent_data['parent_date'] = $parent->post_date;
			}

			if ( isset( $parent->post_status ) ) {
				$parent_data['parent_status'] = $parent->post_status;
			}
		}

		$save_id = $parent_id;
		return $parent_data;
	}

	/**
	 * Fetch and filter meta data for an attachment
	 * 
	 * Returns a filtered array of a post's meta data. Internal values beginning with '_'
	 * are stripped out or converted to an 'mla_' equivalent. 
	 *
	 * @since 0.1
	 *
	 * @param	int		post ID of attachment
	 *
	 * @return	array	Meta data variables
	 */
	public static function mla_fetch_attachment_metadata( $post_id ) {
		static $save_id = -1, $results;

		if ( $save_id == $post_id ) {
			return $results;
		} elseif ( $post_id == -1 ) {
			$save_id = -1;
			return NULL;
		}

		$attached_file = NULL;
		$results = array();
		$post_meta = get_metadata( 'post', $post_id );
		if ( is_array( $post_meta ) ) {
			foreach ( $post_meta as $post_meta_key => $post_meta_value ) {
				if ( empty( $post_meta_key ) ) {
					continue;
				}

				if ( '_' == $post_meta_key{0} ) {
					if ( stripos( $post_meta_key, '_wp_attached_file' ) === 0 ) {
						$key = 'mla_wp_attached_file';
						$attached_file = $post_meta_value[0];
					} elseif ( stripos( $post_meta_key, '_wp_attachment_metadata' ) === 0 ) {
						$key = 'mla_wp_attachment_metadata';
					} elseif ( stripos( $post_meta_key, '_wp_attachment_image_alt' ) === 0 ) {
						$key = 'mla_wp_attachment_image_alt';
					} else {
						continue;
					}
				} else {
					if ( stripos( $post_meta_key, 'mla_' ) === 0 ) {
						$key = $post_meta_key;
					} else {
						$key = 'mla_item_' . $post_meta_key;
					}
				}

				/*
				 * At this point, every value is an array; one element per instance of the key.
				 * We'll test anyway, just to be sure, then convert single-instance values to a scalar.
				 * Metadata array values are serialized for storage in the database.
				 */
				if ( is_array( $post_meta_value ) ) {
					if ( count( $post_meta_value ) == 1 ) {
						$post_meta_value = maybe_unserialize( $post_meta_value[0] );
					} else {
						foreach ( $post_meta_value as $single_key => $single_value ) {
							$post_meta_value[ $single_key ] = maybe_unserialize( $single_value );
						}
					}
				}

				$results[ $key ] = $post_meta_value;
			} // foreach $post_meta

			if ( ! empty( $attached_file ) ) {
				$last_slash = strrpos( $attached_file, '/' );
				if ( false === $last_slash ) {
					$results['mla_wp_attached_path'] = '';
					$results['mla_wp_attached_filename'] = $attached_file;
				} else {
					$results['mla_wp_attached_path'] = substr( $attached_file, 0, $last_slash + 1 );
					$results['mla_wp_attached_filename'] = substr( $attached_file, $last_slash + 1 );
				}
			} // $attached_file
		} // is_array($post_meta)

		$save_id = $post_id;
		return $results;
	}

	/**
	 * WP_Query filter "parameters"
	 *
	 * This array defines parameters for the query's join, where and orderby filters.
	 * The parameters are set up in the _prepare_list_table_query function, and
	 * any further logic required to translate those values is contained in the filters.
	 *
	 * Array index values are: use_alt_text_view, use_postmeta_view, use_orderby_view,
	 * alt_text_value, postmeta_key, postmeta_value, patterns, detached,
	 * orderby, order, mla-metavalue, debug (also in search_parameters)
	 *
	 * @since 0.30
	 *
	 * @var	array
	 */
	public static $query_parameters = array();

	/**
	 * WP_Query 'posts_search' filter "parameters"
	 *
	 * This array defines parameters for the query's posts_search filter, which uses
	 * 'search_string' to add a clause to the query's WHERE clause. It is shared between
	 * the list_table-query functions here and the mla_get_shortcode_attachments function
	 * in class-mla-shortcodes.php. This array passes the relevant parameters to the filter.
	 *
	 * Array index values are:
	 * ['mla_terms_search']['phrases']
	 * ['mla_terms_search']['taxonomies']
	 * ['mla_terms_search']['radio_phrases'] => AND/OR
	 * ['mla_terms_search']['radio_terms'] => AND/OR
	 * ['s'] => numeric for ID/parent search
	 * ['mla_search_fields'] => 'content', 'title', 'excerpt', 'alt-text', 'name', 'terms'
	 * Note: 'alt-text' is not supported in [mla_gallery]
	 * ['mla_search_connector'] => AND/OR
	 * ['sentence'] => entire string must match as one "keyword"
	 * ['exact'] => entire string must match entire field value
	 * ['debug'] => internal element, console/log/shortcode/none
	 * ['tax_terms_count'] => internal element, shared with JOIN and GROUP BY filters
	 *
	 * @since 2.00
	 *
	 * @var	array
	 */
	public static $search_parameters = array();

	/**
	 * Fetch custom field option value given a slug
 	 *
	 * @since 1.10
	 *
	 * @param	string	slug, e.g., 'c_File Size' for the 'File Size' field
	 *
	 * @return	array	option value, e.g., array( 'name' => 'File Size', ... )
	 */
	public static function mla_custom_field_option_value( $slug ) {
		$option_values = MLACore::mla_get_option( 'custom_field_mapping' );

		foreach ( $option_values as $key => $value ) {
			if ( $slug == 'c_' . $value['name'] ) {
				return $value;
			}
		}

		return array();
	} // mla_custom_field_option_value

	/**
	 * Sanitize and expand query arguments from request variables
	 *
	 * Prepare the arguments for WP_Query.
	 * Modeled after wp_edit_attachments_query in wp-admin/post.php
	 *
	 * @since 0.1
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		Optional number of rows (default 0) to skip over to reach desired page
	 * @param	int		Optional number of rows on each page (0 = all rows, default)
	 *
	 * @return	array	revised arguments suitable for WP_Query
	 */
	private static function _prepare_list_table_query( $raw_request, $offset = 0, $count = 0 ) {
		/*
		 * Go through the $raw_request, take only the arguments that are used in the query and
		 * sanitize or validate them.
		 */
		if ( ! is_array( $raw_request ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			error_log( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLAQuery::_prepare_list_table_query', var_export( $raw_request, true ) ), 0 );
			return NULL;
		}

		/*
		 * Make sure the current orderby choice still exists or revert to default.
		 */
		$default_orderby = array_merge( array( 'none' => array('none',false) ), self::mla_get_sortable_columns( ) );
		$current_orderby = MLACore::mla_get_option( MLACoreOptions::MLA_DEFAULT_ORDERBY );
		$found_current = false;
		foreach ( $default_orderby as $key => $value ) {
			if ( $current_orderby == $value[0] ) {
				$found_current = true;
				break;
			}
		}

		if ( $found_current ) {
			/*
			 * Custom fields can have HTML reserved characters, which are encoded by
			 * mla_get_sortable_columns, so a separate, unencoded list is required.
			 */
			$default_orderby = MLACore::mla_custom_field_support( 'custom_sortable_columns' );
			foreach ( $default_orderby as $sort_key => $sort_value ) {
				if ( $current_orderby == $sort_key ) {
					$current_orderby = 'c_' . $sort_value[0];
					break;
				}
			} // foreach
		} else {
			MLACore::mla_delete_option( MLACoreOptions::MLA_DEFAULT_ORDERBY );
			$current_orderby = MLACore::mla_get_option( MLACoreOptions::MLA_DEFAULT_ORDERBY );
		}

		$clean_request = array (
			'm' => 0,
			'orderby' => $current_orderby,
			'order' => MLACore::mla_get_option( MLACoreOptions::MLA_DEFAULT_ORDER ),
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'mla_search_connector' => 'AND',
			'mla_search_fields' => array()
		);

		foreach ( $raw_request as $key => $value ) {
			switch ( $key ) {
				/*
				 * 'sentence' and 'exact' modify the keyword search ('s')
				 * Their value is not important, only their presence.
				 */
				case 'sentence':
				case 'exact':
				case 'mla-tax':
				case 'mla-term':
					$clean_request[ $key ] = sanitize_key( $value );
					break;
				case 'orderby':
					if ( in_array( $value, array( 'none', 'post__in' ) ) ) {
						$clean_request[ $key ] = $value;
					} else {
						$orderby = NULL;
						/*
						 * Custom fields can have HTML reserved characters, which are encoded by
						 * mla_get_sortable_columns, so a separate, unencoded list is required.
						 */
						$sortable_columns = MLACore::mla_custom_field_support( 'custom_sortable_columns' );
						foreach ($sortable_columns as $sort_key => $sort_value ) {
							if ( $value == $sort_key ) {
								$orderby = 'c_' . $sort_value[0];
								break;
							}
						} // foreach

						if ( NULL === $orderby ) {
							$sortable_columns = MLAQuery::mla_get_sortable_columns();
							foreach ($sortable_columns as $sort_key => $sort_value ) {
								if ( $value == $sort_value[0] ) {
									$orderby = $value;
									break;
								}
							} // foreach
						}

						if ( NULL !== $orderby ) {
							$clean_request[ $key ] = $orderby;
						}
					}

					break;
				/*
				 * ids allows hooks to supply a persistent list of items
				 */
				case 'ids':
					if ( is_array( $value ) ) {
						$clean_request[ 'post__in' ] = $value;
					} else {
						$clean_request[ 'post__in' ] = array_map( 'absint', explode( ',', $value ) );
					}
					break;
				/*
				 * post__in and post__not_in are used in the Media Modal Ajax queries
				 */
				case 'post__in':
				case 'post__not_in':
				case 'post_mime_type':
					$clean_request[ $key ] = $value;
					break;
				case 'parent':
				case 'post_parent':
					$clean_request[ 'post_parent' ] = absint( $value );
					break;
				/*
				 * ['m'] - filter by year and month of post, e.g., 201204
				 */
				case 'author':
				case 'm':
					$clean_request[ $key ] = absint( $value );
					break;
				/*
				 * ['mla_filter_term'] - filter by category or tag ID; -1 allowed
				 */
				case 'mla_filter_term':
					$clean_request[ $key ] = intval( $value );
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
				case 'detached':
					if ( ( '0' == $value ) || ( '1' == $value ) ) {
						$clean_request['detached'] = $value;
					}

					break;
				case 'status':
					if ( 'trash' == $value ) {
						$clean_request['post_status'] = 'trash';
					}

					break;
				/*
				 * ['s'] - Search Media by one or more keywords
				 * ['mla_search_connector'], ['mla_search_fields'] - Search Media options
				 */
				case 's':
					switch ( substr( $value, 0, 3 ) ) {
						case '>|<':
							$clean_request['debug'] = 'console';
							break;
						case '<|>':
							$clean_request['debug'] = 'log';
							break;
					}

					if ( isset( $clean_request['debug'] ) ) {
						$value = substr( $value, 3 );
					}

					$value = stripslashes( trim( $value ) );

					if ( ! empty( $value ) ) {
						$clean_request[ $key ] = $value;
					}

					break;
				case 'mla_terms_search':
					if ( ! empty( $value['phrases'] ) && ! empty( $value['taxonomies'] ) ) {
						$value['phrases'] = stripslashes( trim( $value['phrases'] ) );
						if ( ! empty( $value['phrases'] ) ) {
							$clean_request[ $key ] = $value;
						}
					}
					break;
				case 'mla_search_connector':
				case 'mla_search_fields':
					$clean_request[ $key ] = $value;
					break;
				case 'mla-metakey':
				case 'mla-metavalue':
					$clean_request[ $key ] = stripslashes( $value );
					break;
				case 'meta_query':
					if ( ! empty( $value ) ) {
						if ( is_array( $value ) ) {
							$clean_request[ $key ] = $value;
						} else {
							$clean_request[ $key ] = unserialize( stripslashes( $value ) );
							unset( $clean_request[ $key ]['slug'] );
						} // not array
					}

					break;
				default:
					// ignore anything else in $_REQUEST
			} // switch $key
		} // foreach $raw_request

		/*
		 * Pass query and search parameters to the filters for _execute_list_table_query
		 */
		self::$query_parameters = array( 'use_alt_text_subquery' => false, 'use_postmeta_subquery' => false, 'use_orderby_subquery' => false, 'orderby' => $clean_request['orderby'], 'order' => $clean_request['order'] );
		self::$query_parameters['detached'] = isset( $clean_request['detached'] ) ? $clean_request['detached'] : NULL;
		self::$search_parameters = array( 'debug' => 'none' );

		/*
		 * Matching a meta_value to NULL requires a LEFT JOIN to a view and a special WHERE clause
		 * Matching a wildcard pattern requires mainpulating the WHERE clause, too
		 */
		if ( isset( $clean_request['meta_query']['key'] ) ) {
			self::$query_parameters['use_postmeta_subquery'] = true;
			self::$query_parameters['postmeta_key'] = $clean_request['meta_query']['key'];
			self::$query_parameters['postmeta_value'] = NULL;
			unset( $clean_request['meta_query'] );
		} elseif ( isset( $clean_request['meta_query']['patterns'] ) ) {
			self::$query_parameters['patterns'] = $clean_request['meta_query']['patterns'];
			unset( $clean_request['meta_query']['patterns'] );
		}

		if ( isset( $clean_request['debug'] ) ) {
			self::$query_parameters['debug'] = $clean_request['debug'];
			self::$search_parameters['debug'] = $clean_request['debug'];
			MLACore::mla_debug_mode( $clean_request['debug'] );
			unset( $clean_request['debug'] );
		}

		/*
		 * We must patch the WHERE clause if there are leading spaces in the meta_value
		 */
		if ( isset( $clean_request['mla-metavalue'] ) && ( 0 < strlen( $clean_request['mla-metavalue'] ) ) && ( ' ' == $clean_request['mla-metavalue'][0] ) ) {
			self::$query_parameters['mla-metavalue'] = $clean_request['mla-metavalue'];
		}

		/*
		 * We will handle "Terms Search" in the mla_query_posts_search_filter.
		 */
		if ( isset( $clean_request['mla_terms_search'] ) ) {
			self::$search_parameters['mla_terms_search'] = $clean_request['mla_terms_search'];

			/*
			 * The Terms Search overrides any terms-based keyword search for now; too complicated.
			 */
			if ( isset( $clean_request['mla_search_fields'] ) ) {
				foreach ( $clean_request['mla_search_fields'] as $index => $field ) {
					if ( 'terms' == $field ) {
						unset ( $clean_request['mla_search_fields'][ $index ] );
					}
				}
			}
		}

		/*
		 * We will handle keyword search in the mla_query_posts_search_filter.
		 */
		if ( isset( $clean_request['s'] ) ) {
			self::$search_parameters['s'] = $clean_request['s'];
			self::$search_parameters['mla_search_fields'] = apply_filters( 'mla_list_table_search_filter_fields', $clean_request['mla_search_fields'], array( 'content', 'title', 'excerpt', 'alt-text', 'name', 'terms' ) );
			self::$search_parameters['mla_search_connector'] = $clean_request['mla_search_connector'];
			self::$search_parameters['sentence'] = isset( $clean_request['sentence'] );
			self::$search_parameters['exact'] = isset( $clean_request['exact'] );

			if ( in_array( 'alt-text', self::$search_parameters['mla_search_fields'] ) ) {
				self::$query_parameters['use_alt_text_subquery'] = true;
			}

			if ( in_array( 'terms', self::$search_parameters['mla_search_fields'] ) ) {
				self::$search_parameters['mla_search_taxonomies'] = MLACore::mla_supported_taxonomies( 'term-search' );
			}

			unset( $clean_request['s'] );
			unset( $clean_request['mla_search_connector'] );
			unset( $clean_request['mla_search_fields'] );
			unset( $clean_request['sentence'] );
			unset( $clean_request['exact'] );
		}

		/*
		 * We have to handle custom field/post_meta values here
		 * because they need a JOIN clause supplied by WP_Query
		 */
		if ( 'c_' == substr( $clean_request['orderby'], 0, 2 ) ) {
			$option_value = MLAQuery::mla_custom_field_option_value( $clean_request['orderby'] );
			if ( isset( $option_value['name'] ) ) {
				self::$query_parameters['use_orderby_subquery'] = true;
				self::$query_parameters['orderby_key'] = $option_value['name'];

				if ( isset($clean_request['orderby']) ) {
					unset($clean_request['orderby']);
				}

				if ( isset($clean_request['order']) ) {
					unset($clean_request['order']);
				}
			}
		} else { // custom field
			switch ( self::$query_parameters['orderby'] ) {
				/*
				 * '_wp_attachment_image_alt' is special; it can have NULL values,
				 * so we'll handle it in the JOIN and ORDERBY filters
				 */
				case '_wp_attachment_image_alt':
					self::$query_parameters['use_orderby_subquery'] = true;
					self::$query_parameters['orderby_key'] = '_wp_attachment_image_alt';

					if ( isset($clean_request['orderby']) ) {
						unset($clean_request['orderby']);
					}

					if ( isset($clean_request['order']) ) {
						unset($clean_request['order']);
					}

					break;
				case '_wp_attached_file':
					$clean_request['meta_key'] = '_wp_attached_file';
					$clean_request['orderby'] = 'meta_value';
					$clean_request['order'] = self::$query_parameters['order'];
					break;
			} // switch $orderby
		}

		/*
		 * Ignore incoming paged value; use offset and count instead
		 */
		if ( ( (int) $count ) > 0 ) {
			$clean_request['offset'] = $offset;
			$clean_request['posts_per_page'] = $count;
		} elseif ( ( (int) $count ) == -1 ) {
			$clean_request['posts_per_page'] = $count;
		}

		/*
		 * ['mla_filter_term'] - filter by taxonomy
		 *
		 * cat =  0 is "All Categories", i.e., no filtering
		 * cat = -1 is "No Categories"
		 */
		if ( isset( $clean_request['mla_filter_term'] ) ) {
			if ( $clean_request['mla_filter_term'] != 0 ) {
				$tax_filter = MLACore::mla_taxonomy_support('', 'filter');
				if ( $clean_request['mla_filter_term'] == -1 ) {
					$term_list = get_terms( $tax_filter, array(
						'fields' => 'ids',
						'hide_empty' => false
					) );
					$clean_request['tax_query'] = array(
						array(
							'taxonomy' => $tax_filter,
							'field' => 'id',
							'terms' => $term_list,
							'operator' => 'NOT IN' 
						) 
					);
				} else { // mla_filter_term == -1
					$clean_request['tax_query'] = array(
						array(
							'taxonomy' => $tax_filter,
							'field' => 'id',
							'terms' => array(
								(int) $clean_request['mla_filter_term']
							),
							'include_children' => ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_TAXONOMY_FILTER_INCLUDE_CHILDREN ) )
						) 
					);
				} // mla_filter_term != -1
			} // mla_filter_term != 0

			unset( $clean_request['mla_filter_term'] );
		} // isset mla_filter_term

		if ( isset( $clean_request['mla-tax'] ) && isset( $clean_request['mla-term'] )) {
			$clean_request['tax_query'] = array(
				array(
					'taxonomy' => $clean_request['mla-tax'],
					'field' => 'slug',
					'terms' => $clean_request['mla-term'],
					'include_children' => false 
				) 
			);

			unset( $clean_request['mla-tax'] );
			unset( $clean_request['mla-term'] );
		} // isset mla_tax

		if ( isset( $clean_request['mla-metakey'] ) && isset( $clean_request['mla-metavalue'] ) ) {
			$clean_request['meta_key'] = $clean_request['mla-metakey'];
			$clean_request['meta_value'] = $clean_request['mla-metavalue'];

			unset( $clean_request['mla-metakey'] );
			unset( $clean_request['mla-metavalue'] );
		} // isset mla_tax

		return $clean_request;
	}

	/**
	 * Add filters, run query, remove filters
	 *
	 * @since 0.30
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 *
	 * @return	object	WP_Query object with query results
	 */
	private static function _execute_list_table_query( $request ) {
		global $wpdb;
		static $wpmf_pre_get_posts_priority = false, $wpmf_pre_get_posts1_priority = false;

		add_filter( 'posts_search', 'MLAQuery::mla_query_posts_search_filter', 10, 2 ); // $search, &$this
		add_filter( 'posts_where', 'MLAQuery::mla_query_posts_where_filter' );
		add_filter( 'posts_join', 'MLAQuery::mla_query_posts_join_filter' );
		add_filter( 'posts_groupby', 'MLAQuery::mla_query_posts_groupby_filter' );
		add_filter( 'posts_orderby', 'MLAQuery::mla_query_posts_orderby_filter' );

		/*
		 * Disable Relevanssi - A Better Search, v3.2 by Mikko Saari 
		 * relevanssi_prevent_default_request( $request, $query )
		 * apply_filters('relevanssi_admin_search_ok', $admin_search_ok, $query );
		 */
		if ( function_exists( 'relevanssi_prevent_default_request' ) ) {
			add_filter( 'relevanssi_admin_search_ok', 'MLAQuery::mla_query_relevanssi_admin_search_ok_filter' );
		}

		/*
		 * Remove WP Media Folders actions from MLA queries for the Media/Assistant submenu table
		 */
		if ( isset( $GLOBALS['wp_media_folder'] ) && isset( $_REQUEST['page'] ) && ( MLACore::ADMIN_PAGE_SLUG == $_REQUEST['page'] ) ) {
			$wpmf_pre_get_posts_priority = has_filter( 'pre_get_posts', array( $GLOBALS['wp_media_folder'], 'wpmf_pre_get_posts' ) );
			$wpmf_pre_get_posts1_priority = has_filter( 'pre_get_posts', array( $GLOBALS['wp_media_folder'], 'wpmf_pre_get_posts1' ) );
		}

		if ( false !== $wpmf_pre_get_posts_priority ) {
			remove_action( 'pre_get_posts', array( $GLOBALS['wp_media_folder'], 'wpmf_pre_get_posts' ), $wpmf_pre_get_posts_priority );
		}
		
		if ( false !== $wpmf_pre_get_posts1_priority ) {
			remove_action( 'pre_get_posts', array( $GLOBALS['wp_media_folder'], 'wpmf_pre_get_posts1' ), $wpmf_pre_get_posts1_priority );
		}
		
		if ( isset( self::$query_parameters['debug'] ) ) {
			global $wp_filter;
			$debug_array = array( 'posts_search' => $wp_filter['posts_search'], 'posts_join' => $wp_filter['posts_join'], 'posts_where' => $wp_filter['posts_where'], 'posts_orderby' => $wp_filter['posts_orderby'] );

			/* translators: 1: DEBUG tag 2: query filter details */
			MLACore::mla_debug_add( sprintf( _x( '%1$s: _execute_list_table_query $wp_filter = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $debug_array, true ) ) );

			add_filter( 'posts_clauses', 'MLAQuery::mla_query_posts_clauses_filter', 0x7FFFFFFF, 1 );
			add_filter( 'posts_clauses_request', 'MLAQuery::mla_query_posts_clauses_request_filter', 0x7FFFFFFF, 1 );
		} // debug

		$results = new WP_Query( $request );

		if ( isset( self::$query_parameters['debug'] ) ) {
			remove_filter( 'posts_clauses', 'MLAQuery::mla_query_posts_clauses_filter', 0x7FFFFFFF );
			remove_filter( 'posts_clauses_request', 'MLAQuery::mla_query_posts_clauses_request_filter', 0x7FFFFFFF );

			$debug_array = array( 'request' => $request, 'query_parameters' => self::$query_parameters, 'post_count' => $results->post_count, 'found_posts' => $results->found_posts );

			/* translators: 1: DEBUG tag 2: query details */
			MLACore::mla_debug_add( sprintf( _x( '%1$s: _execute_list_table_query WP_Query = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $debug_array, true ) ) );
			/* translators: 1: DEBUG tag 2: SQL statement */
			MLACore::mla_debug_add( sprintf( _x( '%1$s: _execute_list_table_query SQL_request = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $results->request, true ) ) );
		} // debug


		if ( false !== $wpmf_pre_get_posts1_priority ) {
			add_action( 'pre_get_posts', array( $GLOBALS['wp_media_folder'], 'wpmf_pre_get_posts1' ), $wpmf_pre_get_posts1_priority );
		}
		
		if ( false !== $wpmf_pre_get_posts_priority ) {
			add_action( 'pre_get_posts', array( $GLOBALS['wp_media_folder'], 'wpmf_pre_get_posts' ), $wpmf_pre_get_posts_priority );
		}
		
		if ( function_exists( 'relevanssi_prevent_default_request' ) ) {
			remove_filter( 'relevanssi_admin_search_ok', 'MLAQuery::mla_query_relevanssi_admin_search_ok_filter' );
		}

		remove_filter( 'posts_orderby', 'MLAQuery::mla_query_posts_orderby_filter' );
		remove_filter( 'posts_groupby', 'MLAQuery::mla_query_posts_groupby_filter' );
		remove_filter( 'posts_join', 'MLAQuery::mla_query_posts_join_filter' );
		remove_filter( 'posts_where', 'MLAQuery::mla_query_posts_where_filter' );
		remove_filter( 'posts_search', 'MLAQuery::mla_query_posts_search_filter' );

		return $results;
	}

	/**
	 * Detects wildcard searches, i.e., containing an asterisk outside quotes
	 * 
	 * Defined as public because it's a callback from array_map().
	 *
	 * @since 2.13
	 *
	 * @param	string	search string
	 *
	 * @return	boolean	true if wildcard
	 */
	private static function _wildcard_search_string( $search_string ) {
		preg_match_all('/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $search_string, $matches);

		if ( is_array( $matches ) ) {
			foreach ( $matches[0] as $term ) {
				if ( '"' == substr( $term, 0, 1) ) {
					continue;
				}

				if ( false !== strpos( $term, '*' ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Replaces a WordPress function deprecated in v3.7
	 * 
	 * Defined as public because it's a callback from array_map().
	 *
	 * @since 1.51
	 *
	 * @param	string	search term before modification
	 *
	 * @return	string	cleaned up search term
	 */
	public static function mla_search_terms_tidy( $term ) {
		return trim( $term, "\"'\n\r " );
	}

	/**
	 * Isolates keyword match results to word boundaries
	 * 
	 * Eliminates matches such as "man" in "woman".
	 *
	 * @since 2.11
	 *
	 * @param	string	the quoted phrase (without enclosing quotes)
	 * @param	string	the entire term
	 *
	 * @return	boolean	$needle is a word match within $haystack
	 */
	private static function _match_quoted_phrase( $needle, $haystack ) {
		$haystack = strtolower( html_entity_decode( $haystack ) );
		$needle = strtolower( html_entity_decode( $needle ) );

		// Escape the PCRE meta-characters
		$safe_needle = '';
		for ( $index = 0; $index < strlen( $needle ); $index++ ) {
			$chr = $needle[ $index ];
			if ( false !== strpos( '\\^$.[]()?*+{}/', $chr ) ) {
				$safe_needle .= '\\';
			}
			$safe_needle .= $chr;
		}

		$pattern = '/^' . $safe_needle . '$|^' . $safe_needle . '\s+|\s+' . $safe_needle . '\s+|\s+' . $safe_needle . '$/';
		$match_count = preg_match_all($pattern, $haystack, $matches);
		return 0 < $match_count;
	}

	/**
	 * Adds a keyword search to the WHERE clause, if required
	 * 
	 * Defined as public because it's a filter.
	 *
	 * @since 0.60
	 *
	 * @param	string	query clause before modification
	 * @param	object	WP_Query object
	 *
	 * @return	string	query clause after keyword search addition
	 */
	public static function mla_query_posts_search_filter( $search_string, &$query_object ) {
		global $wpdb;

		$numeric_clause = '';
		$search_clause = '';
		$tax_clause = '';
		$tax_connector = 'AND';
		$tax_index = 0;

		/*
		 * Process the Terms Search arguments, if present.
		 */
		if ( isset( self::$search_parameters['mla_terms_search']['phrases'] ) ) {
			$terms_search_parameters = self::$search_parameters['mla_terms_search'];
			$terms = array_map( 'trim', explode( ',', $terms_search_parameters['phrases'] ) );
			if ( 1 < count( $terms ) ) {
				$terms_connector = '(';			
			} else {
				$terms_connector = '';			
			}

			foreach ( $terms as $term ) {
				preg_match_all('/".*?("|$)|\'.*?(\'|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $term, $matches);
				$phrases = array_map('MLAQuery::mla_search_terms_tidy', $matches[0]);

				/*
				 * Find the quoted phrases for a word-boundary check
				 */
				$quoted = array();
				foreach ( $phrases as $index => $phrase ) {
					$quoted[ $index ] = ( '"' == $matches[1][$index] ) || ( "'" == $matches[2][$index] );
				}

				$tax_terms = array();
				$tax_counts = array();
				foreach ( $phrases as $index => $phrase ) {
					if ( isset( $terms_search_parameters['exact'] ) ) {
						$the_terms = array();
						foreach( $terms_search_parameters['taxonomies'] as $taxonomy ) {
							// WordPress encodes special characters, e.g., "&" as HTML entities in term names
							$the_term = get_term_by( 'name', _wp_specialchars( $phrase ), $taxonomy );
							if ( false !== $the_term ) {
								$the_terms[] = $the_term;
							}
						}
					} else {
						$is_wildcard_search = ( ! $quoted[ $index ] ) && self::_wildcard_search_string( $phrase );

						if ( $is_wildcard_search ) {
							add_filter( 'terms_clauses', 'MLAQuery::mla_query_terms_clauses_filter', 0x7FFFFFFF, 3 );
						}

						// WordPress encodes special characters, e.g., "&" as HTML entities in term names
						$the_terms = get_terms( $terms_search_parameters['taxonomies'], array( 'name__like' => _wp_specialchars( $phrase ), 'fields' => 'all', 'hide_empty' => false ) );

						if ( $is_wildcard_search ) {
							remove_filter( 'terms_clauses', 'MLAQuery::mla_query_terms_clauses_filter', 0x7FFFFFFF );
						}

						// Invalid taxonomy will return WP_Error object
						if ( ! is_array( $the_terms ) ) {
							$the_terms = array();
						}

						if ( $quoted[ $index ] ) {
							foreach ( $the_terms as $term_index => $the_term ) {
								if ( ! self::_match_quoted_phrase( $phrase, $the_term->name ) ) {
									unset( $the_terms[ $term_index ]);
								}
							}
						} // quoted phrase
					} // not exact

					foreach( $the_terms as $the_term ) {
						$tax_terms[ $the_term->taxonomy ][ $the_term->term_id ] = (integer) $the_term->term_taxonomy_id;

						if ( isset( $tax_counts[ $the_term->taxonomy ][ $the_term->term_id ] ) ) {
							$tax_counts[ $the_term->taxonomy ][ $the_term->term_id ]++;
						} else {
							$tax_counts[ $the_term->taxonomy ][ $the_term->term_id ] = 1;
						}
					}
				} // foreach phrase

				/*
				 * For the AND connector, a taxonomy term must have all of the search terms within it
				 */
				if ( 'AND' == $terms_search_parameters['radio_phrases'] ) {
					$search_term_count = count( $phrases );
					foreach ($tax_terms as $taxonomy => $term_ids ) {
						foreach ( $term_ids as $term_id => $term_taxonomy_id ) {
							if ( $search_term_count != $tax_counts[ $taxonomy ][ $term_id ] ) {
								unset( $term_ids[ $term_id ] );
							}
						}

						if ( empty( $term_ids ) ) {
							unset( $tax_terms[ $taxonomy ] );
						} else {
							$tax_terms[ $taxonomy ] = $term_ids;
						}
					} // foreach taxonomy
				} // AND (i.e., All phrases)

				if ( ! empty( $tax_terms ) ) {
					$inner_connector = '';

					$tax_clause .= $terms_connector;
					foreach( $tax_terms as $tax_term ) {
						if ( 'AND' == $terms_search_parameters['radio_terms'] ) {
							$prefix = 'mlatt' . $tax_index++;
						} else {
							$prefix = 'mlatt0';
							$tax_index = 1; // only one JOIN needed for the "Any Term" case
						}

						$tax_clause .= sprintf( '%1$s %2$s.term_taxonomy_id IN (%3$s)', $inner_connector, $prefix, implode( ',', $tax_term ) );
						$inner_connector = ' OR';
					} // foreach tax_term

					$terms_connector = ' ) ' . $terms_search_parameters['radio_terms'] . ' (';
				} // tax_terms present
			} // foreach term

			if ( 1 < count( $terms ) && ! empty( $tax_clause ) ) {
				$tax_clause .= ')';
			}

			if ( empty( $tax_clause ) ) {
				$tax_clause = '1=0';
			} else {
				self::$search_parameters['tax_terms_count'] = $tax_index;
			};
		} // isset mla_terms_search

		/*
		 * Process the keyword search argument, if present.
		 */
		if ( ! empty( self::$search_parameters['s'] ) ) {
			// WordPress v3.7 says: there are no line breaks in <input /> fields
			$keyword_string = stripslashes( str_replace( array( "\r", "\n" ), '', self::$search_parameters['s'] ) );
			$is_wildcard_search = self::_wildcard_search_string( $keyword_string );

			if ( $is_wildcard_search || self::$search_parameters['sentence'] || self::$search_parameters['exact'] ) {
				$keyword_array = array( $keyword_string );
			} else {
				// v3.6.1 was '/".*?("|$)|((?<=[\r\n\t ",+])|^)[^\r\n\t ",+]+/'
				preg_match_all('/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $keyword_string, $matches);
				$keyword_array = array_map( 'MLAQuery::mla_search_terms_tidy', $matches[0]);
				$numeric_array = array_filter( $keyword_array, 'is_numeric' );

				/*
				 * If all the "keywords" are numeric, interpret it/them as the ID(s) of a specific attachment
				 * or the ID(s) of a parent post/page; add it/them to the regular text-based search.
				 */
				if ( count( $keyword_array ) && count( $keyword_array ) == count( $numeric_array ) ) {
					$numeric_array = implode( ',', $numeric_array );
					$numeric_clause = '( ( ' . $wpdb->posts . '.ID IN (' . $numeric_array . ') ) OR ( ' . $wpdb->posts . '.post_parent IN (' . $numeric_array . ') ) ) OR ';

				}
			}

			$fields = self::$search_parameters['mla_search_fields'];
			$allow_terms_search = in_array( 'terms', $fields ) && ( ! $is_wildcard_search );
			$percent = self::$search_parameters['exact'] ? '' : '%';
			$connector = '';

			if ( empty( $fields ) ) {
				$search_clause = '1=0';
			} else {
				$tax_terms = array();
				$tax_counts = array();
				foreach ( $keyword_array as $term ) {
					if ( $is_wildcard_search ) {
						/*
						 * Escape any % in the source string
						 */
						if ( self::$wp_4dot0_plus ) {
							$sql_term = $wpdb->esc_like( $term );
							$sql_term = $wpdb->prepare( '%s', $sql_term );
						} else {
							$sql_term = "'" . esc_sql( like_escape( $term ) ) . "'";
						}

						/*
						 * Convert wildcard * to SQL %
						 */
						$sql_term = str_replace( '*', '%', $sql_term );
					} else {
						if ( self::$wp_4dot0_plus ) {
							$sql_term = $percent . $wpdb->esc_like( $term ) . $percent;
							$sql_term = $wpdb->prepare( '%s', $sql_term );
						} else {
							$sql_term = "'" . $percent . esc_sql( like_escape( $term ) ) . $percent . "'";
						}
					}

					$inner_connector = '';
					$inner_clause = '';

					if ( in_array( 'content', $fields ) ) {
						$inner_clause .= "{$inner_connector}({$wpdb->posts}.post_content LIKE {$sql_term})";
						$inner_connector = ' OR ';
					}

					if ( in_array( 'title', $fields ) ) {
						$inner_clause .= "{$inner_connector}({$wpdb->posts}.post_title LIKE {$sql_term})";
						$inner_connector = ' OR ';
					}

					if ( in_array( 'excerpt', $fields ) ) {
						$inner_clause .= "{$inner_connector}({$wpdb->posts}.post_excerpt LIKE {$sql_term})";
						$inner_connector = ' OR ';
					}

					if ( in_array( 'alt-text', $fields ) ) {
						$view_name = self::MLA_ALT_TEXT_SUBQUERY;
						$inner_clause .= "{$inner_connector}({$view_name}.meta_value LIKE {$sql_term})";
						$inner_connector = ' OR ';
					}

					if ( in_array( 'name', $fields ) ) {
						$inner_clause .= "{$inner_connector}({$wpdb->posts}.post_name LIKE {$sql_term})";
					}

					$inner_clause = apply_filters( 'mla_list_table_search_filter_inner_clause', $inner_clause, $inner_connector, $wpdb->posts, $sql_term );

					if ( ! empty($inner_clause) ) {
						$search_clause .= "{$connector}({$inner_clause})";
						$connector = ' ' . self::$search_parameters['mla_search_connector'] . ' ';
					}

					/*
					 * Convert search term text to term_taxonomy_id value(s),
					 * separated by taxonomy.
					 */
					if ( $allow_terms_search ) {
						// WordPress encodes special characters, e.g., "&" as HTML entities in term names
						$the_terms = get_terms( self::$search_parameters['mla_search_taxonomies'], array( 'name__like' => _wp_specialchars( $term ), 'fields' => 'all', 'hide_empty' => false ) );
						// Invalid taxonomy will return WP_Error object
						if ( ! is_array( $the_terms ) ) {
							$the_terms = array();
						}

						foreach( $the_terms as $the_term ) {
							$tax_terms[ $the_term->taxonomy ][ $the_term->term_id ] = (integer) $the_term->term_taxonomy_id;

							if ( isset( $tax_counts[ $the_term->taxonomy ][ $the_term->term_id ] ) ) {
								$tax_counts[ $the_term->taxonomy ][ $the_term->term_id ]++;
							} else {
								$tax_counts[ $the_term->taxonomy ][ $the_term->term_id ] = 1;
							}
						}
					} // in_array terms
				} // foreach term

				if ( $allow_terms_search ) {
					/*
					 * For the AND connector, a taxonomy term must have all of the search terms within it
					 */
					if ( 'AND' == self::$search_parameters['mla_search_connector'] ) {
						$search_term_count = count( $keyword_array );
						foreach ($tax_terms as $taxonomy => $term_ids ) {
							foreach ( $term_ids as $term_id => $term_taxonomy_id ) {
								if ( $search_term_count != $tax_counts[ $taxonomy ][ $term_id ] ) {
									unset( $term_ids[ $term_id ] );
								}
							}

							if ( empty( $term_ids ) ) {
								unset( $tax_terms[ $taxonomy ] );
							} else {
								$tax_terms[ $taxonomy ] = $term_ids;
							}
						} // foreach taxonomy
					} // AND connector

					if ( empty( $tax_terms ) ) {
						/*
						 * If "Terms" is the only field and no terms are present,
						 * the search must fail.
						 */
						if ( ( 1 == count( $fields ) ) && ( 'terms' == array_shift( $fields ) ) ) {
							$tax_clause = '1=0';
						}
					} else {
						$tax_index = 0;
						$inner_connector = '';

						foreach( $tax_terms as $tax_term ) {
							$prefix = 'mlatt' . $tax_index++;
							$tax_clause .= sprintf( '%1$s %2$s.term_taxonomy_id IN (%3$s)', $inner_connector, $prefix, implode( ',', $tax_term ) );
							$inner_connector = ' OR';
						} // foreach tax_term

						self::$search_parameters['tax_terms_count'] = $tax_index;
						$tax_connector = 'OR';
					} // tax_terms present
				} // terms in fields
			} // fields not empty
		} // isset 's'

		if ( ! empty( $tax_clause ) && ! empty( $search_clause ) ) {
			$tax_clause = " {$tax_connector} ({$tax_clause} )";
		}

		if ( ! empty( $search_clause ) || ! empty( $tax_clause ) ) {
			$search_clause = " AND ( {$numeric_clause}{$search_clause}{$tax_clause} ) ";

			if ( ! is_user_logged_in() ) {
				$search_clause .= " AND ( {$wpdb->posts}.post_password = '' ) ";
			}
		}

		if ( 'none' != self::$search_parameters['debug'] ) {
			$debug_array['search_string'] = $search_string;
			$debug_array['search_parameters'] = self::$search_parameters;
			$debug_array['search_clause'] = $search_clause;

			if ( 'shortcode' == self::$search_parameters['debug'] ) {
				MLACore::mla_debug_add( '<strong>mla_debug posts_search filter</strong> = ' . var_export( $debug_array, true ) );
			} else {
				/* translators: 1: DEBUG tag 2: search filter details */
				MLACore::mla_debug_add( sprintf( _x( '%1$s: mla_query_posts_search_filter = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $debug_array, true ) ) );
			}
		} // debug

		return $search_clause;
	}

	/**
	 * Adds/modifies the WHERE clause for meta values, LIKE patterns and detached items
	 * 
	 * Modeled after _edit_attachments_query_helper in wp-admin/post.php.
	 * Defined as public because it's a filter.
	 *
	 * @since 0.1
	 *
	 * @param	string	query clause before modification
	 *
	 * @return	string	query clause after modification
	 */
	public static function mla_query_posts_where_filter( $where_clause ) {
		global $wpdb;

		if ( isset( self::$query_parameters['debug'] ) ) {
			$debug_array = array( 'where_string' => $where_clause );
		}

		/*
		 * WordPress filters meta_value thru trim() - which we must reverse
		 */
		if ( isset( self::$query_parameters['mla-metavalue'] ) ) {
			if ( is_array( self::$query_parameters['mla-metavalue'] ) ) {
				foreach ( self::$query_parameters['mla-metavalue'] as $pattern => $replacement ) {
					$where_clause = preg_replace( '/(^.*meta_value AS CHAR\) = \')(' . $pattern . '[^\']*)/m', '${1}' . $replacement, $where_clause );
				}
			} else {
				$where_clause = preg_replace( '/(^.*meta_value AS CHAR\) = \')([^\']*)/m', '${1}' . self::$query_parameters['mla-metavalue'], $where_clause );
			}
		}

		/*
		 * Matching a NULL meta value 
		 */
		if ( array_key_exists( 'postmeta_value', self::$query_parameters ) && NULL == self::$query_parameters['postmeta_value'] ) {
			$where_clause .= ' AND ' . self::MLA_TABLE_VIEW_SUBQUERY . '.meta_value IS NULL';
		}

		/*
		 * WordPress modifies the LIKE clause - which we must reverse
		 */
		if ( isset( self::$query_parameters['patterns'] ) ) {
			foreach ( self::$query_parameters['patterns'] as $pattern ) {
				$pattern = str_replace( '_', '\\\\_', $pattern );
				$match_clause = '%' . str_replace( '%', '\\\\%', $pattern ) . '%';
				$where_clause = str_replace( "LIKE '{$match_clause}'", "LIKE '{$pattern}'", $where_clause );
			}
		}

		/*
		 * Unattached items require some help
		 */
		if ( isset( self::$query_parameters['detached'] ) ) {
			if ( '1' == self::$query_parameters['detached'] ) {
				$where_clause .= sprintf( ' AND %1$s.post_parent < 1', $wpdb->posts );
			} elseif ( '0' == self::$query_parameters['detached'] ) {
				$where_clause .= sprintf( ' AND %1$s.post_parent > 0', $wpdb->posts );
			}
		}

		if ( isset( self::$query_parameters['debug'] ) ) {
			$debug_array['where_clause'] = $where_clause;

			/* translators: 1: DEBUG tag 2: where filter details */
			MLACore::mla_debug_add( sprintf( _x( '%1$s: mla_query_posts_where_filter = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $debug_array, true ) ) );
		} // debug

		return $where_clause;
	}

	/**
	 * Adds a JOIN clause, if required, to handle sorting/searching on custom fields or ALT Text
	 * 
	 * Defined as public because it's a filter.
	 *
	 * @since 0.30
	 *
	 * @param	string	query clause before modification
	 *
	 * @return	string	query clause after "LEFT JOIN view ON post_id" item modification
	 */
	public static function mla_query_posts_join_filter( $join_clause ) {
		global $wpdb;

		if ( isset( self::$query_parameters['debug'] ) ) {
			$debug_array = array( 'join_string' => $join_clause );
		}

		/*
		 * ALT Text searches, custom field Table Views and custom field sorts are
		 * special; we have to use a subquery to build an intermediate table and
		 * modify the JOIN to include posts with no value for the metadata field.
		 * Three clauses are used because all three conditions can be present at once.
		 */
		if ( self::$query_parameters['use_alt_text_subquery'] ) {
			$sub_query = sprintf( 'SELECT post_id, meta_value FROM %1$s WHERE %1$s.meta_key = \'%2$s\'', $wpdb->postmeta, '_wp_attachment_image_alt' );
			$join_clause .= sprintf( ' LEFT JOIN ( %1$s ) %2$s ON (%3$s.ID = %2$s.post_id)', $sub_query, self::MLA_ALT_TEXT_SUBQUERY, $wpdb->posts );
		}

		if ( self::$query_parameters['use_postmeta_subquery'] ) {
			$sub_query = sprintf( 'SELECT post_id, meta_value FROM %1$s WHERE %1$s.meta_key = \'%2$s\'', $wpdb->postmeta, self::$query_parameters['postmeta_key'] );
			$join_clause .= sprintf( ' LEFT JOIN ( %1$s ) %2$s ON (%3$s.ID = %2$s.post_id)', $sub_query, self::MLA_TABLE_VIEW_SUBQUERY, $wpdb->posts );
		}

		if ( self::$query_parameters['use_orderby_subquery'] ) {
			$sub_query = sprintf( 'SELECT post_id, meta_value FROM %1$s WHERE %1$s.meta_key = \'%2$s\'', $wpdb->postmeta, self::$query_parameters['orderby_key'] );
			$join_clause .= sprintf( ' LEFT JOIN ( %1$s ) %2$s ON (%3$s.ID = %2$s.post_id)', $sub_query, self::MLA_ORDERBY_SUBQUERY, $wpdb->posts );
		}

		/*
		 * Custom field sorts are special; we have to use a subquery to build
		 * an intermediate table and modify the JOIN to include posts with
		 * no value for this metadata field.
		 */
		if ( isset( self::$query_parameters['orderby'] ) ) {
			if ( ( 'c_' == substr( self::$query_parameters['orderby'], 0, 2 ) ) || ( '_wp_attachment_image_alt' == self::$query_parameters['orderby'] ) ) {
				$orderby = self::MLA_ORDERBY_SUBQUERY . '.meta_value';
			}
		}

		if ( isset( self::$search_parameters['tax_terms_count'] ) ) {
			$tax_index = 0;
			$tax_clause = '';

			while ( $tax_index < self::$search_parameters['tax_terms_count'] ) {
				$prefix = 'mlatt' . $tax_index++;
				$tax_clause .= sprintf( ' LEFT JOIN %1$s AS %2$s ON (%3$s.ID = %2$s.object_id)', $wpdb->term_relationships, $prefix, $wpdb->posts );
			}

			$join_clause .= $tax_clause;
		}

		if ( isset( self::$query_parameters['debug'] ) ) {
			$debug_array['join_clause'] = $join_clause;

			/* translators: 1: DEBUG tag 2: join filter details */
			MLACore::mla_debug_add( sprintf( _x( '%1$s: mla_query_posts_join_filter = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $debug_array, true ) ) );
		} // debug

		return $join_clause;
	}

	/**
	 * Adds a GROUPBY clause, if required
	 * 
	 * Taxonomy text queries and postmeta queries can return multiple results for the same ID.
	 * Defined as public because it's a filter.
	 *
	 * @since 1.90
	 *
	 * @param	string	query clause before modification
	 *
	 * @return	string	updated query clause
	 */
	public static function mla_query_posts_groupby_filter( $groupby_clause ) {
		global $wpdb;

		if ( ( ! empty( self::$query_parameters['use_postmeta_subquery'] ) ) || ( ! empty( self::$query_parameters['use_alt_text_subquery'] ) ) || ( ! empty( self::$query_parameters['use_orderby_subquery'] ) ) || isset( self::$search_parameters['tax_terms_count'] ) ) {
			$groupby_clause = "{$wpdb->posts}.ID";
		}

		return $groupby_clause;
	}

	/**
	 * Adds a ORDERBY clause, if required
	 * 
	 * Expands the range of sort options because the logic in WP_Query is limited.
	 * Defined as public because it's a filter.
	 *
	 * @since 0.30
	 *
	 * @param	string	query clause before modification
	 *
	 * @return	string	updated query clause
	 */
	public static function mla_query_posts_orderby_filter( $orderby_clause ) {
		global $wpdb;

		if ( isset( self::$query_parameters['debug'] ) ) {
			$debug_array = array( 'orderby_string' => $orderby_clause );
		}

		if ( isset( self::$query_parameters['orderby'] ) ) {
			if ( 'c_' == substr( self::$query_parameters['orderby'], 0, 2 ) ) {
				$orderby = self::MLA_ORDERBY_SUBQUERY . '.meta_value';
			} /* custom field sort */ else { 
				switch ( self::$query_parameters['orderby'] ) {
					case 'none':
						$orderby = '';
						$orderby_clause = '';
						break;
					/*
					 * post__in is passed from Media Manager Modal Window
					 */
					case 'post__in':
						return $orderby_clause;
					/*
					 * There are two columns defined that end up sorting on post_title,
					 * so we can't use the database column to identify the column but
					 * we actually sort on the database column.
					 */
					case 'title_name':
						$orderby = $wpdb->posts . '.post_title';
						break;
					/*
					 * The _wp_attached_file meta data value is present for all attachments, and the
					 * sorting on the meta data value is handled by WP_Query
					 */
					case '_wp_attached_file':
						$orderby = '';
						break;
					/*
					 * The _wp_attachment_image_alt value is only present for images, so we have to
					 * use the view we prepared to get attachments with no meta data value
					 */
					case '_wp_attachment_image_alt':
						$orderby = self::MLA_ORDERBY_SUBQUERY . '.meta_value';
						break;
					default:
						$orderby = $wpdb->posts . '.' . self::$query_parameters['orderby'];
				} // $query_parameters['orderby']
			}

			if ( ! empty( $orderby ) ) {
				$orderby_clause = $orderby . ' ' . self::$query_parameters['order'];
			}
		} // isset

		if ( isset( self::$query_parameters['debug'] ) ) {
			$debug_array['orderby_clause'] = $orderby_clause;

			/* translators: 1: DEBUG tag 2: orderby details details */
			MLACore::mla_debug_add( sprintf( _x( '%1$s: mla_query_posts_orderby_filter = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $debug_array, true ) ) );
		} // debug

		return $orderby_clause;
	}

	/**
	 * Disable Relevanssi - A Better Search, v3.2 by Mikko Saari
	 * Defined as public because it's a filter.
	 *
	 * @since 1.80
	 *
	 * @param	boolean	Default setting
	 *
	 * @return	boolean	Updated setting
	 */
	public static function mla_query_relevanssi_admin_search_ok_filter( $admin_search_ok ) {
		return false;
	}

	/**
	 * Disable Relevanssi - A Better Search, v3.2 by Mikko Saari
	 * Defined as public because it's a filter.
	 *
	 * @since 2.25
	 *
	 * @param	boolean	Default setting
	 *
	 * @return	boolean	Updated setting
	 */
	public static function mla_query_relevanssi_prevent_default_request_filter( $prevent ) {
		return false;
	}

	/**
	 * Filters all clauses for get_terms queries
	 * 
	 * Defined as public because it's a filter.
	 *
	 * @since 2.13
	 *
	 * @param array $pieces     Terms query SQL clauses.
	 * @param array $taxonomies An array of taxonomies.
	 * @param array $args       An array of terms query arguments.
	 */
	public static function mla_query_terms_clauses_filter( $pieces, $taxonomies, $args ) {
		global $wpdb;

		if ( empty( $args['name__like'] ) ) {
			return $pieces;
		}

		$term = $args['name__like'];

		/*
		 * Escape any % in the source string
		 */
		if ( self::$wp_4dot0_plus ) {
			$sql_term = $wpdb->esc_like( $term );
			$sql_term = $wpdb->prepare( '%s', $sql_term );
		} else {
			$sql_term = "'" . esc_sql( like_escape( $term ) ) . "'";
		}

		/*
		 * Convert wildcard * to SQL %
		 */
		$sql_term = str_replace( '*', '%', $sql_term );

		/*
		 * Replace the LIKE pattern in the WHERE clause
		 */
		$match_clause = '%' . str_replace( '%', '\\\\%', $term ) . '%';
		$pieces['where'] = str_replace( "LIKE '{$match_clause}'", "LIKE {$sql_term}", $pieces['where'] );

		return $pieces;
	}

	/**
	 * Filters all clauses for shortcode queries, pre caching plugins
	 * 
	 * This is for debug purposes only.
	 * Defined as public because it's a filter.
	 *
	 * @since 1.80
	 *
	 * @param	array	query clauses before modification
	 *
	 * @return	array	query clauses after modification (none)
	 */
	public static function mla_query_posts_clauses_filter( $pieces ) {
		/* translators: 1: DEBUG tag 2: SQL clauses */
		MLACore::mla_debug_add( sprintf( _x( '%1$s: mla_query_posts_clauses_filter = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $pieces, true ) ) );

		return $pieces;
	}

	/**
	 * Filters all clauses for shortcode queries, post caching plugins
	 * 
	 * This is for debug purposes only.
	 * Defined as public because it's a filter.
	 *
	 * @since 1.80
	 *
	 * @param	array	query clauses before modification
	 *
	 * @return	array	query clauses after modification (none)
	 */
	public static function mla_query_posts_clauses_request_filter( $pieces ) {
		/* translators: 1: DEBUG tag 2: SQL clauses */
		MLACore::mla_debug_add( sprintf( _x( '%1$s: mla_query_posts_clauses_request_filter = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $pieces, true ) ) );

		return $pieces;
	}
} // class MLAQuery
?>