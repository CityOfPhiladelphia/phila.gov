/* Disable Gutenberg - Plugin Settings */

(function($) {
	
	$(document).ready(function($) {
		
		$('.disable-gutenberg-reset-options').on('click', function(e) {
			e.preventDefault();
			$('.plugin-modal-dialog').dialog('destroy');
			var link = this;
			var button_names = {}
			button_names[alert_reset_options_true]  = function() { window.location = link.href; }
			button_names[alert_reset_options_false] = function() { $(this).dialog('close'); }
			$('<div class="plugin-modal-dialog">'+ alert_reset_options_message +'</div>').dialog({
				title: alert_reset_options_title,
				buttons: button_names,
				modal: true,
				width: 350
			});
		});
		
	});
	
	$(document).ready(function($){
		
		disable_gutenberg_toggle_settings($);
		disable_gutenberg_toggle_whitelist($);
		disable_gutenberg_toggle_more($);
		
	});
	
})(jQuery);

function disable_gutenberg_toggle_settings($) {
	
	var el = 'table.form-table input[name="disable_gutenberg_options[disable-all]"]';
	var go = $(el +':checked').val();
	
	var title = $('.wrap h2').slice(1,5);
	var text  = $('.wrap form').find('p').not('.g7g-display').not('.submit');
	var table = $('.wrap table').slice(1,5);
	
	if (go) {
		title.hide();
		text.hide();
		table.hide();
	}
	
	$(el).bind('change',function(){
		if ($(this).val()) {
			title.toggle(0);
			text.toggle(0);
			table.toggle(0);
		} else {
			title.hide();
			text.hide();
			table.hide();
		}
	});
	
}

function disable_gutenberg_toggle_whitelist($) {
	
	var el = 'table.form-table input[name="disable_gutenberg_options[whitelist]"]';
	var go = $(el +':checked').val();
	
	var title = $('.wrap h2').slice(5,6);
	var text  = $('.g7g-whitelist');
	var table = $('.wrap table').slice(5,6);
	
	if (!go) {
		title.hide();
		text.hide();
		table.hide();
	}
	
	$(el).bind('change',function(){
		if ($(this).val()) {
			title.toggle(0);
			text.toggle(0);
			table.toggle(0);
		} else {
			title.hide();
			text.hide();
			table.hide();
		}
	});
	
}

function disable_gutenberg_toggle_more($) {
	
	var table = $('.wrap table').slice(6,7);
	var row2  = table.find('tr:nth-child(2)');
	var row3  = table.find('tr:nth-child(3)');
	var row4  = table.find('tr:nth-child(4)');
	var row5  = table.find('tr:nth-child(5)');
	var row6  = table.find('tr:nth-child(6)');
	var row7  = table.find('tr:nth-child(7)');
	
	$(row2).hide();
	$(row3).hide();
	$(row4).hide();
	$(row5).hide();
	$(row6).hide();
	$(row7).hide();
	
	$('.g7g-toggle').click(function(e) {
		e.preventDefault();
		$(row2).slideToggle();
		$(row3).slideToggle();
		$(row4).slideToggle();
		$(row5).slideToggle();
		$(row6).slideToggle();
		$(row7).slideToggle();
		return false;
	});
	
}
