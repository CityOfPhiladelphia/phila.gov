<?php
/*
*
* Template part
* for displaying on-site department content
*
*/
?>
<div class="row">
  <div class="columns">
    <?php the_title( '<h2 class="sub-page-title">', '</h2>' ); ?>
  </div>
</div>

<div data-swiftype-index='true' class="entry-content">
<?php
  //set template selection var
  $user_selected_template = rwmb_meta( 'phila_template_select');
  var_dump($user_selected_template);
?>

  <?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>

  <?php get_template_part( 'partials/departments/content', 'hero-header' ); ?>

  <?php get_template_part( 'partials/departments/content', 'row-one' ); ?>


  <?php if( get_the_content() != '' ) : ?>
  <!-- WYSIWYG content -->
  <section class="wysiwyg-content">
    <div class="row">
      <div class="small-24 columns">
        <?php echo the_content();?>
      </div>
    </div>
  </section>
  <!-- End WYSIWYG content -->
  <?php endif; ?>

  <?php if( !empty($user_selected_template) ) : ?>
  <!-- Begin Template Display -->
  <section class="apply-template">
    <?php if ($user_selected_template == 'resource_list') : ?>
      <?php get_template_part( 'partials/resource', 'list' ); ?>
  <?php endif; ?>
  </section>
  <!-- End Template Display -->
  <?php endif; ?>

  <?php get_template_part( 'partials/departments/content', 'row-two' ); ?>

  <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>
</div> <!-- End .entry-content -->
