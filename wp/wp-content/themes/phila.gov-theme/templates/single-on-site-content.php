<?php
/*
*
* Template part
* for displaying on-site department content
*
*/
?>
<?php $user_selected_template = phila_get_selected_template(); ?>

<div class="row">
  <div class="columns">
    <?php // TODO: Figure out what to do with the title on Staff Template ?>
    <?php the_title( '<h2 class="sub-page-title">', '</h2>' ); ?>
  </div>
</div>

<div data-swiftype-index='true' class="entry-content">
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

  <?php if ($user_selected_template == 'staff_directory') : ?>
    <!-- Begin Staff Directory Template Display -->
    <section class="apply-template">
      <?php get_template_part( 'partials/departments/content', 'staff-directory' ); ?>
    </section>
    <!-- End Staff Directory Template Display -->
  <?php endif; ?>

  <?php if ($user_selected_template == 'one_page_department') : ?>
    <?php $staff_directory_listing = rwmb_meta( 'phila_staff_directory_selected' ); ?>
    <?php if ( $staff_directory_listing ): ?>
      <?php get_template_part( 'partials/departments/content', 'staff-directory' ); ?>
    <?php endif; ?>
    <?php $full_row_blog = rwmb_meta( 'phila_full_row_blog_selected' ); ?>
    <?php if ( $full_row_blog == 1): ?>
      <div class="row">
        <?php echo do_shortcode('[recent-posts posts="3"]'); ?>
      </div>
    <?php endif; ?>
    <?php //for now, we don't allow calendar and blog posts on one page templates ?>
  <?php else: ?>

    <?php get_template_part( 'partials/departments/content', 'row-two' ); ?>

  <?php endif; ?>

  <?php if ($user_selected_template == 'one_page_department') : ?>

    <?php get_template_part( 'partials/departments/content', 'call-to-action-multi' ); ?>

  <?php endif;?>

  <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>
</div> <!-- End .entry-content -->
