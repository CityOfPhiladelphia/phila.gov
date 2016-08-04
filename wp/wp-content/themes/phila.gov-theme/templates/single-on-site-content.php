<?php
/*
*
* Template part
* for displaying on-site department content
*
*/
?>
<?php $user_selected_template = phila_get_selected_template(); ?>

<?php if ($user_selected_template == 'programs_initiatives') : ?>
  <?php get_template_part( 'partials/departments/content', 'programs-initiatives-header' ); ?>
<?php else : ?>
  <header class="row">
    <div class="columns">
      <?php the_title( '<h2 class="sub-page-title contrast">', '</h2>' ); ?>
    </div>
  </header>
<?php endif; ?>

<div data-swiftype-index='true' class="entry-content">
  <?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
  <?php get_template_part( 'partials/departments/content', 'hero-header' ); ?>

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

  <?php if ($user_selected_template == 'department_homepage') : ?>
    <!-- Begin Department Homepage Display -->
    <section class="apply-template">
      <?php get_template_part( 'partials/departments/content', 'department-homepage' ); ?>
    </section>
    <!-- End Department Homepage Display -->

  <?php elseif ($user_selected_template == 'department_subpage') : ?>
    <!-- Begin Department Subpage Template Display -->
    <section class="apply-template">
      <?php get_template_part( 'partials/departments/content', 'department-subpage' ); ?>
    </section>
    <!-- End Department Subpage Template Display -->

  <?php elseif ($user_selected_template == 'programs_initiatives') : ?>
    <!-- Begin Department Program & Initiatives Display -->
    <section class="apply-template">
      <?php get_template_part( 'partials/departments/content', 'programs-initiatives' ); ?>
    </section>
    <!-- End Department Program & Initiatives Display -->

  <?php elseif ($user_selected_template == 'one_page_department') : ?>
    <!-- Begin One Page Template Display -->
    <?php get_template_part( 'partials/departments/content', 'row-one' ); ?>

    <?php $staff_directory_listing = rwmb_meta( 'phila_staff_directory_selected' ); ?>
    <?php if ( $staff_directory_listing ): ?>
      <?php get_template_part( 'partials/departments/content', 'staff-directory' ); ?>
    <?php endif; ?>
    <?php $full_row_blog = rwmb_meta( 'phila_full_row_blog_selected' ); ?>
    <?php if ( $full_row_blog == 1): ?>
      <section class="row">
        <?php echo do_shortcode('[recent-posts posts="3"]'); ?>
      </section>
    <?php endif; ?>
    <!-- End One Page Template Display -->

  <?php elseif ($user_selected_template == 'resource_list') : ?>
    <!-- Begin Resource List Display -->
    <section class="apply-template">
      <?php get_template_part( 'partials/resource', 'list' ); ?>
    </section>
    <!-- End Resource List Display -->

  <?php elseif ($user_selected_template == 'staff_directory') : ?>
    <!-- Begin Staff Directory Display -->
    <section class="apply-template">
      <?php get_template_part( 'partials/departments/content', 'staff-directory' ); ?>
    </section>
    <!-- End Staff Directory Display -->
  <?php else: ?>
    <?php get_template_part( 'partials/departments/content', 'row-one' ); ?>
    <?php get_template_part( 'partials/departments/content', 'row-two' ); ?>
  <?php endif; ?>

  <?php if ($user_selected_template == 'one_page_department') : ?>
    <?php get_template_part( 'partials/departments/content', 'call-to-action-multi' ); ?>
  <?php endif;?>

  <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>
</div> <!-- End .entry-content -->
