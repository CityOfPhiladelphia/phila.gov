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

      elseif ( get_post_type() === 'phila_post'):

        get_template_part('partials/content', 'single-post');

      elseif ( get_post_type() === 'news_post'):

        get_template_part('partials/content', 'single-news');

      elseif ( get_post_type() === 'press_release'):

      get_template_part('partials/content', 'press-release');

      else :

        get_template_part( 'templates/default', 'page' );

      endif;

    endwhile; // end of the loop. ?>

  </main><!-- #main -->
</div><!-- #primary -->


<?php get_footer(); ?>
