<?php
  /* Featured event cards
  $featured_events - array() - required.
  */

  $title = $featured_events['title'];
  $grid = phila_grid_column_counter(count($featured_events['features']));
?>
<section class="mvxl">
  <?php if (isset($title) ) : ?>
  <div class="grid-container">
    <h2><?php echo $title ?></h2>
  </div>
  <?php endif; ?>
  <div class="grid-container featured-events">
    <div class="grid-x grid-margin-x align-stretch">

    <?php foreach($featured_events['features'] as $feature) : ?>
      <div class="cell medium-<?php echo $grid ?> bdr-all feature pam grid-x">
        <h3 class="cell align-self-top"><?php echo $feature['phila_custom_wysiwyg']['phila_wysiwyg_title'] ?></h3>
        <p class="feature-description cell align-self-top">
          <?php echo $feature['phila_custom_wysiwyg']['phila_wysiwyg_content'] ?>
        </p>
        <div class="align-self-bottom feature-detail">
          <?php if (isset($feature['venue_name']) || isset($feature['address_1'])) :?>
          <div class="mvm grid-x align-top cell full-width">
            <i class="fa fa-map-marker fa-fw fa-lg inline-block mrm cell small-1"></i>
            <div class="cell small-21">
                <?php echo isset($feature['venue_name']) ? '<b>' . $feature['venue_name'] . '</b> <br />' : ''; ?>
                <address>
                <?php echo isset($feature['address']) ? $feature['address'] . '<br />' : '' ?>
                <?php echo isset($feature['address_2'] ) ? $feature['address_2'] . '<br />': '' ?>
                <?php echo isset( $feature['city'] ) ? $feature['city'] . ', ': '' ?><?php echo isset( $feature['state']) ? $feature['state'] : ''; ?> <?php echo isset($feature['zip']) ? $feature['zip'] : '' ?>
              </address>
            </div>
          </div>
          <?php endif; ?>
          <div class="mvm grid-x align-top full-width">
            <i class="fa fa-calendar fa-fw fa-lg inline-block mrm cell small-1"></i>
            <div class="cell small-21">
              <?php $start = new DateTime("@" . $feature['start_datetime']['timestamp']); ?>
              <?php $end = new DateTime("@" .  $feature['end_datetime']['timestamp']); ?>
              <?php $start_month_format = phila_util_month_format($start); ?>
              <?php $end_month_format = phila_util_month_format($end); ?>

              <?php if ($start->format('m-d') === $end->format('m-d') && $start->format('g:i') === $end->format('g:i')): // single all day ?>

                 <?php $date_output = str_replace(
                   array('Sep','am','pm',':00'),
                   array('Sept','a.m.','p.m.',''),
                  $start->format('<b>' . 'l, ' . $start_month_format . ' j, Y') . '</b>'. '<br /><i>All day event</i>');
                  echo $date_output; ?>
               <?php elseif ($start->format('m-d') === $end->format('m-d') && $start->format('a') === $end->format('a')): //single with time range ?>

                 <?php $date_output = str_replace(
                   array('Sep','am','pm',':00'),
                   array('Sept','a.m.','p.m.',''), $start->format('<b>' . 'l, ' . $start_month_format . ' j, Y') . '</b>'. '<br /><i>' . $start->format( 'g:i a' ) . ' - ' . $end->format('g:i a') . '</i>');
                  echo $date_output; ?>

              <?php elseif ($start ->format('m-d') === $end->format('m-d') && $start->format('a') !== $end->format('a')): ?>
                <?php $date_output = str_replace(
                    array('Sep','12:00 am','12:00 pm','am','pm',':00'),
                    array('Sept','midnight','noon','a.m.','p.m.', ''), $start->format('<b>'.' l, ' . $start_month_format .  ' j, Y'. '</b>') .'<br /><i>' . $start->format( 'g:i a' ) . ' to ' . $end->format(' g:i a') . '</i>' );
                    echo $date_output;?>
              <?php else : //date range ?>
                <?php $date_output = str_replace(
                  array('Sep','12:00 am','12:00 pm','am','pm',':00'),
                  array('Sept','midnight','noon','a.m.','p.m.',''), $start->format('<b>' . $start_month_format . ' j') . ' to ' . $end->format( $end_month_format . ' j, Y' . '</b>') );
                  echo $date_output;
                  ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach?>
    </div>
  </div>
</section>
