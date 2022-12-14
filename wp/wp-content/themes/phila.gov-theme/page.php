<?php
/**
 * The template for displaying all pages.
 * In our theme, Pages are special.
 * We use pages to create "Content Collections."
 * Content collections are items that may stand alone, but also may have
 * children. If children exist, an internal page menu should be generated and
 * the parent item should redirect to it's first child.
 *
 * This is the template that displays all pages by default.
 *
 * @package phila-gov
 */

  get_header();
?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">

    <?php while ( have_posts() ) : the_post();
      $children = get_pages( 'child_of=' . $post->ID );
      $has_parent = get_post_ancestors( $post );

      if ( ( count( $children ) == 0 ) && ( !$has_parent ) )  {
        //single page, no children, full-width content
        get_template_part( 'templates/default', 'page' );
      }else {
        //render side menu
        get_template_part( 'templates/page', 'collection' );
      }

      endwhile; // end of the loop. ?>

  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
