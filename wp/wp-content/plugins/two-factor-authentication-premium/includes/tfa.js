jQuery(document).ready(function($) {
	
	// Return value: whether to submit the form or not
	// Form types: 1 = anything else, 2 = TML shortcode or widget, WP Members, or Ultimate Membership Pro
	// The form parameter is only used for form_type == 2, which is essentially a later bolt-on extra (which explains why there are apparently other form types handled below still covered under 1)
	function runGenerateOTPCall(form_type, form) {

		if (2 == form_type) {
			var username = $(form).find('[name="log"]').val();
		} else {
			var username = $('#user_login').val() || $('[name="log"]').val();
		}
		
		if (!username.length) return false;
		
		var $submit_button = (null === form) ? $('#wp-submit') : $(form).find('input[name="wp-submit"]');
		if ($submit_button.length < 1) {
			$submit_button = $(form).find('input[type="submit"]').first();
		}
					   
		// If this is a "lost password" form, then exit
		if ($('#user_login').parents('#lostpasswordform, #resetpasswordform').length) return false;

		if (simba_tfasettings.hasOwnProperty('spinnerimg')) {
			var styling = 'float:right; margin:6px 12px; width: 20px; height: 20px;';
			if ($('#theme-my-login #wp-submit').length >0) {
				styling = 'margin-left: 4px; position: relative; top: 4px; width: 20px; height: 20px; border:0px; box-shadow:none;';
			}	
			$submit_button.after('<img class="simbaotp_spinner" src="'+simba_tfasettings.spinnerimg+'" style="'+styling+'">');
		}

		$.ajax({
			url: simba_tfasettings.ajaxurl,
			type: 'POST',
			data: {
				action: 'simbatfa-init-otp',
				user: username
			},
			dataType: 'text',
			success: function(resp) {
				try {
					var json_begins = resp.search('{"jsonstarter":"justhere"');
					if (json_begins > -1) {
						if (json_begins > 0) {
							console.log("Expected JSON marker found at position: "+json_begins);
							resp = resp.substring(json_begins);
						}
					} else {
						console.log("Expected JSON marker not found");
						console.log(resp);
					}
					response = JSON.parse(resp);
					if (response.hasOwnProperty('php_output')) {
						console.log("PHP output was returned (follows)");
						console.log(response.php_output);
					}
					if (response.hasOwnProperty('extra_output')) {
						console.log("Extra output was returned (follows)");
						console.log(response.extra_output);
					}
					if (true === response.status) {
						// Don't bother to remove the spinner if the form is being submitted.
						$('.simbaotp_spinner').remove();

						var user_can_trust = (response.hasOwnProperty('user_can_trust') && response.user_can_trust) ? true : false;
						
						var user_already_trusted = (response.hasOwnProperty('user_already_trusted') && response.user_can_trust) ? true : false;
						
						console.log("Simba TFA: User has OTP enabled: showing OTP field (form_type="+form_type+", user_can_trust="+user_can_trust+")");
						
						tfaShowOTPField(form_type, form, user_can_trust, user_already_trusted);
						
					} else {
						console.log("Simba TFA: User does not have OTP enabled: submitting form (form_type="+form_type+")");
						if (2 == form_type) {
							// Form some reason, .submit() stopped working with TML 7.x
							//$(form).submit();
							$(form).find('input[type="submit"], button[type="submit"]').first().click();
						} else {
							$('#wp-submit').parents('form:first').submit();
						}
					}
				} catch(err) {
					$('#login').html(resp);
					console.log("Simba TFA: Error when processing response");
					console.log(err);
					console.log(resp);
				}
			},
			error: function(jq_xhr, text_status, error_thrown) {
				console.log("Simba TFA: AJAX error: "+error_thrown+": "+text_status);
				console.log(jq_xhr);
				if (jq_xhr.hasOwnProperty('responseText')) { console.log(jq_xhr.responseText);}
			}
		});
		return true;
	}
	
	// Parameters: see runGenerateOTPCall
	function tfaShowOTPField(form_type, form, user_can_trust, user_already_trusted) {
		
		var $submit_button;
		
		user_can_trust = ('undefined' == typeof user_can_trust) ? false : user_can_trust;
		user_already_trusted = ('undefined' == typeof user_already_trusted) ? false : user_already_trusted;
		
		if (null === form) {
			$submit_button = $('#wp-submit');
		} else {
			// name="Submit" is WP-Members. 'submit' is Theme My Login starting from 7.x
			$submit_button = $(form).find('input[name="wp-submit"], input[name="Submit"], input[name="submit"]');
			// This hasn't been needed for anything yet (Jul 2018), but is a decent back-stop that would have prevented some breakage in the past that needed manual attention:
			if (0 == $submit_button.length) {
				$submit_button = $(form).find('input[type="submit"]:first');
			}
		}

		// Hide all elements in a browser safe way
		// .user-pass-wrap is the wrapper used (instead of a paragraph) on wp-login.php from WP 5.3
		$submit_button.parents('form:first').find('p, .impu-form-line-fr, .tml-field-wrap, .user-pass-wrap').each(function(i) {
			$(this).css('visibility','hidden').css('position', 'absolute');
		});
		
		// WP-Members
		$submit_button.parents('#wpmem_login').find('fieldset').css('visibility','hidden').css('position', 'absolute');
		
		// Add new field and controls
		var html = '';
		
		html += '<label for="simba_two_factor_auth">' + simba_tfasettings.otp + '<br><input type="text" name="two_factor_code" id="simba_two_factor_auth" autocomplete="off" data-lpignore="true"></label>';
		
		html += '<p class="forgetmenot" style="font-size:small; max-width: 60%">' + simba_tfasettings.otp_login_help

		if (user_can_trust && ('https:' == window.location.protocol || 'localhost' === location.hostname || '127.0.0.1' === location.hostname)) {
			
			html += '<br><input type="checkbox" name="simba_tfa_mark_as_trusted" id="simba_tfa_mark_as_trusted" value="1"><label for="simba_tfa_mark_as_trusted">'+ simba_tfasettings.mark_as_trusted+'</label>';
			
		} else {
			user_already_trusted = false;
		}
		
		html += '</p>';
		
		html += '<p class="submit"><input id="tfa_login_btn" class="button button-primary button-large" type="submit" value="' + $submit_button.val() + '"></p>';
		
// 		if (user_can_trust && user_already_trusted) {
// 			$submit_button.click();
// 			return;
// 		}
		
		$submit_button.prop('disabled', true);
		
		$submit_button.parents('form:first').prepend(html);
		$('#simba_two_factor_auth').focus();
		
		if (user_can_trust && user_already_trusted) {
			$('#simba_two_factor_auth').val(simba_tfasettings.is_trusted);
			$('#tfa_login_btn').click();
		}
		
	}
	
	var tfa_cb = function(e) {
		console.log("Simba TFA: form submit request");

		var form_type = 1;
		var form = null;

		// .tml-login works for both TML 6.x and 7.x.
		if ($(e.target).parents('.tml-login').length > 0 || $(e.target).closest('#wpmem_login').find('form').length > 0 || 'ihc_login_form' == $(e.target).attr('id')) {
			$(e.target).off();
			form_type = 2;
			form = e.target;
		} else {
			$('#wp-submit').parents('form:first').off();
		}

		var res = runGenerateOTPCall(form_type, form);

		if (!res) return true;

		e.preventDefault();
		return false;
	};
	
	// Aug 2017: TML now uses #wp-submit on a reset form; hence the exclusion
	$('#wp-submit').parents('form[name!="resetpassform"]:first').not('.tml-login form[name="loginform"], .tml-login form[name="login"]').on('submit', tfa_cb);
	
	// Theme My Login 6.x - .tml-login form[name="loginform"]
	// Theme My Login 7.x - .tml-login form[name="login"] (Jul 2018)
	// WP Members - Mar 2018
	// Ultimate Membership Pro - April 2018
	$('#ihc_login_form').unbind('submit');
	$('.tml-login form[name="loginform"], .tml-login form[name="login"], #wpmem_login form, form#ihc_login_form').on('submit', tfa_cb);
	
});
