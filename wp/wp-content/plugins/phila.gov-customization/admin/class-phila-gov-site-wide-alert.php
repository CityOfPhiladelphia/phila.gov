<?php

/**
* Add alerts to alpha homepage
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

    //add_filter('manage_edit-site_wide_alert_columns', array( $this, 'site_wide_alert_columns' ) );

    //add_filter('manage_edit-site_wide_alert_sortable_columns', array( $this, 'site_wide_alert_columns' ) );

    //add_action('manage_site_wide_alert_posts_custom_column',  array( $this, 'site_wide_alert_column_output'), 10, 2);

    //add_filter('request', array( $this, 'my_sort_metabox') );

    add_action( 'admin_enqueue_scripts', array($this, 'enqueue_alert_scripts') );

    //add_action( 'template_redirect', array($this, 'redirect_alert_pages') );

  }

  function phila_register_meta_boxes( $meta_boxes ){
    $prefix = 'phila_';
    $meta_boxes[] = array(
      'id'       => 'site-wide-alert',
      'title'    => 'Alert Settings',
      'pages'    => array( 'site_wide_alert' ),
      'context'  => 'side',
      'priority' => 'high',
      'autosave' => 'true',

      'fields' => array(
        array(
          'name'  => 'Alert Type',
          'id'    => $prefix . 'type',
          'type'  => 'select',
          'std'=> '0',
          'options' =>  array(
            'Code Blue Effective' => 'Code Blue Effective',
            'Code Red Effective' => 'Code Red Effective',
            'Code Orange Effective' => 'Code Orange Effective',
            'Code Grey Effective'  =>  'Code Grey Effective',
            'Other' => 'Other'
          )
        ),
        array(
          'name'  => 'Name of Alert Type',
          'id'    => $prefix . 'type-other',
          'type'  => 'text',
          'class' => 'type-other',
          'size'  => 25,
          'desc'  => 'E.g. <i>Hurricane Warning</i>',
          'visible' => array('phila_type', '=', 'Other'),
        ),
        array(
          'name'  => 'Custom Alert Icon',
          'id'    => $prefix . 'icon',
          'type'  => 'text',
          'class' => 'other-icon',
          'size'  => 25,
          'desc'  => '<a href="http://ionicons.com/" target="_new">Choose icon</a>. Enter icon name only i.e. <i>ion-alert-circled</i>',
          'visible' => array('phila_type', '=', 'Other'),
        ),
        array(
          'name'  => 'Non-Urgent Alert',
          'id'    => $prefix . 'alert-class',
          'type'  => 'checkbox',
          'class' => 'other-alert-class',
          'desc'  => 'Use the "subtle" class',
          'visible' => array('phila_type', '=', 'Other'),
        ),
        array(
          'type' => 'divider',
          'visible' => array('phila_type', '=', 'Other'),
        ),
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
          'desc'  => 'Note: The start and end times communicate an alert’s length in the alert bar. The times define when an alert is visible on the site\'s homepage.',
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
        /*
        array(
          'name'  => 'Alert Start Time Hidden',
          'id'    => $prefix . 'start_hidden',
          'type'  => 'datetime',
          'class' =>  'start-time-hidden',
          'size'  =>  25,
          'timestamp' => true
        ),
        array(
          'name'  => 'Alert End Time Hidden',
          'id'    => $prefix . 'end_hidden',
          'type'  => 'datetime',
          'class' =>  'end-time-hidden',
          'size'  =>  25,
          'timestamp' => true
        ),
        */
      )
    );//site wide alert boxes
    return $meta_boxes;
  }

  /*
   * Show active alerts on admin screen
   */
  function site_wide_alert_columns( $columns ) {
    $columns["active_alert"] = "Active";
    return $columns;
  }

  function site_wide_alert_column_output( $colname, $cptid ) {
    echo get_post_meta( $cptid, 'phila_active', true );
  }

  function my_sort_metabox( $vars ) {
    if( array_key_exists('orderby', $vars )) {
      if('Active' == $vars['orderby']) {
        $vars['orderby'] = 'meta_value';
        $vars['meta_key'] = 'phila_end';
      }
    }
    return $vars;
  }

  /**
  * Add scripts only to site_wide_alert posts
  *
  */
  function enqueue_alert_scripts($hook) {
    global $post;
    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
      if ( 'site_wide_alert' === $post->post_type ) {
          wp_enqueue_script( 'alerts-ui', plugin_dir_url( __FILE__ ) . 'js/alerts.js', array('jquery'));
      }
    }
  }

  /**
  * Don't let users find alert pages, they are moot
  *
  */
  function redirect_alert_pages() {
    if ( !is_preview() && is_singular( 'site_wide_alert' ) ) {
      wp_redirect( home_url(), 302 );
      exit;
    }
  }
}
