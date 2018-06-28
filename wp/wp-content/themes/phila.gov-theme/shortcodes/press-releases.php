<?php
/**
*
* Shortcode for displaying filtered press releases
* @param @atts - change category
*
* @package phila-gov_customization
*/

function press_release_shortcode($atts) {

  global $post;
  $a = shortcode_atts( array(
   'name' => 'Press releases',
    'category' => '',
    'tag' => '',
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

  include( locate_template( 'partials/posts/press-release-grid.php' ) );

  wp_reset_postdata();


}
add_action( 'init', 'register_press_release_shortcode' );

function register_press_release_shortcode(){
  add_shortcode( 'press-releases', 'press_release_shortcode' );
}
