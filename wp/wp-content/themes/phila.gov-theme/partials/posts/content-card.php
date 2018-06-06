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
<?php if ($count == 3 && $label_arr['label'] == 'featured') : ?>
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
  <a <?php echo ($label_arr['label'] !== 'announcement') ? 'href=' . get_permalink() : '' ?> class="card card--<?php echo $label_arr['label'] ?> <?php echo ($last && $label_arr['label'] !== 'announcement') ? 'card--last' : ''; ?> pam" <?php echo ($label_arr['label'] == 'announcement') ? 'data-open="announcement-' . get_the_ID() .'"' : ''?>>
    <div class="grid-x flex-dir-column card--content">
      <div class="cell align-self-top post-label post-label--<?php echo $label_arr['label']?>">
        <i class="fa fa-<?php echo $label_arr['icon'] ?> fa-lg" aria-hidden="true"></i> <span><?php echo $label_arr['nice']; ?></span>
        <header class="mvm">
          <h1><?php echo get_the_title(); ?></h1>
        </header>
      </div>
      <div class="cell align-self-bottom">
        <div class="post-meta">
          <span class="date-published"><time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time></span>
          <?php if( $label_arr['label'] != 'featured') : ?>
            <!--<span class="departments"><?php //echo phila_get_current_department_name( $categories, $byline = false, $break_tags = false, $name_list = true ); ?></span> -->
          <?php endif; ?>
        </div>
      </div>
    </div>
  </a>
</article>
