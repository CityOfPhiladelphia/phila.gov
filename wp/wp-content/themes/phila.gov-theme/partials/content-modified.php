<?php
/**
 * The template part for displaying when content was last modified.
 *
 *
 * @package phila-gov
 */
     wp_reset_postdata();
?>
<?php if ( !is_404() ) : ?>
  <div class="row pvm">
    <div class="small-24 columns center">
      <?php
      // NOTE: the id is important. Google Tag Manager uses it to attach the
      // last modified date to our web analytics.
      ?>
      <div class="small-text">This content was last updated on <time id="content-modified-datetime" datetime="<?php the_modified_time('c'); ?>"><?php the_modified_date(); ?></time><?php
        $current_post_type = get_post_type(get_the_ID());

        if ( $current_post_type != 'notices' && !is_tax() && !is_archive() && !is_home() ):
          _e(' by ', 'phila-gov');
          phila_echo_current_department_name();
        endif;?><?php echo '.'; ?>
      </div>
    </div>
  </div>
<?php endif; ?>
