<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 *
 * @package phila-gov
 */
$children = get_pages("child_of=".$post->ID."&sort_column=menu_order");
$content = $post->post_content;

$has_parent = get_post_ancestors( $post );

if ( $children && empty( $content ) ) {
    $firstchild = $children[0];
    wp_redirect(get_permalink($firstchild->ID));
    exit;
}
get_header(); ?>

  <div id="primary" class="content-area row">
    <main id="main" class="site-main small-24 columns" role="main">

      <?php while ( have_posts() ) : the_post();

        $children = get_pages('child_of=' . $post->ID);
        $this_content = get_the_content();
        if ( ( count( $children ) == 0 ) && ( !$this_content == 0 ) && ( !$has_parent ) )  {

          //single page, no children
          get_template_part( 'partials/content', 'page' );

        }elseif( ( $post->id = $post->post_parent ) ) {

          //this is our normal content collection
          get_template_part( 'partials/content', 'page-collection' );
        }else {

          //still show the menu, even if this page has content
          get_template_part( 'partials/content', 'page-collection' );
        }

        endwhile; // end of the loop. ?>

    </main><!-- #main -->
  </div><!-- #primary -->

<?php get_footer(); ?>
