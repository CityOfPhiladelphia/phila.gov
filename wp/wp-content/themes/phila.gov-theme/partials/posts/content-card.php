<?php
/**
 * The template used for displaying a card, containing a title and link
 * Variables to pass to this file include:
 * $count, $label
 *
 * @package phila-gov
 */
?>
<?php $categories = get_the_category($post->ID); ?>
<?php $desc = phila_get_item_meta_desc( ); ?>

<?php
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
  $article_classes = 'flex-child-auto ';
?>
<?php if ($count == 3 && $label == 'featured') : ?>
  <?php $is_last = true; ?>
<?php endif; ?>

<?php if ($count == 4 && $label == 'press_release') : ?>
  <?php $is_last = true; ?>
<?php endif; ?>
<?php if ($label == 'press_release') {
  $article_classes .= 'type-press_release';
  $article_classes .= isset($is_last) ? ' card--last' : '';
}?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $article_classes ); ?>>
  <a href="<?php echo ($label !== 'announcement') ? the_permalink() : '#' ?>" class="card card--<?php echo $label ?> <?php echo isset($is_last) ? 'card--last' : ''; ?> pam" <?php echo ($label == 'announcement') ? 'data-open="announcement-' . get_the_ID() .'"' : ''?>>
    <div class="grid-x flex-dir-column card--content">
      <div class="cell align-self-top post-label post-label--<?php echo $label?>">
        <i class="fa fa-<?php echo $icon ?> fa-lg" aria-hidden="true"></i> <span><?php echo $label_nice; ?></span>
        <header class="mvm">
          <h1><?php echo get_the_title(); ?></h1>
        </header>
      </div>
      <div class="cell align-self-bottom">
        <div class="post-meta">
          <span class="date-published"><time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time></span>
          <?php if( $label != 'featured') : ?>
            <!--<span class="departments"><?php //echo phila_get_current_department_name( $categories, $byline = false, $break_tags = false, $name_list = true ); ?></span> -->
          <?php endif; ?>
        </div>
      </div>
    </div>
  </a>
</article>

<?php if($label == 'announcement') : ?>
  <div id="announcement-<?php the_ID(); ?>" class="reveal reveal--<?php echo $label?>" data-reveal>
    <button class="close-button" data-close aria-label="Close modal" type="button">
      <span aria-hidden="true">&times;</span>
    </button>
    <div class="post-label post-label--<?php echo $label?>">
      <i class="fa fa-<?php echo $icon ?> fa-lg" aria-hidden="true"></i> <span><?php echo $label_nice; ?></span>
    </div>
    <header class="mvm">
      <h1><?php echo get_the_title(); ?></h1>
    </header>
    <?php echo get_the_content(); ?>
    <div class="post-meta mtxl">
      <span class="date-published">Announced on: <time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time></span>
      <span class="departments"><?php echo phila_get_current_department_name( $categories, $byline = false, $break_tags = false ); ?></span>
    </div>
  </div>
<?php endif; ?>
