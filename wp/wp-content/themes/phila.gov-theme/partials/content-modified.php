<?php
/**
 * The template part for displaying when content was last modified.
 *
 *
 * @package phila-gov
 */
    wp_reset_postdata();
?>
<?php 
  $template = phila_get_selected_template($post->ID);
  if ( !is_404() && 
    !is_home() && 
    !is_search() &&
    !is_archive() && 
    !is_page_template() &&
    $template != 'prog_landing_page' &&
    $template != 'homepage_v2' &&
    $template !=  'off_site_department' &&
    $template != 'topic_page' &&
    $template != 'prog_association') : ?>
  <div class="row pvm">
    <div class="small-24 columns center">
      <?php
      // NOTE: the id is important. Google Tag Manager uses it to attach the
      // last modified date to our web analytics.
      ?>
      <div class="small-text">This content was last updated on <time id="content-modified-datetime" datetime="<?php the_modified_time('c'); ?>"><?php the_modified_date(); ?></time>, <?php
        $category = get_the_category();
        echo phila_get_current_department_name( $category, $by_line = true );?><?php echo '.'; ?>
      </div>
    </div>
  </div>
<?php endif; ?>
