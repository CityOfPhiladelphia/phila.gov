<!-- Timeline content mobile -->
<?php if( isset($current_row[$current_row_option]['phila_timeline_content']) && isset($current_row[$current_row_option]['phila_timeline_content']['timeline-items'])): ?>
  <li class="mbs accordion-item" data-accordion-item>
    <a href="#" class="accordion-title"><?php echo $current_row[$current_row_option]['phila_timeline_content']['phila_wysiwyg_title']; ?></a>
    <div class="phm accordion-content" data-tab-content>
      <?php if( isset($current_row[$current_row_option]['phila_timeline_content']['phila_wysiwyg_content'])): ?>
        <?php echo apply_filters( 'the_content', $current_row[$current_row_option]['phila_timeline_content']['phila_wysiwyg_content']) ?>
      <?php endif; ?>
      <?php include(locate_template('partials/posts/action_guide_v2/components/timeline-content.php')); ?>
  </li>
<?php endif;?>
<!-- /Timeline content mobile -->