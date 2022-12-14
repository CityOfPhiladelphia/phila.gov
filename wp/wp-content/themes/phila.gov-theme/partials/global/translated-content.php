<?php
/*
 * Partial for rendering translated content.
 *
 */
?>
<?php

foreach ($translated_content as $content) {
  ?>
  <div id="<?php echo $content['translated_language'].'-form'; ?>" class="embedded-translated-form">
  <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_title'] )): ?>
    <div class="row one-quarter-row mvl">
      <div class="small-24 columns">
        <?php $content_id = sanitize_title_with_dashes( $content['phila_custom_wysiwyg']['phila_wysiwyg_title']);?>
        <h3 id="<?php echo $content_id;?>"><?php echo $content['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></h3>
      </div>
    </div>
  <?php endif;?>
  <?php if( $content['phila_custom_wysiwyg']['phila_wysiwyg_content'] != '' ) : ?>
    <!-- WYSIWYG content -->
    <section class="wysiwyg-content">
      <div class="row">
        <div class="small-24 columns">
          <p><?php echo apply_filters('the_content', $content['phila_custom_wysiwyg']['phila_wysiwyg_content']); ?></p>
        </div>
      </div>
    </section>
    <!-- End WYSIWYG content -->
  <?php endif;?>
  </div>
<?php
}
?>