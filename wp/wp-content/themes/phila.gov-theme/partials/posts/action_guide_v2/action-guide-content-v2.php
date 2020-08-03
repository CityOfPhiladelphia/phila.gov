<?php
/*
 * Action guide V2 display
 */

$get_step_1_content = rwmb_meta( 'step_1_content' );
$step_1_content = phila_loop_clonable_metabox( $get_step_1_content );

$get_step_2_content_before_steps = rwmb_meta( 'step_2_content_before_steps' );
$step_2_content_before_steps = phila_loop_clonable_metabox( $get_step_2_content_before_steps );

$get_phila_stepped_content_step_2 = rwmb_meta( 'phila_stepped_content_step_2' );
$phila_stepped_content_step_2 = phila_extract_stepped_content( $get_phila_stepped_content_step_2 );

$get_step_2_content_after_steps = rwmb_meta( 'step_2_content_after_steps' );
$step_2_content_after_steps = phila_loop_clonable_metabox( $get_step_2_content_after_steps );

$get_step_3_content_before_steps = rwmb_meta( 'step_3_content_before_steps' );
$step_3_content_before_steps = phila_loop_clonable_metabox( $get_step_3_content_before_steps );

$get_step_3_content_after_steps = rwmb_meta( 'step_3_content_after_steps' );
$step_3_content_after_steps = phila_loop_clonable_metabox( $get_step_3_content_after_steps );

$get_phila_stepped_content_step_3 = rwmb_meta( 'phila_stepped_content_step_3' );
$phila_stepped_content_step_3 = phila_extract_stepped_content( $get_phila_stepped_content_step_3 );

?>

<!-- Tabs -->
<div class="grid-container">
  <div class="grid-x grid-margin-x mvl one-quarter-row">
    <div class="cell medium-8 active" id="step-1-label">
      <?php echo rwmb_meta( 'step_1_label' );?>
    </div>
    <div class="cell medium-8" id="step-2-label">
      <?php echo rwmb_meta( 'step_2_label' );?>
    </div>
    <div class="cell medium-8" id="step-3-label">
      <?php echo rwmb_meta( 'step_3_label' );?>
    </div>
  </div>
</div>

<!-- Tab 1 -->
<div class="content-action_guide action-guide-v2 active" id="tab-1-content">

  <hr class="mhn"/>
  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
      <?php foreach( $step_1_content as $content ) :?>
        <div class="mbl">
          <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_title'] )): ?>
            <h4 class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $content['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></h4>
          <?php endif;?>
          <div class="phm">
            <?php echo apply_filters( 'the_content', $content['phila_custom_wysiwyg']['phila_wysiwyg_content']) ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>

<!-- Tab 2 -->
<div class="content-action_guide action-guide-v2" id="tab-2-content">
  <hr class="mhn"/>
  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
      <?php foreach( $step_2_content_before_steps as $content ) :?>
        <div class="mbl">
          <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_title'] )): ?>
            <h4 class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $content['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></h4>
          <?php endif;?>
          <div class="phm">
            <?php echo apply_filters( 'the_content', $content['phila_custom_wysiwyg']['phila_wysiwyg_content']) ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
      <?php $steps = $phila_stepped_content_step_2; ?>
        <div class="mbl">
          <div class="phm">
            <?php include( locate_template( 'partials/stepped-content.php' ) ); ?>
          </div>
        </div>
    </div>
  </div>

  <hr class="mhn"/>
  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
      <?php foreach( $step_2_content_after_steps as $content ) :?>
        <div class="mbl">
          <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_title'] )): ?>
            <h4 class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $content['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></h4>
          <?php endif;?>
          <div class="phm">
            <?php echo apply_filters( 'the_content', $content['phila_custom_wysiwyg']['phila_wysiwyg_content']) ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>

<!-- Tab 3 -->
<div class="content-action_guide action-guide-v2" id="tab-3-content">
  <hr class="mhn"/>
  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
      <?php foreach( $step_3_content_before_steps as $content ) :?>
        <div class="mbl">
          <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_title'] )): ?>
            <h4 class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $content['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></h4>
          <?php endif;?>
          <div class="phm">
            <?php echo apply_filters( 'the_content', $content['phila_custom_wysiwyg']['phila_wysiwyg_content']) ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
      <?php $steps = $phila_stepped_content_step_3; ?>
        <div class="mbl">
          <div class="phm">
            <?php include( locate_template( 'partials/stepped-content.php' ) ); ?>
          </div>
        </div>
    </div>
  </div>

  <hr class="mhn"/>
  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
      <?php foreach( $step_3_content_after_steps as $content ) :?>
        <div class="mbl">
          <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_title'] )): ?>
            <h4 class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $content['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></h4>
          <?php endif;?>
          <div class="phm">
            <?php echo apply_filters( 'the_content', $content['phila_custom_wysiwyg']['phila_wysiwyg_content']) ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>
