<?php
/**
 * Register templates for use on the front-end
 *
 */

if ( class_exists( "Phila_Gov_Register_Service_Templates" ) ){
  $program_templates = new Phila_Gov_Register_Service_Templates();
}

class Phila_Gov_Register_Service_Templates {

  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array( $this, 'register_template_selection_metabox_services'), 10, 1 );

  }

  function register_template_selection_metabox_services( $meta_boxes ){

    $meta_boxes[] = array(
      'id'       => 'service_questions',
      'title'    => 'Default service content',
      'pages' => array( 'service_page' ),
      'priority' => 'high',
      'visible' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'default' ),
        ),
        'relation'  => 'or'
      ),
      'fields' => array(
        array(
          'type' => 'heading',
          'name'  => 'Who is this service for?',
        ),
        array(
          'id' => 'service_who',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'type' => 'heading',
          'name'  => 'What are the requirements for this service?',
        ),
        array(
          'id' => 'service_requirements',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'type' => 'heading',
          'name'  => 'Where is this located and when is it availble?',
        ),
        array(
          'id' => 'service_where_when',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'type' => 'heading',
          'name'  => 'Are there any costs?',
        ),
        array(
          'id' => 'service_cost',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'type' => 'heading',
          'name'  => 'How can someone get this service?',
        ),
        array(
          'id' => 'service_how',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'name'  => 'Add stepped content?',
          'id'  => 'service_how_stepped_select',
          'type'  => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No'
        ),
        array(
          'id' => 'phila_stepped_content',
          'type' => 'group',
          'visible' => array('phila_stepped_select', true),

          'fields'  => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_ordered_content(),
            
          )
        ),
        array(
          'type' => 'heading',
          'name'  => 'Renewal requirements',
        ),
        array(
          'id' => 'service_renewal_requirements',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
      )
    );
    return $meta_boxes;
  }

}
