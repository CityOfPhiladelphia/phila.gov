<?php
  /*
  * Breadcrumbs
  */
?>
<div class="grid-container">
  <div class="<?php $post_type == "project" ? "" : "grid-x"; ?>" data-swiftype-index="false">
    <div class="cell">
      <?php echo phila_breadcrumbs(); ?>
    </div>
  </div>
</div>
