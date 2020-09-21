var NestedPages = NestedPages || {};

/**
* Reset Settings to Default
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.SettingsReset = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.selectors = {
		resetButton : 'data-nestedpages-reset-settings',
		resetForm : '.nestedpages-reset-settings',
		formComplete : '.nestedpages-reset-settings-complete',
		resetAdminMenu : 'data-nestedpages-reset-admin-menu'
	}

	plugin.bindEvents = function()
	{
		$(document).on('click', '[' + plugin.selectors.resetButton + ']', function(e){
			e.preventDefault();
			plugin.resetSettings();
		});
		$(document).on('click', '[' + plugin.selectors.resetAdminMenu + ']', function(e){
			e.preventDefault();
			plugin.resetAdminMenu();
		});
	}

	plugin.resetSettings = function()
	{
		plugin.loading(true);
		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.resetSettings,
				nonce : NestedPages.jsData.nonce
			},
			success: function(data){
				plugin.loading(false);
				$(plugin.selectors.resetForm).hide();
				$(plugin.selectors.formComplete).show();
				if ( data.status !== 'success' ){
					console.log('There was an error saving toggled pages.');
				}
			}
		});
	}

	plugin.resetAdminMenu = function()
	{
		plugin.loading(true);
		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.resetAdminMenuSettings,
				nonce : NestedPages.jsData.nonce
			},
			success: function(data){
				plugin.loading(false);
				if ( data.status == 'success' ){
					location.reload();
				}
				if ( data.status !== 'success' ){
					console.log('There was an error saving toggled pages.');
				}
			}
		});
	}

	plugin.loading = function(loading)
	{
		if ( loading ){
			$('[' + plugin.selectors.resetButton + ']').attr('disabled', true);
			$('[' + plugin.selectors.resetAdminMenu + ']').attr('disabled', true);
			return;
		}
		$('[' + plugin.selectors.resetButton + ']').removeAttr('disabled');
		$('[' + plugin.selectors.resetAdminMenu + ']').removeAttr('disabled');
	}

	return plugin.bindEvents();
}

new NestedPages.SettingsReset;