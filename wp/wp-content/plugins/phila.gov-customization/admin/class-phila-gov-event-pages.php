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
          'desc'  => 'A short description of the event. Required.',
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

    return $meta_boxes;

  }
}
