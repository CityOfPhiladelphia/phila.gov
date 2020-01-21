<?php

if (!defined('ABSPATH')) die('Access denied.');

class Simba_TFA  {

	private $salt_prefix;
	private $pw_prefix;

	/**
	 * Class constructor
	 *
	 * @param Base32 $base32_encoder
	 * @param HOTP	 $otp_helper
	 */
	public function __construct($base32_encoder, $otp_helper) {
		$this->base32_encoder = $base32_encoder;
		$this->otp_helper = $otp_helper;
		$this->time_window_size = apply_filters('simbatfa_time_window_size', 30);
		$this->check_back_time_windows = apply_filters('simbatfa_check_back_time_windows', 2);
		$this->check_forward_time_windows = apply_filters('simbatfa_check_forward_time_windows', 1);
		$this->check_forward_counter_window = apply_filters('simbatfa_check_forward_counter_window', 20);
		$this->otp_length = 6;
		$this->emergency_codes_length = 8;
		$this->salt_prefix = defined('AUTH_SALT') ? AUTH_SALT : wp_salt('auth');
		$this->pw_prefix = defined('AUTH_KEY') ? AUTH_KEY : get_site_option('auth_key');
		$this->default_hmac = 'totp';
		$this->is_mcrypt_deprecated = (7 == PHP_MAJOR_VERSION && PHP_MINOR_VERSION >= 1);
	}
	
	/**
	 * hex2bin() does not exist before PHP 5.4 (https://php.net/hex2bin)
	 *
	 * @param String $data
	 *
	 * @return String
	 */
	private function hex2bin($data) {
		if (function_exists('hex2bin')) return hex2bin($data);
		$len = strlen($data);
		if (null === $len) return;
		if ($len % 2) {
			trigger_error('hex2bin(): Hexadecimal input string must have an even length', E_USER_WARNING);
			return false;
		}
		return pack('H*', $data);
	}

	public function generateOTP($user_ID, $key_b64, $length = 6, $counter = false) {
		
		$length = $length ? (int)$length : 6;
		
		$key = $this->decryptString($key_b64, $user_ID);
		$alg = $this->getUserAlgorithm($user_ID);
		
		if ($alg == 'hotp') {
			$db_counter = $this->getUserCounter($user_ID);
			
			$counter = $counter ? $counter : $db_counter;
			$otp_res = $this->otp_helper->generateByCounter($key, $counter);
		} else {
			//time() is supposed to be UTC
			$time = $counter ? $counter : time();
			$otp_res = $this->otp_helper->generateByTime($key, $this->time_window_size, $time);
		}
		$code = $otp_res->toHotp($length);
		
		return $code;
	}

	/**
	 * Generate a list of TOTP codes based on the user, key and time window
	 *
	 * @param Integer $user_ID - user ID
	 * @param String  $key_b64 - the user's private key, in base64 format
	 *
	 * @return Array
	 */
	public function generateOTPsForLoginCheck($user_ID, $key_b64) {
		$key = trim($this->decryptString($key_b64, $user_ID));
		$alg = $this->getUserAlgorithm($user_ID);
		
		if ('totp' == $alg) {
			$otp_res = $this->otp_helper->generateByTimeWindow($key, $this->time_window_size, -1*$this->check_back_time_windows, $this->check_forward_time_windows);
		} elseif ('hotp' == $alg) {
		
			$counter = $this->getUserCounter($user_ID);
			
			$otp_res = array();
			
			for ($i = 0; $i < $this->check_forward_counter_window; $i++) {
				$otp_res[] = $this->otp_helper->generateByCounter($key, $counter+$i);
			}
		}
		return $otp_res;
	}
	

	/**
	 * Generate a private key for the user.
	 *
	 * @param Integer $user_id - WordPress user ID
	 * @param Boolean|String $key
	 *
	 * @return String
	 */
	public function addPrivateKey($user_id, $key = false) {

		// To work with Google Authenticator it has to be 10 bytes = 16 chars in base32
		$code = $key ? $key : strtoupper($this->randString(10));

		// Encrypt the key
		$code = $this->encryptString($code, $user_id);
		
		// Add private key to usermeta
		update_user_meta($user_id, 'tfa_priv_key_64', $code);
		
		$alg = $this->getUserAlgorithm($user_id);
		
		// This hook is used for generation of emergency codes to accompany the key
		do_action('simba_tfa_adding_private_key', $alg, $user_id, $code, $this);
		
		$this->changeUserAlgorithmTo($user_id, $alg);
		
		return $code;
	}

