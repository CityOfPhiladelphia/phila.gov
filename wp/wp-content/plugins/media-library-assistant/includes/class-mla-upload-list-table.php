<?php
/**
 * Media Library Assistant extended List Table class
 *
 * @package Media Library Assistant
 * @since 1.40
 */

/* 
 * The WP_List_Table class isn't automatically available to plugins
 */
if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class MLA (Media Library Assistant) Upload List Table implements the "Upload" admin settings tab
 *
 * Extends the core WP_List_Table class.
 *
 * @package Media Library Assistant
 * @since 1.40
 */
class MLA_Upload_List_Table extends WP_List_Table {
	/*
	 * These arrays define the table columns.
	 */

	/**
	 * Table column definitions
	 *
	 * This array defines table columns and titles where the key is the column slug (and class)
	 * and the value is the column's title text.
	 * 
	 * All of the columns are added to this array by MLA_Upload_List_Table::mla_admin_init_action.
	 *
	 * @since 1.40
	 *
	 * @var	array
	 */
	private static $default_columns = array();

	/**
	 * Default values for hidden columns
	 *
	 * This array is used when the user-level option is not set, i.e.,
	 * the user has not altered the selection of hidden columns.
	 *
	 * The value on the right-hand side must match the column slug, e.g.,
	 * array(0 => 'ID_parent, 1 => 'title_name').
	 * 
	 * @since 1.40
	 *
	 * @var	array
	 */
	private static $default_hidden_columns	= array(
		// 'name',
		// 'mime_type',
		'icon_type',
		// 'source',
		// 'status',
		'core_type',
		'mla_type',
		'standard_source',
		'core_icon_type',
		'description'
	);

	/**
	 * Sortable column definitions
	 *
	 * This array defines the table columns that can be sorted. The array key
	 * is the column slug that needs to be sortable, and the value is database column
	 * to sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * The array value also contains a boolean which is 'true' if the data is currently
	 * sorted by that column. This is computed each time the table is displayed.
	 *
	 * @since 1.40
	 *
	 * @var	array
	 */
	private static $default_sortable_columns = array(
		'name' => array('slug',false),
		'mime_type' => array('mime_type',false),
		'icon_type' => array('icon_type',false),
		'source' => array('source',false),
		'status'  => array('disabled',false),
		'core_type'  => array('core_type',false),
		'mla_type' => array('mla_type',false),
		'standard_source' => array('standard_source',false),
		'core_icon_type' => array('core_icon_type',false),
		'description' => array('description',false)
        );

	/**
	 * Access the default list of hidden columns
	 *
	 * @since 1.40
	 *
	 * @return	array	default list of hidden columns
	 */
	private static function _default_hidden_columns( ) {
		return self::$default_hidden_columns;
	}

	/**
	 * Return the names and display values of the sortable columns
	 *
	 * @since 1.40
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
	 * Handler for filter 'get_user_option_managesettings_page_mla-settings-menu-uploadcolumnshidden'
	 *
	 * Required because the screen.php get_hidden_columns function only uses
	 * the get_user_option result. Set when the file is loaded because the object
	 * is not created in time for the call from screen.php.
	 *
	 * @since 1.40
	 *
	 * @param	mixed	false or array with current list of hidden columns, if any
	 * @param	string	'managesettings_page_mla-settings-menucolumnshidden'
	 * @param	object	WP_User object, if logged in
	 *
	 * @return	array	updated list of hidden columns
	 */
	public static function mla_manage_hidden_columns_filter( $result, $option, $user_data ) {
		return $result ? $result : self::_default_hidden_columns();
	}

	/**
	 * Handler for filter 'manage_settings_page_mla-settings-menu_columns'
	 *
	 * This required filter dictates the table's columns and titles. Set when the
	 * file is loaded because the list_table object isn't created in time
	 * to affect the "screen options" setup.
	 *
	 * @since 1.40
	 *
	 * @return	array	list of table columns
	 */
	public static function mla_manage_columns_filter( ) {
		/*
		 * For WP 4.3+ icon will be merged with the Extension/name column
		 */
		if ( MLATest::$wp_4dot3_plus ) {
			unset( self::$default_columns['icon'] );
		}

		return self::$default_columns;
	}

