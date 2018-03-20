<?php

if ( class_exists("Phila_Gov_Service_Update_Pages" ) ){
  $phila_service_update = new Phila_Gov_Service_Update_Pages();
}

 class Phila_Gov_Service_Update_Pages {


  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array($this, 'phila_register_service_update_meta_boxes' ), 100 );

  }

  function phila_register_service_update_meta_boxes( $meta_boxes ){

    $meta_boxes[] = array(
      'title'    => 'Service Updates Details',
      'pages'    => array( 'service_updates' ),
      'context'  => 'normal',
      'priority' => 'high',

      'fields' => array(
        array(
         'id' => 'service_update',
         'type' => 'group',

         'fields' => array(
          array(
            'type' => 'custom_html',
            'std' => '<p class="description" style="margin-top:10px;">Choose the type of service impacted (city, roads, transit, or trash collection) and the level of urgency (normal, warning, critical).</p>',
          ),
          array(
            'name' => 'Update Type',
            'id'   => 'phila_update_type',
            'type' => 'select',
            'placeholder' => 'Choose type...',
            'desc' => 'Note: only one type of alert will display at a time, prioritized by urgency.',
            'admin_columns' => 'after title',
            'options' => array(
              'city' => 'City',
              'roads' => 'Roads',
              'transit' => 'Transit',
              'trash' => 'Trash',
              'phones' => 'Phones',
              'offices' => 'Offices',
            ),
          ),
          array(
            'name' => 'Urgency Level',
            'id'   => 'phila_update_level',
            'type' => 'select',
            'placeholder' => 'Choose type...',
            'admin_columns' => 'after title',
            'options' => array(
              '0' => 'Normal (Green)',
              '1' => 'Warning (Yellow)',
              '2' => 'Critical (Red)',
            ),
           ),
          array(
            'name' => 'Service Update Timeframe',
            'type' => 'heading',
            'after' => '<p class="description" style="margin-top:0; margin-bottom:1.5em;">The <strong>Effective Start</strong> and <strong>End</strong> day/times are used to display the window of time a City Service will be impacted. These fields do <strong>not</strong> schedule a service update to appear. If you need an update to appear on a particular day, schedule it using the <strong>Publish</strong> settings.</p>',
           ),
          array(
            'name' => 'Date/Time Format',
            'id' => 'phila_date_format',
            'type' => 'select',
            'placeholder' => 'Choose date format...',
            'options' => array(
              'date' => 'Date only',
              'datetime' => 'Date & Time',
              'none' => 'No Date (must be unpublished when resolved)'
            ),
          ),
          array(
            'name'  => 'Effective Start Time',
            'id'    => 'phila_effective_start_datetime',
            'class' =>  'effective-start-time',
            'type'  => 'datetime',
            'size'  =>  25,
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
            'name'  => 'Effective End Time',
            'id'    => 'phila_effective_end_datetime',
            'type'  => 'datetime',
            'class' =>  'effective-end-time',
            'size'  =>  25,
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
            'name'  => 'Effective Start Day',
            'id'    => 'phila_effective_start_date',
            'class' =>  'effective-start-time',
            'type'  => 'date',
            'size'  =>  25,
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
            'name'  => 'Effective End Day',
            'id'    => 'phila_effective_end_date',
            'type'  => 'date',
            'class' =>  'effective-end-time',
            'size'  =>  25,
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
            'name' => 'Service Update Message',
            'type' => 'heading',
            'before'=> '<p class="description" style="margin-top:0; margin-bottom:1.5em;"><strong>Note:</strong> A service update will be removed after its effective end day or time has passed.</p>',
            'after' => '<p class="description" style="margin-top:0; margin-bottom:1.5em;">A brief description of the Service Update.</p>'
           ),
          array(
            'id'    => 'phila_service_update_message',
            'class' => 'service-update-message',
            'desc'  => '95 character maximum.',
            'type'  => 'textarea',
            //TODO: Use a wysiwyg instead of textarea if we can give a maxcount
            //  'type'  => 'wysiwyg',
            //  'options' => array(
            //    'media_buttons' => false,
            //    'teeny' => true,
            //    'dfw' => false,
            //    'quicktags' => false,
            //    'tinymce' => phila_setup_tiny_mce_basic(
            //      array(
            //        'format_select' => false
            //       )
            //     ),
            //    'editor_height' => 200,
            //  ),
           ),
          array(
            'name' => 'Link (optional)',
            'type' => 'heading',
            'after' => '<p class="description" style="margin-top:0; margin-bottom:1.5em;">Use this area if the Service Update has a related web site, post, press release, etc..</p>',
           ),
          array(
            'name'  => 'Link Text',
            'id'    => 'phila_update_link_text',
            'type'  => 'text',
            'class' => 'update-link-text',
            'desc'  => '80 character maximum.',
            'size'  => '60'
           ),
          array(
            'name'  => 'Link URL',
            'id'    => 'phila_update_link',
            'type'  => 'url',
            'desc'  => 'Example: http://www.phila.gov',
            'class' => 'update-link',
            'size'  => '60'
          ),
          array(
            'name'  => 'Does this link direct users away from beta.phila.gov?',
            'id'    => 'phila_off_site',
            'type'  => 'switch',
            'on_label'  => 'Yes',
            'off_label' => 'No',
          ),
        )
      )
    )
  );// End Service Updates

    return $meta_boxes;

  }
}