	// Port over keys that were encrypted with mcrypt and its non-compliant padding scheme, so that if the site is ever migrated to a server without mcrypt, they can still be decrypted
	public function potentially_port_private_keys() {

		$simba_tfa_priv_key_format = get_site_option('simba_tfa_priv_key_format', false);
		
		if ($simba_tfa_priv_key_format >= 1 || !function_exists('openssl_encrypt')) return;
		
		$attempts = 0;
		$successes = 0;
		
		error_log("TFA: Beginning attempt to port private key encryption over to openssl");
		
		global $wpdb;
		
		$sql = "SELECT user_id, meta_value FROM ".$wpdb->usermeta." WHERE meta_key = 'tfa_priv_key_64'";
		
		$user_results = $wpdb->get_results($sql);
		
		foreach ($user_results as $u) {
			$dec_openssl = $this->decryptString($u->meta_value, $u->user_id, true);

			$ported = false;
			if ('' == $dec_openssl) {

				$attempts++;

				$dec_default = $this->decryptString($u->meta_value, $u->user_id);
				
				if ('' != $dec_default) {

					$enc = $this->encryptString($dec_default, $u->user_id);
					
					if ($enc) {

						$ported = true;
						$successes++;
						update_user_meta($u->user_id, 'tfa_priv_key_64', $enc);
					}
				}

			}
			
			if ($ported) {
				error_log("TFA: Successfully ported the key for user with ID ".$u->user_id." over to openssl");
			} else {
				error_log("TFA: Failed to port the key for user with ID ".$u->user_id." over to openssl");
			}
		}
		
		if ($attempts == 0 || $successes > 0) update_site_option('simba_tfa_priv_key_format', 1);
	
	}

	public function getPrivateKeyPlain($enc, $user_ID) {
		$dec = $this->decryptString($enc, $user_ID);
		$this->potentially_port_private_keys();
		return $dec;
	}


	/**
	 * @param Array $codes - current list of codes (encrypted)
	 * @param Integer $user_ID - WP user ID
	 * @param Boolean $generate_if_empty - generate some new codes if the list is empty
	 *
	 * @return String - human-usable codes, separated by ', ' (or a human-readable message, if there were none)
	 */
	public function getPanicCodesString($codes, $user_ID, $generate_if_empty = false) {
		if (!is_array($codes)) return '<em>'.__('No emergency codes left. Sorry.', 'two-factor-authentication').'</em>';
		if ($generate_if_empty && empty($codes)) {
			$tfa_priv_key = get_user_meta($user_ID, 'tfa_priv_key_64', true);
			$algorithm = get_user_meta($user_ID, 'tfa_algorithm_type', true);
			do_action('simba_tfa_emergency_codes_empty', $algorithm, $user_ID, $tfa_priv_key, $this);
			$codes = get_user_meta($user_ID, 'simba_tfa_emergency_codes_64', true);
			if (!is_array($codes)) $codes = array();
		}
		
		$emergency_str = '';
		
		foreach ($codes as $p_code) {
			$emergency_str .= $this->decryptString($p_code, $user_ID).', ';
		}

		$emergency_str = rtrim($emergency_str, ', ');
		
		$emergency_str = $emergency_str ? $emergency_str : '<em>'.__('There are no emergency codes left. You will need to reset your private key.', 'two-factor-authentication').'</em>';
		
		return $emergency_str;
	}
	
