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
      'include' => array(
        'custom' => 'is_additional_content_conditions',
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
      'exclude' => array(
        'custom' => 'is_postdivrich',
      ),
    );
    
    function is_additional_content_conditions() {
      if( isset($_GET['post']) === true && 
        ( phila_get_selected_template($_GET['post']) == 'default' ||
          phila_get_selected_template($_GET['post']) == 'tax_detail' ||
          phila_get_selected_template($_GET['post']) == 'start_process' ) )
        return true;
      return false;
    }

    function is_postdivrich() {
      if( isset($_GET['post']) === true && 
        ( phila_get_selected_template($_GET['post']) == 'topic_page' ||
          phila_get_selected_template($_GET['post']) == 'service_stub' ||
          phila_get_selected_template($_GET['post']) == 'off_site_department' ||
          phila_get_selected_template($_GET['post']) == 'covid_guidance' ||
          phila_get_selected_template($_GET['post']) == 'prog_off_site' ||
          phila_get_selected_template($_GET['post']) == 'translated_content' ) )
        return true;
      return false;
    }

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
