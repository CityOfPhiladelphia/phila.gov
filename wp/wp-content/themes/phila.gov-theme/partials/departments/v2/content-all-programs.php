<?php
/*
 * Partial for rendering all programs in this department's category.
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

<?php

global $phila_environment;
$app_id = 'vue-app';
$program_file_path = 'https://www.phila.gov/embedded/all-programs/'.$phila_environment;

$vuejs_js_ids = [$program_file_path.'/js/chunk-vendors.js?cache1', $program_file_path.'/js/app.js?cache1'];
$vuejs_css_ids = [$program_file_path.'/css/app.css?cache1'];

include(locate_template( 'partials/vue-apps/vue-register.php' ) );

?>

<?php get_footer(); ?>
