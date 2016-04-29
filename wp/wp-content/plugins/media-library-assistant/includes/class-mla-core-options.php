<?php
/**
 * Media Library Assistant Option Definitions
 *
 * @package Media Library Assistant
 * @since 2.25
 */
defined( 'ABSPATH' ) or die();

/**
 * Class MLA (Media Library Assistant) Core Options defines MLA option settings and defaults
 *
 * @package Media Library Assistant
 * @since 2.25
 */
class MLACoreOptions {
	/**
	 * Provides a unique name for the settings page
	 *
	 * @since 2.20
	 *
	 * @var	string
	 */
	const MLA_SETTINGS_SLUG = 'mla-settings-menu';

	/**
	 * Provides a unique name for the current version option
	 */
	const MLA_VERSION_OPTION = 'current_version';

	/**
	 * Provides a unique name for the exclude revisions option
	 */
	const MLA_EXCLUDE_REVISIONS = 'exclude_revisions';

	/**
	 * Provides a unique name for a database tuning option
	 */
	const MLA_FEATURED_IN_TUNING = 'featured_in_tuning';

	/**
	 * Provides a unique name for a database tuning option
	 */
	const MLA_INSERTED_IN_TUNING = 'inserted_in_tuning';

	/**
	 * Provides a unique name for a database tuning option
	 */
	const MLA_GALLERY_IN_TUNING = 'gallery_in_tuning';

	/**
	 * Provides a unique name for a database tuning option
	 */
	const MLA_MLA_GALLERY_IN_TUNING = 'mla_gallery_in_tuning';

	/**
	 * Provides a unique name for the taxonomy count Attachments option
	 */
	const MLA_COUNT_TERM_ATTACHMENTS = 'count_term_attachments';

	/**
	 * Provides a unique name for the taxonomy support option
	 */
	const MLA_TAXONOMY_SUPPORT = 'taxonomy_support';

	/**
	 * Provides a unique name for the admin screen page title option
	 */
	const MLA_SCREEN_PAGE_TITLE = 'admin_screen_page_title';

	/**
	 * Provides a unique name for the admin screen menu title option
	 */
	const MLA_SCREEN_MENU_TITLE = 'admin_screen_menu_title';

	/**
	 * Provides a unique name for the admin screen menu order option
	 */
	const MLA_SCREEN_ORDER = 'admin_screen_menu_order';

	/**
	 * Provides a unique name for the admin screen remove Media/Library option
	 */
	const MLA_SCREEN_DISPLAY_LIBRARY = 'admin_screen_display_default';

	/**
	 * Provides a unique name for the Media/Assistant list/grid switcher option
	 */
	const MLA_SCREEN_DISPLAY_SWITCHER = 'admin_screen_display_switcher';

	/**
	 * Provides a unique name for the default orderby option
	 */
	const MLA_DEFAULT_ORDERBY = 'default_orderby';

	/**
	 * Provides a unique name for the default order option
	 */
	const MLA_DEFAULT_ORDER = 'default_order';

	/**
	 * Provides a unique name for the Media/Assistant submenu table views width option
	 */
	const MLA_TABLE_VIEWS_WIDTH = 'table_views_width';

	/**
	 * Provides a unique name for the Media/Assistant submenu table thumbnail/icon size option
	 */
	const MLA_TABLE_ICON_SIZE = 'table_icon_size';

	/**
	 * Provides a unique name for the Bulk Update and Map All chunk size option
	 */
	const MLA_BULK_CHUNK_SIZE = 'bulk_chunk_size';

	/**
	 * Provides a unique name for the taxonomy filter maximum depth option
	 */
	const MLA_TAXONOMY_FILTER_DEPTH = 'taxonomy_filter_depth';

	/**
	 * Provides a unique name for the taxonomy filter maximum depth option
	 */
	const MLA_TAXONOMY_FILTER_INCLUDE_CHILDREN = 'taxonomy_filter_include_children';

	/**
	 * Provides a unique name for the display Search Media controls option
	 */
	const MLA_SEARCH_MEDIA_FILTER_SHOW_CONTROLS = 'search_media_filter_show_controls';

	/**
	 * Provides a unique name for the display Search Media controls option
	 */
	const MLA_SEARCH_MEDIA_FILTER_DEFAULTS = 'search_media_filter_defaults';

	/**
	 * Provides a "size" attribute value for the EXIF/Template Value field
	 */
	const MLA_EXIF_SIZE = 30;

	/**
	 * Provides a unique name for the Custom Field "new rule" key
	 */
	const MLA_NEW_CUSTOM_RULE = '__NEW_RULE__';

	/**
	 * Provides a unique name for the Custom Field "new field" key
	 */
	const MLA_NEW_CUSTOM_FIELD = '__NEW_FIELD__';

	/**
	 * Provides a unique name for the "searchable taxonomies" option
	 */
	const MLA_EDIT_MEDIA_SEARCH_TAXONOMY = 'edit_media_search_taxonomy';

	/**
	 * Provides a unique name for the Edit Media additional meta boxes option
	 */
	const MLA_EDIT_MEDIA_META_BOXES = 'edit_media_meta_boxes';

	/**
	 * Provides a unique name for the Media/Add New bulk edit option
	 */
	const MLA_ADD_NEW_BULK_EDIT = 'add_new_bulk_edit';

	/**
	 * Provides a unique name for the Media/Add New bulk edit "on top" option
	 */
	const MLA_ADD_NEW_BULK_EDIT_ON_TOP = 'add_new_bulk_edit_on_top';

	/**
	 * Provides a unique name for the Media/Add New bulk edit "Open Automatically" option
	 */
	const MLA_ADD_NEW_BULK_EDIT_AUTO_OPEN = 'add_new_bulk_edit_auto_open';

	/**
	 * Provides a unique name for the Media Grid toolbar option, which
	 * also controls the ATTACHMENT DETAILS enhancements
	 */
	const MLA_MEDIA_GRID_TOOLBAR = 'media_grid_toolbar';

	/**
	 * Provides a unique name for the Media Manager toolbar option, which
	 * also controls the ATTACHMENT DETAILS enhancements
	 */
	const MLA_MEDIA_MODAL_TOOLBAR = 'media_modal_toolbar';

	/**
	 * Provides a unique name for the Media Manager toolbar MIME Types option
	 */
	const MLA_MEDIA_MODAL_MIMETYPES = 'media_modal_mimetypes';

	/**
	 * Provides a unique name for the Media Manager toolbar Month and Year option
	 */
	const MLA_MEDIA_MODAL_MONTHS = 'media_modal_months';

	/**
	 * Provides a unique name for the Media Manager toolbar Taxonomy Terms option
	 */
	const MLA_MEDIA_MODAL_TERMS = 'media_modal_terms';

	/**
	 * Provides a unique name for the Media Manager toolbar Taxonomy "Terms Search" option
	 */
	const MLA_MEDIA_MODAL_TERMS_SEARCH = 'media_modal_terms_search';

	/**
	 * Provides a unique name for the Media Manager toolbar Search Box option
	 */
	const MLA_MEDIA_MODAL_SEARCHBOX = 'media_modal_searchbox';

	/**
	 * Provides a unique name for the Media Manager toolbar Search Box Controls option
	 */
	const MLA_MEDIA_MODAL_SEARCHBOX_CONTROLS = 'media_modal_searchbox_controls';

	/**
	 * Provides a unique name for the Media Manager Attachment Details searchable taxonomy option
	 * This option is for hierarchical taxonomies, e.g., "Att. Categories".
	 */
	const MLA_MEDIA_MODAL_DETAILS_CATEGORY_METABOX = 'media_modal_details_category_metabox';

