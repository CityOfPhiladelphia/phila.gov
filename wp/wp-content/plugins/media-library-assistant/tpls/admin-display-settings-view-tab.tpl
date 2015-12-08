<!-- template="single-item-edit" -->
<div id="ajax-response"></div>
<h2>[+Edit View+]</h2>
<form action="[+form_url+]" method="post" class="validate" id="mla-edit-view">
	<input type="hidden" name="page" value="mla-settings-menu-view" />
	<input type="hidden" name="mla_tab" value="view" />
	<input type="hidden" name="mla_admin_action" value="[+action+]" />
	<input type="hidden" name="mla_view_item[original_slug]" value="[+original_slug+]" />
	[+_wpnonce+]
	<table class="form-table">
	<tr class="form-field form-required">
	<th scope="row" valign="top"><label for="mla-view-slug">[+Slug+]</label></th>
	<td>
	<input name="mla_view_item[slug]" id="mla-view-slug" type="text" value="[+slug+]" size="40" aria-required="true" />
	<p class="description">[+The slug is+]</p>
	</td>
	</tr>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="mla-view-singular">[+Singular Label+]</label></th>
	<td>
						<input name="mla_view_item[singular]" id="mla-view-singular" type="text" value="[+singular+]" size="40" />
	</td>
	</tr>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="mla-view-plural">[+Plural Label+]</label></th>
	<td>
						<input name="mla_view_item[plural]" id="mla-view-plural" type="text" value="[+plural+]" size="40" />
						<p class="description">[+The labels+]</p>
	</td>
	</tr>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="mla-view-specification">[+Specification+]</label></th>
	<td>
						<input name="mla_view_item[specification]" id="mla-view-specification" type="text" value="[+specification+]" size="40" />
						<p class="description">[+If the specification+]</p>
	</td>
	</tr>
	<tr>
	<th scope="row" valign="top"><label for="mla-view-post-mime-type">[+Post MIME Type+]</label></th>
	<td>
						<input type="checkbox" name="mla_view_item[post_mime_type]" id="mla-view-post-mime-type" [+post_mime_type+] value="1" />
						<span class="description">&nbsp;[+Check Post MIME+]</span>
	</td>
	</tr>
	<tr>
	<th scope="row" valign="top"><label for="mla-view-table-view">[+Table View+]</label></th>
	<td>
						<input type="checkbox" name="mla_view_item[table_view]" id="mla-view-table-view" [+table_view+] value="1" />
						<span class="description">&nbsp;[+Check Table View+]</span>
	</td>
	</tr>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="mla-view-menu-order">[+Menu Order+]</label></th>
	<td>
						<input name="mla_view_item[menu_order]" id="mla-view-menu-order" type="text" value="[+menu_order+]" size="10" />
						<p class="description">[+You can choose+]</p>
	</td>
	</tr>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="mla-view-description">[+Description+]</label></th>
	<td>
						<textarea name="mla_view_item[description]" id="mla-view-description" rows="5" cols="40">[+description+]</textarea>
						<p class="description">[+The description can+]</p>
	</td>
	</tr>
</table>
<p class="submit mla-settings-submit">
<input name="cancel" type="submit" class="button-primary" value="[+Cancel+]" />&nbsp;
<input name="update" type="submit" class="button-primary" value="[+Update+]" />&nbsp;
</p>
</form>
<!-- template="view-disabled" -->
<h2>[+Support is disabled+]</h2>
<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-view-tab">
    <table class="optiontable">
[+options_list+]
	</table>
    <p class="submit mla-settings-submit">
        <input name="mla-view-options-save" type="submit" class="button-primary" value="[+Save Changes+]" />
    </p>
	<input type="hidden" name="page" value="mla-settings-menu-view" />
	<input type="hidden" name="mla_tab" value="view" />
	[+_wpnonce+]
</form>

