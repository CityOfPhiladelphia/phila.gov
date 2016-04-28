<?php
/**
 * Media Library Assistant extended List Table class
 *
 * @package Media Library Assistant
 * @since 0.1
 */

/* 
 * The WP_List_Table class isn't automatically available to plugins
 */
if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class MLA (Media Library Assistant) List Table implements the "Assistant" admin submenu
 *
 * Extends the core WP_List_Table class.
 *
 * @package Media Library Assistant
 * @since 0.1
 */
class MLA_List_Table extends WP_List_Table {
	/**
	 * True if the current view is "Unattached"
	 *
	 * Declaration added in MLA v2.11 for WP 4.2 compatibility.
	 *
	 * @since 0.1
	 *
	 * @var	int
	 */
	private $detached;

	/**
	 * True if the current view is "Attached"
	 *
	 * @since 2.11
	 *
	 * @var	int
	 */
	private $attached;

	/**
	 * True if the current view is "Trash"
	 *
	 * Declaration added in MLA v2.11 for WP 4.2 compatibility.
	 *
	 * @since 0.1
	 *
	 * @var	int
	 */
	private $is_trash;

	/*
	 * These variables are used to assign row_actions to exactly one visible column
	 */

	/**
	 * Records assignment of row-level actions to a table row
	 *
	 * Set to the current Post-ID when row-level actions are output for the row.
	 *
	 * @since 0.1
	 *
	 * @var	int
	 */
	protected $rollover_id = 0;

	/**
	 * Currently hidden columns
	 *
	 * Records hidden columns so row-level actions are not assigned to them.
	 *
	 * @since 0.1
	 *
	 * @var	array
	 */
	protected $currently_hidden = array();

	/**
	 * The WPML_List_table support object, if required
	 *
	 * @since 2.11
	 *
	 * @var	object
	 */
	protected $mla_wpml_table = NULL;

	/*
	 * The $default_columns, $default_hidden_columns, and $default_sortable_columns
	 * arrays define the "Media/Assistant" table columns. The copies here are Compatibility
	 * shims for the real variables in MLAQuery.
	 */

	/**
	 * Table column definitions
	 *
	 * @since 0.1
	 *
	 * @var	array
	 */
	protected static $default_columns = array();

	/**
	 * Default values for hidden columns
	 *
	 * @since 0.1
	 *
	 * @var	array
	 */
	protected static $default_hidden_columns = array();

	/**
	 * Sortable column definitions
	 *
	 * @since 0.1
	 *
	 * @var	array
	 */
	protected static $default_sortable_columns = array();

	/**
	 * Get MIME types with one or more attachments for view preparation
	 *
	 * Modeled after get_available_post_mime_types in wp-admin/includes/post.php,
	 * but uses the output of wp_count_attachments() as input.
	 *
	 * @since 0.1
	 *
	 * @param	array	Number of posts for each MIME type
	 *
	 * @return	array	Mime type names
	 */
	protected static function _avail_mime_types( $num_posts ) {
		$available = array();

		foreach ( $num_posts as $mime_type => $number ) {
			if ( ( $number > 0 ) && ( $mime_type <> 'trash' ) ) {
				$available[] = $mime_type;
			}
		}

		return $available;
	}

	/**
	 * Get dropdown box of terms to filter by, if available
	 *
	 * @since 1.20
	 *
	 * @param	integer	currently selected term_id || zero (default)
	 * @param	array	additional wp_dropdown_categories options; default empty
	 *
	 * @return	string	HTML markup for dropdown box
	 */
	public static function mla_get_taxonomy_filter_dropdown( $selected = 0, $dropdown_options = array() ) {
		$dropdown = '';
		$tax_filter =  MLACore::mla_taxonomy_support('', 'filter');

		if ( ( '' != $tax_filter ) && ( is_object_in_taxonomy( 'attachment', $tax_filter ) ) ) {
			$tax_object = get_taxonomy( $tax_filter );
			$dropdown_options = array_merge( array(
				'show_option_all' => __( 'All', 'media-library-assistant' ) . ' ' . $tax_object->labels->name,
				'show_option_none' => __( 'No', 'media-library-assistant' ) . ' ' . $tax_object->labels->name,
				'orderby' => 'name',
				'order' => 'ASC',
				'show_count' => false,
				'hide_empty' => false,
				'child_of' => 0,
				'exclude' => '',
				// 'exclude_tree => '', 
				'echo' => true,
				'depth' => MLACore::mla_get_option( MLACoreOptions::MLA_TAXONOMY_FILTER_DEPTH ),
				'tab_index' => 0,
				'name' => 'mla_filter_term',
				'id' => 'name',
				'class' => 'postform',
				'selected' => $selected,
				'hierarchical' => true,
				'pad_counts' => false,
				'taxonomy' => $tax_filter,
				'hide_if_empty' => false 
			), $dropdown_options );

			ob_start();
			wp_dropdown_categories( $dropdown_options );
			$dropdown = ob_get_contents();
			ob_end_clean();
		}

		return $dropdown;
	}

	/**
	 * Process $_REQUEST, building $submenu_arguments
	 *
	 * @since 1.42
	 *
	 * @param	boolean	Optional: Include the "click filter" values in the results
	 *
	 * @return	array	non-empty view, search, filter and sort arguments
	 */
	public static function mla_submenu_arguments( $include_filters = true ) {
		static $submenu_arguments = NULL, $has_filters = NULL;

		if ( is_array( $submenu_arguments ) && ( $has_filters == $include_filters ) ) {
			return $submenu_arguments;
		}

		$submenu_arguments = array();
		$has_filters = $include_filters;

		/*
		 * View arguments
		 */
		if ( isset( $_REQUEST['post_mime_type'] ) ) {
			$submenu_arguments['post_mime_type'] = $_REQUEST['post_mime_type'];
		}

		if ( isset( $_REQUEST['detached'] ) ) {
			$submenu_arguments['detached'] = $_REQUEST['detached'];
		}

		if ( isset( $_REQUEST['status'] ) ) {
			$submenu_arguments['status'] = $_REQUEST['status'];
		}

		if ( isset( $_REQUEST['meta_query'] ) ) {
			$submenu_arguments['meta_query'] = urlencode( stripslashes( $_REQUEST['meta_query'] ) );
		}

		/*
		 * Search box arguments
		 */
		if ( !empty( $_REQUEST['s'] ) ) {
			$submenu_arguments['s'] = urlencode( stripslashes( $_REQUEST['s'] ) );

			if ( isset( $_REQUEST['mla_search_connector'] ) ) {
				$submenu_arguments['mla_search_connector'] = $_REQUEST['mla_search_connector'];
			}

			if ( isset( $_REQUEST['mla_search_fields'] ) ) {
				$submenu_arguments['mla_search_fields'] = $_REQUEST['mla_search_fields'];
			}
		}

		/*
		 * Filter arguments (from table header)
		 */
		if ( isset( $_REQUEST['m'] ) && ( '0' != $_REQUEST['m'] ) ) {
			$submenu_arguments['m'] = $_REQUEST['m'];
		}

		if ( isset( $_REQUEST['mla_filter_term'] ) && ( '0' != $_REQUEST['mla_filter_term'] ) ) {
			$submenu_arguments['mla_filter_term'] = $_REQUEST['mla_filter_term'];
		}

		/*
		 * Sort arguments (from column header)
		 */
		if ( isset( $_REQUEST['order'] ) ) {
			$submenu_arguments['order'] = $_REQUEST['order'];
		}

		if ( isset( $_REQUEST['orderby'] ) ) {
			$submenu_arguments['orderby'] = $_REQUEST['orderby'];
		}

		/*
		 * Filter arguments (from interior table cells)
		 */
		if ( $include_filters ) {
			if ( isset( $_REQUEST['heading_suffix'] ) ) {
				$submenu_arguments['heading_suffix'] = $_REQUEST['heading_suffix'];
			}

			if ( isset( $_REQUEST['parent'] ) ) {
				$submenu_arguments['parent'] = $_REQUEST['parent'];
			}

			if ( isset( $_REQUEST['author'] ) ) {
				$submenu_arguments['author'] = $_REQUEST['author'];
			}

			if ( isset( $_REQUEST['mla-tax'] ) ) {
				$submenu_arguments['mla-tax'] = $_REQUEST['mla-tax'];
			}

			if ( isset( $_REQUEST['mla-term'] ) ) {
				$submenu_arguments['mla-term'] = $_REQUEST['mla-term'];
			}

			if ( isset( $_REQUEST['mla-metakey'] ) ) {
				$submenu_arguments['mla-metakey'] = $_REQUEST['mla-metakey'];
			}

			if ( isset( $_REQUEST['mla-metavalue'] ) ) {
				$submenu_arguments['mla-metavalue'] = $_REQUEST['mla-metavalue'];
			}
		}

		return $submenu_arguments = apply_filters( 'mla_list_table_submenu_arguments', $submenu_arguments, $include_filters );
	}

