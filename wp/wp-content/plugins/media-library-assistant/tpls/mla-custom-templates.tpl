<!-- template="default-style" -->
<!-- mla_shortcode_slug="gallery" -->
<!-- mla_description="CSS Styles for the gallery shortcode" -->
<style type='text/css'>
	#[+selector+] {
		margin: auto;
		width: 100%;
	}
	#[+selector+] .gallery-item {
		float: [+float+];
		margin: [+margin+];
		text-align: center;
		width: [+itemwidth+];
	}
	#[+selector+] .gallery-item .gallery-icon img {
		border: 2px solid #cfcfcf;
	}
	#[+selector+] .gallery-caption {
		margin-left: 0;
		vertical-align: top;
	}
	/* see mla_gallery_shortcode() in media-library-assistant/includes/class-mla-shortcode-support.php */
</style>

<!-- template="default-description-markup" -->
This template uses the itemtag, icontag and captiontag values to compose a list-based gallery display.
<!-- template="default-arguments-markup" -->
mla_shortcode_slug="gallery"
<!-- template="default-open-markup" -->
<div id='[+selector+]' class='gallery galleryid-[+id+] gallery-columns-[+columns+] gallery-size-[+size_class+]'>

<!-- template="default-row-open-markup" -->
<!-- row-open -->

<!-- template="default-item-markup" -->
<[+itemtag+] class='gallery-item [+last_in_row+]'>
	<[+icontag+] class='gallery-icon [+orientation+]'>
		[+link+]
	</[+icontag+]>
	[+captiontag_content+]
</[+itemtag+]>

<!-- template="default-row-close-markup" -->
<br style="clear: both" />

<!-- template="default-close-markup" -->
</div>

<!-- template="tag-cloud-style" -->
<!-- mla_shortcode_slug="tag-cloud" -->
<style type='text/css'>
	#[+selector+] {
		margin: auto;
		width: 100%;
	}
	#[+selector+] .tag-cloud-item {
		float: [+float+];
		margin: [+margin+];
		text-align: center;
		width: [+itemwidth+];
	}
	#[+selector+] .tag-cloud-caption {
		margin-left: 0;
		vertical-align: top;
	}
	/* see mla_tag_cloud() in media-library-assistant/includes/class-mla-shortcode-support.php */
</style>

<!-- template="tag-cloud-arguments-markup" -->
mla_shortcode_slug="tag-cloud"
<!-- template="tag-cloud-open-markup" -->
<div id='[+selector+]' class='tag-cloud tag-cloud-taxonomy-[+taxonomy+] tag-cloud-columns-[+columns+]'>

<!-- template="tag-cloud-row-open-markup" -->
<!-- row-open -->

<!-- template="tag-cloud-item-markup" -->
<[+itemtag+] class='tag-cloud-item [+last_in_row+]'>
	<[+termtag+] class='tag-cloud-term'>
		[+thelink+]
	</[+termtag+]>
	<[+captiontag+] class='wp-caption-text tag-cloud-caption'>
		[+caption+]
	</[+captiontag+]>
</[+itemtag+]>

<!-- template="tag-cloud-row-close-markup" -->
<br style="clear: both" />

<!-- template="tag-cloud-close-markup" -->
</div>

<!-- template="tag-cloud-ul-arguments-markup" -->
mla_shortcode_slug="tag-cloud"
<!-- template="tag-cloud-ul-open-markup" -->
<[+itemtag+] id='[+selector+]' class='tag-cloud tag-cloud-taxonomy-[+taxonomy+]'>

<!-- template="tag-cloud-ul-item-markup" -->
	<[+termtag+] class='tag-cloud-term'>[+thelink+]</[+termtag+]>

<!-- template="tag-cloud-ul-close-markup" -->
</[+itemtag+]>

<!-- template="tag-cloud-dl-arguments-markup" -->
mla_shortcode_slug="tag-cloud"
<!-- template="tag-cloud-dl-open-markup" -->
<[+itemtag+] id='[+selector+]' class='tag-cloud tag-cloud-taxonomy-[+taxonomy+]'>

<!-- template="tag-cloud-dl-item-markup" -->
	<[+termtag+] class='tag-cloud-term'>[+thelink+]</[+termtag+]>
	<[+captiontag+] class='wp-caption-text tag-cloud-caption'>[+caption+]</[+captiontag+]>

<!-- template="tag-cloud-dl-close-markup" -->
</[+itemtag+]>

<!-- template="term-list-style" -->
<!-- mla_shortcode_slug="term-list" -->
<style type='text/css'>
	#[+selector+] {
		margin: auto;
		width: 100%;
	}
	#[+selector+] .term-list-item {
		text-align: left;
	}
	#[+selector+] .term-list-caption {
		margin-left: 0;
		vertical-align: top;
	}

	#[+selector+].term-list-checklist {
		list-style: none;
	}
	/* see mla_term_list() in media-library-assistant/includes/class-mla-shortcode-support.php */
</style>

<!-- template="term-list-ul-arguments-markup" -->
mla_shortcode_slug="term-list"
<!-- template="term-list-ul-open-markup" -->
<[+itemtag+] [+itemtag_attributes+] class="[+itemtag_class+]" id="[+itemtag_id+]">

<!-- template="term-list-ul-item-markup" -->
	<[+termtag+] [+termtag_attributes+] class="[+termtag_class+]" id="[+termtag_id+]">[+thelink+]
	[+children+]</[+termtag+]>

<!-- template="term-list-ul-close-markup" -->
</[+itemtag+]>

<!-- template="term-list-dl-arguments-markup" -->
mla_shortcode_slug="term-list"
<!-- template="term-list-dl-open-markup" -->
<[+itemtag+] id='[+selector+]' class='term-list term-list-taxonomy-[+taxonomy+]'>

<!-- template="term-list-dl-item-markup" -->
	<[+termtag+] class='term-list-term'>[+thelink+]</[+termtag+]>
	<[+captiontag+] class='wp-caption-text term-list-caption'>[+caption+]</[+captiontag+]>

<!-- template="term-list-dl-close-markup" -->
</[+itemtag+]>

<!-- template="term-list-dropdown-arguments-markup" -->
mla_shortcode_slug="term-list"
<!-- template="term-list-dropdown-open-markup" -->
<[+itemtag+] [+multiple+] name='[+thename+]' class='term-list term-list-dropdown term-list-taxonomy-[+taxonomy+]' id='[+selector+]'>

<!-- template="term-list-dropdown-item-markup" -->
	<[+termtag+] class='term-list-term term-list-dropdown-term level-[+current_level+]' value='[+thevalue+]' [+selected+]>[+thelabel+]</[+termtag+]>
	[+children+]

<!-- template="term-list-dropdown-close-markup" -->
</[+itemtag+]>

<!-- template="term-list-checklist-arguments-markup" -->
mla_shortcode_slug="term-list"
<!-- template="term-list-checklist-open-markup" -->
<[+itemtag+] id='[+selector+]' class='term-list term-list-checklist term-list-taxonomy-[+taxonomy+]'>

<!-- template="term-list-checklist-item-markup" -->
	<[+termtag+] class='term-list-term term-list-checklist-term level-[+current_level+] [+popular+]' id='[+termtag_id+]'><label class='selectit'><input name='[+thename+]' id='in-[+termtag_id+]' type='checkbox' value='[+thevalue+]' [+selected+]>[+thelabel+]</label>[+children+]</[+termtag+]>

<!-- template="term-list-checklist-close-markup" -->
</[+itemtag+]>
