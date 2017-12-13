<!-- template="page" -->
<a name="backtotop"></a>
&nbsp;
<div class="wrap">
<h1 class="wp-heading-inline">MLA Custom Feed Example [+version+] Settings</h1>
[+messages+]
[+tablist+]
[+tab_content+]
</div><!-- wrap -->

<!-- template="tablist" -->
<h2 class="nav-tab-wrapper">
[+tablist+]
</h2>
<!-- template="tablist-item" -->
<a data-tab-id="[+data-tab-id+]" class="nav-tab [+nav-tab-active+]" href="?page=[+settings-page+]&amp;mla_tab=[+data-tab-id+]">[+title+]</a>

<!-- template="messages" -->
<div class="[+mla_messages_class+]">
<p>
[+messages+]
</p>
[+dismiss_button+]
</div>

<!-- template="page-level-options" -->
<tr valign="top"><td class="textright">
<input name="mla_enable_custom_feeds" id="mla_enable_custom_feeds" type="checkbox" [+enable_custom_feeds_checked+] value="1">
</td><td>
&nbsp;<strong>Enable custom feed processing</strong>
<div class="mla-settings-help">&nbsp;&nbsp;Check this option to add the (active) custom feeds to the WordPress feeds list.</div>
</td></tr>


<!-- template="parent-select" -->
            <select name="mla_add_custom_feed[parent]" id="mla-custom-feed-parent">
[+options+]
            </select>

