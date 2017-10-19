<?php
/**
 * The template used for displaying a featured image, description and link in a list
 *
 * @package phila-gov
 */
?>
<?php $categories = get_the_category($post->ID); ?>
<?php $desc = phila_get_item_meta_desc( ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('mbm'); ?>>
  <a href="<?php echo the_permalink(); ?>" class="grid-x grid-margin-x hover-fade faux-card">
    <?php if ( has_post_thumbnail() ) : ?>
      <div class="cell medium-7">
        <?php echo phila_get_thumbnails(); ?>
      </div>
    <?php endif; ?>
    <div class="cell medium-<?php echo (has_post_thumbnail() ) ? '17' : '24' ?> grid-x bg-ghost-gray card <?php echo isset($label) ? 'card--' . $label : '' ?> pam">
      <div class="cell align-self-top post-label <?php echo isset($label) ? 'post-label--' . $label : '' ?>">
        <i class="fa fa-<?php echo isset($icon) ? $icon : '' ?> fa-lg" aria-hidden="true"></i> <span><?php echo isset($label_nice) ? $label_nice : '' ?></span>
        <header class="mvm">
          <h1><?php echo get_the_title(); ?></h1>
        </header>
      </div>
      <div class="cell align-self-bottom">
        <div class="post-meta">
          <span class="date-published"><time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time></span>
        </div>
      </div>
    </div>
  </a>
</article>
