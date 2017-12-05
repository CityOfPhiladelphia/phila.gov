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
 * Renders the admin settings menu of the plugin.
 * @return void
 */
function swpsmtp_settings() {
	echo '<div class="wrap" id="swpsmtp-mail">';
	echo '<h2>' . __( "Easy WP SMTP Settings", 'easy-wp-smtp' ) . '</h2>';
	echo '<div id="poststuff"><div id="post-body">';

	$display_add_options = $message = $error = $result = '';

	$swpsmtp_options = get_option( 'swpsmtp_options' );
	$smtp_test_mail = get_option( 'smtp_test_mail' );
	$gag_password = '#easywpsmtpgagpass#';
	if ( empty( $smtp_test_mail ) ) {
		$smtp_test_mail = array( 'swpsmtp_to' => '', 'swpsmtp_subject' => '', 'swpsmtp_message' => '', );
	}

	if ( isset( $_POST['swpsmtp_form_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'swpsmtp_nonce_name' ) ) {
		/* Update settings */
		$swpsmtp_options['from_name_field'] = isset( $_POST['swpsmtp_from_name'] ) ? sanitize_text_field( wp_unslash( $_POST['swpsmtp_from_name'] ) ) : '';
		if ( isset( $_POST['swpsmtp_from_email'] ) ) {
			if ( is_email( $_POST['swpsmtp_from_email'] ) ) {
				$swpsmtp_options['from_email_field'] = sanitize_email( $_POST['swpsmtp_from_email'] );
			} else {
				$error .= " " . __( "Please enter a valid email address in the 'FROM' field.", 'easy-wp-smtp' );
			}
		}
		if ( isset( $_POST['swpsmtp_reply_to_email'] ) ) {
			$swpsmtp_options['reply_to_email'] = sanitize_email( $_POST['swpsmtp_reply_to_email'] );
		}

		if ( isset( $_POST['swpsmtp_email_ignore_list'] ) ) {
			$swpsmtp_options['email_ignore_list'] = sanitize_text_field( $_POST['swpsmtp_email_ignore_list'] );
		}

		$swpsmtp_options['smtp_settings']['host'] = sanitize_text_field( $_POST['swpsmtp_smtp_host'] );
		$swpsmtp_options['smtp_settings']['type_encryption'] = ( isset( $_POST['swpsmtp_smtp_type_encryption'] ) ) ? sanitize_text_field( $_POST['swpsmtp_smtp_type_encryption'] ) : 'none';
		$swpsmtp_options['smtp_settings']['autentication'] = ( isset( $_POST['swpsmtp_smtp_autentication'] ) ) ? sanitize_text_field( $_POST['swpsmtp_smtp_autentication'] ) : 'yes';
		$swpsmtp_options['smtp_settings']['username'] = sanitize_text_field( $_POST['swpsmtp_smtp_username'] );
		$smtp_password = $_POST['swpsmtp_smtp_password'];
		if ( $smtp_password !== $gag_password ) {
			$swpsmtp_options['smtp_settings']['password'] = base64_encode( $smtp_password );
		}
		$swpsmtp_options['smtp_settings']['enable_debug'] = isset( $_POST['swpsmtp_enable_debug'] ) ? 1 : false;
		$swpsmtp_options['enable_domain_check'] = isset( $_POST['swpsmtp_enable_domain_check'] ) ? 1 : false;
		if ( isset( $_POST['swpsmtp_allowed_domains'] ) ) {
			$swpsmtp_options['block_all_emails'] = isset( $_POST['swpsmtp_block_all_emails'] ) ? 1 : false;
			$swpsmtp_options['allowed_domains'] = base64_encode( sanitize_text_field( $_POST['swpsmtp_allowed_domains'] ) );
		} else if ( !isset( $swpsmtp_options['allowed_domains'] ) ) {
			$swpsmtp_options['allowed_domains'] = '';
		}

		/* Check value from "SMTP port" option */
		if ( isset( $_POST['swpsmtp_smtp_port'] ) ) {
			if ( empty( $_POST['swpsmtp_smtp_port'] ) || 1 > intval( $_POST['swpsmtp_smtp_port'] ) || (!preg_match( '/^\d+$/', $_POST['swpsmtp_smtp_port'] ) ) ) {
				$swpsmtp_options['smtp_settings']['port'] = '25';
				$error .= " " . __( "Please enter a valid port in the 'SMTP Port' field.", 'easy-wp-smtp' );
			} else {
				$swpsmtp_options['smtp_settings']['port'] = sanitize_text_field( $_POST['swpsmtp_smtp_port'] );
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
	if ( isset( $_POST['swpsmtp_test_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'swpsmtp_nonce_name' ) ) {
		if ( isset( $_POST['swpsmtp_to'] ) ) {
			$to_email = sanitize_text_field( $_POST['swpsmtp_to'] );
			if ( is_email( $to_email ) ) {
				$swpsmtp_to = $to_email;
			} else {
				$error .= __( "Please enter a valid email address in the recipient email field.", 'easy-wp-smtp' );
			}
		}
		$swpsmtp_subject = isset( $_POST['swpsmtp_subject'] ) ? sanitize_text_field( $_POST['swpsmtp_subject'] ) : '';
		$swpsmtp_message = isset( $_POST['swpsmtp_message'] ) ? sanitize_textarea_field( $_POST['swpsmtp_message'] ) : '';

		//Save the test mail details so it doesn't need to be filled in everytime.
		$smtp_test_mail['swpsmtp_to'] = $swpsmtp_to;
		$smtp_test_mail['swpsmtp_subject'] = $swpsmtp_subject;
		$smtp_test_mail['swpsmtp_message'] = $swpsmtp_message;
		update_option( 'smtp_test_mail', $smtp_test_mail );

		if ( !empty( $swpsmtp_to ) ) {
			$result = swpsmtp_test_mail( $swpsmtp_to, $swpsmtp_subject, $swpsmtp_message );
		}
	}
	?>
	<div class="swpsmtp-yellow-box">
		Please visit the <a target="_blank" href="https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197">Easy WP SMTP</a> plugin's documentation page for usage instructions.
	</div>

	<div class="updated fade" <?php if ( empty( $message ) ) echo "style=\"display:none\""; ?>>
		<p><strong><?php echo $message; ?></strong></p>
	</div>
	<div class="error" <?php if ( empty( $error ) ) echo "style=\"display:none\""; ?>>
		<p><strong><?php echo $error; ?></strong></p>
	</div>
	<div id="swpsmtp-settings-notice" class="updated fade" style="display:none">
		<p><strong><?php _e( "Notice:", 'easy-wp-smtp' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'easy-wp-smtp' ); ?></p>
	</div>
	<form id="swpsmtp_settings_form" method="post" action="">

		<div class="postbox">
			<h3 class="hndle"><label for="title"><?php _e( 'SMTP Configuration Settings', 'easy-wp-smtp' ); ?></label></h3>
			<div class="inside">

				<p>You can request your hosting provider for the SMTP details of your site. Use the SMTP details provided by your hosting provider to configure the following settings.</p>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( "From Email Address", 'easy-wp-smtp' ); ?></th>
						<td>
							<input type="text" name="swpsmtp_from_email" value="<?php echo isset( $swpsmtp_options['from_email_field'] ) ? esc_attr( $swpsmtp_options['from_email_field'] ) : ''; ?>"/><br />
							<p class="description"><?php _e( "This email address will be used in the 'From' field.", 'easy-wp-smtp' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( "From Name", 'easy-wp-smtp' ); ?></th>
						<td>
							<input type="text" name="swpsmtp_from_name" value="<?php echo isset( $swpsmtp_options['from_name_field'] ) ? esc_attr( $swpsmtp_options['from_name_field'] ) : ''; ?>"/><br />
							<p class="description"><?php _e( "This text will be used in the 'FROM' field", 'easy-wp-smtp' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( "Reply-To Email Address", 'easy-wp-smtp' ); ?></th>
						<td>
							<input type="email" name="swpsmtp_reply_to_email" value="<?php echo isset( $swpsmtp_options['reply_to_email'] ) ? esc_attr( $swpsmtp_options['reply_to_email'] ) : ''; ?>"/><br />
							<p class="description"><?php _e( "Optional. This email address will be used in the 'Reply-To' field of the email. Leave it blank to use 'From' email as the reply-to value.", 'easy-wp-smtp' ); ?></p>
						</td>
					</tr>
					<tr class="ad_opt swpsmtp_smtp_options">
						<th><?php _e( 'SMTP Host', 'easy-wp-smtp' ); ?></th>
						<td>
							<input type='text' name='swpsmtp_smtp_host' value='<?php echo isset( $swpsmtp_options['smtp_settings']['host'] ) ? esc_attr( $swpsmtp_options['smtp_settings']['host'] ) : ''; ?>' /><br />
							<p class="description"><?php _e( "Your mail server", 'easy-wp-smtp' ); ?></p>
						</td>
					</tr>
					<tr class="ad_opt swpsmtp_smtp_options">
						<th><?php _e( 'Type of Encryption', 'easy-wp-smtp' ); ?></th>
						<td>
							<label for="swpsmtp_smtp_type_encryption_1"><input type="radio" id="swpsmtp_smtp_type_encryption_1" name="swpsmtp_smtp_type_encryption" value='none' <?php if ( isset( $swpsmtp_options['smtp_settings']['type_encryption'] ) && 'none' == $swpsmtp_options['smtp_settings']['type_encryption'] ) echo 'checked="checked"'; ?> /> <?php _e( 'None', 'easy-wp-smtp' ); ?></label>
							<label for="swpsmtp_smtp_type_encryption_2"><input type="radio" id="swpsmtp_smtp_type_encryption_2" name="swpsmtp_smtp_type_encryption" value='ssl' <?php if ( isset( $swpsmtp_options['smtp_settings']['type_encryption'] ) && 'ssl' == $swpsmtp_options['smtp_settings']['type_encryption'] ) echo 'checked="checked"'; ?> /> <?php _e( 'SSL', 'easy-wp-smtp' ); ?></label>
							<label for="swpsmtp_smtp_type_encryption_3"><input type="radio" id="swpsmtp_smtp_type_encryption_3" name="swpsmtp_smtp_type_encryption" value='tls' <?php if ( isset( $swpsmtp_options['smtp_settings']['type_encryption'] ) && 'tls' == $swpsmtp_options['smtp_settings']['type_encryption'] ) echo 'checked="checked"'; ?> /> <?php _e( 'TLS', 'easy-wp-smtp' ); ?></label><br />
							<p class="description"><?php _e( "For most servers SSL is the recommended option", 'easy-wp-smtp' ); ?></p>
						</td>
					</tr>
					<tr class="ad_opt swpsmtp_smtp_options">
						<th><?php _e( 'SMTP Port', 'easy-wp-smtp' ); ?></th>
						<td>
							<input type='text' name='swpsmtp_smtp_port' value='<?php echo isset( $swpsmtp_options['smtp_settings']['port'] ) ? esc_attr( $swpsmtp_options['smtp_settings']['port'] ) : ''; ?>' /><br />
							<p class="description"><?php _e( "The port to your mail server", 'easy-wp-smtp' ); ?></p>
						</td>
					</tr>
					<tr class="ad_opt swpsmtp_smtp_options">
						<th><?php _e( 'SMTP Authentication', 'easy-wp-smtp' ); ?></th>
						<td>
							<label for="swpsmtp_smtp_autentication"><input type="radio" id="swpsmtp_smtp_autentication" name="swpsmtp_smtp_autentication" value='no' <?php if ( isset( $swpsmtp_options['smtp_settings']['autentication'] ) && 'no' == $swpsmtp_options['smtp_settings']['autentication'] ) echo 'checked="checked"'; ?> /> <?php _e( 'No', 'easy-wp-smtp' ); ?></label>
							<label for="swpsmtp_smtp_autentication"><input type="radio" id="swpsmtp_smtp_autentication" name="swpsmtp_smtp_autentication" value='yes' <?php if ( isset( $swpsmtp_options['smtp_settings']['autentication'] ) && 'yes' == $swpsmtp_options['smtp_settings']['autentication'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Yes', 'easy-wp-smtp' ); ?></label><br />
							<p class="description"><?php _e( "This options should always be checked 'Yes'", 'easy-wp-smtp' ); ?></p>
						</td>
					</tr>
					<tr class="ad_opt swpsmtp_smtp_options">
						<th><?php _e( 'SMTP username', 'easy-wp-smtp' ); ?></th>
						<td>
							<input type='text' name='swpsmtp_smtp_username' value='<?php echo isset( $swpsmtp_options['smtp_settings']['username'] ) ? esc_attr( $swpsmtp_options['smtp_settings']['username'] ) : ''; ?>' /><br />
							<p class="description"><?php _e( "The username to login to your mail server", 'easy-wp-smtp' ); ?></p>
						</td>
					</tr>
					<tr class="ad_opt swpsmtp_smtp_options">
						<th><?php _e( 'SMTP Password', 'easy-wp-smtp' ); ?></th>
						<td>
							<input type='password' name='swpsmtp_smtp_password' value='<?php echo (swpsmtp_get_password() !== '' ? $gag_password : ''); ?>' /><br />
							<p class="description"><?php _e( "The password to login to your mail server", 'easy-wp-smtp' ); ?></p>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" id="settings-form-submit" class="button-primary" value="<?php _e( 'Save Changes', 'easy-wp-smtp' ) ?>" />
				</p>
			</div><!-- end of inside -->
		</div><!-- end of postbox -->

		<div class="postbox">
			<h3 class="hndle"><label for="title"><?php _e( 'Additional Settings (Optional)', 'easy-wp-smtp' ); ?></label></h3>
			<div class="inside">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( "Don't Replace \"From\" Field", 'easy-wp-smtp' ); ?></th>
						<td>
							<input type="text" name="swpsmtp_email_ignore_list" value="<?php echo isset( $swpsmtp_options['email_ignore_list'] ) ? esc_attr( $swpsmtp_options['email_ignore_list'] ) : ''; ?>"/><br />
							<p class="description"><?php _e( "Comma separated emails list. Example value: email1@domain.com, email2@domain.com", "easy-wp-smtp" ); ?></p>
							<p class="description"><?php _e( "This option is useful when you are using several email aliases on your SMTP server. If you don't want your aliases to be replaced by the address specified in \"From\" field, enter them in this field.", 'easy-wp-smtp' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( "Enable Domain Check", 'easy-wp-smtp' ); ?></th>
						<td>
							<input type="checkbox" id="swpsmtp_enable_domain_check" name="swpsmtp_enable_domain_check" value="1"<?php echo (isset( $swpsmtp_options['enable_domain_check'] ) && ($swpsmtp_options['enable_domain_check'])) ? ' checked' : ''; ?>/>
							<p class="description"><?php _e( "This option is usually used by developers only. SMTP settings will be used only if the site is running on following domain(s):", 'easy-wp-smtp' ); ?></p>
							<input type="text" name="swpsmtp_allowed_domains" value="<?php echo base64_decode_maybe( $swpsmtp_options['allowed_domains'] ); ?>"<?php echo (isset( $swpsmtp_options['enable_domain_check'] ) && ($swpsmtp_options['enable_domain_check'])) ? '' : ' disabled'; ?>/>
							<p class="description"><?php _e( "Coma-separated domains list. Example: domain1.com, domain2.com", 'easy-wp-smtp' ); ?></p>
							<p>
								<label><input type="checkbox" id="swpsmtp_block_all_emails" name="swpsmtp_block_all_emails" value="1"<?php echo (isset( $swpsmtp_options['block_all_emails'] ) && ($swpsmtp_options['block_all_emails'])) ? ' checked' : ''; ?><?php echo (isset( $swpsmtp_options['enable_domain_check'] ) && ($swpsmtp_options['enable_domain_check'])) ? '' : ' disabled'; ?>/> Block all emails</label>
							</p>
							<p class="description"><?php _e( "When enabled, plugin attempts to block ALL emails from being sent out if domain mismtach." ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( "Enable Debug Log", 'easy-wp-smtp' ); ?></th>
						<td>
							<input type="checkbox" name="swpsmtp_enable_debug" value="1" <?php echo (isset( $swpsmtp_options['smtp_settings']['enable_debug'] ) && ($swpsmtp_options['smtp_settings']['enable_debug'])) ? 'checked' : ''; ?>/>
							<p class="description"><?php _e( "Check this box to enable mail debug log", 'easy-wp-smtp' ); ?></p>
							<a href="<?php echo admin_url(); ?>?swpsmtp_action=view_log" target="_blank"><?php _e( 'View Log', 'easy-wp-smtp' ); ?></a> | <a style="color: red;" id="swpsmtp_clear_log_btn" href="#0"><?php _e( 'Clear Log', 'easy-wp-smtp' ); ?></a>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" id="settings-form-submit" class="button-primary" value="<?php _e( 'Save Changes', 'easy-wp-smtp' ) ?>" />
					<input type="hidden" name="swpsmtp_form_submit" value="submit" />
					<?php wp_nonce_field( plugin_basename( __FILE__ ), 'swpsmtp_nonce_name' ); ?>
				</p>

				<script>
					jQuery(function ($) {
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
			</div><!-- end of inside -->
		</div><!-- end of postbox -->
	</form>
	<div class="postbox">
		<h3 class="hndle"><label for="title"><?php _e( 'Test Email', 'easy-wp-smtp' ); ?></label></h3>
		<div class="inside">

			<p>You can use this section to send an email from your server using the above configured SMTP details to see if the email gets delivered.</p>
			<p><b>Note</b>: debug log for this test email will be automatically displayed right after you send it. Test email also ignores "Enable Domain Check" option.</p>

			<form id="swpsmtp_settings_form" method="post" action="">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( "To", 'easy-wp-smtp' ); ?>:</th>
						<td>
							<input type="text" name="swpsmtp_to" value="<?php echo esc_html( $smtp_test_mail['swpsmtp_to'] ); ?>" /><br />
							<p class="description"><?php _e( "Enter the recipient's email address", 'easy-wp-smtp' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( "Subject", 'easy-wp-smtp' ); ?>:</th>
						<td>
							<input type="text" name="swpsmtp_subject" value="<?php echo esc_html( $smtp_test_mail['swpsmtp_subject'] ); ?>" /><br />
							<p class="description"><?php _e( "Enter a subject for your message", 'easy-wp-smtp' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( "Message", 'easy-wp-smtp' ); ?>:</th>
						<td>
							<textarea name="swpsmtp_message" id="swpsmtp_message" rows="5"><?php echo stripslashes( esc_textarea( $smtp_test_mail['swpsmtp_message'] ) ); ?></textarea><br />
							<p class="description"><?php _e( "Write your email message", 'easy-wp-smtp' ); ?></p>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" id="settings-form-submit" class="button-primary" value="<?php _e( 'Send Test Email', 'easy-wp-smtp' ) ?>" />
					<input type="hidden" name="swpsmtp_test_submit" value="submit" />
					<?php wp_nonce_field( plugin_basename( __FILE__ ), 'swpsmtp_nonce_name' ); ?>
				</p>
			</form>
		</div><!-- end of inside -->
	</div><!-- end of postbox -->

	<div class="swpsmtp-yellow-box">
		Visit the <a target="_blank" href="https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197">Easy WP SMTP</a> plugin's documentation page to learn how to use this plugin.
	</div>

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

	if ( isset( $_REQUEST['page'] ) && 'swpsmtp_settings' == $_REQUEST['page'] ) {
		/* register plugin settings */
		swpsmtp_register_settings();
	}
	add_action( 'wp_ajax_swpsmtp_clear_log', 'swpsmtp_clear_log' );
	//view log file
	if ( isset( $_GET['swpsmtp_action'] ) ) {
		if ( $_GET['swpsmtp_action'] === 'view_log' ) {
			$swpsmtp_options = get_option( 'swpsmtp_options' );
			$log_file_name = $swpsmtp_options['smtp_settings']['log_file_name'];
			if ( !file_exists( plugin_dir_path( __FILE__ ) . $log_file_name ) ) {
				if ( swpsmtp_write_to_log( "Easy WP SMTP debug log file\r\n\r\n" ) === false ) {
					wp_die( 'Can\'t write to log file. Check if plugin directory  (' . plugin_dir_path( __FILE__ ) . ') is writeable.' );
				};
			}
			$logfile = fopen( plugin_dir_path( __FILE__ ) . $log_file_name, 'rb' );
			if ( !$logfile ) {
				wp_die( 'Can\'t open log file.' );
			}
			header( 'Content-Type: text/plain' );
			fpassthru( $logfile );
			die;
		}
	}
}

/**
 * Register settings function
 * @return void
 */
function swpsmtp_register_settings() {
	$swpsmtp_options_default = array(
		'from_email_field' => '',
		'from_name_field' => '',
		'smtp_settings' => array(
			'host' => 'smtp.example.com',
			'type_encryption' => 'none',
			'port' => 25,
			'autentication' => 'yes',
			'username' => 'yourusername',
			'password' => 'yourpassword'
		)
	);

	/* install the default plugin options */
	if ( !get_option( 'swpsmtp_options' ) ) {
		add_option( 'swpsmtp_options', $swpsmtp_options_default, '', 'yes' );
	}
}
