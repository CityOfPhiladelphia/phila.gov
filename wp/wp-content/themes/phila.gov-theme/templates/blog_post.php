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
$archived_state = 0; //default state of not archived
$archived = rwmb_meta('phila_archive_post');
$post_is_old = false;
if (date('Y-m-d', strtotime('-2 years')) > $post->post_date) { // if posts are 2 years old
  $post_is_old = true;
}
if ((empty( $archived ) || !isset($archived) || $archived == 'default') &&  $post_is_old)  {
  $archived_state = 1; //archived after two years
} else if ($archived == 'archive_now') {
  $archived_state = 2; //archived manually
}
$connected = new WP_Query( [
    'relationship' => [
        'id'   => 'series_to_post_relationship',
        'from' => get_the_ID(), 
    ],
    'nopaging'     => true,
] );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post img-floats'); ?>>
  <header class="post-header grid-container">
    <div class="cell medium-6 align-self-bottom">
      <?php get_template_part('partials/social-media') ?>
    </div>
    <div class="grid-x grid-padding-x align-bottom">
      <div class="cell medium-24 post-title">
        <?php if ( $archived_state !== 0 ) : ?>
            <div class="archived-tag">Archived</div>
          <?php endif; ?>
          <?php the_title( '<h1 style="display:inline">', '</h1>' ); ?>
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
  <?php if ( $last_updated || $archived_state == 2 ): ?>
    <div class="grid-x">
      <div class="cell shrink last-updated icon hide-for-small-only">
          <i class="fas fa-clock-rotate-left fa-2x pam"></i>
      </div>
      <div class="cell auto last-updated">
        <div class="content">
          <span class="last-updated-text">Last updated <?php echo $date_formatted->format('F d, Y'); ?></span>
          <?php if ( $archived_state == 2 ): ?>
          <p> This post was reviewed and manually archived because some of the content is out of date. </p>
          <?php endif; ?>
          <?php echo !empty($last_updated_text) ?  apply_filters('the_content', $last_updated_text) : ''; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
  </header>


  <?php if ( has_post_thumbnail() && ($template_type != 'press_release') ): ?>
    <div class="grid-container featured-image">
      <div class="grid-x medium-16 medium-centered align-middle">
        <?php if( strpos(phila_get_thumbnails(), 'phila-thumb') || strpos(phila_get_thumbnails(), 'phila-news')  ) : ?>
          <div class="js-thumbnail-image">
            <div class="lightbox-link lightbox-link--feature" data-open="phila-lightbox-feature">
              <?php echo phila_get_thumbnails(); ?>
              <?php $image_caption = get_post(get_post_thumbnail_id())->post_excerpt; ?>
              <?php $image_credit = get_post_meta(get_post_thumbnail_id())['phila_media_credit'][0]; ?>
              <?php if ($image_caption || $image_credit) { ?>
                <div class="phila-image-caption pam">
                  <?php if ($image_credit) { ?>
                    <p><strong>Photo by: <?php echo $image_credit; ?></strong></p>
                  <?php } ?>
                  <?php if ($image_caption) { ?>
                    <p><?php echo $image_caption; ?></p>
                  <?php } ?>
                </div>
              <?php } ?>
            </div>
          </div>
        <?php else : ?>
          <div class="lightbox-link lightbox-link--feature" data-open="phila-lightbox-feature">
            <?php echo phila_get_thumbnails(); ?>
            <?php $image_caption = get_post(get_post_thumbnail_id())->post_excerpt; ?>
            <?php $image_credit = get_post_meta(get_post_thumbnail_id())['phila_media_credit'][0]; ?>
            <?php if ($image_caption || $image_credit) { ?>
              <div class="phila-image-caption pam">
                <?php if ($image_credit) { ?>
                  <p><strong>Photo by: <?php echo $image_credit; ?></strong></p>
                <?php } ?>
                <?php if ($image_caption) { ?>
                  <p><?php echo $image_caption; ?></p>
                <?php } ?>
              </div>
            <?php } ?>
          </div>
        <?php endif;?>
      </div>
    </div>
  <?php endif ?>
  <div class="grid-container post-content <?php echo $language == 'arabic' ? $language : '' ?>">
  <?php if ($template_type != 'series') {
      while ( $connected->have_posts() ) : $connected->the_post(); 
        $content = get_post_field('phila_series_linking_text', $connected->the_ID(), $context = 'display'); ?>
  <div class="series-blockquote mbm mtm"><blockquote><span><?php echo $content ?> <i><a href="<?php echo the_permalink();?>">link to series</a></i></span></blockquote></div>
  <?php endwhile;
  } 
    wp_reset_postdata();
  ?>
    <div>
      <div class="mtm mbm">
        <?php the_content(); ?>
      </div>
      <?php if ( $template_type == 'advanced_post' ) { ?>
        <?php include(locate_template ('partials/posts/advanced-post-content.php') ); ?> 
      <?php } ?>
      <?php if ( $template_type == 'series' ) { ?>
        <?php include(locate_template ('partials/posts/phila-series.php') ); ?> 
      <?php } ?>
      <?php include(locate_template ('partials/posts/post-end-cta.php') ); ?>
    </div>
    <?php if ( get_post_type() == 'press_release' || $template_type == 'press_release' ) : ?>
      <div class="mvm center">###</div>
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
  }else{
    $template = 'partials/posts/content-related.php';
  }
?>
<div class="grid-container <?php echo $language == 'arabic' ? $language : '' ?>">
    <?php include( locate_template( $template ) ); ?>
</div>


<div id="phila-lightbox-feature" data-reveal class="reveal reveal--auto center"></div>
<div id="phila-lightbox" data-reveal class="reveal reveal--auto center"></div>