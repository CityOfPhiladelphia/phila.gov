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
?>
<?php if (!empty($post_categories)): ?>
  <?php foreach ($post_categories as $category ) {
    $current_cat = get_the_category_by_ID($category);
    $slang_name = urlencode(html_entity_decode(trim(phila_get_department_homepage_typography( null, $return_stripped = true, $page_title = $current_cat ))));
  } ?>
<?php else: ?>
  <?php 
  $current_cat = '';
  $slang_name = '';
  ?>
  <?php endif; ?>
  
<?php 

/* if categories aren't set, this is the latest. */
if ( empty( $post_categories ) ) {
  //empty object + empty array for dealing with this grid but on the latest display
  $sticky_posts = (object) [
    'posts' => array(),
  ];
}else{
  /* Get all sticky posts for department homepages */
  $sticky_args = array(
    'posts_per_page' => -1,
    'post__in'  => get_option( 'sticky_posts' ),
    'cat' => $post_categories,
    'order' => 'desc',
    'orderby' => 'post_date',
    'meta_query'  => array(
      array(
        'key' => 'phila_template_select',
        'value' => 'post',
        'compare' => '=',
      )
    ),
  );

  $sticky_posts = new WP_Query( $sticky_args );

}

if( !empty($tag) && $tag != 'is_single' ) {
  $posts_args  = array(
    'post_type' => array('post'),
    'posts_per_page' => 3,
    'order' => 'desc',
    'orderby' => 'post_date',
    'tag__in'  => array($tag),
    'meta_query'  => array(
      'relation'  => 'AND',
      array(
        'key' => 'phila_template_select',
        'value' => 'post',
        'compare' => '=',
      ),
      array(
        'relation'  => 'OR',
        array(
          'key' => 'phila_select_language',
          'value' => 'english',
          'compare' => '=',
        ),
        array(
          'key' => 'phila_select_language',
          'compare' => 'NOT EXISTS'
        ),
      ),
    )
  );

  $result = new WP_Query( $posts_args );
}else{
  $posts_args  = array(
    'posts_per_page' => 3,
    'order' => 'desc',
    'orderby' => 'post_date',
    'cat' => $post_categories,
    'post__not_in'  => get_option( 'sticky_posts' ),
    'ignore_sticky_posts' => 1,
    'meta_query'  => array(
      'relation'  => 'AND',
      array(
        'key' => 'phila_template_select',
        'value' => 'post',
        'compare' => '=',
      ),
      array(
        'relation'  => 'OR',
        array(
          'key' => 'phila_select_language',
          'value' => 'english',
          'compare' => '=',
        ),
        array(
          'key' => 'phila_select_language',
          'compare' => 'NOT EXISTS'
        ),
      ),
    )
  );
  
  $result = new WP_Query( $posts_args );

  $more_posts = new WP_Query( $posts_args );

  $result = new WP_Query();
  //if sticky posts is empty, don't add it to the results array
  $result->posts = array_merge(isset($sticky_posts->posts) ? $sticky_posts->posts : array(), $more_posts->posts);

  var_dump($result);
  
}
$result->post_count = count( $result->posts );

?>
