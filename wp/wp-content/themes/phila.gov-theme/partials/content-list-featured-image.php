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
  <a href="<?php echo the_permalink(); ?>" class="grid-x grid-margin-x hover-fade">
    <?php if ( has_post_thumbnail() ) : ?>
      <div class="cell medium-7">
        <?php echo phila_get_thumbnails(); ?>
      </div>
    <?php endif; ?>
    <div class="cell medium-17 grid-x bg-ghost-gray card card--<?php echo $label ?> pam">
      <div class="cell align-self-top post-label post-label--<?php echo $label?>">
        <i class="fa fa-<?php echo $icon ?> fa-lg" aria-hidden="true"></i> <span><?php echo $label_nice; ?></span>
        <header class="mvm">
          <h1><?php echo get_the_title(); ?></h1>
        </header>
      </div>
      <div class="cell align-self-bottom">
        <div class="post-meta">
          <span class="date-published"><time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time></span>
          <span class="departments"><?php echo phila_get_current_department_name( $categories, $byline = false, $break_tags = false, $name_list = true ); ?></span>
        </div>
      </div>
    </div>
  </a>
</article>
