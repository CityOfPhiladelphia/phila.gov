<?php
/*
 *
 * Partial for rendering Department "Row Two" Content - Calendar
 */
?>

<?php
  $cal_id = rwmb_meta('phila_full_width_calendar_id');
  $cal_category = rwmb_meta('phila_calendar_owner')->term_id;
?>

<?php include( locate_template( 'partials/departments/v2/calendar.php' ) ); ?>
<!-- End Calendar Module -->
