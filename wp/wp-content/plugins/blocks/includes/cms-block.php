<?php

class wpcmsb_cmsblock {

	const post_type = 'wpcmsb_cms_block';

	private static $found_items = 0;
	private static $current = null;

	private $id;
	private $name;
	private $title;
	private $properties = array();
	private $unit_tag;
	private $responses_count = 0;
	private $scanned_form_tags;

	public static function count() {
		return self::$found_items;
	}

	public static function get_current() {
		return self::$current;
	}

	public static function register_post_type() {
		register_post_type( self::post_type, array(
			'labels' => array(
				'name' => __( 'Block', 'cms-block' ),
				'singular_name' => __( 'Block', 'cms-block' ) ),
			'rewrite' => false,
			'query_var' => false ) );
	}

	public static function find( $args = '' ) {
		$defaults = array(
			'post_status' => 'any',
			'posts_per_page' => -1,
			'offset' => 0,
			'orderby' => 'ID',
			'order' => 'ASC' );

		$args = wp_parse_args( $args, $defaults );

		$args['post_type'] = self::post_type;

		$q = new WP_Query();
		$posts = $q->query( $args );

		self::$found_items = $q->found_posts;

		$objs = array();

		foreach ( (array) $posts as $post )
			$objs[] = new self( $post );

		return $objs;
	}

	public static function get_template( $args = '' ) {
		global $l10n;

		$defaults = array( 'locale' => null, 'title' => '' );
		$args = wp_parse_args( $args, $defaults );

		$locale = $args['locale'];
		$title = $args['title'];

		if ( $locale ) {
			$mo_orig = $l10n['cms-block'];
			wpcmsb_load_textdomain( $locale );
		}

		self::$current = $cms_block = new self;
		$cms_block->title =
			( $title ? $title : __( 'Untitled', 'cms-block' ) );
		$cms_block->locale = ( $locale ? $locale : get_locale() );

		$properties = $cms_block->get_properties();

		foreach ( $properties as $key => $value ) {
			$properties[$key] = wpcmsb_cmsblockTemplate::get_default( $key );
		}

		$cms_block->properties = $properties;

		$cms_block = apply_filters( 'wpcmsb_cms_block_default_pack',
			$cms_block, $args );

		if ( isset( $mo_orig ) ) {
			$l10n['cms-block'] = $mo_orig;
		}

		return $cms_block;
	}

	public static function get_instance( $post ) {
		$post = get_post( $post );

		if ( ! $post || self::post_type != get_post_type( $post ) ) {
			return false;
		}

		self::$current = $cms_block = new self( $post );

		return $cms_block;
	}

	private static function get_unit_tag( $id = 0 ) {
		static $global_count = 0;

		$global_count += 1;

		if ( in_the_loop() ) {
			$unit_tag = sprintf( 'wpb wpb-%1$d wpb-b%1$d-p%2$d-o%3$d',
				absint( $id ), get_the_ID(), $global_count );
		} else {
			$unit_tag = sprintf( 'wp-block wp-block-%1$d wpcmsb-b%1$d-o%2$d',
				absint( $id ), $global_count );
		}

		return $unit_tag;
	}


	private function __construct( $post = null ) {
		$post = get_post( $post );

		if ( $post && self::post_type == get_post_type( $post ) ) {
			$this->id = $post->ID;
			$this->name = $post->post_name;
			$this->title = $post->post_title;
			$this->locale = get_post_meta( $post->ID, '_locale', true );

			$properties = $this->get_properties();

			foreach ( $properties as $key => $value ) {
				if ( metadata_exists( 'post', $post->ID, '_' . $key ) ) {
					$properties[$key] = get_post_meta( $post->ID, '_' . $key, true );
				} elseif ( metadata_exists( 'post', $post->ID, $key ) ) {
					$properties[$key] = get_post_meta( $post->ID, $key, true );
				}
			}

			$this->properties = $properties;
			$this->upgrade();
		}

		do_action( 'wpcmsb_cms_block', $this );
	}

