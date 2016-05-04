<?php
/**
 * The content of a single phila_post
 * @package phila-gov
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="row">
    <header class="entry-header small-24 columns">
      <?php the_title( '<h1 class="entry-title contrast">', '</h1>' ); ?>
    </header><!-- .entry-header -->
  </div>
  <div class="row mvm">
    <div data-swiftype-index='true' class="entry-content medium-18 medium-push-6 columns">

    <?php
      $post_desc = rwmb_meta( 'phila_post_desc', $args = array( 'type' => 'textarea' ) );

      if ($post->post_content != ''):
        the_content();
      else :
        echo '<p class="description">' . $post_desc . '</p>';
      endif;
      ?>
    </div><!-- .entry-content -->
    <aside id="secondary" class="small-24 medium-6 medium-pull-18 columns prm">
      <?php $posted_on_values = phila_get_posted_on(); ?>
        <div class="posted-on row column pvs">
        <?php if ( has_post_thumbnail() ): ?>
              <div class="columns hide-for-small-only medium-24">
              <?php $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
              the_post_thumbnail( 'news-thumb' ); ?>
              </div>
          <?php endif ?>
          <div class="byline small-24 column pvm pvs-mu">
            <div class="details small-text center">
              <span>Posted by <a href="<?php echo $posted_on_values['authorURL']; ?>"><?php echo $posted_on_values['author']; ?></a></span><br>
              <?php if( !get_the_category() == ''): ?>
                <span><a href="<?php echo $posted_on_values['dept_cat_permalink']; ?>" id="content-modified-department" data-slug="<?php echo $posted_on_values['current_cat_slug']; ?>"><?php echo $posted_on_values['dept_title']; ?></a></span><br>
              <?php endif; ?>
              <span>
                <?php echo $posted_on_values['time_string']; ?>
              </span>
              <br>
                  <?php phila_gov_entry_footer();?>
            </div>
        </div>

    </aside>
  </div><!-- .row -->

</article><!-- #post-## -->
