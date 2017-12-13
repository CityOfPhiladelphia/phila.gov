<!-- loaded in class-mla-settings.php function mla_add_help_tab_action for the Settings/Media Library Assistant submenu Custom Fields tab -->
<!-- invoked as /wp-admin/options-general.php?page=mla-settings-menu-upload&mla_tab=custom_field -->
<!-- template="mla-overview" -->
<!-- title="Overview" order="10" -->
<p>The Custom Fields tab lets you manage the rules used to map metadata values to custom fields and to add custom fields to the Media/Assistant admin submenu including its Bulk Edit and Quick Edit areas.</p>
<p>All the existing rules are listed in the table on the right, ordered by the custom field name. You can change the sort order by clicking on one of the blue column names.</p>
<p>You can use the Screen Options tab to customize the display of this screen. You can choose any combination of the columns available for display. You can also choose how many rules appear on each page of the display.</p>
<p>The table can be filtered by clicking one of the "views" listed above the Bulk Actions selector. You can select All rules, a visibility category or the "Read Only" rules that may be left over from an old MLA bug.</p>
<p>You can also narrow the list by entering a keyword or phrase in the text box in the upper-right corner and clicking "Search Rules". <strong>NOTE:</strong> The "Search Rules" filter is &#8220;sticky&#8221;, i.e., it will persist as you resort the display, edit rules, etc. To clear it, delete the text and click "Search Rules" or simply click on the "Custom Fields" tab.</p>
<!-- template="mla-enable-custom-mapping" -->
<!-- title="Enable custom field mapping" order="20" -->
<p>Three checkbox options control the custom field mapping when new items are added to the Media Library:
<ul>
<li><strong>Enable custom field mapping</strong> - Check this option to enable the mapping rules and display the "Map" buttons on the Media/Edit Media and Media/Assistant Bulk Edit screens.</li>
<li><strong>Enable custom field mapping when adding new media</strong> - Check this option to enable mapping when uploading new items (attachments) to the Media Library.</li>
<li><strong>Enable custom field mapping when updating media metadata</strong> - Check this option to enable mapping when item (attachment) metadata is regenerated,
 e.g., when the Media/Edit Media "Edit Image" functions are used.</li>
