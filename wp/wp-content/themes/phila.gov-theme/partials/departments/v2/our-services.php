<?php
/**
 * Display curated service lists on department homepages
 *
 * @package phila-gov
 */
?>

<?php include( locate_template( 'partials/content-service-updates.php' ) ); ?>

<?php
  $services_list = rwmb_meta( 'phila_v2_homepage_services' );
  $services = phila_loop_clonable_metabox( $services_list );
?>
<?php if ( !empty( $services ) ) :?>
<div class="row mtl">
  <div class="columns">
    <h2>Services</h2>
    <div class="row" data-equalizer>
      <div class="columns small-collapse">
        <div class="row inside-border-group" data-equalizer>
          <?php $item_count = count($services); ?>
          <?php $columns = phila_grid_column_counter( $item_count ); ?>
          <?php
          if ( ($item_count % 3 == 0) || $item_count == '5'  ) :
            $columns = "8";
          elseif( $item_count == 4) :
            $columns = "12";
          endif;
          ?>
          <?php foreach ( $services as $service ) : ?>
            <?php if ($item_count == '1') :
              $short_desc = rwmb_meta('phila_meta_desc', $args = null, $service['phila_v2_service_page']);
            endif;
            ?>
            <?php $alt_title = isset( $service['alt_title'] ) ? $service['alt_title'] : ''; ?>
            <div class="inside-border-group-item medium-<?php echo $columns ?> <?php echo ($item_count == '1') ? 'small-24' : 'small-12';?> columns end">
              <a href="<?php echo get_permalink( $service['phila_v2_service_page'] ) ?>" class="valign">
                <div class="valign-cell pal phl-l" data-equalizer-watch>
                  <div><i class="fa <?php echo $service['phila_v2_icon'] ?> fa-2x" aria-hidden="true"></i></div>
                  <div class="<?php echo isset($short_desc) ? 'prl' : ''?>">
                  <?php if( $alt_title == '' ) : ?>
                    <?php echo get_the_title( $service['phila_v2_service_page'] ) ?>
                  <?php else: ?>
                    <?php echo $service['alt_title'] ?>
                  <?php endif; ?>
                </div>
                  <?php if ( isset($short_desc) ) : ?>
                    <div class="short-desc pll">
                      <?php echo $short_desc ?>
                    </div>
                  <?php endif; ?>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
      </div>
    </div>
    </div>
  </div>
</div>
<?php $all_services = rwmb_meta( 'phila_v2_service_link' ) ?>
<?php if ( $all_services != '' ) :?>

  <div class="row mtm">
    <div class="columns">
      <?php $see_all = array(
          'URL' => $all_services,
          'content_type' => 'services',
          'nice_name' => 'Services'
        );?>
      <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
    </div>
  </div>
<?php endif; ?>
<?php endif; ?>
