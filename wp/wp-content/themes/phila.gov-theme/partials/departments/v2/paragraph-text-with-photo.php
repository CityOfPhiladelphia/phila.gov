<!-- Paragraph text with photo -->
<div class="row one-quarter-row mvl photo-paragraph">
  <div class="columns medium-6">
      <?php if( isset($current_row['paragraph_text_with_photo']['phila_wysiwyg_title'] )): ?>
        <?php $current_row_id = sanitize_title_with_dashes( $current_row['paragraph_text_with_photo']['phila_wysiwyg_title']);?>
        <h3 id="<?php echo $current_row_id;?>"><?php echo $current_row['paragraph_text_with_photo']['phila_wysiwyg_title']; ?></h3>
      <?php endif;?>
  </div>
  <div class="columns medium-18 pbxl">
    <div class="mbl">
      <?php if( isset($current_row['paragraph_text_with_photo']['phila_wysiwyg_content'] )): ?>
        <div class="plm">
          <?php echo apply_filters( 'the_content', $current_row['paragraph_text_with_photo']['phila_wysiwyg_content']) ?>
        </div>
      <?php endif;?>
    </div>
  </div>
</div>
<!-- / Paragraph text with photo -->