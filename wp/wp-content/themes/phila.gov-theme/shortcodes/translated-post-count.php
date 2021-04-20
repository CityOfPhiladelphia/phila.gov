<?php
/**
*
* Shortcode for displaying post count by language 
* @param @atts - language of post to count
*
* @package phila-gov_customization
*/

function translated_posts_count_shortcode($atts) {

  $a = shortcode_atts( array(
    'language' => 'english',
  ), $atts );

  $get_possible_pages = array(
    'posts_per_page'  => -1,
    'order' => 'asc',
    'orderby' => 'title',
    'post_status' => 'any',
    'meta_query' => array(
      array(
          'key'     => 'phila_select_language',
          'value'   => $a['language'],
          'compare' => '=',
      ),
    ),
  );
  $query = new WP_Query( $get_possible_pages );
  $count = $query->found_posts;
  echo $a['language'].' posts: '.$count;

}
add_action( 'init', 'register_translated_posts_count_shortcode' );

function register_translated_posts_count_shortcode(){
  add_shortcode( 'translated-post-count', 'translated_posts_count_shortcode' );
}

?>