<?php 

add_filter( 'mb_settings_pages', 'phila_options_page' );

function phila_options_page( $settings_pages ) {
  $settings_pages[] = array(
    'id'          => 'phila_gov',
    'option_name' => 'phila_settings',
    'menu_title'  => 'Phila.gov settings',
    'menu_title'  => 'phila.gov settings',
    'tabs'        => array(
      'general' => 'General Settings',
      'jobs'  => 'Featured jobs',
      'closures'  => 'Closures',
      'sitewide_settings'  => 'Site-wide settings',
    ),
  );
  return $settings_pages;
}

add_filter( 'rwmb_meta_boxes', 'prefix_options_meta_boxes' );

// General Settings
function prefix_options_meta_boxes( $meta_boxes ) {
  $meta_boxes[] = array(
    'id'             => 'homepage_image',
    'title'          => 'Homepage image',
    'settings_pages' => 'phila_gov',
    'tab'            => 'general',
    'include' => array(
      'user_role'  => array( 'administrator', 'editor' ),
    ),
    'fields'         => array(
      array(
        'name' => 'Mobile image',
        'id'   => 'homepage_mobile',
        'type' => 'file_input',
      ),
      array(
        'name' => 'Full size',
        'id'   => 'homepage_desktop',
        'type' => 'file_input',
      ),
    ),
  );

  // Featured Jobs
  $meta_boxes[] = array(
    'id'             => 'featured_jobs',
    'title'          => 'Featured jobs',
    'settings_pages' => 'phila_gov',
    'tab'            => 'jobs',
    'include' => array(
      'user_role'  => array( 'administrator', 'editor', 'job_board_editor' ),
    ),
    'fields'  => array(
      array(
        'id'  => 'phila_featured_jobs',
        'type'   => 'group',
        'clone' => true,
        'max_clone'  => 2,
        'add_button' => '+ Add a second featured job',

        'fields'  => array(
          array(
            'id'  => 'job_title',
            'name'  => 'Job title',
            'type'  => 'text',
            'required'  => true
          ),
          array(
            'id'  => 'job_link',
            'name'  => 'URL to listing',
            'type'  => 'url',
            'required'  => true
          ),
          array(
            'id'  => 'job_description',
            'name'  => 'Description',
            'type'  => 'wysiwyg',
            'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
            'required'  => true
          ),
        ),
      ),
    ),
  );

  $meta_boxes[] = array(
    'id'             => 'closures',
    'title'          => 'Closures',
    'settings_pages' => 'phila_gov',
    'tab'            => 'closures',
    'include' => array(
      'user_role'  => array( 'administrator', 'editor', 'job_board_editor', 'secondary_philagov_closure_settings_editor' ),
    ),
    'fields'  => array(
      array(
        'name'  => 'Current collection status',
        'desc'  => 'Display on phila.gov homepage, covid update page, trash collection page, and streets homepage. NOTE: Active holidays will only work if first option selected',
        'id'    => 'phila_collection_status',
        'type'  => 'radio',
        'inline' => false,
        'std' => '0',
        'options' =>  array(
            '0' => 'On regular or holiday schedule',
            '1' => 'Some delays but stick to schedule',
            '2' => 'Some delays, put out one day later',
            '3' => 'Flexible / unanticipated cause of delays',
        )
      ),
      array(
        'id'  => 'phila_flexible_collection',
        'type'   => 'group',
        'visible' => array(
          'when'  => array(
            array('phila_collection_status', '=', '3'),
          ),
        ),
        'fields'  => array(
          array(
            'id'  => 'phila_flexible_collection_status',
            'name'  => 'Status',
            'type'  => 'wysiwyg',
            'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
          ),
          array(
            'id'    => 'phila_flexible_collection_color',
            'name'  => 'Color of flexible collection status alert',
            'type'  => 'radio',
            'inline' => false,
            'options' =>  array(
                '0' => 'green',
                '1' => 'yellow',
                '2' => 'red',
            )
          ),
          array(
            'id'    => 'phila_flexible_collection_impact',
            'name'  => 'Collection calculation impact',
            'type'  => 'radio',
            'inline' => false,
            'options' =>  array(
                '0' => 'No impact',
                '1' => '1 day delay',
                '2' => 'Cannot determine',
            )
          ),
        )
      ),
      array(
        'type' => 'heading',
        'name' => 'Holiday List',
      ),
      array(
        'id'  => 'phila_holidays',
        'type'   => 'group',
        'clone' => true,
        'add_button' => '+ Add another holiday',

        'fields'  => array(
          array(
            'id'  => 'holiday_label',
            'name'  => 'Holiday label',
            'type'  => 'text',
            'required'  => true
          ),
          array(
            'id'  => 'start_date',
            'name' => 'Holiday start date',
            'type'  => 'date',
            'required'  => true,
          ),
        ),
      ),
    ),
  );


  $meta_boxes[] = array(
    'id'             => 'sitewide_settings',
    'title'          => 'Sitewide settings',
    'settings_pages' => 'phila_gov',
    'tab'            => 'sitewide_settings',
    'include' => array(
      'user_role'  => array( 'administrator', 'editor' ),
    ),
    'fields'  => array(
      array(
        'name'  => 'Display voting banner',
        'desc'  => 'When active, the voting banner will be displayed on all pages',
        'id'    => 'display_voting_banner',
        'type'  => 'radio',
        'inline' => false,
        'std' => '0',
        'options' =>  array(
            '0' => 'Hide',
            '1' => 'Display',
        )
      ),
    ),
  );
  return $meta_boxes;
}