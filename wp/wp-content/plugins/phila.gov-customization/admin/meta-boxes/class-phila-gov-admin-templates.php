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
    'title'    => 'Select Template',
    'pages'    => array( 'department_page' ),
    'context'  => 'advanced',
    'priority' => 'high',

    'fields' => array(
      array(
        'desc'  => '',
        'id'    => 'phila_template_select',
        'type'  => 'select',
        'class' => 'template-select',
        'clone' => false,
        'placeholder'  => 'Select a template',

        'options' => array(
          'default'   => 'Default',
          'off_site_department' => 'Off-site department',
          'one_page_department' => 'One Page department',
          'department_homepage' => 'Department homepage',
          'department_subpage' => 'Department subpage',
          'programs_initiatives' => 'Programs and initiatives',
          'resource_list' => 'Resource list',
          'staff_directory' => 'Staff directory',
          'disabled'  => '──────────',
          'homepage_v2' => 'Department homepage version 2',
          'one_quarter_headings_v2' => '1/4 headings',
          'contact_us_v2' => 'Contact page',
          'all_services_v2' => 'All services',
          'forms_and_documents_v2' => 'Forms & documents',
          'resource_list_v2' => 'Resource list',
          'staff_directory_v2' => 'Staff directory',
          ),
       ),
       array(
        'desc'  => 'Should this page appear in the City government directory?',
        'id'    => $prefix . 'department_home_page',
        'type'  => 'checkbox',
      ),
    ),
  );
   return $meta_boxes;
  }

  //TODO: break these callbacks out into individual functions
  function phila_hide_categories( $conditions ) {

    $conditions['categorydiv'] = array(
      'hidden' => array(
        'when' => array(
          array('phila_get_user_roles_callback()', false ),
        ),
        'relation' => 'or'
      ),
    );

    $conditions['postdivrich'] = array(
      'visible' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'default' ),
          array( 'phila_template_select', '=', '' ),
          array( 'phila_template_select', '=', 'one_quarter_headings_v2' ),
          array( 'phila_template_select', '=', 'process_start' ),
        ),
        'relation' => 'or'
      ),
    );

    $conditions['additional-content'] = array(
      'visible' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'default' ),
          array( 'phila_template_select', '=', 'tax_detail' ),
          array( 'phila_template_select', '=', 'process_start' ),
        ),
        'relation' => 'or'
      ),
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
          'placeholder'  => 'Select a template',
          'id'  => $prefix . 'template_select',
          'type'  => 'select',
          'options' => array(
            'default'   => 'Default',
            'service_stub' => 'Service stub',
            'tax_detail' => 'Tax detail',
            'topic_page' => 'Topic page',
            'process_start' => 'Process start'
          )
        )
      ),
    );
     return $meta_boxes;
  }

}
