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
    <p data-swiftype-name="body" data-swiftype-type="text" class="description"> <?php echo phila_get_item_meta_desc( ); ?> </p>
  </div>
</div>

<div class="row columns mvm equal-height">
  <div class="small-24 medium-12 column intro equal pal external-site-link-left bg-ghost-gray">
    <p class="mbn"><strong>The <?php the_title(); ?>'s content is on our legacy site.</strong></p>
  </div>
  <div class="small-24 medium-12 column intro equal pal external-site-link-right bg-ghost-gray">
    <a href="<?php echo rwmb_meta( 'phila_dept_url', $args = array('type' => 'url')); ?>" class="button icon clearfix">
      <div class="valign">
        <i class="fa fa-sign-out valign-cell"></i>
        <div class="button-label valign-cell">Go to the legacy site</div>
      </div>
    </a>
  </div>
</div>
