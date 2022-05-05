<!-- Stepped process wrapper -->
<div class="row one-quarter-row mvl">
  <div class="columns medium-6">
      <?php if( isset($current_row['phila_resource_group']['phila_wysiwyg_title'] )): ?>	
        <?php $current_row_id = sanitize_title_with_dashes( $current_row['phila_resource_group']['phila_wysiwyg_title']);?>
        <h3 id="<?php echo $current_row_id;?>"><?php echo $current_row['phila_resource_group']['phila_wysiwyg_title']; ?></h3>
      <?php endif;?>	
  </div>
  <div class="columns medium-18 pbxl">
    <div class="mbl">	
      <?php if( isset($current_row['phila_resource_group']['phila_wysiwyg_content'] )): ?>	
        <div class="plm">	
          <?php echo apply_filters( 'the_content', $current_row['phila_resource_group']['phila_wysiwyg_content']) ?>	
        </div>	
      <?php endif;?>	
    </div>
    <?php if( isset($current_row['phila_resource_group']['phila_resource_list_v2']) && isset($current_row['phila_resource_group']['phila_resource_list_v2'])): ?>
      <?php $resource_list_groups = $current_row['phila_resource_group']['phila_resource_list_v2']; ?>
      <div class="mbl">
        <div class="plm">
          <?php include( locate_template( 'partials/departments/v2/collection-resource-list.php' ) ); ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>      
<!-- / Stepped process wrapper -->