<?php

/**
* @since 0.19.2
*
* Filter the post_type_link to add the category into notices URL,
* Add year-month-day to phila_post urls
*
* @package phila-gov_customization
*/


if (class_exists("Phila_Gov_Filter_Post_Type_Links") ){
  $phila_gov_tax = new Phila_Gov_Filter_Post_Type_Links();
}
class Phila_Gov_Filter_Post_Type_Links {

  public function __construct(){

    add_filter( 'post_type_link', array($this, 'phila_post_link'), 10, 2 );

  }

  function phila_post_link( $post_link, $id = 0 ) {

    $post = get_post( $id );

    if ( is_wp_error( $post ) || 'phila_post' != $post->post_type || empty( $post->post_name ) ) {
        return $post_link;
      }

    $terms = get_the_terms( $post->ID, 'category' );

    if( is_wp_error( $terms ) || !$terms ) {
      $cat = 'uncategorised';
    } else {
      $cat_obj = array_shift($terms);
      $cat = $cat_obj->slug;
    }

    $post_date = get_the_date('Y-m-d' , $post);

    return home_url( user_trailingslashit( "posts/$cat/$post_date-$post->post_name" ) );

  }
}
