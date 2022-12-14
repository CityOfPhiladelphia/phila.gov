<!-- Tabs -->
<div id="action-guide-v2-tabs" class="grid-container action-guide-v2-tabs mtxl">
  <div class="grid-x grid-margin-x mvl one-quarter-row">
  <?php
  foreach ($tabs as $tab_label_key => $value){
    $current_tab = $tabs[$tab_label_key];
    $tab_id = $tab_label_key+1;
  ?>
    <div class="cell tab-label bg-dark-ben-franklin white <?php echo ($tab_id == 1) ? 'active' : '' ?>" id="step-<?php echo $tab_id?>-label">
      <div class="bg-dark-ben-franklin active-bar"></div>
      <?php if( isset($current_tab['tab_icon'])) :?>
        <i class="<?php echo $current_tab['tab_icon'] ?> fa-2x" aria-hidden="true"></i>
      <?php endif; ?>
      <?php if( isset($current_tab['tab_label'])) :?>
        <div class="label-copy"><?php echo $current_tab['tab_label'];?></div>
      <?php endif; ?>
    </div>
  <?php }?>
  </div>
</div>
<!-- /Tabs -->