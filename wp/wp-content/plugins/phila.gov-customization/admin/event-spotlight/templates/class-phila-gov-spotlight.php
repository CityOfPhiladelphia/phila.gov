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
      'title'    => 'Spotlight overview',
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

        array(
          'name' => 'Date/Time Format',
          'id' => 'phila_date_format',
          'type' => 'select',
          'placeholder' => 'Choose date format...',
          'options' => array(
            'date' => 'Date only',
            'datetime' => 'Date & Time',
          ),
        ),
        array(
          'name'  => 'Start Day and Time',
          'id'    => 'phila_effective_start_datetime',
          'class' =>  'effective-start-time',
          'type'  => 'datetime',
          'size'  =>  25,
          'columns' => 6,
          'js_options' =>  array(
            'timeFormat' =>  'hh:mm tt',
            'dateFormat'=>'mm-dd-yy',
            'stepMinute' => 15,
            'showHour' => 'true',
            'controlType'=> 'select',
            'oneLine'=> true,
            'timeInput' => true,
          ),
          'timestamp' => true,
          'visible' => array('phila_date_format', '=', 'datetime'),
        ),
        array(
          'name'  => 'End Day and Time',
          'id'    => 'phila_effective_end_datetime',
          'type'  => 'datetime',
          'class' =>  'effective-end-time',
          'size'  =>  25,
          'columns' => 6,
          'js_options' =>  array(
            'timeFormat' => 'hh:mm tt',
            'dateFormat' => 'mm-dd-yy',
            'stepMinute' => 15,
            'showHour' => 'true',
            'controlType'=> 'select',
            'oneLine'=> true,
            'timeInput' => true
          ),
          'timestamp' => true,
          'visible' => array('phila_date_format', '=', 'datetime'),
        ),
        array(
          'name'  => 'Start Date',
          'id'    => 'phila_effective_start_date',
          'class' =>  'effective-start-time',
          'type'  => 'date',
          'size'  =>  25,
          'columns' => 6,
          'js_options' =>  array(
            'dateFormat'=>'mm-dd-yy',
            'stepMinute' => 15,
            'showHour' => 'true',
            'controlType'=> 'select',
            'oneLine'=> true,
            'timeInput' => true,
          ),
          'timestamp' => true,
          'visible' => array('phila_date_format', '=', 'date'),
        ),
        array(
          'name'  => 'End Date',
          'id'    => 'phila_effective_end_date',
          'type'  => 'date',
          'class' =>  'effective-end-time',
          'size'  =>  25,
          'columns' => 6,
          'js_options' =>  array(
            'dateFormat' => 'mm-dd-yy',
            'stepMinute' => 15,
            'showHour' => 'true',
            'controlType'=> 'select',
            'oneLine'=> true,
            'timeInput' => true
          ),
          'timestamp' => true,
          'visible' => array('phila_date_format', '=', 'date'),
        ),
        array(
          'name' => 'Address',
          'id'  => 'address',
          'type'  => 'text'
        ),
        array(
          'name'  => 'Location',
          'id'  => 'location',
          'type'  => 'map',
          'api_key' => '',
          'address_field' => 'address'
        )
      ),
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
