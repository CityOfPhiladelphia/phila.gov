<?php

/**
* @since 0.5.11
* Rewrite rules for news, browse
* /news/category renders filtered news archive page
* /browse/topicname renders filtered topics page
* @uses https://codex.wordpress.org/Rewrite_API/add_rewrite_rule
* @package phila-gov_customization
*/

add_action('init','phila_news_rewrite');

function phila_news_rewrite() {

  add_rewrite_rule("^news/([^/]+)/([^/]+)/?$",'index.php?post_type=news_post&category_name=$matches[1]&news_post=$matches[2]','top');

  add_rewrite_rule("^news/([^/]+)/?$",'index.php?post_type=news_post&category_name=$matches[1]','top');

  add_rewrite_rule("^news/([^/]+)/page/?([0-9]{1,})/?$",'index.php?post_type=news_post&category_name=$matches[1]&paged=$matches[2]','top');

  add_rewrite_rule("^notices/([^/]+)/([^/]+)/?$",'index.php?post_type=notices&category_name=$matches[1]&notices=$matches[2]','top');

  add_rewrite_rule("^notices/([^/]+)/?$",'index.php?post_type=notices&category_name=$matches[1]','top');

  add_rewrite_rule("^notices/([^/]+)/page/?([0-9]{1,})/?$",'index.php?post_type=notices&category_name=$matches[1]&paged=$matches[2]','top');

  add_rewrite_rule("^posts/([^/]+)/([0-9]{4,})-([0-9]{2,})-([0-9]{2,})-([^/]+)?$",'index.php?post_type=phila_post&category_name=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&phila_post=$matches[5]','top');

  add_rewrite_rule("^posts/([^/]+)/?$",'index.php?post_type=phila_posts&category_name=$matches[1]','top');

  add_rewrite_rule("^posts/([^/]+)/page/?([0-9]{1,})/?$",'index.php?post_type=phila_psots&category_name=$matches[1]&paged=$matches[2]','top');

  add_rewrite_rule("^browse/([^/]+)/([^/]+)/?$",'index.php?&topics=$matches[1]&topics=$matches[2]','top');
}

add_action( 'init', 'phila_register_rewrite_tag', 0, 10);

function phila_register_rewrite_tag() {
  add_rewrite_tag( '%topics%', '([^/]+)');
}
