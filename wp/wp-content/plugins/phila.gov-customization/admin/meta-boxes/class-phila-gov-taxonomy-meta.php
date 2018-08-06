<?php
/* Register taxonomy metaboxes */

if ( class_exists('Phila_Gov_Taxonomy_Meta' ) ){
  $phila_taxonomy_meta_load = new Phila_Gov_Taxonomy_Meta();
}

class Phila_Gov_Taxonomy_Meta {

  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array($this, 'phila_register_taxonomy_meta_boxes' ), 100 );

  }
  function phila_register_taxonomy_meta_boxes( $meta_boxes ){
    $meta_boxes[] = array(
      'title'      => 'Metadata',
      'taxonomies' => 'category', // List of taxonomies. Array or string

      'fields' => array(
        array(
          'desc' => 'Prevent this category from appearing on the user-facing site?',
          'id'   => 'hidden',
          'type' => 'checkbox',
        ),
        array(
          'type'  => 'group',
          'name' => 'Unit information',
          'id'  => 'unit',
          'clone' => true,
          'add_button'  => '+ Add another unit',
          'fields'  => array(
            array(
              'name' => 'Unit name',
              'id'   => 'name',
              'type' => 'text',
              'placeholder' => 'E.g. Director\'s Office'
            ),
            Phila_Gov_Standard_Metaboxes::phila_metabox_post_picker('Assocated page (optional)', 'url', 'department_page' ),
          ),
        )
      ),
    );
    return $meta_boxes;
  }

}
