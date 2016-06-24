<?php
/*
 *
 * Partial for rendering Custom Markup Before WYSIWYG
 *
 */
?>
<?php
  //set vars
  if (function_exists('rwmb_meta')) :
    $append_before_wysiwyg = rwmb_meta( 'phila_append_before_wysiwyg', $args = array('type' => 'textarea'));
  endif;
?>
<?php if (!$append_before_wysiwyg == ''):?>
  <!-- If Custom Markup append_before_wysiwyg is present print it -->
  <div class="row before-wysiwyg">
    <div class="small-24 columns">
      <?php echo $append_before_wysiwyg; ?>
    </div>
  </div>
<?php endif; ?>
