<?php
/**
 * The content of a single Press Release
 * @package phila-gov
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="row">
    <header class="entry-header small-24 columns">
      <?php the_title( '<h1 class="entry-title contrast">', '</h1>' ); ?>
    </header><!-- .entry-header -->
  </div>
  <div class="row">
    <div data-swiftype-index='true' class="entry-content columns">
    <?php
      if ( function_exists('rwmb_meta') ) :

        $press_date = rwmb_meta( 'phila_press_release_date', $args = array('type' => 'date'));

        $press_name = rwmb_meta( 'phila_press_release_contact_name', $args = array('type' => 'text'));

        $press_phone = rwmb_meta( 'phila_press_release_contact_phone', $args = array('type' => 'text'));

        $press_email = rwmb_meta( 'phila_press_release_contact_email', $args = array('type' => 'text'));

        printf( _e( '<strong>For Immediate Release: </strong>' . $press_date, 'phila-gov' ) );

        ?><br><?php

        _e('<strong>Published By:</strong> '); phila_echo_current_department_name();

        ?><br><?php

        if ( $press_name != '' ) :

          printf( _e( '<strong>Contact: </strong>', 'phila-gov' ) );

          $phone_exists = ( $press_phone == '' ) ? '' : $press_phone . ', ';

          $contact = $press_name . ', '. $phone_exists . '<a href="mailto:' . $press_email . '">' . $press_email . '</a>' ;

          echo $contact;

        endif;

      endif;
      ?>
      <div class="mtm">
        <?php the_content(); ?>

        <!--end press release--->
        <div class="center">###</div>
      </div>
    </div><!-- .entry-content -->
  </div><!-- .row -->
</article><!-- #post-## -->
