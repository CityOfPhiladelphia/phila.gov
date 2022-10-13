<?php $site_banner_feature_flag = rwmb_meta( 'display_site_wide_banner', array( 'object_type' => 'setting' ), 'phila_settings' );
  if(isset($site_banner_feature_flag) && $site_banner_feature_flag != 0) {
    //declare variables
    $banner_heading_text = rwmb_meta( 'heading_text', array( 'object_type' => 'setting' ), 'phila_settings' );
    $site_banner_subtext = rwmb_meta( 'banner_subtext', array( 'object_type' => 'setting' ), 'phila_settings' );
    $site_button_text = rwmb_meta( 'button_text', array( 'object_type' => 'setting' ), 'phila_settings' );
    $site_button_url = rwmb_meta( 'button_url', array( 'object_type' => 'setting' ), 'phila_settings' );
    $banner_icon = rwmb_meta( 'icon', array( 'object_type' => 'setting' ), 'phila_settings' );

?>
<div class="site-wide-banner">
  <div class="row">
    <div class="medium centered">
      <div class="grid-x grid-padding-x align-top pvs align-justify">
        <div class="cell medium-auto medium-shrink small-24 align-self-middle">
        <?php if(isset($banner_icon)) { ?>
          <i class="<?php echo $banner_icon; ?> fa-fw fa-2x icon hide-for-small-only" aria-hidden="true"></i>
        <?php } ?>
        </div>
        <div class="cell auto message align-self-middle">
        <div class="banner-text">
        <?php if(isset($banner_heading_text)) { ?>
          <p class="mbn"><b><?php echo $banner_heading_text; ?></b></p>
        <?php } ?>
        <?php if(isset($site_banner_subtext)) { ?>
          <p class="banner-deadline mbn"><?php echo $site_banner_subtext; ?></p>
        <?php } ?>
          </div>
        </div>
        <div class="cell medium-auto medium-shrink small-24 align-self-right">
          <?php if(isset($site_button_text) && (isset($site_button_url))) { ?>
          <a class="banner-button button" href="<?php echo $site_button_url;?>"><b><?php echo $site_button_text; ?></b></a>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } // sitewide banner feature flag ?>
