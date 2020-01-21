<?php
if (!defined('ABSPATH')) die('Access denied.');

class TFA_Frontend {

	private $mother;

	/**
	 * Class constructor
	 *
	 * @param Object $mother
	 */
	public function __construct($mother) {

		$this->mother = $mother;
		add_action('wp_ajax_tfa_frontend', array($this, 'ajax'));
		add_shortcode('twofactor_user_settings', array($this, 'tfa_user_settings_front'));
	}
	
	/**
	 * Runs upon the WP action wp_ajax_tfa_frontend
	 */
	public function ajax() {
		$tfa = $this->mother->getTFA();
		global $current_user;
		
		$return_array = array();
		
		if (empty($_POST) || empty($_POST['subaction']) || !isset($_POST['nonce']) || !is_user_logged_in() || !wp_verify_nonce($_POST['nonce'], 'tfa_frontend_nonce')) die('Security check');
		
		if ('savesettings' == $_POST['subaction']) {
			if (empty($_POST['settings']) || !is_string($_POST['settings'])) die;
			
			parse_str(stripslashes($_POST['settings']), $posted_settings);
			
			if (isset($posted_settings["tfa_algorithm_type"])) {
				$old_algorithm = $tfa->getUserAlgorithm($current_user->ID);
		
				if ($old_algorithm != $posted_settings['tfa_algorithm_type'])
					$tfa->changeUserAlgorithmTo($current_user->ID, $posted_settings['tfa_algorithm_type']);
				
				//Re-fetch the algorithm type, url and private string
				$variables = $this->tfa_fetch_assort_vars();
				
				$return_array['qr'] = $this->mother->tfa_qr_code_url($variables['algorithm_type'], $variables['url'], $variables['tfa_priv_key']);
				$return_array['al_type_disp'] = $this->tfa_algorithm_info($variables['algorithm_type']);
			}
			
			if (isset($posted_settings['tfa_enable_tfa'])) {
			
				$allow_enable = false;
			
				if (empty($posted_settings['require_current'])) {
					$allow_enable = true;
				} else {
				
					if (!isset($posted_settings['tfa_enable_current']) || '' == $posted_settings['tfa_enable_current']) {
						$return_array['message'] = __('To enable TFA, you must enter the current code.', 'two-factor-authentication');
						$return_array['error'] = 'code_absent';
					} else {
						// Third parameter: don't allow emergency codes
						if ($tfa->check_code_for_user($current_user->ID, $posted_settings['tfa_enable_current'], false)) {
							$allow_enable = true;
						} else {
							$return_array['error'] = 'code_wrong';
							$return_array['message'] = __('The TFA code you entered was incorrect.', 'two-factor-authentication');
						}
					}
				
				}
				
				if ($allow_enable || !$posted_settings['tfa_enable_tfa']) $tfa->changeEnableTFA($current_user->ID, $posted_settings['tfa_enable_tfa']);
			}
			
			
			
			$return_array['result'] = 'saved';
			
			echo json_encode($return_array);
		}
		
		die;
	}
	
	//Make the algorithm information string easier to update
	public function tfa_algorithm_info($algorithm_type) {
		$al_type_disp = strtoupper($algorithm_type);
		$al_type_desc = ($algorithm_type == 'totp' ? __('a time based', 'two-factor-authentication') : __('an event based', 'two-factor-authentication'));
		
		return array('disp' => $al_type_disp, 'desc' => $al_type_desc);
	}
	
