=== Simple 301 Redirects ===
Contributors: scottnelle
Tags: 301, redirect, url, seo
Requires at least: 3.0
Tested up to: 4.4.2
Stable tag: 1.07
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple 301 Redirects provides an easy method of redirecting requests to another page on your site or elsewhere on the web.

== Description ==

Simple 301 Redirects provides an easy method of redirecting requests to another page on your site or elsewhere on the web. It's especially handy when you migrate a site to WordPress and can't preserve your URL structure. By setting up 301 redirects from your old pages to your new pages, any incoming links will be seemlessly passed along, and their pagerank (or what-have-you) will be passed along with them.

== Installation ==

1. Upload Simple 301 Redirects to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add redirects on the Settings > 301 Redirects page.


== Changelog ==
= 1.07 =
* Security Update - Better handling of posted data.

= 1.06 =
* Security Updates - Special thanks to Ryan Hellyer!

= 1.05 =
* Wildcard support
* Delete functiontionality
* On-page documentation

= 1.04 =
* Removed deprecated function calls
* Updated code to better match WordPress coding standards
* Updated handling of https protocol to prevent errors in certain server configurations
* Better support for destinations that start with a leading slash instead of the full domain

= 1.03 =
* Sorry for the double update. I forgot to check for PHP4 compatibility. Many people are still using PHP4, apparently, so this update is to fix compatibility with these legacy systems.

= 1.02 =
* Added support for special characters in non-english URLs.
* Fixed a case sensitivity bug.

= 1.01 =
* Updated redirect method to send headers directly rather than using wp_redirect() because it was sending 302 codes on some servers

= 1.0 =
* Initial Release
