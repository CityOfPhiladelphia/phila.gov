<?php
/* Displays list of PDFs assigned to this page's category. */

$app_id = 'vue-app';

$vuejs_js_ids = ['https://www.phila.gov/embedded/longform-content/staging/js/chunk-vendors.js?check7573', 'https://www.phila.gov/embedded/longform-content/staging/js/app.js?check7573'];
$vuejs_css_ids = ['https://www.phila.gov/embedded/longform-content/staging/css/chunk-vendors.css?check7573', 'https://www.phila.gov/embedded/longform-content/staging/css/app.css?check7573'];

include(locate_template( 'partials/vue-apps/vue-register.php' ) );