	public function __get( $name ) {
		$message = __( '<code>%1$s</code> property of a <code>wpcmsb_cmsblock</code> object is <strong>no longer accessible</strong>. Use <code>%2$s</code> method instead.', 'cms-block' );

		if ( 'id' == $name ) {
			if ( WP_DEBUG ) {
				trigger_error( sprintf( $message, 'id', 'id()' ) );
			}

			return $this->id;
		} elseif ( 'title' == $name ) {
			if ( WP_DEBUG ) {
				trigger_error( sprintf( $message, 'title', 'title()' ) );
			}

			return $this->title;
		} elseif ( $prop = $this->prop( $name ) ) {
			if ( WP_DEBUG ) {
				trigger_error(
					sprintf( $message, $name, 'prop(\'' . $name . '\')' ) );
			}

			return $prop;
		}
	}

	public function initial() {
		return empty( $this->id );
	}

	public function prop( $name ) {
		$props = $this->get_properties();
		return isset( $props[$name] ) ? $props[$name] : null;
	}

	public function get_properties() {
		$properties = (array) $this->properties;

		$properties = wp_parse_args( $properties, array(
			'wsbenvolver'=> '0',
			'wsbautop'=> '0',
			'wsbtipoenvol' => '1',
			'wsbclaseenvol' => '',
			'form' => '',
			'messages' => array()) );

		$properties = (array) apply_filters( 'wpcmsb_cms_block_properties',
			$properties, $this );

		return $properties;
	}

	public function set_properties( $properties ) {
		$defaults = $this->get_properties();

		$properties = wp_parse_args( $properties, $defaults );
		$properties = array_intersect_key( $properties, $defaults );

		$this->properties = $properties;
	}

	public function id() {
		return $this->id;
	}

	public function name() {
		return $this->name;
	}

	public function title() {
		return $this->title;
	}

	public function set_title( $title ) {
		$title = trim( $title );

		if ( '' === $title ) {
			$title = __( 'Untitled', 'cms-block' );
		}

		$this->title = $title;
	}

	// Return true if this form is the same one as currently POSTed.
	public function is_posted() {

		if ( empty( $_POST['_wpcmsb_unit_tag'] ) ) {
			return false;
		}

		return $this->unit_tag == $_POST['_wpcmsb_unit_tag'];
	}

	/* Generating Form HTML */

