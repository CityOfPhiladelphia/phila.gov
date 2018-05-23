<?php
/*
 * Event spotlight card
*/

if ( is_singular('department_page') ) {
  $spotlight_args  = array(
    'posts_per_page' => 1,
    'post_type' => array('event_spotlight'),
    'order' => 'desc',
    'orderby' => 'date',
    'ignore_sticky_posts' => 1,
    'p' => $spotlight_id
  );
}else{
  $spotlight_args  = array(
    'posts_per_page' => 1,
    'post_type' => array('event_spotlight'),
    'order' => 'desc',
    'orderby' => 'date',
    'ignore_sticky_posts' => 1,
    'meta_query'  => array(
      array(
        'key' => 'spotlight_is_active',
        'value' => '1',
        'compare' => '='
      )
    )
  );
  }
?>
<?php $label = 'event_spotlight'; ?>

<?php $spotlight = new WP_Query( $spotlight_args ); ?>
<?php if ( $spotlight->have_posts() ) : ?>
    <?php while ( $spotlight->have_posts() ) : $spotlight->the_post(); ?>
    <?php if (is_page_template('templates/the-latest.php')): ?>
      <section>
        <div id="event-spotlight" data-magellan-target="event-spotlight">
        <header class="row columns mtl">
          <h1>Event spotlight</h1>
        </header>
      <?php endif; ?>
    <?php
      $hero = rwmb_meta( 'header_img', array( 'limit' => 1 ) );
      $hero = reset($hero);

      $credit = rwmb_meta( 'phila_photo_credit' );
      $description = rwmb_meta( 'phila_meta_desc' );
      $date_option = rwmb_meta('phila_date_format');

      $event_info = rwmb_meta('event_info');

      if ($date_option == 'datetime'){
        $start = rwmb_meta('start_datetime');
        $end = rwmb_meta('end_datetime');

      }else{
        $start = rwmb_meta('start_date');
        $end = rwmb_meta('end_date');
      }

      $address = rwmb_meta( 'address', array( 'limit' => 1 ) );

    ?>
<header id="spotlight-header" class="spotlight">
  <div class="grid-x">
    <img src="<?php echo $hero['full_url']  ?>" class="spotlight-image" alt="<?php echo $hero['alt'] ?>">
    <?php if ( !empty($credit) ): ?>
      <div class="photo-credit small-text">
        <span><i class="fa fa-camera" aria-hidden="true"></i> Photo by <?php echo !empty($credit) ? '<div class="photo-credit">' . $credit . '</div>' : '' ?></span>
      </div>
    <?php endif; ?>
  </div>
  <a class="bg-ghost-gray card card--calendar pvm" href="<?php echo get_the_permalink() ?>">
    <div class="grid-container">
      <div class="grid-x">
        <div class="cell">
          <div class="post-label post-label--calendar"><i class="fa fa-calendar"></i><span>Event</span></div>
          <?php $start = new DateTime("@" . $start); ?>
          <?php $end = new DateTime("@" . $end); ?>
          <?php $start_month_format = phila_util_month_format($start); ?>
          <?php $end_month_format = phila_util_month_format($end); ?>
          <div class="spotlight-date">
            <?php
              if (isset( $date_option ) && $date_option == 'date' ):?>
                <?php if ($start->format('m-d') === $end->format('m-d') ): ?>
                  <?php $date_output =  str_replace(array('Sep'), array('Sept'), $start->format('l, ' . $start_month_format . ' j, Y'));
                  echo $date_output;
                  ?>
                <?php else :?>
                  <?php $date_output = str_replace(array('Sep'), array('Sept'), $start->format('l, ' . $start_month_format . ' j') . ' - ' . $end->format( 'l, ' . $end_month_format . ' j, Y') );
                  echo $date_output;
                  ?>

                <?php endif; ?>

            <?php elseif (isset( $date_option ) && $date_option) : ?>

                  <?php if ($start->format('m-d') === $end->format('m-d') && $start->format('a') === $end->format('a')):
                    //single day with time range ?>
                    <?php $date_output = str_replace(
                      array('Sep','am','pm',':00'),
                      array('Sept','a.m.','p.m.',''),
                     $end->format(' l, ' . $start_month_format . ', j, Y') . '<br />' . $start->format( 'g:i a' ) . ' - ' . $end->format('g:i a'));
                     echo $date_output; ?>
                  <?php elseif ($start->format('m-d') === $end->format('m-d') && $start->format('a') !== $end->format('a')): ?>
                    <?php $date_output = str_replace(
                        array('Sep','12:00 am','12:00 pm','am','pm',':00'),
                        array('Sept','midnight','noon','a.m.','p.m.',''), $start->format( 'g:i a' ) . ' – ' . $end->format(' l, ' . $start_month_format . ' g:i a j, Y') );
                        echo $date_output;?>
                  <?php else : //date range with time ?>
                    <?php $date_output = str_replace(
                      array('Sep','12:00 am','12:00 pm','am','pm',':00'),
                      array('Sept','midnight','noon','a.m.','p.m.',''), $start->format('l, ' . $start_month_format . ' j') . ' - ' . $end->format('l, ' . $end_month_format . ' j, Y' ) .  '<br />'. $start->format('g:i a') . ' - '  . $end->format('g:i a'));
                      echo $date_output;
                      ?>
                      <i class="fa fa-refresh" aria-hidden="true"></i> Recurring daily
                  <?php endif; ?>

                <?php endif; ?>
            </div>
          <h2 class="h1"><?php echo the_title() ?></h2>
          <div><?php echo $description ?></div>
        </div>
      </div>
    </div>
  </a>
</header>
<?php if (is_page_template('templates/the-latest.php')): ?>
  </div>
  </section>
<?php endif;?>
<?php endwhile; ?>
<?php endif ?>
