<?php

/**
* @since 0.19.2
*
* Filter the post_type_link to add the category into notices URL, very similar to /news
*
* @package phila-gov_customization
*/


if (class_exists("Phila_Gov_CPT_Notices") ){
  $phila_gov_tax = new Phila_Gov_CPT_Notices();
}
class Phila_Gov_CPT_Notices {

  public function __construct(){
    add_filter( 'post_type_link', array($this, 'phila_notices_link'), 10, 2 );
  }
  function phila_notices_link( $post_link, $id = 0 ) {

    $post = get_post( $id );

    if ( is_wp_error( $post ) || 'notices' != $post->post_type || empty( $post->post_name ) )
        return $post_link;

    // Get the genre:
    $terms = get_the_terms( $post->ID, 'category' );

    if( is_wp_error( $terms ) || !$terms ) {
        $cat = 'uncategorised';
    } else {
        $cat_obj = array_pop($terms);
        $cat = $cat_obj->slug;
    }

    return home_url( user_trailingslashit( "notices/$cat/$post->post_name" ) );
  }
}
