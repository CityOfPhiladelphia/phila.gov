<?php
/**
 * The template used for displaying Event Spotlight Pages
 *
 * @package phila-gov
 */
?>
<?php
  $page_rows = rwmb_meta('spotlight_row');
?>

<div class="spotlight-content">
  <?php
    foreach ($page_rows as $key => $value):
      $current_row = $page_rows[$key]; ?>
    <!-- Grid Row -->
      <?php if ( $current_row['spotlight_options'] == 'free_text'): ?>
        <?php if ( isset( $current_row['free_text_option'] ) ):
          $custom_text = $current_row['free_text_option']; ?>
          <!-- Custom Text -->
          <section class="row mvl">
            <div class="large-24 column">
              <h2 id="<?php echo phila_format_uri($custom_text['phila_custom_wysiwyg']['phila_wysiwyg_title']) ?>" data-magellan-target="<?php echo phila_format_uri($custom_text['phila_custom_wysiwyg']['phila_wysiwyg_title']) ?>"><?php echo $custom_text['phila_custom_wysiwyg']['phila_wysiwyg_title'] ?></h2>
              <div>
                <?php echo $custom_text['phila_custom_wysiwyg']['phila_wysiwyg_content'] ?>
              </div>
            </div>
          </section>
        <?php endif; ?>
      <?php elseif ( $current_row['spotlight_options'] == 'registration'): ?>
        <?php if ( isset( $current_row['phila_registration'] ) ):

          $registration = $current_row['phila_registration']; ?>
          <?php include(locate_template('partials/programs/registration.php')); ?>
        <?php endif; ?>

      <?php elseif ( $current_row['spotlight_options'] == 'call_to_action_multi'): ?>
        <?php if ( !isset( $current_row['call_to_action_multi']) ):
          $contrast = false;
          $phila_dept_homepage_cta =
          $current_row['call_to_action_multi_row']['phila_call_to_action_section'];?>
            <?php include(locate_template('partials/departments/phila_call_to_action_multi.php')); ?>
        <?php endif; ?>
      <?php elseif ( $current_row['spotlight_options'] == 'calendar'): ?>
        <?php if(!isset($current_row['spotlight_options']['calendar_row'])): ?>
          <?php
          $cal_id = isset( $current_row['calendar_row']['phila_full_width_calendar_id'] ) ? $current_row['calendar_row']['phila_full_width_calendar_id'] : '';

          $cal_category = isset( $current_row['calendar_row']['phila_calendar_owner'] ) ? $current_row['calendar_row']['phila_calendar_owner'] : ''; ?>
          <?php include( locate_template( 'partials/departments/v2/calendar.php' ) ); ?>
        <?php endif;?>
      <?php elseif ( $current_row['spotlight_options'] == 'accordion'): ?>
      <!-- Accordion group  -->
      <?php if ( !isset( $current_row['accordion_group_title']) ):
        $accordion_title = $current_row['accordion_row']['accordion_row_title'];

        $accordion_group = $current_row['accordion_row']['accordion_group'];?>
        <?php include(locate_template('partials/global/accordion.php')); ?>
        <?php endif; ?>

    <?php endif;  /*end full row */?>
    <!-- Grid Row -->
    <?php endforeach; ?>
</div>
<hr>
<div class="grid-container">
  <div class="grid-x mvxl">
    <div class="cell panel <?php echo (!empty( $link['is_survey'] ) ) ? 'survey' : '' ?>">
      <div class="grid-x align-middle">
        <div class="small-24 medium-18 cell">
            <h3 class="mbn">More from the City of Philadelphia</h3>
            <p class="mts">Get the latest announcements, posts, press releases, and events.</p>
        </div>
        <div class="small-24 medium-6 cell center">
          <a class="button full" href="/the-latest">
            <div class="valign">
                <div class="button-label valign-cell">
                  Go to the latest
                </div>
              </div>
            </a>
          </div>
      </div>
    </div>
  </div>
</div>
