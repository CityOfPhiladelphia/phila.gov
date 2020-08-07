<?php
/*
 * Action guide V2 display
 */

$get_step_1_content = rwmb_meta( 'step_1_content' );
$step_1_content = phila_loop_clonable_metabox( $get_step_1_content );
$step_1_wysiwyg = rwmb_meta('step_1_wysiwyg');

$step_2_wysiwyg = rwmb_meta('step_2_wysiwyg');
$get_step_2_content_before_steps = rwmb_meta( 'step_2_content_before_steps' );
$step_2_content_before_steps = phila_loop_clonable_metabox( $get_step_2_content_before_steps );

if( isset($step_2_content_before_steps['phila_custom_wysiwyg']['phila_wysiwyg_title'])) {
  $step_2_content_before_steps['url'] = strtolower(str_replace(' ', '-', $step_2_content_before_steps['phila_custom_wysiwyg']['phila_wysiwyg_title']));
}

$get_phila_stepped_content_step_2 = rwmb_meta( 'phila_stepped_content_step_2' );
$phila_stepped_content_step_2 = phila_extract_stepped_content( $get_phila_stepped_content_step_2 );

$get_step_2_content_after_steps = rwmb_meta( 'step_2_content_after_steps' );
$step_2_content_after_steps = phila_loop_clonable_metabox( $get_step_2_content_after_steps );

$step_3_wysiwyg = rwmb_meta('step_3_wysiwyg');
$step_3_stepped_content_title = rwmb_meta('phila_stepped_content_step_3_title');
$get_phila_stepped_content_step_3 = rwmb_meta( 'phila_stepped_content_step_3' );
$phila_stepped_content_step_3 = phila_extract_stepped_content( $get_phila_stepped_content_step_3 );

?>

<!-- Tabs -->
<div class="grid-container action-guide-v2-tabs">
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

<!-- Tab 1 -->
<div class="content-action_guide action-guide-v2 active" id="tab-1-content">

  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
        <div class="mbl">
          <?php if( isset($step_1_wysiwyg)): ?>
            <div class="phm">
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

<!-- Tab 2 -->
<div class="content-action_guide action-guide-v2" id="tab-2-content">

  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
        <div class="mbl">
          <?php if( isset($step_2_wysiwyg)): ?>
            <div class="phm">
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
    <div class="medium-24 cell pbxl">
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
    <div class="medium-24 cell pbxl">
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

<!-- Tab 3 -->
<div class="content-action_guide action-guide-v2" id="tab-3-content">
  
<?php if( isset($step_3_wysiwyg)): ?>
  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
        <div class="mbl">
          <div class="phm">
            <?php echo apply_filters( 'the_content', $step_3_wysiwyg) ?>
          </div>
        </div>
    </div>
  </div>
<?php endif; ?>

  <?php if( isset($phila_stepped_content_step_3)): ?>
    <div class="grid-x grid-margin-x mvl">
      <div class="medium-24 cell pbxl">
        <?php $steps = $phila_stepped_content_step_3; ?>
          <div class="mbl">
            <div class="phm">
              <?php if( isset($step_3_stepped_content_title)): ?>
                <div class="mbl">
                  <h4 class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $step_3_stepped_content_title; ?></h4>
                </div>
              <?php endif; ?>
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

<!-- Full width call to action-->
<div class="mvl action-guide-v2-cta">
  <?php include(locate_template('partials/departments/v2/full-width-call-to-action.php')); ?>
</div>
<!-- /Full width call to action-->