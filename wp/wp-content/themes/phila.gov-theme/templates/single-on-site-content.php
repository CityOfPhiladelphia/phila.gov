<?php
/*
*
* Template part
* for displaying on-site department content
*
*/
?>
<?php $user_selected_template = phila_get_selected_template(); ?>

<?php $staff_directory_listing = rwmb_meta( 'phila_staff_directory_selected' ); ?>

<?php if ( phila_util_is_v2_template() && phila_get_selected_template() !== 'homepage_v2') : ?>
  <div class="row mtl mbm">
    <div class="columns">
      <?php echo phila_breadcrumbs(); ?>
    </div>
  </div>
<?php endif; ?>

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

  <?php //Begin v2 homepage specific templates ?>
  <?php if ($user_selected_template == 'homepage_v2') : ?>

    <?php $args = array( 'post_type' => 'service_updates', 'category__in' => phila_util_cat_ids()); ?>
    <?php $service_updates_loop = new WP_Query( $args ); ?>
    <?php include( locate_template( 'partials/content-service-updates.php' ) ); ?>
    <?php wp_reset_query();?>
    <?php get_template_part( 'partials/departments/v2/content', 'curated-service-list' ); ?>
  <?php endif;?>

  <?php //Begin v2 templates ?>
  <?php if ($user_selected_template == 'all_services_v2') : ?>
    <?php get_template_part( 'partials/departments/content', 'all-services-v2' ); ?>
  <?php endif;?>

  <?php if ($user_selected_template == 'one_quarter_headings_v2') : ?>
    <?php get_template_part( 'partials/departments/content', 'one-quarter-v2' ); ?>
  <?php endif;?>

  <?php if ($user_selected_template == 'forms_and_documents_v2') : ?>
    <?php get_template_part( 'partials/departments/content', 'forms-documents-v2' ); ?>
  <?php endif;?>

  <?php if ($user_selected_template == 'contact_us_v2') : ?>
    <?php get_template_part( 'partials/departments/v2/content', 'contact-us' ); ?>
  <?php endif;?>

  <?php if ($user_selected_template == 'resource_list_v2') : ?>
    <section class="apply-template">
      <?php get_template_part( 'partials/resource', 'list' ); ?>
    </section>
  <?php endif;?>

  <?php if ($user_selected_template == 'staff_directory_v2') : ?>
    <?php get_template_part( 'partials/departments/content', 'staff-directory' ); ?>
  <?php endif;?>

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
    <?php //else, this is still a v2 homepage and we need to render the rest of the page ?>
  <?php else: ?>

    <?php get_template_part( 'partials/departments/content', 'row-one' ); ?>
    <?php get_template_part( 'partials/departments/v2/content', 'homepage-survey');?>
    <?php get_template_part( 'partials/departments/content', 'row-two' ); ?>

    <?php if ( $staff_directory_listing ): ?>
      <?php get_template_part( 'partials/departments/content', 'staff-directory' ); ?>
    <?php endif; ?>
    <?php get_template_part( 'partials/departments/content', 'call-to-action-multi' ); ?>


  <?php endif; ?>

  <?php if ($user_selected_template == 'one_page_department') : ?>
    <?php get_template_part( 'partials/departments/content', 'call-to-action-multi' ); ?>
  <?php endif;?>

  <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>
</div> <!-- End .entry-content -->
