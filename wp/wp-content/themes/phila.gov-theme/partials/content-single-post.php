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
    <?php the_content(); ?>
    </div>
    <aside id="secondary" class="small-24 medium-6 medium-pull-18 columns">
      <?php $posted_on_values = phila_get_posted_on(); ?>
      <div class="posted-on row">
      <?php if ( has_post_thumbnail() ): ?>
        <div class="columns hide-for-small-only medium-24 ptxs">
          <div class="phila-thumb float-left">
            <?php echo phila_get_thumbnails(); ?>
          </div>
        </div>
        <?php endif; ?>
        <div class="byline small-24 column pvm pvs-mu">
          <div class="details small-text center">
            <span>Posted by <a href="<?php echo $posted_on_values['authorURL']; ?>"><?php echo $posted_on_values['author']; ?></a></span><br>
            <?php $category = get_the_category(); ?>
              <?php echo phila_get_current_department_name( $category, false, true ); ?>
            <br>
            <span>
              <?php echo $posted_on_values['time_string']; ?>
            </span>
            <br>
            <?php phila_gov_entry_footer();?>
          </div>
        </div>
      </div>
    </aside>
  </div><!-- .row -->
</article><!-- #post-## -->
