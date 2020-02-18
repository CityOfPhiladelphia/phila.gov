<?php
/*
 * Display a grid of 3 posts
 *
*/
?>
<?php $post_categories = isset($category) ? $category : ''; ?>
<?php if (!empty($post_categories)): ?>
  <?php foreach ($post_categories as $category ) {
    $current_cat = get_the_category_by_ID($category);
    $slang_name = html_entity_decode(trim(phila_get_department_homepage_typography( null, $return_stripped = true, $page_title = $current_cat )));
  } ?>
<?php else: ?>
  <?php 
  $current_cat = '';
  $slang_name = '';
  ?>
  <?php endif; ?>
<?php $tag = isset($tag) ? $tag : '';?>
<?php

/* Get all sticky posts for department homepages */
$sticky = get_option( 'sticky_posts' );

/* if categories aren't set, this is the latest. */
if ( empty( $post_categories ) ) {
  //empty object + empty array for dealing with this grid but on the latest display
  $sticky_posts = (object) [
    'posts' => array(),
  ];
}else{
  /* Sort sticky posts, newest at the top */
  rsort( $sticky );

  /* Get top 3 sticky posts, we could only have 3 max */
  $sticky = array_slice( $sticky, 0, 3 );
  $sticky_args = array(
    'posts_per_page' => -1,
    'post__in'  => $sticky,
    'cat' => $post_categories,
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

$phila_posts_args  = array(
  'posts_per_page' => 3,
  'post_type' => array( 'phila_post' ),
  'order' => 'desc',
  'orderby' => 'post_date',
  'cat' => $post_categories,
); ?>

<?php

if( !empty($tag) ) {
  $posts_args  = array(
    'post_type' => array('post', 'phila_post'),
    'posts_per_page' => 3,
    'order' => 'desc',
    'orderby' => 'post_date',
    'tag__in'  => $tag,
    'meta_query'  => array(
      'relation'  => 'AND',
      array(
        'key' => 'phila_template_select',
        'value' => 'press_release',
        'compare' => '!=',
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
    'post__not_in'  => $sticky,
    'ignore_sticky_posts' => 1,
    'meta_query'  => array(
      'relation'  => 'AND',
      array(
        'key' => 'phila_template_select',
        'value' => 'post',
        'compare' => '=',
      ),
    )
  );
  $posts = new WP_Query( $posts_args );
  $phila_posts = new WP_Query( $phila_posts_args );

  $result = new WP_Query();
  //if sticky posts is empty, don't add it to the results array
  $result->posts = array_merge( isset($sticky[0]) ? $sticky_posts->posts : array(), $posts->posts, $phila_posts->posts );
}

$result->post_count = count( $result->posts );

?>

<?php $count = 0; ?>
<?php $label = 'post'; ?>
<div class="post-grid">
  <div class="grid-container mbm">
    <?php if ( $result->have_posts() ) : ?>
      <?php if (!is_page_template('templates/the-latest.php')): ?>
        <h2>Posts</h2>
      <?php endif; ?>
      <div class="grid-x grid-margin-x align-stretch">
        <?php $total = $result->post_count; ?>
        <?php $label_arr = phila_get_post_label('post'); ?>
        <?php while ( $result->have_posts() ) : $result->the_post(); ?>
          <?php $post_type = get_post_type(); ?>
          <?php $post_obj = get_post_type_object( $post_type ); ?>
          <?php $count++; ?>
          <?php if ($total >= 3 ): ?>
            <?php if ($count == 1 ): ?>
              <div class="cell medium-16 align-self-stretch post-<?php echo $count ?>">
              <?php include( locate_template( 'partials/posts/content-card-image.php' ) ); ?>
            <?php elseif( $count == 2 ):?>
              <div class="cell medium-8 align-self-stretch post-<?php echo $count ?>">
              <?php include( locate_template( 'partials/posts/content-card-image.php' ) ); ?>
            <?php elseif( $count == 3 )  : ?>
              </div>
            </div>
            <div class="grid-container">
              <div class="grid-x grid-margin-x">
                <div class="cell medium-24 post-<?php echo $count ?>">
                  <?php include( locate_template( 'partials/posts/content-list-image.php' ) ); ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
          <?php elseif ( $count == 1 || $count == 2) : ?>
              <div class="cell medium-24">
                <?php include( locate_template( 'partials/posts/content-list-image.php' ) ); ?>
              </div>
          <?php endif;?>

        <?php endwhile; ?>
        <?php if ($count >= 3 ): ?>
        <div class="grid-container group">
            <?php $see_all = array(
              'URL' => '/the-latest/archives/#/?templates=post&templates=featured',
              'content_type' => $label,
              'nice_name' => 'posts'
            ); ?>
            <?php if( !empty( $post_categories ) ) :?>
              <?php $see_all_URL = array(
                'URL' => '/the-latest/archives/#/?templates=post&templates=featured&department=' . $slang_name,
              );
              $see_all = array_replace( $see_all, $see_all_URL );
              endif;?>
              <?php if( !empty( $tag ) ) :
                  if( gettype($tag) === 'array'):
                    $term = [];
                    foreach($tag as $t) {
                      $name = get_term($t, 'post_tag');
                      array_push($term, $name->name);
                    }
                    $term = implode(', ', $term);
                    $see_all_URL = array(
                      'URL' => '/the-latest/archives/#/?tag=' . $term,
                    );
                  else: 
                    $term = get_term($tag, 'post_tag');
                    $see_all_URL = array(
                      'URL' => '/the-latest/archives/#/?tag=' . $term->name,
                    );
                  endif; ?>
              <?php if (!empty($override_url)) : ?>
              <?php $see_all_URL = array(
                  'URL' => $override_url
                ); ?>
              <?php endif; ?>
              <?php $see_all = array_replace($see_all, $see_all_URL );
                endif;?>
          <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
        </div>
      <?php endif; ?>
    <?php endif;?>
  <?php wp_reset_postdata(); ?>
</div>
