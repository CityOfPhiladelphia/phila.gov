<?php
/* Displays list of PDFs assigned to this page's category. */

$app_id = 'vue-app';

$vuejs_js_ids = ['https://www.phila.gov/embedded/longform-content/staging/js/chunk-vendors.js?test765', 'https://www.phila.gov/embedded/longform-content/staging/js/app.js?test765'];
$vuejs_css_ids = ['https://www.phila.gov/embedded/longform-content/staging/css/chunk-vendors.css?test765', 'https://www.phila.gov/embedded/longform-content/staging/css/app.css?test765'];

include(locate_template( 'partials/vue-apps/vue-register.php' ) );