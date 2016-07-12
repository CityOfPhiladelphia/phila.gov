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

    add_filter( 'rwmb_outside_conditions', array( $this, 'phila_hide_page_attributes' ), 10, 1 );


 }

 function phila_register_template_selection_metabox_departments( $meta_boxes ){
   $prefix = 'phila_';

  $meta_boxes[] = array(
    'id'       => 'template_selection',
    'title'    => 'Select Page Template',
    'pages'    => array( 'department_page' ),
    'context'  => 'advanced',
    'priority' => 'high',
    'autosave' => 'true',

    'fields' => array(

      array(
        'desc'  => '',
        'id'    => $prefix . 'template_select',
        'type'  => 'select',
        'class' => 'template-select',
        'clone' => false,
        'placeholder'  => 'Select a template',

        'options' => array(
          'default'   => 'Default',
          'off_site_department' => 'Off-site Department',
          'one_page_department' => 'One Page Department',
          'resource_list' => 'Resource List',
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

  function phila_hide_page_attributes( $conditions ) {
    $conditions['categorydiv'] = array(
      'include' => array(
        'when' => array(
          //TODO: Determine way to detect when user has access to multiple categories and apply that here.
          array('user_role', '=', 'administrator' ),
          array('user_role', '=', 'primary_department_homepage_editor' ),
        ),
      ),
      'relation' => 'or'
    );
    return $conditions;
  }

}
