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
      'title'    => 'Status',
      'pages' => array( 'event_spotlight' ),
      'priority' => 'high',
      'context'    => 'side',
      'fields'  => array(
        array(
          'id'  => 'spotlight_is_active',
          'type'  => 'switch',
          'std'=> '0',
          'on_label'  => 'Yes',
          'off_label' => 'No',
          'name'  => 'Display this event spotlight on the latest landing page?',
        )
      )
    );

    $meta_boxes[] = array(
      'id'       => 'phila_spotlight_header',
      'title'    => 'Official event information',
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
          'desc'  => '1400px by 375px.',
          'columns' => 6
        ),
        array(
          'id'  => 'phila_v2_department_logo',
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
          'type' => 'heading',
          'name'  => 'Date/Time format',
        ),
        array(
          'id' => 'phila_date_format',
          'type' => 'select',
          'placeholder' => 'Choose date format...',
          'required'  => true,
          'options' => array(
            'date' => 'Date only',
            'datetime' => 'Date & Time',
          ),
          'desc'  => 'Choose <b>date & time</b> when the event occurs on a single day, at a single time.
          <br />Choose <b>date only</b> when the event spans multiple days and is comprised of a number of smaller events or, if the event is on a single day, but is comprised of a number of smaller events.'
        ),
        array(
          'name'  => 'Start Day and Time',
          'id'    => 'start_datetime',
          'class' =>  'effective-start-time',
          'type'  => 'datetime',
          'size'  =>  25,
          'columns' => 4,
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
          'id'    => 'end_datetime',
          'type'  => 'datetime',
          'class' =>  'effective-end-time',
          'size'  =>  25,
          'columns' => 8,

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
          'id'    => 'start_date',
          'class' =>  'effective-start-time',
          'type'  => 'date',
          'size'  =>  25,
          'columns' => 4,

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
          'id'    => 'end_date',
          'type'  => 'date',
          'class' =>  'effective-end-time',
          'size'  =>  25,
          'columns' => 8,

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
          'type' => 'heading',
          'name'  => 'Location',
        ),
        array(
          'id'  => 'address',
          'type'  => 'group',
          'fields'  => array(
              array(
                'id'  => 'venue_name',
                'placeholder'  => 'Venue name',
                'type'  => 'text'
              ),
              array(
                'id'  => 'address',
                'placeholder'  => 'Address',
                'type'  => 'text'
              ),
              array(
                'id'  => 'address_2',
                'placeholder'  => 'Address 2',
                'type'  => 'text'
              ),
              array(
                'id'  => 'city',
                'type'  => 'text',
                'std' => 'Philadelphia',
                'placeholder' => 'City',
                'columns' => 4,
              ),
              array(
                'id'  => 'state',
                'type'  => 'text',
                'placeholder' => 'State',
                'columns' => 2,
                'std' => 'PA'
              ),
              array(
                'id'  => 'zip',
                'type'  => 'text',
                'columns' => 2,
                'placeholder' => 'Zip',
                'std' => '19107'
              ),
          )
        ),
        array(
          'type' => 'heading',
          'name'  => 'Event information',
        ),
        array(
          'id'  => 'event_info',
          'type'  => 'wysiwyg',
        ),
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
                'call_to_action_multi' => 'Call to action (multi)',
                'full_width_cta'  => 'Full-width call to action',
                'image_list'  => 'Image list',
                'featured_events'  => 'Featured events',
                'posts'  => 'Posts'
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
              'fields' => array(
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg(),
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
            array(
              'id' => 'accordion_row',
              'type' => 'group',
              'visible' => array(
                'when' => array(
                  array('spotlight_options', '=', 'accordion'),
                ),
              ),
              'fields' => array(
                array(
                  'name' => ('Accordion row title'),
                  'id'   => 'accordion_row_title',
                  'type' => 'text',
                  'required' => true,
                ),
                array(
                  'id'   => 'accordion_group',
                  'type' => 'group',
                  'clone'  => true,
                  'sort_clone' => true,
                  'add_button' => '+ Add accordion',
                  'fields' => array(
                    Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg($section_name = 'Accordion title', $wysiwyg_desc = '', $columns = 12, $clone = true),
                  )
                )
              ),
            ),
            array(
              'id'  => 'call_to_action_multi_row',
              'type'  => 'group',
              'visible' => array(
                'when' => array(
                  array('spotlight_options', '=', 'call_to_action_multi'),
                ),
              ),
              'fields'  =>  Phila_Gov_Standard_Metaboxes::phila_meta_var_call_to_action_multi()
            ),
            array(
              'id'  => 'full_width_cta',
              'type'  => 'group',
              'visible' => array(
                'when' => array(
                  array('spotlight_options', '=', 'full_width_cta'),
                ),
              ),
              'fields'  =>
                Phila_Gov_Standard_Metaboxes::phila_meta_var_full_width_cta()
            ),
            array(
              'id'       => 'phila_image_list',
              'type'     => 'group',
              'visible'  => array('spotlight_options', '=', 'image_list'),
              'fields'   => array(
                array(
                  'name'  => 'Image list heading (optional)',
                  'id'    => 'title',
                  'type'  => 'text',
                ),
                array(
                  'name'  => 'List of images',
                  'id'    => 'phila_image_list',
                  'type'  => 'image_advanced'
                ),
              ),
            ),
            array(
              'id'  => 'featured_events',
              'type'  => 'group',
              'visible'  => array('spotlight_options', '=', 'featured_events'),
              'fields'  => array(
                array(
                  'name'  => 'Row title',
                  'id'    => 'title',
                  'type'  => 'text',
                ),
                array(
                  'id'  => 'features',
                  'type' => 'group',
                  'clone' => true,
                  'sort_clone'  => true,
                  'max_clone' => 4,
                  'add_button'  => 'Add another feature',
                  'fields'  => array(

                    Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg('Event title'),
                      array(
                        'id'  => 'venue_name',
                        'placeholder'  => 'Venue name',
                        'type'  => 'text'
                      ),
                      array(
                        'id'  => 'address',
                        'placeholder'  => 'Address',
                        'type'  => 'text'
                      ),
                      array(
                        'id'  => 'address_2',
                        'placeholder'  => 'Address 2',
                        'type'  => 'text'
                      ),
                      array(
                        'id'  => 'city',
                        'type'  => 'text',
                        'std' => 'Philadelphia',
                        'placeholder' => 'City',
                        'columns' => 4,
                      ),
                      array(
                        'id'  => 'state',
                        'type'  => 'text',
                        'placeholder' => 'State',
                        'columns' => 2,
                        'std' => 'PA'
                      ),
                      array(
                        'id'  => 'zip',
                        'type'  => 'text',
                        'columns' => 2,
                        'placeholder' => 'Zip',
                        'std' => '19107'
                      ),
                    array(
                      'name'  => 'Start Day and Time',
                      'id'    => 'start_datetime',
                      'type'  => 'datetime',
                      'size'  =>  25,
                      'desc'  => 'For an all day event, enter the same start and end day and time.',
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
                    ),
                    array(
                      'name'  => 'End Day and Time',
                      'id'    => 'end_datetime',
                      'type'  => 'datetime',
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
                    ),
                  )
                ),
              )
            ),
            array(
              'id'  => 'blog_posts',
              'type'  => 'group',
              'visible'  => array('spotlight_options', '=', 'posts'),
              'fields' => array(
                  array(
                    'name'  => 'Filter by a tag',
                    'id'  => 'tag',
                    'type' => 'taxonomy_advanced',
                    'taxonomy'  => 'post_tag',
                    'field_type' => 'select_advanced',
                    'desc'  => '<i>Required.</i> Display posts using this tag. "See all" will pre-filter on these terms.'
                ),
              )
            )
          ),
        ),
      )
    );

    return $meta_boxes;
  }

}
