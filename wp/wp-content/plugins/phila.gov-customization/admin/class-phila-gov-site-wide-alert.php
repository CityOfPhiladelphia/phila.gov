<?php

/**
* Add alerts to homepage
*
* @link https://github.com/CityOfPhiladelphia/phila.gov-customization
*
* @package phila-gov_customization
*/

if ( class_exists( "Phila_Gov_Site_Wide_Alert" ) ){
  $phila_site_wide_alert = new Phila_Gov_Site_Wide_Alert();
}

class Phila_Gov_Site_Wide_Alert {

  public function __construct(){

    add_filter( 'rwmb_meta_boxes',  array($this, 'phila_register_meta_boxes') );

  }

  function phila_register_meta_boxes( $meta_boxes ){
    $prefix = 'phila_';
    $meta_boxes[] = array(
      'id'       => 'site-wide-alert',
      'title'    => 'Alert Settings',
      'pages'    => array( 'site_wide_alert' ),
      'context'  => 'side',
      'priority' => 'high',


      'fields' => array(
        array(
          'name'  => 'Alert Start Time',
          'id'    => $prefix . 'alert_start',
          'class' =>  'start-time',
          'type'  => 'datetime',
          'size'  =>  25,
          'js_options' =>  array(
            'timeFormat' =>  'hh:mm tt',
            'dateFormat'=>'mm-dd-yy',
            'stepMinute' => 15,
            'showHour' => 'true',
            //'altField' => '#phila_start_hidden',
            //'altFormat'=> "@",
            //'altFieldTimeOnly' => false,
            'controlType'=> 'select',
            'oneLine'=> true,
            //'altTimeFormat' => 'c',
            'timeInput' => true,
          ),
          'timestamp' => true

        ),
        array(
          'name'  => 'Alert End Time',
          'id'    => $prefix . 'alert_end',
          'type'  => 'datetime',
          'class' =>  'end-time',
          'size'  =>  25,
          'desc'  => 'Note: The start and end times communicate an alert’s length in the alert bar. The times also define when an alert is visible on the site\'s homepage. <b>Leaving the end time blank will render "until further notice" on the alert. The alert will then need to be unpublished, or given an explicit end time.</b>',
          'js_options' =>  array(
            'timeFormat' => 'hh:mm tt',
            'dateFormat' => 'mm-dd-yy',
            'stepMinute' => 15,
            'showHour' => 'true',
            //'altField' => '#phila_end_hidden',
            //'altFormat'=> "@",
            //'altFieldTimeOnly' => false,
            'controlType'=> 'select',
            'oneLine'=> true,
            //'altTimeFormat' => 'c',
            'timeInput' => true
          ),
          'timestamp' => true

        ),
      )
    );//site wide alert boxes
    return $meta_boxes;
  }

}
