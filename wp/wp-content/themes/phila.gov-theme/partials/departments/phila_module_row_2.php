<?php
/*
 *
 * Partial for rendering Department "Row Two" Content - Calendar
 */
?>

<?php
  $cal_id = rwmb_meta('phila_full_width_calendar_id');
  $owner = rwmb_meta('phila_calendar_owner');
  $cal_category = !empty($owner) ? $owner->slug : '';
?>

<?php include( locate_template( 'partials/departments/v2/calendar.php' ) ); ?>
<!-- End Calendar Module -->
