<!-- template="single-item-edit" -->
<div id="ajax-response"></div>
<h2>[+Edit Rule+]</h2>
<form action="[+form_url+]" method="post" class="validate" id="mla-edit-custom-field">
	<input name="mla_custom_field[post_ID]" id="mla-custom-field-post-ID" type="hidden" value="[+post_ID+]" />
	<input name="mla_custom_field[name]" id="mla-custom-field-name" type="hidden" value="[+name+]" />
	<input name="mla_custom_field[rule_name]" id="mla-custom-field-rule-name" type="hidden" value="[+rule_name+]" />
	[+_wpnonce+]
	<table class="form-table" id="mla-edit-custom-field-table">
		<tbody>
			<tr class="form-field custom-field-name-wrap">
				<th scope="row"> <label for="mla-custom-field-display-name">[+Name+]</label>
				</th>
				<td>
					<input name="mla_custom_field[display_name]" id="mla-custom-field-display-name" type="text" readonly="readonly" value="[+display_name+]" />
					<select name="mla_custom_field[new_name]" id="mla-new-custom-field-name" style="display: none;" >
[+new_names+]
					</select>
					<input name="mla_custom_field[new_field]" id="mla-new-custom-field" style="display: none;" type="text" value="">
					<br />
					
<a class="hide-if-no-js" id="mla-change-name-link" 
onclick="
jQuery( '#mla-custom-field-display-name, #mla-new-custom-field, #mla-change-name-link, #mla-cancel-custom-field-link' ).hide();
jQuery( '#mla-new-custom-field-name, #mla-add-custom-field-link, #mla-cancel-name-change-link' ).show();
return false;" 
href="#mla-new-custom-field">
[+Change Name+]
</a>

<a class="hide-if-no-js hidden" id="mla-add-custom-field-link" 
onclick="
jQuery( '#mla-custom-field-display-name, #mla-new-custom-field-name, #mla-change-name-link, #mla-add-custom-field-link' ).hide();
jQuery( '#mla-new-custom-field-name' ).val('none');
jQuery( '#mla-new-custom-field, #mla-cancel-custom-field-link, #mla-cancel-name-change-link' ).show();
return false;" 
href="#mla-new-custom-field">
[+Enter new field+]
</a>

<a class="hide-if-no-js hidden" id="mla-cancel-custom-field-link" 
onclick="
jQuery( '#mla-custom-field-display-name, #mla-new-custom-field, #mla-cancel-custom-field-link, #mla-change-name-link' ).hide();
jQuery( '#mla-new-custom-field' ).val('');
jQuery( '#mla-new-custom-field-name, #mla-add-custom-field-link, #mla-cancel-name-change-link' ).show();
return false;" 
href="#mla-new-custom-field">
[+Cancel new field+]
</a>
&nbsp;&nbsp;&nbsp;
<a class="hide-if-no-js hidden" id="mla-cancel-name-change-link" 
onclick="
jQuery( '#mla-new-custom-field-name, #mla-new-custom-field, #mla-add-custom-field-link, #mla-cancel-custom-field-link, #mla-cancel-name-change-link' ).hide();
jQuery( '#mla-new-custom-field-name' ).val('none');
jQuery( '#mla-new-custom-field' ).val('');
jQuery( '#mla-custom-field-display-name, #mla-change-name-link' ).show();
return false;" 
href="#mla-new-custom-field">
[+Cancel Name Change+]
</a>

					<p class="description">[+Enter Name+]</p></td>
			</tr>
			<tr class="form-field custom-field-data-source-wrap">
				<th scope="row"> <label for="mla-custom-field-data-source">[+Data Source+]</label>
				</th>
				<td><select name="mla_custom_field[data_source]" id="mla-custom-field-data-source">
						
