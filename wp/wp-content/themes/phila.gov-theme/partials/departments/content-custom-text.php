<?php
/*
 *
 * Partial for rendering Custom Textareas
 *
 */
?>
<?php if ( !empty( $custom_text )):
  $custom_text_title = $custom_text['phila_custom_text_title'];
  $custom_text_content = $custom_text['phila_custom_text_content'];
?>
  <?php if ( !empty( $custom_text_title ) ) :?>
    <h2 class="contrast"><?php echo( $custom_text_title ); ?></h2>
  <?php endif; ?>
  <?php if ( !empty( $custom_text_content ) ) :?>
    <div class="custom-text">
      <?php echo do_shortcode(wpautop( $custom_text_content )); ?>
    </div>
  <?php else :?>
    <div class="placeholder">
      Please enter content.
    </div>
  <?php endif; ?>
<?php endif; ?>
