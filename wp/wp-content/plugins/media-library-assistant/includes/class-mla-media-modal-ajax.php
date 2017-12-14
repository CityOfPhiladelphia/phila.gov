<?php
/**
 * Media Library Assistant  Ajax Handlers for Media Manager enhancements
 *
 * @package Media Library Assistant
 * @since 2.20
 */
 
/**
 * Class MLA (Media Library Assistant) Modal Ajax contains handlers for the WordPress 3.5+ Media Manager
 *
 * @package Media Library Assistant
 * @since 2.20
 */
class MLAModal_Ajax {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 2.20
	 *
	 * @return	void
	 */
	public static function initialize() {
		add_action( 'admin_init', 'MLAModal_Ajax::mla_admin_init_action' );

		add_action( 'wp_ajax_' . MLACore::JAVASCRIPT_QUERY_ATTACHMENTS_ACTION, 'MLAModal_Ajax::mla_query_attachments_action' );
		add_action( 'wp_ajax_' . MLACore::JAVASCRIPT_FILL_COMPAT_ACTION, 'MLAModal_Ajax::mla_fill_compat_fields_action' );
		add_action( 'wp_ajax_' . MLACore::JAVASCRIPT_UPDATE_COMPAT_ACTION, 'MLAModal_Ajax::mla_update_compat_fields_action' );
		
		/*
		 * For each media item found by "query_attachments", these filters are called:
		 *
		 * In /wp-admin/includes/media.php, functions get_media_item() and get_compat_media_markup()
		 * contain "apply_filters( 'get_media_item_args', $args );", documented as:
		 * "Filter the arguments used to retrieve an image for the edit image form."
		 *
		 * In /wp-admin/includes/media.php, functions get_attachment_fields_to_edit()
		 * and get_compat_media_markup() contain
		 * "$form_fields = apply_filters( 'attachment_fields_to_edit', $form_fields, $post );",
		 * documented as: "Filter the attachment fields to edit."
		 */
		add_filter( 'get_media_item_args', 'MLAModal_Ajax::mla_get_media_item_args_filter', 10, 1 );
		add_filter( 'attachment_fields_to_edit', 'MLAModal_Ajax::mla_attachment_fields_to_edit_filter', 0x7FFFFFFF, 2 );
	}

