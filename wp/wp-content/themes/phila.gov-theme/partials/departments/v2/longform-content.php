<?php
/* Displays list of PDFs assigned to this page's category. */

$app_id = 'vue-app';

$vuejs_js_ids = ['https://www.phila.gov/embedded/longform-content/dev-check/js/chunk-vendors.js?moh1', 'https://www.phila.gov/embedded/longform-content/dev-check/js/app.js?moh1'];
$vuejs_css_ids = ['https://www.phila.gov/embedded/longform-content/dev-check/css/chunk-vendors.css?moh1', 'https://www.phila.gov/embedded/longform-content/dev-check/css/app.css?moh1'];

include(locate_template( 'partials/vue-apps/vue-register.php' ) );