<?php if ( rwmb_meta('phila_feat_activites_grid_shown') ) : ?>
<section class="row ppr-feat-activites">
    <div class="columns">
        <h2 class="contrast">Featured activities</h2>
    </div>

    <div class="columns">
        <div class="grid-container">
        <div id="ppr-feat-activites__grid" class="grid-x grid-margin-x align-stretch align-center">
          <?php
            include(locate_template('partials/departments/ppr/ppr-feat-activity-card.php'));
            include(locate_template('partials/departments/ppr/ppr-feat-activity-card.php'));
            include(locate_template('partials/departments/ppr/ppr-feat-activity-card.php'));
          ?>
        </div>
        </div>
    </div>

</section>

<?php endif; ?>
