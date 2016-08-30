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

    add_filter( 'rwmb_outside_conditions', array( $this, 'phila_hide_categories' ), 10, 1 );

    add_filter( 'rwmb_meta_boxes', array( $this, 'phila_register_template_selection_metabox_wp_pages' ), 10, 1 );

 }

 function phila_register_template_selection_metabox_departments( $meta_boxes ){
  $prefix = 'phila_';

  $meta_boxes[] = array(
    'id'       => 'template_selection',
    'title'    => 'Select Page Template',
    'pages'    => array( 'department_page' ),
    'context'  => 'advanced',
    'priority' => 'high',

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
          'department_homepage' => 'Department Homepage',
          'department_subpage' => 'Department Subpage',
          'programs_initiatives' => 'Programs and Initiatives',
          'resource_list' => 'Resource List',
          'staff_directory' => 'Staff Directory',
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


  function phila_hide_categories( $conditions ) {

    $conditions['categorydiv'] = array(
      'hidden' => array(
        'when' => array(
          array('phila_get_user_roles_callback()', false ),
        ),
      ),
      'relation' => 'or'
    );

    $conditions['postdivrich'] = array(
      'hidden' => array(
        'when' => array(
          array('phila_template_select', '=', 'tax_detail' ),
        ),
      ),
      'relation' => 'or'
    );

    return $conditions;
  }

  function phila_register_template_selection_metabox_wp_pages( $meta_boxes ){
    $prefix = 'phila_';

    $meta_boxes[] = array(
      'id'       => 'page_template_selection',
      'title'    => 'Select Template',
      'pages'    => array( 'page', 'service_page' ),
      'context'  => 'advanced',
      'priority' => 'high',

      'fields' => array(
        array(
          'id'  => $prefix . 'template_select',
          'type'  => 'select',
          'options' => array(
            'default'   => 'Default',
            'service_stub' => 'Service Stub',
            'tax_detail' => 'Tax Detail'
          )
        )
      ),
    );
     return $meta_boxes;
  }

}
