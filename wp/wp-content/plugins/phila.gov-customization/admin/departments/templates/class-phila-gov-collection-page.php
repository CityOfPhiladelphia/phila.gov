<?php
/* Collection page template */

if ( class_exists('Phila_Gov_Collection_Page' ) ){
  $phila_collection_page_load = new Phila_Gov_Collection_Page();
}

class Phila_Gov_Collection_Page {

  public function __construct(){

    add_action( 'init', array( $this, 'register_collection_page_metaboxes' ) );

  }
  function register_collection_page_metaboxes(){
    
  }

}
