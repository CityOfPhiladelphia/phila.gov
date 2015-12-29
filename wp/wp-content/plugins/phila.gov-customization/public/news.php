<?php
/**
 * Customize the interface for news
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
 */


function get_home_news(){
  $category = get_the_category();
  $url = rwmb_meta('phila_news_url', $args = array('type'=>'url'));
  $contributor = rwmb_meta('phila_news_contributor', $args = array('type'=>'text'));
  $desc = rwmb_meta('phila_news_desc', $args = array('type'=>'textarea'));

  if (!$url == ''){

      echo '<a href="' . $url .'" target="_blank">';
      the_post_thumbnail(  );
      echo '<span class="accessible"> Opens in new window</span></a>';

      echo '<a href="' . $url .'" target="_blank">';
      the_title('<h3>', '</h3>');
      echo '<span class="accessible"> Opens in new window</span></a>';


  }else{
      echo '<a href="' . get_permalink() .'">';
      the_post_thumbnail(  );
      echo '</a>';

      echo '<a href="' . get_permalink().'">';
      the_title('<h3>', '</h3>');
      echo '</a>';

  }

  if (function_exists('rwmb_meta')) {
      if ($contributor === ''){
          echo '<span>' . $category[0]->cat_name . '</span>';
      }else {
          echo '<span>' . $contributor . '</span>';
      }

      echo '<p>' . $desc  . '</p>';

  }
}
/**
* @since 0.5.11
*
* Filter the post_type_link to add the category into url
*
* @package phila-gov_customization
*/

add_filter( 'post_type_link', 'phila_news_link' , 10, 2 );
function phila_news_link( $post_link, $id = 0 ) {

    $post = get_post( $id );

    if ( is_wp_error( $post ) || 'news_post' != $post->post_type || empty( $post->post_name ) )
        return $post_link;

    // Get the genre:
    $terms = get_the_terms( $post->ID, 'category' );

    if( is_wp_error( $terms ) || !$terms ) {
        $cat = 'uncategorised';
    } else {
        $cat_obj = array_pop($terms);
        $cat = $cat_obj->slug;
    }

    return home_url( user_trailingslashit( "news/$cat/$post->post_name" ) );
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

function is_flag( $flag, $atts ) {
  foreach ( $atts as $key => $value )
    if ( $value === $flag && is_int( $key ) ) return true;
  return false;
}


function recent_news_shortcode($atts) {
  global $post;
  $category = get_the_category();
  $a = shortcode_atts( array(
   'posts' => 1,
    0 => 'list',
    'name' => 'News'
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
  'post_type'  => 'news_post',
  'cat' => $current_category,
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
      $output .= '<div class="row"><h2 class="alternate large-24 columns">' . $a['name'] . '</h2></div><div class="row news"><div class="medium-24 columns"><ul class="news-list">';
    }else{
      if ( $a['posts'] == 3 || $a['posts'] == 4 ) {
        $output .= '<div class="row"><div class="equal-height"><div class="row title-push"><h2 class="alternate large-24 columns">' . $a['name'] . '</h2></div>';
      }
    }

    while( $news_loop->have_posts() ) : $news_loop->the_post();
    $post_counter++;

    $contributor = rwmb_meta('phila_news_contributor', $args = array('type'=>'text'));
    $desc = rwmb_meta('phila_news_desc', $args = array('type'=>'textarea'));

    $link = get_permalink();

    if ( is_flag( 'list', $atts ) ){

      $output .= '<li>';

      $output .= '<a href="' . $link .'">';

      $output .=  get_the_post_thumbnail( $post->ID, 'news-thumb', 'class=alignleft small-thumb' );
      $output .= 	'<span class="entry-date small-text">'. get_the_date() . '</span>';
      $output .=  '<h3>' . get_the_title( $post->ID ) . '</h3>';
      $output .= '<span class="small-text">' . wp_strip_all_tags( $desc ) . '</span>';
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
        $output .= '</div><!-- medium-24 columns --> </div>';
      }
      if( $a['posts'] == 3 && ! is_flag( 'list', $atts ) ) {
        //this means we had equal-height applied and must close those divs
        $output .= '</div></div>';
      }

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

  $current_category = $category[0]->cat_ID;

  $args = array( 'posts_per_page' =>1,
  'order'=> 'DESC',
  'orderby' => 'date',
  'post_type'  => 'news_post',
  'cat' => $current_category,
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
