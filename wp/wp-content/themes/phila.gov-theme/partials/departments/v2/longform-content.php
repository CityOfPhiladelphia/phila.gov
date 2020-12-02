<?php
/* Displays list of PDFs assigned to this page's category. */

$app_id = 'vue-app';
$longform_file_path = 'https://www.phila.gov/embedded/longform-content/'.$phila_environment;
$vuejs_js_ids = [$longform_file_path.'/js/chunk-vendors.js', $longform_file_path.'/js/app.js'];
$vuejs_css_ids = [$longform_file_path.'/css/chunk-vendors.css', $longform_file_path.'/css/app.css'];

include(locate_template( 'partials/vue-apps/vue-register.php' ) );