[+data_sources+]
						
					</select></td>
			</tr>
			<tr class="form-field custom-field-meta-name-wrap">
				<th scope="row"> <label for="mla-custom-field-meta-name">[+Meta/Template+]</label>
				</th>
				<td><input name="mla_custom_field[meta_name]" id="mla-custom-field-meta-name" type="text" value="[+meta_name+]" size="40" />
					<p class="description">&nbsp;[+Enter Meta/Template+]</p></td>
			</tr>
			<tr class="form-field custom-field-mla-column-wrap">
				<th scope="row"> <label for="mla-custom-field-mla-column">[+MLA Column+]</label>
				</th>
				<td><input type="checkbox" name="mla_custom_field[mla_column]" id="mla-custom-field-mla-column" [+mla_column+] value="1" />
					&nbsp;[+Check MLA Column+] </td>
			</tr>
			<tr class="form-field custom-field-quick-edit-wrap">
				<th scope="row"> <label for="mla-custom-field-quick-edit">[+Quick Edit+]</label>
				</th>
				<td><input type="checkbox" name="mla_custom_field[quick_edit]" id="mla-custom-field-quick-edit" [+quick_edit+] value="1" />
					&nbsp;[+Check Quick Edit+] </td>
			</tr>
			<tr class="form-field custom-field-bulk-edit-wrap">
				<th scope="row"> <label for="mla-custom-field-bulk-edit">[+Bulk Edit+]</label>
				</th>
				<td><input type="checkbox" name="mla_custom_field[bulk_edit]" id="mla-custom-field-bulk-edit" [+bulk_edit+] value="1" />
					&nbsp;[+Check Bulk Edit+] </td>
			</tr>
			<tr class="form-field custom-field-existing-text-wrap">
				<th scope="row"> <label for="mla-custom-field-existing-text">[+Existing Text+]</label>
				</th>
				<td><select name="mla_custom_field[keep_existing]" id="mla-custom-field-existing-text">
						<option [+keep_selected+] value="1">[+Keep+]</option>
						<option [+replace_selected+] value="">[+Replace+]</option>
					</select></td>
			</tr>
			<tr class="form-field custom-field-format-wrap">
				<th scope="row"> <label for="mla-custom-field-format">[+Format+]</label>
				</th>
				<td><select name="mla_custom_field[format]" id="mla-custom-field-format">
						<option [+native_format+] value="native">[+Native+]</option>
						<option [+commas_format+] value="commas">[+Commas+]</option>
						<option [+raw_format+] value="raw">[+Raw+]</option>
					</select></td>
			</tr>
			<tr class="form-field custom-field-option-wrap">
				<th scope="row"> <label for="mla-custom-field-option">[+Option+]</label>
				</th>
				<td><select name="mla_custom_field[option]" id="mla-custom-field-option">
						<option [+text_option+] value="text">[+Text+]</option>
						<option [+single_option+] value="single">[+Single+]</option>
						<option [+export_option+] value="export">[+Export+]</option>
						<option [+array_option+] value="array">[+Array+]</option>
						<option [+multi_option+] value="multi">[+Multi+]</option>
					</select></td>
			</tr>
			<tr class="form-field custom-field-no-null-wrap">
			<th scope="row">
				<label for="mla-custom-field-no-null">[+Delete NULL+]</label>
			</th>
			<td>
				<input type="checkbox" name="mla_custom_field[no_null]" id="mla-custom-field-no-null" [+no_null+] value="1" />&nbsp;[+Check Delete NULL+]
			</td>
			</tr>
			<tr class="form-field custom-field-status-wrap">
				<th scope="row"> <label for="mla-custom-field-status">[+Status+]</label>
				</th>
				<td><select name="mla_custom_field[status]" id="mla-custom-field-status">
						<option [+active_selected+] value="1">[+Active+]</option>
						<option [+inactive_selected+] value="">[+Inactive+]</option>
					</select></td>
			</tr>
		</tbody>
	</table>
	<p class="submit mla-settings-submit">
		<input name="[+cancel+]" class="button-primary" type="submit" value="[+Cancel+]" />
		&nbsp;
		<input name="[+submit+]" class="button-primary" [+submit_style+] type="submit" value="[+Update+]" />
		&nbsp; </p>
</form>

<!-- template="custom-field-disabled" -->
<h2>[+Support is disabled+]</h2>
<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-custom-field-tab">
    <table class="optiontable">