<!-- template="before-table" -->
<h2>[+Library Views Processing+]</h2>
<p>[+In this tab+]</p>
<p>[+You can find+]</p>
<div id="ajax-response"></div>
<form action="[+form_url+]" method="get" id="mla-search-views-form">
	<input type="hidden" name="page" value="mla-settings-menu-view" />
	<input type="hidden" name="mla_tab" value="view" />
	[+_wpnonce+]
	[+results+]
	<p class="search-box" style="margin-top: 1em">
		<label class="screen-reader-text" for="mla-search-views-input">[+Search Views+]:</label>
		<input type="search" id="mla-search-views-input" name="s" value="[+s+]" />
		<input type="submit" name="" id="mla-search-views-submit" class="button" value="[+Search Views+]" />
	</p>
</form>
<br class="clear" />
<div id="col-container">
	<div id="col-right">
		<div class="col-wrap">
			<form action="[+form_url+]" method="post" id="mla-search-views-filter">
				<input type="hidden" name="page" value="mla-settings-menu-view" />
				<input type="hidden" name="mla_tab" value="view" />
				[+_wpnonce+]

<!-- template="after-table" -->
			</form><!-- /id=mla-search-views-filter --> 
		</div><!-- /col-wrap --> 
		<div class="mla-settings-after-table">
		<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-view-tab">
			<table class="optiontable">
		[+options_list+]
			</table>
			<span class="submit mla-settings-submit">
				<input name="mla-view-options-save" type="submit" class="button-primary" value="[+Save Changes+]" />
			</span>
		[+_wpnonce+]
		</form>
		</div>
	</div><!-- /col-right -->

	<div id="col-left">
		<div class="col-wrap">
			<div class="form-wrap">
				<h2>[+Add New View+]</h2>
				<form action="[+form_url+]" method="post" class="validate" id="mla-add-view">
					<input type="hidden" name="page" value="mla-settings-menu-view" />
					<input type="hidden" name="mla_tab" value="view" />
					[+_wpnonce+]
					<div class="form-field form-required">
						<label for="mla-view-slug">[+Slug+]</label>
						<input name="mla_view_item[slug]" id="mla-view-slug" type="text" value="[+slug+]" size="40" />
						<p class="description">[+The slug is+]</p>
					</div>
					<div class="form-field">
						<label for="mla-view-singular">[+Singular Label+]</label>
						<input name="mla_view_item[singular]" id="mla-view-singular" type="text" value="[+singular+]" size="40" />
						<label for="mla-view-plural">[+Plural Label+]</label>
						<input name="mla_view_item[plural]" id="mla-view-singular" type="text" value="[+plural+]" size="40" />
						<p class="description">[+The labels+]</p>
					</div>
					<div class="form-field">
						<label for="mla-view-specification">[+Specification+]</label>
						<input name="mla_view_item[specification]" id="mla-view-specification" type="text" value="[+specification+]" size="40" />
						<p class="description">[+If the specification+]</p>
					</div>
					<div>
						<input type="checkbox" name="mla_view_item[post_mime_type]" id="mla-view-post-mime-type" [+post_mime_type+] value="1" />
						[+Post MIME Type+]
						<p class="description">[+Check Post MIME+]</p>
					</div>
					<div>
						<input type="checkbox" name="mla_view_item[table_view]" id="mla-view-table-view" [+table_view+] value="1" />
						[+Table View+]
						<p class="description">[+Check Table View+]</p>
					</div>
					<div class="form-field">
						<label for="mla-view-menu-order">[+Menu Order+]</label>
						<input name="mla_view_item[menu_order]" id="mla-view-menu-order" type="text" value="[+menu_order+]" size="10" />
						<p class="description">[+You can choose+]</p>
					</div>
					<div class="form-field">
						<label for="mla-view-description">[+Description+]</label>
						<textarea name="mla_view_item[description]" id="mla-view-description" rows="5" cols="40">[+description+]</textarea>
						<p class="description">[+The description can+]</p>
					</div>
					<p class="submit mla-settings-submit">
						<input type="submit" name="mla-add-view-submit" id="mla-add-view-submit" class="button button-primary" value="[+Add View+]" />
					</p>
				</form><!-- /id=mla-add-view --> 
			</div><!-- /form-wrap --> 
		</div><!-- /col-wrap -->
	</div><!-- /col-left --> 
