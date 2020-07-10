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
    $meta_boxes[] = array(
      'id'       => 'site-wide-alert',
      'title'    => 'Alert Settings',
      'pages'    => array( 'site_wide_alert' ),
      'context'  => 'side',
      'priority' => 'high',


      'fields' => array(
        array(
          'id'  => 'phila_alert_color',
          'name'  => 'Alert background',
          'type'  => 'select',
          'options' => array(
            'blue'       => 'Blue',
            'orange' => 'Orange',
          ),
        ),
        array(
          'id'  => 'phila_alert_icon',
          'name'  => 'Choose icon',
          'type'  => 'text',
          'desc'  => 'Choose a <a href="https://fontawesome.com/icons?d=gallery" target="_blank">Font Awesome</a> icon. E.g.: fas fa-bell.',
        ),
        array(
          'id'  => 'phila_alert_active',
          'name'  => 'Override start and end times and make alert active?',
          'type'  => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No',
        ),
        array(
          'name'  => 'Alert Start Time',
          'id'    => 'phila_alert_start',
          'class' =>  'start-time',
          'type'  => 'datetime',
          'size'  =>  25,
          'hidden'  => array( 'active', '=', 1),
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
          'id'    => 'phila_alert_end',
          'type'  => 'datetime',
          'class' =>  'end-time',
          'size'  =>  25,
          'hidden'  => array( 'active', '=', 1),
          'desc'  => 'Note: The start and end times communicate an alert’s length in the alert bar. Use the active alert feature to turn alerts on and ignore this setting.',
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
      ),
    );//site wide alert boxes
    return $meta_boxes;
  }

}
