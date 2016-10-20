<?php

if ( class_exists('Phila_Gov_Standard_Metaboxes' ) ){
  $phila_standard_metaboxes_load = new Phila_Gov_Standard_Metaboxes();
}

 class Phila_Gov_Standard_Metaboxes {

  public function __construct(){
    add_action('init', array( $this, 'phila_metabox_document_page_selector'), 1 );

  }

  public static function phila_metabox_document_page_selector(){
    $prefix = 'phila_';

    return $meta_var_document_page_selector = array(
      'id'  => $prefix . 'document_page_picker',
      'type'  => 'post',
      'post_type' => 'document',
      'field_type'  => 'select_advanced',
      'desc'  => 'Add document pages. You can narrow your search options by typing in the field above.',
      'query_args'  => array(
        'orderby' => 'title',
        'order' => 'ASC',
        //TODO: only show document pages that match the current category
      ),
      'multiple'  => true,
      'placeholder' => ' ',
      'js_options'  => array(
        'width' => '100%',
        'closeOnSelect' => false,
      )
    );
  }
}
