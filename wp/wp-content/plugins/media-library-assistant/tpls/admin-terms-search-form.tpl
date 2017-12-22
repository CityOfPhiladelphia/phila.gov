<!-- template="mla-terms-search-taxonomy" -->
						<li>
							<input name="mla_terms_search[taxonomies][]" type="checkbox" [+taxonomy_checked+] value="[+taxonomy_slug+]" />&nbsp;[+taxonomy_label+]
						</li>
<!-- template="mla-terms-search-empty-div" -->
	<div id="mla-terms-search-div" style="display: none;">
		<div id="mla-terms-search-head-div"> [+Search Terms+]
			<div id="mla-terms-search-close-div"></div>
		</div>
		<div id="mla-terms-search-inside-div">
		[+message+]
		</div>
	</div>
	<!-- mla-terms-search-div -->
<!-- template="mla-terms-search-div" -->
	<div id="mla-terms-search-div" style="display: none;">
		<div id="mla-terms-search-head-div"> [+Search Terms+]
			<div id="mla-terms-search-close-div"></div>
		</div>
		<div id="mla-terms-search-inside-div">
			<div id="mla-terms-search-search-div">
				<label class="screen-reader-text" for="mla-terms-search-input">[+Search+]</label>
				<input name="mla_terms_search[phrases]" id="mla-terms-search-input" type="text" value="">
				<span class="spinner"></span>
				<input name="mla_terms_search[submit]" class="button" id="mla-terms-search-submit" type="submit" value="[+Search+]">
				<div class="clear"></div>
			</div>
			<div id="mla-terms-search-radio-div">
				<div id="mla-terms-search-radio-phrases-div">
					<ul class="mla-terms-search-options">
						<li>
							<input name="mla_terms_search[radio_phrases]" id="mla-terms-search-radio-phrases-and" type="radio" value="AND" [+phrases_and_checked+] />
							[+All phrases+]
						</li>
						<li>
							<input name="mla_terms_search[radio_phrases]" id="mla-terms-search-radio-phrases-or" type="radio" value="OR" [+phrases_or_checked+] />
							[+Any phrase+]
						</li>
					</ul>
				</div>
				<div id="mla-terms-search-radio-terms-div">
					<ul class="mla-terms-search-options">
						<li>
							<input name="mla_terms_search[radio_terms]" id="mla-terms-search-radio-terms-and" type="radio" value="AND" [+terms_and_checked+] />
							[+All terms+]
						</li>
						<li>
							<input name="mla_terms_search[radio_terms]" id="mla-terms-search-radio-terms-or" type="radio" value="OR" [+terms_or_checked+] />
							[+Any term+]
						</li>
					</ul>
				</div>
				<div id="mla-terms-search-exact-div">
					<ul class="mla-terms-search-options">
						<li>
							<input name="mla_terms_search[exact]" id="mla-terms-search-exact" type="checkbox" value="exact" [+exact_checked+] />
							[+Exact+]
						</li>
					</ul>
				</div>
			</div>
			<div class="clear"></div>
			<div id="mla-terms-search-taxonomies-div">
				<ul class="mla-terms-search-taxonomies">
[+mla_terms_search_taxonomies+]
				</ul>
			</div>
		</div>
	</div>
	<!-- mla-terms-search-div -->
<!-- template="mla-terms-search-form" -->
<form id="mla-terms-search-form" action="[+mla_terms_search_url+]" method="post">
	<input name="mla_admin_action" id="mla-terms-search-action" type="hidden" value="[+mla_terms_search_action+]">
[+wpnonce+]
[+mla_terms_search_div+]
</form>
