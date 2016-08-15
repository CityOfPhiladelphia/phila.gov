<?php
/* Template part
* for displaying off-site departments
*/
?>

<?php
  $department_external_url = rwmb_meta( 'phila_dept_url', $args = array('type' => 'url'));
?>

<div class="row ptm">
  <div data-swiftype-index='true' class="entry-content columns end">
    <p data-swiftype-name="body" data-swiftype-type="text" class="description"> <?php echo phila_get_item_meta_desc(); ?> </p>
  </div>
</div>

<div class="row columns mvm equal-height">
  <div class="small-24 medium-12 column intro equal pal external-site-link-left bg-ghost-gray">
    <p class="mbn"><strong><?php the_title(); ?> has a separate website</strong>: <a href="<?php echo $department_external_url ?>"><?php echo $department_external_url ?></a></p>
  </div>
  <div class="small-24 medium-12 column intro equal pal external-site-link-right bg-ghost-gray">
    <a class="button man icon" href="<?php echo rwmb_meta( 'phila_dept_url', $args = array('type' => 'url')); ?>"><span class="show-for-large-only">You are now </span>leaving
    <?php phila_util_echo_website_url();?> <i class="fa fa-sign-out bg-sidewalk" aria-hidden="true"></i></a>
  </div>
</div>
