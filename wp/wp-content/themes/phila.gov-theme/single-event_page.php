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
            // Set Event Detail vars
            $event_description = rwmb_meta('phila_event_desc' , $args = array('type' => 'textarea'));
            $event_location = rwmb_meta('phila_event_loc' , $args = array('type' => 'textarea'));
            $event_location_link = rwmb_meta('phila_event_loc_link' , $args = array('type' => 'url'));
            $event_start_date = rwmb_meta('phila_event_start' , $args = array('type' => 'date'));
            $event_end_date = rwmb_meta('phila_event_end' , $args = array('type' => 'date'));
            $event_connect = rwmb_meta('phila_event_connect' , $args = array('type' => 'textarea'));

            $event_contact_blocks_header = rwmb_meta('phila_event_content_blocks_heading' , $args = array('type' => 'text'));
            $event_contact_blocks_link_text = rwmb_meta('phila_event_content_blocks_link_text' , $args = array('type' => 'text'));
            $event_contact_blocks_link = rwmb_meta('phila_event_content_blocks_link' , $args = array('type' => 'url'));

            $action_panel_title = rwmb_meta('phila_action_section_title' , $args = array('type' => 'text'));
            $action_panel_summary = rwmb_meta('phila_action_panel_summary' , $args = array('type' => 'textarea'));
            $action_panel_cta_text = rwmb_meta('phila_action_panel_cta_text' , $args = array('type' => 'text'));
            $action_panel_link = rwmb_meta('phila_action_panel_link' , $args = array('type' => 'url'));
            $action_panel_link_loc  = rwmb_meta('phila_action_panel_link_loc' , $args = array('type' => 'checkbox'));
            $action_panel_fa_circle  = rwmb_meta('phila_action_panel_fa_circle' , $args = array('type' => 'checkbox'));
            $action_panel_fa = rwmb_meta('phila_action_panel_fa' , $args = array('type' => 'text'));
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

                        <h3 class="h3">Dates</h3>

                        <?php // TODO: Create a date utility to replace this ?>
                        <?php $comparison_date_one = explode(' ', $event_start_date); ?>
                        <?php $comparison_date_two = explode(' ', $event_end_date); ?>
                        <?php if ($comparison_date_one[0] === $comparison_date_two[0]): ?>
                          <span class="nowrap"><?php echo $event_start_date; ?></span> - <span class="nowrap"><?php echo $comparison_date_two[1]; ?></span>
                        <?php else: ?>
                          <span class="nowrap"><?php echo $event_start_date; ?></span> - <span class="nowrap"><?php echo $event_end_date; ?></span>
                        <?php endif; ?>

                      </div>
                    </div>
                    <div class="small-14 medium-24 large-24 columns equal-height">
                      <div class="equal event-icon event-location-icon">
                        <i class="fa fa-map-marker fa-4x" aria-hidden="true"></i>
                      </div>
                      <div class="equal event-location-details small-text">

                        <h3 class="h3">Main Location</h3>

                        <?php echo $event_location;?>
                        <?php if (!$event_location_link == ''): ?>
                          <a href="<?php echo $event_location_link;?>" class="external" target="_blank">View map</a>
                        <?php endif; ?>
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

        <div class="row mvm">
          <div class="small-24 columns">
            <div class="row">
              <div class="large-18 columns">

                <?php $service_updates = phila_get_service_updates();?>

                <?php if (is_array($service_updates)): ?>
                <h2 class="contrast">City Service Updates &amp; Changes</h2>
                <p>Please continue to access this page for up-to-date information. To ask questions or report an issue, contact 3-1-1.</p>
                <div class="row">
                <?php $i=0; ?>
                <?php foreach ($service_updates as $update):?>
                  <?php if ($i > 3) break; ?>
                    <div class="small-24 columns centered service-update equal-height <?php if ( !$update['service_level'] == '' ) echo $update['service_level']; ?> ">
                          <div class="service-update-icon equal">
                            <div class="valign">
                              <div class="valign-cell pam">
                                <i class="fa <?php if ( $update['service_icon'] ) echo $update['service_icon']; ?>  fa-2x" aria-hidden="true"></i>
                                <span class="icon-label small-text"><?php if ( $update['service_type'] ) echo $update['service_type']; ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="service-update-details phm equal">
                            <div class="valign">
                              <div class="valign-cell pvm">
                                <?php if ( !$update['service_message'] == '' ):?>
                                  <span><?php  echo $update['service_message']; ?></span>                              <br/>
                                <?php endif;?>
                                <?php if ( !$update['service_link_text'] == '' && !$update['service_link'] == '' ):?>
                                  <a href="<?php echo $update['service_link']; ?>" class="external" target="_blank"><?php echo $update['service_link_text']; ?></a>                              <br/>
                                <?php endif;?>
                                <?php if ( !$update['service_effective_date'] == ''):?>
                                  <span class="date small-text"><em>In Effect: <?php  echo $update['service_effective_date']; ?></em></span>
                                <?php endif;?>
                              </div>
                            </div>
                          </div>
                        </div>
                  <?php ++$i; ?>
              <?php endforeach; ?>
                </div>
          </div>
          <?php endif; ?>
          <?php if (!$action_panel_summary == ''): ?>
            <div class="large-6 columns">
              <h2 class="contrast">Permits</h2>
              <?php if (!$action_panel_link == ''): ?>
                <a href="<?php echo $action_panel_link; ?>"  target="_blank" class="action-panel">
                  <div class="panel">
                    <header>
                      <?php if ($action_panel_fa_circle): ?>
                        <div>
                          <span class="fa-stack fa-4x center" aria-hidden="true">
                          <i class="fa fa-circle fa-stack-2x"></i>
                          <i class="fa <?php echo $action_panel_fa; ?> fa-stack-1x fa-inverse"></i>
                        </span>
                      </div>
                      <?php else:?>
                        <div>
                          <span><i class="fa <?php echo $action_panel_fa; ?> fa-4x" aria-hidden="true"></i></span>
                        </div>
                      <?php endif;?>
                        <?php if (!$action_panel_cta_text == ''): ?>
                          <span class="center <?php if ($action_panel_link_loc) echo 'external';?>"><?php echo $action_panel_cta_text; ?></span>
                        <?php endif; ?>
                    </header>
                    <hr class="mll mrl">
                      <span class="details"><?php echo $action_panel_summary; ?></span>
                  </div>
                </a>
                <?php endif ?>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </div>
    <!-- Things to See and Do -->
    <?php $output_array = phila_get_event_content_blocks(); ?>

    <?php if (is_array($output_array)):?>
      <div class="row equal-height">
        <div class="small-24 columns">
          <?php if (!$event_contact_blocks_header == ''): ?>
            <h2 class="contrast"><?php echo $event_contact_blocks_header;?></h2>
          <?php endif; ?>
          <!-- Begin Column One -->
          <?php if (array_key_exists( 0 , $output_array )): ?>
            <div class="medium-8 columns">
              <div class="row">
                <div class="small-24 columns">
                  <a href="<?php echo $output_array[0]['block_link']; ?>" class="card">
                    <img src="<?php echo $output_array[0]['block_image']; ?>" alt="">
                    <div class="content-block equal">
                      <h3 class="external"><?php echo $output_array[0]['block_title']; ?></h3>
                      <p><?php echo $output_array[0]['block_summary']; ?></p>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          <?php endif; ?>
          <?php if (array_key_exists( 1 , $output_array )): ?>
            <!-- Begin Column Two -->
            <div class="medium-16 columns">
              <div class="row">
                <div class="small-24 columns">
                  <div class="news equal">
                    <ul>
                      <?php $output_index = 0; ?>
                      <?php foreach ($output_array as $key => $array_value):
                        if ($output_index > 0): ?>
                          <li class="group">
                            <a href="<?php echo $array_value['block_link']; ?>" class="group"><img class="alignleft small-thumb wp-post-image" src="<?php echo $array_value['block_image']; ?>" alt="">
                            <div class="pbm">
                              <h3 class="external"><?php echo $array_value['block_title']; ?></h3>
                              <span class="small-text"><?php echo $array_value['block_summary']; ?></span>
                              <?php if (!$array_value['block_image_credit']==''): ?>
                              <span class="photo-credit small-text mtm">Photo by <?php echo $array_value['block_image_credit']; ?></span>
                              <?php endif; ?>
                            </div>
                            </a>
                          </li>
                        <?php endif; ?>
                        <?php $output_index++;?>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>
          <?php if (!$event_contact_blocks_link == ''): ?>
            <?php $link_text = (!$event_contact_blocks_link_text=='') ? $event_contact_blocks_link_text : "More"; ?>
            <a class="see-all-right float-right" href="<?php echo $event_contact_blocks_link;?>"><?php echo $link_text ?></a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (array_key_exists( 0 , get_the_category() )): ?>
      <!-- Recent News  -->
      <div class="row news equal-height">
      <?php echo do_shortcode('[recent-news posts="3"]'); ?>
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
