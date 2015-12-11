<!-- template="page" -->
<form>
  <table width="99%" style="display: none">
    <tbody id="mla-thumbnail-generation">
      <tr id="mla-bulk-thumbnail" class="inline-edit-row inline-edit-row-attachment inline-edit-attachment bulk-thumbnail-row bulk-thumbnail-row-attachment bulk-thumbnail-attachment" style="display: none">
        <td colspan="[+colspan+]" class="colspanchange">
          <fieldset class="inline-edit-col-left">
            <div class="inline-edit-col">
              <h4>[+Generate Thumbnails+]</h4>
              <div id="mla-thumbnail-title-div">
                <div id="mla-thumbnail-titles"></div>
              </div>
            </div>
          </fieldset>
          <fieldset id="mla-thumbnail-settings" class="inline-edit-col-right">
            <div class="inline-edit-col">
            <h4>([+See Documentation+])</h4>
            <div class="inline-edit-group clear">
			<table><tr>
              <td><label> <span class="title">[+Width+]</span>
                <input name="mla_thumbnail_options[width]" id="mla-thumbnail-options-width" type="text" size="5" value="" />
              </label></td>
              <td><label> <span class="title">[+Height+]</span>
                <input name="mla_thumbnail_options[height]" id="mla-thumbnail-options-height" type="text" size="5" value="" />
              </label></td>
              <td class="textleft"><label>
                <input name="mla_thumbnail_options[best_fit]" id="mla-thumbnail-options-best-fit" type="checkbox" value="checked" />
                [+Best Fit+]
              </label></td>
			  </tr><tr>
              <td><label class="alignleft"> <span class="title">[+Page+]</span>
                <input name="mla_thumbnail_options[page]" id="mla-thumbnail-options-page" type="text" size="5" value="" />
              </label></td>
              <td><label> <span class="title">[+Resolution+]</span>
                <input name="mla_thumbnail_options[resolution]" id="mla-thumbnail-options-resolution" type="text" size="5" value="" />
              </label></td>
              <td><label> <span class="title">[+Quality+]</span>
                <input name="mla_thumbnail_options[quality]" id="mla-thumbnail-options-quality" type="text" size="5" value="" />
              </label></td>
			  </tr><tr>
              <td><label>
            <span class="title">[+Type+]</span>
            <input type="radio" name="mla_thumbnail_options[type]" id="mla-thumbnail-options-jpg" checked="checked" value="image/jpeg" />
            JPG&nbsp;&nbsp;
            <input type="radio" name="mla_thumbnail_options[type]" id="mla-thumbnail-options-png" value="image/png" />
            PNG
			</label></td>
              <td><label class="alignleft"> <span class="title">[+Existing Items+]</span>
            <select name="mla_thumbnail_options[existing_thumbnails]" id="mla-thumbnail-options-existing">
                <option selected="selected" value="keep">[+Keep+]</option>
                <option value="ignore">[+Ignore+]</option>
                <option value="trash">[+Trash+]</option>
                <option value="delete">[+Delete+]</option>
            </select>
              </label></td>
              <td><label> <span class="title">[+Suffix+]</span>
                <input name="mla_thumbnail_options[suffix]" id="mla-thumbnail-options-quality" type="text" size="15" value="[+default_suffix+]" />
              </label></td>
			  </tr></table>
            </div> <!-- inline-edit-group -->
<!--		<label class="alignleft clear">
			<span class="title" style="display: inline-block">[+Options+]</span>
            <input name="mla_thumbnail_options[clear_filters]" id="mla-thumbnail-options-clear-filters" type="checkbox" value="checked" checked="checked" />[+Clear Filter-by+]
			</label> -->
            </div> <!-- inline-edit-col -->
          </fieldset>
          <p class="submit inline-edit-save"> <a accesskey="c" href="#mla-generate-thumbnail" title="[+Cancel+]" class="button-secondary cancel alignleft">[+Cancel+]</a>
            <input type="submit" name="mla-generate-thumbnail" id="mla-generate-thumbnail-submit" class="button-primary alignright" value="[+Generate Thumbnails+]" />
            <input type="hidden" name="page" value="mla-menu" />
            <input type="hidden" name="screen" value="media_page_mla-menu" />
            <span class="error" style="display:none;"></span> <br class="clear" />
          </p>
        </td>
      </tr>
    </tbody>
  </table>
</form>
