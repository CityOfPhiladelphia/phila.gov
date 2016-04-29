<?php
/**
 * Media Library Assistant Custom Taxonomy and Widget objects
 *
 * @package Media Library Assistant
 * @since 0.1
 */

/**
 * Class MLA (Media Library Assistant) Objects defines and manages custom taxonomies for Attachment Categories and Tags
 *
 * @package Media Library Assistant
 * @since 0.20
 */
class MLAObjects {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 0.20
	 *
	 * @return	void
	 */
	public static function initialize() {
		self::_build_taxonomies();
	}

	/**
	 * Registers Attachment Categories and Attachment Tags custom taxonomies, adds taxonomy-related filters
	 *
	 * @since 0.1
	 *
	 * @return	void
	 */
	private static function _build_taxonomies( ) {
		if ( MLACore::mla_taxonomy_support('attachment_category') ) {
			$labels = array(
				'name' => _x( 'Att. Categories', 'taxonomy_name_plural', 'media-library-assistant' ),
				'singular_name' => _x( 'Att. Category', 'taxonomy_name_singular', 'media-library-assistant' ),
				'search_items' => __( 'Search Att. Categories', 'media-library-assistant' ),
				'all_items' => __( 'All Att. Categories', 'media-library-assistant' ),
				'parent_item' => __( 'Parent Att. Category', 'media-library-assistant' ),
				'parent_item_colon' => __( 'Parent Att. Category', 'media-library-assistant' ) . ':',
				'edit_item' => __( 'Edit Att. Category', 'media-library-assistant' ),
				'update_item' => __( 'Update Att. Category', 'media-library-assistant' ),
				/* translators: %s: add new taxonomy label */
				'add_new_item' => sprintf( __( 'Add New %1$s', 'media-library-assistant' ), __( 'Att. Category', 'media-library-assistant' ) ),
				'new_item_name' => __( 'New Att. Category Name', 'media-library-assistant' ),
				'menu_name' => __( 'Att. Category', 'media-library-assistant' ) 
			);

			register_taxonomy(
				'attachment_category',
				array( 'attachment' ),
				array(
				  'hierarchical' => true,
				  'labels' => $labels,
				  'show_ui' => true,
				  'query_var' => true,
				  'rewrite' => true,
				  'update_count_callback' => '_update_generic_term_count'
				)
			);
		}

		if ( MLACore::mla_taxonomy_support('attachment_tag') ) {
			$labels = array(
				'name' => _x( 'Att. Tags', 'taxonomy_name_plural', 'media-library-assistant' ),
				'singular_name' => _x( 'Att. Tag', 'taxonomy_name_singular', 'media-library-assistant' ),
				'search_items' => __( 'Search Att. Tags', 'media-library-assistant' ),
				'all_items' => __( 'All Att. Tags', 'media-library-assistant' ),
				'parent_item' => __( 'Parent Att. Tag', 'media-library-assistant' ),
				'parent_item_colon' => __( 'Parent Att. Tag', 'media-library-assistant' ) . ':',
				'edit_item' => __( 'Edit Att. Tag', 'media-library-assistant' ),
				'update_item' => __( 'Update Att. Tag', 'media-library-assistant' ),
				/* translators: %s: add new taxonomy label */
				'add_new_item' => sprintf( __( 'Add New %1$s', 'media-library-assistant' ), __( 'Att. Tag', 'media-library-assistant' ) ),
				'new_item_name' => __( 'New Att. Tag Name', 'media-library-assistant' ),
				'menu_name' => __( 'Att. Tag', 'media-library-assistant' ) 
			);

			register_taxonomy(
				'attachment_tag',
				array( 'attachment' ),
				array(
				  'hierarchical' => false,
				  'labels' => $labels,
				  'show_ui' => true,
				  'update_count_callback' => '_update_post_term_count',
				  'query_var' => true,
				  'rewrite' => true,
				  'update_count_callback' => '_update_generic_term_count'
				)
			);
		}

		MLACore::mla_initialize_tax_checked_on_top();
		$taxonomies = get_taxonomies( array ( 'show_ui' => true ), 'names' );
		foreach ( $taxonomies as $tax_name ) {
			if ( MLACore::mla_taxonomy_support( $tax_name ) ) {
				register_taxonomy_for_object_type( $tax_name, 'attachment');
				add_filter( "manage_edit-{$tax_name}_columns", 'MLAObjects::mla_taxonomy_get_columns_filter', 0x7FFFFFFF, 1 ); // $columns
				add_filter( "manage_{$tax_name}_custom_column", 'MLAObjects::mla_taxonomy_column_filter', 0x7FFFFFFF, 3 ); // $place_holder, $column_name, $tag->term_id
			} // taxonomy support
		} // foreach
	} // _build_taxonomies

