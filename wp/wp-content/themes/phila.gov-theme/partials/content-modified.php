<?php
/**
 * The template part for displaying when content was last modified.
 *
 *
 * @package phila-gov
 */
     wp_reset_postdata();
?>
<?php if ( !is_404() && !is_home()) : ?>
  <div class="row pvm">
    <div class="small-24 columns center">
      <?php
      // NOTE: the id is important. Google Tag Manager uses it to attach the
      // last modified date to our web analytics.
      ?>
      <div class="small-text">This content was last updated on <time id="content-modified-datetime" datetime="<?php the_modified_time('c'); ?>"><?php the_modified_date(); ?></time><?php
      if ( !is_archive() && !is_tax() && !is_home() ) :
        $category = get_the_category();
        echo phila_get_current_department_name( $category, $by_line = true );
      endif; ?><?php echo '.'; ?>
      </div>
    </div>
  </div>
<?php endif; ?>