	/**
	 * Should the user be asked for a TFA code? And optionally, is the user allowed to trust devices?
	 *
	 * @param Array	 $params - the key used is 'log', indicating the username or email address
	 * @param String $response_format - 'simple' (historic format) or 'array' (richer info)
	 *
	 * @return Boolean
	 */
	public function preAuth($params, $response_format = 'simple') {
		global $wpdb;
		
		$query = filter_var($params['log'], FILTER_VALIDATE_EMAIL) ? $wpdb->prepare("SELECT ID, user_email from ".$wpdb->users." WHERE user_email=%s", $params['log']) : $wpdb->prepare("SELECT ID, user_email from ".$wpdb->users." WHERE user_login=%s", $params['log']);
		$user = $wpdb->get_row($query);
		
		if (!$user && filter_var($params['log'], FILTER_VALIDATE_EMAIL)) {
			// Corner-case: login looks like an email, but is a username rather than email address
			$user = $wpdb->get_row($wpdb->prepare("SELECT ID, user_email from ".$wpdb->users." WHERE user_login=%s", $params['log']));
		}
		
		$is_activated_for_user = true;
		$is_activated_by_user = false;
		
		$result = false;
		
		if ($user) {
			$tfa_priv_key = get_user_meta($user->ID, 'tfa_priv_key_64', true);
			$is_activated_for_user = $this->isActivatedForUser($user->ID);
			$is_activated_by_user = $this->isActivatedByUser($user->ID);
			
			if ($is_activated_for_user && $is_activated_by_user) {
				
				// No private key yet, generate one. This shouldn't really be possible.
				if (!$tfa_priv_key) $tfa_priv_key = $this->addPrivateKey($user->ID);
				
				$code = $this->generateOTP($user->ID, $tfa_priv_key);

				$result = true;
			}
		}
		
		if ('array' != $response_format) return $result;
		
		$ret = array('result' => $result);
		
		if ($result) {
			$ret['user_can_trust'] = $this->user_can_trust($user->ID);
			if (!empty($params['trust_token']) && $this->user_trust_token_valid($user->ID, $params['trust_token'])) {
				$ret['user_already_trusted'] = 1;
			}
		}
		
		return $ret;
	}
	
