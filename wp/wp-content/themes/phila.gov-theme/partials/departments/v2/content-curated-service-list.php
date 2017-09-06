<?php
/**
 * Display curated service lists on department homepages
 *
 * @package phila-gov
 */
?>

<?php
  $services_list = rwmb_meta( 'phila_v2_homepage_services' );
  $services = phila_loop_clonable_metabox( $services_list );
?>
<?php if ( !empty( $services ) ) :?>
<div class="row mtl">
  <div class="columns">
    <h2>Services</h2>
    <div class="row inside-border-group" data-equalizer>
      <?php $item_count = count($services); ?>
      <?php $columns = phila_grid_column_counter( $item_count ); ?>
      <?php
      if ( $item_count % 3 == 0) :
        $columns = "8";
      elseif( $item_count == 4) :
        $columns = "12";
      endif;
      ?>
      <?php foreach ( $services as $service ) : ?>
        <?php $alt_title = isset( $service['alt_title'] ) ? $service['alt_title'] : ''; ?>
        <div class="inside-border-group-item medium-<?php echo $columns ?> small-12 columns end">
          <a href="<?php echo get_permalink( $service['phila_v2_service_page'] ) ?>" class="valign">
            <div class="valign-cell pal phl-l" data-equalizer-watch>
              <div><i class="fa <?php echo $service['phila_v2_icon'] ?> fa-2x" aria-hidden="true"></i></div>
              <?php if( $alt_title == '' ) : ?>
                <div><?php echo get_the_title( $service['phila_v2_service_page'] ) ?> </div>
              <?php else: ?>
                <div><?php echo $service['alt_title'] ?> </div>
              <?php endif; ?>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php $all_services = rwmb_meta( 'phila_v2_service_link' ) ?>
<?php if ( $all_services != '' ) :?>

  <?php $see_all_URL = $all_services ?>
  <?php $see_all_content_type = 'Services';?>
  <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>

<?php endif; ?>
<?php endif; ?>
