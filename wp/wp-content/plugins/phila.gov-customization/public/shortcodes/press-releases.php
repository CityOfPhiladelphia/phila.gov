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
   'name' => 'Press Releases',
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

    $output .= '<div class="large-24 columns"><h2 class="contrast">' . __('Press Releases', 'phila-gov') . '</h2><ul class="no-bullet pan border-bottom-list">';

    while( $press_release_loop->have_posts() ) :
      $press_release_loop->the_post();
      $post_counter++;
      $link = get_permalink();

      $output .= '<li>';

      $output .=
        '<a href="' . $link .'">
          <div class="clearfix equal-height press-release">
            <div class="float-left equal icon">
              <div class="valign">
                <div class="valign-cell pam">
                  <i class="fa fa-file-text-o fa-2x" aria-hidden="true"></i>
                </div>
              </div>
            </div>
            <div class="equal details">
              <div class="valign">
                <div class="valign-cell pam">
                  <span>' . get_the_title( $post->ID ) . '</span>
                  <span class="entry-date small-text">'. get_the_date() . '</span>
                </div>
              </div>
            </div>
          </div>
        </a>';

      $output .= '</li>';

    endwhile;

    $output .= '</ul>';

    $output .= '</div><a class="see-all-right see-all-arrow float-right" href="/press-releases/'. $category_slug . '" aria-label="See all ' . $a['name'] . '">
      <div class="valign equal-height">
        <div class="see-all-label phm prxs valign-cell equal">See all</div>
        <div class="valign-cell equal">
          <img style="height:28px" src="' . get_stylesheet_directory_uri() . '/img/see-all-arrow.svg" alt="">
        </div>
      </div>
    </a></div>';

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
