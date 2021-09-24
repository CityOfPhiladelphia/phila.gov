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

      'fields' => Phila_Gov_Standard_Metaboxes::phila_program_template_select_fields()
    );

    $meta_boxes[] = array(
      'id' => 'program_link',
      'title' => 'Program link',
      'type'  => 'URL',
      'pages' => array( 'programs' ),
      'include' => array(
        'custom' => 'is_program_link',
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

    function is_program_link() {
      if( isset($_GET['post']) === true && 
        ( phila_get_selected_template($_GET['post']) == 'prog_off_site' ) )
        return true;
      return false;
    }

    $meta_boxes[] = array(
      'id'       => 'phila_header',
      'title'    => 'Program images',
      'pages' => array( 'programs' ),
      'priority' => 'high',
      'include' => array(
        'user_role'  => array( 'administrator', 'phila_master_homepage_editor', 'editor' ),
        'custom' => 'is_phila_header',
        'relation' => 'and',
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
          'exclude' => array(
            'custom' => 'is_prog_off_site',
          ),
        ),
        array(
          'id'  => 'phila_v2_department_logo',
          'name' => 'Program logo',
          'type'  => 'image_advanced',
          'desc'  => 'Optional. Image must be at least 600px wide.',
          'max_file_uploads' => 1,
          'columns' => 3,
          'exclude' => array(
            'custom' => 'is_prog_off_site',
          ),
        ),
        array(
          'id'  => 'phila_program_owner_logo',
          'name' => 'Program owner logo',
          'type'  => 'image_advanced',
          'desc'  => 'Optional. Appears in header. Must be white with no background.',
          'max_file_uploads' => 1,
          'columns' => 3,
          'exclude' => array(
            'custom' => 'is_prog_off_site',
          ),
        ),
        array(
          'type' => 'heading',
          'name'  => 'Photo credit',
        ),
        Phila_Gov_Standard_Metaboxes::phila_metabox_title('Name & organization', 'phila_photo_credit', 'E.g.: N. Santos for VISIT PHILADELPHIA™', '60' ),
      )
    );

    function is_phila_header() {
      if( isset($_GET['post']) === true && 
        ( phila_get_selected_template($_GET['post']) == 'prog_landing_page' ||
          phila_get_selected_template($_GET['post']) == 'prog_off_site' ) )
        return true;
      return false;
    }

    function is_prog_off_site() {
      if( isset($_GET['post']) === true && 
        ( phila_get_selected_template($_GET['post']) == 'prog_off_site' ) )
        return true;
      return false;
    }

    $meta_boxes[] = array(
      'id'       => 'phila_sub_association_content',
      'title'    => 'Association content',
      'pages' => array( 'programs', 'department_page' ),
      'priority' => 'high',
      'revision' => true,
      'include' => array(
        'custom' => 'is_phila_sub_association',
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

    function is_phila_sub_association() {
      if( isset($_GET['post']) === true && 
        ( phila_get_selected_template($_GET['post']) == 'prog_association' ) )
        return true;
      return false;
    }

    $meta_boxes[] = array(
      'id'       => 'phila_program',
      'title'    => 'Page content',
      'pages' => array( 'programs', 'department_page', 'service_page' ),
      'priority' => 'high',
      'revision' => true,
      'hidden' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'all_programs_v2' ),
          array( 'phila_template_select', '=', 'all_services_v2' ),
          array( 'phila_template_select', '=', 'child_index' ),
          array( 'phila_template_select', '=', 'collection_page_v2' ),
          array( 'phila_template_select', '=', 'contact_us_v2' ),
          array( 'phila_template_select', '=', 'covid_guidance' ),
          array( 'phila_template_select', '=', 'department_stub' ),
          array( 'phila_template_select', '=', 'forms_and_documents_v2' ),
          array( 'phila_template_select', '=', 'homepage_v2' ),
          array( 'phila_template_select', '=', 'off_site_department' ),
          array( 'phila_template_select', '=', 'one_quarter_headings_v2' ),
          array( 'phila_template_select', '=', 'prog_off_site' ),
          array( 'phila_template_select', '=', 'resource_list_v2' ),
          array( 'phila_template_select', '=', 'staff_directory_v2' ),
          array( 'phila_template_select', '=', 'tax_detail' ),
          array( 'phila_template_select', '=', 'timeline' ),
          array( 'phila_template_select', '=', 'translated_content' )
        ),
        'relation' => 'or',
      ),
      'visible' => array(
        'when'  => array(
          array( 'phila_template_select', '=', 'prog_association' ),
          array( 'service_template_selection', '=', 'custom_content' )
        ),
        'relation' => 'or',
      ),
      'fields' => array(
        Phila_Gov_Row_Metaboxes::phila_metabox_grid_row(),
      )
    );

    function is_phila_program() {
      if( isset($_GET['post']) === true && 
        ( phila_get_selected_template($_GET['post']) == 'child_index' ||
          phila_get_selected_template($_GET['post']) == 'collection_page_v2' ||
          phila_get_selected_template($_GET['post']) == 'contact_us_v2' ||
          phila_get_selected_template($_GET['post']) == 'custom_content' ||
          phila_get_selected_template($_GET['post']) == 'default' ||
          phila_get_selected_template($_GET['post']) == 'default_v2' ||
          phila_get_selected_template($_GET['post']) == 'document_finder_v2' ||
          phila_get_selected_template($_GET['post']) == 'homepage_v2' ||
          phila_get_selected_template($_GET['post']) == 'one_quarter_headings_v2' ||
          phila_get_selected_template($_GET['post']) == 'our-locations' ||
          phila_get_selected_template($_GET['post']) == 'phila_one_quarter' ||
          phila_get_selected_template($_GET['post']) == 'prog_association' ||
          phila_get_selected_template($_GET['post']) == 'prog_landing_page' ||
          phila_get_selected_template($_GET['post']) == 'repeating_rows' ||
          phila_get_selected_template($_GET['post']) == 'service_stub' ||
          phila_get_selected_template($_GET['post']) == 'staff_directory_v2' ||
          phila_get_selected_template($_GET['post']) == 'start_process' ||
          phila_get_selected_template($_GET['post']) == 'stub' ||
          phila_get_selected_template($_GET['post']) == 'things-to-do' ||
          phila_get_selected_template($_GET['post']) == 'topic_page' ||
          phila_get_selected_template($_GET['post']) == 'vue_app' ) )
        return true;
      return false;
    } 

    $meta_boxes[] = array(
      'title' => 'Stub',
      'pages' => array('programs'),
      'context' => 'after_title',
      'priority' => 'low',
      'include' => array(
        'custom' => 'is_stub',
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

    function is_stub() {
      if( isset($_GET['post']) === true && 
        ( phila_get_selected_template($_GET['post']) == 'stub' ) )
        return true;
      return false;
    }

    return $meta_boxes;
  }

}
