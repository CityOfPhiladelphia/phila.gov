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
      'context'  => 'after_title',

      'fields' => array(
        array(
          'name'  => 'Select template',
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
            'sort'     => false,
          ),
        ),
        array(
          'hidden' => array(
            'when' => array(
              array('phila_template_select', '=', 'guide_sub_page'),
            ),
          ),
          'name'  => 'Social intent',
          'type' => 'textarea',
          'required'  => true,
          'id'  => 'phila_social_intent',
          'limit' => 256,
          'desc'  => 'Curate Tweet sharing text. Required. 256 character limit.  A link to this page will be automatically added. <br /> E.g.: Now through Sept. 25, #WelcomingWeek has free events citywide to support Philly being welcoming and inclusive',
        ),
  
        array(
          'id' => 'guide_page_icon',
          'type' => 'text',
          'name'  => 'Page icon',
          'desc'  => 'Choose a <a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a> icon to represent this page. E.g.: fas fa-bell.',
          'columns' => 6,
        ),
        array(
          'name'          => 'Color picker',
          'id'            => 'guide_color',
          'type'          => 'color',
          'descripion'    => 'Choose a color to represent this page in navigation',
          'columns' => 6,
          'alpha_channel' => false,
          'hidden' => array(
            'when' => array(
              array('phila_template_select', '=', 'guide_landing_page'),
            ),
          ),
          'js_options'    => array(
              'palettes' => array( '#26cef8', '#58c04d', '#9400c6', '#0f4d90', '#dd2662' )
            ),
          ),
      ),
    );


    $meta_boxes[] = array(
      'id'       => 'phila_guide_calendar',
      'title'    => 'Add calendar?',
      'pages' => array( 'guides' ),
      'hidden' => array(
        'when' => array(
          array('phila_template_select', '=', 'guide_sub_page'),
        ),
      ),

      'fields' =>
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_calendar_full()

    );

    return $meta_boxes;
  }

}
