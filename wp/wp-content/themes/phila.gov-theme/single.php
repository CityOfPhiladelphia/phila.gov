<?php
/**
 * The template for displaying all single posts.
 *
 * @package phila-gov
 */

get_header(); ?>

<div id="primary" class="content-area row">
  <main id="main" class="site-main small-24 columns" role="main">

  <?php while ( have_posts() ) : the_post(); ?>

    <?php
      if ( get_post_type() === 'calendar') :

        get_template_part( 'partials/content', 'page' );

       else :

        get_template_part( 'partials/content', 'single' );

      endif;

    endwhile; // end of the loop. ?>

  </main><!-- #main -->
</div><!-- #primary -->


<?php get_footer(); ?>
