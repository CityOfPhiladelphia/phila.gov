<!-- loaded in class-mla-settings.php function mla_add_help_tab_action for the Settings/Media Library Assistant submenu Shortcodes tab -->
<!-- invoked as /wp-admin/options-general.php?page=mla-settings-menu-shortcodes&mla_tab=shortcodes -->
<!-- template="mla-overview" -->
<!-- title="Overview" order="10" -->
<p>The Shortcdodes tab lets you view the default style and markup templates used by the <code>[mla_gallery]</code>, <code>[mla_tag_cloud]</code> and <code>[mla_term_list]</code> shortcodes. You can also define and edit custom templates for your application.</p>
<p>All the templates are listed in the table on the right, ordered by the Name field. You can change the sort order by clicking on one of the blue column names.</p>
<p>You can use the Screen Options tab to customize the display of this screen. You can choose any combination of the columns available for display. You can also choose how many items appear on each page of the display.</p>
<p>You can narrow the list by entering a keyword or phrase in the text box in the upper-right corner and clicking "Search Templates". <strong>NOTE:</strong> The "Search Templates" filter is &#8220;sticky&#8221;, i.e., it will persist as you resort the display, edit items, etc. To clear it, delete the text and click "Search Views" or simply click on the "Shortcodes" tab.</p>
<!-- template="mla-bulk-actions" -->
<!-- title="Bulk Actions" order="20" -->
<p>The &#8220;Bulk Actions&#8221; dropdown list works with the check box column to let you make changes to many items at once. Click the check box in the column title row to select all items on the page, or click the check box in a row to select items individually.</p>
<p>Once you&#8217;ve selected the items you want, pick an action from the dropdown list and click Apply to perform the action on the selected items.</p>
<p>When using Copy, the template(s) you select are copied to new templates and a unique name is generated for them by adding "- copy" to the original name. You can edit the new name(s) as needed by editing the template copies</p>
<p>Clicking Delete Permanently will delete the item from the Shortcodes list and from the database, <strong>even if it is used in your shortcodes</strong>. There is no undo action, so be sure you really want to delete the template. You can always make a backup copy of your options (on the General tab, near the bottom) before you proceed.</p>
<!-- template="mla-available-actions" -->
<!-- title="Available Actions" order="30" -->
<p>Hovering over a row in the Name column reveals action links such as View, Edit, Copy and Delete Permanently.
<p>Clicking View (for a default template) displays a simple screen to view that individual item&#8217;s name and sections. At the bottom are buttons to close the display or to make a copy of the template.</p>
<p>Clicking Edit (for a custom template) displays a simple screen to edit that individual item&#8217;s name and sections.</p>
<p>When using Copy, the template(s) you select are copied to new templates and a unique name is generated for them by adding "- copy" to the original name. You can edit the new name(s) as needed by editing the template copies</p>
<p>Clicking Delete Permanently will delete the item from the Shortcodes list and from the database, <strong>even if it is used in your shortcodes</strong>. There is no undo action, so be sure you really want to delete the template. You can always make a backup copy of your options (on the General tab, near the bottom) before you proceed.</p>
<!-- template="mla-add-new" -->
<!-- title="Add New Template" order="40" -->
<p>Click the "Add New Template" button at the top left of the template table to add a new custom style or markup template. A separate screen will be displayed with dropdown controls to set the template type and the shortcode to which it applies. Templates are shortcode-specific because the sections and substitution parameters are different for each shortode.</p>
<p>Once you have specified type and shortcode, text areas for each section in the template will be displayed to complete the template. You must also give the template a unique name (you can use the same name for a style template and a markup template). The name can only contain lowercase letters, digits and dashes ("-"). It will be sanitized and made unique before the template is stored.
<p><strong>NOTE:</strong> To save your work and add the template, you must click the "Update" button at the bottom of the screen.</p>
<!-- template="mla-template-settings" -->
<!-- title="[mla_gallery] Settings" order="50" -->
<p>You cannot edit the default shortcode templates, but you can change the templates used for <code>[mla_gallery]</code>shortcodes that have no <code>mla_style</code> or <code>mla_markup</code> parameters. Make your selections in the dropdown controls and then click "Save Changes" at the bottom of the screen to record your new settings.</p>
<p>The <code>mla_margin</code> and <code>mla_itemwidth</code> parameters are used to improve the formatting of the gallery display. The default <code>mla_margin</code> adds a bit of space around the gallery thumbnails. The default <code>mla_itemwidth</code> divides the total gallery width by the number of columns and subtracts the margin to give a percentage width value for each thumbnail. You can change these defaults or remove the parameters entirely.
<!-- template="mla-viewer-settings" -->
<!-- title="MLA Viewer Settings" order="60" -->'
<p>The "MLA Viewer" supports dynamic thumbnail image generation for PDF and Postscript documents on your site&#8217;s server. You can also assign a "Featured Image" to any Media Library item. This feature requires Ghostscript and Imagick/ImageMagick on your server. There will be a warning message below the Enable thumbnail substitution checkbox if either of them are missing.</p>
<p>If you have Ghostscript and Imagick/ImageMagick on your server, check the Enable Featured Image Generation and use them to generate images from document content. To generate the images, use the Media/Assistant "Thumbnail" bulk action. The generated images are Media Library items and can be used just like any other image in the library.</p>
<p>You can use the Enable Featured Images with or without dynamic thumbnail image generation. Check this box to allow you to assign a Featured Image to any media item. It will be used for a thumbnail in the gallery display.</p>

<!-- template="sidebar" -->
<p><strong>For more information:</strong></p>
<p><a href="[+settingsURL+]?page=mla-settings-menu-documentation&mla_tab=documentation#mla_gallery_templates" target="_blank">MLA Documentation on Style and Markup Templates</a></p>
<p><a href="[+settingsURL+]?page=mla-settings-menu-documentation&mla_tab=documentation#thumbnail_substitution" target="_blank">MLA Documentation on Thumbnail Substitution &amp; mla_viewer</a></p>
<p><a href="http://wordpress.org/support/plugin/media-library-assistant" target="_blank">MLA Support Forum</a></p>
