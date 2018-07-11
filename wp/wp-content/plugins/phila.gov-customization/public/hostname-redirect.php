<?php
/*
*  Non-logged in users get kicked back to the public site.
*/

add_action('template_redirect', 'my_non_logged_redirect');

function my_non_logged_redirect(){
  global $SERVER;
  $domain = parse_url($SERVER['REQUEST_URI'], PHP_URL_HOST);
  $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

  if ( !is_user_logged_in() && $domain === 'admin.phila.gov' ) {
    wp_redirect( 'https://'.'www.phila.gov' . $path );
    die();
  }else if(!is_user_logged_in() && $domain === 'admin.phila.website'){
    wp_redirect( 'https://' . 'www.phila.website' . $path );
  }
}
