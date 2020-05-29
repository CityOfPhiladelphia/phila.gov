<?php
/**
 * The template used for displaying a card, containing a title and link
 * Variables to pass to this file include:
 * $count, $label
 *
 * @package phila-gov
 */
?>

<?php $label_arr = phila_get_post_label($label);
  $article_classes = 'flex-child-auto '; ?>
<?php if ($count === 3 && $label_arr['label'] === 'featured') : ?>
  <?php $is_last = true; ?>
<?php endif; ?>

<?php if ($count == 4 && $label_arr['label'] == 'press_release') : ?>
  <?php $is_last = true; ?>
<?php endif; ?>
<?php if ($label_arr['label'] == 'press_release') :
  $article_classes .= 'type-press_release';
  $article_classes .= isset($is_last) ? ' card--last' : '';
  endif; ?>
<?php if ( $label_arr['label'] == 'announcement' ) : ?>
  <?php $is_last = false; ?>
<?php endif;?>
<?php $last = isset($is_last) ? true : false; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $article_classes ); ?>>
  <div class="custom grid-x custom cell medium-24 card <?php echo ($count == $total) ? 'card--last' : '' ?>  card--<?php echo $label_arr['label'] ?>">
    <div class="cell medium-2 small-6 pam card mtm">
      <i class="<?php echo isset($label_arr['icon']) ? $label_arr['icon'] : '' ?> fa-lg fa-3x strong" aria-hidden="true"></i>
    </div>
    <div class="cell medium-22 small-18 grid-x card pam">
      <div class="cell align-self-top">
        <div>
          <span class="date-published"><time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time></span>
        </div>
      </div>
      <div class="cell align-self-bottom">
        <header class="mts">
          <a class="dark-ben-franklin strong hover-fade" <?php echo ($label_arr['label'] !== 'announcement') ? 'href=' . get_permalink() : '' ?> <?php echo ($label_arr['label'] == 'announcement') ? 'data-open="announcement-' . get_the_ID() .'"' : ''?>>
            <?php echo get_the_title(); ?>
          </a>
        </header>
      </div>
    </div>
  </div>
</article>
