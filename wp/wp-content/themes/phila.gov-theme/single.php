<?php
/**
 * The template for displaying all single posts.
 *
 * @package phila-gov
 */

get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">

  <?php while ( have_posts() ) : the_post(); ?>

    <?php
      if ( get_post_type() === 'calendar') :

        get_template_part( 'templates/default', 'page' );

      elseif ( phila_util_return_is_post( get_post_type() ) ):

        get_template_part( 'templates/single', 'post' );

      else :

        get_template_part( 'templates/default', 'page' );

      endif;

    endwhile; // end of the loop. ?>

  </main><!-- #main -->
</div><!-- #primary -->


<?php get_footer(); ?>
