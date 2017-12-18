<?php
/*
 *
 * Partial for rendering Cloneable Custom Textareas
 *
 */

?>
<?php
if ( !empty( $custom_text )):
$custom_text_title = isset($custom_text['phila_custom_row_title']) ? $custom_text['phila_custom_row_title'] : '';
$custom_text_group = $custom_text['phila_custom_text_group'];
?>
<div class="<?php echo !isset($multi_full_row) ? 'large-16' : '' ?> columns custom-text-multi">
<h2 class="contrast"><?php echo isset($custom_text['phila_custom_row_title']) ? $custom_text['phila_custom_row_title'] : ''; ?></h2>
<?php if ( is_array( $custom_text_group ) ):?>
<?php $item_count = count($custom_text_group); ?>
<?php $columns = phila_grid_column_counter( $item_count ); ?>
<div class="row <?php if( $item_count > 1 ) echo 'equal-height';?> ">
  <?php foreach ($custom_text_group as $key => $value):?>
    <div class="medium-<?php echo $columns ?> columns <?php if( $item_count > 1 ) echo 'equal';?>">

      <?php if ( isset( $custom_text_group[$key]['phila_custom_text_title'] ) && $custom_text_group[$key]['phila_custom_text_title'] != '') : ?>
        <h3><?php echo $custom_text_group[$key]['phila_custom_text_title']; ?></h3>
      <?php endif;?>

      <?php if ( isset( $custom_text_group[$key]['phila_custom_text_content'] ) && $custom_text_group[$key]['phila_custom_text_content'] != '') : ?>
        <p><?php echo do_shortcode(wpautop($custom_text_group[$key]['phila_custom_text_content'] )); ?></p>
      <?php else :?>
        <div class="placeholder">
          Please enter content.
        </div>
      <?php endif;?>

    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
</div>
<?php endif; ?>
