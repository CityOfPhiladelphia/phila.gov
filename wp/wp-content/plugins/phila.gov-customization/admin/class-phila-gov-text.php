<?php

/**
* Add Text custom meta
*
* @link https://github.com/CityOfPhiladelphia/phila.gov-customization
*
* @package phila-gov_customization
*/

if ( class_exists( "Phila_Gov_Text" ) ){
  $phila_text = new Phila_Gov_Text();
}

class Phila_Gov_Text {

  public function __construct(){
    add_filter( 'rwmb_meta_boxes',  array($this, 'phila_register_meta_boxes') );
  }

  function phila_register_meta_boxes( $meta_boxes ){
    $prefix = 'phila_';

    $summary_toolbar1['toolbar1'] = 'bold, italic, bullist, numlist, link, unlink, outdent, indent, removeformat, pastetext';

    $meta_boxes[] = array(
      'id'       => 'update-history',
      'title'    => 'Update History',
      'pages'    => array( 'text' ),
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
                  'name' => 'Section Number',
                  'id'   => 'phila_text_section_number',
                  'type' => 'text',
                  'size'  =>  20,
                  'columns' => 6,
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
