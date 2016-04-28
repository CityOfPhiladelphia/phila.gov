<?php
/**
 * Media Library Assistant Ajax Handlers
 *
 * @package Media Library Assistant
 * @since 2.20
 */

/**
 * Class MLA (Media Library Assistant) Ajax contains handlers for simple Ajax requests
 *
 * @package Media Library Assistant
 * @since 2.20
 */
class MLA_Ajax {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 2.20
	 *
	 * @return	void
	 */
	public static function initialize() {
		add_action( 'admin_init', 'MLA_Ajax::mla_admin_init_action' );
	}

	/**
	 * Adds flat checklist taxonomy support to the Media Manager Modal Window.
	 * Declared public because it is an action.
	 *
	 * @since 2.20
	 */
	public static function mla_admin_init_action( ) {
		//error_log( __LINE__ . ' DEBUG: MLA_Ajax::mla_admin_init_action $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );
		/*
		 * If there's no action variable, we have nothing more to do
		 */
		if ( ! isset( $_POST['action'] ) ) {
			return;
		}

		/*
		 * For flat taxonomies that use the checklist meta box, substitute our own handler
		 * for /wp-admin/includes/ajax-actions.php function _wp_ajax_add_hierarchical_term().
		 */
		if ( ( defined('DOING_AJAX') && DOING_AJAX ) && ( 'add-' == substr( $_POST['action'], 0, 4 ) ) ) {
			$key = substr( $_POST['action'], 4 );
			if ( MLACore::mla_taxonomy_support( $key, 'flat-checklist' ) ) {
				self::_mla_ajax_add_flat_term( $key );
				/* note: this function sends an Ajax response and then dies; no return */
			}
		}

		add_action( 'wp_ajax_' . 'mla_find_posts', 'MLA_Ajax::mla_find_posts_ajax_action' );
		add_action( 'wp_ajax_' . MLACore::JAVASCRIPT_INLINE_EDIT_SLUG . '-set-parent', 'MLA_Ajax::mla_set_parent_ajax_action' );
	}

	/**
	 * Add flat taxonomy term from "checklist" meta box on the Media Manager Modal Window
	 *
	 * Adapted from the WordPress post_categories_meta_box() in /wp-admin/includes/meta-boxes.php.
	 *
	 * @since 2.20
	 *
	 * @param string The taxonomy name, from $_POST['action']
	 *
	 * @return void Sends JSON response with updated HTML for the checklist
	 */
	private static function _mla_ajax_add_flat_term( $key ) {
		$taxonomy = get_taxonomy( $key );
		check_ajax_referer( $_POST['action'], '_ajax_nonce-add-' . $key, true );

		if ( !current_user_can( $taxonomy->cap->edit_terms ) ) {
			wp_die( -1 );
		}

		$new_names = explode( ',', $_POST[ 'new' . $key ] );
		$new_terms_markup = '';
		foreach( $new_names as $name ) {
			if ( '' === sanitize_title( $name ) ) {
				continue;
			}

			if ( ! $id = term_exists( $name, $key ) ) {
				$id = wp_insert_term( $name, $key );
			}

			if ( is_wp_error( $id ) ) {
				continue;
			}

			if ( is_array( $id ) ) {
				$id = absint( $id['term_id'] );
			} else {
				continue;
			}
			$term = get_term( $id, $key );
			$name = $term->name;
			$new_terms_markup .= "<li id='{$key}-{$id}'><label class='selectit'><input value='{$name}' type='checkbox' name='tax_input[{$key}][]' id='in-{$key}-{$id}' checked='checked' />{$name}</label></li>\n";
		} // foreach new_name

		$input_new_parent_name = "new{$key}_parent";
		$supplemental = "<input type='hidden' name='{$input_new_parent_name}' id='{$input_new_parent_name}' value='-1' />";	

		$add = array(
			'what' => $key,
			'id' => $id,
			'data' => $new_terms_markup,
			'position' => -1,
			'supplemental' => array( 'newcat_parent' => $supplemental )
		);

		$x = new WP_Ajax_Response( $add );
		$x->send();
	} // _mla_ajax_add_flat_term

