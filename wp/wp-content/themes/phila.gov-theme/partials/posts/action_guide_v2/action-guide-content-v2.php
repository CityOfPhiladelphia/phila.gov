<?php
/*
 * Action guide V2 display
 */

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
