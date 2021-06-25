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
    'context'  => 'after_title',
    'id'       => 'department_template_selection',

    'fields' => array(
      array(
        'id'    => 'phila_template_select',
        'type'  => 'select',
        'class' => 'template-select',
        'clone' => false,
        'placeholder'  => 'Select a template',
        'options' => array(
          'homepage_v2'             => 'Homepage',
          'default'                 => 'Default',
          'repeating_rows'          => 'Repeating rows',
          'one_quarter_headings_v2' => '1/4 headings',
          'all_programs_v2'         => 'All programs',
          'all_services_v2'         => 'All services',
          'collection_page_v2'      => 'Collection page',
          'contact_us_v2'           => 'Contact us',
          'document_finder_v2'      => 'Document finder',
          'forms_and_documents_v2'  => 'Forms & documents',
          'child_index'             => 'List of child pages',
          'off_site_department'     => 'Off-site department',
          'our-locations'           => 'Our locations',
          'resource_list_v2'        => 'Resource list',
          'staff_directory_v2'      => 'Staff directory',
          'department_stub'         => 'Stub',
          'prog_association'        => 'Subpage with association',
          'things-to-do'            => 'Things to do',
          'timeline'                => 'Timeline',

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
        'class' => 'hide-from-non-admin hide-on-load',
        'type'  => 'switch',
        'on_label'  => 'Yes',
        'off_label' => 'No',
        'include' => array(
          'user_role'  => array( 'administrator', 'editor', 'primary_department_homepage_editor' ),
        ),
        'exclude' => array(
          'custom' => 'not_phila_department_home_page',
        ),
      ),
      array(
        'id' => 'phila_template_select_staff',
        'class' => 'hide-on-load',
        'type' => 'custom_html',
        'std' => 'Visit <a href="/wp-admin/edit.php?post_type=staff_directory">staff members</a> section to add/edit staff.',
        'include' => array(
          'custom' => 'is_staff_directory',
        ),
      ),
      array(
        'id'  => 'full_list',
        'class' => 'hide-on-load',
        'name'  => 'Display a list of every staff member assigned to this owner?',
        'desc'  => 'Choosing this option will display all staff in a table regardless of unit and leadership assignment.',
        'type' => 'switch',
        'on_label'  => 'Yes',
        'off_label' => 'No',
        'include' => array(
          'custom' => 'is_staff_directory',
        ),
      ),
      array(
        'id'  => 'units',
        'class' => 'hide-on-load',
        'name'  => 'Display staff grouped by the following units?',
        'type' => 'unit',
        'include' => array(
          'custom' => 'is_staff_directory',
        ),
      ),
      array(
        'id'  => 'anchor_list',
        'class' => 'hide-on-load',
        'name'  => 'Add anchor links to each unit at the top of this page?',
        'type' => 'switch',
        'on_label'  => 'Yes',
        'off_label' => 'No',
        'include' => array(
          'custom' => 'is_staff_directory',
        ),
      ),
    ),
  );

    return $meta_boxes;

    function is_phila_template_select_staff() {
      if( isset($_GET['post']) === true && 
        ( phila_get_selected_template($_GET['post']) == 'staff_directory_v2' ||
          phila_get_selected_template($_GET['post']) == 'staff_directory' ) )
        return true;
      return false;
    }

    function not_phila_department_home_page() {
      if( isset($_GET['post']) === true && 
        ( phila_get_selected_template($_GET['post']) ==  'homepage_v2' ||
          phila_get_selected_template($_GET['post']) ==  'one_quarter_headings_v2' ||
          phila_get_selected_template($_GET['post']) ==  'contact_us_v2' ||
          phila_get_selected_template($_GET['post']) ==  'all_services_v2' ||
          phila_get_selected_template($_GET['post']) ==  'all_programs_v2' ||
          phila_get_selected_template($_GET['post']) ==  'forms_and_documents_v2' ||
          phila_get_selected_template($_GET['post']) ==  'resource_list_v2' ||
          phila_get_selected_template($_GET['post']) ==  'staff_directory_v2' ) )
        return true;
      return false;
    }

  }

}