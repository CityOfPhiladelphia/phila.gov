<?php
/**
 * Media Library Assistant MIME Type Support
 *
 * @package Media Library Assistant
 * @since 1.40
 */

/**
 * Class MLA (Media Library Assistant) MIME filters WordPress MIME Type functions and supports
 * the Views and Uploads Settings tabs
 *
 * @package Media Library Assistant
 * @since 1.40
 */
class MLAMime {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.40
	 *
	 * @return	void
	 */
	public static function initialize() {
//		add_filter( 'sanitize_mime_type', 'MLAMime::mla_sanitize_mime_type_filter', 0x7FFFFFFF, 2 );
		add_filter( 'ext2type', 'MLAMime::mla_ext2type_filter', 0x7FFFFFFF, 1 );
//		add_filter( 'wp_check_filetype_and_ext', 'MLAMime::mla_wp_check_filetype_and_ext_filter', 0x7FFFFFFF, 4 );

		if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ENABLE_UPLOAD_MIMES ) ) {
			if ( function_exists('wp_get_mime_types') ) {
				add_filter( 'mime_types', 'MLAMime::mla_mime_types_filter', 0x7FFFFFFF, 1 );
			}

			add_filter( 'upload_mimes', 'MLAMime::mla_upload_mimes_filter', 0x7FFFFFFF, 2 );
		}

		if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ENABLE_POST_MIME_TYPES ) ) {
			add_filter( 'post_mime_types', 'MLAMime::mla_post_mime_types_filter', 0x7FFFFFFF, 1 );
		}

		add_filter( 'icon_dir', 'MLAMime::mla_icon_dir_filter', 0x7FFFFFFF, 1 );
		add_filter( 'icon_dir_uri', 'MLAMime::mla_icon_dir_uri_filter', 0x7FFFFFFF, 1 );
		add_filter( 'icon_dirs', 'MLAMime::mla_icon_dirs_filter', 0x7FFFFFFF, 1 );
		//add_filter( 'wp_mime_type_icon', 'MLAMime::mla_wp_mime_type_icon_filter', 0x7FFFFFFF, 3 );
	}

	/**
	 * Disable MIME filtering during option initialization
	 *
	 * @since 1.40
	 *
	 * @var	boolean
	 */
	private static $disable_mla_filtering = false;

	/**
	 * Sanitize a MIME type
	 *
	 * Called from /wp-includes/formatting.php, function sanitize_mime_type().
	 * Defined as public because it's a filter.
	 *
	 * @since 1.40
	 *
	 * @param	string	Sanitized MIME type
	 * @param	string	Raw MIME type
	 *
	 * @return	string	Updated sanitized MIME type
	 */
	public static function mla_sanitize_mime_type_filter( $sanitized_mime_type, $raw_mime_type ) {
		global $wp_filter;
//		error_log( 'DEBUG: mla_sanitize_mime_type_filter $sanitized_mime_type = ' . var_export( $sanitized_mime_type, true ), 0 );
//		error_log( 'DEBUG: mla_sanitize_mime_type_filter $raw_mime_type = ' . var_export( $raw_mime_type, true ), 0 );
//		error_log( 'DEBUG: $wp_filter[sanitize_mime_type] = ' . var_export( $wp_filter['sanitize_mime_type'], true ), 0 );
		return $sanitized_mime_type;
	} // mla_sanitize_mime_type_filter

	/**
	 * In-memory representation of the Icon Type => file extension(s) associations
	 *
	 * @since 1.40
	 *
	 * @var	array	slug => ( singular, plural, specification, post_mime_type, table_view, menu_order, description )
	 */
	private static $mla_icon_type_associations = NULL;

	/**
	 * Update the file extension to icon type (e.g., xls => spreadsheet, doc => document) array
	 *
	 * Note that the calling function, wp_ext2type, takes an extension and returns an icon type.
	 * This filter updates the array of possible matches to support the calling function.
	 *
	 * Called from /wp-includes/functions.php, function wp_ext2type(). That function is called from
	 * /wp-admin/includes/ajax-actions.php, function wp_ajax_send_link_to_editor(), 
	 * /wp-admin/includes/media.php, function wp_media_upload_handler(), and
	 * /wp-includes/post.php, function wp_mime_type_icon(). The first two calls look for "audio"
	 * and "video" files to call the appropriate filter. The third call assigns the appropriate icon
	 * to the file for display purposes.
	 *
	 * Defined as public because it's a filter.
	 *
	 * @since 1.40
	 *
	 * @param array The type => ( extensions ) associations.
	 *
	 * @return array The updated associations array.
	 */
	public static function mla_ext2type_filter( $standard_types ) {
		global $wp_filter;

		if ( self::$disable_mla_filtering ) {
			self::$mla_core_icon_types = $standard_types;
			return $standard_types;
		}

		if ( NULL != self::$mla_icon_type_associations ) {
			return self::$mla_icon_type_associations;
		}

		if ( ! self::_get_upload_mime_templates() ) {
			return $standard_types;
		}

		/*
		 * Build and sort the type => extensions list
		 */
		$items = self::mla_query_upload_items( array( 'mla_upload_view' => 'active' ), 0, 0 );
		$pairs = array();
		foreach ( $items as $value )
			if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ENABLE_MLA_ICONS ) ) {
				$pairs[ $value->slug ] = $value->icon_type;
			} else {
				$pairs[ $value->slug ] = $value->wp_icon_type;
			}

		asort( $pairs );

		/*
		 * Compress the list, grouping by icon_type
		 */
		self::$mla_icon_type_associations = array();
		$icon_type = '.bad.value.'; // prime the pump
		$extensions = array ( 'xxx' );
		foreach ( $pairs as $this_extension => $this_type ) {
			if ( $this_type != $icon_type ) {
				self::$mla_icon_type_associations[ $icon_type ] = $extensions;
				$extensions = array( $this_extension );
				$icon_type = $this_type;
			} else {
				$extensions[] = $this_extension;
			}
		}

		self::$mla_icon_type_associations[ $icon_type ] = $extensions;
		unset( self::$mla_icon_type_associations['.bad.value.'] );

		return self::$mla_icon_type_associations;
	} // mla_ext2type_filter

	/**
	 * Attempts to determine the real file type of a file
	 *
	 * Called from /wp-includes/functions.php, function wp_check_filetype_and_ext().
	 * Defined as public because it's a filter.
	 *
	 * @since 1.40
	 *
	 * @param	array	array( ext, type, proper_filename (string or false) )
	 * @param	string	Full path to the image
	 * @param	string	The filename of the image
	 * @param	array	Optional array of MIME types
	 *
	 * @return	array	Updated array( ext, type, proper_filename (string or false) )
	 */
	public static function mla_wp_check_filetype_and_ext_filter( $validate, $file, $filename, $mimes ) {
		global $wp_filter;
//		error_log( 'DEBUG: mla_wp_check_filetype_and_ext_filter $validate = ' . var_export( $validate, true ), 0 );
//		error_log( 'DEBUG: mla_wp_check_filetype_and_ext_filter $file = ' . var_export( $file, true ), 0 );
//		error_log( 'DEBUG: mla_wp_check_filetype_and_ext_filter $filename = ' . var_export( $filename, true ), 0 );
//		error_log( 'DEBUG: mla_wp_check_filetype_and_ext_filter $mimes = ' . var_export( $mimes, true ), 0 );
//		error_log( 'DEBUG: $wp_filter[wp_check_filetype_and_ext] = ' . var_export( $wp_filter['wp_check_filetype_and_ext'], true ), 0 );
		return $validate;
	} // mla_wp_check_filetype_and_ext_filter

	/**
	 * Retrieve list of MIME types and file extensions; use this filter to add types
	 *
	 * Called from /wp-includes/functions.php, function wp_get_mime_types(). That function
	 * is called from /wp-includes/class-wp-image-editor.php functions get_mime_type()
	 * and get_extension(), and from /wp-includes/functions.php, functions do_enclose()
	 * and get_allowed_mime_types().
	 *
	 * Defined as public because it's a filter.
	 *
	 * @since 1.40
	 *
	 * @param	array	Mime types keyed by the file extension regex corresponding to those types
	 *
	 * @return	array	Updated MIME types
	 */
	public static function mla_mime_types_filter( $mime_types ) {
		global $wp_filter;

		if ( self::$disable_mla_filtering || ! self::_get_upload_mime_templates() ) {
			return $mime_types;
		}

		/*
		 * Build and sort the extension => type list
		 */
		$items = self::mla_query_upload_items( array( 'mla_upload_view' => 'active' ), 0, 0 );
		$pairs = array();
		foreach ( $items as $value )
			$pairs[ $value->slug ] = $value->mime_type;

		asort( $pairs );

		/*
		 * Compress the list, grouping my mime_type
		 */
		$items = array();
		$extensions = '.bad.value.'; // prime the pump
		$mime_type = '';
		foreach ( $pairs as $this_extension => $this_type ) {
			if ( $this_type != $mime_type ) {
				$items[ $extensions ] = $mime_type;
				$extensions = $this_extension;
				$mime_type = $this_type;
			} else {
				$extensions .= '|' . $this_extension;
			}
		}

		$items[ $extensions ] = $mime_type;
		unset( $items['.bad.value.'] );

		return $items; // $mime_types;
	} // mla_mime_types_filter

	/**
	 * Retrieve list of allowed MIME types and file extensions; use this filter to remove types
	 *
	 * Called from /wp-includes/functions.php, function get_allowed_mime_types(). That function
	 * is called from /wp-includes/formatting.php function sanitize_file_name() and from
	 * /wp-includes/functions.php, function wp_check_filetype(). wp_check_filetype returns only one
	 * MIME type for a given file extension, so the file extension should/must be a unique key.
	 *
	 * This filter is also hooked by /wp-includes/ms-functions.php and processed in function
	 * check_upload_mimes(), which "is used to filter that list against the filetype whitelist
	 * provided by Multisite Super Admins at wp-admin/network/settings.php." Multisite installs must
	 * respect this restriction, so any list we produce will be passed thru that function if it exists.
	 *
	 * This function is defined as public because it's a filter.
	 *
	 * @since 1.40
	 *
	 * @param	array	Mime types keyed by the file extension regex corresponding to those types
	 * @param	mixed	User ID (integer) or object for checking against 'unfiltered_html' capability
	 *
	 * @return	array	Updated allowed MIME types
	 */
	public static function mla_upload_mimes_filter( $mime_types, $user = NULL ) {
		global $wp_filter;

		if ( self::$disable_mla_filtering || ! self::_get_upload_mime_templates() ) {
			return $mime_types;
		}

		/*
		 * Build and sort the extension => type list
		 */
		$items = self::mla_query_upload_items( array( 'mla_upload_view' => 'active' ), 0, 0 );
		$pairs = array();
		foreach ( $items as $value )
			$pairs[ $value->slug ] = $value->mime_type;

		asort( $pairs );

		/*
		 * Compress the list, grouping by mime_type
		 */
		$items = array();
		$extensions = '.bad.value.'; // prime the pump
		$mime_type = '';
		foreach ( $pairs as $this_extension => $this_type ) {
			if ( $this_type != $mime_type ) {
				$items[ $extensions ] = $mime_type;
				$extensions = $this_extension;
				$mime_type = $this_type;
			} else {
				$extensions .= '|' . $this_extension;
			}
		}

		$items[ $extensions ] = $mime_type;
		unset( $items['.bad.value.'] );

		/*
		 * Respect the WordPress per-user 'unfiltered_html' capability test
		 */
		if ( function_exists( 'current_user_can' ) ) {
			$unfiltered = $user ? user_can( $user, 'unfiltered_html' ) : current_user_can( 'unfiltered_html' );
		} else {
			$unfiltered = true;
		}

		if ( empty( $unfiltered ) ) {
			unset( $items['htm|html'] );
			unset( $items['htm'] );
			unset( $items['html'] );
		}

		return $items;
	} // mla_upload_mimes_filter

	/**
	 * Get default Post MIME Types
	 *
	 * Called from /wp-includes/post.php, function get_post_mime_types(). That function
	 * is called from:
	 * /wp-admin/includes/media.php function get_media_item(), to validate the type of an
	 * attachment when it is edited,
	 * /wp-admin/includes/post.php, function wp_edit_attachments_query() to count the number
	 * of attachments of each type, and
	 * /wp-includes/media.php function wp_enqueue_media(), to populate the the Media Manager/Add Media
	 * "media items" drop down list.
	 *
	 * Defined as public because it's a filter.
	 *
	 * @since 1.40
	 *
	 * @param	array	Content types (image, audio, video) and presentation strings, e.g.
	 * 					'image' => array(__('Images', 'media-library-assistant'), __('Manage Images', 'media-library-assistant'),
	 *	 				_n_noop('Image <span class="count">(%s)</span>', 'Images <span class="count">(%s)</span>', 'media-library-assistant')),
	 *
	 * @return	array	Updated allowed MIME types
	 */
	public static function mla_post_mime_types_filter( $post_mime_types ) {
		global $wp_filter;

		if ( self::$disable_mla_filtering || ! self::_get_post_mime_templates() ) {
			return $post_mime_types;
		}

		/*
		 * Filter the list and sort by menu_order
		 */
		$minor_sort = 0;
		$sorted_types = array();
		foreach ( self::$mla_post_mime_templates as $slug => $value )
			if ( $value['post_mime_type'] ) {
				$value['slug'] = $slug;
				$sorted_types[ ( $value['menu_order'] * 1000 ) + $minor_sort++ ] = $value;
			} // new type
		ksort( $sorted_types, SORT_NUMERIC );

		/*
		 * Generate the merged, sorted list
		 *
		 * The 'singular' and 'plural' strings are already translated. The _n_noop() call
		 * will not actually translate anything since the $singular and $plural variables
		 * are ignored by Poedit and there will be no "msgid" strings that contain the
		 * HTML markup within them.
		 */
		 $manage = _x( 'Manage', 'post_mime_types', 'media-library-assistant' ) . ' ';
		$new_mime_types = array();
		foreach ( $sorted_types as $value ) {
			$singular = sprintf('%s <span class="count">(%%s)</span>', $value['singular'] );
			$plural = sprintf('%s <span class="count">(%%s)</span>', $value['plural'] );
			$new_mime_types[ $value['slug'] ] = array(
				$value['plural'],
				$manage . $value['plural'],
				_n_noop( $singular, $plural, 'media-library-assistant' )
			);
		}

		return $new_mime_types;
	} // mla_post_mime_types_filter

	/**
	 * Retrieve the icon directory for a MIME type
	 *
	 * Called from /wp-includes/deprecated.php, function get_attachment_icon_src().
	 * Called from /wp-includes/media.php, function wp_get_attachment_image_src().
	 * Called from /wp-includes/post.php, function wp_mime_type_icon().
	 * Defined as public because it's a filter.
	 *
	 * @since 1.40
	 *
	 * @param	string	Path to the icon directory
	 *
	 * @return	string	Updated path to the icon directory, no trailing slash
	 */
	public static function mla_icon_dir_filter( $path ) {
		if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ENABLE_MLA_ICONS ) ) {
			return MLA_PLUGIN_PATH . 'images/crystal';
		}
		 
		return $path;
	} // mla_icon_dir_filter

	/**
	 * Retrieve the icon directory URL for a MIME type
	 *
	 * Called from /wp-includes/post.php, function wp_mime_type_icon().
	 * Defined as public because it's a filter.
	 *
	 * @since 1.40
	 *
	 * @param	string	Path to the icon directory URL
	 *
	 * @return	string	Updated path to the icon directory URL, no trailing slash
	 */
	public static function mla_icon_dir_uri_filter( $uri ) {
		if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ENABLE_MLA_ICONS ) ) {
			return MLA_PLUGIN_URL . 'images/crystal';
		}

		return $uri;
	} // mla_icon_dir_uri_filter

	/**
	 * Retrieve the icon (directory => URI) array for a MIME type
	 *
	 * Called from /wp-includes/post.php, function wp_mime_type_icon().
	 * Defined as public because it's a filter.
	 *
	 * @since 1.40
	 *
	 * @param	array	Path(s) and URI(s) to the icon directories
	 *
	 * @return	array	Updated (path => URI) array
	 */
	public static function mla_icon_dirs_filter( $path_uri_array ) {
		if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ENABLE_MLA_ICONS ) ) {
			$path_uri_array [ MLA_PLUGIN_PATH . 'images/crystal' ] = MLA_PLUGIN_URL . 'images/crystal';
		}

		return $path_uri_array;
	} // mla_icon_dirs_filter

	/**
	 * Retrieve the icon for a MIME type
	 *
	 * Called from /wp-includes/post.php, function wp_mime_type_icon().
	 * Defined as public because it's a filter.
	 *
	 * @since 1.40
	 *
	 * @param	string	URI to the MIME type icon
	 * @param	string	MIME type represented by the icon
	 * @param	integer	Attachment ID or zero (0) if MIME type passed in
	 *
	 * @return	array	Updated URI to the MIME type icon
	 */
	public static function mla_wp_mime_type_icon_filter( $icon, $mime, $post_id ) {
		return $icon;
	} // mla_wp_mime_type_icon_filter

	/**
	 * Sanitize and expand query arguments from request variables
	 *
	 * @since 1.40
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		Optional number of rows (default 0) to skip over to reach desired page
	 * @param	int		Optional number of rows on each page (0 = all rows, default)
	 *
	 * @return	array	revised arguments suitable for query
	 */
	private static function _prepare_view_items_query( $raw_request, $offset = 0, $count = 0 ) {
		/*
		 * Go through the $raw_request, take only the arguments that are used in the query and
		 * sanitize or validate them.
		 */
		if ( ! is_array( $raw_request ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			error_log( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLAMime::_prepare_view_items_query', var_export( $raw_request, true ) ), 0 );
			return NULL;
		}

		$clean_request = array (
			'orderby' => 'slug',
			'order' => 'ASC',
			's' => ''
		);

		foreach ( $raw_request as $key => $value ) {
			switch ( $key ) {
				case 'orderby':
					if ( 'none' == $value ) {
						$clean_request[ $key ] = $value;
					} else {
						$sortable_columns = MLA_View_List_Table::mla_get_sortable_columns();
						foreach ($sortable_columns as $sort_key => $sort_value ) {
							if ( $value == $sort_value[0] ) {
								$clean_request[ $key ] = $value;
								break;
							}
						} // foreach
					}
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
				/*
				 * ['s'] - Search Media by one or more keywords
				 */
				case 's':
					$clean_request[ $key ] = stripslashes( trim( $value ) );
					break;
				default:
					// ignore anything else in $_REQUEST
			} // switch $key
		} // foreach $raw_request

		/*
		 * Ignore incoming paged value; use offset and count instead
		 */
		if ( ( (int) $count ) > 0 ) {
			$clean_request['offset'] = $offset;
			$clean_request['posts_per_page'] = $count;
		}

		return $clean_request;
	}

	/**
	 * Add filters, run query, remove filters
	 *
	 * @since 1.40
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 *
	 * @return	array	query results; array of MLA post_mime_type objects
	 */
	private static function _execute_view_items_query( $request ) {
		if ( ! self::_get_post_mime_templates() ) {
			return array ();
		}

		/*
		 * Sort and filter the list
		 */
		$keyword = isset( $request['s'] ) ? $request['s'] : '';
		$index = 0;
		$sorted_types = array();

		foreach ( self::$mla_post_mime_templates as $slug => $value ) {
			$index++;
			if ( ! empty( $keyword ) ) {
				$found  = false !== stripos( $slug, $keyword );
				$found |= false !== stripos( $value['specification'], $keyword );
				$found |= false !== stripos( $value['singular'], $keyword );
				$found |= false !== stripos( $value['plural'], $keyword );
				$found |= false !== stripos( $value['description'], $keyword );

				if ( ! $found ) {
					continue;
				}
			}

			$value['slug'] = $slug;
			$value['post_ID'] = $index;
			switch ( $request['orderby'] ) {
				case 'slug':
					$sorted_types[ $slug ] = (object) $value;
					break;
				case 'specification':
					$sorted_types[ ( empty( $value['specification'] ) ? chr(1) : $value['specification'] ) . $index ] = (object) $value;
					break;
				case 'post_mime_type':
					$sorted_types[ ( $value['post_mime_type'] ? 'yes' : 'no' ) . $index ] = (object) $value;
					break;
				case 'table_view':
					$sorted_types[ ( $value['table_view'] ? 'yes' : 'no' ) . $index ] = (object) $value;
					break;
				case 'singular':
					$sorted_types[ ( empty( $value['singular'] ) ? chr(1) : $value['singular'] ) . $index ] = (object) $value;
					break;
				case 'plural':
					$sorted_types[ ( empty( $value['plural'] ) ? chr(1) : $value['plural'] ) . $index ] = (object) $value;
					break;
				case 'menu_order':
					$sorted_types[ empty( $value['menu_order'] ) ? $index : ( 1000 * $value['menu_order'] ) + $index ] = (object) $value;
					break;
				case 'description':
					$sorted_types[ ( empty( $value['description'] ) ? chr(1) : $value['description'] ) . $index ] = (object) $value;
					break;
				default:
					$sorted_types[ $slug ] = (object) $value;
					break;
			} //orderby
		}
		ksort( $sorted_types );

		if ( 'DESC' == $request['order'] ) {
			$sorted_types = array_reverse( $sorted_types, true );
		}

		/*
		 * Paginate the sorted list
		 */
		$results = array();
		$offset = isset( $request['offset'] ) ? $request['offset'] : 0;
		$count = isset( $request['posts_per_page'] ) ? $request['posts_per_page'] : -1;
		foreach ( $sorted_types as $value ) {
			if ( $offset ) {
				$offset--;
			} elseif ( $count-- ) {
				$results[] = $value;
			} else {
				break;
			}
		}

		return $results;
	}

	/**
	 * Get the total number of MLA post_mime_type objects
	 *
	 * @since 1.40
	 *
	 * @param	array	Query variables, e.g., from $_REQUEST
	 *
	 * @return	integer	Number of MLA post_mime_type objects
	 */
	public static function mla_count_view_items( $request ) {
		$request = self::_prepare_view_items_query( $request );
		$results = self::_execute_view_items_query( $request );
		return count( $results );
	}

	/**
	 * Retrieve MLA post_mime_type objects for list table display
	 *
	 * @since 1.40
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		number of rows to skip over to reach desired page
	 * @param	int		number of rows on each page
	 *
	 * @return	array	MLA post_mime_type objects
	 */
	public static function mla_query_view_items( $request, $offset, $count ) {
		$request = self::_prepare_view_items_query( $request, $offset, $count );
		$results = self::_execute_view_items_query( $request );
		return $results;
	}

	/**
	 * Retrieve views eligible for Media/Assistant table display
	 *
	 * @since 1.40
	 *
	 * @return	array	table views array ( specification => Plural Label )
	 */
	public static function mla_pluck_table_views() {
		$mla_types = MLAMime::mla_query_view_items( array( 'orderby' => 'menu_order' ), 0, 0 );
		if ( ! is_array( $mla_types ) ) {
			$mla_types = array ();
		}

		/*
		 * Filter the list, generate the list
		 */
		$results = array();
		foreach ( $mla_types as $value ) {
			if ( in_array( $value->slug, array( 'all', 'trash', 'detached' ) ) ) {
				continue;
			}

			if ( $value->table_view ) {
				if ( empty( $value->specification ) ) {
					$results[ $value->slug ] = $value->plural;
				} else {
					$results[ $value->specification ] = $value->plural;
				}
			}
		}

		return $results;
	}

	/**
	 * In-memory representation of the Post MIME Types
	 *
	 * @since 1.40
	 *
	 * @var	array	slug => ( singular, plural, specification, post_mime_type, table_view, menu_order, description )
	 */
	private static $mla_post_mime_templates = NULL;

	/**
	 * Highest existing Post MIME Type ID value
	 *
	 * @since 1.40
	 *
	 * @var	integer
	 */
	private static $mla_post_mime_highest_ID = 0;

	/**
	 * Assemble the in-memory representation of the Post MIME Types 
	 *
	 * @since 1.40
	 *
	 * @param	boolean	Force a reload/recalculation of types
	 *
	 * @return	boolean	Success (true) or failure (false) of the operation
	 */
	private static function _get_post_mime_templates( $force_refresh = false ) {
		if ( false == $force_refresh && NULL != self::$mla_post_mime_templates ) {
			return true;
		}

		/*
		 * Start with MLA standard types
		 */
		$mla_types = MLACore::mla_get_option( MLACoreOptions::MLA_POST_MIME_TYPES, true );
		if ( ! is_array( $mla_types ) ) {
			$mla_types = array ();
		}

		/*
		 * If this is the first time MLA Post MIME support is invoked, match to the 
		 * filter-enhanced extensions, retain anything new as a custom type.
		 * Otherwise, add the current MLA custom types.
		 */
		$custom_types = MLACore::mla_get_option( MLACoreOptions::MLA_POST_MIME_TYPES, false, true );

		if ( is_array( $custom_types ) ) {
			$mla_types = array_merge( $mla_types, $custom_types );
		} else {
			/*
			 * Add existing types that are not already in the MLA list
			 */
			self::$disable_mla_filtering = true;
			$post_mime_types = get_post_mime_types();
			self::$disable_mla_filtering = false;

			foreach ( $post_mime_types as $slug => $value )
				if ( ! isset( $mla_types[ $slug ] ) ) {
					$mla_types[ $slug ] = array(
						'singular' => substr( $value[2][0], 0, strpos( $value[2][0], ' <' ) ),
						'plural' => $value[0],
						'specification' => '',
						'post_mime_type' => true,
						'table_view' => true,
						'menu_order' => 0,
						'description' => _x( 'Copied from previous filter/plugin', 'post_mime_types_description', 'media-library-assistant' )
					);
				} // new type
		} // First time called

		self::$mla_post_mime_templates = array();
		self::$mla_post_mime_highest_ID = 0;

		/*
		 * Load and number the entries
		 */
		foreach ( $mla_types as $slug => $value ) {
			self::$mla_post_mime_templates[ $slug ] = $value;
			self::$mla_post_mime_templates[ $slug ]['post_ID'] = ++self::$mla_post_mime_highest_ID;
			}

		self::_put_post_mime_templates();
		return true;
	}

	/**
	 * Store the custom entries of the Post MIME Types 
	 *
	 * @since 1.40
	 *
	 * @return	boolean	Success (true) or failure (false) of the operation
	 */
	private static function _put_post_mime_templates() {
		$mla_post_mimes = array ();

		$mla_types = MLACore::mla_get_option( MLACoreOptions::MLA_POST_MIME_TYPES, true );

		foreach ( self::$mla_post_mime_templates as $slug => $value ) {
			unset( $value['post_ID'] );
			if ( isset ( $mla_types[ $slug ] ) && $value == $mla_types[ $slug ] ) {
				continue;
			}

			$mla_post_mimes[ $slug ] =  $value;
		}

		MLACore::mla_update_option( MLACoreOptions::MLA_POST_MIME_TYPES, $mla_post_mimes );
		return true;
	}

	/**
	 * Convert a Library View/Post MIME Type specification to WP_Query parameters
	 *
	 * Compatibility shim for MLACore::mla_prepare_view_query
	 *
	 * @since 1.40
	 *
	 * @param	string	View slug, unique identifier
	 * @param	string	A specification, e.g., "custom:Field,null" or "audio,application/vnd.*ms*"
	 *
	 * @return	array	post_mime_type specification or custom field query
	 */
	public static function mla_prepare_view_query( $slug, $specification ) {
		return MLACore::mla_prepare_view_query( $slug, $specification );
	}

	/**
	 * Analyze a Library View/Post MIME Type specification, returning an array of the placeholders it contains
	 *
	 * Compatibility shim for MLACore::mla_parse_view_specification
	 *
	 * @since 1.40
	 *
	 * @param	string|array	A specification, e.g., "custom:Field,null" or "audio,application/vnd.*ms*"
	 *
	 * @return	array	( ['prefix'] => string, ['name'] => string, ['value'] => string, ['option'] => string, optional ['error'] => string )
	 */
	public static function mla_parse_view_specification( $specification ) {
		return MLACore::mla_parse_view_specification( $specification );
	}

	/**
	 * Add an MLA post_mime_type object
	 *
	 * @since 1.40
	 *
	 * @param	array	Query variables for a single object, including slug
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	public static function mla_add_post_mime_type( $request ) {
		if ( ! self::_get_post_mime_templates() ) {
			self::$mla_post_mime_templates = array ();
		}

		$messages = '';
		$errors = '';

		/*
		 * Sanitize slug value
		 */
		$slug = sanitize_mime_type( $request['slug'] );
		if ( $request['post_mime_type'] ) {

			if ( !empty( $request['specification'] ) ) {
				$request['specification'] = '';
				$messages .= '<br>' . __( 'Ignoring specification for Post MIME Type; using slug', 'media-library-assistant' );
			}
		}

		if ( $slug != $request['slug'] ) {
			/* translators: 1: element name 2: bad_value 3: good_value */
			$messages .= sprintf( __( '<br>' . 'Changing %1$s "%2$s" to valid value "%3$s"', 'media-library-assistant' ), __( 'Slug', 'media-library-assistant' ), $request['slug'], $slug );
		}

		/*
		 * Make sure new slug is unique
		 */
		if ( isset( self::$mla_post_mime_templates[ $slug ] ) ) {
				/* translators: 1: ERROR tag 2: slug */
			$errors .= '<br>' . sprintf( __( '%1$s: Could not add Slug "%2$s"; value already exists', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $slug );
		}

		/*
		 * Validate specification, if present
		 */
		if ( !empty( $request['specification'] ) ) {
			$specification = MLACore::mla_parse_view_specification( $request['specification'] );
			if ( isset( $specification['error'] ) ) {
				$errors .= $specification['error'];
			}
		}

		if ( ! empty( $errors ) ) {
			return array(
				'message' => substr( $errors . $messages, 4),
				'body' => ''
			);
		}

		$new_type = array();
		$new_type['singular'] = sanitize_text_field( $request['singular'] );
		$new_type['plural'] = sanitize_text_field( $request['plural'] );
		$new_type['specification'] = trim( $request['specification'] );
		$new_type['post_mime_type'] = $request['post_mime_type'];
		$new_type['table_view'] = $request['table_view'];
		$new_type['menu_order'] = absint( $request['menu_order'] );
		$new_type['description'] = sanitize_text_field( $request['description'] );
		$new_type['post_ID'] = ++self::$mla_post_mime_highest_ID;

		self::$mla_post_mime_templates[ $slug ] = $new_type;
		self::_put_post_mime_templates();

		return array(
			/* translators: 1: slug */
			'message' => substr( $messages . '<br>' . sprintf( __( 'Edit view "%1$s"; added', 'media-library-assistant' ), $slug ), 4),
			'body' => ''
		);
	}

	/**
	 * Update an MLA post_mime_type object
	 *
	 * @since 1.40
	 *
	 * @param	array	Query variables for new object values, including optional original_slug
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	public static function mla_update_post_mime_type( $request ) {
		if ( ! self::_get_post_mime_templates() ) {
			self::$mla_post_mime_templates = array ();
		}

		$messages = '';
		$errors = '';
		$slug = sanitize_mime_type( $request['slug'] );
		$original_slug = isset( $request['original_slug'] ) ? $request['original_slug'] : $slug;
		unset( $request['original_slug'] );

		if ( isset( self::$mla_post_mime_templates[ $original_slug ] ) ) {
			$original_type = self::$mla_post_mime_templates[ $original_slug ];
		} else {
			$original_type = array(
				'singular' => '',
				'plural' => '',
				'specification' => '',
				'post_mime_type' => 'checked="checked"',
				'table_view' => 'checked="checked"',
				'menu_order' => '',
				'description' => ''
			);
		}

		/*
		 * Validate changed slug value
		 */
		if ( $slug != $original_slug ) {
			if ( $slug != $request['slug'] ) {
				/* translators: 1: element name 2: bad_value 3: good_value */
				$messages .= sprintf( __( '<br>' . 'Changing new %1$s "%2$s" to valid value "%3$s"', 'media-library-assistant' ), __( 'Slug', 'media-library-assistant' ), $request['slug'], $slug );
			}

			/*
			 * Make sure new slug is unique
			 */
			if ( isset( self::$mla_post_mime_templates[ $slug ] ) ) {
				/* translators: 1: ERROR tag 2: slug */
				$errors .= '<br>' . sprintf( __( '%1$s: Could not add Slug "%2$s"; value already exists', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $slug );
			} else {
				/* translators: 1: element name 2: old_value 3: new_value */
				$messages .= sprintf( '<br>' . __( 'Changing %1$s from "%2$s" to "%3$s"', 'media-library-assistant' ), __( 'Slug', 'media-library-assistant' ), $original_slug, $slug );
			}
		}

		/*
		 * Validate specification, if present and allowed
		 */
		$specification = trim( isset( $request['specification'] ) ? $request['specification'] : $original_type['specification'] );
		$post_mime_type = isset( $request['post_mime_type'] ) ? $request['post_mime_type'] : $original_type['post_mime_type'];
		if ( $post_mime_type ) {
			if ( !empty( $specification ) ) {
				$specification = '';
				$messages .= '<br>' . __( 'Ignoring specification for Post MIME Type; using slug', 'media-library-assistant' );
			}
		}

		if ( !empty( $specification ) ) {
			$result = MLACore::mla_parse_view_specification( $request['specification'] );
			if ( isset( $result['error'] ) ) {
				$errors .= $result['error'];
			}
		}

		if ( ! empty( $errors ) ) {
			return array(
				'message' => substr( $errors . $messages, 4),
				'body' => ''
			);
		}

		$new_type = array();
		$new_type['singular'] = isset( $request['singular'] ) ? sanitize_text_field( $request['singular'] ) : $original_type['singular'];
		$new_type['plural'] = isset( $request['plural'] ) ? sanitize_text_field( $request['plural'] ) : $original_type['plural'];
		$new_type['specification'] = $specification;
		$new_type['post_mime_type'] = $post_mime_type;
		$new_type['table_view'] = isset( $request['table_view'] ) ? $request['table_view'] : $original_type['table_view'];
		$new_type['menu_order'] = isset( $request['menu_order'] ) ? absint( $request['menu_order'] ) : $original_type['menu_order'];
		$new_type['description'] = isset( $request['description'] ) ? sanitize_text_field( $request['description'] ) : $original_type['description'];

		if ( ( $slug == $original_slug ) && ( self::$mla_post_mime_templates[ $slug ] == $new_type ) ) {
			return array(
				/* translators: 1: slug */
				'message' => substr( $messages . '<br>' . sprintf( __( 'Edit view "%1$s"; no changes detected', 'media-library-assistant' ), $slug ), 4),
				'body' => ''
			);
		}

		self::$mla_post_mime_templates[ $slug ] = $new_type;

		if ( $slug != $original_slug ) {
			unset( self::$mla_post_mime_templates[ $original_slug ] );
		}

		self::_put_post_mime_templates();
		return array(
			/* translators: 1: slug */
			'message' => $messages = substr( $messages . '<br>' . sprintf( __( 'Edit view "%1$s"; updated', 'media-library-assistant' ), $slug ), 4),
			'body' => ''
		);
	}

	/**
	 * Retrieve an MLA post_mime_type slug given a post_ID
	 *
	 * @since 1.40
	 *
	 * @param	integer	MLA post_mime_type post_ID
	 *
	 * @return	mixed	string with slug of the requested object; false if object not found
	 */
	public static function mla_get_post_mime_type_slug( $post_ID ) {
		if ( ! self::_get_post_mime_templates() ) {
			self::$mla_post_mime_templates = array ();
		}

		foreach ( self::$mla_post_mime_templates as $slug => $value ) {
			if ( $post_ID == $value['post_ID'] ) {
				return $slug;
			}
		}

		return false;
	}

	/**
	 * Retrieve an MLA post_mime_type object
	 *
	 * @since 1.40
	 *
	 * @param	string	MLA post_mime_type slug
	 *
	 * @return	mixed	Array of elements, including slug, for the requested object; false if object not found
	 */
	public static function mla_get_post_mime_type( $slug ) {
		if ( ! self::_get_post_mime_templates() ) {
			self::$mla_post_mime_templates = array ();
		}

		if ( isset( self::$mla_post_mime_templates[ $slug ] ) ) {
			$matched_value = self::$mla_post_mime_templates[ $slug ];
			$matched_value['slug'] = $slug;
			return $matched_value;
		}

		return false;
	}

	/**
	 * Delete an MLA post_mime_type object
	 *
	 * @since 1.40
	 *
	 * @param	string	MLA post_mime_type slug
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	public static function mla_delete_post_mime_type( $slug ) {
		if ( ! self::_get_post_mime_templates() ) {
			self::$mla_post_mime_templates = array ();
		}

		if ( isset( self::$mla_post_mime_templates[ $slug ] ) ) {
			unset( self::$mla_post_mime_templates[ $slug ] );
			self::_put_post_mime_templates();
			self::_get_post_mime_templates( true );

			if ( isset( self::$mla_post_mime_templates[ $slug ] ) ) {
				return array(
					/* translators: 1: slug */
					'message' => sprintf( __( 'View "%1$s" reverted to standard', 'media-library-assistant' ), $slug ),
					'body' => ''
				);
			} else {
				return array(
					/* translators: 1: slug */
					'message' => sprintf( __( 'View "%1$s" deleted', 'media-library-assistant' ), $slug ),
					'body' => ''
				);
			}
		}

		return array(
			/* translators: 1: ERROR tag 2: slug */
			'message' => sprintf( __( '%1$s: Did not find view "%2$s"', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $slug ),
			'body' => ''
		);
	}

	/**
	 * Sanitize and expand Upload MIME Type query arguments from request variables
	 *
	 * @since 1.40
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		Optional number of rows (default 0) to skip over to reach desired page
	 * @param	int		Optional number of rows on each page (0 = all rows, default)
	 *
	 * @return	array	revised arguments suitable for query
	 */
	private static function _prepare_upload_items_query( $raw_request, $offset = 0, $count = 0 ) {
		/*
		 * Go through the $raw_request, take only the arguments that are used in the query and
		 * sanitize or validate them.
		 */
		if ( ! is_array( $raw_request ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			error_log( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLAMime::_prepare_upload_items_query', var_export( $raw_request, true ) ), 0 );
			return NULL;
		}

		$clean_request = array (
			'mla_upload_view' => 'all',
			'orderby' => 'slug',
			'order' => 'ASC',
			's' => ''
		);

		foreach ( $raw_request as $key => $value ) {
			switch ( $key ) {
				case 'mla_upload_view':
					$clean_request[ $key ] = $value;
					break;
				case 'orderby':
					if ( 'none' == $value ) {
						$clean_request[ $key ] = $value;
					} else {
						$sortable_columns = MLA_Upload_List_Table::mla_get_sortable_columns();
						foreach ($sortable_columns as $sort_key => $sort_value ) {
							if ( $value == $sort_value[0] ) {
								$clean_request[ $key ] = $value;
								break;
							}
						} // foreach
					}
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
				/*
				 * ['s'] - Search Media by one or more keywords
				 */
				case 's':
					$clean_request[ $key ] = stripslashes( trim( $value ) );
					break;
				default:
					// ignore anything else in $_REQUEST
			} // switch $key
		} // foreach $raw_request

		/*
		 * Ignore incoming paged value; use offset and count instead
		 */
		if ( ( (int) $count ) > 0 ) {
			$clean_request['offset'] = $offset;
			$clean_request['posts_per_page'] = $count;
		}

		return $clean_request;
	}

	/**
	 * Execute an Upload MIME Types query
	 *
	 * @since 1.40
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 *
	 * @return	array	query results; array of MLA Upload MIME Type objects
	 */
	private static function _execute_upload_items_query( $request ) {
		if ( ! self::_get_upload_mime_templates() ) {
			return array ();
		}

		/*
		 * Sort and filter the list
		 */
		$keyword = isset( $request['s'] ) ? $request['s'] : '';
		$extension = 0 === strpos( $keyword, '.' ) ? substr( $keyword, 1) : false;
		$view = isset( $request['mla_upload_view'] ) ? $request['mla_upload_view'] : 'all';
		$sorted_types = array();

		foreach ( self::$mla_upload_mime_templates as $slug => $value ) {
			if ( ! empty( $keyword ) ) {
				if ( false === $extension ) {
				$found  = false !== stripos( $slug, $keyword );
				$found |= false !== stripos( $value['mime_type'], $keyword );
				$found |= false !== stripos( $value['icon_type'], $keyword );
				$found |= false !== stripos( $value['core_type'], $keyword );
				$found |= false !== stripos( $value['mla_type'], $keyword );
				$found |= false !== stripos( $value['core_icon_type'], $keyword );
				$found |= false !== stripos( $value['description'], $keyword );
				} else {
					$found  = false !== stripos( $slug, $extension );
				}

				if ( ! $found ) {
					continue;
				}
			}

			switch( $view ) {
				case 'active':
					$found = ! $value['disabled'];
					break;
				case 'inactive':
					$found = $value['disabled'];
					break;
				case 'core':
				case 'mla':
				case 'custom':
					$found = $view == $value['source'];
					break;
				default:
					$found = true;
			}// $view

			if ( ! $found ) {
				continue;
			}

			$value['slug'] = $slug;
			switch ( $request['orderby'] ) {
				case 'slug':
					$sorted_types[ $slug ] = (object) $value;
					break;
				case 'mime_type':
					$sorted_types[ ( empty( $value['mime_type'] ) ? chr(1) : $value['mime_type'] ) . $value['post_ID'] ] = (object) $value;
					break;
				case 'icon_type':
					$sorted_types[ ( empty( $value['icon_type'] ) ? chr(1) : $value['icon_type'] ) . $value['post_ID'] ] = (object) $value;
					break;
				case 'source':
					$sorted_types[ ( empty( $value['source'] ) ? chr(1) : $value['source'] ) . $value['post_ID'] ] = (object) $value;
					break;
				case 'disabled':
					$sorted_types[ ( $value['disabled'] ? 'inactive' : 'active' ) . $value['post_ID'] ] = (object) $value;
					break;
				case 'core_type':
					$sorted_types[ ( empty( $value['core_type'] ) ? chr(1) : $value['core_type'] ) . $value['post_ID'] ] = (object) $value;
					break;
				case 'mla_type':
					$sorted_types[ ( empty( $value['mla_type'] ) ? chr(1) : $value['mla_type'] ) . $value['post_ID'] ] = (object) $value;
					break;
				case 'standard_source':
					$sorted_types[ ( empty( $value['standard_source'] ) ? chr(1) : $value['standard_source'] ) . $value['post_ID'] ] = (object) $value;
					break;
				case 'core_icon_type':
					$sorted_types[ ( empty( $value['core_icon_type'] ) ? chr(1) : $value['core_icon_type'] ) . $value['post_ID'] ] = (object) $value;
					break;
				case 'description':
					$sorted_types[ ( empty( $value['description'] ) ? chr(1) : $value['description'] ) . $value['post_ID'] ] = (object) $value;
					break;
				default:
					$sorted_types[ $slug ] = (object) $value;
					break;
			} //orderby
		}
		ksort( $sorted_types );

		if ( 'DESC' == $request['order'] ) {
			$sorted_types = array_reverse( $sorted_types, true );
		}

		/*
		 * Paginate the sorted list
		 */
		$results = array();
		$offset = isset( $request['offset'] ) ? $request['offset'] : 0;
		$count = isset( $request['posts_per_page'] ) ? $request['posts_per_page'] : -1;
		foreach ( $sorted_types as $value ) {
			if ( $offset ) {
				$offset--;
			} elseif ( $count-- ) {
				$results[] = $value;
			} else {
				break;
			}
		}

		return $results;
	}

	/**
	 * Get the total number of MLA Upload MIME Type objects
	 *
	 * @since 1.40
	 *
	 * @param	array	Query variables, e.g., from $_REQUEST
	 *
	 * @return	integer	Number of MLA Upload MIME Type objects
	 */
	public static function mla_count_upload_items( $request ) {
		$request = self::_prepare_upload_items_query( $request );
		$results = self::_execute_upload_items_query( $request );
		return count( $results );
	}

	/**
	 * Retrieve MLA Upload MIME Type objects for list table display
	 *
	 * @since 1.40
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		number of rows to skip over to reach desired page
	 * @param	int		number of rows on each page
	 *
	 * @return	array	MLA Upload MIME Type objects
	 */
	public static function mla_query_upload_items( $request, $offset, $count ) {
		$request = self::_prepare_upload_items_query( $request, $offset, $count );
		$results = self::_execute_upload_items_query( $request );
		return $results;
	}

	/**
	 * Tabulate MLA Upload MIME Type objects by view for list table display
	 *
	 * @since 1.40
	 *
	 * @param	string	keyword search criterion, optional
	 *
	 * @return	array	( 'singular' label, 'plural' label, 'count' of items )
	 */
	public static function mla_tabulate_upload_items( $s = '' ) {
		if ( empty( $s ) ) {
			$request = array( 'mla_upload_view' => 'all' );
		} else {
			$request = array( 's' => $s );
		}

		$items = self::mla_query_upload_items( $request, 0, 0 );

		$upload_items = array(
			'all' => array(
				'singular' => _x( 'All', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'All', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'active' => array(
				'singular' => _x( 'Active', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Active', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'inactive' => array(
				'singular' => _x( 'Inactive', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Inactive', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'core' => array(
				'singular' => _x( 'WordPress', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'WordPress', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'mla' => array(
				'singular' => _x( 'MLA', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'MLA', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
			'custom' => array(
				'singular' => _x( 'Custom', 'table_view_singular', 'media_library-assistant' ),
				'plural' => _x( 'Custom', 'table_view_plural', 'media_library-assistant' ),
				'count' => 0 ),
		);

		foreach ( $items as $value ) {
			$upload_items['all']['count']++;
			$value->disabled ? $upload_items['inactive']['count']++ : $upload_items['active']['count']++;
			$upload_items[ $value->source ]['count']++;
		}

		return $upload_items;
	}

	/**
	 * Icon types without MLA filtering
	 *
	 * @since 1.40
	 *
	 * @var	array	extension => ( core_icon_type )
	 */
	private static $mla_core_icon_types = NULL;

	/**
	 * Icon types with MLA filtering - basenames of files in the current icon directory
	 *
	 * @since 1.40
	 *
	 * @var	array	( icon_type => icon_image_uri )
	 */
	private static $mla_current_icon_types = NULL;

	/**
	 * In-memory representation of the Upload MIME Types
	 *
	 * @since 1.40
	 *
	 * @var	array	extension => ( post_ID, mime_type, core_type, mla_type, source, standard_source, disabled, description, icon_type, wp_icon_type, mla_icon_type, core_icon_type )
	 */
	private static $mla_upload_mime_templates = NULL;

	/**
	 * In-memory cache of the default Upload MIME Type descriptions
	 *
	 * @since 1.80
	 *
	 * @var	array	extension => description
	 */
	private static $mla_upload_mime_descriptions = NULL;

	/**
	 * Highest existing Upload MIME Type ID value
	 *
	 * @since 1.40
	 *
	 * @var	integer
	 */
	private static $mla_upload_mime_highest_ID = 0;

	/**
	 * Assemble the list of icon types without MLA filtering
	 *
	 * @since 1.40
	 *
	 * @return	boolean	Success (true) or failure (false) of the operation
	 */
	private static function _get_core_icon_types() {
		global $wp_filter;

		if ( NULL != self::$mla_core_icon_types ) {
			return true;
		}

		/*
		 * wp_ext2type will apply our filter in a special mode, initializing the list
		 */
		self::$disable_mla_filtering = true;
		$save_filters = $wp_filter['ext2type'];
		unset( $wp_filter['ext2type'] );
		add_filter( 'ext2type', 'MLAMime::mla_ext2type_filter', 0x7FFFFFFF, 1 );
		wp_ext2type( 'xxx' ); 
		$wp_filter['ext2type'] = $save_filters;
		self::$disable_mla_filtering = false;

		/*
		 * Rebuild the list as extension => type,
		 * Explode any entries with multiple extensions
		 */
		$standard_types = array ();
		foreach ( self::$mla_core_icon_types as $key => $extensions )
			foreach ( $extensions as $extension )
				$standard_types[ $extension ] = $key;
		ksort( $standard_types );
		self::$mla_core_icon_types = $standard_types;
		return true;
	}

	/**
	 * Assemble the list of icon types with MLA filtering
	 *
	 * @since 1.40
	 *
	 * @return	boolean	Success (true) or failure (false) of the operation
	 */
	private static function _get_current_icon_types() {
		if ( NULL != self::$mla_current_icon_types ) {
			return true;
		}

		/*
		 * Get the directories in reverse order, so earlier entries will overwrite later entries and win
		 */
		$icon_dir = apply_filters( 'icon_dir', ABSPATH . WPINC . '/images/crystal' );
		$icon_dir_uri = apply_filters( 'icon_dir_uri', includes_url('images/crystal') );
		$dirs = array_reverse( apply_filters( 'icon_dirs', array($icon_dir => $icon_dir_uri) ), true );

		self::$mla_current_icon_types = array();
		while ( $dirs ) {
			$keys = array_keys( $dirs );
			$dir = array_shift( $keys );
			$uri = array_shift( $dirs );

			if ( $dh = opendir($dir) ) {
				while ( false !== $file = readdir($dh) ) {
					$file = basename($file);
					if ( substr($file, 0, 1) == '.' ) {
						continue;
					}

					if ( !in_array(strtolower(substr($file, -4)), array('.png', '.gif', '.jpg') ) ) {
						if ( is_dir("$dir/$file") ) {
							$dirs["$dir/$file"] = "$uri/$file";
						}

						continue;
					}

					$name = substr( $file, 0, -4);
					self::$mla_current_icon_types[ $name ] = "$uri/$file";
				}

				closedir($dh);
			}
		}

		return true;
	}

	/**
	 * Retrieve a standard icon type, i.e., without MLA filtering
	 *
	 * @since 1.40
	 *
	 * @param	string	file extension
	 *
	 * @return	string	icon type for the requested extension; 'default' if extension not found
	 */
	public static function mla_get_core_icon_type( $extension ) {
		if ( self::_get_core_icon_types() ) {
			if ( isset( self::$mla_core_icon_types[ $extension ] ) ) {
				return self::$mla_core_icon_types[ $extension ];
			}
		}

		return 'default';
	}

	/**
	 * Get an attachment icon height and width
	 *
	 * @since 2.14
	 *
	 * @param	string	Icon Type, e.g., audio, video, spreadsheet
	 *
	 * @return	array	( width, height )
	 */
	public static function mla_get_icon_type_size( $icon_type ) {
		if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ENABLE_MLA_ICONS ) ) {
			return array( 'width' => 64, 'height' => 64 );
		}
		
		$icon_info = NULL;
		$icon_dir = apply_filters( 'icon_dir', ABSPATH . WPINC . '/images/media' );
		if ( false === ( $dh = @opendir( $icon_dir ) ) ) {
			$icon_dir = apply_filters( 'icon_dir', ABSPATH . WPINC . '/images/crystal' );
			$dh = opendir( $icon_dir );
		}

		if ( $dh ) {
			while ( false !== $icon_file = readdir( $dh ) ) {
				$icon_file = basename( $icon_file );
				if ( substr( $icon_file, 0, 1 ) == '.' ) {
					continue;
				}
				
				$file_info = pathinfo( $icon_file );
				if ( in_array( strtolower( $file_info['extension'] ), array('png', 'gif', 'jpg') ) ) {
					if ( $icon_type == $file_info['filename'] ) {
						$icon_info = $file_info;
						break;
					} elseif ( 'default' == $file_info['filename'] ) {
						$icon_info = $file_info;
					}
				}
			}

			closedir( $dh );
		}
	
		if ( is_null( $icon_info ) ) {
			return array( 'width' => 64, 'height' => 64 );
		}
		
		$image_info = getimagesize( $icon_dir . '/' . $icon_info['filename'] . '.' . $icon_info['extension'] );
		if ( $image_info ) {
			if ( isset( $image_info[0] ) ) {
				$image_info['width'] = $image_info[0];
			} else {
				$image_info['width'] = 0;
			}

			if ( isset( $image_info[1] ) ) {
				$image_info['height'] = $image_info[1];
			} else {
				$image_info['height'] = 0;
			}
		}

		return $image_info;
	}

	/**
	 * Get an HTML img element representing an attachment icon
	 *
	 * @since 1.40
	 *
	 * @param	string	Icon Type, e.g., audio, video, spreadsheet
	 * @param	array	( width, height ) optional image size, default (64, 64).
	 *
	 * @return string HTML img element or empty string on failure.
	 */
	public static function mla_get_icon_type_image( $icon_type, $size = NULL ) {
		$icon_file =  wp_mime_type_icon( $icon_type );

		if (is_array( $size ) ) {
			$width = $size[0];
			$height = $size[1];
		} else {
			@list($width, $height) = getimagesize( $icon_file );
		}

		$hwstring = image_hwstring($width, $height);
		$size = $width . 'x' . $height;
		$default_attr = array(
			'src'	=> $icon_file,
			'class'	=> "attachment-$size",
			'alt' => $icon_type . ' ' . __( 'icon', 'media-library-assistant' )
		);

		$attr = array_map( 'esc_attr', $default_attr );
		$html = rtrim("<img $hwstring");
		foreach ( $attr as $name => $value ) {
			$html .= " $name=" . '"' . $value . '"';
		}
		$html .= ' />';

		return $html;
	}

	/**
	 * Get an array of current Icon Type names
	 *
	 * @since 1.40
	 *
	 * @return array ( icon_type ) or false on failure.
	 */
	public static function mla_get_current_icon_types() {
		if ( self::_get_current_icon_types() ) {
			return array_keys( self::$mla_current_icon_types );
		}

		return false;
	}

	/**
	 * Assemble the in-memory representation of the Upload MIME Types 
	 *
	 * @since 1.40
	 *
	 * @param	boolean	Force a reload/recalculation of types
	 * @return	boolean	Success (true) or failure (false) of the operation
	 */
	private static function _get_upload_mime_templates( $force_refresh = false ) {
		self::_get_core_icon_types();
		self::_get_current_icon_types();

		if ( false == $force_refresh && NULL != self::$mla_upload_mime_templates ) {
			return true;
		}

		/*
		 * Find the WordPress-standard (unfiltered) extensions
		 */
		global $wp_filter;
		if ( isset( $wp_filter['mime_types'] ) ) {
			$save_filters = $wp_filter['mime_types'];
			unset( $wp_filter['mime_types'] );
			$core_types = wp_get_mime_types();
			$wp_filter['mime_types'] = $save_filters;
		} else {
			$core_types = wp_get_mime_types();
		}

		/*
		 * If this is the first time MLA Upload support is invoked, match to the 
		 * filter-enhanced extensions, retain anything new as a custom type.
		 */
		$custom_types = array();
		$mla_upload_mimes = MLACore::mla_get_option( MLACoreOptions::MLA_UPLOAD_MIMES );
		if ( is_array( $mla_upload_mimes ) ) {
			$first_time_called = false;
			$custom_types = $mla_upload_mimes['custom'];
		} else {
			$first_time_called = true;
			$mla_upload_mimes = array ( 'custom' => array(), 'disabled' => array(), 'description' => array(), 'icon_type' => array() );
			self::$disable_mla_filtering = true;
			foreach ( get_allowed_mime_types() as $key => $value ) {
				if ( ! isset( $core_types[ $key ]) ) {
					$custom_types[ $key ] = $value;
				}
			}

			self::$disable_mla_filtering = false;
		}

		/*
		 * Explode any entries with multiple extensions
		 */
		foreach ( $core_types as $key => $value )
			if ( false !== strpos( $key, '|' ) ) {
				unset( $core_types[ $key ] );
				$extensions = explode( '|', $key );
				foreach ( $extensions as $extension )
					$core_types[ $extension ] = $value;
			}

		foreach ( $custom_types as $key => $value )
			if ( false !== strpos( $key, '|' ) ) {
				unset( $custom_types[ $key ] );
				$extensions = explode( '|', $key );
				foreach ( $extensions as $extension )
					$custom_types[ $extension ] = $value;
			}

		self::$mla_upload_mime_templates = array();
		self::$mla_upload_mime_highest_ID = 0;

		/*
		 * Start with the MLA extensions, initialized to an inactive state
		 * Save the descriptions for use in _put_upload_mime_types()
		 */
		self::$mla_upload_mime_descriptions = array();
		$template_array = MLACore::mla_load_template( 'mla-default-mime-types.tpl' );
		if ( isset( $template_array['mla-mime-types'] ) ) {
			$mla_mime_types = preg_split('/[\r\n]+/', $template_array['mla-mime-types'] );
			$line_number = 0;
			foreach ( $mla_mime_types as $mla_type ) {
				$line_number++;
				// Ignore blank lines
				if ( empty( $mla_type ) ) {
					continue;
				}
				
				$array = explode(',', $mla_type );

				// Bypass damaged entries
				if ( 5 > count( $array ) ) {
					MLACore::mla_debug_add( __LINE__ . " _get_upload_mime_templates mla-default-mime-types.tpl section mla-mime-types( {$line_number} '{$mla_type}' ) \$array = " . var_export( $array, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
					continue;
				}

				$key = strtolower( $array[0] );
				self::$mla_upload_mime_descriptions[ $key ] = $array[4];
				self::$mla_upload_mime_templates[ $key ] = array(
					'post_ID' => ++self::$mla_upload_mime_highest_ID,
					'mime_type' => $array[1],
					'core_type' => '',
					'mla_type' => $array[1],
					'source' => 'mla',
					'standard_source' => 'mla',
					'disabled' => true,
					'description' => $array[4],
					'icon_type' => $array[2],
					'wp_icon_type' => $array[2],
					'mla_icon_type' => $array[3],
					'core_icon_type' => self::mla_get_core_icon_type( $array[0] )
				);

				if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ENABLE_MLA_ICONS ) ) {
					self::$mla_upload_mime_templates[ $key ]['icon_type'] = self::$mla_upload_mime_templates[ $key ]['mla_icon_type'];
				}
			}
		}

		/*
		 * Add the WordPress-standard (unfiltered) extensions, initialized to an active state
		 */
		foreach ( $core_types as $key => $value ) {
			$key = strtolower( $key );
			if ( isset( self::$mla_upload_mime_templates[ $key ] ) ) {
				$post_ID = self::$mla_upload_mime_templates[ $key ]['post_ID'];
				$mla_type = self::$mla_upload_mime_templates[ $key ]['mla_type'];
				$description = self::$mla_upload_mime_templates[ $key ]['description'];
				$icon_type = self::$mla_upload_mime_templates[ $key ]['icon_type'];
				$wp_icon_type = self::$mla_upload_mime_templates[ $key ]['wp_icon_type'];
				$mla_icon_type = self::$mla_upload_mime_templates[ $key ]['mla_icon_type'];
				$core_icon_type = self::$mla_upload_mime_templates[ $key ]['core_icon_type'];
			} else {
				$post_ID = ++self::$mla_upload_mime_highest_ID;
				$mla_type = '';
				$description = '';

				$icon_type = self::mla_get_core_icon_type( $key );

				$wp_icon_type = $icon_type;
				$mla_icon_type = $icon_type;
				$core_icon_type = $icon_type;
			}

			self::$mla_upload_mime_templates[ $key ] = array(
				'post_ID' => $post_ID,
				'mime_type' => $value,
				'core_type' => $value,
				'mla_type' => $mla_type,
				'source' => 'core',
				'standard_source' => 'core',
				'disabled' => false,
				'description' => $description ,
				'icon_type' => $icon_type,
				'wp_icon_type' => $wp_icon_type,
				'mla_icon_type' => $mla_icon_type,
				'core_icon_type' => $core_icon_type
			);

			if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ENABLE_MLA_ICONS ) ) {
				self::$mla_upload_mime_templates[ $key ]['icon_type'] = self::$mla_upload_mime_templates[ $key ]['mla_icon_type'];
			}
		}

		/*
		 * Add the user-defined custom types
		 */
		foreach ( $custom_types as $key => $value ) {
			$key = strtolower( $key );
			if ( isset( self::$mla_upload_mime_templates[ $key ] ) ) {
				extract( self::$mla_upload_mime_templates[ $key ] );
				/*
				 * Make sure it's really custom
				 */
				if ( ( 'core' == $source && $value == $core_type ) || ( 'mla' == $source && $value == $mla_type ) ) {
					continue;
					 }
			} else { // existing type
				$core_type = '';
				$mla_type = '';
				$standard_source = '';
			} // brand new type

			if ( NULL == $icon_type = wp_ext2type( $key ) ) {
				$icon_type = 'default';
			}

			self::$mla_upload_mime_templates[ $key ] = array(
				'post_ID' => ++self::$mla_upload_mime_highest_ID,
				'mime_type' => $value,
				'core_type' => $core_type,
				'mla_type' => $mla_type,
				'source' => 'custom',
				'standard_source' => $standard_source,
				'disabled' => false,
				'description' => '',
				'icon_type' => $icon_type,
				'wp_icon_type' => $icon_type,
				'mla_icon_type' => $icon_type,
				'core_icon_type' => self::mla_get_core_icon_type( $key )
			);
		}

		if ( $first_time_called ) {
			self::_put_upload_mime_templates();
			return true;
		}

		/*
		 * Apply the current settings, if any
		 */
		foreach ( self::$mla_upload_mime_templates as $key => $value ) {
			$default_description = isset( self::$mla_upload_mime_descriptions[ $key ] ) ? self::$mla_upload_mime_descriptions[ $key ] : '';
			self::$mla_upload_mime_templates[ $key ]['disabled'] = isset( $mla_upload_mimes['disabled'][ $key ] );
			self::$mla_upload_mime_templates[ $key ]['description'] = isset( $mla_upload_mimes['description'][ $key ] ) ? $mla_upload_mimes['description'][ $key ] : $default_description;
			if ( isset( $mla_upload_mimes['icon_type'][ $key ] ) ) {
				self::$mla_upload_mime_templates[ $key ]['icon_type'] = $mla_upload_mimes['icon_type'][ $key ];
			}
		}

		return true;
	}

	/**
	 * Store the options portion of the Upload MIME Types 
	 *
	 * @since 1.40
	 *
	 * @return	boolean	Success (true) or failure (false) of the operation
	 */
	private static function _put_upload_mime_templates() {
		$mla_upload_mimes = array ( 'custom' => array(), 'disabled' => array(), 'description' => array(), 'icon_type' => array() );

		foreach ( self::$mla_upload_mime_templates as $key => $value ) {
			if ( 'custom' == $value['source'] ) {
				$mla_upload_mimes['custom'][ $key ] =  $value['mime_type'];
			}

			if ( $value['disabled'] ) {
				$mla_upload_mimes['disabled'][ $key ] =  true;
			}

			$description = trim( $value['description'] );
			if ( ! empty( $description ) && ( $description != self::$mla_upload_mime_descriptions[ $key ] ) ) {
				$mla_upload_mimes['description'][ $key ] =  $description;
			}

			if ( $value['icon_type'] != $value['core_icon_type'] ) {
				$mla_upload_mimes['icon_type'][ $key ] =  $value['icon_type'];
			}
		}

		MLACore::mla_update_option( MLACoreOptions::MLA_UPLOAD_MIMES, $mla_upload_mimes );
		return true;
	}

	/**
	 * Add an MLA Upload MIME Type object
	 *
	 * @since 1.40
	 *
	 * @param	array	Query variables for a single object, including slug
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	public static function mla_add_upload_mime( $request ) {
		if ( self::_get_upload_mime_templates() ) {
			$errors = '';
		} else {
			return array(
				'message' => __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Cannot load Upload MIME Types', 'media-library-assistant' ),
				'body' => ''
			);
		}

		$messages = '';

		/*
		 * Sanitize slug value
		 */
		if ( empty( $request['slug'] ) ) {
			$errors .= '<br>' . __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Extension is required', 'media-library-assistant' );
		} else {
			$slug = pathinfo( 'X.' . strtolower( trim( $request['slug'] ) ), PATHINFO_EXTENSION );
			if ( $slug != $request['slug'] ) {
				/* translators: 1: element name 2: bad_value 3: good_value */
				$messages .= sprintf( __( '<br>' . 'Changing %1$s "%2$s" to valid value "%3$s"', 'media-library-assistant' ), __( 'Extension', 'media-library-assistant' ), $request['slug'], $slug );
			}

			/*
			 * Make sure new slug is unique
			 */
			if ( isset( self::$mla_upload_mime_templates[ $slug ] ) ) {
				/* translators: 1: ERROR tag 2: slug */
				$errors .= '<br>' . sprintf( __( '%1$s: Could not add extension "%2$s"; value already exists', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $slug );
			}
		}

		/*
		 * Validate mime_type
		 */
		if ( empty( $request['mime_type'] ) ) {
			$errors .= '<br>' . __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'MIME type is required', 'media-library-assistant' );
		} else {
			$clean_mime_type = sanitize_mime_type( $request['mime_type'] );
			if ( $clean_mime_type != $request['mime_type'] ) {
				/* translators: 1: ERROR tag 2: clean_mime_type */
				$errors .= '<br>' . sprintf( __( '%1$s: Bad MIME type; try "%2$s"', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $clean_mime_type );
			}
		}

		if ( ! empty( $errors ) ) {
			return array(
				'message' => substr( $errors . $messages, 4),
				'body' => ''
			);
		}

		if ( '.none.' == $request['icon_type'] ) {
			if ( NULL == $icon_type = wp_ext2type( $slug ) ) {
				$icon_type = 'default';
			}
		} else {
			$icon_type = $request['icon_type'];
		}

		$new_type = array();
		$new_type['post_ID'] = ++self::$mla_upload_mime_highest_ID;
		$new_type['mime_type'] = $clean_mime_type;
		$new_type['core_type'] = '';
		$new_type['mla_type'] = '';
		$new_type['source'] = 'custom';
		$new_type['standard_source'] = '';
		$new_type['disabled'] = isset( $request['disabled'] ) ? $request['disabled'] : false;
		$new_type['description'] = isset( $request['description'] ) ? sanitize_text_field( $request['description'] ) : '';
		$new_type['icon_type'] = $icon_type;
		$new_type['wp_icon_type'] = $icon_type;
		$new_type['mla_icon_type'] = $icon_type;
		$new_type['core_icon_type'] = self::mla_get_core_icon_type( $slug );

		self::$mla_upload_mime_templates[ $slug ] = $new_type;
		if ( self::_put_upload_mime_templates() ) {
			return array(
				/* translators: 1: slug */
				'message' => substr( $messages . '<br>' . sprintf( __( 'Upload MIME Type "%1$s"; added', 'media-library-assistant' ), $slug ), 4),
				'body' => ''
			);
		}

		return array(
			'message' => __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Cannot update Upload MIME Types', 'media-library-assistant' ),
			'body' => ''
		);
	}

	/**
	 * Update an MLA Upload MIME Type object
	 *
	 * @since 1.40
	 *
	 * @param	array	Query variables for new object values, including optional original_slug
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	public static function mla_update_upload_mime( $request = NULL ) {
		if ( self::_get_upload_mime_templates() ) {
			$errors = '';
		} else {
			return array(
				'message' => __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Cannot load Upload MIME Types', 'media-library-assistant' ),
				'body' => ''
			);
		}

		/*
		 * $request = NULL is a call from MLASettings::_version_upgrade
		 */
		if ( NULL == $request ) {
			self::_put_upload_mime_templates();
			return;
		}

		$messages = '';
		$slug = pathinfo( 'X.' . strtolower( trim( $request['slug'] ) ), PATHINFO_EXTENSION );
		$original_slug = isset( $request['original_slug'] ) ? $request['original_slug'] : $slug;
		unset( $request['original_slug'] );

		if ( isset( self::$mla_upload_mime_templates[ $original_slug ] ) ) {
			$original_type = self::$mla_upload_mime_templates[ $original_slug ];
		} else {
			$original_type = array(
				'post_ID' => 0,
				'mime_type' => '',
				'core_type' => '',
				'mla_type' => '',
				'source' => '',
				'standard_source' => '',
				'disabled' => false,
				'description' => '',
				'wp_icon_type' => '',
				'mla_icon_type' => '',
				'icon_type' => '',
				'core_icon_type' => ''
			);
		}

		/*
		 * Validate changed slug value
		 */
		if ( $slug != $original_slug ) {
			if ( $slug != $request['slug'] ) {
				/* translators: 1: element name 2: bad_value 3: good_value */
				$messages .= sprintf( __( '<br>' . 'Changing new %1$s "%2$s" to valid value "%3$s"', 'media-library-assistant' ), __( 'Extension', 'media-library-assistant' ), $request['slug'], $slug );
			}

			/*
			 * Make sure new slug is unique
			 */
			if ( isset( self::$mla_upload_mime_templates[ $slug ] ) ) {
				/* translators: 1: ERROR tag 2: slug */
				$errors .= '<br>' . sprintf( __( '%1$s: Could not add new extension "%2$s"; value already exists', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $slug );
			} else {
				/* translators: 1: element name 2: old_value 3: new_value */
				$messages .= sprintf( '<br>' . __( 'Changing %1$s from "%2$s" to "%3$s"', 'media-library-assistant' ), __( 'Extension', 'media-library-assistant' ), $original_slug, $slug );
			}

			/*
			 * A new extension cannot have an $original_type
			 */
			$original_type = array(
				'post_ID' => 0,
				'mime_type' => '',
				'core_type' => '',
				'mla_type' => '',
				'source' => '',
				'standard_source' => '',
				'disabled' => false,
				'description' => '',
				'icon_type' => '',
				'wp_icon_type' => '',
				'mla_icon_type' => '',
				'core_icon_type' => ''
			);
		}

		/*
		 * Validate mime_type
		 */
		if ( empty( $request['mime_type'] ) ) {
			$clean_mime_type = $original_type['mime_type'];
		} else {
			$clean_mime_type = sanitize_mime_type( $request['mime_type'] );
			if ( $clean_mime_type != $request['mime_type'] ) {
				/* translators: 1: ERROR tag 2: clean_mime_type */
				$errors .= '<br>' . sprintf( __( '%1$s: Bad MIME type; try "%2$s"', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $clean_mime_type );
			}
		}

		if ( ! empty( $errors ) ) {
			return array(
				'message' => substr( $errors . $messages, 4),
				'body' => ''
			);
		}

		$new_type = array();
		if ( 0 == $original_type['post_ID'] ) {
			$new_type['post_ID'] = ++self::$mla_upload_mime_highest_ID;
		} else {
			$new_type['post_ID'] = $original_type['post_ID'];
		}

		$new_type['mime_type'] = $clean_mime_type;
		$new_type['core_type'] = $original_type['core_type'];
		$new_type['mla_type'] = $original_type['mla_type'];

		/*
		 * Determine the source
		 */
		if ( 'core' == $original_type['standard_source'] && $clean_mime_type == $original_type['core_type'] ) {
			$new_type['source'] = 'core';
		} elseif ( 'mla' == $original_type['standard_source'] && $clean_mime_type == $original_type['mla_type'] ) {
			$new_type['source'] = 'mla';
		} else {
			$new_type['source'] = 'custom';
		}

		/*
		 * Determine new icon types
		 */
		$new_type['core_icon_type'] = self::mla_get_core_icon_type( $slug );

		if ( isset( $request['icon_type'] ) ) {
			$new_type['icon_type'] = '.none.' == $request['icon_type'] ? 'default' : $request['icon_type'];
		} elseif ( ! empty( $original_type['icon_type'] ) ) {
			$new_type['icon_type'] = $original_type['icon_type'];
		} else {
			$new_type['icon_type'] = $new_type['core_icon_type'];
		}

		if ( ! empty( $original_type['wp_icon_type'] ) ) {
			$new_type['wp_icon_type'] = $original_type['wp_icon_type'];
		} else {
			$new_type['wp_icon_type'] = $new_type['icon_type'];
		}

		if ( ! empty( $original_type['mla_icon_type'] ) ) {
			$new_type['mla_icon_type'] = $original_type['mla_icon_type'];
		} else {
			$new_type['mla_icon_type'] = $new_type['icon_type'];
		}

		$new_type['standard_source'] = $original_type['standard_source'];
		$new_type['disabled'] = isset( $request['disabled'] ) ? $request['disabled'] : $original_type['disabled'];
		$new_type['description'] = isset( $request['description'] ) ? sanitize_text_field( $request['description'] ) : $original_type['description'];

		if ( ( $slug == $original_slug ) && ( self::$mla_upload_mime_templates[ $slug ] == $new_type ) ) {
			return array(
				/* translators: 1: slug */
				'message' => substr( $messages . '<br>' . sprintf( __( 'Edit type "%1$s"; no changes detected', 'media-library-assistant' ), $slug ), 4),
				'body' => ''
			);
		}

		self::$mla_upload_mime_templates[ $slug ] = $new_type;

		if ( $slug != $original_slug ) {
			unset( self::$mla_upload_mime_templates[ $original_slug ] );
		}

		if ( self::_put_upload_mime_templates() ) {
			return array(
				/* translators: 1: slug */
				'message' => substr( $messages . '<br>' . sprintf( __( 'Edit type "%1$s"; updated', 'media-library-assistant' ), $slug ), 4),
				'body' => ''
			);
		}

		return array(
			'message' => __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Cannot update Upload MIME Types', 'media-library-assistant' ),
			'body' => ''
		);
	}

	/**
	 * Retrieve an MLA Upload MIME Type slug given a post_ID
	 *
	 * @since 1.40
	 *
	 * @param	integer	MLA Upload MIME Type post_ID
	 *
	 * @return	mixed	string with slug of the requested object; false if object not found
	 */
	public static function mla_get_upload_mime_slug( $post_ID ) {
		if ( self::_get_upload_mime_templates() ) {
			foreach ( self::$mla_upload_mime_templates as $slug => $value ) {
				if ( $post_ID == $value['post_ID'] ) {
					return $slug;
				}
			}
		}

		return false;
	}

	/**
	 * Retrieve an MLA Upload MIME Type object
	 *
	 * @since 1.40
	 *
	 * @param	string	MLA Upload MIME Type slug
	 *
	 * @return	mixed	Array of elements, including slug, for the requested object; false if object not found
	 */
	public static function mla_get_upload_mime( $slug ) {
		if ( self::_get_upload_mime_templates() ) {
			if ( isset( self::$mla_upload_mime_templates[ $slug ] ) ) {
				$matched_value = self::$mla_upload_mime_templates[ $slug ];
				$matched_value['slug'] = $slug;
				return $matched_value;
			}
		}

		return false;
	}

	/**
	 * Delete an MLA Upload MIME Type object
	 *
	 * @since 1.40
	 *
	 * @param	string	MLA Upload MIME Type slug
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	public static function mla_delete_upload_mime( $slug ) {
		if ( self::_get_upload_mime_templates() ) {
			if ( isset( self::$mla_upload_mime_templates[ $slug ] ) ) {
				unset( self::$mla_upload_mime_templates[ $slug ] );

				if ( self::_put_upload_mime_templates() ) {
					self::_get_upload_mime_templates( true );

					if ( isset( self::$mla_upload_mime_templates[ $slug ] ) ) {
						return array(
							/* translators: 1: slug */
							'message' => sprintf( __( 'Upload MIME Type "%1$s"; reverted to standard', 'media-library-assistant' ), $slug ),
							'body' => ''
						);
					} else {
						return array(
							/* translators: 1: slug */
							'message' => sprintf( __( 'Upload MIME Type "%1$s"; deleted', 'media-library-assistant' ), $slug ),
							'body' => ''
						);
					}
				} else {
					return array(
						'message' => __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Cannot update Upload MIME Types', 'media-library-assistant' ),
						'body' => ''
					);
				}
			}
		}

		return array(
			/* translators: 1: ERROR tag 2: slug */
			'message' => sprintf( __( '%1$s: Did not find Upload type "%2$s"', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $slug ),
			'body' => ''
		);
	}

	/**
	 * In-memory representation of the (read-only) Optional Upload MIME Types
	 *
	 * @since 1.40
	 *
	 * @var	array	( ID, slug, mime_type, core_type, mla_type, description )
	 */
	private static $mla_optional_upload_mime_templates = NULL;

	/**
	 * Sanitize and expand Optional Upload MIME Type query arguments from request variables
	 *
	 * @since 1.40
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		Optional number of rows (default 0) to skip over to reach desired page
	 * @param	int		Optional number of rows on each page (0 = all rows, default)
	 *
	 * @return	array	revised arguments suitable for query
	 */
	private static function _prepare_optional_upload_items_query( $raw_request, $offset = 0, $count = 0 ) {
		/*
		 * Go through the $raw_request, take only the arguments that are used in the query and
		 * sanitize or validate them.
		 */
		if ( ! is_array( $raw_request ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			error_log( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLAMime::_prepare_optional_upload_items_query', var_export( $raw_request, true ) ), 0 );
			return NULL;
		}

		$clean_request = array (
			'orderby' => 'slug',
			'order' => 'ASC',
			's' => ''
		);

		foreach ( $raw_request as $key => $value ) {
			switch ( $key ) {
				case 'orderby':
					if ( 'none' == $value ) {
						$clean_request[ $key ] = $value;
					} else {
						$sortable_columns = MLA_Upload_Optional_List_Table::mla_get_sortable_columns();
						foreach ($sortable_columns as $sort_key => $sort_value ) {
							if ( $value == $sort_value[0] ) {
								$clean_request[ $key ] = $value;
								break;
							}
						} // foreach
					}
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
				/*
				 * ['s'] - Search Media by one or more keywords
				 */
				case 's':
					$clean_request[ $key ] = stripslashes( trim( $value ) );
					break;
				default:
					// ignore anything else in $_REQUEST
			} // switch $key
		} // foreach $raw_request

		/*
		 * Ignore incoming paged value; use offset and count instead
		 */
		if ( ( (int) $count ) > 0 ) {
			$clean_request['offset'] = $offset;
			$clean_request['posts_per_page'] = $count;
		}

		return $clean_request;
	}

	/**
	 * Execute an Optional Upload MIME Types query
	 *
	 * @since 1.40
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 *
	 * @return	array	query results; array of MLA Optional Upload MIME Type objects
	 */
	private static function _execute_optional_upload_items_query( $request ) {
		if ( ! self::_get_optional_upload_mime_templates() ) {
			return array ();
		}

		/*
		 * Sort and filter the list
		 */
		$keyword = isset( $request['s'] ) ? $request['s'] : '';
		$extension = 0 === strpos( $keyword, '.' ) ? substr( $keyword, 1) : false;
		$sorted_types = array();

		foreach ( self::$mla_optional_upload_mime_templates as $ID => $value ) {
			if ( ! empty( $keyword ) ) {
				if ( false === $extension ) {
					$found  = false !== stripos( $value['slug'], $keyword );
					$found |= false !== stripos( $value['mime_type'], $keyword );
					$found |= false !== stripos( $value['description'], $keyword );
				} else {
					$found  = false !== stripos( $value['slug'], $extension );
				}

				if ( ! $found ) {
					continue;
				}
			}

			switch ( $request['orderby'] ) {
				case 'slug':
					$sorted_types[ $value['slug'] . $ID ] = (object) $value;
					break;
				case 'mime_type':
					$sorted_types[ ( empty( $value['mime_type'] ) ? chr(1) : $value['mime_type'] ) . $ID ] = (object) $value;
					break;
				case 'core_type':
					$sorted_types[ ( empty( $value['core_type'] ) ? chr(1) : $value['core_type'] ) . $ID ] = (object) $value;
					break;
				case 'mla_type':
					$sorted_types[ ( empty( $value['mla_type'] ) ? chr(1) : $value['mla_type'] ) . $ID ] = (object) $value;
					break;
				case 'description':
					$sorted_types[ ( empty( $value['description'] ) ? chr(1) : $value['description'] ) . $ID ] = (object) $value;
					break;
				default:
					$sorted_types[ $value['slug'] . $ID ] = (object) $value;
					break;
			} //orderby
		}
		ksort( $sorted_types );

		if ( 'DESC' == $request['order'] ) {
			$sorted_types = array_reverse( $sorted_types, true );
		}

		/*
		 * Paginate the sorted list
		 */
		$results = array();
		$offset = isset( $request['offset'] ) ? $request['offset'] : 0;
		$count = isset( $request['posts_per_page'] ) ? $request['posts_per_page'] : -1;
		foreach ( $sorted_types as $value ) {
			if ( $offset ) {
				$offset--;
			} elseif ( $count-- ) {
				$results[] = $value;
			} else {
				break;
			}
		}

		return $results;
	}

	/**
	 * Get the total number of MLA Upload MIME Type objects
	 *
	 * @since 1.40
	 *
	 * @param	array	Query variables, e.g., from $_REQUEST
	 *
	 * @return	integer	Number of MLA Upload MIME Type objects
	 */
	public static function mla_count_optional_upload_items( $request ) {
		$request = self::_prepare_optional_upload_items_query( $request );
		$results = self::_execute_optional_upload_items_query( $request );
		return count( $results );
	}

	/**
	 * Retrieve MLA Upload MIME Type objects for list table display
	 *
	 * @since 1.40
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		number of rows to skip over to reach desired page
	 * @param	int		number of rows on each page
	 *
	 * @return	array	MLA Upload MIME Type objects
	 */
	public static function mla_query_optional_upload_items( $request, $offset, $count ) {
		$request = self::_prepare_optional_upload_items_query( $request, $offset, $count );
		$results = self::_execute_optional_upload_items_query( $request );
		return $results;
	}

	/**
	 * Assemble the in-memory representation of the (read-only) Optional Upload MIME Types 
	 *
	 * @since 1.40
	 *
	 * @return	boolean	Success (true) or failure (false) of the operation
	 */
	private static function _get_optional_upload_mime_templates() {
		if ( NULL != self::$mla_optional_upload_mime_templates ) {
			return true;
		}

		self::$mla_optional_upload_mime_templates = array ();
		$template_array = MLACore::mla_load_template( 'mla-default-mime-types.tpl' );
		if ( isset( $template_array['mla-optional-mime-types'] ) ) {
			$mla_mime_types = preg_split('/[\r\n]+/', $template_array['mla-optional-mime-types'] );

			$ID = 0;
			foreach ( $mla_mime_types as $mla_type ) {
				// Ignore blank lines
				if ( empty( $mla_type ) ) {
					continue;
				}
				
				$array = explode(',', $mla_type );

				// Bypass damaged entries
				if ( 3 > count( $array ) ) {
					MLACore::mla_debug_add( __LINE__ . " _get_upload_mime_templates mla-default-mime-types.tpl section mla-optional-mime-types( {$ID} '{$mla_type}' ) \$array = " . var_export( $array, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
					continue;
				}

				$slug = $array[0];
				if ( $matched_type = self::mla_get_upload_mime( $slug ) ) {
					$core_type = $matched_type['core_type'];
					$mla_type = $matched_type['mla_type'];
				} else {
					$core_type = '';
					$mla_type = '';
				}

				self::$mla_optional_upload_mime_templates[ ++$ID ] = array(
					'ID' => $ID,
					'slug' => $slug,
					'mime_type' => $array[1],
					'core_type' => $core_type,
					'mla_type' => $mla_type,
					'description' => $array[2]
				);
			}
		}

		return true;
	}

	/**
	 * Retrieve an MLA Optional Upload MIME Type given an ID
	 *
	 * @since 1.40
	 *
	 * @param	integer	MLA Optional Upload MIME Type ID
	 *
	 * @return	mixed	the requested object; false if object not found
	 */
	public static function mla_get_optional_upload_mime( $ID ) {
		if ( self::_get_optional_upload_mime_templates() ) {
			if ( isset( self::$mla_optional_upload_mime_templates[ $ID ] ) ) {
					return self::$mla_optional_upload_mime_templates[ $ID ];
			}
		}

		return false;
	}
} //Class MLAMime
?>