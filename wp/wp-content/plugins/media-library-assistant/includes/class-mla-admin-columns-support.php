<?php
/**
 * Media Library Assistant Admin Columns (plugin) Support
 *
 * @package Media Library Assistant
 * @since 2.50
 */
defined( 'ABSPATH' ) or die();

if ( class_exists( 'ACP_Editing_Strategy' ) ) {
	/**
	 * Class Admin Columns Addon MLA (Media Library Assistant) Editing Strategy supports the Admin Columns plugin
	 *
	 * @package Media Library Assistant
	 * @since 2.50
	 */
	class ACP_Addon_MLA_Editing_Strategy extends ACP_Editing_Strategy_Post {
	
		/**
		 * Get the available items on the current page for passing them to JS
		 *
		 * @since 2.50
		 *
		 * @return array Items on the current page ([entry_id] => (array) [entry_data])
		 */
		public function get_rows() {
			$table = $this->column->get_list_screen()->get_list_table();
			$table->prepare_items();
	
			return $this->get_editable_rows( $table->items );
		}
	} // class ACP_Addon_MLA_Editing_Strategy
}

/**
 * Class Admin Columns Addon MLA (Media Library Assistant) List Screen supports the Admin Columns plugin
 *
 * @package Media Library Assistant
 * @since 2.50
 */
class AC_Addon_MLA_ListScreen extends AC_ListScreen_Media {

	/**
	 * Initializes some properties, installs filters and then
	 * calls the parent constructor to set some default configs.
	 *
	 * @since 2.50
	 */
	public function __construct() {
		parent::__construct();

		$this->set_screen_id( 'media_page_' . MLACore::ADMIN_PAGE_SLUG );
		$this->set_key( 'mla-media-assistant' );
		$this->set_group( 'media' );
		$this->set_label( __( 'Media Library Assistant' ) );
		$this->set_singular_label( __( 'Assistant' ) );
		$this->set_page( MLACore::ADMIN_PAGE_SLUG );

		/** @see MLA_List_Table */
		$this->set_list_table_class( 'MLA_List_Table' );
		
		add_action( 'acp/column_types', 'AC_Addon_MLA_ListScreen::inline_column_types', 10, 1 );
		add_action( 'acp/column_types', 'AC_Addon_MLA_ListScreen::remove_column_types', 10, 1 );
		add_action( 'ac/column_types', 'AC_Addon_MLA_ListScreen::remove_column_types', 10, 1 );
		add_filter( 'ac/column/custom_field/meta_keys', 'AC_Addon_MLA_ListScreen::remove_custom_columns', 10, 2 );
	}

	/**
	 * Contains the hook that contains the manage_value callback
	 *
	 * @since 2.50
	 */
	public function set_manage_value_callback() {
		add_filter( 'mla_list_table_column_default', array( $this, 'column_default_value' ), 100, 3 );
	}

	/**
	 * Remove duplicate columns from the Admin Columns "Custom" section
	 *
	 * @since 2.50
	 *
	 * @param AC_ListScreen $listscreen
	 */
	public static function remove_column_types( $listscreen ) {
		if ( $listscreen instanceof AC_Addon_MLA_ListScreen ) {
			$exclude = array(
				'comments',
				'title',
				'column-actions',
				'column-alternate_text',
				'column-attached_to',
				'column-author_name',
				'column-caption',
				'column-description',
				'column-file_name',
				'column-full_path',
				'column-mediaid',
				'column-mime_type',
				'column-taxonomy',

				/*
				'column-meta',
				'column-available_sizes',
				'column-dimensions',
				'column-exif_data',
				'column-file_size',
				'column-height',
				'column-image',
				'column-used_by_menu',
				'column-width',
				 */
			);

			foreach ( $exclude as $column_type ) {
				$listscreen->deregister_column_type( $column_type );
			}
		}
	}

	/**
	 * Remove duplicate columns from the Admin Columns "Custom" section
	 *
	 * @since 2.52
	 *
	 * @param array                          $keys Distinct meta keys from DB
	 * @param AC_Settings_Column_CustomField $this_customfield
	 */
	public static function remove_custom_columns( $keys, $this_customfield ) {
		// Find the fields already present in the submenu table
		$mla_columns = apply_filters( 'mla_list_table_get_columns', MLAQuery::$default_columns );
		$mla_custom = array();
		foreach ( $mla_columns as $slug => $heading ) {
			if ( 'c_' === substr( $slug, 0, 2 ) ) {
				$mla_custom[] = $heading;
			}
		}

		// Remove the fields already present in the submenu table
		foreach ( $keys as $index => $value ) {
			if ( in_array( esc_html( $value ), $mla_custom ) ) {
				unset( $keys[ $index ] );
			}
		}

		return $keys;
	}

