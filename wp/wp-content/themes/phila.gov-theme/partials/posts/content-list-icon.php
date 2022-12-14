<?php
/**
 * The template used for displaying an icon, description and link in a list
 *
 * @package phila-gov
 */
?>

<?php if ($user_selected_template === 'custom_content' || $post_type_parent === 'guides'): ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class('mbm'); ?>>
    <div class="grid-x faux-card custom cell medium-24 <?php echo ($count == $total) ? 'card--last' : '' ?>">
      <div class="cell medium-2 small-6 pam card mtm">
        <?php if ( isset( $label_arr['nice'] ) ) : ?>
          <i class="<?php echo isset($label_arr['icon']) ? $label_arr['icon'] : '' ?> fa-lg fa-3x strong" aria-hidden="true"></i>
        <?php endif; ?>
      </div>
      <div class="cell medium-22 small-18 grid-x card pam">
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
<?php endif;?>