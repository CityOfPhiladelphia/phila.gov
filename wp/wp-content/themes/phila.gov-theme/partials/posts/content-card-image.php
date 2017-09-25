<?php
/**
 * The template used for displaying a card -- containing a large image, title, and link
 *
 * @package phila-gov
 */
?>

<?php $categories = get_the_category($post->ID); ?>
<?php $desc = phila_get_item_meta_desc( ); ?>

<?php
//TODO: turn this into function
if ( isset( $label ) ) :
  switch( $label ) {
    case 'press-release':
      $label_nice = 'Press Release';
      $icon = 'file-text-o';
      break;
    case 'featured':
      $label_nice = 'Featured';
      $icon = 'newspaper-o';
      break;
  }
  endif;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <a href="<?php echo the_permalink(); ?>" class="card card--<?php echo $label ?>">
    <?php if ( has_post_thumbnail() ) : ?>
        <?php echo phila_get_thumbnails(); ?>
    <?php endif; ?>
    <div class="pam">
      <div class="post-label post-label--<?php echo $label?>">
        <i class="fa fa-<?php echo $icon ?> fa-lg" aria-hidden="true"></i> <span><?php echo $label_nice; ?></span>
      </div>
        <header class="mbm">
          <h1><?php echo get_the_title(); ?></h1>
        </header>
      <div class="align-self-bottom">
        <div class="post-meta">
          <span class="date-published"><time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time></span>
        </div>
      </div>
    </div>
  </a>
</article>
