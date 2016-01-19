<?php
/**
 * Media Library Assistant Polylang Support classes
 *
 * This file is conditionally loaded in MLA::initialize after a check for Polylang presence.
 *
 * @package Media Library Assistant
 * @since 2.11
 */

/**
 * Class MLA (Media Library Assistant) Polylang provides support for the
 * Polylang Multilingual plugin
 *
 * @package Media Library Assistant
 * @since 2.11
 */
class MLA_Polylang {
	/**
	 * Uniquely identifies the Quick Translate action
	 *
	 * @since 2.11
	 *
	 * @var	string
	 */
	const MLA_PLL_QUICK_TRANSLATE = 'mla-polylang-quick-translate';

	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 2.11
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * The remaining filters are only useful for the admin section;
		 * exit in the front-end posts/pages
		 */
		if ( ! is_admin() ) {
			 /*
			  * Defined in /media-library-assistant/includes/class-mla-shortcodes.php
			  */
			add_filter( 'mla_get_terms_query_arguments', 'MLA_Polylang::mla_get_terms_query_arguments', 10, 1 );
			add_filter( 'mla_get_terms_clauses', 'MLA_Polylang::mla_get_terms_clauses', 10, 1 );

			return;
		}

		/*
		 * Defined in /wp-admin/admin.php
		 */
		add_action( 'admin_init', 'MLA_Polylang::admin_init' );

		/*
		 * Defined in /wp-admin/admin-header.php
		 */
 		add_action( 'admin_enqueue_scripts', 'MLA_Polylang::admin_enqueue_scripts', 10, 1 );

		/*
		 * Defined in wp-admin/includes/post.php function edit_post
		 */
		add_filter( 'attachment_fields_to_save', 'MLA_Polylang::attachment_fields_to_save', 10, 2 );

