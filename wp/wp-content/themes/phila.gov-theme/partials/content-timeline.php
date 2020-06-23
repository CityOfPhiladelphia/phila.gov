<?php
/*
 *
 * Partial for timeline page
 */
?>

<?php $timeline_title = rwmb_meta( 'timeline-title' ) !== null ? rwmb_meta( 'timeline-title' ) : ''; ?>
<?php $timeline_items = rwmb_meta( 'timeline-items' ) !== null ? rwmb_meta( 'timeline-items' ) : []; ?>
<?php $temp_month = ''; ?>
<?php 
  $month_list = array(); 
  usort($timeline_items, function($a, $b) {
    return strtotime(date($b['phila_timeline_item_timestamp']['timestamp'])) - strtotime(date($a['phila_timeline_item_timestamp']['timestamp']));
  });
?>

<!-- Timeline Section -->
<section>
  <div class="grid-container">
    <div class="grid-x">
      <div class="cell">
        <h2 class="contrast"><?php echo $timeline_title; ?></h2>
        <?php 
          foreach($timeline_items as $item) {
            array_push($month_list, date('F Y', $item['phila_timeline_item_timestamp']['timestamp']));
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
            <?php echo (++$i !== $numItems) ? '<span> |</span>' : null; ?>
          <?php } ?>
        </div>
        
        <!-- Timeline -->
        <div class="timeline">
          <?php $first = true; ?>
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
                <div class="timeline-dot-container <?php echo ($first) ? 'first-dot' : '' ?>">
                  <div class="timeline-dot"></div>
                </div>
                <div class="timeline-text">
                  <div class="timeline-month"><?php echo date('F d', $item_date);?></div>
                  <div class="timeline-copy"><?php echo do_shortcode(wpautop( $item['phila_timeline_item'] )); ?></div>
                </div>
              </div>
            </div>
            <?php $first = false; ?>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Timeline Section/ -->