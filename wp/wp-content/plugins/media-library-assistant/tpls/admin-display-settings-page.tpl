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
            <a href="#backtotop">[+Go to Top+]</a> | <a href="#gotobottom">[+Go to Bottom+]</a>
        </td></tr>
        <tr><td colspan="2">
            <h3 id="[+key+]">[+value+]</h3>
        </td></tr>
<!-- template="subheader" -->
        <tr><td colspan="2">
            <h4 id="[+key+]">[+value+]</h4>
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
</p>
[+dismiss_button+]
</div>
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
	<a name="gotobottom"></a>
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

<!-- template="debug-tab" -->
<h2>[+Debug Options+]</h2>
<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-debug-tab">
&nbsp;<br />
    <table class="optiontable">
[+options_list+]
	</table>
<p>[+You can find+]</p>
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
    </p>
	<div class="mla-settings-help">[+Click Save Changes+]</div>
[+_wpnonce+]
[+_wp_http_referer+]
</form>
