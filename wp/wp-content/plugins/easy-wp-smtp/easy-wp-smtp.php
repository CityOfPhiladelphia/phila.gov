<?php
/*
Plugin Name: Easy WP SMTP
Version: 1.2.4
Plugin URI: https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197
Author: wpecommerce
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
if (!function_exists('swpsmtp_plugin_action_links')) {

    function swpsmtp_plugin_action_links($links, $file) {
        /* Static so we don't call plugin_basename on every plugin row. */
        static $this_plugin;
        if (!$this_plugin) {
            $this_plugin = plugin_basename(__FILE__);
        }
        if ($file == $this_plugin) {
            $settings_link = '<a href="options-general.php?page=swpsmtp_settings">' . __('Settings', 'easy-wp-smtp') . '</a>';
            array_unshift($links, $settings_link);
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
if (!function_exists('swpsmtp_register_plugin_links')) {

    function swpsmtp_register_plugin_links($links, $file) {
        $base = plugin_basename(__FILE__);
        if ($file == $base) {
            $links[] = '<a href="options-general.php?page=swpsmtp_settings">' . __('Settings', 'easy-wp-smtp') . '</a>';
        }
        return $links;
    }

}

//plugins_loaded action hook handler
if (!function_exists('swpsmtp_plugins_loaded_handler')) {

    function swpsmtp_plugins_loaded_handler() {
        load_plugin_textdomain('easy-wp-smtp', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

}


/**
 * Function to add plugin scripts
 * @return void
 */
if (!function_exists('swpsmtp_admin_head')) {

    function swpsmtp_admin_head() {
        wp_enqueue_style('swpsmtp_stylesheet', plugins_url('css/style.css', __FILE__));

        if (isset($_REQUEST['page']) && 'swpsmtp_settings' == $_REQUEST['page']) {
            wp_enqueue_script('swpsmtp_script', plugins_url('js/script.js', __FILE__), array('jquery'));
        }
    }

}

/**
 * Function to add smtp options in the phpmailer_init
 * @return void
 */
if (!function_exists('swpsmtp_init_smtp')) {

    function swpsmtp_init_smtp($phpmailer) {
        //check if SMTP credentials have been configured.
        if (!swpsmtp_credentials_configured()) {
            return;
        }
        $swpsmtp_options = get_option('swpsmtp_options');
        /* Set the mailer type as per config above, this overrides the already called isMail method */
        $phpmailer->IsSMTP();
        $from_email = $swpsmtp_options['from_email_field'];
        $phpmailer->From = $from_email;
        $from_name = $swpsmtp_options['from_name_field'];
        $phpmailer->FromName = $from_name;
        $phpmailer->SetFrom($phpmailer->From, $phpmailer->FromName);
        /* Set the SMTPSecure value */
        if ($swpsmtp_options['smtp_settings']['type_encryption'] !== 'none') {
            $phpmailer->SMTPSecure = $swpsmtp_options['smtp_settings']['type_encryption'];
        }

        /* Set the other options */
        $phpmailer->Host = $swpsmtp_options['smtp_settings']['host'];
        $phpmailer->Port = $swpsmtp_options['smtp_settings']['port'];

        /* If we're using smtp auth, set the username & password */
        if ('yes' == $swpsmtp_options['smtp_settings']['autentication']) {
            $phpmailer->SMTPAuth = true;
            $phpmailer->Username = $swpsmtp_options['smtp_settings']['username'];
            $phpmailer->Password = swpsmtp_get_password();
        }
        //PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate.
        $phpmailer->SMTPAutoTLS = false;
    }

}

/**
 * Function to test mail sending
 * @return text or errors
 */
if (!function_exists('swpsmtp_test_mail')) {

    function swpsmtp_test_mail($to_email, $subject, $message) {
        if (!swpsmtp_credentials_configured()) {
            return;
        }
        $errors = '';

        $swpsmtp_options = get_option('swpsmtp_options');

        require_once( ABSPATH . WPINC . '/class-phpmailer.php' );
        $mail = new PHPMailer();

        $charset = get_bloginfo('charset');
        $mail->CharSet = $charset;

        $from_name = $swpsmtp_options['from_name_field'];
        $from_email = $swpsmtp_options['from_email_field'];

        $mail->IsSMTP();

        /* If using smtp auth, set the username & password */
        if ('yes' == $swpsmtp_options['smtp_settings']['autentication']) {
            $mail->SMTPAuth = true;
            $mail->Username = $swpsmtp_options['smtp_settings']['username'];
            $mail->Password = swpsmtp_get_password();
        }

        /* Set the SMTPSecure value, if set to none, leave this blank */
        if ($swpsmtp_options['smtp_settings']['type_encryption'] !== 'none') {
            $mail->SMTPSecure = $swpsmtp_options['smtp_settings']['type_encryption'];
        }

        /* PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate. */
        $mail->SMTPAutoTLS = false;

        /* Set the other options */
        $mail->Host = $swpsmtp_options['smtp_settings']['host'];
        $mail->Port = $swpsmtp_options['smtp_settings']['port'];
        $mail->SetFrom($from_email, $from_name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->MsgHTML($message);
        $mail->AddAddress($to_email);
        $mail->SMTPDebug = 0;

        /* Send mail and return result */
        if (!$mail->Send())
            $errors = $mail->ErrorInfo;

        $mail->ClearAddresses();
        $mail->ClearAllRecipients();

        if (!empty($errors)) {
            return $errors;
        } else {
            return 'Test mail was sent';
        }
    }

}

if (!function_exists('swpsmtp_get_password')) {

    function swpsmtp_get_password() {
        $swpsmtp_options = get_option('swpsmtp_options');
        $temp_password = $swpsmtp_options['smtp_settings']['password'];
        $password = "";
        $decoded_pass = base64_decode($temp_password);
        /* no additional checks for servers that aren't configured with mbstring enabled */
        if (!function_exists('mb_detect_encoding')) {
            return $decoded_pass;
        }
        /* end of mbstring check */
        if (base64_encode($decoded_pass) === $temp_password) {  //it might be encoded
            if (false === mb_detect_encoding($decoded_pass)) {  //could not find character encoding.
                $password = $temp_password;
            } else {
                $password = base64_decode($temp_password);
            }
        } else { //not encoded
            $password = $temp_password;
        }
        return $password;
    }

}

if (!function_exists('swpsmtp_admin_notice')) {

    function swpsmtp_admin_notice() {
        if (!swpsmtp_credentials_configured()) {
            $settings_url = admin_url() . 'options-general.php?page=swpsmtp_settings';
            ?>
            <div class="error">
                <p><?php printf(__('Please configure your SMTP credentials in the <a href="%s">settings menu</a> in order to send email using Easy WP SMTP plugin.', 'easy-wp-smtp'), esc_url($settings_url)); ?></p>
            </div>
            <?php
        }
    }

}

if (!function_exists('swpsmtp_credentials_configured')) {

    function swpsmtp_credentials_configured() {
        $swpsmtp_options = get_option('swpsmtp_options');
        $credentials_configured = true;
        if (!isset($swpsmtp_options['from_email_field']) || empty($swpsmtp_options['from_email_field'])) {
            $credentials_configured = false;
        }
        if (!isset($swpsmtp_options['from_name_field']) || empty($swpsmtp_options['from_name_field'])) {
            $credentials_configured = false;
            ;
        }
        return $credentials_configured;
    }

}

/**
 * Performed at uninstal.
 * @return void
 */
if (!function_exists('swpsmtp_send_uninstall')) {

    function swpsmtp_send_uninstall() {
        /* delete plugin options */
        delete_site_option('swpsmtp_options');
        delete_option('swpsmtp_options');
    }

}

/**
 * Add all hooks
 */
add_filter('plugin_action_links', 'swpsmtp_plugin_action_links', 10, 2);
add_action('plugins_loaded', 'swpsmtp_plugins_loaded_handler');
add_filter('plugin_row_meta', 'swpsmtp_register_plugin_links', 10, 2);

add_action('phpmailer_init', 'swpsmtp_init_smtp');

add_action('admin_menu', 'swpsmtp_admin_default_setup');

add_action('admin_init', 'swpsmtp_admin_init');
add_action('admin_enqueue_scripts', 'swpsmtp_admin_head');
add_action('admin_notices', 'swpsmtp_admin_notice');

register_uninstall_hook(plugin_basename(__FILE__), 'swpsmtp_send_uninstall');