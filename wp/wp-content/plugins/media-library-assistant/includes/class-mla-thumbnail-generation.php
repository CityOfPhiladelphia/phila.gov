<?php
/**
 * Media Library Assistant Generate Featured Image class
 *
 * This file is conditionally loaded in MLA::initialize
 *
 * @package Media Library Assistant
 * @since 2.13
 */

/**
 * Class MLA (Media Library Assistant) Thumbnails provides support for
 * Featured IMage generation
 *
 * @package Media Library Assistant
 * @since 2.13
 */
class MLA_Thumbnail {
	/**
	 * Uniquely identifies the Thumbnails bulk action
	 *
	 * @since 2.13
	 *
	 * @var	string
	 */
	const MLA_GFI_ACTION = 'mla-generate-featured-image';

	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 2.13
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * The remaining filters are only useful for the admin section;
		 * exit in the front-end posts/pages
		 */
		if ( ! is_admin() ) {
			return;
		}

		/*
		 * Defined in /wp-admin/admin-header.php
		 */
 		add_action( 'admin_enqueue_scripts', 'MLA_Thumbnail::admin_enqueue_scripts', 10, 1 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-main.php
		  */
		add_filter( 'mla_list_table_help_template', 'MLA_Thumbnail::mla_list_table_help_template', 10, 3 );
		add_filter( 'mla_list_table_begin_bulk_action', 'MLA_Thumbnail::mla_list_table_begin_bulk_action', 10, 2 );
		add_filter( 'mla_list_table_custom_bulk_action', 'MLA_Thumbnail::mla_list_table_custom_bulk_action', 10, 3 );
		add_filter( 'mla_list_table_end_bulk_action', 'MLA_Thumbnail::mla_list_table_end_bulk_action', 10, 2 );
		add_filter( 'mla_list_table_inline_parse', 'MLA_Thumbnail::mla_list_table_inline_parse', 10, 3 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-list-table.php
		  */
		add_filter( 'mla_list_table_get_bulk_actions', 'MLA_Thumbnail::mla_list_table_get_bulk_actions', 10, 1 );
		add_filter( 'mla_list_table_submenu_arguments', 'MLA_Thumbnail::mla_list_table_submenu_arguments', 10, 2 );
	}

	/**
	 * Load the plugin's Style Sheet and Javascript files
	 *
	 * @since 2.13
	 *
	 * @param	string	Name of the page being loaded
	 *
	 * @return	void
	 */
	public static function admin_enqueue_scripts( $page_hook ) {
		global $wp_locale;

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		if ( 'media_page_mla-menu' != $page_hook ) {
			return;
		}

		if ( $wp_locale->is_rtl() ) {
			wp_register_style( 'mla-thumbnail-generation', MLA_PLUGIN_URL . 'css/mla-thumbnail-generation-rtl.css', false, MLA::CURRENT_MLA_VERSION );
		} else {
			wp_register_style( 'mla-thumbnail-generation', MLA_PLUGIN_URL . 'css/mla-thumbnail-generation.css', false, MLA::CURRENT_MLA_VERSION );
		}

		wp_enqueue_style( 'mla-thumbnail-generation' );

		wp_enqueue_script( 'mla-thumbnail-generation-scripts', MLA_PLUGIN_URL . "js/mla-thumbnail-generation-scripts{$suffix}.js", 
			array( 'jquery' ), MLA::CURRENT_MLA_VERSION, false );

		$script_variables = array(
			'error' => __( 'Error while saving the thumbnails.', 'media-library-assistant' ),
			'ntdelTitle' => __( 'Remove From', 'media-library-assistant' ) . ' ' . __( 'Generate Thumbnails', 'media-library-assistant' ),
			'noTitle' => __( '(no title)', 'media-library-assistant' ),
			'bulkTitle' => __( 'Generate Thumbnails', 'media-library-assistant' ),
			'comma' => _x( ',', 'tag_delimiter', 'media-library-assistant' ),
			'useSpinnerClass' => false,
		);

		if ( version_compare( get_bloginfo( 'version' ), '4.2', '>=' ) ) {
			$script_variables['useSpinnerClass'] = true;
		}

		wp_localize_script( 'mla-thumbnail-generation-scripts', 'mla_thumbnail_support_vars', $script_variables );
	}

	/**
	 * Options for the thumbnail generation bulk action
	 *
	 * @since 2.13
	 *
	 * @var	array
	 */
	private static $bulk_action_options = array();

	/**
	 * Items returned by custom bulk action(s)
	 *
	 * @since 2.13
	 *
	 * @var	array
	 */
	private static $bulk_action_includes = array();

	/**
	 * Load the MLA_List_Table dropdown help menu template
	 *
	 * Add the thumbnail generation options documentation.
	 *
	 * @since 2.13
	 *
	 * @param	array	$template_array NULL, to indicate no replacement template.
	 * @param	string	$file_name the complete name of the default template file.
	 * @param	string	$file_suffix the $screen->id or hook suffix part of the  template file name.
	 */
	public static function mla_list_table_help_template( $template_array, $file_name, $file_suffix ) {
		if ( 'media_page_mla-menu' != $file_suffix ) {
			return $template_array;
		}

		$template_array = MLACore::mla_load_template( $file_name );
		$help_array = MLACore::mla_load_template( 'help-for-thumbnail_generation.tpl' );

		if ( isset( $template_array['sidebar'] ) ) {
			$template_array['sidebar'] .= $help_array['sidebar'];
			unset( $help_array['sidebar'] );
		}

		return array_merge( $template_array, $help_array );
	}

	/**
	 * Begin an MLA_List_Table bulk action
	 *
	 * Prepare the thumbnail generation options.
	 *
	 * @since 2.13
	 *
	 * @param	array	$item_content	NULL, to indicate no handler.
	 * @param	string	$bulk_action	the requested action.
	 */
	public static function mla_list_table_begin_bulk_action( $item_content, $bulk_action ) {
		if ( self::MLA_GFI_ACTION != $bulk_action ) {
			return $item_content;
		}

		self::$bulk_action_options = array();
		$request_options = isset( $_REQUEST['mla_thumbnail_options'] ) ? $_REQUEST['mla_thumbnail_options'] : array();
		$request_options['ghostscript_path'] = MLACore::mla_get_option( 'ghostscript_path' );

		if ( empty( $request_options['existing_thumbnails'] ) ) {
			$request_options['existing_thumbnails'] = 'keep';
		}

		foreach ( $request_options as $key => $value ) {
			if ( ! empty( $value ) ) {
				self::$bulk_action_options[ $key ] = $value;
			}
		}

		// Convert checkboxes to booleans
		self::$bulk_action_options['best_fit'] = isset( $request_options['best_fit'] );
		self::$bulk_action_options['clear_filters'] = isset( $request_options['clear_filters'] );

		// Convert page number to frame
		if ( isset( self::$bulk_action_options['page'] ) ) {
			$page = abs( intval( self::$bulk_action_options['page'] ) );
			self::$bulk_action_options['frame'] = ( 0 < $page ) ? $page - 1 : 0;
			unset( self::$bulk_action_options['page'] );
		}

		return $item_content;
	} // mla_list_table_begin_bulk_action

	/**
	 * Process an MLA_List_Table custom bulk action
	 *
	 * Creates new items from the "Bulk Translate" list.
	 *
	 * @since 2.13
	 *
	 * @param	array	$item_content	NULL, to indicate no handler.
	 * @param	string	$bulk_action	the requested action.
	 * @param	integer	$post_id		the affected attachment.
	 *
	 * @return	object	updated $item_content. NULL if no handler, otherwise
	 *					( 'message' => error or status message(s), 'body' => '' )
	 */
	public static function mla_list_table_custom_bulk_action( $item_content, $bulk_action, $post_id ) {
		if ( self::MLA_GFI_ACTION != $bulk_action ) {
			return $item_content;
		}

		/* translators: 1: post ID */
		$item_prefix = sprintf( __( 'Item %1$d', 'media-library-assistant' ), $post_id ) . ', ';

		/*
		 * If there is a real thumbnail image, no generation is required or allowed
		 */
		$thumbnail = wp_get_attachment_image( $post_id );
		if ( ! empty( $thumbnail ) ) {
			return array( 'message' => $item_prefix . __( 'has native thumbnail.', 'media-library-assistant' ) );
		}

		/*
		 * Look for the "Featured Image" as an alternate thumbnail for PDFs, etc.
		 */
		$thumbnail = get_post_thumbnail_id( $post_id );
		if ( ! empty( $thumbnail ) ) {
			switch ( self::$bulk_action_options['existing_thumbnails'] ) {
				case 'ignore':
					break;
				case 'trash':
					delete_post_thumbnail( $post_id ); 
					wp_delete_post( absint( $thumbnail ), false );
					break;
				case 'delete':
					delete_post_thumbnail( $post_id ); 
					wp_delete_post( absint( $thumbnail ), true );
					break;
				case 'keep':
				default:
					return array( 'message' => $item_prefix . __( 'Featured Image retained.', 'media-library-assistant' ) );
			}
		}

		/*
		 * Validate the file existance and type
		 */
		$file = get_attached_file( $post_id );
		if ( empty( $file ) ) {
			/* translators: 1: ERROR tag 2: Item post ID */
			return array( 'message' => sprintf( __( '%1$s: %2$sno attached file.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $item_prefix ) );
		}

		if ( ! in_array( strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ), array( 'ai', 'eps', 'pdf', 'ps' ) ) ) {
			return array( 'message' => $item_prefix . __( 'unsupported file type.', 'media-library-assistant' ) );
		}

		/*
		 * Generate a thumbnail
		 */
		require_once( MLA_PLUGIN_PATH . 'includes/class-mla-image-processor.php' );
		$results = MLAImageProcessor::mla_handle_thumbnail_sideload( $file, self::$bulk_action_options );
		if ( ! empty( $results['error'] ) ) {
			/* translators: 1: ERROR tag 2: Item post ID */
			return array( 'message' => sprintf( __( '%1$s: %2$sthumbnail generation failed', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $item_prefix ) . ' - ' . $results['error'] );
		}

		/*
		 * Adjust the file name for the new item
		 */
		$pathinfo = pathinfo( $results['name'] );

		if ( isset( self::$bulk_action_options['suffix'] ) ) {
			$pathinfo['filename'] = sanitize_file_name( $pathinfo['filename'] . strtolower( self::$bulk_action_options['suffix'] ) );
		}

		$pathinfo['extension'] = ( 'image/jpeg' == $results['type'] ) ? 'jpg' : 'png';
		$results['name'] = $pathinfo['filename'] . '.' . $pathinfo['extension'];

		$overrides = array( 'test_form' => false, 'test_size' => true, 'test_upload' => true, );

		// move the temporary file into the uploads directory
		$results = wp_handle_sideload( $results, $overrides );

		$item_data = get_post( $post_id, ARRAY_A );
		unset( $item_data['ID'] );
		unset( $item_data['post_author'] );
		unset( $item_data['post_date'] );
		unset( $item_data['post_date_gmt'] );

		if ( isset( self::$bulk_action_options['suffix'] ) ) {
			$item_data['post_title'] .= self::$bulk_action_options['suffix'];
		}

		unset( $item_data['post_name'] );
		unset( $item_data['post_modified'] );
		unset( $item_data['post_modified_gmt'] );
		$item_parent = $item_data['post_parent'];
		unset( $item_data['post_parent'] );
		$item_data['guid'] = $results['url'];
		$item_data['post_mime_type'] = $results['type'];
		unset( $item_data['comment_count'] );
		unset( $item_data['ancestors'] );
		unset( $item_data['post_category'] );
		unset( $item_data['tags_input'] );

		// Insert the attachment.
		$item_id = wp_insert_attachment( $item_data, $results['file'], $item_parent );
		if ( empty( $item_id ) ) {
			/* translators: 1: ERROR tag 2: Item post ID */
			return array( 'message' => sprintf( __( '%1$s: %2$swp_insert_attachment failed.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $item_prefix ) );
		}

		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// Generate the metadata for the attachment, and update the database record.
		$item_data = wp_generate_attachment_metadata( $item_id, $results['file']);
		wp_update_attachment_metadata( $item_id, $item_data );

		// Assign the new item as the source item's Featured Image
		delete_post_thumbnail( $post_id );
		set_post_thumbnail( $post_id, $item_id );

		MLA_Thumbnail::$bulk_action_includes[] = $item_id;

		/* translators: 1: Item post ID, 2: new thumbnail item ID */
		$item_content = array( 'message' => sprintf( __( '%1$sthumbnail generated as new item %2$s.', 'media-library-assistant' ), $item_prefix, $item_id ) );

		return $item_content;
	} // mla_list_table_custom_bulk_action

	/**
	 * End an MLA_List_Table bulk action
	 *
	 * Add the query arguments required for the "Generated Thumbnails" filter.
	 *
	 * @since 2.13
	 *
	 * @param	array	$item_content	NULL, to indicate no handler.
	 * @param	string	$bulk_action	the requested action.
	 */
	public static function mla_list_table_end_bulk_action( $item_content, $bulk_action ) {
		if ( self::MLA_GFI_ACTION != $bulk_action ) {
			return $item_content;
		}

		if ( ! empty( MLA_Thumbnail::$bulk_action_includes ) ) {
			MLA::mla_clear_filter_by( array( 'ids' ) );

			// Reset the current view to "All" to ensure that thumbnails are displayed
			unset( $_REQUEST['post_mime_type'] );
			unset( $_POST['post_mime_type'] );
			unset( $_GET['post_mime_type'] );
			unset( $_REQUEST['meta_query'] );
			unset( $_GET['meta_query'] );
			unset( $_REQUEST['meta_slug'] );
			unset( $_GET['meta_slug'] );

			// Clear the "extra_nav" controls and the Search Media box, too
			unset( $_REQUEST['m'] );
			unset( $_POST['m'] );
			unset( $_GET['m'] );
			unset( $_REQUEST['mla_filter_term'] );
			unset( $_POST['mla_filter_term'] );
			unset( $_GET['mla_filter_term'] );
			unset( $_REQUEST['s'] );
			unset( $_POST['s'] );
			unset( $_GET['s'] );

			$_REQUEST['ids'] = MLA_Thumbnail::$bulk_action_includes;
			$_REQUEST['heading_suffix'] = __( 'Generated Thumbnails', 'media-library-assistant' );
		}

		return $item_content;
	} // mla_list_table_end_bulk_action

	/**
	 * Filter the MLA_List_Table bulk actions
	 *
	 * Adds the "Thumbnail" action to the Bulk Actions list.
	 *
	 * @since 2.13
	 *
	 * @param	array	$actions	An array of bulk actions.
	 *								Format: 'slug' => 'Label'
	 *
	 * @return	array	updated array of actions.
	 */
	public static function mla_list_table_get_bulk_actions( $actions ) {
		$actions[self::MLA_GFI_ACTION] = __( 'Thumbnail', 'media-library-assistant' );
		return $actions;
	} // mla_list_table_get_bulk_actions

	/**
	 * MLA_List_Table inline edit parse
	 *
	 * @since 2.13
	 *
	 * Adds Bulk Translate form and the Language dropdown
	 * markup used for the Quick and Bulk Edit forms.
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for the Quick and Bulk Edit forms
	 */
	public static function mla_list_table_inline_parse( $html_markup, $item_template, $item_values ) {

		/*
		 * Add the Thumbnail Generation Markup
		 */
		$page_template_array = MLACore::mla_load_template( 'mla-thumbnail-generation.tpl' );
		if ( ! is_array( $page_template_array ) ) {
			error_log( 'ERROR: mla-thumbnail-generation.tpl path = ' . var_export( plugin_dir_path( __FILE__ ) . 'mla-thumbnail-generation.tpl', true ), 0 );
			error_log( 'ERROR: mla-thumbnail-generation.tpl non-array result = ' . var_export( $page_template_array, true ), 0 );
			return $html_markup;
		}

		$page_values = array(
			'colspan' => $item_values['colspan'],
			'Generate Thumbnails' => __( 'Generate Thumbnails', 'media-library-assistant' ),
			'See Documentation' => __( 'Pull down the Help menu and select Thumbnail Generation for setting details', 'media-library-assistant' ),
			'Width' => __( 'Width', 'media-library-assistant' ),
			'Height' => __( 'Height', 'media-library-assistant' ),
			'Best Fit' => __( 'Best Fit', 'media-library-assistant' ),
			'Page' => __( 'Page', 'media-library-assistant' ),
			'Resolution' => __( 'Resolution', 'media-library-assistant' ),
			'Quality' => __( 'Quality', 'media-library-assistant' ),
			'Type' => __( 'Type', 'media-library-assistant' ),
			'Existing Items' => __( 'Existing Items', 'media-library-assistant' ),
			'Keep' => __( 'Keep', 'media-library-assistant' ),
			'Ignore' => __( 'Ignore', 'media-library-assistant' ),
			'Trash' => __( 'Trash', 'media-library-assistant' ),
			'Delete' => __( 'Delete', 'media-library-assistant' ),
			'Suffix' => __( 'Suffix', 'media-library-assistant' ),
			'default_suffix' => '-' . __( 'Thumbnail', 'media-library-assistant' ),
			'Options' => __( 'Options', 'media-library-assistant' ),
			'Clear Filter-by' => __( 'Clear Filter-by', 'media-library-assistant' ),
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
//			'Generate Thumbnails' => __( 'Generate Thumbnails', 'media-library-assistant' ),
		);
		$parse_value = MLAData::mla_parse_template( $page_template_array['page'], $page_values );

		return $html_markup . "\n" . $parse_value;
	} // mla_list_table_inline_parse

	/**
	 * Filter the "sticky" submenu URL parameters
	 *
	 * Maintains the pll_view and list of Bulk Translate items in the URLs for paging through the results.
	 *
	 * @since 2.13
	 *
	 * @param	array	$submenu_arguments	Current view, pagination and sort parameters.
	 * @param	object	$include_filters	True to include "filter-by" parameters, e.g., year/month dropdown.
	 *
	 * @return	array	updated submenu_arguments.
	 */
	public static function mla_list_table_submenu_arguments( $submenu_arguments, $include_filters ) {
		if ( isset( $_REQUEST['pll_view'] ) ) {
			$submenu_arguments['pll_view'] = $_REQUEST['pll_view'];
		}

		if ( $include_filters && ( ! empty( MLA_Thumbnail::$bulk_action_includes ) ) ) {
			$submenu_arguments['ids'] = implode( ',', MLA_Thumbnail::$bulk_action_includes );
			$submenu_arguments['heading_suffix'] = __( 'Bulk Translations', 'media-library-assistant' );
		}

		return $submenu_arguments;
	} // mla_list_table_submenu_arguments
} // Class MLA_Thumbnail

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLA_Thumbnail::initialize');
?>