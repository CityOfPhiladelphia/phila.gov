<?php
/*
 * Voting banner
*/
?>

<?php

$voting_banner_feature_flag = rwmb_meta( 'display_voting_banner', array( 'object_type' => 'setting' ), 'phila_settings' );
$phila_election_events = rwmb_meta( 'phila_election_events', array( 'object_type' => 'setting' ), 'phila_settings' );


foreach ( $phila_election_events as $event ) {
  $today = new DateTime();
  $today->setTime(0, 0, 0, 0);
  $event_start_date = new DateTime($event['start_date']);
  $event_end_date = new DateTime($event['end_date']);
  $event_start_date->setTime(0, 0, 0, 0);
  $event_end_date->setTime(23, 59, 59);
  if (( $today >= $event_start_date) && ($today <= $event_end_date)){
    $phila_active_event = $event;
    break;
  }
}

if(isset($voting_banner_feature_flag) && $voting_banner_feature_flag != 0 && isset($phila_active_event)) {
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
            <?php if(isset($phila_active_event['event_label'])) { ?>
              <p class="mbn"><b><?php echo $phila_active_event['event_label']; ?></b></p>
            <?php } if(isset($phila_active_event['event_text'])) {  ?>
              <p class="vote-deadline mbn"><?php echo $phila_active_event['event_text']; ?></p>
            <?php } ?>
          </div>
        </div>
        <?php if(isset($phila_active_event['button_url']) && isset($phila_active_event['button_text'])) { ?>
          <div class="cell medium-auto medium-shrink small-24 align-self-right">
            <a class="vote-button button" href="<?php echo $phila_active_event['button_url']; ?>"><b><?php echo $phila_active_event['button_text']; ?></b></a>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
<?php } // voting banner feature flag ?>
<?php 