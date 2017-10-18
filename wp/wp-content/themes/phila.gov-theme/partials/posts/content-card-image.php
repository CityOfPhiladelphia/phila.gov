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
    case 'announcement':
      $label_nice = 'Announcement';
      $icon = 'bullhorn';
      break;
    case 'featured':
      $label_nice = 'Featured';
      $icon = 'newspaper-o';
      break;
    case 'press_release':
      $label_nice = 'Press Release';
      $icon = 'file-text-o';
      break;
    case 'post':
      $label_nice = 'Post';
      $icon = 'pencil';
      break;
  }
  endif;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('full-height'); ?>>
  <a href="<?php echo the_permalink(); ?>" class="card card--<?php echo $label ?> flex-container flex-dir-row full-height">
    <div class="grid-x flex-dir-column">
      <div class="flex-child-shrink">
        <?php if ( has_post_thumbnail() ) : ?>
            <?php echo phila_get_thumbnails(); ?>
        <?php endif; ?>
      </div>
      <div class="card--content pam flex-child-auto">
        <div class="cell align-self-top post-label post-label--<?php echo $label?>">
          <i class="fa fa-<?php echo $icon ?> fa-lg" aria-hidden="true"></i> <span><?php echo $label_nice; ?></span>
          <header class="cell mbm">
            <h1><?php echo get_the_title(); ?></h1>
          </header>
        </div>
        <div class="cell align-self-bottom">
          <div class="post-meta">
            <span class="date-published"><time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time></span>
          </div>
        </div>
      </div>
    </div>
  </a>
</article>
