<?php
/**
 * Removes and redirects all requests at /service/foo/ to /foo/
 * @package  phila.gov-customization
 * @since    0.17.3
 */


 // Instantiate new class
 $phila_service_page_rewrites = new PhilaGovServiceRewrites();

 class PhilaGovServiceRewrites {

  public function __construct(){

    add_filter( 'post_type_link', array( $this, 'remove_cpt_slug' ), 10, 3 );

    add_action( 'pre_get_posts', array( $this, 'parse_request' ) );

    add_action( 'template_redirect', array( $this, 'redirect_service_page' ) );
  }

  /**
   * Remove /service/ slug from published permalinks.
   * @param $post_link The current link
   * @param $post The post object
   * @return Post link without /service/
   */

  function remove_cpt_slug( $post_link, $post ) {

    if ( 'service_post' != $post->post_type || 'publish' != $post->post_status ) {
        return $post_link;
    }

    $post_link = str_replace( '/' . 'service' . '/', '/', $post_link );

    return $post_link;
  }


  /**
   *
   * Posts, pages and services need to be unique, otherwise, there will be trouble
   * @param $query Wordpress query object
   *
   */

  function parse_request( $query ) {

    // Ignore main query
    if ( ! $query->is_main_query() )
        return;

    // This really only works with non-hierarchical post types
    if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
        return;
    }

    // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
    if ( ! empty( $query->query['name'] ) ) {
        $query->set( 'post_type', array( 'post', 'service_post', 'page' ) );
    }
  }

  function redirect_service_page() {
    if ( get_query_var('post_type') == 'service_post') {
      global $post;
      $current_url = get_permalink( $post->ID );
      $pattern = '/service/';
      $replacement = '/';
      $updated_url =  preg_replace($pattern, $replacement, $current_url);

      wp_redirect( $updated_url );
      exit();
    }
  }

}//PhilaGovServiceRewrites
