<?php
/*
 *
 * Partial for timeline page
 */
?>

<?php $timeline_title = rwmb_meta( 'timeline-title' ) !== null ? rwmb_meta( 'timeline-title' ) : ''; ?>
<?php $timeline_items = rwmb_meta( 'timeline-items' ) !== null ? rwmb_meta( 'timeline-items' ) : []; ?>


  <div class="grid-x mvl">
    <div class="cell">
      <section>
        <h3><?php echo $timeline_title; ?></h3>
        <?php foreach($timeline_items as $item) { ?>
          <div><?php echo $item['phila_timeline_item_title'];?></div>
          <div><?php echo $item['phila_timeline_item_body'];?></div>
          <div><?php echo $item['phila_timeline_item_timestamp']['formatted'];?></div>

        <?php } ?>
      </section>
    </div>
  </div>