<?php
/*
 *
 * Partial for rendering Cloneable Call to Action panels
 *
 */

?>
<?php
  if( isset($phila_dept_homepage_cta)):
    $action_panel_section = $phila_dept_homepage_cta;
  else :
    $action_panel_section = rwmb_meta('phila_call_to_action_section');
  endif;
  $action_panel_title = isset( $action_panel_section['phila_action_section_title_multi'] ) ? $action_panel_section['phila_action_section_title_multi'] : '' ;

  $action_panel_multi = isset( $action_panel_section['phila_call_to_action_multi_group'] ) ? $action_panel_section['phila_call_to_action_multi_group']: '' ;


  if ( ! empty( $action_panel_section ) ) : ?>
  <?php $item_count = count($action_panel_multi); ?>
  <?php $columns = phila_grid_column_counter( $item_count ); ?>

    <div class="row">
      <div class="columns">
        <h2 class="contrast"><?php echo $action_panel_title; ?></h2>
      </div>
    </div>
    <div class="row <?php if( $item_count > 1 ) echo 'equal-height';?>">
    <?php foreach ( $action_panel_multi as $call_to_action ) :

      $action_panel_summary = isset( $call_to_action['phila_action_panel_summary_multi'] ) ? $call_to_action['phila_action_panel_summary_multi'] : '';


      $action_panel_cta_text = isset( $call_to_action['phila_action_panel_cta_text_multi'] ) ? $call_to_action['phila_action_panel_cta_text_multi'] : '';

      $action_panel_link = isset( $call_to_action['phila_action_panel_link_multi'] ) ? $call_to_action['phila_action_panel_link_multi'] : '';

      $action_panel_link_loc = isset(  $call_to_action['phila_action_panel_link_loc_multi'] ) ? $call_to_action['phila_action_panel_link_loc_multi'] : '';

      $action_panel_fa_circle = isset( $call_to_action['phila_action_panel_fa_circle_multi'] ) ? $call_to_action['phila_action_panel_fa_circle_multi'] : '' ;

      $action_panel_fa = isset( $call_to_action['phila_action_panel_fa_multi'] ) ? $call_to_action['phila_action_panel_fa_multi'] : '';
      ?>
      <?php phila_grid_column_counter( $item_count ); ?>
    <div class="large-<?php echo $columns ?> columns">
      <?php if (!$action_panel_link == ''): ?>
      <a href="<?php echo $action_panel_link; ?>"  class="action-panel">
        <div class="panel <?php if( $item_count > 1 ) echo 'equal';?>">
        <header class="<?php echo $columns == '24' ? 'text-align-left' : ''; ?>">
        <?php if ($action_panel_fa_circle): ?>
          <div class="<?php echo $columns == '24' ? 'float-left mrm' : ''; ?>">
            <span class="fa-stack <?php echo $columns == '24' ? 'fa-3x' : 'fa-4x'; ?> center" aria-hidden="true">
              <i class="fa fa-circle fa-stack-2x"></i>
              <i class="fa <?php echo $action_panel_fa; ?> fa-stack-1x fa-inverse"></i>
            </span>
          </div>
        <?php else: ?>
         <div>
           <span><i class="fa <?php echo $action_panel_fa; ?> fa-5x" aria-hidden="true"></i></span>
         </div>
        <?php endif; ?>
        <?php if (!$action_panel_cta_text == ''): ?>
          <span class="<?php if ($action_panel_link_loc) echo 'external';?>"><?php echo $action_panel_cta_text; ?></span>
        <?php endif; ?>
        </header>
        <?php echo $columns == '24' ? '' : '<hr class="mll mrl">'; ?>
          <span class="details"><?php echo $action_panel_summary; ?></span>
        </div>
      </a>
    <?php endif; ?>
    </div>

  <?php endforeach; ?>
</div>
<?php endif; ?>
