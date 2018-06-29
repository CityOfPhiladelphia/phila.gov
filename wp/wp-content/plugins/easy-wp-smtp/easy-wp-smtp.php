<?php
/*
  Plugin Name: Easy WP SMTP
  Version: 1.3.6
  Plugin URI: https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197
  Author: wpecommerce, alexanderfoxc
  Author URI: https://wp-ecommerce.net/
  Description: Send email via SMTP from your WordPress Blog
  Text Domain: easy-wp-smtp
  Domain Path: /languages
 */

//Prefix/Slug - swpsmtp

include_once('easy-wp-smtp-admin-menu.php');

/**
 * Add action links on plugin page in to Plugin Name block
 * @param $links array() action links
 * @param $file  string  relative path to pugin "easy-wp-smtp/easy-wp-smtp.php"
 * @return $links array() action links
 */
if ( ! function_exists( 'swpsmtp_plugin_action_links' ) ) {

    function swpsmtp_plugin_action_links( $links, $file ) {
	/* Static so we don't call plugin_basename on every plugin row. */
	static $this_plugin;
	if ( ! $this_plugin ) {
	    $this_plugin = plugin_basename( __FILE__ );
	}
	if ( $file == $this_plugin ) {
	    $settings_link = '<a href="options-general.php?page=swpsmtp_settings">' . __( 'Settings', 'easy-wp-smtp' ) . '</a>';
	    array_unshift( $links, $settings_link );
	}
	return $links;
    }

}

/**
 * Add action links on plugin page in to Plugin Description block
 * @param $links array() action links
 * @param $file  string  relative path to pugin "easy-wp-smtp/easy-wp-smtp.php"
 * @return $links array() action links
 */
if ( ! function_exists( 'swpsmtp_register_plugin_links' ) ) {

    function swpsmtp_register_plugin_links( $links, $file ) {
	$base = plugin_basename( __FILE__ );
	if ( $file == $base ) {
	    $links[] = '<a href="options-general.php?page=swpsmtp_settings">' . __( 'Settings', 'easy-wp-smtp' ) . '</a>';
	}
	return $links;
    }

}

