<!-- template="parent-select" -->
            <select name="mla_iptc_exif_rule[parent]" id="mla-iptc-exif-parent">
[+options+]
            </select>

<!-- template="single-item-edit" -->

<div id="ajax-response"></div>
<h2>[+Edit Rule+]</h2>
<form action="[+form_url+]" method="post" class="validate" id="mla-edit-iptc-exif">
	<input name="mla_iptc_exif_rule[post_ID]" id="mla-iptc-exif-post-ID" type="hidden" value="[+post_ID+]" />
	<input name="mla_iptc_exif_rule[type]" id="mla-iptc-exif-type" type="hidden" value="[+type+]" />
	<input name="mla_iptc_exif_rule[key]" id="mla-iptc-exif-key" type="hidden" value="[+key+]" />
	<input name="mla_iptc_exif_rule[name]" id="mla-iptc-exif-name" type="hidden" value="[+name+]" />
	<input name="mla_iptc_exif_rule[rule_name]" id="mla-iptc-exif-rule-name" type="hidden" value="[+rule_name+]" />
	<input name="mla_iptc_exif_rule[hierarchical]" id="mla-iptc-exif-hierarchical" type="hidden" value="[+hierarchical+]" />
	[+_wpnonce+]
	<table class="form-table" id="mla-edit-iptc-exif-table">
		<tbody>
			<tr class="form-field iptc-exif-name-wrap">
				<th scope="row"> <label for="mla-iptc-exif-display-name">[+Name+]</label>
				</th>
				<td><input name="mla_iptc_exif_rule[display_name]" id="mla-iptc-exif-display-name" type="text" readonly="readonly" value="[+display_name+]" />
					<select name="mla_iptc_exif_rule[new_name]" id="mla-new-iptc-exif-name" style="display: none;" >
						
[+new_names+]
					
					</select>
					<input name="mla_iptc_exif_rule[new_field]" id="mla-new-iptc-exif" style="display: none;" type="text" value="">
					<br />
					<a class="hide-if-no-js [+custom_class+]" id="mla-change-name-link" 
onclick="
jQuery( '#mla-iptc-exif-display-name, #mla-new-iptc-exif, #mla-change-name-link, #mla-cancel-iptc-exif-link' ).hide();
jQuery( '#mla-new-iptc-exif-name, #mla-add-iptc-exif-link, #mla-cancel-name-change-link' ).show();
return false;" 
href="#mla-new-iptc-exif"> [+Change Name+] </a> <a class="hide-if-no-js hidden" id="mla-add-iptc-exif-link" 
onclick="
jQuery( '#mla-iptc-exif-display-name, #mla-new-iptc-exif-name, #mla-change-name-link, #mla-add-iptc-exif-link' ).hide();
jQuery( '#mla-new-iptc-exif-name' ).val('none');
jQuery( '#mla-new-iptc-exif, #mla-cancel-iptc-exif-link, #mla-cancel-name-change-link' ).show();
return false;" 
href="#mla-new-iptc-exif"> [+Enter new field+] </a> <a class="hide-if-no-js hidden" id="mla-cancel-iptc-exif-link" 
onclick="
jQuery( '#mla-iptc-exif-display-name, #mla-new-iptc-exif, #mla-cancel-iptc-exif-link, #mla-change-name-link' ).hide();
jQuery( '#mla-new-iptc-exif' ).val('');
jQuery( '#mla-new-iptc-exif-name, #mla-add-iptc-exif-link, #mla-cancel-name-change-link' ).show();
return false;" 
href="#mla-new-iptc-exif"> [+Cancel new field+] </a> &nbsp;&nbsp;&nbsp; <a class="hide-if-no-js hidden" id="mla-cancel-name-change-link" 
onclick="
jQuery( '#mla-new-iptc-exif-name, #mla-new-iptc-exif, #mla-add-iptc-exif-link, #mla-cancel-iptc-exif-link, #mla-cancel-name-change-link' ).hide();
jQuery( '#mla-new-iptc-exif-name' ).val('none');
jQuery( '#mla-new-iptc-exif' ).val('');
jQuery( '#mla-iptc-exif-display-name, #mla-change-name-link' ).show();
return false;" 
href="#mla-new-iptc-exif"> [+Cancel Name Change+] </a>
					<p class="description [+custom_class+]">[+Enter Name+]</p></td>
			</tr>
			<tr class="form-field iptc-exif-iptc-value-wrap">
				<th scope="row"> <label for="mla-iptc-exif-iptc-value">[+IPTC Value+]</label>
				</th>
				<td><select name="mla_iptc_exif_rule[iptc_value]" id="mla-iptc-exif-iptc-value">
