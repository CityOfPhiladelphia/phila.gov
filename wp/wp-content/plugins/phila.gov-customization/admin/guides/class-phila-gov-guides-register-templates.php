<?php
/**
 * Register templates for use on the front-end
 *
 */

if ( class_exists( "Phila_Gov_Register_Guide_Templates" ) ){
  $guide_templates = new Phila_Gov_Register_Guide_Templates();
}

class Phila_Gov_Register_Guide_Templates {

  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array( $this, 'register_template_selection_metabox_guides'), 10, 1 );

  }

  function register_template_selection_metabox_guides( $meta_boxes ){
    $meta_boxes[] = array(
      'id'       => 'template_selection',
      'title'    => 'Select Template',
      'pages'    => array( 'guides' ),
      'context'  => 'after_title',

      'fields' => array(
        array(
          'desc'  => '',
          'id'    => 'phila_template_select',
          'type'  => 'select',
          'class' => 'template-select',
          'clone' => false,
          'placeholder'  => 'Select a template',
          'required' => true,

          'options' => array(
            'guide_landing_page'  => 'Homepage',
            'guide_sub_page'    => 'Subpage',
          ),
          'admin_columns' => array(
            'position' => 'after date',
            'title'    => __( 'Template' ),
            'sort'     => true,
          ),
        ),
      ),
    );

  $meta_boxes[] = array(
    'title'    => 'Twitter share pre-filled text',
    'pages'    => array( 'guides' ),
    'context'  => 'normal',
    'visible' => array(
      'when' => array(
        array('phila_template_select', '==', 'guide_landing_page'),
      ),
    ),
    'fields'  => array(
      array(
        'type' => 'textarea',
        'required'  => true,
        'id'  => 'phila_social_intent',
        'limit' => 256,
        'desc'  => 'Curate Tweet sharing text. Required. 256 character limit.  A link to this page will be automatically added. <br /> E.g.: Now through Sept. 25, #WelcomingWeek has free events citywide to support Philly being welcoming and inclusive',
      )
    ),
  );

    $meta_boxes[] = array(
      'id'       => 'phila_guide',
      'title'    => 'Page content',
      'pages' => array( 'guides' ),
      'priority' => 'high',
      'revision' => true,

      'fields' => array(
        Phila_Gov_Row_Metaboxes::phila_metabox_grid_row(),
        
      )
    );

    return $meta_boxes;
  }

}
