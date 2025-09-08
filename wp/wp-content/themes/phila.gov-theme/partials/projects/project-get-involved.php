<?php
/*
 *
 * Partial for rendering Cloneable Call to Action panels as full width Get Involved
 *
 */

?>
<?php
    $action_panel_section = rwmb_meta('phila_project_get_involved_section');

    $action_panel_title = isset( $action_panel_section['phila_project_get_involved_section_title'] ) ? $action_panel_section['phila_project_get_involved_section_title'] : '' ;

    $action_panel_multi = isset( $action_panel_section['phila_project_get_involved_group'] ) ? $action_panel_section['phila_project_get_involved_group']: '' ;

  if ( !empty( $action_panel_section ) ) : ?>
  <?php $item_count = count($action_panel_multi); ?>
  <?php $columns = phila_grid_column_counter( $item_count ); ?>
  <section class="mvl phila-project-get-involved">
    <div class="row">
      <div class="columns">
        <h2 id="<?php echo sanitize_title_with_dashes( $action_panel_title ); ?>"><?php echo $action_panel_title; ?></h2>
      </div>
    </div>
    <section class="get-involved-row" style="background-color: #d7d7d7;">
      <div class="row-wrap">
      <div class="row mbm ptm <?php if( $item_count > 1 ) echo 'equal-height';?>">
      <?php foreach ( $action_panel_multi as $call_to_action ) :

        $action_panel_summary = isset( $call_to_action['phila_project_summary'] ) ? $call_to_action['phila_project_summary'] : '';

        $action_panel_cta_text = isset( $call_to_action['phila_project_link_text'] ) ? $call_to_action['phila_project_link_text'] : '';

        $action_panel_link = isset( $call_to_action['phila_project_link'] ) ? $call_to_action['phila_project_link'] : '';

        $action_panel_link_loc = isset(  $call_to_action['phila_action_panel_link_loc_multi'] ) ? $call_to_action['phila_action_panel_link_loc_multi'] : '';

        $action_panel_fa = isset( $call_to_action['phila_project_fa'] ) ? $call_to_action['phila_project_fa'] : '';
        ?>
        <?php phila_grid_column_counter( $item_count ); ?>
        <div class="medium-<?php echo $columns; ?> columns">
          <?php if ( !empty( $action_panel_link ) ): ?>
          <a href="<?php echo $action_panel_link; ?>"  class="action-panel">
            <div class="panel hover-fade <?php if( $item_count > 1 ) echo 'equal';?>" style="background-color: #ffffff !important;">
            <header class="<?php echo $columns == '24' ? 'text-align-left' : ''; ?>">
            <?php if ($action_panel_fa): ?>
              <div class="<?php echo $columns == '24' ? 'float-left mrm' : ''; ?> icon">
                <span class="fa-stack <?php echo $columns == '24' ? 'fa-3x' : 'fa-4x'; ?> center" aria-hidden="true">
                  <i class="fas fa-circle fa-stack-2x"></i>
                  <i class="<?php echo $action_panel_fa; ?> fa-stack-1x fa-inverse"></i>
                </span>
              </div>
            <?php endif; ?>
            <?php if ( !empty( $action_panel_cta_text ) ): ?>
              <span class="<?php if ($action_panel_link_loc) echo 'external';?>"><?php echo $action_panel_cta_text; ?></span>
            <?php endif; ?>
            </header>
              <div class="details"><?php echo $action_panel_summary; ?></div>
            </div>
          </a>
        <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  </section>
</section>
<?php endif; ?>
