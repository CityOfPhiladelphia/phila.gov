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
    $conditions['#postdivrich'] = array(
      'hidden' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'topic_page' ),
          array( 'phila_template_select', '=', 'service_stub' ),
          array( 'phila_template_select', '=', 'off_site_department' ),
          array( 'phila_template_select', '=', 'covid_guidance' ),
          array( 'phila_template_select', '=', 'prog_off_site' ),
          array( 'phila_template_select', '=', 'translated_content' ),
        ),
        'relation' => 'or'
      ),
    );
    return $conditions;
  }

  function register_template_selection_metabox_service_pages( $meta_boxes ){

    $meta_boxes[] = array(
      'id'       => 'service_template_selection',
      'title'    => 'Service page options',
      'post_types'    => array( 'service_page' ),

      'fields' => Phila_Gov_Standard_Metaboxes::phila_service_template_select_fields()
    );
    return $meta_boxes;
  }

  function register_template_selection_metabox_posts( $meta_boxes ){

    $meta_boxes[] = array(
      'title'    => 'Select Template',
      'post_types'    => array( 'post' ),
      'fields' => Phila_Gov_Standard_Metaboxes::phila_post_template_select_fields()
    );


    return $meta_boxes;

  }

}