[+iptc_field_options+]
					</select></td>
			</tr>
			<tr class="form-field iptc-exif-exif-value-wrap">
				<th scope="row"> <label for="mla-iptc-exif-exif-value">[+EXIF/Template Value+]</label>
				</th>
				<td><input name="mla_iptc_exif_rule[exif_value]" id="mla-iptc-exif-exif-value" type="text" value="[+exif_text+]" size="[+exif_size+]" />
					<p class="description">&nbsp;[+Enter EXIF/Template+]</p></td>
			</tr>
			<tr class="form-field iptc-exif-iptc-first-wrap">
				<th scope="row"> <label for="mla-iptc-exif-iptc-first">[+Priority+]</label>
				</th>
				<td><select name="mla_iptc_exif_rule[iptc_first]" id="mla-iptc-exif-iptc-first">
                <option [+iptc_selected+] value="1">[+IPTC+]</option>
                <option [+exif_selected+] value="">[+EXIF+]</option>
            </select></td>
			</tr>
			<tr class="form-field iptc-exif-keep-existing-wrap">
				<th scope="row"> <label for="mla-iptc-exif-keep-existing">[+Existing Text+]</label>
				</th>
				<td><select name="mla_iptc_exif_rule[keep_existing]" id="mla-iptc-exif-keep-existing">
                <option [+keep_selected+] value="1">[+Keep+]</option>
                <option [+replace_selected+] value="">[+Replace+]</option>
            </select></td>
			</tr>
			<tr class="form-field iptc-exif-delimiters-wrap [+taxonomy_class+]">
				<th scope="row"> <label for="mla-iptc-exif-delimiters">[+Delimiters+]</label>
				</th>
				<td><input name="mla_iptc_exif_rule[delimiters]" id="mla-iptc-exif-delimiters" type="text" value="[+delimiters_text+]" size="[+delimiters_size+]" style="width: [+delimiters_size+]em" /></td>
			</tr>
			<tr class="form-field iptc-exif-parent-wrap [+parent_class+]">
				<th scope="row"> <label for="mla-iptc-exif-parent">[+Parent+]</label>
				</th>
				<td>[+parent_select+]</td>
			</tr>
			<tr class="form-field iptc-exif-format-wrap [+custom_class+]">
				<th scope="row"> <label for="mla-iptc-exif-format">[+Format+]</label>
				</th>
				<td><select name="mla_iptc_exif_rule[format]" id="mla-iptc-exif-format">
						<option [+native_format+] value="native">[+Native+]</option>
						<option [+commas_format+] value="commas">[+Commas+]</option>
						<option [+raw_format+] value="raw">[+Raw+]</option>
					</select></td>
			</tr>
			<tr class="form-field iptc-exif-option-wrap [+custom_class+]">
				<th scope="row"> <label for="mla-iptc-exif-option">[+Option+]</label>
				</th>
				<td><select name="mla_iptc_exif_rule[option]" id="mla-iptc-exif-option">
						<option [+text_option+] value="text">[+Text+]</option>
						<option [+single_option+] value="single">[+Single+]</option>
						<option [+export_option+] value="export">[+Export+]</option>
						<option [+array_option+] value="array">[+Array+]</option>
						<option [+multi_option+] value="multi">[+Multi+]</option>
					</select></td>
			</tr>
			<tr class="form-field iptc-exif-no-null-wrap [+custom_class+]">
				<th scope="row"> <label for="mla-iptc-exif-no-null">[+Delete NULL+]</label>
				</th>
				<td><input type="checkbox" name="mla_iptc_exif_rule[no_null]" id="mla-iptc-exif-no-null" [+no_null+] value="1" />
					&nbsp;[+Check Delete NULL+] </td>
			</tr>
			<tr class="form-field iptc-exif-status-wrap">
				<th scope="row"> <label for="mla-iptc-exif-status">[+Status+]</label>
				</th>
				<td><select name="mla_iptc_exif_rule[status]" id="mla-iptc-exif-status">
						<option [+active_selected+] value="1">[+Active+]</option>
						<option [+inactive_selected+] value="">[+Inactive+]</option>
					</select></td>
			</tr>
		</tbody>
	</table>
	<p class="submit mla-settings-submit">
		<input name="[+cancel+]" class="button-primary" type="submit" value="[+Cancel+]" />
		&nbsp;
		<input name="[+submit+]" class="button-primary" type="submit" value="[+Update+]" />
		&nbsp; </p>
