<?php

if (!defined('ABSPATH')) die('No direct access.');

class Simba_TFA_Provider_TOTP {

	/**
	 * Generate the current code for a specified user
	 *
	 * @param $user_id Integer - WordPress user ID
	 *
	 * @return String|Boolean - false if not set up
	 */
	public function get_current_code($user_id) {
	
		$tfa_priv_key_64 = get_user_meta($user_id, 'tfa_priv_key_64', true);
		
		if (!$tfa_priv_key_64) return false;
		
		return $this->get_simba_tfa()->generateOTP($user_id, $tfa_priv_key_64);
	
	}
	
	/**
	 * Return a new Simba_TFA object. Public because used by legacy methods.
	 *
	 * @returns Simba_TFA
	 */
	public function get_simba_tfa() {
		if (!class_exists('HOTP')) require_once(SIMBA_TFA_PLUGIN_DIR.'/hotp-php-master/hotp.php');
		if (!class_exists('Base32')) require_once(SIMBA_TFA_PLUGIN_DIR.'/Base32/Base32.php');
		if (!class_exists('Simba_TFA')) require_once(SIMBA_TFA_PLUGIN_DIR.'/includes/class-simba-tfa.php');
		return new Simba_TFA(new Base32(), new HOTP());
	}


}
