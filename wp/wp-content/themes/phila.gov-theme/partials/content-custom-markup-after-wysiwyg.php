<?php
/*
 *
 * Partial for rendering Custom Markup After WYSIWYG
 *
 */
?>
<?php
  //set vars
  if ( function_exists( 'rwmb_meta' ) ) :
    $append_after_wysiwyg = rwmb_meta( 'phila_append_after_wysiwyg', $args = array( 'type' => 'textarea' ) );
  endif;
?>
<?php if ( !$append_after_wysiwyg == '' ): ?>
<!-- If Custom Markup append_after_wysiwyg is present print it -->
  <div class="row after-wysiwyg">
    <div class="small-24 columns">
      <?php echo $append_after_wysiwyg; ?>
    </div>
  </div>
<?php endif; ?>
