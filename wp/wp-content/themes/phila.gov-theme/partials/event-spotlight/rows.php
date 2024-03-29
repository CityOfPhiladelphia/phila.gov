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
  <?php $c = 0 ?>
  <?php foreach ($page_rows as $key => $value):
    $current_row = $page_rows[$key];
    $c++; ?>
    <?php if ( $current_row['spotlight_options'] == 'free_text'): ?>
      <?php if ( isset( $current_row['free_text_option'] ) ):
        $custom_text = $current_row['free_text_option']; ?>
        <!-- Custom Text -->
        <div id="anchor-<?php echo $c ?>" data-magellan-target="anchor-<?php echo $c ?>" class="es-free-text">
          <section class="row mvl">
            <div class="large-24 column">
              <h2 id="<?php echo phila_format_uri($custom_text['phila_custom_wysiwyg']['phila_wysiwyg_title']) ?>" data-magellan-target="anchor-<?php echo phila_format_uri($custom_text['phila_custom_wysiwyg']['phila_wysiwyg_title']) ?>"><?php echo $custom_text['phila_custom_wysiwyg']['phila_wysiwyg_title'] ?></h2>
              <div>
                <?php echo isset($custom_text['phila_custom_wysiwyg']['phila_wysiwyg_content']) ? apply_filters('the_content', $custom_text['phila_custom_wysiwyg']['phila_wysiwyg_content']) : '' ?>
              </div>
            </div>
          </section>
        </div>
      <?php endif; ?>

    <?php elseif ( $current_row['spotlight_options'] == 'custom_text_multi'): ?>
      <?php if ( isset( $current_row['custom_text_multi_option'] ) ):
        $custom_text = $current_row['custom_text_multi_option'];
        $multi_full_row = true;?>
        <div id="anchor-<?php echo $c ?>" data-magellan-target="anchor-<?php echo $c ?>" class="es-custom-text-multi">
          <div class="row mvxl">
            <?php include(locate_template('partials/departments/content-custom-text-multi.php')); ?>
          </div>
        </div>
      <?php endif; ?>

    <?php elseif ( $current_row['spotlight_options'] == 'registration'): ?>
      <?php if ( isset( $current_row['phila_registration'] ) ):
        $registration = $current_row['phila_registration']; ?>
        <div id="anchor-<?php echo $c ?>" data-magellan-target="anchor-<?php echo $c ?>" class="es-registration">
          <?php include(locate_template('partials/global/registration.php')); ?>
        </div>
      <?php endif; ?>

    <?php elseif ( $current_row['spotlight_options'] == 'call_to_action_multi'): ?>

      <?php if ( !isset( $current_row['call_to_action_multi']) ):
        $contrast = false;
        $phila_dept_homepage_cta =
        $current_row['call_to_action_multi_row']['phila_call_to_action_section'];?>
        <div id="anchor-<?php echo $c ?>" data-magellan-target="anchor-<?php echo $c ?>" class="es-call-to-action">
          <?php include(locate_template('partials/departments/phila_call_to_action_multi.php')); ?>
        </div>
      <?php endif; ?>

    <?php elseif ( $current_row['spotlight_options'] == 'calendar'): ?>
      <?php if(!isset($current_row['spotlight_options']['calendar_row'])): ?>
        <?php
        $cal_id = isset( $current_row['calendar_row']['phila_full_width_calendar_id'] ) ? $current_row['calendar_row']['phila_full_width_calendar_id'] : '';

        $cal_owner_id = isset( $current_row['calendar_row']['phila_calendar_owner'] ) ? $current_row['calendar_row']['phila_calendar_owner'] : ''; 
        $cal_category = get_the_category_by_ID($cal_owner_id);
        $override_url = isset( $current_row['calendar_row']['override_url'] ) ? $current_row['calendar_row']['override_url'] : '';
        ?>
        <div id="anchor-<?php echo $c ?>" data-magellan-target="<?php echo $c ?>" class="es-calendar">
          <?php include( locate_template( 'partials/departments/v2/calendar.php' ) ); ?>
        </div>
      <?php endif;?>

    <?php elseif ( $current_row['spotlight_options'] == 'accordion'): ?>
      <!-- Accordion group  -->
      <?php if ( !isset( $current_row['accordion_group_title']) ):
        $accordion_title = $current_row['accordion_row']['accordion_row_title'];
        $accordion_group = $current_row['accordion_row']['accordion_group'];
        $is_full_width = true;?>
        <div id="anchor-<?php echo $c ?>" data-magellan-target="anchor-<?php echo $c ?>" class="es-accordion">
          <?php include(locate_template('partials/global/accordion.php')); ?>
        </div>
      <?php endif; ?>

    <?php elseif ( $current_row['spotlight_options'] == 'full_width_cta' ):
      $link = phila_cta_full_display($current_row['full_width_cta']); ?>
        <?php include(locate_template('partials/departments/v2/full-width-call-to-action.php')); ?>

    <?php elseif ( $current_row['spotlight_options'] == 'image_list' ):

        $image_list = $current_row['phila_image_list'];

        $image_list_vars = phila_image_list($image_list);

      ?>
      <div id="anchor-<?php echo $c ?>" data-magellan-target="anchor-<?php echo $c ?>" class="es-full-width-cta">
        <?php include(locate_template('partials/programs/image-list.php')); ?>
      </div>

    <?php elseif ( $current_row['spotlight_options'] == 'featured_events' ):

        $featured_events = $current_row['featured_events'];

      ?>
      <div id="anchor-<?php echo $c ?>" data-magellan-target="anchor-<?php echo $c ?>" class="es-featured-events">
        <?php include(locate_template('partials/event-spotlight/featured-events.php')); ?>
      </div>

    <?php elseif ( $current_row['spotlight_options'] == 'posts' ):
        $is_spotlight_tag['tag'] = isset($current_row['blog_posts']['tag']) ? $current_row['blog_posts']['tag'] : '';
        ?>
        <div id="anchor-<?php echo $c ?>" data-magellan-target="anchor-<?php echo $c ?>" class="es-blogs">
          <div class="mvxl">
            <?php include(locate_template('partials/posts/post-grid.php')); ?>
          </div>
        </div>

    <?php endif; ?>

  <?php endforeach; ?>
</div>
<hr class="margin-auto">
<?php include( locate_template( 'partials/global/cta-go-to-latest.php' ) ); ?>