<!-- template="single-item-edit" -->
<h2>Edit Feed</h2>
<form action="[+form_url+]" method="post" class="validate" id="mla-edit-custom-feed">
	<input name="mla_edit_custom_feed[post_ID]" id="mla-custom-feed-post-ID" type="hidden" value="[+post_ID+]" />
	<input name="mla_edit_custom_feed[old_slug]" id="mla-custom-feed-old-slug" type="hidden" value="[+old_slug+]" />
	[+_wpnonce+]
	<table class="form-table" id="mla-edit-custom-feed-table">
		<tbody>
			<tr class="form-field custom-feed-slug-wrap">
				<th scope="row"> <label for="mla-custom-feed-slug">Slug</label>
				</th>
				<td><input name="mla_edit_custom_feed[slug]" id="mla-custom-feed-slug" type="text" value="[+slug+]" /></td>
			</tr>
			<tr class="form-field custom-feed-type-wrap">
				<th scope="row"> <label for="mla-custom-feed-type">Feed Type</label>
				</th>
				<td><select name="mla_edit_custom_feed[type]" id="mla-custom-feed-type">
				<option [+rss_selected+] value="rss">RSS</option>
				<option [+rss2_selected+] value="rss2">RSS2</option>
				<option [+rss_http_selected+] value="rss-http">RSS-HTTP</option>
			</select></td>
			</tr>
			<tr class="form-field custom-feed-title-wrap">
				<th scope="row"> <label for="mla-custom-feed-title">Title</label>
				</th>
				<td><input name="mla_edit_custom_feed[title]" id="mla-custom-feed-title" type="text" value="" /></td>
			</tr>
			<tr class="form-field custom-feed-link-wrap">
				<th scope="row"> <label for="mla-custom-link-slug">Link</label>
				</th>
				<td><input name="mla_edit_custom_feed[link]" id="mla-custom-feed-link" type="text" value="" /></td>
			</tr>
			<tr class="form-field custom-feed-description-wrap">
				<th scope="row"> <label for="mla-custom-feed-description">Description</label>
				</th>
				<td><input name="mla_edit_custom_feed[description]" id="mla-custom-feed-description" type="text" value="" /></td>
			</tr>
			<tr class="form-field custom-feed-last_build_date-wrap">
				<th scope="row"> <label for="mla-custom-feed-last_build_date">Last Built</label>
				</th>
				<td><select name="mla_edit_custom_feed[last_build_date]" id="mla-custom-feed-last_build_date">
				<option [+current_selected+] value="current">Current</option>
				<option [+modified_selected+] value="modified">Modified</option>
			</select></td>
			</tr>
			<tr class="form-field custom-feed-ttl-wrap">
				<th scope="row"> <label for="mla-custom-feed-ttl">TTL</label>
				</th>
				<td><input name="mla_edit_custom_feed[ttl]" id="mla-custom-feed-ttl" type="text" value="[+ttl+]" />
					<p class="description">&nbsp;Leave empty to omit this tag and use update period.</p></td>
			</tr>
			<tr class="form-field custom-feed-update-period-wrap">
				<th scope="row"> <label for="mla-custom-feed-update-period">Upd. Period</label>
				</th>
				<td><select name="mla_edit_custom_feed[update_period]" id="mla-custom-feed-update-period">
						<option [+none_selected+] value="none">None</option>
						<option [+hourly_selected+] value="hourly">Hourly</option>
						<option [+daily_selected+] value="daily">Daily</option>
						<option [+weekly_selected+] value="weekly">Weekly</option>
						<option [+monthly_selected+] value="monthly">Monthly</option>
						<option [+yearly_selected+] value="yearly">Yearly</option>
					</select></td>
			</tr>
			<tr class="form-field custom-feed-update-frequency-wrap">
				<th scope="row"> <label for="mla-custom-feed-update-frequency">Upd. Frequency</label>
				</th>
				<td><input name="mla_edit_custom_feed[update_frequency]" id="mla-custom-feed-update-frequency" type="text" value="[+update_frequency+]" /></td>
			</tr>
			<tr class="form-field custom-feed-update-base-wrap">
				<th scope="row"> <label for="mla-custom-feed-update-base">Upd. Base</label>
				</th>
				<td><input name="mla_edit_custom_feed[update_base]" id="mla-custom-feed-update-base" type="text" value="[+update_base+]" />
					<p class="description">&nbsp;Base date/time for Update schedule.</p></td>
			</tr>
			<tr class="form-field custom-feed-taxonomies-wrap">
				<th scope="row"> <label for="mla-custom-feed-taxonomies">Taxonomies</label>
				</th>
				<td><input name="mla_edit_custom_feed[taxonomies]" id="mla-custom-feed-taxonomies" type="text" value="[+taxonomies+]" /></td>
			</tr>
			<tr class="form-field custom-feed-parameters-wrap">
				<th scope="row"> <label for="mla-custom-feed-parameters">Parameters</label>
				</th>
				<td><input name="mla_edit_custom_feed[parameters]" id="mla-custom-feed-parameters" type="text" value="[+parameters+]" /></td>
			</tr>
			<tr class="form-field custom-feed-template-slug-wrap">
				<th scope="row"> <label for="mla-custom-feed-template-slug">Tpl. Slug</label>
				</th>
				<td><input name="mla_edit_custom_feed[template_slug]" id="mla-custom-feed-template-slug" type="text" value="[+template_slug+]" /></td>
			</tr>
			<tr class="form-field custom-feed-template-name-wrap">
				<th scope="row"> <label for="mla-custom-feed-template-name">Tpl. Name</label>
				</th>
				<td><input name="mla_edit_custom_feed[template_name]" id="mla-custom-feed-template-name" type="text" value="[+template_name+]" /></td>
			</tr>
			<tr class="form-field custom-feed-status-wrap">
				<th scope="row"> <label for="mla-custom-feed-status">Status</label>
				</th>
				<td><select name="mla_edit_custom_feed[status]" id="mla-custom-feed-status">
						<option [+active_selected+] value="1">Active</option>
						<option [+inactive_selected+] value="">Inactive</option>
					</select></td>
			</tr>
		</tbody>
	</table>
	<p class="submit mla-settings-submit">
		<input name="mla-edit-custom-feed-cancel" class="button-secondary" id="mla-edit-custom-feed-cancel" type="submit" value="Cancel" />
		&nbsp;
		<input name="mla-edit-custom-feed-submit" class="button-primary" name="mla-edit-custom-feed-submit" type="submit" value="Update" />
		&nbsp; </p>
</form>

