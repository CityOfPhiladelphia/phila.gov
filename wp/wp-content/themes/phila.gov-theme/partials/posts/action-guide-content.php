<?php
/*
 * Action guide display
 */

$get_facts = rwmb_meta( 'phila_action_facts' );
$facts = phila_loop_clonable_metabox( $get_facts );

$get_actions = rwmb_meta( 'phila_take_action' );
$actions = phila_loop_clonable_metabox( $get_actions );

?>

<div class="one-quarter-layout bdr-dark-gray content-action_guide">

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
        <div class="mbl">
          <h2 class="black bg-ghost-gray h2 phm-mu mtl mbm"><?php echo $fact['phila_custom_wysiwyg']['phila_wysiwyg_title'] ?></h2>
          <?php echo $fact['phila_custom_wysiwyg']['phila_wysiwyg_content'] ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="row mvl">
    <div class="medium-6 columns">
      <h3 id="take-action" class="mbn">Take action</h3>
    </div>
    <div class="medium-18 columns">
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
          <div class="panel info mbm collapsible">
            <h4 class="mvn all-caps"><i class="fa fa-<?php echo $icon ?>" aria-hidden="true"></i> <?php echo $text; ?></h4>
            <?php if ( strlen( $action['phila_action_content'] ) > 820 ): ?>
              <?php echo $action['phila_action_content'] ?>
              <div class="float-right"> <a href="#" data-toggle="data-expandable"> Expand + </a></div>
            <?php else: ?>
            <div><?php echo $action['phila_action_content'] ?> </div>
          <?php endif; ?>
        </div>

      <?php endforeach; ?>
    </div>
  </div>

</div>
