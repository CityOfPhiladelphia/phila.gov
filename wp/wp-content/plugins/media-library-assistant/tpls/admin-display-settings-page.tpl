<!-- template="page" -->
<a name="backtotop"></a>
&nbsp;
<div class="wrap">
<div id="icon-options-general" class="icon32"><br/></div>
<div id="donate-button-div" class="alignright clear">
	<a title="[+Support Our Work+]" class="button button-large button-primary" href="http://fairtradejudaica.org/make-a-difference/donate/">[+Donate+]</a>
</div>
<h1>[+Media Library Assistant+] [+version+][+development+] [+Settings+]</h1>
[+messages+]
[+tablist+]
[+tab_content+]
</div><!-- wrap -->

<!-- template="checkbox" -->
        <tr valign="top"><td class="textright">
            <input type="checkbox" name="[+key+]" id="[+key+]" [+checked+] value="[+value+]" />
        </td><td>
		    &nbsp;<strong>[+value+]</strong>
            <div class="mla-settings-help">&nbsp;&nbsp;[+help+]</div>
		</td></tr>
<!-- template="header" -->
        <tr><td colspan="2">
            <a href="#backtotop">[+Go to Top+]</a>
        </td></tr>
        <tr><td colspan="2">
            <h2 id="[+key+]">[+value+]</h2>
        </td></tr>
<!-- template="subheader" -->
        <tr><td colspan="2">
            <h3 id="[+key+]">[+value+]</h3>
        </td></tr>
<!-- template="radio" -->
        <tr valign="top"><th scope="row" class="textright">
            [+value+]
        </th><td class="textleft">
            <span class="mla-settings-help">&nbsp;&nbsp;[+help+]</span>
        </td></tr>
[+options+]
        <tr valign="top"><td colspan="2" style="padding-bottom:10px;">
        </td></tr>
<!-- template="radio-option" -->
        <tr valign="top"><td class="textright">
            <input type="radio" name="[+key+]" [+checked+] value="[+option+]" />
        </td><td>
            &nbsp;[+value+]
        </td></tr>
<!-- template="select" -->
        <tr valign="top"><th scope="row" class="textright">
            [+value+]
        </th><td class="textleft">
            <select name="[+key+]" id="[+key+]">
[+options+]
            </select><div class="mla-settings-help">&nbsp;&nbsp;[+help+]</div>
        </td></tr>
<!-- template="select-only" -->
            <select name="[+key+]" id="[+key+]">
[+options+]
            </select>
<!-- template="select-option" -->
                <option [+selected+] value="[+value+]">[+text+]</option>
<!-- template="text" -->
        <tr valign="top"><th scope="row" class="textright">
            [+value+]
        </th><td class="textleft">
            <input name="[+key+]" id="[+key+]" type="text" size="[+size+]" value="[+text+]" />
            <div class="mla-settings-help">&nbsp;&nbsp;[+help+]</div>
        </td></tr>
<!-- template="textarea" -->
        <tr valign="top"><th scope="row" class="textright">
            [+value+]
        </th><td class="textleft">
            <textarea name="[+key+]" id="[+key+]" rows="[+rows+]" cols="[+cols+]">
            [+text+]
            </textarea>
            <div class="mla-settings-help">&nbsp;&nbsp;[+help+]</div>
        </td></tr>
<!-- template="messages" -->
<div class="[+mla_messages_class+]">
<p>
[+messages+]
</p></div>
<!-- template="shortcode-list" -->
<div id="mla-shortcode-list">
<p>[+Shortcodes made available+]:</p>
<ol>
[+shortcode_list+]
</ol>
</div>
<!-- template="shortcode-item" -->
<li><code>[[+name+]]</code> - [+description+]</li>
<!-- template="tablist" -->
<h2 class="nav-tab-wrapper">
[+tablist+]
</h2>
<!-- template="tablist-item" -->
<a data-tab-id="[+data-tab-id+]" class="nav-tab [+nav-tab-active+]" href="?page=[+settings-page+]&amp;mla_tab=[+data-tab-id+]">[+title+]</a>
<!-- template="general-tab" -->
<h2>[+General Processing Options+]</h2>
<p>[+In this tab+]</p>
[+shortcode_list+]
<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-general-tab">
    <table class="optiontable">
