<?php
/*
 * Display a grid of 3 posts
 *
*/
?>
<?php $post_categories = isset($category) ? $category : ''; ?>
<?php $event_tags = isset($event_tags) ? $event_tags : ''; ?>

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
if($event_tags) {
  $event_tag_query = array(
      'taxonomy' => 'event_tags',
      'field' => 'term_id',
      'terms' => $event_tags,
  );
}else{
  $event_tag_query = array();
}


$posts_args  = array(
  'posts_per_page' => 3,
  'order' => 'desc',
  'orderby' => 'post_date',
  'cat' => $post_categories,
  'post__not_in'  => $sticky,
  'ignore_sticky_posts' => 1,
  'tax_query' => array($event_tag_query),
  'meta_query'  => array(
    'relation'  => 'AND',
    array(
      'key' => 'phila_template_select',
      'value' => 'post',
      'compare' => '=',
    ),
  )
);

$phila_posts_args  = array(
  'posts_per_page' => 3,
  'post_type' => array( 'phila_post' ),
  'order' => 'desc',
  'orderby' => 'post_date',
  'cat' => $post_categories,
  'tax_query' => array($event_tag_query),
); ?>

<?php
  $posts = new WP_Query( $posts_args );

  $phila_posts = new WP_Query( $phila_posts_args );

  $result = new WP_Query();

  //if sticky posts is empty, don't add it to the results array
  $result->posts = array_merge( isset($sticky[0]) ? $sticky_posts->posts : array(), $posts->posts, $phila_posts->posts );

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
        <?php while ( $result->have_posts() ) : $result->the_post(); ?>
          <?php $post_type = get_post_type(); ?>
          <?php $post_obj = get_post_type_object( $post_type ); ?>
          <?php $count++; ?>
          <?php if ($count <= 3 ): ?>
            <?php if ($count == 1 ): ?>
              <div class="cell medium-16 align-self-stretch post-<?php echo $count ?>">
              <?php include( locate_template( 'partials/posts/content-card-image.php' ) ); ?>
            <?php elseif( $count == 2 ):?>
              <div class="cell medium-8 align-self-stretch post-<?php echo $count ?>">
              <?php include( locate_template( 'partials/posts/content-card-image.php' ) ); ?>
            <?php else : ?>
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
          <?php endif; ?>

        <?php endwhile; ?>
        <div class="grid-container">
          <?php $see_all = array(
            'URL' => '/the-latest/archives/?template=post',
            'content_type' => $label,
            'nice_name' => 'posts'
          ); ?>
          <?php if( !empty( $post_categories ) ) :
            $see_all_URL = array(
              'URL' => '/the-latest/archives/?template=post&category=' . $post_categories[0],
            );
            $see_all = array_replace($see_all, $see_all_URL );
            endif;?>
          <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
        </div>
      <?php endif; ?>
      <?php wp_reset_postdata(); ?>
</div>
