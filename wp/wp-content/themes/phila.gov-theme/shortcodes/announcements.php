<?php
/**
*
* Shortcode for displaying filtered announcements
* @param @atts - change category
*
* @package phila-gov_customization
*/

function announcement_shortcode($atts) {

  global $post;
  $a = shortcode_atts( array(
   'name' => 'Press releases',
    'category' => '',
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
  
  include locate_template('partials/posts/announcements-grid');
  include( locate_template( 'partials/posts/press-release-grid.php' ) );


  wp_reset_postdata();


}
add_action( 'init', 'register_announcement_shortcode' );

function register_announcement_shortcode(){
  add_shortcode( 'press-releases', 'announcement_shortcode' );
}
