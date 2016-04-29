<?php
/**
* @since 0.22.0
*
* Shortcode for displaying news on department homepage
* @param @atts - posts can be set to 1 or 3 in a card-like view
*                list can be set for ul display
*
* @package phila-gov_customization
*/

function press_release_shortcode($atts) {
  global $post;
  $category = get_the_category();
  $a = shortcode_atts( array(
   'posts' => 1,
  ), $atts );

  $current_category = $category[0]->cat_ID;
  $category_slug = $category[0]->slug;

  $args = array( 'posts_per_page' => $a['posts'],
  'order'=> 'DESC',
  'orderby' => 'date',
  'post_type'  => 'press_release',
  'cat' => $current_category
  );

  $press_release_loop = new WP_Query( $args );

  $output = '';

  if( $press_release_loop->have_posts() ) {
    $post_counter = 0;

    $output .= '<div class="large-24 columns"><h2 class="alternate">' . __('Press Releases', 'phila-gov') . '</h2><ul class="no-bullet pan border-bottom-list">';

    while( $press_release_loop->have_posts() ) :
      $press_release_loop->the_post();
      $post_counter++;
      $link = get_permalink();

      $output .= '<li>';

      $output .= '<a href="' . $link .'">' . get_the_title( $post->ID ) . '</a>';

      $output .= 	'<span class="entry-date small-text">'. get_the_date() . '</span>';
      $output .= '</li>';

    endwhile;

    $output .= '</ul>';

    $output .= '</div><a class="see-all-right float-right" href="/press-releases/'. $category_slug . '">All ' . __('Press Releases', 'phila-gov'). '</a>';

    }else {
      $output .= __( 'Please enter at least one press release.', 'phila.gov' );
    }

  wp_reset_postdata();

  return $output;

}
add_action( 'init', 'register_press_release_shortcode' );

function register_press_release_shortcode(){
  add_shortcode( 'press-releases', 'press_release_shortcode' );
}
