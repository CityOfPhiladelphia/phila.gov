<?php
/**
 * The template used for displaying program sites
 *
 * @package phila-gov
 */

get_header();

$user_selected_template = phila_get_selected_template();

?>

<div id="post-<?php the_ID(); ?>" <?php post_class('program clearfix'); ?>>
  <header>

  <?php

    $parent = phila_util_get_furthest_ancestor($post);

      while ( have_posts() ) : the_post();
        include( locate_template( 'partials/programs/header.php' ) );
        get_template_part( 'partials/breadcrumbs' );
        get_template_part( 'partials/departments/v2/our', 'services' );
        get_template_part( 'partials/departments/content', 'programs-initiatives' );
      endwhile;

  ?>
</div><!-- #post-## -->
<?php get_footer(); ?>
