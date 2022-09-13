<?php $voting_banner_feature_flag = rwmb_meta( 'display_site_wide_banner', array( 'object_type' => 'setting' ), 'phila_settings' );
  if(isset($voting_banner_feature_flag) && $voting_banner_feature_flag != 0) {
?>
<div class="voting-banner">
  <div class="row">
    <div class="medium centered">
      <div class="grid-x grid-padding-x align-top pvs align-justify">
        <div class="cell medium-auto medium-shrink small-24 align-self-middle">
          <i class="fas fa-check-to-slot fa-fw fa-2x icon hide-for-small-only" aria-hidden="true"></i>
        </div>
        <div class="cell auto message align-self-middle">
          <div class="voting-text">
            <p class="mbn"><b>Election day is Nov. 8, 2022</b></p>
            <p class="vote-deadline mbn">The deadline to register to vote is Oct. 24, 2022.</p>
          </div>
        </div>
        <div class="cell medium-auto medium-shrink small-24 align-self-right">
          <a class="vote-button button" href="https://vote.phila.gov/voting/my-vote-my-way/"><b>Make a plan to vote</b></a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } // voting banner feature flag ?>