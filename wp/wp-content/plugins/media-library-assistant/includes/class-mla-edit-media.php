<?php
/**
 * Media Library Assistant Edit Media screen enhancements
 *
 * @package Media Library Assistant
 * @since 0.80
 */

/**
 * Class MLA (Media Library Assistant) Edit contains meta boxes for the Edit Media (advanced-form-edit.php) screen
 *
 * @package Media Library Assistant
 * @since 0.80
 */
class MLAEdit {
	/**
	 * Slug for localizing and enqueueing CSS - Add Media and related dialogs
	 *
	 * @since 1.20
	 *
	 * @var	string
	 */
	const JAVASCRIPT_EDIT_MEDIA_STYLES = 'mla-edit-media-style';

	/**
	 * Slug for localizing and enqueueing JavaScript - Add Media and related dialogs
	 *
	 * @since 1.20
	 *
	 * @var	string
	 */
	const JAVASCRIPT_EDIT_MEDIA_SLUG = 'mla-edit-media-scripts';

	/**
	 * Object name for localizing JavaScript - Add Media and related dialogs
	 *
	 * @since 1.20
	 *
	 * @var	string
	 */
	const JAVASCRIPT_EDIT_MEDIA_OBJECT = 'mla_edit_media_vars';

	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 0.80
	 *
	 * @return	void
	 */
	public static function initialize() {
		// do_action( 'admin_init' ) in wp-admin/admin.php
		add_action( 'admin_init', 'MLAEdit::mla_admin_init_action' );

		// do_action( 'admin_enqueue_scripts', $hook_suffix ) in wp-admin/admin-header.php
		add_action( 'admin_enqueue_scripts', 'MLAEdit::mla_admin_enqueue_scripts_action' );

		// do_action( 'add_meta_boxes', $post_type, $post ) in wp-admin/edit-form-advanced.php
		add_action( 'add_meta_boxes', 'MLAEdit::mla_add_meta_boxes_action', 10, 2 );

		// apply_filters( 'post_updated_messages', $messages ) in wp-admin/edit-form-advanced.php
		add_filter( 'post_updated_messages', 'MLAEdit::mla_post_updated_messages_filter', 10, 1 );

		// do_action in wp-admin/includes/meta-boxes.php function attachment_submit_meta_box
		add_action( 'attachment_submitbox_misc_actions', 'MLAEdit::mla_attachment_submitbox_action' );

		// do_action in wp-includes/post.php function wp_insert_post
		add_action( 'edit_attachment', 'MLAEdit::mla_edit_attachment_action', 10, 1 );

		// apply_filters( 'admin_title', $admin_title, $title ) in /wp-admin/admin-header.php
		add_filter( 'admin_title', 'MLAEdit::mla_edit_add_help_tab', 10, 2 );
	}

