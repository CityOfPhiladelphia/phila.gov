<?php

add_action( 'init', 'wpcmsb_control_init', 11 );

function wpcmsb_control_init() {
	if ( ! isset( $_SERVER['REQUEST_METHOD'] ) ) {
		return;
	}

	if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
		if ( isset( $_GET['_wpcmsb_is_ajax_call'] ) ) {
			wpcmsb_ajax_onload();
		}
	}

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
		if ( isset( $_POST['_wpcmsb_is_ajax_call'] ) ) {
			wpcmsb_ajax_json_echo();
		}

		wpcmsb_submit_nonajax();
	}
}

function wpcmsb_ajax_onload() {
	$echo = '';
	$items = array();

	if ( isset( $_GET['_wpcmsb'] )
	&& $cms_block = wpcmsb_cms_block( (int) $_GET['_wpcmsb'] ) ) {
		$items = apply_filters( 'wpcmsb_ajax_onload', $items );
	}

	$echo = json_encode( $items );

	if ( wpcmsb_is_xhr() ) {
		@header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
		echo $echo;
	}

	exit();
}

function wpcmsb_ajax_json_echo() {
	$echo = '';

	if ( isset( $_POST['_wpcmsb'] ) ) {
		$id = (int) $_POST['_wpcmsb'];
		$unit_tag = wpcmsb_sanitize_unit_tag( $_POST['_wpcmsb_unit_tag'] );

		if ( $cms_block = wpcmsb_cms_block( $id ) ) {
			$items = array(
				'mailSent' => false,
				'into' => '#' . $unit_tag,
				'captcha' => null );

			$result = $cms_block->submit( true );

			if ( ! empty( $result['message'] ) ) {
				$items['message'] = $result['message'];
			}

			if ( 'mail_sent' == $result['status'] ) {
				$items['mailSent'] = true;
			}

			if ( 'validation_failed' == $result['status'] ) {
				$invalids = array();

				foreach ( $result['invalid_fields'] as $name => $field ) {
					$invalids[] = array(
						'into' => 'span.wpcmsb-form-control-wrap.'
							. sanitize_html_class( $name ),
						'message' => $field['reason'],
						'idref' => $field['idref'] );
				}

				$items['invalids'] = $invalids;
			}

			if ( 'spam' == $result['status'] ) {
				$items['spam'] = true;
			}

			if ( ! empty( $result['scripts_on_sent_ok'] ) ) {
				$items['onSentOk'] = $result['scripts_on_sent_ok'];
			}

			if ( ! empty( $result['scripts_on_submit'] ) ) {
				$items['onSubmit'] = $result['scripts_on_submit'];
			}

			$items = apply_filters( 'wpcmsb_ajax_json_echo', $items, $result );
		}
	}

	$echo = json_encode( $items );

	if ( wpcmsb_is_xhr() ) {
		@header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
		echo $echo;
	} else {
		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		echo '<textarea>' . $echo . '</textarea>';
	}

	exit();
}

function wpcmsb_is_xhr() {
	if ( ! isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) )
		return false;

	return $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
}

function wpcmsb_submit_nonajax() {
	if ( ! isset( $_POST['_wpcmsb'] ) )
		return;

	if ( $cms_block = wpcmsb_cms_block( (int) $_POST['_wpcmsb'] ) ) {
		$cms_block->submit();
	}
}

add_filter( 'widget_text', 'wpcmsb_widget_text_filter', 9 );

function wpcmsb_widget_text_filter( $content ) {
	if ( ! preg_match( '/\[[\r\n\t ]*cms-block(-7)?[\r\n\t ].*?\]/', $content ) )
		return $content;

	$content = do_shortcode( $content );

	return $content;
}

// add_action( 'wp_enqueue_scripts', 'wpcmsb_do_enqueue_scripts' );

// function wpcmsb_do_enqueue_scripts() {
// 	if ( wpcmsb_load_js() ) {
// 		wpcmsb_enqueue_scripts();
// 	}

// 	if ( wpcmsb_load_css() ) {
// 		wpcmsb_enqueue_styles();
// 	}
// }

function wpcmsb_enqueue_scripts() {

	$in_footer = true;

	if ( 'header' === wpcmsb_load_js() ) {
		$in_footer = false;
	}

	wp_enqueue_script( 'cms-block',
		wpcmsb_plugin_url( 'includes/js/scripts.js' ),
		array( 'jquery', 'jquery-form' ), wpcmsb_VERSION, $in_footer );

	$_wpcmsb = array(
		'loaderUrl' => wpcmsb_ajax_loader(),
		'sending' => __( 'Sending ...', 'cms-block' ) );

	if ( defined( 'WP_CACHE' ) && WP_CACHE )
		$_wpcmsb['cached'] = 1;

	if ( wpcmsb_support_html5_fallback() )
		$_wpcmsb['jqueryUi'] = 1;

	wp_localize_script( 'cms-block', '_wpcmsb', $_wpcmsb );

	do_action( 'wpcmsb_enqueue_scripts' );
}

function wpcmsb_script_is() {
	return wp_script_is( 'cms-block' );
}

// function wpcmsb_enqueue_styles() {
// 	wp_enqueue_style( 'cms-block',
// 		wpcmsb_plugin_url( 'includes/css/styles.css' ),
// 		array(), wpcmsb_VERSION, 'all' );

// 	if ( wpcmsb_is_rtl() ) {
// 		wp_enqueue_style( 'cms-block-rtl',
// 			wpcmsb_plugin_url( 'includes/css/styles-rtl.css' ),
// 			array(), wpcmsb_VERSION, 'all' );
// 	}

// 	do_action( 'wpcmsb_enqueue_styles' );
// }

function wpcmsb_style_is() {
	return wp_style_is( 'cms-block' );
}

/* HTML5 Fallback */

add_action( 'wp_enqueue_scripts', 'wpcmsb_html5_fallback', 20 );

function wpcmsb_html5_fallback() {
	if ( ! wpcmsb_support_html5_fallback() ) {
		return;
	}

	if ( wpcmsb_script_is() ) {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-spinner' );
	}

	if ( wpcmsb_style_is() ) {
		wp_enqueue_style( 'jquery-ui-smoothness',
			wpcmsb_plugin_url( 'includes/js/jquery-ui/themes/smoothness/jquery-ui.min.css' ), array(), '1.10.3', 'screen' );
	}
}
