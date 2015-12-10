<?php
/**
 * Departmental Notices
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila.gov-customization
 * @since 0.19.0
 */

if ( class_exists("PhilaGovDepartmentHomePageNotices" ) ){
  $phila_document_load = new PhilaGovDepartmentHomePageNotices();
}

class PhilaGovDepartmentHomePageNotices {

  public function __construct(){

    add_action( 'init', array( $this, 'register_notices_shortcode' ) );

  }

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

      $output .= '<ul class="no-bullet margin-bottom-50">';

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
        $output .= '<p class="no-margin"><a href="/notices/' . $category[0]->slug .'" class="button alternate full">See All</a></p>';
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
    add_shortcode( 'notices', array($this, 'notices_shortcode') );
  }

}
