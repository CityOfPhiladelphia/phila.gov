<?php

add_filter( 'mb_settings_pages', 'phila_options_page' );

function phila_options_page( $settings_pages ) {
  $settings_pages[] = array(
    'id'          => 'phila_gov',
    'option_name' => 'phila_settings',
    'menu_title'  => 'Phila.gov settings',
    'menu_title'  => 'phila.gov settings',
    'tabs'        => array(
      'general'       => 'General Settings',
      'jobs'          => 'Featured jobs',
      'closures'      => 'Closures',
      'translations'  => 'Translations'
    ),
  );
  return $settings_pages;
}

add_action( 'rwmb_enqueue_scripts', 'update_translations_script' );
function update_translations_script() {
  $translations_endpoint = rwmb_meta( 'phila_translations_deploy_url', array( 'object_type' => 'setting' ), 'phila_settings' );
  $dept_billing_code = rwmb_meta( 'phila_translations_default_billing_code', array( 'object_type' => 'setting' ), 'phila_settings' );
  $js_vars = array(
    'update_translations_webhook' => $translations_endpoint,
    'update_translations_dept_billing_code' => $dept_billing_code,
  );
  wp_enqueue_script( 'translate-homepage-script', plugins_url( '../js/translate-homepage.js', __FILE__), array( 'jquery' ), '', true );
  wp_localize_script('translate-homepage-script', 'phila_homepage_js_vars', $js_vars );
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
        'name' => 'Holiday list',
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
    'id'             => 'phila_translations_settings',
    'title'          => 'Translations deployment settings',
    'settings_pages' => 'phila_gov',
    'tab'            => 'translations',
    'include' => array(
      'user_role'  => array( 'administrator', 'editor' ),
    ),
    'fields'         => array(
      array(
        'name' => 'Endpoint URL',
        'id'   => 'phila_translations_deploy_url',
        'type'  => 'text',
        'required'  => true,
        'desc'  => 'Include trailing slash'
      ),
      array(
        'name' => 'Default billing code',
        'id'   => 'phila_translations_default_billing_code',
        'type'  => 'text',
        'required'  => true,
        'desc'  => 'Consult OIA team for code to use'
      ),
      array(
        'type'       => 'button',
        'name'       => 'Translate homepage',
        'std'        => 'Translate homepage',
        'attributes' => array(
          'data-section' => 'translate-homepage',
          'class'        => 'translate-homepage',
        ),
      ), 
    ),
  );

  return $meta_boxes;
}
