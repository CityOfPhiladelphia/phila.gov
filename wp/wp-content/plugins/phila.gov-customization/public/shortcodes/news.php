<?php
/**
 * Customize the interface for news
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
 */

/**
* @since 0.7.0
*
* Shortcode for displaying news on department homepage
* @param @atts - posts can be set to 1 or 3 in a card-like view
*                list can be set for ul display
*
* @package phila-gov_customization
*/

function is_flag( $flag, $atts ) {
  foreach ( $atts as $key => $value )
    if ( $value === $flag && is_int( $key ) ) return true;
  return false;
}


function recent_news_shortcode($atts) {
  global $post;

  $category = get_the_category();

  $current_category_id = $category[0]->cat_ID;
  $category_slug = $category[0]->slug;

  $a = shortcode_atts( array(
   'posts' => 1,
    0 => 'list',
    'name' => 'News',
    'category' => $category_slug,
 ), $atts );


   if ( ! is_flag( 'list', $atts ) ){
     if ( $a['posts'] > 4 || $a['posts'] == 2 ){
       $a['posts'] = 3;
     }
   }

  $args = array( 'posts_per_page' => $a['posts'],
  'order'=> 'DESC',
  'orderby' => 'date',
  'post_type'  => 'news_post',
  'category_name' => $a['category'],
  'tax_query'=> array(
    array(
      'taxonomy' => 'news_type',
      'field'    => 'slug',
			'terms'    => 'notice',
      'operator' => 'NOT IN'
      ),
    ),
  );

  $news_loop = new WP_Query( $args );

  $output = '';

  if( $news_loop->have_posts() ) {
    $post_counter = 0;

  if ( is_flag ( 'list', $atts ) ) {
      $output .= '<div class="large-24 columns"><h2 class="contrast">' . $a['name'] . '</h2><div class="news"><ul>';
    }else{
      $output .= '<div class="large-24 columns"><h2 class="contrast">' . $a['name'] . '</h2><div class="row equal-height">';
    }

    while( $news_loop->have_posts() ) : $news_loop->the_post();
    $post_counter++;

    $contributor = rwmb_meta('phila_news_contributor', $args = array('type'=>'text'));
    $desc = rwmb_meta('phila_news_desc', $args = array('type'=>'textarea'));

    $link = get_permalink();

    $thumbnail = phila_get_thumbnails();

    if ( is_flag( 'list', $atts ) ){

      $output .= '<li class="group">';

      $output .= '<a href="' . $link .'" class="group">';

      $output .=  get_the_post_thumbnail( $post->ID, 'news-thumb', 'class=alignleft small-thumb' );
      $output .= '<div class="pbm"><span class="entry-date small-text">'. get_the_date() . '</span>';
      $output .=  '<h3>' . get_the_title( $post->ID ) . '</h3>';
      $output .= '<span class="small-text">' . wp_strip_all_tags( $desc ) . '</span>';
      $output .= '</div></a>';
      $output .= '</li>';

    }else{

      if($a['posts'] == 3){
        $output .=  '<div class="medium-8 columns">';
      }elseif($a['posts'] == 4){
        $output .=  '<div class="medium-6 columns">';
      }else{
        $output .=  '<div class="medium-24 columns">';
      }

      $output .= '<a href="' . get_permalink() .'" class="card equal">';

      $output .= $thumbnail;

      $output .= '<div class="content-block">';

      $output .=  '<h3>' . get_the_title( $post->ID ) . '</h3>';

      if ( function_exists('rwmb_meta' ) ) {
        if ( $contributor != ''){
          $output .= '<span class="small-text">' . $contributor . '</span>';
        }
        $output .= '<p>' . $desc  . '</p>';
      }
      $output .= '</div></a></div>'; //content-block, columns
    }

    endwhile;

    if ( is_flag( 'list', $atts ) ) {
      $output .= '</ul>';
    //  $output .= '</div>';
    }

    $output .= '</div><a class="see-all-right float-right" href="/news/'. $a['category'] . '">All ' . $a['name'] . '</a></div>';


    }else {
      $output .= __( 'Please enter at least one news story.', 'phila.gov' );
    }
    //.news

  wp_reset_postdata();
  return $output;

}
add_action( 'init', 'register_news_shortcode' );

function register_news_shortcode(){
   add_shortcode( 'recent-news', 'recent_news_shortcode' );
}

/**
* @since 0.7.0
*
* Shortcode for displaying news on department homepage
* @param @atts - posts can be set to 1 or 3 in a card-like view
*                list can be set for ul display
*
* @package phila-gov_customization
*/


function featured_news_shortcode() {
  global $post;
  $category = get_the_category();

  $current_category_id = $category[0]->cat_ID;

  $args = array( 'posts_per_page' =>1,
  'order'=> 'DESC',
  'orderby' => 'date',
  'post_type'  => 'news_post',
  'cat' => $current_category_id,
  'tax_query'=> array(
    array(
      'taxonomy' => 'news_type',
      'field'    => 'slug',
			'terms'    => 'notice',
      'operator' => 'IN'
      ),
    ),
  );

  $featured_news_loop = new WP_Query( $args );

  $output = '';

  if( $featured_news_loop->have_posts() ) {
    $post_counter = 0;

    while( $featured_news_loop->have_posts() ) : $featured_news_loop->the_post();
    $post_counter++;

    $content = get_the_content();

    $url = rwmb_meta('phila_news_url', $args = array('type'=>'url'));
    $contributor = rwmb_meta('phila_news_contributor', $args = array('type'=>'text'));
    $desc = rwmb_meta('phila_news_desc', $args = array('type'=>'textarea'));

    if (!$content == '') {
      $output .= '<div class="story s-box">';
    }else{
      $output .= '<div class="unlinked-story s-box">';
    }

    if (!$url == ''){
      $output .= '<a href="' . $url .'">'; //a tag ends after all the content
      $output .=  get_the_post_thumbnail( $post->ID, 'news-thumb' );
      $output .= '<h3>' . get_the_title( $post->ID ) . '</h3>';

    }else{
      if (!$content === '') {
        $output .= '<a href="' . get_permalink() .'">';//a tag ends after all the content
      }
      $output .=   get_the_post_thumbnail( $post->ID, 'news-thumb' );
      $output .=  '<h3>' . get_the_title( $post->ID ) . '</h3>';
    }

    if ( function_exists('rwmb_meta' ) ) {
      $output .= '<p>' . $desc  . '</p>';
    }
    if (!$content == '') {
      $output .= '</a>';
    }
    $output .= '</div>';

    endwhile;

    }else {
      $output .= __( 'Please enter at least one feature.', 'phila.gov' );
    }

  wp_reset_postdata();
  return $output;

}
add_action( 'init', 'register_featured_news_shortcode' );

function register_featured_news_shortcode(){
  add_shortcode( 'featured-news', 'featured_news_shortcode' );
}
