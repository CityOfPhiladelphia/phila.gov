<?php
/**
 *
 * @link              http://phila.gov
 * @package           phila-gov_customization
 *
 * @wordpress-plugin
 * Plugin Name:       PHL AQI
<<<<<<< HEAD
 * Plugin URI:        GitHub something something
 * Description:       Display plugin creates a wp shortcode [phl-aqi] that can be used on any page to display a gauge graph with the current air quality.
=======
 * Plugin URI:        https://github.com/CityOfPhiladelphia/phila.gov
 * Description:       Displays the current air quality conditions in the City of Philadelphia.
>>>>>>> 9e66a4be37429d38bb245ae958d5a40802376253
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
