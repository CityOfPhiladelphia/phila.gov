<?php
/*
 * Action guide display
 */

$get_facts = rwmb_meta( 'phila_action_facts' );
$facts = phila_loop_clonable_metabox( $get_facts );

$get_actions = rwmb_meta( 'phila_take_action' );
$actions = phila_loop_clonable_metabox( $get_actions );

?>

<p class="lead"><i>To help you understand your rights and protections, the City of Philadelphia is creating action guides on federal policies. The action guides include facts, ways you can help, and other resources.</i></p>

<div class="one-quarter-layout bdr-dark-gray content-action_guide">
  <div class="grid-x grid-margin-x mvl one-quarter-row">
    <div class="cell medium-6 print-stack">
      <h3 id="get-informed">Get informed</h3>
    </div>
    <div class="medium-18 cell pbxl">
      <?php echo rwmb_meta('phila_action_get_informed'); ?>
    </div>
  </div>

  <div class="grid-x grid-margin-x mvl">
    <div class="medium-6 cell">
      <h3 id="know-the-facts">Know the facts</h3>
    </div>
    <div class="medium-18 cell pbxl">
      <?php foreach( $facts as $fact ) :?>
        <div class="mbl">
          <h4 class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $fact['phila_custom_wysiwyg']['phila_wysiwyg_title'] ?></h2>
            <div class="phm">
              <?php echo apply_filters( 'the_content', $fact['phila_custom_wysiwyg']['phila_wysiwyg_content']) ?>
            </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="grid-x grid-margin-x mvl">
    <div class="medium-6 cell print-stack">
      <h3 id="take-action" class="mbn">Take action</h3>
    </div>
    <div class="medium-18 cell">
      <?php echo rwmb_meta('phila_action_intro'); ?>
      <?php foreach( $actions as $action ) : ?>
        <?php switch ( $action['phila_select_action'] ):
          case 'share':
            $icon = 'share';
            $text = 'Share';
            break;
          case 'contact':
            $icon = 'address-book-o';
            $text = 'Contact';
            break;
          case 'give_back':
            $icon = 'handshake-o';
            $text = 'Give back';
            break;
          case 'attend':
            $icon = 'calendar-check-o';
            $text = 'Attend';
            break;
          endswitch;
          ?>
          <?php ( strlen( $action['phila_action_content'] ) > 820 ) ? $expand = true : $expand = false; ?>
          <div class="panel info clearfix mbm">
            <div class="<?php echo ($expand) ? 'expandable' : ''?>">
              <h4 class="mbm"><i class="fa fa-<?php echo $icon ?>" aria-hidden="true"></i> <?php echo $text; ?></h4>
              <?php echo apply_filters( 'the_content',  $action['phila_action_content']); ?>
            </div>
            <?php if ( $expand ): ?>
              <a href="#" data-toggle="expandable" class="float-right"> Expand + </a>
            <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>
