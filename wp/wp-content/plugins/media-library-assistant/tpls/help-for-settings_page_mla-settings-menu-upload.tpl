<!-- loaded in class-mla-settings.php function mla_add_help_tab_action for the Settings/Media Library Assistant submenu Uploads tab -->
<!-- invoked as /wp-admin/options-general.php?page=mla-settings-menu-upload&mla_tab=upload -->
<!-- template="mla-overview" -->
<!-- title="Overview" order="10" -->
<p>The Uploads tab lets you manage the list of "Upload MIME Type" entries used to validate file formats uploaded to the Media Library and to assign MIME types based on file extensions.</p>
<p>All the Upload MIME Types are listed in the table on the right, ordered by the file extension field. You can change the sort order by clicking on one of the blue column names.</p>
<p>You can use the Screen Options tab to customize the display of this screen. You can choose any combination of the columns available for display. You can also choose how many items appear on each page of the display.</p>
<p>The table can be filtered by clicking one of the "views" listed above the Bulk Actions selector. You can select All items, Active or Inactive items or the source (WordPress, MLA or Custom) of the item.</p>
<p>You can also narrow the list by entering a keyword or phrase in the text box in the upper-right corner and clicking "Search Uploads". <strong>NOTE:</strong> The "Search Uploads" filter is &#8220;sticky&#8221;, i.e., it will persist as you resort the display, edit items, etc. To clear it, delete the text and click "Search Uploads" or simply click on the "Uploads" tab.</p>
<!-- template="mla-icon-types" -->
<!-- title="Icons and Icon Types" order="20" -->
<p>WordPress maintains a list of "file types" which associate file extensions with type names used to select an icon image. For example, an "audio" file type is associated with an image of musical notes. There are nine of these types: archive, audio, code, default, document, interactive, spreadsheet, text and video. MLA has a much longer list; 112 icon types/images in all. If the "Enable MLA File Type Icons Support" checkbox at the bottom of the screen is checked, the enhanced icon images will be used in place of the WordPress images.</p>
<p>You can change the icon image associated with any file extension by selecting a new value from the dropdown list on the Edit Upload MIME Type screen or in the Quick Edit area. You can change the icon image for several extensions at once using the Bulk Edit action.</p>
<p>If you have some other plugin or mechanism for handling the Upload MIME Type items, you can disable MLA support entirely. Clear the checkbox at the bottom-left corner of the screen and click "Save Changes".</p>
<!-- template="mla-source-status" -->
<!-- title="Source and Status" order="30" -->
<p>The "Source" of an Upload MIME Type reveals where the extension/MIME Type association comes from:</p>
<ul>
<li><strong>core</strong>: WordPress defines a core set of extensions and associated MIME types, and this list changes with new WordPress releases. These are the "official" items. You can't delete them, but you can inactivate them so they are not used to validate file uploads.</li>
<li><strong>mla</strong>: Media Library Assistant adds several more extension/type items, drawing from the most popular items found in other plugins and web sites. They are initialized as "inactive" items, so you must explicitly decide to activate them for use in file upload validation.</li>
<li><strong>custom</strong>: Defined by some other plugin or code, or by manual action. When MLA first builds its list it will automatically add anything it finds in your current list as a new, active custom item. After that, you can use MLA to manage them.</li>
</ul>
<p>The "Status" of an item determines whether it is used by WordPress to validate file uploads and assign MIME types to attachments in your Media Library. Only "active" items are used in this way; making an item "inactive" will prevent further uploads with that extension but will NOT affect any attachments already in your Media Library.</p>
<!-- template="mla-bulk-actions" -->
<!-- title="Bulk Actions" order="40" -->
<p>The &#8220;Bulk Actions&#8221; dropdown list works with the check box column to let you make changes to many items at once. Click the check box in the column title row to select all items on the page, or click the check box in a row to select items individually.</p>
<p>Once you&#8217;ve selected the items you want, pick an action from the dropdown list and click Apply to perform the action on the selected items.</p>
<p>When using Bulk Edit, you can change the Active/Inactive status for all selected items at once. To remove an item from the grouping, just click the x next to its name in the left column of the Bulk Edit area.</p>
<p>The "Delete/Revert Custom" bulk action will only affect items with a "custom" source. It will delete items for which there is no standard source or it will replace the custom information with the standard information for items with a standard source.</p>
<!-- template="mla-available-actions" -->
<!-- title="Available Actions" order="50" -->
<p>Hovering over a row in the Extension column reveals action links such as Edit, Quick Edit, Revert to Standard and Delete Permanently. Clicking Edit displays a simple screen to edit that individual item&#8217;s metadata. Clicking Quick Edit displays an inline form to edit the item's metadata without leaving the menu screen.</p>
<p>If the current item source is "custom", one of two choices will appear. If the item has a standard source (core or mla), clicking Revert to Standard will replace the custom information with the corresponding standard source information. If the item does <strong>NOT</strong> have a standard source, clicking Delete Permanently will delete the custom item from the Uploads list.</p>
<!-- template="mla-add-new" -->
<!-- title="Add New Type" order="60" -->
<p>The left-hand side of the screen contains all the fields you need to define a new item for the list. Extension and MIME Type are required; the other fields are not or have default values. There is more information about each field in the text under the value area.</p>
<p><strong>NOTE:</strong> To save your work and add the item, you must scroll down to the bottom of the screen and click "Add Upload MIME Type".</p>
<!-- template="mla-search" -->
<!-- title="Searching Known Types" order="70" -->
<p>You can search a list of over 1,500 known file extension to MIME type associations compiled from several Internet sources. The list shows alternative MIME types for the core and mla items as well as many other file extensions and MIME types you can add as custom items. Click the "Search Known Types" button at the bottom of the form.</p>
<!-- template="mla-save-changes" -->
<!-- title="Disable/Enable Uploads" order="80" -->
<p>If you have some other plugin or mechanism for handling the Upload MIME Type items, you can disable MLA support entirely. Clear the checkbox at the bottom-left corner of the screen and click "Save Changes". The Uploads table will be replaced by a "disabled" screen and a checkbox that lets you turn MLA support back on when you want it.</p>
<p><strong>NOTE:</strong> This option does <em><strong>NOT</strong></em> enable or disable uploading files; it simply turns off MLA support for managing the list of extensions and MIME types.</p>
<!-- template="sidebar" -->
<p><strong>For more information:</strong></p>
<p><a href="[+settingsURL+]?page=mla-settings-menu-documentation&mla_tab=documentation#mla_uploads" target="_blank">MLA Documentation on Upload MIME Types</a></p>
<p><a href="http://wordpress.org/support/plugin/media-library-assistant" target="_blank">MLA Support Forum</a></p>
