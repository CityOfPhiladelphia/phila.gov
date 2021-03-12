<?php
/*
 * Partial for rendering translated content.
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
$program_file_path = 'https://www.phila.gov/embedded/translated-content/'.$phila_environment;

$vuejs_js_ids = [$program_file_path.'/js/chunk-vendors.js', $program_file_path.'/js/app.js'];
$vuejs_css_ids = [$program_file_path.'/css/chunk-vendors.css', $program_file_path.'/css/app.css'];

include(locate_template( 'partials/vue-apps/vue-register.php' ) );

?>

<?php get_footer(); ?>
