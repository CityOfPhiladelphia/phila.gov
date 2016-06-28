<?php
/**
 * Register templates for use on the front-end
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
 * @since 0.17.6
 */

if ( class_exists( "Phila_Gov_Admin_Templates" ) ){
  $admin_menu_labels = new Phila_Gov_Admin_Templates();
}

class Phila_Gov_Admin_Templates {

  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array( $this, 'phila_register_template_selection_metabox_departments'), 10, 1 );

 }

 function phila_register_template_selection_metabox_departments( $meta_boxes ){
   $prefix = 'phila_';

  $meta_boxes[] = array(
    'id'       => 'template_selection',
    'title'    => 'Select Page Template',
    'pages'    => array( 'department_page' ),
    'context'  => 'side',
    'priority' => 'low',

    'fields' => array(

      array(
        'desc'  => '',
        'id'    => $prefix . 'template_select',
        'type'  => 'select',
        'class' => 'template-select',
        'clone' => false,
        'placeholder'  => 'Select a template',

        'options' => array(
          'resource_list' => 'Resource List',
          'one_page_department' => 'One Page Department',
        ),
      ),
      array(
      'desc'  => 'Is this a department homepage?',
      'id'    => $prefix . 'department_home_page',
      'type'  => 'checkbox',
      ),
    ),
  );
   return $meta_boxes;
  }

}
