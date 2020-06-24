<?php
/*
 *
 * Partial for timeline page
 */
?>

<?php 
$timeline_page =  rwmb_meta('phila_select_timeline') ? rwmb_meta('phila_select_timeline') : null;

  if ( $timeline_page != null ) {
    $timeline_permalink = get_permalink($timeline_page[0]);
    $limit = rwmb_meta('homepage_timeline_item_count') !== '' ? rwmb_meta('homepage_timeline_item_count') : 5;
    $timeline_title = rwmb_meta( 'timeline-title' , '', $timeline_page[0] );
    $timeline_items = rwmb_meta( 'timeline-items' , '', $timeline_page[0] );
  }
  else {
    $timeline_items = rwmb_meta( 'timeline-items' ) !== null ? rwmb_meta( 'timeline-items' ) : [];
    $limit = -1;
  }
?>

<?php $temp_month = ''; ?>
<?php 
  $month_list = array(); 
  usort($timeline_items, function($a, $b) {
    return date($b['phila_timeline_item_timestamp']['timestamp']) - date($a['phila_timeline_item_timestamp']['timestamp']);
  });
?>

<!-- Timeline Section -->
<section>
  <div class="grid-container">
    <div class="grid-x">
      <div class="cell">
        <?php if (isset($timeline_title)) { ?>
          <h2 class="contrast"><?php echo $timeline_title; ?></h2>
        <?php } ?>
        <?php 
          $h = 0;
          foreach($timeline_items as $item) {
            array_push($month_list, date('F Y', $item['phila_timeline_item_timestamp']['timestamp']));
            $h++;
            if ($h == $limit) {
              break;
            }
          }
          $month_list = array_unique ($month_list);
        ?>

        <!-- Menu -->
        <div class='menu'>
          <?php 
            $numItems = count($month_list);
            $i = 0;
          ?>
          <?php foreach($month_list as $month) { ?>
            <span class="month-link"><a href="#<?php echo strtolower(str_replace(' ', '-', $month));?>"><?php echo $month; ?></a></span>
            <?php echo (++$i !== $numItems) ? '<span class="pipe"> |</span>' : null; ?>
          <?php } ?>
        </div>
        
        <!-- Timeline -->
        <div class="timeline">
          <?php $j = 0; ?>
          <?php foreach($timeline_items as $item) { ?>
            <div class="timeline-item row">
              <?php $item_date = $item['phila_timeline_item_timestamp']['timestamp']; ?>
              <?php if( date('F Y', $item_date) != $temp_month ) { ?>
                <?php $temp_month = date('F Y', $item_date); ?>
                <div class="month-label medium-5 columns" id="<?php echo strtolower(str_replace(' ', '-', $temp_month));?>">
                  <div>
                    <span ><?php echo $temp_month; ?></span>
                  </div>
                </div>
              <?php } ?>
              <div class="timeline-details medium-19 columns timeline-right">
                <div class="timeline-dot-container <?php echo ($j == 0) ? 'first-dot' : '' ?>">
                  <div class="timeline-dot"></div>
                </div>
                <div class="timeline-text">
                  <div class="timeline-month"><?php echo date('F d', $item_date);?></div>
                  <div class="timeline-copy"><?php echo do_shortcode(wpautop( $item['phila_timeline_item'] )); ?></div>
                </div>
              </div>
            </div>
            <?php $j++; ?>
            <?php 
              if ($j == $limit) {
                break;
              }
            ?>
          <?php } ?>
        </div>
        <?php if ( $timeline_page != null ) { ?>
          <a href="<?php echo $timeline_permalink; ?>" class="button alignright mts">See All items</a>
        <?php } ?>
      </div>
    </div>
  </div>
</section>
<!-- Timeline Section/ -->