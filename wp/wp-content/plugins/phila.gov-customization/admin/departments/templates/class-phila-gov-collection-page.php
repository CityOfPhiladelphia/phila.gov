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
              'name' => 'Select row',
              'id'   => 'phila_collection_options',
              'desc'  => 'Choose a collection',
              'type' => 'select',
              'placeholder' => 'Select...',
              'options' => array(
                'service' => 'Service pages',
                'program' => 'Program pages',
                'free_text' => 'Free text area',
                'document' => 'Document pages'
                ),
              ),
              array(
                'id'    => 'program_pages',
                'type'  => 'group',

                'visible' => array(
                  'when' => array(
                    array('phila_collection_options', '=', 'program'),
                  ),
                ),

              'fields'  => array(
                array(
                  'name' => '1/4 Heading',
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
                  array('phila_collection_options', '=', 'service'),
                ),
              ),
              'fields'  => array(
                array(
                  'name' => '1/4 Heading',
                  'id'   => 'phila_custom_text_title',
                  'type' => 'text',
                ),
                Phila_Gov_Standard_Metaboxes::phila_v2_service_page_selector( $multiple = true ),
              )
            ),
            array(
              'id' => 'free_text',
              'type' => 'group',
              'visible' => array(
                'when' => array(
                  array('phila_collection_options', '=', 'free_text'),
                ),
              ),
              'clone'  => true,
              'sort_clone' => true,
              'add_button'  => '+ Add section',
              'fields' => array(
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg($section_name = '1/4 Heading'),
              )
            ),
            array(
              'id'    => 'document_pages',
              'type'  => 'group',
              'visible' => array(
                'when' => array(
                  array('phila_collection_options', '=', 'document'),
                ),
              ),
              'fields'  => array(
                array(
                  'name' => '1/4 Heading',
                  'id'   => 'phila_custom_text_title',
                  'type' => 'text',
                ),
                array(
                  'id'    => 'document_page_group',
                  'type'  => 'group',
                  'clone' => true,
                  'sort_clone'  => true,
                  'add_button'  => '+ Add document group',
                  'fields' => array(
                    Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg($section_name = 'Document group title'),
                    Phila_Gov_Standard_Metaboxes::phila_metabox_v2_document_page_selector(),
                  )
                ),

              )
            )
          ),
        ),
      )
    );

    return $meta_boxes;
  }

}
