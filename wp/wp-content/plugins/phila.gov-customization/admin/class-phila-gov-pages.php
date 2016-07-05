<?php

if ( class_exists("Phila_Gov_Pages" ) ){
  $phila_pages = new Phila_Gov_Pages();
}

 class Phila_Gov_Pages {

  public function __construct(){

    add_action( 'admin_init', array( $this, 'determine_page_level' ) );

    if ( $this->determine_page_level() ){
      add_filter( 'rwmb_meta_boxes', array($this, 'phila_register_page_meta_boxes' ), 2 );
    }

  }

  function determine_page_level() {

    global $pagenow;

    if ( ( is_admin() && 'post.php' == $pagenow ) ) {

      $post = get_post( $_GET['post'] );

      $post_id = isset( $_GET['post'] ) ? $_GET['post'] : ( isset( $_POST['post_ID'] ) ? $_POST['post_ID'] : false );

      if( ( $post->post_parent == 0 ) ){

        return true;

      }
    }
  }

  function phila_register_page_meta_boxes(){
    $prefix = 'phila_';

    $meta_boxes[] = array(
      'id'       => 'page-display',
      'title'    => 'Display Location',
      'pages'    => array( 'page' ),
      'context'  => 'side',
      'priority' => 'high',

      'fields' => array(
        array(
         'name' => 'Show this page in topic browser? <a href="https://alpha.phila.gov/browse/business/" target="_new">Example</a>',
         'id'   => $prefix . 'show_in_browse',
         'type' => 'radio',
         'std'=> 'no',
         'options' =>  array(
             'no' => 'No',
             'yes' => 'Yes'
         ),
       ),
       array(
        'name' => 'Browse summary',
        'id'   => $prefix . 'page_desc',
        'type' => 'textarea',
      ),
     ),
    );
    return $meta_boxes;
  }

}
