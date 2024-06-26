<?php

add_filter( 'rwmb_meta_boxes', 'phila_register_department_meta_boxes' );

function phila_register_department_meta_boxes( $meta_boxes ){

  //Department Homepage
  $meta_boxes[] = array(
    'title' => 'Department media',
    'pages'    => array( 'department_page' ),
    'visible' => array(
      'when'  => array(
        array('phila_template_select', '=', 'homepage_v2' ),
        array('phila_template_select', '=', 'homepage_v3' ),
      ),
      'relation' => 'or',
    ),
    'context'  => 'normal',
    'priority' => 'high',
    'include' => array(
      'user_role'  => array( 'administrator', 'editor', 'primary_department_homepage_editor' ),
    ),

    'fields' => array(
      array(
        'type' => 'heading',
        'name'  => 'Hero image',
        'columns' => '4'
      ),
      array(
        'type' => 'heading',
        'name'  => 'Hero image for mobile',
        'columns' => '4'
      ),
      array(
        'type' => 'heading',
        'name'  => 'Department logo',
        'columns' => '4'
      ),
      array(
        'id' => 'phila_v2_homepage_hero',
        'title' => 'Select image',
        'type' => 'image_advanced',
        'desc'  => 'Required. Image must be 1110px wide & 315px tall.',
        'max_file_uploads' => 1,
        'columns' => '4'
      ),
      array(
        'id' => 'phila_v2_homepage_hero_mobile',
        'title' => 'Select image',
        'type' => 'image_advanced',
        'desc'  => 'Required. Image must be 800px wide & 227px tall.',
        'max_file_uploads' => 1,
        'columns' => '4'
      ),
      array(
        'id'  => 'phila_v2_department_logo',
        'title' => 'Department logo',
        'type'  => 'image_advanced',
        'desc'  => 'Optional. Image must be at least 600px wide.',
        'max_file_uploads' => 1,
        'columns' => '4'
      ),
      array(
        'type' => 'heading',
        'name'  => 'Photo credit',
      ),
      Phila_Gov_Standard_Metaboxes::phila_metabox_title('Name & organization', 'phila_v2_photo_credit', 'E.g.: N. Santos for VISIT PHILADELPHIA™', '60' ),
    ),
  );

  $meta_boxes[] = array(
    'title' => 'Services list',
    'pages'    => array( 'department_page' ),
    'visible' => array( 'phila_template_select', 'service_list_v2' ),

    'fields' => array(
      array(
        'id'       => 'phila_v2_services_list',
        'title'    => 'Services',
        'context'  => 'normal',
        'priority' => 'high',
        'type'  => 'group',
        'clone' => true,

        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_v2_service_page_selector(),
        ),
      ),
    ),
  );

  $meta_boxes[] = array(
    'title' => 'Full-width call to action',
    'pages'    => array( 'department_page' ),
    'visible' => array(
      'when' => array(
        array('phila_template_select', 'homepage_v2'),
        array('phila_template_select', 'things-to-do'),
        array('phila_template_select', 'our-locations')
      ),
      'relation' => 'or'
    ),
    'include' => array(
      'user_role'  => array( 'administrator', 'editor', 'primary_department_homepage_editor' ),
    ),

    'fields' => array(
      array(
        'id'       => 'phila_v2_cta_full',
        'context'  => 'normal',
        'priority' => 'default',
        'type'  => 'group',
        'clone' => false,

        'fields' =>
          Phila_Gov_Standard_Metaboxes::phila_meta_var_full_width_cta()
      ),
    ),
  );


  $meta_boxes[] = array(
    'title' => 'Override page category selection',
    'pages'    => array( 'department_page' ),
    'visible' => array( 'phila_template_select', 'staff_directory_v2' ),
    'context'  => 'normal',
    'priority' => 'high',

    'fields' => array(
      array(
        'id'  => 'phila_get_staff_cats',
        'type' => 'group',
        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_category_picker('Select new owner', 'phila_staff_category', 'Display staff from these owners. This will override page ownership selection entirely.' ),
        ),
      ),
    ),
  );


  $meta_boxes[] = array(
      'title'    => 'Photo Callout',
      'pages'    => array( 'department_page' ),
      'context'  => 'normal',
      'priority' => 'high',

      'include' => array(
        'user_role'  => array( 'administrator', 'editor', 'primary_department_homepage_editor' ),
      ),

      'visible' =>  array(
        'when' => array(
            array( 'phila_template_select', '=', 'homepage_v2'),
            array( 'phila_template_select', '=', 'things-to-do'),
            array( 'phila_template_select', '=', 'our-locations')
          ),
          'relation' => 'or'
      ),

      'fields' => Phila_Gov_Row_Metaboxes::phila_metabox_photo_callout(),
  );//Things To Do

