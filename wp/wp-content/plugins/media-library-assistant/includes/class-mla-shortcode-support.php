<?php
/**
 * Media Library Assistant Shortcode handler(s)
 *
 * @package Media Library Assistant
 * @since 2.20
 */

/* 
 * The MLA database access functions aren't available to "front end" posts/pages
 */
if ( !class_exists( 'MLAQuery' ) ) {
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-query.php' );
	MLAQuery::initialize();
}

if ( !class_exists( 'MLAData' ) ) {
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data.php' );
	MLAData::initialize();
}

/**
 * Class MLA (Media Library Assistant) Shortcode Support provides the functions that
 * implement the [mla_gallery] and [mla_tag_cloud] shortcodes. It also implements the
 * mla_get_shortcode_attachments() and mla_get_terms() database access functions.
 *
 * @package Media Library Assistant
 * @since 2.20
 */
class MLAShortcode_Support {
	/**
	 * Style and Markup templates
	 *
	 * @since 2.20
	 *
	 * @var	array
	 */
	public static $mla_custom_templates = NULL;

	/**
	 * Load style and markup templates to $mla_custom_templates
	 *
	 * @since 2.20
	 *
	 * @return	void
	 */
	public static function mla_load_custom_templates() {
		MLAShortcode_Support::$mla_custom_templates = MLAData::mla_load_template( 'mla-custom-templates.tpl' );

		/* 	
		 * Load the default templates
		 */
		if ( is_null( MLAShortcode_Support::$mla_custom_templates ) ) {
			MLACore::mla_debug_add( '<strong>mla_debug _load_option_templates()</strong> ' . __( 'error loading tpls/mla-option-templates.tpl', 'media-library-assistant' ) );
			return;
		} elseif ( !MLAShortcode_Support::$mla_custom_templates ) {
			MLACore::mla_debug_add( '<strong>mla_debug _load_option_templates()</strong> ' . __( 'tpls/mla-option-templates.tpl not found', 'media-library-assistant' ) );
			MLAShortcode_Support::$mla_custom_templates = NULL;
			return;
		}

		/*
		 * Add user-defined Style and Markup templates
		 */
		$templates = MLACore::mla_get_option( 'style_templates' );
		if ( is_array(	$templates ) ) {
			foreach ( $templates as $name => $value ) {
				MLAShortcode_Support::$mla_custom_templates[ $name . '-style' ] = $value;
			} // foreach $templates
		} // is_array

		$templates = MLACore::mla_get_option( 'markup_templates' );
		if ( is_array(	$templates ) ) {
			foreach ( $templates as $name => $value ) {
				MLAShortcode_Support::$mla_custom_templates[ $name . '-open-markup' ] = $value['open'];
				MLAShortcode_Support::$mla_custom_templates[ $name . '-row-open-markup' ] = $value['row-open'];
				MLAShortcode_Support::$mla_custom_templates[ $name . '-item-markup' ] = $value['item'];
				MLAShortcode_Support::$mla_custom_templates[ $name . '-row-close-markup' ] = $value['row-close'];
				MLAShortcode_Support::$mla_custom_templates[ $name . '-close-markup' ] = $value['close'];
			} // foreach $templates
		} // is_array
	}

	/**
	 * Fetch style or markup template from $mla_templates
	 *
	 * @since 2.20
	 *
	 * @param	string	Template name
	 * @param	string	Template type; 'style' (default) or 'markup'
	 *
	 * @return	string|boolean|null	requested template, false if not found or null if no templates
	 */
	public static function mla_fetch_gallery_template( $key, $type = 'style' ) {
		if ( ! is_array( MLAShortcode_Support::$mla_custom_templates ) ) {
			MLACore::mla_debug_add( '<strong>mla_fetch_gallery_template()</strong> ' . __( 'no templates exist', 'media-library-assistant' ) );
			return NULL;
		}

		$array_key = $key . '-' . $type;
		if ( array_key_exists( $array_key, MLAShortcode_Support::$mla_custom_templates ) ) {
			return MLAShortcode_Support::$mla_custom_templates[ $array_key ];
		} else {
			MLACore::mla_debug_add( "<strong>mla_fetch_gallery_template( {$key}, {$type} )</strong> " . __( 'not found', 'media-library-assistant' ) );
			return false;
		}
	}

	/**
	 * Verify the presence of Ghostscript for mla_viewer
	 *
	 * @since 2.20
	 *
	 * @param	string	Non-standard location to override default search, e.g.,
	 *					'C:\Program Files (x86)\gs\gs9.15\bin\gswin32c.exe'
	 * @param	boolean	Force ghostscript-only tests, used by MLASettings::_compose_mla_gallery_tab()
	 *
	 * @return	boolean	true if Ghostscript available else false
	 */
	public static function mla_ghostscript_present( $explicit_path = '', $ghostscript_only = false ) {
		static $ghostscript_present = NULL;

		if ( ! $ghostscript_only ) {
			if ( isset( $ghostscript_present ) ) {
				MLACore::mla_debug_add( '<strong>_ghostscript_present</strong>, ghostscript_present = ' . var_export( $ghostscript_present, true ) );
				return $ghostscript_present;
			}

			if ( 'checked' != MLACore::mla_get_option( 'enable_ghostscript_check' ) ) {
				MLACore::mla_debug_add( '<strong>_ghostscript_present</strong>, disabled' );
				return $ghostscript_present = true;
			}

			/*
			 * Imagick must be installed as well
			 */
			if ( ! class_exists( 'Imagick' ) ) {
				MLACore::mla_debug_add( '<strong>_ghostscript_present</strong>, Imagick missing' );
				return $ghostscript_present = false;
			}
		} // not ghostscript_only

		/*
		 * Look for exec() - from http://stackoverflow.com/a/12980534/866618
		 */
		if ( ini_get('safe_mode') ) {
			MLACore::mla_debug_add( '<strong>_ghostscript_present</strong>, safe_mode' );
			return $ghostscript_present = false;
		}

		$blacklist = preg_split( '/,\s*/', ini_get('disable_functions') . ',' . ini_get('suhosin.executor.func.blacklist') );
		if ( in_array('exec', $blacklist) ) {
			MLACore::mla_debug_add( '<strong>_ghostscript_present</strong>, exec in blacklist' );
			return $ghostscript_present = false;
		}

		if ( 'WIN' === strtoupper( substr( PHP_OS, 0, 3) ) ) {
			if ( ! empty( $explicit_path ) ) {
				$return = exec( 'dir /o:n/s/b "' . $explicit_path . '"' );
				MLACore::mla_debug_add( '<strong>_ghostscript_present</strong>, WIN explicit path = ' . var_export( $return, true ) );
				if ( ! empty( $return ) ) {
					return $ghostscript_present = true;
				} else {
					return $ghostscript_present = false;
				}
			}

			$return = getenv('GSC');
			if ( ! empty( $return ) ) {
				return $ghostscript_present = true;
			}

			$return = exec('where gswin*c.exe');
			if ( ! empty( $return ) ) {
				return $ghostscript_present = true;
			}

			$return = exec('dir /o:n/s/b "C:\Program Files\gs\*gswin*c.exe"');
			if ( ! empty( $return ) ) {
				return $ghostscript_present = true;
			}

			$return = exec('dir /o:n/s/b "C:\Program Files (x86)\gs\*gswin32c.exe"');
			if ( ! empty( $return ) ) {
				return $ghostscript_present = true;
			}

			MLACore::mla_debug_add( '<strong>_ghostscript_present</strong>, WIN detection failed' );
			return $ghostscript_present = false;
		} // Windows platform

		if ( ! empty( $explicit_path ) ) {
			exec( 'test -e ' . $explicit_path, $dummy, $ghostscript_path );
			MLACore::mla_debug_add( '<strong>_ghostscript_present</strong>, explicit path = ' . var_export( $explicit_path, true ) . ', ghostscript_path = ' . var_export( $ghostscript_path, true ) );
			return ( $explicit_path === $ghostscript_path );
		}

		$return = exec('which gs');
		if ( ! empty( $return ) ) {
			return $ghostscript_present = true;
		}

		$test_path = '/usr/bin/gs';
		exec('test -e ' . $test_path, $dummy, $ghostscript_path);
		MLACore::mla_debug_add( '<strong>_ghostscript_present</strong>, test_path = ' . var_export( $test_path, true ) . ', ghostscript_path = ' . var_export( $ghostscript_path, true ) );
		return $ghostscript_present = ( $test_path === $ghostscript_path );
	}

	/**
	 * Make sure $attr is an array and repair line-break damage
	 *
	 * @since 2.20
	 *
	 * @param	mixed	array or string containing shortcode attributes
	 *
	 * @return	array	clean attributes array
	 */
	private static function _validate_attributes( $attr ) {
		if ( empty( $attr ) ) {
			$attr = array();
		} elseif ( is_string( $attr ) ) {
			$attr = shortcode_parse_atts( $attr );
		}

		// Numeric keys indicate parse errors
		$is_valid = true;
		foreach ( $attr as $key => $value ) {
			if ( is_numeric( $key ) ) {
				$is_valid = false;
				break;
			}
		}

		if ( $is_valid ) {
			return $attr;
		}

		/*
		 * Found an error, e.g., line break(s) among the atttributes.
		 * Try to reconstruct the input string without them.
		 */
		$new_attr = '';
		foreach ( $attr as $key => $value ) {
			$break_tag = strpos( $value, '<br' );
			if ( ( false !== $break_tag ) && ( ($break_tag + 3) == strlen( $value ) ) ) {
				$value = substr( $value, 0, ( strlen( $value ) - 3) );
			}

			if ( is_numeric( $key ) ) {
				if ( '/>' !== $value ) {
					$new_attr .= $value . ' ';
				}
			} else {
				$delimiter = ( false === strpos( $value, '"' ) ) ? '"' : "'";
				$new_attr .= $key . '=' . $delimiter . $value . $delimiter . ' ';
			}
		}

		return shortcode_parse_atts( $new_attr );
	}

	/**
	 * Turn debug collection and display on or off
	 *
	 * @since 2.20
	 *
	 * @var	boolean
	 */
	private static $mla_debug = false;

