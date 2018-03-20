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

    add_filter( 'rwmb_outside_conditions', array( $this, 'post_box_hide_from_non_admins' ), 10, 1 );

    add_filter( 'rwmb_meta_boxes', array( $this, 'register_template_selection_metabox_service_pages' ), 10, 1 );

    add_filter( 'rwmb_meta_boxes', array( $this, 'register_template_selection_metabox_posts' ), 10, 1 );


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
