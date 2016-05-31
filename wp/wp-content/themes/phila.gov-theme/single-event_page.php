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
            $event_start_date = rwmb_meta('phila_event_start' , $args = array('type' => 'date'));
            $event_end_date = rwmb_meta('phila_event_end' , $args = array('type' => 'date'));
            $event_connect = rwmb_meta('phila_event_connect' , $args = array('type' => 'textarea'));
            ?>
    <?php endif; ?>
    <?php
      // Set Status Update vars
      // TODO: Replace with actual values
      $status_updates = array(
          0 => array(
            'message' => 'All City of Philadelphia offices will remain open for business.',
            'level' => '',
            'type' => 'City',
            'icon' => 'fa-institution',
            'dates' => 'July 25 -28, 2016',
          ),
          1 => array(
            'message' => 'Trash and recycling pickup will occur on a normal schedule. Stay tuned for additional information.',
            'level' => '',
            'type' => 'Trash',
            'icon' => 'fa-trash',
            'dates' => 'July 25, 2016',
          ),
          2 => array(
            'message' => 'Road closures will be minimal. Stay tuned for additional information.',
            'level' => 'warning',
            'type' => 'Roads',
            'icon' => 'fa-road',
            'dates' => 'July 25 -28, 2016',
          ),
          // 3 => array(
          //   'message' => 'It\'s a transit test with a somewhat long title',
          //   'level' => 'critical',
          //   'type' => 'Transit',
          //   'icon' => 'fa-subway',
          //   'dates' => 'July 25',
          // ),
      );
    ?>
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

        <div class="row mvm">
          <div class="small-24 columns">
            <div class="row">
              <div class="large-18 columns">
            <?php if (is_array($status_updates)): ?>
            <h2 class="contrast">City Service Updates &amp; Changes</h2>
            <p>Please continue to access this page for up-to-date information. To ask questions or report an issue, contact 3-1-1.</p>
            <div class="row">
            <?php foreach ($status_updates as $update):?>
              <div class="small-24 columns centered service-update equal-height <?php if ( !$update['level'] == '' ) echo $update['level']; ?> ">
                <a href="#/">
                    <div class="service-update-icon equal">
                      <div class="valign">
                        <div class="valign-cell pam">
                          <i class="fa <?php if ( $update['icon'] ) echo $update['icon']; ?>  fa-2x" aria-hidden="true"></i>
                          <span class="icon-label small-text"><?php if ( $update['type'] ) echo $update['type']; ?></span>
                        </div>
                      </div>
                    </div>
                    <div class="service-update-details pam equal">
                        <div>
                          <?php if ( !$update['message'] == '' ):?>
                            <span><?php  echo $update['message']; ?></span>
                          <?php endif;?>
                          <br/>
                          <?php if ( !$update['dates'] == '' && !$update['level'] == '' ):?>
                            <span class="date small-text"><em>In Effect: <?php  echo $update['dates']; ?></em></span>
                          <?php endif;?>
                        </div>
                    </div>
                </a>
              </div>
          <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
            <div class="large-6 columns permits">
              <h2 class="contrast">Permits</h2>
              <div class="panel">
                <header>
                  <span class="fa-stack fa-4x center">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-file-text fa-stack-1x fa-inverse"></i>
                  </span>
                  <h3><a href="https://phillymdoevents.files.wordpress.com/2016/01/demonstration-application-revised-01-08-16.pdf" class="h4 external center" target="_blank">Demonstration Permits</a></h3>
                </header>
              <!-- TODO: Determine where this content should be pulled from and replace static copy -->
                <span>Demonstrations are an important part of our political history. Philadelphia welcomes groups looking to express their right to free speech peacefully. Please submit a request for a permit.</span>
              </div>
            </div>
        </div>
      </div>

    <!-- TODO: Identify correct news Category -->
    <!-- Recent News  -->
    <div class="row news equal-height">
    <?php echo do_shortcode('[recent-news posts="3"]'); ?>
    </div>

    <!-- Things to See and Do -->
    <?php $output_array = phila_get_event_posts(); ?>

    <div class="row equal-height">
      <div class="small-24 columns">
        <h2 class="contrast">Things to See &amp; Do</h2>
        <!-- Begin Column One -->
        <div class="large-6 columns">
          <div class="row">
            <div class="large-24 columns">
              <a href="<?php echo $output_array[0]['permalink']; ?>" class="card">
                <?php echo get_the_post_thumbnail( $output_array[0]['postID'], 'news-thumb' ); ?>
                <div class="content-block equal">
                  <h3><?php echo get_the_title( $output_array[0]['postID'] ); ?></h3>
                  <p><?php echo $output_array[0]['desc']; ?></p>
                </div>
              </a>
            </div>
          </div>
        </div>
        <!-- Begin Column Two -->
        <div class="large-18 columns">
          <div class="row">
            <div class="large-24 columns">
              <div class="news equal">
                <ul>
                  <?php $output_index = 0; ?>
                  <?php foreach ($output_array as $output_val):
                    if ($output_index > 0): ?>
                      <li class="group mbm pbm">
                      <?php echo get_the_post_thumbnail( $output_val['postID'], 'news-thumb', 'class= small-thumb mrm' ); ?>
                      <span class="entry-date small-text"><?php echo $output_val['date']; ?></span>
                      <a href="<?php echo $output_val['permalink']; ?>"><h3><?php echo get_the_title( $output_val['postID'] ); ?></h3></a>
                      <span class="small-text"><?php echo $output_val['desc']; ?></span>
                      </li>
                    <?php endif; ?>
                    <?php $output_index++;?>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <!-- TODO: Link to the correct post category -->
        <a class="see-all-right float-right" href="/posts/">All Things to See &amp; Do</a>
      </div>
    </div>

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
