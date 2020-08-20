<!-- Tab pagination -->
<div class="grid-x grid-margin-x mvl tab-nav">
  <?php if( $tab_id != 1 ) { ?>
    <?php $prev_tab = $tabs[$tab_key-1]; ?>
    <?php if( isset($prev_tab['tab_label']) ): ?>
      <div class="<?php echo ($tab_id == $tab_count) ? 'medium-24' : 'medium-12' ?> cell pbxl">
        <a href="#action-guide-v2-tabs" class="prev-tab">
          <i class="fas fa-caret-left"></i>
          <span><?php echo $prev_tab['tab_label'];?></span>
        </a>
      </div>
    <?php endif; ?>
  <?php } ?>
  <?php if( $tab_id != $tab_count ) { ?>
    <?php $next_tab = $tabs[$tab_key+1]; ?>
    <?php if( isset($next_tab['tab_label']) ): ?>
      <div class="<?php echo ($tab_id == 1) ? 'medium-24' : 'medium-12' ?> cell pbxl">
        <a href="#action-guide-v2-tabs" class="next-tab">
          <span><?php echo $next_tab['tab_label'];?></span>
          <i class="fas fa-caret-right"></i>
        </a>
      </div>
    <?php endif; ?>
  <?php } ?>
</div>
<!-- /Tab pagination -->