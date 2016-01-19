<?php

/**
* @package  phila.gov-customization
* @since    0.5.6
*/

add_action('init', 'PhilaGovDepartmentAuthorMedia::initialize');

class PhilaGovDepartmentAuthorMedia {

  public static function initialize() {
    //only in admin
    if ( ! is_admin() )
      return;

     if ( ! current_user_can( PHILA_ADMIN ) ){

     /*
      * Defined in /media-library-assistant/includes/class-mla-data.php
      */
      add_filter( 'mla_media_modal_query_final_terms', 'PhilaGovDepartmentAuthorMedia::filter_media_modal_query_final_terms', 10, 1 );

      /*
      * Defined in /media-library-assistant/includes/class-mla-options.php
      */
      add_filter( 'mla_update_attachment_metadata_prefilter', 'PhilaGovDepartmentAuthorMedia::phila_mla_update_attachment_metadata_prefilter_filter', 10, 3 );

    }
  }

  /**
  * Get category slugs for later use
  *
  *
  * @since 0.22.0
  * @return array of category slugs matched by current user role
  */

  public static function get_current_cat_slugs(){
    $categories_args = array(
      'type'                     => 'post',
      'child_of'                 => 0,
      'parent'                   => '',
      'orderby'                  => 'name',
      'order'                    => 'ASC',
      'hide_empty'               => 1,
      'hierarchical'             => 0,
      'taxonomy'                 => 'category',
      'pad_counts'               => false
    );

    $categories = get_categories( $categories_args );

    $cat_slugs =[];

    //loop through and push slugs to $cat_slugs
    foreach( $categories as $category ){
      array_push( $cat_slugs, $category->slug );
    }
    //add category slugs to their own array

    if ( is_user_logged_in() ){
      //define current_user, we should only do this when we are logged in
      $user = wp_get_current_user();
      $all_user_roles = $user->roles;
      $all_user_roles_to_cats = str_replace('_', '-', $all_user_roles);

      //matches rely on Category slug and user role name matching
      //if there are matches, then you have a secondary role that should not be allowed to see other's menus, etc.
      $current_user_cat_assignment = array_intersect( $all_user_roles_to_cats, $cat_slugs );
    }

    return $current_user_cat_assignment;
  }

  /**
  * MLA Edit Media "Query Attachments" final terms Filter
  *
  * Changes the terms of the
  * Media Manager Modal Window "Query Attachments" query
  * after they are processed by the "Prepare List Table Query" handler.
  *
  * @since 1.01
  *
  * @param    array    WP_Query request prepared by "Prepare List Table Query"
  * @uses get_current_cat_slugs
  */

  public static function filter_media_modal_query_final_terms( $request ) {

    $current_user_cat_assignment = PhilaGovDepartmentAuthorMedia::get_current_cat_slugs();

    if ( isset( $request['tax_query'] ) ) {
      $tax_query = $request['tax_query'];
      $tax_query['relation'] = 'AND';
    } else {
      $tax_query = array();
    }
    //sets the tax query with our current user slugs
    $tax_query[] = array( 'taxonomy' => 'category', 'operator' => 'IN', 'field' => 'slug', 'terms' => $current_user_cat_assignment);

    $request['tax_query'] = $tax_query;

    return $request;

  }


  /**
    * MLA Edit Media "Query Attachments" final terms Filter
    * @param    array    attachment metadata
    * @param    integer  The Post ID of the new/updated attachment
    * @param    array    Processing options, e.g., 'is_upload'
    * @uses get_current_cat_slugs
    *
    * @return    array    updated attachment metadata
  */
  public static function phila_mla_update_attachment_metadata_prefilter_filter( $data, $post_id, $options ) {

    $current_user_cat_assignment = PhilaGovDepartmentAuthorMedia::get_current_cat_slugs();

    $single_id = implode('|', $current_user_cat_assignment );

    $cat_id = get_category_by_slug( $single_id );

    if ( $options['is_upload'] ) {

      $term_ids = array( $cat_id->term_id );
      $result = wp_set_object_terms( $post_id, $term_ids, 'category' );

      return $result;
   }

  }

}
