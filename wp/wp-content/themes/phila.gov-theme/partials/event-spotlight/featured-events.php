<?php
  /* Featured event cards
  $featured_events - array() - required.
  */

  $title = $featured_events['title'];

?>
<div class="grid-container">
  <h2><?php echo $title ?></h2>
</div>
<div class="grid-container featured-events">
  <div class="grid-x grid-margin-x align-stretch">

  <?php foreach($featured_events['features'] as $feature) :?>
    <div class="cell medium-8 bdr-all feature pam">
      <h3><?php echo $feature['phila_custom_wysiwyg']['phila_wysiwyg_title'] ?></h3>
      <p>
        <?php echo $feature['phila_custom_wysiwyg']['phila_wysiwyg_content'] ?>
      </p>
      <div class="mvm">
        <i class="fa fa-map-marker"></i>
        <div class="float-left">
          <address>
            <?php echo $feature['venue_name'] ?>
          </address>
        </div>
      </div>
      <div class="mvm">
        <i class="fa fa-calendar"></i>
        <?php $start = new DateTime("@" . $feature['start_datetime']['timestamp']); ?>
        <?php $end = new DateTime("@" .  $feature['end_datetime']['timestamp']); ?>
        <?php $start_month_format = phila_util_month_format($start); ?>
        <?php $end_month_format = phila_util_month_format($end); ?>
        <?php if ($start->format('m-d') === $end->format('m-d') && $start->format('a') === $end->format('a')):
          //single day with time range ?>
          <?php $date_output = str_replace(
            array('Sep','am','pm',':00'),
            array('Sept','a.m.','p.m.',''),
           $end->format(' l, ' . $start_month_format . ' j, Y') . '<br />' . $start->format( 'g:i a' ) . ' - ' . $end->format('g:i a'));
           echo $date_output; ?>
        <?php elseif ($start ->format('m-d') === $end->format('m-d') && $start->format('a') !== $end->format('a')): ?>
          <?php $date_output = str_replace(
              array('Sep','12:00 am','12:00 pm','am','pm',':00'),
              array('Sept','midnight','noon','a.m.','p.m.', ''), $start->format(' l, ' . $start_month_format .  ' j, Y') .'<br />' . $start->format( 'g:i a' ) . ' to ' . $end->format(' g:i a') );
              echo $date_output;?>
        <?php else : //date range ?>
          <?php $date_output = str_replace(
            array('Sep','12:00 am','12:00 pm','am','pm',':00'),
            array('Sept','midnight','noon','a.m.','p.m.',''), $start->format('l, ' . $start_month_format . ' j') . ' to ' . $end->format('l, ' . $end_month_format . ' j, Y' ) );
            echo $date_output;
            ?>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach?>
  </div>

</div>
