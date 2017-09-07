<?php

function wpcmsb_plugin_path( $path = '' ) {
	return path_join( wpcmsb_PLUGIN_DIR, trim( $path, '/' ) );
}

function wpcmsb_plugin_url( $path = '' ) {
	$url = plugins_url( $path, wpcmsb_PLUGIN );

	if ( is_ssl() && 'http:' == substr( $url, 0, 5 ) ) {
		$url = 'https:' . substr( $url, 5 );
	}

	return $url;
}

function wpcmsb_upload_dir( $type = false ) {
	$uploads = wp_upload_dir();

	$uploads = apply_filters( 'wpcmsb_upload_dir', array(
		'dir' => $uploads['basedir'],
		'url' => $uploads['baseurl'] ) );

	if ( 'dir' == $type )
		return $uploads['dir'];
	if ( 'url' == $type )
		return $uploads['url'];

	return $uploads;
}

function wpcmsb_l10n() {
	static $l10n = array();

	if ( ! empty( $l10n ) ) {
		return $l10n;
	}

	$l10n = array(
		'af' => __( 'Afrikaans', 'cms-block' ),
		'sq' => __( 'Albanian', 'cms-block' ),
		'ar' => __( 'Arabic', 'cms-block' ),
		'hy_AM' => __( 'Armenian', 'cms-block' ),
		'az' => __( 'Azerbaijani', 'cms-block' ),
		'bn_BD' => __( 'Bangla', 'cms-block' ),
		'eu' => __( 'Basque', 'cms-block' ),
		'be_BY' => __( 'Belarusian', 'cms-block' ),
		'bs_BA' => __( 'Bosnian', 'cms-block' ),
		'bg_BG' => __( 'Bulgarian', 'cms-block' ),
		'ca' => __( 'Catalan', 'cms-block' ),
		'ckb' => __( 'Central Kurdish', 'cms-block' ),
		'zh_CN' => __( 'Chinese (China)', 'cms-block' ),
		'zh_TW' => __( 'Chinese (Taiwan)', 'cms-block' ),
		'hr' => __( 'Croatian', 'cms-block' ),
		'cs_CZ' => __( 'Czech', 'cms-block' ),
		'da_DK' => __( 'Danish', 'cms-block' ),
		'nl_NL' => __( 'Dutch', 'cms-block' ),
		'en_US' => __( 'English (United States)', 'cms-block' ),
		'eo_EO' => __( 'Esperanto', 'cms-block' ),
		'et' => __( 'Estonian', 'cms-block' ),
		'fi' => __( 'Finnish', 'cms-block' ),
		'fr_FR' => __( 'French (France)', 'cms-block' ),
		'gl_ES' => __( 'Galician', 'cms-block' ),
		'gu_IN' => __( 'Gujarati', 'cms-block' ),
		'ka_GE' => __( 'Georgian', 'cms-block' ),
		'de_DE' => __( 'German', 'cms-block' ),
		'el' => __( 'Greek', 'cms-block' ),
		'ht' => __( 'Haitian', 'cms-block' ),
		'he_IL' => __( 'Hebrew', 'cms-block' ),
		'hi_IN' => __( 'Hindi', 'cms-block' ),
		'hu_HU' => __( 'Hungarian', 'cms-block' ),
		'bn_IN' => __( 'Indian Bengali', 'cms-block' ),
		'id_ID' => __( 'Indonesian', 'cms-block' ),
		'ga_IE' => __( 'Irish', 'cms-block' ),
		'it_IT' => __( 'Italian', 'cms-block' ),
		'ja' => __( 'Japanese', 'cms-block' ),
		'ko_KR' => __( 'Korean', 'cms-block' ),
		'lv' => __( 'Latvian', 'cms-block' ),
		'lt_LT' => __( 'Lithuanian', 'cms-block' ),
		'mk_MK' => __( 'Macedonian', 'cms-block' ),
		'ms_MY' => __( 'Malay', 'cms-block' ),
		'ml_IN' => __( 'Malayalam', 'cms-block' ),
		'mt_MT' => __( 'Maltese', 'cms-block' ),
		'nb_NO' => __( 'Norwegian (Bokmål)', 'cms-block' ),
		'fa_IR' => __( 'Persian', 'cms-block' ),
		'pl_PL' => __( 'Polish', 'cms-block' ),
		'pt_BR' => __( 'Portuguese (Brazil)', 'cms-block' ),
		'pt_PT' => __( 'Portuguese (Portugal)', 'cms-block' ),
		'pa_IN' => __( 'Punjabi', 'cms-block' ),
		'ru_RU' => __( 'Russian', 'cms-block' ),
		'ro_RO' => __( 'Romanian', 'cms-block' ),
		'sr_RS' => __( 'Serbian', 'cms-block' ),
		'si_LK' => __( 'Sinhala', 'cms-block' ),
		'sk_SK' => __( 'Slovak', 'cms-block' ),
		'sl_SI' => __( 'Slovene', 'cms-block' ),
		'es_ES' => __( 'Spanish (Spain)', 'cms-block' ),
		'sv_SE' => __( 'Swedish', 'cms-block' ),
		'ta' => __( 'Tamil', 'cms-block' ),
		'th' => __( 'Thai', 'cms-block' ),
		'tl' => __( 'Tagalog', 'cms-block' ),
		'tr_TR' => __( 'Turkish', 'cms-block' ),
		'uk' => __( 'Ukrainian', 'cms-block' ),
		'vi' => __( 'Vietnamese', 'cms-block' )
	);

	return $l10n;
}



