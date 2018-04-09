<?php

if ( class_exists("Phila_Gov_Admin_Documents" ) ){
  $phila_document_load = new Phila_Gov_Admin_Documents();
}

 class Phila_Gov_Admin_Documents {

  public function __construct(){
    add_action( 'save_post', array( $this, 'save_document_meta'), 10, 3 );

    add_filter( 'wp_default_editor', array( $this, 'set_default_editor' ) );

    //TODO: move this to the canonical location for metaboxes
    add_filter( 'rwmb_meta_boxes',  array( $this, 'phila_register_attachment_page_meta_boxes' ) );
  }
 /**
  * Save attachment metadata when a document page is saved.
  *
  * @param int $post_id The post ID.
  * @param post $post The post object.
  * @param bool $update Whether this is an existing post being updated or not.
  * @uses get_the_category() https://developer.wordpress.org/reference/functions/get_the_category/
  * @uses wp_set_object_terms() https://codex.wordpress.org/Function_Reference/wp_set_object_terms
  */

  public function save_document_meta( $post_id, $post, $update ) {

    // Check permissions
    if ( !current_user_can( 'edit_page', $post_id ) )
      return;

    //don't run on autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
      return;

    //make sure the metabox plugin exists
    if (function_exists('rwmb_meta')) {
      $documents = rwmb_meta( 'phila_files', $args = array('type' => 'file_advanced'));
    }
    //ensure we have documents attached
    if(!$documents == null) {

      foreach ($documents as $document){
        $current_pdf = $document['ID'];

        $categories = get_the_category($post_id);

         //on save, set the current page category
        foreach ($categories as $category){
          $category_ids[] = $category->cat_ID;
          wp_set_object_terms( $current_pdf, $category_ids, 'category', false );
          wp_add_object_terms( $current_pdf, $category_ids, 'category' );
        }

      }
      $list = get_post_meta($post_id, 'phila_documents');
    }
  }

  public function set_default_editor() {
      $r = 'tinymce';
      return $r;
  }

  function phila_register_attachment_page_meta_boxes( $meta_boxes ){
    $prefix = 'phila_';

    $meta_boxes[] = array(
      'id'       => 'attachment_page_release_date',
      'title'    => 'Release Date',
      'post_types'    => array( 'attachment' ),
      'context'  => 'side',
      'priority' => 'high',


      'fields' => array(
        array(
          'name'  => 'When was this item released?',
          'id'    => $prefix . 'document_page_release_date',
          'type'  => 'date',
          'clone' => false,
          'desc'  => 'If you leave this field blank, the file will show the same release date as the document page it\'s attached to.',
          'js_options' =>  array(
            'dateFormat'=>'MM dd, yy',
            'showTimepicker' => false
          )
        ),
      )
    );
    return $meta_boxes;
  }

}//Phila_Gov_Admin_Documents
