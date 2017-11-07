<?php
/*
 *
 * Partial for rendering Cloneable Call to Action panels as full width Get Involved
 *
 */

?>
<?php
  if( isset($phila_dept_homepage_cta ) ):
    $action_panel_section = $phila_dept_homepage_cta;
  else :
    $action_panel_section = rwmb_meta('phila_call_to_action_section');
  endif;
  $action_panel_title = isset( $action_panel_section['phila_action_section_title_multi'] ) ? $action_panel_section['phila_action_section_title_multi'] : '' ;

  $action_panel_multi = isset( $action_panel_section['phila_call_to_action_multi_group'] ) ? $action_panel_section['phila_call_to_action_multi_group']: '' ;

  $action_panel_bg = isset( $action_panel_section['phila_bg_image'] ) ? $action_panel_section['phila_bg_image'] : '';


  if ( !empty( $action_panel_section ) ) : ?>
  <?php $item_count = count($action_panel_multi); ?>
  <?php $columns = phila_grid_column_counter( $item_count ); ?>
  <!-- Display Multi Call to Action as Get Involved -->
  <section class="mvl">
    <div class="row">
      <div class="columns">
        <h2><?php echo $action_panel_title; ?></h2>
      </div>
    </div>
    <section class="get-involved-row">
      <div class="row-wrap">
      <?php if ( $action_panel_bg != '') :?>
        <img class="banner show-for-large" src="<?php echo $action_panel_bg;?>" alt="">
      <?php endif; ?>
      <div class="row mbm ptm <?php if( $item_count > 1 ) echo 'equal-height';?>">
      <?php foreach ( $action_panel_multi as $call_to_action ) :

        $action_panel_summary = isset( $call_to_action['phila_action_panel_summary_multi'] ) ? $call_to_action['phila_action_panel_summary_multi'] : '';


        $action_panel_cta_text = isset( $call_to_action['phila_action_panel_cta_text_multi'] ) ? $call_to_action['phila_action_panel_cta_text_multi'] : '';

        $action_panel_link = isset( $call_to_action['phila_action_panel_link_multi'] ) ? $call_to_action['phila_action_panel_link_multi'] : '';

        $action_panel_link_loc = isset(  $call_to_action['phila_action_panel_link_loc_multi'] ) ? $call_to_action['phila_action_panel_link_loc_multi'] : '';

        $action_panel_fa = isset( $call_to_action['phila_action_panel_fa_multi'] ) ? $call_to_action['phila_action_panel_fa_multi'] : '';
        ?>
        <?php phila_grid_column_counter( $item_count ); ?>
        <div class="medium-<?php echo $columns; ?> columns">
          <?php if ( !empty( $action_panel_link ) ): ?>
          <a href="<?php echo $action_panel_link; ?>"  class="action-panel">
            <div class="panel hover-fade <?php if( $item_count > 1 ) echo 'equal';?>">
            <header class="<?php echo $columns == '24' ? 'text-align-left' : ''; ?>">
            <?php if ($action_panel_fa): ?>
              <div class="<?php echo $columns == '24' ? 'float-left mrm' : ''; ?> icon">
                <span class="fa-stack <?php echo $columns == '24' ? 'fa-3x' : 'fa-4x'; ?> center" aria-hidden="true">
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa <?php echo $action_panel_fa; ?> fa-stack-1x fa-inverse"></i>
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