</form>

<!-- template="iptc-exif-disabled" -->
<h2>[+Support is disabled+]</h2>
<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-iptc-exif-tab">
    <table class="optiontable">
[+options_list+]
	</table>
    <p class="submit mla-settings-submit">
        <input name="mla-iptc-exif-options-save" type="submit" class="button-primary" value="[+Save Changes+]" />
    </p>
	<input type="hidden" name="page" value="mla-settings-menu-iptc_exif" />
	<input type="hidden" name="mla_tab" value="iptc_exif" />
	[+_wpnonce+]
</form>

<!-- template="before-table" --> 
[+mla-progress-div+]
<h2>[+IPTC EXIF Options+]</h2>
<p>[+In this tab+]</p>
<p>[+You can find+]</p>
<div id="ajax-response"></div>
<form action="[+form_url+]" method="get" id="mla-search-iptc-exif-form">
	<input type="hidden" name="page" value="mla-settings-menu-iptc_exif" />
	<input type="hidden" name="mla_tab" value="iptc_exif" />
	[+view_args+]
	[+_wpnonce+] <span style="margin-top: 1em">
	<input name="mla-search-iptc-exif-submit" class="button alignright" id="mla-search-iptc-exif-submit" type="submit" value="[+Search Rules+]" />
	<label class="screen-reader-text" for="mla-search-iptc-exif-input">[+Search Rules Text+]:</label>
	<input name="s" class="alignright" id="mla-search-iptc-exif-input" type="search" value="[+s+]" />
	[+results+] </span>
</form>
<br class="clear" />
<div id="col-container">
	<div id="col-right">
		<div class="col-wrap">
			<form action="[+form_url+]" method="post" id="mla-search-iptc-exif-filter">
				<input type="hidden" name="page" value="mla-settings-menu-iptc_exif" />
				<input type="hidden" name="mla_tab" value="iptc_exif" />
				[+view_args+]
				[+_wpnonce+] 
				
<!-- template="after-table" -->
			</form>
			<!-- /id=mla-search-iptc-exif-filter --> 
		</div>
		<!-- /col-wrap --> 
	</div>
	<!-- /col-right -->
	<div id="col-left">
		<div class="col-wrap">
			<div class="mla-settings-enable-form">
				<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-iptc-exif-tab">
					<table class="optiontable">
						[+options_list+]
					</table>
					<span class="submit mla-settings-submit">
					<input name="mla-iptc-exif-options-save" type="submit" class="button-primary" value="[+Save Changes+]" />
					</span> <span class="submit mla-settings-submit">
					<input name="iptc-exif-options-map" type="submit" class="alignright button-primary mla-mapping" value="[+Map All+]" />
					</span> [+_wpnonce+]
				</form>
			</div>
			<div class="form-wrap">
				<h2>[+Add New Rule+]</h2>
				<form action="[+form_url+]" method="post" class="validate" id="mla-add-iptc-exif">
					<input type="hidden" name="page" value="mla-settings-menu-iptc_exif" />
					<input type="hidden" name="mla_tab" value="iptc_exif" />
					[+_wpnonce+]
					<table class="form-table" id="mla-add-iptc-exif-table">
						<tbody>
							<tr class="form-field iptc-exif-name-wrap">
								<th scope="row"> <label for="mla-new-iptc-exif-name">[+Name+]</label>
								</th>
								<td><select name="mla_iptc_exif_rule[new_name]" id="mla-new-iptc-exif-name">
										
[+new_names+]
						
									</select>
									<input name="mla_iptc_exif_rule[new_field]" id="mla-new-iptc-exif" style="display: none;" type="text" value="">
									<br />
									<a class="hide-if-no-js" id="mla-add-iptc-exif-link" 
onclick="
jQuery( '#mla-new-iptc-exif-name, #mla-add-iptc-exif-link' ).hide();
jQuery( '#mla-new-iptc-exif-name' ).val('none');
jQuery( '#mla-new-iptc-exif, #mla-cancel-iptc-exif-link' ).show();
return false;" 
href="#mla-new-iptc-exif"> [+Enter new field+] </a> <a class="hide-if-no-js hidden" id="mla-cancel-iptc-exif-link" 
onclick="
jQuery( '#mla-new-iptc-exif, #mla-cancel-iptc-exif-link' ).hide();
jQuery( '#mla-new-iptc-exif' ).val('');
jQuery( '#mla-new-iptc-exif-name, #mla-add-iptc-exif-link' ).show();
return false;" 
href="#mla-new-iptc-exif"> [+Cancel new field+] </a></td>
							</tr>
			<tr class="form-field iptc-exif-iptc-value-wrap">
				<th scope="row"> <label for="mla-iptc-exif-iptc-value">[+IPTC Value+]</label>
				</th>
				<td><select name="mla_iptc_exif_rule[iptc_value]" id="mla-iptc-exif-iptc-value">
