<?php
/**
 *
 * @link              http://phila.gov
 * @since             0.5.6
 * @package           phila-gov_customization
 *
 * @wordpress-plugin
 * Plugin Name:       Phila.gov Customization
 * Plugin URI:        https://github.com/CityOfPhiladelphia/phila.gov-customization
 * Description:       Custom Wordpress functionality, custom post types, custom taxonomies, etc.
 *
 * Version:           0.21.0
 * Author:            City of Philadelphia
 * Author URI:        http://phila.gov
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       phila.gov-customization
 * Domain Path:       /languages
 */

// Direct access?  Get out.
if ( ! defined( 'ABSPATH' ) ) exit;

$dir = plugin_dir_path( __FILE__ );
require $dir. '/admin/admin-ui.php';
require $dir. '/admin/class-phila-gov-admin-documents.php';
require $dir. '/admin/class-phila-gov-admin-menu.php';
require $dir. '/admin/class-phila-gov-cpt-notices.php';
require $dir. '/admin/class-phila-gov-custom-post-types.php';
require $dir. '/admin/class-phila-gov-custom-taxonomies.php';
require $dir. '/admin/class-phila-gov-department-author-media.php';
require $dir. '/admin/class-phila-gov-department-sites.php';
require $dir. '/admin/class-phila-gov-news.php';
require $dir. '/admin/class-phila-gov-role-administration.php';
require $dir. '/admin/class-phila-gov-site-wide-alert.php';
require $dir. '/admin/define-roles.php';
require $dir. '/admin/meta-boxes.php';
require $dir. '/admin/tiny-mce.php';


require $dir. '/public/browse.php';
require $dir. '/public/class-content-collection-walker.php';
require $dir. '/public/class-phila-gov-department-notices.php';
require $dir. '/public/class-phila-gov-service-rewrites.php';
require $dir. '/public/class-phila-gov-sidebar-shortcode.php';
require $dir. '/public/class-phila-gov-site-wide-alert-rendering.php';
require $dir. '/public/departments.php';
require $dir. '/public/news.php';
require $dir. '/public/removals.php';
require $dir. '/public/rewrite-rules.php';
