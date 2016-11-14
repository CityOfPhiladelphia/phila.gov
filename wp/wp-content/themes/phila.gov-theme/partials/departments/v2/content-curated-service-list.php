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
  $i = 0;
?>
<div class="row">
  <div class="columns content-list inverse">
    <h2>Services</h2>

      <?php foreach ( $services as $service ) : ?>
        <?php $i++; ?>
        <?php if($i % 3 == 1) :?>
          <div class="row collapse inside-border-group" data-equalizer>
        <?php endif; ?>

        <div class="inside-border-group-item medium-8 small-12 columns">
          <a href="<?php echo get_permalink( $service['phila_v2_service_page'] ) ?>" class="valign">

          <div class="valign-cell pvm phm phl-l" data-equalizer-watch>
            <div><i class="fa <?php echo $service['phila_v2_icon'] ?> fa-2x" aria-hidden="true"></i></div>
            <div><?php echo get_the_title( $service['phila_v2_service_page'] ) ?> </div>
          </div>
        </a>
      </div>
      <?php if($i % 3 == 0) :?>
        </div>
      <?php endif;?>
      <?php endforeach; ?>
   </div>
  </div>
</div>
