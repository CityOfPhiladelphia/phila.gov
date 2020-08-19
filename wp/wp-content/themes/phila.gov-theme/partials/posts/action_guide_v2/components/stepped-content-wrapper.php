<!-- Stepped content wrapper -->
<div class="grid-x grid-margin-x mvl">
  <div class="medium-24 cell pbm">
    <div class="mbl">	
      <?php if( isset($current_row[$current_row_option]['phila_stepped_content']['phila_wysiwyg_title'] )): ?>	
        <?php $current_row_id = strtolower(str_replace(' ', '-', $current_row[$current_row_option]['phila_stepped_content']['phila_wysiwyg_title']));?>
        <h4 id="<?php echo $current_row_id;?>" class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $current_row[$current_row_option]['phila_stepped_content']['phila_wysiwyg_title']; ?></h4>	
      <?php endif;?>	
      <?php if( isset($current_row[$current_row_option]['phila_stepped_content']['phila_wysiwyg_content'] )): ?>	
        <div class="plm">	
          <?php echo apply_filters( 'the_content', $current_row[$current_row_option]['phila_stepped_content']['phila_wysiwyg_content']) ?>	
        </div>	
      <?php endif;?>	
    </div>
    <?php if( isset($current_row[$current_row_option]['phila_stepped_content']) && isset($current_row[$current_row_option]['phila_stepped_content']['phila_ordered_content'])): ?>
      <?php $steps = $current_row[$current_row_option]['phila_stepped_content']['phila_ordered_content']; ?>
      <div class="mbl">
        <div class="plm">
          <?php include( locate_template( 'partials/stepped-content.php' ) ); ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<!-- / Stepped content wrapper -->