[+options_list+]
	</table>
    <p class="submit mla-settings-submit">
        <input name="mla-custom-field-options-save" type="submit" class="button-primary" value="[+Save Changes+]" />
    </p>
	<input type="hidden" name="page" value="mla-settings-menu-custom_field" />
	<input type="hidden" name="mla_tab" value="custom_field" />
	[+_wpnonce+]
</form>

<!-- template="before-table" -->
[+mla-progress-div+]
<h2>[+Custom Field Options+]</h2>
<p>[+In this tab+]</p>
<p>[+You can find+]</p>
<div id="ajax-response"></div>
<form action="[+form_url+]" method="get" id="mla-search-custom-field-form">
	<input type="hidden" name="page" value="mla-settings-menu-custom_field" />
	<input type="hidden" name="mla_tab" value="custom_field" />
	[+view_args+]
	[+_wpnonce+]
	<span style="margin-top: 1em">
		<input name="mla-search-custom-field-submit" class="button alignright" id="mla-search-custom-field-submit" type="submit" value="[+Search Rules+]" />
		<label class="screen-reader-text" for="mla-search-custom-field-input">[+Search Rules Text+]:</label>
		<input name="s" class="alignright" id="mla-search-custom-field-input" type="search" value="[+s+]" />
	[+results+]
	</span>
</form>
<br class="clear" />
<div id="col-container">
	<div id="col-right">
		<div class="col-wrap">
			<form action="[+form_url+]" method="post" id="mla-search-custom-field-filter">
				<input type="hidden" name="page" value="mla-settings-menu-custom_field" />
				<input type="hidden" name="mla_tab" value="custom_field" />
				[+view_args+]
				[+_wpnonce+]

<!-- template="after-table" -->
			</form><!-- /id=mla-search-custom-field-filter --> 
		</div><!-- /col-wrap --> 
	</div><!-- /col-right -->
	<div id="col-left">
		<div class="col-wrap">
		<div class="mla-settings-enable-form">
		<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-custom-field-tab">
			<table class="optiontable">
		[+options_list+]
			</table>
			<span class="submit mla-settings-submit">
				<input name="mla-custom-field-options-save" type="submit" class="button-primary" value="[+Save Changes+]" />
			</span>
			<span class="submit mla-settings-submit">
				<input name="custom-field-options-map" type="submit" class="alignright button-primary mla-mapping" value="[+Map All+]" />
			</span>
		[+_wpnonce+]
		</form>
		</div>
			<div class="form-wrap">
				<h2>[+Add New Rule+]</h2>
				<form action="[+form_url+]" method="post" class="validate" id="mla-add-custom-field">
					<input type="hidden" name="page" value="mla-settings-menu-custom_field" />
					<input type="hidden" name="mla_tab" value="custom_field" />
					[+_wpnonce+]
					<table class="form-table" id="mla-add-custom-field-table">
					<tbody>
					<tr class="form-field custom-field-name-wrap">
					<th scope="row">
						<label for="mla-new-custom-field-name">[+Name+]</label>
					</th>
					<td>
						<select name="mla_custom_field[new_name]" id="mla-new-custom-field-name">
[+new_names+]
						</select>
						<input name="mla_custom_field[new_field]" id="mla-new-custom-field" style="display: none;" type="text" value="">
						<br />
<a class="hide-if-no-js" id="mla-add-custom-field-link" 
onclick="
jQuery( '#mla-new-custom-field-name, #mla-add-custom-field-link' ).hide();
jQuery( '#mla-new-custom-field-name' ).val('none');
jQuery( '#mla-new-custom-field, #mla-cancel-custom-field-link' ).show();
return false;" 
href="#mla-new-custom-field">
[+Enter new field+]
</a>

<a class="hide-if-no-js hidden" id="mla-cancel-custom-field-link" 
onclick="
jQuery( '#mla-new-custom-field, #mla-cancel-custom-field-link' ).hide();
jQuery( '#mla-new-custom-field' ).val('');
jQuery( '#mla-new-custom-field-name, #mla-add-custom-field-link' ).show();
return false;" 
href="#mla-new-custom-field">
[+Cancel new field+]
</a>
					</td>
					</tr>
					<tr class="form-field custom-field-data-source-wrap">
					<th scope="row">
						<label for="mla-custom-field-data-source">[+Data Source+]</label>
					</th>
					<td>
						<select style="width: 100%" name="mla_custom_field[data_source]" id="mla-custom-field-data-source">
