<?php
/**
 * The template used for displaying "Category Listing" content in page-category-listing.php
 *
 * The default taxonomy slug is “attachment_tag”. You can select the taxonomy you want by adding
 * a query parameter to the URL, e.g., "?taxonomy=attachment_category".
 *
 * For this page to work well, you should install and activate the Collapse-O-Matic plugin.
 *
 * @package Media Library Assistant
 * @subpackage MLA_Child_Theme
 * @version 1.00
 * @since MLA 1.80
 */

/**
 * Harmless declaration to suppress phpDocumentor "No page-level DocBlock" error
 *
 * @global $post
 */
global $post;
?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<?php if ( ! is_page_template( 'page-templates/front-page.php' ) ) : ?>
			<?php the_post_thumbnail(); ?>
			<?php endif; ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header>

		<div class="entry-content">
			<?php the_content(); ?>
			<?php
			if ( ! empty( $_REQUEST['taxonomy'] ) ) {
				$attr = array( 'taxonomy' => $_REQUEST['taxonomy'] );
			} else {
				$attr = array( 'taxonomy' => 'attachment_tag' );
			}

			echo mla_taxonomy_terms_list( $attr ); 
			?>
			<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->
		<footer class="entry-meta">
			<?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-meta -->
	</article><!-- #post -->
