<!-- loaded in /mla-copy-item-example.php function mla_list_table_help_template for the Media/Assistant submenu screen -->
<!-- invoked as /wp-admin/upload.php?page=mla-menu -->
<!-- template="mla-copy-item-example" -->
<!-- title="Copy Item(s)" order="97" -->
<p>The <strong>MLA Copy Item Example</strong> plugin lets you copy one or more Media Lbrary items. The "Copy" Bulk Action makes it easy to create new items as if you had uploaded the attached file(s) again.</p>
<p>You can use the following options to control the thumbnail generation process:</p>
<table>
<tr>
<td class="mla-doc-table-label">Map Custom Field metadata</td>
<td valign="top">run the Custom Fields metadata mapping rules for the new item.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Map IPTC/EXIF metadata</td>
<td valign="top">run the IPTC/EXIF metadata mapping rules (Standard Fields, Taxonomy Terms and Custom Fields) for the new item.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Copy Taxonomy Terms</td>
<td valign="top">copy all of the source item&rsquo;s assigned terms to the new item.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Copy Custom Fields</td>
<td valign="top">copy all of the source item&rsquo;s custom field values to the new item.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Copy Item Fields</td>
<td valign="top">copy the Title, Caption, Description, ALT Text, Parent ID and Menu Order values from the source item to the new item.</td>
</tr>
</table>
<p>It is important to understand the order in which the copying and applying the options are done:</p>
<ol>
<li>A temporary copy of the source item&rsquo;s attached file is created.</li>
<li>The temporary copy is processed by WordPress as if it had been uploaded via the Media/Add New submenu. If you selected the "Copy Item Values" option, the Title, Caption, Description, Parent ID and Menu Order values are copied to the new item.</li>
<li>The Custom Field and/or IPTC/EXIF metadata mapping rules (if selected) are processed. Depending on how the rules are coded they may replace Standard Field values from the previous step.</li>
<li>If you selected the "Copy Item Values" option, the ALT Text value is copied to the new item.</li>
<li>If you selected the "Copy Custom Fields" option, values are copied from the source item to the new item.</li>
<li>If you selected the "Copy Taxonomy Terms" option, terms are copied from the source item to the new item.</li>
</ol>
<p>For most copy actions it is best to leave the option values unaltered. In particular, if you select the "Copy" options in addition to the mapping options be sure you understand how they may overlap.</p>
<p>After you click Copy, the Media/Assistant submenu table will be refreshed to display all the new items added to the Media Library. You can use Quick Edit and Bulk Edit to make additional changes to the new items.</p>
