<?php
/*
*  Non-logged in users get kicked back to the public site.
*/

add_action('template_redirect', 'admin_phila_redirect');

function admin_phila_redirect(){
  $user_agent = $_SERVER['HTTP_USER_AGENT'];
  if (strpos($user_agent, 'beta-static-generator') === true){
    return;
  }

  $domain = parse_url($_SERVER['HTTP_HOST']);
  $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

  if ( !is_user_logged_in() && $domain['path'] === 'admin.phila.gov' ) {
    wp_redirect( 'https://'.'www.phila.gov' . $path );
    die();
  }else if( !is_user_logged_in() && $domain['path'] === 'staging-admin.phila.gov' ){
    wp_redirect( 'https://' . 'staging-www.phila.gov' . $path );
    die();
  }
}