	/**
	 * Adds Custom Field support to the Edit Media screen.
	 * Declared public because it is an action.
	 *
	 * @since 0.80
	 *
	 * @return	void	echoes the HTML markup for the label and value
	 */
	public static function mla_admin_init_action( ) {
		$edit_media_support = array( 'custom-fields' );
		if ( ( 'checked' == MLACore::mla_get_option( 'enable_featured_image' ) ) && current_theme_supports( 'post-thumbnails', 'attachment' ) ) {
			$edit_media_support[] = 'thumbnail';
		}

		add_post_type_support( 'attachment', apply_filters( 'mla_edit_media_support', $edit_media_support ) );

		/*
		 * Check for Media/Add New bulk edit area updates
		 */
		if ( ! empty( $_REQUEST['mlaAddNewBulkEditFormString'] ) && ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ADD_NEW_BULK_EDIT ) ) ) {
			/*
			 * If any of the mapping rule options is enabled, use the MLA filter so this
			 * filter is called after mapping rules have run. If none are enabled,
			 * use the WordPress filter directly.
			 */
			if ( ( 'checked' == MLACore::mla_get_option( 'enable_iptc_exif_mapping' ) ) ||
				( 'checked' == MLACore::mla_get_option( 'enable_custom_field_mapping' ) ) ||
				( 'checked' == MLACore::mla_get_option( 'enable_iptc_exif_update' ) ) ||
				( 'checked' == MLACore::mla_get_option( 'enable_custom_field_update' ) ) ) {
				// Fires after MLA mapping in wp_update_attachment_metadata() processing.
				add_filter( 'mla_update_attachment_metadata_postfilter', 'MLAEdit::mla_update_attachment_metadata_postfilter', 10, 3 );
			} else {
				add_filter( 'wp_update_attachment_metadata', 'MLAEdit::mla_update_attachment_metadata_postfilter', 0x7FFFFFFF, 2 );
			}
		}

		/*
		 * If there's no action variable, we have nothing more to do
		 */
		if ( ! isset( $_POST['action'] ) ) {
			return;
		}

		/*
		 * For flat taxonomies that use the checklist meta box, convert the term array
		 * back into a string of slug values.
		 */
		if ( 'editpost' == $_POST['action']  ) {
			if ( isset( $_POST['tax_input'] ) && is_array( $_POST['tax_input'] ) ) {
				foreach( $_POST['tax_input'] as $key => $value ) {
					if ( is_array( $value ) ) {
						$tax = get_taxonomy( $key );
						if ( $tax->hierarchical ) {
							continue;
						}

						if ( false !== ( $bad_term = array_search( '0', $value ) ) ) { 
							unset( $value[ $bad_term ] );
						}

						$comma = _x( ',', 'tag_delimiter', 'media-library-assistant' );
						$_POST['tax_input'][ $key ] = implode( $comma, $value );
						$_REQUEST['tax_input'][ $key ] = implode( $comma, $value );
					} // array value
				} // foreach tax_input
			} // array tax_input
		} // action editpost
	}

	/**
	 * Load the plugin's Style Sheet and Javascript files
	 *
	 * @since 1.71
	 *
	 * @param	string	Name of the page being loaded
	 *
	 * @return	void
	 */
	public static function mla_admin_enqueue_scripts_action( $page_hook ) {
		global $wp_locale;

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		/*
		 * Add New Bulk Edit Area
		 */
		if ( 'media-new.php' == $page_hook && ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ADD_NEW_BULK_EDIT ) ) ) {
			if ( $wp_locale->is_rtl() ) {
				wp_register_style( 'mla-add-new-bulk-edit', MLA_PLUGIN_URL . 'css/mla-add-new-bulk-edit-rtl.css', false, MLA::CURRENT_MLA_VERSION );
				wp_register_style( 'mla-add-new-bulk-edit' . '-set-parent', MLA_PLUGIN_URL . 'css/mla-style-set-parent-rtl.css', false, MLA::CURRENT_MLA_VERSION );
			} else {
				wp_register_style( 'mla-add-new-bulk-edit', MLA_PLUGIN_URL . 'css/mla-add-new-bulk-edit.css', false, MLA::CURRENT_MLA_VERSION );
				wp_register_style( 'mla-add-new-bulk-edit' . '-set-parent', MLA_PLUGIN_URL . 'css/mla-style-set-parent.css', false, MLA::CURRENT_MLA_VERSION );
			}

			wp_enqueue_style( 'mla-add-new-bulk-edit' );
			wp_enqueue_style( 'mla-add-new-bulk-edit' . '-set-parent' );

			// 'suggest' loads the script for flat taxonomy auto-complete/suggested matches
			wp_enqueue_script( 'mla-add-new-bulk-edit-scripts', MLA_PLUGIN_URL . "js/mla-add-new-bulk-edit-scripts{$suffix}.js", 
				array( 'suggest', 'jquery' ), MLA::CURRENT_MLA_VERSION, false );

			wp_enqueue_script( 'mla-add-new-bulk-edit-scripts' . '-set-parent', MLA_PLUGIN_URL . "js/mla-set-parent-scripts{$suffix}.js", 
				array( 'mla-add-new-bulk-edit-scripts', 'jquery' ), MLA::CURRENT_MLA_VERSION, false );

			$script_variables = array(
				'uploadTitle' => __( 'Upload New Media items', 'media-library-assistant' ),
				'toggleOpen' => __( 'Open Bulk Edit area', 'media-library-assistant' ),
				'toggleClose' => __( 'Close Bulk Edit area', 'media-library-assistant' ),
				'areaOnTop' => ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ADD_NEW_BULK_EDIT_ON_TOP ) ),
				'areaOpen' => ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ADD_NEW_BULK_EDIT_AUTO_OPEN ) ),
				'comma' => _x( ',', 'tag_delimiter', 'media-library-assistant' ),
				'ajaxFailError' => __( 'An ajax.fail error has occurred. Please reload the page and try again.', 'media-library-assistant' ),
				'ajaxDoneError' => __( 'An ajax.done error has occurred. Please reload the page and try again.', 'media-library-assistant' ),
				'useDashicons' => false,
				'useSpinnerClass' => false,
			);

			if ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) {
				$script_variables['useDashicons'] = true;
			}

			if ( version_compare( get_bloginfo( 'version' ), '4.2', '>=' ) ) {
				$script_variables['useSpinnerClass'] = true;
			}

			wp_localize_script( 'mla-add-new-bulk-edit-scripts', 'mla_add_new_bulk_edit_vars', $script_variables );

			// Filter the media upload post parameters.
			// @param array $post_params An array of media upload parameters used by Plupload.
			add_filter( 'upload_post_params', 'MLAEdit::mla_upload_post_params', 10, 1 );

			// Fires on the post upload UI screen; legacy (pre-3.5.0) upload interface.
			add_action( 'post-upload-ui', 'MLAEdit::mla_post_upload_ui' );

			return;
		} // media-new.php

		if ( ( 'post.php' != $page_hook ) || ( ! isset( $_REQUEST['post'] ) ) || ( ! isset( $_REQUEST['action'] ) ) || ( 'edit' != $_REQUEST['action'] ) ) {
			return;
		}

		$post = get_post( $_REQUEST['post'] );
		if ( 'attachment' != $post->post_type ) {
			return;
		}

		/*
		 * Media/Edit Media submenu
		 * Register and queue the style sheet, if needed
		 */
		wp_register_style( self::JAVASCRIPT_EDIT_MEDIA_STYLES, MLA_PLUGIN_URL . 'css/mla-edit-media-style.css', false, MLA::CURRENT_MLA_VERSION );
		wp_enqueue_style( self::JAVASCRIPT_EDIT_MEDIA_STYLES );

		wp_register_style( self::JAVASCRIPT_EDIT_MEDIA_STYLES . '-set-parent', MLA_PLUGIN_URL . 'css/mla-style-set-parent.css', false, MLA::CURRENT_MLA_VERSION );
		wp_enqueue_style( self::JAVASCRIPT_EDIT_MEDIA_STYLES . '-set-parent' );

		wp_enqueue_script( self::JAVASCRIPT_EDIT_MEDIA_SLUG, MLA_PLUGIN_URL . "js/mla-edit-media-scripts{$suffix}.js", 
			array( 'post', 'wp-lists', 'suggest', 'jquery' ), MLA::CURRENT_MLA_VERSION, false );

		wp_enqueue_script( self::JAVASCRIPT_EDIT_MEDIA_SLUG . '-set-parent', MLA_PLUGIN_URL . "js/mla-set-parent-scripts{$suffix}.js", 
			array( 'post', 'wp-lists', 'suggest', 'jquery', self::JAVASCRIPT_EDIT_MEDIA_SLUG ), MLA::CURRENT_MLA_VERSION, false );

		$script_variables = array(
			'comma' => _x( ',', 'tag_delimiter', 'media-library-assistant' ),
			'Ajax_Url' => admin_url( 'admin-ajax.php' ),
			'ajaxFailError' => __( 'An ajax.fail error has occurred. Please reload the page and try again.', 'media-library-assistant' ),
			'ajaxDoneError' => __( 'An ajax.done error has occurred. Please reload the page and try again.', 'media-library-assistant' ),
			'useDashicons' => false,
			'useSpinnerClass' => false,
		);

		if ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) {
			$script_variables['useDashicons'] = true;
		}

		if ( version_compare( get_bloginfo( 'version' ), '4.2', '>=' ) ) {
			$script_variables['useSpinnerClass'] = true;
		}

		wp_localize_script( self::JAVASCRIPT_EDIT_MEDIA_SLUG, self::JAVASCRIPT_EDIT_MEDIA_OBJECT, $script_variables );
	}

	/**
	 * Filter the Media/Add New post parameters.
	 *
	 * @since 2.02
	 *
	 * @param	array	$post_parms An array of media upload parameters used by Plupload.
	 */
	public static function mla_upload_post_params( $post_parms ) {
		/*
		 * You can add elements to the array. It will end up in the client global
		 * variable: wpUploaderInit.multipart_params and is then copied to
		 * uploader.settings.multipart_params
		 *
		 * The elements of this array come back as $_REQUEST elements when the
		 * upload is submitted.
		 */
		//$post_parms['mlaAddNewBulkEdit'] = array ( 'formData' => array() );
		return $post_parms;
	}

	/**
	 * Echoes bulk edit area HTML to the Media/Add New screen
	 *
	 * Fires on the post upload UI screen; legacy (pre-3.5.0) upload interface.
	 * Anything echoed here goes below the "Maximum upload file size" message
	 * and above the id="media-items" div.
	 *
	 * @since 2.02
	 *
	 */
	public static function mla_post_upload_ui() {
		/*
		 * Only add our form to the Media/Add New screen. In particular,
		 * do NOT add it to the Media Manager Modal Window
		 */
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
		} else {
			$screen = NULL;
		}

		if ( is_object( $screen ) && ( 'add' != $screen->action || 'media' != $screen->base ) ) {
			return;
		}

		$taxonomies = get_object_taxonomies( 'attachment', 'objects' );

		$hierarchical_taxonomies = array();
		$flat_taxonomies = array();
		foreach ( $taxonomies as $tax_name => $tax_object ) {
			if ( $tax_object->hierarchical && $tax_object->show_ui && MLACore::mla_taxonomy_support($tax_name, 'quick-edit') ) {
				$hierarchical_taxonomies[$tax_name] = $tax_object;
			} elseif ( $tax_object->show_ui && MLACore::mla_taxonomy_support($tax_name, 'quick-edit') ) {
				$flat_taxonomies[$tax_name] = $tax_object;
			}
		}

		$page_template_array = MLACore::mla_load_template( 'mla-add-new-bulk-edit.tpl' );
		if ( ! is_array( $page_template_array ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			error_log( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLAEdit::mla_post_upload_ui', var_export( $page_template_array, true ) ), 0 );
			return;
		}

		/*
		 * The left-hand column contains the hierarchical taxonomies,
		 * e.g., Attachment Category
		 */
		$category_fieldset = '';

		if ( count( $hierarchical_taxonomies ) ) {
			$bulk_category_blocks = '';

			foreach ( $hierarchical_taxonomies as $tax_name => $tax_object ) {
				if ( current_user_can( $tax_object->cap->assign_terms ) ) {
				  ob_start();
				  wp_terms_checklist( NULL, array( 'taxonomy' => $tax_name ) );
				  $tax_checklist = ob_get_contents();
				  ob_end_clean();
  
				  $page_values = array(
					  'tax_html' => esc_html( $tax_object->labels->name ),
					  'more' => __( 'more', 'media-library-assistant' ),
					  'less' => __( 'less', 'media-library-assistant' ),
					  'tax_attr' => esc_attr( $tax_name ),
					  'tax_checklist' => $tax_checklist,
					  'Add' => __( 'Add', 'media-library-assistant' ),
					  'Remove' => __( 'Remove', 'media-library-assistant' ),
					  'Replace' => __( 'Replace', 'media-library-assistant' ),
				  );
				  $category_block = MLAData::mla_parse_template( $page_template_array['category_block'], $page_values );
				  $taxonomy_options = MLAData::mla_parse_template( $page_template_array['taxonomy_options'], $page_values );
  
				  $bulk_category_blocks .= $category_block . $taxonomy_options;
				} // current_user_can
			} // foreach $hierarchical_taxonomies

			$page_values = array(
				'category_blocks' => $bulk_category_blocks
			);
			$category_fieldset = MLAData::mla_parse_template( $page_template_array['category_fieldset'], $page_values );
		} // count( $hierarchical_taxonomies )

		/*
		 * The middle column contains the flat taxonomies,
		 * e.g., Attachment Tag
		 */
		$tag_fieldset = '';

		if ( count( $flat_taxonomies ) ) {
			$bulk_tag_blocks = '';

			foreach ( $flat_taxonomies as $tax_name => $tax_object ) {
				if ( current_user_can( $tax_object->cap->assign_terms ) ) {
					$page_values = array(
						'tax_html' => esc_html( $tax_object->labels->name ),
						'tax_attr' => esc_attr( $tax_name ),
						'Add' => __( 'Add', 'media-library-assistant' ),
						'Remove' => __( 'Remove', 'media-library-assistant' ),
						'Replace' => __( 'Replace', 'media-library-assistant' ),
					);
					$tag_block = MLAData::mla_parse_template( $page_template_array['tag_block'], $page_values );
					$taxonomy_options = MLAData::mla_parse_template( $page_template_array['taxonomy_options'], $page_values );

				$bulk_tag_blocks .= $tag_block . $taxonomy_options;
				} // current_user_can
			} // foreach $flat_taxonomies

			$page_values = array(
				'tag_blocks' => $bulk_tag_blocks
			);
			$tag_fieldset = MLAData::mla_parse_template( $page_template_array['tag_fieldset'], $page_values );
		} // count( $flat_taxonomies )

		/*
		 * The right-hand column contains the standard and custom fields
		 */
		if ( $authors = MLA::mla_authors_dropdown( -1 ) ) {
			$authors_dropdown  = '              <label class="inline-edit-author alignright">' . "\n";
			$authors_dropdown .= '                <span class="title">' . __( 'Author', 'media-library-assistant' ) . '</span>' . "\n";
			$authors_dropdown .= $authors . "\n";
			$authors_dropdown .= '              </label>' . "\n";
		} else {
			$authors_dropdown = '';
		}

		$custom_fields = '';
		foreach (MLACore::mla_custom_field_support( 'bulk_edit' ) as $slug => $details ) {
			  $page_values = array(
				  'slug' => $slug,
				  'label' => esc_attr( $details['name'] ),
			  );
			  $custom_fields .= MLAData::mla_parse_template( $page_template_array['custom_field'], $page_values );
		}

		$set_parent_form = MLA::mla_set_parent_form( false );

		$page_values = array(
			'NOTE' => __( 'IMPORTANT: Make your entries BEFORE uploading new items. Pull down the Help menu for more information.', 'media-library-assistant' ),
			'Toggle' => __( 'Open Bulk Edit area', 'media-library-assistant' ),
			'Reset' => __( 'Reset', 'media-library-assistant' ),
			'category_fieldset' => $category_fieldset,
			'tag_fieldset' => $tag_fieldset,
			'authors' => $authors_dropdown,
			'Comments' => __( 'Comments', 'media-library-assistant' ),
			'Pings' => __( 'Pings', 'media-library-assistant' ),
			'No Change' => __( 'No Change', 'media-library-assistant' ),
			'Allow' => __( 'Allow', 'media-library-assistant' ),
			'Do not allow' => __( 'Do not allow', 'media-library-assistant' ),
			'custom_fields' => $custom_fields,
			'Title' => __( 'Title', 'media-library-assistant' ),
			'Name/Slug' => __( 'Name/Slug', 'media-library-assistant' ),
			'Caption' => __( 'Caption', 'media-library-assistant' ),
			'Description' => __( 'Description', 'media-library-assistant' ),
			'ALT Text' => __( 'ALT Text', 'media-library-assistant' ),
			'Parent ID' => __( 'Parent ID', 'media-library-assistant' ),
			'Select' => __( 'Select', 'media-library-assistant' ),
			'set_parent_form' => $set_parent_form,
		);

		$page_values = apply_filters( 'mla_upload_bulk_edit_form_values', $page_values );
		$page_template = apply_filters( 'mla_upload_bulk_edit_form_template', $page_template_array['page'] );
		$parse_value = MLAData::mla_parse_template( $page_template, $page_values );
		echo apply_filters( 'mla_upload_bulk_edit_form_parse', $parse_value, $page_template, $page_values );
	}

	/**
	 * Apply Media/Add New bulk edit area updates, if any
	 *
	 * This filter is called AFTER MLA mapping rules are applied during
	 * wp_update_attachment_metadata() processing. If none of the mapping rules
	 * is enabled it is called from the 'wp_update_attachment_metadata' filter
	 * with just two arguments.
	 *
	 * @since 2.02
	 *
	 * @param	array	attachment metadata
	 * @param	integer	The Post ID of the new/updated attachment
	 * @param	array	Processing options, e.g., 'is_upload'
	 *
	 * @return	array	updated attachment metadata
	 */
	public static function mla_update_attachment_metadata_postfilter( $data, $post_id, $options = array( 'is_upload' => true ) ) {
		if ( ( true == $options['is_upload'] ) && ! empty( $_REQUEST['mlaAddNewBulkEditFormString'] ) ) {
			/*
			 * Clean up the inputs, which have everything from the enclosing <form>
			 */
			$args = wp_parse_args( stripslashes( urldecode( $_REQUEST['mlaAddNewBulkEditFormString'] ) ) );

			unset( $args['parent'] );
			unset( $args['children'] );
			unset( $args['mla-set-parent-ajax-nonce'] );
			unset( $args['mla_set_parent_search_text'] );
			unset( $args['mla_set_parent_post_type'] );
			unset( $args['mla_set_parent_count'] );
			unset( $args['mla_set_parent_paged'] );
			unset( $args['mla_set_parent_found'] );
			unset( $args['post_id'] );
			unset( $args['_wpnonce'] );
			unset( $args['_wp_http_referer'] );

			/*
			 * The category taxonomy (edit screens) is a special case because 
			 * post_categories_meta_box() changes the input name
			 */
			if ( !isset( $args['tax_input'] ) ) {
				$args['tax_input'] = array();
			}

			if ( isset( $args['post_category'] ) ) {
				$args['tax_input']['category'] = $args['post_category'];
				unset ( $args['post_category'] );
			}

			/*
			 * Pass the ID
			 */
			$args['cb_attachment'] = array( $post_id );
			$item_content = MLA::mla_process_bulk_action( 'edit', $args );
		}

		return $data;
	} // mla_update_attachment_metadata_postfilter

	/**
	 * Adds mapping update messages for display at the top of the Edit Media screen.
	 * Declared public because it is a filter.
	 *
	 * @since 1.10
	 *
	 * @param	array	messages for the Edit screen
	 *
	 * @return	array	updated messages
	 */
	public static function mla_post_updated_messages_filter( $messages ) {
	if ( isset( $messages['attachment'] ) ) {
		$messages['attachment'][101] = __( 'Custom field mapping updated.', 'media-library-assistant' );
		$messages['attachment'][102] = __('IPTC/EXIF mapping updated.', 'media-library-assistant' );
	}

	return $messages;
	} // mla_post_updated_messages_filter

	/**
	 * Adds Last Modified date to the Submit box on the Edit Media screen.
	 * Declared public because it is an action.
	 *
	 * @since 0.80
	 *
	 * @return	void	echoes the HTML markup for the label and value
	 */
	public static function mla_attachment_submitbox_action( ) {
		global $post;

		/* translators: date_i18n format for last modified date and time */
		$date = date_i18n( __( 'M j, Y @ G:i', 'media-library-assistant' ), strtotime( $post->post_modified ) );
		echo '<div class="misc-pub-section curtime">' . "\n";
		echo '<span id="timestamp">' . sprintf(__( 'Last modified', 'media-library-assistant' ) . ": <b>%1\$s</b></span>\n", $date);
		echo "</div><!-- .misc-pub-section -->\n";
		echo '<div class="misc-pub-section mla-links">' . "\n";

		$view_args = array( 'page' => MLACore::ADMIN_PAGE_SLUG, 'mla_item_ID' => $post->ID );
		if ( isset( $_REQUEST['mla_source'] ) ) {
			$view_args['mla_source'] = $_REQUEST['mla_source'];
		
			// apply_filters( 'get_delete_post_link', wp_nonce_url( $delete_link, "$action-post_{$post->ID}" ), $post->ID, $force_delete ) in /wp-includes/link-template.php
			add_filter( 'get_delete_post_link', 'MLAEdit::get_delete_post_link_filter', 10, 3 );
		}
		if ( isset( $_REQUEST['lang'] ) ) {
			$view_args['lang'] = $_REQUEST['lang'];
		}

		echo '<span id="mla_metadata_links" style="font-weight: bold; line-height: 2em">';

		if ( isset( $_REQUEST['mla_source'] ) ) {
			echo '<input name="mla_source" type="hidden" id="mla_source" value="' . $_REQUEST['mla_source'] . '" />';
		}

		echo '<a href="' . add_query_arg( $view_args, wp_nonce_url( 'upload.php?mla_admin_action=' . MLA::MLA_ADMIN_SINGLE_CUSTOM_FIELD_MAP, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Map Custom Field metadata for this item', 'media-library-assistant' ) . '">' . __( 'Map Custom Field metadata', 'media-library-assistant' ) . '</a><br>';

		echo '<a href="' . add_query_arg( $view_args, wp_nonce_url( 'upload.php?mla_admin_action=' . MLA::MLA_ADMIN_SINGLE_MAP, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="' . __( 'Map IPTC/EXIF metadata for this item', 'media-library-assistant' ) . '">' . __( 'Map IPTC/EXIF metadata', 'media-library-assistant' ) . '</a>';

		echo "</span>\n";
		echo "</div><!-- .misc-pub-section -->\n";
	} // mla_attachment_submitbox_action

	/**
	 * Adds mla_source argument to Trash/Delete link.
	 * Declared public because it is a filter.
	 *
	 * @since 2.25
	 *
	 * @param string $link         The delete link.
	 * @param int    $post_id      Post ID.
	 * @param bool   $force_delete Whether to bypass the trash and force deletion. Default false.
	 */
	public static function get_delete_post_link_filter( $link, $post_id, $force_delete ) {
		/*
		 * Add mla_source to force return to the Media/Assistant submenu
		 */
		if ( $force_delete ) {
			$link = add_query_arg( 'mla_source', 'delete', $link );
		} else {
			$link = add_query_arg( 'mla_source', 'trash', $link );
		}

		return $link;
	} // get_delete_post_link_filter

	/**
	 * Registers meta boxes for the Edit Media screen.
	 * Declared public because it is an action.
	 *
	 * @since 0.80
	 *
	 * @param	string	type of the current post, e.g., 'attachment' (optional, default 'unknown') 
	 * @param	object	current post (optional, default (object) array ( 'ID' => 0 ))
	 *
	 * @return	void
	 */
	public static function mla_add_meta_boxes_action( $post_type = 'unknown', $post = NULL ) {
		/*
		 * Plugins call this action with varying numbers of arguments!
		 */
		if ( NULL == $post ) {
			$post = (object) array ( 'ID' => 0 );
		}

		if ( 'attachment' != $post_type ) {
			return;
		}

		/*
		 * Use the mla_checklist_meta_box callback function for MLA supported taxonomies
		 */
		global $wp_meta_boxes;
		$screen = convert_to_screen( 'attachment' );
		$page = $screen->id;

		if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_EDIT_MEDIA_SEARCH_TAXONOMY ) ) {
			$taxonomies = get_taxonomies( array ( 'show_ui' => true ), 'objects' );
			foreach ( $taxonomies as $key => $value ) {
				if ( MLACore::mla_taxonomy_support( $key ) ) {
					if ( $value->hierarchical ) {
						foreach ( array_keys( $wp_meta_boxes[$page] ) as $a_context ) {
							foreach ( array('high', 'sorted', 'core', 'default', 'low') as $a_priority ) {
								if ( isset( $wp_meta_boxes[$page][$a_context][$a_priority][ $key . 'div' ] ) ) {
									$box = &$wp_meta_boxes[$page][$a_context][$a_priority][ $key . 'div' ];
									if ( 'post_categories_meta_box' == $box['callback'] ) {
										$box['callback'] = 'MLACore::mla_checklist_meta_box';
									}
								} // isset $box
							} // foreach priority
						} // foreach context
					} /* hierarchical */ elseif ( MLACore::mla_taxonomy_support( $key, 'flat-checklist' ) ) {
						foreach ( array_keys( $wp_meta_boxes[$page] ) as $a_context ) {
							foreach ( array('high', 'sorted', 'core', 'default', 'low') as $a_priority ) {
								if ( isset( $wp_meta_boxes[$page][$a_context][$a_priority][ 'tagsdiv-' . $key ] ) ) {
									$box = &$wp_meta_boxes[$page][$a_context][$a_priority][ 'tagsdiv-' . $key ];
									if ( 'post_tags_meta_box' == $box['callback'] ) {
										$box['callback'] = 'MLACore::mla_checklist_meta_box';
									}
								} // isset $box
							} // foreach priority
						} // foreach context
					} // flat checklist
				} // is supported
			} // foreach
		} // MLA_EDIT_MEDIA_SEARCH_TAXONOMY

		if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_EDIT_MEDIA_META_BOXES ) ) {
			$active_boxes = apply_filters( 'mla_edit_media_meta_boxes', array( 
			'mla-parent-info' => 'mla-parent-info', 'mla-menu-order' => 'mla-menu-order', 'mla-image-metadata' => 'mla-image-metadata', 'mla-featured-in' => 'mla-featured-in', 'mla-inserted-in' => 'mla-inserted-in', 'mla-gallery-in' => 'mla-gallery-in', 'mla-mla-gallery-in' => 'mla-mla-gallery-in' ) );

			if ( isset( $active_boxes['mla-parent-info'] ) ) {
				add_meta_box( 'mla-parent-info', __( 'Parent Info', 'media-library-assistant' ), 'MLAEdit::mla_parent_info_handler', 'attachment', 'normal', 'core' );
			}

			if ( isset( $active_boxes['mla-menu-order'] ) ) {
				add_meta_box( 'mla-menu-order', __( 'Menu Order', 'media-library-assistant' ), 'MLAEdit::mla_menu_order_handler', 'attachment', 'normal', 'core' );
			}

			if ( isset( $active_boxes['mla-image-metadata'] ) ) {
				$image_metadata = get_metadata( 'post', $post->ID, '_wp_attachment_metadata', true );
				if ( !empty( $image_metadata ) ) {
					add_meta_box( 'mla-image-metadata', __( 'Attachment Metadata', 'media-library-assistant' ), 'MLAEdit::mla_image_metadata_handler', 'attachment', 'normal', 'core' );
				}
			}

			if ( isset( $active_boxes['mla-featured-in'] ) && MLACore::$process_featured_in ) {
				add_meta_box( 'mla-featured-in', __( 'Featured in', 'media-library-assistant' ), 'MLAEdit::mla_featured_in_handler', 'attachment', 'normal', 'core' );
			}

			if ( isset( $active_boxes['mla-inserted-in'] ) && MLACore::$process_inserted_in ) {
				add_meta_box( 'mla-inserted-in', __( 'Inserted in', 'media-library-assistant' ), 'MLAEdit::mla_inserted_in_handler', 'attachment', 'normal', 'core' );
			}

			if ( isset( $active_boxes['mla-gallery-in'] ) && MLACore::$process_gallery_in ) {
				add_meta_box( 'mla-gallery-in', __( 'Gallery in', 'media-library-assistant' ), 'MLAEdit::mla_gallery_in_handler', 'attachment', 'normal', 'core' );
			}

			if ( isset( $active_boxes['mla-mla-gallery-in'] ) && MLACore::$process_mla_gallery_in ) {
				add_meta_box( 'mla-mla-gallery-in', __( 'MLA Gallery in', 'media-library-assistant' ), 'MLAEdit::mla_mla_gallery_in_handler', 'attachment', 'normal', 'core' );
			}
		}
	} // mla_add_meta_boxes_action

	/**
	 * Add contextual help tabs to the WordPress Edit Media page
	 *
	 * @since 0.90
	 *
	 * @param	string	title as shown on the screen
	 * @param	string	title as shown in the HTML header
	 *
	 * @return	void
	 */
	public static function mla_edit_add_help_tab( $admin_title, $title ) {
		$screen = get_current_screen();

		/*
		 * Upload New Media Bulk Edit Area
		 */
		if ( ( 'media' == $screen->id ) && ( 'add' == $screen->action ) ) {
			$template_array = MLACore::mla_load_template( 'help-for-upload-new-media.tpl' );
			if ( empty( $template_array ) ) {
				return $admin_title;
			}

			/*
			 * Replace sidebar content
			 */
			if ( !empty( $template_array['sidebar'] ) ) {
				$page_values = array( 'settingsURL' => admin_url('options-general.php') );
				$content = MLAData::mla_parse_template( $template_array['sidebar'], $page_values );
				$screen->set_help_sidebar( $content );
			}
			unset( $template_array['sidebar'] );

			/*
			 * Provide explicit control over tab order
			 */
			$tab_array = array();

			foreach ( $template_array as $id => $content ) {
				$match_count = preg_match( '#\<!-- title="(.+)" order="(.+)" --\>#', $content, $matches, PREG_OFFSET_CAPTURE );

				if ( $match_count > 0 ) {
					$tab_array[ $matches[ 2 ][ 0 ] ] = array(
						 'id' => $id,
						'title' => $matches[ 1 ][ 0 ],
						'content' => $content 
					);
				} else {
					/* translators: 1: ERROR tag 2: function name 3: template key */
					error_log( sprintf( _x( '%1$s: %2$s discarding "%3$s"; no title/order', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'mla_edit_add_help_tab', $id ), 0 );
				}
			}

			ksort( $tab_array, SORT_NUMERIC );
			foreach ( $tab_array as $indx => $value ) {
				$screen->add_help_tab( $value );
			}

			return $admin_title;
		}

		/*
		 * Media/Edit Media submenu
		 */
		if ( ( 'attachment' != $screen->id ) || ( 'attachment' != $screen->post_type ) || ( 'post' != $screen->base ) ) {
			return $admin_title;
		}

		$template_array = MLACore::mla_load_template( 'help-for-edit_attachment.tpl' );
		if ( empty( $template_array ) ) {
			return $admin_title;
		}

		/*
		 * Provide explicit control over tab order
		 */
		$tab_array = array();

		foreach ( $template_array as $id => $content ) {
			$match_count = preg_match( '#\<!-- title="(.+)" order="(.+)" --\>#', $content, $matches, PREG_OFFSET_CAPTURE );

			if ( $match_count > 0 ) {
				$tab_array[ $matches[ 2 ][ 0 ] ] = array(
					 'id' => $id,
					'title' => $matches[ 1 ][ 0 ],
					'content' => $content 
				);
			} else {
				/* translators: 1: ERROR tag 2: function name 3: template key */
				error_log( sprintf( _x( '%1$s: %2$s discarding "%3$s"; no title/order', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'mla_edit_add_help_tab', $id ), 0 );
			}
		}

		ksort( $tab_array, SORT_NUMERIC );
		foreach ( $tab_array as $indx => $value ) {
			$screen->add_help_tab( $value );
		}

	return $admin_title;
	}

	/**
	 * Where-used values for the current item
	 *
	 * This array contains the Featured/Inserted/Gallery/MLA Gallery references for the item.
	 * The array is built once each page load and cached for subsequent calls.
	 *
	 * @since 0.80
	 *
	 * @var	array
	 */
	private static $mla_references = NULL;

	/**
	 * Renders the Parent Info meta box on the Edit Media page.
	 * Declared public because it is a callback function.
	 *
	 * @since 0.80
	 *
	 * @param	object	current post
	 *
	 * @return	void	echoes the HTML markup for the meta box content
	 */
	public static function mla_parent_info_handler( $post ) {
		if ( is_null( self::$mla_references ) ) {
			self::$mla_references = MLAQuery::mla_fetch_attachment_references( $post->ID, $post->post_parent );
		}

		if ( is_array( self::$mla_references ) ) {
			if ( empty(self::$mla_references['parent_title'] ) ) {
				$parent_info = self::$mla_references['parent_errors'];
			} else {
				$flag = ', ';
				switch ( self::$mla_references['parent_status'] ) {
					case 'future' :
						$flag .= __('Scheduled');
						break;
					case 'pending' :
						$flag .= _x('Pending', 'post state');
						break;
					case 'draft' :
						$flag .= __('Draft');
						break;
					default:
						$flag = '';
				}

				$parent_info = sprintf( '%1$s (%2$s%3$s) %4$s', self::$mla_references['parent_title'], self::$mla_references['parent_type'], $flag, self::$mla_references['parent_errors'] );
			}
		} else {
			$parent_info = '';
		}

		$parent_info = apply_filters( 'mla_parent_info_meta_box', $parent_info, self::$mla_references, $post );

		echo '<label class="screen-reader-text" for="mla_post_parent">' . __( 'Post Parent', 'media-library-assistant' ) . '</label><input name="mla_post_parent" id="mla_post_parent" type="text" value="' . $post->post_parent . "\" />\n";
		echo '<label class="screen-reader-text" for="mla_parent_info">' . __( 'Select Parent', 'media-library-assistant' ) . '</label><input name="post_parent_set" id="mla_set_parent" class="button-primary parent" type="button" value="' . __( 'Select', 'media-library-assistant' ) . '" />';
		echo '<label class="screen-reader-text" for="mla_parent_info">' . __( 'Parent Info', 'media-library-assistant' ) . '</label><input name="mla_parent_info" id="mla_parent_info" type="text" readonly="readonly" disabled="disabled" value="' . esc_attr( $parent_info ) . "\" /></span>\n";

		echo MLA::mla_set_parent_form( false );
	}

	/**
	 * Renders the Menu Order meta box on the Edit Media page.
	 * Declared public because it is a callback function.
	 *
	 * @since 0.80
	 *
	 * @param	object	current post
	 *
	 * @return	void	echoes the HTML markup for the meta box content
	 */
	public static function mla_menu_order_handler( $post ) {

		$menu_order = apply_filters( 'mla_menu_order_meta_box', $post->menu_order, $post );

		echo '<label class="screen-reader-text" for="mla_menu_order">' . __( 'Menu Order', 'media-library-assistant' ) . '</label><input name="mla_menu_order" type="text" size="4" id="mla_menu_order" value="' . esc_attr( $menu_order ) . "\" />\n";
	}

	/**
	 * Renders the Image Metadata meta box on the Edit Media page.
	 * Declared public because it is a callback function.
	 *
	 * @since 0.80
	 *
	 * @param	object	current post
	 *
	 * @return	void	echoes the HTML markup for the meta box content
	 */
	public static function mla_image_metadata_handler( $post ) {
		$metadata = MLAQuery::mla_fetch_attachment_metadata( $post->ID );

		if ( isset( $metadata['mla_wp_attachment_metadata'] ) ) {
			$value = var_export( $metadata['mla_wp_attachment_metadata'], true );
		} else {
			$value = '';
		}

		$value = apply_filters( 'mla_image_metadata_meta_box', array( 'value' => $value, 'rows' => 5, 'cols' => 80 ), $metadata, $post );

		$html =  '<label class="screen-reader-text" for="mla_image_metadata">' . __( 'Attachment Metadata', 'media-library-assistant' ) . '</label><textarea class="readonly" id="mla_image_metadata" rows="' . absint( $value['rows'] ) . '" cols="' . absint( $value['cols'] ) . '" readonly="readonly" name="mla_image_metadata" >' . esc_textarea( $value['value'] ) . "</textarea>\n";
		echo apply_filters( 'mla_image_metadata_meta_box_html', $html, $value, $metadata, $post );
	}

	/**
	 * Renders the Featured in meta box on the Edit Media page.
	 * Declared public because it is a callback function.
	 *
	 * @since 0.80
	 *
	 * @param	object	current post
	 *
	 * @return	void	echoes the HTML markup for the meta box content
	 */
	public static function mla_featured_in_handler( $post ) {
		if ( is_null( self::$mla_references ) ) {
			self::$mla_references = MLAQuery::mla_fetch_attachment_references( $post->ID, $post->post_parent );
		}

		$features = '';
		if ( is_array( self::$mla_references ) ) {
			foreach ( self::$mla_references['features'] as $feature_id => $feature ) {
				if ( $feature_id == $post->post_parent ) {
					$parent = __( 'PARENT', 'media-library-assistant' ) . ' ';
				} else {
					$parent = '';
				}

				$features .= sprintf( '%1$s (%2$s %3$s), %4$s', /*$1%s*/ $parent, /*$2%s*/ $feature->post_type, /*$3%s*/ $feature_id, /*$4%s*/ $feature->post_title ) . "\n";
			} // foreach $feature
		}

		$features = apply_filters( 'mla_featured_in_meta_box', array( 'features' => $features, 'rows' => 5, 'cols' => 80 ), self::$mla_references, $post );

		$html = '<label class="screen-reader-text" for="mla_featured_in">' . __( 'Featured in', 'media-library-assistant' ) . '</label><textarea class="readonly" id="mla_featured_in" rows="' . absint( $features['rows'] ) . '" cols="' . absint( $features['cols'] ) . '" readonly="readonly" name="mla_featured_in" >' . esc_textarea( $features['features'] ) . "</textarea>\n";

		echo apply_filters( 'mla_featured_in_meta_box_html', $html, $features, self::$mla_references, $post );
	}

	/**
	 * Renders the Inserted in meta box on the Edit Media page.
	 * Declared public because it is a callback function.
	 *
	 * @since 0.80
	 *
	 * @param	object	current post
	 *
	 * @return	void	echoes the HTML markup for the meta box content
	 */
	public static function mla_inserted_in_handler( $post ) {
		if ( is_null( self::$mla_references ) ) {
			self::$mla_references = MLAQuery::mla_fetch_attachment_references( $post->ID, $post->post_parent );
		}

		$inserts = '';
		if ( is_array( self::$mla_references ) ) {
			foreach ( self::$mla_references['inserts'] as $file => $insert_array ) {
				$inserts .= $file . "\n";

				foreach ( $insert_array as $insert ) {
					if ( $insert->ID == $post->post_parent ) {
						$parent = '  ' . __( 'PARENT', 'media-library-assistant' ) . ' ';
					} else {
						$parent = '  ';
					}

					$inserts .= sprintf( '%1$s (%2$s %3$s), %4$s', /*$1%s*/ $parent, /*$2%s*/ $insert->post_type, /*$3%s*/ $insert->ID, /*$4%s*/ $insert->post_title ) . "\n";
				} // foreach $insert
			} // foreach $file
		} // is_array

		$inserts = apply_filters( 'mla_inserted_in_meta_box', array( 'inserts' => $inserts, 'rows' => 5, 'cols' => 80 ), self::$mla_references, $post );

		$html = '<label class="screen-reader-text" for="mla_inserted_in">' . __( 'Inserted in', 'media-library-assistant' ) . '</label><textarea class="readonly" id="mla_inserted_in" rows="' . absint( $inserts['rows'] ) . '" cols="' . absint( $inserts['cols'] ) . '" readonly="readonly" name="mla_inserted_in" >' . esc_textarea( $inserts['inserts'] ) . "</textarea>\n";

		echo apply_filters( 'mla_inserted_in_meta_box_html', $html, $inserts, self::$mla_references, $post );
	}

	/**
	 * Renders the Gallery in meta box on the Edit Media page.
	 * Declared public because it is a callback function.
	 *
	 * @since 0.80
	 *
	 * @param	object	current post
	 *
	 * @return	void	echoes the HTML markup for the meta box content
	 */
	public static function mla_gallery_in_handler( $post ) {
		if ( is_null( self::$mla_references ) ) {
			self::$mla_references = MLAQuery::mla_fetch_attachment_references( $post->ID, $post->post_parent );
		}

		$galleries = '';
		if ( is_array( self::$mla_references ) ) {
			foreach ( self::$mla_references['galleries'] as $gallery_id => $gallery ) {
				if ( $gallery_id == $post->post_parent ) {
					$parent = __( 'PARENT', 'media-library-assistant' ) . ' ';
				} else {
					$parent = '';
				}

				$galleries .= sprintf( '%1$s (%2$s %3$s), %4$s', /*$1%s*/ $parent, /*$2%s*/ $gallery['post_type'], /*$3%s*/ $gallery_id, /*$4%s*/ $gallery['post_title'] ) . "\n";
			} // foreach $feature
		}

		$galleries = apply_filters( 'mla_gallery_in_meta_box', array( 'galleries' => $galleries, 'rows' => 5, 'cols' => 80 ), self::$mla_references, $post );

		$html = '<label class="screen-reader-text" for="mla_gallery_in">' . __( 'Gallery in', 'media-library-assistant' ) . '</label><textarea class="readonly" id="mla_gallery_in" rows="' . absint( $galleries['rows'] ) . '" cols="' . absint( $galleries['cols'] ) . '" readonly="readonly" name="mla_gallery_in" >' . esc_textarea( $galleries['galleries'] ) . "</textarea>\n";

		echo apply_filters( 'mla_gallery_in_meta_box_html', $html, $galleries, self::$mla_references, $post );
	}

	/**
	 * Renders the MLA Gallery in meta box on the Edit Media page.
	 * Declared public because it is a callback function.
	 *
	 * @since 0.80
	 *
	 * @param	object	current post
	 *
	 * @return	void	echoes the HTML markup for the meta box content
	 */
	public static function mla_mla_gallery_in_handler( $post ) {
		if ( is_null( self::$mla_references ) ) {
			self::$mla_references = MLAQuery::mla_fetch_attachment_references( $post->ID, $post->post_parent );
		}

		$galleries = '';
		if ( is_array( self::$mla_references ) ) {
			foreach ( self::$mla_references['mla_galleries'] as $gallery_id => $gallery ) {
				if ( $gallery_id == $post->post_parent ) {
					$parent = __( 'PARENT', 'media-library-assistant' ) . ' ';
				} else {
					$parent = '';
				}

				$galleries .= sprintf( '%1$s (%2$s %3$s), %4$s', /*$1%s*/ $parent, /*$2%s*/ $gallery['post_type'], /*$3%s*/ $gallery_id, /*$4%s*/ $gallery['post_title'] ) . "\n";
			} // foreach $feature
		}

		$galleries = apply_filters( 'mla_mla_gallery_in_meta_box', array( 'galleries' => $galleries, 'rows' => 5, 'cols' => 80 ), self::$mla_references, $post );

		$html = '<label class="screen-reader-text" for="mla_mla_gallery_in">' . __( 'MLA Gallery in', 'media-library-assistant' ) . '</label><textarea class="readonly" id="mla_mla_gallery_in" rows="' . absint( $galleries['rows'] ) . '" cols="' . absint( $galleries['cols'] ) . '" readonly="readonly" name="mla_mla_gallery_in" >' . esc_textarea( $galleries['galleries'] ) . "</textarea>\n";

		echo apply_filters( 'mla_mla_gallery_in_meta_box_html', $html, $galleries, self::$mla_references, $post );
	}

	/**
	 * Saves updates from the Edit Media screen.
	 * Declared public because it is an action.
	 *
	 * @since 0.80
	 *
	 * @param	integer	ID of the current post
	 *
	 * @return	void
	 */
	public static function mla_edit_attachment_action( $post_ID ) {
		$new_data = array();
		if ( isset( $_REQUEST['mla_post_parent'] ) ) {
			$new_data['post_parent'] = $_REQUEST['mla_post_parent'];
		}

		if ( isset( $_REQUEST['mla_menu_order'] ) ) {
			$new_data['menu_order'] = $_REQUEST['mla_menu_order'];
		}

		if ( !empty( $new_data ) ) {
			MLAData::mla_update_single_item( $post_ID, $new_data );
		}
	} // mla_edit_attachment_action
} //Class MLAEdit

?>