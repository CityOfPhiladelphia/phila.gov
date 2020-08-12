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


// MetaBox variables
$tabs = rwmb_meta('phila_tabbed_content');
if (!phila_util_is_array_empty($tabs)):
?>

<div class="action-guide-v2-container-desktop">
  <?php include(locate_template('partials/posts/action_guide_v2/components/action-guide-content-step-tabs.php')); ?>
  <?php include(locate_template('partials/posts/action_guide_v2/components/action-guide-content-tab.php')); ?>
</div>

<div class="action-guide-v2-container-mobile">
  <?php include(locate_template('partials/posts/action_guide_v2/components/mobile/action-guide-content-tab-mobile.php')); ?>
</div>

<?php endif;?>

<!-- Full width call to action-->
<div class="mvl action-guide-v2-cta">
  <?php include(locate_template('partials/departments/v2/full-width-call-to-action.php')); ?>
</div>
<!-- /Full width call to action-->
