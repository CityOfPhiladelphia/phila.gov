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

    add_filter( 'rwmb_meta_boxes', array( $this, 'register_template_selection_metabox_departments'), 10, 1 );

    add_filter( 'rwmb_outside_conditions', array( $this, 'post_box_hide_from_non_admins' ), 10, 1 );

    add_filter( 'rwmb_meta_boxes', array( $this, 'register_template_selection_metabox_service_pages' ), 10, 1 );

    add_filter( 'rwmb_meta_boxes', array( $this, 'register_template_selection_metabox_posts' ), 10, 1 );


 }

 function register_template_selection_metabox_departments( $meta_boxes ){

  $meta_boxes[] = array(
    'title'    => 'Select Template',
    'post_types'    => array( 'department_page' ),
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
          ),
       ),
       array(
        'name'  => 'Should this page appear in the City government directory?',
        'id'    => 'phila_department_home_page',
        'type'  => 'switch',
        'on_label'  => 'Yes',
        'off_label' => 'No',
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
        'visible' => array('phila_template_select', 'in', ['staff_directory_v2','staff_directory'])
      )
    ),
  );
   return $meta_boxes;
  }

  //TODO: break these callbacks out into individual functions
  function post_box_hide_from_non_admins( $conditions ) {

    $conditions['#categorydiv'] = array(
      'hidden' => array(
        'when' => array(
          array('phila_get_user_roles_callback()', false ),
        ),
      ),
    );

    $conditions['.additional-content'] = array(
      'visible' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'default' ),
          array( 'phila_template_select', '=', 'tax_detail' ),
          array( 'phila_template_select', '=', 'start_process' ),
        ),
        'relation' => 'or'
      ),
    );
    //hide submit div when user is a readonly user
    $conditions['#submitdiv'] = array(
      'hidden' => array(
        'when' => array(
          array('phila_user_read_only()', true ),
        ),
      ),
    );
    return $conditions;
  }

  function register_template_selection_metabox_service_pages( $meta_boxes ){

    $meta_boxes[] = array(
      'id'       => 'service_template_selection',
      'title'    => 'Select Template',
      'post_types'    => array( 'service_page' ),
      'context'  => 'advanced',
      'priority' => 'high',
      'fields' => array(
        array(
          'placeholder'  => 'Select a template',
          'id'  => 'phila_template_select',
          'type'  => 'select',
          'options' => array(
            'default'   => 'Default',
            'tax_detail' => 'Tax detail',
            'start_process' => 'Start a process',
            'topic_page' => 'Topic page',
            'service_stub' => 'Service stub'
          ),
          'admin_columns' => array(
            'position' => 'after date',
            'title'    => __( 'Template' ),
            'sort'     => true,
          ),
        ),
      ),
    );
     return $meta_boxes;
  }

  function register_template_selection_metabox_posts( $meta_boxes ){

    $meta_boxes[] = array(
      'title'    => 'Select Template',
      'post_types'    => array( 'post' ),
      'context'  => 'advanced',
      'priority' => 'high',
      'fields' => array(
        array(
          'placeholder'  => 'Select a template',
          'id'  => 'phila_template_select',
          'type'  => 'select',
          'required'  => true,
          'options' => array(
            'post'   => 'Post',
            'press_release' => 'Press Release',
            'action_guide'  => 'Action Guide'
          ),
          'admin_columns' => array(
            'position' => 'after date',
            'title'    => __( 'Template' ),
            'sort'     => true,
          ),
        ),
      ),
    );


    return $meta_boxes;

  }

}