	/**
	 * Provides a unique name for the Media Manager Attachment Details searchable taxonomy option
	 * This option is for flat taxonomies, e.g., "Att. Tags".
	 */
	const MLA_MEDIA_MODAL_DETAILS_TAG_METABOX = 'media_modal_details_tag_metabox';

	/**
	 * Provides a unique name for the Media Manager Attachment Details auto-fill option
	 */
	const MLA_MEDIA_MODAL_DETAILS_AUTOFILL = 'media_modal_details_autofill';

	/**
	 * Provides a unique name for the Media Manager orderby option
	 */
	const MLA_MEDIA_MODAL_ORDERBY = 'media_modal_orderby';

	/**
	 * Provides a unique name for the Media Manager order option
	 */
	const MLA_MEDIA_MODAL_ORDER = 'media_modal_order';

	/**
	 * Provides a unique name for the Media Manager Force Image Default Setings option
	 */
	const MLA_DELETE_OPTION_SETTINGS = 'delete_option_settings';

	/**
	 * Provides a unique name for the Media Manager Force Image Default Setings option
	 */
	const MLA_DELETE_OPTION_BACKUPS = 'delete_option_backups';

	/**
	 * Provides a unique name for the Media Manager Force Image Default Setings option
	 */
	const MLA_MEDIA_MODAL_APPLY_DISPLAY_SETTINGS = 'media_modal_apply_display_settings';

	/**
	 * Provides a unique name for the Post MIME Types option
	 */
	const MLA_POST_MIME_TYPES = 'post_mime_types';

	/**
	 * Provides a unique name for the Enable Post MIME Types option
	 */
	const MLA_ENABLE_POST_MIME_TYPES = 'enable_post_mime_types';

	/**
	 * Provides a unique name for the Upload MIME Types option
	 */
	const MLA_UPLOAD_MIMES = 'upload_mimes';

	/**
	 * Provides a unique name for the Enable Upload MIME Types option
	 */
	const MLA_ENABLE_UPLOAD_MIMES = 'enable_upload_mimes';

	/**
	 * Provides a unique name for the Enable MLA Icons option
	 */
	const MLA_ENABLE_MLA_ICONS = 'enable_mla_icons';

	/**
	 * Provides a unique name for the Debug display limit option
	 */
	const MLA_DEBUG_DISPLAY_LIMIT = 'debug_display_limit';

	/**
	 * Provides a unique name for the Debug alternate log file option
	 */
	const MLA_DEBUG_FILE = 'debug_file';

	/**
	 * Provides a unique name for the Debug replace PHP log file option
	 */
	const MLA_DEBUG_REPLACE_PHP_LOG = 'debug_replace_php_log';

	/**
	 * Provides a unique name for the Debug replace PHP error_reporting option
	 */
	const MLA_DEBUG_REPLACE_PHP_REPORTING = 'debug_replace_php_reporting';

	/**
	 * Provides a unique name for the Debug replace MLA_DEBUG_LEVEL option
	 */
	const MLA_DEBUG_REPLACE_LEVEL = 'debug_replace_level';

	/**
	 * $mla_option_definitions defines the database options and admin page areas for setting/updating them
	 *
	 * The array must be populated at runtime in MLACoreOptions::mla_localize_option_definitions_array();
	 * localization calls cannot be placed in the "public static" array definition itself.
	 *
	 * Each option is defined by an array with the following elements:
	 *
	 * array key => HTML id/name attribute and option database key (OMIT MLA_OPTION_PREFIX)
	 *
	 * tab => Settings page tab id for the option
	 * name => admin page label or heading text
	 * type => 'checkbox', 'header', 'radio', 'select', 'text', 'textarea', 'custom', 'hidden'
	 * std => default value
	 * help => help text
	 * size => text size, default 40
	 * cols => textbox columns, default 90
	 * rows => textbox rows, default 5
	 * options => array of radio or select option values
	 * texts => array of radio or select option display texts
	 * render => rendering function for 'custom' options. Usage:
	 *     $options_list .= ['render']( 'render', $key, $value );
	 * update => update function for 'custom' options; returns nothing. Usage:
	 *     $message = ['update']( 'update', $key, $value, $_REQUEST );
	 * delete => delete function for 'custom' options; returns nothing. Usage:
	 *     $message = ['delete']( 'delete', $key, $value, $_REQUEST );
	 * reset => reset function for 'custom' options; returns nothing. Usage:
	 *     $message = ['reset']( 'reset', $key, $value, $_REQUEST );
	 */
	 
	public static $mla_option_definitions = array ();