	/**
	 * Evaluate whether a trust token is valid for a user
	 *
	 * @param Integer $user_id	   - WP user ID
	 * @param String  $trust_token - trust token
	 *
	 * @return Boolean
	 */
	private function user_trust_token_valid($user_id, $trust_token) {
		
		if (!is_string($trust_token) || strlen($trust_token) < 30) return false;
		
		$trusted_devices = $this->user_get_trusted_devices($user_id);
		
		$time_now = time();
		
		foreach ($trusted_devices as $device) {
			if (empty($device['until']) || $device['until'] <= $time_now) continue;
			if (!empty($device['token']) && $device['token'] === $trust_token) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Get a list of trusted devices for the user
	 *
	 * @param Integer $user_id - WordPress user ID
	 * @param Array	  $trusted_devices - the list of devices
	 */
	public function user_set_trusted_devices($user_id, $trusted_devices) {
		update_user_meta($user_id, 'tfa_trusted_devices', $trusted_devices);
	}
	
	/**
	 * Get a list of trusted devices for the user
	 *
	 * @param Integer $user_id - WordPress user ID
	 *
	 * @return Array
	 */
	public function user_get_trusted_devices($user_id) {
	
		$trusted_devices = get_user_meta($user_id, 'tfa_trusted_devices', true);

		if (!is_array($trusted_devices)) $trusted_devices = array();

		return $trusted_devices;
	}
	
	/**
	 * @param Array	 		 $params
	 * @param Boolean		 $may_be_email
	 *
	 * @return WP_Error|Boolean|Integer - WP_Error or false means failure; true or 1 means success, but true means the TFA code was validated
	 */
	public function authUserFromLogin($params, $may_be_email = false) {
		
		$params = apply_filters('simbatfa_auth_user_from_login_params', $params);
		
		global $simba_two_factor_authentication, $wpdb;

		if (!$this->isCallerActive($params)) return 1;
		
		$query = ($may_be_email && filter_var($params['log'], FILTER_VALIDATE_EMAIL)) ? $wpdb->prepare("SELECT ID, user_registered from ".$wpdb->users." WHERE user_email=%s", $params['log']) : $wpdb->prepare("SELECT ID, user_registered from ".$wpdb->users." WHERE user_login=%s", $params['log']);
		$response = $wpdb->get_row($query);

		if (!$response && $may_be_email && filter_var($params['log'], FILTER_VALIDATE_EMAIL)) {
			// Corner-case: login looks like an email, but is a username rather than email address
			$response = $wpdb->get_row($wpdb->prepare("SELECT ID, user_registered from ".$wpdb->users." WHERE user_login=%s", $params['log']));
		}
		
		$user_ID = is_object($response) ? $response->ID : false;
		$user_registered = is_object($response) ? $response->user_registered : false;

		$user_code = isset($params['two_factor_code']) ? str_replace(' ', '', trim($params['two_factor_code'])) : '';

		// This condition in theory should not be possible
		if (!$user_ID) return new WP_Error('tfa_user_not_found', apply_filters('simbatfa_tfa_user_not_found', '<strong>'.__('Error:', 'two-factor-authentication').'</strong> '.__('The indicated user could not be found.', 'two-factor-authentication')));

		if (!$this->isActivatedForUser($user_ID)) return 1;

		if (!empty($params['trust_token']) && $this->user_trust_token_valid($user_ID, $params['trust_token'])) {
			return 1;
		}
		 
		if (!$this->isActivatedByUser($user_ID)) {
		
			if (!$this->isRequiredForUser($user_ID)) return 1;
			
			$requireafter = absint($simba_two_factor_authentication->get_option('tfa_requireafter')) * 86400;

			$account_age = time() - strtotime($user_registered);

			if ($account_age > $requireafter) {
				return new WP_Error('tfa_required', apply_filters('simbatfa_notfa_forbidden_login', '<strong>'.__('Error:', 'two-factor-authentication').'</strong> '.__('The site owner has forbidden you to login without two-factor authentication. Please contact the site owner to re-gain access.', 'two-factor-authentication')));
			}

			return 1;
		}

		$tfa_creds_user_id = !empty($params['creds_user_id']) ? $params['creds_user_id'] : $user_ID;
		
		if ($tfa_creds_user_id != $user_ID) {
		
			// Authenticating using a different user's credentials (e.g. https://wordpress.org/plugins/use-administrator-password/)
			// In this case, we require that different user to have TFA active - so that this mechanism can't be used to avoid TFA
		
			if (!$this->isActivatedForUser($tfa_creds_user_id) || !$this->isActivatedByUser($tfa_creds_user_id)) {
				return new WP_Error('tfa_required', apply_filters('simbatfa_notfa_forbidden_login_altuser', '<strong>'.__('Error:', 'two-factor-authentication').'</strong> '.__('You are attempting to log in to an account that has two-factor authentication enabled; this requires you to also have two-factor authentication enabled on the account whose credentials you are using.', 'two-factor-authentication')));
			}
		
		}
		
		return $this->check_code_for_user($tfa_creds_user_id, $user_code);
		
	}
	
	/**
	 * Check a code for a user (checks the code only - does not check activation status etc.)
	 *
	 * @param Integer $user_id	 - WP user ID
	 * @param String  $user_code - the code to check
	 * @param Boolean $allow_emergency_code - whether to check against emergency codes
	 *
	 * @return Boolean
	 */
	public function check_code_for_user($user_id, $user_code, $allow_emergency_code = true) {
		
		$tfa_priv_key = get_user_meta($user_id, 'tfa_priv_key_64', true);
// 		$tfa_last_login = get_user_meta($user_id, 'tfa_last_login', true); // Unused
		$tfa_last_pws_arr = get_user_meta($user_id, 'tfa_last_pws', true);
		$tfa_last_pws = @$tfa_last_pws_arr ? $tfa_last_pws_arr : array();
		$alg = $this->getUserAlgorithm($user_id);
		
		$current_time_window = intval(time()/30);
		
		//Give the user 1,5 minutes time span to enter/retrieve the code
		//Or check $this->check_forward_counter_window number of events if hotp
		$codes = $this->generateOTPsForLoginCheck($user_id, $tfa_priv_key);
	
		//A recently used code was entered; that's not OK.
		if (in_array($this->hash($user_code, $user_id), $tfa_last_pws)) return false;
	
		$match = false;
		foreach ($codes as $index => $code) {
			if (trim($code->toHotp(6)) == trim($user_code)) {
				$match = true;
				$found_index = $index;
				break;
			}
		}
		
		// Check emergency codes
		if (!$match) {
			$emergency_codes = $allow_emergency_code ? get_user_meta($user_id, 'simba_tfa_emergency_codes_64', true) : array();
			
			if (!$emergency_codes) return $match;
			
			$dec = array();
			foreach ($emergency_codes as $emergency_code)
				$dec[] = trim($this->decryptString(trim($emergency_code), $user_id));

			$in_array = array_search($user_code, $dec);
			$match = $in_array !== false;
			
			//Remove emergency code
			if ($match) {
				array_splice($emergency_codes, $in_array, 1);
				update_user_meta($user_id, 'simba_tfa_emergency_codes_64', $emergency_codes);
				do_action('simba_tfa_emergency_code_used', $user_id, $emergency_codes);
			}
			
		} else {
			//Add the used code as well so it cant be used again
			//Keep the two last codes
			$tfa_last_pws[] = $this->hash($user_code, $user_id);
			$nr_of_old_to_save = $alg == 'hotp' ? $this->check_forward_counter_window : $this->check_back_time_windows;
			
			if (count($tfa_last_pws) > $nr_of_old_to_save) array_splice($tfa_last_pws, 0, 1);
				
			update_user_meta($user_id, 'tfa_last_pws', $tfa_last_pws);
		}
		
		if ($match) {
			//Save the time window when the last successful login took place
			update_user_meta($user_id, 'tfa_last_login', $current_time_window);
			
			//Update the counter if HOTP was used
			if ($alg == 'hotp') {
				$counter = $this->getUserCounter($user_id);
				
				$enc_new_counter = $this->encryptString($counter+1, $user_id);
				update_user_meta($user_id, 'tfa_hotp_counter', $enc_new_counter);
				
				if ($found_index > 10) update_user_meta($user_id, 'tfa_hotp_off_sync', 1);
			}
		}
		
		return $match;
		
	}

	public function getUserCounter($user_ID) {
		$enc_counter = get_user_meta($user_ID, 'tfa_hotp_counter', true);
		
		if ($enc_counter)
			$counter = $this->decryptString(trim($enc_counter), $user_ID);
		else
			return '';
			
		return trim($counter);
	}
	
	public function changeUserAlgorithmTo($user_id, $new_algorithm) {
		update_user_meta($user_id, 'tfa_algorithm_type', $new_algorithm);
		delete_user_meta($user_id, 'tfa_hotp_off_sync');
		
		$counter_start = rand(13, 999999999);
		$enc_counter_start = $this->encryptString($counter_start, $user_id);
		
		if ($new_algorithm == 'hotp')
			update_user_meta($user_id, 'tfa_hotp_counter', $enc_counter_start);
		else
			delete_user_meta($user_id, 'tfa_hotp_counter');
	}
	
	/**
	 * Enable or disable TFA for a user
	 *
	 * @param Integer $user_id - the WordPress user ID
	 * @param String  $setting - either "true" (to turn on) or "false" (to turn off)
	 */
	public function changeEnableTFA($user_id, $setting) {
		$previously_enabled = $this->isActivatedByUser($user_id) ? 1 : 0;
		$setting = ('true' === $setting) ? 1 : 0;
		update_user_meta($user_id, 'tfa_enable_tfa', $setting);
		do_action('simba_tfa_activation_status_saved', $user_id, $setting, $previously_enabled, $this);
	}
	
	public function getUserAlgorithm($user_id) {
		global $simba_two_factor_authentication;
		$setting = get_user_meta($user_id, 'tfa_algorithm_type', true);
		$default_hmac = $simba_two_factor_authentication->get_option('tfa_default_hmac');
		$default_hmac = $default_hmac ? $default_hmac : $this->default_hmac;
		
		$setting = $setting === false || !$setting ? $default_hmac : $setting;
		return $setting;
	}
	
	/**
	 * See whether TFA is available or not for a particular user - i.e. whether the administrator has permitted it for their user level
	 *
	 * @param Integer $user_id - WordPress user ID
	 *
	 * @return Boolean
	 */
	public function isActivatedForUser($user_id) {

		if (empty($user_id)) return false;

		global $simba_two_factor_authentication;

		// Super admin is not a role (they are admins with an extra attribute); needs separate handling
		if (is_multisite() && is_super_admin($user_id)) {
			// This is always a final decision - we don't want it to drop through to the 'admin' role's setting
			$role = '_super_admin';
			$db_val = $simba_two_factor_authentication->get_option('tfa_'.$role);
			// Defaults to true if no setting has been saved
			return (false === $db_val || $db_val) ? true : false;
		}

		$roles = $this->get_user_roles($user_id);
		
		// N.B. This populates with roles on the current site within a multisite
		foreach ($roles as $role) {
			$db_val = $simba_two_factor_authentication->get_option('tfa_'.$role);
			if (false === $db_val || $db_val) return true;
		}
		
		return false;
		
	}
	
		/**
	 * Get all user roles for a given user (if on multisite, amalgamates all roles from all sites)
	 *
	 * @param Integer $user_id - WordPress user ID
	 *
	 * @return Array
	 */
	private function get_user_roles($user_id) {
		
		// Get roles on the main site
		$user = new WP_User($user_id);
		$roles = (array) $user->roles;
		
		// On multisite, also check roles on non-main sites
		if (is_multisite()) {
			global $wpdb, $table_prefix;
			$roles_db = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value FROM {$wpdb->usermeta} WHERE user_id=%d AND meta_key LIKE '".esc_sql($table_prefix)."%_capabilities'", $user_id));
			if (is_array($roles_db)) {
				foreach ($roles_db as $role_info) {
					if (empty($role_info->meta_key) || !preg_match('/^'.$table_prefix.'\d+_capabilities$/', $role_info->meta_key) || empty($role_info->meta_value) || !preg_match('/^a:/', $role_info->meta_value)) continue;
					$site_roles = unserialize($role_info->meta_value);
					if (!is_array($site_roles)) continue;
					foreach ($site_roles as $role => $active) {
						if ($active && !in_array($role, $roles)) $roles[] = $role;
					}
				}
			}
		}
		
		return $roles;
	}
	
	// N.B. - This doesn't check isActivatedForUser() - the caller would normally want to do that first
	public function isRequiredForUser($user_id) {
		return $this->user_property_active($user_id, 'required_');
	}
	
	// N.B. - This doesn't check isActivatedForUser() - the caller would normally want to do that first
	public function user_can_trust($user_id) {
		// Default is false because this is a new feature and we don't want to surprise existing users by granting broader access than they expected upon an upgrade
		return apply_filters('simba_tfa_user_can_trust', false, $user_id, $this);
	}
	
	/**
	 * See if a particular user property is active
	 *
	 * @param Integer $user_id
	 * @param String  $prefix - e.g. "required_", "trusted_"
	 *
	 * @return Boolean
	 */
	public function user_property_active($user_id, $prefix = 'required_') {
	
		if (empty($user_id)) return false;

		global $simba_two_factor_authentication;

		// Super admin is not a role (they are admins with an extra attribute); needs separate handling
		if (is_multisite() && is_super_admin($user_id)) {
			// This is always a final decision - we don't want it to drop through to the 'admin' role's setting
			$role = '_super_admin';
			$db_val = $simba_two_factor_authentication->get_option('tfa_'.$prefix.$role);
			return $db_val ? true : false;
		}

		$roles = $this->get_user_roles($user_id);
		
		foreach ($roles as $role) {
			$db_val = $simba_two_factor_authentication->get_option('tfa_'.$prefix.$role);
			if ($db_val) return true;
		}
		
		return false;
		
	}
	
	/**
	 * Trust the current device
	 *
	 * @param Integer $user_id - WordPress user ID
	 * @param Integer $trusted_for - time to trust for, in days
	 */
	public function trust_device($user_id, $trusted_for) {
	
		$trusted_devices = get_user_meta($user_id, 'tfa_trusted_devices', true);
	
		if (!is_array($trusted_devices)) $trusted_devices = array();
		
		$time_now = time();
		
		foreach ($trusted_devices as $k => $device) {
			if (empty($device['until']) || $device['until'] <= $time_now) unset($trusted_devices[$k]);
		}
		
		$until = $time_now + $trusted_for * 86400;
		
		$token = bin2hex($this->random_bytes(40));
		
		$trusted_devices[] = array(
			'ip' => $_SERVER['REMOTE_ADDR'],
			'until' => $until,
			'user_agent' => empty($_SERVER['HTTP_USER_AGENT']) ? '' : (string) $_SERVER['HTTP_USER_AGENT'],
			'token' => $token
		);
	
		$this->user_set_trusted_devices($user_id, $trusted_devices);
	
		$this->set_cookie('simbatfa_trust_token', $token, $until);
	}
	
	/**
	 * Set a cookie so that, however we logged in, it can be found
	 *
	 * @param String  $name	   - the cookie name
	 * @param String  $value   - the cookie value
	 * @param Integer $expires - when the cookie expires, in epoch time. Defaults to 24 hours' time. Values in the past cause cookie deletion.
	 */
	private function set_cookie($name, $value, $expires = null) {
		if (null === $expires) $expires = time() + 86400;
		$secure = is_ssl();
		$secure_logged_in_cookie = ($secure && 'https' === parse_url(get_option('home'), PHP_URL_SCHEME));
		$secure = apply_filters('secure_auth_cookie', $secure, get_current_user_id());
		$secure_logged_in_cookie = apply_filters('secure_logged_in_cookie', $secure_logged_in_cookie, get_current_user_id(), $secure);
	
		setcookie($name, $value, $expires, ADMIN_COOKIE_PATH, COOKIE_DOMAIN, $secure, true);
		setcookie($name, $value, $expires, COOKIEPATH, COOKIE_DOMAIN, $secure_logged_in_cookie, true);
		if (COOKIEPATH != SITECOOKIEPATH) {
			setcookie($name, $value, $expires, SITECOOKIEPATH, COOKIE_DOMAIN, $secure_logged_in_cookie, true);
		}
	}
	
	/**
	 * Whether TFA is activated by a specific user. Note that this doesn't check if TFA is enabled for the user's role; the caller should check that first.
	 *
	 * @param Integer $user_id
	 *
	 * @return Boolean
	 */
	public function isActivatedByUser($user_id) {
		$enabled = get_user_meta($user_id, 'tfa_enable_tfa', true);
		return !empty($enabled);
	}

	private function isCallerActive($params) {

		if (!defined('XMLRPC_REQUEST') || !XMLRPC_REQUEST) return true;

		global $simba_two_factor_authentication;
		$saved_data = $simba_two_factor_authentication->get_option('tfa_xmlrpc_on');
		
		return $saved_data ? true : false;
		
	}
	
	private function get_iv_size() {
		// mcrypt first, for backwards compatibility
		if (function_exists('mcrypt_get_iv_size')) {
			return $this->is_mcrypt_deprecated ? @mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC) : mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		} elseif (function_exists('openssl_cipher_iv_length')) {
			return openssl_cipher_iv_length('AES-128-CBC');
		}
		throw new Exception('One of the mcrypt or openssl PHP modules needs to be installed');
	}
	
	/**
	 * Return the specified number of bytes
	 *
	 * @param Integer $bytes
	 *
	 * @return String
	 */
	private function random_bytes($bytes) {
		if (function_exists('random_bytes')) {
			return random_bytes($bytes);
		} elseif (function_exists('mcrypt_create_iv')) {
			 return $this->is_mcrypt_deprecated ? @mcrypt_create_iv($bytes, MCRYPT_RAND) : mcrypt_create_iv($bytes, MCRYPT_RAND);
		} elseif (function_exists('openssl_random_pseudo_bytes')) {
			return openssl_random_pseudo_bytes($bytes);
		}
		throw new Exception('One of the mcrypt or openssl PHP modules needs to be installed');
	}
	
	private function encrypt($key, $string, $iv) {
		// Prefer OpenSSL, because it uses correct padding, and its output can be decrypted by mcrypt - whereas, the converse is not true
		if (function_exists('openssl_encrypt')) {
			return openssl_encrypt($string, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
		} elseif (function_exists('mcrypt_encrypt')) {
			return $this->is_mcrypt_deprecated ? @mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $string, MCRYPT_MODE_CBC, $iv) : mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $string, MCRYPT_MODE_CBC, $iv);
		}
		throw new Exception('One of the mcrypt or openssl PHP modules needs to be installed');
	}

