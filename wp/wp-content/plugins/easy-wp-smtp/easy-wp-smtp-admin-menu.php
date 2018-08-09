<?php

/**
 * Add menu and submenu.
 * @return void
 */
function swpsmtp_admin_default_setup() {
    //add_submenu_page( 'options-general.php', __( 'Easy WP SMTP', 'easy-wp-smtp' ), __( 'Easy WP SMTP', 'easy-wp-smtp' ), $capabilities, 'swpsmtp_settings', 'swpsmtp_settings' );
    add_options_page( __( 'Easy WP SMTP', 'easy-wp-smtp' ), __( 'Easy WP SMTP', 'easy-wp-smtp' ), 'manage_options', 'swpsmtp_settings', 'swpsmtp_settings' );
}

/**
 * Sanitizes textarea. Tries to use wp sanitize_textarea_field() function. If that's not available, uses its own methods
 * @return string
 */
function swpsmtp_sanitize_textarea( $str ) {
    if ( function_exists( 'sanitize_textarea_field' ) ) {
	return sanitize_textarea_field( $str );
    }
    $filtered = wp_check_invalid_utf8( $str );

    if ( strpos( $filtered, '<' ) !== false ) {
	$filtered	 = wp_pre_kses_less_than( $filtered );
	// This will strip extra whitespace for us.
	$filtered	 = wp_strip_all_tags( $filtered, false );

	// Use html entities in a special case to make sure no later
	// newline stripping stage could lead to a functional tag
	$filtered = str_replace( "<\n", "&lt;\n", $filtered );
    }

    $filtered = trim( $filtered );

    $found = false;
    while ( preg_match( '/%[a-f0-9]{2}/i', $filtered, $match ) ) {
	$filtered	 = str_replace( $match[ 0 ], '', $filtered );
	$found		 = true;
    }

    if ( $found ) {
	// Strip out the whitespace that may now exist after removing the octets.
	$filtered = trim( preg_replace( '/ +/', ' ', $filtered ) );
    }

    return $filtered;
}

/**
 * Renders the admin settings menu of the plugin.
 * @return void
 */