[+data_sources+]
						</select>
					</td>
					</tr>
					<tr class="form-field custom-field-meta-name-wrap">
					<th scope="row">
						<label for="mla-custom-field-meta-name">[+Meta/Template+]</label>
					</th>
					<td>
						<input name="mla_custom_field[meta_name]" id="mla-custom-field-meta-name" type="text" value="[+meta_name+]" size="40" />
						<p class="description">&nbsp;[+Enter Meta/Template+]</p>
					</td>
					</tr>
					<tr class="form-field custom-field-mla-column-wrap">
					<th scope="row">
						<label for="mla-custom-field-mla-column">[+MLA Column+]</label>
					</th>
					<td>
						<input type="checkbox" name="mla_custom_field[mla_column]" id="mla-custom-field-mla-column" [+mla_column+] value="1" />&nbsp;[+Check MLA Column+]
					</td>
					</tr>
					<tr class="form-field custom-field-quick-edit-wrap">
					<th scope="row">
						<label for="mla-custom-field-quick-edit">[+Quick Edit+]</label>
					</th>
					<td>
						<input type="checkbox" name="mla_custom_field[quick_edit]" id="mla-custom-field-quick-edit" [+quick_edit+] value="1" />&nbsp;[+Check Quick Edit+]
					</td>
					</tr>
					<tr class="form-field custom-field-bulk-edit-wrap">
					<th scope="row">
						<label for="mla-custom-field-bulk-edit">[+Bulk Edit+]</label>
					</th>
					<td>
						<input type="checkbox" name="mla_custom_field[bulk_edit]" id="mla-custom-field-bulk-edit" [+bulk_edit+] value="1" />&nbsp;[+Check Bulk Edit+]
					</td>
					</tr>
					<tr class="form-field custom-field-existing-text-wrap">
					<th scope="row">
						<label for="mla-custom-field-existing-text">[+Existing Text+]</label>
					</th>
					<td>
						<select name="mla_custom_field[keep_existing]" id="mla-custom-field-existing-text">
							<option [+keep_selected+] value="1">[+Keep+]</option>
							<option [+replace_selected+] value="">[+Replace+]</option>
						</select>
					</td>
					</tr>
					<tr class="form-field custom-field-format-wrap">
					<th scope="row">
						<label for="mla-custom-field-format">[+Format+]</label>
					</th>
					<td>
						<select name="mla_custom_field[format]" id="mla-custom-field-format">
							<option [+native_format+] value="native">[+Native+]</option>
							<option [+commas_format+] value="commas">[+Commas+]</option>
							<option [+raw_format+] value="raw">[+Raw+]</option>
						</select>
					</td>
					</tr>
					<tr class="form-field custom-field-option-wrap">
					<th scope="row">
						<label for="mla-custom-field-option">[+Option+]</label>
					</th>
					<td>
						<select name="mla_custom_field[option]" id="mla-custom-field-option">
							<option [+text_option+] value="text">[+Text+]</option>
							<option [+single_option+] value="single">[+Single+]</option>
							<option [+export_option+] value="export">[+Export+]</option>
							<option [+array_option+] value="array">[+Array+]</option>
							<option [+multi_option+] value="multi">[+Multi+]</option>
						</select>
					</td>
					</tr>
					<tr class="form-field custom-field-no-null-wrap">
					<th scope="row">
						<label for="mla-custom-field-no-null">[+Delete NULL+]</label>
					</th>
					<td>
						<input type="checkbox" name="mla_custom_field[no_null]" id="mla-custom-field-no-null" [+no_null+] value="1" />&nbsp;[+Check Delete NULL+]
					</td>
					</tr>
					<tr class="form-field custom-field-status-wrap">
					<th scope="row">
						<label for="mla-custom-field-status">[+Status+]</label>
					</th>
					<td>
							<select name="mla_custom_field[status]" id="mla-custom-field-status">
								<option [+active_selected+] value="1">[+Active+]</option>
								<option [+inactive_selected+] value="">[+Inactive+]</option>
							</select>
					</td>
					</tr>
					</tbody>
					</table>
					<p class="submit mla-settings-submit">
						<input type="submit" name="mla-add-custom-field-submit" id="mla-add-custom-field-submit" class="button button-primary" value="[+Add Rule+]" />
					</p>
				</form><!-- /id=mla-add-custom-field --> 
			</div><!-- /form-wrap --> 
		</div><!-- /col-wrap -->
	</div><!-- /col-left --> 
