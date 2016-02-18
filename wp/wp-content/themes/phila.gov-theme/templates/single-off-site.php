<?php
/* Template part
* for displaying off-site departments
*/
?>

<div data-swiftype-index='true' class="entry-content columns end">
  <div class="external-site">
    <?php echo '<p data-swiftype-name="body" data-swiftype-type="text" class="description">' . rwmb_meta( 'phila_dept_desc', $args = array('type' => 'textarea')) . '</p>'; ?>
      <div class="panel">
        <div class="row">
          <div class="medium-12 column">
            <p><strong><?php the_title(); ?> has a separate website</strong>: <a href="<?php echo rwmb_meta( 'phila_dept_url', $args = array('type' => 'url')); ?>"><?php echo rwmb_meta( 'phila_dept_url', $args = array('type' => 'url')); ?></a></p>
          </div>
          <div class="medium-12 column">
            <a class="button icon" href="<?php echo rwmb_meta( 'phila_dept_url', $args = array('type' => 'url')); ?>">You are now leaving
            <?php phila_util_echo_website_url();?> <i class="fa fa-sign-out"></i></a>
          </div>
        </div>
      </div>
  </div><!-- .external-site -->
</div><!--.entry-content -->
