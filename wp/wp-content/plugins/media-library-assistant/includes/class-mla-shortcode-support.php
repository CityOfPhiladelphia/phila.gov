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

if ( !class_exists( 'MLATemplate_Support' ) ) {
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-template-support.php' );
}
//error_log( __LINE__ . ' DEBUG: MLAShortcode_Support $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );

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
	 * Verify the presence of Ghostscript for mla_viewer
	 *
	 * @since 2.20
	 *
	 * @param	string	Non-standard location to override default search, e.g.,
	 *					'C:\Program Files (x86)\gs\gs9.15\bin\gswin32c.exe'
	 * @param	boolean	Force ghostscript-only tests, used by 
	 *                  MLASettings_Shortcodes::mla_compose_shortcodes_tab()
	 *
	 * @return	boolean	true if Ghostscript available else false
	 */
	public static function mla_ghostscript_present( $explicit_path = '', $ghostscript_only = false ) {
		static $ghostscript_present = NULL;

		/*
		 * If $ghostscript_only = false, let the mla_debug parameter control logging
		 */
		if ( $ghostscript_only ) {
			$mla_debug_category = MLACore::MLA_DEBUG_CATEGORY_THUMBNAIL;
		} else {
			$mla_debug_category = NULL;
		}

		MLACore::mla_debug_add( "MLAShortcode_Support::mla_ghostscript_present( {$ghostscript_only} ) explicit_path = " . var_export( $explicit_path, true ), MLACore::MLA_DEBUG_CATEGORY_THUMBNAIL );
		MLACore::mla_debug_add( "MLAShortcode_Support::mla_ghostscript_present( {$ghostscript_only} ) ghostscript_present = " . var_export( $ghostscript_present, true ), MLACore::MLA_DEBUG_CATEGORY_THUMBNAIL );

		if ( ! $ghostscript_only ) {
			if ( isset( $ghostscript_present ) ) {
				MLACore::mla_debug_add( '<strong>MLAShortcode_Support::mla_ghostscript_present</strong>, ghostscript_present = ' . var_export( $ghostscript_present, true ), MLACore::MLA_DEBUG_CATEGORY_THUMBNAIL );
				return $ghostscript_present;
			}

			if ( 'checked' != MLACore::mla_get_option( 'enable_ghostscript_check' ) ) {
				MLACore::mla_debug_add( '<strong>MLAShortcode_Support::mla_ghostscript_present</strong>, disabled', $mla_debug_category );
				return $ghostscript_present = true;
			}

			/*
			 * Imagick must be installed as well
			 */
			if ( ! class_exists( 'Imagick' ) ) {
				MLACore::mla_debug_add( '<strong>MLAShortcode_Support::mla_ghostscript_present</strong>, Imagick missing', $mla_debug_category );
				return $ghostscript_present = false;
			}
		} // not ghostscript_only

		/*
		 * Look for exec() - from http://stackoverflow.com/a/12980534/866618
		 */
		$blacklist = preg_split( '/,\s*/', ini_get('disable_functions') . ',' . ini_get('suhosin.executor.func.blacklist') );
		if ( in_array('exec', $blacklist) ) {
			MLACore::mla_debug_add( '<strong>MLAShortcode_Support::mla_ghostscript_present</strong>, exec in blacklist', $mla_debug_category );
			return $ghostscript_present = false;
		}

		if ( 'WIN' === strtoupper( substr( PHP_OS, 0, 3) ) ) {
			if ( ! empty( $explicit_path ) ) {
				$return = exec( 'dir /o:n/s/b "' . $explicit_path . '"' );
				MLACore::mla_debug_add( '<strong>MLAShortcode_Support::mla_ghostscript_present</strong>, WIN explicit path = ' . var_export( $return, true ), MLACore::MLA_DEBUG_CATEGORY_THUMBNAIL );
				if ( ! empty( $return ) ) {
					return $ghostscript_present = true;
				} else {
					return $ghostscript_present = false;
				}
			}

			$return = getenv('GSC');
			MLACore::mla_debug_add( '<strong>MLAShortcode_Support::mla_ghostscript_present</strong>, getenv(GSC) = ' . var_export( $return, true ), MLACore::MLA_DEBUG_CATEGORY_THUMBNAIL );
			if ( ! empty( $return ) ) {
				return $ghostscript_present = true;
			}

			$return = exec('where gswin*c.exe');
			MLACore::mla_debug_add( '<strong>MLAShortcode_Support::mla_ghostscript_present</strong>,  exec(where gswin*c.exe) = ' . var_export( $return, true ), MLACore::MLA_DEBUG_CATEGORY_THUMBNAIL );
			if ( ! empty( $return ) ) {
				return $ghostscript_present = true;
			}

			$return = exec('dir /o:n/s/b "C:\Program Files\gs\*gswin*c.exe"');
			MLACore::mla_debug_add( '<strong>MLAShortcode_Support::mla_ghostscript_present</strong>,  exec(dir /o:n/s/b "C:\Program Files\gs\*gswin*c.exe") = ' . var_export( $return, true ), MLACore::MLA_DEBUG_CATEGORY_THUMBNAIL );
			if ( ! empty( $return ) ) {
				return $ghostscript_present = true;
			}

			$return = exec('dir /o:n/s/b "C:\Program Files (x86)\gs\*gswin32c.exe"');
			MLACore::mla_debug_add( '<strong>MLAShortcode_Support::mla_ghostscript_present</strong>,  exec(dir /o:n/s/b "C:\Program Files (x86)\gs\*gswin32c.exe") = ' . var_export( $return, true ), MLACore::MLA_DEBUG_CATEGORY_THUMBNAIL );
			if ( ! empty( $return ) ) {
				return $ghostscript_present = true;
			}

			MLACore::mla_debug_add( '<strong>MLAShortcode_Support::mla_ghostscript_present</strong>, WIN detection failed', $mla_debug_category );
			return $ghostscript_present = false;
		} // Windows platform

		if ( ! empty( $explicit_path ) ) {
			exec( 'test -e ' . $explicit_path, $dummy, $ghostscript_path );
			MLACore::mla_debug_add( '<strong>MLAShortcode_Support::mla_ghostscript_present</strong>, explicit path = ' . var_export( $explicit_path, true ) . ', ghostscript_path = ' . var_export( $ghostscript_path, true ), MLACore::MLA_DEBUG_CATEGORY_THUMBNAIL );
			return ( $explicit_path === $ghostscript_path );
		}

		$return = exec('which gs');
		MLACore::mla_debug_add( '<strong>MLAShortcode_Support::mla_ghostscript_present</strong>, exec(which gs) = ' . var_export( $return, true ), MLACore::MLA_DEBUG_CATEGORY_THUMBNAIL );
		if ( ! empty( $return ) ) {
			return $ghostscript_present = true;
		}

		$test_path = '/usr/bin/gs';
		$output = array();
		$return_arg = -1;
		$return = exec( 'test -e ' . $test_path, $output, $return_arg );
		MLACore::mla_debug_add( '<strong>MLAShortcode_Support::mla_ghostscript_present</strong>, test_path = ' . var_export( $test_path, true ) . ', return_arg = ' . var_export( $return_arg, true ), MLACore::MLA_DEBUG_CATEGORY_THUMBNAIL );
		MLACore::mla_debug_add( '<strong>MLAShortcode_Support::mla_ghostscript_present</strong>, return = ' . var_export( $return, true ) . ', output = ' . var_export( $output, true ), MLACore::MLA_DEBUG_CATEGORY_THUMBNAIL );
		return $ghostscript_present = ( $test_path === $return_arg );
	}

	/**
	 * Filters the image src result, returning an icon to represent an attachment.
	 *
	 * @since 2.52
	 *
	 * @param array|false  $image         Either array with src, width & height, icon src, or false.
	 * @param int          $attachment_id Image attachment ID.
	 */
	public static function _get_attachment_icon_src( $image, $attachment_id ) {
		if ( $src = wp_mime_type_icon( $attachment_id ) ) {
			/** This filter is documented in wp-includes/post.php */
			$icon_dir = apply_filters( 'icon_dir', ABSPATH . WPINC . '/images/media' );

			$src_file = $icon_dir . '/' . wp_basename( $src );
			@list( $width, $height ) = getimagesize( $src_file );
		}

		if ( $src && $width && $height ) {
			$image = array( $src, $width, $height );
		}

		return $image;
	}

	/**
	 * Make sure $attr is an array, repair line-break damage, merge with $content
	 *
	 * @since 2.20
	 *
	 * @param	mixed	$attr Array or string containing shortcode attributes
	 * @param	string	$content Optional content for enclosing shortcodes
	 *
	 * @return	array	clean attributes array
	 */
	private static function _validate_attributes( $attr, $content = NULL ) {
		if ( empty( $attr ) ) {
			$attr = array();
		} elseif ( is_string( $attr ) ) {
			$attr = shortcode_parse_atts( $attr );
		}

		// Numeric keys indicate parse errors
		$not_valid = false;
		foreach ( $attr as $key => $value ) {
			if ( is_numeric( $key ) ) {
				$not_valid = true;
				break;
			}
		}

		if ( $not_valid ) {
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

			$attr = shortcode_parse_atts( $new_attr );

			/*
			 * Remove empty values and still-invalid parameters
			 */
			$new_attr = '';
			foreach ( $attr as $key => $value ) {
				if ( is_numeric( $key ) || empty( $value ) ) {
					continue;
				}

				$new_attr[ $key ] = $value;
			}

			$attr = $new_attr;
		} // not_valid

		/*
		 * Look for parameters in an enclosing shortcode
		 */
		if ( ! ( empty( $content ) || isset( $attr['mla_alt_shortcode'] ) ) ) {
			$content = str_replace( array( '&#8216;', '&#8217;', '&#8221;', '&#8243;', '<br />', '<p>', '</p>', "\r", "\n" ), array( '\'', '\'', '"', '"', ' ', ' ', ' ', ' ', ' ' ), $content );
			$new_attr = shortcode_parse_atts( $content );
			if ( is_array( $new_attr ) ) {
				$attr = array_merge( $attr, $new_attr );
			}
		}

		return $attr;
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
	 * Default values when global $post is not set
	 *
	 * @since 2.40
	 *
	 * @var	array
	 */
	private static $empty_post = array( 
				'ID' => 0,
				'post_author' => 0,
				'post_date' => '0000-00-00 00:00:00',
				'post_date_gmt' => '0000-00-00 00:00:00',
				'post_content' => '',
				'post_title' => '',
				'post_excerpt' => '',
				'post_status' => 'publish',
				'comment_status' => 'open',
				'ping_status' => 'open',
				'post_name' => '',
				'to_ping' => 'None',
				'pinged' => 'None',
				'post_modified' => '0000-00-00 00:00:00',
				'post_modified_gmt' => '0000-00-00 00:00:00',
				'post_content_filtered' => 'None',
				'post_parent' => 0,
				'guid' => '',
				'menu_order' => 0,
				'post_type' => 'post',
				'post_mime_type' => '',
				'comment_count' => 0,
			);

	/**
	 * The MLA Gallery shortcode.
	 *
	 * This is a superset of the WordPress Gallery shortcode for displaying images on a post,
	 * page or custom post type. It is adapted from /wp-includes/media.php gallery_shortcode.
	 * Enhancements include many additional selection parameters and full taxonomy support.
	 *
	 * @since 2.20
	 *
	 * @param array $attr Attributes of the shortcode
	 * @param string $content Optional content for enclosing shortcodes
	 *
	 * @return string HTML content to display gallery.
	 */
	public static function mla_gallery_shortcode( $attr, $content = NULL ) {
		global $post;

		/*
		 * Some do_shortcode callers may not have a specific post in mind
		 */
		if ( ! is_object( $post ) ) {
			$post = (object) self::$empty_post;
		}

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
			'page_parent' => $post->post_parent,
			'page_guid' => $post->guid,
			'page_type' => $post->post_type,
			'page_mime_type' => $post->post_mime_type,
			'page_url' => get_page_link(),
		);

		/*
		 * Make sure $attr is an array, even if it's empty,
		 * and repair damage caused by link-breaks in the source text
		 */
		$attr = self::_validate_attributes( $attr, $content );

		/*
		 * Filter the attributes before $mla_page_parameter and "request:" prefix processing.
		 */
		 
		$attr = apply_filters( 'mla_gallery_raw_attributes', $attr );

		/*
		 * The mla_paginate_current parameter can be changed to support
		 * multiple galleries per page.
		 */
		if ( ! isset( $attr['mla_page_parameter'] ) ) {
			$attr['mla_page_parameter'] = self::$mla_get_shortcode_attachments_parameters['mla_page_parameter'];
		}

		// The mla_page_parameter can contain page_level parameters like {+page_ID+}
		$attr_value = str_replace( '{+', '[+', str_replace( '+}', '+]', $attr['mla_page_parameter'] ) );
		$mla_page_parameter = MLAData::mla_parse_template( $attr_value, $page_values );

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

		/*
		 * These arguments must not be passed on to alternate gallery shortcodes
		 */
		$mla_arguments = array_merge( array(
			'mla_output' => 'gallery',
			'mla_style' => MLACore::mla_get_option('default_style'),
			'mla_markup' => MLACore::mla_get_option('default_markup'),
			'mla_float' => is_rtl() ? 'right' : 'left',
			'mla_itemwidth' => MLACore::mla_get_option('mla_gallery_itemwidth'),
			'mla_margin' => MLACore::mla_get_option('mla_gallery_margin'),
			'mla_target' => '',
			'mla_debug' => false,

			'mla_named_transfer' => false,
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
			'mla_alt_ids_value' => NULL,

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
			'link' => 'permalink', // or 'post', 'file', a registered size, etc.
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

		// Convert to boolean
		$arguments['mla_named_transfer'] = 'true' === ( ( ! empty( $arguments['mla_named_transfer'] ) ) ? trim( strtolower( $arguments['mla_named_transfer'] ) ) : 'false' );

		// Apply default arguments set in the markup template
		$template = $mla_arguments['mla_markup'];
		if ( isset( $attr['mla_markup'] ) && MLATemplate_Support::mla_fetch_custom_template( $attr['mla_markup'], 'gallery', 'markup', '[exists]' ) ) {
			$template = $attr['mla_markup'];
		}

		$arguments = MLATemplate_Support::mla_fetch_custom_template( $template, 'gallery', 'markup', 'arguments' );
		if ( !empty( $arguments ) ) {
			$attr = wp_parse_args( $attr, self::_validate_attributes( array(), $arguments ) );
		}

		/*
		 * Look for page-level, 'request:' and 'query:' substitution parameters,
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
			$replacement_values = MLAData::mla_expand_field_level_parameters( $attr_value, $attr, $page_values );
			$attr[ $attr_key ] = MLAData::mla_parse_template( $attr_value, $replacement_values );
		}

		/*
		 * Merge gallery arguments with defaults, pass the query arguments on to mla_get_shortcode_attachments.
		 */
		 
		$attr = apply_filters( 'mla_gallery_attributes', $attr );
		$content = apply_filters( 'mla_gallery_initial_content', $content, $attr );
		$arguments = shortcode_atts( $default_arguments, $attr );
		$arguments = apply_filters( 'mla_gallery_arguments', $arguments );

		/*
		 * Decide which templates to use
		 */
		if ( ( 'none' !== $arguments['mla_style'] ) && ( 'theme' !== $arguments['mla_style'] ) ) {
			if ( !MLATemplate_Support::mla_fetch_custom_template( $arguments['mla_style'], 'gallery', 'style', '[exists]' ) ) {
				MLACore::mla_debug_add( '<strong>mla_gallery mla_style</strong> "' . $arguments['mla_style'] . '" ' . __( 'not found', 'media-library-assistant' ), MLACore::MLA_DEBUG_CATEGORY_ANY );
				$arguments['mla_style'] = $default_arguments['mla_style'];
			}
		}

		if ( !MLATemplate_Support::mla_fetch_custom_template( $arguments['mla_markup'], 'gallery', 'markup', '[exists]' ) ) {
			MLACore::mla_debug_add( '<strong>mla_gallery mla_markup</strong> "' . $arguments['mla_markup'] . '" ' . __( 'not found', 'media-library-assistant' ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			$arguments['mla_markup'] = $default_arguments['mla_markup'];
		}

		/*
		 * Look for "no effect" alternate gallery shortcode to support plugins such as Justified Image Grid
		 */
		if ( is_string( $arguments['mla_alt_shortcode'] ) && ( in_array( $arguments['mla_alt_shortcode'], array( 'mla_gallery', 'no' ) ) ) ) {
			$arguments['mla_alt_shortcode'] = NULL;
			$arguments['mla_alt_ids_name'] = 'ids';
			$arguments['mla_alt_ids_value'] = NULL;
		}

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
			MLACore::mla_debug_add( __LINE__ . ' <strong>' . __( 'mla_debug REQUEST', 'media-library-assistant' ) . '</strong> = ' . var_export( $_REQUEST, true ) );
			MLACore::mla_debug_add( __LINE__ . ' <strong>' . __( 'mla_debug attributes', 'media-library-assistant' ) . '</strong> = ' . var_export( $attr, true ) );
			MLACore::mla_debug_add( __LINE__ . ' <strong>' . __( 'mla_debug arguments', 'media-library-assistant' ) . '</strong> = ' . var_export( $arguments, true ) );
		}

		/*
		 * Determine output type
		 */
		$output_parameters = array_map( 'strtolower', array_map( 'trim', explode( ',', $arguments['mla_output'] ) ) );
		if ( ! in_array( $output_parameters[0], array( 'gallery', 'next_link', 'current_link', 'previous_link', 'next_page', 'previous_page', 'paginate_links' ) ) ) {
			$output_parameters[0] = 'gallery';
		}

		$is_gallery = 'gallery' == $output_parameters[0];
		$is_pagination = in_array( $output_parameters[0], array( 'previous_page', 'next_page', 'paginate_links' ) ); 

		if ( $is_pagination && ( NULL !== $arguments['mla_paginate_rows'] ) ) {
			$attachments['found_rows'] = absint( $arguments['mla_paginate_rows'] );
		} else {
			$attachments = self::mla_get_shortcode_attachments( $post->ID, $attr, true );
		}

		if ( is_string( $attachments ) ) {
			return $attachments;
		}

		$current_rows = count( $attachments );

		if ( isset( $attachments['max_num_pages'] ) ) {
			$max_num_pages = $attachments['max_num_pages'];
			unset( $attachments['max_num_pages'] );
			$current_rows--;
		} else {
			$max_num_pages = 1;
		}

		if ( isset( $attachments['found_rows'] ) ) {
			$found_rows = $attachments['found_rows'];
			unset( $attachments['found_rows'] );
			$current_rows--;
		} else {
			$found_rows = $current_rows;
		}

		if ( ( $is_gallery && empty($attachments) ) || ( $is_pagination && empty( $found_rows ) ) ) {
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
		 * Look for Photonic-enhanced gallery; use the [gallery] shortcode if found
		 */
		global $photonic;

		if ( is_object( $photonic ) && ! empty( $arguments['style'] ) && empty( $arguments['mla_alt_shortcode'] ) ) {
			if ( 'default' != strtolower( $arguments['type'] ) )  {
				return '<p>' . __( '<strong>Photonic-enhanced [mla_gallery]</strong> type must be <strong>default</strong>, query = ', 'media-library-assistant' ) . var_export( $attr, true ) . '</p>';
			}

			if ( isset( $arguments['pause'] ) && ( 'false' == $arguments['pause'] ) ) {
				$arguments['pause'] = NULL;
			}

			$arguments['mla_alt_shortcode'] = 'gallery';
		}

		/*
		 * Look for user-specified alternate gallery shortcode
		 */
		if ( is_string( $arguments['mla_alt_shortcode'] ) ) {
			/*
			 * Replace data-selection parameters with the "ids" list
			 */
			$blacklist = array_merge( self::$mla_get_shortcode_attachments_parameters, self::$mla_get_shortcode_dynamic_attachments_parameters );
			if ( 'mla_tag_cloud' !== $arguments['mla_alt_shortcode'] ) {
				$blacklist = array_merge( $mla_arguments, $blacklist );
			}

			$blacklist = apply_filters( 'mla_gallery_alt_shortcode_blacklist', $blacklist );
			$alt_attr = apply_filters( 'mla_gallery_alt_shortcode_attributes', $attr );

			$mla_alt_shortcode_args = '';
			foreach ( $alt_attr as $key => $value ) {
				if ( array_key_exists( $key, $blacklist ) ) {
					continue;
				}

				$slashed = addcslashes( $value, chr(0).chr(7).chr(8)."\f\n\r\t\v\"\\\$" );
				if ( ( false !== strpos( $value, ' ' ) ) || ( false !== strpos( $value, '\'' ) ) || ( $slashed != $value ) ) {
					$value = '"' . $slashed . '"';
				}

				$mla_alt_shortcode_args .= empty( $mla_alt_shortcode_args ) ? $key . '=' . $value : ' ' . $key . '=' . $value;
			} // foreach $attr

			/*
			 * If an alternate value has been specified we must delay alt shortcode execution
			 * and accumulate $mla_alt_shortcode_ids in the template Item section.
			 */
			$mla_alt_ids_value = is_null( $arguments['mla_alt_ids_value'] ) ? NULL : str_replace( '{+', '[+', str_replace( '+}', '+]', $arguments['mla_alt_ids_value'] ) );
			$mla_alt_shortcode_ids = array();

			if ( is_null( $mla_alt_ids_value ) ) {

				$mla_alt_shortcode_ids = apply_filters_ref_array( 'mla_gallery_alt_shortcode_ids', array( $mla_alt_shortcode_ids, $arguments['mla_alt_ids_name'], &$attachments ) );
				if ( is_array( $mla_alt_shortcode_ids ) ) {
					if ( 0 == count( $mla_alt_shortcode_ids ) ) {
						foreach ( $attachments as $value ) {
							$mla_alt_shortcode_ids[] = $value->ID;
						} // foreach $attachments
					}

					$mla_alt_shortcode_ids = $arguments['mla_alt_ids_name'] . '="' . implode( ',', $mla_alt_shortcode_ids ) . '"';
				}

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
					$output .= do_shortcode( sprintf( '[%1$s %2$s %3$s]%4$s[/%1$s]', $arguments['mla_alt_shortcode'], $mla_alt_shortcode_ids, $mla_alt_shortcode_args, $content ) );
				} else {
					$output .= do_shortcode( sprintf( '[%1$s %2$s %3$s]', $arguments['mla_alt_shortcode'], $mla_alt_shortcode_ids, $mla_alt_shortcode_args ) );
				}

				do_action( 'mla_gallery_end_alt_shortcode' );
				return $output;
			} // is_null( $mla_alt_ids_value )
		} // mla_alt_shortcode

		$size_class = $arguments['size'];
		$size = strtolower( $size_class );

		$icon_only = 'icon_only' === $size;
		if ( $icon_only ) {
			$size = $size_class = 'icon';
		}

		if ( 'icon' == strtolower( $size) ) {
			if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ENABLE_MLA_ICONS ) ) {
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
		$mla_viewer_required = false;
		if ( 'checked' == MLACore::mla_get_option( 'enable_mla_viewer' ) ) {
			if ( ! empty( $arguments['mla_viewer'] ) ) {
				// Split out the required suffix
				$mla_viewer_args = explode( ',', strtolower( $arguments['mla_viewer'] ) ) ;
				$mla_viewer_required = ( 1 < count( $mla_viewer_args ) && 'required' == $mla_viewer_args[1] );

				if ( 'single' == $mla_viewer_args[0] ) {
					$arguments['mla_single_thread'] = true;	
					$arguments['mla_viewer'] = true;
				} elseif ( 'true' == $mla_viewer_args[0] ) {
					$arguments['mla_viewer'] = true;
				} elseif ( 'required' == $mla_viewer_args[0] ) {
					$mla_viewer_required = true;
					$arguments['mla_viewer'] = true;
				} else {
					$arguments['mla_viewer'] = false;
				}
			}
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
			'size_class' => sanitize_html_class( $size_class ),
			'found_rows' => $found_rows,
			'current_rows' => $current_rows,
			'max_num_pages' => $max_num_pages,
		) );

		$style_template = $gallery_style = '';

		if ( 'theme' == strtolower( $style_values['mla_style'] ) ) {
			$use_mla_gallery_style = apply_filters( 'use_default_gallery_style', ! $html5 );
		} else {
			$use_mla_gallery_style = ( 'none' != strtolower( $style_values['mla_style'] ) );
		}

		if ( apply_filters( 'use_mla_gallery_style', $use_mla_gallery_style, $style_values['mla_style'] ) ) {
			$style_template = MLATemplate_support::mla_fetch_custom_template( $style_values['mla_style'], 'gallery', 'style' );
			if ( empty( $style_template ) ) {
				$style_values['mla_style'] = 'default';
				$style_template = MLATemplate_support::mla_fetch_custom_template( 'default', 'gallery', 'style' );
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

		$open_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'gallery', 'markup', 'open' );
		if ( empty( $open_template ) ) {
			$open_template = '';
		}

		/*
		 * Emulate [gallery] handling of row open markup for the default template only
		 */
		if ( $html5 && ( 'default' == $markup_values['mla_markup'] ) ) {
			$row_open_template = '';
		} else{
			$row_open_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'gallery', 'markup', 'row-open' );

			if ( empty( $row_open_template ) ) {
				$row_open_template = '';
			}
		}

		$item_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'gallery', 'markup', 'item' );
		if ( empty( $item_template ) ) {
			$item_template = '';
		}

		/*
		 * Emulate [gallery] handling of row close markup for the default template only
		 */
		if ( $html5 && ( 'default' == $markup_values['mla_markup'] ) ) {
			$row_close_template = '';
		} else{
			$row_close_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'gallery', 'markup', 'row-close' );

			if ( empty( $row_close_template ) ) {
				$row_close_template = '';
			}
		}

		$close_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'gallery', 'markup', 'close' );
		if ( empty( $close_template ) ) {
			$close_template = '';
		}

		/*
		 * Look for gallery-level markup substitution parameters
		 */
		$new_text = $open_template . $row_open_template . $row_close_template . $close_template;
		$markup_values = MLAData::mla_expand_field_level_parameters( $new_text, $attr, $markup_values );

		if ( self::$mla_debug ) {
			$mla_alt_ids_output = $output = MLACore::mla_debug_flush();
		} else {
			$mla_alt_ids_output = $output = '';
		}

		/*
		 * These $markup_values are used for both pagination and gallery output
		 */
		$markup_values = apply_filters( 'mla_gallery_open_values', $markup_values );

		if ( $is_gallery ) {
			$open_template = apply_filters( 'mla_gallery_open_template', $open_template );
			if ( empty( $open_template ) ) {
				$gallery_open = '';
			} else {
				$gallery_open = MLAData::mla_parse_template( $open_template, $markup_values );
			}

			$gallery_open = apply_filters( 'mla_gallery_open_parse', $gallery_open, $open_template, $markup_values );
			$output .= apply_filters( 'mla_gallery_style', $gallery_style . $gallery_open, $style_values, $markup_values, $style_template, $open_template );
		} else {
			/*
			 * Handle 'previous_page', 'next_page', and 'paginate_links'
			 */
			$pagination_result = self::_process_pagination_output_types( $output_parameters, $markup_values, $arguments, $attr, $found_rows, $output );
			if ( false !== $pagination_result ) {
				return $pagination_result;
			}
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

			$pagination_index = 1;
			foreach ( $attachments as $id => $attachment ) {
				if ( $attachment->ID == $current_id ) {
					break;
				}

				$pagination_index++;
			}

			$target = NULL;
			if ( isset( $id ) ) {
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
			} // isset id

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
			$item_values = apply_filters( 'mla_gallery_item_initial_values', $markup_values, $attachment );

			// fill in item-specific elements
			$item_values['index'] = (string) $is_gallery ? 1 + $column_index : $pagination_index;
			$item_values['last_in_row'] = '';

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
			$item_values['slug'] = $attachment->post_name;
			$item_values['width'] = '';
			$item_values['height'] = '';
			$item_values['orientation'] = '';
			$item_values['image_meta'] = '';
			$item_values['image_alt'] = '';
			$item_values['base_file'] = '';
			$item_values['path'] = '';
			$item_values['file'] = '';
			$item_values['description'] = wptexturize( $attachment->post_content );
			$item_values['file_url'] = wp_get_attachment_url( $attachment->ID ); // $attachment->guid;
			$item_values['author_id'] = $attachment->post_author;
			$item_values['author'] = '';
			$item_values['caption'] = '';
			$item_values['captiontag_content'] = '';

			$user = get_user_by( 'id', $attachment->post_author );
			if ( isset( $user->data->display_name ) ) {
				$item_values['author'] = wptexturize( $user->data->display_name );
			} else {
				$item_values['author'] = __( 'unknown', 'media-library-assistant' );
			}

			$post_meta = MLAQuery::mla_fetch_attachment_metadata( $attachment->ID );
			$base_file = isset( $post_meta['mla_wp_attached_file'] ) ? $post_meta['mla_wp_attached_file'] : '';
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
				$item_values['image_meta'] = var_export( $post_meta['mla_wp_attachment_metadata']['image_meta'], true );
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
					$item_values['base_file'] = $base_file;
					$item_values['file'] = $base_file;
				} else {
					$file_name = substr( $base_file, $last_slash + 1 );
					$item_values['base_file'] = $base_file;;
					$item_values['path'] = substr( $base_file, 0, $last_slash + 1 );
					$item_values['file'] = $file_name;
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
			if ( 'attachment' == $attachment->post_type ) {
				// Avoid native PDF thumbnails, if specified
				if ( $mla_viewer_required && in_array( $attachment->post_mime_type, array( 'application/pdf' ) ) ) {
					$item_values['pagelink'] = sprintf( '<a href=\'%1$s\'>%2$s</a>', get_permalink( $attachment->ID ), $attachment->post_title );
					$item_values['filelink'] = sprintf( '<a href=\'%1$s\'>%2$s</a>', $attachment->guid, $attachment->post_title );
				} else {
					if ( $icon_only ) {
						add_filter( 'wp_get_attachment_image_src', 'MLAShortcode_Support::_get_attachment_icon_src', 10, 2 );
					}

					$item_values['pagelink'] = wp_get_attachment_link($attachment->ID, $size, true, $show_icon, $link_text);
					$item_values['filelink'] = wp_get_attachment_link($attachment->ID, $size, false, $show_icon, $link_text);

					if ( $icon_only ) {
						remove_filter( 'wp_get_attachment_image_src', 'MLAShortcode_Support::_get_attachment_icon_src' );
					}
				}
			} else {
				$item_values['pagelink'] = sprintf( '<a href=\'%1$s\'>%2$s</a>', $attachment->guid, $attachment->post_title );
				$item_values['filelink'] = sprintf( '<a href=\'%1$s\'>%2$s</a>', get_permalink( $attachment->ID ), $attachment->post_title );
			}

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

				// Extract existing class values and add to them
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

			// Create download and named transfer links with all Content Parameters
			$match_count = preg_match( '#href=\'([^\']+)\'#', $item_values['filelink'], $matches, PREG_OFFSET_CAPTURE );
			if ( ! ( ( $match_count == false ) || ( $match_count == 0 ) ) ) {
				// Forced download link
				$args = array(
					'mla_download_file' => urlencode( $item_values['base_dir'] . '/' . $item_values['base_file'] ),
					'mla_download_type' => $item_values['mime_type']
				);
				
				if ( 'log' == $arguments['mla_debug'] ) {
					$args['mla_debug'] = 'log';
				}

				$item_values['downloadlink_url'] = add_query_arg( $args, MLA_PLUGIN_URL . 'includes/mla-file-downloader.php' );
				$item_values['downloadlink'] = preg_replace( '#' . $matches[0][0] . '#', sprintf( 'href=\'%1$s\'', $item_values['downloadlink_url'] ), $item_values['filelink'] );
				
				// AJAX-based Named Transfer link
				$args = array(
					'action' => 'mla_named_transfer',
					'mla_item' => $attachment->post_name,
					'mla_disposition' => ( 'download' === $arguments['link'] ) ? 'attachment' : 'inline',
				);

				if ( 'log' == $arguments['mla_debug'] ) {
					$args['mla_debug'] = 'log';
				}

				$item_values['transferlink_url'] = add_query_arg( $args, admin_url( 'admin-ajax.php' ) );
				$item_values['transferlink'] = preg_replace( '#' . $matches[0][0] . '#', sprintf( 'href=\'%1$s\'', $item_values['transferlink_url'] ), $item_values['filelink'] );
			} else {
				$item_values['downloadlink_url'] = $item_values['filelink_url'];
				$item_values['downloadlink'] = $item_values['filelink'];
				
				$item_values['transferlink_url'] = $item_values['filelink_url'];
				$item_values['transferlink'] = $item_values['filelink'];
			}

			switch ( $arguments['link'] ) {
				case 'permalink':
				case 'post':
					$item_values['link'] = $item_values['pagelink'];
					break;
				case 'file':
				case 'full':
					$item_values['link'] = $item_values['filelink'];
					break;
				case 'download':
					$item_values['link'] = $item_values['downloadlink'];
					break;
				default:
					$item_values['link'] = $item_values['filelink'];

					// Check for link to specific (registered) file size, image types only
					if ( array_key_exists( $arguments['link'], $sizes ) ) {
						if ( 0 === strpos( $attachment->post_mime_type, 'image/' ) ) {
							$target_file = $sizes[ $arguments['link'] ]['file'];
							$item_values['link'] = str_replace( $file_name, $target_file, $item_values['filelink'] );
						}
					}
			} // switch 'link'

			// Replace link with AJAX-based item transfer using post slug
			if ( $arguments['mla_named_transfer'] ) {
				$item_values['link'] = $item_values['transferlink'];
			}

			// Extract target and thumbnail fields
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

				// Replace single- and double-quote delimited values
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
				$link_tag = '';
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
					$feature = apply_filters( 'mla_gallery_featured_image', $feature, $attachment, $size, $item_values );

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
							$item_values['downloadlink'] = sprintf( '<a %1$shref="%2$s" title="%3$s">%4$s</a>', $link_attributes, $item_values['downloadlink_url'], $rollover_text, $item_values['thumbnail_content'] );
						} else {
							$item_values['pagelink'] = sprintf( '<a %1$shref="%2$s">%3$s</a>', $link_attributes, $item_values['pagelink_url'], $item_values['thumbnail_content'] );
							$item_values['filelink'] = sprintf( '<a %1$shref="%2$s">%3$s</a>', $link_attributes, $item_values['filelink_url'], $item_values['thumbnail_content'] );
							$item_values['downloadlink'] = sprintf( '<a %1$shref="%2$s">%3$s</a>', $link_attributes, $item_values['downloadlink_url'], $item_values['thumbnail_content'] );
						}
						if ( ! empty( $link_href ) ) {
							$item_values['link'] = sprintf( '<a %1$shref="%2$s" title="%3$s">%4$s</a>', $link_attributes, $link_href, $rollover_text, $item_values['thumbnail_content'] );
						} elseif ( 'permalink' == $arguments['link'] || 'post' == $arguments['link'] ) {
							$item_values['link'] = $item_values['pagelink'];
						} elseif ( 'file' == $arguments['link'] || 'full' == $arguments['link'] ) {
							$item_values['link'] = $item_values['filelink'];
						} elseif ( 'download' == $arguments['link'] ) {
							$item_values['link'] = $item_values['downloadlink'];
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

				/*
				 * Accumulate mla_alt_shortcode_ids when mla_alt_ids_value present
				 */
				if ( is_string( $arguments['mla_alt_shortcode'] ) && is_string( $mla_alt_ids_value ) ) {
					$item_values = MLAData::mla_expand_field_level_parameters( $mla_alt_ids_value, $attr, $item_values );
					$mla_alt_shortcode_ids[] = MLAData::mla_parse_template( $mla_alt_ids_value, $item_values );
					continue;
				}

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

		/*
		 * Execute the alternate gallery shortcode with the new parameters
		 */
		if ( is_string( $arguments['mla_alt_shortcode'] ) && is_string( $mla_alt_ids_value ) ) {
			$mla_alt_shortcode_ids = $arguments['mla_alt_ids_name'] . '="' . implode( ',', $mla_alt_shortcode_ids ) . '"';
			$content = apply_filters( 'mla_gallery_final_content', $content );
			if ( ! empty( $content ) ) {
				return $output . do_shortcode( sprintf( '[%1$s %2$s %3$s]%4$s[/%1$s]', $arguments['mla_alt_shortcode'], $mla_alt_shortcode_ids, $mla_alt_shortcode_args, $content ) );
			} else {
				return $output . do_shortcode( sprintf( '[%1$s %2$s %3$s]', $arguments['mla_alt_shortcode'], $mla_alt_shortcode_ids, $mla_alt_shortcode_args ) );
			}
		}

		if ( $is_gallery ) {
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

		// Some do_shortcode callers may not have a specific post in mind
		if ( ! is_object( $post ) ) {
			$post = (object) self::$empty_post;
		}

		// $instance supports multiple clouds in one page/post	
		static $instance = 0;
		$instance++;

		// Some values are already known, and can be used in data selection parameters
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

		// These are the default parameters for tag cloud display
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
			'columns' => MLACore::mla_get_option('mla_tag_cloud_columns'),

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

		// Filter the attributes before $mla_page_parameter and "request:" prefix processing.
		$attr = apply_filters( 'mla_tag_cloud_raw_attributes', $attr );

		/*
		 * The mla_paginate_current parameter can be changed to support
		 * multiple clouds per page.
		 */
		if ( ! isset( $attr['mla_page_parameter'] ) ) {
			$attr['mla_page_parameter'] = $defaults['mla_page_parameter'];
		}

		// The mla_page_parameter can contain page_level parameters like {+page_ID+}
		$attr_value = str_replace( '{+', '[+', str_replace( '+}', '+]', $attr['mla_page_parameter'] ) );
		$mla_page_parameter = MLAData::mla_parse_template( $attr_value, $page_values );
		 
		/*
		 * Special handling of mla_page_parameter to make "MLA pagination" easier.
		 * Look for this parameter in $_REQUEST if it's not present in the shortcode itself.
		 */
		if ( ! isset( $attr[ $mla_page_parameter ] ) ) {
			if ( isset( $_REQUEST[ $mla_page_parameter ] ) ) {
				$attr[ $mla_page_parameter ] = $_REQUEST[ $mla_page_parameter ];
			}
		}
		 
		// Determine markup template to get default arguments
		$arguments = shortcode_atts( $defaults, $attr );
		if ( $arguments['mla_markup'] ) {
			$template = $arguments['mla_markup'];
			if ( ! MLATemplate_Support::mla_fetch_custom_template( $template, 'tag-cloud', 'markup', '[exists]' ) ) {
				$template = NULL;
			}
		} else {
			$template = NULL;
		}

		if ( empty( $template ) ) {
			$output_parameters = array_map( 'strtolower', array_map( 'trim', explode( ',', $arguments['mla_output'] ) ) );

			if ( !in_array( $output_parameters[0], array( 'flat', 'list', 'ulist', 'olist', 'dlist', 'grid', 'array' ) ) ) {
				$output_parameters[0] = 'flat';
			}

			if ( 'grid' == $output_parameters[0] ) {
				$template = MLACore::mla_get_option('default_tag_cloud_markup');
			} elseif ( in_array( $output_parameters[0], array( 'list', 'ulist', 'olist', 'dlist' ) ) ) {
				if ( ( 'dlist' == $output_parameters[0] ) || ('list' == $output_parameters[0] && 'dd' == $arguments['captiontag'] ) ) {
					$template = 'tag-cloud-dl';
				} else {
					$template = 'tag-cloud-ul';
				}
			} else {
				$template = NULL;
			}
		}

		// Apply default arguments set in the markup template
		if ( !empty( $template ) ) {
			$arguments = MLATemplate_Support::mla_fetch_custom_template( $template, 'tag-cloud', 'markup', 'arguments' );
			if ( !empty( $arguments ) ) {
				$attr = wp_parse_args( $attr, self::_validate_attributes( array(), $arguments ) );
			}
		}

		/*
		 * Look for page-level, 'request:' and 'query:' substitution parameters,
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
			$replacement_values = MLAData::mla_expand_field_level_parameters( $attr_value, $attr, $page_values );
			$attr[ $attr_key ] = MLAData::mla_parse_template( $attr_value, $replacement_values );
		}

		$attr = apply_filters( 'mla_tag_cloud_attributes', $attr );
		$arguments = shortcode_atts( $defaults, $attr );

		/*
		 * $mla_page_parameter, if non-default, doesn't make it through the shortcode_atts
		 * filter, so we handle it separately
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
			MLACore::mla_debug_add( __LINE__ . ' <strong>' . __( 'mla_debug REQUEST', 'media-library-assistant' ) . '</strong> = ' . var_export( $_REQUEST, true ) );
			MLACore::mla_debug_add( __LINE__ . ' <strong>' . __( 'mla_debug attributes', 'media-library-assistant' ) . '</strong> = ' . var_export( $attr, true ) );
			MLACore::mla_debug_add( __LINE__ . ' <strong>' . __( 'mla_debug arguments', 'media-library-assistant' ) . '</strong> = ' . var_export( $arguments, true ) );
		}

		/*
		 * Determine templates and output type
		 */
		if ( $arguments['mla_style'] && ( 'none' !== $arguments['mla_style'] ) ) {
			if ( !MLATemplate_Support::mla_fetch_custom_template( $arguments['mla_style'], 'tag-cloud', 'style', '[exists]' ) ) {
				MLACore::mla_debug_add( '<strong>mla_tag_cloud mla_style</strong> "' . $arguments['mla_style'] . '" ' . __( 'not found', 'media-library-assistant' ), MLACore::MLA_DEBUG_CATEGORY_ANY );
				$arguments['mla_style'] = NULL;
			}
		}

		if ( $arguments['mla_markup'] ) {
			if ( !MLATemplate_Support::mla_fetch_custom_template( $arguments['mla_markup'], 'tag-cloud', 'markup', '[exists]' ) ) {
				MLACore::mla_debug_add( '<strong>mla_tag_cloud mla_markup</strong> "' . $arguments['mla_markup'] . '" ' . __( 'not found', 'media-library-assistant' ), MLACore::MLA_DEBUG_CATEGORY_ANY );
				$arguments['mla_markup'] = NULL;
			}
		}

		$output_parameters = array_map( 'strtolower', array_map( 'trim', explode( ',', $arguments['mla_output'] ) ) );

		if ( !in_array( $output_parameters[0], array( 'flat', 'list', 'ulist', 'olist', 'dlist', 'grid', 'array' ) ) ) {
			$output_parameters[0] = 'flat';
		}

		if ( $is_grid = 'grid' == $output_parameters[0] ) {
			$default_style = MLACore::mla_get_option('default_tag_cloud_style');
			$default_markup = MLACore::mla_get_option('default_tag_cloud_markup');

			if ( NULL == $arguments['mla_style'] ) {
				$arguments['mla_style'] = $default_style;
			}

			if ( NULL == $arguments['mla_markup'] ) {
				$arguments['mla_markup'] = $default_markup;
			}

			if ( empty( $attr['itemtag'] ) ) {
				$arguments['itemtag'] = 'dl';
			}

			if ( empty( $attr['termtag'] ) ) {
				$arguments['termtag'] = 'dt';
			}

			if ( empty( $attr['captiontag'] ) ) {
				$arguments['captiontag'] = 'dd';
			}
		}

		if ( $is_list = in_array( $output_parameters[0], array( 'list', 'ulist', 'olist', 'dlist' ) ) ) {
			$default_style = 'none';

			if ( 'list' == $output_parameters[0] && 'dd' == $arguments['captiontag'] ) {
				$default_markup = 'tag-cloud-dl';
				$arguments['itemtag'] = 'dl';
				$arguments['termtag'] = 'dt';
			} else {
				$default_markup = 'tag-cloud-ul';
				$arguments['termtag'] = 'li';
				$arguments['captiontag'] = '';

				switch ( $output_parameters[0] ) {
					case 'ulist':
						$arguments['itemtag'] = 'ul';
						break;
					case 'olist':
						$arguments['itemtag'] = 'ol';
						break;
					case 'dlist':
						$default_markup = 'tag-cloud-dl';
						$arguments['itemtag'] = 'dl';
						$arguments['termtag'] = 'dt';
						$arguments['captiontag'] = 'dd';
					break;
					default:
						$arguments['itemtag'] = 'ul';
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

		/*
		 * Invalid taxonomy names return WP_Error
		 */
		if ( is_wp_error( $tags ) ) {
			$cloud =  '<strong>' . __( 'ERROR', 'media-library-assistant' ) . ': ' . $tags->get_error_message() . '</strong>, ' . $tags->get_error_data( $tags->get_error_code() );

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

		if ( 0 == $found_rows ) {
			if ( self::$mla_debug ) {
				MLACore::mla_debug_add( '<strong>' . __( 'mla_debug empty cloud', 'media-library-assistant' ) . '</strong>, query = ' . var_export( $arguments, true ) );
				$cloud = MLACore::mla_debug_flush();

				if ( '<p></p>' == $cloud ) {
					$cloud = '';
				}
			} else {
				$cloud = '';
			}

			$cloud .= $arguments['mla_nolink_text'];
			if ( 'array' == $arguments['mla_output'] ) {
				if ( empty( $cloud ) ) {
					return array();
				} else {
					return array( $cloud );
				}
			}

			if ( empty($arguments['echo']) ) {
				return $cloud;
			}

			echo $cloud;
			return;
		}

		if ( self::$mla_debug ) {
			$cloud = MLACore::mla_debug_flush();
		} else {
			$cloud = '';
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
			$style_template = MLATemplate_support::mla_fetch_custom_template( $style_values['mla_style'], 'tag-cloud', 'style' );
			if ( empty( $style_template ) ) {
				$style_values['mla_style'] = $default_style;
				$style_template = MLATemplate_support::mla_fetch_custom_template( $default_style, 'tag-cloud', 'style' );
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
			$open_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'tag-cloud', 'markup', 'open' );
			if ( false === $open_template ) {
				$markup_values['mla_markup'] = $default_markup;
				$open_template = MLATemplate_support::mla_fetch_custom_template( $default_markup, 'tag-cloud', 'markup', 'open' );
			}

			if ( empty( $open_template ) ) {
				$open_template = '';
			}

			if ( $is_grid ) {
				$row_open_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'tag-cloud', 'markup', 'row-open' );
				if ( empty( $row_open_template ) ) {
					$row_open_template = '';
				}
			} else {
				$row_open_template = '';
			}

			$item_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'tag-cloud', 'markup', 'item' );
			if ( empty( $item_template ) ) {
				$item_template = '';
			}

			if ( $is_grid ) {
				$row_close_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'tag-cloud', 'markup', 'row-close' );
				if ( empty( $row_close_template ) ) {
					$row_close_template = '';
					}
			} else {
				$row_close_template = '';
			}

			$close_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'tag-cloud', 'markup', 'close' );
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

		// Find delimiter for currentlink, currentlink_url
		if ( strpos( $markup_values['page_url'], '?' ) ) {
			$current_item_delimiter = '&';
		} else {
			$current_item_delimiter = '?';
		}

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
			$item_values['slug'] = $tag->slug;
			$item_values['term_group'] = $tag->term_group;
			$item_values['term_taxonomy_id'] = $tag->term_taxonomy_id;
			$item_values['taxonomy'] = $tag->taxonomy;
			$item_values['description'] = wptexturize( $tag->description );
			$item_values['parent'] = $tag->parent;
			$item_values['count'] = isset ( $tag->count ) ? $tag->count : 0; 
			$item_values['scaled_count'] = $tag->scaled_count;
			$item_values['font_size'] = str_replace( ',', '.', ( $item_values['smallest'] + ( ( $item_values['scaled_count'] - $item_values['min_scaled_count'] ) * $item_values['font_step'] ) ) );
			$item_values['link_url'] = $tag->link;
			$item_values['currentlink_url'] = sprintf( '%1$s%2$scurrent_item=%3$d', $item_values['page_url'], $current_item_delimiter, $item_values['term_id'] );
			$item_values['editlink_url'] = $tag->edit_link;
			$item_values['termlink_url'] = $tag->term_link;
			// Added in the code below:
			$item_values['caption'] = '';
			$item_values['link_attributes'] = '';
			$item_values['current_item_class'] = '';
			$item_values['rollover_text'] = '';
			$item_values['link_style'] = '';
			$item_values['link_text'] = '';
			$item_values['currentlink'] = '';
			$item_values['editlink'] = '';
			$item_values['termlink'] = '';
			$item_values['thelink'] = '';

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
				$class_attributes = self::_process_shortcode_parameter( $arguments['mla_link_class'], $item_values );
				if ( !empty( $class_attributes ) ) {
					$link_attributes .= 'class="' . $class_attributes . '" ';
				}
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
			 * Currentlink, editlink, termlink and thelink
			 */
			$item_values['currentlink'] = sprintf( '<a %1$shref="%2$s%3$scurrent_item=%4$d" title="%5$s" style="%6$s">%7$s</a>', $link_attributes, $item_values['page_url'], $current_item_delimiter, $item_values['term_id'], $item_values['rollover_text'], $item_values['link_style'], $item_values['link_text'] );
			$item_values['editlink'] = sprintf( '<a %1$shref="%2$s" title="%3$s" style="%4$s">%5$s</a>', $link_attributes, $item_values['editlink_url'], $item_values['rollover_text'], $item_values['link_style'], $item_values['link_text'] );
			$item_values['termlink'] = sprintf( '<a %1$shref="%2$s" title="%3$s" style="%4$s">%5$s</a>', $link_attributes, $item_values['termlink_url'], $item_values['rollover_text'], $item_values['link_style'], $item_values['link_text'] );

			if ( ! empty( $link_href ) ) {
				$item_values['thelink'] = sprintf( '<a %1$shref="%2$s" title="%3$s" style="%4$s">%5$s</a>', $link_attributes, $link_href, $item_values['rollover_text'], $item_values['link_style'], $item_values['link_text'] );
			} elseif ( 'current' == $arguments['link'] ) {
				$item_values['link_url'] = $item_values['currentlink_url'];
				$item_values['thelink'] = $item_values['currentlink'];
			} elseif ( 'edit' == $arguments['link'] ) {
				$item_values['thelink'] = $item_values['editlink'];
			} elseif ( 'view' == $arguments['link'] ) {
				$item_values['thelink'] = $item_values['termlink'];
			} elseif ( 'span' == $arguments['link'] ) {
				$item_values['thelink'] = sprintf( '<span %1$sstyle="%2$s">%3$s</span>', $link_attributes, $item_values['link_style'], $item_values['link_text'] );
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
	 * @param string $content Optional content for enclosing shortcodes
	 *
	 * @return string HTML content to display the tag cloud.
	 */
	public static function mla_tag_cloud_shortcode( $attr, $content = NULL ) {
		/*
		 * Make sure $attr is an array, even if it's empty,
		 * and repair damage caused by link-breaks in the source text
		 */
		$attr = self::_validate_attributes( $attr, $content );

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
	 * Compose one level of an mla_term_list
	 *
	 * Adds shortcode output text and term-specific links to arrays passed by reference.
	 *
	 * @since 2.25
	 *
	 * @param string $list Shortcode output text, by reference
	 * @param array $links Term-specific links for flat/array output, by reference
	 * @param array $terms Term objects, by reference
	 * @param array $markup_values Style and list-level substitution parameters, by reference
	 * @param array $arguments Shortcode parameters, including defaults, by reference
	 * @param array $attr Shortcode parameters, explicit, by reference
	 *
	 * @return boolean True if the list contains the "current_item"; appends to &$list, &$links
	 */
	public static function _compose_term_list( &$list, &$links, &$terms, &$markup_values, &$arguments, &$attr ) {
		$term = reset( $terms );
		$markup_values['current_level'] = $current_level = $term->level;
		if ( $current_level ) {
			$markup_values['itemtag_class'] = 'term-list term-list-taxonomy-' . $term->taxonomy . ' children'; 
			$markup_values['itemtag_id'] = $markup_values['selector'] . '-' . $term->parent;
		} else {
			$markup_values['itemtag_class'] = 'term-list term-list-taxonomy-' . $term->taxonomy; 
			$markup_values['itemtag_id'] = $markup_values['selector'];
		}

		$mla_item_parameter = $arguments['mla_item_parameter'];

		// Determine output type and templates
		$output_parameters = array_map( 'strtolower', array_map( 'trim', explode( ',', $arguments['mla_output'] ) ) );

		if ( !in_array( $output_parameters[0], array( 'flat', 'list', 'ulist', 'olist', 'dlist', 'dropdown', 'checklist', 'array' ) ) ) {
			$output_parameters[0] = 'ulist';
		}

		$is_list = in_array( $output_parameters[0], array( 'list', 'ulist', 'olist', 'dlist' ) );
		$is_dropdown = 'dropdown' == $output_parameters[0];
		$is_checklist = 'checklist' == $output_parameters[0];
		$is_hierarchical = !( 'false' === $arguments['hierarchical'] );
		$combine_hierarchical = 'combine' === $arguments['hierarchical'];

		// Using the slug is a common practice and affects current_item
		$current_is_slug = in_array( $arguments['mla_option_value'], array( '{+slug+}', '[+slug+]' ) );

		if ( $is_list || $is_dropdown || $is_checklist ) {
			if ( $term->parent ) {
				$open_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'term-list', 'markup', 'child-open' );
			} else {
				$open_template = false;
			}

			if ( false === $open_template ) {
				$open_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'term-list', 'markup', 'open' );
			}

			// Fall back to default template if no Open section
			if ( false === $open_template ) {
				$markup_values['mla_markup'] = $default_markup;

				if ( $term->parent ) {
					$open_template = MLATemplate_support::mla_fetch_custom_template( $default_markup, 'term-list', 'markup', 'child-open' );
				} else {
					$open_template = false;
				}

				if ( false === $open_template ) {
					$open_template = MLATemplate_support::mla_fetch_custom_template( $default_markup, 'term-list', 'markup', 'open' );
				}
			}

			if ( empty( $open_template ) ) {
				$open_template = '';
			}

			if ( $term->parent ) {
				$item_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'term-list', 'markup', 'child-item' );
			} else {
				$item_template = false;
			}

			if ( false === $item_template ) {
				$item_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'term-list', 'markup', 'item' );
			}

			if ( empty( $item_template ) ) {
				$item_template = '';
			}

			if ( $term->parent ) {
				$close_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'term-list', 'markup', 'child-close' );
			} else {
				$close_template = false;
			}

			if ( false === $close_template ) {
				$close_template = MLATemplate_support::mla_fetch_custom_template( $markup_values['mla_markup'], 'term-list', 'markup', 'close' );
			}

			if ( empty( $close_template ) ) {
				$close_template = '';
			}

			if ( $is_list || ( ( 0 == $current_level ) && $is_dropdown ) || $is_checklist ) {
				// Look for gallery-level markup substitution parameters
				$new_text = $open_template . $close_template;
				$markup_values = MLAData::mla_expand_field_level_parameters( $new_text, $attr, $markup_values );

				$markup_values = apply_filters( 'mla_term_list_open_values', $markup_values );
				$open_template = apply_filters( 'mla_term_list_open_template', $open_template );
				if ( empty( $open_template ) ) {
					$gallery_open = '';
				} else {
					$gallery_open = MLAData::mla_parse_template( $open_template, $markup_values );
				}

				$list .=  apply_filters( 'mla_term_list_open_parse', $gallery_open, $open_template, $markup_values );
			}
		} // is_list

		// Find delimiter for currentlink, currentlink_url
		if ( strpos( $markup_values['page_url'], '?' ) ) {
			$current_item_delimiter = '&';
		} else {
			$current_item_delimiter = '?';
		}

		$has_active = false;
		foreach ( $terms as $key => $term ) {
			$item_values = $markup_values;
			$is_active = false;

			// fill in item-specific elements
			$item_values['key'] = $key;
			$item_values['term_id'] = $term->term_id;
			$item_values['name'] = wptexturize( $term->name );
			$item_values['slug'] = $term->slug;
			$item_values['term_group'] = $term->term_group;
			$item_values['term_taxonomy_id'] = $term->term_taxonomy_id;
			$item_values['taxonomy'] = $term->taxonomy;
			$item_values['description'] = wptexturize( $term->description );
			$item_values['parent'] = $term->parent;
			$item_values['count'] = isset ( $term->count ) ? 0 + $term->count : 0; 
			$item_values['link_url'] = $term->link;
			$item_values['currentlink_url'] = sprintf( '%1$s%2$scurrent_item=%3$d', $item_values['page_url'], $current_item_delimiter, $item_values['term_id'] );
			$item_values['editlink_url'] = $term->edit_link;
			$item_values['termlink_url'] = $term->term_link;
			$item_values['children'] = '';
			$item_values['termtag_attributes'] = '';
			$item_values['termtag_class'] = $term->parent ? 'term-list-term children' : 'term-list-term';
			$item_values['termtag_id'] = sprintf( '%1$s-%2$d', $item_values['taxonomy'], $item_values['term_id'] );
			// Added in the code below:
			$item_values['caption'] = '';
			$item_values['link_attributes'] = '';
			$item_values['active_item_class'] = '';
			$item_values['current_item_class'] = '';
			$item_values['rollover_text'] = '';
			$item_values['link_style'] = '';
			$item_values['link_text'] = '';
			$item_values['currentlink'] = '';
			$item_values['editlink'] = '';
			$item_values['termlink'] = '';
			$item_values['thelink'] = '';

			if ( ! empty( $arguments[ $mla_item_parameter ] ) ) {
				foreach ( $arguments[ $mla_item_parameter ] as $current_item ) {
					// Check for multi-taxonomy taxonomy.term compound values
					$value = explode( '.', $current_item );
					if ( 2 === count( $value ) ) {
						if ( $value[0] !== $term->taxonomy ) {
							continue;
						}

						$current_item = $value[1];
					}

					if ( $current_is_slug || !( ctype_digit( $current_item ) || is_int( $current_item ) ) ) {
						if ( $current_item == $term->slug ) {
							$is_active = true;
							$item_values['current_item_class'] = $arguments['current_item_class'];
							break;
						}
					} else {
						if ( $current_item == $term->term_id ) {
							$is_active = true;
							$item_values['current_item_class'] = $arguments['current_item_class'];
							break;
						}
					}
				}
			}

			// Add item_specific field-level substitution parameters
			$new_text = isset( $item_template ) ? $item_template : '';
			foreach( self::$term_list_item_specific_arguments as $index => $value ) {
				$new_text .= str_replace( '{+', '[+', str_replace( '+}', '+]', $arguments[ $index ] ) );
			}

			$item_values = MLAData::mla_expand_field_level_parameters( $new_text, $attr, $item_values );

			if ( $item_values['captiontag'] ) {
				$item_values['caption'] = wptexturize( $term->description );
				if ( ! empty( $arguments['mla_caption'] ) ) {
					$item_values['caption'] = wptexturize( self::_process_shortcode_parameter( $arguments['mla_caption'], $item_values ) );
				}
			} else {
				$item_values['caption'] = '';
			}

			// Apply the Display Content parameters.
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

			if ( ! empty( $arguments['mla_link_text'] ) ) {
				$item_values['link_text'] = esc_attr( self::_process_shortcode_parameter( $arguments['mla_link_text'], $item_values ) );
			} else {
				$item_values['link_text'] = $item_values['name'];
			}

			if ( ! empty( $arguments['show_count'] ) && ( 'true' == strtolower( $arguments['show_count'] ) ) ) {
				// Ignore option-all
				if ( -1 !== $item_values['count'] ) {
					$item_values['link_text'] .= ' (' . $item_values['count'] . ')';
				}
			}

			if ( empty( $arguments['mla_item_value'] ) ) {
				$item_values['thevalue'] = $item_values['term_id'];
			} else {
				$item_values['thevalue'] = self::_process_shortcode_parameter( $arguments['mla_item_value'], $item_values );
			}

			// Currentlink, editlink, termlink and thelink  TODO - link style
			$item_values['currentlink'] = sprintf( '<a %1$shref="%2$s%3$s%4$s=%5$s" title="%6$s" style="%7$s">%8$s</a>', $link_attributes, $item_values['page_url'], $current_item_delimiter, $mla_item_parameter, $item_values['thevalue'], $item_values['rollover_text'], '', $item_values['link_text'] );
			$item_values['editlink'] = sprintf( '<a %1$shref="%2$s" title="%3$s" style="%4$s">%5$s</a>', $link_attributes, $item_values['editlink_url'], $item_values['rollover_text'], '', $item_values['link_text'] );
			$item_values['termlink'] = sprintf( '<a %1$shref="%2$s" title="%3$s" style="%4$s">%5$s</a>', $link_attributes, $item_values['termlink_url'], $item_values['rollover_text'], '', $item_values['link_text'] );

			if ( ! empty( $link_href ) ) {
				$item_values['thelink'] = sprintf( '<a %1$shref="%2$s" title="%3$s" style="%4$s">%5$s</a>', $link_attributes, $link_href, $item_values['rollover_text'], '', $item_values['link_text'] );
			} elseif ( 'current' == $arguments['link'] ) {
				$item_values['link_url'] = $item_values['currentlink_url'];
				$item_values['thelink'] = $item_values['currentlink'];
			} elseif ( 'edit' == $arguments['link'] ) {
				$item_values['thelink'] = $item_values['editlink'];
			} elseif ( 'view' == $arguments['link'] ) {
				$item_values['thelink'] = $item_values['termlink'];
			} elseif ( 'span' == $arguments['link'] ) {
				$item_values['thelink'] = sprintf( '<span %1$sstyle="%2$s">%3$s</span>', $link_attributes, '', $item_values['link_text'] );
			} else {
				$item_values['thelink'] = $item_values['link_text'];
			}

			if ( $is_dropdown || $is_checklist ) {
				// Indent the dropdown list
				if ( $is_dropdown && $current_level && $is_hierarchical ) {
					$pad = str_repeat('&nbsp;', $current_level * 3);
				} else {
					$pad = '';
				}

				if ( empty( $arguments['mla_option_text'] ) ) {
					$item_values['thelabel'] = $pad . $item_values['link_text'];
				} else {
					$item_values['thelabel'] = $pad . self::_process_shortcode_parameter( $arguments['mla_option_text'], $item_values );
				}

				if ( empty( $arguments['mla_option_value'] ) ) {
					$item_values['thevalue'] = $item_values['term_id'];

					// Combined hierarchical multi-taxonomy controls generate compound taxonomy.term values 
					if ( ( $is_dropdown || $is_checklist ) && 1 < count( $arguments['taxonomy'] ) ) {
						if ( !( $is_hierarchical && !$combine_hierarchical ) ) {
							$item_values['thevalue'] = $item_values['taxonomy'] . '.' . $item_values['term_id'];
						}
					}
				} else {
					$item_values['thevalue'] = self::_process_shortcode_parameter( $arguments['mla_option_value'], $item_values );
				}

				$item_values['popular'] = ''; // TODO Calculate 'term-list-popular'

				if ( $item_values['current_item_class'] == $arguments['current_item_class'] ) {
					if ( $is_dropdown ) {
						$item_values['selected'] = 'selected=selected';
					} else {
						$item_values['selected'] = 'checked=checked';
					}
				} else {
					$item_values['selected'] = '';
				}
			}

			$child_links = array();
			$child_active = false;
			if ( $is_hierarchical && !empty( $term->children ) ) {
				$child_active = self::_compose_term_list( $item_values['children'], $child_links, $term->children, $markup_values, $arguments, $attr );
				$markup_values['current_level'] = $current_level; // Changed in _compose_term_list
			}

			if ( $is_active || $child_active ) {
				$has_active = true;
				$item_values['active_item_class'] = $arguments['active_item_class'];
			}

			if ( $is_list || $is_dropdown || $is_checklist ) {
				// item markup
				$item_values = apply_filters( 'mla_term_list_item_values', $item_values );
				$item_template = apply_filters( 'mla_term_list_item_template', $item_template );
				$parse_value = MLAData::mla_parse_template( $item_template, $item_values );
				$list .= apply_filters( 'mla_term_list_item_parse', $parse_value, $item_template, $item_values );
			} else {
				$item_values = apply_filters( 'mla_term_list_item_values', $item_values );
				$links[] = apply_filters( 'mla_term_list_item_parse', $item_values['thelink'], NULL, $item_values );

				if ( $is_hierarchical && !empty( $child_links ) ) {
					$links = array_merge( $links, $child_links );
				}
			} 
		} // foreach tag

		if ( $is_list || $is_dropdown || $is_checklist ) {
			if ( $is_list || ( ( 0 == $current_level ) && $is_dropdown ) || $is_checklist ) {
				$markup_values = apply_filters( 'mla_term_list_close_values', $markup_values );
				$close_template = apply_filters( 'mla_term_list_close_template', $close_template );
				$parse_value = MLAData::mla_parse_template( $close_template, $markup_values );
				$list .= apply_filters( 'mla_term_list_close_parse', $parse_value, $close_template, $markup_values );
			}
		} else {
			switch ( $markup_values['mla_output'] ) {
			case 'array' :
				$list =& $links;
				break;
			case 'flat' :
			default :
				$list .= join( $markup_values['separator'], $links );
				break;
			} // switch format
		}

		return $has_active;
	}

	/**
	 * These are the default parameters for term list display
	 *
	 * @since 2.30
	 *
	 * @var	array
	 */
	private static $term_list_item_specific_arguments = array(
			'mla_link_attributes' => '',
			'mla_link_class' => '',
			'mla_link_href' => '',
			'mla_link_text' => '',
			'mla_rollover_text' => '',
			'mla_caption' => '',
			'mla_item_value' => '',

			'mla_control_name' => '',
			'mla_option_text' => '',
			'mla_option_value' => '',
		);

	/**
	 * The MLA Term List support function.
	 *
	 * This is an alternative to the WordPress wp_list_categories, wp_dropdown_categories
	 * and wp_terms_checklist functions, with additional options to customize the hyperlink
	 * behind each term.
	 *
	 * @since 2.25
	 *
	 * @param array $attr Attributes of the shortcode.
	 *
	 * @return string HTML content to display the term list, dropdown control or checklist.
	 */
	public static function mla_term_list( $attr ) {
		global $post;

		// Some do_shortcode callers may not have a specific post in mind
		if ( ! is_object( $post ) ) {
			$post = (object) self::$empty_post;
		}

		// $instance supports multiple lists in one page/post	
		static $instance = 0;
		$instance++;

		// Some values are already known, and can be used in data selection parameters
		$upload_dir = wp_upload_dir();
		$page_values = array(
			'instance' => $instance,
			'selector' => "mla_term_list-{$instance}",
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

		$defaults = array_merge(
			self::$mla_get_terms_parameters,
			array(
			'echo' => false,
			'mla_debug' => false,
			'mla_output' => 'ulist',
			'hierarchical' => 'true',

			'separator' => "\n",
			'single_text' => '%d item',
			'multiple_text' => '%d items',
			'link' => 'current',
			'current_item' => '',
			'active_item_class' => 'mla_active_item',
			'current_item_class' => 'mla_current_item',
			'mla_item_parameter' => 'current_item',
			'show_count' => false,

			'mla_style' => NULL,
			'mla_markup' => NULL,
			'itemtag' => 'ul',
			'termtag' => 'li',
			'captiontag' => '',
			'mla_multi_select' => '',

			'mla_nolink_text' => '',
			'mla_target' => '',
			'hide_if_empty' => false,
			'option_all_text' => '',
			'option_all_value' => NULL,
			'option_none_text' => '',
			'option_none_value' => NULL,

			'depth' => 0,
			'child_of' => 0,
			'include_tree' => NULL,
			'exclude_tree' => NULL,
			),
			self::$term_list_item_specific_arguments
		);

		// Filter the attributes before $mla_item_parameter and "request:" prefix processing.
		$attr = apply_filters( 'mla_term_list_raw_attributes', $attr );

		/*
		 * The current_item parameter can be changed to support
		 * multiple lists per page.
		 */
		if ( ! isset( $attr['mla_item_parameter'] ) ) {
			$attr['mla_item_parameter'] = $defaults['mla_item_parameter'];
		}

		// The mla_item_parameter can contain page_level parameters like {+page_ID+}
		$attr_value = str_replace( '{+', '[+', str_replace( '+}', '+]', $attr['mla_item_parameter'] ) );
		$mla_item_parameter = MLAData::mla_parse_template( $attr_value, $page_values );
		 
		/*
		 * Special handling of mla_item_parameter to make multiple lists per page easier.
		 * Look for this parameter in $_REQUEST if it's not present in the shortcode itself.
		 */
		if ( ! isset( $attr[ $mla_item_parameter ] ) ) {
			if ( isset( $_REQUEST[ $mla_item_parameter ] ) ) {
				$attr[ $mla_item_parameter ] = $_REQUEST[ $mla_item_parameter ];
			}
		}
		 
		// Determine markup template to get default arguments
		$arguments = shortcode_atts( $defaults, $attr );

		/*
		 * $mla_item_parameter, if non-default, doesn't make it through the shortcode_atts
		 * filter, so we handle it separately
		 */
		if ( ! isset( $arguments[ $mla_item_parameter ] ) ) {
			if ( isset( $attr[ $mla_item_parameter ] ) ) {
				$arguments[ $mla_item_parameter ] = $attr[ $mla_item_parameter ];
			} else {
				$arguments[ $mla_item_parameter ] = $defaults['current_item'];

			}
		}

		if ( $arguments['mla_markup'] ) {
			$template = $arguments['mla_markup'];
			if ( ! MLATemplate_Support::mla_fetch_custom_template( $template, 'term-list', 'markup', '[exists]' ) ) {
				$template = NULL;
			}
		} else {
			$template = NULL;
		}

		if ( empty( $template ) ) {
			$output_parameters = array_map( 'strtolower', array_map( 'trim', explode( ',', $arguments['mla_output'] ) ) );

			if ( !in_array( $output_parameters[0], array( 'flat', 'list', 'ulist', 'olist', 'dlist', 'dropdown', 'checklist', 'array' ) ) ) {
				$output_parameters[0] = 'ulist';
			}

			if ( in_array( $output_parameters[0], array( 'list', 'ulist', 'olist', 'dlist' ) ) ) {
				if ( ( 'dlist' == $output_parameters[0] ) || ('list' == $output_parameters[0] && 'dd' == $arguments['captiontag'] ) ) {
					$template = 'term-list-dl';
				} else {
					$template = 'term-list-ul';
				}
			} elseif ( 'dropdown' == $output_parameters[0] ) {
				$template = 'term-list-dropdown';
			} elseif ( 'checklist' == $output_parameters[0] ) {
				$template = 'term-list-checklist';
			}
		}

		// Apply default arguments set in the markup template
		$arguments = MLATemplate_Support::mla_fetch_custom_template( $template, 'term-list', 'markup', 'arguments' );
		if ( !empty( $arguments ) ) {
			$attr = wp_parse_args( $attr, self::_validate_attributes( array(), $arguments ) );
		}

		// Adjust data selection arguments; remove pagination-specific arguments
		unset( $attr['limit'] );
		unset( $attr['offset'] );

		/*
		 * Look for page-level, 'request:' and 'query:' substitution parameters,
		 * which can be added to any input parameter
		 */
		foreach ( $attr as $attr_key => $attr_value ) {
			/*
			 * item-specific Display Content parameters must be evaluated
			 * later, when all of the information is available.
			 */
			if ( array_key_exists( $attr_key, self::$term_list_item_specific_arguments ) ) {
				continue;
			}

			$attr_value = str_replace( '{+', '[+', str_replace( '+}', '+]', $attr_value ) );
			$replacement_values = MLAData::mla_expand_field_level_parameters( $attr_value, $attr, $page_values );
			$attr[ $attr_key ] = MLAData::mla_parse_template( $attr_value, $replacement_values );
		}

		$attr = apply_filters( 'mla_term_list_attributes', $attr );
		$arguments = shortcode_atts( $defaults, $attr );

		/*
		 * $mla_item_parameter, if non-default, doesn't make it through the shortcode_atts
		 * filter, so we handle it separately
		 */
		if ( ! isset( $arguments[ $mla_item_parameter ] ) ) {
			if ( isset( $attr[ $mla_item_parameter ] ) ) {
				$arguments[ $mla_item_parameter ] = $attr[ $mla_item_parameter ];
			} else {
				$arguments[ $mla_item_parameter ] = $defaults['current_item'];

			}
		}

		// Clean up the current_item(s) to separate term_id from slug
		if ( ! empty( $arguments[ $mla_item_parameter ] ) ) {
			if ( is_string( $arguments[ $mla_item_parameter ] ) ) {
				$arguments[ $mla_item_parameter ] = explode( ',', $arguments[ $mla_item_parameter ] );
			}

			foreach( $arguments[ $mla_item_parameter ] as $index => $value ) {
				if ( ctype_digit( $value ) ) {
					$arguments[ $mla_item_parameter ][ $index ] = absint( $value );
				}
			}
		}

		$arguments = apply_filters( 'mla_term_list_arguments', $arguments );

		// Clean up hierarchical parameter to simplify later processing
		$arguments['hierarchical'] = strtolower( trim( $arguments['hierarchical'] ) ) ;
		if ( !in_array( $arguments['hierarchical'], array( 'true', 'combine' ) ) ) {
			$arguments['hierarchical'] = 'false';
		}

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
			MLACore::mla_debug_add( __LINE__ . ' <strong>' . __( 'mla_debug REQUEST', 'media-library-assistant' ) . '</strong> = ' . var_export( $_REQUEST, true ) );
			MLACore::mla_debug_add( __LINE__ . ' <strong>' . __( 'mla_debug attributes', 'media-library-assistant' ) . '</strong> = ' . var_export( $attr, true ) );
			MLACore::mla_debug_add( __LINE__ . ' <strong>' . __( 'mla_debug arguments', 'media-library-assistant' ) . '</strong> = ' . var_export( $arguments, true ) );
		}

		// Determine templates and output type
		if ( $arguments['mla_style'] && ( 'none' !== $arguments['mla_style'] ) ) {
			if ( !MLATemplate_Support::mla_fetch_custom_template( $arguments['mla_style'], 'term-list', 'style', '[exists]' ) ) {
				MLACore::mla_debug_add( '<strong>mla_term_list mla_style</strong> "' . $arguments['mla_style'] . '" ' . __( 'not found', 'media-library-assistant' ), MLACore::MLA_DEBUG_CATEGORY_ANY );
				$arguments['mla_style'] = NULL;
			}
		}

		if ( $arguments['mla_markup'] ) {
			if ( !MLATemplate_Support::mla_fetch_custom_template( $arguments['mla_markup'], 'term-list', 'markup', '[exists]' ) ) {
				MLACore::mla_debug_add( '<strong>mla_term_list mla_markup</strong> "' . $arguments['mla_markup'] . '" ' . __( 'not found', 'media-library-assistant' ), MLACore::MLA_DEBUG_CATEGORY_ANY );
				$arguments['mla_markup'] = NULL;
			}
		}

		$output_parameters = array_map( 'strtolower', array_map( 'trim', explode( ',', $arguments['mla_output'] ) ) );

		if ( !in_array( $output_parameters[0], array( 'flat', 'list', 'ulist', 'olist', 'dlist', 'dropdown', 'checklist', 'array' ) ) ) {
			$output_parameters[0] = 'ulist';
		}

		$default_style = 'term-list';
		$default_markup = 'term-list-ul';

		if ( $is_list = in_array( $output_parameters[0], array( 'list', 'ulist', 'olist', 'dlist' ) ) ) {

			if ( 'list' == $output_parameters[0] && 'dd' == $arguments['captiontag'] ) {
				$default_markup = 'term-list-dl';
				$arguments['itemtag'] = 'dl';
				$arguments['termtag'] = 'dt';
			} else {
				$default_markup = 'term-list-ul';
				$arguments['termtag'] = 'li';
				$arguments['captiontag'] = '';

				switch ( $output_parameters[0] ) {
					case 'ulist':
						$arguments['itemtag'] = 'ul';
						break;
					case 'olist':
						$arguments['itemtag'] = 'ol';
						break;
					case 'dlist':
						$default_markup = 'term-list-dl';
						$arguments['itemtag'] = 'dl';
						$arguments['termtag'] = 'dt';
						$arguments['captiontag'] = 'dd';
					break;
					default:
						$arguments['itemtag'] = 'ul';
				}
			}
		}

		if ( $is_dropdown = 'dropdown' == $output_parameters[0] ) {
			$default_markup = 'term-list-dropdown';
			$arguments['itemtag'] = empty( $attr['itemtag'] ) ? 'select' : $attr['itemtag'];
			$arguments['termtag'] = 'option';
		}

		if ( $is_checklist = 'checklist' == $output_parameters[0] ) {
			$default_markup = 'term-list-checklist';
			$arguments['termtag'] = 'li';
		}

		if ( NULL == $arguments['mla_style'] ) {
			$arguments['mla_style'] = $default_style;
		}

		if ( NULL == $arguments['mla_markup'] ) {
			$arguments['mla_markup'] = $default_markup;
		}

		$mla_multi_select = !empty( $arguments['mla_multi_select'] ) && ( 'true' == strtolower( $arguments['mla_multi_select'] ) );

		$is_hierarchical = !( 'false' === $arguments['hierarchical'] );
		$combine_hierarchical = 'combine' === $arguments['hierarchical'];

		// Convert lists to arrays
		if ( is_string( $arguments['taxonomy'] ) ) {
			$arguments['taxonomy'] = explode( ',', $arguments['taxonomy'] );
		}

		if ( is_string( $arguments['post_type'] ) ) {
			$arguments['post_type'] = explode( ',', $arguments['post_type'] );
		}

		if ( is_string( $arguments['post_status'] ) ) {
			$arguments['post_status'] = explode( ',', $arguments['post_status'] );
		}

		// Hierarchical exclude is done in _get_term_tree to exclude children
		if ( $is_hierarchical && isset( $arguments['exclude'] ) ) {
			$exclude_later = $arguments['exclude'];
			unset( $arguments['exclude'] );
		} else {
			$exclude_later = NULL;
		}

		$tags = self::mla_get_terms( $arguments );
		if ( !empty( $exclude_later ) ) {
			$arguments['exclude'] = $exclude_later;
		}

		// Invalid taxonomy names return WP_Error
		if ( is_wp_error( $tags ) ) {
			$list =  '<strong>' . __( 'ERROR', 'media-library-assistant' ) . ': ' . $tags->get_error_message() . '</strong>, ' . $tags->get_error_data( $tags->get_error_code() );

			if ( 'array' == $arguments['mla_output'] ) {
				return array( $list );
			}

			if ( empty($arguments['echo']) ) {
				return $list;
			}

			echo $list;
			return;
		}

		// Fill in the item_specific link properties, calculate list parameters
		if ( isset( $tags['found_rows'] ) ) {
			$found_rows = $tags['found_rows'];
			unset( $tags['found_rows'] );
		} else {
			$found_rows = count( $tags );
		}

		$show_empty = false;
		if ( 0 == $found_rows ) {
			if ( self::$mla_debug ) {
				MLACore::mla_debug_add( '<strong>' . __( 'mla_debug empty list', 'media-library-assistant' ) . '</strong>, query = ' . var_export( $arguments, true ) );
				$list = MLACore::mla_debug_flush();

				if ( '<p></p>' == $list ) {
					$list = '';
				}
			} else {
				$list = '';
			}

			if ( 'array' == $arguments['mla_output'] ) {
				$list .= $arguments['mla_nolink_text'];

				if ( empty( $list ) ) {
					return array();
				} else {
					return array( $list );
				}
			}

			$show_empty = empty( $arguments['hide_if_empty'] ) || ( 'true' !== strtolower( $arguments['hide_if_empty'] ) );
			if ( ( $is_checklist || $is_dropdown ) && $show_empty ) {
				if ( empty( $arguments['option_none_text'] ) ) {
					$arguments['option_none_text'] = __( 'no-terms', 'media-library-assistant' );
				}

				if ( !empty( $arguments['option_none_value'] ) ) {
					$option_none_value = self::_process_shortcode_parameter( $arguments['option_none_value'], $page_values );
					if ( is_numeric( $option_none_value ) ) {
						$option_none_id = intval( $option_none_value );
						$option_none_slug = sanitize_title( $arguments['option_none_text'] );
					} else {
						$option_none_id = -1;
						$option_none_slug = sanitize_title( $option_none_value );
					}
				} else {
					$option_none_id = -1;
					$option_none_slug = sanitize_title( $arguments['option_none_text'] );
				}

				$tags[0] = ( object ) array(
					'term_id' => $option_none_id,
					'name' => $arguments['option_none_text'],
					'slug' => $option_none_slug,
					'term_group' => '0',
					'term_taxonomy_id' => $option_none_id,
					'taxonomy' => reset( $arguments['taxonomy'] ),
					'description' => '',
					'parent' => '0',
					'count' => 0,
					'level' => 0,
					'edit_link' => '',
					'term_link' => '',
					'link' => '',
				);

				$is_hierarchical = false;
				$found_rows = 1;
			} else {
				$list .= $arguments['mla_nolink_text'];

				if ( empty($arguments['echo']) ) {
					return $list;
				}

				echo $list;
				return;
			}
		}

		if ( self::$mla_debug ) {
			$list = MLACore::mla_debug_flush();
		} else {
			$list = '';
		}

		$add_all_option = ( $is_checklist || $is_dropdown ) && !empty( $arguments['option_all_text'] ) && !$show_empty;

		// Using the slug is a common practice and affects option_all_value
		if ( $add_all_option ) {
			if ( !empty( $arguments['option_all_value'] ) ) {
				$option_all_value = self::_process_shortcode_parameter( $arguments['option_all_value'], $page_values );
				if ( is_numeric( $option_all_value ) ) {
					$option_all_id = intval( $option_all_value );
					$option_all_slug = sanitize_title( $arguments['option_all_text'] );
				} else {
					$option_all_id = 0;
					$option_all_slug = sanitize_title( $option_all_value );
				}
			} else {
				$option_all_id = 0;
				$option_all_slug = sanitize_title( $arguments['option_all_text'] );
			}
		} else {
			$option_all_id = 0;
			$option_all_slug = 'all';
		}

		if ( $is_hierarchical ) {
			$tags = self::_get_term_tree( $tags, $arguments );

			if ( is_wp_error( $tags ) ) {
				$list =  '<strong>' . __( 'ERROR', 'media-library-assistant' ) . ': ' . $tags->get_error_message() . '</strong>, ' . $tags->get_error_data( $tags->get_error_code() );

				if ( 'array' == $arguments['mla_output'] ) {
					return array( $list );
				}

				if ( empty($arguments['echo']) ) {
					return $list;
				}

				echo $list;
				return;
			}

			if ( isset( $tags['found_rows'] ) ) {
				$found_rows = $tags['found_rows'];
				unset( $tags['found_rows'] );
			} else {
				$found_rows = count( $tags );
			}
		} else {
			if ( !$show_empty ) {
				foreach ( $tags as $key => $tag ) {
					$tags[ $key ]->level = 0;
					$link = get_edit_tag_link( $tag->term_id, $tag->taxonomy );
					if ( ! is_wp_error( $link ) ) {
						$tags[ $key ]->edit_link = $link;
						$link = get_term_link( intval($tag->term_id), $tag->taxonomy );
						$tags[ $key ]->term_link = $link;
					}

					if ( is_wp_error( $link ) ) {
						$list =  '<strong>' . __( 'ERROR', 'media-library-assistant' ) . ': ' . $link->get_error_message() . '</strong>, ' . $link->get_error_data( $link->get_error_code() );

						if ( 'array' == $arguments['mla_output'] ) {
							return array( $list );
						}

						if ( empty($arguments['echo']) ) {
							return $list;
						}

						echo $list;
						return;
					}

					if ( 'edit' == $arguments['link'] ) {
						$tags[ $key ]->link = $tags[ $key ]->edit_link;
					} else {
						$tags[ $key ]->link = $tags[ $key ]->term_link;
					}
				} // foreach tag
			}
		}

		if ( $add_all_option ) {
			$found_rows += 1;
		}

		$style_values = array_merge( $page_values, array(
			'mla_output' => $arguments['mla_output'],
			'mla_style' => $arguments['mla_style'],
			'mla_markup' => $arguments['mla_markup'],
			'taxonomy' => implode( '-', $arguments['taxonomy'] ),
			'current_item' => $arguments['current_item'],
			'itemtag' => tag_escape( $arguments['itemtag'] ),
			'termtag' => tag_escape( $arguments['termtag'] ),
			'captiontag' => tag_escape( $arguments['captiontag'] ),
			'multiple' => $arguments['mla_multi_select'] ? 'multiple' : '',
			'itemtag_attributes' => '',
			'itemtag_class' => 'term-list term-list-taxonomy-' . implode( '-', $arguments['taxonomy'] ), 
			'itemtag_id' => $page_values['selector'],
			'all_found_rows' => $found_rows,
			'found_rows' => $found_rows,
			'separator' => $arguments['separator'],
			'single_text' => $arguments['single_text'],
			'multiple_text' => $arguments['multiple_text'],
			'echo' => $arguments['echo'],
			'link' => $arguments['link']
		) );

		$style_template = $gallery_style = '';
		$use_mla_term_list_style = 'none' != strtolower( $style_values['mla_style'] );
		if ( apply_filters( 'use_mla_term_list_style', $use_mla_term_list_style, $style_values['mla_style'] ) ) {
			$style_template = MLATemplate_support::mla_fetch_custom_template( $style_values['mla_style'], 'term-list', 'style' );
			if ( empty( $style_template ) ) {
				$style_values['mla_style'] = $default_style;
				$style_template = MLATemplate_support::mla_fetch_custom_template( $default_style, 'term-list', 'style' );
			}

			if ( ! empty ( $style_template ) ) {
				/*
				 * Look for 'query' and 'request' substitution parameters
				 */
				$style_values = MLAData::mla_expand_field_level_parameters( $style_template, $attr, $style_values );

				$style_values = apply_filters( 'mla_term_list_style_values', $style_values );
				$style_template = apply_filters( 'mla_term_list_style_template', $style_template );
				$gallery_style = MLAData::mla_parse_template( $style_template, $style_values );
				$gallery_style = apply_filters( 'mla_term_list_style_parse', $gallery_style, $style_template, $style_values );
			} // !empty template
		} // use_mla_term_list_style

		$list .= $gallery_style;
		$markup_values = $style_values;

		if ( empty( $arguments['mla_control_name'] ) ) {
			$mla_control_name = 'tax_input[[+taxonomy+]][]';
		} else {
			$mla_control_name = $arguments['mla_control_name'];;
		}

		// Accumulate links for flat and array output
		$tag_links = array();

		if ( $is_hierarchical ) {

			if ( $combine_hierarchical ) {
				$combined_tags = array();
				foreach( $tags as $taxonomy => $root_terms ) {
					$combined_tags = array_merge( $combined_tags, $root_terms );
				}
				$tags = array( $markup_values['taxonomy'] => $combined_tags );
			} // $combine_hierarchical

			foreach( $tags as $taxonomy => $root_terms ) {
				$markup_values['taxonomy'] = $taxonomy;
				$markup_values['thename'] = self::_process_shortcode_parameter( $mla_control_name, $markup_values );

				// Add the optional 'all-terms' option, if requested
				if ( $add_all_option ) {
					$option_all = ( object ) array(
						'term_id' => $option_all_id,
						'name' => $arguments['option_all_text'],
						'slug' => $option_all_slug,
						'term_group' => '0',
						'term_taxonomy_id' => $option_all_id,
						'taxonomy' => $taxonomy,
						'description' => '',
						'parent' => '0',
						'count' => -1,
						'level' => 0,
						'edit_link' => '',
						'term_link' => '',
						'link' => '',
					);

					array_unshift( $root_terms, $option_all );
					$add_to_found_rows = 1;
				} else {
					$add_to_found_rows = 0;
				}

				if ( isset( $root_terms['found_rows'] ) ) {
					$markup_values['found_rows'] = $add_to_found_rows + $root_terms['found_rows'];
					unset( $root_terms['found_rows'] );
				} else {
					$markup_values['found_rows'] = count( $root_terms );
				}

				if ( count( $root_terms ) ) {
					self::_compose_term_list( $list, $tag_links, $root_terms, $markup_values, $arguments, $attr );
				}
			}
		} else {
			$markup_values['thename'] = self::_process_shortcode_parameter( $mla_control_name, $markup_values );

			// Add the optional 'all-terms' option, if requested
			if ( $add_all_option ) {
				$option_all = ( object ) array(
					'term_id' => $option_all_id,
					'name' => $arguments['option_all_text'],
					'slug' => $option_all_slug,
					'term_group' => '0',
					'term_taxonomy_id' => $option_all_id,
					'taxonomy' => $markup_values['taxonomy'],
					'description' => '',
					'parent' => '0',
					'count' => -1,
					'level' => 0,
					'edit_link' => '',
					'term_link' => '',
					'link' => '',
				);

				array_unshift( $tags, $option_all );
			}

			if ( count( $tags ) ) {
				self::_compose_term_list( $list, $tag_links, $tags, $markup_values, $arguments, $attr );
			}
		}

		if ( 'array' == $arguments['mla_output'] || empty($arguments['echo']) ) {
			return $list;
		}

		echo $list;
	}

	/**
	 * The MLA Term List shortcode.
	 *
	 * This is an interface to the mla_term_list function.
	 *
	 * @since 2.25
	 *
	 * @param array $attr Attributes of the shortcode.
	 * @param string $content Optional content for enclosing shortcodes
	 *
	 * @return string HTML content to display the term list.
	 */
	public static function mla_term_list_shortcode( $attr, $content = NULL ) {
		/*
		 * Make sure $attr is an array, even if it's empty,
		 * and repair damage caused by link-breaks in the source text
		 */
		$attr = self::_validate_attributes( $attr, $content );

		// The 'array' format makes no sense in a shortcode
		if ( isset( $attr['mla_output'] ) && 'array' == $attr['mla_output'] ) {
			$attr['mla_output'] = 'flat';
		}
			 
		// A shortcode must return its content to the caller, so "echo" makes no sense
		$attr['echo'] = false;

		if ( !empty( $attr['mla_output'] ) ) {
			switch ( $attr['mla_output'] ) {
				case 'wp_list_categories':
					return wp_list_categories( $attr );
				case 'wp_dropdown_categories':
					return wp_dropdown_categories( $attr );
				case 'wp_terms_checklist':
					require_once( ABSPATH . 'wp-admin/includes/template.php' );
					return wp_terms_checklist( 0, $attr );
			}
		}

		return self::mla_term_list( $attr );
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

		if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_ENABLE_MLA_ICONS ) ) {
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
		$new_text = str_replace( '{\+', '\[\+', str_replace( '+\}', '+\\\\]', $text ) );
		$new_text = str_replace( '{', '[', str_replace( '}', ']', $new_text ) );
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
		$markup_values = apply_filters( 'mla_gallery_pagination_values', $markup_values );

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
		$order = isset( $query_parameters['order'] ) ? ' ' . trim( strtoupper( $query_parameters['order'] ) ) : '';
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
				'id' => 'ID',
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
			if ( $count && ( $value == $matches[0] ) ) {
				$matches[1] = strtolower( $matches[1] );
				if ( isset( $matches[2] ) ) {
					$matches[2] = strtoupper( $matches[2] );
				}

				if ( array_key_exists( $matches[1], $allowed_keys ) ) {
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
				} // allowed key
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
			// 'tax_input' => ''
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
			'mla_phrase_delimiter' => '',
			'mla_phrase_connector' => '',
			'mla_term_delimiter' => '',
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
	 * Data selection parameters for the WP_Query in [mla_gallery]
	 *
	 * @since 2.40
	 *
	 * @var	array
	 */
	private static $mla_get_shortcode_dynamic_attachments_parameters = array(
			// Taxonomy parameters are handled separately
			// {tax_slug} => 'term' | array ( 'term', 'term', ... )
			// 'tax_query' => ''
			// 'tax_relation' => 'OR', 'AND' (default),
			// 'tax_operator' => 'OR' (default), 'IN', 'NOT IN', 'AND',
			// 'tax_include_children' => true (default), false
		);

	/**
	 * Parses shortcode parameters and returns the gallery objects
	 *
	 * @since 2.20
	 *
	 * @param int Post ID of the parent
	 * @param array Attributes of the shortcode
	 * @param boolean true to calculate and return ['found_rows', 'max_num_pages'] as array elements
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

			// remove pagination parameters to get a complete result
			$attr['nopaging'] = true;
			unset( $attr['numberposts'] );
			unset( $attr['posts_per_page'] );
			unset( $attr['posts_per_archive_page'] );
			unset( $attr['paged'] );
			unset( $attr['offset'] );
			unset( $attr['mla_paginate_current'] );
			unset( $attr['mla_page_parameter'] );
			unset( $attr['mla_paginate_total'] );

			// There's no point in sorting the items
			$attr['orderby'] = 'none';
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
		self::$mla_get_shortcode_dynamic_attachments_parameters = array();
		$query_arguments = array();
		$no_terms_assigned_query = false;
		if ( ! empty( $attr ) ) {
			$all_taxonomies = get_taxonomies( array ( 'show_ui' => true ), 'names' );
			$simple_tax_queries = array();
			foreach ( $attr as $key => $value ) {
				if ( 'tax_query' == $key ) {
					if ( is_array( $value ) ) {
						$query_arguments[ $key ] = $value;
						self::$mla_get_shortcode_dynamic_attachments_parameters[ $key ] = $value;
					} else {
						$value = self::_sanitize_query_specification( $value );

						// Replace invalid queries from "where-used" callers with a harmless equivalent
						if ( $where_used_query && ( false !== strpos( $value, '{+' ) ) ) {
							$value = "array( array( 'taxonomy' => 'none', 'field' => 'slug', 'terms' => 'none' ) )";
						}

						try {
							$function = @create_function('', 'return ' . $value . ';' );
						} catch ( Throwable $e ) { // PHP 7
							$function = NULL;
						} catch ( Exception $e ) { // PHP 5
							$function = NULL;
						}

						if ( is_callable( $function ) ) {
							$tax_query = $function();
						} else {
							$tax_query = NULL;

						}

						if ( is_array( $tax_query ) ) {
							// Check for no.terms.assigned
							foreach ( $tax_query as $tax_query_key => $tax_query_element ) {
								if ( !is_array( $tax_query_element ) ) {
									continue;
								}
								
								if ( isset( $tax_query_element['taxonomy'] ) ) {
									$tax_query_taxonomy = $tax_query_element['taxonomy'];
								} else {
									continue;
								}

								if ( isset( $tax_query_element['terms'] ) && is_array( $tax_query_element['terms'] ) && in_array( 'no.terms.assigned', $tax_query_element['terms'] ) ) {
									$tax_query[ $tax_query_key ]['terms'] = get_terms( $tax_query_taxonomy, array(
										'fields' => 'ids',
										'hide_empty' => false
									) );
								}
							}
							
							$query_arguments[ $key ] = $tax_query;
							self::$mla_get_shortcode_dynamic_attachments_parameters[ $key ] = $value;
							break; // Done - the tax_query overrides all other taxonomy parameters
						} else {
							return '<p>' . __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Invalid mla_gallery', 'media-library-assistant' ) . ' tax_query = ' . var_export( $value, true ) . '</p>';
						}
					} // not array
				}  // tax_query
				elseif ( 'tax_input' == $key ) {
					$tax_queries = array();
					$compound_values = array_filter( array_map( 'trim', explode( ',', $value ) ) );
					foreach ( $compound_values as $compound_value ) {
						$value = explode( '.', $compound_value );
						if ( 2 === count( $value ) ) {
							if ( array_key_exists( $value[0], $all_taxonomies ) ) {
								$tax_queries[ $value[0] ][] = $value[1];
							} // valid taxonomy
						} // valid coumpound value
					} // foreach compound_value

					foreach( $tax_queries as $key => $value ) {
						$simple_tax_queries[ $key ] = implode(',', $value );
					}
				} // tax_input
				elseif ( array_key_exists( $key, $all_taxonomies ) ) {
					$simple_tax_queries[ $key ] = implode(',', array_filter( array_map( 'trim', explode( ',', $value ) ) ) );
					if ( 'no.terms.assigned' === $simple_tax_queries[ $key ] ) {
						$no_terms_assigned_query = true;
					}
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
			} elseif ( ( 1 < count( $simple_tax_queries ) ) || isset( $attr['tax_operator'] ) || isset( $attr['tax_include_children'] ) || $no_terms_assigned_query ) {
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
					if ( 'false' == strtolower( $attr['tax_include_children'] ) ) {
						$tax_include_children = false;
					}
				}

				foreach( $simple_tax_queries as $key => $value ) {
					if ( empty( $value ) ) {
						continue;
					}

					if ( 'no.terms.assigned' === $value ) {
						$term_list = get_terms( $key, array(
							'fields' => 'ids',
							'hide_empty' => false
						) );

						$tax_query[] = array(
							'taxonomy' => $key,
							'field' => 'id',
							'terms' => $term_list,
							'operator' => 'NOT IN' 
						);

						continue;
					}

					$tax_query[] =	array( 'taxonomy' => $key, 'field' => 'slug', 'terms' => explode( ',', $value ), 'operator' => $tax_operator, 'include_children' => $tax_include_children );
				}

				$query_arguments['tax_query'] = $tax_query;
				self::$mla_get_shortcode_dynamic_attachments_parameters['tax_query'] = $tax_query;
			} else {
				// exactly one simple query is present
				if ( isset( $simple_tax_queries['category'] ) ) {
					$arguments['category_name'] = $simple_tax_queries['category'];
				} else {
					$query_arguments = $simple_tax_queries;
					self::$mla_get_shortcode_dynamic_attachments_parameters = $simple_tax_queries;
				}
			}
		} // ! empty

		// Finish building the dynamic parameters
		if ( isset( $attr['tax_relation'] ) ) {
			self::$mla_get_shortcode_dynamic_attachments_parameters['tax_relation'] = $attr['tax_relation'];
		}

		if ( isset( $attr['tax_operator'] ) ) {
			self::$mla_get_shortcode_dynamic_attachments_parameters['tax_operator'] = $attr['tax_operator'];
		}

		if ( isset( $attr['tax_include_children'] ) ) {
			self::$mla_get_shortcode_dynamic_attachments_parameters['tax_include_children'] = $attr['tax_include_children'];
		}

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
				default:
					if ( false !== strpos( $value, ',' ) ) {
						self::$query_parameters['post_parent'] = array_filter( array_map( 'absint', explode( ',', $value ) ) );
						$value = NULL;
						$use_children = false;
					}
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
			case 'mla_phrase_delimiter':
			case 'mla_term_delimiter':
				if ( ! empty( $value ) ) {
					MLAQuery::$search_parameters[ $key ] = substr( $value, 0, 1 );
				}

				unset( $arguments[ $key ] );
				break;
			case 'mla_terms_phrases':
				$children_ok = false;
				$value = stripslashes( trim( $value ) );
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
				MLAQuery::$search_parameters['s'] = stripslashes( trim( $value ) );
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
						$value = self::_sanitize_query_specification( $value );

						// Replace invalid queries from "where-used" callers with a harmless equivalent
						if ( $where_used_query && ( false !== strpos( $value, '{+' ) ) ) {
							$value = "array( array( 'key' => 'unlikely', 'value' => 'none or otherwise unlikely' ) )";
						}

						try {
							$function = @create_function('', 'return ' . $value . ';' );
						} catch ( Throwable $e ) { // PHP 7
							$function = NULL;
						} catch ( Exception $e ) { // PHP 5
							$function = NULL;
						}

						if ( is_callable( $function ) ) {
							$date_query = $function();
						} else {
							$date_query = NULL;
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
						$value = self::_sanitize_query_specification( $value );

						// Replace invalid queries from "where-used" callers with a harmless equivalent
						if ( $where_used_query && ( false !== strpos( $value, '{+' ) ) ) {
							$value = "array( array( 'key' => 'unlikely', 'value' => 'none or otherwise unlikely' ) )";
						}

						try {
							$function = @create_function('', 'return ' . $value . ';' );
						} catch ( Throwable $e ) { // PHP 7
							$function = NULL;
						} catch ( Exception $e ) { // PHP 5
							$function = NULL;
						}

						if ( is_callable( $function ) ) {
							$meta_query = $function();
						} else {
							$meta_query = NULL;
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
		 * Apply the archive/search tests here because WP_Query doesn't apply them to galleries within
		 * search results or archive pages.
		 */
		if ( self::$mla_debug ) {
			MLACore::mla_debug_add( '<strong>mla_debug is_archive()</strong> = ' . var_export( is_archive(), true ) );
			MLACore::mla_debug_add( '<strong>mla_debug is_search()</strong> = ' . var_export( is_search(), true ) );
		}

		if ( isset( $query_arguments['posts_per_archive_page'] ) && ( is_archive() || is_search() ) ) {
			$query_arguments['posts_per_page'] = $query_arguments['posts_per_archive_page'];
		}
		unset( $query_arguments['posts_per_archive_page'] );

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

			if ( ! ( isset( $query_arguments['posts_per_page'] ) && ( 0 < $query_arguments['posts_per_page'] ) ) ) {
				$query_arguments['posts_per_page'] = count($incposts);  // only the number of posts included
			}

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

				if ( empty( MLAQuery::$search_parameters['mla_phrase_delimiter'] ) ) {
					MLAQuery::$search_parameters['mla_terms_search']['phrase_delimiter'] = ' ';
				} else {
					MLAQuery::$search_parameters['mla_terms_search']['phrase_delimiter'] = MLAQuery::$search_parameters['mla_phrase_delimiter'];
				}

				if ( empty( MLAQuery::$search_parameters['mla_phrase_connector'] ) ) {
					MLAQuery::$search_parameters['mla_terms_search']['radio_phrases'] = 'AND';
				} else {
					MLAQuery::$search_parameters['mla_terms_search']['radio_phrases'] = MLAQuery::$search_parameters['mla_phrase_connector'];
				}

				if ( empty( MLAQuery::$search_parameters['mla_term_delimiter'] ) ) {
					MLAQuery::$search_parameters['mla_terms_search']['term_delimiter'] = ',';
				} else {
					MLAQuery::$search_parameters['mla_terms_search']['term_delimiter'] = MLAQuery::$search_parameters['mla_term_delimiter'];
				}

				if ( empty( MLAQuery::$search_parameters['mla_term_connector'] ) ) {
					MLAQuery::$search_parameters['mla_terms_search']['radio_terms'] = 'OR';
				} else {
					MLAQuery::$search_parameters['mla_terms_search']['radio_terms'] = MLAQuery::$search_parameters['mla_term_connector'];
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
		 * apply_filters('relevanssi_prevent_default_request', $prevent, $query );
		 */
		if ( function_exists( 'relevanssi_prevent_default_request' ) ) {
			add_filter( 'relevanssi_admin_search_ok', 'MLAQuery::mla_query_relevanssi_admin_search_ok_filter' );
			add_filter( 'relevanssi_prevent_default_request', 'MLAQuery::mla_query_relevanssi_prevent_default_request_filter' );
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

			if ( $return_found_rows ) {
				$attachments['found_rows'] = absint( MLAShortcodes::$mla_gallery_wp_query_object->found_posts );
				$attachments['max_num_pages'] = absint( MLAShortcodes::$mla_gallery_wp_query_object->max_num_pages );
			}

			$filtered_attachments = apply_filters_ref_array( 'mla_gallery_the_attachments', array( NULL, &$attachments ) ) ;
			if ( !is_null( $filtered_attachments ) ) {
				$attachments = $filtered_attachments;
			}
		}

		if ( ! empty( MLAQuery::$search_parameters ) ) {
			remove_filter( 'posts_groupby', 'MLAQuery::mla_query_posts_groupby_filter' );
			remove_filter( 'posts_search', 'MLAQuery::mla_query_posts_search_filter' );
		}

		if ( function_exists( 'relevanssi_prevent_default_request' ) ) {
			remove_filter( 'relevanssi_admin_search_ok', 'MLAQuery::mla_query_relevanssi_admin_search_ok_filter' );
			remove_filter( 'relevanssi_prevent_default_request', 'MLAQuery::mla_query_relevanssi_prevent_default_request_filter' );
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
			if ( is_array( self::$query_parameters['post_parent'] ) ) {
				$parent_list = implode( ',', self::$query_parameters['post_parent'] );
				$where_clause .= " AND {$table_prefix}posts.post_parent IN ({$parent_list})";
			} else {
				switch ( self::$query_parameters['post_parent'] ) {
				case 'any':
					$where_clause .= " AND {$table_prefix}posts.post_parent > 0";
					break;
				case 'none':
					$where_clause .= " AND {$table_prefix}posts.post_parent < 1";
					break;
				}
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
	 * Data selection parameters for [mla_tag_cloud], [mla_term_list]
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
	 * fields - string with fields for the SQL SELECT clause, e.g.,
	 *          't.term_id, t.name, t.slug, COUNT(p.ID) AS `count`'
	 *
	 * @since 2.20
	 *
	 * @param	array	taxonomies to search and query parameters
	 *
	 * @return	array	array of term objects, empty if none found
	 */
	public static function mla_get_terms( $attr ) {
		global $wpdb;

		// Make sure $attr is an array, even if it's empty
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

		// Build an array of individual clauses that can be filtered
		$clauses = array( 'fields' => '', 'join' => '', 'where' => '', 'orderby' => '', 'limits' => '', );

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

			// Add type and status constraints
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
		if ( !empty( $clause_parameters ) ) {
			$clauses['join'] = $wpdb->prepare( $clause, $clause_parameters );
		} else {
			$clauses['join'] = $clause;
		}

		// Start WHERE clause with a taxonomy constraint
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

			// If there are no terms we want an empty cloud
			if ( empty( $includes ) ) {
				$arguments['include'] = (string) 0x7FFFFFFF;
			} else {
				ksort( $includes );
				$arguments['include'] = implode( ',', $includes );
			}
		}

		// Add include/exclude and parent constraints to WHERE cluse
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
		if ( !empty( $clause_parameters ) ) {
			$clauses['where'] = $wpdb->prepare( $clause, $clause_parameters );
		} else {
			$clauses['where'] = $clause;
		}

		// For the inner/initial query, always select the most popular terms
		if ( $no_orderby = 'true' == (string) $arguments['no_orderby'] ) {
			$arguments['orderby'] = 'count';
			$arguments['order']  = 'DESC';
		}

		// Add sort order
		if ( 'none' !== strtolower( $arguments['orderby'] ) ) {
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
	
			$clauses['orderby'] = 'ORDER BY ' . self::_validate_sql_orderby( $arguments, '', $allowed_keys, $binary_keys );
		} else {
			$clauses['orderby'] = '';
		}

		// Add pagination
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

		// Build the final query
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
		 * Unless specifically told to omit the ORDER BY clause or the COUNT,
		 * supply a sort order for the initial/inner query only
		 */
		if ( ! ( $no_orderby || $no_count ) ) {
			$query[] = 'ORDER BY count DESC, t.term_id ASC';
		}

		// Limit the total number of terms returned
		$terms_limit = absint( $arguments['number'] );
		if ( 0 < $terms_limit ) {
			$query[] = "LIMIT {$terms_limit}";
		}

		// $final_clauses, if present, require an SQL subquery
		$final_clauses = array();

		if ( !empty( $clauses['orderby'] ) && 'ORDER BY count DESC' != $clauses['orderby'] ) {
			$final_clauses[] = $clauses['orderby'];
		}

		if ( '' !== $clauses['limits'] ) {
			$final_clauses[] = $clauses['limits'];
		}

		// If we're limiting the final results, we need to get an accurate total count first
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
	 * Walk a list of terms and find hierarchy, preserving source order.
	 *
	 * @since 2.25
	 *
	 * @param	array	$terms Term objects, by reference
	 * @param	array	$arguments Shortcode arguments, including defaults
	 *
	 * @return	array	( [taxonomy] => array( [root terms] => array( [fields], array( 'children' => [child terms] )
	 */
	private static function _get_term_tree( &$terms, $arguments = array() ) {
		$term = current( $terms );

		if ( empty( $term ) or ! isset( $term->parent ) ) {
			return array();
		}

		/*
		 * Set found_rows aside to be restored later
		 */
		if ( isset( $terms['found_rows'] ) ) {
			$found_rows = $terms['found_rows'];
			unset( $terms['found_rows'] );
		} else {
			$found_rows = NULL;
		}

		$child_of = ! empty( $arguments['child_of'] ) ? absint( $arguments['child_of'] ) : NULL;
		$include_tree = ! empty( $arguments['include_tree'] ) ? wp_parse_id_list( $arguments['include_tree'] ) : NULL;
		$exclude_tree = empty( $include_tree ) && !empty( $arguments['exclude_tree'] ) ? wp_parse_id_list( $arguments['exclude_tree'] ) : NULL;

		$depth = !empty( $arguments['depth'] ) ? absint( $arguments['depth'] ) : 0;
		$term_tree = array();
		$root_ids = array();
		$parents = array();
		$child_ids = array();
		foreach( $terms as $term ) {
			// TODO Make this conditional on $arguments['link']
			$link = get_edit_tag_link( $term->term_id, $term->taxonomy );
			if ( ! is_wp_error( $link ) ) {
				$term->edit_link = $link;
				$link = get_term_link( intval($term->term_id), $term->taxonomy );
				$term->term_link = $link;
			}

			if ( is_wp_error( $link ) ) {
				return $link;
			}

			if ( 'edit' == $arguments['link'] ) {
				$term->link = $term->edit_link;
			} else {
				$term->link = $term->term_link;
			}

			$term->children = array();
			$parent = absint( $term->parent );
			if ( 0 == $parent ) {
				$term_tree[ $term->taxonomy ][] = $term;
				$root_ids[ $term->taxonomy ][ $term->term_id ] = count( $term_tree[ $term->taxonomy ] ) - 1;
			} else {
				$parents[ $term->taxonomy ][ $term->parent ][] = $term;
				$child_ids[ $term->taxonomy ][ $term->term_id ] = absint( $term->parent );
			}
		}

		/*
		 * Collapse multi-level children
		 */
		foreach ( $parents as $taxonomy => $tax_parents ) {
			if ( ! isset( $term_tree[ $taxonomy ] ) ) {
				$term_tree[ $taxonomy ] = array();
				$root_ids[ $taxonomy ] = array();
			}

			while ( !empty( $tax_parents ) ) {
				foreach( $tax_parents as $parent_id => $children ) {
					foreach( $children as $index => $child ) {
						if ( ! array_key_exists( $child->term_id, $tax_parents ) ) {

							if ( array_key_exists( $child->parent, $root_ids[ $taxonomy ] ) ) {
								// Found a root node - attach the leaf
								$term_tree[ $taxonomy ][ $root_ids[ $taxonomy ][ $child->parent ] ]->children[] = $child;
							} elseif ( isset( $child_ids[ $taxonomy ][ $child->parent ] ) ) {
								// Found a non-root parent node - attach the leaf
								$the_parent = $child_ids[ $taxonomy ][ $child->parent ];
								foreach( $tax_parents[ $the_parent ] as $candidate_index => $candidate ) {
									if ( $candidate->term_id == $child->parent ) {
										$parents[ $taxonomy ][ $the_parent ][ $candidate_index ]->children[] = $child;
										break;
									}
								} // foreach candidate
							} else {
								// No parent exists; make this a root node
								$term_tree[ $taxonomy ][] = $child;
								$root_ids[ $taxonomy ][ $child->term_id ] = count( $term_tree[ $taxonomy ] ) - 1;
							} // Move the leaf node

							unset( $tax_parents[ $parent_id ][ $index ] );
							if ( empty( $tax_parents[ $parent_id ] ) ) {
								unset( $tax_parents[ $parent_id ] );
							}
						} // leaf node; no children
					} // foreach child
				} // foreach parent_id
			} // has parents
		} // foreach taxonomy

		/*
		 * Calculate and potentially trim parent/child tree
		 */
		$all_terms_count = 0;
		foreach ( array_keys( $term_tree ) as $taxonomy ) {
			if ( $child_of ) {
				$result = self::_find_child_of( $term_tree[ $taxonomy ], $child_of );
				if ( false !== $result ) {
					$term_tree[ $taxonomy ] = $result->children;
				} else {
					$term_tree[ $taxonomy ] = array();
					continue;
				}
			} // $child_of

			if ( $include_tree ) {
				$result = self::_find_include_tree( $term_tree[ $taxonomy ], $include_tree );
				if ( false !== $result ) {
					$term_tree[ $taxonomy ] = $result;
				} else {
					$term_tree[ $taxonomy ] = array();
					continue;
				}
			} // $include_tree

			if ( $exclude_tree ) {
				self::_remove_exclude_tree( $term_tree[ $taxonomy ], $exclude_tree );
			} // $include_tree

			$term_count = 0;
			$root_limit = count( $term_tree[ $taxonomy ] );

			if ( $root_limit ) {
				for ( $root_index = 0; $root_index < $root_limit; $root_index++ ) {
					if ( isset( $term_tree[ $taxonomy ][ $root_index ] ) ) {
						$term_count++;
						$term_tree[ $taxonomy ][ $root_index ]->level = 0;
						if ( ! empty( $term_tree[ $taxonomy ][ $root_index ]->children ) ) {
							$term_count += self::_count_term_children( $term_tree[ $taxonomy ][ $root_index ], $depth );
						}
					} else {
						$root_limit++;
					}
				}
			}

			$term_tree[ $taxonomy ]['found_rows'] = $term_count;
			$all_terms_count += $term_count;
		}

		$term_tree['found_rows'] = $all_terms_count;
		return $term_tree;
	} // _get_term_tree

	/**
	 * Find a term that matches $child_of
	 *
	 * @since 2.25
	 *
	 * @param	array	$parents Potential parent Term objects, by reference
	 * @param	integer	$parent_id Term_id of the desired parent
	 *
	 * @return	mixed	Term object of the desired parent else false
	 */
	private static function _find_child_of( &$parents, $parent_id ) {
		foreach( $parents as $parent ) {
			if ( $parent_id == $parent->term_id ) {
				return $parent;
			}

			$result = self::_find_child_of( $parent->children, $parent_id );
			if ( false !== $result ) {
				return $result;
			}
		}

		return false;
	} // _find_child_of

	/**
	 * Find the term(s) that match $include_tree
	 *
	 * @since 2.25
	 *
	 * @param	array	$terms Potential term objects, by reference
	 * @param	array	$include_tree term_id(s) of the desired terms
	 *
	 * @return	mixed	Term object(s) of the desired terms else false
	 */
	private static function _find_include_tree( &$terms, $include_tree ) {
		$new_tree = array();

		foreach( $terms as $term ) {
			if ( in_array( $term->term_id, $include_tree ) ) {
				$new_tree[] = $term;
			} elseif ( !empty( $term->children ) ) {
				$result = self::_find_include_tree( $term->children, $include_tree );
				if ( false !== $result ) {
					$new_tree = array_merge( $new_tree, $result );
				}
			}
		}

		if ( empty( $new_tree ) ) {		
			return false;
		}

		return $new_tree;
	} // _find_include_tree

	/**
	 * Remove the term(s) that match $exclude_tree
	 *
	 * @since 2.25
	 *
	 * @param	array	$terms Potential term objects, by reference
	 * @param	array	$exclude_tree term_id(s) of the desired terms
	 *
	 * @return	void	Term object(s) are removed from the &parents array
	 */
	private static function _remove_exclude_tree( &$terms, $exclude_tree ) {
		foreach( $terms as $index => $term ) {
			if ( in_array( $term->term_id, $exclude_tree ) ) {
				unset( $terms[ $index ] );
			} elseif ( !empty( $term->children ) ) {
				self::_remove_exclude_tree( $term->children, $exclude_tree );
			}
		}
	} // _remove_exclude_tree

	/**
	 * Add level to term children and count them, all levels.
	 *
	 * Recalculates term counts by including items from child terms. Assumes all
	 * relevant children are already in the $terms argument.
	 *
	 * @since 2.25
	 *
	 * @param	object	$parent Parent Term objects, by reference
	 * @param	integer	$depth Maximum depth of parent/child relationship
	 *
	 * @return	integer	Total number of children, all levels
	 */
	private static function _count_term_children( &$parent, $depth = 0 ) {
		$term_count = 0;
		$child_level = $parent->level + 1;

		// level is zero-based, depth is one-based
		if ( $depth && $child_level >= $depth ) {
			$parent->children = array();
			return 0;
		}

		$child_limit = count( $parent->children );
		for ( $child_index = 0; $child_index < $child_limit; $child_index++ ) {
				if ( isset( $parent->children[ $child_index ] ) ) {
					$term_count++;
					$parent->children[ $child_index ]->level = $child_level;
					if ( ! empty( $parent->children[ $child_index ]->children ) ) {
						$term_count += self::_count_term_children( $parent->children[ $child_index ], $depth );
					}
				} else {
					$child_limit++;
				}
			}

		return $term_count;
	} // _count_term_children

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
?>