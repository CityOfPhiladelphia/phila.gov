<?php
/* Displays list of PDFs assigned to this page's category. */

$app_id = 'vue-app';

$vuejs_js_ids = ['https://www.phila.gov/embedded/longform-content/production/js/chunk-vendors.js', 'https://www.phila.gov/embedded/longform-content/production/js/app.js'];
$vuejs_css_ids = ['https://www.phila.gov/embedded/longform-content/production/css/chunk-vendors.css', 'https://www.phila.gov/embedded/longform-content/production/css/app.css'];

include(locate_template( 'partials/vue-apps/vue-register.php' ) );