	/**
	 * Adjust ajax handler for Media Manager queries 
	 *
	 * Replace 'query-attachments' with our own handler if the request is coming from the "Assistant" tab.
	 * Clean up the 'save-attachment-compat' values, removing the taxonomy updates MLS already handled.
	 *
	 * @since 2.20
	 *
	 * @return	void	
	 */
	public static function mla_admin_init_action() {
//error_log( __LINE__ . ' DEBUG: class MLAModal_Ajax::mla_admin_init_action $_POST = ' . var_export( $_POST, true ), 0 );
//cause_an_error();
//$cause_notice = $screen->bad_property;
//trigger_error( 'mla_print_media_templates_action', E_USER_WARNING );
//error_log( 'DEBUG: xdebug_get_function_stack = ' . var_export( xdebug_get_function_stack(), true), 0 );		
		/*
		 * If there's no action variable, we have nothing to do
		 */
		if ( ! isset( $_POST['action'] ) ) {
			return;
		}

		/*
		 * Build a list of enhanced taxonomies for later $_REQUEST/$_POST cleansing.
		 * Remove "Media Categories" instances, if present.
		 */
		$enhanced_taxonomies = array();
		foreach ( get_taxonomies( array ( 'show_ui' => true ), 'objects' ) as $key => $value ) {
			if ( MLACore::mla_taxonomy_support( $key ) ) {
				if ( ! $use_checklist = $value->hierarchical ) {
					$use_checklist = MLACore::mla_taxonomy_support( $key, 'flat-checklist' );
				}

				if ( $use_checklist ) {
					if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_DETAILS_CATEGORY_METABOX ) ) {
						$enhanced_taxonomies[] = $key;

						if ( class_exists( 'Media_Categories' ) && is_array( Media_Categories::$instances ) ) {
							foreach( Media_Categories::$instances as $index => $instance ) {
								if ( $instance->taxonomy == $key ) {
									// unset( Media_Categories::$instances[ $index ] );
									Media_Categories::$instances[ $index ]->taxonomy = 'MLA-has-disabled-this-instance';
								}
							}
						} // class_exists
					} // checked
				} // use_checklist
			} // supported
		} // foreach taxonomy 

		/*
		 * The 'query-attachments' action fills the Modal Window thumbnail pane with media items.
		 * If the 's' value is an array, the MLA Enhanced elements are present; unpack the arguments
		 * and substitute our handler for the WordPress default handler.
		 */
		if ( ( $_POST['action'] == 'query-attachments' ) && isset( $_POST['query']['s'] ) && is_array( $_POST['query']['s'] ) ){
			foreach ( $_POST['query']['s'] as $key => $value ) {
				$_POST['query'][ $key ] = $value;
				$_REQUEST['query'][ $key ] = $value;
			}

			unset( $_POST['query']['s'] );
			unset( $_REQUEST['query']['s'] );
			$_POST['action'] = MLACore::JAVASCRIPT_QUERY_ATTACHMENTS_ACTION;
			$_REQUEST['action'] = MLACore::JAVASCRIPT_QUERY_ATTACHMENTS_ACTION;
			return;
		} // query-attachments

		/*
		 * The 'save-attachment-compat' action updates taxonomy and custom field
		 * values for an item. Remove any MLA-enhanced taxonomy data from the
		 * incoming data. The other taxonomies will be processed by
		 * /wp-admin/includes/ajax-actions.php, function wp_ajax_save_attachment_compat().
		 */
		if ( ( $_POST['action'] == 'save-attachment-compat' ) ){
			if ( empty( $_REQUEST['id'] ) || ! $id = absint( $_REQUEST['id'] ) ) {
				wp_send_json_error();
			}

			if ( empty( $_REQUEST['attachments'] ) || empty( $_REQUEST['attachments'][ $id ] ) ) {
				wp_send_json_error();
			}

			/*
			 * Media Categories uses this
			 */
			if ( isset( $_REQUEST['category-filter'] ) ) {
				unset( $_REQUEST['category-filter'] );
				unset( $_POST['category-filter'] );
			}

			if ( isset( $_REQUEST['mla_attachments'] ) ) {
				unset( $_REQUEST['mla_attachments'] );
				unset( $_POST['mla_attachments'] );
			}

			if ( isset( $_REQUEST['tax_input'] ) ) {
				unset( $_REQUEST['tax_input'] );
				unset( $_POST['tax_input'] );
			}

			foreach( $enhanced_taxonomies as $taxonomy ) {
				if ( isset( $_REQUEST['attachments'][ $id ][ $taxonomy ] ) ) {
					unset( $_REQUEST['attachments'][ $id ][ $taxonomy ] );
					unset( $_POST['attachments'][ $id ][ $taxonomy ] );
				}

				if ( isset( $_REQUEST[ $taxonomy ] ) ) {
					unset( $_REQUEST[ $taxonomy ] );
					unset( $_POST[ $taxonomy ] );
				}

				if ( ( 'category' == $taxonomy ) && isset( $_REQUEST['post_category'] ) ) {
					unset( $_REQUEST['post_category'] );
					unset( $_POST['post_category'] );
				}

				if ( isset( $_REQUEST[ 'new' . $taxonomy ] ) ) {
					unset( $_REQUEST[ 'new' . $taxonomy ] );
					unset( $_POST[ 'new' . $taxonomy ] );
					unset( $_REQUEST[ 'new' . $taxonomy . '_parent' ] );
					unset( $_POST[ 'new' . $taxonomy . '_parent' ] );
					unset( $_REQUEST[ '_ajax_nonce-add-' . $taxonomy ] );
					unset( $_POST[ '_ajax_nonce-add-' . $taxonomy ] );
				}

				if ( isset( $_REQUEST[ 'search-' . $taxonomy ] ) ) {
					unset( $_REQUEST[ 'search-' . $taxonomy ] );
					unset( $_POST[ 'search-' . $taxonomy ] );
					unset( $_REQUEST[ '_ajax_nonce-search-' . $taxonomy ] );
					unset( $_POST[ '_ajax_nonce-search-' . $taxonomy ] );
				}
			} // foreach taxonomy
		} // save-attachment-compat
	} // mla_admin_init_action

	/**
	 * Saves the get_media_item_args array for the attachment_fields_to_edit filter
	 *
	 * Declared public because it is a filter.
	 *
	 * @since 1.71
	 *
	 * @param	array	arguments for the get_media_item function in /wp-admin/includes/media.php
	 *
	 * @return	array	arguments for the get_media_item function (unchanged)
	 */
	public static function mla_get_media_item_args_filter( $args ) {
		self::$media_item_args = $args;
		return $args;
	} // mla_get_media_item_args_filter

	/**
	 * The get_media_item_args array
	 *
	 * @since 1.71
	 *
	 * @var	array ( 'errors' => array of strings, 'in_modal => boolean )
	 */
	private static $media_item_args = array( 'errors' => NULL, 'in_modal' => false );

	/**
	 * Add/change custom fields to the Edit Media screen and Modal Window
	 *
	 * Called from /wp-admin/includes/media.php, function get_compat_media_markup();
	 * If "get_media_item_args"['in_modal'] => false ) its the Edit Media screen.
	 * If "get_media_item_args"['in_modal'] => true ) its the Media Manager Modal Window.
	 * For the Modal Window, $form_fields contains all the "compat-attachment-fields"
	 * including the taxonomies, which we want to enhance.
	 * Declared public because it is a filter.
	 *
	 * @since 1.71
	 *
	 * @param	array	descriptors for the "compat-attachment-fields" 
	 * @param	object	the post to be edited
	 *
	 * @return	array	updated descriptors for the "compat-attachment-fields"
	 */
	public static function mla_attachment_fields_to_edit_filter( $form_fields, $post ) {
		$id = $post->ID;

		/*
		 * This logic is only required for the MLA-enhanced Media Manager Modal Window.
		 * For the non-Modal Media/Edit Media screen, the MLAEdit::mla_add_meta_boxes_action
		 * function changes the default meta box to the MLA searchable meta box.
		 */
		if ( isset( self::$media_item_args['in_modal'] ) && self::$media_item_args['in_modal'] ) {
			foreach ( get_taxonomies( array ( 'show_ui' => true ), 'objects' ) as $key => $value ) {
				if ( MLACore::mla_taxonomy_support( $key ) ) {
					if ( isset( $form_fields[ $key ] ) ) {
						$field = $form_fields[ $key ];
					} else {
						continue;
					}

					if ( ! $use_checklist = $value->hierarchical ) {
						$use_checklist =  MLACore::mla_taxonomy_support( $key, 'flat-checklist' );
					}

					/*
					 * Make sure the appropriate MMMW Enhancement option has been checked
					 */
					if ( $use_checklist ) {
						if ( 'checked' !== MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_DETAILS_CATEGORY_METABOX ) ) {
							continue;
						}
					} else {
						if ( 'checked' !== MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_DETAILS_TAG_METABOX ) ) {
							continue;
						}
					}

					/*
					 * Remove "Media Categories" meta box, if present.
					 */
					if ( isset( $form_fields[ $key . '_metabox' ] ) ) {
						unset( $form_fields[ $key . '_metabox' ] );
					}

					/*
					 * Simulate the default MMMW text box with a hidden field;
					 * use term names for flat taxonomies and term_ids for hierarchical.
					 */
					$post_id = $post->ID;
					$label = $field['labels']->name;
					$terms = get_object_term_cache( $post_id, $key );

					if ( false === $terms ) {
						$terms = wp_get_object_terms( $post_id, $key );
						wp_cache_add( $post_id, $terms, $key . '_relationships' );
					}

					if ( is_wp_error( $terms ) || empty( $terms ) ) {
						$terms = array();
					}

					$list = array();
					foreach ( $terms as $term ) {
						if ( $value->hierarchical ) {
							$list[] = $term->term_id;
						} else {
							$list[] = $term->name;
						}
					} // foreach $term

					sort( $list );
					$list = join( ',', $list );
					$class = ( $value->hierarchical ) ? 'categorydiv' : 'tagsdiv';

					$row  = "\t\t<tr class='compat-field-{$key} mla-taxonomy-row' style='display: none'>\n";
					$row .= "\t\t<th class='label' valign='top' scope='row'>\n";
					$row .= "\t\t<label for='mla-attachments-{$post_id}-{$key}'>\n";
					$row .= "\t\t<span title='" . __( 'Click to toggle', 'media-library-assistant' ) . "' class='alignleft'>{$label}</span><br class='clear'>\n";
					$row .= "\t\t</label></th>\n";
					$row .= "\t\t<td class='field'>\n";
					$row .= "\t\t<div class='mla-taxonomy-field'>\n";
					$row .= "\t\t<input name='mla_attachments[{$post_id}][{$key}]' class='text' id='mla-attachments-{$post_id}-{$key}' type='hidden' value='{$list}'>\n";
					$row .= "\t\t<div id='mla-taxonomy-{$key}' class='{$class}'>\n";
					$row .= '&lt;- ' . __( 'Click to toggle', 'media-library-assistant' ) . "\n";
					$row .= "\t\t</div>\n";
					$row .= "\t\t</div>\n";
					$row .= "\t\t</td>\n";
					$row .= "\t\t</tr>\n";
					//$form_fields[ $key ] = array( 'tr' => $row );
					$form_fields[ 'mla-' . $key ] = array( 'tr' => $row );
				} // is supported
			} // foreach

			$form_fields = apply_filters( 'mla_media_modal_form_fields', $form_fields, $post );
		} // in_modal

		self::$media_item_args = array( 'errors' => NULL, 'in_modal' => false );
		return $form_fields;
	} // mla_attachment_fields_to_edit_filter

	/**
	 * Ajax handler for Media Manager "fill compat-attachment-fields" queries 
	 *
	 * Prepares an array of (HTML) taxonomy meta boxes with attachment-specific values.
	 *
	 * @since 2.20
	 *
	 * @return	void	passes array of results to wp_send_json_success() for JSON encoding and transmission
	 */
	public static function mla_fill_compat_fields_action() {
		if ( empty( $_REQUEST['query'] ) || ! $requested = $_REQUEST['query'] ) {
			wp_send_json_error();
		}

		if ( empty( $_REQUEST['id'] ) || ! $post_id = absint( $_REQUEST['id'] ) ) {
			wp_send_json_error();
		}

		if ( NULL == ( $post = get_post( $post_id ) ) ) {
			wp_send_json_error();
		}

		$results = apply_filters( 'mla_media_modal_begin_fill_compat_fields', array(), $requested, $post );
		if ( ! empty( $results ) ) {
			wp_send_json_success( $results );
		}

		/*
		 * Match all supported taxonomies against the requested list
		 */
		foreach ( get_taxonomies( array ( 'show_ui' => true ), 'objects' ) as $key => $value ) {
			if ( MLACore::mla_taxonomy_support( $key ) ) {
				if ( is_integer( $index = array_search( $key, $requested ) ) ) {
					$request = $requested[ $index ];
				} else {
					continue;
				}

				if ( ! $use_checklist = $value->hierarchical ) {
					$use_checklist = MLACore::mla_taxonomy_support( $key, 'flat-checklist' );
				}

				if ( $use_checklist ) {
					if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_DETAILS_CATEGORY_METABOX ) ) {
						unset( $requested[ $index ] );
						$label = $value->label;
						$terms = get_object_term_cache( $post_id, $key );

						if ( false === $terms ) {
							$terms = wp_get_object_terms( $post_id, $key );
							wp_cache_add( $post_id, $terms, $key . '_relationships' );
						}

						if ( is_wp_error( $terms ) || empty( $terms ) ) {
							$terms = array();
						}

						$list = array();
						foreach ( $terms as $term ) {
							$list[] = $term->term_id;
						} // foreach $term

						sort( $list );
						$list = join( ',', $list );

						/*
						 * Simulate the 'add_meta_boxes' callback
						 */
						$box = array (
							'id' => $key . 'div',
							'title' => $label,
							'callback' => 'MLACore::mla_checklist_meta_box',
							'args' => array ( 'taxonomy' => $key, 'in_modal' => true ),

						);

						ob_start();
						MLACore::mla_checklist_meta_box( $post, $box );
						$row_content = ob_get_clean();

						$row = "\t\t<th class='label' valign='top' scope='row' style='width: 99%;'>\n";
						$row .= "\t\t<label for='mla-attachments-{$post_id}-{$key}'>\n";
						$row .= "\t\t<span title='" . __( 'Click to toggle', 'media-library-assistant' ) . "' class='alignleft' style='width: 99%; text-align: left;'>{$label}</span><br class='clear'>\n";
						$row .= "\t\t</label></th>\n";
						$row .= "\t\t<td class='field' style='width: 99%; display: none'>\n";
						$row .= "\t\t<div class='mla-taxonomy-field'>\n";
						$row .= "\t\t<input name='attachments[{$post_id}][{$key}]' class='text' id='mla-attachments-{$post_id}-{$key}' type='hidden' value='{$list}'>\n";
						$row .= $row_content;
						$row .= "\t\t</div>\n";
						$row .= "\t\t</td>\n";
						$results[ $key ] = $row;
					} // checked
				} /* use_checklist */ else { // flat
					if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_DETAILS_TAG_METABOX ) ) {
						unset( $requested[ $index ] );
						$label = $value->label;
						$terms = get_object_term_cache( $post_id, $key );

						if ( false === $terms ) {
							$terms = wp_get_object_terms( $post_id, $key );
							wp_cache_add( $post_id, $terms, $key . '_relationships' );
						}

						if ( is_wp_error( $terms ) || empty( $terms ) ) {
							$terms = array();
						}

						$list = array();
						foreach ( $terms as $term ) {
							$list[] = $term->name;
						} // foreach $term

						sort( $list );
						$hidden_list = join( ',', $list );

						$row = "\t\t<th class='label' valign='top' scope='row' style='width: 99%;'>\n";
						$row .= "\t\t<label for='mla-attachments-{$post_id}-{$key}'>\n";
						$row .= "\t\t<span title='" . __( 'Click to toggle', 'media-library-assistant' ) . "' class='alignleft' style='width: 99%; text-align: left;'>{$label}</span><br class='clear'>\n";
						$row .= "\t\t</label></th>\n";
						$row .= "\t\t<td class='field' style='width: 99%; display: none'>\n";
						$row .= "\t\t<div class='mla-taxonomy-field'>\n";
						$row .= "\t\t<div class='tagsdiv' id='mla-taxonomy-{$key}'>\n";
						$row .= "\t\t<div class='jaxtag'>\n";
						$row .= "\t\t<div class='nojs-tags hide-if-js'>\n";
						$row .= "\t\t<input name='attachments[{$post_id}][{$key}]' class='the-tags' id='mla-attachments-{$post_id}-{$key}' type='hidden' value='{$hidden_list}'>\n";
						$row .= "\t\t<input name='mla_tags[{$post_id}][{$key}]' class='server-tags' id='mla-tags-{$post_id}-{$key}' type='hidden' value='{$hidden_list}'>\n";
						$row .= "\t\t</div>\n"; // nojs-tags
						$row .= "\t\t<div class='ajaxtag'>\n";
						$row .= "\t\t<label class='screen-reader-text' for='new-tag-{$key}'>" . __( 'Tags', 'media-library-assistant' ) . "</label>\n";
						/* translators: %s: add new taxonomy label */
						$row .= "\t\t<div class='taghint'>" . sprintf( __( 'Add New %1$s', 'media-library-assistant' ), $label ) . "</div>\n";
						$row .= "\t\t<p>\n";
						$row .= "\t\t<input name='newtag[{$key}]' class='newtag form-input-tip' id='new-tag-{$key}' type='text' size='16' value='' autocomplete='off'>\n";
						$row .= "\t\t<input class='button tagadd' type='button' value='Add'>\n";
						$row .= "\t\t</p>\n";
						$row .= "\t\t</div>\n"; // ajaxtag
						$row .= "\t\t<p class='howto'>Separate tags with commas</p>\n";
						$row .= "\t\t</div>\n"; // jaxtag
						$row .= "\t\t<div class='tagchecklist'>\n";

						foreach ( $list as $index => $term ) {
							$row .= "\t\t<span><a class='ntdelbutton' id='post_tag-check-num-{$index}'>X</a>&nbsp;{$term}</span>\n";
						}

						$row .= "\t\t</div>\n"; // tagchecklist
						$row .= "\t\t</div>\n"; // tagsdiv
						$row .= "\t\t<p><a class='tagcloud-link' id='mla-link-{$key}' href='#titlediv'>" . __( 'Choose from the most used tags', 'media-library-assistant' ) . "</a></p>\n";
						$row .= "\t\t</div>\n"; // mla-taxonomy-field
						$row .= "\t\t</td>\n";
						$results[ $key ] = $row;
					} // checked
				} // flat
			} // is supported
		} // foreach

		/*
		 * Any left-over requests are for unsupported taxonomies
		 */
		foreach( $requested as $key ) {
			$row  = "\t\t<tr class='compat-field-{$key} mla-taxonomy-row'>\n";
			$row .= "\t\t<th class='label' valign='top' scope='row'>\n";
			$row .= "\t\t<label for='mla-attachments-{$post_id}-{$key}'>\n";
			$row .= "\t\t<span title='" . __( 'Click to toggle', 'media-library-assistant' ) . "' class='alignleft'>{$label}</span><br class='clear'>\n";
			$row .= "\t\t</label></th>\n";
			$row .= "\t\t<td class='field' style='display: none'>\n";
			$row .= "\t\t<div class='mla-taxonomy-field'>\n";
			$row .= "\t\t<input name='attachments[{$post_id}][{$key}]' class='text' id='mla-attachments-{$post_id}-{$key}' type='hidden' value=''>\n";
			$row .= "\t\t<div id='taxonomy-{$key}' class='categorydiv'>\n";
			$row .= __( 'Not Supported', 'media-library-assistant' ) . ".\n";
			$row .= "\t\t</div>\n";
			$row .= "\t\t</div>\n";
			$row .= "\t\t</td>\n";
			$row .= "\t\t</tr>\n";
			$results[ $key ] = $row;
		}

		wp_send_json_success( apply_filters( 'mla_media_modal_end_fill_compat_fields', $results, $_REQUEST['query'], $requested, $post ) );
	} // mla_fill_compat_fields_action

	/**
	 * Ajax handler for Media Manager "update compat-attachment-fields" queries 
	 *
	 * Updates one (or more) supported taxonomy and returns updated checkbox or tag/term lists
	 *
	 * @since 2.20
	 *
	 * @return	void	passes array of results to wp_send_json_success() for JSON encoding and transmission
	 */
	public static function mla_update_compat_fields_action() {
		global $post;

		if ( empty( $_REQUEST['id'] ) || ! $post_id = absint( $_REQUEST['id'] ) ) {
			wp_send_json_error();
		}

		if ( empty( $post ) ) {
			$post = get_post( $post_id ); // for filters and wp_popular_terms_checklist
		}

		do_action( 'mla_media_modal_begin_update_compat_fields', $post );

		$taxonomies = array();
		$results = array();

		foreach ( get_taxonomies( array ( 'show_ui' => true ), 'objects' ) as $key => $value ) {
			if ( isset( $_REQUEST[ $key ] ) && MLACore::mla_taxonomy_support( $key ) ) {
				$taxonomies[ $key ] = $value;

				if ( ! $use_checklist = $value->hierarchical ) {
					$use_checklist =  MLACore::mla_taxonomy_support( $key, 'flat-checklist' );
				}

				if ( $value->hierarchical ) {
					$terms = array_map( 'absint', preg_split( '/,+/', $_REQUEST[ $key ] ) );
				} else {
					$terms = array_map( 'trim', preg_split( '/,+/', $_REQUEST[ $key ] ) );
				}

				$terms = apply_filters( 'mla_media_modal_update_compat_fields_terms', $terms, $key, $value, $post_id );

				if ( is_array( $terms ) ) { 
					wp_set_object_terms( $post_id, $terms, $key, false );
					delete_transient( MLA_OPTION_PREFIX . 't_term_counts_' . $key );
				}

				if ( $use_checklist ) {
					ob_start();
					$popular_ids = wp_popular_terms_checklist( $key );
					$results[$key]["mla-{$key}-checklist-pop"] = ob_get_clean();

					ob_start();

					if ( $value->hierarchical ) {
						wp_terms_checklist( $post_id, array( 'taxonomy' => $key, 'popular_cats' => $popular_ids ) );
					} else {
						$checklist_walker = new MLA_Checklist_Walker;
						wp_terms_checklist( $post_id, array( 'taxonomy' => $key, 'popular_cats' => $popular_ids, 'walker' => $checklist_walker ) );
					}

					$results[$key]["mla-{$key}-checklist"] = ob_get_clean();
				} else {
					$terms = get_object_term_cache( $post_id, $key );

					if ( false === $terms ) {
						$terms = wp_get_object_terms( $post_id, $key );
						wp_cache_add( $post_id, $terms, $key . '_relationships' );
					}

					if ( is_wp_error( $terms ) || empty( $terms ) ) {
						$terms = array();
					}

					$list = array();
					$object_terms = array();
					foreach ( $terms as $term ) {
						$list[] = $term->name;
						$object_terms[ $term->term_id ] = $term->name;
					} // foreach $term

					sort( $list );
					$hidden_list = join( ',', $list );

					$results[$key]["object-terms"] = $object_terms;
					$results[$key]["mla-attachments-{$post_id}-{$key}"] = "\t\t<input name='attachments[{$post_id}][{$key}]' class='the-tags' id='mla-attachments-{$post_id}-{$key}' type='hidden' value='{$hidden_list}'>\n";
					$results[$key]["mla-tags-{$post_id}-{$key}"] = "\t\t<input name='mla_tags[{$post_id}][{$key}]' class='server-tags' id='mla-tags-{$post_id}-{$key}' type='hidden' value='{$hidden_list}'>\n";
				}
			} // set and supported
		} // foreach taxonomy

		wp_send_json_success( apply_filters( 'mla_media_modal_end_update_compat_fields', $results, $taxonomies, $post ) );
	} // mla_update_compat_fields_action

	/**
	 * Ajax handler for Media Manager "Query Attachments" queries 
	 *
	 * Adapted from wp_ajax_query_attachments in /wp-admin/includes/ajax-actions.php
	 *
	 * @since 2.20
	 *
	 * @return	void	passes array of post arrays to wp_send_json_success() for JSON encoding and transmission
	 */
	public static function mla_query_attachments_action() {
		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error();
		}

		/*
		 * Pick out and clean up the query terms we can process
		 */
		$raw_query = isset( $_REQUEST['query'] ) ? (array) $_REQUEST['query'] : array();
		$query = array_intersect_key( $raw_query, array_flip( array(
			'order', 'orderby', 'posts_per_page', 'paged', 'post_mime_type',
			'post_parent', 'post__in', 'post__not_in',
			'mla_filter_month', 'mla_filter_term', 'mla_terms_search',
			'mla_search_value', 's', 'mla_search_fields', 'mla_search_connector'
		) ) );