//plugins_loaded action hook handler
if ( ! function_exists( 'swpsmtp_plugins_loaded_handler' ) ) {

    function swpsmtp_plugins_loaded_handler() {
	load_plugin_textdomain( 'easy-wp-smtp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

}


/**
 * Function to add plugin scripts
 * @return void
 */
if ( ! function_exists( 'swpsmtp_admin_head' ) ) {

    function swpsmtp_admin_head() {
	wp_enqueue_style( 'swpsmtp_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
    }

}

function swpsmtp_clear_log() {
    if ( swpsmtp_write_to_log( "Easy WP SMTP debug log file\r\n\r\n", true ) !== false ) {
	echo '1';
    } else {
	echo 'Can\'t clear log - log file is not writeable.';
    }
    wp_die();
}

function swpsmtp_write_to_log( $str, $overwrite = false ) {
    $swpsmtp_options = get_option( 'swpsmtp_options' );
    if ( isset( $swpsmtp_options[ 'smtp_settings' ][ 'log_file_name' ] ) ) {
	$log_file_name = $swpsmtp_options[ 'smtp_settings' ][ 'log_file_name' ];
    } else {
	// let's generate log file name
	$log_file_name						 = uniqid() . '_debug_log.txt';
	$swpsmtp_options[ 'smtp_settings' ][ 'log_file_name' ]	 = $log_file_name;
	update_option( 'swpsmtp_options', $swpsmtp_options );
	file_put_contents( plugin_dir_path( __FILE__ ) . $log_file_name, "Easy WP SMTP debug log file\r\n\r\n" );
    }
    return(file_put_contents( plugin_dir_path( __FILE__ ) . $log_file_name, $str, ( ! $overwrite ? FILE_APPEND : 0 ) ));
}

function base64_decode_maybe( $str ) {
    if ( ! function_exists( 'mb_detect_encoding' ) ) {
	return base64_decode( $str );
    }
    if ( mb_detect_encoding( $str ) === mb_detect_encoding( base64_decode( base64_encode( base64_decode( $str ) ) ) ) ) {
	$str = base64_decode( $str );
    }
    return $str;
}

/**
 * Function to add smtp options in the phpmailer_init
 * @return void
 */
if ( ! function_exists( 'swpsmtp_init_smtp' ) ) {

    function swpsmtp_init_smtp( &$phpmailer ) {
	//check if SMTP credentials have been configured.
	if ( ! swpsmtp_credentials_configured() ) {
	    return;
	}
	$swpsmtp_options = get_option( 'swpsmtp_options' );
	//check if Domain Check enabled
	$domain		 = swpsmtp_is_domain_blocked();
	if ( $domain !== false ) {
	    //domain check failed
	    //let's check if we have block all emails option enabled
	    if ( isset( $swpsmtp_options[ 'block_all_emails' ] ) && $swpsmtp_options[ 'block_all_emails' ] === 1 ) {
		// it's enabled. Let's use gag mailer class that would prevent emails from being sent out.
		$phpmailer = new swpsmtp_gag_mailer();
	    } else {
		// it's disabled. Let's write some info to the log
		swpsmtp_write_to_log(
		"\r\n------------------------------------------------------------------------------------------------------\r\n" .
		"Domain check failed: website domain (" . $domain . ") is not in allowed domains list.\r\n" .
		"SMTP settings won't be used.\r\n" .
		"------------------------------------------------------------------------------------------------------\r\n\r\n" );
	    }
	    return;
	}
	/* Set the mailer type as per config above, this overrides the already called isMail method */
	$phpmailer->IsSMTP();
	if ( isset( $swpsmtp_options[ 'force_from_name_replace' ] ) && $swpsmtp_options[ 'force_from_name_replace' ] === 1 ) {
	    $from_name = $swpsmtp_options[ 'from_name_field' ];
	} else {
	    $from_name = ! empty( $phpmailer->FromName ) ? $phpmailer->FromName : $swpsmtp_options[ 'from_name_field' ];
	}
	$from_email = $swpsmtp_options[ 'from_email_field' ];
	//set ReplyTo option if needed
	//this should be set before SetFrom, otherwise might be ignored
	if ( ! empty( $swpsmtp_options[ 'reply_to_email' ] ) ) {
	    $phpmailer->AddReplyTo( $swpsmtp_options[ 'reply_to_email' ], $from_name );
	}
	// let's see if we have email ignore list populated
	if ( isset( $swpsmtp_options[ 'email_ignore_list' ] ) && ! empty( $swpsmtp_options[ 'email_ignore_list' ] ) ) {
	    $emails_arr = explode( ',', $swpsmtp_options[ 'email_ignore_list' ] );
	    if ( is_array( $emails_arr ) && ! empty( $emails_arr ) ) {
		//we have coma-separated list
	    } else {
		//it's single email
		unset( $emails_arr );
		$emails_arr = array( $swpsmtp_options[ 'email_ignore_list' ] );
	    }
	    $from		 = $phpmailer->From;
	    $match_found	 = false;
	    foreach ( $emails_arr as $email ) {
		if ( strtolower( trim( $email ) ) === strtolower( trim( $from ) ) ) {
		    $match_found = true;
		    break;
		}
	    }
	    if ( $match_found ) {
		//we should not override From and Fromname
		$from_email	 = $phpmailer->From;
		$from_name	 = $phpmailer->FromName;
	    }
	}
	$phpmailer->From	 = $from_email;
	$phpmailer->FromName	 = $from_name;
	$phpmailer->SetFrom( $phpmailer->From, $phpmailer->FromName );
	//This should set Return-Path header for servers that are not properly handling it, but needs testing first
	//$phpmailer->Sender	 = $phpmailer->From;
	/* Set the SMTPSecure value */
	if ( $swpsmtp_options[ 'smtp_settings' ][ 'type_encryption' ] !== 'none' ) {
	    $phpmailer->SMTPSecure = $swpsmtp_options[ 'smtp_settings' ][ 'type_encryption' ];
	}

	/* Set the other options */
	$phpmailer->Host = $swpsmtp_options[ 'smtp_settings' ][ 'host' ];
	$phpmailer->Port = $swpsmtp_options[ 'smtp_settings' ][ 'port' ];

	/* If we're using smtp auth, set the username & password */
	if ( 'yes' == $swpsmtp_options[ 'smtp_settings' ][ 'autentication' ] ) {
	    $phpmailer->SMTPAuth	 = true;
	    $phpmailer->Username	 = $swpsmtp_options[ 'smtp_settings' ][ 'username' ];
	    $phpmailer->Password	 = swpsmtp_get_password();
	}
//PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate.
	$phpmailer->SMTPAutoTLS = false;

	if ( isset( $swpsmtp_options[ 'smtp_settings' ][ 'insecure_ssl' ] ) && $swpsmtp_options[ 'smtp_settings' ][ 'insecure_ssl' ] !== false ) {
	    // Insecure SSL option enabled
	    $phpmailer->SMTPOptions = array(
		'ssl' => array(
		    'verify_peer'		 => false,
		    'verify_peer_name'	 => false,
		    'allow_self_signed'	 => true
		) );
	}

	if ( isset( $swpsmtp_options[ 'smtp_settings' ][ 'enable_debug' ] ) && $swpsmtp_options[ 'smtp_settings' ][ 'enable_debug' ] ) {
	    $phpmailer->Debugoutput = function($str, $level) {
		swpsmtp_write_to_log( $str );
	    };
	    $phpmailer->SMTPDebug = 1;
	}
    }

}

/**
 * Function to test mail sending
 * @return text or errors
 */
if ( ! function_exists( 'swpsmtp_test_mail' ) ) {

    function swpsmtp_test_mail( $to_email, $subject, $message ) {
	$ret = array();
	if ( ! swpsmtp_credentials_configured() ) {
	    return false;
	}

	$swpsmtp_options = get_option( 'swpsmtp_options' );

	require_once( ABSPATH . WPINC . '/class-phpmailer.php' );
	$mail = new PHPMailer( true );

	try {

	    $charset	 = get_bloginfo( 'charset' );
	    $mail->CharSet	 = $charset;

	    $from_name	 = $swpsmtp_options[ 'from_name_field' ];
	    $from_email	 = $swpsmtp_options[ 'from_email_field' ];

	    $mail->IsSMTP();

	    // send plain text test email
	    $mail->ContentType = 'text/plain';
	    $mail->IsHTML( false );

	    /* If using smtp auth, set the username & password */
	    if ( 'yes' == $swpsmtp_options[ 'smtp_settings' ][ 'autentication' ] ) {
		$mail->SMTPAuth	 = true;
		$mail->Username	 = $swpsmtp_options[ 'smtp_settings' ][ 'username' ];
		$mail->Password	 = swpsmtp_get_password();
	    }

	    /* Set the SMTPSecure value, if set to none, leave this blank */
	    if ( $swpsmtp_options[ 'smtp_settings' ][ 'type_encryption' ] !== 'none' ) {
		$mail->SMTPSecure = $swpsmtp_options[ 'smtp_settings' ][ 'type_encryption' ];
	    }

	    /* PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate. */
	    $mail->SMTPAutoTLS = false;

	    if ( isset( $swpsmtp_options[ 'smtp_settings' ][ 'insecure_ssl' ] ) && $swpsmtp_options[ 'smtp_settings' ][ 'insecure_ssl' ] !== false ) {
		// Insecure SSL option enabled
		$mail->SMTPOptions = array(
		    'ssl' => array(
			'verify_peer'		 => false,
			'verify_peer_name'	 => false,
			'allow_self_signed'	 => true
		    ) );
	    }

	    /* Set the other options */
	    $mail->Host	 = $swpsmtp_options[ 'smtp_settings' ][ 'host' ];
	    $mail->Port	 = $swpsmtp_options[ 'smtp_settings' ][ 'port' ];
	    if ( ! empty( $swpsmtp_options[ 'reply_to_email' ] ) ) {
		$mail->AddReplyTo( $swpsmtp_options[ 'reply_to_email' ], $from_name );
	    }
	    $mail->SetFrom( $from_email, $from_name );
	    //This should set Return-Path header for servers that are not properly handling it, but needs testing first
	    //$mail->Sender		 = $mail->From;
	    $mail->Subject		 = $subject;
	    $mail->Body		 = $message;
	    $mail->AddAddress( $to_email );
	    global $debugMSG;
	    $debugMSG		 = '';
	    $mail->Debugoutput	 = function($str, $level) {
		global $debugMSG;
		$debugMSG .= $str;
	    };
	    $mail->SMTPDebug = 1;

	    /* Send mail and return result */
	    $mail->Send();
	    $mail->ClearAddresses();
	    $mail->ClearAllRecipients();
	} catch ( Exception $e ) {
	    $ret[ 'error' ] = $mail->ErrorInfo;
	}

	$ret[ 'debug_log' ] = $debugMSG;

	return $ret;
    }

}

function swpsmtp_encrypt_password( $pass ) {
    if ( $pass === '' ) {
	return '';
    }

    $swpsmtp_options = get_option( 'swpsmtp_options' );

    if ( empty( $swpsmtp_options[ 'smtp_settings' ][ 'encrypt_pass' ] ) || ! extension_loaded( 'openssl' ) ) {
	// no openssl extension loaded - we can't encrypt the password
	$password = base64_encode( $pass );
	update_option( 'swpsmtp_pass_encrypted', false );
    } else {
	// let's encrypt password
	require_once('easy-wp-smtp-utils.php');
	$cryptor	 = SWPSMTPUtils::get_instance();
	$password	 = $cryptor->encrypt_password( $pass );
	update_option( 'swpsmtp_pass_encrypted', true );
    }
    return $password;
}

if ( ! function_exists( 'swpsmtp_get_password' ) ) {

    function swpsmtp_get_password() {
	$swpsmtp_options = get_option( 'swpsmtp_options' );

	$temp_password = isset( $swpsmtp_options[ 'smtp_settings' ][ 'password' ] ) ? $swpsmtp_options[ 'smtp_settings' ][ 'password' ] : '';
	if ( $temp_password == '' ) {
	    return '';
	}

	if ( get_option( 'swpsmtp_pass_encrypted' ) ) {
	    //this is encrypted password
	    require_once('easy-wp-smtp-utils.php');
	    $cryptor	 = SWPSMTPUtils::get_instance();
	    $decrypted	 = $cryptor->decrypt_password( $temp_password );
	    //check if encryption option is disabled
	    if ( empty( $swpsmtp_options[ 'smtp_settings' ][ 'encrypt_pass' ] ) ) {
		//it is. let's save decrypted password
		$swpsmtp_options[ 'smtp_settings' ][ 'password' ] = swpsmtp_encrypt_password( addslashes( $decrypted ) );
		update_option( 'swpsmtp_options', $swpsmtp_options );
	    }
	    return $decrypted;
	}

	$password	 = "";
	$decoded_pass	 = base64_decode( $temp_password );
	/* no additional checks for servers that aren't configured with mbstring enabled */
	if ( ! function_exists( 'mb_detect_encoding' ) ) {
	    return $decoded_pass;
	}
	/* end of mbstring check */
	if ( base64_encode( $decoded_pass ) === $temp_password ) {  //it might be encoded
	    if ( false === mb_detect_encoding( $decoded_pass ) ) {  //could not find character encoding.
		$password = $temp_password;
	    } else {
		$password = base64_decode( $temp_password );
	    }
	} else { //not encoded
	    $password = $temp_password;
	}
	return stripslashes( $password );
    }

}

if ( ! function_exists( 'swpsmtp_admin_notice' ) ) {

    function swpsmtp_admin_notice() {
	if ( ! swpsmtp_credentials_configured() ) {
	    $settings_url = admin_url() . 'options-general.php?page=swpsmtp_settings';
	    ?>
	    <div class="error">
	        <p><?php printf( __( 'Please configure your SMTP credentials in the <a href="%s">settings menu</a> in order to send email using Easy WP SMTP plugin.', 'easy-wp-smtp' ), esc_url( $settings_url ) ); ?></p>
	    </div>
	    <?php
	}
    }

}

if ( ! function_exists( 'swpsmtp_credentials_configured' ) ) {

    function swpsmtp_credentials_configured() {
	$swpsmtp_options	 = get_option( 'swpsmtp_options' );
	$credentials_configured	 = true;
	if ( ! isset( $swpsmtp_options[ 'from_email_field' ] ) || empty( $swpsmtp_options[ 'from_email_field' ] ) ) {
	    $credentials_configured = false;
	}
	if ( ! isset( $swpsmtp_options[ 'from_name_field' ] ) || empty( $swpsmtp_options[ 'from_name_field' ] ) ) {
	    $credentials_configured = false;
	}
	return $credentials_configured;
    }

}

/**
 * Performed at uninstall.
 * @return void
 */
if ( ! function_exists( 'swpsmtp_send_uninstall' ) ) {

    function swpsmtp_send_uninstall() {
	/* Don't delete plugin options. It is better to retain the options so if someone accidentally deactivates, the configuration is not lost. */

//delete_site_option('swpsmtp_options');
//delete_option('swpsmtp_options');
    }

}

function swpsmtp_activate() {
    $swpsmtp_options_default = array(
	'from_email_field'		 => '',
	'from_name_field'		 => '',
	'force_from_name_replace'	 => 0,
	'smtp_settings'			 => array(
	    'host'			 => 'smtp.example.com',
	    'type_encryption'	 => 'none',
	    'port'			 => 25,
	    'autentication'		 => 'yes',
	    'username'		 => '',
	    'password'		 => ''
	)
    );

    /* install the default plugin options if needed */
    $swpsmtp_options = get_option( 'swpsmtp_options' );
    if ( ! $swpsmtp_options ) {
	$swpsmtp_options = $swpsmtp_options_default;
    }
    $swpsmtp_options = array_merge( $swpsmtp_options_default, $swpsmtp_options );
    update_option( 'swpsmtp_options', $swpsmtp_options, 'yes' );
    //add current domain to allowed domains list
    if ( ! isset( $swpsmtp_options[ 'allowed_domains' ] ) ) {
	$domain = parse_url( get_site_url(), PHP_URL_HOST );
	if ( $domain ) {
	    $swpsmtp_options[ 'allowed_domains' ] = base64_encode( $domain );
	    update_option( 'swpsmtp_options', $swpsmtp_options );
	}
    } else { // let's check if existing value should be base64 encoded
	if ( ! empty( $swpsmtp_options[ 'allowed_domains' ] ) ) {
	    if ( base64_decode_maybe( $swpsmtp_options[ 'allowed_domains' ] ) === $swpsmtp_options[ 'allowed_domains' ] ) {
		$swpsmtp_options[ 'allowed_domains' ] = base64_encode( $swpsmtp_options[ 'allowed_domains' ] );
		update_option( 'swpsmtp_options', $swpsmtp_options );
	    }
	}
    }
    // Encrypt password if needed
    if ( ! get_option( 'swpsmtp_pass_encrypted' ) ) {
	if ( extension_loaded( 'openssl' ) ) {
	    if ( $swpsmtp_options[ 'smtp_settings' ][ 'password' ] !== '' ) {
		$swpsmtp_options[ 'smtp_settings' ][ 'password' ] = swpsmtp_encrypt_password( swpsmtp_get_password() );
		update_option( 'swpsmtp_options', $swpsmtp_options );
	    }
	}
    }
}

function swpsmtp_is_domain_blocked() {
    $swpsmtp_options = get_option( 'swpsmtp_options' );
    //check if Domain Check enabled
    if ( isset( $swpsmtp_options[ 'enable_domain_check' ] ) && $swpsmtp_options[ 'enable_domain_check' ] ) {
	//check if allowed domains list is not blank
	if ( isset( $swpsmtp_options[ 'allowed_domains' ] ) && ! empty( $swpsmtp_options[ 'allowed_domains' ] ) ) {
	    $swpsmtp_options[ 'allowed_domains' ]	 = base64_decode_maybe( $swpsmtp_options[ 'allowed_domains' ] );
	    //let's see if we have one domain or coma-separated domains
	    $domains_arr				 = explode( ',', $swpsmtp_options[ 'allowed_domains' ] );
	    if ( is_array( $domains_arr ) && ! empty( $domains_arr ) ) {
		//we have coma-separated list
	    } else {
		//it's single domain
		unset( $domains_arr );
		$domains_arr = array( $swpsmtp_options[ 'allowed_domains' ] );
	    }
	    $site_domain	 = parse_url( get_site_url(), PHP_URL_HOST );
	    $match_found	 = false;
	    foreach ( $domains_arr as $domain ) {
		if ( strtolower( trim( $domain ) ) === strtolower( trim( $site_domain ) ) ) {
		    $match_found = true;
		    break;
		}
	    }
	    if ( ! $match_found ) {
		return $site_domain;
	    }
	}
    }
    return false;
}

function swpsmtp_wp_mail( $args ) {
    $swpsmtp_options = get_option( 'swpsmtp_options' );
    $domain		 = swpsmtp_is_domain_blocked();
    if ( $domain !== false && (isset( $swpsmtp_options[ 'block_all_emails' ] ) && $swpsmtp_options[ 'block_all_emails' ] === 1) ) {
	swpsmtp_write_to_log(
	"\r\n------------------------------------------------------------------------------------------------------\r\n" .
	"Domain check failed: website domain (" . $domain . ") is not in allowed domains list.\r\n" .
	"Following email not sent (block all emails option is enabled):\r\n" .
	"To: " . $args[ 'to' ] . "; Subject: " . $args[ 'subject' ] . "\r\n" .
	"------------------------------------------------------------------------------------------------------\r\n\r\n" );
    }
    return $args;
}

class swpsmtp_gag_mailer extends stdClass {

    public function Send() {
	return true;
    }

}

/**
 * Add all hooks
 */
add_filter( 'plugin_action_links', 'swpsmtp_plugin_action_links', 10, 2 );
add_action( 'plugins_loaded', 'swpsmtp_plugins_loaded_handler' );
add_filter( 'plugin_row_meta', 'swpsmtp_register_plugin_links', 10, 2 );
add_filter( 'wp_mail', 'swpsmtp_wp_mail', 2147483647 );

add_action( 'phpmailer_init', 'swpsmtp_init_smtp', 999 );

add_action( 'admin_menu', 'swpsmtp_admin_default_setup' );

add_action( 'admin_init', 'swpsmtp_admin_init' );
add_action( 'admin_enqueue_scripts', 'swpsmtp_admin_head' );
add_action( 'admin_notices', 'swpsmtp_admin_notice' );

register_activation_hook( __FILE__, 'swpsmtp_activate' );
register_uninstall_hook( plugin_basename( __FILE__ ), 'swpsmtp_send_uninstall' );
