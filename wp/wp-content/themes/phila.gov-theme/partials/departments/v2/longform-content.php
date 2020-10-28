<?php
/* Displays list of PDFs assigned to this page's category. */

$app_id = 'vue-app';

$vuejs_js_ids = ['https://www.phila.gov/embedded/longform-content/staging/js/chunk-vendors.js?check876', 'https://www.phila.gov/embedded/longform-content/staging/js/app.js?check876'];
$vuejs_css_ids = ['https://www.phila.gov/embedded/longform-content/staging/css/chunk-vendors.css?check876', 'https://www.phila.gov/embedded/longform-content/staging/css/app.css?check876'];

include(locate_template( 'partials/vue-apps/vue-register.php' ) );