	/**
	 * Make the assorted required variables more accessible for ajax
	 * Returns: Site URL, private key, emergency codes, algorithm type
	 *
	 * @return Array
	 */
	public function tfa_fetch_assort_vars() {
		global $current_user;
		$tfa = $this->mother->getTFA();
		
		$url = preg_replace('/^https?:\/\//i', '', site_url());
				
		$tfa_priv_key_64 = get_user_meta($current_user->ID, 'tfa_priv_key_64', true);
		
		if (!$tfa_priv_key_64) $tfa_priv_key_64 = $tfa->addPrivateKey($current_user->ID);

		$tfa_priv_key = trim($tfa->getPrivateKeyPlain($tfa_priv_key_64, $current_user->ID));
			
		$algorithm_type = $tfa->getUserAlgorithm($current_user->ID);
		
		return apply_filters('simba_tfa_fetch_assort_vars', array(
			'url' => $url,
			'tfa_priv_key_64' => $tfa_priv_key_64,
			'tfa_priv_key' => $tfa_priv_key,
			'emergency_str' => '<em>'.__('No emergency codes left. Sorry.', 'two-factor-authentication').'</em>',
			'algorithm_type' => $algorithm_type
		), $tfa, $current_user);
	}
	
	public function save_settings_button() {
		echo '<button style="margin-left: 4px;margin-bottom: 10px" class="simbatfa_settings_save button button-primary">'.__('Save Settings', 'two-factor-authentication').'</button>';
	}

	private function get_tfa() {
		if (empty($this->tfa)) $this->tfa = $this->mother->getTFA();
	}

	/**
	 * Paint output for the TFA on/off radio
	 *
	 * @param String $style - valid values are 'show_current' and 'require_current'
	 */
	public function settings_enable_or_disable_output($style = 'show_current') {
		$this->save_settings_javascript_output();
		global $current_user;
		?>
			<div class="simbatfa_frontend_settings_box tfa_settings_form">
				<p><?php $this->mother->tfaListEnableRadios($current_user->ID, true, $style); ?></p>
				<button style="margin-left: 4px; margin-bottom: 10px;" class="button button-primary simbatfa_settings_save"><?php _e('Save Settings', 'two-factor-authentication'); ?></button>
			</div>
		<?php
	}

