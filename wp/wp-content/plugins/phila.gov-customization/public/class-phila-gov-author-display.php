<?php
/**
 * Departmental Notices
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
 * @since 0.19.0
 */

if ( class_exists("Phila_Gov_Custom_Posts" ) ){
  $phila_document_load = new Phila_Gov_Custom_Posts();
}

class Phila_Gov_Custom_Posts {

  public function __construct(){

    add_action( 'pre_get_posts', array($this, 'author_page_include_phila_post' ) );

  }

  function author_page_include_phila_post( $query ) {
    if ( $query->is_author() ) {
        $query->set( 'post_type', 'phila_post' );
    }
  }
}
function filter_post_link($permalink, $post) {
    if ($post->post_type != 'post')
        return $permalink;
    return 'posts'.$permalink;
}