	public function form_html( $atts = array() ) {

		//fcp: 04/11/2015 aqui se recuperan las variables para envolver al contenido

		$wsbenvolver = $this->prop( 'wsbenvolver' );
		$wsbtipoenvol = $this->prop( 'wsbtipoenvol' );
		$wsbclaseenvol = $this->prop( 'wsbclaseenvol' );


		//$envol='';
		//$tipenvol='div';

		if ($wsbenvolver=='0'){
			$tipenvol='';
		}
		else {
			switch ($wsbtipoenvol) {
				case 1:
					$tipenvol = 'div';
					break;
				case 2:
					$tipenvol = 'p';
					break;
				case 3:
					$tipenvol = 'span';
					break;
				default:
					$tipenvol = 'div';
					break;
			}
		}

		//var_dump($wsbenvolver);

		$atts = wp_parse_args( $atts, array(
			'html_id' => '',
			'html_name' => '',
			'html_class' => '',
			'output' => $tipenvol ) );

		if ( 'raw_form' == $atts['output'] ) {
			return '<pre class="wpcmsb-raw-form"><code>'
				. esc_html( $this->prop( 'div' ) ) . '</code></pre>';
		}

		$this->unit_tag = self::get_unit_tag( $this->id );


		if ($wsbenvolver=='1'){
			$envol = '<'.$tipenvol.' %s>';

			if(trim($wsbclaseenvol) ==''){
				$clase=$this->unit_tag;
			}else {
				$clase=$wsbclaseenvol;
			}

			$html = sprintf( $envol, wpcmsb_format_atts( array(// 'id' => 'wp-blocks',
			'class' => $clase ) ) ) . "\n";
		}else {
			$html = '';
		}

		// $html .= $this->screen_reader_response() . "\n";

		$url = wpcmsb_get_request_uri();

		if ( $frag = strstr( $url, '#' ) )
			$url = substr( $url, 0, -strlen( $frag ) );

		$url .= '#' . $this->unit_tag;

		$url = apply_filters( 'wpcmsb_form_action_url', $url );

		$id_attr = apply_filters( 'wpcmsb_form_id_attr',
			preg_replace( '/[^A-Za-z0-9:._-]/', '', $atts['html_id'] ) );

		$name_attr = apply_filters( 'wpcmsb_form_name_attr',
			preg_replace( '/[^A-Za-z0-9:._-]/', '', $atts['html_name'] ) );

		$class = 'wpcmsb-wrap';

		if ( $atts['html_class'] ) {
			$class .= ' ' . $atts['html_class'];
		}

		if ( $this->in_demo_mode() ) {
			$class .= ' demo';
		}

		$class = explode( ' ', $class );
		$class = array_map( 'sanitize_html_class', $class );
		$class = array_filter( $class );
		$class = array_unique( $class );
		$class = implode( ' ', $class );
		$class = apply_filters( 'wpcmsb_form_class_attr', $class );

		$enctype = apply_filters( 'wpcmsb_form_enctype', '' );

		$novalidate = apply_filters( 'wpcmsb_form_novalidate', wpcmsb_support_html5() );

		// $html .= sprintf( '<div %s>',
		// 	wpcmsb_format_atts( array(
		// 		/*'action' => esc_url( $url ),
		// 		'method' => 'post',*/
		// 		'id' => $id_attr,
		// 		'name' => $name_attr,
		// 		'class' => $class/*,
		// 		'enctype' => wpcmsb_enctype_value( $enctype ),
		// 		'novalidate' => $novalidate ? 'novalidate' : ''*/ ) ) ) . "\n";

		// /*$html .= $this->form_hidden_fields();*/

		$html .= $this->form_elements();
		//remove_filter ($html, 'wpautop');

		//$html = wpautop($html, false);

		// if ( ! $this->responses_count ) {
		// 	//$html .= $this->form_response_output();
		// }

		// $html .= '</div>';

		if ($wsbenvolver=='1'){
			$envol ='</'.$tipenvol.'>';
			$html .= $envol;
		}else{
			$html .='';
		}

		return $html;
	}


	// public function screen_reader_response() {
	// 	$class = 'screen-reader-response';
	// 	$role = '';
	// 	$content = '';

	// 	if ( $this->is_posted() ) { // Post response output for non-AJAX
	// 		$role = 'alert';

	// 		$submission = wpcmsb_Submission::get_instance();

	// 		if ( $response = $submission->get_response() ) {
	// 			$content = esc_html( $response );
	// 		}

	// 		if ( $invalid_fields = $submission->get_invalid_fields() ) {
	// 			$content .= "\n" . '<ul>' . "\n";

	// 			foreach ( (array) $invalid_fields as $name => $field ) {
	// 				if ( $field['idref'] ) {
	// 					$link = sprintf( '<a href="#%1$s">%2$s</a>',
	// 						esc_attr( $field['idref'] ),
	// 						esc_html( $field['reason'] ) );
	// 					$content .= sprintf( '<li>%s</li>', $link );
	// 				} else {
	// 					$content .= sprintf( '<li>%s</li>',
	// 						esc_html( $field['reason'] ) );
	// 				}

	// 				$content .= "\n";
	// 			}

	// 			$content .= '</ul>' . "\n";
	// 		}
	// 	}

	// 	$atts = array(
	// 		'class' => trim( $class ),
	// 		'role' => trim( $role ) );

	// 	$atts = wpcmsb_format_atts( $atts );

	// 	$output = sprintf( '<div %1$s>%2$s</div>',
	// 		$atts, $content );

	// 	return $output;
	// }

	/* Form Elements */

	public function form_do_shortcode() {
		$manager = wpcmsb_ShortcodeManager::get_instance();
		$form = $this->prop( 'form' );
		$wsbautop = $this->prop( 'wsbautop' );

		if ( wpcmsb_AUTOP ) {
			$form = $manager->normalize_shortcode( $form );
			if ( $wsbautop==1 ) {
				$form = wpcmsb_autop( $form );
			}
		}

		$form = $manager->do_shortcode( $form );
		$this->scanned_form_tags = $manager->get_scanned_tags();

		return $form;
	}

