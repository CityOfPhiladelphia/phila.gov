<!-- template="single-item-controls" -->
	<tr class="form-field form-required">
	<th scope="row" valign="top">&nbsp;</th>
	<td>
		<select name="mla_template_type" id="mla-template-type">
			<option selected="selected" value="any">[+Select a type+]</option>
			<option value="style">Style</option>
			<option value="markup">Markup</option>
		</select>
		<select name="mla_template_shortcode" id="mla-template-shortcode">
			<option selected="selected" value="any">[+Select a shortcode+]</option>
			[+shortcode_options+]
		</select>
		<p class="description">[+controls_help+]</p>
	</td>
	</tr>

<!-- template="single-item-section" -->
	<tr class="form-field [+class+]"[+style+]>
	<th scope="row" valign="top"><label for="mla-template-[+section_slug+]">[+section_name+]</label></th>
	<td>
		<textarea name="mla_template_item[sections][[+section_slug+]]" id="mla-template-[+section_slug+]" rows="[+section_rows+]" [+readonly+]>[+section_value+]</textarea>
		<p class="description">[+section_help+]</p>
	</td>
	</tr>

<!-- template="single-item-edit" -->
<div id="ajax-response"></div>
<h2>[+Edit Template+]</h2>
<form action="[+form_url+]" method="post" class="validate" id="mla-edit-template">
	<input name="mla_template_item[post_ID]" id="mla-template-item-post-ID" type="hidden" value="[+ID+]" />
	<input name="mla_template_item[type]" id="mla-template-item-type" type="hidden" value="[+type+]" />
	<input name="mla_template_item[shortcode]" id="mla-template-item-shortcode" type="hidden" value="[+shortcode+]" />
	[+_wpnonce+]
	<table class="form-table">
	[+controls+]
	<tr class="form-field form-required">
	<th scope="row" valign="top"><label for="mla-template-slug">[+Name+]</label></th>
	<td>
	<input name="mla_template_item[name]" id="mla-template-slug" type="text" value="[+name+]" size="40" [+readonly+] aria-required="true" />
	<p class="description">[+The name is+]</p>
	</td>
	</tr>
	[+section_list+]
</table>
<p class="submit mla-settings-submit">
<input name="[+cancel+]" class="button-primary" type="submit" value="[+Cancel+]" />&nbsp;
<input name="[+submit+]" class="button-primary" [+submit_style+] type="submit" value="[+Update+]" />&nbsp;
<a class="button-primary" [+copy_style+] href="[+copy_href+]">[+Copy+]</a>
</p>
</form>

<!-- template="before-table" -->
<h2>[+MLA Shortcode Options+]</h2>
<p>[+In this tab+]</p>
<p>[+You can find+]</p>
<div id="ajax-response"></div>
<div id="col-container">
	<div id="col-right">
		<div class="col-wrap">
			<form action="[+form_url+]" method="post" id="mla-search-templates-filter">
				[+view_args+]
				[+_wpnonce+]
				<span style="margin-top: 1em">
					<label class="screen-reader-text" for="mla-search-templates-input">[+Search Templates+]:</label>
					<input name="mla-search-templates-submit" class="button alignright" id="mla-search-templates-submit" type="submit" value="[+Search Templates+]" />
					<input name="s" class="alignright" id="mla-search-templates-input" type="search" value="[+s+]" />
					<input name="mla-add-new-template-submit" class="button-primary alignleft" type="submit" value="[+Add New Template+]" />
					[+results+]
				</span>
<!-- template="after-table" -->
			</form><!-- /id=mla-search-templates-filter --> 
		</div><!-- /col-wrap --> 
	</div><!-- /col-right -->

	<div id="col-left">
		<div class="col-wrap">
			<div class="form-wrap">
				<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-shortcodes-tab">
				[+options_list+]
					<a name="gotobottom"></a>
					<p class="submit mla-settings-submit">
						<input name="mla-shortcodes-options-save" type="submit" class="button-primary" value="[+Save Changes+]" />
					</p>
				[+_wpnonce+]
				[+_wp_http_referer+]
				</form>
			</div><!-- /form-wrap --> 
		</div><!-- /col-wrap -->
	</div><!-- /col-left --> 
</div><!-- /col-container -->
<script type="text/javascript">
try{document.forms.addtag['mla-search-templates-input'].focus();}catch(e){}
</script> 
