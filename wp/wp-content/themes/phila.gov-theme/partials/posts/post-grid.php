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

  if ( false === ( $sticky_posts = get_transient( get_the_ID().'_sticky_posts_results' ) ) ) {
    $sticky_posts = new WP_Query( $sticky_args );
    set_transient( get_the_ID().'_sticky_posts_results', $sticky_posts, 12 * HOUR_IN_SECONDS );
  }
  

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
  if ( false === ( $result = get_transient( get_the_ID().'_default_posts_results' ) ) ) {
    $result = new WP_Query( $posts_args );
    set_transient( get_the_ID().'_default_posts_results', $result, 12 * HOUR_IN_SECONDS );
  }

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

  if ( false === ( $more_posts = get_transient( get_the_ID().'_more_posts_results' ) ) ) {
    $more_posts = new WP_Query( $posts_args );
    set_transient( get_the_ID().'_more_posts_results', $more_posts, 12 * HOUR_IN_SECONDS );
  }

  if ( false === ( $result = get_transient( get_the_ID().'_empty_posts_results' ) ) ) {
    $result = new WP_Query();
    set_transient( get_the_ID().'_empty_posts_results', $result, 12 * HOUR_IN_SECONDS );
  }

  
  //if sticky posts is empty, don't add it to the results array
  $result->posts = array_merge(isset($sticky_posts->posts) ? $sticky_posts->posts : array(), $more_posts->posts);
  
}
$result->post_count = count( $result->posts );

?>

<?php $user_selected_template = phila_get_selected_template(); ?>
<?php $post_type_parent = get_post_type($post->ID); ?>

<?php $count = 0; ?>
<?php $label = 'post'; ?>
<div class="post-grid">
  <div class="grid-container mbm">
    <?php if ( $result->have_posts() ) : ?>
      <?php include( locate_template( 'partials/posts/post-translated-langs-see-all.php' ) ); ?>
        <div class="grid-x grid-margin-x align-stretch">
        <?php $total = $result->post_count; ?>
        <?php $label_arr = phila_get_post_label('post'); ?>
        <?php while ( $result->have_posts() ) : $result->the_post(); ?>
          <?php $post_type = get_post_type(); ?>
          <?php $post_obj = get_post_type_object( $post_type ); ?>
          <?php $count++; ?>
          <?php if( $user_selected_template === 'custom_content' || $post_type_parent === 'guides' ): ?>
            <div class="cell medium-24 align-self-stretch post-<?php echo $count ?>">
              <?php include( locate_template( 'partials/posts/content-list-icon.php' ) ); ?>
            </div>
          <?php else: ?>
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
          <?php endif; ?>
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
              <?php if( !empty( $tag ) && $tag != 'is_single' ) :
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
          <?php if ($user_selected_template == 'custom_content' || $post_type_parent === 'guides'): ?>
            </div>
            <div class='custom'>
              <?php include( locate_template( 'partials/custom-content-see-all.php' ) ); ?>
            </div>
          <?php else: ?>
            <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    <?php endif;?>
  <?php wp_reset_postdata(); ?>
</div>
