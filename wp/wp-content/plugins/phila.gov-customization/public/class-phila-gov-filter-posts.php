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

    add_action( 'pre_get_posts', array( $this, 'archives_display_phila_post' ) );
    add_filter( 'pre_get_posts',  array( $this, 'phila_filter_notices' ) );


  }

  function archives_display_phila_post( $query ) {
    if ( $query->is_author() || $query->is_tag ()) {
        $query->set( 'post_type', 'phila_post' );
    }
  }

  function phila_filter_notices( $query ) {
    if ( !is_admin() && !is_tax() && is_post_type_archive( 'news_post' ) ) {
      $taxquery = array( 'tax_query', array(
        array(
            'taxonomy' => 'news_type',
            'field' => 'slug',
            'terms' => array('notice'),
            'operator' => 'NOT IN',
            )
          )
        );
      $query->set( 'tax_query', $taxquery );
    }
  }
}
