<!-- template="default-style" -->
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

<!-- template="tag-cloud-ul-open-markup" -->
<[+itemtag+] id='[+selector+]' class='tag-cloud tag-cloud-taxonomy-[+taxonomy+]'>

<!-- template="tag-cloud-ul-item-markup" -->
	<[+termtag+] class='tag-cloud-term'>[+thelink+]</[+termtag+]>

<!-- template="tag-cloud-ul-close-markup" -->
</[+itemtag+]>

<!-- template="tag-cloud-dl-open-markup" -->
<[+itemtag+] id='[+selector+]' class='tag-cloud tag-cloud-taxonomy-[+taxonomy+]'>

<!-- template="tag-cloud-dl-item-markup" -->
	<[+termtag+] class='tag-cloud-term'>[+thelink+]</[+termtag+]>
	<[+captiontag+] class='wp-caption-text tag-cloud-caption'>[+caption+]</[+captiontag+]>

<!-- template="tag-cloud-dl-close-markup" -->
</[+itemtag+]>
