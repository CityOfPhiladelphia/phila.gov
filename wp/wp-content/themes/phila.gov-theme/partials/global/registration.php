<?php
/* Display registration information */
$is_closed = true;

$current_time = current_time('timestamp');

$registration_start = isset( $registration['start_date']['timestamp'] ) ? $registration['start_date']['timestamp'] : '';

$registration_end = isset( $registration['end_date']['timestamp'] ) ? $registration['end_date']['timestamp'] : '';

//Add the number of seconds in 24 hours to the base date, which will always be 00:00:00 of the selected day. This ensures the update will remain visible for the duration of the selected day. TODO: Turn this into a helper function
if ( ( intval( $registration_start ) ) <= $current_time && ( intval( $registration_end  ) + 86400 ) >= $current_time ) :
  $is_closed = false;
endif;

?>
<?php if (isset($registration_start) ): ?>
  <section class="registration">
    <div class="row">
      <div class="columns">
        <h2 id="<?php echo phila_format_uri( $registration['title'] )?>"><?php echo $registration['title']?></h2>
      </div>
    </div>
    <div class="row">
      <div class="columns">
        <div class="status <?php echo ($is_closed == true ? 'closed' : 'open') ?>">
          <div class="row">
            <div class="columns small-1 hide-for-small-only">
              <?php if( $is_closed == true ) : ?>
                <span class="fa-stack fa-lg">
                  <i class="fa fa-circle fa-stack-2x" aria-hidden="true"></i>
                  <i class="fa fa-exclamation fa-stack-1x fa-inverse"  aria-hidden="true"></i>
                </span>
              <?php else :?>
                <span class="fa-stack fa-lg">
                  <i class="fa fa-circle fa-stack-2x" aria-hidden="true"></i>
                  <i class="fa fa-check fa-stack-1x fa-inverse"  aria-hidden="true"></i>
                </span>
              <?php endif; ?>
            </div>
            <div class="columns small-23">
              <div class="copy">
                <h3><?php echo ( $is_closed == true ?  $registration['closed']['phila_custom_wysiwyg']['phila_wysiwyg_title']: $registration['open']['phila_custom_wysiwyg']['phila_wysiwyg_title'] ) ?>
                </h3>
                <?php echo ($is_closed == true ? apply_filters( 'the_content', $registration['closed']['phila_custom_wysiwyg']['phila_wysiwyg_content']) : apply_filters( 'the_content',$registration['open']['phila_custom_wysiwyg']['phila_wysiwyg_content'] ) )?>
                <?php if ($is_closed == false) : ?>
                  <?php foreach ($registration['open']['links'] as $link) : ?>
                    <a class="button <?php echo isset( $link[is_external] ) ? 'external' : '' ?>" href="<?php echo $link['link_url']?>"><?php echo $link['link_text']?></a>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section><!-- End registration -->
<?php endif; ?>
