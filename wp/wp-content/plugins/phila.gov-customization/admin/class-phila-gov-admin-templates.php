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
    'title'    => 'Choose Template',
    'pages'    => array( 'department_page' ),
    'context'  => 'advanced',
    'priority' => 'high',

    'fields' => array(

      array(
        'desc'  => '',
        'id'    => $prefix . 'template_select',
        'type'  => 'radio',
        'class' => 'template-select',
        'clone' => false,
        'options' => array(
          'homepage' => 'Homepage',
          'subpage' => 'Subpage',
          'resource_list' => 'Grouped List of On-Site Links'
        ),
       ),
    ),
  );

   return $meta_boxes;
  }

}