	/**
	 * WordPress Filter for edit taxonomy "Attachments" column,
	 * which replaces the "Posts" column with an equivalent "Attachments" column.
	 *
	 * @since 0.30
	 *
	 * @param	array	column definitions for the edit taxonomy list table
	 *
	 * @return	array	updated column definitions for the edit taxonomy list table
	 */
	public static function mla_taxonomy_get_columns_filter( $columns ) {
		/*
		 * Adding or inline-editing a tag is done with AJAX, and there's no current screen object
		 */
		if ( isset( $_POST['action'] ) && in_array( $_POST['action'], array( 'add-tag', 'inline-save-tax' ) ) ) {
			$post_type = !empty($_POST['post_type']) ? $_POST['post_type'] : 'post';
			$taxonomy = !empty($_POST['taxonomy']) ? $_POST['taxonomy'] : 'post_tag';
		} else {
			$screen = get_current_screen();
			$post_type = !empty( $screen->post_type ) ? $screen->post_type : 'post';
			$taxonomy = !empty( $screen->taxonomy ) ? $screen->taxonomy : 'post_tag';
		}

		if ( 'attachment' == $post_type ) {
			$filter_columns = apply_filters( 'mla_taxonomy_get_columns', NULL, $columns, $taxonomy );
			if ( ! empty( $filter_columns ) ) {
				return $filter_columns;
			}

			if ( isset ( $columns[ 'posts' ] ) ) {
				unset( $columns[ 'posts' ] );
			}

			$columns[ 'attachments' ] = __( 'Attachments', 'media-library-assistant' );
		}

		return $columns;
	}

	/**
	 * WordPress Filter for edit taxonomy "Attachments" column,
	 * which returns a count of the attachments assigned a given term
	 *
	 * @since 0.30
	 *
	 * @param	string	current column value; filled in by earlier filter handlers
	 * @param	string	name of the column
	 * @param	integer	ID of the term for which the count is desired
	 *
	 * @return	array	HTML markup for the column content; number of attachments in the category
	 *					and alink to retrieve a list of them
	 */
	public static function mla_taxonomy_column_filter( $current_value, $column_name, $term_id ) {
		static $taxonomy = NULL, $tax_object = NULL, $count_terms = false, $terms = array();

		/*
		 * Do setup tasks once per page load
		 */
		if ( NULL == $taxonomy ) {
			/*
			 * Adding or inline-editing a tag is done with AJAX, and there's no current screen object
			 */
			if ( defined('DOING_AJAX') && DOING_AJAX ) {
				$taxonomy = !empty($_POST['taxonomy']) ? $_POST['taxonomy'] : 'post_tag';
			} else {
				$screen = get_current_screen();
				$taxonomy = !empty( $screen->taxonomy ) ? $screen->taxonomy : 'post_tag';
			}
		}

		$filter_content = apply_filters( 'mla_taxonomy_column', NULL, $current_value, $column_name, $term_id, $taxonomy );
		if ( ! empty( $filter_content ) ) {
			return $filter_content;
		}

		if ( 'attachments' !== $column_name ) {
			return $current_value;
		}

		/*
		 * Do setup tasks once per page load
		 */
		if ( NULL == $tax_object ) {
			/*
			 * Adding or inline-editing a tag is done with AJAX, and there's no current screen object
			 */
			if ( defined('DOING_AJAX') && DOING_AJAX ) {
				$taxonomy = !empty($_POST['taxonomy']) ? $_POST['taxonomy'] : 'post_tag';
			} else {
				$screen = get_current_screen();
				$taxonomy = !empty( $screen->taxonomy ) ? $screen->taxonomy : 'post_tag';
			}

			$tax_object = get_taxonomy( $taxonomy );

			$count_terms = 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_COUNT_TERM_ATTACHMENTS );
			if ( $count_terms ) {
				$terms = get_transient( MLA_OPTION_PREFIX . 't_term_counts_' . $taxonomy );

				if ( ! is_array( $terms ) ) {
					/* 
					 * The MLAShortcodes class is only loaded when needed.
					 */
					if ( !class_exists( 'MLAShortcodes' ) ) {
						require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcodes.php' );
					}
		
					$cloud = MLAShortcodes::mla_get_terms( array(
						'taxonomy' => $taxonomy,
						'fields' => 't.term_id, t.name, t.slug, COUNT(p.ID) AS `count`',
						'number' => 0,
						'no_orderby' => true
					) );

					unset( $cloud['found_rows'] );
					foreach( $cloud as $term ) {
						$terms[ $term->term_id ] = $term;
					}

					set_transient( MLA_OPTION_PREFIX . 't_term_counts_' . $taxonomy, $terms, 300 ); // five minutes
				}// build the array
			} // set $terms
		} // setup tasks

