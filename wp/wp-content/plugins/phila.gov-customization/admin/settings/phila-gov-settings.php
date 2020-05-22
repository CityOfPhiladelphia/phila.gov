<?php 

add_filter( 'mb_settings_pages', 'phila_options_page' );

function phila_options_page( $settings_pages ) {
  $settings_pages[] = array(
    'id'          => 'phila_gov',
    'option_name' => 'phila_settings',
    'menu_title'  => 'Phila.gov settings',
    'parent'      => 'options-general.php',
    'tabs'        => array(
      'general' => 'General Settings',
      'jobs'  => 'Featured jobs',
      'closures'  => 'Closures',
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
      'user_role'  => array( 'administrator', 'editor', 'job_board_editor' ),
    ),
    'fields'  => array(
      array(
        'id'  => 'phila_closures',
        'type'   => 'group',
        'clone' => true,
        'add_button' => '+ Add another closure',

        'fields'  => array(
          array(
            'id'  => 'closure_label',
            'name'  => 'Closure Label',
            'type'  => 'text',
            'required'  => true
          ),
        ),
      ),
    ),
  );
  return $meta_boxes;
}