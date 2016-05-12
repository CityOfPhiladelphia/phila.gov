<?php
/**
 * The content of a single post
 * @package phila-gov
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="row">
    <header class="entry-header small-24 columns mbs">
      <?php the_title( '<h1 class="entry-title contrast">', '</h1>' ); ?>
      <?php $posted_on_values = phila_get_posted_on(); ?>
      <span class="small-text"><?php echo $posted_on_values['time_string'];?>
        <?php if( !get_the_category() == ''): ?>
          <?php echo ' by ';  ?>
          <?php phila_echo_current_department_name( false ); ?>
        <?php endif; ?>
      </span>
    </header><!-- .entry-header -->
  </div>
  <div class="row">
    <div data-swiftype-index='true' class="entry-content medium-24 columns">
      <?php
      if ( has_post_thumbnail() ) : ?>
            <?php
              $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
              the_post_thumbnail( 'news-thumb' , array( 'class' => 'float-left hide-for-small-only' ) );
      endif;
      $news_desc = rwmb_meta( 'phila_news_desc', $args = array('type' => 'textarea'));
      if ($post->post_content != ''):
        the_content();
      else :
        if ($news_desc) :
          echo '<p class="description">' . $news_desc . '</p>';
        endif;
      endif;
      ?>
    </div><!-- .entry-content -->
  </div><!-- .row -->
</article><!-- #post-## -->
