<?php

class wpcmsb_cmsblockTemplate {

	public static function get_default( $prop = 'form' ) {
		if ( 'form' == $prop ) {
			$template = self::form();
		} elseif ( 'messages' == $prop ) {
			$template = self::messages();
		} else {
			$template = null;
		}

		return apply_filters( 'wpcmsb_default_template', $template, $prop );
	}

	public static function form() {
		$template =
			'<p>' . __( 'Your Name', 'cms-block' )
			. ' ' . __( '(required)', 'cms-block' ) . '<br />' . "\n"
			. '    [text* your-name] </p>' . "\n\n"
			. '<p>' . __( 'Your Email', 'cms-block' )
			. ' ' . __( '(required)', 'cms-block' ) . '<br />' . "\n"
			. '    [email* your-email] </p>' . "\n\n"
			. '<p>' . __( 'Subject', 'cms-block' ) . '<br />' . "\n"
			. '    [text your-subject] </p>' . "\n\n"
			. '<p>' . __( 'Your Message', 'cms-block' ) . '<br />' . "\n"
			. '    [textarea your-message] </p>' . "\n\n"
			. '<p>[submit "' . __( 'Send', 'cms-block' ) . '"]</p>';

		$template = "";	
		return $template;
	}

	public static function messages() {
		$messages = array();

		foreach ( wpcmsb_messages() as $key => $arr ) {
			$messages[$key] = $arr['default'];
		}

		return $messages;
	}
}

function wpcmsb_messages() {
	$messages = array(
		
		'accept_terms' => array(
			'description'
				=> __( "There are terms that the sender must accept", 'cms-block' ),
			'default'
				=> __( 'Please accept the terms to proceed.', 'cms-block' )
		),

		'invalid_required' => array(
			'description'
				=> __( "There is a field that the sender must fill in", 'cms-block' ),
			'default'
				=> __( 'Please fill in the required field.', 'cms-block' )
		),

		'invalid_too_long' => array(
			'description'
				=> __( "There is a field that the user input is longer than the maximum allowed length", 'cms-block' ),
			'default'
				=> __( 'This input is too long.', 'cms-block' )
		),

		'invalid_too_short' => array(
			'description'
				=> __( "There is a field that the user input is shorter than the minimum allowed length", 'cms-block' ),
			'default'
				=> __( 'This input is too short.', 'cms-block' )
		)
	);

	return apply_filters( 'wpcmsb_messages', $messages );
}
