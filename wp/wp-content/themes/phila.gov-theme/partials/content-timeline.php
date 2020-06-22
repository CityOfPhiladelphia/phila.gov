<?php
/*
 *
 * Partial for timeline page
 */
?>

<?php $timeline_title = rwmb_meta( 'timeline-title' ) !== null ? rwmb_meta( 'timeline-title' ) : ''; ?>
<?php $timeline_items = rwmb_meta( 'timeline-items' ) !== null ? rwmb_meta( 'timeline-items' ) : []; ?>
<?php $month_list = array(); ?>


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
          usort($month_list, function($a, $b) {
            return strtotime($b) - strtotime($a);
          }); 
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
        <?php foreach($timeline_items as $item) { ?>
          <div><?php echo $item['phila_timeline_item_timestamp']['formatted'];?></div>
          <div><?php echo $item['phila_timeline_item'];?></div>
        <?php } ?>
      </div>
    </div>
  </div>
</section>