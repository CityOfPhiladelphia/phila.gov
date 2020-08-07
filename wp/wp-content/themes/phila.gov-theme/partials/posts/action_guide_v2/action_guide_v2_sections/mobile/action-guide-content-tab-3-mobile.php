<!-- Tab 3 -->
<div class="content-action_guide action-guide-v2 active" id="tab-3-content-mobile">


  <?php if( rwmb_meta('step_3_label') != null) :?>
    <h2 class="label-copy"><?php echo rwmb_meta( 'step_3_label' );?></h2>
  <?php endif; ?>

  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbl">
      <ul class="accordion phn" data-accordion data-multi-expand="true" data-allow-all-closed="true">
        
        <?php if( isset($step_3_stepped_content_title )): ?>
          <li class="mbl accordion-item" data-accordion-item>
            <a href="#" class="accordion-title"><?php echo $step_3_stepped_content_title; ?></a>
            <div class="phm accordion-content" data-tab-content>
              <?php if( isset($step_3_wysiwyg)): ?>
                <?php echo apply_filters( 'the_content', $step_3_wysiwyg) ?>
              <?php endif; ?>
              <div class="grid-x grid-margin-x mvl">
                <div class="medium-24 cell pbm">
                  <?php if( isset($phila_stepped_content_step_3)): ?>
                    <?php $steps = $phila_stepped_content_step_3; ?>
                    <div class="mbl">
                      <div class="phm">
                      <?php include( locate_template( 'partials/stepped-content.php' ) ); ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </li>
        <?php endif;?>
      </ul>
    </div>
  </div>

</div>
<!-- /Tab 3 -->