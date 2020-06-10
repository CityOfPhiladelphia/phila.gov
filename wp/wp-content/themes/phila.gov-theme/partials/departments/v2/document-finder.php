<?php
/* Displays list of PDFs assigned to this page's category. */

$tables = rwmb_meta('phila_document_table');
$app_title = rwmb_meta('phila_vue_app_title') ;;
$app_id = 'vue-app';

$vuejs_js_ids = ['https://philagov-vue-apps.s3.amazonaws.com/document-finder/v2/js/chunk-vendors.js', 'https://philagov-vue-apps.s3.amazonaws.com/document-finder/v2/js/app.js'];
$vuejs_css_ids = ['https://philagov-vue-apps.s3.amazonaws.com/document-finder/v2/css/chunk-vendors.css', 'https://philagov-vue-apps.s3.amazonaws.com/document-finder/v2/css/app.css'];

include(locate_template( 'partials/vue-apps/vue-register.php' ) );
?>