function swpsmtp_settings() {
    //check if OpenSSL PHP extension is loaded and display warning if it's not
    if ( ! extension_loaded( 'openssl' ) ) {
	$class	 = 'notice notice-warning';
	$message = __( "PHP OpenSSL extension is not installed on the server. It's required by Easy WP SMTP plugin to operate properly. Please contact your server administrator or hosting provider and ask them to install it.", 'easy-wp-smtp' );
	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }
    echo '<div class="wrap" id="swpsmtp-mail">';
    echo '<h2>' . __( "Easy WP SMTP Settings", 'easy-wp-smtp' ) . '</h2>';
    echo '<div id="poststuff"><div id="post-body">';

    $display_add_options	 = $message		 = $error			 = $result			 = '';

    $swpsmtp_options = get_option( 'swpsmtp_options' );
    $smtp_test_mail	 = get_option( 'smtp_test_mail' );
    $gag_password	 = '#easywpsmtpgagpass#';
    if ( empty( $smtp_test_mail ) ) {
	$smtp_test_mail = array( 'swpsmtp_to' => '', 'swpsmtp_subject' => '', 'swpsmtp_message' => '', );
    }

    if ( isset( $_POST[ 'swpsmtp_form_submit' ] ) ) {
	// check nounce
	if ( ! check_admin_referer( plugin_basename( __FILE__ ), 'swpsmtp_nonce_name' ) ) {
	    $error .= " " . __( "Nonce check failed.", 'easy-wp-smtp' );
	}
	/* Update settings */
	$swpsmtp_options[ 'from_name_field' ]		 = isset( $_POST[ 'swpsmtp_from_name' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'swpsmtp_from_name' ] ) ) : '';
	$swpsmtp_options[ 'force_from_name_replace' ]	 = isset( $_POST[ 'swpsmtp_force_from_name_replace' ] ) ? 1 : false;

	if ( isset( $_POST[ 'swpsmtp_from_email' ] ) ) {
	    if ( is_email( $_POST[ 'swpsmtp_from_email' ] ) ) {
		$swpsmtp_options[ 'from_email_field' ] = sanitize_email( $_POST[ 'swpsmtp_from_email' ] );
	    } else {
		$error .= " " . __( "Please enter a valid email address in the 'FROM' field.", 'easy-wp-smtp' );
	    }
	}
	if ( isset( $_POST[ 'swpsmtp_reply_to_email' ] ) ) {
	    $swpsmtp_options[ 'reply_to_email' ] = sanitize_email( $_POST[ 'swpsmtp_reply_to_email' ] );
	}

	if ( isset( $_POST[ 'swpsmtp_email_ignore_list' ] ) ) {
	    $swpsmtp_options[ 'email_ignore_list' ] = sanitize_text_field( $_POST[ 'swpsmtp_email_ignore_list' ] );
	}

	$swpsmtp_options[ 'smtp_settings' ][ 'host' ]		 = stripslashes( $_POST[ 'swpsmtp_smtp_host' ] );
	$swpsmtp_options[ 'smtp_settings' ][ 'type_encryption' ] = ( isset( $_POST[ 'swpsmtp_smtp_type_encryption' ] ) ) ? sanitize_text_field( $_POST[ 'swpsmtp_smtp_type_encryption' ] ) : 'none';
	$swpsmtp_options[ 'smtp_settings' ][ 'autentication' ]	 = ( isset( $_POST[ 'swpsmtp_smtp_autentication' ] ) ) ? sanitize_text_field( $_POST[ 'swpsmtp_smtp_autentication' ] ) : 'yes';
	$swpsmtp_options[ 'smtp_settings' ][ 'username' ]	 = stripslashes( $_POST[ 'swpsmtp_smtp_username' ] );

	$swpsmtp_options[ 'smtp_settings' ][ 'enable_debug' ]	 = isset( $_POST[ 'swpsmtp_enable_debug' ] ) ? 1 : false;
	$swpsmtp_options[ 'smtp_settings' ][ 'insecure_ssl' ]	 = isset( $_POST[ 'swpsmtp_insecure_ssl' ] ) ? 1 : false;
	$swpsmtp_options[ 'smtp_settings' ][ 'encrypt_pass' ]	 = isset( $_POST[ 'swpsmtp_encrypt_pass' ] ) ? 1 : false;

	$smtp_password = $_POST[ 'swpsmtp_smtp_password' ];
	if ( $smtp_password !== $gag_password ) {
	    $swpsmtp_options[ 'smtp_settings' ][ 'password' ] = swpsmtp_encrypt_password( $smtp_password );
	}

	if ( $swpsmtp_options[ 'smtp_settings' ][ 'encrypt_pass' ] && ! get_option( 'swpsmtp_pass_encrypted', false ) ) {
	    update_option( 'swpsmtp_options', $swpsmtp_options );
	    $pass							 = swpsmtp_get_password();
	    $swpsmtp_options[ 'smtp_settings' ][ 'password' ]	 = swpsmtp_encrypt_password( $pass );
	    update_option( 'swpsmtp_options', $swpsmtp_options );
	}


	$swpsmtp_options[ 'enable_domain_check' ] = isset( $_POST[ 'swpsmtp_enable_domain_check' ] ) ? 1 : false;
	if ( isset( $_POST[ 'swpsmtp_allowed_domains' ] ) ) {
	    $swpsmtp_options[ 'block_all_emails' ]	 = isset( $_POST[ 'swpsmtp_block_all_emails' ] ) ? 1 : false;
	    $swpsmtp_options[ 'allowed_domains' ]	 = base64_encode( sanitize_text_field( $_POST[ 'swpsmtp_allowed_domains' ] ) );
	} else if ( ! isset( $swpsmtp_options[ 'allowed_domains' ] ) ) {
	    $swpsmtp_options[ 'allowed_domains' ] = '';
	}

	/* Check value from "SMTP port" option */
	if ( isset( $_POST[ 'swpsmtp_smtp_port' ] ) ) {
	    if ( empty( $_POST[ 'swpsmtp_smtp_port' ] ) || 1 > intval( $_POST[ 'swpsmtp_smtp_port' ] ) || ( ! preg_match( '/^\d+$/', $_POST[ 'swpsmtp_smtp_port' ] ) ) ) {
		$swpsmtp_options[ 'smtp_settings' ][ 'port' ]	 = '25';
		$error						 .= " " . __( "Please enter a valid port in the 'SMTP Port' field.", 'easy-wp-smtp' );
	    } else {
		$swpsmtp_options[ 'smtp_settings' ][ 'port' ] = sanitize_text_field( $_POST[ 'swpsmtp_smtp_port' ] );
	    }
	}

	/* Update settings in the database */
	if ( empty( $error ) ) {
	    update_option( 'swpsmtp_options', $swpsmtp_options );
	    $message .= __( "Settings saved.", 'easy-wp-smtp' );
	} else {
	    $error .= " " . __( "Settings are not saved.", 'easy-wp-smtp' );
	}
    }

    /* Send test letter */
    $swpsmtp_to = '';
    if ( isset( $_POST[ 'swpsmtp_test_submit' ] ) && check_admin_referer( plugin_basename( __FILE__ ), 'swpsmtp_test_nonce_name' ) ) {
	if ( isset( $_POST[ 'swpsmtp_to' ] ) ) {
	    $to_email = sanitize_text_field( $_POST[ 'swpsmtp_to' ] );
	    if ( is_email( $to_email ) ) {
		$swpsmtp_to = $to_email;
	    } else {
		$error .= __( "Please enter a valid email address in the recipient email field.", 'easy-wp-smtp' );
	    }
	}
	$swpsmtp_subject = isset( $_POST[ 'swpsmtp_subject' ] ) ? sanitize_text_field( $_POST[ 'swpsmtp_subject' ] ) : '';
	$swpsmtp_message = isset( $_POST[ 'swpsmtp_message' ] ) ? swpsmtp_sanitize_textarea( $_POST[ 'swpsmtp_message' ] ) : '';

	//Save the test mail details so it doesn't need to be filled in everytime.
	$smtp_test_mail[ 'swpsmtp_to' ]		 = $swpsmtp_to;
	$smtp_test_mail[ 'swpsmtp_subject' ]	 = $swpsmtp_subject;
	$smtp_test_mail[ 'swpsmtp_message' ]	 = $swpsmtp_message;
	update_option( 'smtp_test_mail', $smtp_test_mail );

	if ( ! empty( $swpsmtp_to ) ) {
	    $test_res = swpsmtp_test_mail( $swpsmtp_to, $swpsmtp_subject, $swpsmtp_message );
	}
    }

    //check if server meets encryption requirements
    $enc_req_met	 = true;
    $enc_req_err	 = '';
    if ( ! extension_loaded( 'openssl' ) ) {
	$enc_req_err	 .= __( "PHP OpenSSL extension is not installed on the server. It is required for encryption to work properly. Please contact your server administrator or hosting provider and ask them to install it.", 'easy-wp-smtp' ) . '<br />';
	$enc_req_met	 = false;
    }
    if ( version_compare( PHP_VERSION, '5.3.0' ) < 0 ) {
	$enc_req_err	 = ! empty( $enc_req_err ) ? $enc_req_err	 .= '<br />' : '';
	$enc_req_err	 .= sprintf( __( 'Your PHP version is %s, encryption function requires PHP version 5.3.0 or higher.', 'easy-wp-smtp' ), PHP_VERSION );
	$enc_req_met	 = false;
    }
    ?>
    <style>
        div.swpsmtp-tab-container, #swpsmtp-save-settings-notice {
    	display: none;
        }
        .swpsmtp-stars-container {
    	text-align: center;
    	margin-top: 10px;
        }
        .swpsmtp-stars-container span {
    	vertical-align: text-top;
    	color: #ffb900;
        }
        .swpsmtp-stars-container a {
    	text-decoration: none;
        }
        .swpsmtp-settings-grid {
    	display:inline-block;
        }
        .swpsmtp-settings-main-cont {
    	width: 80%;
        }
        .swpsmtp-settings-sidebar-cont {
    	width: 19%;
    	float: right;
        }

        div.swpsmtp-msg-cont {
    	clear: both;
    	margin-bottom: 10px;
    	padding: 5px 10px;
    	border-radius: 2px;
    	background-color: #ffffe0;
        }
        div.swpsmtp-msg-cont.msg-error {
    	border-left: 5px solid red;
        }
        div.swpsmtp-msg-cont.msg-success {
    	border-left: 5px solid green;
        }

        #swpsmtp-debug-log-cont {
    	display: none;
        }

        @media (max-width: 782px) {
    	.swpsmtp-settings-grid {
    	    display: block;
    	    float: none;
    	    width: 100%;
    	}
        }
    </style>
    <div class="updated fade" <?php if ( empty( $message ) ) echo "style=\"display:none\""; ?>>
        <p><strong><?php echo $message; ?></strong></p>
    </div>
    <div class="error" <?php if ( empty( $error ) ) echo "style=\"display:none\""; ?>>
        <p><strong><?php echo $error; ?></strong></p>
    </div>

    <div class="nav-tab-wrapper">
        <a href="#smtp" data-tab-name="smtp" class="nav-tab"><?php _e( 'SMTP Settings', 'easy-wp-smtp' ); ?></a>
        <a href="#additional" data-tab-name="additional" class="nav-tab"><?php _e( 'Additional Settings', 'easy-wp-smtp' ); ?></a>
        <a href="#testemail" data-tab-name="testemail" class="nav-tab"><?php _e( 'Test Email', 'easy-wp-smtp' ); ?></a>
    </div>

    <div class="swpsmtp-yellow-box">
	<?php _ex( sprintf( "Please visit the %s plugin's documentation page to learn how to use this plugin.", '<a target="_blank" href="https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197">Easy WP SMTP</a>' ), '%s is replaced by <a target="_blank" href="https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197">Easy WP SMTP</a>', 'easy-wp-smtp' ); ?>
    </div>
    <div class="swpsmtp-settings-container">
        <div class="swpsmtp-settings-grid swpsmtp-settings-main-cont">

    	<form autocomplete="off" id="swpsmtp_settings_form" method="post" action="">

    	    <input type="hidden" id="swpsmtp-urlHash" name="swpsmtp-urlHash" value="">

    	    <div class="swpsmtp-tab-container" data-tab-name="smtp">
    		<div class="postbox">
    		    <h3 class="hndle"><label for="title"><?php _e( 'SMTP Configuration Settings', 'easy-wp-smtp' ); ?></label></h3>
    		    <div class="inside">

    			<p><?php _e( 'You can request your hosting provider for the SMTP details of your site. Use the SMTP details provided by your hosting provider to configure the following settings.', 'easy-wp-smtp' ) ?></p>

    			<table class="form-table">
    			    <tr valign="top">
    				<th scope="row"><?php _e( "From Email Address", 'easy-wp-smtp' ); ?></th>
    				<td>
    				    <input id="swpsmtp_from_email" type="text" name="swpsmtp_from_email" value="<?php echo isset( $swpsmtp_options[ 'from_email_field' ] ) ? esc_attr( $swpsmtp_options[ 'from_email_field' ] ) : ''; ?>"/><br />
    				    <p class="description"><?php _e( "This email address will be used in the 'From' field.", 'easy-wp-smtp' ); ?></p>
    				</td>
    			    </tr>
    			    <tr valign="top">
    				<th scope="row"><?php _e( "From Name", 'easy-wp-smtp' ); ?></th>
    				<td>
    				    <input id="swpsmtp_from_name" type="text" name="swpsmtp_from_name" value="<?php echo isset( $swpsmtp_options[ 'from_name_field' ] ) ? esc_attr( $swpsmtp_options[ 'from_name_field' ] ) : ''; ?>"/><br />
    				    <p class="description"><?php _e( "This text will be used in the 'FROM' field", 'easy-wp-smtp' ); ?></p>
    				    <p>
    					<label><input type="checkbox" id="swpsmtp_force_from_name_replace" name="swpsmtp_force_from_name_replace" value="1"<?php echo (isset( $swpsmtp_options[ 'force_from_name_replace' ] ) && ($swpsmtp_options[ 'force_from_name_replace' ])) ? ' checked' : ''; ?>/> <?php _e( "Force From Name Replacement", 'easy-wp-smtp' ); ?></label>
    				    </p>
    				    <p class="description"><?php _e( "When enabled, the plugin will set the above From Name for each email. Disable it if you're using contact form plugins, it will prevent the plugin from replacing form submitter's name when contact email is sent.", 'easy-wp-smtp' ); ?>
    					<br />
					    <?php _e( "If email's From Name is empty, the plugin will set the above value regardless.", 'easy-wp-smtp' ); ?>
    				    </p>
    				</td>
    			    </tr>
    			    <tr valign="top">
    				<th scope="row"><?php _e( "Reply-To Email Address", 'easy-wp-smtp' ); ?></th>
    				<td>
    				    <input id="swpsmtp_reply_to_email" type="email" name="swpsmtp_reply_to_email" value="<?php echo isset( $swpsmtp_options[ 'reply_to_email' ] ) ? esc_attr( $swpsmtp_options[ 'reply_to_email' ] ) : ''; ?>"/><br />
    				    <p class="description"><?php _e( "Optional. This email address will be used in the 'Reply-To' field of the email. Leave it blank to use 'From' email as the reply-to value.", 'easy-wp-smtp' ); ?></p>
    				</td>
    			    </tr>
    			    <tr class="ad_opt swpsmtp_smtp_options">
    				<th><?php _e( 'SMTP Host', 'easy-wp-smtp' ); ?></th>
    				<td>
    				    <input id='swpsmtp_smtp_host' type='text' name='swpsmtp_smtp_host' value='<?php echo isset( $swpsmtp_options[ 'smtp_settings' ][ 'host' ] ) ? esc_attr( $swpsmtp_options[ 'smtp_settings' ][ 'host' ] ) : ''; ?>' /><br />
    				    <p class="description"><?php _e( "Your mail server", 'easy-wp-smtp' ); ?></p>
    				</td>
    			    </tr>
    			    <tr class="ad_opt swpsmtp_smtp_options">
    				<th><?php _e( 'Type of Encryption', 'easy-wp-smtp' ); ?></th>
    				<td>
    				    <label for="swpsmtp_smtp_type_encryption_1"><input type="radio" id="swpsmtp_smtp_type_encryption_1" name="swpsmtp_smtp_type_encryption" value='none' <?php if ( isset( $swpsmtp_options[ 'smtp_settings' ][ 'type_encryption' ] ) && 'none' == $swpsmtp_options[ 'smtp_settings' ][ 'type_encryption' ] ) echo 'checked="checked"'; ?> /> <?php _e( 'None', 'easy-wp-smtp' ); ?></label>
    				    <label for="swpsmtp_smtp_type_encryption_2"><input type="radio" id="swpsmtp_smtp_type_encryption_2" name="swpsmtp_smtp_type_encryption" value='ssl' <?php if ( isset( $swpsmtp_options[ 'smtp_settings' ][ 'type_encryption' ] ) && 'ssl' == $swpsmtp_options[ 'smtp_settings' ][ 'type_encryption' ] ) echo 'checked="checked"'; ?> /> <?php _e( 'SSL/TLS', 'easy-wp-smtp' ); ?></label>
    				    <label for="swpsmtp_smtp_type_encryption_3"><input type="radio" id="swpsmtp_smtp_type_encryption_3" name="swpsmtp_smtp_type_encryption" value='tls' <?php if ( isset( $swpsmtp_options[ 'smtp_settings' ][ 'type_encryption' ] ) && 'tls' == $swpsmtp_options[ 'smtp_settings' ][ 'type_encryption' ] ) echo 'checked="checked"'; ?> /> <?php _e( 'STARTTLS', 'easy-wp-smtp' ); ?></label><br />
    				    <p class="description"><?php _e( "For most servers SSL/TLS is the recommended option", 'easy-wp-smtp' ); ?></p>
    				</td>
    			    </tr>
    			    <tr class="ad_opt swpsmtp_smtp_options">
    				<th><?php _e( 'SMTP Port', 'easy-wp-smtp' ); ?></th>
    				<td>
    				    <input id='swpsmtp_smtp_port' type='text' name='swpsmtp_smtp_port' value='<?php echo isset( $swpsmtp_options[ 'smtp_settings' ][ 'port' ] ) ? esc_attr( $swpsmtp_options[ 'smtp_settings' ][ 'port' ] ) : ''; ?>' /><br />
    				    <p class="description"><?php _e( "The port to your mail server", 'easy-wp-smtp' ); ?></p>
    				</td>
    			    </tr>
    			    <tr class="ad_opt swpsmtp_smtp_options">
    				<th><?php _e( 'SMTP Authentication', 'easy-wp-smtp' ); ?></th>
    				<td>
    				    <label for="swpsmtp_smtp_autentication"><input type="radio" id="swpsmtp_smtp_autentication_1" name="swpsmtp_smtp_autentication" value='no' <?php if ( isset( $swpsmtp_options[ 'smtp_settings' ][ 'autentication' ] ) && 'no' == $swpsmtp_options[ 'smtp_settings' ][ 'autentication' ] ) echo 'checked="checked"'; ?> /> <?php _e( 'No', 'easy-wp-smtp' ); ?></label>
    				    <label for="swpsmtp_smtp_autentication"><input type="radio" id="swpsmtp_smtp_autentication_2" name="swpsmtp_smtp_autentication" value='yes' <?php if ( isset( $swpsmtp_options[ 'smtp_settings' ][ 'autentication' ] ) && 'yes' == $swpsmtp_options[ 'smtp_settings' ][ 'autentication' ] ) echo 'checked="checked"'; ?> /> <?php _e( 'Yes', 'easy-wp-smtp' ); ?></label><br />
    				    <p class="description"><?php _e( "This options should always be checked 'Yes'", 'easy-wp-smtp' ); ?></p>
    				</td>
    			    </tr>
    			    <tr class="ad_opt swpsmtp_smtp_options">
    				<th><?php _e( 'SMTP Username', 'easy-wp-smtp' ); ?></th>
    				<td>
    				    <input id='swpsmtp_smtp_username' type='text' name='swpsmtp_smtp_username' value='<?php echo isset( $swpsmtp_options[ 'smtp_settings' ][ 'username' ] ) ? esc_attr( $swpsmtp_options[ 'smtp_settings' ][ 'username' ] ) : ''; ?>'/><br />
    				    <p class="description"><?php _e( "The username to login to your mail server", 'easy-wp-smtp' ); ?></p>
    				</td>
    			    </tr>
    			    <tr class="ad_opt swpsmtp_smtp_options">
    				<th><?php _e( 'SMTP Password', 'easy-wp-smtp' ); ?></th>
    				<td>
    				    <input id='swpsmtp_smtp_password' type='password' name='swpsmtp_smtp_password' value='<?php echo (swpsmtp_get_password() !== '' ? $gag_password : ''); ?>' autocomplete='new-password' /><br />
    				    <p class="description"><?php _e( "The password to login to your mail server", 'easy-wp-smtp' ); ?></p>
    				    <p class="description"><b><?php _e( 'Note', 'easy-wp-smtp' ); ?></b>: <?php _e( 'when you click "Save Changes", your actual password is stored in the database and then used to send emails. This field is replaced with a gag (#easywpsmtpgagpass#). This is done to prevent someone with the access to Settings page from seeing your password (using password fields unmasking programs, for example).', 'easy-wp-smtp' ); ?></p>
    				</td>
    			    </tr>
    			</table>
    			<p class="submit">
    			    <input type="submit" id="settings-form-submit" class="button-primary" value="<?php _e( 'Save Changes', 'easy-wp-smtp' ) ?>" />
    			    <input type="hidden" name="swpsmtp_form_submit" value="submit" />
				<?php wp_nonce_field( plugin_basename( __FILE__ ), 'swpsmtp_nonce_name' ); ?>
    			</p>
    		    </div><!-- end of inside -->
    		</div><!-- end of postbox -->
    	    </div>

    	    <div class="swpsmtp-tab-container" data-tab-name="additional">
    		<div class="postbox">
    		    <h3 class="hndle"><label for="title"><?php _e( 'Additional Settings (Optional)', 'easy-wp-smtp' ); ?></label></h3>
    		    <div class="inside">
    			<table class="form-table">
    			    <tr valign="top">
    				<th scope="row"><?php _e( "Don't Replace \"From\" Field", 'easy-wp-smtp' ); ?></th>
    				<td>
    				    <input id="swpsmtp_email_ignore_list" type="text" name="swpsmtp_email_ignore_list" value="<?php echo isset( $swpsmtp_options[ 'email_ignore_list' ] ) ? esc_attr( $swpsmtp_options[ 'email_ignore_list' ] ) : ''; ?>"/><br />
    				    <p class="description"><?php _e( "Comma separated emails list. Example value: email1@domain.com, email2@domain.com", "easy-wp-smtp" ); ?></p>
    				    <p class="description"><?php _e( "This option is useful when you are using several email aliases on your SMTP server. If you don't want your aliases to be replaced by the address specified in \"From\" field, enter them in this field.", 'easy-wp-smtp' ); ?></p>
    				</td>
    			    </tr>
    			    <tr valign="top">
    				<th scope="row"><?php _e( "Enable Domain Check", 'easy-wp-smtp' ); ?></th>
    				<td>
    				    <input id="swpsmtp_enable_domain_check" type="checkbox" id="swpsmtp_enable_domain_check" name="swpsmtp_enable_domain_check" value="1"<?php echo (isset( $swpsmtp_options[ 'enable_domain_check' ] ) && ($swpsmtp_options[ 'enable_domain_check' ])) ? ' checked' : ''; ?>/>
    				    <p class="description"><?php _e( "This option is usually used by developers only. SMTP settings will be used only if the site is running on following domain(s):", 'easy-wp-smtp' ); ?></p>
    				    <input id="swpsmtp_allowed_domains" type="text" name="swpsmtp_allowed_domains" value="<?php echo base64_decode_maybe( $swpsmtp_options[ 'allowed_domains' ] ); ?>"<?php echo (isset( $swpsmtp_options[ 'enable_domain_check' ] ) && ($swpsmtp_options[ 'enable_domain_check' ])) ? '' : ' disabled'; ?>/>
    				    <p class="description"><?php _e( "Coma-separated domains list. Example: domain1.com, domain2.com", 'easy-wp-smtp' ); ?></p>
    				    <p>
    					<label><input type="checkbox" id="swpsmtp_block_all_emails" name="swpsmtp_block_all_emails" value="1"<?php echo (isset( $swpsmtp_options[ 'block_all_emails' ] ) && ($swpsmtp_options[ 'block_all_emails' ])) ? ' checked' : ''; ?><?php echo (isset( $swpsmtp_options[ 'enable_domain_check' ] ) && ($swpsmtp_options[ 'enable_domain_check' ])) ? '' : ' disabled'; ?>/> <?php _e( 'Block all emails', 'easy-wp-smtp' ); ?></label>
    				    </p>
    				    <p class="description"><?php _e( "When enabled, plugin attempts to block ALL emails from being sent out if domain mismtach." ); ?></p>
    				</td>
    			    </tr>
    			    <tr valign="top">
    				<th scope="row"><?php _e( "Encrypt Password", 'easy-wp-smtp' ); ?></th>
    				<td>
					<?php if ( $enc_req_met ) { ?>
					    <input id="swpsmtp_encrypt_pass" type="checkbox" name="swpsmtp_encrypt_pass" value="1" <?php echo (isset( $swpsmtp_options[ 'smtp_settings' ][ 'encrypt_pass' ] ) && ($swpsmtp_options[ 'smtp_settings' ][ 'encrypt_pass' ])) ? 'checked' : ''; ?>/>
					    <p class="description"><?php _e( "When enabled, your SMTP password is stored in the database using AES-256 encryption.", 'easy-wp-smtp' ); ?></p>
					<?php } else { ?>
					    <p style="color: red;"><?php echo $enc_req_err; ?></p>
					<?php } ?>
    				</td>
    			    </tr>
    			    <tr valign="top">
    				<th scope="row"><?php _e( "Allow Insecure SSL Certificates", 'easy-wp-smtp' ); ?></th>
    				<td>
    				    <input id="swpsmtp_insecure_ssl" type="checkbox" name="swpsmtp_insecure_ssl" value="1" <?php echo (isset( $swpsmtp_options[ 'smtp_settings' ][ 'insecure_ssl' ] ) && ($swpsmtp_options[ 'smtp_settings' ][ 'insecure_ssl' ])) ? 'checked' : ''; ?>/>
    				    <p class="description"><?php _e( "Allows insecure and self-signed SSL certificates on SMTP server. It's highly recommended to keep this option disabled.", 'easy-wp-smtp' ); ?></p>
    				</td>
    			    </tr>
    			    <tr valign="top">
    				<th scope="row"><?php _e( "Enable Debug Log", 'easy-wp-smtp' ); ?></th>
    				<td>
    				    <input id="swpsmtp_enable_debug" type="checkbox" name="swpsmtp_enable_debug" value="1" <?php echo (isset( $swpsmtp_options[ 'smtp_settings' ][ 'enable_debug' ] ) && ($swpsmtp_options[ 'smtp_settings' ][ 'enable_debug' ])) ? 'checked' : ''; ?>/>
    				    <p class="description"><?php _e( "Check this box to enable mail debug log", 'easy-wp-smtp' ); ?></p>
    				    <a href="<?php echo admin_url(); ?>?swpsmtp_action=view_log" target="_blank"><?php _e( 'View Log', 'easy-wp-smtp' ); ?></a> | <a style="color: red;" id="swpsmtp_clear_log_btn" href="#0"><?php _e( 'Clear Log', 'easy-wp-smtp' ); ?></a>
    				</td>
    			    </tr>
    			</table>
    			<p class="submit">
    			    <input type="submit" id="additional-settings-form-submit" class="button-primary" value="<?php _e( 'Save Changes', 'easy-wp-smtp' ) ?>" />
    			</p>

    		    </div><!-- end of inside -->
    		</div><!-- end of postbox -->
    	    </div>
    	</form>

    	<div class="swpsmtp-tab-container" data-tab-name="testemail">
    	    <div class="postbox">
    		<h3 class="hndle"><label for="title"><?php _e( 'Test Email', 'easy-wp-smtp' ); ?></label></h3>
    		<div class="inside">
    		    <div id="swpsmtp-save-settings-notice" class="swpsmtp-msg-cont msg-error"><b><?php _e( 'Notice:', 'easy-wp-smtp' ); ?></b> <?php _e( 'You have unsaved settings. In order to send a test email, you need to go back to previous tab and click "Save Changes" button first.', 'easy-wp-smtp' ); ?></div>

			<?php
			if ( isset( $test_res ) && is_array( $test_res ) ) {
			    if ( isset( $test_res[ 'error' ] ) ) {
				$errmsg_class	 = ' msg-error';
				$errmsg_text	 = '<b>' . __( 'Following error occured when attempting to send test email:', 'easy-wp-smtp' ) . '</b><br />' . $test_res[ 'error' ];
			    } else {
				$errmsg_class	 = ' msg-success';
				$errmsg_text	 = '<b>' . __( 'Test email was successfully sent. No errors occured during the process.', 'easy-wp-smtp' ) . '</b>';
			    }
			    ?>

			    <div class="swpsmtp-msg-cont<?php echo $errmsg_class; ?>">
				<?php echo $errmsg_text; ?>

				<?php
				if ( isset( $test_res[ 'debug_log' ] ) ) {
				    ?>
	    			<br /><br />
	    			<a id="swpsmtp-show-hide-log-btn" href="#0"><?php _e( 'Show Debug Log', 'easy-wp-smtp' ); ?></a>
	    			<p id="swpsmtp-debug-log-cont"><textarea rows="20" style="width: 100%;"><?php echo $test_res[ 'debug_log' ]; ?></textarea></p>
	    			<script>
	    			    jQuery(function ($) {
	    				$('#swpsmtp-show-hide-log-btn').click(function (e) {
	    				    e.preventDefault();
	    				    var logCont = $('#swpsmtp-debug-log-cont');
	    				    if (logCont.is(':visible')) {
	    					$(this).html('<?php echo esc_attr( __( 'Show Debug Log', 'easy-wp-smtp' ) ); ?>');
	    				    } else {
	    					$(this).html('<?php echo esc_attr( __( 'Hide Debug Log', 'easy-wp-smtp' ) ); ?>');
	    				    }
	    				    logCont.toggle();
	    				});
	    <?php if ( isset( $test_res[ 'error' ] ) ) {
		?>
						$('#swpsmtp-show-hide-log-btn').click();
	    <?php }
	    ?>
	    			    });
	    			</script>
				    <?php
				}
				?>
			    </div>
			    <?php
			}
			?>

    		    <p><?php _e( 'You can use this section to send an email from your server using the above configured SMTP details to see if the email gets delivered.', 'easy-wp-smtp' ); ?></p>
    		    <p><b><?php _ex( 'Note:', '"Note" as in "Note: keep this in mind"', 'easy-wp-smtp' ); ?></b> <?php _e( 'debug log for this test email will be automatically displayed right after you send it. Test email also ignores "Enable Domain Check" option.', 'easy-wp-smtp' ); ?></p>

    		    <form id="swpsmtp_settings_test_email_form" method="post" action="">
    			<table class="form-table">
    			    <tr valign="top">
    				<th scope="row"><?php _e( "To", 'easy-wp-smtp' ); ?>:</th>
    				<td>
    				    <input id="swpsmtp_to" type="text" class="ignore-change" name="swpsmtp_to" value="<?php echo esc_html( $smtp_test_mail[ 'swpsmtp_to' ] ); ?>" /><br />
    				    <p class="description"><?php _e( "Enter the recipient's email address", 'easy-wp-smtp' ); ?></p>
    				</td>
    			    </tr>
    			    <tr valign="top">
    				<th scope="row"><?php _e( "Subject", 'easy-wp-smtp' ); ?>:</th>
    				<td>
    				    <input id="swpsmtp_subject" type="text" class="ignore-change" name="swpsmtp_subject" value="<?php echo esc_html( $smtp_test_mail[ 'swpsmtp_subject' ] ); ?>" /><br />
    				    <p class="description"><?php _e( "Enter a subject for your message", 'easy-wp-smtp' ); ?></p>
    				</td>
    			    </tr>
    			    <tr valign="top">
    				<th scope="row"><?php _e( "Message", 'easy-wp-smtp' ); ?>:</th>
    				<td>
    				    <textarea name="swpsmtp_message" id="swpsmtp_message" rows="5"><?php echo stripslashes( esc_textarea( $smtp_test_mail[ 'swpsmtp_message' ] ) ); ?></textarea><br />
    				    <p class="description"><?php _e( "Write your email message", 'easy-wp-smtp' ); ?></p>
    				</td>
    			    </tr>
    			</table>
    			<p class="submit">
    			    <input type="submit" id="test-email-form-submit" class="button-primary" value="<?php _e( 'Send Test Email', 'easy-wp-smtp' ) ?>" />
    			    <input type="hidden" name="swpsmtp_test_submit" value="submit" />
				<?php wp_nonce_field( plugin_basename( __FILE__ ), 'swpsmtp_test_nonce_name' ); ?>
    			</p>
    		    </form>
    		</div><!-- end of inside -->
    	    </div><!-- end of postbox -->

    	</div>
        </div>
        <div class="swpsmtp-settings-grid swpsmtp-settings-sidebar-cont">
    	<div class="postbox" style="min-width: inherit;">
    	    <h3 class="hndle"><label for="title"><?php _e( "Support", 'easy-wp-smtp' ); ?></label></h3>
    	    <div class="inside">
		    <?php echo sprintf( _x( "Having issues or difficulties? You can post your issue on the %s", '%s is replaced by "Support Forum" link', 'easy-wp-smtp' ), sprintf( '<a href="https://wordpress.org/support/plugin/easy-wp-smtp/" target="_blank">%s</a>', __( 'Support Forum', 'easy-wp-smtp' ) ) ); ?>
    	    </div>
    	</div>
    	<div class="postbox" style="min-width: inherit;">
    	    <h3 class="hndle"><label for="title"><?php _e( "Rate Us", 'easy-wp-smtp' ); ?></label></h3>
    	    <div class="inside">
		    <?php echo sprintf( _x( 'Like the plugin? Please give us a %s', '%s is replaced by "rating" link', 'easy-wp-smtp' ), sprintf( '<a href="https://wordpress.org/support/plugin/easy-wp-smtp/reviews/#new-post" target="_blank">%s</a>', __( 'rating', 'easy-wp-smtp' ) ) ); ?>
    		<div class="swpsmtp-stars-container">
    		    <a href="https://wordpress.org/support/plugin/easy-wp-smtp/reviews/#new-post" target="_blank"><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span>
    		    </a>
    		</div>
    	    </div>
    	</div>
        </div>
    </div>
    <div class="swpsmtp-yellow-box">
	<?php _ex( sprintf( "Please visit the %s plugin's documentation page to learn how to use this plugin.", '<a target="_blank" href="https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197">Easy WP SMTP</a>' ), '%s is replaced by <a target="_blank" href="https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197">Easy WP SMTP</a>', 'easy-wp-smtp' ); ?>
    </div>

    <script>
        function parseHash(hash) {
    	hash = hash.substring(1, hash.length);

    	var hashObj = [];

    	hash.split('&').forEach(function (q) {
    	    if (typeof q !== 'undefined') {
    		hashObj.push(q);
    	    }
    	});

    	return hashObj;
        }

        var swpsmtp_urlHash = 'smtp';
        var swpsmtp_focusObj = false;
        var swpsmtp_urlHashArr = parseHash(window.location.hash);

        if (swpsmtp_urlHashArr[0] !== '') {
    	swpsmtp_urlHash = swpsmtp_urlHashArr[0];
        }

        if (swpsmtp_urlHashArr[1] !== "undefined") {
    	swpsmtp_focusObj = swpsmtp_urlHashArr[1];
        }

        jQuery(function ($) {
    	var swpsmtp_activeTab = "";
    	$('a.nav-tab').click(function (e) {
    	    if ($(this).attr('data-tab-name') !== swpsmtp_activeTab) {
    		$('div.swpsmtp-tab-container[data-tab-name="' + swpsmtp_activeTab + '"]').hide();
    		$('a.nav-tab[data-tab-name="' + swpsmtp_activeTab + '"]').removeClass('nav-tab-active');
    		swpsmtp_activeTab = $(this).attr('data-tab-name');
    		$('div.swpsmtp-tab-container[data-tab-name="' + swpsmtp_activeTab + '"]').show();
    		$(this).addClass('nav-tab-active');
    		$('input#swpsmtp-urlHash').val(swpsmtp_activeTab);
    		if (window.location.hash !== swpsmtp_activeTab) {
    		    window.location.hash = swpsmtp_activeTab;
    		}
    		if (swpsmtp_focusObj) {
    		    $('html, body').animate({
    			scrollTop: $('#' + swpsmtp_focusObj).offset().top
    		    }, 'fast', function () {
    			$('#' + swpsmtp_focusObj).focus();
    			swpsmtp_focusObj = false;
    		    });
    		}
    	    }
    	});
    	$('a.nav-tab[data-tab-name="' + swpsmtp_urlHash + '"]').trigger('click');
        });

        jQuery(function ($) {
    	$('#swpsmtp-mail input').not('.ignore-change').change(function () {
    	    $('#swpsmtp-save-settings-notice').show();
    	    ;
    	});
    	$('#swpsmtp_enable_domain_check').change(function () {
    	    $('input[name="swpsmtp_allowed_domains"]').prop('disabled', !$(this).is(':checked'));
    	    $('input[name="swpsmtp_block_all_emails"]').prop('disabled', !$(this).is(':checked'));
    	});
    	$('#swpsmtp_clear_log_btn').click(function (e) {
    	    e.preventDefault();
    	    if (confirm("<?php _e( 'Are you sure want to clear log?', 'easy-wp-smtp' ); ?>")) {
    		var req = jQuery.ajax({
    		    url: ajaxurl,
    		    type: "post",
    		    data: {action: "swpsmtp_clear_log"}
    		});
    		req.done(function (data) {
    		    if (data === '1') {
    			alert("<?php _e( 'Log cleared.', 'easy-wp-smtp' ); ?>");
    		    } else {
    			alert("Error occured: " + data);
    		    }
    		});
    	    }
    	});
        });

    </script>

    <?php
    echo '</div></div>'; //<!-- end of #poststuff and #post-body -->
    echo '</div>'; //<!--  end of .wrap #swpsmtp-mail .swpsmtp-mail -->
}

