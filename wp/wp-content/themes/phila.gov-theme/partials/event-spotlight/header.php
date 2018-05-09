<?php
  /*
   * Event spotlight header
  */
  $hero = rwmb_meta( 'header_img', array( 'limit' => 1 ) );
  $hero = reset($hero);

  $owner = rwmb_meta( 'owner_logo', array( 'limit' => 1 ) );
  $owner = reset($owner);

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
?>
<header class="spotlight">
  <div class="grid-x">
    <img src="<?php echo $hero['full_url']  ?>" class="spotlight-header">
      <?php echo !empty($credit) ? '<div class="photo-credit">' . $credit . '</div>' : '' ?>
      <?php echo $description ?>
  </div>
  <div class="bg-ghost-gray">
    <div class="grid-container">
      <div class="grid-x">
        <div class="cell">
          <div><i class="fa fa-calendar"></i> Event</div>
          <?php $start = new DateTime("@" . $start); ?>
          <?php $end = new DateTime("@" . $end); ?>
          <?php $start_month_format = phila_util_month_format($start); ?>
          <?php $end_month_format = phila_util_month_format($end); ?>

          <?php
          //TODO: Make this its own template or function
            if (isset( $date_option ) && $date_option == 'date' ):?>
                <?php if ($start->format('m-d') === $end->format('m-d') ): ?>
                  <?php echo str_replace(array('Sep'), array('Sept'), $start->format('l, ' . $start_month_format . ' j Y')); ?>
                <?php else :?>
                  <?php echo str_replace(array('Sep'), array('Sept'), $start->format('l, ' . $start_month_format . ' j') . ' - ' . $end->format( 'l, ' . $end_month_format . ' j Y') ); ?>
                <?php endif; ?>

            <?php elseif (isset( $date_option ) && $date_option) : ?>

                  <?php if ($start->format('m-d') === $end->format('m-d') && $start->format('a') === $end->format('a')):
                    //single day with time range ?>
                    <?php echo str_replace(
                      array('Sep','am','pm',':00'),
                      array('Sept','a.m.','p.m.',''),
                     $end->format(' l, ' . $start_month_format . ', j Y') . '<br />' . $start->format( 'g:i a' ) . ' - ' . $end->format('g:i a')
                    ); ?>
                  <?php elseif ($start->format('m-d') === $end->format('m-d') && $start->format('a') !== $end->format('a')): ?>
                    <?php echo str_replace(
                        array('Sep','12:00 am','12:00 pm','am','pm',':00'),
                        array('Sept','midnight','noon','a.m.','p.m.',''), $start->format( 'g:i a' ) . ' – ' . $end->format(' l, ' . $start_month_format . ' g:i a j Y') ); ?>
                  <?php else : //date range with time ?>
                    <?php echo str_replace(
                      array('Sep','12:00 am','12:00 pm','am','pm',':00'),
                      array('Sept','midnight','noon','a.m.','p.m.',''), $start->format('l, ' . $start_month_format . ' j') . ' - ' . $end->format('l, ' . $end_month_format . ' j, Y' ) .  '<br />'. $start->format('g:i a') . ' - '  . $end->format('g:i a'));
                      ?>
                      <i class="fa fa-refresh" aria-hidden="true"></i> Recurring daily
                  <?php endif; ?>

                <?php endif; ?>

          <h1><?php echo the_title() ?></h1>
          <p><?php echo $description ?></p>
        </div>
      </div>
    </div>
  </div>
</header>
<nav>
  <ul>
    <li>
      <a href="#">Event listings</a>
    </li>
  </ul>
</nav>
<section>
  <div class="grid-container">
    <h2>Official event information</h2>
    <div class="grid-x">
      <div class="cell medium-12">
        <?php echo $event_info ?>
      </div>
      <div class="cell medium-12">

      </div>
    </div>
  </div>
</section>
<style>
.spotlight{
  border-bottom: 5px solid #000;
}
.spotlight-header{
  width:100%;
  height: 100%;
}
</style>
