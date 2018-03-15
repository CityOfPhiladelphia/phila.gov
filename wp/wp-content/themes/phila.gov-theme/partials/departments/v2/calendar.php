<?php /*
  * Template to display full-width calendars
  * $cal_id - required
  * $cal_category - required
  */
 ?>
<?php if ( !empty( $cal_id ) ):?>
  <!-- Full Width Calendar -->
  <section class="expanded mtm">
    <div class="row">
      <div class="columns">
        <h2>Events</h2>
      </div>
    </div>
    <div class="row calendar-row">
      <div class="medium-centered large-24 columns">
        <?php echo do_shortcode('[calendar id="' . $cal_id . '"]'); ?>
      </div>
    </div>
    <?php if ( !empty( $cal_category ) ):?>
      <div class="row">
        <div class="columns">
          <?php $see_all = array(
            'URL' => '/the-latest/all-events/?category=' . $cal_category,
            'content_type' => 'events',
            'nice_name' => 'events'
          ); ?>
          <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
        </div>
      </div>
    <?php endif; ?>
  </section>
<?php endif;?>
