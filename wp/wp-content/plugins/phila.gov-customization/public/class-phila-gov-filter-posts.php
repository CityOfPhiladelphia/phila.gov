<?php
/**
 * Use phila_post instead of WP posts for archives
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
 * @since 0.22.0
 */

if ( class_exists("Phila_Gov_Filter_Posts" ) ){
  $phila_document_load = new Phila_Gov_Filter_Posts();
}

class Phila_Gov_Filter_Posts {

  public function __construct(){

    add_action( 'pre_get_posts', array($this, 'archives_display_phila_post' ) );

  }

  function archives_display_phila_post( $query ) {
    if ( $query->is_author() || $query->is_tag ()) {
        $query->set( 'post_type', 'phila_post' );
    }
  }
}