	/**
	 * Builds the $default_columns array with translated source texts.
	 *
	 * Called from MLATest::initialize because the $default_columns information might be
	 * accessed from "front end" posts/pages.
	 *
	 * @since 1.71
	 *
	 * @return	void
	 */
	public static function mla_localize_default_columns_array( ) {
		/*
		 * Build the default columns array at runtime to accomodate calls to the localization functions
		 */
		self::$default_columns = array(
			'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
			'icon'   => '',		
			'name' => _x( 'Extension', 'list_table_column', 'media-library-assistant' ),
			'mime_type' => _x( 'MIME Type', 'list_table_column', 'media-library-assistant' ),
			'icon_type' => _x( 'Icon Type', 'list_table_column', 'media-library-assistant' ),
			'source' => _x( 'Source', 'list_table_column', 'media-library-assistant' ),
			'status'  => _x( 'Status', 'list_table_column', 'media-library-assistant' ),
			'core_type'  => _x( 'WordPress Type', 'list_table_column', 'media-library-assistant' ),
			'mla_type' => _x( 'MLA Type', 'list_table_column', 'media-library-assistant' ),
			'standard_source' => _x( 'Std. Source', 'list_table_column', 'media-library-assistant' ),
			'core_icon_type' => _x( 'Std. Icon Type', 'list_table_column', 'media-library-assistant' ),
			'description' => _x( 'Description', 'list_table_column', 'media-library-assistant' )
		);
	}

	/**
	 * Print optional in-lne styles for Uploads submenu table
	 *
	 * @since 2.14
	 */
	public static function mla_admin_print_styles_action() {
		if ( MLATest::$wp_4dot3_plus ) {
			echo "<style type='text/css'>\n";

			// Any icon_type will do
			$image_info = MLAMime::mla_get_icon_type_size( 'image' );

			/*
			 * Primary column including icon and some margin
			 */
			$icon_width = ( $image_info['width'] + 10 ) . 'px';
			$icon_height = ( $image_info['height'] + 5 ) . 'px';

			echo "  table.upload_types td.column-primary {\n";
			echo "    position: relative;\n";
			echo "  }\n";
			echo "  table.upload_types div.upload_types-icon {\n";
			echo "    position: absolute;\n";
			echo "    top: 8px;\n";
			echo "    left: 10px;\n";
			echo "  }\n";
			echo "  table.upload_types div.upload_types-info {\n";
			echo "    margin-left: {$icon_width};\n";
			echo "    min-height: {$icon_height};\n";
			echo "  }\n";

			echo "</style>\n";
		}
	}

	/**
	 * Called in the admin_init action because the list_table object isn't
	 * created in time to affect the "screen options" setup.
	 *
	 * @since 1.40
	 *
	 * @return	void
	 */
	public static function mla_admin_init_action( ) {
 		if ( isset( $_REQUEST['mla-optional-uploads-display'] ) || isset( $_REQUEST['mla-optional-uploads-search'] ) ) {
			return;
		}

		if ( isset( $_REQUEST['mla_tab'] ) && $_REQUEST['mla_tab'] == 'upload' ) {
			add_filter( 'get_user_option_managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-uploadcolumnshidden', 'MLA_Upload_list_Table::mla_manage_hidden_columns_filter', 10, 3 );
			add_filter( 'manage_settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-upload_columns', 'MLA_Upload_list_Table::mla_manage_columns_filter', 10, 0 );
			add_action( 'admin_print_styles', 'MLA_Upload_List_Table::mla_admin_print_styles_action' );
		}
	}

