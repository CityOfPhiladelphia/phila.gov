<!-- template="before-example-table" -->
<h2>[+Example Plugins+][+results+]</h2>
<p>[+In this tab+]</p>
<p>[+You can find+]</p>
[+views+]
<form action="[+form_url+]" method="get" id="mla-search-example-form">
	<input type="hidden" name="page" value="mla-settings-menu-documentation" />
	<input type="hidden" name="mla_tab" value="documentation" />
	[+_wpnonce+]
	<p class="search-box" style="margin-top: 1em">
		<label class="screen-reader-text" for="mla-search-example-input">[+Search Example Plugins+]:</label>
		<input type="search" id="mla-search-example-input" name="s" value="[+s+]" />
		<input type="submit" name="mla-example-search" id="mla-search-example-submit" class="button" value="[+Search Plugins+]" />
		<span class="description"><br />[+Search help+]</span>
	</p>
</form>
<br class="clear" />
<div id="col-container">
	<form action="[+form_url+]" method="post" id="mla-search-example-filter">
		<input type="hidden" name="page" value="mla-settings-menu-documentation" />
		<input type="hidden" name="mla_tab" value="documentation" />
		<input type="hidden" name="mla-example-display" value="true" />
		[+_wpnonce+]

<!-- template="after-example-table" -->
		<p class="submit mla-settings-submit">
		<input name="mla-example-cancel" type="submit" class="button-primary" value="Cancel" />&nbsp;
		</p>
	</form><!-- /id=mla-search-example-filter --> 
</div><!-- /col-container -->

<!-- template="view-plugin" -->
<h2>[+View Plugin+]</h2>
<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-view-plugin">
&nbsp;<br />
    <table>
        <tr>
            <td>
                <textarea name="" id="mla-view-plugin" rows="30" cols="100" readonly="readonly">[+plugin_text+]</textarea>
            </td>
        </tr>
	</table>
    <p class="submit mla-settings-submit">
        <input name="mla-view-plugin-close" class="button-primary" id="mla-view-plugin-close" type="submit" value="[+Close+]" />
    </p>
[+_wpnonce+]
[+_wp_http_referer+]
</form>
