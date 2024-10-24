<?php
/**
 * The template for displaying the front page.
 *
 * @package phila-gov
 */

get_header();

$mobile_homepage_image = rwmb_meta( 'homepage_mobile', array( 'object_type' => 'setting' ), 'phila_settings' );
$desktop_homepage_image = rwmb_meta( 'homepage_desktop', array( 'object_type' => 'setting' ), 'phila_settings' );
?>

<div class="site-main home">
  <div id="phila-gov-homepage"></div>
</div><!-- .site-main .home -->

<?php get_footer(); ?>