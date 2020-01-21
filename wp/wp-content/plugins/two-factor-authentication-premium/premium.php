<?php

if (!defined('ABSPATH')) die('Access denied.');

class Simba_Two_Factor_Authentication_Premium {

	private $tfa;
	private $frontend;

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_filter('simba_tfa_emergency_codes_user_settings', array($this, 'simba_tfa_emergency_codes_user_settings'), 10, 2);
		add_filter('simba_tfa_fetch_assort_vars', array($this, 'simba_tfa_fetch_assort_vars'), 10, 3);
		add_action('simba_tfa_adding_private_key', array($this, 'generate_emergency_codes'), 10, 4);
		add_action('simba_tfa_emergency_codes_empty', array($this, 'generate_emergency_codes'), 10, 4);
		add_action('simba_tfa_emergency_code_used', array($this, 'simba_tfa_emergency_code_used'), 10, 2);
		add_filter('simba_tfa_support_url', array($this, 'simba_tfa_support_url'));
		add_action('simba_tfa_users_settings', array($this, 'simba_tfa_users_settings'));
		add_filter('simba_tfa_trusted_devices_config', array($this, 'simba_tfa_trusted_devices_config'));
		add_filter('simba_tfa_user_can_trust', array($this, 'simba_tfa_user_can_trust'), 10, 3);
		add_action('wp_ajax_simbatfa_choose_user', array($this, 'wp_ajax_simbatfa_choose_user'));
		add_action('wp_ajax_simbatfa_user_get_codes', array($this, 'wp_ajax_simbatfa_user_get_codes'));
		add_action('wp_ajax_simbatfa_user_activation', array($this, 'wp_ajax_simbatfa_user_activation'));
		add_action('wp_ajax_simbatfa_user_privkey_reset', array($this, 'wp_ajax_simbatfa_user_privkey_reset'));
		add_filter('simba_tfa_after_user_roles', array($this, 'simba_tfa_after_user_roles'));
		add_filter('simba_tfa_tfa_from_password', array($this, 'simba_tfa_tfa_from_password'), 10, 2);
		add_action('simba_tfa_user_settings_after_advanced_settings', array($this, 'user_settings_after_advanced_settings'));
		add_action('simba_tfa_untrust_device', array($this, 'simba_tfa_untrust_device'));
		add_action('all_admin_notices', array($this, 'all_admin_notices'));
		add_action('admin_scripts', array($this, 'admin_scripts'), 11);
		add_action('plugins_loaded', array($this, 'plugins_loaded'));
		
