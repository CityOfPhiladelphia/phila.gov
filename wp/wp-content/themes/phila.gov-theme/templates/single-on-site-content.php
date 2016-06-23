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
  <?php $user_selected_template = phila_get_selected_template(); ?>

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

  <?php if ($user_selected_template == 'resource_list') : ?>
    <!-- Begin Resource List Template Display -->
    <section class="apply-template">
      <?php get_template_part( 'partials/resource', 'list' ); ?>
    </section>
    <!-- End Resource List Template Display -->
  <?php endif; ?>

  <?php if ($user_selected_template == 'one_page_department') : ?>
  <?php $full_row_blog = rwmb_meta( 'phila_full_row_blog_selected' ); ?>
    <?php if ( $full_row_blog == 1): ?>
      <div class="row">
        <?php echo do_shortcode('[recent-posts posts="3"]'); ?>
      </div>
    <?php endif; ?>

  <?php endif; ?>
  <?php get_template_part( 'partials/departments/content', 'row-two' ); ?>

  <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>
</div> <!-- End .entry-content -->
