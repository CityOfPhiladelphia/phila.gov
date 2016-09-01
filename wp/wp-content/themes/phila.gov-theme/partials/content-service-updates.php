

<div class="row">
  <div class="large-24 columns">

    <?php $service_updates = phila_get_service_updates();?>

    <?php if (is_array($service_updates)): ?>
    <h2 class="contrast">City Service Updates &amp; Changes</h2>
    <p>Please continue to access this page for up-to-date information. To ask questions or report an issue, contact 3-1-1.</p>
    <div class="row">
    <?php $i=0; ?>
    <?php foreach ($service_updates as $update):?>
      <?php if ($i > 3) break; ?>
        <div class="small-24 columns centered service-update equal-height <?php if ( !$update['service_level'] == '' ) echo $update['service_level']; ?> ">
              <div class="service-update-icon equal">
                <div class="valign">
                  <div class="valign-cell pam">
                    <i class="fa <?php if ( $update['service_icon'] ) echo $update['service_icon']; ?>  fa-2x" aria-hidden="true"></i>
                    <span class="icon-label small-text"><?php if ( $update['service_type'] ) echo $update['service_type']; ?></span>
                  </div>
                </div>
              </div>
              <div class="service-update-details phm equal">
                <div class="valign">
                  <div class="valign-cell pvm">
                    <?php if ( !$update['service_message'] == '' ):?>
                      <span><?php  echo $update['service_message']; ?></span>                              <br/>
                    <?php endif;?>
                    <?php if ( !$update['service_link_text'] == '' && !$update['service_link'] == '' ):?>
                      <a href="<?php echo $update['service_link']; ?>" class="external"><?php echo $update['service_link_text']; ?></a>                              <br/>
                    <?php endif;?>
                    <?php if ( !$update['service_effective_date'] == ''):?>
                      <span class="date small-text"><em>In Effect: <?php  echo $update['service_effective_date']; ?></em></span>
                    <?php endif;?>
                  </div>
                </div>
              </div>
            </div>
      <?php ++$i; ?>
  <?php endforeach; ?>
</div>
</div>
<?php endif; ?>
<?php if (!$action_panel_summary == ''): ?>
<div class="large-6 columns">
  <h2 class="contrast"><?php echo $action_panel_title; ?></h2>
  <?php if (!$action_panel_link == ''): ?>
    <a href="<?php echo $action_panel_link; ?>"  class="action-panel">
      <div class="panel">
        <header>
          <?php if ($action_panel_fa_circle): ?>
            <div>
              <span class="fa-stack fa-4x center" aria-hidden="true">
              <i class="fa fa-circle fa-stack-2x"></i>
              <i class="fa <?php echo $action_panel_fa; ?> fa-stack-1x fa-inverse"></i>
            </span>
          </div>
          <?php else:?>
            <div>
              <span><i class="fa <?php echo $action_panel_fa; ?> fa-4x" aria-hidden="true"></i></span>
            </div>
          <?php endif;?>
            <?php if (!$action_panel_cta_text == ''): ?>
              <span class="center <?php if ($action_panel_link_loc) echo 'external';?>"><?php echo $action_panel_cta_text; ?></span>
            <?php endif; ?>
        </header>
        <hr class="mll mrl">
          <span class="details"><?php echo $action_panel_summary; ?></span>
      </div>
    </a>
  <?php endif; ?>
</div>
<?php endif; ?>

</div>