<!-- template="before-table" --> 
<h2>Custom Feed Processing Options</h2>
<p>In this tab you can define custom RSS feeds to be added to the WordPress feed list.</p>
<p>You can find more information about using the features of this tab in the Documentation tab on this screen.</p>
<div id="ajax-response"></div>
<form action="[+form_url+]" method="get" id="mla-search-custom-feed-form">
	<input type="hidden" name="page" value="mlafeed-settings-general" />
	<input type="hidden" name="mla_tab" value="general" />
	[+view_args+]
	[+_wpnonce+] <span style="margin-top: 1em">
	<input name="mla-search-custom-feed-submit" class="button alignright" id="mla-search-custom-feed-submit" type="submit" value="Search Feeds" />
	<label class="screen-reader-text" for="mla-search-custom-feed-input">Search Feeds Text:</label>
	<input name="s" class="alignright" id="mla-search-custom-feed-input" type="search" value="[+s+]" />
	[+results+] </span>
</form>
<br class="clear" />
<div id="col-container">
	<div id="col-right">
		<div class="col-wrap">
			<form action="[+form_url+]" method="post" id="mla-search-custom-feed-filter">
				<input type="hidden" name="page" value="mlafeed-settings-general" />
				<input type="hidden" name="mla_tab" value="general" />
				[+view_args+]
				[+_wpnonce+] 
				
<!-- template="after-table" -->
			</form>
			<!-- /id=mla-search-custom-feed-filter --> 
		</div>
		<!-- /col-wrap --> 
	</div>
	<!-- /col-right -->
<style type='text/css'>
.mla-settings-help {
	font-size: 8pt;
	padding-bottom: 5px
}

.mla-settings-enable-form {
	margin-left: 0px;
	margin-top: 10px;
	padding-bottom: 10px;
	border-bottom:thin solid #888888;
}

span.submit.mla-settings-submit,
p.submit.mla-settings-submit {
	padding-bottom: 0px
}
.
mla-settings-enable-form {
	margin-left: 0px;
	margin-top: 10px;
	padding-bottom: 10px;
	border-bottom:thin solid #888888;
}

#mla-add-custom-feed-table {
	margin-bottom: 15px;
}

#mla-add-custom-feed-table th {
	padding: 5px 5px 5px 0px;
	width: 10px;
}

#mla-add-custom-feed-table td {
	padding: 5px 0px 5px 0px;
}

