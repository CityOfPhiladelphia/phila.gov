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
          $formatted_date = $date_obj->format('g:i a \o\n l, M\. d, Y');
        } else {
          $formatted_date = $date_obj->format('g:i a \o\n l, F d, Y');
        }
        echo str_replace(array('am','pm'),array('a.m.','p.m.'),$formatted_date);
      }
    }

    $alert_query = new WP_Query($args);
    if ( $alert_query->have_posts() ) {

      while ( $alert_query->have_posts() ) {

        $alert_query->the_post();

        $alert_active = rwmb_meta( 'phila_active', $args = array('type' => 'radio'));

        $alert_type = rwmb_meta( 'phila_type', $args = array('type' => 'select'));
        $alert_start = rwmb_meta( 'phila_alert_start', $args = array('type' => 'datetime'));
        $alert_end = rwmb_meta( 'phila_alert_end', $args = array('type' => 'datetime'));

        $alert_icon = 'ion-alert-circled';

        switch($alert_type){
          case 'Code Blue Effective':
            $alert_icon = 'ion-ios-snowy';
            break;
          case 'Code Red Effective':
            $alert_icon = 'ion-ios-sunny';
            break;
          case 'Code Orange Effective':
            $alert_icon = 'ion-cloud';
            break;
          case 'Code Grey Effective':
            $alert_icon = 'ion-ios-rainy';
            break;
          case 'Other':
            $alert_type_other = rwmb_meta( 'phila_type-other', $args = array('type' => 'text'));
            $alert_class = rwmb_meta( 'phila_alert-class', $args = array('type' => 'text'));
            $alert_icon = rwmb_meta( 'phila_icon', $args = array('type' => 'text'));
            ($alert_icon == '') ? $alert_icon = 'ion-alert-circled' : $alert_icon;
            break;
        }

        $date_seperator = ' <strong>to</strong> ';
        if(($alert_start == '') || ($alert_end == '')){
          $date_seperator = ' ';
        }

        $now = current_time('timestamp');

        if ( ( $alert_start <= $now && $alert_end >= $now ) || ( is_preview() && is_singular( 'site_wide_alert' ) ) ) :

        ?><div id="site-wide-alert" <?php if ( $alert_type == 'Other' && $alert_class ) echo 'class="subtle"'; ?> data-swiftype-index='false'>
            <div class="row"><?php
        echo '<div class="large-9 columns">';
        echo '<h2><i class="ionicons ' . $alert_icon . '"></i>' . get_the_title() .'</h2>';

        echo '<div class="alert-start">';
        echo '<strong>Begins:</strong> ';
        dateTimeFormat($alert_start);
        echo '<br>';
        echo '<strong>Ends:</strong> ';
        dateTimeFormat($alert_end);
        echo '</div>';
        echo '</div>';
        echo '<div class="large-15 columns">';

        if ($alert_type != 'Other'){
          echo '<strong>'.$alert_type . ': </strong>';
        } elseif ( $alert_type == 'Other' && isset( $alert_type_other ) && $alert_type_other != '' ){
          echo '<strong>' . $alert_type_other . ': </strong>';
        }

        $content = get_the_content();
        echo $content;
        echo '</div></div></div>';
      endif;
      }//end while
    }

    wp_reset_postdata();

  }
}