function wpcmsb_is_valid_locale( $locale ) {
	$l10n = wpcmsb_l10n();
	return isset( $l10n[$locale] );

}

function wpcmsb_is_rtl( $locale = '' ) {
	if ( empty( $locale ) ) {
		return function_exists( 'is_rtl' ) ? is_rtl() : false;
	}

	$rtl_locales = array(
		'ar' => 'Arabic',
		'he_IL' => 'Hebrew',
		'fa_IR' => 'Persian' );

	return isset( $rtl_locales[$locale] );
}

function wpcmsb_ajax_loader() {
	$url = wpcmsb_plugin_url( 'images/ajax-loader.gif' );

	return apply_filters( 'wpcmsb_ajax_loader', $url );
}

function wpcmsb_verify_nonce( $nonce, $action = -1 ) {
	if ( substr( wp_hash( $action, 'nonce' ), -12, 10 ) == $nonce )
		return true;

	return false;
}

function wpcmsb_create_nonce( $action = -1 ) {
	return substr( wp_hash( $action, 'nonce' ), -12, 10 );
}

function wpcmsb_blacklist_check( $target ) {
	$mod_keys = trim( get_option( 'blacklist_keys' ) );

	if ( empty( $mod_keys ) ) {
		return false;
	}

	$words = explode( "\n", $mod_keys );

	foreach ( (array) $words as $word ) {
		$word = trim( $word );

		if ( empty( $word ) || 256 < strlen( $word ) ) {
			continue;
		}

		$pattern = sprintf( '#%s#i', preg_quote( $word, '#' ) );

		if ( preg_match( $pattern, $target ) ) {
			return true;
		}
	}

	return false;
}

function wpcmsb_array_flatten( $input ) {
	if ( ! is_array( $input ) )
		return array( $input );

	$output = array();

	foreach ( $input as $value )
		$output = array_merge( $output, wpcmsb_array_flatten( $value ) );

	return $output;
}

function wpcmsb_flat_join( $input ) {
	$input = wpcmsb_array_flatten( $input );
	$output = array();

	foreach ( (array) $input as $value )
		$output[] = trim( (string) $value );

	return implode( ', ', $output );
}

function wpcmsb_support_html5() {
	return (bool) apply_filters( 'wpcmsb_support_html5', true );
}

function wpcmsb_support_html5_fallback() {
	return (bool) apply_filters( 'wpcmsb_support_html5_fallback', false );
}

function wpcmsb_load_css() {
	return apply_filters( 'wpcmsb_load_css', wpcmsb_LOAD_CSS );
}

function wpcmsb_format_atts( $atts ) {
	$html = '';

	$prioritized_atts = array( 'type', 'name', 'value' );

	foreach ( $prioritized_atts as $att ) {
		if ( isset( $atts[$att] ) ) {
			$value = trim( $atts[$att] );
			$html .= sprintf( ' %s="%s"', $att, esc_attr( $value ) );
			unset( $atts[$att] );
		}
	}

	foreach ( $atts as $key => $value ) {
		$key = strtolower( trim( $key ) );

		if ( ! preg_match( '/^[a-z_:][a-z_:.0-9-]*$/', $key ) ) {
			continue;
		}

		$value = trim( $value );

		if ( '' !== $value ) {
			$html .= sprintf( ' %s="%s"', $key, esc_attr( $value ) );
		}
	}

	$html = trim( $html );

	return $html;
}

function wpcmsb_link( $url, $anchor_text, $args = '' ) {
	$defaults = array(
		'id' => '',
		'class' => '' );

	$args = wp_parse_args( $args, $defaults );
	$args = array_intersect_key( $args, $defaults );
	$atts = wpcmsb_format_atts( $args );

	$link = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
		esc_url( $url ),
		esc_html( $anchor_text ),
		$atts ? ( ' ' . $atts ) : '' );

	return $link;
}