[+iptc_field_options+]
					</select></td>
			</tr>
			<tr class="form-field iptc-exif-exif-value-wrap">
				<th scope="row"> <label for="mla-iptc-exif-exif-value">[+EXIF/Template Value+]</label>
				</th>
				<td><input name="mla_iptc_exif_rule[exif_value]" id="mla-iptc-exif-exif-value" type="text" value="[+exif_text+]" size="[+exif_size+]" />
					<p class="description">&nbsp;[+Enter EXIF/Template+]</p></td>
			</tr>
			<tr class="form-field iptc-exif-iptc-first-wrap">
				<th scope="row"> <label for="mla-iptc-exif-mla-column">[+Priority+]</label>
				</th>
				<td><select name="mla_iptc_exif_rule[iptc_first]" id="mla-iptc-exif-iptc-first">
                <option [+iptc_selected+] value="1">[+IPTC+]</option>
                <option [+exif_selected+] value="">[+EXIF+]</option>
            </select></td>
			</tr>
			<tr class="form-field iptc-exif-keep-existing-wrap">
				<th scope="row"> <label for="mla-iptc-exif-keep-existing">[+Existing Text+]</label>
				</th>
				<td><select name="mla_iptc_exif_rule[keep_existing]" id="mla-iptc-exif-keep-existing">
                <option selected="selected" value="1">[+Keep+]</option>
                <option value="">[+Replace+]</option>
            </select></td>
			</tr>
							<tr class="form-field iptc-exif-format-wrap">
								<th scope="row"> <label for="mla-iptc-exif-format">[+Format+]</label>
								</th>
								<td><select name="mla_iptc_exif_rule[format]" id="mla-iptc-exif-format">
										<option selected="selected" value="native">[+Native+]</option>
										<option value="commas">[+Commas+]</option>
										<option value="raw">[+Raw+]</option>
									</select></td>
							</tr>
							<tr class="form-field iptc-exif-option-wrap">
								<th scope="row"> <label for="mla-iptc-exif-option">[+Option+]</label>
								</th>
								<td><select name="mla_iptc_exif_rule[option]" id="mla-iptc-exif-option">
										<option selected="selected" value="text">[+Text+]</option>
										<option value="single">[+Single+]</option>
										<option value="export">[+Export+]</option>
										<option value="array">[+Array+]</option>
										<option value="multi">[+Multi+]</option>
									</select></td>
							</tr>
							<tr class="form-field iptc-exif-no-null-wrap">
								<th scope="row"> <label for="mla-iptc-exif-no-null">[+Delete NULL+]</label>
								</th>
								<td><input type="checkbox" name="mla_iptc_exif_rule[no_null]" id="mla-iptc-exif-no-null" [+no_null+] value="1" />
									&nbsp;[+Check Delete NULL+] </td>
							</tr>
							<tr class="form-field iptc-exif-status-wrap">
								<th scope="row"> <label for="mla-iptc-exif-status">[+Status+]</label>
								</th>
								<td><select name="mla_iptc_exif_rule[status]" id="mla-iptc-exif-status">
										<option selected="selected" value="1">[+Active+]</option>
										<option value="">[+Inactive+]</option>
									</select></td>
							</tr>
						</tbody>
					</table>
					<p class="submit mla-settings-submit">
						<input type="submit" name="mla-add-iptc-exif-submit" id="mla-add-iptc-exif-submit" class="button button-primary" value="[+Add Rule+]" />
					</p>
				</form>
				<!-- /id=mla-add-iptc-exif --> 
			</div>
			<!-- /form-wrap --> 
		</div>
		<!-- /col-wrap --> 
	</div>
	<!-- /col-left --> 
</div>
<!-- /col-container -->
<form>
	<table width="99%" style="display: none">
		<tbody id="inlineedit">
			<tr id="inline-edit" class="inline-edit-row inline-edit-row-custom inline-edit-custom quick-edit-row quick-edit-row-custom quick-edit-custom" style="display: none">
				<td colspan="[+colspan+]" class="colspanchange"><fieldset class="inline-edit-col">
						<div class="inline-edit-col">
							<h4>[+Quick Edit+]:
								<input name="name" class="ptitle" type="text" readonly="readonly" value="" />
							</h4>
							<div class="inline-edit-group">
								<label class="alignleft"> <span class="title">[+IPTC Value+]</span> <span class="input-text-wrap">
									<select name="iptc_value">
										