#mla-add-custom-feed-table input[type="text"],
#mla-add-custom-feed-table select {
	width: 100%;
}
</style>
	<div id="col-left">
		<div class="col-wrap">
			<div class="mla-settings-enable-form">
				<form action="[+form_url+]" method="post" class="mla-display-settings-page" id="mla-display-settings-custom-feed-tab">
					<table class="optiontable">
					<tbody>
						[+options_list+]
					</tbody>
					</table>
					<span class="submit mla-settings-submit">
					<input name="mla-custom-feed-options-save" class="button-primary" id="mla-custom-feed-options-save" type="submit" value="Save Changes" />
					</span>
					[+_wpnonce+]
				</form>
			</div>
			<div class="form-wrap">
				<h2>Add New Feed</h2>
				<p>Field definitions and rules are in the Documentation tab.</p>
				<form action="[+form_url+]" method="post" class="validate" id="mla-add-custom-feed">
					<input type="hidden" name="page" value="mlafeed-settings-general" />
					<input type="hidden" name="mla_tab" value="general" />
					[+_wpnonce+]
					<table class="form-table" id="mla-add-custom-feed-table">
						<tbody>
							<tr class="form-field custom-feed-slug-wrap">
								<th scope="row"> <label for="mla-custom-feed-slug">Slug</label>
								</th>
								<td><input name="mla_add_custom_feed[slug]" id="mla-custom-feed-slug" type="text" value="" /></td>
							</tr>
							<tr class="form-field custom-feed-type-wrap">
								<th scope="row"> <label for="mla-custom-feed-type">Feed Type</label>
								</th>
								<td><select name="mla_add_custom_feed[type]" id="mla-custom-feed-type">
								<option value="rss">RSS</option>
								<option selected="selected" value="rss2">RSS2</option>
								<option value="rss-http">RSS-HTTP</option>
							</select></td>
							</tr>
							<tr class="form-field custom-feed-title-wrap">
								<th scope="row"> <label for="mla-custom-feed-title">Title</label>
								</th>
								<td><input name="mla_add_custom_feed[title]" id="mla-custom-feed-title" type="text" value="" /></td>
							</tr>
							<tr class="form-field custom-feed-link-wrap">
								<th scope="row"> <label for="mla-custom-link-slug">Link</label>
								</th>
								<td><input name="mla_add_custom_feed[link]" id="mla-custom-feed-link" type="text" value="" /></td>
							</tr>
							<tr class="form-field custom-feed-description-wrap">
								<th scope="row"> <label for="mla-custom-feed-description">Description</label>
								</th>
								<td><input name="mla_add_custom_feed[description]" id="mla-custom-feed-description" type="text" value="" /></td>
							</tr>
							<tr class="form-field custom-feed-last_build_date-wrap">
								<th scope="row"> <label for="mla-custom-feed-last_build_date">Last Built</label>
								</th>
								<td><select name="mla_add_custom_feed[last_build_date]" id="mla-custom-feed-last_build_date">
								<option selected="selected" value="current">Current</option>
								<option value="modified">Modified</option>
							</select></td>
							</tr>
							<tr class="form-field custom-feed-ttl-wrap">
								<th scope="row"> <label for="mla-custom-feed-ttl">TTL</label>
								</th>
								<td><input name="mla_add_custom_feed[ttl]" id="mla-custom-feed-ttl" type="text" value="" />
									<p class="description">&nbsp;Leave empty to omit this tag and use update period.</p></td>
							</tr>
							<tr class="form-field custom-feed-update-period-wrap">
								<th scope="row"> <label for="mla-custom-feed-update-period">Upd. Period</label>
								</th>
								<td><select name="mla_add_custom_feed[update_period]" id="mla-custom-feed-update-period">
										<option value="none">None</option>
										<option selected="selected" value="hourly">Hourly</option>
										<option value="daily">Daily</option>
										<option value="weekly">Weekly</option>
										<option value="monthly">Monthly</option>
										<option value="yearly">Yearly</option>
									</select></td>
							</tr>
							<tr class="form-field custom-feed-update-frequency-wrap">
								<th scope="row"> <label for="mla-custom-feed-update-frequency">Upd. Frequency</label>
								</th>
								<td><input name="mla_add_custom_feed[update_frequency]" id="mla-custom-feed-update-frequency" type="text" value="1" /></td>
							</tr>
							<tr class="form-field custom-feed-update-base-wrap">
								<th scope="row"> <label for="mla-custom-feed-update-base">Upd. Base</label>
								</th>
								<td><input name="mla_add_custom_feed[update_base]" id="mla-custom-feed-update-base" type="text" value="2000-01-01T12:00+00:00" />
									<p class="description">&nbsp;Base date/time for Update schedule.</p></td>
							</tr>
							<tr class="form-field custom-feed-taxonomies-wrap">
								<th scope="row"> <label for="mla-custom-feed-taxonomies">Taxonomies</label>
								</th>
								<td><input name="mla_add_custom_feed[taxonomies]" id="mla-custom-feed-taxonomies" type="text" value="" /></td>
							</tr>
							<tr class="form-field custom-feed-parameters-wrap">
								<th scope="row"> <label for="mla-custom-feed-parameters">Parameters</label>
								</th>
								<td><input name="mla_add_custom_feed[parameters]" id="mla-custom-feed-parameters" type="text" value="post_parent=all posts_per_page=6" /></td>
							</tr>
							<tr class="form-field custom-feed-template-slug-wrap">
								<th scope="row"> <label for="mla-custom-feed-template-slug">Tpl. Slug</label>
								</th>
								<td><input name="mla_add_custom_feed[template_slug]" id="mla-custom-feed-template-slug" type="text" value="" /></td>
							</tr>
							<tr class="form-field custom-feed-template-name-wrap">
								<th scope="row"> <label for="mla-custom-feed-template-name">Tpl. Name</label>
								</th>
								<td><input name="mla_add_custom_feed[template_name]" id="mla-custom-feed-template-name" type="text" value="" /></td>
							</tr>
							<tr class="form-field custom-feed-status-wrap">
								<th scope="row"> <label for="mla-custom-feed-status">Status</label>
								</th>
								<td><select name="mla_add_custom_feed[status]" id="mla-custom-feed-status">
										<option selected="selected" value="1">Active</option>
										<option value="">Inactive</option>
									</select></td>
							</tr>
						</tbody>
					</table>
					<p class="submit mla-settings-submit">
						<input name="mla-add-custom-feed-submit" class="button button-primary" id="mla-add-custom-feed-submit" type="submit" value="Add Feed" />
					</p>
				</form>
				<!-- /id=mla-add-custom-feed --> 
			</div>
			<!-- /form-wrap --> 
		</div>
		<!-- /col-wrap --> 
	</div>
	<!-- /col-left --> 
