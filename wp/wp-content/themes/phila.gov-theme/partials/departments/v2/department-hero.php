<?php
  /* Department hero header */
?>
<header>
    <div class="hero-content" style="background-image:url(<?php echo $this->bg['desktop'] ?>) ">
        <div class="hero-subpage" style="background-image:url(<?php echo $this->bg['desktop']; ?>)">
            <?php if (!empty($this->bg['photo_credit']) ): ?>
              <div class="photo-credit small-text">
                <span><i class="fas fa-camera" aria-hidden="true"></i> Photo by <?php echo $this->bg['photo_credit'] ?></span>
              </div>
            <?php endif; ?>

            <div class="row expanded <?php echo $this->is_homepage ? 'pbs pvxxl-mu' : 'pvl' ?>">
              <div class="medium-18 small-centered columns text-overlay">
                  <?php echo phila_get_department_typography( $this->parent ); ?>

                  <?php if ($this->is_homepage): ?>
                    <div class="row">
                      <div class="medium-16 small-centered columns text-overlay">
                        <p class="sub-title mbn-mu"><strong><?php echo phila_get_item_meta_desc( ); ?></strong></p>
                      </div>
                    </div>
                  <?php endif;?>

              </div>
              </div>

            </div><!-- END .row.expanded   -->
            <?php

              /*
              Our navigation menu. We use categories to drive functionality.
              This checks to make sure a category exists for the given page,
              if it does, we render our menu w/ markup.
              */
                //TODO: clean up menu rendering
                //
                phila_get_menu();
            ?>

        </div> <!-- END .hero-wrap  -->

    </div>

</header>