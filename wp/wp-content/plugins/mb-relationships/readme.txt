=== MB Relationships ===
Contributors: metabox, rilwis, truongwp
Donate link: https://metabox.io/pricing/
Tags: relationship, relationships, connection, connections, posts to posts, post relationship, post relationships
Requires at least: 4.8
Tested up to: 5.3.2
Stable tag: 1.9.0
License: GPLv2 or later

A lightweight solution for creating many-to-many posts to posts relationships.

== Description ==

**MB Relationships** helps you create many-to-many relationships between posts, pages or any custom post type. The plugin is lightweight and optimized for database and query performance.

The plugin allows you to create connections from posts to posts, posts to pages and so on. Then you can perform corresponding queries to retrieve posts that are connected to or from given posts.

It supports reciprocal and bi-directional relationships.

### Why Do You Need Posts To Posts Relationships In WordPress?

Post relationships is a missing part in WordPress. The only "built-in" way that mimic the post relationship in WordPress is the `post_parent` for pages where you can create many child pages of a page (a one-to-many relationship). Unfortunately, that's available for pages and hierarchical post types only. Besides, it's not many-to-many relationship.

Below are some examples of posts to posts relationships that might help you see the benefit of this feature:

#### Creating Related Posts In WordPress

The simplest example is to manually create related posts in your WordPress website. When you edit a post, you can select posts that have similar or related content and display them in the frontend for further reading.

You can also query backward: displaying posts that link to the being read post as a reference to provide more information to your readers. With this, you don't need a WordPress related posts plugin anymore.

#### Example: Events And Bands

Suppose you have two custom post types, event and band, where:

- In each event there may be multiple bands, and
- Each band can participate in many events.

If people want to buy tickets, they could search for events in their location and see what bands are playing on a given date, or they could search for bands they like and see what date they are playing near their location.

In this example, we have created many-to-many relationships between events and bands.

### Bi-directional relationships

**MB Relationships** allows you to create bi-directional connections. You will be able to query back and forth without any problem.

The data is stored in the database as a pair of (from_id, to_id), thus making it independent from either side.

Besides, for each side, there's a meta box that shows what are connected from/to. So you don't have to worry about the direction of the connection anymore.

### An Alternative For The Posts 2 Posts Plugin (P2P Plugin)

**MB Relationships** is very much inspired by the popular plugin [Posts 2 Posts](https://wordpress.org/plugins/posts-to-posts/) which is not maintained anymore. We have taken the idea and made some improvements. The codebase and data structure is very similar.

### Plugin features

- **Simple APIs**: the plugin provides simple APIs for registering relationships and retrieving connected items. It integrates with existing WordPress APIs such as `WP_Query`,` get_terms` and `get_users`. See [documentation](https://docs.metabox.io/extensions/mb-relationships/) for more information.
- Uses **custom relationship table** to store relationships. That helps optimize the database storage and query performance.
- You can **create relationships between any kind of content** in WordPress: posts to posts, posts to users, etc. For posts to terms and posts to users, it's required the [MB Term Meta](https://metabox.io/plugins/mb-term-meta/) and [MB User Meta](https://metabox.io/plugins/mb-user-meta/).
- Supports creating **reciprocal relationships** (posts-posts, users-users, ...).
- Supports creating **bi-directional relationships** and easily query them.
- Display connected items easily with **shortcode**.
- Extremely **lightweight and fast**.

### Plugin Links

- [Project Page](https://metabox.io/plugins/mb-relationships/)
- [Github Repo](https://github.com/wpmetabox/mb-relationships/)

This plugin is a free extension of [Meta Box](https://metabox.io) plugin, which is a powerful, professional solution to create custom fields and custom meta boxes for WordPress websites. Using **MB Relationships** in combination with [other extensions](https://metabox.io/plugins/) will help you manage any content types in WordPress easily and make your website more professional.

== Installation ==

You need to install [Meta Box](https://metabox.io) plugin first

- Go to Plugins | Add New and search for Meta Box
- Click **Install Now** button to install the plugin
- After installing, click **Activate Plugin** to activate the plugin

Install **MB Relationships** extension

- Go to **Plugins | Add New** and search for **MB Relationships**
- Click **Install Now** button to install the plugin
- After installing, click **Activate Plugin** to activate the plugin

== Frequently Asked Questions ==

== Screenshots ==
1. "Connects To" meta box
2. "Connected From" meta box
3. Database structure

== Changelog ==

= 1.9.0 - 2020-02-19 =

- Add `link` parameter to `admin_column` which accepts `view` (default), `edit` or `false` (no link).
- Fix duplicated connections.

= 1.8.0 - 2020-01-05 =

**Added**

- Add support for reciprocal relationships
- Add `mb_relationships_registered` hook

= 1.7.1 - 2019-11-01 =

**Fixed**

- Fix creating table error due to empty collate

= 1.7.0 - 2019-09-18 =

**Added**

- Add a separate `field` array for field settings
- Add `order_from` and `order_to` to API::add method

= 1.6.1 - 2019-07-17 =

**Fixed**

- Make `hidden` param work for `to` side
- Remove duplicate from query

= 1.6.0 - 2019-02-25 =

**Added**

- Added support for querying by multiple relationships. See [docs](https://docs.metabox.io/extensions/mb-relationships/) for details.

**Changed**

- Extracted admin columns and meta boxes into their own classes
- Renamed files and classes for clarity

= 1.5.0 - 2018-12-19 =

**Fixed**

- Fixed incorrect order of items when changing order of connected items.

**Changed**

- Changed the database structure by adding `order_from` and `order_to` columns to track order of items.
- Removed reference to global `$wpdb` and use the global variable directly. This prevents serialize objects in some unexpected situations.

= 1.4.1 - 2018-10-26 =

**Fixed**

 - Fixed cannot query for posts excluded from search.
 - Fixed not showing entries in admin columns.

= 1.4.0 - 2018-08-24 =

**Added**

- Added 'closed' and 'autosave' param to relationship meta boxes.

**Fixed**

- Fixed indirect variable access in PHP 5.x.

= 1.3.2 - 2018-07-02 =

**Changed**

- Reverted the `'post_type' => 'any'` as it relates to many queries.

= 1.3.1 - 2018-07-02 =

**Changed**

- Remove `'post_type' => 'any'` in the query for relationship, which causes unexpected behaviour with post types that have `'exclude_from_search' => true`. Developers should always set `post_type` in their queries. See https://bit.ly/2lPvnPk.

= 1.3.0 - 2018-05-25 =

**Added**

- Added support for admin columns. See [documentation](https://docs.metabox.io/extensions/mb-relationships/) for details.

= 1.2.0 - 2018-04-27 =

**Added**

- Added API to get siblings items.

= 1.1.2 - 2018-03-08 =

**Fixed**

- Fixed output of related posts with the same order as in the backend.

= 1.1.1 - 2018-02-27 =

**Added**

- Made clones sortable

= 1.1.0 =

**Added**

- Added meta box for selecting 'from' objects
- Added CRUD API for relationships

= 1.0.0 =
- Initial version

== Upgrade Notice ==