[+options_list+]
	</table>
    <p class="submit mla-settings-submit">
        <input name="mla-general-options-save" type="submit" class="button-primary" value="[+Save Changes+]" />&nbsp;&nbsp;
        <input name="mla-general-options-export" type="submit" class="button-primary" value="[+Export ALL Settings+]" />
        [+import_settings+]
        <input name="mla-general-options-reset" type="submit" class="button-primary alignright" value="[+Delete General options+]"/>
    </p>
[+_wpnonce+]
[+_wp_http_referer+]
</form>
<p>
<a href="#backtotop">[+Go to Top+]</a>
</p>
<h2>[+Support Our Work+]</h2>
<table width="700" border="0" cellpadding="10">
	<tr>
		<td>
			<a title="[+Donate to FTJ+]" class="button button-large button-primary" href="http://fairtradejudaica.org/make-a-difference/donate/">[+Donate+]</a>
		</td>
		<td>[+This plugin was+]</td>
	</tr>
</table>
<!-- template="mla-gallery-default" -->
		<td colspan="2" width="500">
            <div class="mla-settings-help">[+help+]</div>
		</td>
<!-- template="mla-gallery-delete" -->
		<td width="1%" class="textright">
            <input type="checkbox" name="[+name+]" id="[+id+]" value="[+value+]" />
        </td><td width="500">
		    &nbsp;<strong>[+value+]</strong>
            <div class="mla-settings-help">&nbsp;[+help+]</div>
		</td>
<!-- template="mla-gallery-style" -->
<table width="700">
        <tr valign="top"><th width="1%" scope="row" class="textright">
            [+Name+]:
        </th><td width="1%" class="textleft">
            <input name="[+name_name+]" id="[+name_id+]" type="text" size="15" [+readonly+] value="[+name_text+]" />
        </td>
		[+control_cells+]
		</tr>
        <tr valign="top"><th scope="row" class="textright">
            [+Styles+]:
        </th><td colspan="3" class="textleft">
            <textarea name="[+value_name+]" id="[+value_id+]" rows="11" cols="100" [+readonly+]>[+value_text+]</textarea>
            <div class="mla-settings-help">&nbsp;&nbsp;[+value_help+]</div>
        </td></tr>
</table>
<!-- template="mla-gallery-markup" -->
<table width="700">
        <tr valign="top"><th width="1%" scope="row" class="textright">
            [+Name+]:
        </th><td width="1%" class="textleft">
            <input name="[+name_name+]" id="[+name_id+]" type="text" size="15" [+readonly+] value="[+name_text+]" />
        </td>
		[+control_cells+]
		</tr>
        <tr valign="top"><th scope="row" class="textright">
            [+Arguments+]:
        </th><td colspan="3" class="textleft">
            <textarea name="[+arguments_name+]" id="[+arguments_id+]" rows="3" cols="100" [+readonly+]>[+arguments_text+]</textarea>
            <div class="mla-settings-help">&nbsp;&nbsp;[+arguments_help+]</div>
        </td></tr>
        <tr valign="top"><th scope="row" class="textright">
            [+Open+]:
        </th><td colspan="3" class="textleft">
            <textarea name="[+open_name+]" id="[+open_id+]" rows="3" cols="100" [+readonly+]>[+open_text+]</textarea>
            <div class="mla-settings-help">&nbsp;&nbsp;[+open_help+]</div>
        </td></tr>
        <tr valign="top"><th scope="row" class="textright">
            [+Row+]&nbsp;[+Open+]:
        </th><td colspan="3" class="textleft">
            <textarea name="[+row_open_name+]" id="[+row_open_id+]" rows="3" cols="100" [+readonly+]>[+row_open_text+]</textarea>
            <div class="mla-settings-help">&nbsp;&nbsp;[+row_open_help+]</div>
        </td></tr>
        <tr valign="top"><th scope="row" class="textright">
            [+Item+]:
        </th><td colspan="3" class="textleft">
            <textarea name="[+item_name+]" id="[+item_id+]" rows="6" cols="100" [+readonly+]>[+item_text+]</textarea>
            <div class="mla-settings-help">&nbsp;&nbsp;[+item_help+]</div>
        </td></tr>
        <tr valign="top"><th scope="row" class="textright">
            [+Row+]&nbsp;[+Close+]:
        </th><td colspan="3" class="textleft">
            <textarea name="[+row_close_name+]" id="[+row_close_id+]" rows="3" cols="100" [+readonly+]>[+row_close_text+]</textarea>
            <div class="mla-settings-help">&nbsp;&nbsp;[+row_close_help+]</div>
        </td></tr>
        <tr valign="top"><th scope="row" class="textright">
            [+Close+]:
        </th><td colspan="3" class="textleft">
            <textarea name="[+close_name+]" id="[+close_id+]" rows="3" cols="100" [+readonly+]>[+close_text+]</textarea>
            <div class="mla-settings-help">&nbsp;&nbsp;[+close_help+]</div>
        </td></tr>