</ul>
The "when adding" and "when updating" options do <strong>NOT</strong> affect the operation of the "Map" buttons on the bulk edit or single edit screens, nor do they affect any of the "Execute" mapping functions On this Settings screen.</p>
<p>Check one or more options to enable these features, then click the "Save Changes" button to record your new setting(s).</p>
<!-- template="mla-execute-rules" -->
<!-- title="Execute Rule(s)" order="30" -->
<p>In this tab there are three ways to execute one or more custom field mapping rules for <strong>ALL</strong> of your Media Library items:
<ul>
<li><strong>Execute All Rules button</strong> - just below the "Enable" checkbox controls in the upper-left portion of the tab. Click this button to immediately run <strong>ALL</strong> of the active rules. Rules marked as inactive will not be executed.</li>
<li><strong>Bulk Actions "Execute"</strong> - Runs the rules you select by checking the box to the left of one or more rule names. Pull down the "Bulk Actions" control and select "Execute", then click the "Apply" button. <strong>Inactive rules will be executed</strong>; do not select them unless you want to execute them!</li>
<li><strong>"Execute" rollover action</strong> - Runs the single rule you select by clicking the rule's "Execute" rollover action. <strong>Inactive rules will be executed</strong>.</li>
</ul>
These commands process your items in "chunks" to prevent timeout errors. You can pause/resume or cancel the operation between chunks. Note that rules with a Data Source of "none" are ignored because they can't change the custom field value.</p>
<p>There are two other ways you can perform custom field mapping for one or more existing attachments: 
<ul>
<li><strong>Edit Media screen</strong> - You can click the "Map Custom Field metadata" link in the "Image Metadata" postbox to apply the existing mapping rules to a single attachment.</li>
<li><strong>Bulk Action edit area</strong> - To perform mapping for a group of attachments you can use the Bulk Action facility on the Media/Assistant screen.</li>
</ul>
</p>
<!-- template="mla-bulk-actions" -->
<!-- title="Bulk Actions" order="40" -->
<p>The &#8220;Bulk Actions&#8221; dropdown list works with the check box column to let you make changes to many rules at once. Click the check box in the column title row to select all rules on the page, or click the check box in a row to select rules individually.</p>
<p>Once you&#8217;ve selected the rules you want, pick an action from the dropdown list and click Apply to perform the action on the selected rules:
<ul>
<li><strong>Edit</strong> - changes one or more rule parameters for all selected rules at once. To remove an rule from the grouping, just click the x next to its name in the left column of the Bulk Edit area.</li>
<li><strong>Delete Permanently</strong> - deletes the rules you have selected. There is no "trash" area or "undo" feature, so proceed with caution. This action does <strong>NOT</strong> delete any custom field values associated with your items; see "Purge Values" below for that.</li>
<li><strong>Execute</strong> - runs the rules for all items. See the "Execute Rule(s)" section of this Help menu.</li>
<li><strong>Purge Values</strong> - deletes <strong>ALL</strong> of the values associated with your items for the custom field named in the rule(s). There is no "trash" area or "undo" feature, so proceed with caution. This action does <strong>NOT</strong> delete the mapping rule itself; see "Delete Permanently" above for that.</li>
</ul>
</p>
<!-- template="mla-rollover-actions" -->
<!-- title="Rollover Actions" order="50" -->
<p>Hovering over a row in the Name column reveals action links that apply to that specific rule:
<ul>
<li><strong>Edit</strong> - displays a simple screen to edit that individual rule&#8217;s elements. You can also change the custom field to which the rule applies on this screen.</li>
<li><strong>Quick Edit</strong> - displays an inline form to edit the rule's elements without leaving the menu screen.</li>
<li><strong>Execute</strong> - runs the rule for all items. See the "Execute Rule(s)" section of this Help menu.</li>
<li><strong>Purge Values</strong> - deletes <strong>ALL</strong> of the values associated with your items for the custom field named in the rule.  This action does <strong>NOT</strong> delete the mapping rule itself.</li>
<li><strong>Delete Permanently</strong> - deletes the rule. This action does <strong>NOT</strong> delete any custom field values associated with your items.</li>
</ul>
There is no "trash" area or "undo" feature, so use the "Purge Values" and "Delete Permanently" actions with caution.</p>
<!-- template="mla-add-new" -->
<!-- title="Add New Rule" order="60" -->
<p>The left-hand side of the screen contains all the fields you need to define a new custom field rule. Name is required, and must not have a rule already defined for it; the other fields are not or have default values. There is more information about each field in the text under the value area.</p>
<p>You can pick from a list of the custom fields already associated with one or more items. If you want to define a new custom field, click "Enter new field" and type the new field's name in the text box.</p>
<p><strong>NOTE:</strong> To save your work and add the rule, you must scroll down to the bottom of the form and click "Add Rule".</p>
<p>You can find complete information on each of the custom field mapping rule fields in the "The custom field rule elements" portion of the "Documentation on Custom Field Mapping Rules" section of the Documentation tab (see the "For more information" link in the sidebar at the right).</p>
<!-- template="mla-read-only" -->
<!-- title="Read Only Rules" order="70" -->
<p>Long ago in an MLA version far away there was a bug that caused some custom field mapping rule names to be mis-interpreted. This added multiple rules for the same custom field to the table.</p>
<p>If you have any of these damaged rules they are detected and marked "Read Only" in the submenu table. You can delete them, or edit them and change the name to some other custom field. Have a look at any other rules with a similar name and decide which one you want to keep.</p>
<!-- template="sidebar" -->
<p><strong>For more information:</strong></p>
<p><a href="[+settingsURL+]?page=mla-settings-menu-documentation&mla_tab=documentation#mla_custom_field_mapping" target="_blank">Documentation on Custom Field Mapping Rules</a></p>
<p><a href="[+settingsURL+]?page=mla-settings-menu-documentation&mla_tab=documentation#attachment_metadata_mapping" target="_blank">Documentation on updating WordPress "attachment metadata"</a></p>
<p><a href="http://wordpress.org/support/plugin/media-library-assistant" target="_blank">MLA Support Forum</a></p>
