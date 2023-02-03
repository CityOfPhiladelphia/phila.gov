<?php 
/**
 * The template used for displaying a card in the series post template -- containing a large image, title, and link
 *
 * @package phila-gov
 */

$series_posts = rwmb_meta('phila_post_picker');
$label = 'post';
$label_arr = phila_get_post_label($label);

foreach( $series_posts as $collection_post_id ) {
  global $post;
  $post = get_post( $collection_post_id, OBJECT );
  setup_postdata( $post );
  ?>

  <div class="cell large-6 medium-8 mbxxl pbs series-card">
    <article id="post-<?php the_ID(); ?>" <?php post_class('full-height'); ?>>
      <a href="<?php echo the_permalink(); ?>" class="card card--<?php echo $label_arr['label'] ?> flex-container flex-dir-row full-height">
        <div class="grid-x flex-dir-column">
          <div class="flex-child-shrink">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php echo phila_get_thumbnails(); ?>
            <?php endif; ?>
          </div>
          <div class="card--content pvm flex-child-auto">
            <div class="cell align-self-top post-label post-label--<?php echo $label_arr['label']?>">
              <div><?php echo $label_arr['nice']; ?></div>
              <header class="cell mvm">
                <h1><?php echo get_the_title(); ?></h1>
              </header>
            </div>
            <div class="post-meta date-published">
              <time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time>
            </div>
          </div>
        </div>
      </a>
    </article>
  </div>

<?php } ?>