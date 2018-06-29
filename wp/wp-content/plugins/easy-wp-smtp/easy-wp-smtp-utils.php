<?php

use ioncube\phpOpensslCryptor\Cryptor;

class SWPSMTPUtils {

    var $enc_key;
    protected static $instance = null;

    function __construct() {
	require_once('inc/Cryptor.php');
	$key = get_option( 'swpsmtp_enc_key', false );
	if ( empty( $key ) ) {
	    $key = wp_salt();
	    update_option( 'swpsmtp_enc_key', $key );
	}
	$this->enc_key = $key;
    }

    public static function get_instance() {

	// If the single instance hasn't been set, set it now.
	if ( null == self::$instance ) {
	    self::$instance = new self;
	}

	return self::$instance;
    }

    function encrypt_password( $pass ) {
	if ( $pass === '' ) {
	    return '';
	}

	$password = Cryptor::Encrypt( $pass, $this->enc_key );
	return $password;
    }

    function decrypt_password( $pass ) {

	$password = Cryptor::Decrypt( $pass, $this->enc_key );
	return $password;
    }

}
