<?php
/* Collection page template */

if ( class_exists('Phila_Gov_Collection_Page' ) ){
  $phila_collection_page_load = new Phila_Gov_Collection_Page();
}

class Phila_Gov_Collection_Page {

  public function __construct(){

    add_action( 'rwmb_meta_boxes', array( $this, 'register_collection_page_metaboxes' ), 10, 1 );

  }

  function register_collection_page_metaboxes( $meta_boxes ){

    $meta_boxes[] = array(
      'id'       => 'phila_collection_page',
      'title'    => 'Collection page rows',
      'pages'    => array( 'department_page' ),
      'context'  => 'normal',
      'revision' => true,
      'visible' => array(
        'when' => array(
          array('phila_template_select', '=', 'collection_page_v2'),
        ),
      ),

      'fields' => array(
        array(
          'id'    => 'collection_row',
          'type'  => 'group',
          'clone' => true,
          'sort_clone' => true,
          'add_button'  => '+ Add row',

          'fields' => array(
            array(
              'name' => 'Select rows',
              'id'   => 'phila_collection_options',
              'desc'  => 'Choose a collection',
              'type' => 'select',
              'placeholder' => 'Select...',
              'options' => array(
                'phila_collection_option_service' => 'Service pages',
                'phila_collection_option_program' => 'Program pages',
                'phila_collection_options_textbox' => 'Free text area',
                'phila_collection_options_documents' => 'Document pages'
                ),
              ),
              array(
                'id'    => 'program_pages',
                'type'  => 'group',

                'visible' => array(
                  'when' => array(
                    array('phila_collection_options', '=', 'phila_collection_option_program'),
                  ),
                ),

              'fields'  => array(
                array(
                  'name' => 'Heading',
                  'id'   => 'phila_custom_text_title',
                  'type' => 'text',
                ),
                Phila_Gov_Standard_Metaboxes::phila_program_page_selector( $multiple = true ),
              )
            ),
            array(
              'id'    => 'service_pages',
              'type'  => 'group',
              'visible' => array(
                'when' => array(
                  array('phila_collection_options', '=', 'phila_collection_option_service'),
                ),
              ),
              'fields'  => array(
                array(
                  'name' => 'Heading',
                  'id'   => 'phila_custom_text_title',
                  'type' => 'text',
                ),
                Phila_Gov_Standard_Metaboxes::phila_v2_service_page_selector( $multiple = true),
              )
            )
          ),
        ),
      )
    );

    return $meta_boxes;
  }

}
