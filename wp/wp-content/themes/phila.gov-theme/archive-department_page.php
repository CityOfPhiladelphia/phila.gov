<?php
/*
 * Partial for rendering the covid guidance program
 *
 */
?>
<?php
/**
 * See all programs template
 * @version 0.23.0
 * @package phila-gov
 */

get_header(); ?>

<div class="row">
        <header class="small-24 columns">
          <h1 class="contrast">
            Departments and other agencies
          </h1>
        </header>
      </div>
<?php

global $phila_environment;
$app_id = 'vue-app';

$vuejs_js_ids = ['https://philagov-vue-apps.s3.amazonaws.com/city-government-directory/test/js/chunk-vendors.js?1', 'https://philagov-vue-apps.s3.amazonaws.com/city-government-directory/test/js/app.js?1'];
$vuejs_css_ids = ['https://philagov-vue-apps.s3.amazonaws.com/city-government-directory/test/css/app.css?1'];

include(locate_template( 'partials/vue-apps/vue-register.php' ) );


?>


<?php get_footer(); ?>