		if ( isset( $terms[ $term_id ] ) ) {
			$term = $terms[ $term_id ];
			$column_text = number_format_i18n( $term->count );
		} else {
			$term = get_term( $term_id, $taxonomy );

			if ( is_wp_error( $term ) ) {
				/* translators: 1: ERROR tag 2: taxonomy 3: error message */
				error_log( sprintf( _x( '%1$s: mla_taxonomy_column_filter( "%2$s" ) - get_term failed: "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $taxonomy, $term->get_error_message() ), 0 );
				return 0;
			} elseif ($count_terms ) {
				$column_text = number_format_i18n( 0 );
			} else {
				$column_text = __( 'click to search', 'media-library-assistant' );
			}
		}

		return sprintf( '<a href="%1$s">%2$s</a>', esc_url( add_query_arg(
				array( 'page' => MLACore::ADMIN_PAGE_SLUG, 'mla-tax' => $taxonomy, 'mla-term' => $term->slug, 'heading_suffix' => urlencode( $tax_object->label . ':' . $term->name ) ), 'upload.php' ) ), $column_text );
	}
} //Class MLAObjects

/**
 * Class MLA (Media Library Assistant) Text Widget defines a shortcode-enabled version of the WordPress Text widget
 *
 * @package Media Library Assistant
 * @since 1.60
 */
class MLATextWidget extends WP_Widget {

	/**
	 * Calls the parent constructor to set some defaults.
	 *
	 * @since 1.60
	 *
	 * @return	void
	 */
	function __construct() {
		$widget_args = array(
			'classname' => 'mla_text_widget',
			'description' => __( 'Shortcode(s), HTML and/or Plain Text', 'media-library-assistant' )
		);

		$control_args = array(
			'width' => 400,
			'height' => 350
		);

		parent::__construct( 'mla-text-widget', __( 'MLA Text', 'media-library-assistant' ), $widget_args, $control_args );
	}

	/**
	 * Display the widget content - called from the WordPress "front end"
	 *
	 * @since 1.60
	 *
	 * @param	array	Widget arguments
	 * @param	array	Widget definition, from the database
	 *
	 * @return	void	Echoes widget output
	 */
	function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$text = do_shortcode( apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance ) );
		echo $args['before_widget'];
		if ( !empty( $title ) ) { echo $args['before_title'] . $title . $args['after_title']; } ?>
			<div class="textwidget"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Echo the "edit widget" form on the Appearance/Widgets admin screen
	 *
	 * @since 1.60
	 *
	 * @param	array	Previous definition values, from the database
	 *
	 * @return	void	Echoes "edit widget" form
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags( $instance['title'] );
		$text = esc_textarea( $instance['text'] );
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title', 'media-library-assistant' ) . ':'; ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo $text; ?></textarea>

		<p><input id="<?php echo $this->get_field_id( 'filter' ); ?>" name="<?php echo $this->get_field_name( 'filter' ); ?>" type="checkbox" <?php checked( isset( $instance['filter'] ) ? $instance['filter'] : 0 ); ?> />&nbsp;<label for="<?php echo $this->get_field_id( 'filter' ); ?>"><?php _e( 'Automatically add paragraphs', 'media-library-assistant' ); ?></label></p>
<?php
	}

	/**
	 * Sanitize widget definition as it is saved to the database
	 *
	 * @since 1.60
	 *
	 * @param	array	Current definition values, to be saved in the database
	 * @param	array	Previous definition values, from the database
	 *
	 * @return	array	Updated definition values to be saved in the database
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] =  $new_instance['text'];
		} else {
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['text'] ) ) ); // wp_filter_post_kses() expects slashed
		}

		$instance['filter'] = isset( $new_instance['filter'] );
		return $instance;
	}

	/**
	 * Register the widget with WordPress
	 * 
	 * Defined as public because it's an action.
	 *
	 * @since 1.60
	 *
	 * @return	void
	 */
	public static function mla_text_widget_widgets_init_action(){
		register_widget( 'MLATextWidget' );
	}
} // Class MLATextWidget

/*
 * Actions are added here, when the source file is loaded, because the MLATextWidget
 * object(s) are created too late to be useful.
 */
add_action( 'widgets_init', 'MLATextWidget::mla_text_widget_widgets_init_action' );
?>