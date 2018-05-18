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
    'context'  => 'advanced',
    'priority' => 'high',

    'fields' => array(
      array(
        'id'    => 'phila_template_select',
        'type'  => 'select',
        'class' => 'template-select',
        'clone' => false,
        'placeholder'  => 'Select a template',

        'options' => array(
          'default'                 => 'Default',
          'homepage_v2'             => 'Department homepage version 2',
          'one_quarter_headings_v2' => '1/4 headings',
          'all_services_v2'         => 'All services',
          'collection_page_v2'      => 'Collection page',
          'contact_us_v2'           => 'Contact us',
          'document_finder_v2'      => 'Document finder',
          'forms_and_documents_v2'  => 'Forms & documents',
          'things-to-do'            => 'Things to do',
          'our-locations'           => 'Our locations',
          'resource_list_v2'        => 'Resource list',
          'staff_directory_v2'      => 'Staff directory',
          'disabled'                => '──────────',
          'off_site_department'     => 'Off-site department',
          'department_homepage'     => 'Department homepage',
          'department_subpage'      => 'Department subpage',
          'programs_initiatives'    => 'Programs and initiatives',
          'resource_list'           => 'Resource list',
          'staff_directory'         => 'Staff directory',
          ),
          'admin_columns' => array(
            'position' => 'after date',
            'title'    => __( 'Template' ),
            'sort'     => true,
          ),
       ),
       array(
        'name'  => 'Should this page appear in the City government directory?',
        'id'    => 'phila_department_home_page',
        'class' => 'hide-from-non-admin',
        'type'  => 'switch',
        'on_label'  => 'Yes',
        'off_label' => 'No',
        'include' => array(
          'user_role'  => array( 'administrator', 'editor', 'primary_department_homepage_editor' ),
        ),
        'hidden' => array(
          'when' => array(
            array('phila_template_select', '=', 'homepage_v2' ),
            array('phila_template_select', '=', 'one_quarter_headings_v2' ),
            array('phila_template_select', '=', 'contact_us_v2' ),
            array('phila_template_select', '=', 'all_services_v2' ),
            array('phila_template_select', '=', 'forms_and_documents_v2' ),
            array('phila_template_select', '=', 'resource_list_v2' ),
            array('phila_template_select', '=', 'staff_directory_v2' ),
          ),
          'relation' => 'or'
        ),
      ),
      array(
        'id' => 'phila_template_select_staff',
        'type' => 'custom_html',
        'std' => 'Visit <a href="/wp-admin/edit.php?post_type=staff_directory">staff members</a> section to add/edit staff.',
        'visible' => array(
          'phila_template_select', 'in', ['staff_directory_v2','staff_directory']
        )
      ),
    ),
  );

    return $meta_boxes;
  }

}
