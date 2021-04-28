<?php
/**
*
* Shortcode for displaying PPR Things to do component
* @param @atts - none
*
* @package phila-gov_customization
*/
function ppr_things_to_do_shortcode( $atts ){

  if ( rwmb_meta('phila_feat_activites_grid_shown') ) : ?>

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
      
    <?php endif; ?>
    <?php 
}

add_action( 'init', 'register_ppr_things_to_do_shortcode' );

function register_ppr_things_to_do_shortcode(){
  add_shortcode( 'ppr-things-to-do', 'ppr_things_to_do_shortcode' );
}