	/**
	 * Handler for filter 'get_user_option_managemedia_page_mla-menucolumnshidden'
	 *
	 * Required because the screen.php get_hidden_columns function only uses
	 * the get_user_option result. Set when the file is loaded because the object
	 * is not created in time for the call from screen.php.
	 *
	 * @since 0.1
	 *
	 * @param	string	current list of hidden columns, if any
	 * @param	string	'managemedia_page_mla-menucolumnshidden'
	 * @param	object	WP_User object, if logged in
	 *
	 * @return	array	updated list of hidden columns
	 */
	public static function mla_manage_hidden_columns_filter( $result, $option, $user_data ) {
		if ( $result ) {
			return $result;
		}

		return self::$default_hidden_columns;
	}

	/**
	 * Handler for filter 'manage_media_page_mla-menu_columns'
	 *
	 * This required filter dictates the table's columns and titles. Set when the
	 * file is loaded because the list_table object isn't created in time
	 * to affect the "screen options" setup.
	 *
	 * @since 0.1
	 *
	 * @return	array	list of table columns
	 */
	public static function mla_manage_columns_filter( ) {
		return apply_filters( 'mla_list_table_get_columns', self::$default_columns );
	}

	/**
	 * Adds support for taxonomy and custom field columns
	 *
	 * Called in the admin_init action because the list_table object isn't
	 * created in time to affect the "screen options" setup.
	 *
	 * @since 0.30
	 */
	public static function mla_admin_init_action( ) {
		self::$default_columns =& MLAQuery::$default_columns;
		self::$default_hidden_columns =& MLAQuery::$default_hidden_columns;
		self::$default_sortable_columns =& MLAQuery::$default_sortable_columns;
	}