</table>
<hr width="650" align="left" />

<!-- template="mla-gallery-tab" -->
<h2>[+MLA Gallery Options+]</h2>
<p><a href="#style">[+Go to Style Templates+]</a></p>
<p><a href="#markup">[+Go to Markup Templates+]</a></p>
<p>[+In this tab+]</p>
<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-mla-gallery-tab">
[+options_list+]
<a name="style">&nbsp;<br />&nbsp;<br /><a href="#backtotop">[+Go to Top+]</a>
<h3>[+Style Templates+]</h3>
    <table class="optiontable">
[+style_options_list+]
	</table>
<a name="markup">&nbsp;<br />&nbsp;<br /><a href="#backtotop">[+Go to Top+]</a>
<h3>[+Markup Templates+]</h3>
    <table class="optiontable">
[+markup_options_list+]
	</table>
    <p class="submit mla-settings-submit">
        <input name="mla-gallery-options-save" type="submit" class="button-primary" value="[+Save Changes+]" />
    </p>
[+_wpnonce+]
[+_wp_http_referer+]
</form>

<!-- template="mla-progress-div" -->
<div class="wrap" id="mla-progress-div" style="display:none; border-bottom:1px solid #cccccc">
	<h2>[+Mapping Progress+]</h2>
	<p style="font-weight:bold">[+DO NOT+]:</p>
	<ol>
		<li>[+DO NOT Close+]</li>
		<li>[+DO NOT Reload+]</li>
		<li>[+DO NOT Click+]</li>
	</ol>
	<p style="font-weight:bold">[+Progress+]:</p>
	<div id="mla-progress-meter-div" style="padding: 3px; border: 1px solid rgb(101, 159, 255); border-image: none; width: 80%; height: 11px;">
		<div id="mla-progress-meter" style="width: 100%; height: 11px; text-align: center; color: rgb(255, 255, 255); line-height: 11px; font-size: 6pt; background-color: rgb(101, 159, 255);">100%
		</div>
	</div>
	<div id="mla-progress-message">&nbsp;</div>
	<p class="submit inline-edit-save">
		<a title="[+Cancel+]" class="button-secondary alignleft" id="mla-progress-cancel" accesskey="c" href="#mla-progress">[+Cancel+]</a>
		<a title="[+Resume+]" class="button-secondary alignleft" id="mla-progress-resume" accesskey="r" href="#mla-progress">[+Resume+]</a>
		<input name="mla_resume_offset" id="mla-progress-offset" type="text" size="3" />
		<a title="[+Close+]" class="button-primary alignright" id="mla-progress-close" accesskey="x" href="#mla-progress">[+Close+]</a>
		<a title="[+Refresh+]" class="button-primary alignright" id="mla-progress-refresh" accesskey="f" href="[+refresh_href+]">[+Refresh+]</a>
		<span class="spinner"></span>
		<span id="mla-progress-error" style="display:inline"></span><br class="clear" />
	</p>
