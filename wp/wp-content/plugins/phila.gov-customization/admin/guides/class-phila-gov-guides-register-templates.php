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
            'guide_resource_page' => 'Resource page'
          ),
          'admin_columns' => array(
            'position' => 'after date',
            'title'    => __( 'Template' ),
            'sort'     => false,
          ),
        ),
        array(
          'name'  => 'Social intent',
          'type' => 'textarea',
          'required'  => true,
          'id'  => 'phila_social_intent',
          'limit' => 256,
          'desc'  => 'Curate Tweet sharing text. Required. 256 character limit.  A link to this page will be automatically added. <br /> E.g.: Now through Sept. 25, #WelcomingWeek has free events citywide to support Philly being welcoming and inclusive',
          'include' => array(
            'custom' => 'is_guide_landing_page',
          ),
        ),
        array(
          'id' => 'guide_page_icon',
          'type' => 'text',
          'name'  => 'Page icon',
          'desc'  => 'Choose a <a href="https://fontawesome.com/icons?d=gallery" target="_blank">Font Awesome</a> icon to represent this page. E.g.: fas fa-bell.',
          'columns' => 6,
        ),
        array(
          'id' => 'guide_landing_page_title',
          'type' => 'text',
          'name'  => 'Name of this page',
          'desc'  => 'Defaults to "Overview" if left empty',
          'columns' => 6,
          'std' => 'Overview',
          'include' => array(
            'custom' => 'is_guide_landing_page',
          ),
        ),
        array(
          'name'          => 'Color picker',
          'id'            => 'guide_color_picker',
          'type'          => 'color',
          'descripion'    => 'Choose a color to represent this page in navigation',
          'columns' => 6,
          'js_options'    => array(
            'palettes' => array( '#26cef8', '#58c04d', '#9400c6', '#0f4d90', '#dd2662' )
          ),
          'exclude' => array(
            'custom' => 'is_guide_landing_page',
          ),
        ),
      ),
    );

    $meta_boxes[] = array(
      'id'       => 'phila_guide_calendar',
      'title'    => 'Add calendar?',
      'pages' => array( 'guides' ),
      'include' => array(
        'custom' => 'is_guide_landing_page',
      ),
      'fields' =>
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_calendar_full()

    );

    $meta_boxes[] = array(
      'title' => 'Heading groups',
      'pages' => array( 'guides' ),
      'revision' => true,
      'include' => array(
        'custom' => 'is_heading_groups_guides',
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

    function is_guide_landing_page() {
      if( isset($_GET['post']) === true && 
        ( phila_get_selected_template($_GET['post']) == 'guide_landing_page' ) )
        return true;
      return false;
    }

    function is_heading_groups_guides() {
      if( isset($_GET['post']) === true && 
        ( phila_get_selected_template($_GET['post']) == 'guide_sub_page' ) )
        return true;
      return false;
    }

    function is_resource_groups() {
      if( isset($_GET['post']) === true && 
        ( phila_get_selected_template($_GET['post']) == 'guide_landing_page' ||
          phila_get_selected_template($_GET['post']) == 'guide_resource_page' ) )
        return true;
      return false;
    }

    $meta_boxes[] = array(
      'title' => 'Resource groups',
      'pages' => array('guides'),
      'include' => array(
        'custom' => 'is_resource_groups',
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
