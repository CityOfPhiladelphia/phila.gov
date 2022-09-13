<?php
if ( class_exists( "Phila_Gov_Site_Wide_Alert_Rendering" ) ){
  $phila_site_wide_alert = new Phila_Gov_Site_Wide_Alert_Rendering();
}

class Phila_Gov_Site_Wide_Alert_Rendering {
  /**
  * TODO: set cookie when button is closed, remember until alert is updated
  * Display alert if display is true, also show on preview
  *
  */
  static function create_site_wide_alerts(){

    $args = array( 'post_type' => 'site_wide_alert' );

    $pages = get_posts( $args );

    $args = array(
      'post_type' => array ('site_wide_alert'),
      'posts_per_page'    => -1
    );

    function dateTimeFormat($date) {
      if ( !$date == '' ) {
        $date_obj = new DateTime("@$date");
        if( strlen($date_obj->format('F')) > 5 ){
          $formatted_date = $date_obj->format('g:i a \o\n l, M\. d');
        } else {
          $formatted_date = $date_obj->format('g:i a \o\n l, F d');
        }
        echo str_replace(array('Sep','12:00 am','12:00 pm','am','pm'),array('Sept','midnight','noon','a.m.','p.m.'),$formatted_date);
      }
    }

    $alert_query = new WP_Query($args);

    if ( $alert_query->have_posts() ) {

      while ( $alert_query->have_posts() ) {

        $alert_query->the_post();

        $alert_active = rwmb_meta( 'phila_alert_active');

        $alert_start = rwmb_meta( 'phila_alert_start', $args = array('type' => 'datetime'));
        $alert_end = rwmb_meta( 'phila_alert_end', $args = array('type' => 'datetime'));
        $alert_color = rwmb_meta( 'phila_alert_color');
        $alert_icon = rwmb_meta( 'phila_alert_icon');

        $date_seperator = ' <strong>to</strong> ';
        if(($alert_start == '') || ($alert_end == '')){
          $date_seperator = ' ';
        }
        $now = current_time('timestamp');

        if( $alert_active ) { ?>
          <div class="site-wide-alert <?php echo $alert_color?>" data-alert="alert-<?php echo $alert_start ?>-<?php echo $alert_end; ?>-<?php echo get_the_ID(); ?>" data-swiftype-index="false">
            <div class="row">
              <div class="medium-centered">
                <div class="grid-x grid-padding-x align-top pvs">
                  <div class="cell auto shrink center icon hide-for-small-only align-self-middle">
                      <i class="<?php echo ($alert_icon) ? $alert_icon :  'fas fa-exclamation' ?> fa-fw fa-2x" aria-hidden="true"></i>
                  </div>
                  <?php if ($alert_color == 'red') :?>
                    <div class="cell medium-auto medium-shrink small-24 align-self-middle">
                      <h2><?php $title = get_the_title(); echo $title;?> </h2>
                    </div>
                  <?php endif; ?>
                  <div class="cell auto message align-self-middle">
                  <?php
                    $content = get_the_content();
                    echo $content;
                  ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
<<<<<<< HEAD
          <?php include( locate_template( 'partials/global/site-wide-banner.php' ) ); ?>
=======
          <?php $voting_banner_feature_flag = rwmb_meta( 'display_site_wide_banner', array( 'object_type' => 'setting' ), 'phila_settings' );
            if(isset($voting_banner_feature_flag) && $voting_banner_feature_flag != 0) {
          ?>
          <div class="voting-banner">
            <div class="row">
              <div class="medium centered">
                <div class="grid-x grid-padding-x align-top pvs align-justify">
                  <div class="cell medium-auto medium-shrink small-24 align-self-middle">
                    <i class="fas fa-check-to-slot fa-fw fa-2x icon hide-for-small-only" aria-hidden="true"></i>
                  </div>
                  <div class="cell auto message align-self-middle">
                    <div class="voting-text">
                      <p class="mbn"><b>Election day is Nov. 8, 2022</b></p>
                      <p class="vote-deadline mbn">The deadline to register to vote is Oct. 24, 2022.</p>
                    </div>
                  </div>
                  <div class="cell medium-auto medium-shrink small-24 align-self-right">
                    <a class="vote-button button" href="https://vote.phila.gov/voting/my-vote-my-way/"><b>Make a plan to vote</b></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php } // voting banner feature flag ?>
>>>>>>> bb2323ca5 (sitewide banner fixed)
          <?php
        }else if ( ($alert_start <= $now && $alert_end == 0 ) || $alert_start <= $now && ( $alert_end >= $now ) ){ ?>
          <div class="site-wide-alert <?php echo $alert_color?>" data-alert="alert-<?php echo $alert_start ?>-<?php echo $alert_end; ?>-<?php echo get_the_ID(); ?>" data-swiftype-index="false">
          <div class="row">
            <div class="medium-centered">
              <div class="grid-x grid-padding-x align-top pvs">
                <div class="cell auto shrink center icon hide-for-small-only align-self-middle">
                  <i class="<?php echo ($alert_icon) ? $alert_icon :  'fas fa-exclamation' ?>  fa-fw fa-2x" aria-hidden="true"></i>
                </div>
              <div class="cell auto message align-self-middle">
                <?php
                  $content = get_the_content();
                  echo $content;
                  ?><div class="dates pts"><?php
                  echo 'In effect: ';
                  dateTimeFormat($alert_start);
                  if ( empty( $alert_end ) ) {
                    echo ' until further notice';
                  } else {
                    echo ' to ';
                    dateTimeFormat($alert_end);
                  }
                ?>
              </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php
        }
      }
    }
    include( locate_template( 'partials/global/site-wide-banner.php' ) );
    wp_reset_postdata();
  }//function
}//class
?>