	/**
	 * Initializes some properties from $_REQUEST variables, then
	 * calls the parent constructor to set some default configs.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		$this->detached = isset( $_REQUEST['detached'] ) && ( '1' == $_REQUEST['detached'] );
		$this->attached = isset( $_REQUEST['detached'] ) && ( '0' == $_REQUEST['detached'] );
		$this->is_trash = isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'trash';

		// MLA does not use this
		$this->modes = array(
			'list' => __( 'List View' ),
		);

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'attachment', //singular name of the listed records
			'plural' => 'attachments', //plural name of the listed records
			'ajax' => true, //does this table support ajax?
			'screen' => 'media_page_' . MLACore::ADMIN_PAGE_SLUG
		), self::$default_columns );

		$this->currently_hidden = self::get_hidden_columns();

		/*
		 * NOTE: There is one add_action call at the end of this source file.
		 * NOTE: There are two add_filter calls at the end of this source file.
		 *
		 * They are added when the source file is loaded because the MLA_List_Table
		 * object is created too late to be useful.
		 */
	}

	/**
	 * Checks the current user's permissions
	 *
	 * @since 2.13
	 *
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can('upload_files');
	}

	/**
	 * Supply a column value if no column-specific function has been defined
	 *
	 * Called when the parent class can't find a method specifically built for a given column.
	 * The taxonomy and custom field columns are handled here. All other columns should have
	 * a specific method, so this function returns a troubleshooting message.
	 *
	 * @since 0.1
	 *
	 * @param	array	A singular item (one full row's worth of data)
	 * @param	array	The name/slug of the column to be processed
	 * @return	string	Text or HTML to be placed inside the column
	 */
	function column_default( $item, $column_name ) {
		static $custom_columns = NULL;

		if ( 't_' == substr( $column_name, 0, 2 ) ) {
			$taxonomy = substr( $column_name, 2 );
			$tax_object = get_taxonomy( $taxonomy );
			$terms = get_object_term_cache( $item->ID, $taxonomy );

			if ( false === $terms ) {
				$terms = wp_get_object_terms( $item->ID, $taxonomy );
				wp_cache_add( $item->ID, $terms, $taxonomy . '_relationships' );
			}

			if ( !is_wp_error( $terms ) ) {
				if ( empty( $terms ) ) {
					return __( 'None', 'media-library-assistant' );
				}

				$list = array();
				foreach ( $terms as $term ) {
					$term_name = esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, $taxonomy, 'display' ) );
					$list[] = sprintf( '<a href="%1$s" title="' . __( 'Filter by', 'media-library-assistant' ) . ' &#8220;%2$s&#8221;">%3$s</a>', esc_url( add_query_arg( array_merge( array(
						'page' => MLACore::ADMIN_PAGE_SLUG,
						'mla-tax' => $taxonomy,
						'mla-term' => $term->slug,
						'heading_suffix' => urlencode( $tax_object->label . ': ' . $term->name ) 
					), self::mla_submenu_arguments( false ) ), 'upload.php' ) ), $term_name, $term_name );
				} // foreach $term

				return join( ', ', $list );
			} else { // if !is_wp_error
				return __( 'Not Supported', 'media-library-assistant' );
			}
		} // 't_'
		elseif ( 'c_' == substr( $column_name, 0, 2 ) ) {
			if ( NULL === $custom_columns ) {
				$custom_columns = MLACore::mla_custom_field_support( 'custom_columns' );
			}

			$values = get_post_meta( $item->ID, $custom_columns[ $column_name ], false );
			if ( empty( $values ) ) {
				return '';
			}

			$list = array();
			foreach ( $values as $index => $value ) {
				/*
				 * For display purposes, convert array values.
				 * They are not links because no search will match them.
				 * Use "@" because embedded arrays throw PHP Warnings from implode.
				 */
				if ( is_array( $value ) ) {
					$list[] = 'array( ' . @implode( ', ', $value ) . ' )';
				} else {
					$list[] = sprintf( '<a href="%1$s" title="' . __( 'Filter by', 'media-library-assistant' ) . ' &#8220;%2$s&#8221;">%3$s</a>', esc_url( add_query_arg( array_merge( array(
						'page' => MLACore::ADMIN_PAGE_SLUG,
						'mla-metakey' => urlencode( self::$default_columns[ $column_name ] ),
						'mla-metavalue' => urlencode( $value ),
						'heading_suffix' => urlencode( self::$default_columns[ $column_name ] . ': ' . $value ) 
					), self::mla_submenu_arguments( false ) ), 'upload.php' ) ), esc_html( substr( $value, 0, 64 ) ), esc_html( $value ) );
				}
			}

			if ( count( $list ) > 1 ) {
				return '[' . join( '], [', $list ) . ']';
			} else {
				return $list[0];
			}
		} else { // 'c_'

			$content = apply_filters( 'mla_list_table_column_default', NULL, $item, $column_name );
			if ( is_null( $content ) ) {
				//Show the whole array for troubleshooting purposes
				/* translators: 1: column_name 2: column_values */
				return sprintf( __( 'column_default: %1$s, %2$s', 'media-library-assistant' ), $column_name, print_r( $item, true ) );
			} else {
				return $content;
			}
		}
	}

	/**
	 * Displays checkboxes for using bulk actions. The 'cb' column
	 * is given special treatment when columns are processed.
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="cb_%1$s[]" value="%2$s" />',
		/*%1$s*/ $this->_args['singular'], //Let's simply repurpose the table's singular label ("attachment")
		/*%2$s*/ $item->ID //The value of the checkbox should be the object's id
		);
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_icon( $item ) {
		$thumb = self::_build_item_thumbnail( $item );

		if ( $this->is_trash || ! current_user_can( 'edit_post', $item->ID ) ) {
			return $thumb;
		}

		/*
		 * Use the WordPress Edit Media screen
		 */
		$view_args = self::mla_submenu_arguments();
		if ( isset( $view_args['lang'] ) ) {
			$edit_url = 'post.php?post=' . $item->ID . '&action=edit&mla_source=edit&lang=' . $view_args['lang'];
		} else {
			$edit_url = 'post.php?post=' . $item->ID . '&action=edit&mla_source=edit';
		}

		return sprintf( '<a href="%1$s" title="' . __( 'Edit', 'media-library-assistant' ) . ' &#8220;%2$s&#8221;">%3$s</a>', admin_url( $edit_url ), _draft_or_post_title( $item ), $thumb ); 
	}

	/**
	 * Translate post_status 'future', 'pending', 'draft' and 'trash' to label
	 *
	 * @since 2.01
	 * 
	 * @param	string	post_status
	 *
	 * @return	string	Status label or empty string
	 */
	protected function _format_post_status( $post_status ) {
		$flag = ',<br>';
		switch ( $post_status ) {
			case 'draft' :
				$flag .= __('Draft');
				break;
			case 'future' :
				$flag .= __('Scheduled');
				break;
			case 'pending' :
				$flag .= _x('Pending', 'post state');
				break;
			case 'trash' :
				$flag .= __('Trash');
				break;
			default:
				$flag = '';
		}

	return $flag;
	}

	/**
	 * Get the name of the default primary column.
	 *
	 * @since 2.13
	 * @access protected
	 *
	 * @return string Name of the default primary column
	 */
	protected function get_default_primary_column_name() {
		$hidden_columns = $this->get_hidden_columns();

		$primary_column = '';
		foreach ( array( 'ID_parent', 'title_name', 'post_title', 'post_name' ) as $column_name ) {
			if ( ! in_array( $column_name, $hidden_columns ) ) {
				$primary_column = $column_name;
				break;
			}
		}

		// Fallback to the first visible column
		if ( empty( $primary_column ) ) {
			foreach ( $this->get_columns() as $column_name => $column_title ) {
				if ( ( 'cb' !== $column_name ) && ! in_array( $column_name, $hidden_columns ) ) {
					$primary_column = $column_name;
					break;
				}
			}
		}

		return $primary_column;
	}

	/**
	 * Generate and display row actions links.
	 *
	 * @since 2.13
	 * @access protected
	 *
	 * @param object $item        Attachment being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string Row actions output for media attachments.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary === $column_name ) {
			$actions = $this->row_actions( $this->_build_rollover_actions( $item, $column_name ) );
			$actions .= $this->_build_inline_data( $item );
			return $actions;
		}

		return '';
	}

	/**
	 * Add rollover actions to the current primary column, one of:
	 * 'ID_parent', 'title_name', 'post_title', 'post_name'
	 *
	 * @since 0.1
	 * 
	 * @param	object	A singular attachment (post) object
	 * @param	string	Current column name
	 *
	 * @return	array	Names and URLs of row-level actions
	 */
	protected function _build_rollover_actions( $item, $column ) {
		$actions = array();
		$att_title = _draft_or_post_title( $item );

		if ( ( $this->rollover_id != $item->ID ) && !in_array( $column, $this->currently_hidden ) ) {
			/*
			 * Build rollover actions
			 */
			$view_args = array_merge( array( 'page' => MLACore::ADMIN_PAGE_SLUG, 'mla_item_ID' => $item->ID ),
				self::mla_submenu_arguments() );

			if ( isset( $_REQUEST['paged'] ) ) {
				$view_args['paged'] = $_REQUEST['paged'];
			}

			if ( current_user_can( 'edit_post', $item->ID ) ) {
				if ( $this->is_trash ) {
					$actions['restore'] = '<a class="submitdelete" href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_RESTORE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Restore this item from the Trash', 'media-library-assistant' ) . '">' . __( 'Restore', 'media-library-assistant' ) . '</a>';
				} else {
					/*
					 * Use the WordPress Edit Media screen
					 */
					if ( isset( $view_args['lang'] ) ) {
						$edit_url = 'post.php?post=' . $item->ID . '&action=edit&mla_source=edit&lang=' . $view_args['lang'];
					} else {
						$edit_url = 'post.php?post=' . $item->ID . '&action=edit&mla_source=edit';
					}

					$actions['edit'] = '<a href="' . admin_url( $edit_url ) . '" title="' . __( 'Edit this item', 'media-library-assistant' ) . '">' . __( 'Edit', 'media-library-assistant' ) . '</a>';
					$actions['inline hide-if-no-js'] = '<a class="editinline" href="#" title="' . __( 'Edit this item inline', 'media-library-assistant' ) . '">' . __( 'Quick Edit', 'media-library-assistant' ) . '</a>';
				}
			} // edit_post

			if ( current_user_can( 'delete_post', $item->ID ) ) {
				if ( !$this->is_trash && EMPTY_TRASH_DAYS && MEDIA_TRASH ) {
					$actions['trash'] = '<a class="submitdelete" href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_TRASH, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Move this item to the Trash', 'media-library-assistant' ) . '">' . __( 'Move to Trash', 'media-library-assistant' ) . '</a>';
				} else {
					// If using trash for posts and pages but not for attachments, warn before permanently deleting 
					$delete_ays = EMPTY_TRASH_DAYS && !MEDIA_TRASH ? ' onclick="return showNotice.warn();"' : '';

					$actions['delete'] = '<a class="submitdelete"' . $delete_ays . ' href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_DELETE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Delete this item Permanently', 'media-library-assistant' ) . '">' . __( 'Delete Permanently', 'media-library-assistant' ) . '</a>';
				}
			} // delete_post

			if ( current_user_can( 'upload_files' ) ) {
				$file = get_attached_file( $item->ID );
				$download_args = array( 'page' => MLACore::ADMIN_PAGE_SLUG, 'mla_download_file' => urlencode( $file ), 'mla_download_type' => $item->post_mime_type );

				$actions['download'] = '<a href="' . add_query_arg( $download_args, wp_nonce_url( 'upload.php', MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Download', 'media-library-assistant' ) . ' &#8220;' . $att_title . '&#8221;">' . __( 'Download', 'media-library-assistant' ) . '</a>';
			}

			if ( ! $this->is_trash ) {
				$actions['view']  = '<a href="' . site_url( ) . '?attachment_id=' . $item->ID . '" rel="permalink" title="' . __( 'View', 'media-library-assistant' ) . ' &#8220;' . $att_title . '&#8221;">' . __( 'View', 'media-library-assistant' ) . '</a>';
			}

			$actions = apply_filters( 'mla_list_table_build_rollover_actions', $actions, $item, $column );

			$this->rollover_id = $item->ID;
		} // $this->rollover_id != $item->ID

		return $actions;
	}

	/**
	 * Generate item thumbnail image tag
	 *
	 * @since 2.15
	 * 
	 * @param	object	A singular attachment (post) object
	 *
	 * @return	string	HTML <img> for thumbnail
	 */
	protected function _build_item_thumbnail( $item ) {
		static $thumb = NULL, $item_id = 0;

		if ( $item->ID == $item_id ) {
			return $thumb;
		} else {
			$item_id = $item->ID;
		}

		$icon_width = MLACore::mla_get_option( MLACoreOptions::MLA_TABLE_ICON_SIZE );
		if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ENABLE_MLA_ICONS ) ) {
			if ( empty( $icon_width ) ) {
				$icon_width = $icon_height = 64;
			} else {
				$icon_width = $icon_height = absint( $icon_width );
			}
		} else {
			if ( empty( $icon_width ) ) {
				if ( MLATest::$wp_4dot3_plus ) {
					$icon_width = 60;
				} else {
					$icon_width = 80;
				}
			} else {
				$icon_width = absint( $icon_width );
			}

			if ( MLATest::$wp_4dot3_plus ) {
				$icon_height = $icon_width;
			} else {
				$icon_height = absint( .75 * (float) $icon_width );
			}
		}

		$dimensions = array( $icon_width, $icon_height );
		$thumb = wp_get_attachment_image( $item->ID, $dimensions, true, array( 'class' => 'mla_media_thumbnail' ) );

		if ( in_array( $item->post_mime_type, array( 'image/svg+xml' ) ) ) {
			$thumb = preg_replace( '/width=\"[^\"]*\"/', sprintf( 'width="%1$d"', $dimensions[0] ), $thumb );
			$thumb = preg_replace( '/height=\"[^\"]*\"/', sprintf( 'height="%1$d"', $dimensions[1] ), $thumb );
		}

		return $thumb;
	}

	/**
	 * Add hidden fields with the data for use in the inline editor
	 *
	 * @since 0.20
	 * 
	 * @param	object	A singular attachment (post) object
	 *
	 * @return	string	HTML <div> with row data
	 */
	protected function _build_inline_data( $item ) {
		$inline_data = "\r\n" . '<div class="hidden" id="inline_' . $item->ID . "\">\r\n";
		$inline_data .= '	<div class="item_thumbnail">' . self::_build_item_thumbnail( $item ) . "</div>\r\n";
		$inline_data .= '	<div class="post_title">' . esc_attr( $item->post_title ) . "</div>\r\n";
		$inline_data .= '	<div class="post_name">' . esc_attr( $item->post_name ) . "</div>\r\n";
		$inline_data .= '	<div class="post_excerpt">' . esc_attr( $item->post_excerpt ) . "</div>\r\n";
		$inline_data .= '	<div class="post_content">' . esc_attr( $item->post_content ) . "</div>\r\n";

		if ( !empty( $item->mla_wp_attachment_metadata ) ) {
			$inline_data .= '	<div class="image_alt">';

			if ( isset( $item->mla_wp_attachment_image_alt ) ) {
				if ( is_array( $item->mla_wp_attachment_image_alt ) ) {
					$inline_data .= esc_attr( $item->mla_wp_attachment_image_alt[0] );
				} else {
					$inline_data .= esc_attr( $item->mla_wp_attachment_image_alt );
				}
			}

			$inline_data .= "</div>\r\n";
		}

		$inline_data .= '	<div class="post_parent">' . $item->post_parent . "</div>\r\n";

		if ( $item->post_parent ) {
			if ( isset( $item->parent_title ) ) {
				$parent_title = $item->parent_title;
			} else {
				$parent_title = __( '(no title)', 'media-library-assistant' );
			}
		} else {
			$parent_title = '';
		}

		$inline_data .= '	<div class="post_parent_title">' . $parent_title . "</div>\r\n";
		$inline_data .= '	<div class="menu_order">' . $item->menu_order . "</div>\r\n";
		$inline_data .= '	<div class="post_author">' . $item->post_author . "</div>\r\n";

		$custom_fields = MLACore::mla_custom_field_support( 'quick_edit' );
		$custom_fields = array_merge( $custom_fields, MLACore::mla_custom_field_support( 'bulk_edit' ) );
		foreach ( $custom_fields as $slug => $details ) {
			$value = get_metadata( 'post', $item->ID, $details['name'], true );

			if ( is_array( $value ) ) {
				if ( 'array' == $details['option'] ) {
					$value = implode( ',', $value );
				} else {
					// '(Array)' indicates an existing array value in the field, which we preserve
					$value = '(Array)';
				}
			}
			
			$inline_data .= '	<div class="' . $slug . '">' . esc_html( $value ) . "</div>\r\n";
		}

		$taxonomies = get_object_taxonomies( 'attachment', 'objects' );

		foreach ( $taxonomies as $tax_name => $tax_object ) {
			if ( $tax_object->show_ui && MLACore::mla_taxonomy_support( $tax_name, 'quick-edit' ) ) {
				$terms = get_object_term_cache( $item->ID, $tax_name );
				if ( false === $terms ) {
					$terms = wp_get_object_terms( $item->ID, $tax_name );
					wp_cache_add( $item->ID, $terms, $tax_name . '_relationships' );
				}

				if ( is_wp_error( $terms ) || empty( $terms ) ) {
					$terms = array();
				}

				$ids = array();

				if ( $tax_object->hierarchical ) {
					foreach( $terms as $term ) {
						$ids[] = $term->term_id;
					}

					$inline_data .= '	<div class="mla_category" id="' . $tax_name . '_' . $item->ID . '">'
						. implode( ',', $ids ) . "</div>\r\n";
				} else {
					foreach( $terms as $term ) {
						$ids[] = $term->name;
					}

					$inline_data .= '	<div class="mla_tags" id="'.$tax_name.'_'.$item->ID. '">'
						. esc_attr( implode( ', ', $ids ) ) . "</div>\r\n";
				}
			}
		}

		$inline_data = apply_filters( 'mla_list_table_build_inline_data', $inline_data, $item );

		$inline_data .= "</div>\r\n";

		return $inline_data;
	}

	/**
	 * Format primary column before/after Wordpress v4.3
	 *
	 * For WordPress before 4.3, add rollover actions and inline_data to the
	 * first visible column. For 4.3 and later, merge the icon with the primary
	 * visible column and add div tags.
	 *
	 * @since 2.13
	 * 
	 * @param	object	A singular attachment (post) object
	 * @param	string	Current column name
	 * @param	string	Current column contents
	 *
	 * @return	string	Complete column content
	 */
	protected function _handle_primary_column( $item, $column_name, $column_content ) {
		if ( MLATest::$wp_4dot3_plus ) {
			static $primary_column = NULL;

			if ( NULL == $primary_column ) {
				$primary_column = $this->get_default_primary_column_name();
			}

			if ( $primary_column != $column_name ) {
				return $column_content;
			}

			list( $mime ) = explode( '/', $item->post_mime_type );
			$final_content = "<div class=\"attachment-icon {$mime}-icon\">\n" . $this->column_icon( $item ) . "\n</div>\n";
			return $final_content . "<div class=\"attachment-info\">\n" . $column_content . "\n</div>\n";
		}

		$actions = $this->row_actions( $this->_build_rollover_actions( $item, $column_name ) );
		if ( ! empty( $actions ) ) {
			$column_content .= $actions . $this->_build_inline_data( $item );
		}

		return $column_content;
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_ID_parent( $item ) {
		if ( $item->post_parent ) {
			if ( isset( $item->parent_title ) ) {
				$parent_title = $item->parent_title;
			} else {
				$parent_title = sprintf( '%1$d %2$s', $item->post_parent, __( '(no title)', 'media-library-assistant' ) );
			}

			$parent = sprintf( '<a href="%1$s" title="' . __( 'Filter by', 'media-library-assistant' ) . ' ' . __( 'Parent ID', 'media-library-assistant' ) . '">(' . __( 'Parent', 'media-library-assistant' ) . ':%2$s)</a>', esc_url( add_query_arg( array_merge( array(
					'page' => MLACore::ADMIN_PAGE_SLUG,
					'parent' => $item->post_parent,
					'heading_suffix' => urlencode( __( 'Parent', 'media-library-assistant' ) . ': ' .  $parent_title ) 
				), self::mla_submenu_arguments( false ) ), 'upload.php' ) ), (string) $item->post_parent );
		} else {// $item->post_parent
			$parent = __( 'Parent', 'media-library-assistant' ) . ':0';
		}

		$content = sprintf( '%1$s<br><span style="color:silver">%2$s</span>', /*%1$s*/ $item->ID, /*%2$s*/ $parent );
		return $this->_handle_primary_column( $item, 'ID_parent', $content );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_title_name( $item ) {
		$errors = $item->mla_references['parent_errors'];
		if ( '(' . __( 'NO REFERENCE TESTS', 'media-library-assistant' ) . ')' == $errors ) {
			$errors = '';
		}

		$content =  sprintf( '%1$s<br>%2$s<br>%3$s', /*%1$s*/ _draft_or_post_title( $item ), /*%2$s*/ esc_attr( $item->post_name ), /*%3$s*/ $errors );
		return $this->_handle_primary_column( $item, 'title_name', $content );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_post_title( $item ) {
		return $this->_handle_primary_column( $item, 'post_title', _draft_or_post_title( $item ) );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_post_name( $item ) {
		return $this->_handle_primary_column( $item, 'post_name', esc_attr( $item->post_name ) );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_parent( $item ) {
		if ( $item->post_parent ){
			if ( isset( $item->parent_title ) ) {
				$parent_title = $item->parent_title;
			} else {
				$parent_title = __( '(no title: bad ID)', 'media-library-assistant' );
			}

			return sprintf( '<a href="%1$s" title="' . __( 'Filter by', 'media-library-assistant' ) . ' ' . __( 'Parent ID', 'media-library-assistant' ) . '">%2$s</a>', esc_url( add_query_arg( array_merge( array(
				'page' => MLACore::ADMIN_PAGE_SLUG,
				'parent' => $item->post_parent,
				'heading_suffix' => urlencode( __( 'Parent', 'media-library-assistant' ) . ': ' .  $parent_title ) 
			), self::mla_submenu_arguments( false ) ), 'upload.php' ) ), (string) $item->post_parent );
		} else {
			return (string) $item->post_parent;
		}
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.60
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_menu_order( $item ) {
		return (string) $item->menu_order;
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_featured( $item ) {
		if ( !MLACore::$process_featured_in ) {
			return __( 'Disabled', 'media-library-assistant' );
		}

		/*
		 * Move parent to the top of the list
		 */
		$features = $item->mla_references['features'];
		if ( isset( $features[ $item->post_parent ] ) ) {
			$parent = $features[ $item->post_parent ];
			unset( $features[ $item->post_parent ] );
			array_unshift( $features, $parent );
		}

		$value = '';
		foreach ( $features as $feature ) {
			$status = self::_format_post_status( $feature->post_status );

			if ( $feature->ID == $item->post_parent ) {
				$parent = ',<br>' . __( 'PARENT', 'media-library-assistant' );
			} else {
				$parent = '';
			}

			$value .= sprintf( '<a href="%1$s" title="' . __( 'Edit', 'media-library-assistant' ) . ' &#8220;%2$s&#8221;">%2$s</a> (%3$s %4$s%5$s%6$s), ',
				/*%1$s*/ esc_url( add_query_arg( array('post' => $feature->ID, 'action' => 'edit'), 'post.php' ) ),
				/*%2$s*/ esc_attr( $feature->post_title ),
				/*%3$s*/ esc_attr( $feature->post_type ),
				/*%4$s*/ $feature->ID,
				/*%5$s*/ $status,
				/*%6$s*/ $parent ) . "<br>\r\n";
		} // foreach $feature

		return $value;
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_inserted( $item ) {
		if ( !MLACore::$process_inserted_in ) {
			return __( 'Disabled', 'media-library-assistant' );
		}

		$value = '';
		foreach ( $item->mla_references['inserts'] as $file => $inserts ) {
			if ( 'base' != $item->mla_references['inserted_option'] ) {
				$value .= sprintf( '<strong>%1$s</strong><br>', $file );
			}

			/*
			 * Move parent to the top of the list
			 */
			if ( isset( $inserts[ $item->post_parent ] ) ) {
				$parent = $inserts[ $item->post_parent ];
				unset( $inserts[ $item->post_parent ] );
				array_unshift( $inserts, $parent );
			}

			foreach ( $inserts as $insert ) {
				$status = self::_format_post_status( $insert->post_status );

				if ( $insert->ID == $item->post_parent ) {
					$parent = ',<br>' . __( 'PARENT', 'media-library-assistant' );
				} else {
					$parent = '';
				}

				$value .= sprintf( '<a href="%1$s" title="' . __( 'Edit', 'media-library-assistant' ) . ' &#8220;%2$s&#8221;">%2$s</a> (%3$s %4$s%5$s%6$s), ',
				/*%1$s*/ esc_url( add_query_arg( array('post' => $insert->ID, 'action' => 'edit'), 'post.php' ) ),
				/*%2$s*/ esc_attr( $insert->post_title ),
				/*%3$s*/ esc_attr( $insert->post_type ),
				/*%4$s*/ $insert->ID,
				/*%3$s*/ $status,
				/*%6$s*/ $parent ) . "<br>\r\n";
			} // foreach $insert
		} // foreach $file

		return $value;
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.70
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_galleries( $item ) {
		if ( !MLACore::$process_gallery_in ) {
			return __( 'Disabled', 'media-library-assistant' );
		}

		/*
		 * Move parent to the top of the list
		 */
		$galleries = $item->mla_references['galleries'];
		if ( isset( $galleries[ $item->post_parent ] ) ) {
			$parent = $galleries[ $item->post_parent ];
			unset( $galleries[ $item->post_parent ] );
			array_unshift( $galleries, $parent );
		}

		$value = '';
		foreach ( $galleries as $ID => $gallery ) {
			$status = self::_format_post_status( $gallery['post_status'] );

			if ( $gallery['ID'] == $item->post_parent ) {
				$parent = ',<br>' . __( 'PARENT', 'media-library-assistant' );
			} else {
				$parent = '';
			}

			$value .= sprintf( '<a href="%1$s" title="' . __( 'Edit', 'media-library-assistant' ) . ' &#8220;%2$s&#8221;">%2$s</a> (%3$s %4$s%5$s%6$s),',
				/*%1$s*/ esc_url( add_query_arg( array('post' => $gallery['ID'], 'action' => 'edit'), 'post.php' ) ),
				/*%2$s*/ esc_attr( $gallery['post_title'] ),
				/*%3$s*/ esc_attr( $gallery['post_type'] ),
				/*%4$s*/ $gallery['ID'],
				/*%5$s*/ $status,
				/*%6$s*/ $parent ) . "<br>\r\n";
		} // foreach $gallery

		return $value;
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.70
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_mla_galleries( $item ) {
		if ( !MLACore::$process_mla_gallery_in ) {
			return __( 'Disabled', 'media-library-assistant' );
		}

		/*
		 * Move parent to the top of the list
		 */
		$mla_galleries = $item->mla_references['mla_galleries'];
		if ( isset( $mla_galleries[ $item->post_parent ] ) ) {
			$parent = $mla_galleries[ $item->post_parent ];
			unset( $mla_galleries[ $item->post_parent ] );
			array_unshift( $mla_galleries, $parent );
		}

		$value = '';
		foreach ( $mla_galleries as $gallery ) {
			$status = self::_format_post_status( $gallery['post_status'] );

			if ( $gallery['ID'] == $item->post_parent ) {
				$parent = ',<br>' . __( 'PARENT', 'media-library-assistant' );
			} else {
				$parent = '';
			}

			$value .= sprintf( '<a href="%1$s" title="' . __( 'Edit', 'media-library-assistant' ) . ' &#8220;%2$s&#8221;">%2$s</a> (%3$s %4$s%5$s%6$s),',
				/*%1$s*/ esc_url( add_query_arg( array('post' => $gallery['ID'], 'action' => 'edit'), 'post.php' ) ),
				/*%2$s*/ esc_attr( $gallery['post_title'] ),
				/*%3$s*/ esc_attr( $gallery['post_type'] ),
				/*%4$s*/ $gallery['ID'],
				/*%5$s*/ $status,
				/*%6$s*/ $parent ) . "<br>\r\n";
		} // foreach $gallery

		return $value;
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_alt_text( $item ) {
		if ( isset( $item->mla_wp_attachment_image_alt ) ) {
			if ( is_array( $item->mla_wp_attachment_image_alt ) ) {
				$alt_text = $item->mla_wp_attachment_image_alt[0];
			} else {
				$alt_text = $item->mla_wp_attachment_image_alt;
			}

			return sprintf( '<a href="%1$s" title="' . __( 'Filter by', 'media-library-assistant' ) . ' &#8220;%2$s&#8221;">%3$s</a>', esc_url( add_query_arg( array_merge( array(
				'page' => MLACore::ADMIN_PAGE_SLUG,
				'mla-metakey' => '_wp_attachment_image_alt',
				'mla-metavalue' => urlencode( $alt_text ),
				'heading_suffix' => urlencode( __( 'ALT Text', 'media-library-assistant' ) . ': ' . $alt_text ) 
			), self::mla_submenu_arguments( false ) ), 'upload.php' ) ), esc_html( $alt_text ), esc_html( $alt_text ) );
		}

		return '';
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_caption( $item ) {
		return esc_attr( $item->post_excerpt );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_description( $item ) {
		return esc_textarea( $item->post_content );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.30
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_post_mime_type( $item ) {
		return sprintf( '<a href="%1$s" title="' . __( 'Filter by', 'media-library-assistant' ) . ' &#8220;%2$s&#8221;">%2$s</a>', esc_url( add_query_arg( array_merge( array(
			'page' => MLACore::ADMIN_PAGE_SLUG,
			'post_mime_type' => urlencode( $item->post_mime_type ),
			'heading_suffix' => urlencode( __( 'MIME Type', 'media-library-assistant' ) . ': ' . $item->post_mime_type ) 
		), self::mla_submenu_arguments( false ) ), 'upload.php' ) ), esc_html( $item->post_mime_type ), esc_html( $item->post_mime_type ) );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_file_url( $item ) {
		$attachment_url = wp_get_attachment_url( $item->ID );

		return $attachment_url ? $attachment_url : __( 'None', 'media-library-assistant' );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_base_file( $item ) {
		$base_file = isset( $item->mla_wp_attached_file ) ? $item->mla_wp_attached_file : '';

		return sprintf( '<a href="%1$s" title="' . __( 'Filter by', 'media-library-assistant' ) . ' &#8220;%2$s&#8221;">%2$s</a>', esc_url( add_query_arg( array_merge( array(
			'page' => MLACore::ADMIN_PAGE_SLUG,
			'mla-metakey' => urlencode( '_wp_attached_file' ),
			'mla-metavalue' => urlencode( $base_file ),
			'heading_suffix' => urlencode( __( 'Base File', 'media-library-assistant' ) . ': ' . $base_file ) 
		), self::mla_submenu_arguments( false ) ), 'upload.php' ) ), esc_html( $base_file ) );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_date( $item ) {
		global $post;

		if ( '0000-00-00 00:00:00' == $item->post_date ) {
			$h_time = __( 'Unpublished', 'media-library-assistant' );
		} else {
			$post = $item; // Resolve issue with "The Events Calendar"
			$m_time = $item->post_date;
			$time = get_post_time( 'G', true, $item, false );

			if ( ( abs( $t_diff = time() - $time ) ) < 86400 ) {
				if ( $t_diff < 0 ) {
					/* translators: 1: upload/last modified date and time */
					$h_time = sprintf( __( '%1$s from now', 'media-library-assistant' ), human_time_diff( $time ) );
				} else {
					/* translators: 1: upload/last modified date and time */
					$h_time = sprintf( __( '%1$s ago', 'media-library-assistant' ), human_time_diff( $time ) );
				}
			} else {
				/* translators: format for upload/last modified date */
				$h_time = mysql2date( __( 'Y/m/d', 'media-library-assistant' ), $m_time );
			}
		}

		return $h_time;
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.30
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_modified( $item ) {
		if ( '0000-00-00 00:00:00' == $item->post_modified ) {
			$h_time = __( 'Unpublished', 'media-library-assistant' );
		} else {
			$m_time = $item->post_modified;
			$time = get_post_time( 'G', true, $item, false );

			if ( ( abs( $t_diff = time() - $time ) ) < 86400 ) {
				if ( $t_diff < 0 ) {
					$h_time = sprintf( __( '%1$s from now', 'media-library-assistant' ), human_time_diff( $time ) );
				} else {
					$h_time = sprintf( __( '%1$s ago', 'media-library-assistant' ), human_time_diff( $time ) );
				}
			} else {
				$h_time = mysql2date( __( 'Y/m/d', 'media-library-assistant' ), $m_time );
			}
		}

		return $h_time;
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.30
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_author( $item ) {
		$user = get_user_by( 'id', $item->post_author );

		if ( isset( $user->data->display_name ) ) {
			return sprintf( '<a href="%s" title="' . __( 'Filter by', 'media-library-assistant' ) . ' ' . __( 'Author', 'media-library-assistant' ) . '">%s</a>', esc_url( add_query_arg( array_merge( array(
				 'page' => MLACore::ADMIN_PAGE_SLUG,
				'author' => $item->post_author,
				'heading_suffix' => urlencode( __( 'Author', 'media-library-assistant' ) . ': ' . $user->data->display_name ) 
			), self::mla_submenu_arguments( false ) ), 'upload.php' ) ), esc_html( $user->data->display_name ) );
		}

		return 'unknown';
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 0.1
	 * 
	 * @param	array	A singular attachment (post) object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_attached_to( $item ) {
		if ( isset( $item->parent_title ) ) {
			$parent_title = sprintf( '<a href="%1$s" title="' . __( 'Edit', 'media-library-assistant' ) . ' &#8220;%2$s&#8221;">%3$s</a>', esc_url( add_query_arg( array(
				'post' => $item->post_parent,
				'action' => 'edit'
			), 'post.php' ) ), esc_attr( $item->parent_title ), esc_attr( $item->parent_title ) );

			if ( isset( $item->parent_date ) ) {
				$parent_date = $item->parent_date;
			} else {
				$parent_date = '';
			}

			if ( isset( $item->parent_type ) ) {
				$parent_type = '(' . $item->parent_type . ' ' . (string) $item->post_parent . self::_format_post_status( $item->parent_status ) . ')';
			} else {
				$parent_type = '';
			}

			$parent =  sprintf( '%1$s<br>%2$s<br>%3$s', /*%1$s*/ $parent_title, /*%2$s*/ mysql2date( __( 'Y/m/d', 'media-library-assistant' ), $parent_date ), /*%3$s*/ $parent_type ); // . "<br>\r\n";
		} else {
			$parent = '(' . _x( 'Unattached', 'table_view_singular', 'media-library-assistant' ) . ')';
		}

		$set_parent = sprintf( '<a class="hide-if-no-js" id="mla-child-%2$s" onclick="mla.inlineEditAttachment.tableParentOpen( \'%1$s\',\'%2$s\',\'%3$s\' ); return false;" href="#the-list">%4$s</a><br>', /*%1$s*/ $item->post_parent, /*%2$s*/ $item->ID, /*%3$s*/ _draft_or_post_title( $item ), /*%4$s*/ __( 'Set Parent', 'media-library-assistant' ) );

		return $parent . "<br>\n" . $set_parent . "\n";
	}

	/**
	 * Display the pagination, adding view, search and filter arguments
	 *
	 * @since 1.42
	 * 
	 * @param	string	'top' | 'bottom'
	 */
	function pagination( $which ) {
		$save_uri = $_SERVER['REQUEST_URI'];
		$_SERVER['REQUEST_URI'] = add_query_arg( self::mla_submenu_arguments(), $save_uri );
		parent::pagination( $which );
		$_SERVER['REQUEST_URI'] = $save_uri;
	}

	/**
	 * This method dictates the table's columns and titles
	 *
	 * @since 0.1
	 * 
	 * @return	array	Column information: 'slugs'=>'Visible Titles'
	 */
	function get_columns( ) {
		return self::mla_manage_columns_filter();
	}

	/**
	 * Returns the list of currently hidden columns from a user option or
	 * from default values if the option is not set
	 *
	 * @since 0.1
	 * 
	 * @return	array	Column information,e.g., array(0 => 'ID_parent, 1 => 'title_name')
	 */
	function get_hidden_columns( ) {
		$columns = get_user_option( 'managemedia_page_' . MLACore::ADMIN_PAGE_SLUG . 'columnshidden' );

		if ( is_array( $columns ) ) {
			foreach ( $columns as $index => $value ){
				if ( empty( $value ) ) {
					unset( $columns[ $index ] );
				}
			}
		} else {
			$columns = self::$default_hidden_columns;
		}

		return apply_filters( 'mla_list_table_get_hidden_columns', $columns );
	}

	/**
	 * Returns an array where the  key is the column that needs to be sortable
	 * and the value is db column (or other criteria) to sort by.
	 *
	 * @since 0.1
	 * 
	 * @return	array	Sortable column information,e.g.,
	 * 					'slug' => array('data_value', (boolean) initial_descending )
	 */
	function get_sortable_columns( ) {
		return apply_filters( 'mla_list_table_get_sortable_columns', self::$default_sortable_columns );
	}

	/**
	 * Print column headers, adding view, search and filter arguments
	 *
	 * @since 1.42
	 *
	 * @param bool $with_id Whether to set the id attribute or not
	 */
	function print_column_headers( $with_id = true ) {
		$save_uri = $_SERVER['REQUEST_URI'];
		$_SERVER['REQUEST_URI'] = add_query_arg( self::mla_submenu_arguments(), $save_uri );
		parent::print_column_headers( $with_id );
		$_SERVER['REQUEST_URI'] = $save_uri;
	}

	/**
	 * Wrapper for _get_view; returns HTML markup for one view that can be used with this table
	 *
	 * @since 2.11
	 *
	 * @param	string	View slug, key to MLA_POST_MIME_TYPES array 
	 * @param	string	Slug for current view 
	 * 
	 * @return	string | false	HTML for link to display the view, false if count = zero
	 */
	public function mla_get_view( $view_slug, $current_view ) {
		return self::_get_view( $view_slug, $current_view );
	}

	/**
	 * Returns HTML markup for one view that can be used with this table
	 *
	 * @since 1.40
	 *
	 * @param	string	View slug, key to MLA_POST_MIME_TYPES array 
	 * @param	string	Slug for current view 
	 * 
	 * @return	string | false	HTML for link to display the view, false if count = zero
	 */
	private static function _get_view( $view_slug, $current_view ) {
		global $wpdb;
		static $mla_types = NULL, $default_types, $posts_per_type, $post_mime_types, $avail_post_mime_types, $matches, $num_posts, $detached_items;

		/*
		 * Calculate the common values once per page load
		 */
		if ( is_null( $mla_types ) ) {
			$query_types = MLAMime::mla_query_view_items( array( 'orderby' => 'menu_order' ), 0, 0 );
			if ( ! is_array( $query_types ) ) {
				$query_types = array ();
			}

			$mla_types = array ();
			foreach ( $query_types as $value ) {
				$mla_types[ $value->slug ] = $value;
			}

			$default_types = MLACore::mla_get_option( MLACoreOptions::MLA_POST_MIME_TYPES, true );
			$posts_per_type = (array) wp_count_attachments();
			$post_mime_types = get_post_mime_types();
			$avail_post_mime_types = self::_avail_mime_types( $posts_per_type );
			$matches = wp_match_mime_types( array_keys( $post_mime_types ), array_keys( $posts_per_type ) );

			foreach ( $matches as $type => $reals ) {
				foreach ( $reals as $real ) {
					$num_posts[ $type ] = ( isset( $num_posts[ $type ] ) ) ? $num_posts[ $type ] + $posts_per_type[ $real ] : $posts_per_type[ $real ];
				}
			}

			$detached_items = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_status != 'trash' AND post_parent < 1" );
		}

		$class = ( $view_slug == $current_view ) ? ' class="current"' : '';
		$base_url = 'upload.php?page=' . MLACore::ADMIN_PAGE_SLUG;

		/*
		 * Handle the special cases: all, detached, attached and trash
		 */
		switch( $view_slug ) {
			case 'all':
				$total_items = array_sum( $posts_per_type ) - $posts_per_type['trash'];
				return "<a href='{$base_url}'$class>" . sprintf( _nx( 'All', 'All', $total_items, 'uploaded files', 'media-library-assistant' ) . ' <span class="count">(%1$s)</span></a>', number_format_i18n( $total_items ) );
			case 'detached':
				if ( $detached_items ) {
					$value = $default_types['detached'];
					$singular = sprintf('%s <span class="count">(%%s)</span>', $value['singular'] );
					$plural = sprintf('%s <span class="count">(%%s)</span>', $value['plural'] );
					return '<a href="' . add_query_arg( array( 'detached' => '1' ), $base_url ) . '"' . $class . '>' . sprintf( _nx( $singular, $plural, $detached_items, 'detached files', 'media-library-assistant' ), number_format_i18n( $detached_items ) ) . '</a>';
				}

				return false;
			case 'attached':
				if ( $attached_items = ( array_sum( $posts_per_type ) - $posts_per_type['trash'] ) - $detached_items ) {
					$value = $default_types['attached'];
					$singular = sprintf('%s <span class="count">(%%s)</span>', $value['singular'] );
					$plural = sprintf('%s <span class="count">(%%s)</span>', $value['plural'] );
					return '<a href="' . add_query_arg( array( 'detached' => '0' ), $base_url ) . '"' . $class . '>' . sprintf( _nx( $singular, $plural, $attached_items, 'attached files', 'media-library-assistant' ), number_format_i18n( $attached_items ) ) . '</a>';
				}

				return false;
			case 'trash':
				if ( $posts_per_type['trash'] ) {
					$value = $default_types['trash'];
					$singular = sprintf('%s <span class="count">(%%s)</span>', $value['singular'] );
					$plural = sprintf('%s <span class="count">(%%s)</span>', $value['plural'] );
					return '<a href="' . add_query_arg( array( 'status' => 'trash'
					), $base_url ) . '"' . $class . '>' . sprintf( _nx( $singular, $plural, $posts_per_type['trash'], 'uploaded files', 'media-library-assistant' ), number_format_i18n( $posts_per_type['trash'] ) ) . '</a>';
				}

				return false;
		} // switch special cases

		/*
		 * Make sure the slug is in our list
		 */
		if ( array_key_exists( $view_slug, $mla_types ) ) {
			$mla_type = $mla_types[ $view_slug ];
		} else {
			return false;
		}

		/*
		 * Handle post_mime_types
		 */
		if ( $mla_type->post_mime_type ) {
			if ( !empty( $num_posts[ $view_slug ] ) ) {
				return "<a href='" . add_query_arg( array( 'post_mime_type' => $view_slug
				), $base_url ) . "'$class>" . sprintf( translate_nooped_plural( $post_mime_types[ $view_slug ][ 2 ], $num_posts[ $view_slug ], 'media-library-assistant' ), number_format_i18n( $num_posts[ $view_slug ] ) ) . '</a>';
			}

			return false;
		}

		/*
		 * Handle extended specification types
		 */
		if ( empty( $mla_type->specification ) ) {
			$query = array ( 'post_mime_type' => $view_slug );
		} else {
			$query = MLACore::mla_prepare_view_query( $view_slug, $mla_type->specification );
		}

		$total_items = MLAQuery::mla_count_list_table_items( $query );
		if ( $total_items ) {
			$singular = sprintf('%s <span class="count">(%%s)</span>', $mla_type->singular );
			$plural = sprintf('%s <span class="count">(%%s)</span>', $mla_type->plural );
			$nooped_plural = _n_noop( $singular, $plural, 'media-library-assistant' );

			if ( isset( $query['post_mime_type'] ) ) {
				$query['post_mime_type'] = urlencode( $query['post_mime_type'] );
			} else {
				$query['meta_slug'] = $view_slug;
				$query['meta_query'] = urlencode( serialize( $query['meta_query'] ) );
			}

			return "<a href='" . add_query_arg( $query, $base_url ) . "'$class>" . sprintf( translate_nooped_plural( $nooped_plural, $total_items, 'media-library-assistant' ), number_format_i18n( $total_items ) ) . '</a>';
		}

		return false;
	} // _get_view

	/**
	 * Returns an associative array listing all the views that can be used with this table.
	 * These are listed across the top of the page and managed by WordPress.
	 *
	 * @since 0.1
	 * 
	 * @return	array	View information,e.g., array ( id => link )
	 */
	function get_views( ) {
		/*
		 * Find current view
		 */
		if ( $this->detached  ) {
			$current_view = 'detached';
		} elseif ( $this->attached ) {
			$current_view = 'attached';
		} elseif ( $this->is_trash ) {
			$current_view = 'trash';
		} elseif ( empty( $_REQUEST['post_mime_type'] ) ) {
			if ( isset( $_REQUEST['meta_query'] ) ) {
				$query = unserialize( stripslashes( $_REQUEST['meta_query'] ) );
				$current_view = $query['slug'];
			} else {
				$current_view = 'all';
			}
		} else {
			$current_view = $_REQUEST['post_mime_type'];
		}

		$mla_types = MLAMime::mla_query_view_items( array( 'orderby' => 'menu_order' ), 0, 0 );
		if ( ! is_array( $mla_types ) ) {
			$mla_types = array ();
		}

		/*
		 * Filter the list, generate the views
		 */
		$view_links = array();
		foreach ( $mla_types as $value ) {
			if ( $value->table_view ) {
				if ( $current_view == $value->specification ) {
					$current_view = $value->slug;
				}

				if ( $link = self::_get_view( $value->slug, $current_view ) ) {
					$view_links[ $value->slug ] = $link;
				}
			}
		}

		return $view_links;
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 0.1
	 * 
	 * @return	array	Contains all the bulk actions: 'slugs'=>'Visible Titles'
	 */
	function get_bulk_actions( ) {
		$actions = array();

		if ( $this->is_trash ) {
			$actions['restore'] = __( 'Restore', 'media-library-assistant' );
			$actions['delete'] = __( 'Delete Permanently', 'media-library-assistant' );
		} else {
			$actions['edit'] = __( 'Edit', 'media-library-assistant' );

			if ( EMPTY_TRASH_DAYS && MEDIA_TRASH ) {
				$actions['trash'] = __( 'Move to Trash', 'media-library-assistant' );
			} else {
				$actions['delete'] = __( 'Delete Permanently', 'media-library-assistant' );
			}
		}

		return apply_filters( 'mla_list_table_get_bulk_actions', $actions );
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * Adds the list/grid switcher in WP 4.0+
	 *
	 * @since 2.25
	 *
	 * @param	string	'top' or 'bottom', i.e., above or below the table rows
	 */
	function display_tablenav( $which ) {
		if ( 'top' === $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		}
		?>

	<div class="tablenav <?php echo esc_attr( $which ); ?>">
		<?php if ( 'top' === $which && MLAQuery::$wp_4dot0_plus && ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_SCREEN_DISPLAY_SWITCHER ) )): ?>
		<div class="view-switch media-grid-view-switch" style="float: left"> <a class="view-list current" href="<?php echo admin_url( 'upload.php?page=' . MLACore::ADMIN_PAGE_SLUG ); ?>"> <span class="screen-reader-text">List View</span> </a> <a class="view-grid" href="<?php echo admin_url( 'upload.php?mode=grid' ); ?>"> <span class="screen-reader-text">Grid View</span> </a> </div>
		<?php endif; ?>

		<?php if ( $this->has_items() ): ?>
		<div class="alignleft actions bulkactions">
			<?php $this->bulk_actions( $which ); ?>
		</div>
		<?php endif;
		$this->extra_tablenav( $which );
		$this->pagination( $which );
?>

		<br class="clear" />
	</div>
<?php
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * Modeled after class-wp-posts-list-table.php in wp-admin/includes.
	 *
	 * @since 0.1
	 * 
	 * @param	string	'top' or 'bottom', i.e., above or below the table rows
	 *
	 * @return	void
	 */
	function extra_tablenav( $which ) {
		/*
		 * Decide which actions to show
		 */
		if ( 'top' == $which ) {
			$actions = array( 'month', 'mla_filter_term', 'mla_filter' );

			$term_search_taxonomies = MLACore::mla_supported_taxonomies('term-search');
			if ( ! empty( $term_search_taxonomies ) ) {
				$actions[] = 'terms_search';
			}
		} else {
			$actions = array();
		}

		if ( self::mla_submenu_arguments( true ) != self::mla_submenu_arguments( false ) ) {
			$actions[] = 'clear_filter_by';
		}
		
		if ( $this->is_trash && current_user_can( 'edit_others_posts' ) ) {
			$actions[] = 'delete_all';
		}

		$actions = apply_filters( 'mla_list_table_extranav_actions', $actions, $which );
		
		if ( empty( $actions ) ) {
			return;
		}

		echo ( '<div class="alignleft actions">' );

		foreach ( $actions as $action ) {
			switch ( $action ) {
				case 'month':
					$this->months_dropdown( 'attachment' );
					break;
				case 'mla_filter_term':
					echo self::mla_get_taxonomy_filter_dropdown( isset( $_REQUEST['mla_filter_term'] ) ? $_REQUEST['mla_filter_term'] : 0 );
					break;
				case 'mla_filter':
					submit_button( __( 'Filter', 'media-library-assistant' ), 'secondary', 'mla_filter', false, array( 'id' => 'post-query-submit' ) );
					break;
				case 'terms_search':
					submit_button( __( 'Terms Search', 'media-library-assistant' ), 'secondary', 'mla_terms_search', false, array(
					 'id' => 'mla-terms-search-open', 'onclick' => 'mlaTaxonomy.termsSearch.open()' 
				) );
					break;
				case 'clear_filter_by':
					submit_button( __( 'Clear Filter-by', 'media-library-assistant' ), 'button apply', 'clear_filter_by', false );
					break;
				case 'delete_all':
					submit_button( __( 'Empty Trash', 'media-library-assistant' ), 'button apply', 'delete_all', false );
					break;
				default:
					do_action( 'mla_list_table_extranav_custom_action', $action, $which );
			}
		}

		echo ( '</div>' );
	}

	/**
	 * Prepares the list of items for displaying
	 *
	 * This is where you prepare your data for display. This method will usually
	 * be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args().
	 *
	 * @since 0.1
	 */
	function prepare_items( ) {
		// Initialize $this->_column_headers
		$this->get_column_info();

		/*
		 * Calculate and filter pagination arguments.
		 */
		$user = get_current_user_id();
		$option = $this->screen->get_option( 'per_page', 'option' );
		$per_page = (integer) get_user_meta( $user, $option, true );
		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = (integer) $this->screen->get_option( 'per_page', 'default' );
		}

		$current_page = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;

		$pagination = apply_filters_ref_array( 'mla_list_table_prepare_items_pagination', array( compact( array( 'per_page', 'current_page' ) ), &$this ) );
		$per_page = isset( $pagination[ 'per_page' ] ) ? $pagination[ 'per_page' ] : $per_page;
		$current_page = isset( $pagination[ 'current_page' ] ) ? $pagination[ 'current_page' ] : $current_page;

		/*
		 * Assign sorted and paginated data to the items property, where 
		 * it can be used by the rest of the class.
		 */
		$total_items = apply_filters_ref_array( 'mla_list_table_prepare_items_total_items', array( NULL, &$this ) );
		if ( is_null( $total_items ) ) {
			$total_items = MLAQuery::mla_count_list_table_items( $_REQUEST, ( ( $current_page - 1 ) * $per_page ), $per_page );
		}

		/*
		 * Register the pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page' => $per_page, //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page ) //WE have to calculate the total number of pages
		) );

		$this->items = apply_filters_ref_array( 'mla_list_table_prepare_items_the_items', array( NULL, &$this ) );
		if ( is_null( $this->items ) ) {
			$this->items = MLAQuery::mla_query_list_table_items( $_REQUEST, ( ( $current_page - 1 ) * $per_page ), $per_page );
		}

		do_action_ref_array( 'mla_list_table_prepare_items', array( &$this ) );
	}

	/**
	 * Generates (echoes) content for a single row of the table
	 *
	 * @since .20
	 *
	 * @param object the current item
	 *
	 * @return void Echoes the row HTML
	 */
	function single_row( $item ) {
		static $row_class = '';

		// WP 4.2+ uses "striped" CSS styles to implement "alternate"
		if ( version_compare( get_bloginfo( 'version' ), '4.2', '<' ) ) {
			$row_class = ( $row_class == '' ? ' class="alternate"' : '' );
		}

		echo '<tr id="attachment-' . $item->ID . '"' . $row_class . '>';
		echo parent::single_row_columns( $item );
		echo '</tr>';
	}
} // class MLA_List_Table

/*
 * Some actions and filters are added here, when the source file is loaded, because the
 * MLA_List_Table object is created too late to be useful.
 */
add_action( 'admin_init', 'MLA_List_Table::mla_admin_init_action' );
 
add_filter( 'get_user_option_managemedia_page_' . MLACore::ADMIN_PAGE_SLUG . 'columnshidden', 'MLA_List_Table::mla_manage_hidden_columns_filter', 10, 3 );
add_filter( 'manage_media_page_' . MLACore::ADMIN_PAGE_SLUG . '_columns', 'MLA_List_Table::mla_manage_columns_filter', 10, 0 );
?>