[+iptc_field_options+]
								
									</select>
									</span> </label>
							</div>
							<div class="inline-edit-group">
								<label class="alignleft"> <span class="title">[+EXIF/Template Value+]</span> <span class="input-text-wrap">
									<input name="exif_value" class="ptitle" type="text" value="" />
									</span> </label>
							</div>
							<div class="inline-edit-group">
								<label class="alignleft"> <span class="dropdown-title">[+Priority+]</span> <span class="input-dropdown-wrap">
									<select name="iptc_first">
										<option value="1">[+IPTC+]</option>
										<option value="">[+EXIF+]</option>
									</select>
									</span> </label>
								<label class="alignleft"> <span class="dropdown-title">[+Existing Text+]</span> <span class="input-dropdown-wrap">
									<select name="keep_existing">
										<option value="1">[+Keep+]</option>
										<option value="">[+Replace+]</option>
									</select>
									</span> </label>
								<label class="alignleft"> <span class="dropdown-title">[+Status+]</span> <span class="input-dropdown-wrap">
									<select name="active">
										<option value="1">[+Active+]</option>
										<option value="">[+Inactive+]</option>
									</select>
									</span> </label>
							</div>
							<div class="inline-edit-group inline-taxonomy-group">
								<label class="alignleft"> <span class="title">[+Delimiters+]</span> <span class="input-text-wrap">
									<input name="delimiters" class="ptitle" type="text" value="" size="[+delimiters_size+]" style="width: [+delimiters_size+]em" />
									</span> </label>
								<label class="alignleft"> <span class="dropdown-title">[+Parent+]</span> <span class="input-dropdown-wrap">
									<select name="parent">
									</select>
									</span> </label>
							</div>
							<div class="inline-edit-group inline-custom-group">
								<label class="alignleft checkbox-label">
									<input name="no_null" class="ptitle" type="checkbox" value="1" />
									</span> <span class="checkbox-title">[+Delete NULL+]</span> </label>
								<label class="alignleft"> <span class="dropdown-title">[+Format+]</span> <span class="input-dropdown-wrap">
									<select name="format">
										<option value="native">[+Native+]</option>
										<option value="commas">[+Commas+]</option>
										<option value="raw">[+Raw+]</option>
									</select>
									</span> </label>
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
						</div>
					</fieldset>
					<p class="submit inline-edit-save"> <a accesskey="c" href="#inline-edit" class="cancel button-secondary alignleft">[+Cancel+]</a> <a accesskey="s" href="#inline-edit" class="save button-primary alignright">[+Update+]</a>
						<input type="hidden" name="rule_name" value="" />
						<input type="hidden" name="page" value="mla-settings-menu-iptc_exif" />
						<input type="hidden" name="mla_tab" value="iptc_exif" />
						<input type="hidden" name="screen" value="settings_page_mla-settings-menu-iptc_exif" />
						<span class="spinner"></span> <span class="error" style="display: none;"></span> <br class="clear" />
					</p></td>
			</tr>
			<tr id="bulk-edit" class="inline-edit-row inline-edit-row-custom inline-edit-custom bulk-edit-row bulk-edit-row-custom bulk-edit-custom" style="display: none">
				<td colspan="[+colspan+]" class="colspanchange"><h4>[+Bulk Edit+]</h4>
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
								<label class="alignleft inline-edit-iptc-first"> <span class="dropdown-title">[+Priority+]</span>
									<select name="iptc_first">
										<option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
										<option value="1">[+IPTC+]</option>
										<option value="">[+EXIF+]</option>
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
					<p class="submit inline-edit-save"> <a accesskey="c" href="#inline-edit" class="cancel button-secondary alignleft">[+Cancel+]</a>
						<input accesskey="s" type="submit" name="bulk_update" id="bulk_update" class="save button-primary alignright" value="[+Update+]"  />
						<input type="hidden" name="page" value="mla-settings-menu-iptc_exif" />
						<input type="hidden" name="mla_tab" value="iptc_exif" />
						<input type="hidden" name="screen" value="settings_page_mla-settings-menu-iptc_exif" />
						<span class="error" style="display:none"></span> <br class="clear" />
					</p></td>
			</tr>
		</tbody>
	</table>
</form>
