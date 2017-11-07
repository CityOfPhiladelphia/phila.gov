<?php
/*
 * Action guide display
 */

$get_facts = rwmb_meta( 'phila_action_facts' );
$facts = phila_loop_clonable_metabox( $get_facts );

?>

<div class="one-quarter-layout bdr-dark-gray">

  <div class="row mvl">
    <div class="medium-6 columns">
      <h3 id="get-informed">Get informed</h3>
    </div>
    <div class="medium-18 columns pbxl">
      <?php echo rwmb_meta('phila_action_get_informed'); ?>
    </div>
  </div>

  <div class="row mvl">
    <div class="medium-6 columns">
      <h3 id="know-the-facts">Know the facts</h3>
    </div>
    <div class="medium-18 columns pbxl">
      <?php foreach( $facts as $fact ) :?>
        <div class="panel info mbl">
          <h4 class="h2"><?php echo $fact['phila_custom_wysiwyg']['phila_wysiwyg_title'] ?></h4>
          <?php echo $fact['phila_custom_wysiwyg']['phila_wysiwyg_content'] ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="row mvl">
    <h3 id="take-action" class="mbn">Take action</h3>
    <?php include( locate_template( 'partials/departments/phila_call_to_action_multi.php' ) ); ?>
  </div>

</div>
