<?php

/**
* Add Longform Content custom meta
*
* @link https://github.com/CityOfPhiladelphia/phila.gov-customization
*
* @package phila-gov_customization
*/

if ( class_exists( "Phila_Gov_Longform_Content" ) ){
  $phila_longform_content = new Phila_Gov_Longform_Content();
}

class Phila_Gov_Longform_Content {

  public function __construct(){
    add_filter( 'rwmb_meta_boxes',  array($this, 'phila_register_meta_boxes') );
  }

  function phila_register_meta_boxes( $meta_boxes ){
    $prefix = 'phila_';

    $summary_toolbar1['toolbar1'] = 'bold, italic, bullist, numlist, link, unlink, outdent, indent, removeformat, pastetext';

    $meta_boxes[] = array(
      'id'       => 'longform_content',
      'title'    => 'Longform content section',
      'pages'    => array( 'longform_content' ),
      'priority' => 'high',
      'context'  => 'normal',

      'fields' => array(
        array(
          'name' => 'Section Title',
          'id'   => 'phila_longform_content_section_title',
          'type' => 'text',
          'size'  =>  75,
          'columns' => 6,
        ),
        array(
          'name' => 'Section Number',
          'id'   => 'phila_longform_content_section_number',
          'type' => 'text',
          'size'  =>  20,
          'columns' => 6,
        ),
        array(
          'name' => 'Footnote copy',
          'id'   => 'phila_longform_content_footnote_copy',
          'type' => 'text',
          'size'  =>  75,
          'limit' => 400,
          'desc'  => '400 character limit.',
          'columns' => 6,
        ),
        array(
          'name' => 'Footnote index',
          'id'   => 'phila_longform_content_footnote_index',
          'type' => 'number',
          'columns' => 6,
        ),
        array(
          'name' => 'Section copy',
          'id'   => 'phila_longform_content_section_copy',
          'type' => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
        ),
      ),
    );

    $meta_boxes[] = array(
      'id'       => 'update-history',
      'title'    => 'Update History',
      'pages'    => array( 'longform_content' ),
      'context'  => 'normal',
      'priority' => 'high',
      'revision' => true,

      'fields' => array(
        array(
          'id'  => 'phila_longform_document_update_history',
          'type' => 'group',
  
          'fields' => array(
            array(
              'id'  => 'phila_longform_document_update',
              'type' => 'group',
              'clone'  => true,
              'sort_clone' => true,
              'add_button' => '+ Add an update',
              'fields' => array(
                array(
                  'name' => 'Update information',
                  'id'   => 'phila_update_information',
                  'type' => 'text',
                  'limit' => 200,
                  'required'  => true,
                  'desc'  => 'Required. 200 character limit.',
                ),
                array(
                  'name' => 'Update date',
                  'id'   => 'phila_update_date',
                  'type'  => 'date',
                  'class' =>  'press-release-date',
                  'size'  =>  30,
                  'required'  => true,
                  'js_options' =>  array(
                    'dateFormat'=>'MM dd, yy',
                    'showTimepicker' => false
                  )
                ),
              ),
            ),
          ),
        )
      )
    );

    return $meta_boxes;
  }
}
