<?php
/**
 * The content of a single phila_post
 * @package phila-gov
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="row">
    <header class="entry-header small-24 columns">
      <?php the_title( '<h1 class="entry-title contrast ptm">', '</h1>' ); ?>
    </header><!-- .entry-header -->
  </div>
  <div class="row mvm">
    <div data-swiftype-index='true' class="entry-content medium-18 medium-push-6 columns">

    <?php
      $post_desc = rwmb_meta( 'phila_post_desc', $args = array( 'type' => 'textrea' ) );

      if ($post->post_content != ''):
        the_content();
      else :
        if ($news_desc) :
          echo '<p class="description">' . $news_desc . '</p>';
        else :
          echo '<p class="description">' . $post_desc . '</p>';
        endif;
      endif;
      ?>
    </div><!-- .entry-content -->
    <aside id="secondary" class="small-24 medium-6 medium-pull-18 columns prm" role="complementary">
      <?php
        phila_get_posted_on();
        phila_gov_entry_footer();

      ?>
    </aside>
  </div><!-- .row -->

</article><!-- #post-## -->