/**
 * Plugin functions for init
 * @return void
 */
function swpsmtp_admin_init() {
    /* Internationalization, first(!) */
    load_plugin_textdomain( 'easy-wp-smtp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    add_action( 'wp_ajax_swpsmtp_clear_log', 'swpsmtp_clear_log' );
//view log file
    if ( isset( $_GET[ 'swpsmtp_action' ] ) ) {
	if ( $_GET[ 'swpsmtp_action' ] === 'view_log' ) {
	    $swpsmtp_options = get_option( 'swpsmtp_options' );
	    $log_file_name	 = $swpsmtp_options[ 'smtp_settings' ][ 'log_file_name' ];
	    if ( ! file_exists( plugin_dir_path( __FILE__ ) . $log_file_name ) ) {
		if ( swpsmtp_write_to_log( "Easy WP SMTP debug log file\r\n\r\n" ) === false ) {
		    wp_die( 'Can\'t write to log file. Check if plugin directory  (' . plugin_dir_path( __FILE__ ) . ') is writeable.' );
		};
	    }
	    $logfile = fopen( plugin_dir_path( __FILE__ ) . $log_file_name, 'rb' );
	    if ( ! $logfile ) {
		wp_die( 'Can\'t open log file.' );
	    }
	    header( 'Content-Type: text/plain' );
	    fpassthru( $logfile );
	    die;
	}
    }
}
