<!-- Stepped content mobile -->
<?php if( isset($current_row[$current_row_option]['phila_stepped_content']) && isset($current_row[$current_row_option]['phila_stepped_content']['phila_ordered_content'])): ?>
  <li class="mbs accordion-item" data-accordion-item>
    <a href="#" class="accordion-title"><?php echo $current_row[$current_row_option]['phila_stepped_content']['phila_wysiwyg_title']; ?></a>
    <div class="phm accordion-content" data-tab-content>
      <?php if( isset($current_row[$current_row_option]['phila_stepped_content']['phila_wysiwyg_content'])): ?>
        <?php echo apply_filters( 'the_content', $current_row[$current_row_option]['phila_stepped_content']['phila_wysiwyg_content']) ?>
      <?php endif; ?>
      <div class="grid-x grid-margin-x mvl">
        <div class="medium-24 cell pbm">
          <?php $steps = $current_row[$current_row_option]['phila_stepped_content']['phila_ordered_content']; ?>
          <div class="mbl">
            <div class="phm">
            <?php include( locate_template( 'partials/stepped-content.php' ) ); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </li>
<?php endif;?>
<!-- /Stepped content mobile -->