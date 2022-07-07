<!-- Stepped process wrapper -->
<div class="row one-quarter-row mvl">
  <div class="columns medium-6">
      <?php if( isset($stepped_content['phila_wysiwyg_title'] )): ?>
        <?php $current_row_id = sanitize_title_with_dashes( $stepped_content['phila_wysiwyg_title']);?>
        <h3 id="<?php echo $current_row_id;?>"><?php echo $stepped_content['phila_wysiwyg_title']; ?></h3>
      <?php endif;?>
  </div>
  <div class="columns medium-18 pbxl">
    <div class="mbl">
      <?php if( isset($stepped_content['phila_wysiwyg_content'] )): ?>
        <div class="plm">
          <?php echo apply_filters( 'the_content', $stepped_content['phila_wysiwyg_content']) ?>
        </div>
      <?php endif;?>
    </div>
    <?php if( isset($stepped_content) && isset($stepped_content['phila_ordered_content'])): ?>
      <?php $steps = $stepped_content['phila_ordered_content']; ?>
      <div class="mbl">
        <div class="plm">
          <?php include( locate_template( 'partials/stepped-content.php' ) ); ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<!-- / Stepped process wrapper -->