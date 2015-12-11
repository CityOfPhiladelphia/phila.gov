=== Simple Calendar - formerly Google Calendar Events ===
Contributors: moonstonemedia, pderksen, nickyoung87, nekojira, rosshanney
Tags: calendar, calendars, calendar manager, custom calendar, custom calendars, event, events, events feed, google calendar, google
Requires at least: 4.0
Tested up to: 4.4
Stable tag: 3.0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Google Calendar events to your WordPress site in minutes. Beautiful calendar displays. Fully responsive.

== Description ==

[Simple Calendar](https://simplecalendar.io) is the easiest way to add Google Calendar events to your WordPress site. Keep managing everything in Google Calendar. Fine tune the display of your calendars to your liking. Add advanced features with add-ons.

= SIMPLE CALENDAR FEATURES =

* Display events from any public Google Calendar.
* Keep managing events in Google Calendar. No need to create events in WordPress.
* Out-of-the-box designs that match your theme’s look and feel.
* Fully responsive and mobile-friendly monthly grid and list views.
* Customize event content display using simple tags. No coding required.
* Combine multiple calendars into single displays explicitly or using categories.
* Intuitive admin settings for simple configuration changes.
* Advanced settings to adjust time zones, date/time formats and start of the week.
* Many translations available with more being added on a regular basis. [Translations welcome!](https://translate.wordpress.org/projects/wp-plugins/google-calendar-events)
* Fully extensible to support add-ons with advanced features for more specific needs.
* Open source with code hosted on GitHub. [Contributions welcome!](https://github.com/moonstonemedia/Simple-Calendar)

>**[Love the plugin? Please consider purchasing our Google Calendar Pro add-on](https://simplecalendar.io/addons/google-calendar-pro/?utm_source=wordpress.org&utm_medium=link&utm_campaign=simple-cal-readme&utm_content=description)**

= GOOGLE CALENDAR PRO ADD-ON FEATURES =

* Display events from **Private** Google Calendars.
* Secure connection established between your site and Google using the OAuth standard.
* Highlight events with colors that match those selected within each Google Calendar event.
* Display event guest information for any private or public event such as their avatar and if they’re attending.
* Display event organizer information.
* Display a list of attachments with links to their original source.
* Automatic easy updates.
* Priority email support with a 24-hour response time during weekdays backed by a top-notch team.

>**[Get these additional features with Google Calendar Pro](https://simplecalendar.io/addons/google-calendar-pro/?utm_source=wordpress.org&utm_medium=link&utm_campaign=simple-cal-readme&utm_content=description)**

== Installation ==

[Plugin installation instructions](http://docs.simplecalendar.io/simple-calendar-installation/?utm_source=wordpress.org&utm_medium=link&utm_campaign=simple-cal-readme&utm_content=installation)

== Frequently Asked Questions ==

= Where's your plugin documentation? =

Find our docs at [docs.simplecalendar.io](http://docs.simplecalendar.io/?utm_source=wordpress.org&utm_medium=link&utm_campaign=simple-cal-readme&utm_content=faq)

= Can I get notified by email of new releases? =

[Subscribe here](https://www.getdrip.com/forms/9434542/submissions/new) to be notified by email of major features or updates.

= How do I contribute to Simple Calendar? =

We'd love your help! Here's a few things you can do:

* [Rate our plugin](https://wordpress.org/support/view/plugin-reviews/google-calendar-events?postform#postform) and help spread the word!
* Help answer questions in our [community support forum](https://wordpress.org/support/plugin/google-calendar-events).
* Report bugs (with steps to reproduce) or submit pull requests [on GitHub](https://github.com/moonstonemedia/Simple-Calendar).
* Help add or update a [plugin translation](https://translate.wordpress.org/projects/wp-plugins/google-calendar-events).

== Screenshots ==

1. Basic Grid view
2. Basic List view
3. Calendar settings - Events
4. Calendar settings - Appearance
5. Calendar settings - Google Calendar
6. Calendar settings - Advanced
7. Add a calendar widget
8. Attach a calendar to a post or page

== Changelog ==

= 3.0.5 - November 19, 2015 =

* Fix: Fixed a bug where HTML in event description was not being rendered properly.
* Fix: Fixed a script loading issue when using the CPT view.
* Feature: Check for required PHP extensions upon plugin activation (curl and mbstring so far).

= 3.0.4 - November 18, 2015 =

* Fix: Fixed always enqueue option to work correctly.
* Fix: Fixed issue where multiple shortcodes would sometimes not load scripts correctly.
* Fix: Fixed z-index issue for admin tooltips.
* Tweak: Change default "today" color to blue (#1e73be).

= 3.0.3 - November 13, 2015 =

* Feature: Added option to display a compact list view.
* Feature: Added option to hide the header in list view.
* Tweak: Improved CSS styling for default list and grid CSS.

= 3.0.2 - November 12, 2015 =

* Fix: Fixed bug where calendar days were off by one day.

= 3.0.1 - November 9, 2015 =

* Fix: Fixed bug with update script being skipped via attachment to activation hook.

= 3.0.0 - November 8, 2015 =

* Announcement: Plugin renamed to Simple Calendar.
* Announcement: Visit our new website at [simplecalendar.io](https://simplecalendar.io/?utm_source=wordpress.org&utm_medium=link&utm_campaign=simple-cal-readme&utm_content=changelog)!
* Feature: Modular and extensible plugin, add-ons ready.
* Feature: Reworked default calendar views, now fully responsive (and titles in grid).
* Feature: Completely redesigned the admin user interface.
* Feature: Many new settings panel in admin dashboard, with better semantics and organization.
* Feature: A System Report page to help you troubleshoot problems and improve support response.
* Tweak: Use categories to organize your calendars.
* Tweak: Feeds moved from 'gce_feed' to 'calendar' post type slug, permalink change. 
* Fix: Timezones handling are more accurate.
* Fix: Incompatibilities with themes and other plugins.
* Fix: Several other bugfixes.
* Refactor: Plugin rebuilt from ground up: namespaces, closures, Composer support, entirely OOP.
* Dev: PHP 5.3 minimum required.
* Dev: All requests to Google from now on will be handled with the official Google API PHP Client.
* Dev: Tested up to WordPress 4.4.

= 2.4.0 - September 29, 2015 =

* Announcement: Simple Calendar is coming, changes ahead.
* Deprecation: The bundled/default Google API key reached it's quota and was shut off. Using your own API key is now required.

= 2.3.2 - September 1, 2015 =

* Fix: Bug in HTML support in events description.

= 2.3.1 - August 31, 2015 =

* Fix: Fallback for DateTime::setTimestamp() for installations still using PHP 5.2.
* Fix: Support HTML in events description when using `html="true"` attribute in shortcode.
* Localization: Added Finnish translations, courtesy of Ville Myllymäki.

= 2.3.0 - August 24, 2015 =

* Fix: Improve timezone handling when sending a request to Google.
* Tweak: Reintroduced imagesloaded library to improve compatibility with themes using Isotope and Masonry.

= 2.2.91 - August 18, 2015 =

* Fix: Calendar not working correctly with custom date range grid after 2.2.9 changes.

= 2.2.9 - August 14, 2015 =

* Fix: Event links pointing to Google Calendar have a timezone argument from feed setting.
* Fix: Improved assets loading, only load scripts on posts and pages that have a calendar.
* Localization: Updated Norwegian translations.

= 2.2.8 - August 7, 2015 =

* Fix: Improved security when saving plugin settings.
* Fix: Added URL encoding to fix some issues with API keys containing special characters.
* Tweak: Use calendar feed timezone or website timezone (default calendar).

= 2.2.7 - July 31, 2015 =

* Feature: Added an 'Add Calendar' button to quickly add a shortcode in posts.
* Fix: Reverted register scripts hook to init.
* Localization: Updated French translations.
* Tweak: Flush permalinks on plugin activation and deactivation.
* Tweak: Added `[if-not-location]` event builder conditional shortcode.
* Tweak: Added a 'gce_no_events_message_text' filter when no events are found.
* Tweak: Added a clear cache bulk action for clearing caches of multiple feeds.

= 2.2.6 - July 16, 2015 =

* Plugin performs a requirements check to ensure users are running a recent version of WordPress.
* Added '.gce-has-<n>-events' class to count events in each day in grid display.
* Removed unnecessary imagesLoaded JS library (optional dependency of the qTip2 library).
* Fixed a bug with the start month of the custom date range grid view.
* Fixed a bug with backslashes in date ant time custom format inputs.
* Fixed scripts and styles loading issues.
* Fixed bugs when custom date range values were left blank.
* Added Russian translation provided by Vadim Reutskiy.
* Added Swedish translation provided by Familjedaghemmet Chicos.
* Tested up to WordPress 4.3.

= 2.2.5 - April 22, 2015 =

* Updated calls to add_query_arg to prevent any possible XSS attacks.
* Fixed bug with fatal error in rare cases by rearragning order of plugin file includes.
* Fixed bug with navigation links sometimes returning -1.
* Corrected typo with paging links title attributes.

= 2.2.4 - April 6, 2015 =

* Updated French translation files.
* Fixed Catalan translation files.
* Fixed encoding bug with [maps-link] new window attribute.

= 2.2.3 - March 26, 2015 =

* Fixed bug with the "More details..." link encoding.
* Added note about total event limit of 2,500 now enforced by the Google Calendar API.
* Updated earliest feed event date default to 1 (one) month back.
* 0 (zero) value now allowed for earliest and latest feed event dates (sets them to the current date).
* Minor public script improvements.
* Added Brazilian Portuguese translation files.
* Updated jQuery UI datepicker CSS CDN reference for feed settings pages.
* Tested up to WordPress 4.2.

= 2.2.2.1 - March 17, 2015 =

* Option to always enqueue scripts & styles now enabled by default.

= 2.2.2 - March 15, 2015 =

* Added option to always enqueue scripts and styles on every post and page.
* Added custom date range grid option to display modes.
* Added option to disable the plugin CSS file.
* Fixed bug with list intervals.
* Fixed bug with event list showing past events.
* Minor public JavaScript performance updates.

= 2.2.1 - February 28, 2015 =

* Enqueue scripts & styles on all posts & pages temporarily until better detection can be put in place.
* Fixed GCal ID encoding in feed settings.
* Updated Italian translation.
* Updated French translation.

= 2.2.0 - February 25, 2015 =

* Added custom date range options.
* Added option to hide tooltips on grid display.
* Added additional save button at the bottom of the feed settings.
* Performance updates to script enqueues.
* Updated Catalan translation files.
* Updated list output logic.
* Fixed bug with multi-day events sometimes not showing up in list view.
* Fixed bug with calendar ID field not getting encoded.
* Fixed bug with tooltips scrolling on mobile.
* Fixed bug with Google Hangout event links.
* Fixed bug with widget settings not being unique.
* Widget UI enhancements.
* Feed settings UI enhancements.
* Error messaging updates.
* Security improvements.

= 2.1.7 - December 14, 2014 =

* Restructured next and back paging navigation script.
* Updated Italian translation files.

= 2.1.6.1 - December 5, 2014 =

* More tooltip (qTip2) effects & styling.

= 2.1.6 - December 5, 2014 =

* Fixed bug with tooltip (qTip2) in some cases by now including it's imagesLoaded script.
* Tooltip style improvements.
* Tooltip minified JS map file now included.
* Updated French translation.
* Updated Lithuanian translation.
* Fix bug with grouped-list multi-day event title.
* Fix bug when saving a bulk edit.

= 2.1.5 - December 2, 2014 =

* Updated jQuery tooltip library to [qTip2](http://qtip2.com/). Previously using unmaintained original qTip library.

= 2.1.4 - November 26, 2014 =

* Reverted CSS enqueue change.
* Added Catalan translation - Provided by Toni Ginard & Monica Grau of [agora.xtec.cat](http://agora.xtec.cat/).
* Updated AJAX security code.
* Fixed bug with calendar ID field not trimming extra spaces.
* Updated French translation - Pull Request by @Jojaba
* Tested up to WordPress 4.1.

= 2.1.3 - November 23, 2014 =

* Only load plugin scripts and stylesheets when the viewable page is rendering output from this plugin.
* Fixed bugs with simple display options.
* Added better error checking and output options to help in debugging GCal feeds.
* Added Dutch translation - Provided by Henri van Werkhoven.

= 2.1.2 - November 21, 2014 =

* Fixed bug with quick edit clearing out feed settings.
* Fix bug with pagination creating extra DOM elements.
* Localization string fixes - Pull Request by @Jojaba
* Added HTML to group events of the same day semantically - Pull Request by @martinburchell
* Fixed bug with [cal-id] event builder code.
* Add in post data resets.

= 2.1.1 - November 20, 2014 =

* Fixed bug with all day events not displaying.
* Added missing timezone parameter to internal query.

= 2.1.0 - November 19, 2014 =

* Updated to use Google Calendar API version 3. Version 2 deprecated on Nov. 17, 2014.

= 2.0.7.1 -November 17, 2014 =

* As of Nov. 17, 2014 the GCal API v2 is deprecated, which breaks all calendar feed displays. This update will temporarily hide the display while we work on a solution that uses GCal API v3.

= 2.0.7 - October 28, 2014 =

* Events will now display if it hasn't ended yet for list views.
* Fixed bug with date() call causing a display error in some cases.
* Fixed bug with with widget tooltip text display.
* Added cache clearing on upgrade.
* Added filters for Previous and Next link text.
* Internationalization and language file updates.
* Simplified text domain function.
* Added Lithuanian translation - Provided by Andrius Mazeika of [mazeika.info](http://mazeika.info/).

= 2.0.6.2 - October 22, 2014 =

* Reverted previous bug fix that introduced new bugs.

= 2.0.6.1 - October 22, 2014 =

* Added Polish translation - Provided by Michał Pasternak of [iplweb.pl](http://iplweb.pl/).
* Updated Italian translation.
* Updated French translation.
* Fixed bug with date() call causing a display error in some cases.
* Minor bug fixes.

= 2.0.6 - October 16, 2014 =

* Added minimum and maximum feed date options to fix event display issues and boost performance.
* Fixed a caching issue to increase performance.
* Fixed bug where backslashes kept getting added to event titles that already contained single quotes when navigating through pages in widget.

= 2.0.5.1 - October 15, 2014 =

* Fix timezone issue.

= 2.0.5 - October 10, 2014 =

* Fixed display bug with event date and grouped lists.
* Fixed broken paging links when feed IDs contain spaces between them.
* Fixed bug with start offset and grouped lists not working properly.
* Fixed bug where clear cache link was showing on any type of CPT.
* Added German translation - Provided by Stefanie Drucker of [kreativhuhn.at](http://www.kreativhuhn.at/).
* Added Norwegian translation - Provided by Tore Hjartland of [aliom.no](http://www.aliom.no/).

= 2.0.4 - October 7, 2014 =

* Added option to show/hide paging.
* Added option to limit display to any number of days or events per page.
* Added option to set the start date offset any number of days back or ahead (list view).
* Removed retrieve events from/until options now that display limit options will be used.
* Removed max number of events to retrieve option.
* Added shortcode attribute 'paging'.
* Added shortcode attribute 'interval'.
* Added shortcode attribute 'interval_count'.
* Added shortcode attribute 'offset_interval_count'.
* Added shortcode attribute 'offset_direction'.
* Updated shortcode 'display' attribute to allow a value of 'grouped-list'.
* Date no longer shows up for the title (list view).
* HTML restructured to use div tags instead of an unordered list (list view).
* Nav bar HTML (Back/Next links and month title) restructured to use div tags instead of span and percentages.
* Moved clear cache button and changed style.
* Added Italian translation - Provided by Francesco Paccagnella of [pacca.it](http://www.pacca.it/).
* Added French translation - Provided by Vincent Bray.
* Fixed PHP error during upgrade.
* Fixed cross-site scripting (XSS) vulnerability.
* JavaScript restructured to fit more in line with best practices.
* Remove unused admin script file.

= 2.0.3.1 - October 7, 2014=

* Fixed bug where retrieve from/until dates were accidentally removed.

= 2.0.3 - September 20, 2014 =

* Fixed bug where calendar feed caches weren't getting cleared properly.
* Fixed feed settings metabox content wrapping issue.

= 2.0.2 - September 17, 2014 =

* Added Spanish translation - Provided by Eduardo Larequi of [educacion.navarra.es/web/pnte/](http://www.educacion.navarra.es/web/pnte/).
* Fixed timezone issues by forcing calendar feeds to use the timezone selected in the site's General Settings. Feed-specific timezone setting removed.
* Fixed a bug with recurring events display.
* Fixed an upgrade bug with multiple day events.

= 2.0.1 - September 11, 2014 =

* Fixed display errors with certain event builder shortcodes.
* Added language folder.

= 2.0.0 - September 9, 2014 =

* Plugin rewritten from scratch.
* Now using custom post types for storing and customizing Google calendar feeds.
* Introduced the shortcode `[gcal]` (old shortcode still supported).

== Upgrade Notice ==

= 3.0.0 =

This is a major rewrite of the plugin with lots of additions and changes. Please backup before proceeding.
