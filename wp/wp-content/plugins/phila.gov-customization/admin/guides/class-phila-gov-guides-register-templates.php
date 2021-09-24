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
      'id'       => 'guides_template_selection',
      'title'    => 'Page settings',
      'pages'    => array( 'guides' ),

      'fields' => Phila_Gov_Standard_Metaboxes::phila_guide_template_select_fields()
    );

    $meta_boxes[] = array(
      'id'       => 'phila_guide_calendar',
      'title'    => 'Add calendar?',
      'pages' => array( 'guides' ),
      'hidden' => array(
        'when' => array(
          array('phila_template_select', '!=', 'guide_landing_page'),
        ),
      ),

      'fields' =>
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_calendar_full()

    );

    $meta_boxes[] = array(
      'title' => 'Heading groups',
      'pages' => array( 'guides' ),
      'revision' => true,

      'visible' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'guide_sub_page' ),
        ),
      ),

      'fields' => array(
        array(
          'id' => 'phila_heading_groups',
          'type'  => 'group',
          'clone' => false,

          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_address_fields_simple(),
          ),
        )
      )
    );

    $meta_boxes[] = array(
      'title' => 'Resource groups',
      'pages' => array('guides'),
      'visible'   => array(
        'relation' => 'or',
        'when'  => array(
          array('phila_template_select', '=', 'guide_landing_page'),
          array('phila_template_select', '=', 'guide_resource_page'),
        )
      ),
      'fields' => array(
        array(
          'type'=> 'wysiwyg',
          'name'  => 'Addtional page copy',
          'id'  => 'phila_addtional_page_copy',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
        ),
        Phila_Gov_Standard_Metaboxes::phila_resource_list_v2(),
        array(
          'name'          => 'Print this guide',
          'id'            => 'guide_print_all',
          'type'          => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No',
          'desc'    => 'Displays a "print entire guide" option.',
        )
      )
    );

    return $meta_boxes;
  
  }

}
