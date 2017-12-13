<?php
/**
 * A place for calendar mods
 */

if ( class_exists("Phila_Gov_Calendar" ) ){
  $phila_calendar_load = new Phila_Gov_Calendar();
}

 class Phila_Gov_Calendar {

  public function __construct(){
    add_action( 'init', array( $this, 'register_categories_for_cal_plugin' )  );


  }
  function register_categories_for_cal_plugin(){
    register_taxonomy_for_object_type( 'category',  'calendar' );
  }
}
