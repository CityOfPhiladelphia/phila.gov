<?php
/*
 *
 * Partial for timeline page
 */
?>

<?php 
$timeline_page = !isset($timeline_page) ? rwmb_meta('phila_select_timeline') : $timeline_page['phila_select_timeline'];
 $limit = $limit ? $limit : 5;

  if ( $timeline_page != null ) {
    $timeline_permalink = get_permalink($timeline_page);
    $timeline_title = rwmb_meta( 'timeline-title' , '', $timeline_page );
    $timeline_items = rwmb_meta( 'timeline-items' , '', $timeline_page );
    $timeline_toggle = rwmb_meta( 'timeline-month-year-toggle' , '', $timeline_page );
  }
  else {
    $timeline_items = rwmb_meta( 'timeline-items' ) !== null ? rwmb_meta( 'timeline-items' ) : [];
    $timeline_toggle = rwmb_meta( 'timeline-month-year-toggle' );
    $limit = -1;
  }
  ?>

<?php $temp_month = ''; ?>
<?php 
  $date_type = $timeline_toggle == 'year' ? 'Y' : 'F Y';
  $month_list = array(); 
  if ($timeline_toggle == 'day-month-year' ) {
    usort($timeline_items, function($a, $b) {
      return strtotime(DateTime::createFromFormat('m-d-Y', $b['phila_timeline_item_timestamp'])->format('Y-m-d H:i:s')) - strtotime(DateTime::createFromFormat('m-d-Y', $a['phila_timeline_item_timestamp'])->format('Y-m-d H:i:s'));
    });
  }
?>

<!-- Timeline Section -->
<section>
  <div class="grid-container <?php echo ( $timeline_page != null ) ? 'mtl' : ''; ?>">
    <div class="grid-x">
      <div class="cell">
        <?php if (isset($timeline_title)) { ?>
          <h2 id="<?php echo sanitize_title_with_dashes( $timeline_title); ?>" class="contrast"><?php echo $timeline_title; ?></h2>
        <?php } ?>
        <?php 
          $h = 0;
          foreach($timeline_items as $item) {
            array_push($month_list, DateTime::createFromFormat('m-d-Y', $item['phila_timeline_item_timestamp'])->format($date_type) );
            $h++;
            if ($h == $limit) {
              break;
            }
          }
          $month_list = array_unique ($month_list);
        ?>
        <?php if ( $timeline_page == null ) { ?>
          <!-- Menu -->
          <div class='menu'>
            <?php 
              $numItems = count($month_list);
              $i = 0;
            ?>
            <?php foreach($month_list as $month) { ?>
              <span class="month-link"><a href="#<?php echo sanitize_title_with_dashes($month);?>"><?php echo $month; ?></a></span>
              <?php echo (++$i !== $numItems) ? '<span class="pipe"> |</>' : null; ?>
            <?php } ?>
          </div>
        <?php } ?>

        <!-- Timeline -->
        <div class="timeline">
          <?php $j = 0; ?>
          <?php foreach($timeline_items as $item) { ?>
            <div class="timeline-item row">
              <?php $item_date = $item['phila_timeline_item_timestamp']; ?>
              <?php if( DateTime::createFromFormat('m-d-Y', $item['phila_timeline_item_timestamp'])->format($date_type) != $temp_month ) { ?>
                <?php $temp_month = DateTime::createFromFormat('m-d-Y', $item_date)->format($date_type); ?>
                <div class="month-label medium-6 columns" id="<?php echo sanitize_title_with_dashes( $temp_month);?>">
                  <div>
                    <span ><?php echo $temp_month; ?></span>
                  </div>
                </div>
              <?php } ?>
              <div class="timeline-details medium-18 columns timeline-right">
                <div class="timeline-dot-container <?php echo ($j == 0) ? 'first-dot' : '' ?>">
                  <div class="timeline-dot"></div>
                </div>
                <div class="timeline-text">
                  <?php if ($timeline_toggle == 'day-month-year') { ?>
                    <div class="timeline-month"><?php echo DateTime::createFromFormat('m-d-Y', $item_date)->format('F d');?></div>
                  <?php } ?>
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
          <a href="<?php echo $timeline_permalink; ?>" class="button alignright mts">See full timeline</a>
        <?php } ?>
      </div>
    </div>
  </div>
</section>
<!-- Timeline Section/ -->