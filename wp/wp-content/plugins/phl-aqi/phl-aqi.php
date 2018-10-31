<?php
/**
 *
 * @link              http://phila.gov
 * @package           phila-gov_customization
 *
 * @wordpress-plugin
 * Plugin Name:       PHL AQI
 * Plugin URI:        GitHub something something
 * Description:       Display plugin creates a wp shortcode [phl-aqi] that can be used on any page to display a gauge graph with the current air quality.
 *
 * Version:           0.0.1
 * Author:            City of Philadelphia
 */

// Direct access?  Get out.
if ( ! defined( 'ABSPATH' ) ) exit;

require_once('includes/class-phl-aqi.php');

add_action( 'init', 'init_phl_aqi_plugin' );

function init_phl_aqi_plugin() {
  new PHL_AQI();
}
