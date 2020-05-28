<?php
/**
 * The template used for displaying an image, description and link in a list
 *
 * @package phila-gov
 */
?>

<?php if ($user_selected_template === 'custom_content' || $post_type_parent === 'guides'): ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class('mbm'); ?>>
    <div class="grid-x faux-card custom cell medium-24 <?php echo ($count == $total) ? 'card--last' : '' ?>">
      <div class="cell medium-4 small-6 pam card mtm">
        <?php if ( isset( $label_arr['nice'] ) ) : ?>
          <i class="<?php echo isset($label_arr['icon']) ? $label_arr['icon'] : '' ?> fa-lg fa-3x strong" aria-hidden="true"></i>
        <?php endif; ?>
      </div>
      <div class="cell medium-20 small-18 grid-x card pam">
        <div class="cell align-self-top">
          <div>
            <span class="date-published"><time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time></span>
          </div>
        </div>
        <div class="cell align-self-bottom">
          <header class="mts">
            <a class="dark-ben-franklin strong hover-fade" href="<?php echo the_permalink(); ?>">
              <?php echo get_the_title(); ?>
            </a>
          </header>
        </div>
      </div>
    </div>
  </article>

<?php else: ?>

  <?php $label_arr = phila_get_post_label($label); ?>

  <article id="post-<?php the_ID(); ?>" <?php post_class('full-height'); ?>>
    <a href="<?php echo the_permalink(); ?>" class="card card--<?php echo $label_arr['label'] ?> flex-container flex-dir-row full-height">
      <div class="grid-x flex-dir-column">
        <div class="flex-child-shrink">
          <?php if ( has_post_thumbnail() ) : ?>
              <?php echo phila_get_thumbnails(); ?>
          <?php endif; ?>
        </div>
        <div class="card--content pam flex-child-auto">
          <div class="cell align-self-top post-label post-label--<?php echo $label_arr['label']?>">
            <i class="<?php echo $label_arr['icon'] ?> fa-lg" aria-hidden="true"></i> <span><?php echo $label_arr['nice']; ?></span>
            <header class="cell mvm">
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
<?php endif; ?>