	public function form_scan_shortcode( $cond = null ) {
		$manager = wpcmsb_ShortcodeManager::get_instance();

		if ( ! empty( $this->scanned_form_tags ) ) {
			$scanned = $this->scanned_form_tags;
		} else {
			$scanned = $manager->scan_shortcode( $this->prop( 'form' ) );
			$this->scanned_form_tags = $scanned;
		}

		if ( empty( $scanned ) )
			return null;

		if ( ! is_array( $cond ) || empty( $cond ) )
			return $scanned;

		for ( $i = 0, $size = count( $scanned ); $i < $size; $i++ ) {

			if ( isset( $cond['type'] ) ) {
				if ( is_string( $cond['type'] ) && ! empty( $cond['type'] ) ) {
					if ( $scanned[$i]['type'] != $cond['type'] ) {
						unset( $scanned[$i] );
						continue;
					}
				} elseif ( is_array( $cond['type'] ) ) {
					if ( ! in_array( $scanned[$i]['type'], $cond['type'] ) ) {
						unset( $scanned[$i] );
						continue;
					}
				}
			}

			if ( isset( $cond['name'] ) ) {
				if ( is_string( $cond['name'] ) && ! empty( $cond['name'] ) ) {
					if ( $scanned[$i]['name'] != $cond['name'] ) {
						unset ( $scanned[$i] );
						continue;
					}
				} elseif ( is_array( $cond['name'] ) ) {
					if ( ! in_array( $scanned[$i]['name'], $cond['name'] ) ) {
						unset( $scanned[$i] );
						continue;
					}
				}
			}
		}

		return array_values( $scanned );
	}

	public function form_elements() {
		return apply_filters( 'wpcmsb_form_elements', $this->form_do_shortcode() );
	}


	public function is_true( $name ) {
		/*$settings = $this->additional_setting( $name, false );

		foreach ( $settings as $setting ) {
			if ( in_array( $setting, array( 'on', 'true', '1' ) ) )
				return true;
		}*/

		return false;
	}

	public function in_demo_mode() {
		return $this->is_true( 'demo_mode' );
	}

	/* Upgrade */

	private function upgrade() {

		$messages = $this->prop( 'messages' );

		if ( is_array( $messages ) ) {
			foreach ( wpcmsb_messages() as $key => $arr ) {
				if ( ! isset( $messages[$key] ) ) {
					$messages[$key] = $arr['default'];
				}
			}
		}

		$this->properties['messages'] = $messages;
	}

	/* Save */

	public function save() {
		$props = $this->get_properties();

		$post_content = implode( "\n", wpcmsb_array_flatten( $props ) );

		if ( $this->initial() ) {
			$post_id = wp_insert_post( array(
				'post_type' => self::post_type,
				'post_status' => 'publish',
				'post_title' => $this->title,
				'post_content' => trim( $post_content ) ) );
		} else {
			$post_id = wp_update_post( array(
				'ID' => (int) $this->id,
				'post_status' => 'publish',
				'post_title' => $this->title,
				'post_content' => trim( $post_content ) ) );
		}

		if ( $post_id ) {
			foreach ( $props as $prop => $value ) {
				update_post_meta( $post_id, '_' . $prop,
					wpcmsb_normalize_newline_deep( $value ) );
			}

			if ( wpcmsb_is_valid_locale( $this->locale ) ) {
				update_post_meta( $post_id, '_locale', $this->locale );
			}

			if ( $this->initial() ) {
				$this->id = $post_id;
				do_action( 'wpcmsb_after_create', $this );
			} else {
				do_action( 'wpcmsb_after_update', $this );
			}

			do_action( 'wpcmsb_after_save', $this );
		}

		return $post_id;
	}

	public function copy() {
		$new = new self;
		$new->title = $this->title . '_copy';
		$new->locale = $this->locale;
		$new->properties = $this->properties;

		return apply_filters( 'wpcmsb_copy', $new, $this );
	}

	public function delete() {
		if ( $this->initial() )
			return;

		if ( wp_delete_post( $this->id, true ) ) {
			$this->id = 0;
			return true;
		}

		return false;
	}

