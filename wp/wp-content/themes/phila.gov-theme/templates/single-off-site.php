<?php
/* Template part
* for displaying off-site departments
*/
?>
<div data-swiftype-index='true' class="entry-content medium-18 small-24 columns end">
    <div class="external-site">
      <p><?php the_title(); ?> has a <strong>separate website</strong>: <a href="<?php echo rwmb_meta( 'phila_dept_url', $args = array('type' => 'url')); ?>"><?php echo rwmb_meta( 'phila_dept_url', $args = array('type' => 'url')); ?></a></p>

    <a class="button icon" href="<?php echo rwmb_meta( 'phila_dept_url', $args = array('type' => 'url')); ?>">You are now leaving
    <?php phila_util_echo_website_url();?> <i class="fa fa-sign-out"></i></a>
    <?php echo '<p data-swiftype-name="body" data-swiftype-type="text" class="description">' . rwmb_meta( 'phila_dept_desc', $args = array('type' => 'textarea')) . '</p>'; ?>
  </div><!-- .external-site -->
</div><!--.entry-content -->
