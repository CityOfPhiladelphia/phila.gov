<?php
/*
 * Display a stack of 3 posts
 *
*/
?>
<?php
  $override = rwmb_meta('phila_get_post_cats');
  $override_url = isset($override['override_url']) ? $override['override_url'] : '';
  $post_categories = isset($category) ? $category : '';
  $override_url = isset($override['override_url']) ? $override['override_url'] : '';

  if (!is_page_template('templates/the-latest.php') ) {
    $is_tag = isset($is_spotlight_tag) ? $is_spotlight_tag : rwmb_meta('phila_get_post_cats');
    $tag = isset($is_tag['tag']) ? $is_tag['tag'] : $a['tag'];

  }

?>
<?php if (!empty($post_categories)): ?>
  <?php foreach ($post_categories as $category ) {
    $current_cat = get_category($category);
    $slang_name = urlencode(html_entity_decode(trim( phila_get_owner_typography( $current_cat ))));
  } ?>
<?php else: ?>
  <?php
  $current_cat = null;
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
    set_transient( get_the_ID().'_sticky_posts_results', $sticky_posts, 1 * HOUR_IN_SECONDS );
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
    set_transient( get_the_ID().'_default_posts_results', $result, 1 * HOUR_IN_SECONDS );
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
      ),
    )
  );

  if ( false === ( $more_posts = get_transient( get_the_ID().'_more_posts_results' ) ) ) {
    $more_posts = new WP_Query( $posts_args );
    set_transient( get_the_ID().'_more_posts_results', $more_posts, 1 * HOUR_IN_SECONDS );
  }

  if ( false === ( $result = get_transient( get_the_ID().'_empty_posts_results' ) ) ) {
    $result = new WP_Query();
    set_transient( get_the_ID().'_empty_posts_results', $result, 1 * HOUR_IN_SECONDS );
  }


  //if sticky posts is empty, don't add it to the results array
  $result->posts = array_merge(isset($sticky_posts->posts) ? $sticky_posts->posts : array(), $more_posts->posts);

}
$result->post_count = count( $result->posts );

?>

<?php $user_selected_template = phila_get_selected_template(); ?>
<?php $post_type_parent = get_post_type($post->ID); ?>

<div class="columns">
    <h2 id="project-posts-title" >Posts</h2>
</div>

<div class="project-posts columns pbxl">
  <div class="mbl">
    <?php
      if ( !empty( $result->posts ) ) :
        global $post;
        $post_counter = 0;
        foreach ( $result->posts as $post ) :
          setup_postdata( $post );
          $post_counter++;
          if ( $post_counter > 3 ) break;
    ?>
      <article id="post-<?php the_ID(); ?>" class="mbm prm flex-container row">
        <?php if ( has_post_thumbnail() ) : ?>
          <div class="columns medium-4">
            <?php echo phila_get_thumbnails(); ?>
          </div>
        <?php endif; ?>
        <a href="<?php the_permalink(); ?>" class="card flex-dir-row full-height columns medium-20">
          <div class="grid-x flex-dir-column">
            <div class="card--content prm pvm flex-child-auto">
              <div class="cell align-self-top post-label">
                <header class="cell mvs">
                  <h3><?php the_title(); ?></h3>
                </header>
              </div>
              <div class="cell align-self-bottom">
                <div class="post-meta">
                  <span class="date-published">
                    <time datetime="<?php echo get_post_time( 'Y-m-d' ); ?>">
                      <?php echo get_the_date(); ?>
                    </time>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </a>
      </article>
    <?php
        endforeach;
        wp_reset_postdata();
      else :
    ?>
      <p>No posts found.</p>
    <?php endif; ?>
  </div>
  <?php
  $see_all = array(
    'URL' => '/the-latest/archives/?templates=posts&department=' . $slang_name,
    'content_type' => 'posts',
    'nice_name' => 'Posts',
); 
include(locate_template('partials/content-see-all.php')); ?>
</div>
