<!-- template="category_fieldset" -->
  <fieldset class="inline-edit-col-left inline-edit-categories"><div class="inline-edit-col">
[+category_blocks+]
  </div></fieldset>
<!-- template="category_block" -->
    <span class="title inline-edit-categories-label">[+tax_html+]
      <span class="catshow">[[+more+]]</span>
      <span class="cathide" style="display:none;">[[+less+]]</span>
    </span>
    <input type="hidden" name="tax_input[[+tax_attr+]][]" value="0" />
    <ul class="cat-checklist [+tax_attr+]-checklist">
[+tax_checklist+]
    </ul>
<!-- template="tag_fieldset" -->
  <fieldset class="inline-edit-col-center inline-edit-tags"><div class="inline-edit-col">
[+tag_blocks+]
  </div></fieldset>
<!-- template="tag_block" -->
    <label class="inline-edit-tags">
      <span class="title">[+tax_html+]</span>
      <textarea cols="22" rows="1" name="tax_input[[+tax_attr+]]" class="tax_input_[+tax_attr+] mla_tags"></textarea>
    </label>
<!-- template="taxonomy_options" -->
    <div class="mla_taxonomy_options">
      <input type="radio" name="tax_action[[+tax_attr+]]" id="tax_add_[+tax_attr+]" checked="checked" value="add" /> [+Add+]&nbsp;
      <input type="radio" name="tax_action[[+tax_attr+]]" id="tax_remove_[+tax_attr+]" value="remove" /> [+Remove+]&nbsp;
      <input type="radio" name="tax_action[[+tax_attr+]]" id="tax_reset_[+tax_attr+]" value="replace" /> [+Replace+]&nbsp;
    </div>
<!-- template="custom_field" -->
      <label class="inline-edit-[+slug+] clear"><span class="title">[+label+]</span><span class="input-text-wrap">
        <input type="text" name="[+slug+]" value="" />
        </span></label>
<!-- template="page" -->
<div id="mla-blank-add-new-bulk-edit-div" style="display: none;">
[+category_fieldset+]
[+tag_fieldset+]
  <fieldset class="inline-edit-col-right inline-edit-fields">
    <div class="inline-edit-col">
      <label><span class="title">[+Title+]</span><span class="input-text-wrap">
        <input type="text" name="post_title" class="ptitle" value="" />
        </span></label>
      <label><span class="title">[+Caption+]</span><span class="input-text-wrap">
        <input type="text" name="post_excerpt" value="" />
        </span></label>
      <label><span class="title">[+Description+]</span><span class="input-text-wrap">
        <textarea class="widefat" name="post_content"></textarea>
        </span></label>
      <label class="inline-edit-image-alt"><span class="title">[+ALT Text+]</span><span class="input-text-wrap">
        <input type="text" name="image_alt" value="" />
        </span></label>
      <div class="inline-edit-group">
        <label class="inline-edit-post-parent alignleft"><span class="title">[+Parent ID+]</span><span class="input-text-wrap">
          <input type="text" name="post_parent" value="" />
          </span></label>
          <input id="bulk-edit-set-parent" title="[+Select+]" class="button-primary parent" type="button" name="post_parent_set" value="[+Select+]" />
[+authors+]
      </div>
      <div class="inline-edit-group">
        <label class="inline-edit-comments alignleft"><span class="title">[+Comments+]</span><span class="input-text-wrap">
          <select name="comment_status">
            <option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
            <option value="open">[+Allow+]</option>
            <option value="closed">[+Do not allow+]</option>
          </select>
        </span></label>
        <label class="inline-edit-pings alignright"><span class="title">[+Pings+]</span><span class="input-text-wrap">
          <select name="ping_status">
            <option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
            <option value="open">[+Allow+]</option>
            <option value="closed">[+Do not allow+]</option>
          </select>
        </span></label>
      </div>
[+custom_fields+]
    </div>
  </fieldset>
</div>
<div id="mla-add-new-bulk-edit-div" style="display: none;">
<input id="bulk-edit-toggle" title="[+Toggle+]" class="button-primary alignright" type="button" name="bulk_edit_toggle" value="[+Toggle+]" />
<input id="bulk-edit-reset" title="[+Reset+]" class="button-secondary alignright" type="button" name="bulk_edit_reset" value="[+Reset+]" style="display:none" />
<strong>[+NOTE+]</strong><br />
[+category_fieldset+]
[+tag_fieldset+]
  <fieldset class="inline-edit-col-right inline-edit-fields">
    <div class="inline-edit-col">
      <label><span class="title">[+Title+]</span><span class="input-text-wrap">
        <input type="text" name="post_title" class="ptitle" value="" />
        </span></label>
      <label><span class="title">[+Caption+]</span><span class="input-text-wrap">
        <input type="text" name="post_excerpt" value="" />
        </span></label>
      <label><span class="title">[+Description+]</span><span class="input-text-wrap">
        <textarea class="widefat" name="post_content"></textarea>
        </span></label>
      <label class="inline-edit-image-alt"><span class="title">[+ALT Text+]</span><span class="input-text-wrap">
        <input type="text" name="image_alt" value="" />
        </span></label>
      <div class="inline-edit-group">
        <label class="inline-edit-post-parent alignleft"><span class="title">[+Parent ID+]</span><span class="input-text-wrap">
          <input type="text" name="post_parent" value="" />
          </span></label>
          <input id="bulk-edit-set-parent" title="[+Select+]" class="button-primary parent" type="button" name="post_parent_set" value="[+Select+]" />
[+authors+]
      </div>
      <div class="inline-edit-group">
        <label class="inline-edit-comments alignleft"><span class="title">[+Comments+]</span><span class="input-text-wrap">
          <select name="comment_status">
            <option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
            <option value="open">[+Allow+]</option>
            <option value="closed">[+Do not allow+]</option>
          </select>
        </span></label>
        <label class="inline-edit-pings alignright"><span class="title">[+Pings+]</span><span class="input-text-wrap">
          <select name="ping_status">
            <option selected="selected" value="-1">&mdash; [+No Change+] &mdash;</option>
            <option value="open">[+Allow+]</option>
            <option value="closed">[+Do not allow+]</option>
          </select>
        </span></label>
      </div>
[+custom_fields+]
    </div>
  </fieldset>
    <input type="hidden" name="page" value="media-new.php" />
    <input type="hidden" name="screen" value="async-upload" />
<div class="clear" style="border-bottom: thin solid #bbb"></div>
</div>
[+set_parent_form+]
