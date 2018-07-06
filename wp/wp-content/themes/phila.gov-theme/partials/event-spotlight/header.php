<?php
  /*
   * Event spotlight page header
  */

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
  <div class="bg-ghost-gray card card--calendar pvm">
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

                  <?php if ($start->format('m-d') === $end->format('m-d') && $start->format('a') === $end->format('a')): ?>
                    <?php $date_output = str_replace(
                      array('Sep','am','pm',':00'),
                      array('Sept','a.m.','p.m.',''),
                       $end->format(' l, ' . $start_month_format . ' j, Y') . '<br />' . $start->format( 'g:i a' ) . ' - ' . $end->format('g:i a')
                   );
                     echo $date_output; ?>
                  <?php elseif ($start->format('m-d') === $end->format('m-d') && $start->format('a') !== $end->format('a')): ?>
                     <?php $date_output = str_replace(
                       array('Sep','am','pm',':00'),
                       array('Sept','a.m.','p.m.',''),
                        $end->format(' l, ' . $start_month_format . ' j, Y') . '<br />' . $start->format( 'g:i a' ) . ' - ' . $end->format('g:i a')
                    );
                      echo $date_output; ?>
                  <?php else : ?>
                    <?php $date_output = str_replace(
                      array('Sep','12:00 am','12:00 pm','am','pm',':00'),
                      array('Sept','midnight','noon','a.m.','p.m.',''), $start->format('l, ' . $start_month_format . ' j') . ' - ' . $end->format('l, ' . $end_month_format . ' j, Y' ) .  '<br />'. $start->format('g:i a') . ' - '  . $end->format('g:i a'));
                      echo $date_output;
                      ?>
                      <i class="fa fa-refresh" aria-hidden="true"></i> Recurring daily
                  <?php endif; ?>

                <?php endif; ?>
            </div>
          <h1><?php echo the_title() ?></h1>
          <div><?php echo apply_filters('the_content', $description); ?></div>
        </div>
      </div>
    </div>
  </div>
</header>
