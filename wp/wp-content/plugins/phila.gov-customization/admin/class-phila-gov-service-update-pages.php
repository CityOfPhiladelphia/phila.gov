<?php

if ( class_exists("Phila_Gov_Service_Update_Pages" ) ){
  $phila_service_update = new Phila_Gov_Service_Update_Pages();
}

 class Phila_Gov_Service_Update_Pages {


  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array($this, 'phila_register_service_update_meta_boxes' ), 100 );

  }

  function phila_register_service_update_meta_boxes( $meta_boxes ){
    $prefix = 'phila_';

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
          //  array(
          //    'name' => 'Schedule an expiration date and time?',
          //    'type' => 'heading',
          //    'after' => '<p class="description" style="margin-top:0; margin-bottom:1.5em;">The Display Start and End times set the window of time that the Service Update will appear on the site. This may be differ from the effective date of the update.<br/><em><strong>Example:</strong> Trash will not be collected on a specific date but we want to provide advanced notice.</em></p>',
          //  ),
           array(
             'name' => 'Service Update Timeframe',
             'type' => 'heading',
             'after' => '<p class="description" style="margin-top:0; margin-bottom:1.5em;">The Effective Start and End times are used to define the window of time a City Service will be impacted as well as when the update is removed from the site.</p>',
           ),
           array(
             'name' => 'Date/Time Format',
             'id' => $prefix . 'date_format',
             'type' => 'radio',
             'options' => array(
               'date' => 'Date only',
               'datetime' => 'Date & Time',
               'none' => 'No Date (must be unpublished when resolved)'
             ),
           ),
           array(
             'name'  => 'Effective Start Time',
             'id'    => $prefix . 'effective_start_datetime',
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
             'id'    => $prefix . 'effective_end_datetime',
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
             'name'  => 'Effective Start Time',
             'id'    => $prefix . 'effective_start_date',
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
             'name'  => 'Effective End Time',
             'id'    => $prefix . 'effective_end_date',
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
             'after' => '<p class="description" style="margin-top:0; margin-bottom:1.5em;">A brief description of the Service Update.</p>'
           ),
           array(
             'id'    => $prefix . 'service_update_message',
             'class' => 'service-update-message',
             'desc'  => '95 character maximum.',
             'type'  => 'wysiwyg',
             'options' => array(
               'media_buttons' => false,
               'teeny' => true,
               'dfw' => false,
               'quicktags' => false,
               'tinymce' => phila_setup_tiny_mce_basic(
                 array(
                   'format_select' => false
                  )
                ),
               'editor_height' => 200,
             ),
           ),
           array(
             'name' => 'Link (optional)',
             'type' => 'heading',
             'after' => '<p class="description" style="margin-top:0; margin-bottom:1.5em;">Use this area if the Service Update has a related web site, news article, press release, etc..</p>',
           ),
           array(
             'name'  => 'Link Text',
             'id'    => $prefix . 'update_link_text',
             'type'  => 'text',
             'class' => 'update-link-text',
             'desc'  => '80 character maximum.',
             'size'  => '60'
           ),
           array(
             'name'  => 'Link URL',
             'id'    => $prefix . 'update_link',
             'type'  => 'url',
             'desc'  => 'Example: http://www.phila.gov',
             'class' => 'update-link',
             'size'  => '60'
           ),
          )
        )
      )
    );// End Service Updates

    return $meta_boxes;

  }
}
