<?php 

add_filter( 'mb_settings_pages', 'phila_options_page' );

function phila_options_page( $settings_pages ) {
  $settings_pages[] = array(
    'id'          => 'phila_gov',
    'option_name' => 'phila_settings',
    'menu_title'  => 'Phila.gov',
    'parent'      => 'options-general.php',
    'tabs'        => array(
      'general' => 'General Settings',
      'jobs'  => 'Featured jobs',
    ),
  );
  return $settings_pages;
}

add_filter( 'rwmb_meta_boxes', 'prefix_options_meta_boxes' );

function prefix_options_meta_boxes( $meta_boxes ) {
  $meta_boxes[] = array(
    'id'             => 'homepage_image',
    'title'          => 'Homepage image',
    'settings_pages' => 'phila_gov',
    'tab'            => 'general',
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

  $meta_boxes[] = array(
    'id'             => 'featured_jobs',
    'title'          => 'Featured jobs',
    'settings_pages' => 'phila_gov',
    'tab'            => 'jobs',
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
  return $meta_boxes;
}