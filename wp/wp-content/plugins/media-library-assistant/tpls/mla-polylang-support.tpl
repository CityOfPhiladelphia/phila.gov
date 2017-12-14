<!-- template="quick_action" -->
<tr class = "pll-media-action-row-[+language_slug+]">
  <td class = "pll-media-language-column"><span class = "pll-translation-flag">[+language_flag+]</span>[+language_name+]</td>
  <td class = "pll-media-action-column pll-media-action-column-[+language_slug+]">
    <input type="hidden" name="media_tr_lang[[+language_slug+]]" value="" />
	<a href="#pll-quick-translate-edit" title="" class=""></a>
  </td>
</tr>

<!-- template="bulk_action" -->
<tr class = "pll-media-action-row-[+language_slug+]">
  <td class = "pll-media-language-column"><span class = "pll-translation-flag">[+language_flag+]</span></td>
  <td class = "pll-media-language-column">[+language_name+]</td>
  <td class = "pll-media-action-column pll-media-action-column-[+language_slug+]">
    <input type="checkbox" name="bulk_tr_languages[[+language_slug+]]" value="translate" />
  </td>
</tr>

<!-- template="page" -->
<form>
  <table width="99%" style="display: none">
    <tbody id="pll-inline-translate">
      <tr id="pll-quick-translate" class="inline-edit-row inline-edit-row-attachment inline-edit-attachment quick-edit-row quick-edit-row-attachment quick-edit-attachment" style="display: none">
        <td colspan="[+colspan+]" class="colspanchange">
          <fieldset class="inline-edit-col-left">
            <div class="inline-edit-col">
              <h4>[+Quick Translate+]</h4>
			  <span class="icon column-icon"></span>
            </div>
          </fieldset>
          <fieldset class="inline-edit-col-center">
            <div class="inline-edit-col">
[+quick_translate_language+]
            </div>
          </fieldset>
          <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
[+quick_translate_actions+]
            </div>
          </fieldset>
          <p class="submit pll-quick-translate-save">
		  	<a accesskey="c" href="#pll-quick-translate" title="[+Cancel+]" class="button-secondary cancel alignleft">[+Cancel+]</a>
		  	<a accesskey="s" href="#pll-quick-translate" title="[+Update+]" class="button-primary save alignright">[+Update+]</a>
			<span class="spinner"></span>
            <input type="hidden" name="page" value="mla-menu" />
            <input type="hidden" name="screen" value="media_page_mla-menu" />
            <input type="hidden" name="inline_translations" value="" />
            <input type="hidden" name="pll_quick_language" value="" />
            <input type="hidden" name="pll_quick_id" value="" />
            <span class="error" style="display:none"></span>
			<br class="clear" />
          </p>
        </td>
      </tr>
      <tr id="pll-bulk-translate" class="inline-edit-row inline-edit-row-attachment inline-edit-attachment bulk-edit-row bulk-edit-row-attachment bulk-edit-attachment" style="display: none">
        <td colspan="[+colspan+]" class="colspanchange">
          <fieldset class="inline-edit-col-left">
            <div class="inline-edit-col">
              <h4>[+Bulk Translate+]</h4>
              <div id="pll-bulk-title-div">
                <div id="pll-bulk-titles"></div>
              </div>
            </div>
          </fieldset>
          <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
              <h4>([+Add or Modify+])</h4>
			<label class="alignleft clear">
			<span class="title">[+Language+]</span>
[+bulk_translate_actions+]
			</label>
			<label class="alignleft clear">
			<span class="title">[+Options+]</span>
            <input type="checkbox" name="bulk_tr_options[clear_filters]" value="checked" />[+Clear Filter-by+]
			</label>
            </div>
          </fieldset>
          <p class="submit inline-edit-save">
		  	<a accesskey="c" href="#pll-bulk-translate" title="[+Cancel+]" class="button-secondary cancel alignleft">[+Cancel+]</a>
            <input type="submit" name="pll-bulk-translate" id="pll-bulk-translate-submit" class="button-primary alignright" value="[+Bulk Translate+]" />
            <input type="hidden" name="page" value="mla-menu" />
            <input type="hidden" name="screen" value="media_page_mla-menu" />
			<input type="hidden" name="pll_bulk_language" value="" />
            <span class="error" style="display:none;"></span> <br class="clear" />
          </p>
        </td>
      </tr>
    </tbody>
  </table>
</form>
