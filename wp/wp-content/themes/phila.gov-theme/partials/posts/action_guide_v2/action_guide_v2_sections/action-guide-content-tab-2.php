<!-- Tab 2 -->
<div class="content-action_guide action-guide-v2" id="tab-2-content">

  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
        <div class="mbl">
          <?php if( isset($step_2_wysiwyg)): ?>
            <div>
              <?php echo apply_filters( 'the_content', $step_2_wysiwyg) ?>
            </div>
          <?php endif; ?>
          <ul class="no-bullet mbn pln">
            <?php if( isset($step_2_content_before_steps['phila_custom_wysiwyg']['phila_wysiwyg_title'] )): ?>
              <li class="pvs-mu phl-mu phs">
                <a href="<?php echo '#'.$step_2_content_before_steps['url'];?>" class="anchor underline">- <?php echo $step_2_content_before_steps['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></a>
              </li>
            <?php endif;?>
            <?php foreach( $step_2_content_after_steps as $key => $content) :?>
              <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_title'])): ?>
                <?php $step_2_content_after_steps[$key]['url'] = strtolower(str_replace(' ', '-', $content['phila_custom_wysiwyg']['phila_wysiwyg_title']));?>
                <li class="pvs-mu phl-mu phs">
                  <a href="<?php echo '#'.$step_2_content_after_steps[$key]['url'];?>" class="anchor underline">- <?php echo $content['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></a>
                </li>
              <?php endif; ?>
            <?php endforeach; ?>
          </ul>
        </div>
    </div>
  </div>

  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbm">
      <div class="mbl">
        <?php if( isset($step_2_content_before_steps['phila_custom_wysiwyg']['phila_wysiwyg_title'] )): ?>
          <h4 id="<?php echo $step_2_content_before_steps['url'];?>" class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $step_2_content_before_steps['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></h4>
        <?php endif;?>
        <?php if( isset($step_2_content_before_steps['phila_custom_wysiwyg']['phila_wysiwyg_content'] )): ?>
          <div class="phm">
            <?php echo apply_filters( 'the_content', $step_2_content_before_steps['phila_custom_wysiwyg']['phila_wysiwyg_content']) ?>
          </div>
        <?php endif;?>
      </div>
    </div>
  </div>

  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbm">
      <?php if( isset($phila_stepped_content_step_2)): ?>
        <?php $steps = $phila_stepped_content_step_2; ?>
        <div class="mbl">
          <div class="phm">
            <?php include( locate_template( 'partials/stepped-content.php' ) ); ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
      <?php foreach( $step_2_content_after_steps as $content ) :?>
        <div class="mbl">
          <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_title'] )): ?>
            <h4 id="<?php echo $content['url'];?>" class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $content['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></h4>
          <?php endif;?>
          <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_content'] )): ?>
            <div class="phm">
              <?php echo apply_filters( 'the_content', $content['phila_custom_wysiwyg']['phila_wysiwyg_content']) ?>
            </div>
            <?php endif;?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="grid-x grid-margin-x mvl tab-nav">
    <?php if( rwmb_meta( 'step_1_label' ) != null ): ?>
      <div class="medium-12 cell pbxl">
        <div class="left-nav" id="step-2-to-1-nav">
          <i class="fas fa-caret-left"></i>
          <span><?php echo rwmb_meta( 'step_1_label' );?></span>
        </div>
      </div>
    <?php endif; ?>
    <?php if( rwmb_meta( 'step_3_label' ) != null ): ?>
      <div class="medium-12 cell pbxl">
        <div class="right-nav" id="step-2-to-3-nav">
          <span><?php echo rwmb_meta( 'step_3_label' );?></span>
          <i class="fas fa-caret-right"></i>
        </div>
      </div>
    <?php endif; ?>
  </div>

</div>
<!-- /Tab 2 -->