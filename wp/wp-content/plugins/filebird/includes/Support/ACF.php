<?php

namespace FileBird\Support;

defined( 'ABSPATH' ) || exit;

class ACF {

	public function __construct() {
		add_action( 'acf/include_field_types', array( $this, 'include_field' ) ); // v5
		add_action( 'acf/register_fields', array( $this, 'include_field' ) ); // v4
	}

	public function include_field( $version = false ) {
		if ( ! $version ) {
			$version = 4;
		}
		include_once 'ACF/acf-field-filebird-v' . $version . '.php';
	}
}