//error_log( __LINE__ . ' mla_query_attachments_action query = ' . var_export( $query, true ), 0 );

		$query = apply_filters( 'mla_media_modal_query_initial_terms', $query, $raw_query );

		if ( isset( $query['post_mime_type'] ) ) {
			if ( 'detached' == $query['post_mime_type'] ) {
				$query['detached'] = '1';
				unset( $query['post_mime_type'] );
			} elseif ( 'attached' == $query['post_mime_type'] ) {
				$query['detached'] = '0';
				unset( $query['post_mime_type'] );
			} elseif ( 'trash' == $query['post_mime_type'] ) {
				$query['status'] = 'trash';
				unset( $query['post_mime_type'] );
			} else {
				$view = $query['post_mime_type'];
				unset( $query['post_mime_type'] );
				$query = array_merge( $query, MLACore::mla_prepare_view_query( 'view', $view ) );
			}
		}

		/*
		 * Convert mla_filter_month back to the WordPress "m" parameter
		 */
		if ( isset( $query['mla_filter_month'] ) ) {
			if ( '0' != $query['mla_filter_month'] ) {
				$query['m'] = $query['mla_filter_month'];
			}

			unset( $query['mla_filter_month'] );
		}

		/*
		 * Process the enhanced search box OR fix up the default search box
		 */
		if ( isset( $query['mla_search_value'] ) ) {
			if ( ! empty( $query['mla_search_value'] ) ) {
				$query['s'] = $query['mla_search_value'];
			}

			unset( $query['mla_search_value'] );
		}

		if ( isset( $query['posts_per_page'] ) ) {
			$count = $query['posts_per_page'];
			$offset = $count * (isset( $query['paged'] ) ? $query['paged'] - 1 : 0);
		} else {
			$count = 0;
			$offset = 0;
		}

		/*
		 * Check for sorting override
		 */
		$option =  MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_ORDERBY );
		if ( 'default' != $option ) {
			/*
			 * Make sure the current orderby choice still exists or revert to default.
			 */
			$default_orderby = array_merge( array( 'none' => array('none',false) ), MLAQuery::mla_get_sortable_columns( ) );
			$found_current = false;
			foreach ($default_orderby as $key => $value ) {
				if ( $option == $value[0] ) {
					$found_current = true;
					break;
				}
			}

			if ( ! $found_current ) {
				MLACore::mla_delete_option( MLACoreOptions::MLA_DEFAULT_ORDERBY );
				$option = MLACore::mla_get_option( MLACoreOptions::MLA_DEFAULT_ORDERBY );
			}

			$query['orderby'] = $option;
		}

		$option = MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_ORDER );
		if ( 'default' != $option ) {
			$query['order'] = $option;
		}

		$query['post_type'] = 'attachment';

		if ( empty( $query['status'] ) ) {
			$query['post_status'] = 'inherit';
			if ( current_user_can( get_post_type_object( 'attachment' )->cap->read_private_posts ) ) {
				$query['post_status'] .= ',private';
			}
		}

		$query = apply_filters( 'mla_media_modal_query_filtered_terms', $query, $raw_query );

		$query = MLAQuery::mla_query_media_modal_items( $query, $offset, $count );
		$posts = array_map( 'wp_prepare_attachment_for_js', $query->posts );
		$posts = array_filter( $posts );

		wp_send_json_success( $posts );
	}
} //Class MLAModal_Ajax
?>