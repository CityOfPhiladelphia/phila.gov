<!-- Resource group wrapper -->
<div class="row one-quarter-row mvl">
  <div class="columns medium-6">
      <?php if( isset($current_row['phila_resource_group']['phila_wysiwyg_title'] )): ?>
        <?php $current_row_id = sanitize_title_with_dashes( $current_row['phila_resource_group']['phila_wysiwyg_title']);?>
        <h3 id="<?php echo $current_row_id;?>"><?php echo $current_row['phila_resource_group']['phila_wysiwyg_title']; ?></h3>
      <?php endif;?>
  </div>
  <div class="columns medium-18 pbxl">
    <?php if( isset($current_row['phila_resource_group']['phila_wysiwyg_content'] )): ?>
      <div class="mbl">
        <?php echo apply_filters( 'the_content', $current_row['phila_resource_group']['phila_wysiwyg_content']) ?>
      </div>
    <?php endif;?>
    <?php if( isset($current_row['phila_resource_group']['phila_resource_list_v2']) && isset($current_row['phila_resource_group']['phila_resource_list_v2'])): ?>
      <?php $resource_list_groups = $current_row['phila_resource_group']['phila_resource_list_v2']; ?>
      <div class="mbl">
        <?php include( locate_template( 'partials/resource-list.php' ) ); ?>
      </div>
    <?php endif; ?>
  </div>
</div>
<!-- / Resource group wrapper -->