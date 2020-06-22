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
            <span><?php echo $month; ?></span>
            <?php echo (++$i !== $numItems) ? '<span> |</span>' : null; ?>
          <?php } ?>
        </div>
        
        <!-- Timeline -->
        <div class="timeline">
          <?php foreach($timeline_items as $item) { ?>
            <div class="timeline-item">
              <?php $item_date = $item['phila_timeline_item_timestamp']['timestamp']; ?>
              <?php if( date('F Y', $item_date) != $temp_month ) { ?>
                <?php $temp_month = date('F Y', $item_date); ?>
                <div class="month-label">
                  <span><?php echo $temp_month; ?></span>
                </div>
              <?php } ?>
              <div><?php echo date('F d', $item_date);?></div>
              <div><?php echo $item['phila_timeline_item'];?></div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Timeline Section/ -->