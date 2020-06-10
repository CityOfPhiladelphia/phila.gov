<?php 
/*
 *
 * Partial for registering vue app files
 * Required params: 
 * 
 * (string) $app_id
 * (array of urls) $vuejs_js_ids
 * (array of urls) $vuejs_css_ids
 * 
 */

if (is_array($vuejs_js_ids)) {
  $count = 1;
  foreach($vuejs_js_ids as $url) {
    $handle = $post->post_name . '-vue-app-js-url-' . $count . $app_id;
    wp_enqueue_script($handle, $url, array(), null, true );
    $count++;
  }
}

if (is_array($vuejs_css_ids)) {
  $count = 1;
  foreach($vuejs_css_ids as $url) {
    $handle = $post->post_name . '-vue-app-css-url-' . $count . $app_id;
    wp_enqueue_style($handle, $url);
    $count++;
  }
}

include(locate_template( 'partials/vue-apps/app-container.php' ) );
?>
