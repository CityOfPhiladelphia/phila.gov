<?php
/* Displays list of PDFs assigned to this page's category. */

?>
<?php
$tables = rwmb_meta('phila_document_table');
$no_pagination = rwmb_meta('phila_doc_no_paginate') ;
$app_title = 'Document Finder';
//ensure 0 index for js initialization
$c = -1;

$app_id = 'vue-app';

$vuejs_js_ids = ['https://philagov-vue-apps.s3.amazonaws.com/document-finder/v2/js/chunk-vendors.js', 'https://philagov-vue-apps.s3.amazonaws.com/document-finder/v2/js/app.js'];
$vuejs_css_ids = ['https://philagov-vue-apps.s3.amazonaws.com/document-finder/v2/css/chunk-vendors.css', 'https://philagov-vue-apps.s3.amazonaws.com/document-finder/v2/css/app.css'];

?>
<?php include(locate_template( 'partials/vue-apps/vue-register.php' ) );?>