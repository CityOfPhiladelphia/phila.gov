<?php
/**
 * Register department templates
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
 */

if ( class_exists( "Phila_Gov_Deparment_Templates" ) ){
  $department_templates = new Phila_Gov_Deparment_Templates();
}

class Phila_Gov_Deparment_Templates {

  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array( $this, 'register_template_selection_metabox_departments'), 10, 1 );

}

function register_template_selection_metabox_departments( $meta_boxes ){

  $meta_boxes[] = array(
    'title'    => 'Select Template',
    'pages'    => array( 'department_page' ),
    'id'       => 'department_template_selection',

    'fields' => Phila_Gov_Standard_Metaboxes::phila_department_template_select_fields()
  );

    return $meta_boxes;

  }

}