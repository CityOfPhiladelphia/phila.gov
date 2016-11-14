<?php
/**
 * Display Contact Page
 *
 * @package phila-gov
 */
?>
<?php
$contact_us_vars = rwmb_meta('phila_contact_us');
$contact_us = phila_loop_clonable_metabox($contact_us_vars);
?>

<div class="row">
  <div class="columns small-24 medium-8">
    <?php var_dump($contact_us); ?>
  </div>
</div>
