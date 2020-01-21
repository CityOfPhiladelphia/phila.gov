<?php

if (!defined('ABSPATH')) die('Access denied.');

global $current_user, $simba_two_factor_authentication, $wpdb;

if (!empty($_REQUEST['_tfa_activate_nonce']) && !empty($_POST['tfa_enable_tfa']) && wp_verify_nonce($_REQUEST['_tfa_activate_nonce'], 'tfa_activate') && !empty($_GET['settings-updated'])) {
	$tfa->changeEnableTFA($current_user->ID, $_POST['tfa_enable_tfa']);
	$tfa_settings_saved = true;
} elseif (!empty($_REQUEST['_tfa_algorithm_nonce']) && !empty($_POST['tfa_algorithm_type']) && !empty($_GET['settings-updated']) && wp_verify_nonce($_REQUEST['_tfa_algorithm_nonce'], 'tfa_algorithm')) {

	$old_algorithm = $tfa->getUserAlgorithm($current_user->ID);
	
	if ($old_algorithm != $_POST['tfa_algorithm_type']) {
		$tfa->changeUserAlgorithmTo($current_user->ID, $_POST['tfa_algorithm_type']);
	}

	$tfa_settings_saved = true;
}

if (isset($_GET['warning_button_clicked']) && 1 == $_GET['warning_button_clicked'] && !empty($_REQUEST['resyncnonce']) && wp_verify_nonce($_REQUEST['resyncnonce'], 'tfaresync')) {
	delete_user_meta($current_user->ID, 'tfa_hotp_off_sync');
}
?>
<style>
	#icon-tfa-plugin {
    	background: transparent url('<?php print plugin_dir_url(__FILE__); ?>img/tfa_admin_icon_32x32.png' ) no-repeat;
	}
	.inside > h3, .normal {
		cursor: default;
		margin-top: 20px;
	}
</style>
<div class="wrap">

	<h2><?php echo __('Two Factor Authentication', 'two-factor-authentication').' '.__('Settings', 'two-factor-authentication'); ?></h2>

	<?php

		if (isset($tfa_settings_saved)) {
			echo '<div class="updated notice is-dismissible">'."<p><strong>".__('Settings saved.', 'two-factor-authentication')."</strong></p></div>";
		}

		$simba_two_factor_authentication->settings_intro_notices();

	?>
	
	<!-- New Radios to enable/disable tfa -->
	<form method="post" action="<?php print esc_url(add_query_arg('settings-updated', 'true', $_SERVER['REQUEST_URI'])); ?>">
	
		<?php wp_nonce_field('tfa_activate', '_tfa_activate_nonce', false, true); ?>
		
		<h2><?php _e('Activate two factor authentication', 'two-factor-authentication'); ?></h2>
		<p>
			<?php
				$utc_date = gmdate('Y-m-d H:i:s');
				$date_now = get_date_from_gmt($utc_date, 'Y-m-d H:i:s');
				echo sprintf(__('N.B. Getting your TFA app/device to generate the correct code depends upon a) you first setting it up by entering or scanning the code below into it, and b) upon your web-server and your TFA app/device agreeing upon the UTC time (within a minute or so). The current UTC time according to the server when this page loaded: %s, and in the time-zone you have configured in your WordPress settings: %s', 'two-factor-authentication'), htmlspecialchars($utc_date), htmlspecialchars($date_now));
			?>
		</p>
		<p>
		<?php
			$simba_two_factor_authentication->tfaListEnableRadios($current_user->ID);
		?></p>
		<?php submit_button(); ?>
	</form>
	<?php
	
		$simba_two_factor_authentication->current_codes_box();

		$simba_two_factor_authentication->advanced_settings_box();

		do_action('simba_tfa_user_settings_after_advanced_settings');
		
	?>

</div>
