<!-- Tabs -->
<div class="grid-container action-guide-v2-tabs mtxl">
  <div class="grid-x grid-margin-x mvl one-quarter-row">
    <div class="cell medium-8 active step-label bg-dark-ben-franklin white" id="step-1-label">
      <div class="bg-dark-ben-franklin active-bar"></div>
      <?php if( rwmb_meta('step_1_icon') != null) :?>
        <i class="<?php echo rwmb_meta('step_1_icon') ?> fa-2x" aria-hidden="true"></i>
      <?php endif; ?>
      <?php if( rwmb_meta('step_1_label') != null) :?>
        <div class="label-copy"><?php echo rwmb_meta( 'step_1_label' );?></div>
      <?php endif; ?>
    </div>
    <div class="cell medium-8 step-label bg-dark-ben-franklin white" id="step-2-label">
      <div class="bg-dark-ben-franklin active-bar"></div>
      <?php if( rwmb_meta('step_2_icon') != null) :?>
        <i class="<?php echo rwmb_meta('step_2_icon') ?> fa-2x" aria-hidden="true"></i>
      <?php endif; ?>
      <?php if( rwmb_meta('step_2_label') != null) :?>
        <div class="label-copy"><?php echo rwmb_meta( 'step_2_label' );?></div>
      <?php endif; ?>
    </div>
    <div class="cell medium-8 step-label bg-dark-ben-franklin white" id="step-3-label">
      <div class="bg-dark-ben-franklin active-bar"></div>
      <i class="<?php echo rwmb_meta('step_3_icon') ?> fa-2x" aria-hidden="true"></i>
      <div class="label-copy"><?php echo rwmb_meta( 'step_3_label' );?></div>
    </div>
  </div>
</div>
<!-- /Tabs -->