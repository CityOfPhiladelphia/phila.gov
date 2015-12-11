<?php
/**
 * The template for displaying the news archive.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package phila-gov
 */

get_header();
?>

<section id="primary" class="content-area archive row news">
  <?php
    if ( have_posts() ) : ?>
      <header class="columns">
        <h1>
          <?php
            _e( 'News ', 'phila-gov' );

          $taxonomy = 'topics';
          $queried_term = get_query_var($taxonomy);
          if (!$queried_term == 0) :
            $term_obj = get_term_by( 'slug', $queried_term, $taxonomy);
            echo ' | ' . $term_obj->name ;
          elseif (is_category()):
            $current_cat = get_the_category();
            echo ' | ' . $current_cat[0]->name;
          endif;
          ?>
        </h1>
        </header><!-- .page-header -->
      <main id="main" class="site-main medium-19 columns end" role="main">
        <?php while ( have_posts() ) : the_post(); ?>
        <?php
            get_template_part( 'partials/content', 'news' );

         endwhile;

          phila_gov_paging_nav();

      else :

         get_template_part( 'partials/content', 'none' );

       endif; ?>

    </main><!-- #main -->

</section><!-- #primary -->
<?php get_footer(); ?>
