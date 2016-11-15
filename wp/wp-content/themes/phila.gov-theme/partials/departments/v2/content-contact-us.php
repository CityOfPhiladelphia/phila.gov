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
<?php foreach ($contact_us as $row) : ?>
    <div class="row">
      <div class="columns">
        <?php
        $row_title = isset( $row['phila_contact_row_title']) ? $row['phila_contact_row_title'] : '';
        ?>
        <?php if ($row_title) :?>
          <h2 class="contrast"> <?php echo $row_title; ?> </h2>
        <?php endif; ?>
        <?php foreach($row['phila_contact_group'] as $column ): ?>
          <?php $column_title = isset( $column['phila_contact_column_title']) ? $column['phila_contact_column_title'] : '';?>
          <h3><?php echo $column_title ?> </h3>
          
        <?php endforeach; ?>
      </div>
    </div>
<?php endforeach; ?>
