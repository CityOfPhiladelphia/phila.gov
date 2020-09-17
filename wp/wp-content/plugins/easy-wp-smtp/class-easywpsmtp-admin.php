<?php

class EasyWPSMTP_Admin {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function remove_conflicting_scripts() {
		$html = ob_get_clean();
		if ( defined( 'CLICKY_PLUGIN_DIR_URL' ) ) {
			$expr = '/^(.*)' . preg_quote( CLICKY_PLUGIN_DIR_URL, '/' ) . '(.*)$/m';
			$html = preg_replace( $expr, '', $html );
		}
		echo $html;
	}

	public function admin_enqueue_scripts( $hook ) {
		// Load only on ?page=swpsmtp_settings
		if ( 'settings_page_swpsmtp_settings' !== $hook ) {
			return;
		}

		//generate secret code for self-destruct function
		$sd_code = md5( uniqid( 'swpsmtp', true ) );
		set_transient( 'easy_wp_smtp_sd_code', $sd_code, 12 * 60 * 60 );

		$core           = EasyWPSMTP::get_instance();
		$plugin_data    = get_file_data( $core->plugin_file, array( 'Version' => 'Version' ), false );
		$plugin_version = $plugin_data['Version'];
		wp_enqueue_style( 'swpsmtp_admin_css', plugins_url( 'css/style.css', __FILE__ ), array(), '1.0' );
		wp_register_script( 'swpsmtp_admin_js', plugins_url( 'js/script.js', __FILE__ ), array(), $plugin_version, true );
		$params = array(
			'sd_redir_url'    => get_admin_url(),
			'sd_code'         => $sd_code,
			'clear_log_nonce' => wp_create_nonce( 'easy-wp-smtp-clear-log' ),
			'str'             => array(
				'clear_log'               => __( 'Are you sure want to clear log?', 'easy-wp-smtp' ),
				'log_cleared'             => __( 'Log cleared.', 'easy-wp-smtp' ),
				'error_occured'           => __( 'Error occurred:', 'easy-wp-smtp' ),
				'sending'                 => __( 'Sending...', 'easy-wp-smtp' ),
				'confirm_self_destruct'   => __( 'Are you sure you want to delete ALL your settings and deactive plugin?', 'easy-wp-smtp' ),
				'self_destruct_completed' => __( 'All settings have been deleted and plugin is deactivated.', 'easy-wp-smtp' ),
			),
		);
		wp_localize_script( 'swpsmtp_admin_js', 'easywpsmtp', $params );
		wp_enqueue_script( 'swpsmtp_admin_js' );

		// `Clicky by Yoast` plugin's admin-side scripts should be removed from settings page to prevent JS errors
		// https://wordpress.org/support/topic/plugin-causing-conflicts-on-admin-side/
		if ( class_exists( 'Clicky_Admin' ) ) {
			ob_start();
			add_action( 'admin_print_scripts', array( $this, 'remove_conflicting_scripts' ), 10000 );
		}
	}

	public function admin_menu() {
		add_options_page( __( 'Easy WP SMTP', 'easy-wp-smtp' ), __( 'Easy WP SMTP', 'easy-wp-smtp' ), 'manage_options', 'swpsmtp_settings', 'swpsmtp_settings' );
	}
}

new EasyWPSMTP_Admin();

/**
 * Renders the admin settings menu of the plugin.
 * @return void
 */