</div>

<!-- template="custom-field-tab" -->
[+mla-progress-div+]
<h2>[+Custom Field Options+]</h2>
<p>
[+In this tab+]
</p>
<p>
[+You can find+]
</p>
<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-custom-field-tab">
&nbsp;<br />
[+options_list+]
<h3>[+Custom field mapping+]</h3>
    <table class="optiontable">
[+custom_options_list+]
	</table>
    <p class="submit mla-settings-submit">
        <input name="custom-field-options-save" class="button-primary" id="custom-field-options-save" type="submit" value="[+Save Changes+]" />
        <input name="custom-field-options-map" class="button-secondary mla-mapping" id="custom-field-options-map" type="submit" value="[+Map All Rules+]" />
	<div class="mla-settings-help">[+Click Save Changes+]</div>
	<div class="mla-settings-help">[+Click Map All+]</div>
    </p>
[+_wpnonce+]
[+_wp_http_referer+]
</form>

<!-- template="iptc-exif-tab" -->
[+mla-progress-div+]
<h2>[+IPTX/EXIF Options+]</h2>
<p>
[+In this tab+]
</p>
<p>
[+You can find+]
</p>
<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-iptc-exif-tab">
&nbsp;<br />
[+options_list+]
<h3>[+Standard field mapping+] <input name="iptc-exif-options-process-standard" class="button-secondary mla-mapping" id="iptc-exif-options-process-standard" type="submit" value="[+Map Standard Fields+]" /></h3>
    <table class="optiontable">
[+standard_options_list+]
	</table>
<h3>[+Taxonomy term mapping+] <input name="iptc-exif-options-process-taxonomy" class="button-secondary mla-mapping" id="iptc-exif-options-process-taxonomy" type="submit" value="[+Map Taxonomy Terms+]" /></h3>
    <table class="optiontable">
[+taxonomy_options_list+]
	</table>
<h3>[+Custom field mapping+] <input name="iptc-exif-options-process-custom" class="button-secondary mla-mapping" id="iptc-exif-options-process-custom" type="submit" value="[+Map Custom Fields+]" /></h3>
    <table class="optiontable">
[+custom_options_list+]
	</table>
    <p class="submit mla-settings-submit">
        <input name="iptc-exif-options-save" class="button-primary" id="iptc-exif-options-save" type="submit" value="[+Save Changes+]" />
	<div class="mla-settings-help">[+Click Save Changes+]</div>
    </p>
[+_wpnonce+]
[+_wp_http_referer+]
</form>

<!-- template="debug-tab" -->
<h2>[+Debug Options+]</h2>
<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-debug-tab">
&nbsp;<br />
    <table class="optiontable">
[+options_list+]
	</table>
<h3>[+Debug Settings+]</h3>
    <table class="optiontable">
[+settings_list+]
	</table>
<h3>[+Error Log+]</h3>
[+Error Log Name+] ( [+Error Log Size+] )
    <table>
        <tr>
            <td>
                <textarea name="" id="mla-error-log-display" rows="24" cols="100" readonly="readonly">[+error_log_text+]</textarea>
            </td>
        </tr>
        <tr>
            <td>
                [+download_link+]&nbsp;[+reset_link+]
            </td>
        </tr>
	</table>
    <p class="submit mla-settings-submit">
        <input name="mla-debug-options-save" class="button-primary" id="mla-debug-options-save" type="submit" value="[+Save Changes+]" />
	<div class="mla-settings-help">[+Click Save Changes+]</div>
    </p>
[+_wpnonce+]
[+_wp_http_referer+]
</form>
