<?php
/**
 *
 * @link              http://phila.gov
 * @package           phila-gov_customization
 *
 * @wordpress-plugin
 * Plugin Name:       Phila.gov Customization
 * Plugin URI:        https://github.com/CityOfPhiladelphia/phila.gov-customization
 * Description:       Custom Wordpress functionality, custom post types, custom taxonomies, etc.
 *
 * Version:           0.22.0
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
require $dir. '/admin/admin-documentation.php';
require $dir. '/admin/admin-ui.php';
require $dir. '/admin/class-phila-gov-admin-documents.php';
require $dir. '/admin/class-phila-gov-admin-menu.php';
require $dir. '/admin/class-phila-gov-admin-templates.php';
require $dir. '/admin/class-phila-gov-custom-post-types.php';
require $dir. '/admin/class-phila-gov-custom-taxonomies.php';
//require $dir. '/admin/class-phila-gov-department-author-media.php';
require $dir. '/admin/class-phila-gov-department-sites.php';
require $dir. '/admin/class-phila-gov-filter-post-type-links.php';
require $dir. '/admin/class-phila-gov-item-meta-desc.php';
require $dir. '/admin/class-phila-gov-role-administration.php';
require $dir. '/admin/class-phila-gov-site-wide-alert.php';
require $dir. '/admin/class-phila-gov-event-pages.php';
require $dir. '/admin/class-phila-gov-staff-directory.php';
require $dir. '/admin/define-roles.php';
require $dir. '/admin/meta-boxes.php';
require $dir. '/admin/tiny-mce.php';

require $dir. '/public/shortcodes/blogs.php';
require $dir. '/public/shortcodes/callout.php';
require $dir. '/public/shortcodes/news.php';
require $dir. '/public/shortcodes/press-releases.php';
require $dir. '/public/shortcodes/pullquote.php';
require $dir. '/public/shortcodes/vertical-rule.php';



require $dir. '/public/class-content-collection-walker.php';
require $dir. '/public/class-phila-gov-filter-posts.php';
require $dir. '/public/class-phila-gov-site-wide-alert-rendering.php';
require $dir. '/public/modify-post-type-links.php';
require $dir. '/public/removals.php';
require $dir. '/public/rewrite-rules.php';
