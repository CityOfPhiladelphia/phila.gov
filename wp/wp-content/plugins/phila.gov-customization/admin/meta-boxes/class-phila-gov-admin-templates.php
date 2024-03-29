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

    $conditions['#translation-settings'] = array(
      'hidden' => array(
        'when' => array(
          array('phila_get_user_roles_is_admin()', false ),
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
      'context'  => 'after_title',

      'fields' => array(
        array(
          'id'  => 'phila_template_select',
          'type'  => 'select',
          'options' => array(
            'default_v2'  => 'Default',
            'custom_content'  => 'Default (2020)',
            'default'   => 'Generic (old default)',
            'tax_detail' => 'Tax detail',
            'start_process' => 'Start a process',
            'topic_page' => 'Topic page',
            'service_stub' => 'Service stub',
            'vue_app'     => 'Vue app',
          ),
          'admin_columns' => array(
            'position' => 'after date',
            'title'    => __( 'Template' ),
            'sort'     => true,
          ),
        ),
        array(
          'type'  => 'heading',
          'name' => 'Alternate title',
          'visible' => array(
            'when' => array(
              array('phila_template_select', '!=', 'service_stub'),
            ),
          ),
        ),
        array(
          'id'  => 'phila_service_alt_title',
          'type'  => 'text',
          'desc' => 'Enter an alternate title for this service. This will appear in place of the page title on alphabetical lists of services.',
          'size'  => 100,
          'visible' => array(
            'when' => array(
              array('phila_template_select', '!=', 'service_stub'),
            ),
          ),
        )
      ),
    );
    return $meta_boxes;
  }

  function register_template_selection_metabox_posts( $meta_boxes ){

    $meta_boxes[] = array(
      'title'    => 'Select Template',
      'post_types'    => array( 'post' ),
      'context'  => 'after_title',
      'fields' => array(
        array(
          'placeholder'  => 'Select a template',
          'id'  => 'phila_template_select',
          'type'  => 'select',
          'required'  => true,
          'options' => array(
            'post'   => 'Post',
            'press_release' => 'Press Release',
            'action_guide'  => 'Action Guide',
            'action_guide_2'  => 'Action Guide V2'
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
