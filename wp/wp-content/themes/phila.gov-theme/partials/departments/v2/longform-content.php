<?php
/* Displays list of PDFs assigned to this page's category. */

$app_id = 'vue-app';

$vuejs_js_ids = ['https://www.phila.gov/embedded/longform-content/dev-2/js/chunk-vendors.js?tester345', 'https://www.phila.gov/embedded/longform-content/dev-2/js/app.js?tester345'];
$vuejs_css_ids = ['https://www.phila.gov/embedded/longform-content/dev-2/css/chunk-vendors.css?tester345', 'https://www.phila.gov/embedded/longform-content/dev-2/css/app.css?tester345'];

include(locate_template( 'partials/vue-apps/vue-register.php' ) );