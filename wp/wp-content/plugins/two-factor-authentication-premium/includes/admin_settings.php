<?php

if (!defined('ABSPATH')) die('Access denied.');

if (!is_admin() || !current_user_can($simba_two_factor_authentication->get_management_capability())) exit;

global $wp_roles;

$tfa->setUserHMACTypes();

?><div class="wrap">

	<div>

	<h1><?php echo sprintf(__('Two Factor Authentication (Version: %s) - Admin Settings', 'two-factor-authentication'), $simba_two_factor_authentication->version); ?> </h1>

	<?php
		if (!class_exists('Simba_Two_Factor_Authentication_Premium')) {
			?>
			<a href="https://www.simbahosting.co.uk/s3/product/two-factor-authentication/"><?php _e("Premium version", 'two-factor-authentication');?></a> | 
			<?php
		}
	?>
	<a href="<?php echo apply_filters('simba_tfa_support_url', 'https://wordpress.org/support/plugin/two-factor-authentication/');?>"><?php _e("Support", 'two-factor-authentication');?></a> | 
	<a href="https://profiles.wordpress.org/davidanderson#content-plugins"><?php _e('More free plugins', 'two-factor-authentication');?></a> | 
	<a href="http://updraftplus.com">UpdraftPlus - <?php _e('WordPress backups', 'two-factor-authentication'); ?></a> | 
		<a href="https://www.simbahosting.co.uk/s3/shop/"><?php _e('More premium plugins', 'two-factor-authentication');?></a>  | 
		<a href="https://twitter.com/updraftplus"><?php _e('Twitter', 'two-factor-authentication');?></a> | 
		
		<a href="http://david.dw-perspective.org.uk"><?php _e("Lead developer's homepage", 'two-factor-authentication');?></a> 
		<br>

	</div>

	<?php if (defined('TWO_FACTOR_DISABLE') && TWO_FACTOR_DISABLE) { ?>
	<div class="error">
		<h3><?php _e('Two Factor Authentication currently disabled', 'two-factor-authentication');?></h3>
		<p>
			<?php _e('Two factor authentication is currently disabled via the TWO_FACTOR_DISABLE constant (which is mostly likely to be defined in your wp-config.php)', 'two-factor-authentication'); ?>
		</p>
	</div>
	<?php } ?>

	<div style="max-width:800px;">

	<?php
		if (is_multisite()) {
			if (is_super_admin()) {
				?>
				<p style="font-size: 120%; font-weight: bold;">
				<?php _e('N.B. These two-factor settings apply to your entire WordPress network. (i.e. They are not localised to one particular site).', 'two-factor-authentication');?>
				</p>
				<?php
			} else {
				// Should not be possible to reach this; but an extra check does not hurt.
				die('Security check');
			}
		}
	?>

	<form method="post" action="options.php" style="margin-top: 12px">
		<?php settings_fields('tfa_user_roles_group'); ?>
		<h2><?php _e('User roles', 'two-factor-authentication'); ?></h2>
		<?php _e('Choose which user roles will have two factor authentication available.', 'two-factor-authentication'); ?>
		<p>
			<?php $simba_two_factor_authentication->list_user_roles_checkboxes(); ?>
		</p>
		<?php submit_button(); ?>
	</form>
	
	<hr>

	<h2><?php _e('Make two factor authentication compulsory', 'two-factor-authentication'); ?></h2>

	<?php

		$output = '<p><a href="https://www.simbahosting.co.uk/s3/product/two-factor-authentication/">'.__('Requiring users to use two-factor authentication is a feature of the Premium version of this plugin.', 'two-factor-authentication').'</a><p>';
		echo apply_filters('simba_tfa_after_user_roles', $output);

	?>

	<hr>
	<h2><?php _e('Trusted devices', 'two-factor-authentication'); ?></h2>

	<form method="post" action="options.php" style="margin-top: 12px">
		<?php settings_fields('tfa_user_roles_trusted_group'); ?>
		<?php _e('Choose which user roles are permitted to mark devices they login on as trusted. This feature requires browser cookies and an https (i.e. SSL) connection to the website to work.', 'two-factor-authentication'); ?>
		
		<?php
			$output = '<p><a href="https://www.simbahosting.co.uk/s3/product/two-factor-authentication/">'.__('Allowing users to mark a device as trusted so that a two-factor code is only needed once in a specified number of days (instead of every login) is a feature of the Premium version of this plugin.', 'two-factor-authentication').'</a><p>';
			echo apply_filters('simba_tfa_trusted_devices_config', $output);
		?>
		
	</form>	
	
	<div>
		<hr>
		<form method="post" action="options.php" style="margin-top: 40px">
		<?php
			settings_fields('tfa_xmlrpc_status_group');
		?>
			<h2><?php _e('XMLRPC requests', 'two-factor-authentication'); ?></h2>
			<?php 

			echo '<p>';
			echo __("XMLRPC is a feature within WordPress allowing other computers to talk to your WordPress install. For example, it could be used by an app on your tablet that allows you to blog directly from the app (instead of needing the WordPress dashboard).", 'two-factor-authentication');

			echo '<p></p>';

			echo __("Unfortunately, XMLRPC also provides a way for attackers to perform actions on your WordPress site, using only a password (i.e. without a two-factor password). More unfortunately, authors of legitimate programmes using XMLRPC have not yet added two-factor support to their code.", 'two-factor-authentication');

			echo '<p></p>';

			echo __(" i.e. XMLRPC requests coming in to WordPress (whether from a legitimate app, or from an attacker) can only be verified using the password - not with a two-factor code. As a result, there not be an ideal option to pick below. You may have to choose between the convenience of using your apps, or the security of two factor authentication.", 'two-factor-authentication');

			echo '</p>';
			?>
			<p>
			<?php
				$simba_two_factor_authentication->tfaListXMLRPCStatusRadios();
			?></p>
			<?php submit_button(); ?>
		</form>
	</div>
	
	<hr>
	<form method="post" action="options.php" style="margin-top: 40px">
	<?php
		settings_fields('simba_tfa_default_hmac_group');
	?>
		<h2><?php _e('Default algorithm', 'two-factor-authentication'); ?></h2>
		<?php _e('Your users can change this in their own settings if they want.', 'two-factor-authentication'); ?>
		<p>
		<?php
			$simba_two_factor_authentication->tfaListDefaultHMACRadios();
		?></p>
		<?php submit_button(); ?>
	</form>
	<hr>
	
	<?php
	if (function_exists('WC')) {
		
		?>
		<br><br>
		<h2><?php _e("WooCommerce integration", 'two-factor-authentication'); ?></h2>
		<p>
			<?php echo apply_filters('simba_tfa_settings_woocommerce', '<a href="https://www.simbahosting.co.uk/s3/product/two-factor-authentication/">'.__('The Premium version of this plugin allows you to add a configuration tab for users in the WooCommerce "My account" area.', 'two-factor-authentication').'</a>'); ?>
		</p>
		<hr>
	<?php } ?>
	
	<br><br>
	<h2><?php _e("Users' settings", 'two-factor-authentication'); ?></h2>
	<p>

		<?php
			if (!class_exists('Simba_Two_Factor_Authentication_Premium')) { ?>

				<a href="https://www.simbahosting.co.uk/s3/product/two-factor-authentication/"><?php _e("The Premium version of this plugin allows you to see and reset the TFA settings of other users.", 'two-factor-authentication'); ?></a>

				<a href="https://wordpress.org/plugins/user-switching/"><?php _e('Another way to do that is by using a user-switching plugin like this one.', 'two-factor-authentication'); ?></a>

			<?php } ?>
		
		<?php do_action('simba_tfa_users_settings'); ?>

		<?php
		
		// Disabled
		if (1==0) {
		// List users and type of tfa
		foreach ($wp_roles->role_names as $id => $name) {
			$setting = $simba_two_factor_authentication->get_option('tfa_'.$id);
			$setting = $setting === false || $setting ? 1 : 0;
			if(!$setting)
				continue;
			
			$users_q = new WP_User_Query( array(
			  'role' => $name
			));
			$users = $users_q->get_results();
			
			if(!$users)
				continue;
			
			print '<h3>'.$name.'s</h3>';
			
			foreach( $users as $user )
			{
				$userdata = get_userdata( $user->ID );
				$tfa_type = get_user_meta($user->ID, 'simbatfa_delivery_type', true);
				print '<span style="font-size: 1.2em">'.esc_attr( $userdata->user_nicename ).'</span>';
				if(!$tfa_type)
					print ' - '.__('Default', 'two-factor-authentication');
				else
					print ' - <a class="button" href="'.esc_url(add_query_arg(array('tfa_change_to_email' => 1, 'tfa_user_id' => $user->ID))).'">'.__('Change to email', 'two-factor-authentication').'</a>';
				print '<br>';
			}
		}
		}

		?>
	<hr>
	<?php if (!class_exists('Simba_Two_Factor_Authentication_Premium')) { ?>
	<h2><?php _e('Premium version', 'two-factor-authentication'); ?></h2>
	<p>
		<a href="https://www.simbahosting.co.uk/s3/product/two-factor-authentication/"><?php _e("If you want to say 'thank you' or help this plugin's development, or get extra features, then please take a look at the premium version of this plugin.", 'two-factor-authentication'); ?></a> <?php _e('It comes with these extra features:', 'two-factor-authentication');?><br>
	</p>
	<p>
		<ul style="list-style: disc inside;">
			<li><strong><?php _e('Emergency codes', 'two-factor-authentication');?></strong> - <?php _e('provide your users with one-time codes to use in case they lose their device.', 'two-factor-authentication');?></li>
			<li><strong><?php _e('Make TFA compulsory', 'two-factor-authentication');?></strong> - <?php _e('require your users to set up TFA to be able to log in, after an optional grace period.', 'two-factor-authentication');?></li>
			<li><strong><?php _e('Trusted devices', 'two-factor-authentication');?></strong> - <?php _e('allow privileged (or all) users to mark a device as trusted and thereby only needing to supply a TFA code upon login every so-many days (e.g. every 30 days) instead of on each login.', 'two-factor-authentication');?></li>
			<li><strong><?php _e('Manage all users centrally', 'two-factor-authentication');?></strong> - <?php _e('enable, disable or see TFA codes for all your users from one central location.', 'two-factor-authentication');?></li>
			<li><strong><?php _e('More shortcodes', 'two-factor-authentication');?></strong> - <?php _e('flexible shortcodes allowing you to design your front-end settings page for your users exactly as you wish.', 'two-factor-authentication');?></li>
			<li><strong><?php _e('Personal support', 'two-factor-authentication');?></strong> - <?php _e('access to our personal support desk for 12 months.', 'two-factor-authentication');?></li>
		</ul>
	</p>
	<hr>
	<?php } ?>

	<h2><?php _e('Translations', 'two-factor-authentication'); ?></h2>
	<p>
		<?php echo sprintf(__("If you want to translate this plugin, please go to %s.", 'two-factor-authentication'), '<a href="https://translate.wordpress.org/projects/wp-plugins/two-factor-authentication/">'.__('the wordpress.org translation website.', 'two-factor-authentication').'</a>').' '.__("Don't send us the translation file directly - plugin authors do not have access to the wordpress.org translation system (local language teams do).", 'two-factor-authentication'); ?>
		<br>
	</p>

</div>
</div>
