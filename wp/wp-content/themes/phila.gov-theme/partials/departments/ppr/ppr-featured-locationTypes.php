<?php if(rwmb_meta('phila_feat_locations_grid_shown')):?>
<section class="row ppr-feat-locations">
    <div class="columns">
        <h2 class="contrast"><?= rwmb_meta( 'phila_feat_locations_grid__header' )  ?></h2>
        <?= rwmb_meta('phila_feat_locations_grid__desc'); ?>
    </div>

    <div class="columns">
        <div class="grid-container">
        <div id="ppr-feat-locations__grid" class="grid-x grid-margin-x align-justify">
          <?php
            include(locate_template('partials/departments/ppr/ppr-feat-locationType-card.php'));
            include(locate_template('partials/departments/ppr/ppr-feat-locationType-card.php'));
            include(locate_template('partials/departments/ppr/ppr-feat-locationType-card.php'));
            include(locate_template('partials/departments/ppr/ppr-feat-locationType-card.php'));
            include(locate_template('partials/departments/ppr/ppr-feat-locationType-card.php'));
            include(locate_template('partials/departments/ppr/ppr-feat-locationType-card.php'));
          ?>
        </div>
        </div>
    </div>

</section>

<?php endif; ?>