	private function decrypt($key, $enc, $iv, $force_openssl = false) {
		// Prefer mcrypt, because it can decrypt the output of both mcrypt_encrypt() and openssl_decrypt(), whereas (because of mcrypt_encrypt() using bad padding), the converse is not true
		if (function_exists('mcrypt_decrypt') && !$force_openssl) {
			return $this->is_mcrypt_deprecated ? @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $enc, MCRYPT_MODE_CBC, $iv) : mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $enc, MCRYPT_MODE_CBC, $iv);
		} elseif (function_exists('openssl_decrypt')) {
			$decrypted = openssl_decrypt($enc, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
			if (false === $decrypted && !$force_openssl) {
				$extra = function_exists('wp_debug_backtrace_summary') ? " backtrace: ".wp_debug_backtrace_summary() : '';
				error_log("TFA decryption failure: was your site migrated to a server without mcrypt? You may need to install mcrypt, or disable TFA, in order to successfully decrypt data that was previously encrypted with mcrypt.$extra");
			}
			return $decrypted;
		}
		if ($force_openssl) return false;
		throw new Exception('One of the mcrypt or openssl PHP modules needs to be installed');
	}

	public function encryptString($string, $salt_suffix) {
		$key = $this->hashAndBin($this->pw_prefix.$salt_suffix, $this->salt_prefix.$salt_suffix);
		
		$iv_size = $this->get_iv_size();
		$iv = $this->random_bytes($iv_size);
		
		$enc = $this->encrypt($key, $string, $iv);
		
		if (false === $enc) return false;
		
		$enc = $iv.$enc;
		$enc_b64 = base64_encode($enc);
		return $enc_b64;
	}
	
	private function decryptString($enc_b64, $salt_suffix, $force_openssl = false) {
		$key = $this->hashAndBin($this->pw_prefix.$salt_suffix, $this->salt_prefix.$salt_suffix);
		
		$iv_size = $this->get_iv_size();
		$enc_conc = bin2hex(base64_decode($enc_b64));
		
		$iv = $this->hex2bin(substr($enc_conc, 0, $iv_size*2));
		$enc = $this->hex2bin(substr($enc_conc, $iv_size*2));
		
		$string = $this->decrypt($key, $enc, $iv, $force_openssl);

		// Remove padding bytes
		return rtrim($string, "\x00..\x1F");
	}

	private function hashAndBin($pw, $salt) {
		$key = $this->hash($pw, $salt);
		$key = pack('H*', $key);
		// Yes: it's a null encryption key. See: https://wordpress.org/support/topic/warning-mcrypt_decrypt-key-of-size-0-not-supported-by-this-algorithm-only-k?replies=5#post-6806922
		// Basically: the original plugin had a bug here, which caused a null encryption key. This fails on PHP 5.6+. But, fixing it would break backwards compatibility for existing installs - and note that the only unknown once you have access to the encrypted data is the AUTH_SALT and AUTH_KEY constants... which means that actually the intended encryption was non-portable, + problematic if you lose your wp-config.php or try to migrate data to another site, or changes these values. (Normally changing these values only causes a compulsory re-log-in - but with the intended encryption in the original author's plugin, it'd actually cause a permanent lock-out until you disabled his plugin). If someone has read-access to the database, then it'd be reasonable to assume they have read-access to wp-config.php too: or at least, the number of attackers who can do one and not the other would be small. The "encryption's" not worth it.
		// In summary: this isn't encryption, and is not intended to be.
		return str_repeat(chr(0), 16);
	}

	private function hash($pw, $salt) {
		//$hash = hash_pbkdf2('sha256', $pw, $salt, 10);
		//$hash = crypt($pw, '$5$'.$salt.'$');
		$hash = md5($salt.$pw);
		return $hash;
	}

	private function randString($len = 10) {
		$chars = '23456789QWERTYUPASDFGHJKLZXCVBNM';
		$chars = str_split($chars);
		shuffle($chars);
		if (function_exists('random_int')) {
			$code = '';
			for ($i = 1; $i <= $len; $i++) {
				$code .= $chars[random_int(0, count($chars)-1)];
			}
		} else {
			$code = implode('', array_splice($chars, 0, $len));
		}
		return $code;
	}
	
	public function setUserHMACTypes() {
		//We need this because we dont want to change third party apps users algorithm
		$users = get_users(array('meta_key' => 'simbatfa_delivery_type', 'meta_value' => 'third-party-apps'));
		if (!empty($users))
		{
			foreach ($users as $user)
			{
				$tfa_algorithm_type = get_user_meta($user->ID, 'tfa_algorithm_type', true);
				if ($tfa_algorithm_type)
					continue;
				
				update_user_meta($user->ID, 'tfa_algorithm_type', $this->getUserAlgorithm($user->ID));
			}
		}
	}
	
}