	/**
	 * Initializes some properties from $_REQUEST variables, then
	 * calls the parent constructor to set some default configs.
	 *
	 * @since 1.40
	 *
	 * @return	void
	 */
	function __construct( ) {
		// MLA does not use this
		$this->modes = array(
			'list' => __( 'List View' ),
		);

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'upload_type', //singular name of the listed records
			'plural' => 'upload_types', //plural name of the listed records
			'ajax' => true, //does this table support ajax?
			'screen' => 'settings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-upload'
		) );

		/*
		 * NOTE: There is one add_action call at the end of this source file.
		 */
	}

	/**
	 * Checks the current user's permissions
	 *
	 * @since 2.14
	 *
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can('manage_options');
	}

	/**
	 * Get the name of the default primary column.
	 *
	 * @since 2.14
	 * @access protected
	 *
	 * @return string Name of the default primary column
	 */
	protected function get_default_primary_column_name() {
		return 'name';
	}

	/**
	 * Generate and display row actions links.
	 *
	 * @since 2.14
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
	 * Supply a column value if no column-specific function has been defined
	 *
	 * Called when the parent class can't find a method specifically built for a
	 * given column. All columns should have a specific method, so this function
	 * returns a troubleshooting message.
	 *
	 * @since 1.40
	 *
	 * @param	array	A singular item (one full row's worth of data)
	 * @param	array	The name/slug of the column to be processed
	 * @return	string	Text or HTML to be placed inside the column
	 */
	function column_default( $item, $column_name ) {
		//Show the whole array for troubleshooting purposes
		/* translators: 1: column_name 2: column_values */
		return sprintf( __( 'column_default: %1$s, %2$s', 'media-library-assistant' ), $column_name, print_r( $item, true ) );
	}

	/**
	 * Displays checkboxes for using bulk actions. The 'cb' column
	 * is given special treatment when columns are processed.
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="cb_mla_item_ID[]" value="%1$s" />',
		/*%1$s*/ $item->post_ID
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
		return MLAMime::mla_get_icon_type_image( $item->icon_type );
	}

	/**
	 * Add rollover actions to a table column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @param	string	Current column name
	 *
	 * @return	array	Names and URLs of row-level actions
	 */
	private function _build_rollover_actions( $item, $column ) {
		$actions = array();

		/*
		 * Compose view arguments
		 */

		$view_args = array(
			'page' => MLACoreOptions::MLA_SETTINGS_SLUG . '-upload',
			'mla_tab' => 'upload',
			'mla_item_slug' => urlencode( $item->slug )
		);

		if ( isset( $_REQUEST['paged'] ) ) {
			$view_args['paged'] = $_REQUEST['paged'];
		}

		if ( isset( $_REQUEST['order'] ) ) {
			$view_args['order'] = $_REQUEST['order'];
		}

		if ( isset( $_REQUEST['orderby'] ) ) {
			$view_args['orderby'] = $_REQUEST['orderby'];
		}

		$actions['edit'] = '<a href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLA::MLA_ADMIN_SINGLE_EDIT_DISPLAY, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Edit this item', 'media-library-assistant' ) . '">' . __( 'Edit', 'media-library-assistant' ) . '</a>';

		$actions['inline hide-if-no-js'] = '<a class="editinline" href="#" title="' . __( 'Edit this item inline', 'media-library-assistant' ) . '">' . __( 'Quick Edit', 'media-library-assistant' ) . '</a>';

		if ( 'custom' == $item->source ) {
			if ( empty( $item->standard_source ) ) {
				$actions['delete'] = '<a class="delete-tag"' . ' href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_DELETE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Delete this item Permanently', 'media-library-assistant' ) . '">' . __( 'Delete Permanently', 'media-library-assistant' ) . '</a>';
			} else {
				$actions['delete'] = '<a class="delete-tag"' . ' href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_DELETE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Revert to standard item', 'media-library-assistant' ) . '">' . __( 'Revert to Standard', 'media-library-assistant' ) . '</a>';
			}
		}

		return $actions;
	}

	/**
	 * Add hidden fields with the data for use in the inline editor
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 *
	 * @return	string	HTML <div> with row data
	 */
	private function _build_inline_data( $item ) {
		$inline_data = "\r\n" . '<div class="hidden" id="inline_' . $item->post_ID . "\">\r\n";
		$inline_data .= '	<div class="original_slug">' . esc_attr( $item->slug ) . "</div>\r\n";
		$inline_data .= '	<div class="slug">' . esc_attr( $item->slug ) . "</div>\r\n";
		$inline_data .= '	<div class="mime_type">' . esc_attr( $item->mime_type ) . "</div>\r\n";
		$inline_data .= '	<div class="icon_type">' . esc_attr( $item->icon_type ) . "</div>\r\n";
		$inline_data .= '	<div class="core_type">' . esc_attr( $item->core_type ) . "</div>\r\n";
		$inline_data .= '	<div class="mla_type">' . esc_attr( $item->mla_type ) . "</div>\r\n";
		$inline_data .= '	<div class="source">' . esc_attr( $item->source ) . "</div>\r\n";
		$inline_data .= '	<div class="standard_source">' . esc_attr( $item->standard_source ) . "</div>\r\n";
		$inline_data .= '	<div class="disabled">' . esc_attr( $item->disabled ) . "</div>\r\n";
		$inline_data .= '	<div class="description">' . esc_attr( $item->description ) . "</div>\r\n";
		$inline_data .= '	<div class="wp_icon_type">' . esc_attr( $item->wp_icon_type ) . "</div>\r\n";
		$inline_data .= '	<div class="mla_icon_type">' . esc_attr( $item->mla_icon_type ) . "</div>\r\n";
		$inline_data .= '	<div class="core_icon_type">' . esc_attr( $item->core_icon_type ) . "</div>\r\n";
		$inline_data .= "</div>\r\n";
		return $inline_data;
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_name( $item ) {
		if ( MLATest::$wp_4dot3_plus ) {
			$content = "<div class=\"upload_types-icon\">\n";
			$content .= self::column_icon( $item );
			$content .= "\n</div>\n";
			$content .= '<div class="upload_types-info">' . esc_attr( $item->slug ) . "</div>\n";
			return $content;
		}

		$row_actions = self::_build_rollover_actions( $item, 'name' );
		$slug = esc_attr( $item->slug );
		return sprintf( '%1$s<br>%2$s%3$s', /*%1$s*/ $slug, /*%2$s*/ $this->row_actions( $row_actions ), /*%3$s*/ $this->_build_inline_data( $item ) );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_mime_type( $item ) {
		return esc_attr( $item->mime_type );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_icon_type( $item ) {
		return esc_attr( $item->icon_type );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_source( $item ) {
		return esc_attr( $item->source );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_status( $item ) {
		if ( $item->disabled ) {
			return __( 'Inactive', 'media-library-assistant' );
		} else {
			return __( 'Active', 'media-library-assistant' );
		}
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_core_type( $item ) {
		return esc_attr( $item->core_type );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_mla_type( $item ) {
		return esc_attr( $item->mla_type );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_standard_source( $item ) {
		return (string) $item->standard_source;
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_core_icon_type( $item ) {
		return esc_attr( $item->core_icon_type );
	}

	/**
	 * Supply the content for a custom column
	 *
	 * @since 1.40
	 * 
	 * @param	object	An MLA upload_type object
	 * @return	string	HTML markup to be placed inside the column
	 */
	function column_description( $item ) {
		return esc_attr( $item->description );
	}

	/**
	 * This method dictates the table's columns and titles
	 *
	 * @since 1.40
	 * 
	 * @return	array	Column information: 'slugs'=>'Visible Titles'
	 */
	function get_columns( ) {
		return MLA_Upload_list_Table::mla_manage_columns_filter();
	}

	/**
	 * Returns the list of currently hidden columns from a user option or
	 * from default values if the option is not set
	 *
	 * @since 1.40
	 * 
	 * @return	array	Column information,e.g., array(0 => 'ID_parent, 1 => 'title_name')
	 */
	function get_hidden_columns( ) {
		$columns = get_user_option( 'managesettings_page_' . MLACoreOptions::MLA_SETTINGS_SLUG . '-uploadcolumnshidden' );

		if ( is_array( $columns ) ) {
			return $columns;
		}

		return self::_default_hidden_columns();
	}

	/**
	 * Returns an array where the  key is the column that needs to be sortable
	 * and the value is db column to sort by. Also notes the current sort column,
	 * if set.
	 *
	 * @since 1.40
	 * 
	 * @return	array	Sortable column information,e.g.,
	 * 					'slugs'=>array('data_values',boolean)
	 */
	function get_sortable_columns( ) {
		$columns = self::$default_sortable_columns;

		if ( isset( $_REQUEST['orderby'] ) ) {
			$needle = array( $_REQUEST['orderby'], false );
			$key = array_search( $needle, $columns );
			if ( $key ) {
				$columns[ $key ][ 1 ] = true;
			}
		} else {
			$columns['menu_order'][ 1 ] = true;
		}

		return $columns;
	}

	/**
	 * Returns HTML markup for one view that can be used with this table
	 *
	 * @since 1.40
	 *
	 * @param	string	View slug
	 * @param	array	count and labels for the View
	 * @param	string	Slug for current view 
	 * 
	 * @return	string | false	HTML for link to display the view, false if count = zero
	 */
	function _get_view( $view_slug, $upload_item, $current_view ) {
		static $base_url = NULL;

		$class = ( $view_slug == $current_view ) ? ' class="current"' : '';

		/*
		 * Calculate the common values once per page load
		 */
		if ( is_null( $base_url ) ) {
			/*
			 * Remember the view filters
			 */
			$base_url = 'options-general.php?page=' . MLACoreOptions::MLA_SETTINGS_SLUG . '-upload&mla_tab=upload';

			if ( isset( $_REQUEST['s'] ) ) {
				$base_url = add_query_arg( array( 's' => $_REQUEST['s'] ), $base_url );
			}
		}

		$singular = sprintf('%s <span class="count">(%%s)</span>', $upload_item['singular'] );
		$plural = sprintf('%s <span class="count">(%%s)</span>', $upload_item['plural'] );
		$nooped_plural = _n_noop( $singular, $plural, 'media-library-assistant' );
		return "<a href='" . add_query_arg( array( 'mla_upload_view' => $view_slug ), $base_url )
			. "'$class>" . sprintf( translate_nooped_plural( $nooped_plural, $upload_item['count'], 'media-library-assistant' ), number_format_i18n( $upload_item['count'] ) ) . '</a>';
	} // _get_view

	/**
	 * Returns an associative array listing all the views that can be used with this table.
	 * These are listed across the top of the page and managed by WordPress.
	 *
	 * @since 1.40
	 * 
	 * @return	array	View information,e.g., array ( id => link )
	 */
	function get_views( ) {
		/*
		 * Find current view
		 */
		$current_view = isset( $_REQUEST['mla_upload_view'] ) ? $_REQUEST['mla_upload_view'] : 'all';

		/*
		 * Generate the list of views, retaining keyword search criterion
		 */
		$s = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';
		$upload_items = MLAMime::mla_tabulate_upload_items( $s );
		$view_links = array();
		foreach ( $upload_items as $slug => $item )
			$view_links[ $slug ] = self::_get_view( $slug, $item, $current_view );

		return $view_links;
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 1.40
	 * 
	 * @return	array	Contains all the bulk actions: 'slugs'=>'Visible Titles'
	 */
	function get_bulk_actions( ) {
		$actions = array();

		$actions['edit'] = __( 'Edit', 'media-library-assistant' );
		$actions['delete'] = __( 'Delete/Revert Custom', 'media-library-assistant' );

		return $actions;
	}

	/**
	 * Prepares the list of items for displaying
	 *
	 * This is where you prepare your data for display. This method will usually
	 * be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args().
	 *
	 * @since 1.40
	 *
	 * @return	void
	 */
	function prepare_items( ) {
		$this->_column_headers = array(
			$this->get_columns(),
			$this->get_hidden_columns(),
			$this->get_sortable_columns() 
		);

		/*
		 * REQUIRED for pagination.
		 */
		$total_items = MLAMime::mla_count_upload_items( $_REQUEST );
		$user = get_current_user_id();
		$screen = get_current_screen();
		$option = $screen->get_option( 'per_page', 'option' );
		if ( is_string( $option ) ) {
			$per_page = get_user_meta( $user, $option, true );
		} else {
			$per_page = 10;
		}

		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = $screen->get_option( 'per_page', 'default' );
		}

		/*
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page' => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );

		$current_page = $this->get_pagenum();

		/*
		 * REQUIRED. Assign sorted and paginated data to the items property, where 
		 * it can be used by the rest of the class.
		 */
		$this->items = MLAMime::mla_query_upload_items( $_REQUEST, ( ( $current_page - 1 ) * $per_page ), $per_page );
	}

	/**
	 * Generates (echoes) content for a single row of the table
	 *
	 * @since 1.40
	 *
	 * @param object the current item
	 *
	 * @return void Echoes the row HTML
	 */
	function single_row( $item ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		echo '<tr id="upload-' . $item->post_ID . '"' . $row_class . '>';
		echo parent::single_row_columns( $item );
		echo '</tr>';
	}
} // class MLA_Upload_List_Table

/*
 * Actions are added here, when the source file is loaded, because the MLA_Upload_list_Table
 * object is created too late to be useful.
 */
add_action( 'admin_init', 'MLA_Upload_list_Table::mla_admin_init_action' );
?>