	/**
	 * The MLA Gallery shortcode.
	 *
	 * This is a superset of the WordPress Gallery shortcode for displaying images on a post,
	 * page or custom post type. It is adapted from /wp-includes/media.php gallery_shortcode.
	 * Enhancements include many additional selection parameters and full taxonomy support.
	 *
	 * @since 2.20
	 *
	 * @param array Attributes of the shortcode
	 * @param string Optional content for enclosing shortcodes; used with mla_alt_shortcode
	 *
	 * @return string HTML content to display gallery.
	 */
	public static function mla_gallery_shortcode( $attr, $content = NULL ) {
		global $post;

		/*
		 * Some do_shortcode callers may not have a specific post in mind
		 */
		if ( ! is_object( $post ) ) {
			$post = (object) array( 'ID' => 0 );
		}

		/*
		 * Make sure $attr is an array, even if it's empty,
		 * and repair damage caused by link-breaks in the source text
		 */
		$attr = self::_validate_attributes( $attr );

		/*
		 * Filter the attributes before $mla_page_parameter and "request:" prefix processing.
		 */
		 
		$attr = apply_filters( 'mla_gallery_raw_attributes', $attr );

		/*
		 * The mla_paginate_current parameter can be changed to support multiple galleries per page.
		 */
		if ( ! isset( $attr['mla_page_parameter'] ) ) {
			$attr['mla_page_parameter'] = self::$mla_get_shortcode_attachments_parameters['mla_page_parameter'];
		}

		$mla_page_parameter = $attr['mla_page_parameter'];
		 
		/*
		 * Special handling of the mla_paginate_current parameter to make
		 * "MLA pagination" easier. Look for this parameter in $_REQUEST
		 * if it's not present in the shortcode itself.
		 */
		if ( ! isset( $attr[ $mla_page_parameter ] ) ) {
			if ( isset( $_REQUEST[ $mla_page_parameter ] ) ) {
				$attr[ $mla_page_parameter ] = $_REQUEST[ $mla_page_parameter ];
			}
		}
		 
		/*
		 * These are the parameters for gallery display
		 */
		$mla_item_specific_arguments = array(
			'mla_link_attributes' => '',
			'mla_link_class' => '',
			'mla_link_href' => '',
			'mla_link_text' => '',
			'mla_nolink_text' => '',
			'mla_rollover_text' => '',
			'mla_image_class' => '',
			'mla_image_alt' => '',
			'mla_image_attributes' => '',
			'mla_caption' => ''
		);

		$mla_arguments = array_merge( array(
			'mla_output' => 'gallery',
			'mla_style' => MLACore::mla_get_option('default_style'),
			'mla_markup' => MLACore::mla_get_option('default_markup'),
			'mla_float' => is_rtl() ? 'right' : 'left',
			'mla_itemwidth' => MLACore::mla_get_option('mla_gallery_itemwidth'),
			'mla_margin' => MLACore::mla_get_option('mla_gallery_margin'),
			'mla_target' => '',
			'mla_debug' => false,

			'mla_viewer' => false,
			'mla_single_thread' => false,
			'mla_viewer_extensions' => 'ai,eps,pdf,ps',
			'mla_viewer_limit' => '0',
			'mla_viewer_width' => '0',
			'mla_viewer_height' => '0',
			'mla_viewer_best_fit' => NULL,
			'mla_viewer_page' => '1',
			'mla_viewer_resolution' => '0',
			'mla_viewer_quality' => '0',
			'mla_viewer_type' => '',

			'mla_alt_shortcode' => NULL,
			'mla_alt_ids_name' => 'ids',

			// paginatation arguments defined in $mla_get_shortcode_attachments_parameters
			// 'mla_page_parameter' => 'mla_paginate_current', handled in code with $mla_page_parameter
			// 'mla_paginate_current' => NULL,
			// 'mla_paginate_total' => NULL,
			// 'id' => NULL,

			'mla_end_size'=> 1,
			'mla_mid_size' => 2,
			'mla_prev_text' => '&laquo; ' . __( 'Previous', 'media-library-assistant' ),
			'mla_next_text' => __( 'Next', 'media-library-assistant' ) . ' &raquo;',
			'mla_paginate_type' => 'plain',
			'mla_paginate_rows' => NULL ),
			$mla_item_specific_arguments
		);

		$html5 = current_theme_supports( 'html5', 'gallery' );
		$default_arguments = array_merge( array(
			'size' => 'thumbnail', // or 'medium', 'large', 'full' or registered size
			'itemtag' => $html5 ? 'figure' : 'dl',
			'icontag' => $html5 ? 'div' : 'dt',
			'captiontag' => $html5 ? 'figcaption' : 'dd',
			'columns' => MLACore::mla_get_option('mla_gallery_columns'),
			'link' => 'permalink', // or 'post' or file' or a registered size
			// Photonic-specific
			'id' => NULL,
			'style' => NULL,
			'type' => 'default', // also used by WordPress.com Jetpack!
			'thumb_width' => 75,
			'thumb_height' => 75,
			'thumbnail_size' => 'thumbnail',
			'slide_size' => 'large',
			'slideshow_height' => 500,
			'fx' => 'fade',
			'timeout' => 4000,
			'speed' => 1000,
			'pause' => NULL),
			$mla_arguments
		);

		// $instance supports multiple galleries in one page/post	
		static $instance = 0;
		$instance++;

		/*
		 * Some values are already known, and can be used in data selection parameters
		 */
		$upload_dir = wp_upload_dir();
		$page_values = array(
			'instance' => $instance,
			'selector' => "mla_gallery-{$instance}",
			'site_url' => site_url(),
			'base_url' => $upload_dir['baseurl'],
			'base_dir' => $upload_dir['basedir'],
			'id' => $post->ID,
			'page_ID' => $post->ID,
			'page_author' => $post->post_author,
			'page_date' => $post->post_date,
			'page_content' => $post->post_content,
			'page_title' => $post->post_title,
			'page_excerpt' => $post->post_excerpt,
			'page_status' => $post->post_status,
			'page_name' => $post->post_name,
			'page_modified' => $post->post_modified,
			'page_guid' => $post->guid,
			'page_type' => $post->post_type,
			'page_url' => get_page_link(),
		);

		/*
		 * Look for page-level and 'request:' substitution parameters,
		 * which can be added to any input parameter
		 */
		foreach ( $attr as $attr_key => $attr_value ) {
			/*
			 * attachment-specific Gallery Display Content parameters must be evaluated
			 * later, when all of the information is available.
			 */
			if ( array_key_exists( $attr_key, $mla_item_specific_arguments ) ) {
				continue;
			}

			$attr_value = str_replace( '{+', '[+', str_replace( '+}', '+]', $attr_value ) );
			$replacement_values = MLAData::mla_expand_field_level_parameters( $attr_value, NULL, $page_values );
			$attr[ $attr_key ] = MLAData::mla_parse_template( $attr_value, $replacement_values );
		}

		/*
		 * Merge gallery arguments with defaults, pass the query arguments on to mla_get_shortcode_attachments.
		 */
		 
		$attr = apply_filters( 'mla_gallery_attributes', $attr );
		$content = apply_filters( 'mla_gallery_initial_content', $content, $attr );
		$arguments = shortcode_atts( $default_arguments, $attr );
		$arguments = apply_filters( 'mla_gallery_arguments', $arguments );

		self::$mla_debug = ( ! empty( $arguments['mla_debug'] ) ) ? trim( strtolower( $arguments['mla_debug'] ) ) : false;
		if ( self::$mla_debug ) {
			if ( 'true' == self::$mla_debug ) {
				MLACore::mla_debug_mode( 'buffer' );
			} elseif ( 'log' == self::$mla_debug ) {
				MLACore::mla_debug_mode( 'log' );
			} else {
				self::$mla_debug = false;
			}
		}

		/*
		 * Determine output type
		 */
		$output_parameters = array_map( 'strtolower', array_map( 'trim', explode( ',', $arguments['mla_output'] ) ) );
		$is_gallery = 'gallery' == $output_parameters[0];
		$is_pagination = in_array( $output_parameters[0], array( 'previous_page', 'next_page', 'paginate_links' ) ); 

		if ( $is_pagination && ( NULL !== $arguments['mla_paginate_rows'] ) ) {
			$attachments['found_rows'] = absint( $arguments['mla_paginate_rows'] );
		} else {
			$attachments = self::mla_get_shortcode_attachments( $post->ID, $attr, $is_pagination );
		}

		if ( is_string( $attachments ) ) {
			return $attachments;
		}

		if ( empty($attachments) ) {
			if ( self::$mla_debug ) {
				MLACore::mla_debug_add( '<strong>' . __( 'mla_debug empty gallery', 'media-library-assistant' ) . '</strong>, query = ' . var_export( $attr, true ) );
				$output = MLACore::mla_debug_flush();
			} else {
				$output =  '';
			}

			$output .= $arguments['mla_nolink_text'];
			return $output;
		} // empty $attachments

		/*
		 * Look for user-specified alternate gallery shortcode
		 */
		if ( is_string( $arguments['mla_alt_shortcode'] ) ) {
			/*
			 * Replace data-selection parameters with the "ids" list
			 */
			if ( 'mla_tag_cloud' == $arguments['mla_alt_shortcode'] ) {
				$blacklist = self::$mla_get_shortcode_attachments_parameters;
			} else {
				$blacklist = array_merge( $mla_arguments, self::$mla_get_shortcode_attachments_parameters );
			}

			$new_args = '';
			foreach ( $attr as $key => $value ) {
				if ( array_key_exists( $key, $blacklist ) ) {
					continue;
				}

				$slashed = addcslashes( $value, chr(0).chr(7).chr(8)."\f\n\r\t\v\"\\\$" );
				if ( ( false !== strpos( $value, ' ' ) ) || ( false !== strpos( $value, '\'' ) ) || ( $slashed != $value ) ) {
					$value = '"' . $slashed . '"';
				}

				$new_args .= empty( $new_args ) ? $key . '=' . $value : ' ' . $key . '=' . $value;
			} // foreach $attr

			$new_ids = '';
			foreach ( $attachments as $value ) {
				$new_ids .= empty( $new_ids ) ? (string) $value->ID : ',' . $value->ID;
			} // foreach $attachments

			$new_ids = $arguments['mla_alt_ids_name'] . '="' . $new_ids . '"';

			if ( self::$mla_debug ) {
				$output = MLACore::mla_debug_flush();
			} else {
				$output = '';
			}

			/*
			 * Execute the alternate gallery shortcode with the new parameters
			 */
			$content = apply_filters( 'mla_gallery_final_content', $content );
			if ( ! empty( $content ) ) {
				return $output . do_shortcode( sprintf( '[%1$s %2$s %3$s]%4$s[/%5$s]', $arguments['mla_alt_shortcode'], $new_ids, $new_args, $content, $arguments['mla_alt_shortcode'] ) );
			} else {
				return $output . do_shortcode( sprintf( '[%1$s %2$s %3$s]', $arguments['mla_alt_shortcode'], $new_ids, $new_args ) );
			}
		} // mla_alt_shortcode

		/*
		 * Look for Photonic-enhanced gallery
		 */
		global $photonic;

		if ( is_object( $photonic ) && ! empty( $arguments['style'] ) ) {
			if ( 'default' != strtolower( $arguments['type'] ) )  {
				return '<p>' . __( '<strong>Photonic-enhanced [mla_gallery]</strong> type must be <strong>default</strong>, query = ', 'media-library-assistant' ) . var_export( $attr, true ) . '</p>';
			}

			$images = array();
			foreach ($attachments as $key => $val) {
				$images[$val->ID] = $attachments[$key];
			}

			if ( isset( $arguments['pause'] ) && ( 'false' == $arguments['pause'] ) ) {
				$arguments['pause'] = NULL;
			}

			$output = $photonic->build_gallery( $images, $arguments['style'], $arguments );
			return $output;
		}

		$size = $size_class = $arguments['size'];
		if ( 'icon' == strtolower( $size) ) {
			if ( 'checked' == MLACore::mla_get_option( MLACore::MLA_ENABLE_MLA_ICONS ) ) {
				$size = array( 64, 64 );
			} else {
				$size = array( 60, 60 );
			}

			$show_icon = true;
		} else {
			$show_icon = false;
		}

		/*
		 * Feeds such as RSS, Atom or RDF do not require styled and formatted output
		 */
		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment )
				$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
			return $output;
		}

		/*
		 * Check for Imagick thumbnail generation arguments
		 */
		if ( 'checked' == MLACore::mla_get_option( 'enable_mla_viewer' ) ) {
			if ( ! empty( $arguments['mla_viewer'] ) && ( 'single' == strtolower( $arguments['mla_viewer'] ) ) ) {
				$arguments['mla_single_thread'] = true;	
				$arguments['mla_viewer'] = 'true';
			}

			$arguments['mla_viewer'] = !empty( $arguments['mla_viewer'] ) && ( 'true' == strtolower( $arguments['mla_viewer'] ) );
		} else {
			$arguments['mla_viewer'] = false;
		}

		if ( $arguments['mla_viewer'] ) {
			/*
			 * Test for Ghostscript here so debug messages can be recorded
			 */
			$ghostscript_path = MLACore::mla_get_option( 'ghostscript_path' );
			if ( self::mla_ghostscript_present( $ghostscript_path ) ) {
				$arguments['mla_viewer_extensions'] = array_filter( array_map( 'trim', explode( ',', $arguments['mla_viewer_extensions'] ) ) );
			} else {
				$arguments['mla_viewer_extensions'] = array();
			}

			// convert limit (in MB) to float
			$arguments['mla_viewer_limit'] = abs( 0.0 + $arguments['mla_viewer_limit'] );

			$arguments['mla_viewer_width'] = absint( $arguments['mla_viewer_width'] );
			$arguments['mla_viewer_height'] = absint( $arguments['mla_viewer_height'] );
			$arguments['mla_viewer_page'] = absint( $arguments['mla_viewer_page'] );

			if ( isset( $arguments['mla_viewer_best_fit'] ) ) {
				$arguments['mla_viewer_best_fit'] = 'true' == strtolower( $arguments['mla_viewer_best_fit'] );
			}

			$arguments['mla_viewer_resolution'] = absint( $arguments['mla_viewer_resolution'] );
			$arguments['mla_viewer_quality'] = absint( $arguments['mla_viewer_quality'] );
		}

		/*
		 * The default MLA style template includes "margin: 1.5%" to put a bit of
		 * minimum space between the columns. "mla_margin" can be used to change
		 * this. "mla_itemwidth" can be used with "columns=0" to achieve a "responsive"
		 * layout.
		 */
		 
		$columns = absint( $arguments['columns'] );
		$margin_string = strtolower( trim( $arguments['mla_margin'] ) );

		if ( is_numeric( $margin_string ) && ( 0 != $margin_string) ) {
			$margin_string .= '%'; // Legacy values are always in percent
		}

		if ( '%' == substr( $margin_string, -1 ) ) {
			$margin_percent = (float) substr( $margin_string, 0, strlen( $margin_string ) - 1 );
		} else {
			$margin_percent = 0;
		}

		$width_string = strtolower( trim( $arguments['mla_itemwidth'] ) );
		if ( 'none' != $width_string ) {
			switch ( $width_string ) {
				case 'exact':
					$margin_percent = 0;
					// fallthru
				case 'calculate':
					$width_string = $columns > 0 ? (floor(1000/$columns)/10) - ( 2.0 * $margin_percent ) : 100 - ( 2.0 * $margin_percent );
					// fallthru
				default:
					if ( is_numeric( $width_string ) && ( 0 != $width_string) ) {
						$width_string .= '%'; // Legacy values are always in percent
					}
			}
		} // $use_width

		$float = strtolower( $arguments['mla_float'] );
		if ( ! in_array( $float, array( 'left', 'none', 'right' ) ) ) {
			$float = is_rtl() ? 'right' : 'left';
		}

		$style_values = array_merge( $page_values, array(
			'mla_style' => $arguments['mla_style'],
			'mla_markup' => $arguments['mla_markup'],
			'itemtag' => tag_escape( $arguments['itemtag'] ),
			'icontag' => tag_escape( $arguments['icontag'] ),
			'captiontag' => tag_escape( $arguments['captiontag'] ),
			'columns' => $columns,
			'itemwidth' => $width_string,
			'margin' => $margin_string,
			'float' => $float,
			'size_class' => sanitize_html_class( $size_class )
		) );

		$style_template = $gallery_style = '';

		if ( 'theme' == strtolower( $style_values['mla_style'] ) ) {
			$use_mla_gallery_style = apply_filters( 'use_default_gallery_style', ! $html5 );
		} else {
			$use_mla_gallery_style = ( 'none' != strtolower( $style_values['mla_style'] ) );
		}

		if ( apply_filters( 'use_mla_gallery_style', $use_mla_gallery_style, $style_values['mla_style'] ) ) {
			$style_template = MLAShortcode_support::mla_fetch_gallery_template( $style_values['mla_style'], 'style' );
			if ( empty( $style_template ) ) {
				$style_values['mla_style'] = $default_arguments['mla_style'];
				$style_template = MLAShortcode_support::mla_fetch_gallery_template( $style_values['mla_style'], 'style' );
				if ( empty( $style_template ) ) {
					$style_values['mla_style'] = 'default';
					$style_template = MLAShortcode_support::mla_fetch_gallery_template( 'default', 'style' );
				}
			}

			if ( ! empty ( $style_template ) ) {
				/*
				 * Look for 'query' and 'request' substitution parameters
				 */
				$style_values = MLAData::mla_expand_field_level_parameters( $style_template, $attr, $style_values );

				/*
				 * Clean up the template to resolve width or margin == 'none'
				 */
				if ( 'none' == $margin_string ) {
					$style_values['margin'] = '0';
					$style_template = preg_replace( '/margin:[\s]*\[\+margin\+\][\%]*[\;]*/', '', $style_template );
				}

				if ( 'none' == $width_string ) {
					$style_values['itemwidth'] = 'auto';
					$style_template = preg_replace( '/width:[\s]*\[\+itemwidth\+\][\%]*[\;]*/', '', $style_template );
				}

				$style_values = apply_filters( 'mla_gallery_style_values', $style_values );
				$style_template = apply_filters( 'mla_gallery_style_template', $style_template );
				$gallery_style = MLAData::mla_parse_template( $style_template, $style_values );
				$gallery_style = apply_filters( 'mla_gallery_style_parse', $gallery_style, $style_template, $style_values );

				/*
				 * Clean up the styles to resolve extra "%" suffixes on width or margin (pre v1.42 values)
				 */
				$preg_pattern = array( '/([margin|width]:[^\%]*)\%\%/', '/([margin|width]:.*)auto\%/', '/([margin|width]:.*)inherit\%/' );
				$preg_replacement = array( '${1}%', '${1}auto', '${1}inherit',  );
				$gallery_style = preg_replace( $preg_pattern, $preg_replacement, $gallery_style );
			} // !empty template
		} // use_mla_gallery_style

		$markup_values = $style_values;

		$open_template = MLAShortcode_support::mla_fetch_gallery_template( $markup_values['mla_markup'] . '-open', 'markup' );
		if ( false === $open_template ) {
			$markup_values['mla_markup'] = $default_arguments['mla_markup'];
			$open_template = MLAShortcode_support::mla_fetch_gallery_template( $markup_values['mla_markup'] . '-open', 'markup' );
			if ( false === $open_template ) {
				$markup_values['mla_markup'] = 'default';
				$open_template = MLAShortcode_support::mla_fetch_gallery_template( $markup_values['mla_markup'] . '-open', 'markup' );
			}
		}
		if ( empty( $open_template ) ) {
			$open_template = '';
		}

		/*
		 * Emulate [gallery] handling of row open markup for the default template only
		 */
		if ( $html5 && ( 'default' == $markup_values['mla_markup'] ) ) {
			$row_open_template = '';
		} else{
			$row_open_template = MLAShortcode_support::mla_fetch_gallery_template( $markup_values['mla_markup'] . '-row-open', 'markup' );

			if ( empty( $row_open_template ) ) {
				$row_open_template = '';
			}
		}

		$item_template = MLAShortcode_support::mla_fetch_gallery_template( $markup_values['mla_markup'] . '-item', 'markup' );
		if ( empty( $item_template ) ) {
			$item_template = '';
		}

		/*
		 * Emulate [gallery] handling of row close markup for the default template only
		 */
		if ( $html5 && ( 'default' == $markup_values['mla_markup'] ) ) {
			$row_close_template = '';
		} else{
			$row_close_template = MLAShortcode_support::mla_fetch_gallery_template( $markup_values['mla_markup'] . '-row-close', 'markup' );

			if ( empty( $row_close_template ) ) {
				$row_close_template = '';
			}
		}

		$close_template = MLAShortcode_support::mla_fetch_gallery_template( $markup_values['mla_markup'] . '-close', 'markup' );
		if ( empty( $close_template ) ) {
			$close_template = '';
		}

		/*
		 * Look for gallery-level markup substitution parameters
		 */
		$new_text = $open_template . $row_open_template . $row_close_template . $close_template;

		$markup_values = MLAData::mla_expand_field_level_parameters( $new_text, $attr, $markup_values );
		if ( self::$mla_debug ) {
			$output = MLACore::mla_debug_flush();
		} else {
			$output = '';
		}

		if ($is_gallery ) {
			$markup_values = apply_filters( 'mla_gallery_open_values', $markup_values );

			$open_template = apply_filters( 'mla_gallery_open_template', $open_template );
			if ( empty( $open_template ) ) {
				$gallery_open = '';
			} else {
				$gallery_open = MLAData::mla_parse_template( $open_template, $markup_values );
			}

			$gallery_open = apply_filters( 'mla_gallery_open_parse', $gallery_open, $open_template, $markup_values );
			$output .= apply_filters( 'mla_gallery_style', $gallery_style . $gallery_open, $style_values, $markup_values, $style_template, $open_template );
		} else {
			if ( ! isset( $attachments['found_rows'] ) ) {
				$attachments['found_rows'] = 0;
			}

			/*
			 * Handle 'previous_page', 'next_page', and 'paginate_links'
			 */
			$pagination_result = self::_process_pagination_output_types( $output_parameters, $markup_values, $arguments, $attr, $attachments['found_rows'], $output );
			if ( false !== $pagination_result ) {
				return $pagination_result;
			}

			unset( $attachments['found_rows'] );
		}

		/*
		 * For "previous_link", "current_link" and "next_link",
		 * discard all of the $attachments except the appropriate choice
		 */
		if ( ! $is_gallery ) {
			$link_type = $output_parameters[0];

			if ( ! in_array( $link_type, array ( 'previous_link', 'current_link', 'next_link' ) ) ) {
				return ''; // unknown output type
			}

			$is_wrap = isset( $output_parameters[1] ) && 'wrap' == $output_parameters[1];
			$current_id = empty( $arguments['id'] ) ? $markup_values['id'] : $arguments['id'];

			foreach ( $attachments as $id => $attachment ) {
				if ( $attachment->ID == $current_id ) {
					break;
				}
			}

			switch ( $link_type ) {
				case 'previous_link':
					$target_id = $id - 1;
					break;
				case 'next_link':
					$target_id = $id + 1;
					break;
				case 'current_link':
				default:
					$target_id = $id;
			} // link_type

			$target = NULL;
			if ( isset( $attachments[ $target_id ] ) ) {
				$target = $attachments[ $target_id ];
			} elseif ( $is_wrap ) {
				switch ( $link_type ) {
					case 'previous_link':
						$target = array_pop( $attachments );
						break;
					case 'next_link':
						$target = array_shift( $attachments );
				} // link_type
			} // is_wrap

			if ( isset( $target ) ) {
				$attachments = array( $target );			
			} elseif ( ! empty( $arguments['mla_nolink_text'] ) ) {
				return self::_process_shortcode_parameter( $arguments['mla_nolink_text'], $markup_values ) . '</a>';
			} else {
				return '';
			}
		} else { // ! is_gallery
			$link_type= '';
		}

		$column_index = 0;
		foreach ( $attachments as $id => $attachment ) {
			$item_values = $markup_values;

			/*
			 * fill in item-specific elements
			 */
			$item_values['index'] = (string) 1 + $column_index;

			$item_values['excerpt'] = wptexturize( $attachment->post_excerpt );
			$item_values['attachment_ID'] = $attachment->ID;
			$item_values['mime_type'] = $attachment->post_mime_type;
			$item_values['menu_order'] = $attachment->menu_order;
			$item_values['date'] = $attachment->post_date;
			$item_values['modified'] = $attachment->post_modified;
			$item_values['parent'] = $attachment->post_parent;
			$item_values['parent_name'] = '';
			$item_values['parent_type'] = '';
			$item_values['parent_title'] = '(' . __( 'Unattached', 'media-library-assistant' ) . ')';
			$item_values['parent_date'] = '';
			$item_values['parent_permalink'] = '';
			$item_values['title'] = wptexturize( $attachment->post_title );
			$item_values['slug'] = wptexturize( $attachment->post_name );
			$item_values['width'] = '';
			$item_values['height'] = '';
			$item_values['orientation'] = '';
			$item_values['image_meta'] = '';
			$item_values['image_alt'] = '';
			$item_values['base_file'] = '';
			$item_values['path'] = '';
			$item_values['file'] = '';
			$item_values['description'] = wptexturize( $attachment->post_content );
			$item_values['file_url'] = wptexturize( $attachment->guid );
			$item_values['author_id'] = $attachment->post_author;
			$item_values['caption'] = '';
			$item_values['captiontag_content'] = '';

			$user = get_user_by( 'id', $attachment->post_author );
			if ( isset( $user->data->display_name ) ) {
				$item_values['author'] = wptexturize( $user->data->display_name );
			} else {
				$item_values['author'] = __( 'unknown', 'media-library-assistant' );
			}

			$post_meta = MLAQuery::mla_fetch_attachment_metadata( $attachment->ID );
			$base_file = $post_meta['mla_wp_attached_file'];
			$sizes = isset( $post_meta['mla_wp_attachment_metadata']['sizes'] ) ? $post_meta['mla_wp_attachment_metadata']['sizes'] : array();

			if ( !empty( $post_meta['mla_wp_attachment_metadata']['width'] ) ) {
				$item_values['width'] = $post_meta['mla_wp_attachment_metadata']['width'];
				$width = absint( $item_values['width'] );
			} else {
				$width = 0;
			}

			if ( !empty( $post_meta['mla_wp_attachment_metadata']['height'] ) ) {
				$item_values['height'] = $post_meta['mla_wp_attachment_metadata']['height'];
				$height = absint( $item_values['height'] );
			} else {
				$height = 0;
			}

			if ( $width && $height ) {
				$item_values['orientation'] = ( $height > $width ) ? 'portrait' : 'landscape';
			}

			if ( !empty( $post_meta['mla_wp_attachment_metadata']['image_meta'] ) ) {
				$item_values['image_meta'] = wptexturize( var_export( $post_meta['mla_wp_attachment_metadata']['image_meta'], true ) );
			}

			if ( !empty( $post_meta['mla_wp_attachment_image_alt'] ) ) {
				if ( is_array( $post_meta['mla_wp_attachment_image_alt'] ) ) {
					$item_values['image_alt'] = wptexturize( $post_meta['mla_wp_attachment_image_alt'][0] );
				} else {
					$item_values['image_alt'] = wptexturize( $post_meta['mla_wp_attachment_image_alt'] );
				}
			}

			if ( ! empty( $base_file ) ) {
				$last_slash = strrpos( $base_file, '/' );
				if ( false === $last_slash ) {
					$file_name = $base_file;
					$item_values['base_file'] = wptexturize( $base_file );
					$item_values['file'] = wptexturize( $base_file );
				} else {
					$file_name = substr( $base_file, $last_slash + 1 );
					$item_values['base_file'] = wptexturize( $base_file );
					$item_values['path'] = wptexturize( substr( $base_file, 0, $last_slash + 1 ) );
					$item_values['file'] = wptexturize( $file_name );
				}
			} else {
				$file_name = '';
			}

			if ( 0 < $attachment->post_parent ) {
				$parent_info = MLAQuery::mla_fetch_attachment_parent_data( $attachment->post_parent );
				if ( isset( $parent_info['parent_name'] ) ) {
					$item_values['parent_name'] = $parent_info['parent_name'];
				}

				if ( isset( $parent_info['parent_type'] ) ) {
					$item_values['parent_type'] = wptexturize( $parent_info['parent_type'] );
				}

				if ( isset( $parent_info['parent_title'] ) ) {
					$item_values['parent_title'] = wptexturize( $parent_info['parent_title'] );
				}

				if ( isset( $parent_info['parent_date'] ) ) {
					$item_values['parent_date'] = wptexturize( $parent_info['parent_date'] );
				}

				$permalink = get_permalink( $attachment->post_parent );
				if ( false !== $permalink ) {
					$item_values['parent_permalink'] = $permalink;
				}
			} // has parent

			/*
			 * Add attachment-specific field-level substitution parameters
			 */
			$new_text = isset( $item_template ) ? $item_template : '';
			foreach( $mla_item_specific_arguments as $index => $value ) {
				$new_text .= str_replace( '{+', '[+', str_replace( '+}', '+]', $arguments[ $index ] ) );
			}
			$item_values = MLAData::mla_expand_field_level_parameters( $new_text, $attr, $item_values, $attachment->ID );

			if ( $item_values['captiontag'] ) {
				$item_values['caption'] = wptexturize( $attachment->post_excerpt );
				if ( ! empty( $arguments['mla_caption'] ) ) {
					$item_values['caption'] = wptexturize( self::_process_shortcode_parameter( $arguments['mla_caption'], $item_values ) );
				}
			} else {
				$item_values['caption'] = '';
			}

			if ( ! empty( $arguments['mla_link_text'] ) ) {
				$link_text = self::_process_shortcode_parameter( $arguments['mla_link_text'], $item_values );
			} else {
				$link_text = false;
			}

			/*
			 * As of WP 3.7, this function returns "<a href='$url'>$link_text</a>", where
			 * $link_text can be an image thumbnail or a text link. The "title=" attribute
			 * was dropped. The function is defined in /wp-includes/post-template.php.
			 *
			 * As of WP 4.1, this function has an additional optional parameter, an "Array or
			 * string of attributes", used in the [gallery] shortcode to tie the link to a
			 * caption with 'aria-describedby'. The caption has a matching 'id' attribute
			 * "$selector-#id". See below for the MLA equivalent processing.
			 */
			$item_values['pagelink'] = wp_get_attachment_link($attachment->ID, $size, true, $show_icon, $link_text);
			$item_values['filelink'] = wp_get_attachment_link($attachment->ID, $size, false, $show_icon, $link_text);

			if ( in_array( $attachment->post_mime_type, array( 'image/svg+xml' ) ) ) {
				$registered_dimensions = self::_registered_dimensions();
				if ( isset( $registered_dimensions[ $size_class ] ) ) {
					$dimensions = $registered_dimensions[ $size_class ];
				} else {
					$dimensions = $registered_dimensions['thumbnail'];
				}

				$thumb = preg_replace( '/width=\"[^\"]*\"/', sprintf( 'width="%1$d"', $dimensions[1] ), $item_values['pagelink'] );
				$item_values['pagelink'] = preg_replace( '/height=\"[^\"]*\"/', sprintf( 'height="%1$d"', $dimensions[0] ), $thumb );
				$thumb = preg_replace( '/width=\"[^\"]*\"/', sprintf( 'width="%1$d"', $dimensions[1] ), $item_values['filelink'] );
				$item_values['filelink'] = preg_replace( '/height=\"[^\"]*\"/', sprintf( 'height="%1$d"', $dimensions[0] ), $thumb );
			} // SVG thumbnail

			/*
			 * Apply the Gallery Display Content parameters.
			 * Note that $link_attributes and $rollover_text
			 * are used in the Google Viewer code below
			 */
			$link_attributes = '';
			if ( ! empty( $arguments['mla_rollover_text'] ) ) {
				$rollover_text = esc_attr( self::_process_shortcode_parameter( $arguments['mla_rollover_text'], $item_values ) );

				/*
				 * The "title=" attribute was removed in WP 3.7+, but look for it anyway.
				 * If it's not there, add the "title=" value to the link attributes.
				 */
				if ( false === strpos( $item_values['pagelink'], ' title=' ) ) {
					$link_attributes .= 'title="' . $rollover_text . '" ';
				}else {
					/*
					 * Replace single- and double-quote delimited values
					 */
					$item_values['pagelink'] = preg_replace('# title=\'([^\']*)\'#', " title='{$rollover_text}'", $item_values['pagelink'] );
					$item_values['pagelink'] = preg_replace('# title=\"([^\"]*)\"#', " title=\"{$rollover_text}\"", $item_values['pagelink'] );
					$item_values['filelink'] = preg_replace('# title=\'([^\']*)\'#', " title='{$rollover_text}'", $item_values['filelink'] );
					$item_values['filelink'] = preg_replace('# title=\"([^\"]*)\"#', " title=\"{$rollover_text}\"", $item_values['filelink'] );
				}
			} else {
				$rollover_text = esc_attr( $item_values['title'] );
			}

			if ( ! empty( $arguments['mla_target'] ) ) {
				$link_attributes .= 'target="' . $arguments['mla_target'] . '" ';
			}

			if ( ! empty( $arguments['mla_link_attributes'] ) ) {
				$link_attributes .= self::_process_shortcode_parameter( $arguments['mla_link_attributes'], $item_values ) . ' ';
			}

			if ( ! empty( $arguments['mla_link_class'] ) ) {
				$link_attributes .= 'class="' . self::_process_shortcode_parameter( $arguments['mla_link_class'], $item_values ) . '" ';
			}

			if ( ! empty( $link_attributes ) ) {
				$item_values['pagelink'] = str_replace( '<a href=', '<a ' . $link_attributes . 'href=', $item_values['pagelink'] );
				$item_values['filelink'] = str_replace( '<a href=', '<a ' . $link_attributes . 'href=', $item_values['filelink'] );
			}

			/*
			 * Process the <img> tag, if present
			 * Note that $image_attributes, $image_class and $image_alt
			 * are used in the Google Viewer code below
			 */
			if ( ! empty( $arguments['mla_image_attributes'] ) ) {
				$image_attributes = self::_process_shortcode_parameter( $arguments['mla_image_attributes'], $item_values ) . ' ';
			} else {
				$image_attributes = '';
			}

			/*
			 * WordPress 4.1 ties the <img> tag to the caption with 'aria-describedby'
			 * has a matching 'id' attribute "$selector-#id".
			 */
			if ( trim( $item_values['caption'] ) && ( false === strpos( $image_attributes, 'aria-describedby=' ) ) && ( 'default' == $item_values['mla_markup'] ) ) {
				$image_attributes .= 'aria-describedby="' . $item_values['selector'] . '-' . $item_values['attachment_ID'] . '" ';
			}

			if ( ! empty( $arguments['mla_image_class'] ) ) {
				$image_class = esc_attr( self::_process_shortcode_parameter( $arguments['mla_image_class'], $item_values ) );
			} else {
				$image_class = '';
			}

			if ( ! empty( $arguments['mla_image_alt'] ) ) {
				$image_alt = esc_attr( self::_process_shortcode_parameter( $arguments['mla_image_alt'], $item_values ) );
			} else {
				$image_alt = '';
			}

			/*
			 * Look for alt= and class= attributes in $image_attributes. If found,
			 * they override and completely replace the corresponding values.
			 */
			$class_replace = false;
			if ( ! empty( $image_attributes ) ) {
				$match_count = preg_match( '#alt=(([\'\"])([^\']+?)\2)#', $image_attributes, $matches, PREG_OFFSET_CAPTURE );
				if ( 1 === $match_count ) {
					$image_alt = $matches[3][0];
					$image_attributes = substr_replace( $image_attributes, '', $matches[0][1], strlen( $matches[0][0] ) );
				}

				$match_count = preg_match( '#class=(([\'\"])([^\']+?)\2)#', $image_attributes, $matches, PREG_OFFSET_CAPTURE );
				if ( 1 === $match_count ) {
					$class_replace = true;
					$image_class = $matches[3][0];
					$image_attributes = substr_replace( $image_attributes, '', $matches[0][1], strlen( $matches[0][0] ) );
				}

				$image_attributes = trim( $image_attributes );
				if ( ! empty( $image_attributes ) ) {
					$image_attributes .= ' ';
				}
			}

			if ( false !== strpos( $item_values['pagelink'], '<img ' ) ) {
				if ( ! empty( $image_attributes ) ) {
					$item_values['pagelink'] = str_replace( '<img ', '<img ' . $image_attributes, $item_values['pagelink'] );
					$item_values['filelink'] = str_replace( '<img ', '<img ' . $image_attributes, $item_values['filelink'] );
				}

				/*
				 * Extract existing class values and add to them
				 */
				if ( ! empty( $image_class ) ) {
					$match_count = preg_match_all( '# class=\"([^\"]+)\" #', $item_values['pagelink'], $matches, PREG_OFFSET_CAPTURE );
					if ( ! ( $class_replace || ( $match_count == false ) || ( $match_count == 0 ) ) ) {
						$class = $matches[1][0][0] . ' ' . $image_class;
					} else {
						$class = $image_class;
					}

					$item_values['pagelink'] = preg_replace('# class=\"([^\"]*)\"#', " class=\"{$class}\"", $item_values['pagelink'] );
					$item_values['filelink'] = preg_replace('# class=\"([^\"]*)\"#', " class=\"{$class}\"", $item_values['filelink'] );
				}

				if ( ! empty( $image_alt ) ) {
					$item_values['pagelink'] = preg_replace('# alt=\"([^\"]*)\"#', " alt=\"{$image_alt}\"", $item_values['pagelink'] );
					$item_values['filelink'] = preg_replace('# alt=\"([^\"]*)\"#', " alt=\"{$image_alt}\"", $item_values['filelink'] );
				}
			} // process <img> tag

			switch ( $arguments['link'] ) {
				case 'permalink':
				case 'post':
					$item_values['link'] = $item_values['pagelink'];
					break;
				case 'file':
				case 'full':
					$item_values['link'] = $item_values['filelink'];
					break;
				default:
					$item_values['link'] = $item_values['filelink'];

					/*
					 * Check for link to specific (registered) file size
					 */
					if ( array_key_exists( $arguments['link'], $sizes ) ) {
						$target_file = $sizes[ $arguments['link'] ]['file'];
						$item_values['link'] = str_replace( $file_name, $target_file, $item_values['filelink'] );
					}
			} // switch 'link'

			/*
			 * Extract target and thumbnail fields
			 */
			$match_count = preg_match_all( '#href=\'([^\']+)\'#', $item_values['pagelink'], $matches, PREG_OFFSET_CAPTURE );
 			if ( ! ( ( $match_count == false ) || ( $match_count == 0 ) ) ) {
				$item_values['pagelink_url'] = $matches[1][0][0];
			} else {
				$item_values['pagelink_url'] = '';
			}

			$match_count = preg_match_all( '#href=\'([^\']+)\'#', $item_values['filelink'], $matches, PREG_OFFSET_CAPTURE );
			if ( ! ( ( $match_count == false ) || ( $match_count == 0 ) ) ) {
				$item_values['filelink_url'] = $matches[1][0][0];
			} else {
				$item_values['filelink_url'] = '';
			}

			$match_count = preg_match_all( '#href=\'([^\']+)\'#', $item_values['link'], $matches, PREG_OFFSET_CAPTURE );
			if ( ! ( ( $match_count == false ) || ( $match_count == 0 ) ) ) {
				$item_values['link_url'] = $matches[1][0][0];
			} else {
				$item_values['link_url'] = '';
			}

			/*
			 * Override the link value; leave filelink and pagelink unchanged
			 * Note that $link_href is used in the Google Viewer code below
			 */
			if ( ! empty( $arguments['mla_link_href'] ) ) {
				$link_href = self::_process_shortcode_parameter( $arguments['mla_link_href'], $item_values );

				/*
				 * Replace single- and double-quote delimited values
				 */
				$item_values['link'] = preg_replace('# href=\'([^\']*)\'#', " href='{$link_href}'", $item_values['link'] );
				$item_values['link'] = preg_replace('# href=\"([^\"]*)\"#', " href=\"{$link_href}\"", $item_values['link'] );
			} else {
				$link_href = '';
			}

			$match_count = preg_match_all( '#(\<a [^\>]+\>)(.*)\</a\>#', $item_values['link'], $matches, PREG_OFFSET_CAPTURE );
			if ( ! ( ( $match_count == false ) || ( $match_count == 0 ) ) ) {
				$link_tag = $matches[1][0][0];
				$item_values['thumbnail_content'] = $matches[2][0][0];
			} else {
				$item_values['thumbnail_content'] = '';
			}

			$match_count = preg_match_all( '# width=\"([^\"]+)\" height=\"([^\"]+)\" src=\"([^\"]+)\" #', $item_values['link'], $matches, PREG_OFFSET_CAPTURE );
			if ( ! ( ( $match_count == false ) || ( $match_count == 0 ) ) ) {
				$item_values['thumbnail_width'] = $matches[1][0][0];
				$item_values['thumbnail_height'] = $matches[2][0][0];
				$item_values['thumbnail_url'] = $matches[3][0][0];
			} else {
				$item_values['thumbnail_width'] = '';
				$item_values['thumbnail_height'] = '';
				$item_values['thumbnail_url'] = '';

				if ( ( 'none' !== $arguments['size'] ) && ( 'checked' == MLACore::mla_get_option( 'enable_featured_image' ) ) ) {
					/*
					 * Look for the "Featured Image" as an alternate thumbnail for PDFs, etc.
					 */
					$feature = get_the_post_thumbnail( $attachment->ID, $size, array( 'class' => 'attachment-thumbnail' ) );
					if ( ! empty( $feature ) ) {
						$match_count = preg_match_all( '# width=\"([^\"]+)\" height=\"([^\"]+)\" src=\"([^\"]+)\" #', $feature, $matches, PREG_OFFSET_CAPTURE );
						if ( ! ( ( $match_count == false ) || ( $match_count == 0 ) ) ) {
							$item_values['link'] = $link_tag . $feature . '</a>';
							$item_values['thumbnail_content'] = $feature;
							$item_values['thumbnail_width'] = $matches[1][0][0];
							$item_values['thumbnail_height'] = $matches[2][0][0];
							$item_values['thumbnail_url'] = $matches[3][0][0];
						}
					}
				} // enable_featured_image
			}

			/*
			 * Now that we have thumbnail_content we can check for 'span' and 'none'
			 */
			if ( 'none' == $arguments['link'] ) {
				$item_values['link'] = $item_values['thumbnail_content'];
			} elseif ( 'span' == $arguments['link'] ) {
				$item_values['link'] = sprintf( '<span %1$s>%2$s</span>', $link_attributes, $item_values['thumbnail_content'] );
			}

			/*
			 * Check for Imagick thumbnail generation, uses above-defined
			 * $link_attributes (includes target), $rollover_text, $link_href (link only),
			 * $image_attributes, $image_class, $image_alt
			 */
			if ( $arguments['mla_viewer'] && empty( $item_values['thumbnail_url'] ) ) {
				/*
				 * Check for a match on file extension
				 */
				$last_dot = strrpos( $item_values['file'], '.' );
				if ( !( false === $last_dot) ) {
					$extension = substr( $item_values['file'], $last_dot + 1 );
					if ( in_array( $extension, $arguments['mla_viewer_extensions'] ) ) {
						/*
						 * Default to an icon if thumbnail generation is not available
						 */
						$icon_url = wp_mime_type_icon( $attachment->ID );
						$upload_dir = wp_upload_dir();
						$args = array(
							'page' => MLACore::ADMIN_PAGE_SLUG,
							'mla_stream_file' => urlencode( $upload_dir['basedir'] . '/' . $item_values['base_file'] ),
						);

						if ( 'log' == $arguments['mla_debug'] ) {
							$args['mla_debug'] = 'log';
						}

						if ( $arguments['mla_single_thread'] ) {
							$args['mla_single_thread'] = 'true';
						}

						if ( $arguments['mla_viewer_width'] ) {
							$args['mla_stream_width'] = $arguments['mla_viewer_width'];
						}

						if ( $arguments['mla_viewer_height'] ) {
							$args['mla_stream_height'] = $arguments['mla_viewer_height'];
						}

						if ( isset( $arguments['mla_viewer_best_fit'] ) ) {
							$args['mla_stream_fit'] = $arguments['mla_viewer_best_fit'] ? '1' : '0';
						}

						/*
						 * Non-standard location, if not empty. Write the value to a file that can be
						 * found by the stand-alone (no WordPress) image stream processor.
						 */
						$ghostscript_path = MLACore::mla_get_option( 'ghostscript_path' );
						if ( ! empty( $ghostscript_path ) ) {
							if ( false !== @file_put_contents( dirname( __FILE__ ) . '/' . 'mla-ghostscript-path.txt', $ghostscript_path ) ) {
								$args['mla_ghostscript_path'] = 'custom';
							}
						}

						if ( self::mla_ghostscript_present( $ghostscript_path ) ) {
							/*
							 * Optional upper limit (in MB) on file size
							 */
							if ( $limit = ( 1024 * 1024 ) * $arguments['mla_viewer_limit'] ) {
								$file_size = 0 + @filesize( $item_values['base_dir'] . '/' . $item_values['base_file'] );
								if ( ( 0 < $file_size ) && ( $file_size > $limit ) ) {
									$file_size = 0;
								}
							} else {
								$file_size = 1;
							}

							/*
							 * Generate "real" thumbnail
							 */
							if ( $file_size ) {
								$frame = ( 0 < $arguments['mla_viewer_page'] ) ? $arguments['mla_viewer_page'] - 1 : 0;
								if ( $frame ) {
									$args['mla_stream_frame'] = $frame;
								}

								if ( $arguments['mla_viewer_resolution'] ) {
									$args['mla_stream_resolution'] = $arguments['mla_viewer_resolution'];
								}

								if ( $arguments['mla_viewer_quality'] ) {
									$args['mla_stream_quality'] = $arguments['mla_viewer_quality'];
								}

								if ( ! empty( $arguments['mla_viewer_type'] ) ) {
									$args['mla_stream_type'] = $arguments['mla_viewer_type'];
								}

								/*
								 * For efficiency, image streaming is done outside WordPress
								 */
								$icon_url = add_query_arg( $args, wp_nonce_url( MLA_PLUGIN_URL . 'includes/mla-stream-image.php', MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) );
							}
						}

						/*
						 * <img> tag (thumbnail_text)
						 */
						if ( ! empty( $image_class ) ) {
							$image_class = ' class="' . $image_class . '"';
						}

						if ( ! empty( $image_alt ) ) {
							$image_alt = ' alt="' . $image_alt . '"';
						} elseif ( ! empty( $item_values['caption'] ) ) {
							$image_alt = ' alt="' . $item_values['caption'] . '"';
						}

						$item_values['thumbnail_content'] = sprintf( '<img %1$ssrc="%2$s"%3$s%4$s>', $image_attributes, $icon_url, $image_class, $image_alt );

						/*
						 * Filelink, pagelink and link.
						 * The "title=" attribute is in $link_attributes for WP 3.7+.
						 */
						if ( false === strpos( $link_attributes, 'title=' ) ) {
							$item_values['pagelink'] = sprintf( '<a %1$shref="%2$s" title="%3$s">%4$s</a>', $link_attributes, $item_values['pagelink_url'], $rollover_text, $item_values['thumbnail_content'] );
							$item_values['filelink'] = sprintf( '<a %1$shref="%2$s" title="%3$s">%4$s</a>', $link_attributes, $item_values['filelink_url'], $rollover_text, $item_values['thumbnail_content'] );
						} else {
							$item_values['pagelink'] = sprintf( '<a %1$shref="%2$s">%3$s</a>', $link_attributes, $item_values['pagelink_url'], $item_values['thumbnail_content'] );
							$item_values['filelink'] = sprintf( '<a %1$shref="%2$s">%3$s</a>', $link_attributes, $item_values['filelink_url'], $item_values['thumbnail_content'] );
						}
						if ( ! empty( $link_href ) ) {
							$item_values['link'] = sprintf( '<a %1$shref="%2$s" title="%3$s">%4$s</a>', $link_attributes, $link_href, $rollover_text, $item_values['thumbnail_content'] );
						} elseif ( 'permalink' == $arguments['link'] ) {
							$item_values['link'] = $item_values['pagelink'];
						} elseif ( 'file' == $arguments['link'] ) {
							$item_values['link'] = $item_values['filelink'];
						} elseif ( 'span' == $arguments['link'] ) {
							$item_values['link'] = sprintf( '<a %1$s>%2$s</a>', $link_attributes, $item_values['thumbnail_content'] );
						} else {
							$item_values['link'] = $item_values['thumbnail_content'];
						}
					} // viewer extension
				} // has extension
			} // mla_viewer

			if ( $is_gallery ) {
				/*
				 * Start of row markup
				 */
				if ( $markup_values['columns'] > 0 && $column_index % $markup_values['columns'] == 0 ) {
					$markup_values = apply_filters( 'mla_gallery_row_open_values', $markup_values );
					$row_open_template = apply_filters( 'mla_gallery_row_open_template', $row_open_template );
					$parse_value = MLAData::mla_parse_template( $row_open_template, $markup_values );
					$output .= apply_filters( 'mla_gallery_row_open_parse', $parse_value, $row_open_template, $markup_values );
				}

				/*
				 * item markup
				 */
				$column_index++;
				if ( $item_values['columns'] > 0 && $column_index % $item_values['columns'] == 0 ) {
					$item_values['last_in_row'] = 'last_in_row';
				} else {
					$item_values['last_in_row'] = '';
				}

				/*
				 * Conditional caption tag to replicate WP 4.1+,
				 * now used in the default markup template.
				 */
				if ( $item_values['captiontag'] && trim( $item_values['caption'] ) ) {
					$item_values['captiontag_content'] = '<' . $item_values['captiontag'] . " class='wp-caption-text gallery-caption' id='" . $item_values['selector'] . '-' . $item_values['attachment_ID'] . "'>\n\t\t" . $item_values['caption'] . "\n\t</" . $item_values['captiontag'] . ">\n";
				} else {
					$item_values['captiontag_content'] = '';
				}

				$item_values = apply_filters( 'mla_gallery_item_values', $item_values );
				$item_template = apply_filters( 'mla_gallery_item_template', $item_template );
				$parse_value = MLAData::mla_parse_template( $item_template, $item_values );
				$output .= apply_filters( 'mla_gallery_item_parse', $parse_value, $item_template, $item_values );

				/*
				 * End of row markup
				 */
				if ( $markup_values['columns'] > 0 && $column_index % $markup_values['columns'] == 0 ) {
					$markup_values = apply_filters( 'mla_gallery_row_close_values', $markup_values );
					$row_close_template = apply_filters( 'mla_gallery_row_close_template', $row_close_template );
					$parse_value = MLAData::mla_parse_template( $row_close_template, $markup_values );
					$output .= apply_filters( 'mla_gallery_row_close_parse', $parse_value, $row_close_template, $markup_values );
				}
			} // is_gallery
			elseif ( ! empty( $link_type ) ) {
				return $item_values['link'];
			}
		} // foreach attachment

		if ($is_gallery ) {
			/*
			 * Close out partial row
			 */
			if ( ! ($markup_values['columns'] > 0 && $column_index % $markup_values['columns'] == 0 ) ) {
				$markup_values = apply_filters( 'mla_gallery_row_close_values', $markup_values );
				$row_close_template = apply_filters( 'mla_gallery_row_close_template', $row_close_template );
				$parse_value = MLAData::mla_parse_template( $row_close_template, $markup_values );
				$output .= apply_filters( 'mla_gallery_row_close_parse', $parse_value, $row_close_template, $markup_values );
			}

			$markup_values = apply_filters( 'mla_gallery_close_values', $markup_values );
			$close_template = apply_filters( 'mla_gallery_close_template', $close_template );
			$parse_value = MLAData::mla_parse_template( $close_template, $markup_values );
			$output .= apply_filters( 'mla_gallery_close_parse', $parse_value, $close_template, $markup_values );
		} // is_gallery

		return $output;
	}

	/**
	 * The MLA Tag Cloud support function.
	 *
	 * This is an alternative to the WordPress wp_tag_cloud function, with additional
	 * options to customize the hyperlink behind each term.
	 *
	 * @since 2.20
	 *
	 * @param array $attr Attributes of the shortcode.
	 *
	 * @return string HTML content to display the tag cloud.
	 */
	public static function mla_tag_cloud( $attr ) {
		global $post;

		/*
		 * These are the default parameters for tag cloud display
		 */
		$mla_item_specific_arguments = array(
			'mla_link_attributes' => '',
			'mla_link_class' => '',
			'mla_link_style' => '',
			'mla_link_href' => '',
			'mla_link_text' => '',
			'mla_nolink_text' => '',
			'mla_rollover_text' => '',
			'mla_caption' => ''
		);

		$mla_arguments = array_merge( array(
			'mla_output' => 'flat',
			'mla_style' => NULL,
			'mla_markup' => NULL,
			'mla_float' => is_rtl() ? 'right' : 'left',
			'mla_itemwidth' => MLACore::mla_get_option('mla_tag_cloud_itemwidth'),
			'mla_margin' => MLACore::mla_get_option('mla_tag_cloud_margin'),
			'mla_target' => '',
			'mla_debug' => false,

			// Pagination parameters
			'term_id' => NULL,
			'mla_end_size'=> 1,
			'mla_mid_size' => 2,
			'mla_prev_text' => '&laquo; ' . __( 'Previous', 'media-library-assistant' ),
			'mla_next_text' => __( 'Next', 'media-library-assistant' ) . ' &raquo;',
			'mla_page_parameter' => 'mla_cloud_current',
			'mla_cloud_current' => NULL,
			'mla_paginate_total' => NULL,
			'mla_paginate_type' => 'plain'),
			$mla_item_specific_arguments
		);

		$defaults = array_merge(
			self::$mla_get_terms_parameters,
			array(
			'smallest' => 8,
			'largest' => 22,
			'unit' => 'pt',
			'separator' => "\n",
			'single_text' => '%d item',
			'multiple_text' => '%d items',

			'echo' => false,
			'link' => 'view',
			'current_item' => '',
			'current_item_class' => 'mla_current_item',

			'itemtag' => 'ul',
			'termtag' => 'li',
			'captiontag' => '',
			'columns' => MLACore::mla_get_option('mla_tag_cloud_columns')
			),
			$mla_arguments
		);

		/*
		 * The mla_paginate_current parameter can be changed to support multiple galleries per page.
		 */
		if ( ! isset( $attr['mla_page_parameter'] ) ) {
			$attr['mla_page_parameter'] = $defaults['mla_page_parameter'];
		}

		$mla_page_parameter = $attr['mla_page_parameter'];
		 
		/*
		 * Special handling of mla_page_parameter to make
		 * "MLA pagination" easier. Look for this parameter in $_REQUEST
		 * if it's not present in the shortcode itself.
		 */
		if ( ! isset( $attr[ $mla_page_parameter ] ) ) {
			if ( isset( $_REQUEST[ $mla_page_parameter ] ) ) {
				$attr[ $mla_page_parameter ] = $_REQUEST[ $mla_page_parameter ];
			}
		}
		 
		// $instance supports multiple clouds in one page/post	
		static $instance = 0;
		$instance++;

		/*
		 * Some values are already known, and can be used in data selection parameters
		 */
		$upload_dir = wp_upload_dir();
		$page_values = array(
			'instance' => $instance,
			'selector' => "mla_tag_cloud-{$instance}",
			'site_url' => site_url(),
			'base_url' => $upload_dir['baseurl'],
			'base_dir' => $upload_dir['basedir'],
			'id' => $post->ID,
			'page_ID' => $post->ID,
			'page_author' => $post->post_author,
			'page_date' => $post->post_date,
			'page_content' => $post->post_content,
			'page_title' => $post->post_title,
			'page_excerpt' => $post->post_excerpt,
			'page_status' => $post->post_status,
			'page_name' => $post->post_name,
			'page_modified' => $post->post_modified,
			'page_guid' => $post->guid,
			'page_type' => $post->post_type,
			'page_url' => get_page_link(),
		);

		/*
		 * Look for 'request' substitution parameters,
		 * which can be added to any input parameter
		 */
		foreach ( $attr as $attr_key => $attr_value ) {
			/*
			 * item-specific Display Content parameters must be evaluated
			 * later, when all of the information is available.
			 */
			if ( array_key_exists( $attr_key, $mla_item_specific_arguments ) ) {
				continue;
			}

			$attr_value = str_replace( '{+', '[+', str_replace( '+}', '+]', $attr_value ) );
			$replacement_values = MLAData::mla_expand_field_level_parameters( $attr_value, NULL, $page_values );
			$attr[ $attr_key ] = MLAData::mla_parse_template( $attr_value, $replacement_values );
		}

		$attr = apply_filters( 'mla_tag_cloud_attributes', $attr );
		$arguments = shortcode_atts( $defaults, $attr );

		/*
		 * $mla_page_parameter, if non-default, doesn't make it through the shortcode_atts filter,
		 * so we handle it separately
		 */
		if ( ! isset( $arguments[ $mla_page_parameter ] ) ) {
			if ( isset( $attr[ $mla_page_parameter ] ) ) {
				$arguments[ $mla_page_parameter ] = $attr[ $mla_page_parameter ];
			} else {
				$arguments[ $mla_page_parameter ] = $defaults['mla_cloud_current'];

			}
		}

		/*
		 * Process the pagination parameter, if present
		 */
		if ( isset( $arguments[ $mla_page_parameter ] ) ) {
			$arguments['offset'] = $arguments['limit'] * ( $arguments[ $mla_page_parameter ] - 1);
		}

		/*
		 * Clean up the current_item to separate term_id from slug
		 */
		if ( ! empty( $arguments['current_item'] ) && ctype_digit( $arguments['current_item'] ) ) {
			$arguments['current_item'] = absint( $arguments['current_item'] );
		}

		$arguments = apply_filters( 'mla_tag_cloud_arguments', $arguments );

		self::$mla_debug = ( ! empty( $arguments['mla_debug'] ) ) ? trim( strtolower( $arguments['mla_debug'] ) ) : false;
		if ( self::$mla_debug ) {
			if ( 'true' == self::$mla_debug ) {
				MLACore::mla_debug_mode( 'buffer' );
			} elseif ( 'log' == self::$mla_debug ) {
				MLACore::mla_debug_mode( 'log' );
			} else {
				self::$mla_debug = false;
			}
		}

		if ( self::$mla_debug ) {
			MLACore::mla_debug_add( '<strong>' . __( 'mla_debug attributes', 'media-library-assistant' ) . '</strong> = ' . var_export( $attr, true ) );
			MLACore::mla_debug_add( '<strong>' . __( 'mla_debug arguments', 'media-library-assistant' ) . '</strong> = ' . var_export( $arguments, true ) );
		}

		/*
		 * Determine output type and templates
		 */
		$output_parameters = array_map( 'strtolower', array_map( 'trim', explode( ',', $arguments['mla_output'] ) ) );

		if ( $is_grid = 'grid' == $output_parameters[0] ) {
			$default_style = MLACore::mla_get_option('default_tag_cloud_style');
			$default_markup = MLACore::mla_get_option('default_tag_cloud_markup');

			if ( NULL == $arguments['mla_style'] ) {
				$arguments['mla_style'] = $default_style;
			}

			if ( NULL == $arguments['mla_markup'] ) {
				$arguments['mla_markup'] = $default_markup;
				$arguments['itemtag'] = 'dl';
				$arguments['termtag'] = 'dt';
				$arguments['captiontag'] = 'dd';
			}
		}

		if ( $is_list = 'list' == $output_parameters[0] ) {
			$default_style = 'none';
			if ( empty( $arguments['captiontag'] ) ) {
				$default_markup = 'tag-cloud-ul';
			} else {
				$default_markup = 'tag-cloud-dl';

				if ( 'dd' == $arguments['captiontag'] ) {
					$arguments['itemtag'] = 'dl';
					$arguments['termtag'] = 'dt';
				}
			}

			if ( NULL == $arguments['mla_style'] ) {
				$arguments['mla_style'] = $default_style;
			}

			if ( NULL == $arguments['mla_markup'] ) {
				$arguments['mla_markup'] = $default_markup;
			}
		}

		$is_pagination = in_array( $output_parameters[0], array( 'previous_link', 'current_link', 'next_link', 'previous_page', 'next_page', 'paginate_links' ) ); 

		/*
		 * Convert lists to arrays
		 */
		if ( is_string( $arguments['taxonomy'] ) ) {
			$arguments['taxonomy'] = explode( ',', $arguments['taxonomy'] );
		}

		if ( is_string( $arguments['post_type'] ) ) {
			$arguments['post_type'] = explode( ',', $arguments['post_type'] );
		}

		if ( is_string( $arguments['post_status'] ) ) {
			$arguments['post_status'] = explode( ',', $arguments['post_status'] );
		}

		$tags = self::mla_get_terms( $arguments );

		if ( self::$mla_debug ) {
			$cloud = MLACore::mla_debug_flush();
		} else {
			$cloud = '';
		}

		/*
		 * Invalid taxonomy names return WP_Error
		 */
		if ( is_wp_error( $tags ) ) {
			$cloud .=  '<strong>' . __( 'ERROR', 'media-library-assistant' ) . ': ' . $tags->get_error_message() . '</strong>, ' . $tags->get_error_data( $tags->get_error_code() );

			if ( 'array' == $arguments['mla_output'] ) {
				return array( $cloud );
			}

			if ( empty($arguments['echo']) ) {
				return $cloud;
			}

			echo $cloud;
			return;
		}

		if ( empty( $tags ) ) {
			if ( self::$mla_debug ) {
				MLACore::mla_debug_add( '<strong>' . __( 'mla_debug empty cloud', 'media-library-assistant' ) . '</strong>, query = ' . var_export( $arguments, true ) );
				$cloud = MLACore::mla_debug_flush();
			}

			$cloud .= $arguments['mla_nolink_text'];
			if ( 'array' == $arguments['mla_output'] ) {
				return array( $cloud );
			}

			if ( empty($arguments['echo']) ) {
				return $cloud;
			}

			echo $cloud;
			return;
		}

		/*
		 * Fill in the item_specific link properties, calculate cloud parameters
		 */
		if ( isset( $tags['found_rows'] ) ) {
			$found_rows = $tags['found_rows'];
			unset( $tags['found_rows'] );
		} else {
			$found_rows = count( $tags );
		}

		$min_count = 0x7FFFFFFF;
		$max_count = 0;
		$min_scaled_count = 0x7FFFFFFF;
		$max_scaled_count = 0;
		foreach ( $tags as $key => $tag ) {
			$tag_count = isset ( $tag->count ) ? $tag->count : 0;
			$tag->scaled_count = apply_filters( 'mla_tag_cloud_scale', round(log10($tag_count + 1) * 100), $attr, $arguments, $tag );

			if ( $tag_count < $min_count ) {
				$min_count = $tag_count;
			}

			if ( $tag_count > $max_count ) {
				$max_count = $tag_count;
			}

			if ( $tag->scaled_count < $min_scaled_count ) {
				$min_scaled_count = $tag->scaled_count;
			}

			if ( $tag->scaled_count > $max_scaled_count ) {
				$max_scaled_count = $tag->scaled_count;
			}

			$link = get_edit_tag_link( $tag->term_id, $tag->taxonomy );
			if ( ! is_wp_error( $link ) ) {
				$tags[ $key ]->edit_link = $link;
				$link = get_term_link( intval($tag->term_id), $tag->taxonomy );
				$tags[ $key ]->term_link = $link;
			}

			if ( is_wp_error( $link ) ) {
				$cloud =  '<strong>' . __( 'ERROR', 'media-library-assistant' ) . ': ' . $link->get_error_message() . '</strong>, ' . $link->get_error_data( $link->get_error_code() );

			if ( 'array' == $arguments['mla_output'] ) {
				return array( $cloud );
			}

			if ( empty($arguments['echo']) ) {
				return $cloud;
			}

			echo $cloud;
			return;
			}

			if ( 'edit' == $arguments['link'] ) {
				$tags[ $key ]->link = $tags[ $key ]->edit_link;
			} else {
				$tags[ $key ]->link = $tags[ $key ]->term_link;
			}
		} // foreach tag

		/*
		 * The default MLA style template includes "margin: 1.5%" to put a bit of
		 * minimum space between the columns. "mla_margin" can be used to change
		 * this. "mla_itemwidth" can be used with "columns=0" to achieve a
		 * "responsive" layout.
		 */
		 
		$columns = absint( $arguments['columns'] );
		$margin_string = strtolower( trim( $arguments['mla_margin'] ) );

		if ( is_numeric( $margin_string ) && ( 0 != $margin_string) ) {
			$margin_string .= '%'; // Legacy values are always in percent
		}

		if ( '%' == substr( $margin_string, -1 ) ) {
			$margin_percent = (float) substr( $margin_string, 0, strlen( $margin_string ) - 1 );
		} else {
			$margin_percent = 0;
		}

		$width_string = strtolower( trim( $arguments['mla_itemwidth'] ) );
		if ( 'none' != $width_string ) {
			switch ( $width_string ) {
				case 'exact':
					$margin_percent = 0;
					// fallthru
				case 'calculate':
					$width_string = $columns > 0 ? (floor(1000/$columns)/10) - ( 2.0 * $margin_percent ) : 100 - ( 2.0 * $margin_percent );
					// fallthru
				default:
					if ( is_numeric( $width_string ) && ( 0 != $width_string) ) {
						$width_string .= '%'; // Legacy values are always in percent
					}
			}
		} // $use_width

		$float = strtolower( $arguments['mla_float'] );
		if ( ! in_array( $float, array( 'left', 'none', 'right' ) ) ) {
			$float = is_rtl() ? 'right' : 'left';
		}

		/*
		 * Calculate cloud parameters
		 */
		$spread = $max_scaled_count - $min_scaled_count;
		if ( $spread <= 0 ) {
			$spread = 1;
		}

		$font_spread = $arguments['largest'] - $arguments['smallest'];
		if ( $font_spread < 0 ) {
			$font_spread = 1;
		}

		$font_step = $font_spread / $spread;

		$style_values = array_merge( $page_values, array(
			'mla_output' => $arguments['mla_output'],
			'mla_style' => $arguments['mla_style'],
			'mla_markup' => $arguments['mla_markup'],
			'taxonomy' => implode( '-', $arguments['taxonomy'] ),
			'current_item' => $arguments['current_item'],
			'itemtag' => tag_escape( $arguments['itemtag'] ),
			'termtag' => tag_escape( $arguments['termtag'] ),
			'captiontag' => tag_escape( $arguments['captiontag'] ),
			'columns' => $columns,
			'itemwidth' => $width_string,
			'margin' => $margin_string,
			'float' => $float,
			'found_rows' => $found_rows,
			'min_count' => $min_count,
			'max_count' => $max_count,
			'min_scaled_count' => $min_scaled_count,
			'max_scaled_count' => $max_scaled_count,
			'spread' => $spread,
			'smallest' => $arguments['smallest'],
			'largest' => $arguments['largest'],
			'unit' => $arguments['unit'],
			'font_spread' => $font_spread,
			'font_step' => $font_step,
			'separator' => $arguments['separator'],
			'single_text' => $arguments['single_text'],
			'multiple_text' => $arguments['multiple_text'],
			'echo' => $arguments['echo'],
			'link' => $arguments['link']
		) );

		$style_template = $gallery_style = '';
		$use_mla_tag_cloud_style = ( $is_grid || $is_list ) && ( 'none' != strtolower( $style_values['mla_style'] ) );
		if ( apply_filters( 'use_mla_tag_cloud_style', $use_mla_tag_cloud_style, $style_values['mla_style'] ) ) {
			$style_template = MLAShortcode_support::mla_fetch_gallery_template( $style_values['mla_style'], 'style' );
			if ( empty( $style_template ) ) {
				$style_values['mla_style'] = $default_style;
				$style_template = MLAShortcode_support::mla_fetch_gallery_template( $default_style, 'style' );
			}

			if ( ! empty ( $style_template ) ) {
				/*
				 * Look for 'query' and 'request' substitution parameters
				 */
				$style_values = MLAData::mla_expand_field_level_parameters( $style_template, $attr, $style_values );

				/*
				 * Clean up the template to resolve width or margin == 'none'
				 */
				if ( 'none' == $margin_string ) {
					$style_values['margin'] = '0';
					$style_template = preg_replace( '/margin:[\s]*\[\+margin\+\][\%]*[\;]*/', '', $style_template );
				}

				if ( 'none' == $width_string ) {
					$style_values['itemwidth'] = 'auto';
					$style_template = preg_replace( '/width:[\s]*\[\+itemwidth\+\][\%]*[\;]*/', '', $style_template );
				}

				$style_values = apply_filters( 'mla_tag_cloud_style_values', $style_values );
				$style_template = apply_filters( 'mla_tag_cloud_style_template', $style_template );
				$gallery_style = MLAData::mla_parse_template( $style_template, $style_values );
				$gallery_style = apply_filters( 'mla_tag_cloud_style_parse', $gallery_style, $style_template, $style_values );
			} // !empty template
		} // use_mla_tag_cloud_style

		$markup_values = $style_values;

		if ( $is_grid || $is_list ) {
			$open_template = MLAShortcode_support::mla_fetch_gallery_template( $markup_values['mla_markup'] . '-open', 'markup' );
			if ( false === $open_template ) {
				$markup_values['mla_markup'] = $default_markup;
				$open_template = MLAShortcode_support::mla_fetch_gallery_template( $default_markup, 'markup' );
			}

			if ( empty( $open_template ) ) {
				$open_template = '';
			}

			if ( $is_grid ) {
				$row_open_template = MLAShortcode_support::mla_fetch_gallery_template( $markup_values['mla_markup'] . '-row-open', 'markup' );
				if ( empty( $row_open_template ) ) {
					$row_open_template = '';
				}
			} else {
				$row_open_template = '';
			}

			$item_template = MLAShortcode_support::mla_fetch_gallery_template( $markup_values['mla_markup'] . '-item', 'markup' );
			if ( empty( $item_template ) ) {
				$item_template = '';
			}

			if ( $is_grid ) {
				$row_close_template = MLAShortcode_support::mla_fetch_gallery_template( $markup_values['mla_markup'] . '-row-close', 'markup' );
				if ( empty( $row_close_template ) ) {
					$row_close_template = '';
					}
			} else {
				$row_close_template = '';
			}

			$close_template = MLAShortcode_support::mla_fetch_gallery_template( $markup_values['mla_markup'] . '-close', 'markup' );
			if ( empty( $close_template ) ) {
				$close_template = '';
			}

			/*
			 * Look for gallery-level markup substitution parameters
			 */
			$new_text = $open_template . $row_open_template . $row_close_template . $close_template;
			$markup_values = MLAData::mla_expand_field_level_parameters( $new_text, $attr, $markup_values );

			$markup_values = apply_filters( 'mla_tag_cloud_open_values', $markup_values );
			$open_template = apply_filters( 'mla_tag_cloud_open_template', $open_template );
			if ( empty( $open_template ) ) {
				$gallery_open = '';
			} else {
				$gallery_open = MLAData::mla_parse_template( $open_template, $markup_values );
			}

			$gallery_open = apply_filters( 'mla_tag_cloud_open_parse', $gallery_open, $open_template, $markup_values );
			$cloud .= $gallery_style . $gallery_open;
		} // is_grid || is_list
		elseif ( $is_pagination ) {
			/*
			 * Handle 'previous_page', 'next_page', and 'paginate_links'
			 */
			if ( isset( $attr['limit'] ) ) {
				$attr['posts_per_page'] = $attr['limit'];
			}

			$pagination_result = self::_process_pagination_output_types( $output_parameters, $markup_values, $arguments, $attr, $found_rows );
			if ( false !== $pagination_result ) {
				return $pagination_result;
			}

			/*
			 * For "previous_link", "current_link" and "next_link", discard all of the $tags except the appropriate choice
			 */
			$link_type = $output_parameters[0];

			if ( ! in_array( $link_type, array ( 'previous_link', 'current_link', 'next_link' ) ) ) {
				return ''; // unknown output type
			}

			$is_wrap = isset( $output_parameters[1] ) && 'wrap' == $output_parameters[1];
			if ( empty( $arguments['term_id'] ) ) {
				$target_id = -2; // won't match anything
			} else {
				$current_id = $arguments['term_id'];

				foreach ( $tags as $id => $tag ) {
					if ( $tag->term_id == $current_id ) {
						break;
					}
				}

				switch ( $link_type ) {
					case 'previous_link':
						$target_id = $id - 1;
						break;
					case 'next_link':
						$target_id = $id + 1;
						break;
					case 'current_link':
					default:
						$target_id = $id;
				} // link_type
			}

			$target = NULL;
			if ( isset( $tags[ $target_id ] ) ) {
				$target = $tags[ $target_id ];
			} elseif ( $is_wrap ) {
				switch ( $link_type ) {
					case 'previous_link':
						$target = array_pop( $tags );
						break;
					case 'next_link':
						$target = array_shift( $tags );
				} // link_type
			} // is_wrap

			if ( isset( $target ) ) {
				$tags = array( $target );
			} elseif ( ! empty( $arguments['mla_nolink_text'] ) ) {
				return self::_process_shortcode_parameter( $arguments['mla_nolink_text'], $markup_values ) . '</a>';
			} else {
				return '';
			}
		} // is_pagination

		/*
		 * Accumulate links for flat and array output
		 */
		$tag_links = array();

		$column_index = 0;
		foreach ( $tags as $key => $tag ) {
			$item_values = $markup_values;

			/*
			 * fill in item-specific elements
			 */
			$item_values['index'] = (string) 1 + $column_index;
			if ( $item_values['columns'] > 0 && ( 1 + $column_index ) % $item_values['columns'] == 0 ) {
				$item_values['last_in_row'] = 'last_in_row';
			} else {
				$item_values['last_in_row'] = '';
			}

			$item_values['key'] = $key;
			$item_values['term_id'] = $tag->term_id;
			$item_values['name'] = wptexturize( $tag->name );
			$item_values['slug'] = wptexturize( $tag->slug );
			$item_values['term_group'] = $tag->term_group;
			$item_values['term_taxonomy_id'] = $tag->term_taxonomy_id;
			$item_values['taxonomy'] = wptexturize( $tag->taxonomy );
			$item_values['current_item_class'] = '';
			$item_values['description'] = wptexturize( $tag->description );
			$item_values['parent'] = $tag->parent;
			$item_values['count'] = isset ( $tag->count ) ? $tag->count : 0; 
			$item_values['scaled_count'] = $tag->scaled_count;
			$item_values['font_size'] = str_replace( ',', '.', ( $item_values['smallest'] + ( ( $item_values['scaled_count'] - $item_values['min_scaled_count'] ) * $item_values['font_step'] ) ) );
			$item_values['link_url'] = $tag->link;
			$item_values['editlink_url'] = $tag->edit_link;
			$item_values['termlink_url'] = $tag->term_link;
			// Added in the code below:
			// 'caption', 'link_attributes', 'current_item_class', 'rollover_text', 'link_style', 'link_text', 'editlink', 'termlink', 'thelink'

			if ( ! empty( $arguments['current_item'] ) ) {
				if ( is_integer( $arguments['current_item'] ) ) {
					if ( $arguments['current_item'] == $tag->term_id ) {
						$item_values['current_item_class'] = $arguments['current_item_class'];
					}
				} else {
					if ( $arguments['current_item'] == $tag->slug ) {
						$item_values['current_item_class'] = $arguments['current_item_class'];
					}
				}
			}

			/*
			 * Add item_specific field-level substitution parameters
			 */
			$new_text = isset( $item_template ) ? $item_template : '';
			foreach( $mla_item_specific_arguments as $index => $value ) {
				$new_text .= str_replace( '{+', '[+', str_replace( '+}', '+]', $arguments[ $index ] ) );
			}

			$item_values = MLAData::mla_expand_field_level_parameters( $new_text, $attr, $item_values );

			if ( $item_values['captiontag'] ) {
				$item_values['caption'] = wptexturize( $tag->description );
				if ( ! empty( $arguments['mla_caption'] ) ) {
					$item_values['caption'] = wptexturize( self::_process_shortcode_parameter( $arguments['mla_caption'], $item_values ) );
				}
			} else {
				$item_values['caption'] = '';
			}

			if ( ! empty( $arguments['mla_link_text'] ) ) {
				$link_text = self::_process_shortcode_parameter( $arguments['mla_link_text'], $item_values );
			} else {
				$link_text = false;
			}

			/*
			 * Apply the Display Content parameters.
			 */
			if ( ! empty( $arguments['mla_target'] ) ) {
				$link_attributes = 'target="' . $arguments['mla_target'] . '" ';
			} else {
				$link_attributes = '';
			}

			if ( ! empty( $arguments['mla_link_attributes'] ) ) {
				$link_attributes .= self::_process_shortcode_parameter( $arguments['mla_link_attributes'], $item_values ) . ' ';
			}

			if ( ! empty( $arguments['mla_link_class'] ) ) {
				$link_attributes .= 'class="' . self::_process_shortcode_parameter( $arguments['mla_link_class'], $item_values ) . '" ';
			}

			$item_values['link_attributes'] = $link_attributes;

			$item_values['rollover_text'] = sprintf( _n( $item_values['single_text'], $item_values['multiple_text'], $item_values['count'], 'media-library-assistant' ), number_format_i18n( $item_values['count'] ) );
			if ( ! empty( $arguments['mla_rollover_text'] ) ) {
				$item_values['rollover_text'] = esc_attr( self::_process_shortcode_parameter( $arguments['mla_rollover_text'], $item_values ) );
			}

			if ( ! empty( $arguments['mla_link_href'] ) ) {
				$link_href = self::_process_shortcode_parameter( $arguments['mla_link_href'], $item_values );
				$item_values['link_url'] = $link_href;
			} else {
				$link_href = '';
			}

			if ( ! empty( $arguments['mla_link_style'] ) ) {
				$item_values['link_style'] = esc_attr( self::_process_shortcode_parameter( $arguments['mla_link_style'], $item_values ) );
			} else {
				$item_values['link_style'] = 'font-size: ' . $item_values['font_size'] . $item_values['unit'];
			}

			if ( ! empty( $arguments['mla_link_text'] ) ) {
				$item_values['link_text'] = esc_attr( self::_process_shortcode_parameter( $arguments['mla_link_text'], $item_values ) );
			} else {
				$item_values['link_text'] = $item_values['name'];
			}

			/*
			 * Editlink, termlink and thelink
			 */
			$item_values['editlink'] = sprintf( '<a %1$shref="%2$s" title="%3$s" style="%4$s">%5$s</a>', $link_attributes, $item_values['editlink_url'], $item_values['rollover_text'], $item_values['link_style'], $item_values['link_text'] );
			$item_values['termlink'] = sprintf( '<a %1$shref="%2$s" title="%3$s" style="%4$s">%5$s</a>', $link_attributes, $item_values['termlink_url'], $item_values['rollover_text'], $item_values['link_style'], $item_values['link_text'] );

			if ( ! empty( $link_href ) ) {
				$item_values['thelink'] = sprintf( '<a %1$shref="%2$s" title="%3$s" style="%4$s">%5$s</a>', $link_attributes, $link_href, $item_values['rollover_text'], $item_values['link_style'], $item_values['link_text'] );
			} elseif ( 'edit' == $arguments['link'] ) {
				$item_values['thelink'] = $item_values['editlink'];
			} elseif ( 'view' == $arguments['link'] ) {
				$item_values['thelink'] = $item_values['termlink'];
			} elseif ( 'span' == $arguments['link'] ) {
				$item_values['thelink'] = sprintf( '<span %1$sstyle="%2$s">%3$s</a>', $link_attributes, $item_values['link_style'], $item_values['link_text'] );
			} else {
				$item_values['thelink'] = $item_values['link_text'];
			}

			if ( $is_grid || $is_list ) {
				/*
				 * Start of row markup
				 */
				if ( $is_grid && ( $markup_values['columns'] > 0 && $column_index % $markup_values['columns'] == 0 ) ) {
					$markup_values = apply_filters( 'mla_tag_cloud_row_open_values', $markup_values );
					$row_open_template = apply_filters( 'mla_tag_cloud_row_open_template', $row_open_template );
					$parse_value = MLAData::mla_parse_template( $row_open_template, $markup_values );
					$cloud .= apply_filters( 'mla_tag_cloud_row_open_parse', $parse_value, $row_open_template, $markup_values );
				}

				/*
				 * item markup
				 */
				$column_index++;
				$item_values = apply_filters( 'mla_tag_cloud_item_values', $item_values );
				$item_template = apply_filters( 'mla_tag_cloud_item_template', $item_template );
				$parse_value = MLAData::mla_parse_template( $item_template, $item_values );
				$cloud .= apply_filters( 'mla_tag_cloud_item_parse', $parse_value, $item_template, $item_values );

				/*
				 * End of row markup
				 */
				if ( $is_grid && ( $markup_values['columns'] > 0 && $column_index % $markup_values['columns'] == 0 ) ) {
					$markup_values = apply_filters( 'mla_tag_cloud_row_close_values', $markup_values );
					$row_close_template = apply_filters( 'mla_tag_cloud_row_close_template', $row_close_template );
					$parse_value = MLAData::mla_parse_template( $row_close_template, $markup_values );
					$cloud .= apply_filters( 'mla_tag_cloud_row_close_parse', $parse_value, $row_close_template, $markup_values );
				}
			} // is_grid || is_list
			elseif ( $is_pagination ) {
				return $item_values['thelink'];
			} else {
				$column_index++;
				$item_values = apply_filters( 'mla_tag_cloud_item_values', $item_values );
				$tag_links[] = apply_filters( 'mla_tag_cloud_item_parse', $item_values['thelink'], NULL, $item_values );
			} 
		} // foreach tag

		if ($is_grid || $is_list ) {
			/*
			 * Close out partial row
			 */
			if ( $is_grid && ( ! ($markup_values['columns'] > 0 && $column_index % $markup_values['columns'] == 0 ) ) ) {
				$markup_values = apply_filters( 'mla_tag_cloud_row_close_values', $markup_values );
				$row_close_template = apply_filters( 'mla_tag_cloud_row_close_template', $row_close_template );
				$parse_value = MLAData::mla_parse_template( $row_close_template, $markup_values );
				$cloud .= apply_filters( 'mla_tag_cloud_row_close_parse', $parse_value, $row_close_template, $markup_values );
			}

			$markup_values = apply_filters( 'mla_tag_cloud_close_values', $markup_values );
			$close_template = apply_filters( 'mla_tag_cloud_close_template', $close_template );
			$parse_value = MLAData::mla_parse_template( $close_template, $markup_values );
			$cloud .= apply_filters( 'mla_tag_cloud_close_parse', $parse_value, $close_template, $markup_values );
		} // is_grid || is_list
		else {
			switch ( $markup_values['mla_output'] ) {
			case 'array' :
				$cloud =& $tag_links;
				break;
			case 'flat' :
			default :
				$cloud .= join( $markup_values['separator'], $tag_links );
				break;
			} // switch format
		}

		//$cloud = wp_generate_tag_cloud( $tags, $arguments );

		if ( 'array' == $arguments['mla_output'] || empty($arguments['echo']) ) {
			return $cloud;
		}

		echo $cloud;
	}

	/**
	 * The MLA Tag Cloud shortcode.
	 *
	 * This is an interface to the mla_tag_cloud function.
	 *
	 * @since 2.20
	 *
	 * @param array $attr Attributes of the shortcode.
	 *
	 * @return string HTML content to display the tag cloud.
	 */
	public static function mla_tag_cloud_shortcode( $attr ) {
		/*
		 * Make sure $attr is an array, even if it's empty,
		 * and repair damage caused by link-breaks in the source text
		 */
		$attr = self::_validate_attributes( $attr );

		/*
		 * The 'array' format makes no sense in a shortcode
		 */
		if ( isset( $attr['mla_output'] ) && 'array' == $attr['mla_output'] ) {
			$attr['mla_output'] = 'flat';
		}
			 
		/*
		 * A shortcode must return its content to the caller, so "echo" makes no sense
		 */
		$attr['echo'] = false;
			 
		return self::mla_tag_cloud( $attr );
	}

	/**
	 * Computes image dimensions for scalable graphics, e.g., SVG 
	 *
	 * @since 2.20
	 *
	 * @return array 
	 */
	private static function _registered_dimensions() {
		global $_wp_additional_image_sizes;

		if ( 'checked' == MLACore::mla_get_option( MLACore::MLA_ENABLE_MLA_ICONS ) ) {
			$sizes = array( 'icon' => array( 64, 64 ) );
		} else {
			$sizes = array( 'icon' => array( 60, 60 ) );
		}

		foreach( get_intermediate_image_sizes() as $s ) {
			$sizes[ $s ] = array( 0, 0 );

			if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$sizes[ $s ][0] = get_option( $s . '_size_w' );
				$sizes[ $s ][1] = get_option( $s . '_size_h' );
			} else {
				if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) ) {
					$sizes[ $s ] = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'], );
				}
			}
		}
 
		return $sizes;
	}

	/**
	 * Handles brace/bracket escaping and parses template for a shortcode parameter
	 *
	 * @since 2.20
	 *
	 * @param string raw shortcode parameter, e.g., "text {+field+} {brackets} \\{braces\\}"
	 * @param string template substitution values, e.g., ('instance' => '1', ...  )
	 *
	 * @return string parameter with brackets, braces, substitution parameters and templates processed
	 */
	private static function _process_shortcode_parameter( $text, $markup_values ) {
		$new_text = str_replace( '{', '[', str_replace( '}', ']', $text ) );
		$new_text = str_replace( '\[', '{', str_replace( '\]', '}', $new_text ) );
		return MLAData::mla_parse_template( $new_text, $markup_values );
	}

	/**
	 * Handles pagnation output types 'previous_page', 'next_page', and 'paginate_links'
	 *
	 * @since 2.20
	 *
	 * @param array	value(s) for mla_output_type parameter
	 * @param string template substitution values, e.g., ('instance' => '1', ...  )
	 * @param string merged default and passed shortcode parameter values
	 * @param integer number of attachments in the gallery, without pagination
	 * @param string output text so far, may include debug values
	 *
	 * @return mixed	false or string with HTML for pagination output types
	 */
	private static function _paginate_links( $output_parameters, $markup_values, $arguments, $found_rows, $output = '' ) {
		if ( 2 > $markup_values['last_page'] ) {
			return '';
		}

		$show_all = $prev_next = false;

		if ( isset ( $output_parameters[1] ) ) {
				switch ( $output_parameters[1] ) {
				case 'show_all':
					$show_all = true;
					break;
				case 'prev_next':
					$prev_next = true;
			}
		}

		$mla_page_parameter = $arguments['mla_page_parameter'];
		$current_page = $markup_values['current_page'];
		$last_page = $markup_values['last_page'];
		$end_size = absint( $arguments['mla_end_size'] );
		$mid_size = absint( $arguments['mla_mid_size'] );
		$posts_per_page = $markup_values['posts_per_page'];

		$new_target = ( ! empty( $arguments['mla_target'] ) ) ? 'target="' . $arguments['mla_target'] . '" ' : '';

		/*
		 * these will add to the default classes
		 */
		$new_class = ( ! empty( $arguments['mla_link_class'] ) ) ? ' ' . esc_attr( self::_process_shortcode_parameter( $arguments['mla_link_class'], $markup_values ) ) : '';

		$new_attributes = ( ! empty( $arguments['mla_link_attributes'] ) ) ? esc_attr( self::_process_shortcode_parameter( $arguments['mla_link_attributes'], $markup_values ) ) . ' ' : '';

		//$new_base =  ( ! empty( $arguments['mla_link_href'] ) ) ? esc_attr( self::_process_shortcode_parameter( $arguments['mla_link_href'], $markup_values ) ) : $markup_values['new_url'];
		$new_base =  ( ! empty( $arguments['mla_link_href'] ) ) ? self::_process_shortcode_parameter( $arguments['mla_link_href'], $markup_values ) : $markup_values['new_url'];

		/*
		 * Build the array of page links
		 */
		$page_links = array();
		$dots = false;

		if ( $prev_next && $current_page && 1 < $current_page ) {
			$markup_values['new_page'] = $current_page - 1;
			$new_title = ( ! empty( $arguments['mla_rollover_text'] ) ) ? 'title="' . esc_attr( self::_process_shortcode_parameter( $arguments['mla_rollover_text'], $markup_values ) ) . '" ' : '';
			$new_url = remove_query_arg( $mla_page_parameter, $new_base );
			$new_url = add_query_arg( array(  $mla_page_parameter  => $current_page - 1 ), $new_url );
			$prev_text = ( ! empty( $arguments['mla_prev_text'] ) ) ? esc_attr( self::_process_shortcode_parameter( $arguments['mla_prev_text'], $markup_values ) ) : '&laquo; ' . __( 'Previous', 'media-library-assistant' );
			$page_links[] = sprintf( '<a %1$sclass="prev page-numbers%2$s" %3$s%4$shref="%5$s">%6$s</a>',
				/* %1$s */ $new_target,
				/* %2$s */ $new_class,
				/* %3$s */ $new_attributes,
				/* %4$s */ $new_title,
				/* %5$s */ $new_url,
				/* %6$s */ $prev_text );
		}

		for ( $new_page = 1; $new_page <= $last_page; $new_page++ ) {
			$new_page_display = number_format_i18n( $new_page );
			$markup_values['new_page'] = $new_page;
			$new_title = ( ! empty( $arguments['mla_rollover_text'] ) ) ? 'title="' . esc_attr( self::_process_shortcode_parameter( $arguments['mla_rollover_text'], $markup_values ) ) . '" ' : '';

			if ( $new_page == $current_page ) {
				// build current page span
				$page_links[] = sprintf( '<span class="page-numbers current%1$s">%2$s</span>',
					/* %1$s */ $new_class,
					/* %2$s */ $new_page_display );
				$dots = true;
			} else {
				if ( $show_all || ( $new_page <= $end_size || ( $current_page && $new_page >= $current_page - $mid_size && $new_page <= $current_page + $mid_size ) || $new_page > $last_page - $end_size ) ) {
					// build link
					$new_url = remove_query_arg( $mla_page_parameter, $new_base );
					$new_url = add_query_arg( array(  $mla_page_parameter  => $new_page ), $new_url );
					$page_links[] = sprintf( '<a %1$sclass="page-numbers%2$s" %3$s%4$shref="%5$s">%6$s</a>',
						/* %1$s */ $new_target,
						/* %2$s */ $new_class,
						/* %3$s */ $new_attributes,
						/* %4$s */ $new_title,
						/* %5$s */ $new_url,
						/* %6$s */ $new_page_display );
					$dots = true;
				} elseif ( $dots && ! $show_all ) {
					// build link
					$page_links[] = sprintf( '<span class="page-numbers dots%1$s">&hellip;</span>',
						/* %1$s */ $new_class );
					$dots = false;
				}
			} // ! current
		} // for $new_page

		if ( $prev_next && $current_page && ( $current_page < $last_page || -1 == $last_page ) ) {
			// build next link
			$markup_values['new_page'] = $current_page + 1;
			$new_title = ( ! empty( $arguments['mla_rollover_text'] ) ) ? 'title="' . esc_attr( self::_process_shortcode_parameter( $arguments['mla_rollover_text'], $markup_values ) ) . '" ' : '';
			$new_url = remove_query_arg( $mla_page_parameter, $new_base );
			$new_url = add_query_arg( array(  $mla_page_parameter  => $current_page + 1 ), $new_url );
			$next_text = ( ! empty( $arguments['mla_next_text'] ) ) ? esc_attr( self::_process_shortcode_parameter( $arguments['mla_next_text'], $markup_values ) ) : __( 'Next', 'media-library-assistant' ) . ' &raquo;';
			$page_links[] = sprintf( '<a %1$sclass="next page-numbers%2$s" %3$s%4$shref="%5$s">%6$s</a>',
				/* %1$s */ $new_target,
				/* %2$s */ $new_class,
				/* %3$s */ $new_attributes,
				/* %4$s */ $new_title,
				/* %5$s */ $new_url,
				/* %6$s */ $next_text );
		}

		switch ( strtolower( trim( $arguments['mla_paginate_type'] ) ) ) {
			case 'list':
				$results = "<ul class='page-numbers'>\n\t<li>";
				$results .= join("</li>\n\t<li>", $page_links);
				$results .= "</li>\n</ul>\n";
				break;
			case 'plain':
			default:
				$results = join("\n", $page_links);
		} // mla_paginate_type

		return $output . $results;
	}

	/**
	 * Handles pagnation output types 'previous_page', 'next_page', and 'paginate_links'
	 *
	 * @since 2.20
	 *
	 * @param array	value(s) for mla_output_type parameter
	 * @param string template substitution values, e.g., ('instance' => '1', ...  )
	 * @param string merged default and passed shortcode parameter values
	 * @param string raw passed shortcode parameter values
	 * @param integer number of attachments in the gallery, without pagination
	 * @param string output text so far, may include debug values
	 *
	 * @return mixed	false or string with HTML for pagination output types
	 */
	private static function _process_pagination_output_types( $output_parameters, $markup_values, $arguments, $attr, $found_rows, $output = '' ) {
		if ( ! in_array( $output_parameters[0], array( 'previous_page', 'next_page', 'paginate_links' ) ) ) {
			return false;
		}

		/*
		 * Add data selection parameters to gallery-specific and mla_gallery-specific parameters
		 */
		$arguments = array_merge( $arguments, shortcode_atts( self::$mla_get_shortcode_attachments_parameters, $attr ) );
		$posts_per_page = absint( $arguments['posts_per_page'] );
		$mla_page_parameter = $arguments['mla_page_parameter'];

		/*
		 * $mla_page_parameter, if set, doesn't make it through the shortcode_atts filter,
		 * so we handle it separately
		 */
		if ( ! isset( $arguments[ $mla_page_parameter ] ) ) {
			if ( isset( $attr[ $mla_page_parameter ] ) ) {
				$arguments[ $mla_page_parameter ] = $attr[ $mla_page_parameter ];
			} else {
				$arguments[ $mla_page_parameter ] = '';
			}
		}

		if ( 0 == $posts_per_page ) {
			$posts_per_page = absint( $arguments['numberposts'] );
		}

		if ( 0 == $posts_per_page ) {
			$posts_per_page = absint( get_option('posts_per_page') );
		}

		if ( 0 < $posts_per_page ) {
			$max_page = floor( $found_rows / $posts_per_page );
			if ( $max_page < ( $found_rows / $posts_per_page ) ) {
				$max_page++;
			}
		} else {
			$max_page = 1;
		}

		if ( isset( $arguments['mla_paginate_total'] )  && $max_page > absint( $arguments['mla_paginate_total'] ) ) {
			$max_page = absint( $arguments['mla_paginate_total'] );
		}

		if ( isset( $arguments[ $mla_page_parameter ] ) ) {
			$paged = absint( $arguments[ $mla_page_parameter ] );
		} else {
			$paged = absint( $arguments['paged'] );
		}

		if ( 0 == $paged ) {
			$paged = 1;
		}

		if ( $max_page < $paged ) {
			$paged = $max_page;
		}

		switch ( $output_parameters[0] ) {
			case 'previous_page':
				if ( 1 < $paged ) {
					$new_page = $paged - 1;
				} else {
					$new_page = 0;

					if ( isset ( $output_parameters[1] ) ) {
						switch ( $output_parameters[1] ) {
							case 'wrap':
								$new_page = $max_page;
								break;
							case 'first':
								$new_page = 1;
						}
					}
				}

				break;
			case 'next_page':
				if ( $paged < $max_page ) {
					$new_page = $paged + 1;
				} else {
					$new_page = 0;

					if ( isset ( $output_parameters[1] ) ) {
						switch ( $output_parameters[1] ) {
							case 'last':
								$new_page = $max_page;
								break;
							case 'wrap':
								$new_page = 1;
						}
					}
				}

				break;
			case 'paginate_links':
				$new_page = 0;
		}

		$markup_values['current_page'] = $paged;
		$markup_values['new_page'] = $new_page;
		$markup_values['last_page'] = $max_page;
		$markup_values['posts_per_page'] = $posts_per_page;
		$markup_values['found_rows'] = $found_rows;

		if ( $paged ) {
			$markup_values['current_offset'] = ( $paged - 1 ) * $posts_per_page;
		} else {
			$markup_values['current_offset'] = 0;
		}

		if ( $new_page ) {
			$markup_values['new_offset'] = ( $new_page - 1 ) * $posts_per_page;
		} else {
			$markup_values['new_offset'] = 0;
		}

		$markup_values['current_page_text'] = 'mla_paginate_current="[+current_page+]"';
		$markup_values['new_page_text'] = 'mla_paginate_current="[+new_page+]"';
		$markup_values['last_page_text'] = 'mla_paginate_total="[+last_page+]"';
		$markup_values['posts_per_page_text'] = 'posts_per_page="[+posts_per_page+]"';

		if ( 'HTTPS' == substr( $_SERVER["SERVER_PROTOCOL"], 0, 5 ) ) {
			$markup_values['scheme'] = 'https://';
		} else {
			$markup_values['scheme'] = 'http://';
		}

		$markup_values['http_host'] = $_SERVER['HTTP_HOST'];

		if ( 0 < $new_page ) {
			$new_uri = remove_query_arg( $mla_page_parameter, $_SERVER['REQUEST_URI'] );
			$markup_values['request_uri'] = add_query_arg( array(  $mla_page_parameter  => $new_page ), $new_uri );	
		} else {
			$markup_values['request_uri'] = $_SERVER['REQUEST_URI'];	
		}

		$markup_values['new_url'] = set_url_scheme( $markup_values['scheme'] . $markup_values['http_host'] . $markup_values['request_uri'] );

		/*
		 * Expand pagination-specific Gallery Display Content parameters,
		 * which can contain request: and query: arguments.
		 */
		$pagination_arguments = array( 'mla_nolink_text', 'mla_link_class', 'mla_rollover_text', 'mla_link_attributes', 'mla_link_href', 'mla_link_text', 'mla_prev_text', 'mla_next_text' );

		$new_text = '';
		foreach( $pagination_arguments as $value ) {
			$new_text .= str_replace( '{+', '[+', str_replace( '+}', '+]', $arguments[ $value ] ) );
		}

		$markup_values = MLAData::mla_expand_field_level_parameters( $new_text, $attr, $markup_values );

		/*
		 * Build the new link, applying Gallery Display Content parameters
		 */
		if ( 'paginate_links' == $output_parameters[0] ) {
			return self::_paginate_links( $output_parameters, $markup_values, $arguments, $found_rows, $output );
		}

		if ( 0 == $new_page ) {
			if ( ! empty( $arguments['mla_nolink_text'] ) ) {
				return self::_process_shortcode_parameter( $arguments['mla_nolink_text'], $markup_values );
			} else {
				return '';
			}
		}

		$new_link = '<a ';

		if ( ! empty( $arguments['mla_target'] ) ) {
			$new_link .= 'target="' . $arguments['mla_target'] . '" ';
		}

		if ( ! empty( $arguments['mla_link_class'] ) ) {
			$new_link .= 'class="' . esc_attr( self::_process_shortcode_parameter( $arguments['mla_link_class'], $markup_values ) ) . '" ';
		}

		if ( ! empty( $arguments['mla_rollover_text'] ) ) {
			$new_link .= 'title="' . esc_attr( self::_process_shortcode_parameter( $arguments['mla_rollover_text'], $markup_values ) ) . '" ';
		}

		if ( ! empty( $arguments['mla_link_attributes'] ) ) {
			$new_link .= esc_attr( self::_process_shortcode_parameter( $arguments['mla_link_attributes'], $markup_values ) ) . ' ';
		}

		if ( ! empty( $arguments['mla_link_href'] ) ) {
			//$new_link .= 'href="' . esc_attr( self::_process_shortcode_parameter( $arguments['mla_link_href'], $markup_values ) ) . '" >';
			$new_link .= 'href="' . self::_process_shortcode_parameter( $arguments['mla_link_href'], $markup_values ) . '" >';
		} else {
			$new_link .= 'href="' . $markup_values['new_url'] . '" >';
		}

		if ( ! empty( $arguments['mla_link_text'] ) ) {
			$new_link .= self::_process_shortcode_parameter( $arguments['mla_link_text'], $markup_values ) . '</a>';
		} else {
			if ( 'previous_page' == $output_parameters[0] ) {
				if ( isset( $arguments['mla_prev_text'] ) ) {
					$new_text = esc_attr( self::_process_shortcode_parameter( $arguments['mla_prev_text'], $markup_values ) );
				} else {
					$new_text = '&laquo; ' . __( 'Previous', 'media-library-assistant' );
				}
			} else {
				if ( isset( $arguments['mla_next_text'] ) ) {
					$new_text = esc_attr( self::_process_shortcode_parameter( $arguments['mla_next_text'], $markup_values ) );
				} else {
					$new_text = __( 'Next', 'media-library-assistant' ) . ' &raquo;';
				}
			}

			$new_link .= $new_text . '</a>';
		}

		return $new_link;
	}

	/**
	 * WP_Query filter "parameters"
	 *
	 * This array defines parameters for the query's join, where and orderby filters.
	 * The parameters are set up in the mla_get_shortcode_attachments function, and
	 * any further logic required to translate those values is contained in the filter.
	 *
	 * Array index values are: orderby, post_parent
	 *
	 * @since 2.20
	 *
	 * @var	array
	 */
	private static $query_parameters = array();

	/**
	 * Cleans up damage caused by the Visual Editor to the tax_query and meta_query specifications
	 *
	 * @since 2.20
	 *
	 * @param string query specification; PHP nested arrays
	 *
	 * @return string query specification with HTML escape sequences and line breaks removed
	 */
	private static function _sanitize_query_specification( $specification ) {
		$specification = wp_specialchars_decode( $specification );
		$specification = str_replace( array( '<br>', '<br />', '<p>', '</p>', "\r", "\n" ), ' ', $specification );
		return $specification;
	}

	/**
	 * Translates query parameters to a valid SQL order by clause.
	 *
	 * Accepts one or more valid columns, with or without ASC/DESC.
	 * Enhanced version of /wp-includes/formatting.php function sanitize_sql_orderby().
	 *
	 * @since 2.20
	 *
	 * @param array Validated query parameters; 'order', 'orderby', 'meta_key', 'post__in'.
	 * @param string Optional. Database table prefix; can be empty. Default taken from $wpdb->posts.
	 * @param array Optional. Field names (keys) and database column equivalents (values). Defaults from [mla_gallery].
	 * @param array Optional. Field names (values) that require a BINARY prefix to preserve case order. Default array()
	 * @return string|bool Returns the orderby clause if present, false otherwise.
	 */
	private static function _validate_sql_orderby( $query_parameters, $table_prefix = NULL, $allowed_keys = NULL, $binary_keys = array() ){
		global $wpdb;

		$results = array ();
		$order = isset( $query_parameters['order'] ) ? ' ' . $query_parameters['order'] : '';
		$orderby = isset( $query_parameters['orderby'] ) ? $query_parameters['orderby'] : '';
		$meta_key = isset( $query_parameters['meta_key'] ) ? $query_parameters['meta_key'] : '';

		if ( is_null( $table_prefix ) ) {
			$table_prefix = $wpdb->posts . '.';
		}

		if ( is_null( $allowed_keys ) ) {
			$allowed_keys = array(
				'empty_orderby_default' => 'post_date',
				'explicit_orderby_field' => 'post__in',
				'explicit_orderby_column' => 'ID',
				'ID' => 'ID',
				'author' => 'post_author',
				'date' => 'post_date',
				'description' => 'post_content',
				'content' => 'post_content',
				'title' => 'post_title',
				'caption' => 'post_excerpt',
				'excerpt' => 'post_excerpt',
				'slug' => 'post_name',
				'name' => 'post_name',
				'modified' => 'post_modified',
				'parent' => 'post_parent',
				'menu_order' => 'menu_order',
				'mime_type' => 'post_mime_type',
				'comment_count' => 'post_content',
				'rand' => 'RAND()',
			);
		}

		if ( empty( $orderby ) ) {
			if ( ! empty( $allowed_keys['empty_orderby_default'] ) ) {
				return $table_prefix . $allowed_keys['empty_orderby_default'] . " {$order}";
			} else {
				return "{$table_prefix}post_date {$order}";
			}
		} elseif ( 'none' == $orderby ) {
			return '';
		} elseif ( ! empty( $allowed_keys['explicit_orderby_field'] ) ) {
			$explicit_field = $allowed_keys['explicit_orderby_field'];
			if ( $orderby == $explicit_field ) {
				if ( ! empty( $query_parameters[ $explicit_field ] ) ) {
					$explicit_order = implode(',', array_map( 'absint', $query_parameters[ $explicit_field ] ) );

					if ( ! empty( $explicit_order ) ) {
						$explicit_column = $allowed_keys['explicit_orderby_column'];
						return "FIELD( {$table_prefix}{$explicit_column}, {$explicit_order} )";
					} else {
						return '';
					}
				}
			}
		}

		if ( ! empty( $meta_key ) ) {
			$allowed_keys[ $meta_key ] = "$wpdb->postmeta.meta_value";
			$allowed_keys['meta_value'] = "$wpdb->postmeta.meta_value";
			$allowed_keys['meta_value_num'] = "$wpdb->postmeta.meta_value+0";
		}

		$obmatches = preg_split('/\s*,\s*/', trim($query_parameters['orderby']));
		foreach ( $obmatches as $index => $value ) {
			$count = preg_match('/([a-z0-9_]+)(\s+(ASC|DESC))?/i', $value, $matches);

			if ( $count && ( $value == $matches[0] ) && array_key_exists( $matches[1], $allowed_keys ) ) {
				if ( ( 'rand' == $matches[1] ) || ( 'random' == $matches[1] ) ){
						$results[] = 'RAND()';
				} else {
					switch ( $matches[1] ) {
						case $meta_key:
						case 'meta_value':
							$matches[1] = "$wpdb->postmeta.meta_value";
							break;
						case 'meta_value_num':
							$matches[1] = "$wpdb->postmeta.meta_value+0";
							break;
						default:
							if ( in_array( $matches[1], $binary_keys ) ) {
								$matches[1] = 'BINARY ' . $table_prefix . $allowed_keys[ $matches[1] ];
							} else {
								$matches[1] = $table_prefix . $allowed_keys[ $matches[1] ];
							}
					} // switch $matches[1]

					$results[] = isset( $matches[2] ) ? $matches[1] . $matches[2] : $matches[1] . $order;
				} // not 'rand'
			} // valid column specification
		} // foreach $obmatches

		$orderby = implode( ', ', $results );
		if ( empty( $orderby ) ) {
			return false;
		}

		return $orderby;
	}

	/**
	 * Data selection parameters for the WP_Query in [mla_gallery]
	 *
	 * @since 2.20
	 *
	 * @var	array
	 */
	private static $mla_get_shortcode_attachments_parameters = array(
			'order' => 'ASC', // or 'DESC' or 'RAND'
			'orderby' => 'menu_order,ID',
			'id' => NULL,
			'ids' => array(),
			'include' => array(),
			'exclude' => array(),
			// MLA extensions, from WP_Query
			// Force 'get_children' style query
			'post_parent' => NULL, // post/page ID, 'none', 'any', 'current' or 'all'
			// Author
			'author' => NULL,
			'author_name' => '',
			// Category
			'cat' => 0,
			'category_name' => '',
			'category__and' => array(),
			'category__in' => array(),
			'category__not_in' => array(),
			// Tag
			'tag' => '',
			'tag_id' => 0,
			'tag__and' => array(),
			'tag__in' => array(),
			'tag__not_in' => array(),
			'tag_slug__and' => array(),
			'tag_slug__in' => array(),
			// Taxonomy parameters are handled separately
			// {tax_slug} => 'term' | array ( 'term', 'term', ... )
			// 'tax_query' => ''
			// 'tax_relation' => 'OR', 'AND' (default),
			// 'tax_operator' => 'OR' (default), 'IN', 'NOT IN', 'AND',
			// 'tax_include_children' => true (default), false
			// Post 
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'post_mime_type' => 'image',
			// Pagination - no default for most of these
			'nopaging' => true,
			'numberposts' => 0,
			'posts_per_page' => 0,
			'posts_per_archive_page' => 0,
			'paged' => NULL, // page number or 'current'
			'offset' => NULL,
			'mla_page_parameter' => 'mla_paginate_current',
			'mla_paginate_current' => NULL,
			'mla_paginate_total' => NULL,
			// Date and Time Queries
			'date_query' => '',
			// Custom Field
			'meta_key' => '',
			'meta_value' => '',
			'meta_value_num' => NULL,
			'meta_compare' => '',
			'meta_query' => '',
			// Terms Search
			'mla_terms_phrases' => '',
			'mla_terms_taxonomies' => '',
			'mla_phrase_connector' => '',
			'mla_term_connector' => '',
			// Search
			's' => '',
			'mla_search_fields' => '',
			'mla_search_connector' => '',
			'sentence' => '',
			'exact' => '',
			// Returned fields, for support topic "Adding 'fields' to function mla_get_shortcode_attachments" by leoloso
			'fields' => '',
			// Caching parameters, for support topic "Lag in attachment categories" by Ruriko
			'cache_results' => NULL,
			'update_post_meta_cache' => NULL,
			'update_post_term_cache' => NULL,
		);

	/**
	 * Parses shortcode parameters and returns the gallery objects
	 *
	 * @since 2.20
	 *
	 * @param int Post ID of the parent
	 * @param array Attributes of the shortcode
	 * @param boolean true to calculate and return ['found_posts'] as an array element
	 *
	 * @return array List of attachments returned from WP_Query
	 */
	public static function mla_get_shortcode_attachments( $post_parent, $attr, $return_found_rows = NULL ) {
		global $wp_query;

		/*
		 * Parameters passed to the where and orderby filter functions
		 */
		self::$query_parameters = array();

		/*
		 * Parameters passed to the posts_search filter function in MLAData
		 */
		MLAQuery::$search_parameters = array( 'debug' => 'none' );

		/*
		 * Make sure $attr is an array, even if it's empty
		 */
		if ( empty( $attr ) ) {
			$attr = array();
		} elseif ( is_string( $attr ) ) {
			$attr = shortcode_parse_atts( $attr );
		}

		/*
		 * The "where used" queries have no $_REQUEST context available to them,
		 * so tax_, date_ and meta_query evaluation will fail if they contain "{+request:"
		 * parameters. Ignore these errors.
		 */
		if ( isset( $attr['where_used_query'] ) && ( 'this-is-a-where-used-query' == $attr['where_used_query'] ) ) {
			$where_used_query = true;
			unset( $attr['where_used_query'] );
		} else {
			$where_used_query = false;
		}

		/*
		 * Merge input arguments with defaults, then extract the query arguments.
		 *
		 * $return_found_rows is used to indicate that the call comes from gallery_shortcode(),
		 * which is the only call that supplies it.
		 */
		if ( ! is_null( $return_found_rows ) ) {
			$attr = apply_filters( 'mla_gallery_query_attributes', $attr );
		}

		$arguments = shortcode_atts( self::$mla_get_shortcode_attachments_parameters, $attr );
		$mla_page_parameter = $arguments['mla_page_parameter'];
		unset( $arguments['mla_page_parameter'] );

		/*
		 * $mla_page_parameter, if set, doesn't make it through the shortcode_atts filter,
		 * so we handle it separately
		 */
		if ( ! isset( $arguments[ $mla_page_parameter ] ) ) {
			if ( isset( $attr[ $mla_page_parameter ] ) ) {
				$arguments[ $mla_page_parameter ] = $attr[ $mla_page_parameter ];
			} else {
				$arguments[ $mla_page_parameter ] = NULL;
			}
		}

		if ( !empty( $arguments['ids'] ) ) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
			if ( empty( $attr['orderby'] ) ) {
				$arguments['orderby'] = 'post__in';
			}

			$arguments['include'] = $arguments['ids'];
		}
		unset( $arguments['ids'] );

		if ( ! is_null( $return_found_rows ) ) {
			$arguments = apply_filters( 'mla_gallery_query_arguments', $arguments );
		}

		/*
		 * Extract taxonomy arguments
		 */
		$query_arguments = array();
		if ( ! empty( $attr ) ) {
			$all_taxonomies = get_taxonomies( array ( 'show_ui' => true ), 'names' );
			$simple_tax_queries = array();
			foreach ( $attr as $key => $value ) {
				if ( 'tax_query' == $key ) {
					if ( is_array( $value ) ) {
						$query_arguments[ $key ] = $value;
					} else {
						$tax_query = NULL;
						$value = self::_sanitize_query_specification( $value );

						/*
						 * Replace invalid queries from "where-used" callers with a harmless equivalent
						 */
						if ( $where_used_query && ( false !== strpos( $value, '{+' ) ) ) {
							$value = "array( array( 'taxonomy' => 'none', 'field' => 'slug', 'terms' => 'none' ) )";
						}

						$function = @create_function('', 'return ' . $value . ';' );
						if ( is_callable( $function ) ) {
							$tax_query = $function();
						}

						if ( is_array( $tax_query ) ) {
							$query_arguments[ $key ] = $tax_query;
							break; // Done - the tax_query overrides all other taxonomy parameters
						} else {
							return '<p>' . __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Invalid mla_gallery', 'media-library-assistant' ) . ' tax_query = ' . var_export( $value, true ) . '</p>';
						}
					} // not array
				}  /* tax_query */ elseif ( array_key_exists( $key, $all_taxonomies ) ) {
					$simple_tax_queries[ $key ] = implode(',', array_filter( array_map( 'trim', explode( ',', $value ) ) ) );
				} // array_key_exists
			} //foreach $attr

			/*
			 * One of five outcomes:
			 * 1) An explicit tax_query was found; use it and ignore all other taxonomy parameters
			 * 2) No tax query is present; no further processing required
			 * 3) Two or more simple tax queries are present; compose a tax_query
			 * 4) One simple tax query and (tax_operator or tax_include_children) are present; compose a tax_query
			 * 5) One simple tax query is present; use it as-is or convert 'category' to 'category_name'
			 */
			if ( isset( $query_arguments['tax_query'] ) || empty( $simple_tax_queries ) ) {
				// No further action required
			} elseif ( ( 1 < count( $simple_tax_queries ) ) || isset( $attr['tax_operator'] ) || isset( $attr['tax_include_children'] ) ) {
				// Build a tax_query
				if  ( 1 < count( $simple_tax_queries ) ) {
					$tax_relation = 'AND';
					if ( isset( $attr['tax_relation'] ) ) {
						if ( 'OR' == strtoupper( $attr['tax_relation'] ) ) {
							$tax_relation = 'OR';
						}
					}
					$tax_query = array ('relation' => $tax_relation );
				} else {
					$tax_query = array ();
				}

				// Validate other tax_query parameters or set defaults
				$tax_operator = 'IN';
				if ( isset( $attr['tax_operator'] ) ) {
					$attr_value = strtoupper( $attr['tax_operator'] );
					if ( in_array( $attr_value, array( 'IN', 'NOT IN', 'AND' ) ) ) {
						$tax_operator = $attr_value;
					}
				}

				$tax_include_children = true;
				if ( isset( $attr['tax_include_children'] ) ) {
					if ( 'FALSE' == strtoupper( $attr['tax_include_children'] ) ) {
						$tax_include_children = false;
					}
				}

				foreach( $simple_tax_queries as $key => $value ) {
					$tax_query[] =	array( 'taxonomy' => $key, 'field' => 'slug', 'terms' => explode( ',', $value ), 'operator' => $tax_operator, 'include_children' => $tax_include_children );
				}

				$query_arguments['tax_query'] = $tax_query;
			} else {
				// exactly one simple query is present
				if ( isset( $simple_tax_queries['category'] ) ) {
					$arguments['category_name'] = $simple_tax_queries['category'];
				} else {
					$query_arguments = $simple_tax_queries;
				}
			}
		} // ! empty

		/*
		 * $query_arguments has been initialized in the taxonomy code above.
		 */
		$is_tax_query = ! ($use_children = empty( $query_arguments ));
		foreach ($arguments as $key => $value ) {
			/*
			 * There are several "fallthru" cases in this switch statement that decide 
			 * whether or not to limit the query to children of a specific post.
			 */
			$children_ok = true;
			switch ( $key ) {
			case 'post_parent':
				switch ( strtolower( $value ) ) {
				case 'all':
					$value = NULL;
					$use_children = false;
					break;
				case 'any':
					self::$query_parameters['post_parent'] = 'any';
					$value = NULL;
					$use_children = false;
					break;
				case 'current':
					$value = $post_parent;
					$use_children = true;
					break;
				case 'none':
					self::$query_parameters['post_parent'] = 'none';
					$value = NULL;
					$use_children = false;
					break;
				}
				// fallthru
			case 'id':
				if ( is_numeric( $value ) ) {
					$query_arguments[ $key ] = intval( $value );
					if ( ! $children_ok ) {
						$use_children = false;
					}
				}
				unset( $arguments[ $key ] );
				break;
			case 'numberposts':
			case 'posts_per_page':
			case 'posts_per_archive_page':
				if ( is_numeric( $value ) ) {
					$value =  intval( $value );
					if ( ! empty( $value ) ) {
						$query_arguments[ $key ] = $value;
					}
				}
				unset( $arguments[ $key ] );
				break;
			case 'meta_value_num':
				$children_ok = false;
				// fallthru
			case 'offset':
				if ( is_numeric( $value ) ) {
					$query_arguments[ $key ] = intval( $value );
					if ( ! $children_ok ) {
						$use_children = false;
					}
				}
				unset( $arguments[ $key ] );
				break;
			case 'paged':
				if ( 'current' == strtolower( $value ) ) {
					/*
					 * Note: The query variable 'page' holds the pagenumber for a single paginated
					 * Post or Page that includes the <!--nextpage--> Quicktag in the post content. 
					 */
					if ( get_query_var('page') ) {
						$query_arguments[ $key ] = get_query_var('page');
					} else {
						$query_arguments[ $key ] = (get_query_var('paged')) ? get_query_var('paged') : 1;
					}
				} elseif ( is_numeric( $value ) ) {
					$query_arguments[ $key ] = intval( $value );
				} elseif ( '' === $value ) {
					$query_arguments[ $key ] = 1;
				}

				unset( $arguments[ $key ] );
				break;
			case  $mla_page_parameter :
			case 'mla_paginate_total':
				if ( is_numeric( $value ) ) {
					$query_arguments[ $key ] = intval( $value );
				} elseif ( '' === $value ) {
					$query_arguments[ $key ] = 1;
				}

				unset( $arguments[ $key ] );
				break;
			case 'author':
			case 'cat':
			case 'tag_id':
				if ( ! empty( $value ) ) {
					if ( is_array( $value ) ) {
						$query_arguments[ $key ] = array_filter( $value );
					} else {
						$query_arguments[ $key ] = array_filter( array_map( 'intval', explode( ",", $value ) ) );
					}

					if ( 1 == count( $query_arguments[ $key ] ) ) {
						$query_arguments[ $key ] = $query_arguments[ $key ][0];
					} else {
						$query_arguments[ $key ] = implode(',', $query_arguments[ $key ] );
					}

					$use_children = false;
				}
				unset( $arguments[ $key ] );
				break;
			case 'category__and':
			case 'category__in':
			case 'category__not_in':
			case 'tag__and':
			case 'tag__in':
			case 'tag__not_in':
			case 'include':
				$children_ok = false;
				// fallthru
			case 'exclude':
				if ( ! empty( $value ) ) {
					if ( is_array( $value ) ) {
						$value = array_filter( $value );
					} else {
						$value = array_filter( array_map( 'intval', explode( ",", $value ) ) );
					}

					if ( ! empty( $value ) ) {
						$query_arguments[ $key ] = $value;

						if ( ! $children_ok ) {
							$use_children = false;
						}
					}
				}
				unset( $arguments[ $key ] );
				break;
			case 'tag_slug__and':
			case 'tag_slug__in':
				if ( ! empty( $value ) ) {
					if ( is_array( $value ) ) {
						$query_arguments[ $key ] = $value;
					} else {
						$query_arguments[ $key ] = array_filter( array_map( 'trim', explode( ",", $value ) ) );
					}

					$use_children = false;
				}
				unset( $arguments[ $key ] );
				break;
			case 'nopaging': // boolean value, default false
				if ( ! empty( $value ) && ( 'false' != strtolower( $value ) ) ) {
					$query_arguments[ $key ] = true;
				}

				unset( $arguments[ $key ] );
				break;
			// boolean values, default true
			case 'cache_results':
			case 'update_post_meta_cache':
			case 'update_post_term_cache':
				if ( ! empty( $value ) && ( 'true' != strtolower( $value ) ) ) {
					$query_arguments[ $key ] = false;
				}

				unset( $arguments[ $key ] );
				break;
			case 'sentence':
			case 'exact':
				if ( ! empty( $value ) && ( 'true' == strtolower( $value ) ) ) {
					MLAQuery::$search_parameters[ $key ] = true;
				} else {
					MLAQuery::$search_parameters[ $key ] = false;
				}

				unset( $arguments[ $key ] );
				break;
			case 'mla_search_connector':
			case 'mla_phrase_connector':
			case 'mla_term_connector':
				if ( ! empty( $value ) && ( 'OR' == strtoupper( $value ) ) ) {
					MLAQuery::$search_parameters[ $key ] = 'OR';
				} else {
					MLAQuery::$search_parameters[ $key ] = 'AND';
				}

				unset( $arguments[ $key ] );
				break;
			case 'mla_terms_phrases':
				$children_ok = false;
				// fallthru
			case 'mla_terms_taxonomies':
			case 'mla_search_fields':
				if ( ! empty( $value ) ) {
					MLAQuery::$search_parameters[ $key ] = $value;

					if ( ! $children_ok ) {
						$use_children = false;
					}
				}

				unset( $arguments[ $key ] );
				break;
			case 's':
				MLAQuery::$search_parameters['s'] = trim( $value );
				// fallthru
			case 'author_name':
			case 'category_name':
			case 'tag':
			case 'meta_key':
			case 'meta_value':
			case 'meta_compare':
				$children_ok = false;
				// fallthru
			case 'post_type':
			case 'post_status':
			case 'post_mime_type':
			case 'orderby':
				if ( ! empty( $value ) ) {
					$query_arguments[ $key ] = $value;

					if ( ! $children_ok ) {
						$use_children = false;
					}
				}

				unset( $arguments[ $key ] );
				break;
			case 'order':
				if ( ! empty( $value ) ) {
					$value = strtoupper( $value );
					if ( in_array( $value, array( 'ASC', 'DESC' ) ) ) {
						$query_arguments[ $key ] = $value;
					}
				}

				unset( $arguments[ $key ] );
				break;
			case 'date_query':
				if ( ! empty( $value ) ) {
					if ( is_array( $value ) ) {
						$query_arguments[ $key ] = $value;
					} else {
						$date_query = NULL;
						$value = self::_sanitize_query_specification( $value );

						/*
						 * Replace invalid queries from "where-used" callers with a harmless equivalent
						 */
						if ( $where_used_query && ( false !== strpos( $value, '{+' ) ) ) {
							$value = "array( array( 'key' => 'unlikely', 'value' => 'none or otherwise unlikely' ) )";
						}

						$function = @create_function('', 'return ' . $value . ';' );
						if ( is_callable( $function ) ) {
							$date_query = $function();
						}

						if ( is_array( $date_query ) ) {
							$query_arguments[ $key ] = $date_query;
						} else {
							return '<p>' . __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Invalid mla_gallery', 'media-library-assistant' ) . ' date_query = ' . var_export( $value, true ) . '</p>';
						}
					} // not array

					$use_children = false;
				}
				unset( $arguments[ $key ] );
				break;
			case 'meta_query':
				if ( ! empty( $value ) ) {
					if ( is_array( $value ) ) {
						$query_arguments[ $key ] = $value;
					} else {
						$meta_query = NULL;
						$value = self::_sanitize_query_specification( $value );

						/*
						 * Replace invalid queries from "where-used" callers with a harmless equivalent
						 */
						if ( $where_used_query && ( false !== strpos( $value, '{+' ) ) ) {
							$value = "array( array( 'key' => 'unlikely', 'value' => 'none or otherwise unlikely' ) )";
						}

						$function = @create_function('', 'return ' . $value . ';' );
						if ( is_callable( $function ) ) {
							$meta_query = $function();
						}

						if ( is_array( $meta_query ) ) {
							$query_arguments[ $key ] = $meta_query;
						} else {
							return '<p>' . __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Invalid mla_gallery', 'media-library-assistant' ) . ' meta_query = ' . var_export( $value, true ) . '</p>';
						}
					} // not array

					$use_children = false;
				}
				unset( $arguments[ $key ] );
				break;
			case 'fields':
				if ( ! empty( $value ) ) {
					$value = strtolower( $value );
					if ( in_array( $value, array( 'ids', 'id=>parent' ) ) ) {
						$query_arguments[ $key ] = $value;
					}
				}

				unset( $arguments[ $key ] );
				break;
			default:
				// ignore anything else
			} // switch $key
		} // foreach $arguments 
 
		/*
		 * Decide whether to use a "get_children" style query
		 */
		self::$query_parameters['disable_tax_join'] = $is_tax_query && ! $use_children;
		if ( $use_children && ! isset( $query_arguments['post_parent'] ) ) {
			if ( ! isset( $query_arguments['id'] ) ) {
				$query_arguments['post_parent'] = $post_parent;
			} else {
				$query_arguments['post_parent'] = $query_arguments['id'];
			}

			unset( $query_arguments['id'] );
		}

		if ( isset( $query_arguments['numberposts'] ) && ! isset( $query_arguments['posts_per_page'] )) {
			$query_arguments['posts_per_page'] = $query_arguments['numberposts'];
		}
		unset( $query_arguments['numberposts'] );

		/*
		 * MLA pagination will override WordPress pagination
		 */
		if ( isset( $query_arguments[ $mla_page_parameter ] ) ) {
			unset( $query_arguments['nopaging'] );
			unset( $query_arguments['offset'] );
			unset( $query_arguments['paged'] );

			if ( isset( $query_arguments['mla_paginate_total'] ) && ( $query_arguments[ $mla_page_parameter ] > $query_arguments['mla_paginate_total'] ) ) {
				$query_arguments['offset'] = 0x7FFFFFFF; // suppress further output
			} else {
				$query_arguments['paged'] = $query_arguments[ $mla_page_parameter ];
			}
		} else {
			if ( isset( $query_arguments['posts_per_page'] ) || isset( $query_arguments['posts_per_archive_page'] ) ||
				isset( $query_arguments['paged'] ) || isset( $query_arguments['offset'] ) ) {
				unset( $query_arguments['nopaging'] );
			}
		}
		unset( $query_arguments[ $mla_page_parameter ] );
		unset( $query_arguments['mla_paginate_total'] );

		if ( isset( $query_arguments['post_mime_type'] ) && ('all' == strtolower( $query_arguments['post_mime_type'] ) ) ) {
			unset( $query_arguments['post_mime_type'] );
		}

		if ( ! empty($query_arguments['include']) ) {
			$incposts = wp_parse_id_list( $query_arguments['include'] );
			$query_arguments['posts_per_page'] = count($incposts);  // only the number of posts included
			$query_arguments['post__in'] = $incposts;
			unset( $query_arguments['include'] );
		} elseif ( ! empty($query_arguments['exclude']) ) {
			$query_arguments['post__not_in'] = wp_parse_id_list( $query_arguments['exclude'] );
			unset( $query_arguments['exclude'] );
		}

		$query_arguments['ignore_sticky_posts'] = true;
		$query_arguments['no_found_rows'] = is_null( $return_found_rows ) ? true : ! $return_found_rows;

		/*
		 * We will always handle "orderby" in our filter
		 */ 
		self::$query_parameters['orderby'] = self::_validate_sql_orderby( $query_arguments );
		if ( false === self::$query_parameters['orderby'] ) {
			unset( self::$query_parameters['orderby'] );
		}

		unset( $query_arguments['orderby'] );
		unset( $query_arguments['order'] );

		if ( self::$mla_debug ) {
			add_filter( 'posts_clauses', 'MLAShortcode_Support::mla_shortcode_query_posts_clauses_filter', 0x7FFFFFFF, 1 );
			add_filter( 'posts_clauses_request', 'MLAShortcode_Support::mla_shortcode_query_posts_clauses_request_filter', 0x7FFFFFFF, 1 );
		}

		add_filter( 'posts_join', 'MLAShortcode_Support::mla_shortcode_query_posts_join_filter', 0x7FFFFFFF, 1 );
		add_filter( 'posts_where', 'MLAShortcode_Support::mla_shortcode_query_posts_where_filter', 0x7FFFFFFF, 1 );
		add_filter( 'posts_orderby', 'MLAShortcode_Support::mla_shortcode_query_posts_orderby_filter', 0x7FFFFFFF, 1 );

		/*
		 * Handle the keyword and terms search in the posts_search filter.
		 * One or both of 'mla_terms_phrases' and 's' must be present to
		 * trigger the search.
		 */
		if ( empty( MLAQuery::$search_parameters['mla_terms_phrases'] ) && empty( MLAQuery::$search_parameters['s'] ) ) {
			MLAQuery::$search_parameters = array( 'debug' => 'none' );
		} else {
			/*
			 * Convert Terms Search parameters to the filter's requirements.
			 * mla_terms_taxonomies is shared with keyword search.
			 */
			if ( empty( MLAQuery::$search_parameters['mla_terms_taxonomies'] ) ) {
				MLAQuery::$search_parameters['mla_terms_search']['taxonomies'] = MLACore::mla_supported_taxonomies( 'term-search' );
			} else {
				MLAQuery::$search_parameters['mla_terms_search']['taxonomies'] = array_filter( array_map( 'trim', explode( ',', MLAQuery::$search_parameters['mla_terms_taxonomies'] ) ) );
			}

			if ( ! empty( MLAQuery::$search_parameters['mla_terms_phrases'] ) ) {
				MLAQuery::$search_parameters['mla_terms_search']['phrases'] = MLAQuery::$search_parameters['mla_terms_phrases'];

				if ( empty( MLAQuery::$search_parameters['mla_phrase_connector'] ) ) {
					MLAQuery::$search_parameters['mla_terms_search']['radio_phrases'] = 'AND';
				} else {
					MLAQuery::$search_parameters['mla_terms_search']['radio_phrases'] = MLAQuery::$search_parameters['mla_phrase_connector'];
				}

				if ( empty( MLAQuery::$search_parameters['mla_term_connector'] ) ) {
					MLAQuery::$search_parameters['mla_terms_search']['radio_terms'] = 'OR';
				} else {
					MLAQuery::$search_parameters['mla_terms_search']['radio_terms'] = MLAQuery::$search_parameters['mla_phrase_connector'];
				}
			}

			unset( MLAQuery::$search_parameters['mla_terms_phrases'] );
			unset( MLAQuery::$search_parameters['mla_terms_taxonomies'] );
			unset( MLAQuery::$search_parameters['mla_phrase_connector'] );
			unset( MLAQuery::$search_parameters['mla_term_connector'] );

			if ( empty( MLAQuery::$search_parameters['mla_search_fields'] ) ) {
				MLAQuery::$search_parameters['mla_search_fields'] = array( 'title', 'content' );
			} else {
				MLAQuery::$search_parameters['mla_search_fields'] = array_filter( array_map( 'trim', explode( ',', MLAQuery::$search_parameters['mla_search_fields'] ) ) );
				MLAQuery::$search_parameters['mla_search_fields'] = array_intersect( array( 'title', 'content', 'excerpt', 'name', 'terms' ), MLAQuery::$search_parameters['mla_search_fields'] );

				/*
				 * Look for keyword search including 'terms' 
				 */
				foreach ( MLAQuery::$search_parameters['mla_search_fields'] as $index => $field ) {
					if ( 'terms' == $field ) {
						if ( isset( MLAQuery::$search_parameters['mla_terms_search']['phrases'] ) ) {
							/*
							 * The Terms Search overrides any terms-based keyword search for now; too complicated.
							 */
							unset ( MLAQuery::$search_parameters['mla_search_fields'][ $index ] );
						} else {
							MLAQuery::$search_parameters['mla_search_taxonomies'] = MLAQuery::$search_parameters['mla_terms_search']['taxonomies'];
							unset( MLAQuery::$search_parameters['mla_terms_search']['taxonomies'] );
						}
					} // terms in search fields
				}
			} // mla_search_fields present

			if ( empty( MLAQuery::$search_parameters['mla_search_connector'] ) ) {
				MLAQuery::$search_parameters['mla_search_connector'] = 'AND';
			}

			if ( empty( MLAQuery::$search_parameters['sentence'] ) ) {
				MLAQuery::$search_parameters['sentence'] = false;
			}

			if ( empty( MLAQuery::$search_parameters['exact'] ) ) {
				MLAQuery::$search_parameters['exact'] = false;
			}

			MLAQuery::$search_parameters['debug'] = self::$mla_debug ? 'shortcode' : 'none';

			add_filter( 'posts_search', 'MLAQuery::mla_query_posts_search_filter', 10, 2 );
			add_filter( 'posts_groupby', 'MLAQuery::mla_query_posts_groupby_filter' );
		}

		if ( self::$mla_debug ) {
			global $wp_filter;

			foreach( $wp_filter['posts_where'] as $priority => $filters ) {
				$debug_message = '<strong>mla_debug $wp_filter[posts_where]</strong> priority = ' . var_export( $priority, true ) . '<br />';
				foreach ( $filters as $name => $descriptor ) {
					$debug_message .= 'filter name = ' . var_export( $name, true ) . '<br />';
				}
				MLACore::mla_debug_add( $debug_message );
			}

			foreach( $wp_filter['posts_orderby'] as $priority => $filters ) {
				$debug_message = '<strong>mla_debug $wp_filter[posts_orderby]</strong> priority = ' . var_export( $priority, true ) . '<br />';
				foreach ( $filters as $name => $descriptor ) {
					$debug_message .= 'filter name = ' . var_export( $name, true ) . '<br />';
				}
				MLACore::mla_debug_add( $debug_message );
			}
		}

		/*
		 * Disable Relevanssi - A Better Search, v3.2 by Mikko Saari 
		 * relevanssi_prevent_default_request( $request, $query )
		 * apply_filters('relevanssi_admin_search_ok', $admin_search_ok, $query );
		 */
		if ( function_exists( 'relevanssi_prevent_default_request' ) ) {
			add_filter( 'relevanssi_admin_search_ok', 'MLAData::mla_query_relevanssi_admin_search_ok_filter' );
		}

		if ( class_exists( 'MLA_Polylang' ) ) {
			$query_arguments = apply_filters( 'mla_get_shortcode_attachments_final_terms', $query_arguments, $return_found_rows );
		}
		
		MLAShortcodes::$mla_gallery_wp_query_object = new WP_Query;
		$attachments = MLAShortcodes::$mla_gallery_wp_query_object->query( $query_arguments );

		/*
		 * $return_found_rows is used to indicate that the call comes from gallery_shortcode(),
		 * which is the only call that supplies it.
		 */
		if ( is_null( $return_found_rows ) ) {
			$return_found_rows = false;
		} else  {
			do_action( 'mla_gallery_wp_query_object', $query_arguments );
		}

		if ( $return_found_rows ) {
			$attachments['found_rows'] = MLAShortcodes::$mla_gallery_wp_query_object->found_posts;
		}

		if ( ! empty( MLAQuery::$search_parameters ) ) {
			remove_filter( 'posts_groupby', 'MLAQuery::mla_query_posts_groupby_filter' );
			remove_filter( 'posts_search', 'MLAQuery::mla_query_posts_search_filter' );
		}

		if ( function_exists( 'relevanssi_prevent_default_request' ) ) {
			remove_filter( 'relevanssi_admin_search_ok', 'MLAData::mla_query_relevanssi_admin_search_ok_filter' );
		}

		remove_filter( 'posts_join', 'MLAShortcode_Support::mla_shortcode_query_posts_join_filter', 0x7FFFFFFF );
		remove_filter( 'posts_where', 'MLAShortcode_Support::mla_shortcode_query_posts_where_filter', 0x7FFFFFFF );
		remove_filter( 'posts_orderby', 'MLAShortcode_Support::mla_shortcode_query_posts_orderby_filter', 0x7FFFFFFF );

		if ( self::$mla_debug ) {
			remove_filter( 'posts_clauses', 'MLAShortcode_Support::mla_shortcode_query_posts_clauses_filter', 0x7FFFFFFF );
			remove_filter( 'posts_clauses_request', 'MLAShortcode_Support::mla_shortcode_query_posts_clauses_request_filter', 0x7FFFFFFF );

			MLACore::mla_debug_add( '<strong>' . __( 'mla_debug query', 'media-library-assistant' ) . '</strong> = ' . var_export( $query_arguments, true ) );
			MLACore::mla_debug_add( '<strong>' . __( 'mla_debug request', 'media-library-assistant' ) . '</strong> = ' . var_export( MLAShortcodes::$mla_gallery_wp_query_object->request, true ) );
			MLACore::mla_debug_add( '<strong>' . __( 'mla_debug query_vars', 'media-library-assistant' ) . '</strong> = ' . var_export( MLAShortcodes::$mla_gallery_wp_query_object->query_vars, true ) );
			MLACore::mla_debug_add( '<strong>' . __( 'mla_debug post_count', 'media-library-assistant' ) . '</strong> = ' . var_export( MLAShortcodes::$mla_gallery_wp_query_object->post_count, true ) );
		}

		MLAShortcodes::$mla_gallery_wp_query_object = NULL;
		return $attachments;
	}

	/**
	 * Filters the JOIN clause for shortcode queries
	 * 
	 * Defined as public because it's a filter.
	 *
	 * @since 2.20
	 *
	 * @param	string	query clause before modification
	 *
	 * @return	string	query clause after item modification
	 */
	public static function mla_shortcode_query_posts_join_filter( $join_clause ) {
		global $wpdb;

		/*
		 * Set for taxonomy queries unless post_parent=current. If true, we must disable
		 * the LEFT JOIN clause that get_posts() adds to taxonomy queries.
		 * We leave the clause in because the WHERE clauses refer to "p2.".
		 */
		if ( self::$query_parameters['disable_tax_join'] ) {
			$join_clause = str_replace( " LEFT JOIN {$wpdb->posts} AS p2 ON ({$wpdb->posts}.post_parent = p2.ID) ", " LEFT JOIN {$wpdb->posts} AS p2 ON (p2.ID = p2.ID) ", $join_clause );
		}

		/*
		 * These joins support the 'terms' search_field
		 */
		if ( isset( MLAQuery::$search_parameters['tax_terms_count'] ) ) {
			$tax_index = 0;
			$tax_clause = '';

			while ( $tax_index < MLAQuery::$search_parameters['tax_terms_count'] ) {
				$prefix = 'mlatt' . $tax_index++;
				$tax_clause .= sprintf( ' INNER JOIN %1$s AS %2$s ON (%3$s.ID = %2$s.object_id)', $wpdb->term_relationships, $prefix, $wpdb->posts );
			}

			$join_clause .= $tax_clause;
		}


		return $join_clause;
	}

	/**
	 * Filters the WHERE clause for shortcode queries
	 * 
	 * Captures debug information. Adds whitespace to the post_type = 'attachment'
	 * phrase to circumvent subsequent Role Scoper modification of the clause.
	 * Handles post_parent "any" and "none" cases.
	 * Defined as public because it's a filter.
	 *
	 * @since 2.20
	 *
	 * @param	string	query clause before modification
	 *
	 * @return	string	query clause after modification
	 */
	public static function mla_shortcode_query_posts_where_filter( $where_clause ) {
		global $table_prefix;

		if ( self::$mla_debug ) {
			$old_clause = $where_clause;
			MLACore::mla_debug_add( '<strong>' . __( 'mla_debug WHERE filter', 'media-library-assistant' ) . '</strong> = ' . var_export( $where_clause, true ) );
		}

		if ( strpos( $where_clause, "post_type = 'attachment'" ) ) {
			$where_clause = str_replace( "post_type = 'attachment'", "post_type  =  'attachment'", $where_clause );
		}

		if ( isset( self::$query_parameters['post_parent'] ) ) {
			switch ( self::$query_parameters['post_parent'] ) {
			case 'any':
				$where_clause .= " AND {$table_prefix}posts.post_parent > 0";
				break;
			case 'none':
				$where_clause .= " AND {$table_prefix}posts.post_parent < 1";
				break;
			}
		}

		if ( self::$mla_debug && ( $old_clause != $where_clause ) ) {
			MLACore::mla_debug_add( '<strong>' . __( 'mla_debug modified WHERE filter', 'media-library-assistant' ) . '</strong> = ' . var_export( $where_clause, true ) );
		}

		return $where_clause;
	}

	/**
	 * Filters the ORDERBY clause for shortcode queries
	 * 
	 * This is an enhanced version of the code found in wp-includes/query.php, function get_posts.
	 * Defined as public because it's a filter.
	 *
	 * @since 2.20
	 *
	 * @param	string	query clause before modification
	 *
	 * @return	string	query clause after modification
	 */
	public static function mla_shortcode_query_posts_orderby_filter( $orderby_clause ) {
		global $wpdb;

		if ( self::$mla_debug ) {
			$replacement = isset( self::$query_parameters['orderby'] ) ? var_export( self::$query_parameters['orderby'], true ) : 'none';
			MLACore::mla_debug_add( '<strong>' . __( 'mla_debug ORDER BY filter, incoming', 'media-library-assistant' ) . '</strong> = ' . var_export( $orderby_clause, true ) . '<br>' . __( 'Replacement ORDER BY clause', 'media-library-assistant' ) . ' = ' . $replacement );
		}

		if ( isset( self::$query_parameters['orderby'] ) ) {
			return self::$query_parameters['orderby'];
		}

		return $orderby_clause;
	}

	/**
	 * Filters all clauses for shortcode queries, pre caching plugins
	 * 
	 * This is for debug purposes only.
	 * Defined as public because it's a filter.
	 *
	 * @since 2.20
	 *
	 * @param	array	query clauses before modification
	 *
	 * @return	array	query clauses after modification (none)
	 */
	public static function mla_shortcode_query_posts_clauses_filter( $pieces ) {
		MLACore::mla_debug_add( '<strong>' . __( 'mla_debug posts_clauses filter', 'media-library-assistant' ) . '</strong> = ' . var_export( $pieces, true ) );

		return $pieces;
	}

	/**
	 * Filters all clauses for shortcode queries, post caching plugins
	 * 
	 * This is for debug purposes only.
	 * Defined as public because it's a filter.
	 *
	 * @since 2.20
	 *
	 * @param	array	query clauses before modification
	 *
	 * @return	array	query clauses after modification (none)
	 */
	public static function mla_shortcode_query_posts_clauses_request_filter( $pieces ) {
		MLACore::mla_debug_add( '<strong>' . __( 'mla_debug posts_clauses_request filter', 'media-library-assistant' ) . '</strong> = ' . var_export( $pieces, true ) );

		return $pieces;
	}

	/**
	 * Data selection parameters for [mla_tag_cloud]
	 *
	 * @since 2.20
	 *
	 * @var	array
	 */
	private static $mla_get_terms_parameters = array(
		'taxonomy' => 'post_tag',
		'post_mime_type' => 'all',
		'post_type' => 'attachment',
		'post_status' => 'inherit',
		'ids' => array(),
		'fields' => 't.term_id, t.name, t.slug, t.term_group, tt.term_taxonomy_id, tt.taxonomy, tt.description, tt.parent, COUNT(p.ID) AS `count`',
		'include' => '',
		'exclude' => '',
		'parent' => '',
		'minimum' => 0,
		'no_count' => false,
		'number' => 0,
		'orderby' => 'name',
		'order' => 'ASC',
		'no_orderby' => false,
		'preserve_case' => false,
		'pad_counts' => false,
		'limit' => 0,
		'offset' => 0
	);


	/**
	 * Retrieve the terms in one or more taxonomies.
	 *
	 * Alternative to WordPress /wp-includes/taxonomy.php function get_terms() that provides
	 * an accurate count of attachments associated with each term.
	 *
	 * taxonomy - string containing one or more (comma-delimited) taxonomy names
	 * or an array of taxonomy names. Default 'post_tag'.
	 *
	 * post_mime_type - MIME type(s) of the items to include in the term-specific counts. Default 'all'.
	 *
	 * post_type - The post type(s) of the items to include in the term-specific counts.
	 * The default is "attachment". 
	 *
	 * post_status - The post status value(s) of the items to include in the term-specific counts.
	 * The default is "inherit".
	 *
	 * ids - A comma-separated list of attachment ID values for an item-specific cloud.
	 *
	 * include - An array, comma- or space-delimited string of term ids to include
	 * in the return array.
	 *
	 * exclude - An array, comma- or space-delimited string of term ids to exclude
	 * from the return array. If 'include' is non-empty, 'exclude' is ignored.
	 *
	 * parent - term_id of the terms' immediate parent; 0 for top-level terms.
	 *
	 * minimum - minimum number of attachments a term must have to be included. Default 0.
	 *
	 * no_count - 'true', 'false' (default) to suppress term-specific attachment-counting process.
	 *
	 * number - maximum number of term objects to return. Terms are ordered by count,
	 * descending and then by term_id before this value is applied. Default 0.
	 *
	 * orderby - 'count', 'id', 'name' (default), 'none', 'random', 'slug'
	 *
	 * order - 'ASC' (default), 'DESC'
	 *
	 * no_orderby - 'true', 'false' (default) to suppress ALL sorting clauses else false.
	 *
	 * preserve_case - 'true', 'false' (default) to make orderby case-sensitive.
	 *
	 * pad_counts - 'true', 'false' (default) to to include the count of all children in their parents' count.
	 *
	 * limit - final number of term objects to return, for pagination. Default 0.
	 *
	 * offset - number of term objects to skip, for pagination. Default 0.
	 *
	 * @since 2.20
	 *
	 * @param	array	taxonomies to search and query parameters
	 *
	 * @return	array	array of term objects, empty if none found
	 */
	public static function mla_get_terms( $attr ) {
		global $wpdb;

		/*
		 * Make sure $attr is an array, even if it's empty
		 */
		if ( empty( $attr ) ) {
			$attr = array();
		} elseif ( is_string( $attr ) ) {
			$attr = shortcode_parse_atts( $attr );
		}

		/*
		 * Merge input arguments with defaults
		 */
		$attr = apply_filters( 'mla_get_terms_query_attributes', $attr );
		$arguments = shortcode_atts( self::$mla_get_terms_parameters, $attr );
		$arguments = apply_filters( 'mla_get_terms_query_arguments', $arguments );

		/*
		 * Build an array of individual clauses that can be filtered
		 */
		$clauses = array( 'fields' => '', 'join' => '', 'where' => '', 'order' => '', 'orderby' => '', 'limits' => '', );

		/*
		 * If we're not counting attachments per term, strip
		 * post fields out of list and adjust the orderby  value
		 */
		if ( $no_count = 'true' == (string) $arguments['no_count'] ) {
			$field_array = explode( ',', $arguments['fields'] );
			foreach ( $field_array as $index => $field ) {
				if ( false !== strpos( $field, 'p.' ) ) {
					unset( $field_array[ $index ] );
				}
			}
			$arguments['fields'] = implode( ',', $field_array );
			$arguments['minimum'] = 0;
			$arguments['post_mime_type'] = 'all';

			if ( 'count' ==strtolower( $arguments['orderby'] ) ) {
				$arguments['orderby'] = 'none';
			}
		}

		$clauses['fields'] = $arguments['fields'];

		$clause = array ( 'INNER JOIN `' . $wpdb->term_taxonomy . '` AS tt ON t.term_id = tt.term_id' );
		$clause_parameters = array();

		if ( ! $no_count ) {
			$clause[] = 'LEFT JOIN `' . $wpdb->term_relationships . '` AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id';
			$clause[] = 'LEFT JOIN `' . $wpdb->posts . '` AS p ON tr.object_id = p.ID';

			/*
			 * Add type and status constraints
			 */
			if ( is_array( $arguments['post_type'] ) ) {
				$post_types = $arguments['post_type'];
			} else {
				$post_types = array( $arguments['post_type'] );
			}

			$placeholders = array();
			foreach ( $post_types as $post_type ) {
				$placeholders[] = '%s';
				$clause_parameters[] = $post_type;
			}

			$clause[] = 'AND p.post_type IN (' . join( ',', $placeholders ) . ')';

			if ( is_array( $arguments['post_status'] ) ) {
				$post_stati = $arguments['post_status'];
			} else {
				$post_stati = array( $arguments['post_status'] );
			}

			$placeholders = array();
			foreach ( $post_stati as $post_status ) {
				if ( ( 'private' != $post_status ) || is_user_logged_in() ) {
					$placeholders[] = '%s';
					$clause_parameters[] = $post_status;
				}
			}

			$clause[] = 'AND p.post_status IN (' . join( ',', $placeholders ) . ')';
		}

		$clause =  join(' ', $clause);
		$clauses['join'] = $wpdb->prepare( $clause, $clause_parameters );

		/*
		 * Start WHERE clause with a taxonomy constraint
		 */
		if ( is_array( $arguments['taxonomy'] ) ) {
			$taxonomies = $arguments['taxonomy'];
		} else {
			$taxonomies = array( $arguments['taxonomy'] );
		}

		foreach ( $taxonomies as $taxonomy ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				$error = new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy', 'media-library-assistant' ), $taxonomy );
				return $error;
			}
		}

		$clause_parameters = array();
		$placeholders = array();
		foreach ($taxonomies as $taxonomy) {
		    $placeholders[] = '%s';
			$clause_parameters[] = $taxonomy;
		}

		$clause = array( 'tt.taxonomy IN (' . join( ',', $placeholders ) . ')' );

		/*
		 * The "ids" parameter can build an item-specific cloud.
		 * Compile a list of all the terms assigned to the items.
		 */
		if ( ! empty( $arguments['ids'] ) && empty( $arguments['include'] ) ) {
			$ids = wp_parse_id_list( $arguments['ids'] );
		    $placeholders = implode( "','", $ids );
			$clause[] = "AND p.ID IN ( '{$placeholders}' )";

			$includes = array();
			foreach ( $ids as $id ) {
				foreach ($taxonomies as $taxonomy) {
					$terms = get_the_terms( $id, $taxonomy );
					if ( is_array( $terms ) ) {
						foreach( $terms as $term ) {
							$includes[ $term->term_id ] = $term->term_id;
						} // terms
					}
				} // taxonomies
			} // ids

			/*
			 * If there are no terms we want an empty cloud
			 */
			if ( empty( $includes ) ) {
				$arguments['include'] = (string) 0x7FFFFFFF;
			} else {
				ksort( $includes );
				$arguments['include'] = implode( ',', $includes );
			}
		}

		/*
		 * Add include/exclude and parent constraints to WHERE cluse
		 */
		if ( ! empty( $arguments['include'] ) ) {
		    $placeholders = implode( "','", wp_parse_id_list( $arguments['include'] ) );
			$clause[] = "AND t.term_id IN ( '{$placeholders}' )";
		} elseif ( ! empty( $arguments['exclude'] ) ) {
		    $placeholders = implode( "','", wp_parse_id_list( $arguments['exclude'] ) );
			$clause[] = "AND t.term_id NOT IN ( '{$placeholders}' )";
		}

		if ( '' !== $arguments['parent'] ) {
			$parent = (int) $arguments['parent'];
			$clause[] = "AND tt.parent = '{$parent}'";
		}

		if ( 'all' !== strtolower( $arguments['post_mime_type'] ) ) {
			$where = str_replace( '%', '%%', wp_post_mime_type_where( $arguments['post_mime_type'], 'p' ) );

			if ( 0 == absint( $arguments['minimum'] ) ) {
				$clause[] = ' AND ( p.post_mime_type IS NULL OR ' . substr( $where, 6 );
			} else {
				$clause[] = $where;
			}
		}

		$clause =  join(' ', $clause);
		$clauses['where'] = $wpdb->prepare( $clause, $clause_parameters );

		/*
		 * For the inner/initial query, always select the most popular terms
		 */
		if ( $arguments['no_orderby'] ) {
			$arguments['orderby'] = 'count';
			$arguments['order']  = 'DESC';
		}

		/*
		 * Add sort order
		 */
		$orderby = strtolower( $arguments['orderby'] );
		$order = strtoupper( $arguments['order'] );
		if ( 'DESC' != $order ) {
			$order = 'ASC';
		}

		$clauses['order'] = $order;
		$clauses['orderby'] = "ORDER BY {$orderby}";

		/*
		 * Count, Descending, is the default order so no further work
		 * is needed unless a different order is specified
		 */
		if ( 'count' != $orderby || 'DESC' != $order ) {
		    if ( 'true' == strtolower( $arguments['preserve_case'] ) ) {
				$binary_keys = array( 'name', 'slug', );
			} else {
				$binary_keys = array();
			}

			$allowed_keys = array(
				'empty_orderby_default' => 'name',
				'count' => 'count',
				'id' => 'term_id',
				'name' => 'name',
				'random' => 'RAND()',
				'slug' => 'slug',
			);

			$clause = 'ORDER BY ' . self::_validate_sql_orderby( $arguments, '', $allowed_keys, $binary_keys );
			$clauses['orderby'] = substr( $clause, 0, strrpos( $clause, ' ' . $order ) );
		} // add ORDER BY

		/*
		 * Add pagination
		 */
		$clauses['limits'] = '';
		$offset = absint( $arguments['offset'] );
		$limit = absint( $arguments['limit'] );
		if ( 0 < $offset && 0 < $limit ) {
			$clauses['limits'] = "LIMIT {$offset}, {$limit}";
		} elseif ( 0 < $limit ) {
			$clauses['limits'] = "LIMIT {$limit}";
		} elseif ( 0 < $offset ) {
			$clause_parameters = 0x7FFFFFFF;
			$clauses['limits'] = "LIMIT {$offset}, {$clause_parameters}";
		}

		$clauses = apply_filters( 'mla_get_terms_clauses', $clauses );

		/*
		 * Build the final query
		 */
		$query = array( 'SELECT' );
		$query[] = $clauses['fields'];
		$query[] = 'FROM `' . $wpdb->terms . '` AS t';
		$query[] = $clauses['join'];
		$query[] = 'WHERE (';
		$query[] = $clauses['where'];
		$query[] = ') GROUP BY tt.term_taxonomy_id';

		$clause_parameters = absint( $arguments['minimum'] );
		if ( 0 < $clause_parameters ) {
			$query[] = "HAVING count >= {$clause_parameters}";
		}

		/*
		 * If specifically told to omit the ORDER BY clause or the COUNT,
		 * supply a sort order for the initial/inner query only
		 */
		if ( ! ( $arguments['no_orderby'] || $no_count ) ) {
			$query[] = 'ORDER BY count DESC, t.term_id ASC';
		}

		/*
		 * Limit the total number of terms returned
		 */
		$terms_limit = absint( $arguments['number'] );
		if ( 0 < $terms_limit ) {
			$query[] = "LIMIT {$terms_limit}";
		}

		/*
		 * $final_clauses, if present, require an SQL subquery
		 */
		$final_clauses = array();
		if ( 'count' != $orderby || 'DESC' != $order ) {
			$final_clauses[] = $clauses['orderby'];
			$final_clauses[] = $clauses['order'];
		}

		if ( '' !== $clauses['limits'] ) {
			$final_clauses[] = $clauses['limits'];
		}

		/*
		 * If we're limiting the final results, we need to get an accurate total count first
		 */
		if ( ! $no_count && ( 0 < $offset || 0 < $limit ) ) {
			$count_query = 'SELECT COUNT(*) as count FROM (' . join(' ', $query) . ' ) as subQuery';
			$count = $wpdb->get_results( $count_query );
			$found_rows = $count[0]->count;
		}

		if ( ! empty( $final_clauses ) ) {
			if ( ! $no_count ) {
			    array_unshift($query, 'SELECT * FROM (');
			    $query[] = ') AS subQuery';
			}

			$query = array_merge( $query, $final_clauses );
		}

		$query =  join(' ', $query);
		$tags = $wpdb->get_results(	$query );
		if ( ! isset( $found_rows ) ) {
			$found_rows = $wpdb->num_rows;
		}

		if ( self::$mla_debug ) {
			MLACore::mla_debug_add( '<strong>' . __( 'mla_debug query arguments', 'media-library-assistant' ) . '</strong> = ' . var_export( $arguments, true ) );
			MLACore::mla_debug_add( '<strong>' . __( 'mla_debug last_query', 'media-library-assistant' ) . '</strong> = ' . var_export( $wpdb->last_query, true ) );
			MLACore::mla_debug_add( '<strong>' . __( 'mla_debug last_error', 'media-library-assistant' ) . '</strong> = ' . var_export( $wpdb->last_error, true ) );
			MLACore::mla_debug_add( '<strong>' . __( 'mla_debug num_rows', 'media-library-assistant' ) . '</strong> = ' . var_export( $wpdb->num_rows, true ) );
			MLACore::mla_debug_add( '<strong>' . __( 'mla_debug found_rows', 'media-library-assistant' ) . '</strong> = ' . var_export( $found_rows, true ) );
		}

		if ( 'true' == strtolower( trim( $arguments['pad_counts'] ) ) ) {
			self::_pad_term_counts( $tags, reset( $taxonomies ), $post_types, $post_stati );
		}

		$tags['found_rows'] = $found_rows;
		$tags = apply_filters( 'mla_get_terms_query_results', $tags );

		return $tags;
	} // mla_get_terms

	/**
	 * Add count of children to parent count.
	 *
	 * Recalculates term counts by including items from child terms. Assumes all
	 * relevant children are already in the $terms argument.
	 *
	 * @since 2.20
	 *
	 * @param	array	Array of Term objects, by reference
	 * @param	string	Term Context
	 * @param	array	Qualifying post type value(s)
	 * @param	array	Qualifying post status value(s)
	 * @return	null	Will break from function if conditions are not met.
	 */
	private static function _pad_term_counts( &$terms, $taxonomy, $post_types = NULL, $post_stati = NULL ) {
		global $wpdb;

		// This function only works for hierarchical taxonomies like post categories.
		if ( !is_taxonomy_hierarchical( $taxonomy ) ) {
			return;
		}

		// WordPress "private" function, in /wp-includes/taxonomy.php
		$term_hier = _get_term_hierarchy( $taxonomy );

		if ( empty( $term_hier ) ) {
			return;
		}

		$terms_by_id = array(); // key term_id, value = reference to term object
		$term_ids = array(); // key term_taxonomy_id, value = term_id
		$term_items = array(); // key term_id

		foreach ( (array) $terms as $key => $term ) {
			if ( is_integer( $key ) ) {
				$terms_by_id[$term->term_id] = & $terms[$key];
				$term_ids[$term->term_taxonomy_id] = $term->term_id;
			}
		}

		if ( is_array( $post_stati ) ) {
			$post_stati = esc_sql( $post_stati );
		} else {
			$post_stati = array( 'inherit' );
		}

		if ( is_array( $post_types ) ) {
			$post_types = esc_sql( $post_types );
		} else {
			$tax_obj = get_taxonomy( $taxonomy );
			$post_types = esc_sql( $tax_obj->object_type );
		}

		// Get the object and term ids and stick them in a lookup table
		$results = $wpdb->get_results( "SELECT object_id, term_taxonomy_id FROM $wpdb->term_relationships INNER JOIN $wpdb->posts ON object_id = ID WHERE term_taxonomy_id IN (" . implode( ',', array_keys($term_ids) ) . ") AND post_type IN ('" . implode( "', '", $post_types ) . "') AND post_status in ( '" . implode( "', '", $post_stati ) . "' )" );
		foreach ( $results as $row ) {
			$id = $term_ids[ $row->term_taxonomy_id ];
			$term_items[ $id ][ $row->object_id ] = isset( $term_items[ $id ][ $row->object_id ] ) ? ++$term_items[ $id ][ $row->object_id ] : 1;
		}

		// Touch every ancestor's lookup row for each post in each term
		foreach ( $term_ids as $term_id ) {
			$child = $term_id;
			while ( !empty( $terms_by_id[ $child] ) && $parent = $terms_by_id[ $child ]->parent ) {
				if ( !empty( $term_items[ $term_id ] ) ) {
					foreach ( $term_items[ $term_id ] as $item_id => $touches ) {
						$term_items[ $parent ][ $item_id ] = isset( $term_items[ $parent ][ $item_id ] ) ? ++$term_items[ $parent ][ $item_id ]: 1;
					}
				}

				$child = $parent;
			}
		}

		// Transfer the touched cells
		foreach ( (array) $term_items as $id => $items ) {
			if ( isset( $terms_by_id[ $id ] ) ) {
				$terms_by_id[ $id ]->count = count( $items );
			}
		}
	}
} // Class MLAShortcode_Support

MLAShortcode_Support::mla_load_custom_templates();
?>