	/**
	 * Localize $mla_option_definitions array
	 *
	 * Localization must be done at runtime; these calls cannot be placed in the
	 * "public static" array definition itself. Called from MLATest::initialize.
	 *
	 * @since 2.20
	 *
	 * @return	void
	 */
	public static function mla_localize_option_definitions_array() {
		self::$mla_option_definitions = array (
			/*
			 * This option records the highest MLA version so-far installed
			 */
			self::MLA_VERSION_OPTION =>
				array('tab' => '',
					'type' => 'hidden', 
					'std' => '0'),

			/* 
			 * These checkboxes are no longer used;
			 * they are retained for the database version/update check
			 */
			'attachment_category' =>
				array('tab' => '',
					'name' => __( 'Attachment Categories', 'media-library-assistant' ),
					'type' => 'hidden', // checkbox',
					'autoload' => true,
					'std' => 'checked',
					'help' => __( 'Check this option to add support for Attachment Categories.', 'media-library-assistant' )),

			'attachment_tag' =>
				array('tab' => '',
					'name' => __( 'Attachment Tags', 'media-library-assistant' ),
					'type' => 'hidden', // checkbox',
					'autoload' => true,
					'std' => 'checked',
					'help' => __( 'Check this option to add support for Attachment Tags.'), 'media-library-assistant' ),

			'where_used_header' =>
				array('tab' => 'general',
					'name' => __( 'Where-used Reporting', 'media-library-assistant' ),
					'type' => 'header'),

			self::MLA_EXCLUDE_REVISIONS =>
				array('tab' => 'general',
					'name' => __( 'Exclude Revisions', 'media-library-assistant' ),
					'type' => 'checkbox',
					'autoload' => true,
					'std' => 'checked',
					'help' => __( 'Check this option to exclude revisions from where-used reporting.', 'media-library-assistant' )),

			'where_used_subheader' =>
				array('tab' => 'general',
					'name' => __( 'Where-used database access tuning', 'media-library-assistant' ),
					'type' => 'subheader'),

			self::MLA_FEATURED_IN_TUNING =>
				array('tab' => 'general',
					'name' => __( 'Featured in', 'media-library-assistant' ),
					'type' => 'select',
					'autoload' => true,
					'std' => 'disabled',
					'options' => array('enabled', 'disabled'),
					'texts' => array( __( 'Enabled', 'media-library-assistant' ), __( 'Disabled', 'media-library-assistant' ) ),
					'help' => __( 'Search database posts and pages for Featured Image attachments.', 'media-library-assistant' )),

			self::MLA_INSERTED_IN_TUNING =>
				array('tab' => 'general',
					'name' => __( 'Inserted in', 'media-library-assistant' ),
					'type' => 'select',
					'autoload' => true,
					'std' => 'disabled',
					'options' => array('enabled', 'base', 'disabled'),
					'texts' => array( __( 'Enabled', 'media-library-assistant' ), __( 'Base', 'media-library-assistant' ), __( 'Disabled', 'media-library-assistant' ) ),
					'help' => __( 'Search database posts and pages for attachments embedded in content.<br>&nbsp;&nbsp;Base = ignore intermediate size suffixes; use path, base name and extension only.', 'media-library-assistant' )),

			self::MLA_GALLERY_IN_TUNING =>
				array('tab' => 'general',
					'name' => __( 'Gallery in', 'media-library-assistant' ),
					'type' => 'select',
					'autoload' => true,
					'std' => 'disabled',
					'options' => array('dynamic', 'refresh', 'cached', 'disabled'),
					'texts' => array( __( 'Dynamic', 'media-library-assistant' ), __( 'Refresh', 'media-library-assistant' ), __( 'Cached', 'media-library-assistant' ), __( 'Disabled', 'media-library-assistant' ) ),
					'help' => __( 'Search database posts and pages for [ gallery ] shortcode results.<br>&nbsp;&nbsp;Dynamic = once every page load, Cached = once every login, Disabled = never.<br>&nbsp;&nbsp;Refresh = update references, then set to Cached.', 'media-library-assistant' )),

			self::MLA_MLA_GALLERY_IN_TUNING =>
				array('tab' => 'general',
					'name' => __( 'MLA Gallery in', 'media-library-assistant' ),
					'type' => 'select',
					'autoload' => true,
					'std' => 'disabled',
					'options' => array('dynamic', 'refresh', 'cached', 'disabled'),
					'texts' => array( __( 'Dynamic', 'media-library-assistant' ), __( 'Refresh', 'media-library-assistant' ), __( 'Cached', 'media-library-assistant' ), __( 'Disabled', 'media-library-assistant' ) ),
					'help' => __( 'Search database posts and pages for [mla_gallery] shortcode results.<br>&nbsp;&nbsp;Dynamic = once every page load, Cached = once every login, Disabled = never.<br>&nbsp;&nbsp;Refresh = update references, then set to Cached.', 'media-library-assistant' )),

			'taxonomy_header' =>
				array('tab' => 'general',
					'name' => __( 'Taxonomy Support', 'media-library-assistant' ),
					'type' => 'header'),

			self::MLA_COUNT_TERM_ATTACHMENTS =>
				array('tab' => 'general',
					'name' => __( 'Compute Attachments Column', 'media-library-assistant' ),
					'type' => 'checkbox',
					'autoload' => true,
					'std' => 'checked',
					'help' => __( 'Check this option to calculate attachments per term in the Attachments Column.', 'media-library-assistant' )),

			self::MLA_TAXONOMY_SUPPORT =>
				array('tab' => 'general',
					'help' => __( 'Check the "<strong>Support</strong>" box to add the taxonomy to the Assistant and the Edit Media screen.', 'media-library-assistant' ) . '<br>' .
						__( 'Check the "<strong>Inline Edit</strong>" box to display the taxonomy in the Quick Edit and Bulk Edit areas.', 'media-library-assistant' ) . '<br>' .
						__( 'Check the "<strong>Term Search</strong>" box to add the taxonomy to the "Search Media/Terms" list.', 'media-library-assistant' ) . 
						sprintf( ' %1$s <a href="%2$s">%3$s</a>.',  __( 'For complete documentation', 'media-library-assistant' ), admin_url( 'options-general.php?page=' . self::MLA_SETTINGS_SLUG . '-documentation&amp;mla_tab=documentation#terms_search' ), __( 'click here', 'media-library-assistant' ) )
 . '<br>' .
						__( 'Check the "<strong>Checklist</strong>" box to enable the checklist-style meta box for a flat taxonomy.', 'media-library-assistant' ) . '&nbsp;' .
						__( 'You must also check the <strong>"Enable enhanced checklist taxonomies"</strong> box below to enable this feature.', 'media-library-assistant' ) . '<br>' .
						__( 'Check the "<strong>Checked On Top</strong>" box to moved checked terms to the top of the checklist-style meta box.', 'media-library-assistant' ) . '<br>' .
						__( 'Use the "<strong>List Filter</strong>" option to select the taxonomy on which to filter the Assistant table listing.', 'media-library-assistant' ),
 					'std' =>  array (
						'tax_support' => array (
							'attachment_category' => 'checked',
							'attachment_tag' => 'checked',
						  ),
						'tax_quick_edit' => array (
							'attachment_category' => 'checked',
							'attachment_tag' => 'checked',
						),
						'tax_term_search' => array (
							'attachment_category' => 'checked',
							'attachment_tag' => 'checked',
						),
						'tax_flat_checklist' => array(),
						'tax_checked_on_top' => NULL, // default "true", handled in mla_initialize_tax_checked_on_top
						'tax_filter' => 'attachment_category'
						), 
					'type' => 'custom',
					'render' => 'mla_taxonomy_option_handler',
					'update' => 'mla_taxonomy_option_handler',
					'delete' => 'mla_taxonomy_option_handler',
					'reset' => 'mla_taxonomy_option_handler'),

			'media_assistant_header' =>
				array('tab' => 'general',
					'name' => __( 'Media/Assistant Screen Options', 'media-library-assistant' ),
					'type' => 'header'),

			'admin_sidebar_subheader' =>
				array('tab' => 'general',
					'name' => __( 'Admin Menu Options', 'media-library-assistant' ),
					'type' => 'subheader'),

			self::MLA_SCREEN_PAGE_TITLE =>
				array('tab' => 'general',
					'name' => __( 'Page Title', 'media-library-assistant' ),
					'type' => 'text',
					'autoload' => true,
					'std' => __( 'Media Library Assistant', 'media-library-assistant' ),
					'size' => 40,
					'help' => __( 'Enter the title for the Media/Assistant submenu page', 'media-library-assistant' )),

			self::MLA_SCREEN_MENU_TITLE =>
				array('tab' => 'general',
					'name' => __( 'Menu Title', 'media-library-assistant' ),
					'type' => 'text',
					'autoload' => true,
					'std' => __( 'Assistant', 'media-library-assistant' ),
					'size' => 20,
					'help' => __( 'Enter the title for the Media/Assistant submenu entry', 'media-library-assistant' )),

			self::MLA_SCREEN_ORDER =>
				array('tab' => 'general',
					'name' => __( 'Submenu Order', 'media-library-assistant' ),
					'type' => 'text',
					'autoload' => true,
					'std' => '0',
					'size' => 2,
					'help' => __( 'Enter the position of the Media/Assistant submenu entry.<br>&nbsp;&nbsp;0 = natural order (at bottom),&nbsp;&nbsp;&nbsp;&nbsp;1 - 4 = at top<br>&nbsp;&nbsp;6-9 = after "Library",&nbsp;&nbsp;&nbsp;&nbsp;11-16 = after "Add New"', 'media-library-assistant' )),

			self::MLA_SCREEN_DISPLAY_LIBRARY =>
				array('tab' => 'general',
					'name' => __( 'Display Media/Library', 'media-library-assistant' ),
					'type' => 'checkbox',
					'autoload' => true,
					'std' => 'checked',
					'help' => __( 'Check/uncheck this option to display/remove the WordPress Media/Library submenu entry.', 'media-library-assistant' )),

			self::MLA_SCREEN_DISPLAY_SWITCHER =>
				array('tab' => 'general',
					'name' => __( 'Display Media/Assistant list/grid view switcher', 'media-library-assistant' ),
					'type' => 'checkbox',
					'autoload' => true,
					'std' => '',
 					'help' => __( 'Check/uncheck this option to display/remove the "list/grid" view switcher on the Media/Assistant submenu.', 'media-library-assistant' )),

			'table_defaults_subheader' =>
				array('tab' => 'general',
					'name' => __( 'Table Defaults', 'media-library-assistant' ),
					'type' => 'subheader'),

			self::MLA_DEFAULT_ORDERBY =>
				array('tab' => 'general',
					'name' => __( 'Order By', 'media-library-assistant' ),
					'type' => 'select',
					'autoload' => true,
					'std' => 'ID',
					'options' => array('none', 'ID'),
					'texts' => array( __( 'None', 'media-library-assistant' ), __( 'ID/Parent', 'media-library-assistant' ) ),
					'help' => __( 'Select the column for the sort order of the Assistant table listing.', 'media-library-assistant' )),

			self::MLA_DEFAULT_ORDER =>
				array('tab' => 'general',
					'name' => __( 'Order', 'media-library-assistant' ),
					'type' => 'radio',
					'autoload' => true,
					'std' => 'DESC',
					'options' => array('ASC', 'DESC'),
					'texts' => array( __( 'Ascending', 'media-library-assistant' ), __( 'Descending', 'media-library-assistant' ) ),
					'help' => __( 'Choose the sort order.', 'media-library-assistant' )),

			self::MLA_TABLE_VIEWS_WIDTH =>
				array('tab' => 'general',
					'name' => __( 'Views Width', 'media-library-assistant' ),
					'type' => 'text',
					'autoload' => true,
					'std' => '',
					'size' => 10,
					'help' => __( 'Enter the width for the views list, in pixels (px) or percent (%)', 'media-library-assistant' )),

			self::MLA_TABLE_ICON_SIZE =>
				array('tab' => 'general',
					'name' => __( 'Icon Size', 'media-library-assistant' ),
					'type' => 'text',
					'autoload' => true,
					'std' => '',
					'size' => 5,
					'help' => __( 'Enter the size of the thumbnail/icon images, in pixels', 'media-library-assistant' )),

			self::MLA_BULK_CHUNK_SIZE =>
				array('tab' => 'general',
					'name' => __( 'Bulk Chunk Size', 'media-library-assistant' ),
					'type' => 'text',
					'autoload' => true,
					'std' => '25',
					'size' => 5,
					'help' => __( 'Enter the size of the Bulk Edit and Map All processing chunks', 'media-library-assistant' )),

			'taxonomy_filter_subheader' =>
				array('tab' => 'general',
					'name' => __( 'Taxonomy Filter parameters', 'media-library-assistant' ),
					'type' => 'subheader'),

			self::MLA_TAXONOMY_FILTER_DEPTH =>
				array('tab' => 'general',
					'name' => __( 'Maximum Depth', 'media-library-assistant' ),
					'type' => 'text',
					'autoload' => true,
					'std' => '3',
					'size' => 2,
					'help' => __( 'Enter the number of levels displayed for hierarchial taxonomies; enter zero for no limit.', 'media-library-assistant' )),

			self::MLA_TAXONOMY_FILTER_INCLUDE_CHILDREN =>
				array('tab' => 'general',
					'name' => __( 'Include Children', 'media-library-assistant' ),
					'type' => 'checkbox',
					'autoload' => true,
					'std' => 'checked',
					'help' => __( 'Check/uncheck this option to include/exclude children for hierarchical taxonomies.', 'media-library-assistant' )),

			'search_media_subheader' =>
				array('tab' => 'general',
					'name' => __( 'Search Media Defaults', 'media-library-assistant' ),
					'type' => 'subheader'),

			self::MLA_SEARCH_MEDIA_FILTER_SHOW_CONTROLS =>
				array('tab' => 'general',
					'name' => __( 'Display Search Controls', 'media-library-assistant' ),
					'type' => 'checkbox',
					'autoload' => true,
					'std' => 'checked',
					'help' => __( 'Check/uncheck this option to display/hide the and/or connector and search fields controls.', 'media-library-assistant' )),

			self::MLA_SEARCH_MEDIA_FILTER_DEFAULTS =>
				array('tab' => 'general',
					'help' => __( 'Use these controls to set defaults for the and/or connector and search fields controls.<br>These defaults will be used for the Search Media boxes on both the Media/Assistant submenu<br>and the Media Manager Modal Window.', 'media-library-assistant' ),
					'autoload' => true,
					'std' =>  array (
						'search_connector' => 'AND',
						'search_fields' => array ( 'title', 'content' ),
						), 
					'type' => 'custom',
					'render' => 'mla_search_option_handler',
					'update' => 'mla_search_option_handler',
					'delete' => 'mla_search_option_handler',
					'reset' => 'mla_search_option_handler'),

			'edit_media_header' =>
				array('tab' => 'general',
					'name' => __( 'Media/Edit Media Enhancements', 'media-library-assistant' ),
					'type' => 'header'),

			self::MLA_EDIT_MEDIA_SEARCH_TAXONOMY =>
				array('tab' => 'general',
					'name' => __( 'Enable &quot;enhanced checklist&quot; taxonomies', 'media-library-assistant' ),
					'type' => 'checkbox',
					'autoload' => true,
					'std' => 'checked',
					'help' => __( 'Check this option to enable the "? Search" feature for hierarchical taxonomies, e.g., Att. Categories.<br>&nbsp;&nbsp;This option also enables the "checklist-style" support for flat taxonomies, e.g., Att. Tags.', 'media-library-assistant' )),

			self::MLA_EDIT_MEDIA_META_BOXES =>
				array('tab' => 'general',
					'name' => __( 'Enable Edit Media additional meta boxes', 'media-library-assistant' ),
					'type' => 'checkbox',
					'autoload' => true,
					'std' => 'checked',
					'help' => __( 'Check this option to add "Parent Info", "Menu Order", "Attachment Metadata" and four "where-used" meta boxes to the Edit Media screen.', 'media-library-assistant' ) . '<br>&nbsp;&nbsp;' .
						__( 'You can also use Filters to customize the meta boxes.', 'media-library-assistant' ) . 
						sprintf( ' %1$s <a href="%2$s">%3$s</a>.',  __( 'For complete documentation', 'media-library-assistant' ), admin_url( 'options-general.php?page=' . self::MLA_SETTINGS_SLUG . '-documentation&amp;mla_tab=documentation#mla_edit_meta_boxes' ), __( 'click here', 'media-library-assistant' )  ) ),

			'media_add_new_header' =>
				array('tab' => 'general',
					'name' => __( 'Media/Add New Enhancements', 'media-library-assistant' ),
					'type' => 'header'),

			self::MLA_ADD_NEW_BULK_EDIT =>
				array('tab' => 'general',
					'name' => __( 'Enable &quot;bulk edit&quot; area', 'media-library-assistant' ),
					'type' => 'checkbox',
					'autoload' => true,
					'std' => 'checked',
					'help' => __( 'Check this option to enable the "Bulk Edit area" feature on the Media/Add New screen.', 'media-library-assistant' )),

			self::MLA_ADD_NEW_BULK_EDIT_ON_TOP =>
				array('tab' => 'general',
					'name' => __( '&quot;bulk edit&quot; area on top', 'media-library-assistant' ),
					'type' => 'checkbox',
					'autoload' => true,
					'std' => '',
					'help' => __( 'Check this option to move the "Bulk Edit area" to the top of the Media/Add New screen.', 'media-library-assistant' )),

			self::MLA_ADD_NEW_BULK_EDIT_AUTO_OPEN =>
				array('tab' => 'general',
					'name' => __( '&quot;bulk edit&quot; area initially open', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => '',
					'help' => __( 'Check this option to automatically open the "Bulk Edit area" when the Media/Add New screen is displayed.', 'media-library-assistant' )),

			'media_modal_header' =>
				array('tab' => 'general',
					'name' => __( 'Media Manager/Media Grid Enhancements', 'media-library-assistant' ),
					'type' => 'header'),

			self::MLA_MEDIA_GRID_TOOLBAR =>
				array('tab' => 'general',
					'name' => __( 'Enable Media Grid Enhancements', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check/uncheck this option to enable/disable Media Library Grid View Enhancements.', 'media-library-assistant' )),

			self::MLA_MEDIA_MODAL_TOOLBAR =>
				array('tab' => 'general',
					'name' => __( 'Enable Media Manager Enhancements', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check/uncheck this option to enable/disable Media Manager Modal Window Enhancements.', 'media-library-assistant' )),

			self::MLA_MEDIA_MODAL_MIMETYPES =>
				array('tab' => 'general',
					'name' => __( 'Media Manager Enhanced MIME Type filter', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to filter by more MIME Types, e.g., text, applications.', 'media-library-assistant' )),

			self::MLA_MEDIA_MODAL_MONTHS =>
				array('tab' => 'general',
					'name' => __( 'Media Manager Month and Year filter', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to filter by month and year uploaded.', 'media-library-assistant' )),

			self::MLA_MEDIA_MODAL_TERMS =>
				array('tab' => 'general',
					'name' => __( 'Media Manager Category/Tag filter', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to filter by taxonomy terms.', 'media-library-assistant' )),

			self::MLA_MEDIA_MODAL_TERMS_SEARCH =>
				array('tab' => 'general',
					'name' => __( 'Media Manager Terms Search popup', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to enable the "Terms Search" popup window.', 'media-library-assistant' )),

			self::MLA_MEDIA_MODAL_SEARCHBOX =>
				array('tab' => 'general',
					'name' => __( 'Media Manager Enhanced Search Media box', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to enable search box enhancements.', 'media-library-assistant' )),

			self::MLA_MEDIA_MODAL_SEARCHBOX_CONTROLS =>
				array('tab' => 'general',
					'name' => __( 'Media Manager Enhanced Search Media Controls', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check/uncheck this option to display/hide the and/or connector and search fields controls.', 'media-library-assistant' )),

			self::MLA_MEDIA_MODAL_DETAILS_CATEGORY_METABOX =>
				array('tab' => 'general',
					'name' => __( 'Media Manager Checklist meta boxes', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to enable MLA-enhanced meta boxes in the "ATTACHMENT DETAILS" pane.<br>&nbsp;&nbsp;This option is for any taxonomy that uses a <strong>"checklist-style"</strong> meta box.', 'media-library-assistant' )),

			self::MLA_MEDIA_MODAL_DETAILS_TAG_METABOX =>
				array('tab' => 'general',
					'name' => __( 'Media Manager Flat meta boxes', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to enable MLA-enhanced meta boxes in the "ATTACHMENT DETAILS" pane.<br>&nbsp;&nbsp;This option is for <strong>flat taxonomies</strong>, e.g., "Tags" or "Att. Tags", that do not use the "checklist-style" meta box.', 'media-library-assistant' )),

			self::MLA_MEDIA_MODAL_DETAILS_AUTOFILL =>
				array('tab' => 'general',
					'name' => __( 'Media Manager auto-fill meta boxes', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => '',
					'help' => __( 'Check this option to automatically fill MLA-enhanced meta boxes in the "ATTACHMENT DETAILS" pane<br>&nbsp;&nbsp;when the item is selected.', 'media-library-assistant' )),

			self::MLA_MEDIA_MODAL_ORDERBY =>
				array('tab' => '',
					'name' => __( 'Media Manager Order By', 'media-library-assistant' ),
					'type' => 'select',
					'std' => 'default',
					'options' => array('default', 'none', 'title_name'),
					'texts' => array('&mdash; ' . __( 'Media Manager Default', 'media-library-assistant' ) . ' &mdash;', __( 'None', 'media-library-assistant' ), __( 'Title/Name', 'media-library-assistant' )),
					'help' => __( 'If you want to override the Media Manager default,<br>&nbsp;&nbsp;select a column for the sort order of the Media Library listing.', 'media-library-assistant' )),

			self::MLA_MEDIA_MODAL_ORDER =>
				array('tab' => '',
					'name' => __( 'Media Manager Order', 'media-library-assistant' ),
					'type' => 'radio',
					'std' => 'default',
					'options' => array('default', 'ASC', 'DESC'),
					'texts' => array( '&mdash; ' . __( 'Media Manager Default', 'media-library-assistant' ) . ' &mdash;', 'Ascending', 'Descending' ),
					'help' => __( 'Choose the sort order.', 'media-library-assistant' )),

			'attachment_display_settings_subheader' =>
				array('tab' => 'general',
					'name' => __( 'Attachment Display Settings', 'media-library-assistant' ),
					'type' => 'subheader'),

			self::MLA_MEDIA_MODAL_APPLY_DISPLAY_SETTINGS =>
				array('tab' => 'general',
					'name' => __( 'Media Manager Apply Display Settings', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to always start with the Attachment Display Settings set here,<br>&nbsp;&nbsp;overriding browser-/cookie-based defaults.', 'media-library-assistant' )),

			'image_default_align' =>
				array('tab' => 'general',
					'name' => __( 'Alignment', 'media-library-assistant' ),
					/* translators: 1: option name, e.g., Alignment, Link To or Size */
					'help' => sprintf( __( 'Select a value for the default %1$s option in the Attachment Display Settings.', 'media-library-assistant' ), __( 'Alignment', 'media-library-assistant' ) ),
					'std' =>  'default', 
					'options' => array('default', 'left', 'center', 'right', 'none'),
					'texts' => array('&mdash; ' . __( 'Media Manager Default', 'media-library-assistant' ) . ' &mdash;', __( 'Left', 'media-library-assistant' ), __( 'Center', 'media-library-assistant' ), __( 'Right', 'media-library-assistant' ), __( 'None', 'media-library-assistant' )),
					'type' => 'custom',
					'render' => 'mla_attachment_display_settings_option_handler',
					'update' => 'mla_attachment_display_settings_option_handler',
					'delete' => 'mla_attachment_display_settings_option_handler',
					'reset' => 'mla_attachment_display_settings_option_handler'),

			'image_default_link_type' =>
				array('tab' => 'general',
					'name' => __( 'Link To', 'media-library-assistant' ),
					/* translators: 1: option name, e.g., Alignment, Link To or Size */
					'help' => sprintf( __( 'Select a value for the default %1$s option in the Attachment Display Settings.', 'media-library-assistant' ), __( 'Link To', 'media-library-assistant' ) ),
					'std' =>  'default', 
					'options' => array('default', 'file', 'post', 'custom', 'none'),
					'texts' => array('&mdash; ' . __( 'Media Manager Default', 'media-library-assistant' ) . ' &mdash;', __( 'Media File', 'media-library-assistant' ), __( 'Attachment Page', 'media-library-assistant' ), __( 'Custom URL', 'media-library-assistant' ), __( 'None', 'media-library-assistant' )),
					'type' => 'custom',
					'render' => 'mla_attachment_display_settings_option_handler',
					'update' => 'mla_attachment_display_settings_option_handler',
					'delete' => 'mla_attachment_display_settings_option_handler',
					'reset' => 'mla_attachment_display_settings_option_handler'),

			'image_default_size' =>
				array('tab' => 'general',
					'name' => __( 'Size', 'media-library-assistant' ),
					/* translators: 1: option name, e.g., Alignment, Link To or Size */
					'help' => sprintf( __( 'Select a value for the default %1$s option in the Attachment Display Settings.', 'media-library-assistant' ), __( 'Size', 'media-library-assistant' ) ),
					'std' =>  'default', 
					'options' => array('default', 'thumbnail', 'medium', 'large', 'full'),
					'texts' => array('&mdash; ' . __( 'Media Manager Default', 'media-library-assistant' ) . ' &mdash;', __( 'Thumbnail', 'media-library-assistant' ), __( 'Medium', 'media-library-assistant' ), __( 'Large', 'media-library-assistant' ), __( 'Full Size', 'media-library-assistant' )),
					'type' => 'custom',
					'render' => 'mla_attachment_display_settings_option_handler',
					'update' => 'mla_attachment_display_settings_option_handler',
					'delete' => 'mla_attachment_display_settings_option_handler',
					'reset' => 'mla_attachment_display_settings_option_handler'),

			'uninstall_plugin_subheader' =>
				array('tab' => 'general',
					'name' => __( 'Uninstall (Delete) Plugin Settings', 'media-library-assistant' ),
					'type' => 'subheader'),

			self::MLA_DELETE_OPTION_SETTINGS =>
				array('tab' => 'general',
					'name' => __( 'Delete Option Settings', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => '',
					'help' => __( 'Check this option to remove all MLA option settings from the database when the plugin is deleted.<br>&nbsp;&nbsp;<strong>You can make a backup copy</strong> of your settings below by clicking "', 'media-library-assistant' ) . __( 'Export ALL Settings', 'media-library-assistant' ) . '".' ),

			self::MLA_DELETE_OPTION_BACKUPS =>
				array('tab' => 'general',
					'name' => __( 'Delete Option Settings Backups', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => '',
					'help' => __( 'Check this option to remove the <code>/wp-content/mla-backup</code> directory and its contents when the plugin is deleted.', 'media-library-assistant' )),

			'template_header' =>
				array('tab' => 'mla_gallery',
					'name' => __( 'Default [mla_gallery] Templates and Settings', 'media-library-assistant' ),
					'type' => 'header'),

			'default_tag_cloud_style' =>
				array('tab' => '',
					'name' => __( 'Style Template', 'media-library-assistant' ),
					'type' => 'select',
					'std' => 'tag-cloud',
					'options' => array(),
					'texts' => array(),
					/* translators: 1: template type 2: shortcode */
					'help' => sprintf( __( 'Select the default %1$s for your %2$s shortcodes.', 'media-library-assistant' ), __( 'Style Template', 'media-library-assistant' ), '[mla_tag_cloud]' ) ),

			'default_tag_cloud_markup' =>
				array('tab' => '',
					'name' => __( 'Markup Template', 'media-library-assistant' ),
					'type' => 'select',
					'std' => 'tag-cloud',
					'options' => array(),
					'texts' => array(),
					/* translators: 1: template type 2: shortcode */
					'help' => sprintf( __( 'Select the default %1$s for your %2$s shortcodes.', 'media-library-assistant' ), __( 'markup template', 'media-library-assistant' ), '[mla_tag_cloud]' ) ),

			'mla_tag_cloud_columns' =>
				array('tab' => '',
					'name' => __( 'Default columns', 'media-library-assistant' ),
					'type' => 'text',
					'std' => '3',
					'size' => 3,
					'help' => __( 'Enter the number of [mla_tag_cloud] columns; must be a positive integer.', 'media-library-assistant' )),

			'mla_tag_cloud_margin' =>
				array('tab' => '',
					'name' => __( 'Default mla_margin', 'media-library-assistant' ),
					'type' => 'text',
					'std' => '1.5%',
					'size' => 10,
					'help' => __( 'Enter the CSS "margin" property value, in length (px, em, pt, etc.), percent (%), "auto" or "inherit".<br>&nbsp;&nbsp;Enter "none" to remove the property entirely.', 'media-library-assistant' )),

			'mla_tag_cloud_itemwidth' =>
				array('tab' => '',
					'name' => __( 'Default mla_itemwidth', 'media-library-assistant' ),
					'type' => 'text',
					'std' => 'calculate',
					'size' => 10,
					'help' => __( 'Enter the CSS "width" property value, in length (px, em, pt, etc.), percent (%), "auto" or "inherit".<br>&nbsp;&nbsp;Enter "calculate" (the default) to calculate the value taking the "margin" value into account.<br>&nbsp;&nbsp;Enter "exact" to calculate the value without considering the "margin" value.<br>&nbsp;&nbsp;Enter "none" to remove the property entirely.', 'media-library-assistant' )),

			'default_style' =>
				array('tab' => 'mla_gallery',
					'name' => __( 'Style Template', 'media-library-assistant' ),
					'type' => 'select',
					'std' => 'default',
					'options' => array(),
					'texts' => array(),
					/* translators: 1: template type 2: shortcode */
					'help' => sprintf( __( 'Select the default %1$s for your %2$s shortcodes.', 'media-library-assistant' ), __( 'Style Template', 'media-library-assistant' ), '[mla_gallery]' ) ),

			'default_markup' =>
				array('tab' => 'mla_gallery',
					'name' => __( 'Markup Template', 'media-library-assistant' ),
					'type' => 'select',
					'std' => 'default',
					'options' => array(),
					'texts' => array(),
					/* translators: 1: template type 2: shortcode */
					'help' => sprintf( __( 'Select the default %1$s for your %2$s shortcodes.', 'media-library-assistant' ), __( 'markup template', 'media-library-assistant' ), '[mla_gallery]' ) ),

			'mla_gallery_columns' =>
				array('tab' => 'mla_gallery',
					'name' => __( 'Default columns', 'media-library-assistant' ),
					'type' => 'text',
					'std' => '3',
					'size' => 3,
					'help' => __( 'Enter the number of [mla_gallery] columns; must be a positive integer.', 'media-library-assistant' )),

			'mla_gallery_margin' =>
				array('tab' => 'mla_gallery',
					'name' => __( 'Default mla_margin', 'media-library-assistant' ),
					'type' => 'text',
					'std' => '1.5%',
					'size' => 10,
					'help' => __( 'Enter the CSS "margin" property value, in length (px, em, pt, etc.), percent (%), "auto" or "inherit".<br>&nbsp;&nbsp;Enter "none" to remove the property entirely.', 'media-library-assistant' )),

			'mla_gallery_itemwidth' =>
				array('tab' => 'mla_gallery',
					'name' => __( 'Default mla_itemwidth', 'media-library-assistant' ),
					'type' => 'text',
					'std' => 'calculate',
					'size' => 10,
					'help' => __( 'Enter the CSS "width" property value, in length (px, em, pt, etc.), percent (%), "auto" or "inherit".<br>&nbsp;&nbsp;Enter "calculate" (the default) to calculate the value taking the "margin" value into account.<br>&nbsp;&nbsp;Enter "exact" to calculate the value without considering the "margin" value.<br>&nbsp;&nbsp;Enter "none" to remove the property entirely.', 'media-library-assistant' )),

			'mal_viewer_header' =>
				array('tab' => 'mla_gallery',
					'name' => __( 'Thumbnail Substitution Support, mla_viewer', 'media-library-assistant' ),
					'type' => 'subheader'),

			'enable_mla_viewer' =>
				array('tab' => 'mla_gallery',
					'name' => __( 'Enable thumbnail substitution', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to allow the "mla_viewer" to generate thumbnail images for PDF  documents. Thumbnails are generated dynamically, each time the item appears in an [mla_gallery] display.<br>&nbsp;&nbsp;<strong>IMPORTANT: both Ghostscript and Imagick/ImageMagick must be installed for this feature.</strong>', 'media-library-assistant' )),

			'enable_featured_image' =>
				array('tab' => 'mla_gallery',
					'name' => __( 'Enable Featured Images', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to extend Featured Image support to all Media Library items. The Featured Image can be used as a thumbnail image for the item in an [mla_gallery] display.', 'media-library-assistant' )),

			'enable_featured_image_generation' =>
				array('tab' => 'mla_gallery',
					'name' => __( 'Enable Featured Image Generation', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to enable the "Thumbnail" generation action in the Media/Assistant submenu Bulk Actions dropdown.', 'media-library-assistant' )),

			'enable_ghostscript_check' =>
				array('tab' => 'mla_gallery',
					'name' => __( 'Enable explicit Ghostscript check', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to enable the explicit check for Ghostscript support required for thumbnail generation. If your Ghostscript software is in a non-standard location, unchecking this option bypasses the check. Bad things can happen if Ghostscript is missing but Imagick/ImageMagick is present, so leave this option checked unless you know it is safe to turn it off.', 'media-library-assistant' )),

			'ghostscript_path' =>
				array('tab' => 'mla_gallery',
					'name' => __( 'Ghostscript path', 'media-library-assistant' ),
					'type' => 'text',
					'std' => '',
					'size' => 20,
					'help' => __( 'If your &ldquo;gs&rdquo; executable is in a non-standard location, enter the full path and filename here, e.g., &ldquo;/usr/bin/gs&rdquo;. It will override the search for Ghostscript in other places.', 'media-library-assistant' )),

			/*
			 * Managed by mla_get_style_templates and mla_put_style_templates
			 */
			'style_templates' =>
				array('tab' => '',
					'type' => 'hidden',
					'std' => array()),

			/*
			 * Managed by mla_get_markup_templates and mla_put_markup_templates
			 */
			'markup_templates' =>
				array('tab' => '',
					'type' => 'hidden',
					'std' => array()),

			'enable_custom_field_mapping' =>
				array('tab' => 'custom_field',
					'name' => __( 'Enable custom field mapping when adding new media', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => '',
					'help' => __( 'Check this option to enable mapping when uploading new media (attachments).<br>&nbsp;&nbsp;Click Save Changes at the bottom of the screen if you change this option.<br>&nbsp;&nbsp;Does NOT affect the operation of the "Map" buttons on the bulk edit, single edit and settings screens.', 'media-library-assistant' )),

			'enable_custom_field_update' =>
				array('tab' => 'custom_field',
					'name' => __( 'Enable custom field mapping when updating media metadata', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => '',
					'help' => __( 'Check this option to enable mapping when media (attachments) metadata is regenerated,<br>&nbsp;&nbsp;e.g., when the Media/Edit Media "Edit Image" functions are used.', 'media-library-assistant' )),

			'custom_field_mapping' =>
				array('tab' => '',
					'help' => __( 'Update the custom field mapping values above, then click Save Changes to make the updates permanent.<br>You can also make temporary updates and click a Map All Attachments button to apply the rule(s) to all attachments without saving any rule changes.', 'media-library-assistant' ),
					'std' =>  array(),
					'type' => 'custom',
					'render' => 'mla_custom_field_option_handler',
					'update' => 'mla_custom_field_option_handler',
					'delete' => 'mla_custom_field_option_handler',
					'reset' => 'mla_custom_field_option_handler'),

			'enable_iptc_exif_mapping' =>
				array('tab' => 'iptc_exif',
					'name' => __( 'Enable IPTC/EXIF Mapping when adding new media', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => '',
					'help' => __( 'Check this option to enable mapping when uploading new media (attachments).<br>&nbsp;&nbsp;Does NOT affect the operation of the "Map" buttons on the bulk edit, single edit and settings screens.', 'media-library-assistant' )),

			'enable_iptc_exif_update' =>
				array('tab' => 'iptc_exif',
					'name' => __( 'Enable IPTC/EXIF Mapping when updating media metadata', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => '',
					'help' => __( 'Check this option to enable mapping when media (attachments) metadata is regenerated,<br>&nbsp;&nbsp;e.g., when the Media/Edit Media "Edit Image" functions are used.', 'media-library-assistant' )),

			'iptc_exif_standard_mapping' =>
				array('tab' => '',
					'help' => __( 'Update the standard field mapping values above, then click <strong>Save Changes</strong> to make the updates permanent.<br>You can also make temporary updates and click <strong>Map All Attachments, Standard Fields Now</strong> to apply the updates to all attachments without saving the rule changes.', 'media-library-assistant' ),
					'std' =>  NULL, 
					'type' => 'custom',
					'render' => 'mla_iptc_exif_option_handler',
					'update' => 'mla_iptc_exif_option_handler',
					'delete' => 'mla_iptc_exif_option_handler',
					'reset' => 'mla_iptc_exif_option_handler'),

			'iptc_exif_taxonomy_mapping' =>
				array('tab' => '',
					'help' => __( 'Update the taxonomy term mapping values above, then click <strong>Save Changes</strong> or <strong>Map All Attachments, Taxonomy Terms Now</strong>.', 'media-library-assistant' ),
					'std' =>  NULL,
					'type' => 'custom',
					'render' => 'mla_iptc_exif_option_handler',
					'update' => 'mla_iptc_exif_option_handler',
					'delete' => 'mla_iptc_exif_option_handler',
					'reset' => 'mla_iptc_exif_option_handler'),

			'iptc_exif_custom_mapping' =>
				array('tab' => '',
					'help' => __( '<strong>Update</strong> individual custom field mapping values above, or make several updates and click <strong>Save Changes</strong> below to apply them all at once.<br>You can also <strong>add a new rule</strong> for an existing field or <strong>add a new field</strong> and rule.<br>You can make temporary updates and click <strong>Map All Attachments, Custom Fields Now</strong> to apply the updates to all attachments without saving the rule changes.', 'media-library-assistant' ),
					'std' =>  NULL, 
					'type' => 'custom',
					'render' => 'mla_iptc_exif_option_handler',
					'update' => 'mla_iptc_exif_option_handler',
					'delete' => 'mla_iptc_exif_option_handler',
					'reset' => 'mla_iptc_exif_option_handler'),

			'iptc_exif_mapping' =>
				array('tab' => '',
					'help' => __( 'IPTC/EXIF Mapping help', 'media-library-assistant' ),
					'std' =>  array (
						'standard' => array (
							'post_title' => array (
								'name' => __( 'Title', 'media-library-assistant' ),
								'iptc_value' => 'none',
								'exif_value' => '',
								'iptc_first' => true,
								'keep_existing' => true
							),
							'post_name' => array (
								'name' => __( 'Name/Slug', 'media-library-assistant' ),
								'iptc_value' => 'none',
								'exif_value' => '',
								'iptc_first' => true,
								'keep_existing' => true
							),
							'image_alt' => array (
								'name' => __( 'ALT Text', 'media-library-assistant' ),
								'iptc_value' => 'none',
								'exif_value' => '',
								'iptc_first' => true,
								'keep_existing' => true
							),
							'post_excerpt' => array (
								'name' => __( 'Caption', 'media-library-assistant' ),
								'iptc_value' => 'none',
								'exif_value' => '',
								'iptc_first' => true,
								'keep_existing' => true
							),
							'post_content' => array (
								'name' => __( 'Description', 'media-library-assistant' ),
								'iptc_value' => 'none',
								'exif_value' => '',
								'iptc_first' => true,
								'keep_existing' => true
							),
						),
						'taxonomy' => array (
						),
						'custom' => array (
						)
						), 
					'type' => 'custom',
					'render' => 'mla_iptc_exif_option_handler',
					'update' => 'mla_iptc_exif_option_handler',
					'delete' => 'mla_iptc_exif_option_handler',
					'reset' => 'mla_iptc_exif_option_handler'),

			self::MLA_ENABLE_POST_MIME_TYPES =>
				array('tab' => 'view',
					'name' => __( 'Enable View and Post MIME Type Support', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check/uncheck this option to enable/disable Post MIME Type Support, then click <strong>Save Changes</strong> to record the new setting.', 'media-library-assistant' ) ),

			self::MLA_POST_MIME_TYPES =>
				array('tab' => '',
					'type' => 'custom',
					'render' => 'mla_post_mime_types_option_handler',
					'update' => 'mla_post_mime_types_option_handler',
					'delete' => 'mla_post_mime_types_option_handler',
					'reset' => 'mla_post_mime_types_option_handler',
					'help' => __( 'Post MIME Types help.', 'media-library-assistant' ),
					'std' => array(
						'all' => array(
							'singular' => _x( 'All', 'table_view_singular', 'media-library-assistant' ),
							'plural' => _x( 'All', 'table_view_plural', 'media-library-assistant' ),
							'specification' => '',
							'post_mime_type' => false,
							'table_view' => true,
							'menu_order' => 0,
							'description' => _x( 'Built-in view', 'post_mime_types_description', 'media-library-assistant' )
						),
						'image' => array(
							'singular' => _x( 'Image', 'table_view_singular', 'media-library-assistant' ),
							'plural' => _x( 'Images', 'table_view_plural', 'media-library-assistant' ),
							'specification' => '',
							'post_mime_type' => true,
							'table_view' => true,
							'menu_order' => 0,
							'description' => _x( 'All image subtypes', 'post_mime_types_description', 'media-library-assistant' )
						),
						'audio' => array(
							'singular' => _x( 'Audio', 'table_view_singular', 'media-library-assistant' ),
							'plural' => _x( 'Audio', 'table_view_plural', 'media-library-assistant' ),
							'specification' => '',
							'post_mime_type' => true,
							'table_view' => true,
							'menu_order' => 0,
							'description' => _x( 'All audio subtypes', 'post_mime_types_description', 'media-library-assistant' )
						),
						'video' => array(
							'singular' => _x( 'Video', 'table_view_singular', 'media-library-assistant' ),
							'plural' => _x( 'Video', 'table_view_plural', 'media-library-assistant' ),
							'specification' => '',
							'post_mime_type' => true,
							'table_view' => true,
							'menu_order' => 0,
							'description' => _x( 'All video subtypes', 'post_mime_types_description', 'media-library-assistant' )
						),
						'text' => array(
							'singular' => _x( 'Text', 'table_view_singular', 'media-library-assistant' ),
							'plural' => _x( 'Text', 'table_view_plural', 'media-library-assistant' ),
							'specification' => '',
							'post_mime_type' => true,
							'table_view' => true,
							'menu_order' => 0,
							'description' => _x( 'All text subtypes', 'post_mime_types_description', 'media-library-assistant' )
						),
						'application' => array(
							'singular' => _x( 'Application', 'table_view_singular', 'media-library-assistant' ),
							'plural' => _x( 'Applications', 'table_view_plural', 'media-library-assistant' ),
							'specification' => '',
							'post_mime_type' => true,
							'table_view' => true,
							'menu_order' => 0,
							'description' => _x( 'All application subtypes', 'post_mime_types_description', 'media-library-assistant' )
						),
						'detached' => array(
							'singular' => _x( 'Unattached', 'table_view_singular', 'media-library-assistant' ),
							'plural' => _x( 'Unattached', 'table_view_plural', 'media-library-assistant' ),
							'specification' => '',
							'post_mime_type' => false,
							'table_view' => true,
							'menu_order' => 0,
							'description' => _x( 'Built-in view', 'post_mime_types_description', 'media-library-assistant' )
						),
						'attached' => array(
							'singular' => _x( 'Attached', 'table_view_singular', 'media-library-assistant' ),
							'plural' => _x( 'Attached', 'table_view_plural', 'media-library-assistant' ),
							'specification' => '',
							'post_mime_type' => false,
							'table_view' => true,
							'menu_order' => 0,
							'description' => _x( 'Built-in view', 'post_mime_types_description', 'media-library-assistant' )
						),
						'trash' => array(
							'singular' => _x( 'Trash', 'table_view_singular', 'media-library-assistant' ),
							'plural' => _x( 'Trash', 'table_view_plural', 'media-library-assistant' ),
							'specification' => '',
							'post_mime_type' => false,
							'table_view' => true,
							'menu_order' => 0,
							'description' => _x( 'Built-in view', 'post_mime_types_description', 'media-library-assistant' )
						)
					)),

			self::MLA_ENABLE_UPLOAD_MIMES =>
				array('tab' => 'upload',
					'name' => __( 'Enable Upload MIME Type Support', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check/uncheck this option to enable/disable Upload MIME Type Support, then click <strong>Save Changes</strong> to record the new setting.', 'media-library-assistant' )),

			self::MLA_UPLOAD_MIMES =>
				array('tab' => '',
					'type' => 'custom',
					'render' => 'mla_upload_mimes_option_handler',
					'update' => 'mla_upload_mimes_option_handler',
					'delete' => 'mla_upload_mimes_option_handler',
					'reset' => 'mla_upload_mimes_option_handler',
					'help' => __( 'Upload MIME Types help.', 'media-library-assistant' ),
					'std' => false), // false to detect first-time load; will become an array

			self::MLA_ENABLE_MLA_ICONS =>
				array('tab' => 'upload',
					'name' => __( 'Enable MLA File Type Icons Support', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check/uncheck this option to enable/disable MLA File Type Icons Support, then click <strong>Save Changes</strong> to record the new setting.', 'media-library-assistant' )),

			self::MLA_DEBUG_DISPLAY_LIMIT =>
				array('tab' => 'debug',
					'name' => __( 'Display Limit', 'media-library-assistant' ),
					'type' => 'text',
					'std' => '131072',
					'size' => 5,
					'help' => __( 'Enter the maximum number of debug log characters to display; enter zero or leave blank for no limit.', 'media-library-assistant' )),

			self::MLA_DEBUG_FILE =>
				array('tab' => 'debug',
					'name' => __( 'Debug File', 'media-library-assistant' ),
					'type' => 'text',
					'std' => '',
					'size' => 60,
					'help' => __( 'Enter the name of an alternate, MLA-specific debug log file; leave blank to use the PHP error_log.', 'media-library-assistant' )),

			self::MLA_DEBUG_REPLACE_PHP_LOG =>
				array('tab' => 'debug',
					'name' => __( 'Replace PHP error_log file', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => '',
					'help' => __( 'Check this option to replace the PHP error_log file with the MLA Debug File.<br>&nbsp;&nbsp;allows capture of PHP messages in the MLA Debug File.', 'media-library-assistant' )),

			self::MLA_DEBUG_REPLACE_PHP_REPORTING =>
				array('tab' => 'debug',
					'name' => __( 'PHP Reporting', 'media-library-assistant' ),
					'type' => 'text',
					'std' => '',
					'size' => 10,
					'help' => __( 'Enter a numeric error_reporting value, e.g., 0x7FFF or 32767; leave blank to use the existing PHP error_reporting value.', 'media-library-assistant' )),

			self::MLA_DEBUG_REPLACE_LEVEL =>
				array('tab' => 'debug',
					'name' => __( 'MLA Reporting', 'media-library-assistant' ),
					'type' => 'text',
					'std' => '',
					'size' => 10,
					'help' => __( 'Enter a numeric MLA_DEBUG_LEVEL value, e.g., 0x0003 or 3; leave blank to use the existing MLA_DEBUG_LEVEL value.', 'media-library-assistant' )),

			/* Here are examples of the other option types
			'textarea' =>
				array('tab' => '',
					'name' => 'Text Area',
					'type' => 'textarea',
					'std' => 'default text area',
					'cols' => 60,
					'rows' => 4,
					'help' => __( 'Enter the text area...'),
			*/
		);
	}
} // class MLACoreOptions
?>