<?php
/**
 * Register templates for use on the front-end
 *
 */

if ( class_exists( "Phila_Gov_Register_Program_Templates" ) ){
  $program_templates = new Phila_Gov_Register_Program_Templates();
}

class Phila_Gov_Register_Program_Templates {

  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array( $this, 'register_template_selection_metabox_programs'), 10, 1 );

  }

  function register_template_selection_metabox_programs( $meta_boxes ){
    $meta_boxes[] = array(
      'id'       => 'template_selection',
      'title'    => 'Select Template',
      'pages'    => array( 'programs' ),
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
            'prog_landing_page'  => 'Homepage',
            'phila_one_quarter'    => '1/4 Headings (subpage)',
            'collection_page_v2'   => 'Collection page',
            'covid_guidance'   =>  'Covid guidance',
            'document_finder_v2'   =>  'Document finder',
            'child_index'   => 'List of child pages',
            'prog_off_site' => 'Off-site program',
            'resource_list_v2'  => 'Resource list',
            'stub'              => 'Stub',
            'prog_association'  => 'Subpage with association',
            'timeline'   =>  'Timeline',
            'translated_content'   =>  'Translated content',
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
      'title' => 'Program link',
      'type'  => 'URL',
      'pages' => array( 'programs' ),
      'visible' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'prog_off_site' )
        ),
      ),
      'fields' => array(
        array(
          'type'  => 'URL',
          'id' => 'prog_off_site_link',
          'name' => 'URL',
          'required'  => true,
          'desc'  => 'Once a URL is entered, this page will automatically redirect to this URL. To render this page normally, change the "Off-site program" template.'
        ),
      ),
    );

    $meta_boxes[] = array(
      'id'       => 'phila_header',
      'title'    => 'Program images',
      'pages' => array( 'programs' ),
      'priority' => 'high',
      'visible' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'prog_landing_page' ),
          array( 'phila_template_select', '=', 'prog_off_site' ),
        ),
        'relation'  => 'or'
      ),
      'include' => array(
        'user_role'  => array( 'administrator', 'phila_master_homepage_editor', 'editor' ),
        'relation' => 'or',
      ),
      'fields' => array(
        array(
          'id' => 'prog_header_img',
          'name' => 'Program hero image',
          'type'  => 'image_advanced',
          'max_file_uploads' => 1,
          'desc'  => 'Minimum size 700px by 500px. Used on the Programs + initiatives landing page and hero header.',
          'columns' => 3,
        ),
        array(
          'id' => 'prog_header_img_sub',
          'name' => 'Subpage header image',
          'type'  => 'image_advanced',
          'max_file_uploads' => 1,
          'desc'  => 'Required if subpages exist. Minimum size 700px by 500px.',
          'columns' => 3,
          'hidden' => array(
            'when' => array(
              array( 'phila_template_select', '=', 'prog_off_site' ),
            ),
          ),
        ),
        array(
          'id'  => 'phila_v2_department_logo',
          'name' => 'Program logo',
          'type'  => 'image_advanced',
          'desc'  => 'Optional. Image must be at least 600px wide.',
          'max_file_uploads' => 1,
          'columns' => 3,
          'hidden' => array(
            'when' => array(
              array( 'phila_template_select', '=', 'prog_off_site' )
            ),
          ),
        ),
        array(
          'id'  => 'phila_program_owner_logo',
          'name' => 'Program owner logo',
          'type'  => 'image_advanced',
          'desc'  => 'Optional. Appears in header. Must be white with no background.',
          'max_file_uploads' => 1,
          'columns' => 3,
          'hidden' => array(
            'when' => array(
              array( 'phila_template_select', '=', 'prog_off_site' ),
            ),
            'relation'  => 'or'
          ),
        ),
        array(
          'type' => 'heading',
          'name'  => 'Photo credit',
        ),
        Phila_Gov_Standard_Metaboxes::phila_metabox_title('Name & organization', 'phila_photo_credit', 'E.g.: N. Santos for VISIT PHILADELPHIA™', '60' ),
      )
    );

    $meta_boxes[] = array(
      'id'       => 'phila_sub_association',
      'title'    => 'Association content',
      'pages' => array( 'programs', 'department_page' ),
      'priority' => 'high',
      'revision' => true,
      'visible' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'prog_association' )
        ),
      ),

      'fields' => array(
        array(
          'id'    => 'prog_sub_head',
          'name'  => 'Subheader text',
          'type'  => 'text',
          'desc'  => 'Used in the header to associate this page with its parent. <br /> E.g. A community school',
          'columns' => 6,
        ),
        array(
          'id'   => 'prog_association_img',
          'name' => 'Header image',
          'type' => 'image_advanced',
          'max_file_uploads' => 1,
          'desc'  => 'Minimum size 700px by 500px. Overrides image field from program homepage.',
          'columns' => 3,
        ),

      )
    );

    $meta_boxes[] = array(
      'id'       => 'phila_program',
      'title'    => 'Page content',
      'pages' => array( 'programs', 'department_page', 'service_page' ),
      'priority' => 'high',
      'revision' => true,
      'include' => array( 'custom' => array('is_prog_landing_page', 'is_prog_association', 'is_custom_content')),
      'fields' => array(
        Phila_Gov_Row_Metaboxes::phila_metabox_grid_row(),
      )
    );


    function is_prog_landing_page() {
      if( isset($_GET['post']) === true && phila_get_selected_template($_GET['post']) == 'prog_landing_page')
        return true;
      return false;
    } 
    function is_phila_one_quarter() {
      if( isset($_GET['post']) === true && phila_get_selected_template($_GET['post']) == 'phila_one_quarter')
        return true;
      return false;
    } 
    function is_collection_page_v2() {
      if( isset($_GET['post']) === true && phila_get_selected_template($_GET['post']) == 'collection_page_v2')
        return true;
      return false;
    } 
    function is_covid_guidance() {
      if( isset($_GET['post']) === true && phila_get_selected_template($_GET['post']) == 'covid_guidance')
        return true;
      return false;
    } 
    function is_document_finder_v2() {
      if( isset($_GET['post']) === true && phila_get_selected_template($_GET['post']) == 'document_finder_v2')
        return true;
      return false;
    } 
    function is_child_index() {
      if( isset($_GET['post']) === true && phila_get_selected_template($_GET['post']) == 'child_index')
        return true;
      return false;
    } 
    function is_prog_off_site() {
      if( isset($_GET['post']) === true && phila_get_selected_template($_GET['post']) == 'prog_off_site')
        return true;
      return false;
    } 
    function is_resource_list_v2() {
      if( isset($_GET['post']) === true && phila_get_selected_template($_GET['post']) == 'resource_list_v2')
        return true;
      return false;
    } 
    function is_stub() {
      if( isset($_GET['post']) === true && phila_get_selected_template($_GET['post']) == 'stub')
        return true;
      return false;
    } 
    function is_prog_association() {
      if( isset($_GET['post']) === true && phila_get_selected_template($_GET['post']) == 'prog_association')
        return true;
      return false;
    } 
    function is_timeline() {
      if( isset($_GET['post']) === true && phila_get_selected_template($_GET['post']) == 'timeline')
        return true;
      return false;
    } 
    function is_translated_content() {
      if( isset($_GET['post']) === true && phila_get_selected_template($_GET['post']) == 'translated_content')
        return true;
      return false;
    } 


    $meta_boxes[] = array(
      'title' => 'Stub',
      'pages' => array('programs'),
      'context' => 'after_title',
      'priority' => 'low',
      'visible' => array(
        'when'  => array(
          array('phila_template_select', '=', 'stub'),
        ),
      ),
      'revision' => true,
    
      'fields'  => array(
        array(
          'name' => 'Page source',
          'type'  => 'heading',
        ),
        array(
          'id' => 'phila_stub_source',
          'type' => 'post',
          'post_type' => 'programs',
          'desc'  => 'Display content from the selected page on the front-end.',
          'query_args'  => array(
            'post_status'    => array('publish', 'draft', 'private'),
            'posts_per_page' => - 1,
            'meta_key' => 'phila_template_select',
            'meta_value' => 'stub',
            'meta_compare' => '!=',
            'post_parent__not_in' => array('0')
          ),
        )
      )
    );

    return $meta_boxes;
  }

}
