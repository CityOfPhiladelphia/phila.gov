<!-- Collection callout -->
<div class="row one-quarter-row mvl collection-callout">
  <div class="columns medium-6">
      <?php if( isset($current_row['phila_callout_group']['phila_callout_heading'] )): ?>
        <?php $current_row_id = sanitize_title_with_dashes( $current_row['phila_callout_group']['phila_callout_heading']);?>
        <h3 id="<?php echo $current_row_id;?>"><?php echo $current_row['phila_callout_group']['phila_callout_heading']; ?></h3>
      <?php endif;?>
  </div>
  <div class="columns medium-18 pbxl">
    <?php if( isset($current_row['phila_callout_group']['phila_before_callout_copy'] )): ?>
      <div class="medium-24">
        <?php echo apply_filters( 'the_content', $current_row['phila_callout_group']['phila_before_callout_copy']) ?>
      </div>
    <?php endif;?>
    <?php include(locate_template('partials/departments/v2/photo-callout.php')); ?>
    <?php if( isset($current_row['phila_callout_group']['phila_after_callout_copy'] )): ?>
      <div class="medium-24">
        <?php echo apply_filters( 'the_content', $current_row['phila_callout_group']['phila_after_callout_copy']) ?>
      </div>
    <?php endif;?>
  </div>
</div>
<!-- / Collection callout -->