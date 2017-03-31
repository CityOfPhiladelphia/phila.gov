<?php
if ( class_exists( "Phila_Gov_Site_Wide_Alert_Rendering" ) ){
  $phila_site_wide_alert = new Phila_Gov_Site_Wide_Alert_Rendering();
}

class Phila_Gov_Site_Wide_Alert_Rendering {

  /**
  *
  * Display alert if display is true, also show on preview
  *
  */
  static function create_site_wide_alerts(){

    $args = array( 'post_type' => 'site_wide_alert' );

    $pages = get_posts( $args );

    $args = array(
      'post_type' => array ('site_wide_alert'),
      'posts_per_page'    => 1
    );

    function dateTimeFormat($date){
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

        $alert_active = rwmb_meta( 'phila_active', $args = array('type' => 'radio'));

        $alert_start = rwmb_meta( 'phila_alert_start', $args = array('type' => 'datetime'));
        $alert_end = rwmb_meta( 'phila_alert_end', $args = array('type' => 'datetime'));

        $date_seperator = ' <strong>to</strong> ';
        if(($alert_start == '') || ($alert_end == '')){
          $date_seperator = ' ';
        }

        $now = current_time('timestamp');

        if ( ( $alert_start <= $now && $alert_end >= $now ) || ( is_preview() && is_singular( 'site_wide_alert' ) ) ) :

        ?><div id="site-wide-alert" data-swiftype-index="false">
            <div class="row">
              <div class="medium-18 medium-centered">
                <div class="row equal-height pvs">
                  <div class="small-1 columns center equal icon hide-for-small-only">
                    <div class="valign">
                      <div class="valign-cell">
                        <i class="fa fa-exclamation fa-3x" aria-hidden="true"></i>
                      </div>
                    </div>
                  </div>
              <div class="small-24 medium-23 columns equal message">

        <?php echo '<strong>' . get_the_title() . '</strong>';

        $content = get_the_content();
        echo $content;
        ?><div class="dates pts"><?php
        echo 'In effect: ';
        dateTimeFormat($alert_start);
        echo ' to ';
        dateTimeFormat($alert_end);
        ?>
      </div></div></div></div></div></div>
      <?php endif;
      }//end while
    }

    wp_reset_postdata();

  }
}
