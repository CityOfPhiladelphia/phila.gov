(function($) {

	if (typeof _wpcmsb == 'undefined' || _wpcmsb === null)
		_wpcmsb = {};

	_wpcmsb = $.extend({ cached: 0 }, _wpcmsb);

	$(function() {
		_wpcmsb.supportHtml5 = $.wpcmsbSupportHtml5();
		$('div.wpcmsb > form').wpcmsbInitForm();
	});

	$.fn.wpcmsbInitForm = function() {
		this.ajaxForm({
			beforeSubmit: function(arr, $form, options) {
				$form.wpcmsbClearResponseOutput();
				$form.find('[aria-invalid]').attr('aria-invalid', 'false');
				$form.find('img.ajax-loader').css({ visibility: 'visible' });
				return true;
			},
			beforeSerialize: function($form, options) {
				$form.find('[placeholder].placeheld').each(function(i, n) {
					$(n).val('');
				});
				return true;
			},
			data: { '_wpcmsb_is_ajax_call': 1 },
			dataType: 'json',
			success: $.wpcmsbAjaxSuccess,
			error: function(xhr, status, error, $form) {
				var e = $('<div class="ajax-error"></div>').text(error.message);
				$form.after(e);
			}
		});

		if (_wpcmsb.cached)
			this.wpcmsbOnloadRefill();

		this.wpcmsbToggleSubmit();

		this.find('.wpcmsb-submit').wpcmsbAjaxLoader();

		this.find('.wpcmsb-acceptance').click(function() {
			$(this).closest('form').wpcmsbToggleSubmit();
		});

		this.find('.wpcmsb-exclusive-checkbox').wpcmsbExclusiveCheckbox();

		this.find('.wpcmsb-list-item.has-free-text').wpcmsbToggleCheckboxFreetext();

		this.find('[placeholder]').wpcmsbPlaceholder();

		if (_wpcmsb.jqueryUi && ! _wpcmsb.supportHtml5.date) {
			this.find('input.wpcmsb-date[type="date"]').each(function() {
				$(this).datepicker({
					dateFormat: 'yy-mm-dd',
					minDate: new Date($(this).attr('min')),
					maxDate: new Date($(this).attr('max'))
				});
			});
		}

		if (_wpcmsb.jqueryUi && ! _wpcmsb.supportHtml5.number) {
			this.find('input.wpcmsb-number[type="number"]').each(function() {
				$(this).spinner({
					min: $(this).attr('min'),
					max: $(this).attr('max'),
					step: $(this).attr('step')
				});
			});
		}

		this.find('.wpcmsb-character-count').wpcmsbCharacterCount();

		this.find('.wpcmsb-validates-as-url').change(function() {
			$(this).wpcmsbNormalizeUrl();
		});
	};

	$.wpcmsbAjaxSuccess = function(data, status, xhr, $form) {
		if (! $.isPlainObject(data) || $.isEmptyObject(data))
			return;

		var $responseOutput = $form.find('div.wpcmsb-response-output');

		$form.wpcmsbClearResponseOutput();

		$form.find('.wpcmsb-form-control').removeClass('wpcmsb-not-valid');
		$form.removeClass('invalid spam sent failed');

		if (data.captcha)
			$form.wpcmsbRefillCaptcha(data.captcha);

		if (data.quiz)
			$form.wpcmsbRefillQuiz(data.quiz);

		if (data.invalids) {
			$.each(data.invalids, function(i, n) {
				$form.find(n.into).wpcmsbNotValidTip(n.message);
				$form.find(n.into).find('.wpcmsb-form-control').addClass('wpcmsb-not-valid');
				$form.find(n.into).find('[aria-invalid]').attr('aria-invalid', 'true');
			});

			$responseOutput.addClass('wpcmsb-validation-errors');
			$form.addClass('invalid');

			$(data.into).trigger('invalid.wpcmsb');

		} else if (1 == data.spam) {
			$responseOutput.addClass('wpcmsb-spam-blocked');
			$form.addClass('spam');

			$(data.into).trigger('spam.wpcmsb');

		} else if (1 == data.mailSent) {
			$responseOutput.addClass('wpcmsb-mail-sent-ok');
			$form.addClass('sent');

			if (data.onSentOk)
				$.each(data.onSentOk, function(i, n) { eval(n) });

			$(data.into).trigger('mailsent.wpcmsb');

		} else {
			$responseOutput.addClass('wpcmsb-mail-sent-ng');
			$form.addClass('failed');

			$(data.into).trigger('mailfailed.wpcmsb');
		}

		if (data.onSubmit)
			$.each(data.onSubmit, function(i, n) { eval(n) });

		$(data.into).trigger('submit.wpcmsb');

		if (1 == data.mailSent)
			$form.resetForm();

		$form.find('[placeholder].placeheld').each(function(i, n) {
			$(n).val($(n).attr('placeholder'));
		});

		$responseOutput.append(data.message).slideDown('fast');
		$responseOutput.attr('role', 'alert');

		$.wpcmsbUpdateScreenReaderResponse($form, data);
	};

	$.fn.wpcmsbExclusiveCheckbox = function() {
		return this.find('input:checkbox').click(function() {
			var name = $(this).attr('name');
			$(this).closest('form').find('input:checkbox[name="' + name + '"]').not(this).prop('checked', false);
		});
	};

	$.fn.wpcmsbPlaceholder = function() {
		if (_wpcmsb.supportHtml5.placeholder)
			return this;

		return this.each(function() {
			$(this).val($(this).attr('placeholder'));
			$(this).addClass('placeheld');

			$(this).focus(function() {
				if ($(this).hasClass('placeheld'))
					$(this).val('').removeClass('placeheld');
			});

			$(this).blur(function() {
				if ('' == $(this).val()) {
					$(this).val($(this).attr('placeholder'));
					$(this).addClass('placeheld');
				}
			});
		});
	};

	$.fn.wpcmsbAjaxLoader = function() {
		return this.each(function() {
			var loader = $('<img class="ajax-loader" />')
				.attr({ src: _wpcmsb.loaderUrl, alt: _wpcmsb.sending })
				.css('visibility', 'hidden');

			$(this).after(loader);
		});
	};

	$.fn.wpcmsbToggleSubmit = function() {
		return this.each(function() {
			var form = $(this);
			if (this.tagName.toLowerCase() != 'form')
				form = $(this).find('form').first();

			if (form.hasClass('wpcmsb-acceptance-as-validation'))
				return;

			var submit = form.find('input:submit');
			if (! submit.length) return;

			var acceptances = form.find('input:checkbox.wpcmsb-acceptance');
			if (! acceptances.length) return;

			submit.removeAttr('disabled');
			acceptances.each(function(i, n) {
				n = $(n);
				if (n.hasClass('wpcmsb-invert') && n.is(':checked')
				|| ! n.hasClass('wpcmsb-invert') && ! n.is(':checked'))
					submit.attr('disabled', 'disabled');
			});
		});
	};

	$.fn.wpcmsbToggleCheckboxFreetext = function() {
		return this.each(function() {
			var $wrap = $(this).closest('.wpcmsb-form-control');

			if ($(this).find(':checkbox, :radio').is(':checked')) {
				$(this).find(':input.wpcmsb-free-text').prop('disabled', false);
			} else {
				$(this).find(':input.wpcmsb-free-text').prop('disabled', true);
			}

			$wrap.find(':checkbox, :radio').change(function() {
				var $cb = $('.has-free-text', $wrap).find(':checkbox, :radio');
				var $freetext = $(':input.wpcmsb-free-text', $wrap);

				if ($cb.is(':checked')) {
					$freetext.prop('disabled', false).focus();
				} else {
					$freetext.prop('disabled', true);
				}
			});
		});
	};

	$.fn.wpcmsbCharacterCount = function() {
		return this.each(function() {
			var $count = $(this);
			var name = $count.attr('data-target-name');
			var down = $count.hasClass('down');
			var starting = parseInt($count.attr('data-starting-value'), 10);
			var maximum = parseInt($count.attr('data-maximum-value'), 10);
			var minimum = parseInt($count.attr('data-minimum-value'), 10);

			var updateCount = function($target) {
				var length = $target.val().length;
				var count = down ? starting - length : length;
				$count.attr('data-current-value', count);
				$count.text(count);

				if (maximum && maximum < length) {
					$count.addClass('too-long');
				} else {
					$count.removeClass('too-long');
				}

				if (minimum && length < minimum) {
					$count.addClass('too-short');
				} else {
					$count.removeClass('too-short');
				}
			};

			$count.closest('form').find(':input[name="' + name + '"]').each(function() {
				updateCount($(this));

				$(this).keyup(function() {
					updateCount($(this));
				});
			});
		});
	};

	$.fn.wpcmsbNormalizeUrl = function() {
		return this.each(function() {
			var val = $.trim($(this).val());

			if (val && ! val.match(/^[a-z][a-z0-9.+-]*:/i)) { // check the scheme part
				val = val.replace(/^\/+/, '');
				val = 'http://' + val;
			}

			$(this).val(val);
		});
	};

	$.fn.wpcmsbNotValidTip = function(message) {
		return this.each(function() {
			var $into = $(this);

			$into.find('span.wpcmsb-not-valid-tip').remove();
			$into.append('<span role="alert" class="wpcmsb-not-valid-tip">' + message + '</span>');

			if ($into.is('.use-floating-validation-tip *')) {
				$('.wpcmsb-not-valid-tip', $into).mouseover(function() {
					$(this).wpcmsbFadeOut();
				});

				$(':input', $into).focus(function() {
					$('.wpcmsb-not-valid-tip', $into).not(':hidden').wpcmsbFadeOut();
				});
			}
		});
	};

	$.fn.wpcmsbFadeOut = function() {
		return this.each(function() {
			$(this).animate({
				opacity: 0
			}, 'fast', function() {
				$(this).css({'z-index': -100});
			});
		});
	};

	$.fn.wpcmsbOnloadRefill = function() {
		return this.each(function() {
			var url = $(this).attr('action');
			if (0 < url.indexOf('#'))
				url = url.substr(0, url.indexOf('#'));

			var id = $(this).find('input[name="_wpcmsb"]').val();
			var unitTag = $(this).find('input[name="_wpcmsb_unit_tag"]').val();

			$.getJSON(url,
				{ _wpcmsb_is_ajax_call: 1, _wpcmsb: id, _wpcmsb_request_ver: $.now() },
				function(data) {
					if (data && data.captcha)
						$('#' + unitTag).wpcmsbRefillCaptcha(data.captcha);

					if (data && data.quiz)
						$('#' + unitTag).wpcmsbRefillQuiz(data.quiz);
				}
			);
		});
	};

	$.fn.wpcmsbRefillCaptcha = function(captcha) {
		return this.each(function() {
			var form = $(this);

			$.each(captcha, function(i, n) {
				form.find(':input[name="' + i + '"]').clearFields();
				form.find('img.wpcmsb-captcha-' + i).attr('src', n);
				var match = /([0-9]+)\.(png|gif|jpeg)$/.exec(n);
				form.find('input:hidden[name="_wpcmsb_captcha_challenge_' + i + '"]').attr('value', match[1]);
			});
		});
	};

	$.fn.wpcmsbRefillQuiz = function(quiz) {
		return this.each(function() {
			var form = $(this);

			$.each(quiz, function(i, n) {
				form.find(':input[name="' + i + '"]').clearFields();
				form.find(':input[name="' + i + '"]').siblings('span.wpcmsb-quiz-label').text(n[0]);
				form.find('input:hidden[name="_wpcmsb_quiz_answer_' + i + '"]').attr('value', n[1]);
			});
		});
	};

	$.fn.wpcmsbClearResponseOutput = function() {
		return this.each(function() {
			$(this).find('div.wpcmsb-response-output').hide().empty().removeClass('wpcmsb-mail-sent-ok wpcmsb-mail-sent-ng wpcmsb-validation-errors wpcmsb-spam-blocked').removeAttr('role');
			$(this).find('span.wpcmsb-not-valid-tip').remove();
			$(this).find('img.ajax-loader').css({ visibility: 'hidden' });
		});
	};

	$.wpcmsbUpdateScreenReaderResponse = function($form, data) {
		$('.wpcmsb .screen-reader-response').html('').attr('role', '');

		if (data.message) {
			var $response = $form.siblings('.screen-reader-response').first();
			$response.append(data.message);

			if (data.invalids) {
				var $invalids = $('<ul></ul>');

				$.each(data.invalids, function(i, n) {
					if (n.idref) {
						var $li = $('<li></li>').append($('<a></a>').attr('href', '#' + n.idref).append(n.message));
					} else {
						var $li = $('<li></li>').append(n.message);
					}

					$invalids.append($li);
				});

				$response.append($invalids);
			}

			$response.attr('role', 'alert').focus();
		}
	};

	$.wpcmsbSupportHtml5 = function() {
		var features = {};
		var input = document.createElement('input');

		features.placeholder = 'placeholder' in input;

		var inputTypes = ['email', 'url', 'tel', 'number', 'range', 'date'];

		$.each(inputTypes, function(index, value) {
			input.setAttribute('type', value);
			features[value] = input.type !== 'text';
		});

		return features;
	};

})(jQuery);
