﻿=== Media Library Assistant ===
Contributors: dglingren
Donate link: http://fairtradejudaica.org/make-a-difference/donate/
Tags: attachment, attachments, documents, gallery, image, images, media, library, media library, tag cloud, media-tags, media tags, tags, media categories, categories, IPTC, EXIF, XMP, GPS, PDF, meta, metadata, photo, photos, photograph, photographs, photoblog, photo albums, lightroom, photoshop, MIME, mime-type, icon, upload, file extensions, WPML, Polylang, multilanguage, multilingual, localization
Requires at least: 3.5.0
Tested up to: 4.3
Stable tag: 2.15
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enhances the Media Library; powerful [mla_gallery], taxonomy support, IPTC/EXIF/PDF processing, bulk/quick edit actions and where-used reporting.

== Description ==

The Media Library Assistant provides several enhancements for managing the Media Library, including:

* The **`[mla_gallery]` shortcode**, used in a post, page or custom post type to add a gallery of images and/or other Media Library items (such as PDF documents). MLA Gallery is a superset of the WordPress `[gallery]` shortcode; it is compatible with `[gallery]` and provides many enhancements. These include: 1) full query and display support for WordPress categories, tags, custom taxonomies and custom fields, 2) support for all post_mime_type values, not just images 3) media Library items need not be "attached" to the post, and 4) control over the styles, markup and content of each gallery using Style and Markup Templates. **Twenty-eight hooks** provided for complete gallery customization from your theme or plugin code.

* The **`[mla_tag_cloud]` shortcode**, used in a post, page, custom post type or widget to display the "most used" terms in your Media Library where the size of each term is determined by how many times that particular term has been assigned to Media Library items. **Twenty-five hooks** provided for complete cloud customization from your theme or plugin code.

* Powerful **Content Templates**, which let you compose a value from multiple data sources, mix literal text with data values, test for empty values and choose among two or more alternatives or suppress output entirely.

* **Attachment metadata** such as file size, image dimensions and where-used information can be assigned to WordPress custom fields. You can then use the custom fields in your `[mla_gallery]` display and you can add custom fields as sortable, searchable columns in the Media/Assistant submenu table. You can also **modify the WordPress `_wp_attachment_metadata` contents** to suit your needs.

* **IPTC**, **EXIF (including GPS)**, **XMP** and **PDF** metadata can be assigned to standard WordPress fields, taxonomy terms and custom fields. You can update all existing attachments from the Settings page IPTC/EXIF tab, groups of existing attachments with a Bulk Action or one existing attachment from the Edit Media/Edit Single Item screen. Display **IPTC**, **EXIF**, **XMP** and **PDF** metadata with `[mla_gallery]` custom templates. **Twelve hooks** provided for complete mapping customization from your theme or plugin code.

* Support for **WPML** and **Polylang** multi-language CMS plugins.

* Complete control over **Post MIME Types, File Upload extensions/MIME Types and file type icon images**. Fifty four (54) additional upload types, 112 file type icon images and a searchable list of over 1,500 file extension/MIME type associations.

* **Integrates with Photonic Gallery, Jetpack and other plugins**, so you can add slideshows, thumbnail strips and special effects to your `[mla_gallery]` galleries.

* **Enhanced Search Media box**. Search can be extended to the name/slug, ALT text and caption fields. The connector between search terms can be "and" or "or". Search by attachment ID or Parent ID is supported, and you can search on keywords in the taxonomy terms assigned to Media Library items. Works in the Media Manager Modal Window, too.

* **Where-used reporting** shows which posts use a media item as the "featured image", an inserted image or link, an entry in a `[gallery]` and/or an entry in an `[mla_gallery]`.

* **Complete support for ALL taxonomies**, including the standard Categories and Tags, your custom taxonomies and the Assistant's pre-defined Att. Categories and Att. Tags. You can add taxonomy columns to the Assistant listing, filter on any taxonomy, assign terms and list the attachments for a term.

* Taxonomy and custom field support in the ATTACHMENT DETAILS pane of the Media Manager Modal Window.

* An inline **"Bulk Edit"** area; update author, parent and custom fields, add, remove or replace taxonomy terms for several attachments at once. Works on the Media/Add New screen as well.

* An inline **"Quick Edit"** action for many common fields and for custom fields

* Displays more attachment information such as parent information, file URL and image metadata.

* Allows you to edit the post_parent, the menu_order and to "unattach" items

* Provides additional view filters for MIME types and taxonomies

* Provides many more listing columns (more than 20) to choose from

The Assistant is designed to work like the standard Media Library pages, so the learning curve is short and gentle. Contextual help is provided on every new screen to highlight new features.

