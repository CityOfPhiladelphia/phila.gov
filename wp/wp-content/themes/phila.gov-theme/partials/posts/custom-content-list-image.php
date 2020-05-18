<?php
/**
 * The template used for displaying an image, description and link in a list
 *
 * @package phila-gov
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('mbm'); ?>>
  <a href="<?php echo the_permalink(); ?>" class="grid-x hover-fade faux-card custom cell medium-24">
    <?php if ( has_post_thumbnail() ) : ?>
      <div class="cell medium-4 small-6 pam card mtm">
        <?php if ( isset( $label_arr['nice'] ) ) : ?>
          <i class="<?php echo isset($label_arr['icon']) ? $label_arr['icon'] : '' ?> fa-lg fa-3x strong" aria-hidden="true"></i>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <div class="cell medium-20 small-18 grid-x card pam">
      <div class="cell align-self-top">
        <div>
          <span class="date-published"><time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time></span>
        </div>
      </div>
      <div class="cell align-self-bottom">
        <header class="mts">
          <p class="dark-ben-franklin strong"><?php echo get_the_title(); ?></p>
        </header>
      </div>
    </div>
  </a>
</article>
