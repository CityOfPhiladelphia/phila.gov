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
        ),
        array(
          'name' => 'Footnote copy',
          'id'   => 'phila_longform_content_footnote_copy',
          'type' => 'text',
          'limit' => 400,
          'desc'  => '400 character limit.',
        ),
        array(
          'name' => 'Footnote index',
          'id'   => 'phila_longform_content_footnote_index',
          'type' => 'number'
        ),
        array(
          'name' => 'Section copy',
          'id'   => 'phila_longform_content_section_copy',
          'type' => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
        ),
      ),
    );
    return $meta_boxes;
  }
}
