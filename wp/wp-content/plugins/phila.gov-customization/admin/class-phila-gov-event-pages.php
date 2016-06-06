<?php

if ( class_exists("Phila_Gov_Event_Pages" ) ){
  $phila_department_sites = new Phila_Gov_Event_Pages();
}

 class Phila_Gov_Event_Pages {


  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array($this, 'phila_register_event_meta_boxes' ), 100 );

  }

  function phila_register_event_meta_boxes( $meta_boxes ){
    $prefix = 'phila_';

    $meta_boxes[] = array(
      'id'       => 'events',
      'title'    => 'Event General Information',
      'pages'    => array( 'event_page' ),
      'context'  => 'normal',
      'priority' => 'high',
      'fields' => array(
        array(
          'name'  => 'Description',
          'desc'  => 'A short description of the event (365 character maximum). Required.',
          'id'    => $prefix . 'event_desc',
          'type'  => 'textarea',
          'class' => 'event-description',
          'clone' => false,
        ),
        array(
          'name'  => 'Location',
          'desc'  => 'Address or general location of the event. Required.',
          'id'    => $prefix . 'event_loc',
          'type'  => 'textarea',
          'class' => 'event-location',
          'clone' => false,
        ),
        array(
          'name'  => 'Link to Map',
          'desc'  => 'Provide a link to a map of the event location. Optional.',
          'id'    => $prefix . 'event_loc_link',
          'type'  => 'url',
          'class' => 'event-location-link',
          'clone' => false,
        ),
        array(
          'name'  => 'Event Start Date',
          'id'    => $prefix . 'event_start',
          'class' =>  'start-date',
          'type'  => 'date',
          'size'  =>  25,
          'js_options' =>  array(
            'dateFormat'=>'MM dd',
          ),
          'timestamp' => false
        ),
        array(
          'name'  => 'Event End Date',
          'id'    => $prefix . 'event_end',
          'class' =>  'end-date',
          'type'  => 'date',
          'size'  =>  25,
          'js_options' =>  array(
            'dateFormat'=>'MM dd',
          ),
          'timestamp' => false
        ),
      )
    );//Event General Information

    $meta_boxes[] = array(
      'id'       => 'events_connect',
      'title'    => 'Connect Information',
      'pages'    => array( 'event_page' ),
      'context'  => 'normal',
      'priority' => 'high',
      'fields' => array(
        array(
          'name'  => 'Custom Markup',
          'desc'  => 'This custom code is responsible for rendering the event\'s "Connect" information.',
          'id'    => $prefix . 'event_connect',
          'type'  => 'textarea',
          'class' => 'event-connect-info',
          'clone' => false,
        ),
      )
    );//Event Connect Information

    $meta_boxes[] = array(
      'title'    => 'Service Updates & Changes',
      'pages'    => array( 'event_page' ),
      'context'  => 'normal',
      'priority' => 'high',

      'fields' => array(
        array(
         'id' => 'service_updates',
         'type' => 'group',
         'clone'  => true,

         'fields' => array(
           array(
              'name' => 'Update Type',
              'id'   => $prefix . 'update_type',
              'type' => 'select',
              'placeholder' => 'Choose type...',
              'options' => array(
                'city' => 'City',
                'roads' => 'Roads',
                'transit' => 'Transit',
                'trash' => 'Trash',
              ),
           ),
           array(
              'name' => 'Urgency Level',
              'id'   => $prefix . 'update_level',
              'type' => 'select',
              'placeholder' => 'Choose type...',
              'options' => array(
                'normal' => 'Normal (Green)',
                'warning' => 'Warning (Yellow)',
                'critical' => 'Critical (Red)',
              ),
           ),
           array(
             'name'  => 'Update Message',
             'id'    => $prefix . 'service_update_message',
             'type'  => 'textarea',
             'class' => 'service-update-message',
             'desc'  => '100 character maximum.',
             'size'  => '60'
           ),
           array(
             'name'  => 'Update Link Text',
             'id'    => $prefix . 'update_link_text',
             'type'  => 'text',
             'class' => 'update-link-text',
             'size'  => '60'
           ),
           array(
             'name'  => 'Update Link',
             'id'    => $prefix . 'update_link',
             'type'  => 'url',
             'class' => 'update-link',
           ),
           array(
             'name'  => 'Update Effective Date',
             'id'    => $prefix . 'update_effective_date',
             'type'  => 'text',
             'class' => 'update-effective-date',
             'size'  => '60'
           ),
          //  TODO: Create a better way to accept a date range or date and time range
          //  array(
          //    'name'  => 'Start Date',
          //    'id'    => $prefix . 'update_start_date',
          //    'class' =>  'update-start-date',
          //    'type'  => 'date',
          //    'size'  =>  25,
          //    'js_options' =>  array(
          //      'dateFormat'=>'MM dd',
          //    ),
          //    'timestamp' => false
          //  ),
          //  array(
          //    'name'  => 'End Date',
          //    'id'    => $prefix . 'update_end_date',
          //    'class' =>  'update-end-date',
          //    'type'  => 'date',
          //    'size'  =>  25,
          //    'js_options' =>  array(
          //      'dateFormat'=>'MM dd',
          //    ),
          //    'timestamp' => false
          //  ),
          )
        )
      )
    );//Service Updates

    $meta_boxes[] = array(
      'id'       => 'action_panel',
      'title'    => 'Action Panel',
      'pages'    => array( 'event_page' ),
      'context'  => 'normal',
      'priority' => 'high',
      'fields' => array(
        array(
          'name'  => 'Panel Title',
          'id'    => $prefix . 'action_panel_title',
          'type'  => 'text',
          'class' => 'action-panel-title',
          'clone' => false,
        ),
        array(
          'name'  => 'Call to Action Text',
          'id'    => $prefix . 'action_panel_cta_text',
          'type'  => 'text',
          'class' => 'action-panel-cta-text',
          'clone' => false,
        ),
        array(
          'name'  => 'Details',
          'id'    => $prefix . 'action_panel_details',
          'type'  => 'textarea',
          'class' => 'action-panel-details',
          'clone' => false,
        ),
        array(
          'name'  => 'Icon',
          'id'    => $prefix . 'action_panel_fa',
          'type'  => 'text',
          'class' => 'action-panel-fa',
          'clone' => false,
        ),
        array(
          'name'  => 'Icon Background Circle',
          'id'    => $prefix . 'action_panel_fa_circle',
          'type'  => 'checkbox',
          'class' => 'action-panel-fa',
          'clone' => false,
        ),
        array(
          'name'  => 'Link',
          'id'    => $prefix . 'action_panel_link',
          'desc'  => 'Link to permit documents',
          'type'  => 'url',
          'class' => 'action-panel-link',
          'clone' => false,
        ),
      )
    );//Event Connect Information

    $meta_boxes[] = array(
      'title'    => 'Event Content Blocks',
      'pages'    => array( 'event_page' ),
      'context'  => 'normal',
      'priority' => 'high',

      'fields' => array(
        array(
          'name'  => 'Section Heading',
          'desc'  => 'Custom heading for optional Content Blocks',
          'id'    => $prefix . 'event_content_blocks_heading',
          'type'  => 'text',
          'class' => 'event_content_blocks_heading',
          'clone' => false,
        ),
        array(
          'name'  => '"More Link" Text',
          'desc'  => 'Custom text to encourage visitors to "see more"',
          'id'    => $prefix . 'event_content_blocks_link_text',
          'type'  => 'text',
          'class' => 'event_content_blocks_link_text',
          'clone' => false,
        ),
        array(
          'name'  => '"More Link" URL',
          'desc'  => 'Location for more Content Block content',
          'id'    => $prefix . 'event_content_blocks_link',
          'type'  => 'url',
          'class' => 'event_content_blocks_link',
          'clone' => false,
        ),
        array(
          'type' => 'divider'
        ),
        array(
         'id'         => 'event_content_blocks',
         'type'       => 'group',
         'clone'      => true,
         'sort_clone' => true,

         'fields' => array(
           array(
             'name'  => 'Title',
             'id'    => $prefix . 'event_block_content_title',
             'type'  => 'text',
             'class' => 'event-block-content-title',
             'desc'  => '70 character maximum.',
             'size'  => '60'
           ),
            array(
              'name'  => 'Image',
              'id'    => $prefix . 'event_block_image',
              'type'  => 'file_input',
              'class' => 'event-block-image',
              'desc'  => 'Image should be no smaller than 274px by 180px.'
            ),
            array(
              'name' => 'Image Credit',
              'id'   => $prefix . 'event_block_image_credit',
              'type' => 'text',
              'class' => 'event-block-image-credit',
              'desc'  => 'Provide attribution information when necessary.',
              'size'  => '60'
            ),
            array(
              'name'  => 'Summary',
              'id'    => $prefix . 'event_block_summary',
              'type'  => 'textarea',
              'class' => 'event-block-summary',
              'desc'  => '200 character maximum.'
            ),
            array(
              'name'  => 'Link to Content',
              'id'    => $prefix . 'event_block_link',
              'type'  => 'url',
              'class' => 'event-block-url',
              'desc'  => 'Enter a URL. E.g. http://alpha.phila.gov/oem',
              'size'  => '60',
            ),
          )
        )
      )
    ); //Event Content Blocks

    return $meta_boxes;

  }
}
