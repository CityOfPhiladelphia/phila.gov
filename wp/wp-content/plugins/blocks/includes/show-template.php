<?php

/* Add template path
  ================================================== */
if ( ! function_exists( 'show_template' ) ) {
  function show_templates() {

    global $template;

    $path = $template;
    $path_part = explode('/', $path);
    $path_parts = array_slice($path_part, 5);
    $new_path = implode(' ', $path_parts);
    $new_path = str_replace('themes', 'skin', $new_path);
    $new_path = str_replace('plugins', 'modules', $new_path);
    $new_path = str_replace('.php', '', $new_path);

    return $new_path;

  }

}



/* Add browser info to the HTML tag
  ================================================== */
if ( ! function_exists( 'spartan_html_class' ) ) {
  function spartan_html_classs($output) {

    $server_info = ' class="no-js spartan '. show_templates().'"';
    $server_info .= ' data-useragent="' . $_SERVER['HTTP_USER_AGENT'] . '"';
    $server_info .= ' data-spartan="' . show_templates(). '"';

    return $output . $server_info;

  }
  add_filter('language_attributes', 'spartan_html_classs');

}