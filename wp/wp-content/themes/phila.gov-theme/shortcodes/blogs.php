<?php
/**
*
* Shortcode for displaying filtered posts
* @param @atts - category can be set to display from a different category, accepts cat ID
*
*/

add_action( 'init', 'register_posts_shortcode' );

function latest_posts_shortcode( $atts ) {
  global $post;
  $a = shortcode_atts( array(
    'name' => 'Posts',
    'category' => '',
    'tag' => ''
  ), $atts );

  $category = array();

  if ($a['category'] != ''){
    //get page category
    $category = array(get_category($a['category'])->term_id);
  } else {
    $cats = get_the_category();
    foreach ($cats as $cat) {
      array_push($category, $cat->term_id);
    }
  }

  if ($a['tag'] != ''){
    $tag = $a['tag'];
  }

  include( locate_template( 'partials/posts/announcements-grid.php' ) );
  include( locate_template( 'partials/posts/post-grid.php' ) );

  wp_reset_postdata();

}

function register_posts_shortcode(){
   add_shortcode( 'recent-posts', 'latest_posts_shortcode' );
}
