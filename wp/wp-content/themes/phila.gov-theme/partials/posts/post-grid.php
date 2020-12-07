<?php
/*
 * Display a grid of 3 posts
 *
*/
?>
<?php 
  $override = rwmb_meta('phila_get_post_cats');
  $override_url = isset($override['override_url']) ? $override['override_url'] : '';
  $post_categories = isset($category) ? $category : '';
  $override_url = isset($override['override_url']) ? $override['override_url'] : '';
  $is_tag = isset($is_spotlight_tag) ? $is_spotlight_tag : rwmb_meta('phila_get_post_cats');
  $tag = isset($is_tag['tag']) ? $is_tag['tag'] : $a['tag'];
  var_dump($post_categories);
?>