		add_shortcode('twofactor_user_settings_enabled', array($this, 'shortcode_twofactor_user_settings_enabled'));
		add_shortcode('twofactor_user_qrcode', array($this, 'shortcode_twofactor_user_qrcode'));
		add_shortcode('twofactor_user_emergencycodes', array($this, 'shortcode_twofactor_user_emergencycodes'));
		add_shortcode('twofactor_user_advancedsettings', array($this, 'shortcode_twofactor_user_advancedsettings'));
		add_shortcode('twofactor_user_privatekeys', array($this, 'shortcode_twofactor_user_privatekeys'));
		add_shortcode('twofactor_user_privatekeys_reset', array($this, 'shortcode_twofactor_user_privatekeys_reset'));
		add_shortcode('twofactor_user_currentcode', array($this, 'shortcode_twofactor_user_currentcode'));
		add_shortcode('twofactor_user_presstorefresh', array($this, 'shortcode_twofactor_user_presstorefresh'));
		add_shortcode('twofactor_conditional', array($this, 'shortcode_twofactor_conditional'));
	}

	public function simba_tfa_user_can_trust($can_trust, $user_id, $tfa) {
		return $tfa->user_property_active($user_id, 'trusted_');
	}
	
	/**
	 * Support appending the TFA code to the password
	 *
	 * @param Boolean|Array $from_password
	 * @param String		$password
	 *
	 * @return Boolean|Array
	 */
	public function simba_tfa_tfa_from_password($from_password, $password) {
		if (!is_array($from_password) && preg_match('/([0-9]{6})$/', $password)) {
			return array('password' => substr($password, 0, strlen($password)-6), 'tfa_code' => substr($password, -6));
		}
		return $from_password;
	}
	
	public function simba_tfa_untrust_device($device_id) {
		
		global $simba_two_factor_authentication;

		$tfa = $simba_two_factor_authentication->getTFA();
		
		global $current_user;
		
		$trusted_devices = $tfa->user_get_trusted_devices($current_user->ID);
		
		$device_id = $_POST['device_id'];
		
		unset($trusted_devices[$device_id]);
		
		$tfa->user_set_trusted_devices($current_user->ID, $trusted_devices);
	
		ob_start();
		
		$this->trusted_devices_inner_box();
	
		echo json_encode(array('trusted_list' => ob_get_clean()));
	}
	
	/**
	 * Runs upon the WP action simba_tfa_user_settings_after_advanced_settings
	 */
	public function user_settings_after_advanced_settings() {
	
		global $current_user;
	
		?>
		
		<h2 style="clear:both;"><?php _e('Trusted devices', 'two-factor-authentication'); ?></h2>

		<div id="tfa_trusted_devices_box" class="tfa_settings_form" style="margin-top: 20px;">
		
			<?php $this->trusted_devices_inner_box(); ?>
	
		</div>
		
		<?php
	}
	
	public function trusted_devices_inner_box() {
		?>
		
			<div id="tfa_trusted_devices_box_inner">
		
			<p><?php _e('Trusted devices are devices which have previously logged in with a second factor, belonging to users who have been permitted to mark devices as trusted, and for which the user checked the checkbox on the login form to trust the device.', 'two-factor-authentication'); ?></p>
		
			<?php
			
			global $simba_two_factor_authentication;

			$tfa = $simba_two_factor_authentication->getTFA();
			
			global $current_user;
			
			$trusted_devices = $tfa->user_get_trusted_devices($current_user->ID);
			
			if (empty($trusted_devices)) {
				echo '<em>'.__('(none)', 'two-factor-authentication').'</em>';
			}
			
			foreach ($trusted_devices as $device_id => $device) {
			
				if (!isset($device['token']) || '' == $device['token']) continue;
				
				$user_agent = empty($device['user_agent']) ? __('(unspecified)', 'two-factor-authentication'): $device['user_agent'];
				
				echo '<span class="simbatfa_trusted_device">'.sprintf(__('User agent %s logged in from IP address %s and is trusted until %s', 'two-factor-authentication'), '<strong>'.htmlspecialchars($user_agent).'</strong>', '<strong><a target="_blank" href="https://ipinfo.io/'.$device['ip'].'">'.htmlspecialchars($device['ip']).'</a></strong>', '<strong>'.date_i18n(get_option('time_format').' '.get_option('date_format'), $device['until']).'</strong>').' - <a href="#" class="simbatfa-trust-remove" data-trusted-device-id="'.esc_attr($device_id).'">'.__('Remove trust', 'two-factor-authentication').'</a></span><br>';
			
			}
			
			?>
			
			</div>
		
		<?php
	
	}
	
	public function simba_tfa_trusted_devices_config() {
		ob_start();
		echo '<p>';
		global $simba_two_factor_authentication;
		$simba_two_factor_authentication->list_user_roles_checkboxes('trusted_');
		
		$trusted_for = $simba_two_factor_authentication->get_option('tfa_trusted_for');
		$trusted_for = (false === $trusted_for) ? 30 : (string) absint($trusted_for);

		echo '<p>'.sprintf(__("When a device is trusted, don't require a two-factor code for another %s days", 'two-factor-authentication'), '<input type="number" style="width:60px;" step="1" min="0" name="tfa_trusted_for" id="tfa_trusted_for" value="'.$trusted_for.'">').'</p>';
		
		echo '</p>';

		submit_button();
		
		return ob_get_clean();
	}
	
	/**
	 * Runs upon the WP filter simba_tfa_support_url
	 *
	 * @param String $url - pre-filter value
	 *
	 * @return String - filtered value
	 */
	public function simba_tfa_support_url($url) {
		return 'https://www.simbahosting.co.uk/s3/support/tickets/';
	}

	public function plugins_loaded() {
		global $simba_two_factor_authentication;
		// WP-Members support
		add_action('login_form', array($simba_two_factor_authentication, 'login_enqueue_scripts'));
	}
	
	public function simba_tfa_after_user_roles($default) {

		global $simba_two_factor_authentication;

		$ret = '';
		$ret .= '<form method="post" action="options.php" style="margin-top: 12px">';
			
// 			settings_fields('tfa_user_roles_required_group');
		$ret .= "<input type='hidden' name='option_page' value='tfa_user_roles_required_group' />";
		$ret .= '<input type="hidden" name="action" value="update" />';
		$ret .= wp_nonce_field("tfa_user_roles_required_group-options", '_wpnonce', true, false);


		$ret .= __('Choose which user roles are required to have two-factor authentication active (remember to also make it available for any chosen roles).', 'two-factor-authentication');
		$ret .= '<p>';

		if (is_multisite()) {
			// Not a real WP role; needs separate handling
			$id = '_super_admin';
			$name = __('Multisite Super Admin', 'two-factor-authentication');
			$setting = (bool)$simba_two_factor_authentication->get_option('tfa_required_'.$id);
			
			$ret .= '<input type="checkbox" id="tfa_required_'.$id.'" name="tfa_required_'.$id.'" value="1" '.($setting ? 'checked="checked"' :'').'> <label for="tfa_required_'.$id.'">'.htmlspecialchars($name)."</label><br>\n";
		}

		global $wp_roles;
		if (!isset($wp_roles)) $wp_roles = new WP_Roles();
		
		foreach($wp_roles->role_names as $id => $name) {	
			$setting = (bool)$simba_two_factor_authentication->get_option('tfa_required_'.$id);
			
			$ret .= '<input type="checkbox" id="tfa_required_'.$id.'" name="tfa_required_'.$id.'" value="1" '.($setting ? 'checked="checked"' :'').'> <label for="tfa_required_'.$id.'">'.htmlspecialchars($name)."</label><br>\n";
		}

		$ret .= '</p><p>';

		$requireafter = $simba_two_factor_authentication->get_option('tfa_requireafter');
		if (false === $requireafter) {
			$requireafter = 10;
		} else {
			$requireafter = absint($requireafter);
		}

		$ret .= sprintf(__('Enforce this requirement only for accounts at least %s days old', 'two-factor-authentication'), '<input type="number" style="width:60px;" step="1" min="0" name="tfa_requireafter" id="tfa_requireafter" value="'.$requireafter.'">').'<br>'.__('(If you are setting up for the first time and have pre-existing users, then you should tell them that they need to set up TFA before a certain date, and then turn this feature on at that date).', 'two-factor-authentication');
		
		$hide_turn_off = $simba_two_factor_authentication->get_option('tfa_hide_turn_off');
		
		$ret .= '<br>'.'<input type="checkbox" '.($hide_turn_off ? 'checked="checked" ' : '').' name="tfa_hide_turn_off" id="tfa_hide_turn_off" value="1"><label for="tfa_hide_turn_off">'.__('For these users, hide the option to turn TFA off', 'two-factor-authentication').'</label>';

		$ret .= '</p>'.get_submit_button().'</form>';

		return $ret;

	}

	public function wp_ajax_simbatfa_user_get_codes() {
		if (empty($_REQUEST['userid']) || !is_numeric($_REQUEST['userid']) || empty($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'simbatfa_user_get_codes')) die('Security check (4).');

		if (!current_user_can('edit_users')) die('Security check (10).');

		global $simba_two_factor_authentication;

		$tfa = $simba_two_factor_authentication->getTFA();

		if (!$tfa->isActivatedForUser($_REQUEST['userid'])){
			echo  '<p><em>'.__('Two factor authentication is not available for this user.', 'two-factor-authentication').'</em></p>';;
		} else {
			if (!$tfa->isActivatedByUser($_REQUEST['userid'])) {
				echo '<p><em>'.__('Two factor authentication is not activated for this user.', 'two-factor-authentication').'</em></p>';
			} else {
				$simba_two_factor_authentication->current_codes_box(true, $_REQUEST['userid']);
			}
		}

		exit;
	}

	/**
	 * Runs upon the WP action wp_ajax_simbatfa_user_activation
	 */
	public function wp_ajax_simbatfa_user_activation() {
	
		if (empty($_REQUEST['userid']) || !is_numeric($_REQUEST['userid']) || empty($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'simbatfa_user_activation')) die('Security check (5).');

		if (!current_user_can('edit_users')) die('Security check (9).');
		
		global $simba_two_factor_authentication;

		$tfa = $simba_two_factor_authentication->getTFA();

		if (!$tfa->isActivatedForUser($_REQUEST['userid'])){
			echo  '<p><em>'.__('Two factor authentication is not available for this user.', 'two-factor-authentication').'</em></p>';
		} else {
			$activate_or_not = empty($_REQUEST['activate']) ? false : true;

			// TFA:changeEnableTFA() just checks on whether the parameter is (string)'true' or not.
			$activate_string = $activate_or_not ? 'true' : 'no';

			$tfa->changeEnableTFA($_REQUEST['userid'], $activate_string);

			if ($activate_or_not) {
				echo  '<p><em>'.__('Two factor authentication has been activated for this user.', 'two-factor-authentication').'</em></p>';
			} else {
				echo  '<p><em>'.__('Two factor authentication has been de-activated for this user.', 'two-factor-authentication').'</em></p>';
			}
		}
		exit;
	}
	
	public function wp_ajax_simbatfa_user_privkey_reset() {
		if (empty($_REQUEST['user_id']) || !is_numeric($_REQUEST['user_id']) || empty($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'simbatfa_user_privkey_reset')) die('Security check (7).');

		if (!current_user_can('edit_users')) die('Security check (8).');
		
		$user_id = $_REQUEST['user_id'];
		
		global $simba_two_factor_authentication;

		$tfa = $simba_two_factor_authentication->getTFA();

		if (!$tfa->isActivatedForUser($user_id)){
			echo  '<p><em>'.__('Two factor authentication is not available for this user.', 'two-factor-authentication').'</em></p>';;
		} else {
			if (!$tfa->isActivatedByUser($user_id)) {
				echo '<p><em>'.__('Two factor authentication is not activated for this user.', 'two-factor-authentication').'</em></p>';
			} else {
				$simba_two_factor_authentication->reset_private_key_and_emergency_codes($user_id, false);
				$simba_two_factor_authentication->current_codes_box(true, $user_id);
			}
		}
		
		exit;
	}

	/**
	 * Called upon the WP action wp_ajax_simbatfa_choose_user
	 */
	public function wp_ajax_simbatfa_choose_user() {
		if (empty($_REQUEST['q']) || empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'simbatfa-choose-user')) die('Security check (6).');

		if (!current_user_can('edit_users')) die('Security check (11).');
		
		// https://codex.wordpress.org/Class_Reference/WP_User_Query

		$args = array(
			'search' => '*'.stripslashes($_REQUEST['q']).'*',
			'fields' => array('ID', 'user_login', 'user_email', 'user_nicename'),
			'search_columns' => array('user_login', 'user_email')
		);

		// Search all blogs on a multisite
		if (is_multisite()) $args['blog_id'] = 0;
		
		$res = array();

		$user_query = new WP_User_Query($args);

		if (!empty($user_query->results)) {
			foreach ($user_query->results as $user) {
				$res[] = array(
					'id' => $user->ID,
					'text' => sprintf("%s - %s (%s)", $user->user_nicename, $user->user_login, $user->user_email),
				);
			}
		}

		$results = json_encode(array('results' => $res));

		echo $results;
		die;
	}

	public function simba_tfa_users_settings() {
		$suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
		wp_deregister_script('select2');
		wp_register_script('select2', SIMBA_TFA_PLUGIN_URL . '/includes/select2'.$suffix.'.js', array('jquery'), '4.0.2');
		wp_enqueue_script('select2');
		wp_enqueue_style('select2', SIMBA_TFA_PLUGIN_URL . '/includes/select2.css', array(), '4.0.2');
		add_action('admin_footer', array($this, 'admin_footer_select2'));
		?>
		<div class="simba_tfa_users">
			<p>
				<h3><?php _e('Show codes for a particular user', 'two-factor-authentication');?></h3>
				<select class="simba_tfa_choose_user" style="width: 240px;">
				</select>
				<button class="simba_tfa_user_get_codes button button-primary"><?php _e('Get codes', 'two-factor-authentication');?></button>
				<button class="simba_tfa_user_deactivate button button-primary"><?php _e('De-activate TFA', 'two-factor-authentication');?></button>
				<button class="simba_tfa_user_activate button button-primary"><?php _e('Activate TFA', 'two-factor-authentication');?></button>
			</p>
			<p class="simba_tfa_user_results">
			</p>
		</div>
		<?php
		// Enqueue jquery qrcode
		global $simba_two_factor_authentication;
		$simba_two_factor_authentication->add_footer(true);
		/*
		<button class="simba_tfa_user_reset button button-primary"><?php _e('Reset', 'two-factor-authentication');?></button>
		*/
	}

	public function admin_footer_select2() {
		?>
		<script>
			jQuery(document).ready(function($) {
				$('.simba_tfa_user_get_codes').click(function(e) {
					e.preventDefault();
					var $area = $(this);
					var whichuser = $(this).siblings('.simba_tfa_choose_user').val();
					if (null == whichuser || '' == whichuser) {
						alert('<?php echo esc_js(__('You must first choose a valid user.', 'two-factor-authentication'));?>');
						return;
					};
					$.post(ajaxurl, {
						action: "simbatfa_user_get_codes",
						userid: whichuser,
						nonce: "<?php echo wp_create_nonce("simbatfa_user_get_codes");?>"
					}, function(response) {
						$area.parents('.simba_tfa_users').find('.simba_tfa_user_results').html(response);
						$('.simba_tfa_user_results .simbaotp_qr_container').qrcode({
							"render": "image",
							"text": $('.simbaotp_qr_container:first').data('qrcode'),
						});
					});
				});
				$('.simba_tfa_user_deactivate').click(function(e) {
					e.preventDefault();
					var $area = $(this);
					var whichuser = $(this).siblings('.simba_tfa_choose_user').val();
					if (null == whichuser || '' == whichuser) {
						alert('<?php echo esc_js(__('You must first choose a valid user.', 'two-factor-authentication'));?>');
						return;
					};
					$.post(ajaxurl, {
						action: "simbatfa_user_activation",
						userid: whichuser,
						activate: 0,
						nonce: "<?php echo wp_create_nonce("simbatfa_user_activation");?>"
					}, function(response) {
						$area.parents('.simba_tfa_users').find('.simba_tfa_user_results').html(response);
					});
				});
				$('.simba_tfa_user_results').on('click', '#tfa-reset-privkey-for-user', function(e) {
					e.preventDefault();
					if (!confirm('<?php echo esc_attr('Warning: if you reset this key then the user will have to update his apps with the new one. Are you sure you want this?', 'two-factor-authentication');?>')) { return; }
					var user_id = $(this).data('user_id');
					var $area = $(this);
					if (!user_id) {
						console.log("TFA: Error: user_id not found for privkey reset click");
						return;
					}
					$.post(ajaxurl, {
						action: "simbatfa_user_privkey_reset",
						user_id: user_id,
						nonce: "<?php echo wp_create_nonce("simbatfa_user_privkey_reset");?>"
					}, function(response) {
// 						$area.parents('.simba_tfa_users').find('.simba_tfa_user_results').html(response);
						$area.parents('.simba_tfa_users').find('.simba_tfa_user_get_codes').click();
					});
				});
				$('.simba_tfa_user_activate').click(function(e) {
					e.preventDefault();
					var $area = $(this);
					var whichuser = $(this).siblings('.simba_tfa_choose_user').val();
					if (null == whichuser || '' == whichuser) {
						alert('<?php echo esc_js(__('You must first choose a valid user.', 'two-factor-authentication'));?>');
						return;
					};
					$.post(ajaxurl, {
						action: "simbatfa_user_activation",
						userid: whichuser,
						activate: 1,
						nonce: "<?php echo wp_create_nonce("simbatfa_user_activation");?>"
					}, function(response) {
						$area.parents('.simba_tfa_users').find('.simba_tfa_user_results').html(response);
					});
				});
				$('.simba_tfa_choose_user').select2({
					 ajax: {
						url: "<?php echo addslashes(admin_url('admin-ajax.php?action=simbatfa_choose_user&_wpnonce=').wp_create_nonce('simbatfa-choose-user')) ; ?>",
						dataType: 'json',
						delay: 250,
						data: function (params) {
							return {
								q: params.term, // search term
								page: params.page
							};
						},
						processResults: function (data) {
							return data;
						},
						cache: true
					},
					
// 					escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
					minimumInputLength: 2,
// 					templateResult: formatRepo, // omitted for brevity, see the source of this page
// 					templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
				});
			});
		</script>
		<?php
	}


	public function all_admin_notices() {
		// Test for whether they're require to have TFA active and haven't yet done so.
		
		global $current_user, $simba_two_factor_authentication;
		if (empty($this->tfa)) $this->tfa = $simba_two_factor_authentication->getTFA();
		if ($this->tfa->isActivatedForUser($current_user->ID) && $this->tfa->isRequiredForUser($current_user->ID) && !$this->tfa->isActivatedByUser($current_user->ID)) {
			$simba_two_factor_authentication->show_admin_warning('<strong>'.__('Please set up two-factor authentication', 'two-factor-authentication').'</strong><br> <a href="'.admin_url('admin.php').'?page=two-factor-auth-user">'.__('You will need to set up and use two-factor authentication to login in future.</a>', 'two-factor-authentication'), 'error');
		}
	}


	// This function is intended for use by third party developers
	public function tfa_is_available_and_active() {
		global $current_user, $simba_two_factor_authentication;
		if (empty($this->tfa)) $this->tfa = $simba_two_factor_authentication->getTFA();
		return ($this->tfa->isActivatedForUser($current_user->ID) && $this->tfa->isActivatedByUser($current_user->ID)) ? true : false;
	}

	public function shortcode_twofactor_conditional($atts, $content = null) {

		global $current_user, $simba_two_factor_authentication;

		// Valid: available, unavailable, active, inactive (which implies available)
		$atts = shortcode_atts( array(
			'onlyif' => 'active'
		), $atts );

		if (!in_array($atts['onlyif'], array('active', 'inactive', 'available', 'unavailable'))) return '(twofactor_conditional: Unrecognised value for the "onlyif" parameter)';

		if (empty($this->tfa)) $this->tfa = $simba_two_factor_authentication->getTFA();

		$condition = $atts['onlyif'];
		$condition_fulfilled = false;

		if ($this->tfa->isActivatedForUser($current_user->ID)){
			if ('available' == $condition) {
				$condition_fulfilled = true;
			} elseif ('inactive' == $condition && !$this->tfa->isActivatedByUser($current_user->ID)) {
				$condition_fulfilled = true;
			} elseif ('active' == $condition  && $this->tfa->isActivatedByUser($current_user->ID)) {
				$condition_fulfilled = true;
			}
		} elseif ('unavailable' == $condition) {
			$condition_fulfilled = true;
		}

		return ($condition_fulfilled) ? do_shortcode($content) : '';

	}

	public function shortcode_twofactor_user_presstorefresh($atts, $content = null) {
		global $simba_two_factor_authentication, $current_user;

		if (empty($this->tfa)) $this->tfa = $simba_two_factor_authentication->getTFA();

		if (!$this->tfa->isActivatedForUser($current_user->ID)){
			return __('Two factor authentication is not available for your user.', 'two-factor-authentication');
		} else {
			$simba_two_factor_authentication->add_footer(false);
			return '<span class="simbaotp_refresh">'.do_shortcode($content).'</span>';
		}
	}

	public function shortcode_twofactor_user_currentcode($atts, $content = null) {
		global $simba_two_factor_authentication, $current_user;

		if (empty($this->tfa)) $this->tfa = $simba_two_factor_authentication->getTFA();

		if (!$this->tfa->isActivatedForUser($current_user->ID)){
			return __('Two factor authentication is not available for your user.', 'two-factor-authentication');
		} else {
			return $simba_two_factor_authentication->current_otp_code($this->tfa);
		}

	}

	public function shortcode_twofactor_user_privatekeys($atts, $content = null) {
		global $simba_two_factor_authentication, $current_user;

		// Valid: full, plain, base32, base64
		$atts = shortcode_atts( array(
			'type' => 'full'
		), $atts );

		if (empty($this->tfa)) $this->tfa = $simba_two_factor_authentication->getTFA();

		if (!$this->tfa->isActivatedForUser($current_user->ID)){
			return __('Two factor authentication is not available for your user.', 'two-factor-authentication');
		} else {
			ob_start();
			$simba_two_factor_authentication->print_private_keys(false, $atts['type']);
			return ob_get_clean();
		}
	}

	public function shortcode_twofactor_user_privatekeys_reset($atts, $content = null) {
		global $simba_two_factor_authentication, $current_user;

		if (empty($this->tfa)) $this->tfa = $simba_two_factor_authentication->getTFA();

		if (!$this->tfa->isActivatedForUser($current_user->ID)){
			return __('Two factor authentication is not available for your user.', 'two-factor-authentication');
		} else {
			return $simba_two_factor_authentication->reset_link(false);
		}
	}

	public function shortcode_twofactor_user_advancedsettings($atts, $content = null) {
		global $simba_two_factor_authentication, $current_user;

		if (empty($this->tfa)) $this->tfa = $simba_two_factor_authentication->getTFA();

		if (!$this->tfa->isActivatedForUser($current_user->ID)){
			return __('Two factor authentication is not available for your user.', 'two-factor-authentication');
		} else {
			ob_start();
			$simba_two_factor_authentication->advanced_settings_box(array($this, 'save_settings_button'));
			$simba_two_factor_authentication->load_frontend()->save_settings_javascript_output();
			return ob_get_clean();
			
		}
	}

	public function save_settings_button() {
		echo '<button style="margin-left: 4px;margin-bottom: 10px" class="simbatfa_settings_save button button-primary">'.__('Save Settings', 'two-factor-authentication').'</button>';
	}

	public function shortcode_twofactor_user_emergencycodes($atts, $content = null) {
		global $simba_two_factor_authentication, $current_user;

		if (empty($this->tfa)) $this->tfa = $simba_two_factor_authentication->getTFA();

		if (!$this->tfa->isActivatedForUser($current_user->ID)){
			return __('Two factor authentication is not available for your user.', 'two-factor-authentication');
		} else {
			return $this->get_emergency_codes_as_string($current_user->ID, true);
		}
		
	}

	public function shortcode_twofactor_user_qrcode($atts, $content = null) {

		global $simba_two_factor_authentication, $current_user;

		if (empty($this->tfa)) $this->tfa = $simba_two_factor_authentication->getTFA();

		$simba_two_factor_authentication->add_footer(false);
		
		ob_start();

		if (!$this->tfa->isActivatedForUser($current_user->ID)){
			echo __('Two factor authentication is not available for your user.', 'two-factor-authentication');
		} else {

			$url = preg_replace('/^https?:\/\//', '', site_url());
			
			$tfa_priv_key_64 = get_user_meta($current_user->ID, 'tfa_priv_key_64', true);
			
			if (!$tfa_priv_key_64) $tfa_priv_key_64 = $this->tfa->addPrivateKey($current_user->ID);

			$tfa_priv_key = trim($this->tfa->getPrivateKeyPlain($tfa_priv_key_64, $current_user->ID), "\x00..\x1F");

			$tfa_priv_key_32 = Base32::encode($tfa_priv_key);

			$algorithm_type = $this->tfa->getUserAlgorithm($current_user->ID);

			?>

			<p title="<?php echo sprintf(__("Private key: %s (base 32: %s)", 'two-factor-authentication'), $tfa_priv_key, $tfa_priv_key_32);?>">
				<?php $qr_url = $simba_two_factor_authentication->tfa_qr_code_url($algorithm_type, $url, $tfa_priv_key) ?>
				<div class="simbaotp_qr_container" data-qrcode="<?php echo esc_attr($qr_url); ?>"></div>
			</p>

			<?php
		}

		return ob_get_clean();
		
	}

	/**
	 * Called by the twofactor_user_settings_enabled shortcode
	 *
	 * @param Array $atts - shortcode attributes
	 * @param String|Null $content
	 *
	 * @return String - shortcode output
	 */
	public function shortcode_twofactor_user_settings_enabled($atts, $content = null) {

		global $simba_two_factor_authentication, $current_user;
		
		// Valid: show_current | require_current
		$atts = shortcode_atts(array(
			'style' => 'show_current'
		), $atts);

		if (empty($this->tfa)) $this->tfa = $simba_two_factor_authentication->getTFA();

		ob_start();

		if (!$this->tfa->isActivatedForUser($current_user->ID)){
			echo __('Two factor authentication is not available for your user.', 'two-factor-authentication');
		} else {
			$simba_two_factor_authentication->load_frontend()->settings_enable_or_disable_output($atts['style']);
		}

		return ob_get_clean();

	}

	// Let the user know that an emergency code was used, and that they may need to generate some more.
	public function simba_tfa_emergency_code_used($user_id, $emergency_codes) {

		$extra = empty($emergency_codes) ? "\r\n".__('Your must now go to the Two Factor Authentication settings to generate some new emergency codes if you wish to use any emergency codes in future.', 'two-factor-authentication') : '';

		$user = get_userdata($user_id);
		if (!is_object($user) || empty($user->user_email)) return;
		wp_mail(
			$user->user_email,
			home_url().': '.__('emergency login code used', 'two-factor-authentication'), 
			sprintf(__('An emergency code was used to login (username: %s) on this website: ', 'two-factor-authentication'), $user->user_login).home_url()."\r\n\r\n".
			sprintf(__('You now have %s emergency code(s) remaining.', 'two-factor-authentication'), count($emergency_codes))."\r\n".
			$extra
		);
		
	}

	private function get_otp($alg, $user_ID, $code, $tfa, $counter = false) {
		if ('hotp' == $alg) {
			return $tfa->encryptString($tfa->generateOTP($user_ID, $code, 8, $counter), $user_ID);
		} else {
			return $tfa->encryptString($tfa->generateOTP($user_ID, $code, 8, $counter), $user_ID);
		}
	}

	private function set_emergency_codes($user_ID, $codes) {
		return update_user_meta($user_ID, 'simba_tfa_emergency_codes_64', $codes);
	}
	
	/**
	 * Called by the WP action simba_tfa_adding_private_key
	 * When a new private key is added, we create some emergency codes.
	 *
	 * @param String $algorithm - 'totp' or 'hotp'
	 * @param Integer $user_ID
	 * @param String $code - this is the stored private key (i.e. after encryption)
	 * @param Object $tfa - TFA class object
	 */
	public function generate_emergency_codes($algorithm, $user_ID, $code, $tfa) {
		if ('hotp' == $algorithm) {
			// Add some emergency codes as well. Take 8 digits from events 1,2,3
			$this->set_emergency_codes($user_ID, array(
				$this->get_otp($algorithm, $user_ID, $code, $tfa, 1),
				$this->get_otp($algorithm, $user_ID, $code, $tfa, 2),
				$this->get_otp($algorithm, $user_ID, $code, $tfa, 3)
			));
		} else {
			// Add some emergency codes as well. The weakness of the random number routine here does not matter, since the private key is also used (for regular 6-digit codes, the time is completely known)
			$rand = time() + 30 * rand(0, 100000);
			$this->set_emergency_codes($user_ID, array(
				$this->get_otp($algorithm, $user_ID, $code, $tfa, $rand),
				$this->get_otp($algorithm, $user_ID, $code, $tfa, $rand+120),
				$this->get_otp($algorithm, $user_ID, $code, $tfa, $rand+240),
			));
		}
	}

	/**
	 * Get the currently list of available emergency codes
	 *
	 * @param Boolean|Integer $user_id - WP user ID (or false for current logged-in user)
	 * @param Boolean		  $generate_if_empty - generate some codes if the user currently has none
	 *
	 * @return String
	 */
	private function get_emergency_codes_as_string($user_id = false, $generate_if_empty = false) {
		global $current_user, $simba_two_factor_authentication;
		if (false == $user_id) $user_id = $current_user->ID;
		if (empty($this->tfa)) $this->tfa = $simba_two_factor_authentication->getTFA();
		$codes = get_user_meta($user_id, 'simba_tfa_emergency_codes_64', true);
		if (!is_array($codes)) $codes = array();
		return $this->tfa->getPanicCodesString($codes, $user_id, $generate_if_empty);
	}

	public function simba_tfa_emergency_codes_user_settings($m, $user_id) {

		$m = __("You have three emergency codes that can be used. Keep them in a safe place; if you lose your authentication device, then you can use them to log in.", 'two-factor-authentication').' '.__("These can only be used once each.", 'two-factor-authentication');
		$m .= '<br><br>';
		$m .= '<strong>'.__('Your emergency codes are:', 'two-factor-authentication').'</strong> '.$this->get_emergency_codes_as_string($user_id, true);
		return $m;
	}

	public function simba_tfa_fetch_assort_vars($vars, $tfa, $current_user) {
		// Get the list
		$codes = get_user_meta($current_user->ID, 'simba_tfa_emergency_codes_64', true);
		// Convert to string
		$vars['emergency_str'] = $tfa->getPanicCodesString($codes, $current_user->ID);
		return $vars;
	}

}

global $simba_two_factor_authentication_premium;
$simba_two_factor_authentication_premium = new Simba_Two_Factor_Authentication_Premium();
