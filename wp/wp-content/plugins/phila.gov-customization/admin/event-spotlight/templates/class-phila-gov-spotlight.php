<?php
/* Event spotlight template */

if ( class_exists('Phila_Gov_Event_Spotlight_Template' ) ){
  $phila_event_spotlight_load = new Phila_Gov_Event_Spotlight_Template();
}

class Phila_Gov_Event_Spotlight_Template {

  public function __construct(){

    add_action( 'rwmb_meta_boxes', array( $this, 'register_event_spotlight_metaboxes' ), 10, 1 );

  }

  function register_event_spotlight_metaboxes( $meta_boxes ){

    $meta_boxes[] = array(
      'id'       => 'phila_spotlight_header',
      'title'    => 'Spotlight images',
      'pages' => array( 'event_spotlight' ),
      'priority' => 'high',
      'include' => array(
        'user_role'  => array( 'administrator', 'editor', 'phila_master_homepage_editor' ),
        'relation' => 'or',
       ),
      'fields' => array(
        array(
          'id' => 'header_img',
          'name' => 'Event hero image',
          'type'  => 'image_advanced',
          'max_file_uploads' => 1,
          'desc'  => '1440px by 375px.',
          'columns' => 6
        ),
        array(
          'id'  => 'owner_logo',
          'name' => 'Event owner logo',
          'type'  => 'image_advanced',
          'desc'  => 'Optional. Appears above footer. 600px by 600px.',
          'max_file_uploads' => 1,
          'columns' => 6
        ),
        array(
          'type' => 'heading',
          'name'  => 'Photo credit',
        ),
        Phila_Gov_Standard_Metaboxes::phila_metabox_title('Name & organization', 'photo_credit', 'E.g.: N. Santos for VISIT PHILADELPHIA™', '60' ),
      )
    );

    $meta_boxes[] = array(
      'id'       => 'phila_event_spotlight',
      'title'    => 'Event spotlight rows',
      'pages'    => array( 'event_spotlight' ),
      'context'  => 'normal',
      'revision' => true,

      'fields' => array(
        array(
          'id'    => 'spotlight_row',
          'type'  => 'group',
          'clone' => true,
          'sort_clone' => true,
          'add_button'  => '+ Add row',

          'fields' => array(
            array(
              'name' => 'Select row',
              'id'   => 'spotlight_options',
              'desc'  => 'Choose a spotlight row',
              'type' => 'select',
              'placeholder' => 'Select...',
              'options' => array(
                'registration' => 'Registration',
                'calendar' => 'Calendar',
                'accordion' => 'Accordions',
                'free_text' => 'Free text',
                'call_to_action_multi' => 'Call to action (multi)'
              ),
            ),
            array(
              'id' => 'free_text_option',
              'type' => 'group',
              'visible' => array(
                'when' => array(
                  array('spotlight_options', '=', 'free_text'),
                ),
              ),
              'clone'  => true,
              'sort_clone' => true,
              'add_button'  => '+ Add section',
              'fields' => array(
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg($section_name = '1/4 Heading'),
              )
            ),
            array(
              'id' => 'phila_registration',
              'type'  => 'group',
              'visible' => array(
                'when' => array(
                  array( 'spotlight_options', '=', 'registration' )
                )
              ),
              'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_registration(),
            ),
            array(
              'id'  => 'calendar_row',
              'type'  => 'group',
              'visible'  => array(
                'when'  => array(
                  array('spotlight_options', '=', 'calendar')
                  ),
                ),
                'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_calendar_full()
            ),
          ),
        ),
      )
    );

    return $meta_boxes;
  }

}
