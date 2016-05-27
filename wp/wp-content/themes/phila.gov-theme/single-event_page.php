<?php
/**
 * The template used for displaying event pages
 *
 * @package phila-gov
 */

global $post;

get_header(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('department clearfix'); ?>>
  <div class="row">
    <header class="entry-header small-24 columns">
        <h1 class="entry-title contrast mbn"><?php echo $post->post_title;?></h1>
    </header>
  </div>

  <div data-swiftype-index='true' class="entry-content">
    <?php if (function_exists('rwmb_meta')): ?>
      <?php // Set custom markup vars
            $append_before_wysiwyg = rwmb_meta( 'phila_append_before_wysiwyg', $args = array('type' => 'textarea'));
            $append_before_wysiwyg = rwmb_meta( 'phila_append_before_wysiwyg', $args = array('type' => 'textarea'));
            $append_after_wysiwyg = rwmb_meta( 'phila_append_after_wysiwyg', $args = array('type' => 'textarea'));
            // Set hero-header vars
            $hero_header_image = rwmb_meta( 'phila_hero_header_image', $args = array('type' => 'file_input'));
            $hero_header_alt_text = rwmb_meta( 'phila_hero_header_image_alt_text', $args = array('type' => 'text'));
            $hero_header_credit = rwmb_meta( 'phila_hero_header_image_credit', $args = array('type' => 'text'));
            $hero_header_title = rwmb_meta( 'phila_hero_header_title', $args = array('type' => 'text'));
            $hero_header_body_copy = rwmb_meta( 'phila_hero_header_body_copy', $args = array('type' => 'textarea'));
            $hero_header_call_to_action_button_url = rwmb_meta( 'phila_hero_header_call_to_action_button_url', $args = array('type' => 'URL'));
            $hero_header_call_to_action_button_text = rwmb_meta( 'phila_hero_header_call_to_action_button_text', $args = array('type' => 'text'));
            // Set Event Details
            $event_description = rwmb_meta('phila_event_desc' , $args = array('type' => 'textarea'));
            $event_location = rwmb_meta('phila_event_loc' , $args = array('type' => 'textarea'));
            $event_start_date = rwmb_meta('phila_event_start' , $args = array('type' => 'date'));
            $event_end_date = rwmb_meta('phila_event_end' , $args = array('type' => 'date'));
            $event_connect = rwmb_meta('phila_event_connect' , $args = array('type' => 'textarea'));
      ?>
    <?php endif; ?>
    <!-- If Custom Markup append_before_wysiwyg is present print it -->
    <?php if (!$append_before_wysiwyg == ''):?>
      <div class="row before-wysiwyg">
        <div class="small-24 columns">
          <?php echo $append_before_wysiwyg; ?>
        </div>
      </div>
    <?php endif; ?>
    <!-- Hero-Header MetaBox Modules -->
    <?php if (!$hero_header_image == ''): ?>
    <div class="row mtm">
      <div class="small-24 columns">
        <section class="department-header">
          <img id="header-image" class="size-full wp-image-4069" src="<?php echo $hero_header_image; ?>" alt="<?php echo $hero_header_alt_text;?>" width="975" height="431" />
          <?php if (!$hero_header_credit == ''): ?>
            <div class="photo-credit small-text">
              <span><i class="fa fa-camera" aria-hidden="true"></i> Photo by <?php echo $hero_header_credit; ?></span>
            </div>
          <?php endif; ?>
          <div class="intro row">
            <div class="column">
              <?php if (!$hero_header_title == ''): ?>
              <h1><?php echo $hero_header_title; ?></h1>
              <?php endif; ?>
              <?php if (!$hero_header_body_copy == ''): ?>
                <p><?php echo $hero_header_body_copy; ?></p>
              <?php endif; ?>
              <?php if (!$hero_header_call_to_action_button_url == ''): ?>
                <p><a href="<?php echo $hero_header_call_to_action_button_url; ?>" class="button alternate no-margin"><?php echo $hero_header_call_to_action_button_text; ?></a></p>
              <?php endif; ?>
            </div>
          </div>
        </section>
      </div>
    </div>
    <?php endif; ?>
    <!-- Event Details -->
    <?php if (!$event_description == ''): ?>
      <div class="row event-official">
        <div class="small-24 columns event-details">
          <div class="row">

            <div class="large-18 columns">
              <h2 class="contrast">Official Event Information</h2>

              <div class="row">
                <div class=" medium-8 large-8 columns event-logistics">
                  <div class="row">
                    <div class="small-10 medium-24 large-24 columns equal-height">
                      <div class="equal event-icon event-date-icon">
                        <i class="fa fa-calendar fa-3x" aria-hidden="true"></i>
                      </div>
                      <div class="equal event-date-details small-text">
                        <h4>Dates</h4>
                        <?php echo '<span class="nowrap">' . $event_start_date . '</span> - <span class="nowrap">' . $event_end_date . '</span>';?>
                      </div>
                    </div>
                    <div class="small-14 medium-24 large-24 columns equal-height">
                      <div class="equal event-icon event-location-icon">
                        <i class="fa fa-map-marker fa-4x" aria-hidden="true"></i>
                      </div>
                      <div class="equal event-location-details small-text">
                        <h4>Main Location</h4>
                        <?php echo $event_location;?>
                      </div>
                    </div>

                  </div>
                </div>
                <div class="large-16 medium-16 columns event-description">
                  <div>
                    <?php echo $event_description;?>
                  </div>
                </div>
              </div>
            </div>

            <div class="large-6 columns connect">
              <h2 class="contrast">Connect</h2>
              <?php echo $event_connect;?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <!-- WYSIWYG content -->
    <?php if( get_the_content() != '' ) : ?>
    <section class="wysiwyg-content">
      <div class="row">
        <div class="small-24 columns">
          <?php echo the_content();?>
        </div>
      </div>
    </section>
    <?php endif; ?>
    <!-- End WYSIWYG content -->

    <!-- If Custom Markup append_after_wysiwyg is present print it -->
    <?php if (!$append_after_wysiwyg == ''):?>
     <div class="row after-wysiwyg">
       <div class="small-24 columns">
         <?php echo $append_after_wysiwyg; ?>
       </div>
     </div>
    <?php endif; ?>

  </div> <!-- End .entry-content -->
</article><!-- #post-## -->
<?php get_footer(); ?>
