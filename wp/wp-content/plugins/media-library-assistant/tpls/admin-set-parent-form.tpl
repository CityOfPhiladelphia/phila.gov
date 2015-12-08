<!-- template="post-type-select-option" -->
                <option [+selected+] value="[+value+]">[+text+]</option>

<!-- template="post-type-select" -->
            <select name="mla_set_parent_post_type" id="mla-set-parent-post-type">
[+options+]
            </select>

<!-- template="mla-set-parent-div" -->
	<div id="mla-set-parent-div" style="display: none;">
		<input name="parent" id="mla-set-parent-parent" type="hidden" value="">
		<input name="children[]" id="mla-set-parent-children" type="hidden" value="">
		[+mla_find_posts_nonce+]
		<div id="mla-set-parent-head-div"> [+Select Parent+]
			<div id="mla-set-parent-close-div"></div>
		</div>
		<div id="mla-set-parent-inside-div">
			<div id="mla-set-parent-search-div">
				<label class="screen-reader-text" for="mla-set-parent-input">[+Search+]</label>
				<input name="mla_set_parent_search_text" id="mla-set-parent-input" type="text" value="">
				<span class="spinner"></span>
				<input class="button" id="mla-set-parent-search" type="button" value="[+Search+]">
				&nbsp;[+post_type_dropdown+]
				<div class="clear"></div>
			</div>
			<div id="mla-set-parent-titles-div">
				<div id="mla-set-parent-current-title-div">
				[+For+]: <span id="mla-set-parent-titles"></span>
				</div>
				<div id="mla-set-parent-pagination-div">
					<input class="button" id="mla-set-parent-previous" type="button" value="[+Previous+]">
					<input class="button" id="mla-set-parent-next" type="button" value="[+Next+]">
				</div>
			</div>
			<div class="clear"></div>
			<div id="mla-set-parent-response-div">
				<input name="mla_set_parent_count" id="mla-set-parent-count" type="hidden" value="[+count+]">
				<input name="mla_set_parent_paged" id="mla-set-parent-paged" type="hidden" value="[+paged+]">
				<input name="mla_set_parent_found" id="mla-set-parent-found" type="hidden" value="[+found+]">
				<table class="widefat">
					<thead><tr>
						<th class="found-radio"><br /></th>
						<th>[+Title+]</th>
						<th class="no-break">[+Type+]</th>
						<th class="no-break">[+Date+]</th>
						<th class="no-break">[+Status+]</th>
					</tr></thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
		<div id="mla-set-parent-buttons-div">
			[+mla_set_parent_cancel+]
			[+mla_set_parent_update+]
			<div class="clear"></div>
		</div>
	</div>
	<!-- mla-set-parent-div -->
	<table id="found-0-table" style="display: none">
		<tbody>
			<tr id="found-0-row" class="found-posts">
				<td class="found-radio">
					<input name="found_post_id" id="found-0" type="radio" value="0">
				</td>
				<td>
					<label for="found-0">([+Unattached+])</label>
				</td>
				<td class="no-break">&mdash;</td>
				<td class="no-break">&mdash;</td>
				<td class="no-break">&mdash;</td>
			</tr>
		</tbody>
	</table>
<!-- template="mla-set-parent-form" -->
<form id="mla-set-parent-form" action="[+mla_set_parent_url+]" method="post">
	<input name="mla_admin_action" id="mla-set-parent-action" type="hidden" value="[+mla_set_parent_action+]">
	[+wpnonce+]
	[+mla_set_parent_div+]
</form>