</div><!-- /col-container -->
<form>
	<table width="99%" style="display: none">
		<tbody id="inlineedit">
			<tr id="inline-edit" class="inline-edit-row inline-edit-row-custom inline-edit-custom quick-edit-row quick-edit-row-custom quick-edit-custom" style="display: none">
				<td colspan="[+colspan+]" class="colspanchange">
					<fieldset class="inline-edit-col">
						<div class="inline-edit-col">
							<h4>[+Quick Edit+]: <input name="name" class="ptitle" type="text" readonly="readonly" value="" /></h4>
							<div class="inline-edit-group">
							<label class="alignleft"> <span class="title">[+Data Source+]</span> <span class="input-text-wrap">
								<select name="data_source">
[+data_sources+]
								</select>
								</span> </label>
							</div>
							<div class="inline-edit-group">
							<label class="alignleft"> <span class="title">[+Meta/Template+]</span> <span class="input-text-wrap">
								<input name="meta_name" class="ptitle" type="text" value="" />
								</span> </label>
							</div>
							<div class="inline-edit-group">
								<label class="alignleft checkbox-label">
								<input name="mla_column" class="ptitle" type="checkbox" checked="checked" value="1" />
								</span> <span class="checkbox-title">[+MLA Column+]</span>
								</label> 
								<label class="alignleft checkbox-label">
								<input name="quick_edit" class="ptitle" type="checkbox" checked="checked" value="1" />
								</span> <span class="checkbox-title">[+Quick Edit+]</span>
								</label> 
								<label class="alignleft checkbox-label">
								<input name="bulk_edit" class="ptitle" type="checkbox" checked="checked" value="1" />
								</span> <span class="checkbox-title">[+Bulk Edit+]</span>
								</label> 
							</div>
							<div class="inline-edit-group">
								<label class="alignleft"> <span class="dropdown-title">[+Existing Text+]</span> <span class="input-dropdown-wrap">
								<select name="keep_existing">
									<option value="1">[+Keep+]</option>
									<option value="">[+Replace+]</option>
								</select>
								</span> </label> 
								<label class="alignleft"> <span class="dropdown-title">[+Format+]</span> <span class="input-dropdown-wrap">
								<select name="format">
									<option value="native">[+Native+]</option>
									<option value="commas">[+Commas+]</option>
									<option value="raw">[+Raw+]</option>
								</select>
								</span>	</label> 
								<label class="alignleft"> <span class="dropdown-title">[+Option+]</span> <span class="input-dropdown-wrap">
								<select name="option">
									<option value="text">[+Text+]</option>
									<option value="single">[+Single+]</option>
									<option value="export">[+Export+]</option>
									<option value="array">[+Array+]</option>
									<option value="multi">[+Multi+]</option>
								</select>
								</span> </label> 
							</div>
							<div class="inline-edit-group">
								<label class="alignleft checkbox-label">
								<input name="no_null" class="ptitle" type="checkbox" value="1" />
								</span> <span class="checkbox-title">[+Delete NULL+]</span>
								</label> 
								<label class="alignleft"> <span class="dropdown-title">[+Status+]</span> <span class="input-dropdown-wrap">
								<select name="status">
									<option value="1">[+Active+]</option>
									<option value="">[+Inactive+]</option>
								</select>
								</span> </label>
							</div>
						</div>
					</fieldset>
					<p class="submit inline-edit-save">
						<a accesskey="c" href="#inline-edit" class="cancel button-secondary alignleft">[+Cancel+]</a>
						<a accesskey="s" href="#inline-edit" class="save button-primary alignright">[+Update+]</a>
						<input type="hidden" name="rule_name" value="" />
						<input type="hidden" name="page" value="mla-settings-menu-custom_field" />
						<input type="hidden" name="mla_tab" value="custom_field" />
						<input type="hidden" name="screen" value="settings_page_mla-settings-menu-custom_field" />
						<span class="spinner"></span>
						<span class="error" style="display: none;"></span>
						<br class="clear" />
					</p>
				</td>
			</tr>
			<tr id="bulk-edit" class="inline-edit-row inline-edit-row-custom inline-edit-custom bulk-edit-row bulk-edit-row-custom bulk-edit-custom" style="display: none">
				<td colspan="[+colspan+]" class="colspanchange">
					<h4>[+Bulk Edit+]</h4>
					<fieldset class="inline-edit-col-left">
						<div class="inline-edit-col">
							<div id="bulk-title-div">
								<div id="bulk-titles"></div>
							</div>
						</div>
					</fieldset>
					<fieldset class="inline-edit-col-right">
						<div class="inline-edit-col">
							<div class="inline-edit-group">
							<label class="alignleft inline-edit-mla_column"> <span class="dropdown-title">[+MLA Column+]</span>
							<select name="mla_column">
								<option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
								<option value="1">[+Yes+]</option>
								<option value="">[+No+]</option>
							</select>
							</label>
							<label class="alignleft inline-edit-quick_edit"> <span class="dropdown-title">[+Quick Edit+]</span>
							<select name="quick_edit">
								<option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
								<option value="1">[+Yes+]</option>
								<option value="">[+No+]</option>
							</select>
							</label>
							</div>
							<div class="inline-edit-group">
							<label class="alignleft inline-edit-bulk_edit"> <span class="dropdown-title">[+Bulk Edit+]</span>
							<select name="bulk_edit">
								<option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
								<option value="1">[+Yes+]</option>
								<option value="">[+No+]</option>
							</select>
							</label>
							<label class="alignleft inline-edit-keep_existing"> <span class="dropdown-title">[+Existing Text+]</span> 
							<select name="keep_existing">
								<option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
								<option value="1">[+Keep+]</option>
								<option value="">[+Replace+]</option>
							</select>
							</label>
							</div>
							<div class="inline-edit-group">
							<label class="alignleft inline-edit-format"> <span class="dropdown-title">[+Format+]</span>
							<select name="format">
								<option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
								<option value="native">[+Native+]</option>
								<option value="commas">[+Commas+]</option>
								<option value="raw">[+Raw+]</option>
							</select>
							</label>
							<label class="alignleft inline-edit-option"> <span class="dropdown-title">[+Option+]</span>
							<select name="option">
								<option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
								<option value="text">[+Text+]</option>
								<option value="single">[+Single+]</option>
								<option value="export">[+Export+]</option>
								<option value="array">[+Array+]</option>
								<option value="multi">[+Multi+]</option>
							</select>
							</label>
							</div>
							<div class="inline-edit-group">
							<label class="alignleft inline-edit-no_null"> <span class="dropdown-title">[+Delete NULL+]</span>
							<select name="no_null">
								<option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
								<option value="1">[+Yes+]</option>
								<option value="">[+No+]</option>
							</select>
							</label>
							<label class="alignleft inline-edit-active"> <span class="dropdown-title">[+Status+]</span>
							<select name="active">
								<option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
								<option value="1">[+Active+]</option>
								<option value="">[+Inactive+]</option>
							</select>
							</label>
							</div>
						</div>
					</fieldset>
					<p class="submit inline-edit-save">
						<a accesskey="c" href="#inline-edit" class="cancel button-secondary alignleft">[+Cancel+]</a>
						<input accesskey="s" type="submit" name="bulk_update" id="bulk_update" class="save button-primary alignright" value="[+Update+]"  />
						<input type="hidden" name="page" value="mla-settings-menu-custom_field" />
						<input type="hidden" name="mla_tab" value="custom_field" />
						<input type="hidden" name="screen" value="settings_page_mla-settings-menu-custom_field" />
						<span class="error" style="display:none"></span>
						<br class="clear" />
					</p>
				</td>
			</tr>
		</tbody>
	</table>
</form>
