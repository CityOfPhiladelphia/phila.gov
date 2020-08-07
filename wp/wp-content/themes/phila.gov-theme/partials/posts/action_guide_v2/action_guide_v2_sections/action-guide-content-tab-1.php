<!-- Tab 1 -->
<div class="content-action_guide action-guide-v2 active" id="tab-1-content">

  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
        <div class="mbl">
          <?php if( isset($step_1_wysiwyg)): ?>
            <div>
              <?php echo apply_filters( 'the_content', $step_1_wysiwyg) ?>
            </div>
          <?php endif; ?>
          <ul class="no-bullet mbn pln">
            <?php foreach( $step_1_content as $key => $content) :?>
              <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_title'])): ?>
                <?php $step_1_content[$key]['url'] = strtolower(str_replace(' ', '-', $content['phila_custom_wysiwyg']['phila_wysiwyg_title']));?>
                <li class="pvs-mu phl-mu phs">
                  <a href="<?php echo '#'.$step_1_content[$key]['url'];?>" class="anchor underline">- <?php echo $content['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></a>
                </li>
              <?php endif; ?>
            <?php endforeach; ?>
          </ul>
        </div>
    </div>
  </div>

  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
      <?php foreach( $step_1_content as $content ) :?>
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

  <?php if( rwmb_meta( 'step_2_label' ) != null ): ?>
    <div class="grid-x grid-margin-x mvl tab-nav">
      <div class="medium-24 cell pbxl">
        <div class="right-nav" id="step-1-to-2-nav">
          <span><?php echo rwmb_meta( 'step_2_label' );?></span>
          <i class="fas fa-caret-right"></i>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>
<!-- /Tab 1 -->