	/**
	 * Default column headers
	 *
	 * @since 2.50
	 *
	 * @return array
	 */
	public function get_column_headers() {
		if ( ! class_exists( 'MLAQuery' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-query.php' );
			MLAQuery::initialize();
		}

		return apply_filters( 'mla_list_table_get_columns', MLAQuery::$default_columns );
	}

	/**
	 * Return the column value
	 *
	 * @param string|null $content
	 * @param WP_Post $post
	 * @param string $column_name
	 *
	 * @return string|false
	 */
	public function column_default_value( $content, $post, $column_name ) {
		if ( is_null( $content ) ) {
			$content = $this->get_display_value_by_column_name( $column_name, $post->ID );
		}

		return $content;
	}


	/**
	 * Create and return a new MLA List Table object
	 *
	 * @param array $args
	 *
	 * @return WP_List_Table|false
	 */
	public function get_list_table( $args = array() ) {
		$class = $this->get_list_table_class();

		if ( ! class_exists( $class ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-list-table.php' );
			MLA_List_Table::mla_admin_init_action();
		}

		return new $class;
	}

	/**
	 * Test for current screen = the Media/Assistant submenu screen,
	 * For Admin Columns 2.4.9+
	 *
	 * @since 2.23
	 *
	 * @param object $wp_screen
	 *
	 * @return boolean true if the Media/Assistant submenu is the current screen
	 */
	public function is_current_screen( $wp_screen ) {
		return $wp_screen && $wp_screen->id === $this->get_screen_id();
	}

	/**
	 * Return 
	 *
	 * @since 2.52
	 *
	 * @param integer $post_id
	 *
	 * @return object attachment object
	 */
	protected function get_object_by_id( $post_id ) {
		// Author column depends on this global to be set.
		global $authordata;

		$authordata = get_userdata( get_post_field( 'post_author', $post_id ) );

		if ( ! class_exists( 'MLAData' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data.php' );
			MLAData::initialize();
		}

		return (object) MLAData::mla_get_attachment_by_id( $post_id );
	}

	/**
	 * Add inline editing columns to Media/Assistant submenu table
	 *
	 * @since 2.52
	 *
	 * @param AC_ListScreen $listscreen
	 */
	public static function inline_column_types( $listscreen ) {
		if ( $listscreen instanceof AC_Addon_MLA_ListScreen ) {
			if ( class_exists( 'ACP_Editing_Model_Media_Title' ) ) {
				$listscreen->register_column_type( new ACP_Addon_MLA_Column_Title() );
				$listscreen->register_column_type( new ACP_Addon_MLA_Column_Parent() );
				$listscreen->register_column_type( new ACP_Addon_MLA_Column_MenuOrder() );
				$listscreen->register_column_type( new ACP_Addon_MLA_Column_AltText() );
				$listscreen->register_column_type( new ACP_Addon_MLA_Column_Caption() );
				$listscreen->register_column_type( new ACP_Addon_MLA_Column_Description() );
				$listscreen->register_column_type( new ACP_Addon_MLA_Column_MimeType() );
				$listscreen->register_column_type( new ACP_Addon_MLA_Column_Date() );
				$listscreen->register_column_type( new ACP_Addon_MLA_Column_Author() );
			}
		}
	}
} // class AC_Addon_MLA_ListScreen

if ( class_exists( 'ACP_Editing_Model_Media_Title' ) ) {
	/**
	 * Provides view_settings for MLA's post_title
	 *
	 * @package Media Library Assistant
	 * @since 2.52
	 */
	class ACP_Addon_MLA_Editing_Model_Media_Title extends ACP_Editing_Model_Media_Title {
	
		/**
		 * Remove JavaScript selector settings
		 */
		public function get_view_settings() {
			return array(
				'type'         => 'text',
				'display_ajax' => false,
			);
		}
	}
	
	/**
	 * Provides inline-editing for post_title
	 *
	 * @package Media Library Assistant
	 * @since 2.52
	 */
	class ACP_Addon_MLA_Column_Title extends AC_Column_Media_Title
		implements ACP_Column_EditingInterface {
	
		/**
		 * Define column properties
		 */
		public function __construct() {
	
			// Mark as an existing column
			$this->set_original( true );
	
			// Type of column
			$this->set_type( 'post_title' );
		}
	
		/**
		 * Add inline editing support
		 *
		 * @return ACP_Editing_Model_Media_Title
		 */
		public function editing() {
			return new ACP_Addon_MLA_Editing_Model_Media_Title( $this );
		}
	
	}
	
	/**
	 * Removes ACP defaults for parent
	 *
	 * @package Media Library Assistant
	 * @since 2.52
	 */
	class ACP_Addon_MLA_Column_Parent extends AC_Column_Media_Parent {
		/**
		 * Remove default column width
		 */
		public function register_settings() {
		}
	}
	
	/**
	 * Provides inline-editing for menu_order
	 *
	 * @package Media Library Assistant
	 * @since 2.52
	 */
	class ACP_Addon_MLA_Column_MenuOrder extends AC_Column
		implements ACP_Column_EditingInterface {
	
		/**
		 * Define column properties
		 */
		public function __construct() {
	
			// Mark as an existing column
			$this->set_original( true );
	
			// Type of column
			$this->set_type( 'menu_order' );
		}
	
		/**
		 * Add inline editing support
		 *
		 * @return ACP_Editing_Model_Post_Order
		 */
		public function editing() {
			return new ACP_Editing_Model_Post_Order( $this );
		}
	
	}
	
	/**
	 * Provides inline-editing for alt_text
	 *
	 * @package Media Library Assistant
	 * @since 2.52
	 */
	class ACP_Addon_MLA_Column_AltText extends ACP_Column_Media_AlternateText
		implements ACP_Column_EditingInterface {
	
		/**
		 * Define column properties
		 */
		public function __construct() {
	
			// Mark as an existing column
			$this->set_original( true );
	
			// Type of column
			$this->set_type( 'alt_text' );
		}
	}
	
	/**
	 * Provides inline-editing for caption
	 *
	 * @package Media Library Assistant
	 * @since 2.52
	 */
	class ACP_Addon_MLA_Column_Caption extends ACP_Column_Media_Caption
		implements ACP_Column_EditingInterface {
	
		/**
		 * Define column properties
		 */
		public function __construct() {
	
			// Mark as an existing column
			$this->set_original( true );
	
			// Type of column
			$this->set_type( 'caption' );
		}
	}
	
	/**
	 * Provides inline-editing for caption
	 *
	 * @package Media Library Assistant
	 * @since 2.52
	 */
	class ACP_Addon_MLA_Column_Description extends AC_Column_Media_Description
		implements ACP_Column_EditingInterface {
	
		/**
		 * Define column properties
		 */
		public function __construct() {
	
			// Mark as an existing column
			$this->set_original( true );
	
			// Type of column
			$this->set_type( 'description' );
		}
	
		/**
		 * Add inline editing support
		 *
		 * @return ACP_Editing_Model_Post_Content
		 */
		public function editing() {
			return new ACP_Editing_Model_Post_Content( $this );
		}
	}
	
	/**
	 * Provides inline-editing for caption
	 *
	 * @package Media Library Assistant
	 * @since 2.52
	 */
	class ACP_Addon_MLA_Column_MimeType extends AC_Column_Media_MimeType
		implements ACP_Column_EditingInterface {
	
		/**
		 * Define column properties
		 */
		public function __construct() {
	
			// Mark as an existing column
			$this->set_original( true );
	
			// Type of column
			$this->set_type( 'post_mime_type' );
		}
	
		/**
		 * Add inline editing support
		 *
		 * @return ACP_Editing_Model_Post_Content
		 */
		public function editing() {
			return new ACP_Editing_Model_Media_MimeType( $this );
		}
	}
	
	/**
	 * Removes ACP defaults for date
	 *
	 * @package Media Library Assistant
	 * @since 2.52
	 */
	class ACP_Addon_MLA_Column_Date extends ACP_Column_Media_Date {
		/**
		 * Remove default column width
		 */
		public function register_settings() {
		}
	}
	
	/**
	 * Removes ACP defaults & provides inline-editing for caption
	 *
	 * @package Media Library Assistant
	 * @since 2.52
	 */
	class ACP_Addon_MLA_Column_Author extends AC_Column_Media_Author
		implements ACP_Column_EditingInterface {
	
		/**
		 * Remove default column width
		 */
		public function register_settings() {
		}
	
		/**
		 * Add inline editing support
		 *
		 * @return ACP_Editing_Model_Post_Content
		 */
		public function editing() {
			return new ACP_Editing_Model_Post_Author( $this );
		}
	}
}
