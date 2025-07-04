<?php
/**
 *
 * @link              https://phila.gov
 * @package           phila-gov_customization
 *
 * @wordpress-plugin
 * Plugin Name:       Phila.gov Customization
 * Plugin URI:        https://github.com/CityOfPhiladelphia/phila.gov-customization
 * Description:       Custom Wordpress functionality, custom post types, custom taxonomies, etc.
 *
 * Author:            City of Philadelphia
 * Author URI:        http://phila.gov
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       phila.gov-customization
 * Domain Path:       /languages
 */

// Direct access?  Get out.
if ( ! defined( 'ABSPATH' ) ) exit;

//TODO: Use an autoloader already

$dir = plugin_dir_path( __FILE__ );
require $dir. '/admin/admin-documentation.php';
require $dir. '/admin/admin-ui.php';
require $dir. '/admin/class-phila-gov-admin-documents.php';
require $dir. '/admin/class-phila-gov-admin-menu.php';
require $dir. '/admin/class-phila-gov-calendar-helper.php';
require $dir. '/admin/class-phila-gov-calendars.php';
require $dir. '/admin/class-phila-gov-custom-post-types.php';
require $dir. '/admin/class-phila-gov-custom-taxonomies.php';
//require $dir. '/admin/class-phila-gov-department-author-media.php';
require $dir. '/admin/class-phila-gov-department-sites.php';
require $dir. '/admin/class-phila-gov-filter-post-type-links.php';
require $dir. '/admin/class-phila-gov-role-administration.php';
require $dir. '/admin/class-phila-gov-site-wide-alert.php';
require $dir. '/admin/class-phila-gov-staff-directory.php';
require $dir. '/admin/class-phila-gov-longform-content.php';
require $dir. '/admin/define-roles.php';
require $dir. '/admin/rest-additions.php';
require $dir. '/admin/tiny-mce.php';

require $dir. '/admin/service-page/class-phila-gov-cpt-service-page.php';
require $dir. '/admin/service-page/class-phila-gov-service-register-templates.php';

require $dir. '/admin/departments/class-phila-gov-cpt-departments.php';
require $dir. '/admin/departments/class-phila-gov-department-register-templates.php';
require $dir. '/admin/departments/templates/class-phila-gov-collection-page.php';

require $dir. '/admin/event-spotlight/class-phila-gov-cpt-event-spotlight.php';
require $dir. '/admin/event-spotlight/templates/class-phila-gov-spotlight.php';

require $dir. '/admin/guides/class-phila-gov-cpt-guides.php';
require $dir. '/admin/guides/class-phila-gov-guides-register-templates.php';

require $dir. '/admin/meta-boxes/meta-boxes.php';
require $dir. '/admin/meta-boxes/class-phila-gov-admin-templates.php';
require $dir. '/admin/meta-boxes/class-phila-gov-custom-phone.php';
require $dir. '/admin/meta-boxes/class-phila-gov-custom-unit.php';
require $dir. '/admin/meta-boxes/class-phila-gov-item-meta-desc.php';
require $dir. '/admin/meta-boxes/class-phila-gov-post.php';
require $dir. '/admin/meta-boxes/class-phila-gov-row-select-options.php';
require $dir. '/admin/meta-boxes/class-phila-gov-row-metaboxes.php';
require $dir. '/admin/meta-boxes/class-phila-gov-service-update-pages.php';
require $dir. '/admin/meta-boxes/class-phila-gov-standard-metaboxes.php';
require $dir. '/admin/meta-boxes/class-phila-gov-taxonomy-meta.php';
require $dir. '/admin/meta-boxes/class-phila-gov-vue-app.php';
require $dir. '/admin/meta-boxes/page-template-contact-us.php';
require $dir. '/admin/meta-boxes/tax-detail-fields.php';
require $dir. '/admin/meta-boxes/v2-departments.php';

require $dir. '/admin/programs-initiatives/class-phila-gov-cpt-programs.php';
require $dir. '/admin/programs-initiatives/class-phila-gov-program-register-templates.php';

require $dir. '/admin/settings/phila-gov-settings.php';
require $dir. '/admin/translations.php';

require $dir. '/public/shortcodes/callout.php';
require $dir. '/public/shortcodes/info-block.php';
require $dir. '/public/shortcodes/modal.php';
require $dir. '/public/shortcodes/program-tiles.php';
require $dir. '/public/shortcodes/pullquote.php';
require $dir. '/public/shortcodes/service-tiles.php';
require $dir. '/public/shortcodes/standard-date-time.php';
require $dir. '/public/shortcodes/vertical-rule.php';

require $dir. '/public/controllers/class-phila-closures.php';
require $dir. '/public/controllers/class-phila-calendars.php';
require $dir. '/public/controllers/class-phila-departments.php';
require $dir. '/public/controllers/class-phila-documents.php';
require $dir. '/public/controllers/class-phila-featured-news.php';
require $dir. '/public/controllers/class-phila-document-finder.php';
require $dir. '/public/controllers/class-phila-jobs.php';
require $dir. '/public/controllers/class-phila-last-updated.php';
require $dir. '/public/controllers/class-phila-last-updated-for-search.php';
require $dir. '/public/controllers/class-phila-longform-content.php';
require $dir. '/public/controllers/class-phila-pages.php';
require $dir. '/public/controllers/class-phila-posts.php';
require $dir. '/public/controllers/class-phila-programs.php';
require $dir. '/public/controllers/class-phila-service-pages.php';
require $dir. '/public/controllers/class-phila-staff-members.php';

require $dir. '/public/add-headers.php';
require $dir. '/public/hostname-redirect.php';
require $dir. '/public/modify-post-type-links.php';
require $dir. '/public/removals.php';
require $dir. '/public/rewrite-rules.php';

require $dir. '/public/controllers/v2/class-phila-site-wide-alerts-v2.php';