function swpsmtp_settings() {
	$easy_wp_smtp = EasyWPSMTP::get_instance();
	$enc_req_met  = true;
	$enc_req_err  = '';
	//check if OpenSSL PHP extension is loaded and display warning if it's not
	if ( ! extension_loaded( 'openssl' ) ) {
		$class   = 'notice notice-warning';
		$message = __( "PHP OpenSSL extension is not installed on the server. It's required by Easy WP SMTP plugin to operate properly. Please contact your server administrator or hosting provider and ask them to install it.", 'easy-wp-smtp' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		//also show encryption error message
		$enc_req_err .= __( 'PHP OpenSSL extension is not installed on the server. It is required for encryption to work properly. Please contact your server administrator or hosting provider and ask them to install it.', 'easy-wp-smtp' ) . '<br />';
		$enc_req_met  = false;
	}

	//check if server meets encryption requirements
	if ( version_compare( PHP_VERSION, '5.3.0' ) < 0 ) {
		$enc_req_err = ! empty( $enc_req_err ) ? $enc_req_err   .= '<br />' : '';
		// translators: %s is PHP version
		$enc_req_err .= sprintf( __( 'Your PHP version is %s, encryption function requires PHP version 5.3.0 or higher.', 'easy-wp-smtp' ), PHP_VERSION );
		$enc_req_met  = false;
	}

	echo '<div class="wrap" id="swpsmtp-mail">';
	echo '<h2>' . esc_html( __( 'Easy WP SMTP Settings', 'easy-wp-smtp' ) ) . '</h2>';
	echo '<div id="poststuff"><div id="post-body">';

	$message = '';
	$error   = '';

	$swpsmtp_options = get_option( 'swpsmtp_options' );
	$smtp_test_mail  = get_option( 'smtp_test_mail' );
	$gag_password    = '#easywpsmtpgagpass#';
	if ( empty( $smtp_test_mail ) ) {
		$smtp_test_mail = array(
			'swpsmtp_to'      => '',
			'swpsmtp_subject' => '',
			'swpsmtp_message' => '',
		);
	}

	if ( isset( $_POST['swpsmtp_form_submit'] ) ) {
		// check nounce
		if ( ! check_admin_referer( plugin_basename( __FILE__ ), 'swpsmtp_nonce_name' ) ) {
			$error .= ' ' . __( 'Nonce check failed.', 'easy-wp-smtp' );
		}
		/* Update settings */
		$swpsmtp_options['from_name_field']         = isset( $_POST['swpsmtp_from_name'] ) ? sanitize_text_field( wp_unslash( $_POST['swpsmtp_from_name'] ) ) : '';
		$swpsmtp_options['force_from_name_replace'] = isset( $_POST['swpsmtp_force_from_name_replace'] ) ? 1 : false;
		$swpsmtp_options['sub_mode']                = isset( $_POST['swpsmtp_sub_mode'] ) ? 1 : false;

		if ( isset( $_POST['swpsmtp_from_email'] ) ) {
			if ( is_email( $_POST['swpsmtp_from_email'] ) ) {
				$swpsmtp_options['from_email_field'] = sanitize_email( $_POST['swpsmtp_from_email'] );
			} else {
				$error .= ' ' . __( "Please enter a valid email address in the 'FROM' field.", 'easy-wp-smtp' );
			}
		}
		if ( isset( $_POST['swpsmtp_reply_to_email'] ) ) {
			$swpsmtp_options['reply_to_email'] = sanitize_email( $_POST['swpsmtp_reply_to_email'] );
		}
                if ( isset( $_POST['swpsmtp_bcc_email'] ) ) {
                        $swpsmtp_options['bcc_email'] = sanitize_text_field( $_POST['swpsmtp_bcc_email'] );//Can contain comma seperated addresses.
                }

		if ( isset( $_POST['swpsmtp_email_ignore_list'] ) ) {
			$swpsmtp_options['email_ignore_list'] = sanitize_text_field( $_POST['swpsmtp_email_ignore_list'] );
		}

		$swpsmtp_options['smtp_settings']['host']            = stripslashes( $_POST['swpsmtp_smtp_host'] );
		$swpsmtp_options['smtp_settings']['type_encryption'] = ( isset( $_POST['swpsmtp_smtp_type_encryption'] ) ) ? sanitize_text_field( $_POST['swpsmtp_smtp_type_encryption'] ) : 'none';
		$swpsmtp_options['smtp_settings']['autentication']   = ( isset( $_POST['swpsmtp_smtp_autentication'] ) ) ? sanitize_text_field( $_POST['swpsmtp_smtp_autentication'] ) : 'yes';
		$swpsmtp_options['smtp_settings']['username']        = stripslashes( $_POST['swpsmtp_smtp_username'] );

		$swpsmtp_options['smtp_settings']['enable_debug'] = isset( $_POST['swpsmtp_enable_debug'] ) ? 1 : false;
		$swpsmtp_options['smtp_settings']['insecure_ssl'] = isset( $_POST['swpsmtp_insecure_ssl'] ) ? 1 : false;
		$swpsmtp_options['smtp_settings']['encrypt_pass'] = isset( $_POST['swpsmtp_encrypt_pass'] ) ? 1 : false;

		$smtp_password = $_POST['swpsmtp_smtp_password'];
		if ( $smtp_password !== $gag_password ) {
			$swpsmtp_options['smtp_settings']['password'] = $easy_wp_smtp->encrypt_password( $smtp_password );
		}

		if ( $swpsmtp_options['smtp_settings']['encrypt_pass'] && ! get_option( 'swpsmtp_pass_encrypted', false ) ) {
			update_option( 'swpsmtp_options', $swpsmtp_options );
			$pass = $easy_wp_smtp->get_password();
			$swpsmtp_options['smtp_settings']['password'] = $easy_wp_smtp->encrypt_password( $pass );
			update_option( 'swpsmtp_options', $swpsmtp_options );
		}

		$swpsmtp_options['enable_domain_check'] = isset( $_POST['swpsmtp_enable_domain_check'] ) ? 1 : false;
		if ( isset( $_POST['swpsmtp_allowed_domains'] ) ) {
			$swpsmtp_options['block_all_emails'] = isset( $_POST['swpsmtp_block_all_emails'] ) ? 1 : false;
			$swpsmtp_options['allowed_domains']  = base64_encode( sanitize_text_field( $_POST['swpsmtp_allowed_domains'] ) ); //phpcs:ignore
		} elseif ( ! isset( $swpsmtp_options['allowed_domains'] ) ) {
			$swpsmtp_options['allowed_domains'] = '';
		}

		/* Check value from "SMTP port" option */
		if ( isset( $_POST['swpsmtp_smtp_port'] ) ) {
			if ( empty( $_POST['swpsmtp_smtp_port'] ) || 1 > intval( $_POST['swpsmtp_smtp_port'] ) || ( ! preg_match( '/^\d+$/', $_POST['swpsmtp_smtp_port'] ) ) ) {
				$swpsmtp_options['smtp_settings']['port'] = '25';
				$error                                   .= ' ' . __( "Please enter a valid port in the 'SMTP Port' field.", 'easy-wp-smtp' );
			} else {
				$swpsmtp_options['smtp_settings']['port'] = sanitize_text_field( $_POST['swpsmtp_smtp_port'] );
			}
		}

		/* Update settings in the database */
		if ( empty( $error ) ) {
			update_option( 'swpsmtp_options', $swpsmtp_options );
			$message .= __( 'Settings saved.', 'easy-wp-smtp' );
		} else {
			$error .= ' ' . __( 'Settings are not saved.', 'easy-wp-smtp' );
		}
	}

	/* Send test letter */
	$swpsmtp_to = '';
	if ( isset( $_POST['swpsmtp_test_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'swpsmtp_test_nonce_name' ) ) {
		if ( isset( $_POST['swpsmtp_to'] ) ) {
			$to_email = sanitize_text_field( $_POST['swpsmtp_to'] );
			if ( is_email( $to_email ) ) {
				$swpsmtp_to = $to_email;
			} else {
				$error .= __( 'Please enter a valid email address in the recipient email field.', 'easy-wp-smtp' );
			}
		}
		$swpsmtp_subject = isset( $_POST['swpsmtp_subject'] ) ? sanitize_text_field( $_POST['swpsmtp_subject'] ) : '';
		$swpsmtp_message = isset( $_POST['swpsmtp_message'] ) ? EasyWPSMTP_Utils::sanitize_textarea( $_POST['swpsmtp_message'] ) : '';

		//Save the test mail details so it doesn't need to be filled in everytime.
		$smtp_test_mail['swpsmtp_to']      = $swpsmtp_to;
		$smtp_test_mail['swpsmtp_subject'] = $swpsmtp_subject;
		$smtp_test_mail['swpsmtp_message'] = $swpsmtp_message;
		update_option( 'smtp_test_mail', $smtp_test_mail );

		if ( ! empty( $swpsmtp_to ) ) {
			$test_res = $easy_wp_smtp->test_mail( $swpsmtp_to, $swpsmtp_subject, $swpsmtp_message );
		}
	}
	?>

	<div class="updated fade" <?php echo empty( $message ) ? ' style="display:none"' : ''; ?>>
		<p><strong><?php echo esc_html( $message ); ?></strong></p>
	</div>
	<div class="error" <?php echo empty( $error ) ? 'style="display:none"' : ''; ?>>
		<p><strong><?php echo esc_html( $error ); ?></strong></p>
	</div>

	<div class="nav-tab-wrapper">
		<a href="#smtp" data-tab-name="smtp" class="nav-tab"><?php esc_html_e( 'SMTP Settings', 'easy-wp-smtp' ); ?></a>
		<a href="#additional" data-tab-name="additional" class="nav-tab"><?php esc_html_e( 'Additional Settings', 'easy-wp-smtp' ); ?></a>
		<a href="#testemail" data-tab-name="testemail" class="nav-tab"><?php esc_html_e( 'Test Email', 'easy-wp-smtp' ); ?></a>
	</div>

	<div class="swpsmtp-settings-container">
		<div class="swpsmtp-settings-grid swpsmtp-settings-main-cont">

			<form autocomplete="off" id="swpsmtp_settings_form" method="post" action="">

				<input type="hidden" id="swpsmtp-urlHash" name="swpsmtp-urlHash" value="">

				<div class="swpsmtp-tab-container" data-tab-name="smtp">
					<div class="postbox">
						<h3 class="hndle"><label for="title"><?php esc_html_e( 'SMTP Configuration Settings', 'easy-wp-smtp' ); ?></label></h3>
						<div class="inside">

							<p><?php esc_html_e( 'You can request your hosting provider for the SMTP details of your site. Use the SMTP details provided by your hosting provider to configure the following settings.', 'easy-wp-smtp' ); ?></p>

							<table class="form-table">
								<tr valign="top">
									<th scope="row"><?php esc_html_e( 'From Email Address', 'easy-wp-smtp' ); ?></th>
									<td>
										<input id="swpsmtp_from_email" type="text" name="swpsmtp_from_email" value="<?php echo isset( $swpsmtp_options['from_email_field'] ) ? esc_attr( $swpsmtp_options['from_email_field'] ) : ''; ?>" /><br />
										<p class="description"><?php esc_html_e( "This email address will be used in the 'From' field.", 'easy-wp-smtp' ); ?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php esc_html_e( 'From Name', 'easy-wp-smtp' ); ?></th>
									<td>
										<input id="swpsmtp_from_name" type="text" name="swpsmtp_from_name" value="<?php echo isset( $swpsmtp_options['from_name_field'] ) ? esc_attr( $swpsmtp_options['from_name_field'] ) : ''; ?>" /><br />
										<p class="description"><?php esc_html_e( "This text will be used in the 'FROM' field", 'easy-wp-smtp' ); ?></p>
										<p>
											<label><input type="checkbox" id="swpsmtp_force_from_name_replace" name="swpsmtp_force_from_name_replace" value="1" <?php echo ( isset( $swpsmtp_options['force_from_name_replace'] ) && ( $swpsmtp_options['force_from_name_replace'] ) ) ? ' checked' : ''; ?> /> <?php esc_html_e( 'Force From Name Replacement', 'easy-wp-smtp' ); ?></label>
										</p>
										<p class="description"><?php esc_html_e( "When enabled, the plugin will set the above From Name for each email. Disable it if you're using contact form plugins, it will prevent the plugin from replacing form submitter's name when contact email is sent.", 'easy-wp-smtp' ); ?>
											<br />
											<?php esc_html_e( "If email's From Name is empty, the plugin will set the above value regardless.", 'easy-wp-smtp' ); ?>
										</p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php esc_html_e( 'Reply-To Email Address', 'easy-wp-smtp' ); ?></th>
									<td>
										<input id="swpsmtp_reply_to_email" type="email" name="swpsmtp_reply_to_email" value="<?php echo isset( $swpsmtp_options['reply_to_email'] ) ? esc_attr( $swpsmtp_options['reply_to_email'] ) : ''; ?>" /><br />
										<p class="description"><?php esc_html_e( "Optional. This email address will be used in the 'Reply-To' field of the email. Leave it blank to use 'From' email as the reply-to value.", 'easy-wp-smtp' ); ?></p>
										<p>
											<label><input type="checkbox" id="swpsmtp_sub_mode" name="swpsmtp_sub_mode" value="1" <?php echo ( isset( $swpsmtp_options['sub_mode'] ) && ( $swpsmtp_options['sub_mode'] ) ) ? ' checked' : ''; ?> /> <?php esc_html_e( 'Substitute Mode', 'easy-wp-smtp' ); ?></label>
										</p>
										<p class="description"><?php esc_html_e( 'When enabled, the plugin will substitute occurances of the above From Email with the Reply-To Email address. The Reply-To Email will still be used if no other Reply-To Email is present. This option can prevent conflicts with other plugins that specify reply-to email addresses but still replaces the From Email with the Reply-To Email.', 'easy-wp-smtp' ); ?></p>
										<p>
									</td>
								</tr>
                                                                <tr valign="top">
                                                                        <th scope="row"><?php esc_html_e( 'BCC Email Address', 'easy-wp-smtp' ); ?></th>
                                                                        <td>
                                                                                <input id="swpsmtp_bcc_email" type="text" name="swpsmtp_bcc_email" value="<?php echo isset( $swpsmtp_options['bcc_email'] ) ? esc_attr( $swpsmtp_options['bcc_email'] ) : ''; ?>" /><br />
                                                                                <p class="description"><?php esc_html_e( "Optional. This email address will be used in the 'BCC' field of the outgoing emails. Use this option carefully since all your outgoing emails from this site will add this address to the BCC field. You can also enter multiple email addresses (comma separated).", 'easy-wp-smtp' ); ?></p>
                                                                        </td>
                                                                </tr>

								<tr class="ad_opt swpsmtp_smtp_options">
									<th><?php esc_html_e( 'SMTP Host', 'easy-wp-smtp' ); ?></th>
									<td>
										<input id='swpsmtp_smtp_host' type='text' name='swpsmtp_smtp_host' value='<?php echo isset( $swpsmtp_options['smtp_settings']['host'] ) ? esc_attr( $swpsmtp_options['smtp_settings']['host'] ) : ''; ?>' /><br />
										<p class="description"><?php esc_html_e( 'Your mail server', 'easy-wp-smtp' ); ?></p>
									</td>
								</tr>
								<tr class="ad_opt swpsmtp_smtp_options">
									<th><?php esc_html_e( 'Type of Encryption', 'easy-wp-smtp' ); ?></th>
									<td>
										<label for="swpsmtp_smtp_type_encryption_1"><input type="radio" id="swpsmtp_smtp_type_encryption_1" name="swpsmtp_smtp_type_encryption" value='none'
										<?php
										if ( isset( $swpsmtp_options['smtp_settings']['type_encryption'] ) && 'none' === $swpsmtp_options['smtp_settings']['type_encryption'] ) {
											echo 'checked="checked"';}
										?>
										/> <?php esc_html_e( 'None', 'easy-wp-smtp' ); ?></label>
										<label for="swpsmtp_smtp_type_encryption_2"><input type="radio" id="swpsmtp_smtp_type_encryption_2" name="swpsmtp_smtp_type_encryption" value='ssl'
										<?php
										if ( isset( $swpsmtp_options['smtp_settings']['type_encryption'] ) && 'ssl' === $swpsmtp_options['smtp_settings']['type_encryption'] ) {
											echo 'checked="checked"';}
										?>
										/> <?php esc_html_e( 'SSL/TLS', 'easy-wp-smtp' ); ?></label>
										<label for="swpsmtp_smtp_type_encryption_3"><input type="radio" id="swpsmtp_smtp_type_encryption_3" name="swpsmtp_smtp_type_encryption" value='tls'
										<?php
										if ( isset( $swpsmtp_options['smtp_settings']['type_encryption'] ) && 'tls' === $swpsmtp_options['smtp_settings']['type_encryption'] ) {
											echo 'checked="checked"';}
										?>
										/> <?php esc_html_e( 'STARTTLS', 'easy-wp-smtp' ); ?></label><br />
										<p class="description"><?php esc_html_e( 'For most servers SSL/TLS is the recommended option', 'easy-wp-smtp' ); ?></p>
									</td>
								</tr>
								<tr class="ad_opt swpsmtp_smtp_options">
									<th><?php esc_html_e( 'SMTP Port', 'easy-wp-smtp' ); ?></th>
									<td>
										<input id='swpsmtp_smtp_port' type='text' name='swpsmtp_smtp_port' value='<?php echo isset( $swpsmtp_options['smtp_settings']['port'] ) ? esc_attr( $swpsmtp_options['smtp_settings']['port'] ) : ''; ?>' /><br />
										<p class="description"><?php esc_html_e( 'The port to your mail server', 'easy-wp-smtp' ); ?></p>
									</td>
								</tr>
								<tr class="ad_opt swpsmtp_smtp_options">
									<th><?php esc_html_e( 'SMTP Authentication', 'easy-wp-smtp' ); ?></th>
									<td>
										<label for="swpsmtp_smtp_autentication"><input type="radio" id="swpsmtp_smtp_autentication_1" name="swpsmtp_smtp_autentication" value='no'
										<?php
										if ( isset( $swpsmtp_options['smtp_settings']['autentication'] ) && 'no' === $swpsmtp_options['smtp_settings']['autentication'] ) {
											echo 'checked="checked"';}
										?>
										/> <?php esc_html_e( 'No', 'easy-wp-smtp' ); ?></label>
										<label for="swpsmtp_smtp_autentication"><input type="radio" id="swpsmtp_smtp_autentication_2" name="swpsmtp_smtp_autentication" value='yes'
										<?php
										if ( isset( $swpsmtp_options['smtp_settings']['autentication'] ) && 'yes' === $swpsmtp_options['smtp_settings']['autentication'] ) {
											echo 'checked="checked"';}
										?>
										/> <?php esc_html_e( 'Yes', 'easy-wp-smtp' ); ?></label><br />
										<p class="description"><?php esc_html_e( "This options should always be checked 'Yes'", 'easy-wp-smtp' ); ?></p>
									</td>
								</tr>
								<tr class="ad_opt swpsmtp_smtp_options">
									<th><?php esc_html_e( 'SMTP Username', 'easy-wp-smtp' ); ?></th>
									<td>
										<input id='swpsmtp_smtp_username' type='text' name='swpsmtp_smtp_username' value='<?php echo isset( $swpsmtp_options['smtp_settings']['username'] ) ? esc_attr( $swpsmtp_options['smtp_settings']['username'] ) : ''; ?>' /><br />
										<p class="description"><?php esc_html_e( 'The username to login to your mail server', 'easy-wp-smtp' ); ?></p>
									</td>
								</tr>
								<tr class="ad_opt swpsmtp_smtp_options">
									<th><?php esc_html_e( 'SMTP Password', 'easy-wp-smtp' ); ?></th>
									<td>
										<input id="swpsmtp_smtp_password" type="password" name="swpsmtp_smtp_password" value="<?php echo esc_attr( ( $easy_wp_smtp->get_password() !== '' ? $gag_password : '' ) ); ?>" autocomplete="new-password" /><br />
										<p class="description"><?php echo esc_html( __( 'The password to login to your mail server', 'easy-wp-smtp' ) ); ?></p>
										<p class="description"><b><?php echo esc_html( _x( 'Note:', '"Note" as in "Note: keep this in mind"', 'easy-wp-smtp' ) ); ?></b> <?php echo esc_html( __( 'when you click "Save Changes", your actual password is stored in the database and then used to send emails. This field is replaced with a gag (#easywpsmtpgagpass#). This is done to prevent someone with the access to Settings page from seeing your password (using password fields unmasking programs, for example).', 'easy-wp-smtp' ) ); ?></p>
									</td>
								</tr>
							</table>
							<p class="submit">
								<input type="submit" id="settings-form-submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'easy-wp-smtp' ); ?>" />
								<input type="hidden" name="swpsmtp_form_submit" value="submit" />
								<?php wp_nonce_field( plugin_basename( __FILE__ ), 'swpsmtp_nonce_name' ); ?>
							</p>
						</div><!-- end of inside -->
					</div><!-- end of postbox -->
				</div>

				<div class="swpsmtp-tab-container" data-tab-name="additional">
					<div class="postbox">
						<h3 class="hndle"><label for="title"><?php esc_html_e( 'Additional Settings (Optional)', 'easy-wp-smtp' ); ?></label></h3>
						<div class="inside">
							<table class="form-table">
								<tr valign="top">
									<th scope="row"><?php esc_html_e( "Don't Replace \"From\" Field", 'easy-wp-smtp' ); ?></th>
									<td>
										<input id="swpsmtp_email_ignore_list" type="text" name="swpsmtp_email_ignore_list" value="<?php echo isset( $swpsmtp_options['email_ignore_list'] ) ? esc_attr( $swpsmtp_options['email_ignore_list'] ) : ''; ?>" /><br />
										<p class="description"><?php esc_html_e( 'Comma separated emails list. Example value: email1@domain.com, email2@domain.com', 'easy-wp-smtp' ); ?></p>
										<p class="description"><?php esc_html_e( "This option is useful when you are using several email aliases on your SMTP server. If you don't want your aliases to be replaced by the address specified in \"From\" field, enter them in this field.", 'easy-wp-smtp' ); ?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php esc_html_e( 'Enable Domain Check', 'easy-wp-smtp' ); ?></th>
									<td>
										<input id="swpsmtp_enable_domain_check" type="checkbox" id="swpsmtp_enable_domain_check" name="swpsmtp_enable_domain_check" value="1" <?php echo ( isset( $swpsmtp_options['enable_domain_check'] ) && ( $swpsmtp_options['enable_domain_check'] ) ) ? ' checked' : ''; ?> />
										<p class="description"><?php esc_html_e( 'This option is usually used by developers only. SMTP settings will be used only if the site is running on following domain(s):', 'easy-wp-smtp' ); ?></p>
										<input id="swpsmtp_allowed_domains" type="text" name="swpsmtp_allowed_domains" value="<?php echo esc_attr( EasyWPSMTP_Utils::base64_decode_maybe( $swpsmtp_options['allowed_domains'] ) ); ?>" <?php echo ( isset( $swpsmtp_options['enable_domain_check'] ) && ( $swpsmtp_options['enable_domain_check'] ) ) ? '' : ' disabled'; ?> />
										<p class="description"><?php esc_html_e( 'Coma-separated domains list. Example: domain1.com, domain2.com', 'easy-wp-smtp' ); ?></p>
										<p>
											<label><input type="checkbox" id="swpsmtp_block_all_emails" name="swpsmtp_block_all_emails" value="1" <?php echo ( isset( $swpsmtp_options['block_all_emails'] ) && ( $swpsmtp_options['block_all_emails'] ) ) ? ' checked' : ''; ?><?php echo ( isset( $swpsmtp_options['enable_domain_check'] ) && ( $swpsmtp_options['enable_domain_check'] ) ) ? '' : ' disabled'; ?> /> <?php esc_html_e( 'Block all emails', 'easy-wp-smtp' ); ?></label>
										</p>
										<p class="description"><?php esc_html_e( 'When enabled, plugin attempts to block ALL emails from being sent out if domain mismtach.', 'easy-wp-smtp' ); ?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php esc_html_e( 'Encrypt Password', 'easy-wp-smtp' ); ?></th>
									<td>
										<?php if ( $enc_req_met ) { ?>
											<input id="swpsmtp_encrypt_pass" type="checkbox" name="swpsmtp_encrypt_pass" value="1" <?php echo ( isset( $swpsmtp_options['smtp_settings']['encrypt_pass'] ) && ( $swpsmtp_options['smtp_settings']['encrypt_pass'] ) ) ? 'checked' : ''; ?> />
											<p class="description"><?php esc_html_e( 'When enabled, your SMTP password is stored in the database using AES-256 encryption.', 'easy-wp-smtp' ); ?></p>
										<?php } else { ?>
											<p style="color: red;"><?php echo esc_html( $enc_req_err ); ?></p>
										<?php } ?>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php esc_html_e( 'Allow Insecure SSL Certificates', 'easy-wp-smtp' ); ?></th>
									<td>
										<input id="swpsmtp_insecure_ssl" type="checkbox" name="swpsmtp_insecure_ssl" value="1" <?php echo ( isset( $swpsmtp_options['smtp_settings']['insecure_ssl'] ) && ( $swpsmtp_options['smtp_settings']['insecure_ssl'] ) ) ? 'checked' : ''; ?> />
										<p class="description"><?php esc_html_e( "Allows insecure and self-signed SSL certificates on SMTP server. It's highly recommended to keep this option disabled.", 'easy-wp-smtp' ); ?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php esc_html_e( 'Enable Debug Log', 'easy-wp-smtp' ); ?></th>
									<td>
										<input id="swpsmtp_enable_debug" type="checkbox" name="swpsmtp_enable_debug" value="1" <?php echo ( isset( $swpsmtp_options['smtp_settings']['enable_debug'] ) && ( $swpsmtp_options['smtp_settings']['enable_debug'] ) ) ? 'checked' : ''; ?> />
										<p class="description"><?php esc_html_e( 'Check this box to enable mail debug log', 'easy-wp-smtp' ); ?></p>
										<a href="<?php echo esc_attr( admin_url() ); ?>?swpsmtp_action=view_log" target="_blank"><?php esc_html_e( 'View Log', 'easy-wp-smtp' ); ?></a> | <a style="color: red;" id="swpsmtp_clear_log_btn" href="#0"><?php esc_html_e( 'Clear Log', 'easy-wp-smtp' ); ?></a>
									</td>
								</tr>
							</table>
							<p class="submit">
								<input type="submit" id="additional-settings-form-submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'easy-wp-smtp' ); ?>" />
							</p>

						</div><!-- end of inside -->
					</div><!-- end of postbox -->

					<div class="postbox">
						<h3 class="hndle"><label for="title"><?php esc_html_e( 'Danger Zone', 'easy-wp-smtp' ); ?></label></h3>
						<div class="inside">
							<p><?php esc_html_e( 'Actions in this section can (and some of them will) erase or mess up your settings. Use it with caution.', 'easy-wp-smtp' ); ?></p>
							<table class="form-table">
								<tr valign="top">
									<th scope="row"><?php esc_html_e( 'Export\Import Settings', 'easy-wp-smtp' ); ?></th>
									<td>
										<button id="swpsmtp_export_settings_btn" type="button" class="button"><?php esc_html_e( 'Export Settings', 'easy-wp-smtp' ); ?></button>
										<p class="description"><?php esc_html_e( 'Use this to export plugin settings to a file.', 'easy-wp-smtp' ); ?></p>
										<p></p>
										<button id="swpsmtp_import_settings_btn" type="button" class="button"><?php esc_html_e( 'Import Settings', 'easy-wp-smtp' ); ?></button>
										<p class="description"><?php esc_html_e( 'Use this to import plugin settings from a file. Note this would replace all your existing settings, so use with caution.', 'easy-wp-smtp' ); ?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php esc_html_e( 'Delete Settings and Deactivate Plugin', 'easy-wp-smtp' ); ?></th>
									<td>
										<button id="swpsmtp_self_destruct_btn" style="color: red;" type="button" class="button button-secondary">>>> <?php esc_html_e( 'Self-destruct', 'easy-wp-smtp' ); ?> <<<</button> <p class="description"><?php esc_html_e( "This will remove ALL your settings and deactivate the plugin. Useful when you're uninstalling the plugin and want to completely remove all crucial data stored in the database.", 'easy-wp-smtp' ); ?></p>
												<p style="color: red; font-weight: bold;"><?php esc_html_e( "Warning! This can't be undone.", 'easy-wp-smtp' ); ?></p>
									</td>
								</tr>
							</table>
						</div>
					</div>

				</div>
			</form>

			<form id="swpsmtp_export_settings_frm" style="display: none;" method="POST">
				<input type="hidden" name="swpsmtp_export_settings" value="1">
				<?php wp_nonce_field( 'easy_wp_smtp_export_settings', 'easy_wp_smtp_export_settings_nonce' ); ?>
			</form>

			<form id="swpsmtp_import_settings_frm" style="display: none;" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="swpsmtp_import_settings" value="1">
				<input id="swpsmtp_import_settings_select_file" type="file" name="swpsmtp_import_settings_file">
				<?php wp_nonce_field( 'easy_wp_smtp_import_settings', 'easy_wp_smtp_import_settings_nonce' ); ?>
			</form>

			<div class="swpsmtp-tab-container" data-tab-name="testemail">
				<div class="postbox">
					<h3 class="hndle"><label for="title"><?php esc_html_e( 'Test Email', 'easy-wp-smtp' ); ?></label></h3>
					<div class="inside">
						<div id="swpsmtp-save-settings-notice" class="swpsmtp-msg-cont msg-error"><b><?php echo esc_html( _x( 'Note:', '"Note" as in "Note: keep this in mind"', 'easy-wp-smtp' ) ); ?></b> <?php esc_html_e( 'You have unsaved settings. In order to send a test email, you need to go back to previous tab and click "Save Changes" button first.', 'easy-wp-smtp' ); ?></div>

						<?php
						if ( isset( $test_res ) && is_array( $test_res ) ) {
							if ( isset( $test_res['error'] ) ) {
								$errmsg_class = ' msg-error';
								$errmsg_text  = '<b>' . esc_html__( 'Following error occurred when attempting to send test email:', 'easy-wp-smtp' ) . '</b><br />' . esc_html( $test_res['error'] );
							} else {
								$errmsg_class = ' msg-success';
								$errmsg_text  = '<b>' . esc_html__( 'Test email was successfully sent. No errors occurred during the process.', 'easy-wp-smtp' ) . '</b>';
							}
							?>

							<div class="swpsmtp-msg-cont<?php echo esc_attr( $errmsg_class ); ?>">
								<?php echo $errmsg_text; //phpcs:ignore?>

								<?php
								if ( isset( $test_res['debug_log'] ) ) {
									?>
									<br /><br />
									<a id="swpsmtp-show-hide-log-btn" href="#0"><?php esc_html_e( 'Show Debug Log', 'easy-wp-smtp' ); ?></a>
									<p id="swpsmtp-debug-log-cont"><textarea rows="20" style="width: 100%;"><?php echo esc_html( $test_res['debug_log'] ); ?></textarea></p>
									<script>
										jQuery(function($) {
											$('#swpsmtp-show-hide-log-btn').click(function(e) {
												e.preventDefault();
												var logCont = $('#swpsmtp-debug-log-cont');
												if (logCont.is(':visible')) {
													$(this).html('<?php esc_attr_e( 'Show Debug Log', 'easy-wp-smtp' ); ?>');
												} else {
													$(this).html('<?php esc_attr_e( 'Hide Debug Log', 'easy-wp-smtp' ); ?>');
												}
												logCont.toggle();
											});
											<?php
											if ( isset( $test_res['error'] ) ) {
												?>
												$('#swpsmtp-show-hide-log-btn').click();
												<?php
											}
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

						<p><?php esc_html_e( 'You can use this section to send an email from your server using the above configured SMTP details to see if the email gets delivered.', 'easy-wp-smtp' ); ?></p>
						<p><b><?php echo esc_html( _x( 'Note:', '"Note" as in "Note: keep this in mind"', 'easy-wp-smtp' ) ); ?></b> <?php esc_html_e( 'debug log for this test email will be automatically displayed right after you send it. Test email also ignores "Enable Domain Check" option.', 'easy-wp-smtp' ); ?></p>

						<form id="swpsmtp_settings_test_email_form" method="post" action="">
							<table class="form-table">
								<tr valign="top">
									<th scope="row"><?php esc_html_e( 'To', 'easy-wp-smtp' ); ?>:</th>
									<td>
										<input id="swpsmtp_to" type="text" class="ignore-change" name="swpsmtp_to" value="<?php echo esc_html( $smtp_test_mail['swpsmtp_to'] ); ?>" /><br />
										<p class="description"><?php esc_html_e( "Enter the recipient's email address", 'easy-wp-smtp' ); ?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php esc_html_e( 'Subject', 'easy-wp-smtp' ); ?>:</th>
									<td>
										<input id="swpsmtp_subject" type="text" class="ignore-change" name="swpsmtp_subject" value="<?php echo esc_html( $smtp_test_mail['swpsmtp_subject'] ); ?>" /><br />
										<p class="description"><?php esc_html_e( 'Enter a subject for your message', 'easy-wp-smtp' ); ?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php esc_html_e( 'Message', 'easy-wp-smtp' ); ?>:</th>
									<td>
										<textarea name="swpsmtp_message" id="swpsmtp_message" rows="5"><?php echo esc_textarea( stripslashes( $smtp_test_mail['swpsmtp_message'] ) ); ?></textarea><br />
										<p class="description"><?php esc_html_e( 'Write your email message', 'easy-wp-smtp' ); ?></p>
									</td>
								</tr>
							</table>
							<p class="submit">
								<input type="submit" id="test-email-form-submit" class="button-primary" value="<?php esc_attr_e( 'Send Test Email', 'easy-wp-smtp' ); ?>" />
								<input type="hidden" name="swpsmtp_test_submit" value="submit" />
								<?php wp_nonce_field( plugin_basename( __FILE__ ), 'swpsmtp_test_nonce_name' ); ?>
								<span id="swpsmtp-spinner" class="spinner"></span>
							</p>
						</form>
					</div><!-- end of inside -->
				</div><!-- end of postbox -->

			</div>
		</div>
		<div class="swpsmtp-settings-grid swpsmtp-settings-sidebar-cont">
			<div class="postbox" style="min-width: inherit;">
				<h3 class="hndle"><label for="title"><?php esc_html_e( 'Documentation', 'easy-wp-smtp' ); ?></label></h3>
				<div class="inside">
					<?php
						printf(
							esc_html(
								// translators: %s is replaced by documentation page URL
								_x( "Please visit the %s plugin's documentation page to learn how to use this plugin.", '%s is replaced by <a target="_blank" href="https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197">Easy WP SMTP</a>', 'easy-wp-smtp' )
							),
							'<a target="_blank" href="https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197">Easy WP SMTP</a>'
						);
					?>
				</div>
			</div>
			<div class="postbox" style="min-width: inherit;">
				<h3 class="hndle"><label for="title"><?php esc_html_e( 'Support', 'easy-wp-smtp' ); ?></label></h3>
				<div class="inside">
					<?php
						printf(
							esc_html(
								// translators: %s is replaced by support forum URL
								_x( 'Having issues or difficulties? You can post your issue on the %s', '%s is replaced by "Support Forum" link', 'easy-wp-smtp' )
							),
							sprintf(
								'<a href="https://wordpress.org/support/plugin/easy-wp-smtp/" target="_blank">%s</a>',
								esc_html( __( 'Support Forum', 'easy-wp-smtp' ) )
							)
						);
					?>
				</div>
			</div>
			<div class="postbox" style="min-width: inherit;">
				<h3 class="hndle"><label for="title"><?php esc_html_e( 'Rate Us', 'easy-wp-smtp' ); ?></label></h3>
				<div class="inside">
					<?php
						printf(
							esc_html(
								// translators: %s is replaced by rating link
								_x( 'Like the plugin? Please give us a %s', '%s is replaced by "rating" link', 'easy-wp-smtp' )
							),
							sprintf(
								'<a href="https://wordpress.org/support/plugin/easy-wp-smtp/reviews/#new-post" target="_blank">%s</a>',
								esc_html( __( 'rating', 'easy-wp-smtp' ) )
							)
						);
					?>
					<div class="swpsmtp-stars-container">
						<a href="https://wordpress.org/support/plugin/easy-wp-smtp/reviews/?filter=5" target="_blank"><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	echo '</div></div>'; //<!-- end of #poststuff and #post-body -->
	echo '</div>'; //<!--  end of .wrap #swpsmtp-mail .swpsmtp-mail -->
}
