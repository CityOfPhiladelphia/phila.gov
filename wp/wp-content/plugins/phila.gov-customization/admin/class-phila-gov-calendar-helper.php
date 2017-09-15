<?php
/**
 * Create "master" calendar and use as template for all other calendars
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
 * @since beta.phila.gov
 */

if ( class_exists( "Phila_Gov_Calendar_Helper" ) ){
  $admin_menu_labels = new Phila_Gov_Calendar_Helper();
}

class Phila_Gov_Calendar_Helper {

  public function __construct(){
    add_filter( 'rwmb_meta_boxes', array( $this, 'register_master_cal_meta'), 10, 1 );
    //add_filter('admin_init', array($this, 'copy_post_meta'));

    add_action( 'save_post', array($this, 'copy_master_calendar') );

    add_action( 'admin_init', array($this, 'update_calendars') );

  }

  function register_master_cal_meta($meta_boxes){
    $meta_boxes[] = array(
      'id'  => 'phila_use_master_cal',
      'title' => 'Master calendar settings',
      'pages' => array('calendar'),
      'fields' => array(
        array(
          'id'  => 'is_master_calendar',
          'type'  => 'checkbox',
          'desc'  => 'Make master template?',
          'std' => 0,
        ),
        array(
          'id'  => 'phila_use_master',
          'type'=> 'checkbox',
          'desc'  => 'Use the master calendar template?',
          'std' => 1,
          'visible' => ['is_master_calendar', false],
          'admin_columns' => array(
            'position'   => 'after title',
            'title'      => __( 'Uses master' ),
            'sort'       => true,
          ),
        ),
      ),
    );
    return $meta_boxes;
  }
  function get_master(){
    $query = new WP_Query(
        array(
          'post_status' => 'private',
          'post_type' => 'calendar',
          'posts_per_page'=> -1,
          'meta_query' => array(
            array(
            'key'     => 'is_master_calendar',
            'value'   => 1,
          ),
         ),
      ));

    if( ! empty( $query->posts ) ) {
      foreach( $query->posts as $post ) {
        //NOTE: this assumes there is only one "master", should probably be handled in the future
       $master = $post->ID;
        return $master;
      }
    }
  }

  /**
  * This function gets the current post type in admin
  * Via @DomeicF
  * Github https://gist.github.com/DomenicF
  */
  public static function get_current_post_type() {
    global $post, $typenow, $current_screen;
    //we have a post so we can just get the post type from that
    if ( $post && $post->post_type ) {
      return $post->post_type;
    }
    //check the global $typenow - set in admin.php
    elseif ( $typenow ) {
      return $typenow;
    }
    //check the global $current_screen object - set in sceen.php
    elseif ( $current_screen && $current_screen->post_type ) {
      return $current_screen->post_type;
    }
    //check the post_type querystring
    elseif ( isset( $_REQUEST['post_type'] ) ) {
      return sanitize_key( $_REQUEST['post_type'] );
    }
    //lastly check if post ID is in query string
    elseif ( isset( $_REQUEST['post'] ) ) {
      return get_post_type( $_REQUEST['post'] );
    }
    //we do not know the post type!
    return null;
  }
  function get_all_non_masters(){
    $non_masters = array();
    $query = new WP_Query(
        array(
        'post_type' => 'calendar',
        'meta_query' => array(
          array(
            'relation' => 'AND',
            array(
              'key'     => 'is_master_calendar',
              'value'   => 0,
            ),
            array(
              'key'     => 'phila_use_master',
              'value'   => 1,
            )
          )
         ),
      ));

    if( ! empty( $query->posts ) ) {
      foreach( $query->posts as $post ) {
        array_push($non_masters, $post->ID);
      }
      return $non_masters;
    }else{
      return null;
    }

  }

  function get_master_meta(){

    $id = $this->get_master();
    $meta = get_post_meta($id);

    $meta = array_diff_key($meta, ['is_master_calendar'=> '', 'phila_use_master'=> '', '_google_calendar_id'=> '']);

    return $meta;
  }

  function get_master_content(){
    $id = $this->get_master();
    $content = get_post($id);

    //All we really want is the content & post type
    foreach ($content as $key => $post) {
      if ( ($key != 'post_content') ) {
        unset($content->$key);
      }
    }
    $content = get_object_vars($content);
    return $content;
  }

  function copy_master_post_data($post_id){
    $post_content = $this-> get_master_content();

    $post_data = array(
      'ID'       => $post_id,
      'post_status' => 'private',
      'post_content' => maybe_unserialize($post_content['post_content']),
    );

    remove_action( 'save_post', array( $this, 'copy_master_calendar' ) );

    wp_update_post( $post_data, true );

    add_action( 'save_post', array( $this, 'copy_master_calendar' ) );

  }

  function copy_master_calendar(){
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
       return;

    $post_type = $this->get_current_post_type();
    if ($post_type != 'calendar'){
      return;
    }

    $posts = $this->get_all_non_masters();
    $post_meta = $this->get_master_meta();

    foreach( $posts as $post ) {
      foreach($post_meta as $k => $v) {
        update_post_meta( $post, $k, wp_slash(maybe_unserialize($v[0])) );
      }
      $this->copy_master_post_data($post);
    }
  }

  function update_calendars(){
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
      return;

    $post_type = $this->get_current_post_type();

    if ($post_type != 'calendar')
      return;

      global $post;
      $post_ID = isset($post->ID) ? $post->ID : '';

      $master_id = $this->get_master();

      if( $post_ID == $master_id ) {
        $post_obj = get_post($master_id);

      if( $post_obj->post_modified_gmt == $post_obj->post_date_gmt ){
        //this is a new master, not a revision of an existing one
        return;
      }else{
        $this->copy_master_calendar();
      }
      }
    }

}
