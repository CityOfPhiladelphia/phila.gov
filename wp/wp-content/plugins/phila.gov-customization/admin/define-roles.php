<?php
/**
 * Define our custom roles.
 *
 * @since   0.11.0
 */
// If a user is not PHILA_ADMIN, then much of the admin is hidden.
define ( 'PHILA_ADMIN', 'phila_see_all_content' );

add_action( 'wp_loaded', 'phila_roles_and_capabilities' );

function phila_roles_and_capabilities(){

  // add the custom capability to other qualified roles
  get_role( 'administrator' )->add_cap( PHILA_ADMIN );
  get_role( 'editor' )->add_cap( PHILA_ADMIN );

}
