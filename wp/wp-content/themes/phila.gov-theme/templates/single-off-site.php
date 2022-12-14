<?php
/* Template part
* for displaying off-site departments
*/
?>

<?php
  $department_external_url = rwmb_meta( 'phila_dept_url', $args = array('type' => 'url'));
  $program_external_url = rwmb_meta('prog_off_site_link', $args = array('type' => 'url'));
?>
<div class="row columns">
  <h1 class="contrast"><?php echo the_title() ?></h1>
</div>

<div class="row ptm">
  <div data-swiftype-index='true' class="entry-content columns end">
    <p data-swiftype-name="body" data-swiftype-type="text" class="description"> <?php echo phila_get_item_meta_desc( ); ?> </p>
  </div>
</div>

<?php if (isset($user_selected_template)): ?>
<div class="row">
    <a class="columns" href="<?php echo $program_external_url?>">
      <div class="row program-card columns card bg-ghost-gray">
      <?php
      $img = rwmb_meta( 'prog_header_img', $args = array( 'size' => 'large', 'limit' => 1 ), $post->ID );
      $img = reset( $img );?>
      <img src="<?php echo $img['url'] ?>" alt="<?php echo $img['alt']?>" class="columns medium-7 pan show-for-medium">
      <div class="content-block columns medium-16">
        <div class="medium-text mvl">You can find more information on <?php the_title(); ?> on their website.</div>
        <div class="button icon clearfix mbm">
          <div class="valign">
            <div class="button-label valign-cell">Go to <?php the_title(); ?></div>
          </div>
        </div>
      </div>
    </div>
  </a>
</div>
<?php return; ?>
<?php endif; ?>

<div class="row columns mvm equal-height">
  <div class="small-24 medium-12 column intro equal pal external-site-link-left bg-ghost-gray">
    <p class="mbn"><strong>You can find more information on <?php the_title(); ?> on their website.</strong></p>
  </div>
  <div class="small-24 medium-12 column intro equal pal external-site-link-right bg-ghost-gray">
    <a href="<?php echo rwmb_meta( 'phila_dept_url', $args = array('type' => 'url')); ?>" class="button icon clearfix">
      <div class="valign">
        <div class="button-label valign-cell">Go to <?php the_title(); ?></div>
      </div>
    </a>
  </div>
</div>