	/**
	 * Ajax handler to fetch candidates for the "Set Parent" popup window
	 *
	 * Adapted from wp_ajax_find_posts in /wp-admin/includes/ajax-actions.php.
	 * Adds filters for post type and pagination.
	 *
	 * @since 1.90
	 *
	 * @return	void	passes results to wp_send_json_success() for JSON encoding and transmission
	 */
	public static function mla_find_posts_ajax_action() {
		global $wpdb;

		check_ajax_referer( 'mla_find_posts', MLACore::MLA_ADMIN_NONCE_NAME );

		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		unset( $post_types['attachment'] );

		$s = stripslashes( $_REQUEST['mla_set_parent_search_text'] );
		$count = isset( $_REQUEST['mla_set_parent_count'] ) ? $_REQUEST['mla_set_parent_count'] : 50;
		$paged = isset( $_REQUEST['mla_set_parent_paged'] ) ? $_REQUEST['mla_set_parent_paged'] : 1;

		$args = array(
			'post_type' => ( 'all' == $_REQUEST['mla_set_parent_post_type'] ) ? array_keys( $post_types ) : $_REQUEST['mla_set_parent_post_type'],
			'post_status' => 'any',
			'posts_per_page' => $count,
			'paged' => $paged,
		);

		if ( '' !== $s )
			$args['s'] = $s;

		$posts = get_posts( $args );

		if ( ( ! $posts ) && $paged > 1 ) {
			$args['paged'] = $paged = 1;
			$posts = get_posts( $args );
		}

		$found = count( $posts );

		$html = '<input name="mla_set_parent_count" id="mla-set-parent-count" type="hidden" value="' . $count . "\">\n";
		$html .= '<input name="mla_set_parent_paged" id="mla-set-parent-paged" type="hidden" value="' . $paged . "\">\n";
		$html .= '<input name="mla_set_parent_found" id="mla-set-parent-found" type="hidden" value="' . $found . "\">\n";

		$html .= '<table class="widefat"><thead><tr><th class="found-radio"><br /></th><th>'.__('Title').'</th><th class="no-break">'.__('Type').'</th><th class="no-break">'.__('Date').'</th><th class="no-break">'.__('Status').'</th></tr></thead><tbody>' . "\n";
		if ( $found ) {
			$alt = '';
			foreach ( $posts as $post ) {
				$title = trim( $post->post_title ) ? $post->post_title : __( '(no title)' );
				$alt = ( 'alternate' == $alt ) ? '' : 'alternate';

				switch ( $post->post_status ) {
					case 'publish' :
					case 'private' :
						$stat = __('Published');
						break;
					case 'future' :
						$stat = __('Scheduled');
						break;
					case 'pending' :
						$stat = __('Pending Review');
						break;
					case 'draft' :
						$stat = __('Draft');
						break;
					default:
						$stat = sanitize_text_field( $post->post_status );
				}

				if ( '0000-00-00 00:00:00' == $post->post_date ) {
					$time = '';
				} else {
					/* translators: date format in table columns, see http://php.net/date */
					$time = mysql2date(__('Y/m/d'), $post->post_date);
				}

				$html .= '<tr class="' . trim( 'found-posts ' . $alt ) . '"><td class="found-radio"><input type="radio" id="found-'.$post->ID.'" name="found_post_id" value="' . esc_attr($post->ID) . '"></td>';
				$html .= '<td><label for="found-'.$post->ID.'">' . esc_html( $title ) . '</label></td><td class="no-break">' . esc_html( $post_types[$post->post_type]->labels->singular_name ) . '</td><td class="no-break">'.esc_html( $time ) . '</td><td class="no-break">' . esc_html( $stat ). ' </td></tr>' . "\n";
			} // foreach post
		} else {
				$html .= '<tr class="' . trim( 'found-posts ' ) . '"><td class="found-radio">&nbsp;</td>';
				$html .= '<td colspan="4">No results found.</td></tr>' . "\n";
		}

		$html .= "</tbody></table>\n";

		wp_send_json_success( $html );
	}

	/**
	 * Ajax handler to set post_parent for a single attachment
	 *
	 * Adapted from wp_ajax_inline_save in /wp-admin/includes/ajax-actions.php
	 *
	 * @since 0.20
	 *
	 * @return	void	echo HTML <td> innerHTML for updated call or error message, then die()
	 */
	public static function mla_set_parent_ajax_action() {
		check_ajax_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

		if ( empty( $_REQUEST['post_ID'] ) ) {
			echo __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'No post ID found', 'media-library-assistant' );
			die();
		} else {
			$post_id = $_REQUEST['post_ID'];
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'You are not allowed to edit this Attachment.', 'media-library-assistant' ) );
		}

		if ( ! class_exists( 'MLAData' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data.php' );
			MLAData::initialize();
		}
		
		$results = MLAData::mla_update_single_item( $post_id, $_REQUEST );
		if ( false !== strpos( $results['message'], __( 'ERROR', 'media-library-assistant' ) ) ) {
			wp_die( $results['message'] );
		}

		$new_item = (object) MLAData::mla_get_attachment_by_id( $post_id );

		if ( ! class_exists( 'MLA_List_Table' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-list-table.php' );
			MLA_List_Table::mla_admin_init_action();
		}
		
		//	Create an instance of our package class and echo the new HTML
		$MLAListTable = apply_filters( 'mla_list_table_new_instance', NULL );
		if ( is_null( $MLAListTable ) ) {
			$MLAListTable = new MLA_List_Table();
		}

		$MLAListTable->single_row( $new_item );
		die(); // this is required to return a proper result
	}
} // Class MLA_Ajax

/*
 * Check for Media Manager Enhancements
 */
if ( ( ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_TOOLBAR ) ) || ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_GRID_TOOLBAR ) ) ) ) {
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-media-modal-ajax.php' );
	add_action( 'init', 'MLAModal_Ajax::initialize', 0x7FFFFFFF );
}
?>