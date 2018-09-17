<?php
/*
 * List of program subpages, generally should be location-based.
*/

?>
<?php if ( isset( $location_list_title ) ) : ?>

  <section>
    <div class="grid-container">
        <div class="grid-x grid-padding-x">
          <div class="cell small-24">
            <h2 class="contrast"><?php echo $location_list_title ?></h2>
          </div>
      </div>
    </div>

    <?php if ( isset( $location_list['group'] ) ) : ?>

      <?php foreach($location_list['group'] as $group): ?>
        <?php if ( $group['group_title'] ) :?>
          <div class="grid-container">
            <div class="grid-x grid-padding-x">
              <div class="cell small-24">
                <h3><?php echo $group['group_title'] ?></h3>
              </div>
            </div>
          </div>
        <?php endif; ?>

          <div class="grid-container">
            <div class="grid-x grid-padding-x">
              <div class="cell small-24">
                <div class="location-list">
                <?php foreach ($group['location_list'] as $id): ?>
                  <a href="<?php echo get_the_permalink($id)?>" class=""><div class="location-link"><?php echo get_the_title($id);?>  </div></a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

    <?php endif; ?>
  </section>
<?php endif;?>
