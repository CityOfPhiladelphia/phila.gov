<?php
/**
 * Removes and redirects all requests at /service/foo/ to /foo/
 * Thanks to: http://ryansechrest.com/2013/04/remove-post-type-slug-in-custom-post-type-url-and-move-subpages-to-website-root-in-wordpress/
 *
 * @package  phila.gov-customization
 * @since    0.17.3
 */


 // Instantiate new class
 $phila_service_page_rewrites = new Phila_Gov_Service_Rewrites();

 class Phila_Gov_Service_Rewrites {

  public function __construct(){

    add_filter( 'post_type_link', array($this, 'filter_post_type_link' ), 10, 3 );

    add_action( 'pre_get_posts', array($this, 'pre_get_posts_services' ) );

  }

  /**
   *
   * Filter the post type link
   * @param $permalink Wordpress query object
   * @param $post Post object
   * @return $permalink Our new slugless permalink
   */

  function filter_post_type_link( $permalink, $post ) {
    if ( ! gettype( $post ) == 'post' ) {
      return $permalink;
    }
    switch ( $post->post_type ) {
      case 'service_post':
        $permalink = get_home_url() . '/' . $post->post_name . '/';
        break;
    }

    return $permalink;
  }

  /**
   * Tell WP what kind of post we are dealing with, since the slug is gone now.
   *
   * @param $query The current query
   */


  function pre_get_posts_services( $query ) {
    global $wpdb;

    if( ! $query->is_main_query() ) {
      return;
    }

    $post_name = $query->get( 'pagename' );

    $post_type = $wpdb->get_var(
      $wpdb->prepare(
          'SELECT post_type FROM ' . $wpdb->posts . ' WHERE post_name = %s LIMIT 1',
          $post_name
      )
    );

    $result = $wpdb->get_row(
      $wpdb->prepare(
        'SELECT wpp1.post_type, wpp2.post_name AS parent_post_name FROM ' . $wpdb->posts . ' as wpp1 LEFT JOIN ' . $wpdb->posts . ' as wpp2 ON wpp1.post_parent = wpp2.ID WHERE wpp1.post_name = %s LIMIT 1',
        $post_name
      )
    );

    switch($post_type) {
      case 'service_post':
        if ( $result->parent_post_name !== '' ) {
            $post_name = $result->parent_post_name . '/' . $post_name;
        }
        $query->set('service_post', $post_name);
        $query->set('post_type', $post_type);
        $query->is_single = true;
        $query->is_page = false;

        break;
    }

  }

}//Phila_Gov_Service_Rewrites
