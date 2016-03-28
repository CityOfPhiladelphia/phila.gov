<?php
/**
* Departmental Notices
*
* @link https://github.com/CityOfPhiladelphia/phila.gov-customization
*
* @package phila-gov_customization
* @since 0.19.0
*/

add_action( 'init', 'register_notices_shortcode' );

function notices_shortcode( $atts ) {
  global $post;
  $category = get_the_category();

  $current_category = $category[0]->cat_ID;

  $args = array( 'posts_per_page' => 5,
    'order'=> 'DESC',
    'orderby' => 'date',
    'post_type'  => 'notices',
    'cat' => $current_category,
  );

  $notices_loop = new WP_Query( $args );
  $counter = 1;
  $output = '';

  if( $notices_loop->have_posts() ) {

    $output .= '<h2 class="alternate">Notices</h2>';
    $output .= '<div class="notices content-block">';

    $output .= '<ul class="no-bullet pan">';

    while( $notices_loop->have_posts() ) : $notices_loop->the_post();
    $counter ++;

      $link = get_permalink();

      $output .= '<li>';
      $output .= '<a href="' . $link .'">';
      $output .= get_the_title();
      $output .= '</a>';
      $output .= '<span class="entry-date small-text">' . get_the_date() . '</span>';
      $output .= '</li>';

    endwhile;
    $output .= '</ul>';

    $output .= '</div>';

    if( $counter > 5 ) {
      $output .= '<a href="/notices/' . $category[0]->slug .'" class="see-all-right float-right">See All</a>';
    }

  }else {
    $output .= '<h2 class="alternate">Notices</h2>';
    $output .= '<div class="notices content-block">';
    $output .= '<p>There are no notices.</p>';
    $output .= '</div>';
  }

    wp_reset_postdata();
    return $output;
}

function register_notices_shortcode(){
  add_shortcode( 'notices', 'notices_shortcode' );
}
