<?php
/**
 * The template used for displaying an image, description and link in a list
 *
 * @package phila-gov
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('mbm'); ?>>
  <a href="<?php echo the_permalink(); ?>" class="grid-x hover-fade faux-card cell medium-24">
    <?php if ( has_post_thumbnail() ) : ?>
      <div class="cell medium-8 prm">
        <?php echo phila_get_thumbnails(); ?>
      </div>
    <?php endif; ?>
    <div class="cell medium-<?php echo (has_post_thumbnail() ) ? '16' : '24' ?> grid-x bg-ghost-gray card <?php echo isset($label_arr['label']) ? 'card--' . $label_arr['label'] : '' ?> pam">
      <div class="cell align-self-top post-label <?php echo isset($label_arr['label']) ? 'post-label--' . $label_arr['label'] : '' ?>">
        <?php if ( isset( $label_arr['nice'] ) ) : ?>
          <i class="fa fa-<?php echo isset($label_arr['icon']) ? $label_arr['icon'] : '' ?> fa-lg" aria-hidden="true"></i> <span><?php echo isset($label_arr['nice']) ? $label_arr['nice'] : '' ?></span>
        <?php endif; ?>
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