	public function save_settings_javascript_output() {
		static $is_already_added;
		if (!empty($is_already_added)) return;
		$is_already_added = true;
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'jquery-blockui', SIMBA_TFA_PLUGIN_URL . '/includes/jquery.blockUI' . $suffix . '.js', array('jquery' ), '2.60' );
		wp_enqueue_script('jquery-blockui');
		add_action('wp_footer', array($this, 'wp_footer'));
	}

	public function wp_footer() {
		$ajax_url = admin_url('admin-ajax.php');
		// It's possible that FORCE_ADMIN_SSL will make that SSL, whilst the user is on the front-end having logged in over non-SSL - and as a result, their login cookies won't get sent, and they're not registered as logged in.
		if (!is_admin() && substr(strtolower($ajax_url), 0, 6) == 'https:' && !is_ssl()) {
			$also_try = 'http:'.substr($ajax_url, 6);
		}
		?>

		<script type="text/javascript">
			var tfa_query_leaving = false;
			
			// Prevent accidental leaving if there are unsaved settings
			window.onbeforeunload = function(e) {
				if (tfa_query_leaving) {
					var ask = "<?php echo esc_js(__('You have unsaved settings.', 'two-factor-authentication')); ?>";
					e.returnValue = ask;
					return ask;
				}
			}
			
			jQuery(document).ready(function($) {
				$(".tfa_settings_form input, .tfa_settings_form textarea, .tfa_settings_form select" ).change(function() {
					tfa_query_leaving = true;
				});
				
				$(".tfa_settings_form input[name='simbatfa_delivery_type']").change(function() {
					$(".tfa_third_party_holder").slideToggle();
				});

				//Save Settings
				$(".simbatfa_settings_save").click(function() {

					$.blockUI({ message: '<div style="margin: 8px;font-size:150%;"><?php echo esc_js(__('Saving...', 'two-factor-authentication' )); ?></div>' });
					
					// https://stackoverflow.com/questions/10147149/how-can-i-override-jquerys-serialize-to-include-unchecked-checkboxes
					var formData = $(".tfa_settings_form input, .tfa_settings_form textarea, .tfa_settings_form select").serialize();
					
					// include unchecked checkboxes. use filter to only include unchecked boxes.
					$.each($(".tfa_settings_form input[type=checkbox]")
					.filter(function(idx){
						return $(this).prop("checked") === false
					}),
					function(idx, el){
						// attach matched element names to the formData with a chosen value.
						var emptyVal = "0";
						formData += "&" + $(el).attr("name") + "=" + emptyVal;
					}
					);

					$.post('<?php echo esc_js($ajax_url);?>', {
						action: "tfa_frontend",
						subaction: "savesettings",
						settings: formData,
						nonce: "<?php echo wp_create_nonce("tfa_frontend_nonce");?>"
					}, function(response) {
						var settings_saved = false;
						try {
							var resp = JSON.parse(response);
							if (resp.hasOwnProperty('result')) {
								settings_saved = true;
								tfa_query_leaving = false;
								// Allow user code to respond
								$(document).trigger('tfa_settings_saved', resp);
							}
							if (resp.hasOwnProperty('message')) {
								alert(resp.message);
							}
							if (resp.hasOwnProperty('qr')) {
								$('.simbaotp_qr_container').data('qrcode', resp['qr']).empty().qrcode({
									"render": "image",
									"text": resp['qr'],
								});
							}
							if (resp.hasOwnProperty('al_type_disp')) {
								$("#al_type_name").html(resp['al_type_disp']['disp']);
								$("#al_type_desc").html(resp['al_type_disp']['desc']);
							}
							
						} catch(err) {
							console.log(err);
							console.log(response);
							<?php if (!isset($also_try)) { ?> alert("<?php echo esc_js(__('Response:', 'two-factor-authentication')); ?> "+response);<?php } ?>
						}
						<?php if (isset($also_try)) { ?>
						if (!settings_saved) {
							$.post('<?php echo esc_js($also_try);?>', {
								action: "tfa_frontend",
								subaction: "savesettings",
								settings: formData,
								nonce: "<?php echo wp_create_nonce("tfa_frontend_nonce");?>"
							}, function(response) {

								try {
									var resp = JSON.parse(response);
									if (resp.hasOwnProperty('result')) {
										settings_saved = true;
										tfa_query_leaving = false;
										// Allow user code to respond
										$(document).trigger('tfa_settings_saved', resp);
									}
									if (resp.hasOwnProperty('message')) {
										alert(resp.message);
									}
									if (resp.hasOwnProperty('qr')) {
										$('.simbaotp_qr_container').data('qrcode', resp['qr']).empty().qrcode({
											"render": "image",
											"text": resp['qr'],
										});
									}
									if (resp.hasOwnProperty('al_type_disp')) {
										$("#al_type_name").html(resp['al_type_disp']['disp']);
										$("#al_type_desc").html(resp['al_type_disp']['desc']);
									}
									
								} catch(err) {
									console.log(err);
									console.log(response);
									alert("<?php echo esc_js(__('Response:', 'two-factor-authentication')); ?> "+response);
								}
								$.unblockUI();
							});
						} else {
							$.unblockUI();
						}
						<?php } else { ?>
							$.unblockUI();
						<?php } ?>
					});

				});
			});
		</script>
		<?php
	}

	/* Main Output function*/
	public function tfa_user_settings_front($atts, $content = null){

		if (!is_user_logged_in()) return '';

		global $current_user;
		
		// We want to print to buffer, since the shortcode API wants the value returned, not echoed
		ob_start();

		$this->get_tfa();

		if (!$this->tfa->isActivatedForUser($current_user->ID)){
			echo __('Two factor authentication is not available for your user.', 'two-factor-authentication');
		} else {

			?>

			<div class="wrap" style="padding-bottom:10px">
				
				<?php $this->mother->settings_intro_notices(); ?>
				
				<?php $this->settings_enable_or_disable_output(); ?>

				<?php $this->mother->current_codes_box(false); ?>

				<?php $this->mother->advanced_settings_box(array($this, 'save_settings_button')); ?>
				
			</div>
			
			<?php $this->save_settings_javascript_output(); ?>

			<?php
		}

		return ob_get_clean();

	}
}