</div><!-- /col-container -->
<script type="text/javascript">
try{document.forms.addtag['mla-view-slug'].focus();}catch(e){}
</script> 
<form>
	<table width="99%" style="display: none">
		<tbody id="inlineedit">
			<tr id="inline-edit" class="inline-edit-row inline-edit-row-view inline-edit-view quick-edit-row quick-edit-row-view quick-edit-view" style="display: none">
				<td colspan="[+colspan+]" class="colspanchange">
					<fieldset class="inline-edit-col">
						<div class="inline-edit-col">
							<h4>[+Quick Edit+]</h4>
							<label class="alignleft"> <span class="title">[+Slug+]</span> <span class="input-text-wrap">
								<input type="text" name="slug" class="ptitle" value="" />
								</span> </label>
							<label class="alignleft"> <span class="title">[+Specification+]</span> <span class="input-text-wrap">
								<input type="text" name="specification" class="ptitle" value="" />
								</span> </label>
							<label class="alignleft"> <span class="title">[+Singular Label+]</span> <span class="input-text-wrap">
								<input type="text" name="singular" class="ptitle" value="" />
								</span> </label>
							<label class="alignleft"> <span class="title">[+Plural Label+]</span> <span class="input-text-wrap">
								<input type="text" name="plural" class="ptitle" value="" />
								</span> </label>
							<div class="inline-edit-group">
								<label class="alignleft checkbox-label">
									<input type="checkbox" name="post_mime_type" class="ptitle" checked="checked" value="1" />
									<span class="checkbox-title">[+Post MIME Type+]</span>
								</label>
								<label class="alignleft checkbox-label">
									<input type="checkbox" name="table_view" class="ptitle" checked="checked" value="1" />
									<span class="checkbox-title">[+Table View+]</span> 
								</label>
								<label class="alignleft">
									<span class="title">[+Menu Order+]</span>
									<span class="input-text-wrap"><input type="text" name="menu_order" class="ptitle inline-edit-menu-order" value="" /></span>
								</label>
							</div>
						</div>
					</fieldset>
					<p class="inline-edit-save submit"> <a accesskey="c" href="#inline-edit" title="Cancel" class="cancel button-secondary alignleft">[+Cancel+]</a> <a accesskey="s" href="#inline-edit" title="[+Update+]" class="save button-primary alignright">[+Update+]</a>
						<input type="hidden" name="original_slug" value="" />
						<input type="hidden" name="page" value="mla-settings-menu-view" />
						<input type="hidden" name="mla_tab" value="view" />
						<input type="hidden" name="screen" value="settings_page_mla-settings-menu-view" />
						<span class="spinner"></span>
						<span class="error" style="display: none;"></span>
						<br class="clear" />
					</p>
				</td>
			</tr>
			<tr id="bulk-edit" class="inline-edit-row inline-edit-row-view inline-edit-view bulk-edit-row bulk-edit-row-view bulk-edit-view" style="display: none">
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
							<label class="inline-edit-post-mime-type"> <span class="title">[+Post MIME Type+]</span> <span class="input-text-wrap">
								<select name="post_mime_type">
									<option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
									<option value="0">[+No+]</option>
									<option value="1">[+Yes+]</option>
								</select>
								</span> </label>
							<br />
							<label class="inline-edit-table-view"> <span class="title">[+Table View+]</span> <span class="input-text-wrap">
								<select name="table_view">
									<option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
									<option value="0">[+No+]</option>
									<option value="1">[+Yes+]</option>
								</select>
								</span> </label>
							<br />
							<label class="inline-edit-menu-order"> <span class="title">[+Menu Order+]</span> <span class="input-text-wrap">
								<input type="text" name="menu_order" value="" />
								</span> </label>
						</div>
					</fieldset>
					<p class="submit inline-edit-save"> <a accesskey="c" href="#inline-edit" title="[+Cancel+]" class="button-secondary cancel alignleft">[+Cancel+]</a>
						<input accesskey="s" type="submit" name="bulk_edit" id="bulk_edit" class="button-primary alignright" value="[+Update+]"  />
						<input type="hidden" name="page" value="mla-settings-menu-view" />
						<input type="hidden" name="mla_tab" value="view" />
						<input type="hidden" name="screen" value="settings_page_mla-settings-menu-view" />
						<span class="error" style="display:none"></span> <br class="clear" />
					</p>
				</td>
			</tr>
		</tbody>
	</table>
</form>