</div>
<!-- /col-container -->

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
    </p>
	<div class="mla-settings-help">[+Click Save Changes+]</div>
[+_wpnonce+]
[+_wp_http_referer+]
</form>

<!-- template="documentation-tab" -->
<h2>Plugin Documentation. In this tab, jump to:</h2>
<style type='text/css'>
.mla-doc-toc-list {
	list-style-position:inside;
	list-style:disc;
	line-height: 15px;
	padding-left: 20px
}

.mla-doc-hook-label {
	text-align: right;
	padding: 0 1em 2em 0;
	vertical-align: top;
	font-weight:bold
}

.mla-doc-hook-definition {
	vertical-align: top;
}

.mla-doc-table-label {
	text-align: right;
	padding-right: 10px;
	vertical-align: top;
	font-weight:bold
}

.mla-doc-table-sublabel {
	padding-right: 10px;
	vertical-align: top
}

.mla-doc-table-reverse {
	text-align: right;
	padding-right: 10px;
	vertical-align:top
}

.mla-doc-table-definition {
	vertical-align: top;
}

.mla-doc-bold-link {
	font-size:14px;
	font-weight:bold
}
</style>
<div class="mla-display-settings-page" id="mla-display-settings-documentation-tab" style="width:700px">
<ul class="mla-doc-toc-list">
<li><a href="#introduction"><strong>Introduction</strong></a></li>
<li><a href="#defining"><strong>Defining Your Feeds</strong></a></li>
<ul class="mla-doc-toc-list">
<li><a href="#elements">Feed Elements</a></li>
<li><a href="#taxonomies">Taxonomies</a></li>
<li><a href="#parameters">Data Selection Parameters</a></li>
</ul>
<li><a href="#managing"><strong>Managing Your Feeds</strong></a></li>
<li><a href="#feed-templates"><strong>Feed Templates</strong></a></li>
<ul class="mla-doc-toc-list">
<li><a href="#default-template">Default Template</a></li>
<li><a href="#theme-template">Theme-based Templates</a></li>
</ul>
<li><a href="#accessing"><strong>Accessing Your Feeds</strong></a></li>
<ul class="mla-doc-toc-list">
<li><a href="#url-slug">URL-based slugs</a></li>
<li><a href="#query-slug">HTML Query Parameter slugs</a></li>
<li><a href="#tax-arguments">Taxonomy Arguments</a></li>
<li><a href="#query-parms">Other HTML Query Arguments</a></li>
</ul>
</ul>
<a name="introduction"></a>
<p>
<a href="#backtotop">Go to Top</a>
</p>
<h3>Introduction</h3>
<p>
This example plugin lets you define one or more WordPress RSS feeds that return Media Library items filtered by the data selection parameters supported in the <code>[mla_gallery]</code> shortcode. You can define as many feeds as you like, each with different parameters and other settings.
</p>
<p>
You can find more general information in the <a href="https://codex.wordpress.org/WordPress_Feeds" title="WordPress Feeds codex article" target="_blank">WordPress Feeds</a> and <a href="https://codex.wordpress.org/Customizing_Feeds" title="WordPress Feeds codex article" target="_blank">Customizing Feeds</a> codex articles. Other resources include:
</p>
<ul class="mla-doc-toc-list">
<li><a href="http://www.rssboard.org/rss-specification" target="_blank">RSS 2.0 Specification</a></li>
<li><a href="http://www.rssboard.org/rss-profile" target="_blank">Really Simple Syndication Best Practices Profile</a></li>
<li><a href="http://www.feedforall.com/syndication.htm" target="_blank">FeedForAll Syndication Extension</a></li>
<li><a href="http://web.resource.org/rss/1.0/modules/syndication/" target="_blank">RDF Site Summary 1.0 Modules: Syndication</a></li>
<li><a href="https://codex.wordpress.org/WordPress_Feeds#More_Information_and_Resources" target="_blank">More WordPress Information</a></li>
</ul>
<p>
The basic idea is quite simple. You define a feed and give it a name, or "feed slug", such as "mlafeed". You specify MLA data selection parameters that define which Media Library items are part of the feed. When a feed reader accesses the feed, the data selection parameters are executed and the items are returned in a format that the feed reader can process.
<a name="#defining"></a>
</p>
<p>
<a href="#backtotop">Go to Top</a>
</p>
<h3><strong>Defining Your Feeds</strong></h3>
<p>
You can define a new feed, also known as an RSS "channel" using the "Add New Feed" area at the left of the Settings/MLA Feed admin screen. Simply fill in the field values you want and click "Add Feed" at the bottom of the area. The new feed will be added to the submenu table in the right-hand side of the screen. If you set the "Active" status the feed will be added to the WordPress feed list.
<a name="#elements"></a>
</p>
<p>
<a href="#backtotop">Go to Top</a>
</p>
<h4>Feed Elements</h4>
<p>
Each RSS feed, or channel, is defined by an XML-based document that contains a number of data elements for the channel and each of the items it contains. The MLA Custom Feed Example Plugin contains a PHP file that generates the document when the channel is accessed. You can modify or replace the PHP file to customize any aspect of the feed, but the Feed Elements described here give you an easy way to adjust the most common elements without any coding.
</p>
<table>
<tr>
<td class="mla-doc-table-label">Slug</td>
<td>is the feed/channel "name", the identifier for the feed. It will become part of the URL that is used to access the feed, so it is sanitized by translating all letters to lowercase and spaces to dashes; punctuation is removed. The slug must be unique.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Feed Type</td>
<td>selects a mime-type for the "Content-Type:" HTML header. Acceptable values are "RSS", "RSS2" or "RSS-HTTP"; the default value is "RSS". The "RSS-HTTP" type can be helpful for testing; it will usually make the browser display the feed content in the browser window.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Title</td>
<td>The name of the channel. It's how people refer to your service. If you have an HTML website that contains the same information as your RSS file, the title of your channel should be the same as the title of your website.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Link</td>
<td>The URL to the HTML website corresponding to the channel.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Description</td>
<td>Phrase or sentence describing the channel.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Last Built</td>
<td>The last time the content of the channel changed. You can select the current date and time, i.e., the time when the channel is accessed, or the most recent "modified" date for items in the channel.</td>
</tr>
<tr>
<td class="mla-doc-table-label">TTL</td>
<td>TTL stands for time to live. It's a number of minutes that indicates how long a channel can be cached before refreshing from the source. If you leave this field blank the TTL field will not be added to the channel and the Update Period, Frequency and Base fields will be used instead.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Upd. Period</td>
<td>The interval or units used by the "Update Frequency" element and is explained in more detail in it's description. Possible values: 'hourly', 'daily', 'weekly', 'monthly', or 'yearly'.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Upd. Frequency</td>
<td>How often the feed is typically updated. This helps some automated systems that access RSS feeds to know when it should check back for updates. The "Update Frequency" and the "Update Period" are used together. For example if your RSS feed is typically updated every other week, you would set the "Update Period" to 'weekly' and the "Update Frequency" to '2'.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Upd. Base</td>
<td>The date and time that the update interval should be calculated from. For example if a feed's "Update Period" is 'yearly' and its "Update Frequency" is '1' , the reading application won't know from what date it should use to calculate a year from, to then look for an update. The date/time format is in W3CDTF format (ie. 2006-01-25+14:00+02:00)</td>
</tr>
<tr>
<td class="mla-doc-table-label">Taxonomies</td>
<td>Each item can have one or more "Category" elements. More information is in the <a href="#taxonomies">Taxonomies</a> section below.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Parameters</td>
<td>The data selection parameters used to select items for the channel. More information is in the <a href="#parameters">Data Selection Parameters</a> section below.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Tpl. Slug</td>
<td>The "slug" portion of a custom WordPress template in your theme files. More information is in the <a href="#theme-template">Theme-based Templates</a> section below.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Tpl. Name</td>
<td>The "name" portion of a custom WordPress template in your theme files. More information is in the <a href="#theme-template">Theme-based Templates</a> section below.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Status</td>
<td>You can select "Active" to include the channel in the WordPress feed list or "Inactive" to leave it out of the list. This is an easy way to keep a channel from being accessed but leave its definition in place for future use.</td>
</tr>
</table>
<p>
&nbsp;
<a name="#taxonomies"></a>
</p>
<p>
<a href="#backtotop">Go to Top</a>
</p>
<h4>Taxonomies</h4>
<p>
Each item can be classified by one or more "Category" elements. You can name the taxonomies that these elements will be taken from, e.g., <code>attachment_category</code>. Multiple taxonomies can be named, separated by commas. When the item is added to a feed the plugin will retrieve all the terms assigned to the item, de-duplicate them and add them to the item in the feed.
<a name="#parameters"></a>
</p>
<p>
<a href="#backtotop">Go to Top</a>
</p>
<h4>Data Selection Parameters</h4>
<p>
The Data selection Parameters are the most important element of each feed. The example plugin uses the same MLA code that composes galleries for <code>[mla_gallery]</code>. For example, you can use <code>post_mime_type=application/pdf</code> to get a feed of your PDF documents. Author, taxonomy, date/time, custom field and keyword/term search parameters are all accepted.
</p>
<p>
A common use of selection parameters is to select items from one or more terms assigned to a taxonomy. WordPress includes code to extract taxonomy and term values from the feed url. The example plugin will copy these values into the data selection parameters for your feed. Let's say you want to select the items assigned to the "abc" term in the Att. Categories taxonomy. You can use a content template to add these values to your Data Selection Parameters for a feed you have named "mlafeed":
</p>
<code>[+template:((attachment_category=[+request:attachment_category+])|(attachment_category=[+query:attachment_category+])|post_parent=all)+] posts_per_page=6</code>
<p>
With the above template you can access the feed in different ways:
</p>
<ul>
<li><code>http://www.example.com/?attachment_category=abc&feed=mlafeed</code></li>
<li><code>http://www.example.com/attachment_category/abc/mlafeed</code></li>
<li><code>http://www.example.com/mlafeed/?attachment_category=abc</code></li>
</ul>
<p>
Of course, you could also code the taxonomy and term values explicitly in the Data Selection parameters, e.g.,<br />&nbsp;<br /> 
<code>attachment_category=abc posts_per_page=6</code><br  />&nbsp;<br />
and then just use the feed name to access it.
<a name="#managing"></a>
</p>
<p>
<a href="#backtotop">Go to Top</a>
</p>
<h3><strong>Managing Your Feeds</strong></h3>
<p>
Managing your feeds is just like managing posts, pages and Media Library items:
</p>
<ul class="mla-doc-toc-list">
<li>You can select the fields you want to display by clicking the "Screen Options" drop down and checking the boxes next to the field name(s)</li>
<li>You can change the number of feeds per page by clicking the "Screen Options" drop down, entering a new value for "Feeds per page:" and clicking "Apply"</li>
<li>You can filter the list of feed by entering a key word and clicking the "Search Feeds" button</li>
<li>You can show just Active or Inactive feeds by selecting a Status value and clicking "Filter"</li>
<li>You can delete several feeds at once by checking the box to the left of the slug, selecting the "Delete" Bulk Action and clicking "Apply"</li>
<li>You can edit a feed or delete it by hovering over the feed Slug and clicking the rollover action that appears under the slug value</li>
</ul>
<a name="#feed-templates"></a>
</p>
<p>
<a href="#backtotop">Go to Top</a>
</p>
<h3><strong>Feed Templates</strong></h3>
<p>
The example plugin uses a PHP feed template file to display its feeds, in much the same way as WordPress uses theme templates to display your content. The feed template is located in the plugin's root directory. It is possible to use custom feed templates to achieve a theme-based solution (see further information and links below) or change which template is used on a feed-by-feed basis. 
<a name="#default-template"></a>
</p>
<p>
<a href="#backtotop">Go to Top</a>
</p>
<h4>Default Template</h4>
<p>
The example plugin includes a default template file modeled on the WordPress <code>feed-rss2.php</code> file. This file, <code>/plugins//mla-custom-feed-example/mla-custom-feed-template.php</code>, is in the root directory of the example plugin. You can use this file as a model for your own theme-based custom templates.
<p>
The default file uses a number of fields defined in the Feed Elements section above. In the code you can see these as elements of the "active feed" array, such as <code>MLACustomFeedExample::$active_feed['title']</code>. The file also has a traditional WordPress "loop" to process the items selected by the data selection parameters:<br />&nbsp;<br /> 
<code>while ( MLACustomFeedExample::$wp_query_object->have_posts() ) : MLACustomFeedExample::$wp_query_object->the_post();</code><br  />&nbsp;<br />
You can use all of the WordPress template tags to access item values within the "loop".
<a name="#theme-template"></a>
</p>
<p>
<a href="#backtotop">Go to Top</a>
</p>
<h4>Theme-based Templates</h4>
<p>
You can create your own template file and add it to your theme or child theme. Defining your own template file goves you complete control over the feed content.
</p>
<p>
If you define your own template file you can substitute it for the default file by filling in the "Tpl. Slug" and optionally the "Tpl. Name" fields in the Add New Theme area. For example, let's say you have created a template file:<br />&nbsp;<br /> 
<code>/wp-content/themes/my-theme/mlafeed-authors.php</code><br  />&nbsp;<br />
You can access the template by entering:<br />&nbsp;<br /> 
Tpl. Slug: mlafeed<br  />
Tpl. Name: authors<br  />&nbsp;<br />
in the Add New Feed area.
<a name="#accessing"></a>
</p>
<p>
<a href="#backtotop">Go to Top</a>
</p>
<h3><strong>Accessing Your Feeds</strong></h3>
<p>
Accessing your custom feeds follows several rules defined by WordPress. The "Slug" that you use to name your feed(s) is the most important element, but WordPress has some additional rules for formatting URLs that contain taxonomy arguments as well. 
<a name="#url-slug"></a>
</p>
<p>
<a href="#backtotop">Go to Top</a>
</p>
<h4>URL-based slugs</h4>
<p>
If your site uses Custom/Pretty Permalinks you can use the feed slug as part of the URL. For example, the "mlafeed" feed can be accessed as:<br />&nbsp;<br /> 
<code>http://www.example.com/mlafeed/</code><br  />
&nbsp;
<a name="#query-slug"></a>
</p>
<p>
<a href="#backtotop">Go to Top</a>
</p>
<h4>HTML Query Argument slugs</h4>
<p>
No matter what permalink structure you use you can always specify a feed using a query argument, e.g.,<br />&nbsp;<br /> 
<code>http://www.example.com/?feed=mlafeed</code><br  />
&nbsp;
<a name="#tax-arguments"></a>
</p>
<p>
<a href="#backtotop">Go to Top</a>
</p>
<h4>Taxonomy Arguments</h4>
<p>
You can provide feeds for specific taxonomy terms as part of the URL or as an HTML query argument:<br />&nbsp;<br />
<code>http://l.mladev/attachment_tag/abc,def/mlafeed/</code><br  />
<code>http://l.mladev/mlafeed/?attachment_tag=abc,def</code><br  />
</p>
<p>
Passing the parameter values into your data selection parameters is different for each case. For the URL case WordPress parses the URL components into the database query parameters and you can access the value as <code>[+query:attachment_tag+]</code>. If you choose the HTML query argument format the value will be available in the "request:" area, i.e., <code>[+request:attachment_tag+]</code>. I regret the confusing prefix values but that's how it works.
</p>
<p>
<a name="#query-parms"></a>
</p>
<p>
<a href="#backtotop">Go to Top</a>
</p>
<h4>Other HTML Query Arguments</h4>
<p>
You can pass any other parameters you need as query arguments following the basic URL components, e.g.<br />&nbsp;<br />
<code>http://l.mladev/attachment_tag/abc,def/mlafeed/?author=johnsmith</code><br  />
&nbsp;<br />
The above example would select all items "owned" by John Smith and assigned to the "abc" or "def" terms in the Att. Tags taxonomy. The corresponding data selection parameters in the "mlafeed" would be:<br />&nbsp;<br />
<code>attachment_tag=[+query:attachment_tag+] author=[+request:author+]</code><br  />
&nbsp;<br />
</div>