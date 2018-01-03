<?php
/**
 * Register templates for use on the front-end
 *
 */

if ( class_exists( "Phila_Gov_Program_Templates" ) ){
  $admin_menu_labels = new Phila_Gov_Program_Templates();
}

class Phila_Gov_Program_Templates {

  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array( $this, 'register_template_selection_metabox_programs'), 10, 1 );

    add_filter( 'rwmb_outside_conditions', array( $this, 'hide_prog_wysiwyg' ), 10, 1 );


  }

  function register_template_selection_metabox_programs( $meta_boxes ){
    $meta_boxes[] = array(
      'id'       => 'template_selection',
      'title'    => 'Select Template',
      'pages'    => array( 'programs' ),
      'context'  => 'advanced',
      'priority' => 'high',

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
            'phila_one_quarter'    => '1/4 Headings',
            'prog_off_site' => 'Off-site program'
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
      'fields' => array(
        array(
          'type'  => 'URL',
          'id' => 'prog_off_site_link',
          'name' => 'URL',
        ),
        array(
          'type'  => 'checkbox',
          'id' => 'is_external',
          'name' => 'Does this program exist outside of phila.gov?',
        ),
      ),
    );

    $meta_boxes[] = array(
      'id'       => 'phila_header',
      'title'    => 'Header image',
      'pages' => array( 'programs' ),
      'priority' => 'high',
      'visible' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'prog_landing_page' ),
          array( 'phila_template_select', '=', 'prog_off_site' )
        ),
        'relation'  => 'or'
      ),
      'fields' => array(
        array(
          'id' => 'prog_header_img',
          'name' => 'Program header image',
          'type'  => 'image_advanced',
          'max_file_uploads' => 1,
          'desc'  => 'Required. Minimum size 700px by 500px.',
          'columns' => 3,
        ),
        array(
          'id' => 'prog_header_img_sub',
          'name' => 'Subpage header image',
          'type'  => 'image_advanced',
          'max_file_uploads' => 1,
          'desc'  => 'Required. Minimum size 700px by 500px.',
          'columns' => 3,
        ),
        array(
          'id'  => 'phila_v2_department_logo',
          'name' => 'Program logo',
          'type'  => 'image_advanced',
          'desc'  => 'Optional. Image must be at least 600px wide.',
          'max_file_uploads' => 1,
          'columns' => 3
        ),
        array(
          'id'  => 'phila_program_owner_logo',
          'name' => 'Program owner logo',
          'type'  => 'image_advanced',
          'desc'  => 'Optional. Appears in header. Must be white with no background.',
          'max_file_uploads' => 1,
          'columns' => 3
        ),
        array(
          'type' => 'heading',
          'name'  => 'Photo credit',
        ),
        Phila_Gov_Standard_Metaboxes::phila_metabox_title('Name & organization', 'phila_photo_credit', 'E.g.: N. Santos for VISIT PHILADELPHIA™', '60' ),
      )
    );

    $meta_boxes[] = array(
      'id'       => 'phila_program',
      'title'    => 'Page content',
      'pages' => array( 'programs' ),
      'priority' => 'high',
      'revision' => true,
      'hidden' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'prog_off_site' )
        ),
      ),

      'fields' => array(
        Phila_Gov_Row_Metaboxes::phila_metabox_grid_row(),
      )
    );

    return $meta_boxes;
  }

  function hide_prog_wysiwyg( $conditions ) {
    $conditions['#postdivrich'] = array(
      'hidden' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'prog_off_site' ),
        ),
      ),
    );

    return $conditions;
  }

}
