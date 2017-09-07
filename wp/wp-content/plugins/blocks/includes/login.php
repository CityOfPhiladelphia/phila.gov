<?php

/* Enqueue wp-login CSS and JS
================================================== */
if ( ! function_exists( 'spartan_login_scripts_and_styles' ) ) {
  function spartan_login_scripts_and_styless() {

    global $wp_styles;

    wp_register_style( 'wp-login', wpcmsb_plugin_url() . '/includes/less/admin/login.less', array(), '' );

    wp_enqueue_style('wp-login');

  }
  // add_action('login_enqueue_scripts', 'spartan_login_scripts_and_styles', 999);
}