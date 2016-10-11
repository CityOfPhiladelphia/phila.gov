<?php
/*
 * Partial for the "beta alert," i.e. the bar says this site is a work in progress
*/

//TODO: Move away from using id based styles / remove reference to #alpha-alert
?>
<div data-swiftype-index='false' class="alert alert-persistent">
  <div class="row">
    <div class="small-24 columns">
      <p class="hide-for-small-only">
        <?php printf( 'We\'re piloting a new, user-friendly website design. To view the existing City website, visit <a class="go-back external" href="http://www.phila.gov?opt-out">phila.gov</a>.'); ?>
      </p>
      <p class="show-for-small-only">
        Back to <a class="go-back external" href="http://www.phila.gov?opt-out">phila.gov</a>.
      </p>
    </div>
  </div>
</div>