		/*
		 * Defined in wp-includes/post.php function wp_insert_post
		 */
		add_action( 'edit_attachment', 'MLA_Polylang::edit_attachment', 10, 1 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-data.php
		  */
		add_action( 'mla_updated_single_item', 'MLA_Polylang::mla_updated_single_item', 10, 2 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-media-modal.php
		  */
		add_filter( 'mla_media_modal_terms_options', 'MLA_Polylang::mla_media_modal_terms_options', 10, 1 );
		add_action( 'mla_media_modal_begin_update_compat_fields', 'MLA_Polylang::mla_media_modal_begin_update_compat_fields', 10, 1 );
		add_filter( 'mla_media_modal_update_compat_fields_terms', 'MLA_Polylang::mla_media_modal_update_compat_fields_terms', 10, 4 );
		add_filter( 'mla_media_modal_end_update_compat_fields', 'MLA_Polylang::mla_media_modal_end_update_compat_fields', 10, 3 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-main.php
		  */
		add_filter( 'mla_list_table_inline_fields', 'MLA_Polylang::mla_list_table_inline_fields', 10, 1 );
		add_filter( 'mla_list_table_inline_action', 'MLA_Polylang::mla_list_table_inline_action', 10, 2 );
		add_filter( 'mla_list_table_bulk_action_initial_request', 'MLA_Polylang::mla_list_table_bulk_action_initial_request', 10, 3 );
		add_filter( 'mla_list_table_bulk_action_item_request', 'MLA_Polylang::mla_list_table_bulk_action_item_request', 10, 4 );
		add_filter( 'mla_list_table_bulk_action', 'MLA_Polylang::mla_list_table_bulk_action', 10, 3 );
		add_filter( 'mla_list_table_custom_bulk_action', 'MLA_Polylang::mla_list_table_custom_bulk_action', 10, 3 );
		add_filter( 'mla_list_table_inline_values', 'MLA_Polylang::mla_list_table_inline_values', 10, 1 );
		add_filter( 'mla_list_table_inline_parse', 'MLA_Polylang::mla_list_table_inline_parse', 10, 3 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-list-table.php
		  */
		add_filter( 'mla_list_table_get_columns', 'MLA_Polylang::mla_list_table_get_columns', 10, 1 );
		add_filter( 'mla_list_table_get_bulk_actions', 'MLA_Polylang::mla_list_table_get_bulk_actions', 10, 1 );
		add_filter( 'mla_list_table_column_default', 'MLA_Polylang::mla_list_table_column_default', 10, 3 );

		add_filter( 'mla_list_table_submenu_arguments', 'MLA_Polylang::mla_list_table_submenu_arguments', 10, 2 );

		add_filter( 'mla_list_table_prepare_items_pagination', 'MLA_Polylang::mla_list_table_prepare_items_pagination', 10, 2 );
		add_filter( 'mla_list_table_prepare_items_total_items', 'MLA_Polylang::mla_list_table_prepare_items_total_items', 10, 2 );

		add_filter( 'mla_list_table_build_rollover_actions', 'MLA_Polylang::mla_list_table_build_rollover_actions', 10, 3 );
		add_filter( 'mla_list_table_build_inline_data', 'MLA_Polylang::mla_list_table_build_inline_data', 10, 2 );

		/*
		 * Defined in /media-library-assistant/includes/class-mla-objects.php
		 */
		//add_filter( 'mla_taxonomy_get_columns', 'MLA_Polylang::mla_taxonomy_get_columns', 10, 3 );

		/*
		 * Defined in /media-library-assistant/includes/class-mla-settings.php
		 */
		add_filter( 'mla_get_options_tablist', 'MLA_Polylang::mla_get_options_tablist', 10, 3 );
		add_action( 'mla_begin_mapping', 'MLA_Polylang::mla_begin_mapping', 10, 2 );
		add_filter( 'mla_mapping_new_text', 'MLA_Polylang::mla_mapping_new_text', 10, 5 );
		add_action( 'mla_end_mapping', 'MLA_Polylang::mla_end_mapping', 10, 0 );

		/*
		 * Defined in /polylang/admin/admin-filters-media.php
		 */
		add_action( 'pll_translate_media', 'MLA_Polylang::pll_translate_media', 10, 3 );
	}

	/**
	 * MLA Tag Cloud Query Arguments
	 *
	 * Saves [mla_tag_cloud] query parameters for use in MLA_Polylang::mla_get_terms_clauses.
	 *
	 * @since 2.11
	 * @uses MLA_Polylang::$all_query_parameters
	 *
	 * @param	array	shortcode arguments merged with attachment selection defaults, so every possible parameter is present
	 *
	 * @return	array	updated attachment query arguments
	 */
	public static function mla_get_terms_query_arguments( $all_query_parameters ) {
		MLA_Polylang::$all_query_parameters = $all_query_parameters;

		return $all_query_parameters;
	} // mla_get_terms_query_arguments

	/**
	 * Save the query arguments
	 *
	 * @since 2.11
	 *
	 * @var	array
	 */
	private static $all_query_parameters = array();

	/**
	 * MLA Tag Cloud Query Clauses
	 *
	 * Adds language-specific clauses to filter the cloud terms.
	 *
	 * @since 2.11
	 *
	 * @param	array	SQL clauses ( 'fields', 'join', 'where', 'order', 'orderby', 'limits' )
	 *
	 * @return	array	updated SQL clauses
	 */
	public static function mla_get_terms_clauses( $clauses ) {
		global $polylang;

		$clauses = $polylang->filters->terms_clauses($clauses, MLA_Polylang::$all_query_parameters['taxonomy'], MLA_Polylang::$all_query_parameters );

		return $clauses;
	} // mla_get_terms_clauses

	/**
	 * Load the plugin's Ajax handler(s)
	 *
	 * @since 2.11
	 *
	 * @return	void
	 */
	public static function admin_init() {
		/*
		 * Add styles for the language management column
		 */
		if ( isset( $_REQUEST['page'] ) && ( MLACore::ADMIN_PAGE_SLUG == $_REQUEST['page'] ) ) {
			add_action( 'admin_print_styles', 'MLA_Polylang::mla_list_table_add_pll_styles' );
		}

		if ( defined('DOING_AJAX') && DOING_AJAX ) {
			add_action( 'wp_ajax_' . MLA_Polylang::MLA_PLL_QUICK_TRANSLATE, 'MLA_Polylang::quick_translate' );
		}

		/*
		 * Localize $mla_language_option_definitions array
		 */
		MLA_Polylang::mla_localize_language_option_definitions();

		if ( isset( $_REQUEST['pll-bulk-translate'] ) ) {		
			// Set "Show all languages" to display mixed-language results
			$request['lang'] = 'all';
			$_REQUEST['lang'] = 'all';
			$_GET['lang'] = 'all';
		}
	}

	/**
	 * Find or create an item translation
	 *
	 * @since 2.11
	 *
	 * @param	integer	item ID
	 * @param	string	Slug of the desired language
	 *
	 * @return	integer	ID of the corresponding item in the desired language
	 */
	private static function _get_translation( $post_id, $new_language ) {
		global $polylang;

		/*
		 * Get the existing translations, if any
		 */
		$translations = $polylang->model->get_translations( 'post', $post_id );
		if ( ! $translations && $lang = $polylang->model->get_post_language( $post_id ) ) {
			$translations[ $lang->slug ] = $post_id;
		}

		if ( array_key_exists( $new_language, $translations ) ) {
			$new_id = $translations[ $new_language ];
		} else {
			/*
			 * create a new attachment (translate attachment parent if exists)
			 * modeled after /polylang/admin/admin-filters-media.php
			 * function translate_media()
			 */
			$post = get_post( $post_id );
			$post->ID = NULL; // will force the creation
			$post->post_parent = ( $post->post_parent && $tr_parent = $polylang->model->get_translation( 'post', $post->post_parent, $new_language ) ) ? $tr_parent : 0;
			$new_id = wp_insert_attachment( $post );
			add_post_meta( $new_id, '_wp_attachment_metadata', get_post_meta( $post_id, '_wp_attachment_metadata', true ) );
			add_post_meta( $new_id, '_wp_attached_file', get_post_meta( $post_id, '_wp_attached_file', true ) );

			if ( 'checked' == MLACore::mla_get_option( 'term_synchronization', false, false, MLA_Polylang::$mla_language_option_definitions ) ) {
				self::_build_existing_terms( $post_id );
				self::_build_tax_input( $post_id );
				$tax_inputs = self::_apply_tax_input( 0, $new_language );
			} else {
				$tax_inputs = NULL;
			}

			if ( !empty( $tax_inputs ) ) {
				MLAData::mla_update_single_item( $new_id, array(), $tax_inputs );
			}

			self::$existing_terms = array( 'element_id' => 0 );
			self::$relevant_terms = array();

			$polylang->model->set_post_language($new_id, $new_language);

			$translations = $polylang->model->get_translations( 'post', $post_id );
			if ( ! $translations && $lang = $polylang->model->get_post_language( $post_id ) )
				$translations[ $lang->slug ] = $post_id;

			$translations[ $new_language ] = $new_id;
			$polylang->model->save_translations( 'post', $new_id, $translations );
		} // add new translation

		return (integer) $new_id;
	} // _get_translation

	/**
	 * Ajax handler to Quick Translate a single attachment
	 *
	 * @since 2.11
	 *
	 * @return	void	echo HTML <td> innerHTML for updated call or error message, then die()
	 */
	public static function quick_translate() {
		global $polylang;

		check_ajax_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

		if ( empty( $_REQUEST['post_ID'] ) ) {
			echo __( 'ERROR: No post ID found', 'media-library-assistant' );
			die();
		} else {
			$post_id = (integer) $_REQUEST['post_ID'];
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( __( 'You are not allowed to edit this Attachment.', 'media-library-assistant' ) );
		}

		self::_build_existing_terms( $post_id );

		/*
		 * pll_quick_language is used by the translation status links; edit or add the selected translation
		 * inline_lang_choice is the value of the Language dropdown control; change the value of the current item
		 */
		if ( ! empty( $_REQUEST['pll_quick_language'] ) ) {
			$new_id = MLA_Polylang::_get_translation( $post_id, $_REQUEST['pll_quick_language'] );
		} else {
			$new_id = $post_id;

			// Language dropdown in Quick Edit area
			if ( isset( $_REQUEST['inline_lang_choice'] ) ) {
				$translations = $polylang->model->get_translations( 'post', $post_id );

				if ( ! array_key_exists( $_REQUEST['inline_lang_choice'], $translations ) ) {
					$post = get_post( $post_id );
					// save_post() does a check_admin_referer() security test
					$_REQUEST['_inline_edit'] = wp_create_nonce( 'inlineeditnonce' );
					$polylang->filters_post->save_post( $post_id, $post, true );

					if ( 'checked' == MLACore::mla_get_option( 'term_assignment', false, false, MLA_Polylang::$mla_language_option_definitions ) ) {
						// Record new language for Term Assignment and Synchronization
						if ( ! empty( $_REQUEST['tax_input'] ) ) {
							// Discard the old translation, which is gone
							unset( self::$existing_terms[ self::$existing_terms['slug'] ] );
							self::$existing_terms['slug'] = $_REQUEST['inline_lang_choice'];
						}

						self::_build_existing_terms( $post_id );
						self::_build_tax_input( $post_id );
						$tax_inputs = self::_apply_tax_input( 0, $_REQUEST['inline_lang_choice'] );
					} else {
						$tax_inputs = NULL;
					}

					if ( !empty( $tax_inputs ) ) {
						MLAData::mla_update_single_item( $post_id, array(), $tax_inputs );
					}
				} // change language
			}
		}

		//	Create an instance of our package class and echo the new HTML for all translations
		$translations = $polylang->model->get_translations( 'post', $post_id );

		$MLAListTable = new MLA_List_Table();
		$new_item = (object) MLAData::mla_get_attachment_by_id( $new_id );
		$MLAListTable->single_row( $new_item );

		foreach( $translations as $language => $post_id ) {
			if ( $new_id == $post_id ) {
				continue;
			}

			$new_item = (object) MLAData::mla_get_attachment_by_id( $post_id );
			$MLAListTable->single_row( $new_item );
			echo "\n";
		}

		die(); // this is required to return a proper result
	} // quick_translate

	/**
	 * Load the plugin's Style Sheet and Javascript files
	 *
	 * @since 2.11
	 *
	 * @param	string	Name of the page being loaded
	 *
	 * @return	void
	 */
	public static function admin_enqueue_scripts( $page_hook ) {
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		if ( 'media_page_mla-menu' != $page_hook ) {
			return;
		}

		wp_register_style( 'mla-polylang-support', MLA_PLUGIN_URL . 'css/mla-polylang-support.css', false, MLA::CURRENT_MLA_VERSION );
		wp_enqueue_style( 'mla-polylang-support' );

		wp_enqueue_script( 'mla-polylang-support-scripts', MLA_PLUGIN_URL . "js/mla-polylang-support-scripts{$suffix}.js", 
			array( 'jquery' ), MLA::CURRENT_MLA_VERSION, false );

		// For Quick and Bulk Translate
		$fields = array( 'old_lang', 'inline_lang_choice', 'inline_translations' );

		$script_variables = array(
			'fields' => $fields,
			'error' => __( 'Error while saving the translations.', 'media-library-assistant' ),
			'ntdelTitle' => __( 'Remove From Bulk Translate', 'media-library-assistant' ),
			'noTitle' => __( '(no title)', 'media-library-assistant' ),
			'bulkTitle' => __( 'Bulk Translate items', 'media-library-assistant' ),
			'addNew' => __( 'Add new', 'media-library-assistant' ),
			'edit' => __( 'Edit', 'media-library-assistant' ),
			'comma' => _x( ',', 'tag_delimiter', 'media-library-assistant' ),
			'useSpinnerClass' => false,
			'ajax_action' => MLA_Polylang::MLA_PLL_QUICK_TRANSLATE,
			'ajax_nonce' => wp_create_nonce( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) 
		);

		if ( version_compare( get_bloginfo( 'version' ), '4.2', '>=' ) ) {
			$script_variables['useSpinnerClass'] = true;
		}

		wp_localize_script( 'mla-polylang-support-scripts', 'mla_polylang_support_vars', $script_variables );
	}

	/**
	 * Duplicates created during media upload
	 *
	 * @since 2.11
	 *
	 * @var	array	[ $post_id ] => $language;
	 */
	private static $duplicate_attachments = array();

	/**
	 * Copies taxonomy terms from the source item to the new translated item
	 *
	 * @since 2.11
	 *
	 * @param	integer	ID of the new item
	 * @param	object	post object of the new item
	 * @param	array	 an associative array of translations with language code as key and translation id as value
	 */
	public static function pll_translate_media( $duplicated_attachment_id, $duplicated_attachment_object, $translations ) {
		global $polylang;
		static $already_adding = 0;

		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::pll_translate_media( {$duplicated_attachment_id} ) translations = " . var_export( $translations, true ), MLACore::MLA_DEBUG_CATEGORY_LANGUAGE );

		if ( $already_adding == $duplicated_attachment_id ) {
			return;
		} else {
			$already_adding = $duplicated_attachment_id;
		}

		$attachment_id = absint( isset( $_REQUEST['from_media'] ) ? $_REQUEST['from_media'] : $duplicated_attachment_id );
		$language_code = array_search( $duplicated_attachment_id, $translations );
		self::$duplicate_attachments [ $duplicated_attachment_id ] = $language_code;

		if ( isset( $_REQUEST['action'] ) && 'translate_media' ==  $_REQUEST['action'] ) {
			if ( 'checked' == MLACore::mla_get_option( 'term_synchronization', false, false, MLA_Polylang::$mla_language_option_definitions ) ) {
				// Clone the existing common terms to the new translation
				self::_build_existing_terms( $attachment_id );
				self::_build_tax_input( $attachment_id );
				$tax_inputs = self::_apply_tax_input( 0, $language_code );
			} else {
				$tax_inputs = NULL;
			}

			if ( !empty( $tax_inputs ) ) {
				MLAData::mla_update_single_item( $duplicated_attachment_id, array(), $tax_inputs );
			}

			self::$existing_terms = array( 'element_id' => 0 );
			self::$relevant_terms = array();
		} // translate_media
	} // pll_translate_media

	/**
	 * Force "All languages" mode for IPTC/EXIF mapping, which uses mla_get_shortcode_attachments
	 *
	 * @since 2.20
	 *
	 * @param	array	Arguments for mla_get_shortcode_attachments
	 * @param	boolean	true to calculate and return ['found_posts'] as an array element
	 */
	public static function mla_get_shortcode_attachments_final_terms( $arguments, $return_found_rows ) {
		$arguments['lang'] = 'all';
//error_log( __LINE__ . ' MLA_Polylang::mla_get_shortcode_attachments_final_terms $arguments = ' . var_export( $arguments, true ), 0 );
		return $arguments;
	}

	/**
	 * Force "All languages" mode for IPTC/EXIF mapping, which uses mla_get_shortcode_attachments
	 *
	 * @since 2.20
	 *
	 * @param	string 	what kind of mapping action is starting:
	 *					single_custom, single_iptc_exif, bulk_custom, bulk_iptc_exif,
	 *					create_metadata, update_metadata, custom_fields, custom_rule,
	 *					iptc_exif_standard, iptc_exif_taxonomy, iptc_exif_custom,
	 *					iptc_exif_custom_rule
	 * @param	mixed	Attachment ID or NULL, depending on scope
	 */
	public static function mla_begin_mapping( $source, $post_id = NULL ) {
//error_log( __LINE__ . " mla_begin_mapping( {$source} ) ", 0 );
		if ( in_array( $source, array( 'create_metadata', 'single_iptc_exif', 'iptc_exif_standard', 'iptc_exif_taxonomy', 'iptc_exif_custom', 'iptc_exif_custom_rule' ) ) ) {
			add_filter( 'mla_get_shortcode_attachments_final_terms', 'MLA_Polylang::mla_get_shortcode_attachments_final_terms', 10, 2 );
			add_filter( 'mla_mapping_rule', 'MLA_Polylang::mla_mapping_rule', 10, 4 );
		}
	} // mla_begin_mapping

	/**
	 * Saves the current mapping rule for term creation
	 *
	 * @since 2.20
	 *
	 * @param	array 	mapping rule
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against: iptc_exif_standard_mapping, iptc_exif_taxonomy_mapping or iptc_exif_custom_mapping
	 * @param	array 	attachment_metadata, default NULL
	 */
	public static function mla_mapping_rule( $setting_value, $post_id, $category, $attachment_metadata ) {
		return self::$current_mapping_rule = $setting_value;
	} // mla_mapping_rule

	/**
	 * Current mapping rule for term creation
	 *
	 * @since 2.20
	 *
	 * @var	array	mapping rule
	 */
	private static $current_mapping_rule = array();

	/**
	 * Manages the creation of new taxonomy terms from metadata values
	 *
	 * @since 2.20
	 *
	 * @param	mixed 	string or array value returned by the rule
	 * @param	string 	field name or taxonomy name
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against: iptc_exif_standard_mapping, iptc_exif_taxonomy_mapping or iptc_exif_custom_mapping
	 * @param	array 	attachment_metadata, default NULL
	 *
	 * @return	array	updated rule EXIF/Template value
	 */
	public static function mla_mapping_new_text( $new_text, $setting_key, $post_id, $category, $attachment_metadata ) {
		global $polylang;
		static $replicate = NULL, $current_language, $taxonomies, $other_languages, $parent_term;

		if ( 'iptc_exif_taxonomy_mapping' !== $category ) {
			return $new_text;
		}
		
		if ( is_null( $replicate ) ) {
			$replicate = ( 'checked' == MLACore::mla_get_option( 'term_mapping_replication', false, false, MLA_Polylang::$mla_language_option_definitions ) );
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $replicate = ' . var_export( $replicate, true ), 0 );

			if ( $polylang->curlang ) {
				$current_language = $polylang->curlang->slug;
			} else {
				$current_language = pll_default_language();
			}
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $current_language = ' . var_export( $current_language, true ), 0 );
			$taxonomies = array();
			foreach( $polylang->model->get_translated_taxonomies() as $taxonomy ) {
				if ( MLACore::mla_taxonomy_support($taxonomy, 'support') ) {
					$taxonomies[ $taxonomy ] = $taxonomy;
				}
			}
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $category = ' . var_export( $category, true ), 0 );
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $taxonomies = ' . var_export( $taxonomies, true ), 0 );

			$other_languages = array();
			foreach( $polylang->model->get_languages_list() as $item_language ) {
				if ( $current_language !== $item_language->slug ) {
					$other_languages[ $item_language->slug ] = $item_language;
				}
			}
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $other_languages = ' . var_export( $other_languages, true ), 0 );
		}
		
		if ( ( ! empty( $new_text ) ) && in_array( $setting_key, $taxonomies ) ) {
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text mapping rule = ' . var_export( self::$current_mapping_rule, true ), 0 );
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $new_text = ' . var_export( $new_text, true ), 0 );
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $setting_key = ' . var_export( $setting_key, true ), 0 );
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $post_id = ' . var_export( $post_id, true ), 0 );
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $attachment_metadata = ' . var_export( $attachment_metadata, true ), 0 );
			$language_details = $polylang->model->get_post_language( $post_id );
			$item_language = $language_details->slug;
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $language_details = ' . var_export( $language_details, true ), 0 );

			/*
			 * Find the parent term and its translations
			 */
			if ( isset( self::$current_mapping_rule['parent'] ) ) {
				if ( $parent_term = absint( self::$current_mapping_rule['parent'] ) ) {
					$parent_term = self::_get_relevant_term( 'id', $parent_term, $setting_key );
				}
			} else {
				$parent_term = 0;
			}
			
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $parent_term = ' . var_export( $parent_term, true ), 0 );
			
			$new_terms = array();
			foreach( $new_text as $new_name ) {
				$relevant_term = self::_get_relevant_term( 'name', $new_name, $setting_key );
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $relevant_term = ' . var_export( $relevant_term, true ), 0 );

				if ( $relevant_term ) {
					if ( isset( $relevant_term['translations'][ $item_language ] ) ) {
						$new_terms[] = absint( $relevant_term['translations'][ $item_language ]->element_id );
					}
				} else {
					/*
					 * Always create the new term in the current language
					 */
					if ( $parent_term && isset( $parent_term['translations'][ $current_language ] ) ) {
						$parent = $parent_term['translations'][ $current_language ]->element_id;
					} else {
						$parent = 0;
					}
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $parent = ' . var_export( $parent, true ), 0 );
					
					$res = wp_insert_term( $new_name, $setting_key, array( 'parent' => $parent ) );
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $res = ' . var_export( $res, true ), 0 );
					if ( ( ! is_wp_error( $res ) ) && isset( $res['term_id'] ) ) {
						$polylang->model->set_term_language( $res['term_id'], $current_language );
					}
					 
					/*
					 * Add translations in the other languages?
					 */
					if ( $replicate ) {
						$source_term = $res['term_id'];
						$translations = array();
						foreach( $other_languages as $language => $language_details ) {
							if ( $parent_term && isset( $parent_term['translations'][ $language ] ) ) {
								$parent = $parent_term['translations'][ $language ]->element_id;
							} else {
								$parent = 0;
							}
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $parent = ' . var_export( $parent, true ), 0 );
					
							// save_language() does a check_admin_referer() security test
							$_REQUEST['_pll_nonce'] = wp_create_nonce( 'pll_language' );
							$_POST['term_lang_choice'] = $language;
							$_POST['action'] = 'mla';
							$res = wp_insert_term( $new_name, $setting_key, array( 'parent' => $parent ) );
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $res = ' . var_export( $res, true ), 0 );
							if ( ( ! is_wp_error( $res ) ) && isset( $res['term_id'] ) ) {
								$polylang->model->set_term_language( $res['term_id'], $language );
								$translations[ $language ] = $res['term_id'];
							}
						}

						unset( $_POST['term_lang_choice'] );
						unset( $_POST['action'] );
						
						if ( ! empty( $translations ) ) {
							$polylang->model->save_translations( 'term', $source_term, $translations);
						}
					} // replicate

					/*
					 * Reload the term with all of its new translations
					 */
					$relevant_term = self::_get_relevant_term( 'name', $new_name, $setting_key );
//error_log( __LINE__ . ' MLA_Polylang::mla_mapping_new_text $relevant_term = ' . var_export( $relevant_term, true ), 0 );
					if ( isset( $relevant_term['translations'][ $item_language ] ) ) {
						$new_terms[] = absint( $relevant_term['translations'][ $item_language ]->element_id );
					}
				} // new term
			} // foreach new_name

			MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::mla_mapping_new_text( {$setting_key}, {$post_id} ) \$new_terms = " . var_export( $new_terms, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
			return $new_terms;
		} // translated taxonomy

		return  $new_text;
	} // mla_mapping_new_text

	/**
	 * Remove "All languages" filter
	 *
	 * @since 2.20
	 *
	 * @return	void
	 */
	public static function mla_end_mapping() {
			remove_filter( 'mla_get_shortcode_attachments_final_terms', 'MLA_Polylang::mla_get_shortcode_attachments_final_terms' );
	} // mla_end_mapping

	/**
	 * Taxonomy terms and translations
	 *
	 * NOTE: WPML uses term_taxonomy_id as the "element_id" in its translations;
	 * Polylang uses term_id as the "element_id".
	 *
	 * @since 2.11
	 *
	 * @var	array	[ $term_taxonomy_id ] => array( $term, $translations )
	 */
	private static $relevant_terms = array();

	/**
	 * Adds a term and its translations to $relevant_terms
	 *
	 * @since 2.11
	 * @uses MLA_Polylang::$relevant_terms
	 *
	 * @param	object	WordPress term object
	 * @param	object	Polylang translations object; optional
	 */
	private static function _add_relevant_term( $term, $translations = NULL ) {
		global $polylang;

		if ( ! is_object( $term ) ) {
			return false;
		}

		if ( ! array_key_exists( $term->term_taxonomy_id, self::$relevant_terms ) ) {
			if ( empty( $translations ) ) {
				$translations = array();
				foreach ( $polylang->model->get_translations( 'term', $term->term_id ) as $language_code => $translation ) {
					$translations[ $language_code ] = (object) array( 'element_id' => $translation );
				}

				if ( empty( $translations ) ) {
					$language_code = pll_default_language();
					$translations[ $language_code ] = (object) array( 'element_id' => $term->term_id );
				}
			}

			self::$relevant_terms[ $term->term_taxonomy_id ]['term'] = $term;
			self::$relevant_terms[ $term->term_taxonomy_id ]['translations'] = $translations;
		}

		return self::$relevant_terms[ $term->term_taxonomy_id ];
	} // _add_relevant_term

	/**
	 * Finds a $relevant_term (if defined) given a key and (optional) a language
	 *
	 * @since 2.11
	 * @uses MLA_Polylang::$relevant_terms
	 *
	 * @param	string	$field to search in; 'id', 'name', or 'term_taxonomy_id'
	 * @param	mixed	$value to search for; integer, string or integer
	 * @param	string	$taxonomy to search in; slug
	 * @param	string	$language code; string; optional
	 * @param	boolean	$test_only false (default) to add missing term, true to leave term out
	 */
	private static function _get_relevant_term( $field, $value, $taxonomy, $language = NULL, $test_only = false ) {
		/*
		 * WordPress encodes special characters, e.g., "&" as HTML entities in term names
		 */
		if ( 'name' == $field ) {
			$value = _wp_specialchars( $value );
		}

		$relevant_term = false;
		foreach( self::$relevant_terms as $term_taxonomy_id => $candidate ) {
			if ( $taxonomy != $candidate['term']->taxonomy ) {
				continue;
			}

			switch ( $field ) {
				case 'id':
					if ( $value == $candidate['term']->term_id ) {
						$relevant_term = $candidate;
					}
					break;
				case 'name':
					if ( $value == $candidate['term']->name ) {
						$relevant_term = $candidate;
					}
					break;
				case 'term_taxonomy_id':
					if ( $value == $term_taxonomy_id ) {
						$relevant_term = $candidate;
					}
					break;
			} // field

			if ( ! empty( $relevant_term ) ) {
				break;
			}
		} // relevant term

 		if ( ( false === $relevant_term ) && $test_only ) {
			return false;
		}

		/*
		 * If no match, try to add it and its translations
		 */
 		if ( ( false === $relevant_term ) && $candidate = get_term_by( $field, $value, $taxonomy ) ) {
			$relevant_term =  self::_add_relevant_term( $candidate );

			foreach ( $relevant_term['translations'] as $translation ) {
				if ( get_term_by( 'id', $translation->element_id, $taxonomy, NULL, true ) ) {
					continue;
				}

				$term_object = get_term_by( 'id', $translation->element_id, $taxonomy );
				self::_add_relevant_term( $term_object, $relevant_term['translations'] );
			} // translation
		} // new term

		/*
		 * Find the language-specific value, if requested
		 */
		if ( $relevant_term && ! empty( $language ) ) {
			if ( $relevant_term && array_key_exists( $language, $relevant_term['translations'] ) ) {
				$relevant_term = self::$relevant_terms[ $relevant_term['translations'][ $language ]->element_id ];
			} else {
				$relevant_term = false;
			}
		}

		return $relevant_term;
	}

	/**
	 * Taxonomy terms for the current item translation in the database
	 *
	 * @since 2.11
	 *
	 * @var	array	['element_id'] => $post_id;
	 * 				[ 'language_code' ] => WPML item language or default language, e.g., 'en'
	 * 				[ 'slug' ] => Polylang item language or default language, e.g., 'en'
	 * 				[ $language ][ translation_details ]
	 * 				[ $language ][ $taxonomy ][ $term_taxonomy_id ] => $term
	 */
	private static $existing_terms = array( 'element_id' => 0 );

	/**
	 * Build the $existing_terms array
	 *
	 * Takes each translatable taxonomy and builds an array of
	 * language-specific term_id to term_id/term_name mappings 
	 * for terms already assigned to the item translation.
	 *
	 * @since 2.11
	 * @uses MLA_Polylang::$existing_terms
	 * @uses MLA_Polylang::$relevant_terms
	 *
	 * @param	integer	$post_id ID of the current post
	 *
	 */
	private static function _build_existing_terms( $post_id ) {
		global $polylang;

		if ( $post_id == self::$existing_terms['element_id'] ) {
			return;
		}

		$language_details = $polylang->model->get_post_language( $post_id );
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_build_existing_terms( {$post_id} ) \$polylang->model->get_post_language = " . var_export( $language_details, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );

		if ( is_object( $language_details ) ) {
			$language_details = (array) $language_details;
		} else {
			MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_build_existing_terms( {$post_id} ) pll_default_language() = " . var_export( pll_default_language(), true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
			$language_details = (array) $polylang->model->get_language( pll_default_language() );
			MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_build_existing_terms( {$post_id} ) \$polylang->model->get_language( pll_default_language() ) = " . var_export( $language_details, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		}

		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_build_existing_terms( {$post_id} ) \$polylang->model->get_translations() = " . var_export( $polylang->model->get_translations( 'post', $post_id ), true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		$translations = array();
		foreach ( $polylang->model->get_translations( 'post', $post_id ) as $language_code => $translation ) {
			$translations[ $language_code ] = array( 'element_id' => $translation );
		}

		if ( empty( $translations ) ) {
			$translations[ $language_details['slug'] ] = array( 'element_id' => $post_id );
		}

		self::$existing_terms = array_merge( array( 'element_id' => $post_id, 'slug' => $language_details['slug'] ), $translations );
		$taxonomies = $polylang->model->get_translated_taxonomies();

		/*
		 * Find all assigned terms and build term_master array
		 */		
		foreach ( $translations as $language_code => $translation ) {
			foreach ( $taxonomies as $taxonomy_name ) {
				if ( $terms = get_the_terms( $translation['element_id'], $taxonomy_name ) ) {
					foreach ( $terms as $term ) {
						self::_add_relevant_term( $term );
						self::$existing_terms[ $language_code ][ $taxonomy_name ][ $term->term_taxonomy_id ] = $term;
					} // term
				} else {
					self::$existing_terms[ $language_code ][ $taxonomy_name ] = array();
				}
			} // taxonomy
		} // translation

		/*
		 * Add missing translated terms to the term_master array
		 */		
		foreach ( self::$relevant_terms as $term ) {
			foreach ( $term['translations'] as $translation ) {
				if ( get_term_by( 'id', $translation->element_id, $term['term']->taxonomy, NULL, true ) ) {
					continue;
				}

				$term_object = get_term_by( 'id', $translation->element_id, $term['term']->taxonomy );
				self::_add_relevant_term( $term_object, $term['translations'] );
			} // translation
		} // term

		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_build_existing_terms( {$post_id} ) self::\$existing_terms = " . var_export( self::$existing_terms, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_build_existing_terms( {$post_id} ) self::\$relevant_terms = " . var_export( self::$relevant_terms, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		return;
	}

	/**
	 * Update the $existing_terms array
	 *
	 * Takes each translatable taxonomy and rebuilds the array of
	 * language-specific term_id to term_id/term_name mappings 
	 * for the "current translation" represented by the $post_id.
	 *
	 * @since 2.11
	 * @uses MLA_Polylang::$existing_terms
	 * @uses MLA_Polylang::$relevant_terms
	 *
	 * @param	integer	$post_id ID of the current post
	 *
	 * @return	array	( taxonomy => term assignments ) before the update
	 */
	private static function _update_existing_terms( $post_id ) {
		global $polylang;
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_update_existing_terms( {$post_id} ) initial self::\$existing_terms = " . var_export( self::$existing_terms, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_update_existing_terms( {$post_id} ) initial self::\$relevant_terms = " . var_export( self::$relevant_terms, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );

		if ( $post_id != self::$existing_terms['element_id'] ) {
			return false;
		}

		$language_code = self::$existing_terms['slug'];

		if ( isset( self::$existing_terms[ $language_code ] ) ) {
			$translation = self::$existing_terms[ $language_code ];
		} else {
			$translation = array();
		}

		$terms_before = array();

		/*
		 * Find all assigned terms and update the array
		 */		
		$taxonomies = $polylang->model->get_translated_taxonomies();
		foreach ( $taxonomies as $taxonomy_name ) {
			$terms_before[ $taxonomy_name ] = isset( $translation[ $taxonomy_name ] ) ? $translation[ $taxonomy_name ] : array();
			$translation[ $taxonomy_name ] = array();
			if ( $terms = get_the_terms( $post_id, $taxonomy_name ) ) {
				foreach ( $terms as $term ) {
					self::_add_relevant_term( $term );
					$translation[ $taxonomy_name ][ $term->term_taxonomy_id ] = $term;
				} // term
			}
		} // taxonomy

		self::$existing_terms[ $language_code ] = $translation;

		/*
		 * Add missing translated terms to the term_master array
		 */		
		foreach ( self::$relevant_terms as $term ) {
			foreach ( $term['translations'] as $translation ) {
				if ( get_term_by( 'id', $translation->element_id, $term['term']->taxonomy, NULL, true ) ) {
					continue;
				}

				$term_object = get_term_by( 'id', $translation->element_id, $term['term']->taxonomy );
				self::_add_relevant_term( $term_object, $term['translations'] );
			} // translation
		} // term

		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_update_existing_terms( {$post_id} ) final self::\$existing_terms = " . var_export( self::$existing_terms, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_update_existing_terms( {$post_id} ) final self::\$relevant_terms = " . var_export( self::$relevant_terms, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		return $terms_before;
	}

	/**
	 * Replacement tax_input values in all languages
	 *
	 * @since 2.11
	 *
	 * @var	array	['tax_input_post_id'] => $post_id;
	 * 				[ $language ][ $taxonomy ] => array of integer term_ids (hierarchical)
	 * 				[ $language ][ $taxonomy ] => comma-delimited string of term names (flat)
	 */
	private static $tax_input = array( 'tax_input_post_id' => 0 );

	/**
	 * Build the $tax_input array
	 *
	 * Takes each term from the $tax_inputs parameter and builds an array of
	 * language-specific term_id to term_id/term_name mappings for all languages.
	 *
	 * @since 2.11
	 * @uses MLA_Polylang::$tax_input
	 * @uses MLA_Polylang::$existing_terms
	 *
	 * @param	integer	$post_id ID of the current post
	 * @param	array	$tax_inputs 'tax_input' request parameter
	 * @param	array	$tax_actions 'tax_action' request parameter
	 */
	private static function _build_tax_input( $post_id, $tax_inputs = NULL, $tax_actions = NULL ) {
		global $polylang;

		if ( $post_id == self::$tax_input['tax_input_post_id'] ) {
			return;
		}

		self::$tax_input = array( 'tax_input_post_id' => $post_id );
		$active_languages = $polylang->model->get_languages_list();

		/*
		 * See if we are cloning/"replacing" the existing assignments
		 */
		if ( ( NULL == $tax_inputs ) && ( NULL == $tax_actions ) && isset( self::$existing_terms['element_id'] ) && ($post_id == self::$existing_terms['element_id'] ) ) {
			$translation = self::$existing_terms[ self::$existing_terms['slug'] ];
			$taxonomies = $polylang->model->get_translated_taxonomies();
			$tax_inputs = array();
			$no_terms = true;
			foreach ( $taxonomies as $taxonomy_name ) {
				$terms = isset( $translation[ $taxonomy_name ] ) ? $translation[ $taxonomy_name ] : array();
				if ( ! empty( $terms ) ) {
					$no_terms = false;
					$input_terms = array();
					foreach ( $terms as $term ) {
						$input_terms[] = $term->term_id;
					}

					$tax_inputs[ $taxonomy_name ] = $input_terms;
				} else {
					$tax_inputs[ $taxonomy_name ] = array();
				}
			} // taxonomy_name

			if ( $no_terms ) {
				foreach( $active_languages as $language => $language_details ) {
					self::$tax_input[ $language_details->slug ] = array();
				}

				return;
			}
		} // cloning

		foreach ( $tax_inputs as $taxonomy => $terms ) {
			$tax_action = isset( $tax_actions[ $taxonomy ] ) ? $tax_actions[ $taxonomy ] : 'replace'; 
			$input_terms = array();
			// hierarchical taxonomy => array of term_id integer values; flat => comma-delimited string of names
			if ( $hierarchical = is_array( $terms ) ) {

				foreach( $terms as $term ) {
					if ( 0 == $term ) {
						continue;
					}

					$relevant_term = self::_get_relevant_term( 'id', $term, $taxonomy );
					if ( isset( $relevant_term['translations'] ) ) {
						foreach ( $relevant_term['translations'] as $language => $translation ) {
							if ( $translated_term = self::_get_relevant_term( 'id', $translation->element_id, $taxonomy ) ) {
								$input_terms[ $language ][ $translated_term['term']->term_taxonomy_id ] = $translated_term['term'];
							}
						} // for each language
					} // translations exist
				} // foreach term
			} else {
				// Convert names to an array
				$term_names = array_map( 'trim', explode( ',', $terms ) );

				foreach ( $term_names as $term_name ) {
					if ( ! empty( $term_name ) ) {
						$relevant_term = self::_get_relevant_term( 'name', $term_name, $taxonomy );
						if ( isset( $relevant_term['translations'] ) ) {
							foreach ( $relevant_term['translations'] as $language => $translation ) {
								if ( $translated_term = self::_get_relevant_term( 'id', $translation->element_id, $taxonomy ) ) {
									$input_terms[ $language ][ $translated_term['term']->term_taxonomy_id ] = $translated_term['term'];
								}
							} // for each language
						} // translations exist
					} // not empty
				} // foreach name
			} // flat taxonomy

			foreach( $active_languages as $language => $language_details ) {
				$language = $language_details->slug;
				/*
				 * Apply the tax_action to the terms_before to find the terms_after
				 */
				$term_changes = isset( $input_terms[ $language ] ) ? $input_terms[ $language ] : array();
				if ( 'replace' == $tax_action ) {
					$terms_after = $term_changes;
				} else {
					$terms_after = isset( self::$existing_terms[ $language ][ $taxonomy ] ) ? self::$existing_terms[ $language ][ $taxonomy ] : array();

					foreach( $term_changes as $term_taxonomy_id => $input_term ) {
						if ( 'add' == $tax_action ) {
							$terms_after[ $term_taxonomy_id ] = $input_term;
						} else {
							unset( $terms_after[ $term_taxonomy_id ] );
						}
					} // input_term
				}

				/*
				 * Convert terms_after to tax_input format
				 */
				$term_changes = array();
				foreach( $terms_after as $input_term ) {
					$term_changes[] = $input_term->term_id;
				}

				self::$tax_input[ $language ][ $taxonomy ] = $term_changes;
			} // language

		} // foreach taxonomy

		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_build_tax_input( {$post_id} ) self::\$tax_input = " . var_export( self::$tax_input, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_build_tax_input( {$post_id} ) self::\$relevant_terms = " . var_export( self::$relevant_terms, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
	} // _build_tax_input

	/**
	 * Filter the $tax_input array to a specific language
	 *
	 * @since 2.11
	 * @uses MLA_Polylang::$tax_input
	 * @uses MLA_Polylang::$existing_terms
	 *
	 * @param	integer	$post_id ID of the post to be updated
	 * @param	string	$post_language explicit language_code; optional
	 *
	 * @return	array	language-specific $tax_inputs
	 */
	private static function _apply_tax_input( $post_id, $post_language = NULL ) {
		global $polylang;

		if ( NULL == $post_language ) {
			if ( isset( self::$existing_terms['element_id'] ) && $post_id == self::$existing_terms['element_id'] ) {
				$post_language = self::$existing_terms['slug'];
			} else {
				$post_language = $polylang->model->get_post_language( $post_id );
				$post_language = $post_language->slug;
			}
		}

		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_apply_tax_input( {$post_id} ) \$post_language = " . var_export( $post_language, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_apply_tax_input( {$post_id} ) self::\$tax_input[ \$post_language ] = " . var_export( self::$tax_input[ $post_language ], true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		return self::$tax_input[ $post_language ];
	} // _apply_tax_input

	/**
	 * Compute Term Synchronization replacement $tax_inputs
	 *
	 * Assumes the "current post" in $existing_terms is the source
	 * and $existing_terms contains the target translation
	 *
	 * @since 2.11
	 * @uses MLA_Polylang::$existing_terms
	 *
	 * @param	string	$language the target translation code
	 *
	 * @return	array	$tax_inputs for Term Synchronization
	 */
	private static function _apply_synch_input( $language ) {
		global $polylang;

		// Make sure there IS a target translation
		if ( empty( self::$existing_terms[ $language ] ) ) {
			return false;
		}

		$source_language = self::$existing_terms['slug'];
		$taxonomies = $polylang->model->get_translated_taxonomies();

		/*
		 * Find all source terms with a destination equivalent, record destination equivalent
		 */
		$new_terms = array();
		foreach ( $taxonomies as $taxonomy ) {
			$new_terms[ $taxonomy ] = array();
			foreach( self::$existing_terms[ $source_language ][ $taxonomy ] as $ttid => $term ) {
				$source_term = self::_get_relevant_term( 'term_taxonomy_id', $ttid, $taxonomy );
				if ( isset( $source_term['translations'][ $language ] ) ) {
					$dest_term = self::_get_relevant_term( 'id', $source_term['translations'][ $language ]->element_id, $taxonomy );
					$new_terms[ $taxonomy ][ $dest_term['term']->term_taxonomy_id ] = $dest_term['term'];
				}
			}
		}

		/*
		 * Find all destination terms with a source equivalent, record destination equivalent
		 */
		$old_terms = array();
		foreach ( $taxonomies as $taxonomy ) {
			$old_terms[ $taxonomy ] = array();
			foreach( self::$existing_terms[ $language ][ $taxonomy ] as $ttid => $term ) {
				$source_term = self::_get_relevant_term( 'term_taxonomy_id', $ttid, $taxonomy );
				if ( isset( $source_term['translations'][ $source_language ] ) ) {
					$dest_term = self::_get_relevant_term( 'id', $source_term['translations'][ $source_language ]->element_id, $taxonomy );
					$old_terms[ $taxonomy ][ $dest_term['term']->term_taxonomy_id ] = $dest_term['term'];
				}
			}
		}

		/*
		 * Remove terms in common, leaving new_terms => add, old_terms => remove
		 */
		foreach ( $old_terms as $taxonomy => $terms ) {
			foreach ( $terms as $ttid => $term ) {
				if ( isset( $new_terms[ $taxonomy ][ $ttid ] ) ) {
					unset( $old_terms[ $taxonomy ][ $ttid ] );
					unset( $new_terms[ $taxonomy ][ $ttid ] );
				}
			} // terms
		} // taxonomies

		/*
		 * Compute "replace" tax_inputs for the target translation
		 */
		$translation = self::$existing_terms[ $language ];
		$synch_inputs = array();

		foreach ( $old_terms as $taxonomy => $terms ) {
			$translation_terms = isset( $translation[ $taxonomy ] ) ? $translation[ $taxonomy ] : array();
			$terms_changed = false;

			// Remove common terms
			foreach ( $old_terms[ $taxonomy ] as $ttid => $term ) {
				if ( isset( self::$relevant_terms[ $ttid ]['translations'][ $language ] ) ) {
					$term = self::_get_relevant_term( 'id', self::$relevant_terms[ $ttid ]['translations'][ $language ]->element_id, $taxonomy );
					$ttid = $term['term']->term_taxonomy_id;
					if ( isset( $translation_terms[ $ttid ] ) ) {
						unset( $translation_terms[ $ttid ] );
						$terms_changed = true;
					}
				}
			}

			// Add common terms
			foreach ( $new_terms[ $taxonomy ] as $ttid => $term ) {
				if ( isset( self::$relevant_terms[ $ttid ]['translations'][ $language ] ) ) {
					$term = self::_get_relevant_term( 'id', self::$relevant_terms[ $ttid ]['translations'][ $language ]->element_id, $taxonomy );
					$ttid = $term['term']->term_taxonomy_id;
					if ( ! isset( $translation_terms[ $ttid ] ) ) {
						$translation_terms[ $ttid ] = (object) array( 'term_id' => absint( $term['term']->term_id ), 'name' => $term['term']->name );
						$terms_changed = true;
					}
				}
			}

			if ( $terms_changed ) {
				$synch_inputs[ $taxonomy ] = $translation_terms;
			}
		} // taxonomies

		/*
		 * Convert synch terms to $tax_inputs format
		 */
		$tax_inputs = array();
		foreach ( $synch_inputs as $taxonomy_name => $terms ) {
			$taxonomy = get_taxonomy( $taxonomy_name );
			$input_terms = array();
			foreach ( $terms as $term ) {
				$input_terms[] = $term->term_id;
			}

			$tax_inputs[ $taxonomy_name ] = $input_terms;
		} // synch_inputs

		$post_id = self::$existing_terms[ $language ]['element_id'];
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_apply_synch_input( {$post_id} ) \$language = " . var_export( $language, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::_apply_synch_input( {$post_id} ) \$tax_inputs = " . var_export( $tax_inputs, true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		return $tax_inputs;		
	} // _apply_synch_input

	/**
	 * Apply Term Synchronization
	 *
	 * @since 2.15
	 * @uses MLA_Polylang::$existing_terms
	 *
	 * @param	integer	$post_id the item we're synchronizing to
	 *
	 * @return	array	$tax_inputs for Term Synchronization
	 */
	private static function _apply_term_synchronization( $post_id ) {
		if ( 'checked' == MLACore::mla_get_option( 'term_synchronization', false, false, MLA_Polylang::$mla_language_option_definitions ) ) {

			/*
			 * Update terms because they have changed
			 */
			$terms_before = self::_update_existing_terms( $post_id );

			// $tax_input is a convenient source of language codes; ignore $tax_inputs
			foreach( self::$tax_input as $language => $tax_inputs ) {
				/*
				 * Skip the language we've already updated
				 */
				if ( ( ! isset( self::$existing_terms[ $language ] ) ) || ( self::$existing_terms[ 'slug' ] == $language ) ) {
					continue;
				}

				$tax_inputs = self::_apply_synch_input( $language );
				if ( ! empty( $tax_inputs ) ) {
					$translation = self::$existing_terms[ $language ]['element_id'];
					MLAData::mla_update_single_item( $translation, array(), $tax_inputs );
				}
			} // translation
		} // do synchronization
	}

	/**
	 * Applies Term Synchronization after item updates
	 *
	 * @since 2.15
	 *
	 * @param	integer	$post_id ID of the item that was updated.
	 * @param	integer	$result	Zero if the update failed else ID of the item that was updated.
	 */
	public static function mla_updated_single_item( $post_id, $result ) {
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::mla_updated_single_item( {$post_id}, {$result} )", MLACore::MLA_DEBUG_CATEGORY_LANGUAGE );

		if ( self::$existing_terms['element_id'] == $post_id ) {
			/*
			 * Synchronize the changes to all other translations
			 */
			self::_apply_term_synchronization( $post_id );
		}
	}

	/**
	 * Captures "before update" term assignments from the Media/Edit Media screen
	 *
	 * @since 2.13
	 *
	 * @param WP_Post $post       The WP_Post object.
	 * @param array   $attachment An array of attachment metadata.
	 */
	public static function attachment_fields_to_save( $post, $attachment ) {
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::attachment_fields_to_save post = " . var_export( $post, true ), MLACore::MLA_DEBUG_CATEGORY_LANGUAGE );

		if ( 'editpost' ==  $post['action'] && 'attachment' == $post['post_type'] ) {
			self::_build_existing_terms( $post['post_ID'] );
		}

		return $post;
	}

	/**
	 * Filters taxonomy updates by language for Bulk Edit during Add New Media
	 * and the Media/Edit Media screen
	 *
	 * @since 2.11
	 *
	 * @param	integer	ID of the current post
	 */
	public static function edit_attachment( $post_id ) {
		static $already_updating = 0;

		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::edit_attachment( {$post_id} ) _REQUEST = " . var_export( $_REQUEST, true ), MLACore::MLA_DEBUG_CATEGORY_LANGUAGE );

		/*
		 * mla_update_single_item may call this action again
		 */
		if ( $already_updating == $post_id ) {
			return;
		} else {
			$already_updating = $post_id;
		}

		/*
		 * Check for Bulk Edit during Add New Media
		 */
		if ( ! empty( $_REQUEST['mlaAddNewBulkEditFormString'] ) ) {
			if ( ! empty( self::$bulk_edit_request['tax_input'] ) ) {
				$tax_inputs = self::$bulk_edit_request['tax_input'];
				if ( 'checked' == MLACore::mla_get_option( 'term_assignment', false, false, MLA_Polylang::$mla_language_option_definitions ) ) {
					self::_build_tax_input( $post_id, $tax_inputs, self::$bulk_edit_request['tax_action'] );
					$tax_inputs = self::_apply_tax_input( $post_id );
				}
			} else {
				$tax_inputs = NULL;
			}

			$updates = 	MLA::mla_prepare_bulk_edits( $post_id, self::$bulk_edit_request, self::$bulk_edit_map );
			unset( $updates['tax_input'] );
			unset( $updates['tax_action'] );

			MLAData::mla_update_single_item( $post_id, $updates, $tax_inputs );

			return;
		} // Upload New Media Bulk Edit

		/*
		 * For the Bulk Edit action on the Media/Assistant screen, only synchronization is needed
		 */
		if ( ! ( isset( $_REQUEST['bulk_action'] ) && 'bulk_edit' == $_REQUEST['bulk_action'] ) ) {
			/*
			 * This is the Media/Edit Media screen.
			 * The category taxonomy (edit screens) is a special case because 
			 * post_categories_meta_box() changes the input name
			 */
			if ( isset( $_REQUEST['tax_input'] ) ) {
				$tax_inputs = $_REQUEST['tax_input'];
			} else {
				$tax_inputs = array();
			}

			if ( isset( $_REQUEST['post_category'] ) ) {
				$tax_inputs['category'] = $_REQUEST['post_category'];
			}

			if ( isset( $_REQUEST['tax_action'] ) ) {
				$tax_actions = $_REQUEST['tax_action'];
			} else {
				$tax_actions = NULL;
			}

			if ( ( ! empty( $tax_inputs ) ) && ( 'checked' == MLACore::mla_get_option( 'term_assignment', false, false, MLA_Polylang::$mla_language_option_definitions ) ) ) {
				self::_build_tax_input( $post_id, $tax_inputs, $tax_actions );
				$tax_inputs = self::_apply_tax_input( $post_id );
			}

			if ( ! empty( $tax_inputs ) ) {
				MLAData::mla_update_single_item( $post_id, array(), $tax_inputs );
			}
		} // Media/Edit Media screen, NOT Bulk Edit
	} // edit_attachment

	/**
	 * Return terms in all languages when "Activate languages and translations for media"
	 * is disabled
	 *
	 * @since 2.22
	 *
	 * @param	array	( 'class' => $class_array, 'value' => $value_array, 'text' => $text_array )
	 */
	public static function mla_media_modal_terms_options( $term_values ) {
		global $polylang;
		static $in_process = false;

		// Avoid recursion loop		
		if ( $in_process ) {
			return $term_values;
		}

		/*
		 * Check Polylang Languages/Settings "Activate languages and translations for media" option
		 */
		if ( isset( $polylang->options['media_support'] ) && ! $polylang->options['media_support'] ) {
			$in_process = true;
			$dropdown_options = array( 'pll_get_terms_not_translated' => true );
			$term_values = MLAModal::mla_terms_options( MLA_List_Table::mla_get_taxonomy_filter_dropdown( 0, $dropdown_options ) );
			$in_process = false;
		}

		/*
		 * $class_array => HTML class attribute value for each option
		 * $value_array => HTML value attribute value for each option
		 * $text_array => HTML text content for each option
		 */
		return $term_values;
	} // mla_media_modal_terms_options

	/**
	 * Captures the existing term assignments before the  
	 * Media Manager Modal Window ATTACHMENT DETAILS taxonomy meta boxes updates
	 *
	 * @since 2.11
	 *
	 * @param	object	the current post
	 */
	public static function mla_media_modal_begin_update_compat_fields( $post ) {
		$post_id = $post->ID;

		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::mla_media_modal_begin_update_compat_fields( {$post_id} ) post = " . var_export( $post, true ), MLACore::MLA_DEBUG_CATEGORY_LANGUAGE );

		// Accumulate for possible term_assignment or term_synchronization
		self::_build_existing_terms( $post_id );
	} // mla_media_modal_begin_update_compat_fields

	/**
	 * Applies Term Assignment to the terms assigned to one
	 * Media Manager Modal Window ATTACHMENT DETAILS taxonomy
	 *
	 * @since 2.11
	 *
	 * @param	array	assigned term id/name values
	 * @param	string	taxonomy slug
	 * @param	object	taxonomy object
	 * @param	integer	current post ID
	 */
	public static function mla_media_modal_update_compat_fields_terms( $terms, $key, $value, $post_id ) {
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::mla_media_modal_update_compat_fields_terms( {$key}, {$post_id} ) terms = " . var_export( $terms, true ), MLACore::MLA_DEBUG_CATEGORY_LANGUAGE );

		if ( 'checked' == MLACore::mla_get_option( 'term_assignment', false, false, MLA_Polylang::$mla_language_option_definitions ) ) {
			if ( $value->hierarchical ) {
				$tax_inputs = array( $key => $terms );
			} else {
				$tax_inputs = array( $key => implode( ',', $terms ) );
			}

			self::_build_tax_input( $post_id, $tax_inputs );
			$tax_inputs = self::_apply_tax_input( $post_id );
			$terms = $tax_inputs[ $key ];
		} // term_assignment

		return $terms;
	} // mla_media_modal_update_compat_fields_terms

	/**
	 * Applies Term Synchronization after the
	 * Media Manager Modal Window taxonomy updates
	 *
	 * @since 2.11
	 *
	 * @param	string	HTML markup for the taxonomy meta box elements
	 * @param	array	supported  taxonomy objects
	 * @param	object	current post object
	 */
	public static function mla_media_modal_end_update_compat_fields( $results, $taxonomies, $post ) {
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::mla_media_modal_end_update_compat_fields( {$post->ID} ) taxonomies = " . var_export( $taxonomies, true ), MLACore::MLA_DEBUG_CATEGORY_LANGUAGE );

		/*
		 * Synchronize the changes to all other translations
		 */
		self::_apply_term_synchronization( $post->ID );

		return $results;
	} // mla_media_modal_end_update_compat_fields

	/**
	 * Captures the Quick Edit "before update" term assignments and
	 * process the Language dropdown selection, if changed
	 *
	 * @since 2.11
	 *
	 * @param	array	$item_content	NULL, to indicate no handler.
	 * @param	integer	$post_id		the affected attachment.
	 *
	 * @return	object	updated $item_content. NULL if no handler, otherwise
	 *					( 'message' => error or status message(s), 'body' => '',
	 *					  'prevent_default' => true to bypass the MLA handler )
	 */
	public static function mla_list_table_inline_action( $item_content, $post_id ) {
		global $polylang;

		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::mla_list_table_inline_action( {$post_id} )", MLACore::MLA_DEBUG_CATEGORY_LANGUAGE );

		self::_build_existing_terms( $post_id );
		if ( isset( $_REQUEST['action'] ) && 'mla-inline-edit-scripts' === $_REQUEST['action'] && isset( $_REQUEST['tax_input'] ) ) {
			MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::mla_list_table_inline_action( {$post_id} ) Quick Edit initial \$_REQUEST['tax_input'] = " . var_export( $_REQUEST['tax_input'], true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
			
			if ( 'checked' == MLACore::mla_get_option( 'term_assignment', false, false, MLA_Polylang::$mla_language_option_definitions ) ) {
				// Quick Edit calls update_single_item right after this filter
				self::_build_tax_input( $post_id, $_REQUEST['tax_input'] );
				$_REQUEST['tax_input'] = self::_apply_tax_input( $post_id );
			}
			
			MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::mla_list_table_inline_action( {$post_id} ) Quick Edit final \$_REQUEST['tax_input'] = " . var_export( $_REQUEST['tax_input'], true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		}

		// Language dropdown in Quick Edit area
		if ( isset( $_REQUEST['inline_lang_choice'] ) ) {
			$translations = $polylang->model->get_translations( 'post', $post_id );

			if ( ! array_key_exists( $_REQUEST['inline_lang_choice'], $translations ) ) {
				$post = get_post( $post_id );
				// save_post() does a check_admin_referer() security test
				$_REQUEST['_inline_edit'] = wp_create_nonce( 'inlineeditnonce' );
				$polylang->filters_post->save_post( $post_id, $post, true );

				// Record new language for Term Assignment and Synchronization
				if ( ( ! empty( $_REQUEST['tax_input'] ) ) && ( 'checked' == MLACore::mla_get_option( 'term_assignment', false, false, MLA_Polylang::$mla_language_option_definitions ) ) ) {
					// Discard the old translation, which is gone
					unset( self::$existing_terms[ self::$existing_terms['slug'] ] );
					self::$existing_terms['slug'] = $_REQUEST['inline_lang_choice'];
				}
			} // change language
		}

		return $item_content;
	} // mla_list_table_inline_action

	/**
	 * Captures the Bulk Edit, "Upload New Media" parameters
	 *
	 * @since 2.11
	 *
	 * @param	array	$request		bulk action request parameters, including ['mla_bulk_action_do_cleanup'].
	 * @param	string	$bulk_action	the requested action.
	 * @param	array	$custom_field_map	[ slug => field_name ]
	 *
	 * @return	array	updated bulk action request parameters
	 */
	public static function mla_list_table_bulk_action_initial_request( $request, $bulk_action, $custom_field_map ) {
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::mla_list_table_bulk_action_initial_request( {$bulk_action} ) request = " . var_export( $request, true ), MLACore::MLA_DEBUG_CATEGORY_LANGUAGE );

		self::$bulk_edit_request = $request;
		self::$bulk_edit_map = $custom_field_map;

		return $request;
	} // mla_list_table_bulk_action_initial_request

	/**
	 * Custom Field Map during Bulk Edit, "Upload New Media"
	 *
	 * @since 2.11
	 *
	 * @var	array	[ id ] => field name
	 */
	private static $bulk_edit_map = NULL;

	/**
	 * Bulk Edit parameters during Bulk Edit, "Upload New Media"
	 *
	 * @since 2.11
	 *
	 * @var	array	[ field ] => new value
	 */
	private static $bulk_edit_request = NULL;

	/**
	 * Converts Bulk Edit taxonomy inputs to language-specific values
	 *
	 * @since 2.11
	 *
	 * @param	array	$request		bulk action request parameters, including ['mla_bulk_action_do_cleanup'].
	 * @param	string	$bulk_action	the requested action.
	 * @param	integer	$post_id		the affected attachment.
	 * @param	array	$custom_field_map	[ slug => field_name ]
	 *
	 * @return	array	updated bulk action request parameters
	 */
	public static function mla_list_table_bulk_action_item_request( $request, $bulk_action, $post_id, $custom_field_map ) {
		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::mla_list_table_bulk_action_item_request( {$post_id} ) request = " . var_export( $request, true ), MLACore::MLA_DEBUG_CATEGORY_LANGUAGE );

		/*
		 * Note that $request may be modified by previous items, so we must return to the initial vlues
		 */
		if ( 'edit' == $bulk_action && ( ! empty( self::$bulk_edit_request['tax_input'] ) ) && ( 'checked' == MLACore::mla_get_option( 'term_assignment', false, false, MLA_Polylang::$mla_language_option_definitions ) ) ) {
			self::_build_existing_terms( $post_id );
			self::_build_tax_input( $post_id, self::$bulk_edit_request['tax_input'], $request['tax_action'] );
			$request['tax_input'] = self::_apply_tax_input( $post_id );
			foreach( self::$bulk_edit_request['tax_action'] as $taxonomy => $action ) {
				// _apply_tax_input changes a remove to a replace
				if ( 'remove' == $action ) {
					$request['tax_action'][ $taxonomy ] = 'replace';
				}
			}
		}

		if ( isset( $request['tax_input'] ) ) {
			MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::bulk_action_item_request( {$bulk_action}, {$post_id} ) \$request['tax_input'] = " . var_export( $request['tax_input'], true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		} else {
			MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::bulk_action_item_request( {$bulk_action}, {$post_id} ) \$request['tax_input'] NOT SET", MLACore::MLA_DEBUG_CATEGORY_AJAX );
		}

		if ( isset( $request['tax_action'] ) ) {
			MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::bulk_action_item_request( {$bulk_action}, {$post_id} ) \$request['tax_action'] = " . var_export( $request['tax_action'], true ), MLACore::MLA_DEBUG_CATEGORY_AJAX );
		} else {
			MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::bulk_action_item_request( {$bulk_action}, {$post_id} ) \$request['tax_action'] NOT SET", MLACore::MLA_DEBUG_CATEGORY_AJAX );
		}

		return $request;
	} // mla_list_table_bulk_action_item_request

	/**
	 * Sets the new item language from the Language dropdown selection.
	 *
	 * @since 2.11
	 *
	 * @param	array	$item_content	NULL, to indicate no handler.
	 * @param	string	$bulk_action	the requested action.
	 * @param	integer	$post_id		the affected attachment.
	 *
	 * @return	object	updated $item_content. NULL if no handler, otherwise
	 *					( 'message' => error or status message(s), 'body' => '',
	 *					  'prevent_default' => true to bypass the MLA handler )
	 */
	public static function mla_list_table_bulk_action( $item_content, $bulk_action, $post_id ) {
		global $polylang;

		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::mla_list_table_bulk_action( {$bulk_action}, {$post_id} )", MLACore::MLA_DEBUG_CATEGORY_LANGUAGE );

		// Language dropdown in Bulk Edit area
		if ( isset( $_POST['inline_lang_choice'] ) && ( '-1' != $_POST['inline_lang_choice'] ) ) {
			$post = get_post( $post_id );
			// save_post() does a check_admin_referer() security test
			$_REQUEST['_wpnonce'] = wp_create_nonce( 'bulk-posts' );
			$_REQUEST['bulk_edit'] = 'Update';
			$polylang->filters_post->save_post( $post_id, $post, true );

			if ( $_REQUEST['inline_lang_choice'] != -1 ) {
				$item_content = array( 'message' => "Item {$post_id}, language updated." );
			}
		}

		return $item_content;
	} // mla_list_table_bulk_action

	/**
	 * Items returned by custom bulk action(s)
	 *
	 * @since 2.11
	 *
	 * @var	array
	 */
	private static $bulk_action_includes = array();

	/**
	 * Creates new items from the "Bulk Translate" list.
	 *
	 * @since 2.11
	 *
	 * @param	array	$item_content	NULL, to indicate no handler.
	 * @param	string	$bulk_action	the requested action.
	 * @param	integer	$post_id		the affected attachment.
	 *
	 * @return	object	updated $item_content. NULL if no handler, otherwise
	 *					( 'message' => error or status message(s), 'body' => '' )
	 */
	public static function mla_list_table_custom_bulk_action( $item_content, $bulk_action, $post_id ) {
		global $polylang;

		MLACore::mla_debug_add( __LINE__ . " MLA_Polylang::mla_list_table_bulk_action_item_request( {$bulk_action}, {$post_id} )", MLACore::MLA_DEBUG_CATEGORY_LANGUAGE );

		if ( 'pll-translate' == $bulk_action ) {
			$translations = array();
			if ( isset( $_REQUEST['bulk_tr_languages'] ) ) {
				$bulk_tr_languages = $_REQUEST['bulk_tr_languages'];

				// Expand All Languages selection
				if ( isset( $bulk_tr_languages['all'] ) ) {
					foreach ($polylang->model->get_languages_list() as $language) {
						$bulk_tr_languages[ $language->slug ] = 'translate';
					}

					unset( $bulk_tr_languages['all'] );
				}

				// Process language selection(s)
				foreach( $bulk_tr_languages as $language => $action ) {
					$new_id = MLA_Polylang::_get_translation( $post_id, $language );
					$translations[] = $new_id;
				}
			}

			// Clear all the "Filter-by" parameters
			if ( isset( $_REQUEST['bulk_tr_options']['clear_filters'] ) ) {
				MLA::mla_clear_filter_by();
			}

			if ( empty( $translations ) ) {
				$item_content = array( 'message' => "Item {$post_id}, no translations." );
			} else {
				$_REQUEST['heading_suffix'] = __( 'Bulk Translations', 'media-library-assistant' );
				MLA_Polylang::$bulk_action_includes = array_merge( MLA_Polylang::$bulk_action_includes, $translations );
				$translations = implode( ',', $translations );
				$item_content = array( 'message' => "Item {$post_id}, translation(s): {$translations}." );
			}
		}

		return $item_content;
	} // mla_list_table_custom_bulk_action

	/**
	 * Filter the MLA_List_Table bulk actions
	 *
	 * Adds the "Translate" action to the Bulk Actions list.
	 *
	 * @since 2.11
	 *
	 * @param	array	$actions	An array of bulk actions.
	 *								Format: 'slug' => 'Label'
	 *
	 * @return	array	updated array of actions.
	 */
	public static function mla_list_table_get_bulk_actions( $actions ) {
		if ( 'checked' == MLACore::mla_get_option( 'bulk_translate', false, false, MLA_Polylang::$mla_language_option_definitions ) ) {
			$actions['pll-translate'] = __( 'Translate', 'media-library-assistant' );
		}
		return $actions;
	} // mla_list_table_get_bulk_actions

	/**
	 * MLA_List_Table inline edit item values
	 *
	 * Builds the Language dropdown and edit translation links for the
	 * Quick and Bulk Edit forms, adding them to the 'custom_fields'
	 * and 'bulk_custom_fields' substitution parameters.
	 *
	 * @since 2.11
	 *
	 * @param	array	$item_values parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_list_table_inline_values( $item_values ) {
		global $polylang;

		// Find the first "language" column slug
		foreach ( $polylang->filters_columns->model->get_languages_list() as $language) {
			if ( empty($polylang->filters_columns->curlang) || $language->slug != $polylang->filters_columns->curlang->slug) {
				$language_column = 'language_'.$language->slug;
				break;
			}
		}

		// do_action is required because the Polylang function uses "current_filter()" to compose its output
		ob_start();
		do_action( 'quick_edit_custom_box', $language_column, 'attachment' );
		$value = ob_get_clean();

		// Strip off <fieldset> and <div> tags around the <input> and <label> tags
		preg_match('/\<input|\<label/', $value, $match_start, PREG_OFFSET_CAPTURE );
		preg_match('/\<\/label[^\>]*\>/', $value, $match_end, PREG_OFFSET_CAPTURE );
		$item_values['custom_fields'] .= substr( $value, $match_start[0][1], ( $match_end[0][1] + strlen( $match_end[0][0] ) ) - $match_start[0][1] );

		// Add the Translate links to the Quick Edit values
		if ( array_key_exists( 'Quick Edit', $item_values ) ) {
			$actions = "<input name=\"inline_translations\" type=\"hidden\" value=\"\">\n";
			$actions .= "<input name=\"pll_quick_language\" type=\"hidden\" value=\"\">\n";
			$actions .= "<input name=\"pll_quick_id\" type=\"hidden\" value=\"\">\n";
			$actions .= "<input name=\"lang\" type=\"hidden\" value=\"\">\n";
			$actions .= "<label class=\"alignleft\" style=\"clear: both;\">\n<span class=\"title\">Translate</span>\n";
			$actions .= "<table class=\"pll-media-action-table\">\n";
			foreach ($polylang->model->get_languages_list() as $language) {
				$actions .= '<tr class = "pll-media-action-row-' . $language->slug . "\">\n";
				$actions .= '<td class = "pll-media-language-column"><span class = "pll-translation-flag">'. $language->flag . '</span>' . esc_html( $language->name ) . "</td>\n";
				$actions .= '<td class = "pll-media-action-column pll-media-action-column-' . $language->slug . '">';
				$actions .= sprintf( '<input type="hidden" name="media_tr_lang[%s]" value="" /><a href="#pll-quick-translate-edit" title="" class=""></a>', esc_attr($language->slug) );
				$actions .= "</td>\n";
				$actions .= "</tr>\n";
			}
			$actions .= "</table>\n</label>\n";
			$actions .= "<div class=\"pll-quick-translate-save\"><span class=\"spinner\" style=\"float: left;\"></span></div>\n";
			$item_values['custom_fields'] .= $actions;
		}

		ob_start();
		do_action( 'bulk_edit_custom_box', $language_column, 'attachment' );
		$value = ob_get_clean();

		// Strip off <fieldset> and <div> tags around the <input> and <label> tags
		preg_match('/\<input|\<label/', $value, $match_start, PREG_OFFSET_CAPTURE );
		preg_match('/\<\/label[^\>]*\>/', $value, $match_end, PREG_OFFSET_CAPTURE );
		$item_values['bulk_custom_fields'] .= substr( $value, $match_start[0][1], ( $match_end[0][1] + strlen( $match_end[0][0] ) ) - $match_start[0][1] );

		return $item_values;
	} // mla_list_table_inline_values

	/**
	 * MLA_List_Table inline edit parse
	 *
	 * @since 2.11
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
		global $polylang, $post_ID;

		/*
		 * Add the Quick and Bulk Translate Markup
		 */
		$page_template_array = MLACore::mla_load_template( 'mla-polylang-support.tpl' );
		if ( ! is_array( $page_template_array ) ) {
			error_log( 'ERROR: mla-polylang-support.tpl path = ' . var_export( plugin_dir_path( __FILE__ ) . 'mla-polylang-support.tpl', true ), 0 );
			error_log( 'ERROR: mla-polylang-support.tpl non-array result = ' . var_export( $page_template_array, true ), 0 );
			return $html_markup;
		}

		$language_dropdowns = MLA_Polylang::mla_list_table_inline_values( array( 'custom_fields' => '', 'bulk_custom_fields' => '' ) );

		$quick_actions = "<table class=\"pll-media-action-table\">\n";
		$bulk_actions = "<table class=\"pll-media-action-table\">\n";
		foreach ($polylang->model->get_languages_list() as $language) {
			$page_values = array(
				'language_slug' => $language->slug,
				'language_flag' => $language->flag,
				'language_name' => $language->name,
			);
			$quick_actions .= MLAData::mla_parse_template( $page_template_array['quick_action'], $page_values );
			$bulk_actions .= MLAData::mla_parse_template( $page_template_array['bulk_action'], $page_values );
		}

		$quick_actions .= "</table>\n";

		$page_values = array(
			'language_slug' => 'all',
			'language_flag' => '&nbsp;',
			'language_name' => __( 'All Languages', 'media-library-assistant' ),
		);
		$bulk_actions .= MLAData::mla_parse_template( $page_template_array['bulk_action'], $page_values );
		$bulk_actions .= "</table>\n";

		$page_values = array(
			'colspan' => $item_values['colspan'],
			'Quick Translate' => __( 'Quick Translate', 'media-library-assistant' ),
			'quick_translate_actions' => $quick_actions,
			'quick_translate_language' => $language_dropdowns['custom_fields'],
			'Cancel' => __( 'Cancel', 'media-library-assistant' ),
			'Update' => __( 'Set Language', 'media-library-assistant' ),
			'Bulk Translate' => __( 'Bulk Translate', 'media-library-assistant' ),
			'Add or Modify' => __( 'Add or Modify Translation', 'media-library-assistant' ),
			'Language' => __( 'Language', 'media-library-assistant' ),
			'bulk_translate_actions' => $bulk_actions,
			'Options' => __( 'Options', 'media-library-assistant' ),
			'Clear Filter-by' => __( 'Clear Filter-by', 'media-library-assistant' ),
		);
		$parse_value = MLAData::mla_parse_template( $page_template_array['page'], $page_values );

		return $html_markup . "\n" . $parse_value;
	} // mla_list_table_inline_parse

	/**
	 * Table language column definitions
	 *
	 * @since 2.11
	 *
	 * @var	array
	 */
	protected static $language_columns = NULL;

	/**
	 * Filter the MLA_List_Table columns
	 *
	 * Inserts the language columns just after the item thumbnail column
	 *
	 * @since 2.11
	 *
	 * @param	array	$columns An array of columns.
	 *					format: column_slug => Column Label
	 *
	 * @return	array	updated array of columns.
	 */
	public static function mla_list_table_get_columns( $columns ) {
		if ( is_null( MLA_Polylang::$language_columns ) ) {
			global $polylang;

			/*
			 * Build language management columns
			 */
			$show_language = 'checked' == MLACore::mla_get_option( 'language_column', false, false, MLA_Polylang::$mla_language_option_definitions );

			$languages = count( $polylang->model->get_languages_list() );
			$view_status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';
			if ( 1 < $languages && $view_status != 'trash' ) {
				$show_translations = 'checked' == MLACore::mla_get_option( 'translations_column', false, false, MLA_Polylang::$mla_language_option_definitions );
			} else {
				$show_translations = false;
			}

			MLA_Polylang::$language_columns = array();

			if ( $show_language && empty( $polylang->curlang ) ) {
				MLA_Polylang::$language_columns[ 'language' ] = __( 'Language', 'media-library-assistant' );	
			}

			if ( $show_translations ) {
				$flags_column = $polylang->filters_columns->add_post_column( array() );
				if ( is_array($flags_column ) ) {
					$flags_column = implode( '', $flags_column );
					MLA_Polylang::$language_columns['pll_translations'] = $flags_column;
				}
			}
		} // add columns

		if ( ! empty( MLA_Polylang::$language_columns ) ) {
			$end = array_slice( $columns, 2) ;
			$columns = array_slice( $columns, 0, 2 );
			$columns = array_merge( $columns, MLA_Polylang::$language_columns, $end );
		}

		return $columns;
	} // mla_list_table_get_columns_filter

	/**
	 * Add styles for the pll_translations table column
	 *
	 * @since 2.11
	 *
	 * @return	void	echoes CSS styles before returning
	 */
	public static function mla_list_table_add_pll_styles() {
		global $polylang;

		$current_language = $polylang->curlang;
		$languages = count( $polylang->model->get_languages_list() );
		$view_status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';

		if ( 1 < $languages && $view_status != 'trash' ) {
			$w = 22 * ( empty( $current_language ) ? $languages : $languages - 1 );
			echo '<style type="text/css">.column-pll_translations{width:' . $w . 'px;}.column-pll_translations img{margin:2px;}</style>';
		}
	}

	/**
	 * Supply a column value if no column-specific function has been defined
	 *
	 * Fills in the Language columns with the item's translation status values.
	 *
	 * @since 2.11
	 *
	 * @param	string	NULL, indicating no default content
	 * @param	array	A singular item (one full row's worth of data)
	 * @param	array	The name/slug of the column to be processed
	 * @return	string	Text or HTML to be placed inside the column
	 */
	public static function mla_list_table_column_default( $content, $item, $column_name ) {
		global $polylang;
		static $languages = NULL, $current_language;

		if ( 'language' == $column_name ) {
			$item_language = $polylang->model->get_post_language( $item->ID );
			$content = is_object( $item_language ) ? $item_language->name : 'none';
		} elseif ('pll_translations' == $column_name ) {
			if ( is_null( $languages ) ) {
				$current_language = $polylang->curlang;
				$languages = $polylang->model->get_languages_list();
			}

			$content = '';
			foreach ($polylang->model->get_languages_list() as $language) {
				// don't add the column for the filtered language
				if ( empty($current_language) || $language->slug != $current_language->slug ) {
					// Polylang post_column() function applies this test for 'inline-save' before updating "language"
					$inline = defined('DOING_AJAX') && isset($_POST['inline_lang_choice']) && in_array( $_REQUEST['action'], array( MLA_Polylang::MLA_PLL_QUICK_TRANSLATE, MLACore::JAVASCRIPT_INLINE_EDIT_SLUG ) );

					if ( $inline ) {
						$save_action = $_REQUEST['action'];
						$_REQUEST['action'] = 'inline-save';
					}

					// post_column echoes the content and returns NULL
					ob_start();
					$polylang->filters_columns->post_column( 'language_' . $language->slug, $item->ID );
					$content .= ob_get_clean();

					if ( $inline ) {
						$_REQUEST['action'] = $save_action;
					}
				} // include language
			} // each language
		}

		return $content;
	} // mla_list_table_column_default_filter

	/**
	 * Data selection parameters for custom views
	 *
	 * @since 2.11
	 *
	 * @var	array
	 */
	private static $list_table_parameters = array(
		'total_items' => NULL,
		'per_page' => NULL,
		'current_page' => NULL,
	);

	/**
	 * Filter the "sticky" submenu URL parameters
	 *
	 * Adds a language ('lang') parameter to the URL parameters that
	 * will be retained when the submenu page refreshes.
	 * Maintains the list of Bulk Translate items in the URLs for
	 * paging through the results.
	 *
	 * @since 2.11
	 *
	 * @param	array	$submenu_arguments	Current view, pagination and sort parameters.
	 * @param	object	$include_filters	True to include "filter-by" parameters, e.g., year/month dropdown.
	 *
	 * @return	array	updated submenu_arguments.
	 */
	public static function mla_list_table_submenu_arguments( $submenu_arguments, $include_filters ) {
		global $polylang;

		if ( isset( $_REQUEST['lang'] ) ) {
			$submenu_arguments['lang'] = $_REQUEST['lang'];
		} elseif ( $polylang->curlang ) {		 
			$submenu_arguments['lang'] = $polylang->curlang->slug;
		} else {		 
			$submenu_arguments['lang'] = 'all';
		}

		if ( $include_filters && ( ! empty( MLA_Polylang::$bulk_action_includes ) ) ) {
			$submenu_arguments['ids'] = implode( ',', MLA_Polylang::$bulk_action_includes );
			$submenu_arguments['heading_suffix'] = __( 'Bulk Translations', 'media-library-assistant' );
		}

		return $submenu_arguments;
	} // mla_list_table_submenu_arguments

	/**
	 * Filter the pagination parameters for prepare_items()
	 *
	 * Records the pagination parameters for use with custom table views, e.g., "attached".
	 *
	 * @since 2.11
	 *
	 * @param	array	$pagination		Contains 'per_page', 'current_page'.
	 * @param	object	$mla_list_table	The MLA_List_Table object, passed by reference.
	 *
	 * @return	array	updated pagination array.
	 */
	public static function mla_list_table_prepare_items_pagination( $pagination, $mla_list_table ) {
		MLA_Polylang::$list_table_parameters = array_merge( MLA_Polylang::$list_table_parameters, $pagination );

		return $pagination;
	} // mla_list_table_prepare_items_pagination_filter

	/**
	 * Filter the total items count for prepare_items()
	 *
	 * A convenient place to add the query argument required for the
	 * "Bulk Translate" custom view.
	 *
	 * @since 2.11
	 *
	 * @param	integer	$total_items	NULL, indicating no substitution.
	 * @param	object	$mla_list_table	The MLA_List_Table object, passed by reference.
	 *
	 * @return	integer	updated total_items.
	 */
	public static function mla_list_table_prepare_items_total_items( $total_items, $mla_list_table ) {
		global $wpdb;

		if ( isset( $_REQUEST['pll-bulk-translate'] ) ) {
			$_REQUEST['ids'] = MLA_Polylang::$bulk_action_includes;
		}

		return $total_items;
	} // mla_list_table_prepare_items_total_items_filter

	/**
	 * Filter the list of item "Rollover" actions
	 *
	 * Adds "Quick Translate" to the list of item-level "Rollover" actions.
	 *
	 * @since 2.11
	 *
	 * @param	array	$actions	The list of item "Rollover" actions.
	 * @param	object	$item		The current Media Library item.
	 * @param	string	$column		The List Table column slug.
	 *
	 * @return	array	updated		"Rollover" actions.
	 */
	public static function mla_list_table_build_rollover_actions( $actions, $item, $column ) {
		if ( 'checked' == MLACore::mla_get_option( 'quick_translate', false, false, MLA_Polylang::$mla_language_option_definitions ) ) {
			/*
			 * Add the Quick Translate action
			 */
			$actions['translate hide-if-no-js'] = '<a class="inlineTranslate" href="#" title="' . __( 'Translate this item inline', 'media-library-assistant' ) . '">' . __( 'Quick Translate', 'media-library-assistant' ) . '</a>';
		}

		return $actions;
	} // mla_list_table_build_rollover_actions_filter

	/**
	 * Define the fields for inline (Quick) editing
	 *
	 * Adds Language dropdown and Quick Translate links.
	 *
	 * @since 2.11
	 *
	 * @param	array	$fields	The field names for inline data.
	 *
	 * @return	string	updated fields for inline data.
	 */
	public static function mla_list_table_inline_fields( $fields ) {
		$fields[] = 'lang';
		$fields[] = 'old_lang';
		$fields[] = 'inline_lang_choice';
		$fields[] = 'inline_translations';

		return $fields;
	} // mla_list_table_inline_fields

	/**
	 * Filter the data for inline (Quick and Bulk) editing
	 *
	 * Adds item-specific translations data for the JS quick and bulk edit functions.
	 *
	 * @since 2.11
	 *
	 * @param	string	$inline_data	The HTML markup for inline data.
	 * @param	object	$item			The current Media Library item.
	 *
	 * @return	string	updated HTML markup for inline data.
	 */
	public static function mla_list_table_build_inline_data( $inline_data, $item ) {
		global $polylang;

		$item_id = $item->ID;
		$old_lang = $polylang->model->get_post_language( $item_id );
		$translations = $polylang->model->get_translations( 'post', $item_id );

		if ( isset( $old_lang->slug ) ) {
			$old_lang = $old_lang->slug;
			$translations[ $old_lang ] = $item_id;
		} else {
			$old_lang = '';
		}

		$translations = json_encode( $translations );

		$inline_data .= "\n\t<div class=\"lang\">{$old_lang}</div>";
		$inline_data .= "\n\t<div class=\"old_lang\">{$old_lang}</div>";
		$inline_data .= "\n\t<div class=\"inline_lang_choice\">{$old_lang}</div>";
		$inline_data .= "\n\t<div class=\"inline_translations\">{$translations}</div>";

		return $inline_data;
	} // mla_list_table_build_inline_data

	/**
	 * Not used in this version of the plugin
	 *
	 * @since 2.15
	 *
	 * @param	NULL	NULL to indicate no changes to the default processing.
	 * @param	array	Column definitions for the edit taxonomy list table.
	 * @param	string	Slug of the taxonomy for this submenu.
	 *
	 * @return	array	NULL or replacement columns array.
	 */
	public static function mla_taxonomy_get_columns( $filter_columns, $columns, $taxonomy ) {
		return $filter_columns;
	}

	/**
	 * Adds the "Language" tab to the Settings/Media Library Assistant list
	 *
	 * @since 2.11
	 *
	 * @param	array|false	The entire tablist ( $tab = NULL ), a single tab entry or false if not found/not allowed.
	 * @param	array		The entire default tablist
	 * @param	string|NULL	tab slug for single-element return or NULL to return entire tablist
	 *
	 * @return	array	updated tablist or single tab element
	 */
	public static function mla_get_options_tablist( $results, $mla_tablist, $tab ) {
		$language_key = 'language';
		$language_value = array( 'title' => __( 'Language', 'media-library-assistant' ), 'render' => array( 'MLA_Polylang', 'mla_render_language_tab' ) );

		if ( $language_key == $tab ) {
			return $language_value;
		}

		return array_merge( $results, array( $language_key => $language_value ) );
	}

	/**
	 * $mla_language_option_definitions defines the language-specific database options and
	 * admin page areas for setting/updating them
	 *
	 * The array must be populated at runtime in MLA_Polylang::mla_localize_language_option_definitions(),
	 * because localization calls cannot be placed in the "public static" array definition itself.
	 *
	 * Each option is defined by an array with the elements documented in class-mla-options.php
	 */
	 
	public static $mla_language_option_definitions = array ();

	/**
	 * Localize $mla_language_option_definitions array
	 *
	 * Localization must be done at runtime, and these calls cannot be placed
	 * in the "public static" array definition itself.
	 *
	 * @since 2.11
	 *
	 * @return	void
	 */
	public static function mla_localize_language_option_definitions() {
		global $polylang;

		MLA_Polylang::$mla_language_option_definitions = array (
			'media_assistant_table_header' =>
				array('tab' => 'language',
					'name' => __( 'Media/Assistant submenu table', 'media-library-assistant' ),
					'type' => 'header'),

			'language_column' =>
				array('tab' => 'language',
					'name' => __( 'Language Column', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to add a Language column to the Media/Assistant submenu table.', 'media-library-assistant' )),

			'translations_column' =>
				array('tab' => 'language',
					'name' => __( 'Translations Column', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to add a Translation Status column to the Media/Assistant submenu table.', 'media-library-assistant' )),

			'quick_translate' =>
				array('tab' => 'language',
					'name' => __( 'Quick Translate', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to add a Quick Translate rollover action to the Media/Assistant submenu table.', 'media-library-assistant' )),

			'bulk_translate' =>
				array('tab' => 'language',
					'name' => __( 'Bulk Translate', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to add "Translate" to the "Bulk Actions" control on the Media/Assistant submenu table.', 'media-library-assistant' )),

			'term_translation_header' =>
				array('tab' => 'language',
					'name' => __( 'Term Management', 'media-library-assistant' ),
					'type' => 'header'),

			'term_assignment' =>
				array('tab' => 'language',
					'name' => __( 'Term Assignment', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to assign language-specific terms when items are updated.'), 'media-library-assistant' ),

			'term_synchronization' =>
				array('tab' => 'language',
					'name' => __( 'Term Synchronization', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'Check this option to synchronize common terms among all item translations.'), 'media-library-assistant' ),

			'term_mapping_replication' =>
				array('tab' => 'language',
					'name' => __( 'Term Mapping Replication', 'media-library-assistant' ),
					'type' => 'checkbox',
					'std' => 'checked',
					'help' => __( 'When mapping IPTC/EXIF metadata to taxonomy terms, make them available in all languages.'), 'media-library-assistant' ),
		);
		
		/*
		 * Respect the Polylang Languages/Settings "Activate languages and translations for media" option.
		 */
		if ( isset( $polylang->options['media_support'] ) && ! $polylang->options['media_support'] ) {
			MLA_Polylang::$mla_language_option_definitions['term_assignment']['std'] = 'unchecked';
			MLA_Polylang::$mla_language_option_definitions['term_synchronization']['std'] = 'unchecked';
			MLA_Polylang::$mla_language_option_definitions['term_mapping_replication']['std'] = 'unchecked';
		}
	}

	/**
	 * Renders the Settings/Media Library Assistant "Language" tab
	 *
	 * @since 2.11
	 *
	 * @return	array	( 'message' => '', 'body' => '' )
	 */
	public static function mla_render_language_tab() {
		$page_content = array(
			'message' => '',
			'body' => '<h2>' . __( 'Language', 'media-library-assistant' ) . '</h2>' 
		);

		/*
		 * Check for submit buttons to change or reset settings.
		 * Initialize page messages and content.
		 */
		if ( !empty( $_REQUEST['mla-language-options-save'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = MLA_Polylang::_save_language_settings( );
		} elseif ( !empty( $_REQUEST['mla-language-options-reset'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = MLA_Polylang::_reset_language_settings( );
		} else {
			$page_content = array(
				'message' => '',
				'body' => '' 
			);
		}

		if ( !empty( $page_content['body'] ) ) {
			return $page_content;
		}

		$page_values = array(
			'Language Options' => __( 'Language Options', 'media-library-assistant' ),
			/* translators: 1: - 4: page subheader values */
			'In this tab' => sprintf( __( 'In this tab you can find a number of options for controlling Polylang-specific operations. Scroll down to find options for %1$s and %2$s. Be sure to click "Save Changes" at the bottom of the tab to save any changes you make.', 'media-library-assistant' ), '<strong>' . __( 'Media/Assistant submenu table', 'media-library-assistant' ) . '</strong>', '<strong>' . __( 'Term Management', 'media-library-assistant' ) . '</strong>' ),
			/* translators: 1: Documentation hyperlink */
			'You can find' => sprintf( __( 'You can find more information about multilingual features in the %1$s section of the Documentation.', 'media-library-assistant' ), '<a href="[+settingsURL+]?page=mla-settings-menu-documentation&amp;mla_tab=documentation#mla_language_tab" title="' . __( 'Language Options documentation', 'media-library-assistant' ) . '">' . __( 'WPML &amp; Polylang Multilingual Support; the MLA Language Tab', 'media-library-assistant' ) . '</a>' ),
			'WPML Status' => '',
			'settingsURL' => admin_url('options-general.php'),
			'Save Changes' => __( 'Save Changes', 'media-library-assistant' ),
			'Delete Language options' => __( 'Delete Language options and restore default settings', 'media-library-assistant' ),
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'_wp_http_referer' => wp_referer_field( false ),
			'Go to Top' => __( 'Go to Top', 'media-library-assistant' ),
			'form_url' => admin_url( 'options-general.php' ) . '?page=mla-settings-menu-language&mla_tab=language',
			'options_list' => '',
		);

		$options_list = '';
		foreach ( MLA_Polylang::$mla_language_option_definitions as $key => $value ) {
			if ( 'language' == $value['tab'] ) {
				$options_list .= MLASettings::mla_compose_option_row( $key, $value, MLA_Polylang::$mla_language_option_definitions );
			}
		}

		$page_values['options_list'] = $options_list;
		$page_template = MLACore::mla_load_template( 'admin-display-language-tab.tpl' );
		$page_content['body'] = MLAData::mla_parse_template( $page_template, $page_values );
		return $page_content;
	}

	/**
	 * Save Language settings to the options table
 	 *
	 * @since 2.11
	 *
	 * @uses $_REQUEST
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _save_language_settings( ) {
		$message_list = '';

		foreach ( MLA_Polylang::$mla_language_option_definitions as $key => $value ) {
			if ( 'language' == $value['tab'] ) {
				$message_list .= MLASettings::mla_update_option_row( $key, $value, MLA_Polylang::$mla_language_option_definitions );
			} // language option
		} // foreach mla_options

		$page_content = array(
			'message' => __( 'Language settings saved.', 'media-library-assistant' ) . "\n",
			'body' => '' 
		);

		/*
		 * Uncomment this for debugging.
		 */
		//$page_content['message'] .= $message_list;

		return $page_content;
	} // _save_language_settings

	/**
	 * Delete saved settings, restoring default values
 	 *
	 * @since 2.11
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _reset_language_settings( ) {
		$message_list = '';

		foreach ( MLA_Polylang::$mla_language_option_definitions as $key => $value ) {
			if ( 'language' == $value['tab'] ) {
				if ( 'custom' == $value['type'] && isset( $value['reset'] ) ) {
					$message = MLA_Polylang::$value['reset']( 'reset', $key, $value, $_REQUEST );
				} elseif ( ('header' == $value['type']) || ('hidden' == $value['type']) ) {
					$message = '';
				} else {
					MLACore::mla_delete_option( $key, MLA_Polylang::$mla_language_option_definitions );
					/* translators: 1: option name */
					$message = '<br>' . sprintf( _x( 'delete_option "%1$s"', 'message_list', 'media-library-assistant'), $key );
				}

				$message_list .= $message;
			}
		}

		$page_content = array(
			'message' => __( 'Language settings reset to default values.', 'media-library-assistant' ) . "\n",
			'body' => '' 
		);

		/*
		 * Uncomment this for debugging.
		 */
		//$page_content['message'] .= $message_list;

		return $page_content;
	} // _reset_language_settings
} // Class MLA_Polylang

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLA_Polylang::initialize');
?>