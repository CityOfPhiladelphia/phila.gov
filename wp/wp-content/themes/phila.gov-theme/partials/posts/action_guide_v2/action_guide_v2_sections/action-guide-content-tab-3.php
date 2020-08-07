<!-- Tab 3 -->
<div class="content-action_guide action-guide-v2" id="tab-3-content">
  
<?php if( isset($step_3_wysiwyg)): ?>
  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbm">
        <div class="mbl">
          <div>
            <?php echo apply_filters( 'the_content', $step_3_wysiwyg) ?>
          </div>
        </div>
    </div>
  </div>
<?php endif; ?>

  <?php if( isset($phila_stepped_content_step_3)): ?>
    <div class="grid-x grid-margin-x mvl">
      <div class="medium-24 cell pbm">
        <?php $steps = $phila_stepped_content_step_3; ?>
          <div class="mbl">
            <?php if( isset($step_3_stepped_content_title)): ?>
              <div class="mbl">
                <h4 class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $step_3_stepped_content_title; ?></h4>
              </div>
            <?php endif; ?>
            <div class="phm">
              <?php include( locate_template( 'partials/stepped-content.php' ) ); ?>
            </div>
          </div>
      </div>
    </div>
  <?php endif; ?>

  <?php if( rwmb_meta( 'step_2_label' ) != null ): ?>
    <div class="grid-x grid-margin-x mvl tab-nav">
      <div class="medium-24 cell pbxl">
        <div class="left-nav" id="step-3-to-2-nav">
          <i class="fas fa-caret-left"></i>
          <span><?php echo rwmb_meta( 'step_2_label' );?></span>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>
<!-- /Tab 3 -->