function wpcmsb_load_textdomain( $locale = null ) {
	global $l10n;

	$domain = 'cms-block';

	if ( get_locale() == $locale ) {
		$locale = null;
	}

	if ( empty( $locale ) ) {
		if ( is_textdomain_loaded( $domain ) ) {
			return true;
		} else {
			return load_plugin_textdomain( $domain, false, $domain . '/languages' );
		}
	} else {
		$mo_orig = $l10n[$domain];
		unload_textdomain( $domain );

		$mofile = $domain . '-' . $locale . '.mo';
		$path = WP_PLUGIN_DIR . '/' . $domain . '/languages';

		if ( $loaded = load_textdomain( $domain, $path . '/'. $mofile ) ) {
			return $loaded;
		} else {
			$mofile = WP_LANG_DIR . '/plugins/' . $mofile;
			return load_textdomain( $domain, $mofile );
		}

		$l10n[$domain] = $mo_orig;
	}

	return false;
}


function wpcmsb_get_request_uri() {
	static $request_uri = '';

	if ( empty( $request_uri ) ) {
		$request_uri = add_query_arg( array() );
	}

	return esc_url_raw( $request_uri );
}

function wpcmsb_register_post_types() {
	if ( class_exists( 'wpcmsb_cmsblock' ) ) {
		wpcmsb_cmsblock::register_post_type();
		return true;
	} else {
		return false;
	}
}

function wpcmsb_version( $args = '' ) {
	$defaults = array(
		'limit' => -1,
		'only_major' => false );

	$args = wp_parse_args( $args, $defaults );

	if ( $args['only_major'] ) {
		$args['limit'] = 2;
	}

	$args['limit'] = (int) $args['limit'];

	$ver = wpcmsb_VERSION;
	$ver = strtr( $ver, '_-+', '...' );
	$ver = preg_replace( '/[^0-9.]+/', ".$0.", $ver );
	$ver = preg_replace( '/[.]+/', ".", $ver );
	$ver = trim( $ver, '.' );
	$ver = explode( '.', $ver );

	if ( -1 < $args['limit'] ) {
		$ver = array_slice( $ver, 0, $args['limit'] );
	}

	$ver = implode( '.', $ver );

	return $ver;
}

function wpcmsb_version_grep( $version, array $input ) {
	$pattern = '/^' . preg_quote( (string) $version, '/' ) . '(?:\.|$)/';

	return preg_grep( $pattern, $input );
}

function wpcmsb_enctype_value( $enctype ) {
	$enctype = trim( $enctype );

	if ( empty( $enctype ) ) {
		return '';
	}

	$valid_enctypes = array(
		'application/x-www-form-urlencoded',
		'multipart/form-data',
		'text/plain' );

	if ( in_array( $enctype, $valid_enctypes ) ) {
		return $enctype;
	}

	$pattern = '%^enctype="(' . implode( '|', $valid_enctypes ) . ')"$%';

	if ( preg_match( $pattern, $enctype, $matches ) ) {
		return $matches[1]; // for back-compat
	}

	return '';
}

function wpcmsb_rmdir_p( $dir ) {
	if ( is_file( $dir ) ) {
		@unlink( $dir );
		return true;
	}

	if ( ! is_dir( $dir ) ) {
		return false;
	}

	if ( $handle = @opendir( $dir ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( $file == "." || $file == ".." ) {
				continue;
			}

			wpcmsb_rmdir_p( path_join( $dir, $file ) );
		}

		closedir( $handle );
	}

	return @rmdir( $dir );
}

/* From _http_build_query in wp-includes/functions.php */
function wpcmsb_build_query( $args, $key = '' ) {
	$sep = '&';
	$ret = array();

	foreach ( (array) $args as $k => $v ) {
		$k = urlencode( $k );

		if ( ! empty( $key ) ) {
			$k = $key . '%5B' . $k . '%5D';
		}

		if ( null === $v ) {
			continue;
		} elseif ( false === $v ) {
			$v = '0';
		}

		if ( is_array( $v ) || is_object( $v ) ) {
			array_push( $ret, wpcmsb_build_query( $v, $k ) );
		} else {
			array_push( $ret, $k . '=' . urlencode( $v ) );
		}
	}

	return implode( $sep, $ret );
}

/**
 * Returns the number of code units in a string.
 *
 * @see http://www.w3.org/TR/html5/infrastructure.html#code-unit-length
 *
 * @return int|bool The number of code units, or false if mb_convert_encoding is not available.
 */
function wpcmsb_count_code_units( $string ) {
	static $use_mb = null;

	if ( is_null( $use_mb ) ) {
		$use_mb = function_exists( 'mb_convert_encoding' );
	}

	if ( ! $use_mb ) {
		return false;
	}

	$string = (string) $string;

	$encoding = mb_detect_encoding( $string, mb_detect_order(), true );

	if ( $encoding ) {
		$string = mb_convert_encoding( $string, 'UTF-16', $encoding );
	} else {
		$string = mb_convert_encoding( $string, 'UTF-16', 'UTF-8' );
	}

	$byte_count = mb_strlen( $string, '8bit' );

	return floor( $byte_count / 2 );
}

function wpcmsb_is_localhost() {
	$server_name = strtolower( $_SERVER['SERVER_NAME'] );
	return in_array( $server_name, array( 'localhost', '127.0.0.1' ) );
}
