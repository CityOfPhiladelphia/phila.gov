=== Reusable Text Blocks ===
Contributors: halgatewood
Donate link: https://halgatewood.com/donate/
Tags: content, block, reusable content, reusable text, widget, shortcode, dry, text blocks, content blocks
Requires at least: 3.5
Tested up to: 5.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create reusable pieces of content that you can insert into themes, posts, pages & widgets.

== Description ==

This plugin creates a new 'text-blocks' custom post type with it's own section in the WordPress admin sidebar. It uses the standard WordPress user interface so you and your clients will know how to use it instantly. 

>New in Version 1.5: Variables! You can add {{player}} to your text block content and then pass in 'player' to the shortcode: [text-blocks id="1" player="Hal Gatewood"]


You can add it to your site in three ways:

= 1. Widget =
The included widget allows you to specify which block you want to insert. You can also include a title if needed.

= 2. Widget =
`[text-blocks id="1"] or [text-blocks id="text_block_slug"]`

= 3. PHP Function =
A PHP function has been setup so you do not have to use the do_shortcode function.

`<?php if(function_exists('show_text_block')) { echo show_text_block(421); } ?>`
`<?php if(function_exists('show_text_block')) { echo show_text_block('slug'); } ?>`




== Installation ==

1. Add plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create text blocks and include them in your site using one of the 3 methods below

You can add it to your site in three ways:

= 1. Widget =
The included widget allows you to specify which block you want to insert. You can also include a title if needed.

= 2. Widget =
`[text-blocks id="1"] or [text-blocks id="text_block_slug"]`

= 3. PHP Function =
A PHP function has been setup so you do not have to use the do_shortcode function.

`<?php if(function_exists('show_text_block')) { echo show_text_block(421); } ?>`
`<?php if(function_exists('show_text_block')) { echo show_text_block('slug'); } ?>`


== Screenshots ==

1. Text Blocks list view, with quick view of content and shortcode.
2. Uses standard WordPress functionality. No surprises, you already know how to use it.
3. Widget included
4. New in 1.4.6: Media Button
5. Create custom templates

== Changelog ==

= 1.5.2 =
* New: checks for customs templates in a folder called 'text-blocks' first
* New: filter to check where templates are checked - text_blocks_template_location

= 1.5.1 =
* Fixed no attribute error

= 1.5 =
* Fixed PHP 7 Warnings
* Added slug="" parameter when added from Add Block button (helps you know which shortcode block is which)
* Text Blocks will only show if Published
* Variables. You can add {{player}} to your text block and then pass in 'player' to the shortcode: [text-blocks id="1" player="Hal Gatewood"]

= 1.4.10 =
* Added filter wpml_object_id

= 1.4.9 =
* WP_Widget construct changed

= 1.4.8 =
* Fixed plugin not adhering to the language needed. Thanks @elvi992

= 1.4.7 =
* Added ‘id’ to text_blocks_shortcode_html filter
* Fixed widget checkboxes with on
* Added quotes around plain=1

= 1.4.6 = 
* Added .pot file, translation ready
* Removed custom post messages
* Added filter: 'text_blocks_post_type_args'
* Added Media Button to quickly add shortcode to WP Text Editor
* 'show_text_block' now has third parameter that passes all shortcode parameters to the text block, for example: [text-blocks id="198" location="okc"]
* Create custom templates: text-blocks-{$id}.php, or pass template="homepage" in your shortcode for text-blocks-homepage.php
* New checkbox in widget to hide the title

= 1.4.5 =
* Enabled revisions
* Added filter: 'text_blocks_show_text_block_id'
* Tested on WordPress 4.1

= 1.4.4 =
* Fix for error if no content found

= 1.4.3 =
* Moved init into plugins_loaded
* WordPresss 3.8 testing and icon

= 1.4.2 =
* Post thumbnail support added.

= 1.4.1 =
* Added second parameter to keep out the_content filter and display exactly what is in the content.

= 1.4 =
* Display by slug (post_name) or id
* Slug class added to widget so you can target text-blocks easier with CSS

= 1.3 =
* Added ability to set wpautop in widget
* Also added filter to target widget code: 'text_blocks_widget_html'

= 1.2 =
* Bug in widget select box (Thanks Shaun Forsyth)

= 1.1 =
* Small bug fixes

= 1.0 =
* Initial load of the plugin.

