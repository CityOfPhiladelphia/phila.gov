<?php
/**
 * Define our custom role. If a user is not PHILA_ADMIN, then they get things hidden.
 *
 * @since   0.11.0
 */
// define the custom capability name for protected content
define ( 'PHILA_ADMIN', 'phila_see_all_content' );

add_action( 'wp_loaded', 'phila_roles_and_capabilities' );

function phila_roles_and_capabilities(){

  // add the custom capability to other qualified roles
  get_role( 'administrator' )->add_cap( PHILA_ADMIN );
  get_role( 'editor' )->add_cap( PHILA_ADMIN );

}
