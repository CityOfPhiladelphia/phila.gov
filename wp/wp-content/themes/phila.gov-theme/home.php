<?php
/**
 * Template Name: Blog
 *
 * This is the template to display all our blog posts at /posts
 *
 * @package phila-gov
 */

get_header(); ?>

<section id="primary" class="content-area archive row">
<?php
  global $post;
  $paged = ( get_query_var('paged')) ? get_query_var('paged') : 1;
  $args  = array(
            'posts_per_page' => 10,
            'paged'     =>$paged,
            'post_type' => 'post',
            'order' => 'DESC',
            'orderby' => 'post_date'
            );

    $display_posts = new WP_Query( $args );

    if ( $display_posts->have_posts() ) : ?>
      <header class="columns">
        <h1 class="contrast ptm">
          <?php echo get_the_title(); ?>
        </h1>
      </header><!-- .page-header -->
      <main id="main" class="site-main small-24 medium-24 end columns" role="main">

        <?php while ( $display_posts->have_posts() ) : $display_posts->the_post(); ?>

          <?php get_template_part( 'partials/content', 'list-featured-image' ) ?>


        <?php endwhile; ?>

        <?php phila_gov_paging_nav(); ?>

        <?php else : ?>

          <?php get_template_part( 'partials/content', 'none' ); ?>

        <?php endif; ?>

      </main><!-- #main -->
</section><!-- #primary -->
<?php get_footer(); ?>
