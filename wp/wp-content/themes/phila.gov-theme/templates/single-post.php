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
$post_type = get_post_type();
$post_obj = get_post_type_object( $post_type );
$post_id = get_the_id();
$template_type = phila_get_selected_template();
$last_updated = rwmb_meta('is_last_updated');
$last_updated_date = rwmb_meta('last_updated_date');
$date_formatted = new DateTime($last_updated_date);
$last_updated_text = rwmb_meta('last_updated_text');
$translations = rwmb_meta('phila_translations');

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
      <div class="cell medium-6 align-self-bottom">
        <?php get_template_part('partials/social-media') ?>
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
          <?php if (sizeof($posted_on_values['author'] ) === 2 ) : ?>
            <?php echo implode(' and ', $posted_on_values['author']); ?>
          <?php else:?>
            <?php
              $last  = array_slice($posted_on_values['author'], -1);
              $first = join(', ', array_slice($posted_on_values['author'], 0, -1));
              $both  = array_filter(array_merge(array($first), $last), 'strlen');
              echo join(', and ', $both);
            ?>
          <?php endif;?>
          </span>
        <?php endif?>
        <span class="departments">
          <?php echo phila_get_current_department_name( $category, false, false ); ?>
        </span>
      <?php endif; ?>
  </div>
  <?php if ( $last_updated ): ?>
    <div class="last-updated mtm">
      <span class="last-updated-text ptm">Last updated:</span> <?php echo $date_formatted->format('F d, Y'); ?> 
      <?php echo !empty($last_updated_text) ? '<p>' . $last_updated_text . '</p>' : '' ?>
    </div>
  <?php endif; ?>
  </header>
  <?php if ( has_post_thumbnail() && ($template_type != 'action_guide') && ($template_type != 'press_release') ): ?>
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
    <?php 
      if ( !empty($translations) ) :
        foreach ($translations as $translation ) : ?>
        <?php 
        $lang = get_post_meta($translation, 'phila_select_language'); 
        $id = intval($translation);
        $link = get_the_permalink($id);
        ?>
          <a href="<?php echo $link ?>"><?php echo $lang[0] ?></a>
      <?php endforeach; ?>
    <?php endif; ?>
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
  <hr class="margin-auto"/>
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
