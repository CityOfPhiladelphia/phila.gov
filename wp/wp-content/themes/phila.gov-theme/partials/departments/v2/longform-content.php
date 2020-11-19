<?php
/* Displays list of PDFs assigned to this page's category. */

$app_id = 'vue-app';

$vuejs_js_ids = ['https://www.phila.gov/embedded/longform-content/production/js/chunk-vendors.js?check7000', 'https://www.phila.gov/embedded/longform-content/production/js/app.js?check7000'];
$vuejs_css_ids = ['https://www.phila.gov/embedded/longform-content/production/css/chunk-vendors.css?check7000', 'https://www.phila.gov/embedded/longform-content/production/css/app.css?check7000'];

include(locate_template( 'partials/vue-apps/vue-register.php' ) );