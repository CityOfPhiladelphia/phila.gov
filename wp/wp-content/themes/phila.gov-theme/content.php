<?php
/**
 * @package phila-gov
 */
?>
<?php get_role( 'administrator' ); ?>
<article id="post-<?php the_ID(); ?>">
  <div class="row">
    <header class="entry-header small-24 columns">
      <?php the_title( sprintf( '<h1 class="entry-title contrast"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

      <?php if ( 'post' == get_post_type() ) : ?>
      <div class="entry-meta">
        <?php phila_gov_posted_on(); ?>
      </div><!-- .entry-meta -->
      <?php endif; ?>

    </header><!-- .entry-header -->
  </div>
    <div class="row">
        <div data-swiftype-index='true' class="entry-content small-24 columns">
            <?php
                /* translators: %s: Name of current post */
                the_content( sprintf(
                    __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'phila-gov' ),
                    the_title( '<span class="screen-reader-text">"', '"</span>', false )
                ) );
            ?>
        </div><!-- .entry-content -->
    </div>
  <footer class="entry-footer">
    <?php phila_gov_entry_footer(); ?>
  </footer><!-- .entry-footer -->
</article><!-- #post-## -->
