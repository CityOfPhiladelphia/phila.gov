<?php
/**
* @since 0.22.0
*
* Shortcode for displaying posts on department homepage
* @param @atts - posts can be set to 1 or 3 in a card-like view
*                list can be set for ul display
*
* @package phila-gov_customization
*/

add_action( 'init', 'register_posts_shortcode' );


function latest_posts_shortcode($atts) {
  global $post;
  $category = get_the_category();
  $a = shortcode_atts( array(
   'posts' => 1,
    0 => 'list',
    'name' => 'Blog Posts',
 ), $atts );

   $current_category = $category[0]->cat_ID;

   if ( ! is_flag( 'list', $atts ) ){
     if ( $a['posts'] > 4 || $a['posts'] == 2 ){
       $a['posts'] = 3;
     }
   }

  $args = array( 'posts_per_page' => $a['posts'],
  'order'=> 'DESC',
  'orderby' => 'date',
  'post_type'  => 'phila_post',
  'cat' => $current_category,
  );

  $blog_loop = new WP_Query( $args );

  $output = '';

  if( $blog_loop->have_posts() ) {
    $post_counter = 0;

  if ( is_flag ('list', $atts) ) {
      $output .= '<div class="row"><h2 class="alternate large-24 columns">' . $a['name'] . '</h2></div><div class="row news"><div class="medium-24 columns"><ul class="news-list">';
    }else{
      if ( $a['posts'] == 3 || $a['posts'] == 4 ) {
        $output .= '<div class="row equal-height"><h2 class="alternate large-24 columns">' . $a['name'] . '</h2>';
      }
    }

    while( $blog_loop->have_posts() ) : $blog_loop->the_post();
    $post_counter++;

    $desc = rwmb_meta('phila_post_desc', $args = array('type'=>'textarea'));

    $link = get_permalink();

    if ( is_flag( 'list', $atts ) ){
      $output .= '<li>';

      $output .= '<a href="' . get_permalink() .'">';

      $output .=  get_the_post_thumbnail( $post->ID, 'news-thumb', 'class=alignleft small-thumb' );
      $output .= 	'<span class="entry-date small-text">'. get_the_date() . '</span>';
      $output .=  '<h3>' . get_the_title( $post->ID ) . '</h3>';
      $output .= '<span class="small-text">' . $desc . '</span>';
      $output .= '</a>';
      $output .= '</li>';


    }else{

      if( $a['posts'] == 4 ){
        $output .=  '<div class="medium-6 columns">';
      }else{
        $output .=  '<div class="medium-8 columns">';
      }

      //news title on first item
      if ( $post_counter == 1 && $a['posts'] == 1) {
        $output .= '<h2 class="alternate">' . $a['name'] . '</h2>';
      }

      $output .= '<a href="' . get_permalink() .'" class="card">';

      $output .=   get_the_post_thumbnail( $post->ID, 'news-thumb' );

      $output .= '<div class="content-block equal">';

      $output .=  '<h3>' . get_the_title( $post->ID ) . '</h3>';

      $output .= '<p>' . $desc  . '</p>';

      $output .= '</div></a></div>'; //content-block, columns
    }

    endwhile;

    if ( is_flag( 'list', $atts ) ) {
      $output .= '</ul>';
      $output .= '</div></div>';
    }
    if( $a['posts'] == 3 ) {
      //this means we had equal-height applied and must close those divs
      $output .= '</div>';
    }

    }else {
      $output .= __( 'Please enter at least one post.', 'phila.gov' );
    }
    //.news

  wp_reset_postdata();
  return $output;

}

function register_posts_shortcode(){
   add_shortcode( 'recent-posts', 'latest_posts_shortcode' );
}
