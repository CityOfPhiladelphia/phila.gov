<?php
/* Displays list of PDFs assigned to this page's category. */

$app_id = 'vue-app';

$vuejs_js_ids = ['https://www.phila.gov/embedded/longform-content/production/js/chunk-vendors.js?test5000', 'https://www.phila.gov/embedded/longform-content/production/js/app.js?test5000'];
$vuejs_css_ids = ['https://www.phila.gov/embedded/longform-content/production/css/chunk-vendors.css?test5000', 'https://www.phila.gov/embedded/longform-content/production/css/app.css?test5000'];

include(locate_template( 'partials/vue-apps/vue-register.php' ) );