<?php /*
  * Template to display full-width calendars
  * $cal_id - required
  * $cal_category - required
  */
  global $post;
  if (!isset($display_spotlight) || $display_spotlight == null ) {
    $display_spotlight = rwmb_meta('phila_active_event_spotlight');
  } 
  if (!isset($spotlight_id) || $spotlight_id == null ) {
    $spotlight_id = rwmb_meta('phila_event_spotlight');
  }
  $user_selected_template = phila_get_selected_template();
  $post_type_parent = get_post_type($post->ID);
  if( !isset($override_url)) {
    $override_url = rwmb_meta('override_url');
  }
  ?>
<?php if ( !empty( $cal_id )  || !empty($display_spotlight)):?>
  <!-- Full Width Calendar -->
  <section class="expanded mvl">
    <div class="row">
      <div class="columns">
        <h2 id="calendar">Events</h2>
      </div>
    </div>
    <?php if ( !is_singular('event_spotlight') && $display_spotlight == 1 ): ?>
      <div class="grid-container mbl">
        <?php include(locate_template('partials/event-spotlight/card.php')); ?>
      </div>
    <?php endif;?>
    <div class="row calendar-row">
      <div class="medium-centered large-24 columns <?php echo ($user_selected_template == 'custom_content' || $post_type_parent === 'guides') ? 'custom' : '' ?>">
        <?php echo do_shortcode('[calendar id="' . $cal_id . '"]'); ?>
      </div>
    </div>
    <?php if ( !empty( $cal_category ) ):?>
      <div class="row">
        <div class="columns">
          <?php 
          $slang_name = rawurlencode(html_entity_decode(trim( $cal_category ) ) );
          $see_all = array(
            'URL' => '/the-latest/all-events/?category=' . $slang_name,
            'content_type' => 'events',
            'nice_name' => 'events'
          ); ?>
          <?php if (!empty($calendar_see_all) ):
              $override_url = $calendar_see_all;
            endif; ?>
          <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
        </div>
      </div>
    <?php endif; ?>
  </section>
<?php endif;?>
