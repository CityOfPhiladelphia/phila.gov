=== Meta Box Text Limiter ===
Contributors: metabox, rilwis
Donate link: https://metabox.io
Tags: meta-box, custom-fields, custom-field, meta, meta-boxes, text limit, character limit, word limit
Requires at least: 5.0
Tested up to: 5.6
Stable tag: 1.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Limit number of characters or words entered for text and textarea fields in meta boxes.

== Description ==

Text Limiter is an extension for [Meta Box](https://metabox.io) plugin which allows you to limit number of characters or words entered for text and textarea fields.

### Usage

To start using text limiter, just add the following parameters to `text` or `textarea` fields:

`'limit'      => 20, // Number of characters or words
'limit_type' => 'character', // Limit by 'character' or 'word'. Optional. Default is 'character'`

### Plugin Links

- [Project Page](https://metabox.io/plugins/meta-box-text-limiter/)
- [Documentation](https://docs.metabox.io)
- [Github repo](https://github.com/wpmetabox/meta-box-text-limiter)
- [View other extensions](https://metabox.io/plugins/)

== Installation ==

You need to install [Meta Box](https://metabox.io) plugin first

- Go to Plugins | Add New and search for Meta Box
- Click **Install Now** button to install the plugin
- After installing, click **Activate Plugin** to activate the plugin

Install **Meta Box Text Limiter** extension

- Go to **Plugins | Add New** and search for **Meta Box Text Limiter**
- Click **Install Now** button to install the plugin
- After installing, click **Activate Plugin** to activate the plugin

To start using text limiter, just add the following parameters to `text` or `textarea` fields:

`'limit'      => 20, // Number of characters or words
'limit_type' => 'character', // Limit by 'character' or 'word'. Optional. Default is 'character'`


== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 1.1.3 - 2021-04-24 =
* Fix notice "Trying to access array offset" (by checking field value if field not found).

= 1.1.2 - 2021-01-27 =
* Fix input references which breaks the functionality.

= 1.1.0 =
* Changed: Rewrite the JavaScript, making it work for cloneable groups.

= 1.0.4 =
* Changed: Allow the plugin to be included in themes/plugins.

= 1.0.3 =
* Fix: Multi-bytes characters are cut from the frontend.

= 1.0.2 =
* Fix: Warning in helper function if using limit by character.

= 1.0.1 =
* Improvement: Added front-end text-limiting functionality

= 1.0.0 =
* First release

== Upgrade Notice ==
