function parseHash(hash) {
    hash = hash.substring(1, hash.length);

    var hashObj = [];

    hash.split('&').forEach(function (q) {
	if (typeof q !== 'undefined') {
	    hashObj.push(q);
	}
    });

    return hashObj;
}

var swpsmtp_urlHash = 'smtp';
var swpsmtp_focusObj = false;
var swpsmtp_urlHashArr = parseHash(window.location.hash);

if (swpsmtp_urlHashArr[0] !== '') {
    swpsmtp_urlHash = swpsmtp_urlHashArr[0];
}

if (swpsmtp_urlHashArr[1] !== "undefined") {
    swpsmtp_focusObj = swpsmtp_urlHashArr[1];
}

jQuery(function ($) {
    var swpsmtp_activeTab = "";
    $('a.nav-tab').click(function (e) {
	if ($(this).attr('data-tab-name') !== swpsmtp_activeTab) {
	    $('div.swpsmtp-tab-container[data-tab-name="' + swpsmtp_activeTab + '"]').hide();
	    $('a.nav-tab[data-tab-name="' + swpsmtp_activeTab + '"]').removeClass('nav-tab-active');
	    swpsmtp_activeTab = $(this).attr('data-tab-name');
	    $('div.swpsmtp-tab-container[data-tab-name="' + swpsmtp_activeTab + '"]').show();
	    $(this).addClass('nav-tab-active');
	    $('input#swpsmtp-urlHash').val(swpsmtp_activeTab);
	    if (window.location.hash !== swpsmtp_activeTab) {
		window.location.hash = swpsmtp_activeTab;
	    }
	    if (swpsmtp_focusObj) {
		$('html, body').animate({
		    scrollTop: $('#' + swpsmtp_focusObj).offset().top
		}, 'fast', function () {
		    $('#' + swpsmtp_focusObj).focus();
		    swpsmtp_focusObj = false;
		});
	    }
	}
    });
    $('a.nav-tab[data-tab-name="' + swpsmtp_urlHash + '"]').trigger('click');
});

jQuery(function ($) {
    $('#swpsmtp-mail input').not('.ignore-change').change(function () {
	$('#swpsmtp-save-settings-notice').show();
	$('#test-email-form-submit').prop('disabled', true);
    });
    $('#swpsmtp_enable_domain_check').change(function () {
	$('input[name="swpsmtp_allowed_domains"]').prop('disabled', !$(this).is(':checked'));
	$('input[name="swpsmtp_block_all_emails"]').prop('disabled', !$(this).is(':checked'));
    });
    $('#swpsmtp_clear_log_btn').click(function (e) {
	e.preventDefault();
	if (confirm(easywpsmtp.str.clear_log)) {
	    var req = jQuery.ajax({
		url: ajaxurl,
		type: "post",
		data: {action: "swpsmtp_clear_log"}
	    });
	    req.done(function (data) {
		if (data === '1') {
		    alert(easywpsmtp.str.log_cleared);
		} else {
		    alert(easywpsmtp.str.error_occured + ' ' + data);
		}
	    });
	}
    });

    $('#swpsmtp_export_settings_btn').click(function (e) {
	e.preventDefault();
	$('#swpsmtp_export_settings_frm').submit();
    });

    $('#swpsmtp_import_settings_btn').click(function (e) {
	e.preventDefault();
	$('#swpsmtp_import_settings_select_file').click();
    });

    $('#swpsmtp_import_settings_select_file').change(function (e) {
	e.preventDefault();
	$('#swpsmtp_import_settings_frm').submit();
    });

    $('#swpsmtp_self_destruct_btn').click(function (e) {
	e.preventDefault();
	if (confirm(easywpsmtp.str.confirm_self_destruct)) {
	    var req = jQuery.ajax({
		url: ajaxurl,
		type: "post",
		data: {action: "swpsmtp_self_destruct", sd_code: easywpsmtp.sd_code}
	    });
	    req.done(function (data) {
		if (data === '1') {
		    alert(easywpsmtp.str.self_destruct_completed);
		    window.location.href = easywpsmtp.sd_redir_url;
		} else {
		    alert(easywpsmtp.str.error_occured + ' ' + data);
		}
	    });
	    req.fail(function (err) {
		alert(easywpsmtp.str.error_occured + ' ' + err.status + ' (' + err.statusText + ')');
	    });
	}
    });

    $('#test-email-form-submit').click(function () {
	$(this).val(easywpsmtp.str.sending);
	$(this).prop('disabled', true);
	$('#swpsmtp-spinner').addClass('is-active');
	$('#swpsmtp_settings_test_email_form').submit();
	return true;
    });
});
