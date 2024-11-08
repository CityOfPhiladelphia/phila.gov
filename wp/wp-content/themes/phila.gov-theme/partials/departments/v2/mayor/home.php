<?php
  /* breaking all the rules */
?>
<div class="mayor-page">

<header>
    <div class="hero-content">
        <div class="hero-subpage">
            <div class="row expanded ">
              <div class="medium-18 small-centered columns text-overlay">
                <h1 class="page-title">Office of the <span class="MAYOR">Mayor</span></h1>

                    <div class="row">
                      <div class="medium-16 small-centered columns text-overlay">
                        <p class="sub-title mbn-mu"><strong><?php echo phila_get_item_meta_desc( ); ?></strong></p>
                      </div>
                    </div>

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
</header>

<div class="test">great</div>
