
<header>

    <div class="hero-content" style="background-image:url(<?= $this->bg['desktop'] ?>) ">

            <?php if ( $this->is_homepage_v2) : ?>
                <img class="show-for-small-only" src="<?= $this->bg['mobile'] ?>" alt="">
            <?php endif; ?>

            <div class="hero-wrap">

                <?php if (!empty($this->bg['photo_credit']) ): ?>
                  <div class="photo-credit small-text">
                    <span><i class="fa fa-camera" aria-hidden="true"></i> Photo by <?= $this->bg['photo_credit'] ?></span>
                  </div>
                <?php endif; ?>

                <div class="row expanded <?= $this->is_homepage_v2 ? 'pbs pvxxl-mu' : 'pbl' ?>">

                        <div class="medium-18 small-centered columns text-overlay">
                            <?php echo phila_get_department_homepage_typography( $this->parent ); ?>

                            <?php if ($this->is_homepage_v2): ?>
                              <div class="row">
                                <div class="medium-16 small-centered columns text-overlay">
                                  <p class="sub-title mbn-mu"><strong><?= phila_get_item_meta_desc( ); ?></strong></p>
                                </div>
                              </div>
                            <?php endif;?>

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
