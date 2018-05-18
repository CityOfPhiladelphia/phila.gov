<?php /*
  * Template to display full-width calendars
  * $cal_id - required
  * $cal_category - required
  */
  $display_spotlight = rwmb_meta('phila_active_event_spotlight');
 ?>
<?php if ( !empty( $cal_id ) ):?>
  <!-- Full Width Calendar -->
  <section class="expanded mtm">
    <div class="row">
      <div class="columns">
        <h2 id="calendar">Events</h2>
      </div>
    </div>
    <?php if ( !is_singular('event_spotlight') && $display_spotlight == 1 ): ?>
      <div class="grid-container mbl">
        <?php get_template_part('partials/event-spotlight/card'); ?>
      </div>
    <?php endif;?>
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
