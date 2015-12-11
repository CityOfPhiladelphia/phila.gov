<!-- loaded in class-mla-main.php function mla_add_help_tab for the Media/Assistant submenu screen -->
<!-- invoked as /wp-admin/upload.php?page=mla-menu -->
<!-- template="mla-thumbnail-generation" -->
<!-- title="Thumbnail Generation" order="95" -->
<p>Media Library Assistant lets you assign a "Featured Image" to yourMedia Library items. For non-image items such as PDF documents this image can be used as the <code>mla_viewer</code> thumbnail image, avoiding the overhead of generating the image each time the gallery is composed. The "Thumbnail" Bulk Action makes it easy to generate thumbnail images and assign them to their corresponding non-image Media Library items.</p>
<p>You can use the following fields to control the thumbnail generation process:</p>
<table>
<tr>
<td class="mla-doc-table-label">Width</td>
<td>the maximum width in pixels (default "150") of the thumbnail image. The height (unless also specified) will be adjusted to maintain the page proportions.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Height</td>
<td>the maximum width in pixels (default "0") of the thumbnail image. The width (unless also specified) will be adjusted to maintain the page proportions.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Best Fit</td>
<td>retain page proportions when both height and width are explicitly stated. If unchecked, the image will be stretched as required to exactly fit the height and width. If checked, the image will be reduced in size to fit within the bounds, but proportions will be preserved. For example, a typical page is 612 pixels wide and 792 pixels tall. If you set width and height to 300 and set best fit to true, the thumbnail will be reduced to 231 pixels wide by 300 pixels tall.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Page</td>
<td>the page number (default "1") to be used for the thumbnail image. If the page does not exist for a particular document the first page will be used instead.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Resolution</td>
<td>the pixels/inch resolution (default 72) of the page before reduction. If you set this to a higher number, such as 300, you will improve thumbnail quality at the expense of additional processing time.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Quality</td>
<td>the compression quality (default 90) of the final page. You can set this to a value between 1 and 100 to get smaller files at the expense of image quality; 1 is smallest/worst and 100 is largest/best.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Type</td>
<td>the MIME type, "JPG" (image/jpeg, default) or "PNG" (image/png), of the final thumbnail. You can, for example, set this to "PNG" to retain a transparent background instead of the white jpeg background.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Existing Items</td>
<td>the action to take if an item already has a thumbnail. Select "<strong>Keep</strong>" to retain the thumbnail and not generate anything. Select "<strong>Ignore</strong>" to generate and assign a new thumbnail, leaving the old item unchanged. Select "<strong>Trash</strong>" to generate and assign a new thumbnail, moving the old item to the Media Trash (if defined) or deleting it. Select "<strong>Delete</strong>" to generate and assign a new thumbnail, permanently deleting the old item.
</td>
</tr>
<tr>
<td class="mla-doc-table-label">Suffix</td>
<td>the suffix added to the source item's Title to create the thumbnail's Title.</td>
</tr>
<tr>
<td class="mla-doc-table-label">Clear Filter-by</td>
<td>remove the "Filter-by" criteria when refreshing the display. Leaving criteria such as year/month or Search Media values in place can prevent the display of the new, generated items.</td>
</tr>
</table>
<p>After you click Generate Thumbnails, the Media/Assistant submenu table will be refreshed to display all the new items generated and added to the Media Library. You can use Quick Edit and Bulk Edit to make additional changes to the new items.</p>
<!-- template="sidebar" -->
<p><a href="[+settingsURL+]?page=mla-settings-menu-documentation&mla_tab=documentation#thumbnail_substitution" target="_blank">MLA Documentation for Thumbnail Substitution Support, mla_viewer</a></p>
