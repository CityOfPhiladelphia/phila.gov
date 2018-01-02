<?php
/**
 * The template used for displaying program sites
 *
 * @package phila-gov
 */

get_header();

$user_selected_template = phila_get_selected_template(); ?>

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
      if ($user_selected_template == 'phila_one_quarter'):
        get_template_part( 'partials/departments/v2/content', 'one-quarter' );
      endif;
      get_template_part( 'partials/departments/content', 'programs-initiatives' );
    endwhile;

  ?>
</div><!-- #post-## -->
<?php get_footer(); ?>