	public function shortcode( $args = '' ) {
		$args = wp_parse_args( $args, array(
			'use_old_format' => false ) );

		$title = str_replace( array( '"', '[', ']' ), '', $this->title );

		if ( $args['use_old_format'] ) {
			$old_unit_id = (int) get_post_meta( $this->id, '_old_cf7_unit_id', true );

			if ( $old_unit_id ) {
				$shortcode = sprintf( '[block %1$d "%2$s"]', $old_unit_id, $title );
			} else {
				$shortcode = '';
			}
		} else {
			$shortcode = sprintf( '[block id="%1$d" title="%2$s"]',
				$this->id, $title );
		}

		return apply_filters( 'wpcmsb_cms_block_shortcode', $shortcode, $args, $this );
	}
}

function wpcmsb_cms_block( $id ) {
	return wpcmsb_cmsblock::get_instance( $id );
}

function wpcmsb_get_cms_block_by_old_id( $old_id ) {
	global $wpdb;

	$q = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_old_cf7_unit_id'"
		. $wpdb->prepare( " AND meta_value = %d", $old_id );

	if ( $new_id = $wpdb->get_var( $q ) )
		return wpcmsb_cms_block( $new_id );
}

function wpcmsb_get_cms_block_by_title( $title ) {
	$page = get_page_by_title( $title, OBJECT, wpcmsb_cmsblock::post_type );

	if ( $page )
		return wpcmsb_cms_block( $page->ID );

	return null;
}

function wpcmsb_get_current_cms_block() {
	if ( $current = wpcmsb_cmsblock::get_current() ) {
		return $current;
	}
}

function wpcmsb_is_posted() {
	if ( ! $cms_block = wpcmsb_get_current_cms_block() )
		return false;

	return $cms_block->is_posted();
}

function wpcmsb_get_hangover( $name, $default = null ) {
	if ( ! wpcmsb_is_posted() ) {
		return $default;
	}

	return isset( $_POST[$name] ) ? wp_unslash( $_POST[$name] ) : $default;
}

function wpcmsb_get_message( $status ) {
	if ( ! $cms_block = wpcmsb_get_current_cms_block() )
		return '';

	return $cms_block->message( $status );
}

function wpcmsb_scan_shortcode( $cond = null ) {
	if ( ! $cms_block = wpcmsb_get_current_cms_block() )
		return null;

	return $cms_block->form_scan_shortcode( $cond );
}

function wpcmsb_form_controls_class( $type, $default = '' ) {
	$type = trim( $type );
	$default = array_filter( explode( ' ', $default ) );

	$classes = array_merge( array( 'wpcmsb-form-control' ), $default );

	$typebase = rtrim( $type, '*' );
	$required = ( '*' == substr( $type, -1 ) );

	$classes[] = 'wpcmsb-' . $typebase;

	if ( $required )
		$classes[] = 'wpcmsb-validates-as-required';

	$classes = array_unique( $classes );

	return implode( ' ', $classes );
}

function wpcmsb_cms_block_tag_func( $atts, $content = null, $code = '' ) {
	//var_dump($code); var_dump( ' FIN FIN  '    );
	if ( is_feed() ) {
		return '[cms-block]';
	}

	
	if ( 'cms-block' == $code or 'block' == $code  ) {
		$atts = shortcode_atts( array(
			'id' => 0,
			'title' => '',
			'html_id' => '',
			'html_name' => '',
			'html_class' => '',
			'output' => 'form' ), $atts );

		$id = (int) $atts['id'];
		$title = trim( $atts['title'] );

		if ( ! $cms_block = wpcmsb_cms_block( $id ) ) {
			$cms_block = wpcmsb_get_cms_block_by_title( $title );
		}

	} else {
		if ( is_string( $atts ) ) {
			$atts = explode( ' ', $atts, 2 );
		}

		$id = (int) array_shift( $atts );
		$cms_block = wpcmsb_get_cms_block_by_old_id( $id );
	}

	if ( ! $cms_block ) {
		return '[cms-block 404 "Not Found"]';
	}

	return $cms_block->form_html( $atts );
}
