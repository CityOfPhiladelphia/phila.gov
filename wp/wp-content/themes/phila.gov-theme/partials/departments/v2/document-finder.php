<?php
/* Displays list of PDFs assigned to this page's category. */

?>
<?php
$tables = rwmb_meta('phila_document_table');
$no_pagination = rwmb_meta('phila_doc_no_paginate') ;
//ensure 0 index for js initialization
$c = -1;
?>
<?php include(locate_template( 'partials/departments/content-programs-initiatives.php' ) );?>