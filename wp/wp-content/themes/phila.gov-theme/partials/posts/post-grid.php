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
  var_dump('test');
  var_dump($post_categories);
?>
<?php if (!empty($post_categories)){ ?>
  <?php var_dump('checker'); ?>
  <?php foreach ($post_categories as $category ) {
    var_dump($category);
    var_dump(get_the_category_by_ID($category));
    var_dump(urlencode(html_entity_decode(trim(phila_get_department_homepage_typography( null, $return_stripped = true, $page_title = $current_cat )))));
  }
 } // end if
?>
