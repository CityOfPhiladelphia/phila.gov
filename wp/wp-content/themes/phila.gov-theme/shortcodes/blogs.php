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
    'tag' => '',
    'see_all' => ''
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

  if ( !empty($a['tag'] ) ){
    $tag = explode(',', $a['tag']);
  }

  if ( !empty($a['see_all']) ){
    $override_url = $a['see_all'];
  }

  include( locate_template( 'partials/posts/announcements-grid.php' ) );
  include( locate_template( 'partials/posts/post-grid.php' ) );

  wp_reset_postdata();

}

function add_columns_shortcode( $atts, $content = null ) {
  extract(shortcode_atts(array(
    'columns' => '2'
  ), $atts));
  return '<div class="column-content" style="column-count: ' . $columns . '">' . $content . '</div>';
}

function register_posts_shortcode(){
  add_shortcode( 'recent-posts', 'latest_posts_shortcode' );
  add_shortcode( 'add-columns', 'add_columns_shortcode' );
}
