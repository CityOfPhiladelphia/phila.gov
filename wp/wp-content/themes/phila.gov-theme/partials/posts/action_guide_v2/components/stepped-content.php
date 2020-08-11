<!-- Stepped content -->
<div class="grid-x grid-margin-x mvl">
  <div class="medium-24 cell pbm">
    <?php if( isset($current_row[$current_row_option]['phila_stepped_content']) && isset($current_row[$current_row_option]['phila_stepped_content']['phila_ordered_content'])): ?>
      <?php $steps = $current_row[$current_row_option]['phila_stepped_content']['phila_ordered_content']; ?>
      <div class="mbl">
        <div class="phm">
          <?php include( locate_template( 'partials/stepped-content.php' ) ); ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<!-- /Stepped content -->