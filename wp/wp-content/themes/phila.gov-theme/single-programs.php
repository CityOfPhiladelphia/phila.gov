<?php
/**
 * The template used for displaying program sites
 *
 * @package phila-gov
*/

$user_selected_template = phila_get_selected_template();

get_header();

?>

<?php if ( $user_selected_template == 'prog_off_site' ) : ?>
  <?php include(locate_template('templates/single-off-site.php')); ?>

  <?php get_footer(); ?>
  <?php return; ?>
<?php endif;?>

<div id="post-<?php the_ID(); ?>" <?php post_class('program clearfix'); ?>>
  <?php
    while ( have_posts() ) : the_post();
      include( locate_template( 'partials/programs/header.php' ) );

      get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
      <?php if( !empty( get_the_content() ) ) : ?>
        <div class="row">
          <div class="columns">
            <?php the_content(); ?>
          </div>
        </div>
      <?php endif; ?>

      <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>

      <?php get_template_part( 'partials/departments/v2/our', 'services' );

      if ($user_selected_template == 'phila_one_quarter'): ?>
      <!--1/4 Content-->
        <?php get_template_part( 'partials/departments/v2/content', 'one-quarter' ); ?>
      <!--/ 1/4 Content-->
      <?php endif; ?>

      <?php if ($user_selected_template == 'resource_list_v2'): ?>
        <!-- Resource list -->
        <section class="mtl">
          <?php include(locate_template('partials/resource-list.php')); ?>
        </section>
        <!-- /Resource list -->
        <?php wp_reset_postdata(); ?>
      <?php endif; ?>

      <?php if ($user_selected_template == 'collection_page_v2'): ?>
        <!-- Collection page -->
        <section class="mtl">
          <?php include(locate_template('partials/departments/v2/collection-page.php')); ?>
        </section>
        <?php wp_reset_postdata(); ?>
        <!-- Collection page -->
      <?php endif; ?>

      <?php if ($user_selected_template == 'document_finder_v2'): ?>
        <!-- Document finder -->
        <section class="mtl">
          <?php include(locate_template('partials/departments/v2/document-finder.php')); ?>
        </section>
        <?php wp_reset_postdata(); ?>
        <!-- Document finder -->
      <?php endif; ?>

      <!-- Program and initiatives -->
      <?php get_template_part( 'partials/departments/content', 'programs-initiatives' ); ?>
      <!-- /Program and initiatives -->

      <!-- Additional Content-->
      <div class="mtxl">
        <?php get_template_part( 'partials/content', 'additional' ); ?>
      </div>

      <!-- /Additional Content-->

    <?php endwhile; ?>
</div><!-- #post-## -->
<?php get_footer(); ?>