This plugin was inspired by my work on the WordPress web site for our nonprofit, Fair Trade Judaica. If you find the Media Library Assistant plugin useful and would like to support a great cause, consider a [<strong>tax-deductible</strong> donation](http://fairtradejudaica.org/make-a-difference/donate/ "Support Our Work") to our work. Thank you!

== Installation ==

1. Upload `media-library-assistant` and its subfolders to your `/wp-content/plugins/` directory, **OR** Visit the Plugins/Add New page and search for "Media Library Assistant"; click "Install Now" to upload it

1. Activate the plugin through the "Plugins" menu in WordPress

1. Visit the Settings/Media Library Assistant page to customize taxonomy (e.g., category and tag) support

1. Visit the Settings/Media Library Assistant Custom Fields and IPTC/EXIF tabs to map metadata to attachment fields

1. Visit the "Assistant" submenu in the Media admin section

1. Click the Screen Options link to customize the display

1. Use the enhanced Edit, Quick Edit and Bulk Edit pages to assign categories and tags

1. Use the `[mla_gallery]` shortcode to add galleries of images, documents and more to your posts and pages

1. Use the `[mla_tagcloud]` shortcode to add clickable lists of taxonomy terms to your posts and pages

== Frequently Asked Questions ==

= How can I sort the Media/Assistant submenu table on values such as File Size? =

You can add support for many attachment metadata values such as file size by visiting the Custom Fields tab on the Settings page. There you can define a rule that maps the data to a WordPress custom field and check the "MLA Column" box to make that field a sortable column in the Media/Assistant submenu table. You can also use the field in your `[mla_gallery]` shortcodes. For example, this shortcode displays a gallery of the ten largest images in the "general" category, with a custom caption:

`
[mla_gallery category="general" mla_caption="{+caption+}<br>{+custom:File Size+}" meta_key="File Size" orderby="meta_value" order="DESC" numberposts=10]
`

= How can I use Categories, Tags and custom taxonomies to select images for display in my posts and pages? =

The powerful `[mla_gallery]` shortcode supports almost all of the query flexibility provided by the WP_Query class. You can find complete documentation in the Settings/Media Library Assistant Documentation tab. A simple example is in the preceding question. Here's an example that displays PDF documents with Att. Category "fauna" or Att. Tag "animal":

`
[mla_gallery post_mime_type="application/pdf" size=icon mla_caption="{+title+}" tax_query="array(array('taxonomy'=>'attachment_category','field'=>'slug','terms'=>'fauna'),array('taxonomy'=>'attachment_tag','field'=>'slug','terms'=>'animal'),'relation'=>'OR')"]
`

= Can I use [mla_gallery] for attachments other than images? =

Yes! The `[mla_gallery]` shortcode supports all MIME types when you add the post_mime_type parameter to your query. You can build a gallery of your PDF documents, plain text files and other attachments. You can mix images and other MIME types in the same gallery, too. Here's an example that displays a gallery of PDF documents, using the Google File Viewer to show the first page of each document as a thumbnail:

`
[mla_gallery post_mime_type=application/pdf post_parent=all link=file mla_viewer=true columns=1 orderby=date order=desc]
`

= Can I attach an image to more than one post or page? =

No; that's a structural limitation of the WordPress database. However, you can use Categories, Tags and custom taxonomies to organize your images and associate them with posts and pages in any way you like. The `[mla_gallery]` shortcode makes it easy. You can also use the `ids=` parameter to compose a gallery from a list of specific images.

= Can the Assistant use the standard WordPress post Categories and Tags? =

Yes! You can activate or deactivate support for Categories and Tags at any time by visiting the Media Library Assistant Settings page.

= Do I have to use the WordPress post Categories and Tags? =

No! The Assistant supplies pre-defined Att. Categories and Att. Tags; these are WordPress custom taxonomies, with all of the API support that implies. You can activate or deactivate the pre-defined taxonomies at any time by visiting the Media Library Assistant Settings page.

= Can I add my own custom taxonomies to the Assistant? =

Yes. Any custom taxonomy you register with the Attachment post type will appear in the Assistant UI. Use the Media Library Assistant Settings page to add support for your taxonomies to the Assistant UI.

= Can I use Jetpack Tiled Gallery or a lightbox plugin to display my gallery? =
You can use other gallery-generating shortcodes to give you the data selection power of [mla_gallery] and the formatting/display power of popular alternatives such as the WordPress.com Jetpack Carousel and Tiled Galleries modules. Any shortcode that accepts "ids=" or a similar parameter listing the attachment ID values for the gallery can be used. Here's an example of a Jetpack Tiled gallery for everything except vegetables:

`
[mla_gallery attachment_category=vegetable tax_operator="NOT IN" mla_alt_shortcode=gallery type="rectangular"]
`

Most lightbox plugins use HTML `class=` and/or `rel=` tags to activate their features. `[mla_gallery]` lets you add this tag information to your gallery output. Here's an example that opens PDF documents in a shadowbox using Easy Fancybox:

`
[mla_gallery post_mime_type=application/pdf post_parent=all link=file size=icon mla_caption='<a class="fancybox-iframe fancybox-pdf" href={+filelink_url+} target=_blank>{+title+}</a>' mla_link_attributes='class="fancybox-pdf fancybox-iframe"']
`

In the example, the `mla_caption=` parameter turns the document title into a link to the shadowbox display so you can click on the thumbnail image or the caption to activate the display.

= Why don't the "Posts" counts in the taxonomy edit screens match the search results when you click on them? =

This is a known WordPress problem with multiple support tickets already in Trac, e.g., 
Ticket #20708(closed defect (bug): duplicate) Wrong posts count in taxonomy table,
Ticket #14084(assigned defect (bug)) Custom taxonomy count includes draft & trashed posts,
and Ticket #14076(closed defect (bug): duplicate) Misleading post count on taxonomy screen.

For example, if you add Tags support to the Assistant and then assign tag values to your attachments, the "Posts" column in the "Tags" edit screen under the Posts admin section includes attachments in the count. If you click on the number in that column, only posts and pages are displayed. There are similar issues with custom post types and taxonomies (whether you use the Assistant or not). The "Attachments" column in the edit screens added by the Assistant shows the correct count because it works in a different way.

= How do I "unattach" an item? =

Hover over the item you want to modify and click the "Edit" or "Quick Edit" action. Set the ID portion of the Parent Info field to zero (0), then click "Update" to record your changes. If you change your mind, click "Cancel" to return to the main page without recording any changes. You can also click the "Select" button to bring up a list of posts//pages and select one to be the new parent for the item. The "Set Parent" link in the Media/Assistant submenu table also supports changing the parent and unattaching an item.

= The Media/Assistant submenu seems sluggish; is there anything I can do to make it faster? =

Some of the MLA features such as where-used reporting and ALT Text sorting/searching require a lot of database processing. If this is an issue for you, go to the Settings page and adjust the **"Where-used database access tuning"** settings. For any where-used category you can enable or disable processing. For the "Gallery in" and "MLA Gallery in" you can also choose to update the results on every page load or to cache the results for fifteen minutes between updates. The cache is also flushed automatically when posts, pages or attachments are inserted or updated.

= Are other language versions available? =

Not yet, but all of the internationalization work in the plugin source code has been completed and there is a Portable Object Template (.POT) available in the "/languages" directory. I don't have working knowledge of anything but English, but if you'd like to volunteer to produce a translation, I would be delighted to work with you to make it happen. Have a look at the "MLA Internationalization Guide.pdf" file in the languages directory and get in touch.

= What's in the "phpDocs" directory and do I need it? =

All of the MLA source code has been annotated with "DocBlocks", a special type of comment used by phpDocumentor to generate API documentation. If you'd like a deeper understanding of the code, click on "index.html" in the phpDocs directory and have a look. Note that these pages require JavaScript for much of their functionality.

== Screenshots ==

1. The Media/Assistant submenu table showing the available columns, including "Featured in", "Inserted in", "Att. Categories" and "Att. Tags"; also shows the Quick Edit area.
2. The Media/Assistant submenu table showing the Bulk Edit area with taxonomy Add, Remove and Replace options; also shows the tags suggestion popup.
3. A typical edit taxonomy page, showing the "Attachments" column.
4. The enhanced Edit page showing additional fields, categories and tags.
5. The Settings page General tab, where you can customize support of Att. Categories, Att. Tags and other taxonomies, where-used reporting and the default sort order.
6. The Settings page MLA Gallery tab, where you can add custom style and markup templates for `[mla_gallery]` shortcode output.
7. The Settings page IPTC &amp; EXIF Processing Options screen, where you can map image metadata to standard fields (e.g. caption), taxonomy terms and custom fields.
8. The Settings page Custom Field Processing Options screen, where you can map attachment metadata to custom fields for display in [mla_gallery] shortcodes and as sortable, searchable columns in the Media/Assistant submenu.
9. The Media Manager popup modal window showing additional filters for date and taxonomy terms. Also shows the enhanced Search Media box and the full-function taxonomy support in the ATTACHMENT DETAILS area.

== Changelog ==

= 2.15 =

* New: **A "Reset" button** has been added to the Media/Assistant Bulk Edit area and the Media/Add New (Upload New Media) Bulk Edit area. When clicked, the button clears all of the fields in the Bulk Edit area to simplify the entry of new values. 
* New: An option has been added to the "Media/Add New Enhancements" section of the Settings/Media Library Assistant General tab to allow **moving the Bulk Edit area** to the top of the Media/Add New (Upload New Media) screen, above the drag-and-drop area.
* New: When WPML is active, **MLA will fix a WPML defect that assigns the wrong term to flat taxonomies** if two or more translations have the same term name.
* New: Options have been added to the Settings/Media Library Assistant Debug tab that allow control over the PHP error_log file, PHP error_reporting level and MLA_DEBUG_LEVEL without modifying the `wp-config.php` file.
* New: The Media/Assistant "Search Media by ID/Parent" search has been extended to allow **multiple ID values**. If all the "keywords" in the Search Media box are numeric they will be matched against item IDs and item parent IDs in addition to the normal text-based search.
* New: The Media/Assistant Quick Edit area now displays the item's thumbnail/icon image to make it easier to confirm which item is being edited.
* New: A new `/media-library-assistant/examples/mla-upload-bulk-edit-example.php.txt` example plugin has been added to demonstrate updates to the Title field from the Bulk Edit area on the Media/Upload New Media submenu.
* New: The `/media-library-assistant/examples/mla-simple-mapping-hooks-example.php.txt` example plugin has been updated to demonstrate splitting an IPTC value with a regular expression (regex) and updating two custom fields with the results.
* New: The Portable Object (".po") files are now included in the `/media-library-assistant/languages` directory, facilitating the translation update process.
* New: Harm Kramer has contributed **updates and additions to the Dutch translation**; thanks Harm!
* New: Comments have been added to each "/tpls/help-for-*.tpl" template showing where in MLA the template is used. This should help with the translation of these templates to other languages.
* New: Two new hooks, `mla_taxonomy_get_columns` and `mla_taxonomy_column`, have been added to allow control of the Media/"Taxonomy Edit" submenu columns such as the "Attachments" column added by MLA.
* New: Two new hooks, `mla_update_single_item` and `mla_update_single_item`, have been added to increase control over Media Library item updates, e.g., for taxonomy term updates.
* New: The "mla_media_modal_initial_filters" and "mla_media_modal_settings" filters have been updated to allow control over the "uploaded" MIME type dropdown, e.g., on the "Create Gallery" and "Set Featured Image" views of the Media Manager Modal (popup) Window.
* New: The `/examples/mla-tax-query-example.php.txt` example plugin has been updated to support keyword search parameters.
* New: The `/examples/random-feature.php.txt` example plugin has been updated to allow for separate "post" and "item" taxonomies.
* Fix: For the `[mla_gallery]` shortcode, keyword searches only on `terms` generate an empty result when no terms match the search string.
* Fix: For the `[mla_gallery]` shortcode, keyword searches with special characters such as single and/or double quotes now work correctly.
* Fix: Style sheets and HTML markup changes have been added to support sites with Right-to-left (TRL) languages.
* Fix: For the Media/Assistant submenu table, WordPress 4.2+ alternate-row coloring (striping) for the Quick Edit and Bulk Edit areas has been fixed.
* Fix: For the Media/Assistant submenu table, combinations of ALT Text filtering in the Search Media box, sorting on custom field columns and Table Views based on custom field values now work correctly.
* Fix: The Edit Taxonomy "Attachments" column is displayed correctly when othe plugins, e.g., Polylang, add columns to the submenu table.
* Fix: When WPML or Polylang are active, flat taxonomy "auto-completion" suggestions for the Media/Assistant Quick Edit area and the Media/Edit Media screen are now language-specific.
* Fix: When WPML or Polylang are active, **Term Assignment and Synchronization defects** when two or more translations have the same term name are corrected.
* Fix: For item updates thet only affect postmeta and/or taxonomy values, `wp_update_post` is no longer called.
* Fix: Commas are now accepted within quoted arguments of the "Field-level option/format values". Commas outside of quoted arguments of the `timestamp` and `date` format values no longer generate PHP Warning messages.
* Fix: The Bulk Edit on Upload (Media/Add New) has been fixed for WordPress versions before 4.0.
* Fix: When WPML is active, a defect in Bulk Edit term assignment that copied terms from one item to subsequent items in the list has been corrected.
* Fix: The Settings/Media Library Assistant tab HTML heading tags have been adjusted to conform to the new WordPress v4.3+ standards.
* Fix: The Settings/Media Library Assistant tab titles and option content is loaded later in the startup process, allowing their translation to other languages.
* Fix: The Settings/Media Library Assistant "Donate" button is changed from an image to text, facilitating translation ro other languages.
* Fix: An array initialization defect that caused a PHP warning message in PHP Version 7 has been corrected.

= 2.10 - 2.14 =
* 2.14 - Final WordPress 4.3 updates. New Debug tab features. Updated Dutch translation. Four other fixes.
* 2.13 - WordPress 4.3 updates. PDF Thumbnail image generator. Wildcard keyword/term searching. Several WPML and Polylang fixes. Dutch and Swedish translations! Twelve other enhancements, twelve other fixes.
* 2.12 - Fixes a defect in [mla_gallery] handling of the mla_caption parameter. Adds mla_debug=log option.
* 2.11 - Enhanced WPML and new Polylang support. "Attached" Media/Assistant table view. Eight other enhancements, fifteen fixes.
*2.10 - mla_viewer is back, with a Featured Image option! XMP support for image meta data. Eight other enhancements, twelve fixes.


= 2.00 - 2.02 =
* 2.02 - Bulk Edit on Media/Add New, pause/restart IPTC/EXIF mapping, EXIF CAMERA fields, "timestamp", "date" and "fraction" format options. Six other enhancements, twelve fixes.
* 2.01 - Google File Viewer (mla_viewer) disabled. IPTC/EXIF mapping performance gains. Four other enhancements, five fixes.
* 2.00 - Requires WP v3.5+. Ajax-powered bulk edit and mapping, front-end "terms search" for [mla_gallery]. Five other enhancements, two fixes.

= 1.90 - 1.95 =
* 1.95: New [mla_gallery] parameters, Download rollover action, Media/Assistant submenu filters. Eleven enhancements, seven fixes.
* 1.94: Media Manager fixes and new "current-item" parameters for [mla_tag_cloud]. Two other enhancements, seven fixes.
* 1.93: WordPress 4.0 Media Grid enhancements (optional) and compatibility fixes. New auto-fill option for Media Manager taxonomy meta boxes. One other enhancement, three other fixes.
* 1.92: Three bug fixes, one serious.
* 1.91: WordPress 4.0 support! New "Edit Media meta box" and "Media Modal Initial Values" filters and example plugins. Four other enhancements, six fixes.
* 1.90: New "Terms Search" popup window and Search Media "Terms" checkbox. Post Type filter and pagination for "Select Parent" popup. Ten other enhancements, five fixes.

= 1.80 - 1.83 =
* 1.83: Corrects serious defect, restoring Quick Edit, Bulk Edit and Screen Options to Media/Assistant submenu. Three other fixes.
* 1.82: "Select Parent" popup window (Media/Edit Media, Attached to column, Quick Edit area), SVG support and several new filter examples. Five other enhancements, three other fixes.
* 1.81: Corrects serious defect in Media Manager Modal Window file uploading. Adds item-specific tag clouds. One other enhancement, five other fixes.
* 1.80: Full taxonomy meta box support in the Media Manager Modal Window. Checkbox-style meta box for flat taxonomies. Fourteen other enhancements, nine fixes.

= 1.70 - 1.71 =
* 1.71: Searchable Category meta boxes for the Media/Edit Media screen. Support for the WordPress "Attachment Display Settings". Six fixes.
* 1.70: Internationalization and localization support! Custom Field and IPTC/EXIF Mapping hooks. One other enhancement, six fixes.

= 1.60 - 1.61 =
* 1.61: Three fixes, including one significant fix for item-specific markup substitution parameters. Tested for compatibility with WP 3.8.
* 1.60: New [mla_tag_cloud] shortcode and shortcode-enabled MLA Text Widget. Five other enhancements, four fixes.

= 1.50 - 1.52 =
* 1.52: Corrected serious defect in [mla_gallery] that incorrectly limited the number of items returned for non-paginated galleries. One other fix.
* 1.51: Attachment Metadata mapping/updating, [mla_gallery] "apply_filters" hooks, multiple paginated galleries per page, "ALL_CUSTOM" pseudo value. Three other enhancements, six fixes.
* 1.50: PDF and GPS Metadata support. Content Templates; mix literal text with data values, test for empty values and choose among two or more alternatives for [mla_gallery] and data mapping. Four other enhancements, seven fixes.

= 1.40 - 1.43 =
* 1.43: Generalized pagination support with "mla_output=paginate_links". One other enhancement, four fixes.
* 1.42: Pagination support for [mla_gallery]! Improved CSS width (itemwidth) and margin handling. Eight other enhancements, six fixes.
* 1.41: New [mla_gallery] "previous link" and "next link" output for gallery navigation. New "request" substitution parameter to access $_REQUEST variables. Three other enhancements, seven fixes.
* 1.40: Better performance! New custom table views, Post MIME Type and Upload file/MIMEs control; 112 file type icons to choose from. Four new Gallery Display Content parameters. four other enhancements, twelve fixes.

= 1.00 - 1.30 =
* 1.30: New "mla_alt_shortcode" parameter combines [mla_gallery] with other gallery display shortcodes, e.g., Jetpack Carousel and Tiled Mosaic. Support for new 3.6 audio/video metadata. One other enhancement, eight fixes.
* 1.20: Media Manager (Add Media, etc.) enhancements: filter by more MIME types, date, taxonomy terms; enhanced search box for name/slug, ALT text, caption and attachment ID. New [mla_gallery] sort options. Four other enhancements, four fixes.
* 1.14: New [mla_gallery] mla_target and tax_operator parameters, tax_query cleanup and ids/include fix. Attachments column fix. IPTC/EXIF and Custom Field mapping fixes. Three other fixes.
* 1.13: Add custom fields to the quick and bulk edit areas; sort and search on them in the Media/Assistant submenu. Expanded EXIF data access, including COMPUTED values. Google File Viewer support, two other enhancements and two fixes.
* 1.11: Search by attachment ID, avoid fatal errors and other odd results when adding taxonomy terms. One other fix.
* 1.10: Map attachment metadata to custom fields; add them to [mla_gallery] display and as sortable columns on the Media/Assistant submenu table. Get Photonic Gallery (plugin) integration and six other fixes.
* 1.00: Map IPTC and EXIF metadata to standard fields, taxonomy terms and custom fields. Improved performance for where-used reporting. Specify default `[mla_gallery]` style and markup templates. Five other fixes.

= 0.11 - 0.90 =
* `[mla_gallery]` support for custom fields, taxonomy terms and IPTC/EXIF metadata. Updated for WordPress 3.5!
* Improved default Style template, `[mla_gallery]` parameters "mla_itemwidth" and "mla_margin" for control of gallery item spacing. Quick edit support of WordPress standard Categories taxonomy has been fixed.
* MLA Gallery Style and Markup Templates for control over CSS styles, HTML markup and data content of `[mla_gallery]` shortcode output. Eight other enhancements and four fixes.
* Removed (!) Warning displays for empty Gallery in and MLA Gallery in column entries.
* New "Gallery in" and "MLA Gallery in" where-used reporting to see where items are returned by the `[gallery]` and `[mla_gallery]` shortcodes. Two other enhancements and two fixes.
* Enhanced Search Media box. Extend search to the name/slug, ALT text and caption fields. Connect search terms with "and" or "or". Five other enhancements and two fixes.
* New `[mla_gallery]` shortcode, a superset of the `[gallery]` shortcode that provides many enhancements. These include taxonomy support and all post_mime_type values (not just images). Media Library items need not be "attached" to the post.
* SQL View (supporting ALT Text sorting) now created for automatic plugin upgrades
* Bulk Edit area; add, remove or replace taxonomy terms for several attachments at once. Sort your media listing on ALT Text, exclude revisions from where-used reporting.
* Support ALL taxonomies, including the standard Categories and Tags, your custom taxonomies and the Assistant's pre-defined Att. Categories and Att. Tags. Add taxonomy columns to the Assistant admin screen, filter on any taxonomy, assign terms and list the attachments for a term. 
* Quick Edit action for inline editing of attachment metadata
* Fixed "404 Not Found" errors when updating single items.

= 0.1 =
* Initial release.

== Upgrade Notice ==

= 2.15 =
Bulk Edit Reset button, Debug tab enhancements, Quick Edit thumbnails, new examples and hooks. Sixteen enhancements in all, sixteen fixes.

== Other Notes ==

In this section, scroll down to see highlights from the documentation, including new and unique plugin features

**NOTE:** Complete documentation is included in the Documentation tab on the Settings/Media Library Assistant admin screen and the drop-down "Help" content in the admin screens.

== Acknowledgements ==

Media Library Assistant includes many images drawn (with permission) from the [Crystal Project Icons](http://www.softicons.com/free-icons/system-icons/crystal-project-icons-by-everaldo-coelho), created by [Everaldo Coelho](http://www.everaldo.com), founder of [Yellowicon](http://www.yellowicon.com).

<strong>Many thanks</strong> to Aurovrata Venet and Il'ya Karastel for testing and advising on the new multilingual support features!

<h4>WPML &amp; Polylang Multilingual Support; the MLA Language Tab</h4>

Media Library Assistant provides integrates support for two popular "Multilanguage/ Multilingual/ Internationalization" plugins; [WPML](https://wpml.org/ "WPML - The WordPress Multilingual Plugin") and [Polylang](https://wordpress.org/plugins/polylang/ "Polylang - Making WordPress multilingual"). These plugins let you write posts and pages in multiple languages and make it easy for a visitor to select the language in which to view your site. MLA works with the plugins to make language-specific Media library items easy to create and manage.

MLA detects the presence of either plugin and automatically adds several features that work with them:

* <strong>Language-specific filtering</strong> of the <code>[mla_gallery]</code> and <code>[mla_tag_cloud]</code> shortcodes.
* <strong>Media/Assistant submenu table enhancements</strong> for displaying and managing item translations.
* <strong>Term Assignment and Term Synchronization</strong>, to match terms to language-specific items and automatically keep all translations for an item in synch.

<strong>Items, Translations and Terms</strong>

Each Media Library item can have one or more "translations". The item translations are linked and they use the same file in the Media Library. The linkage lets us know that "&iexcl;Hola Mundo!" (Spanish), "Bonjour Monde" (French) and "Hello world!" (English) are all translations of the same post/page. Post/page translation is optional; some posts/pages may not be defined for all languages. The language of the first translation entered for a post/page is noted as the "source language".

Taxonomy terms can also have one or more translations, which are also linked. The linkage lets us know that "Accesorio Categor&iacute;a" (Spanish), "Cat&eacute;gorie Attachement" (French) and "Attachment Category" (English) are all translations of the same term. Term translation is optional; some terms may not be defined for all languages. The language of the first translation entered for a term is noted as the "source language".

When an item is uploaded to the Media Library it is assigned to the current language (note: <strong>avoid uploading items when you are in "All Languages"/"Show all languages" mode</strong>; bad things happen). WPML provides an option to duplicate the new item in all active languages; Polylang does not. MLA makes it easy to add translations to additional languages with the Translations column on the Media/Assistant submenu table. For Polylang, MLA provides Quick Translate and Bulk Translate actions as well.

Assigning language-specific terms to items with multiple translations can be complex. MLA's <strong>Term Assignment</strong> logic assures that every term you assign on any of the editing screens (Media/Add New Bulk Edit, Media/Edit, Media/Assistant Quick Edit and Bulk Edit, Media Manager ATTACHMENT DETAILS pane) will be matched to the language of each item and translation. MLA's <strong>Term Synchronization</strong> logic ensures that changes made in one translation are replicated to all other translations that have an equivalent language-specific term.

<strong>Shortcode Support</strong>

The <code>[mla_gallery]</code> shortcode selects items using the WordPress <code>WP_Query</code> class. Both WPML and Polylang use the hooks provided by <code>WP_Query</code> to return items in the current language. If you use taxonomy parameters in your shortcode you must make sure that the term name, slug or other value is in the same language as the post/page in which it is embedded. This is easily done when the post/page content is translated from one language to another.

The <code>[mla_tag_cloud]</code> shortcode selects terms using the WordPress <code>wpdb</code> class. MLA adds language qualifiers to the database queries that compose the cloud so all terms displated are appropriate for the current language. No special coding or shortcode modification is required.

<strong>Media/Assistant submenu table</strong>

Two columns are added to the table when WPML or Polylang is active:

* <strong>Language</strong> - displays the language of the item. This column is only present when "All languages/Show all languages" is selected in the admin toolbar at the top of the screen.
* <strong>"Translations"</strong> - displays the translation status of the item in all active languages. The column header displays the flag icon for the language. The column content will have a checkmark icon for the item's language, a pencil icon for an existing translation or a plus icon for a translation that does not exist. You can click any icon to go directly to the Media/Edit Media screen for that translation. If you click a plus icon, a new translation will be created and initialized with content and terms from the current item and you will go to the Media/Edit Media screen for the new translation.

When Polylang is active, several additional features are available:

* <strong>A Language dropdown control</strong> is added to the Quick Edit and Bulk Edit areas. You can change the language of one or more items by selecting a new value in the dropdown and clicking Update. The new language must not have an exising translation; if a translation already exists the change will be ignored.
* <strong>Translation status links</strong> are added to the Quick Edit area, just below the Language dropdown control. If you click one of the pencil/plus translation status links, a new Quick Edit area will open for the translation you selected. A new translation is created if you click a plus status icon.
* <strong>A Quick Translate rollover action</strong> can be added to each item (the default option setting is "unchecked"). If you activate this option, when you click the "Quick Translate" rollover action for an item the Quick Translate area opens, showing the Language dropdown control and the translation status links. From there, click "Set Language" to change the language assigned to the item or click one of the pencil/plus translation status links. A new Quick Edit area will open for the translation you selected. A new translation is created if you click a plus status icon.
* <strong>A Translate action</strong> is added to the Bulk Actions dropdown control. If you click the box next to one or more items, select Translate in the Bulk Actions dropdown and click Apply, the Bulk Translate area will open. The center column contains a checkbox for each active language and an "All Languages" checkbox. Check the box(es) for the languages you want and then click "Bulk Translate". The Media/Assistant submenu table will be refreshed to display only the items you selected in the language(s) you selected. Existing translations will be displayed, and <strong>new translations will be created</strong> as needed so every item has a translation in every language selected.

<strong>Term Management</strong>

Taxonomy terms are language-specific, and making sure the right terms are assigned to all items and translations can be a challenge. Terms can change when an item is updated in any of four ways:

1. <strong>Individual edit</strong> - this is the full-screen Media/Edit Media submenu provided by WordPress. Taxonomies are displayed and updated in meta boxes along the right side of the screen. When "Update" is clicked whatever terms have been selected/entered are assigned to the item; they replace any old assignments.
1. <strong>Media Manager Modal Window</strong> – this is the popup window provided by WordPress' "Add Media" and "Select Featured Image" features. Taxonomies are displayed and updated in the ATTACHMENT DETAILS meta boxes along the right side of the window. Whatever terms are selected/entered here are assigned to the item; they replace any old assignments.
1. <strong>Quick Edit</strong> - this is a row-level action on the Media/Assistant screen. When "Update" is clicked whatever terms have been selected/entered are assigned to the item; they replace any old assignments.
1. <strong>Bulk edit</strong> - this is a bulk action on the Media/Assistant screen, and is also available on the Media/Upload New Media screen. In the Bulk Edit area, terms can be added or removed or all terms can be replaced. The bulk edit can be applied to multiple item translations in one or more languages.

When terms change in any of the above ways there are two tasks that require rules:

1. How should language-specific terms be assigned to items selected? This is "Term Assignment".1. How should terms assigned to one translation of an item be used to update other translations of the same item? This is "Term Synchronization".

<strong>Term Assignment</strong>

When a specific language is selected only the item translations for that language are shown, and only the terms for that language are displayed (except for a Polylang bug that shows all languages in the "auto-complete" list for flat taxonomies). When "All Languages"/"Show all languages" is selected the terms for all languages are displayed even if they cannot be assigned to an item. For example, a Spanish term may appear in the list be cannot be assigned to an English item translations.

For individual edit and quick edit updates the rule is simple:

1. For all terms selected/entered, find the equivalent term in the language of the item translation. Assign the equivalent (language-specific) term if one exists. If no equivalent term exists, ignore the selected/entered term. Assign all equivalent terms to the item translation, replacing any previous terms.

For bulk edit updates the rule depends on which action (add, remove, replace) has been selected. Each of the item translations in the bulk edit list is updated by these rules:

1. <strong>Add</strong>: For all terms selected/entered, find the equivalent term in the language of the item translation. Assign the equivalent (language-specific) term if one exists. If the equivalent term exists, add it to the item translation.
1. <strong>Remove</strong>: For all terms selected/entered, find the equivalent term in the language of the item translation. Assign the equivalent (language-specific) term if one exists. If the equivalent term exists, remove it from the item translation.
1. <strong>Replace</strong>: This is the tricky case. What should happen to terms already assigned to an item translation that have not been selected/entered for the update? In particular, what about terms that do not have translations to all languages? Should a "French-only" term be preserved?

The "<strong>Replace</strong>" answer is the same as the individual/quick edit answer. If the term is not selected/entered for the update it is discarded along with the other old assignments. After all, in "All Languages"/"Show all languages" mode the "French-only" term would have been in the list and could be selected if desired.

<strong>Term Synchronization</strong>

If you edit an item translation, for example to add or remove a term assignment, what should happen to the other translations of the same item? Term synchroniztion will add or remove the equivalent term in the other item translations if the equivalent term exists.

What about "untranslated" terms that do not have translations to all languages? Should an existing "French-only" (untranslated) term be preserved? It is, since there is no way to indicate that it should be removed.

Individual and quick edits are "replace" updates, and "replace" is an option for bulk edits as well. For term synchronization to preserve untranslated terms "replace" updates must be converted to separate "add" and "remove" updates that include only the changes made to the original item translation. For example, if these terms are defined:

English

- Common-term-1-eng
- Common-term-2-eng
- English-only-term

Spanish

- Common-term-1-esp
- Common-term-2-esp
- Spanish-only-term

And these term assignments exist:

English Translation

- Common-term-1-eng
- English-only-term

Spanish Translation

- Common-term-1-esp
- Spanish-only-term

Then synchronization handles common editing actions as follows:

1. If you edit the English Translation and add "Common-term-2-eng", synchronization will add "Common-term-2-esp" to the Spanish Translation.
1. If you edit the English Translation and remove "Common-term-1-eng", synchronization will remove "Common-term-1-esp" from the Spanish Translation.
1. If you edit the English Translation and remove "English-only-term", nothing will happen to the Spanish Translation.

<h4>Thumbnail Substitution Support, mla_viewer</h4>

<strong>NOTE: Google has discontinued the File Viewer support for thumbnail images.</strong> This solution supports dynamic thumbnail image generation for PDF and Postscript documents on your site's server. You can also assign a "Featured Image" to any Media Library item. For non-image items such as Microsoft Office documents the featured image will replace the MIME-type icon or document title in an <code>[mla_gallery]</code> display. Simply go to the Media/Edit Media screen, scroll down to the "Featured Image" meta box and select an image as you would for a post or page. 

The dynamic thumbnail image generation for PDF and Postscript documents uses the PHP <code>Imagick</code> class, which <strong>requires ImageMagick and Ghostscript</strong> to be installed on your server. If you need help installing them, look at this <a href="https://wordpress.org/support/topic/nothing-but-error-messages" title="Help with installation" target="_blank">PDF Thumbnails support topic</a>. If you don't have them on your server you can still use the Featured Image support to supply thumbnails for your non-image items. 

Ten <code>[mla_gallery]</code> parameters provide an easy way to simulate thumbnail images for the non-image file types.

* <strong>mla_viewer</strong> - must be "true" or "single" to enable thumbnail substitution. Use "true" unless you experience generation failures due to memory limitations on your server. Use "single" to generate one thumbnail at a time, which may be slower but requires less memory.
* <strong>mla_viewer_extensions</strong> - a comma-delimited list of the file extensions to be processed; the default is "ai,eps,pdf,ps" (do not include the dot (".") preceding the file extension). You may add or remove extensions (when support for additional types becomes available).
* <strong>mla_viewer_limit</strong> - the upper limit in megabytes (default none) on the size of the file to be processed. You can set this to avoid processing large documents if performance becomes an issue.
* <strong>mla_viewer_width</strong> - the maxinum width in pixels (default "150") of the thumbnail image. The height (unless also specified) will be adjusted to maintain the page proportions.
* <strong>mla_viewer_height</strong> - the maxinum width in pixels (default "0") of the thumbnail image. The width (unless also specified) will be adjusted to maintain the page proportions.
* <strong>mla_viewer_best_fit</strong> - retain page proportions (default "false") when both height and width are explicitly stated. If "false", the image will be stretched as required to exactly fit the height and width. If "true", the image will be reduced in size to fit within the bounds, but proportions will be preserved. For example, a typical page is 612 pixels wide and 792 pixels tall. If you set width and height to 300 and set best_fit to true, the thumbnail will be reduced to 231 pixels wide by 300 pixels tall.
* <strong>mla_viewer_page</strong> - the page number (default "1") to be used for the thumbnail image. If the page does not exist for a particular document the first page will be used instead.
* <strong>mla_viewer_resolution</strong> - the pixels/inch resolution (default 72) of the page before reduction. If you set this to a higher number, such as 300, you will improve thumbnail quality at the expense of additional processing time.
* <strong>mla_viewer_quality</strong> - the compression quality (default 90) of the final page. You can set this to a value between 1 and 100 to get smaller files at the expense of image quality; 1 is smallest/worst and 100 is largest/best. 
* <strong>mla_viewer_type</strong> - the MIME type (default image/jpeg) of the final thumbnail. You can, for example, set this to "image/png" to retain a transparent background instead of the white jpeg background.</td>

When this feature is active, gallery items for which WordPress can generate a thumbnail image are not altered. If WordPress generation fails, the "Featured Image" will be used, if one is specified for the item. If the item does not have a Featured Image, supported file/MIME types (PDF for now) will have a gallery thumbnail generated dynamically. If all else fails, the thumbnail is replaced by an "img" html tag whose "src" attribute contains a url reference to the appropriate icon for the file/MIME type.

Four options in the Settings/Media Library Assistant MLA Gallery tab allow control over mla_viewer operation:

* <strong>Enable thumbnail substitution</strong><br />
Check this option to allow the "mla_viewer" to generate thumbnail images for PDF documents. Thumbnails are generated dynamically, each time the item appears in an <code>[mla_gallery]</code> display.
* <strong>Enable Featured Images</strong><br />
Check this option to extend Featured Image support to all Media Library items. The Featured Image can be used as a thumbnail image for the item in an <code>[mla_gallery]</code> display.
* <strong>Enable explicit Ghostscript check</strong><br />
Check this option to enable the explicit check for Ghostscript support required for thumbnail generation. If your Ghostscript software is in a non-standard location, unchecking this option bypasses the check. Bad things can happen if Ghostscript is missing but ImageMagick is present, so leave this option checked unless you know it is safe to turn it off.
* <strong>Ghostscript path</strong><br />
If your Ghostscript software is in a non-standard location, enter the full path and name of the executable here. The value you enter will be used as-is and the search for Ghostscript in the usual locations will be bypassed.

<h3>Field-level Substitution Parameters</h3>

Field-level substitution parameters let you access query arguments, custom fields, taxonomy terms and attachment metadata for display in an MLA gallery or in an MLA tag cloud. You can also use them in IPTC/EXIF or Custom Field mapping rules. For field-level parameters, the value you code within the surrounding the ('[+' and '+]' or '{+' and '+}') delimiters has three parts; the prefix, the field name (or template content) and, if desired, an option/format value.

<h4>Prefix values</h4>

There are ten prefix values for field-level parameters.

* <strong>request</strong> - The parameters defined in the <code>$_REQUEST</code> array; the "query strings" sent from the browser.
* <strong>query</strong> - The parameters defined in the <code>[mla_gallery]</code> shortcode.
* <strong>custom</strong> - WordPress Custom Fields, which you can define and populate on the Edit Media screen or map from various sources on the Settings/Media Library Assistant Custom and IPTC/EXIF tabs.
* <strong>terms</strong> - WordPress Category, tag or custom taxonomy terms.
* <strong>meta</strong> - WordPress attachment metadata, if any, embedded in the image/audio/video file.
* <strong>pdf</strong> - The Document Information Dictionary (D.I.D.)and XMP metadata, if any, embedded in a PDF file.
* <strong>iptc</strong> - The IPTC (International Press Telecommunications Council) metadata, if any, embedded in the image file.
* <strong>exif</strong> - The EXIF (EXchangeable Image File) metadata, if any, embedded in a JPEG DCT or TIFF Rev 6.0 image file.
* <strong>xmp</strong> -  Data defined by the Extensible Metadata Platform (XMP) framework, if present. XMP metadata varies from image to image but is often extensive.
* <strong>template</strong> - A Content Template, which lets you compose a value from multiple substitution parameters and test for empty values, choosing among two or more alternatives or suppressing output entirely.

<h4>Field-level option/format values</h4>

You can use a field-level option or format value to specify the treatment of fields with multiple values or to change the format of a field for display/mapping purposes.

Two "option" values change the treatment of fields with multiple values:

* <strong>,single</strong> - If this option is present, only the first value of the field will be returned..
* <strong>,export</strong> - If this option is present, the PHP <code>var_export</code> function is used to return a string representation of all the elements in an array field.

Seven "format" values help you reformat fields or encode them for use in HTML attributes and tags:

* <strong>,raw</strong> - If you want to avoid filtering a value through the WordPress <code>sanitize_text_field()</code> function you can add the ",raw" option.
* <strong>,commas</strong> - For numeric data source parameters such as "file_size" you can add the ",commas" option to format the value for display purposes.
* <strong>,attr</strong> - If you use a substitution parameter in an HTML attribute such as the <code>title</code> attribute of a hyperlink (<code>a</code>) or <code>img</code> tag you can add the ",attr" option to encode the <, >, &, " and ' (less than, greater than, ampersand, double quote and single quote) characters.
* <strong>,url</strong> - If you use a substitution parameter in an HTML <code>href</code> attribute such as a hyperlink (<code>a</code>) or <code>img</code> tag you can add the ",url" option to convert special characters such as quotes, spaces and ampersands to their URL-encoded equivalents.
* <strong>,fraction(f,s)</strong> - Many of the EXIF metadata fields are expressed as "rational" quantities, i.e., separate numerator and denominator values separated by a slash. The "fraction" format converts these to a more useful format.
* <strong>,timestamp(f)</strong> - Many date and time values are stored as a UNIX timestamp. The ",timestamp" format converts a timestamp into a variety of date and/or time string formats, using the PHP date() function.
* <strong>,date(f)</strong> - Many EXIF date and time values such as DateTimeOriginal and DateTimeDigitized are stored as strings with a format of "YYYY:MM:DD HH:MM:SS". You can format these values by using the ",date" format.
