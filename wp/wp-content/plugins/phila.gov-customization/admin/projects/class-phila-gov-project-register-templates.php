<?php
/**
 * Register project templates
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
 */

if ( class_exists( "Phila_Gov_Project_Templates" ) ){
  $department_templates = new Phila_Gov_Project_Templates();
}

class Phila_Gov_Project_Templates {

  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array( $this, 'register_template_selection_metabox_projects'), 10, 1 );

}

function register_template_selection_metabox_projects( $meta_boxes ){

  $meta_boxes[] = array(
    'title'    => 'Select Template',
    'pages'    => array( 'project' ),
    'context'  => 'after_title',
    'id'       => 'project_template_selection',

    'fields' => array(
      array(
        'id'    => 'phila_template_select',
        'type'  => 'select',
        'class' => 'template-select',
        'clone' => false,
        'placeholder'  => 'Select a template',
        'options' => array(
          'project_homepage'    => 'Homepage',
          'project_timeline'     => 'Timeline',
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