$meta_boxes[] = array(
    'title' => 'Image Grid with Links',
    'pages'    => array( 'department_page' ),
    'visible' => array( 'phila_template_select', 'things-to-do' ),

    'fields' => array(
      array(
        'id' => 'phila_v2_linked_image_grid__header',
        'type' => 'text',
        'title' => 'Header'
      ),
      array(
        'id'       => 'phila_v2_linked_image_grid',
        'title'    => 'Image Grid with Links',
        'context'  => 'normal',
        'priority' => 'high',
        'type'  => 'group',
        'clone' => true,
        'max_clone' => 6,
        'sort_clone'  => true,

        'fields' => array(
          array(
            'id' => 'phila_v2_linked_image_grid__image',
            'title' => 'Select image',
            'type' => 'image_advanced',
            'max_file_uploads' => 1,
            'desc' => 'Images should be exactly 320px by 214px.',
            'columns' => 4
          ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_link_fields('', 'phila_v2_linked_image_grid__link', true, 8)
        ),
      ),
    ),
  );

$meta_boxes[] = array(
      'title'    => 'Programs and Initiatives Grid',

      'pages'    => array( 'department_page' ),
      'visible' => array( 'phila_template_select', 'things-to-do' ),
      'context'  => 'normal',

      'fields' => array(
          array(
            'name' => 'Display a Programs and Initiatives grid?',
            'id'   => 'phila_progs_inits_grid_shown',
            'type' => 'switch',
            'on_label'  => 'Yes',
            'off_label' => 'No'
          )
      )
    );//Things To Do


$meta_boxes[] = array(
      'title'    => 'Featured Activities Grid',

      'pages'    => array( 'department_page' ),
      'visible' => array( 'phila_template_select', 'things-to-do' ),
      'context'  => 'normal',

      'fields' => array(
          array(
            'name' => 'Display a Featured Activities grid?',
            'id'   => 'phila_feat_activites_grid_shown',
            'type' => 'switch',
            'on_label'  => 'Yes',
            'off_label' => 'No',
          )
      )
    );

    $meta_boxes[] = array(
      'title'    => 'Featured Locations Grid',

      'pages'    => array( 'department_page' ),
      'visible' => array(
          array('phila_template_select', 'our-locations')
      ),
      'context'  => 'normal',

      'fields' => array(
          array(
            'name' => 'Display a Featured Locations grid?',
            'id'   => 'phila_feat_locations_grid_shown',
            'type' => 'switch',
            'on_label'  => 'Yes',
            'off_label' => 'No',
          ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('Header', 'phila_feat_locations_grid__header'),
          array(
            'id'  => 'phila_feat_locations_grid__desc',
            'type'  => 'wysiwyg',
            'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
        )
      )
    );

  $meta_boxes[] = array(
      'title'    => 'WYSIWYG section with header',
      'pages'    => array( 'department_page' ),
      'visible' => array( 'phila_template_select', 'things-to-do' ),
      'context'  => 'normal',

      'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg()
      )
    );//Things To Do

  $meta_boxes[] = array(
    'title' => 'Department metadata',
    'pages'    => array( 'department_page' ),
    'context'  => 'side',
    'priority' => 'low',
    'include' => array(
      'user_role'  => array( 'administrator', 'editor', 'primary_department_homepage_editor' ),
    ),
    'visible' => array(
      'when'  => array(
        array('phila_template_select', '=', 'homepage_v2' ),
        array('phila_template_select', '=', 'homepage_v3' ),
        array('phila_template_select', '=', 'off_site_department' ),
      ),
      'relation' => 'or',
    ),
    'fields' => array(
      array(
        'type'  => 'number',
        'id'  => 'phila_department_code',
        'name' => 'Official code'
      ),
      array(
        'type'  => 'text',
        'id'  => 'phila_department_acronym',
        'name' => 'Acronym',
        'desc' => 'E.g. Parks & Rec'
      ),
      array(
        'type'  => 'textarea',
        'id'  => 'phila_department_keywords',
        'name' => 'Keywords',
        'desc'  => 'Separate keywords with commas. E.g. Parks & Rec, trees, recreation centers'
      ),
    )
  );

  return $meta_boxes;
}
