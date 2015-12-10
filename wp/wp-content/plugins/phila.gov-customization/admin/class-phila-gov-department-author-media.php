<?php
/**
* @package  phila.gov-customization
* @since    0.5.6
*/

/*
* Install the filters at an early opportunity
*/
add_action('init', 'PhilaGovDepartmentAuthorMedia::initialize');

class PhilaGovDepartmentAuthorMedia {

	/**
	 * @since 1.00
	 *
	 * @return	void
	 */

	public static function initialize() {
		//only in admin
		if ( ! is_admin() )
			return;

		/*
		 * Defined in /media-library-assistant/includes/class-mla-data.php
		 */
 		if ( ! current_user_can( PHILA_ADMIN ) ){
			add_filter( 'mla_media_modal_query_final_terms', 'PhilaGovDepartmentAuthorMedia::filter_media_modal_query_final_terms', 10, 1 );

		  add_filter( 'ajax_query_attachments_args', 'PhilaGovDepartmentAuthorMedia::show_current_user_attachments', 10, 1 );
		}
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
	*/

	public static function filter_media_modal_query_final_terms( $request ) {

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
   * Only show attachments this user uploaded.
   *
   * @since 0.12.0
   * @uses get_current_user_id() https://codex.wordpress.org/Function_Reference/get_current_user_id
   * @return $cat_slugs array Returns an array of all categories.
   */

  function show_current_user_attachments( $query ) {
    $user_id = get_current_user_id();
    if ( $user_id ) {
      $query['author'] = $user_id;
    }
    return $query;
	}
}
