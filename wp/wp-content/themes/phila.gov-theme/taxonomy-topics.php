<?php
/**
 * Template Name: Browse
 *
 * @package phila-gov
 */

get_header(); ?>

<div id="primary" class="content-area browse">
  <main id="main" class="site-main" role="main">
    <div class="row">
      <?php
        $term = get_term_by('slug', get_query_var('term'), 'topics');
        if ($term) {
          if( $term->parent == 0 ) {
                get_template_part('templates/topics', 'parent');
              }else{
                get_template_part('templates/topics', 'child');
              }
        }else { ?>
          <nav class="topics-nav small-24 large-8 columns">
            <?php get_parent_topics(); ?>
          </nav>
      <?php  }?>
      </div>
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
