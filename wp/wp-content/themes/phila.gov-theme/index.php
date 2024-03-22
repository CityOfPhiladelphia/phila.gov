<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package phila-gov
 */

get_header(); ?>

<div id="primary" class="content-area row">
  <main id="main" class="site-main medium-18 columns">
    <?php if ( have_posts() ) : ?>
      <?php while ( have_posts() ) : the_post(); ?>
        <?php get_template_part( 'partials/content', 'list-featured-image' ); ?>

      <?php endwhile; ?>

    <?php else : ?>

      <?php get_template_part( 'partials/content', 'none' ); ?>

    <?php endif; ?>
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

<?php

$args = array(
  'post_type' => array(
    'service_page',
  ),
  'post_status' => 'any',
  'posts_per_page' => -1,
);
$the_query = new WP_Query( $args );

// The Loop
if ( $the_query->have_posts() ) {
    echo '<table><thead><tr><th>alternate title</th><th>title</th><th>url</th><th>owners</th><th>service type</th><th>template</th><th>parent</th><th>children</th></tr></thead>';

    while ( $the_query->have_posts() ) {
        $the_query->the_post();
        echo '<tr><td>'. rwmb_meta('phila_service_alt_title') .'</td><td>' . get_the_title(). '</td><td>' .  get_permalink() .'</td> <td>';

      if( get_the_category() != null ) {
        foreach((get_the_category()) as $category){
          echo $category->name;
        }
      }

    echo '</td>';

    echo '<td>';

    if( get_the_terms($post, 'service_type') != null ) {
      foreach(get_the_terms($post, 'service_type') as $term){
        echo $term->name;
      }
    }
    echo'</td>';
    echo '<td>';
      $template = rwmb_meta( 'phila_template_select' );
      echo $template;

  echo   '</td>';

  echo '<td>';

  echo get_permalink(wp_get_post_parent_id($post->ID));

    echo '</td>';

    echo '<td>';

    $args = array(
        'post_parent' => $post->ID,
        'post_type'   => 'any',
        'numberposts' => -1,
        'post_status' => 'any'
    );
    $children = get_children( $args );
    foreach ($children as $child){
      echo get_permalink($child->ID);
      echo '<br>';
    }
    echo '</td>';

  echo   '</tr>';
    }
    echo '</table>';
    /* Restore original Post Data */
    wp_reset_postdata();
}?>