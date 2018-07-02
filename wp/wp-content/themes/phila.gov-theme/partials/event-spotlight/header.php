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
<?php $page_rows = rwmb_meta('spotlight_row'); ?>
<?php $c = 0; ?>
<div data-sticky-container class="bg-white">
  <nav class="sticky sticky--in-page center bg-white menu" data-sticky data-top-anchor="spotlight-header:bottom" style="width:100%" data-sticky-on="medium" data-margin-top="4.8">
    <ul class="inline-list man pam" data-magellan data-options="offset: 106;">
    <?php foreach ($page_rows as $key => $value): ?>
      <?php $c++; ?>
      <?php $current_row = $page_rows[$key]; ?>
        <?php if ( $current_row['spotlight_options'] == 'free_text'): ?>
          <?php $custom_text = $current_row['free_text_option']; ?>
          <li class="event medium-auto">
            <a href="#anchor-<?php echo $c ?>">
              <?php echo $custom_text['phila_custom_wysiwyg']['phila_wysiwyg_title'] ?></a>
            </li>
            <?php elseif ( $current_row['spotlight_options'] == 'registration'): ?>
            <?php $registration = $current_row['phila_registration']; ?>
            <li class="event medium-auto">
              <a href="#anchor-<?php echo $c ?>"><?php echo $registration['title'] ?></a>
            </li>
          <?php elseif ( $current_row['spotlight_options'] == 'call_to_action_multi'): ?>
            <?php $phila_dept_homepage_cta =
            $current_row['call_to_action_multi_row']['phila_call_to_action_section']; ?>
            <li class="event medium-auto">
              <a href="#anchor-<?php echo $c ?>"><?php echo $phila_dept_homepage_cta['phila_action_section_title_multi']?></a>
            </li>
            <?php elseif ( $current_row['spotlight_options'] == 'calendar'): ?>
              <li class="event medium-auto">
                <a href="#anchor-<?php echo $c ?>">Event listings</a>
              </li>
            <?php elseif ( $current_row['spotlight_options'] == 'accordion'): ?>
            <?php $accordion_title =        $current_row['accordion_row']['accordion_row_title']; ?>
              <li class="event medium-auto">
                <a href="#anchor-<?php echo $c ?>"><?php echo $accordion_title ?></a>
              </li>
            <?php elseif ( $current_row['spotlight_options'] == 'image_list'): ?>
            <?php $title = $current_row['phila_image_list']['title']; ?>
              <li class="event medium-auto">
                <a href="#anchor-<?php echo $c ?>"><?php echo $title ?></a>
              </li>
            <?php elseif ( $current_row['spotlight_options'] == 'featured_events'): ?>
            <?php $title = $current_row['featured_events']['title']; ?>
              <li class="event medium-auto">
                <a href="#anchor-<?php echo $c ?>"><?php echo $title ?></a>
              </li>
            <?php elseif ( $current_row['spotlight_options'] == 'posts'): ?>
              <li class="event medium-auto">
                <a href="#anchor-<?php echo $c ?>">Posts</a>
              </li>
            <?php else: ?>
          <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  </nav>
</div>
<section class="mvxl">
  <div class="grid-container">
    <h2 id="official-event-information">Official event information</h2>
    <div class="grid-x">
      <div class="cell medium-12">
        <h3>When</h3>
        <?php echo isset($date_output) ? $date_output : ''?>
        <?php if ( isset($address['address'] )): ?>
          <h3 class="mtl">Where</h3>
            <b><?php echo $address['venue_name'] ?></b><br />
            <address>
              <?php echo isset($address['address'])? $address['address'] : '' ?><br />
              <?php echo isset($address['address_2'])? $address['address_2'] : '' ?>
              <?php echo isset($address['city'])? $address['city'] : '' ?>,
              <?php echo isset($address['state'])? $address['state'] : '' ?> <?php echo isset($address['zip'])? $address['zip'] : '' ?>
            </address>
        <?php endif ?>
      </div>
      <div class="cell medium-12">
        <?php echo apply_filters('the_content', $event_info); ?>
      </div>
    </div>
  </div>
</section>
