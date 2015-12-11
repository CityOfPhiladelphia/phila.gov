<?php

if ( class_exists("Phila_Gov_Admin_Documents" ) ){
  $phila_document_load = new Phila_Gov_Admin_Documents();
}

 class Phila_Gov_Admin_Documents {

  public function __construct(){
    add_action( 'save_post', array( $this, 'save_document_meta'), 10, 3 );

    add_action( 'rwmb_after', array( $this, 'load_document_media_js'), 1000 );

    add_filter( 'wp_default_editor', array( $this, 'set_default_editor' ) );
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
        $current_pdf = $document[ID];

        $categories = get_the_category($post_id);

         //on save, set the current page category
        foreach ($categories as $category){
          $category_ids[] = $category->cat_ID;
          wp_set_object_terms( $current_pdf, $category_ids, 'category', false );
          wp_add_object_terms( $current_pdf, $category_ids, 'category' );
        }

        $types =  get_the_terms( $post_id, 'document_type' );

        foreach ($types as $type){
          $type_ids[] = $type->term_id;
          wp_set_object_terms( $current_pdf, $type_ids, 'document_type', false );
        }
      }
      $list = get_post_meta($post_id, 'phila_documents');
    }
  }

  public function load_document_media_js(){
    global $post_type;
    if( 'document' == $post_type ) {
    	wp_enqueue_script( 'admin-document-script', plugins_url( 'js/admin-documents.js' , __FILE__, array('jQuery') ) );
    }
  }

  public function set_default_editor() {
      $r = 'tinymce';
      return $r;
  }

}//Phila_Gov_Admin_Documents
