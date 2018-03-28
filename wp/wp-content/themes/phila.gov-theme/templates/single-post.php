<?php
/**
 * The content of a single post
 * Updated: 9/5/17
 * @package phila-gov
 */
?>
<?php
$category = get_the_category();
$posted_on_values = phila_get_posted_on();
$the_title =  get_the_title();
$email_title = urlencode(html_entity_decode($the_title));
$post_type = get_post_type();
$post_obj = get_post_type_object( $post_type );
$post_id = get_the_id();
$template_type = phila_get_selected_template();
$tweet_intent = rwmb_meta('phila_social_intent');
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('post img-floats'); ?>>
  <header class="post-header grid-container">
    <div class="grid-x grid-padding-x align-bottom">
      <div class="cell medium-18 post-title">
        <?php if ( $template_type == 'action_guide' ) : ?>
          <?php include( locate_template( 'partials/posts/action-guide-title.php' ) ); ?>
        <?php else:  ?>
          <?php the_title( '<h1>', '</h1>' ); ?>
        <?php endif; ?>
      </div>
      <div class="cell medium-6 align-self-bottom social-media">
        <a href="#" id="fb-share" data-analytics="social"><i class="fa fa-facebook" aria-hidden="true"></i></a>
        <a href="https://twitter.com/intent/tweet?text=<?php echo ( $tweet_intent != '' ) ? phila_encode_title(rwmb_meta('phila_social_intent') ) :  phila_encode_title( $the_title );?>&url=<?php echo get_permalink()?>"><i class="fa fa-twitter" aria-hidden="true"></i></a>
        <a href="mailto:?subject=<?php echo str_replace('+', '%20', $email_title) ?>&body=<?php echo get_permalink()?>" data-analytics="social"><i class="fa fa-envelope-o" aria-hidden="true"></i></a>
        <a href="javascript:window.print()" data-analytics="social"><i class="fa fa-print" aria-hidden="true"></i></a>
      </div>
      <div class="border-bottom-fat"></div>
    </div>
    <div class="post-meta">
      <?php if ( get_post_type() == 'press_release' || $template_type == 'press_release' ): ?>
        <div class="mbm">
          <?php get_template_part( 'partials/posts/press-release', 'meta' ); ?>
        </div>
      <?php else : ?>
        <span class="date-published">
          <?php echo $posted_on_values['time_string']; ?>
        </span>
        <?php if ( get_post_type() != 'news_post'): ?>
          <span class="author">
            <?php echo $posted_on_values['author']; ?>
          </span>
        <?php endif?>
        <span class="departments">
          <?php echo phila_get_current_department_name( $category, false, false ); ?>
        </span>
      <?php endif; ?>
    </div>
  </header>
  <?php if ( has_post_thumbnail() && ($template_type != 'action_guide') ): ?>
    <div class="grid-container featured-image">
      <div class="grid-x medium-16 medium-centered align-middle">
        <?php if( strpos(phila_get_thumbnails(), 'phila-thumb') || strpos(phila_get_thumbnails(), 'phila-news')  ) : ?>
          <div class="js-thumbnail-image">
            <div class="lightbox-link lightbox-link--feature" data-open="phila-lightbox-feature">
              <?php echo phila_get_thumbnails(); ?>
            </div>
          </div>
        <?php else : ?>
          <div class="lightbox-link lightbox-link--feature" data-open="phila-lightbox-feature">
            <?php echo phila_get_thumbnails(); ?>
          </div>
        <?php endif;?>
      </div>
    </div>
  <?php endif ?>
  <div class="grid-container post-content">
    <div class="medium-18 medium-centered mtm">
      <?php the_content(); ?>
      <?php include(locate_template ('partials/posts/post-end-cta.php') ); ?>
    </div>
    <?php if ( get_post_type() == 'press_release' || $template_type == 'press_release' ) : ?>
      <div class="mvm center">###</div>
    <?php endif; ?>
    <?php if ( $template_type == 'action_guide' ) : ?>
      <?php include(locate_template ('partials/posts/action-guide-content.php') ); ?>
    <?php endif; ?>
  </div>
  <hr />
</article>

<?php wp_reset_postdata(); ?>
<?php
  $cat_ids = array();

  foreach( $category as $cat ){
    array_push( $cat_ids, $cat->cat_ID );
  }

  $cat_id_string = implode( ', ', $cat_ids );

  $related_post_type = array( $post_type );
  $posts_per = 3;

  //fallback for old post types
  if( $template_type == 'phila_post' || $template_type == 'post' ) {
    array_push( $related_post_type, 'phila_post' );

  }elseif( $template_type == 'press_release' ) {
    array_push($related_post_type, 'press_release' );
    $posts_per = 4;
  }

  $related_content_args = array(
    'post_type' => $related_post_type,
    'category__and' => array($cat_id_string),
    'posts_per_page'  => $posts_per,
    'post__not_in'  => array($post_id),
    'meta_query' => array(
      array(
        'key'     => 'phila_template_select',
        'value'   => $template_type,
        'compare' => '=',
      ),
    ),
  );

  if ( ($post_type == 'press_release' || $template_type == 'press_release') ) {
    $is_press_release = true;
    $label = 'press_release';
    $count = 4;
    $category = array($cat_id_string);

    $template = 'partials/posts/press-release-grid.php';
  }elseif($template_type == 'action_guide'){
    $template = 'partials/posts/action-guide-grid.php';

  }else{
    $template = 'partials/posts/content-related.php';
  }
?>
<div class="grid-container">
    <?php include( locate_template( $template ) ); ?>
</div>


<div id="phila-lightbox-feature" data-reveal class="reveal reveal--auto center"></div>
<div id="phila-lightbox" data-reveal class="reveal reveal--auto center"></div>
