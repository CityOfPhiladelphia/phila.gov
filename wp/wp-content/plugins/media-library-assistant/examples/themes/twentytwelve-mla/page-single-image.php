<?php
/**
 * The template used for displaying "attachment page" content
 *
 * You must define a WordPress static page "Single Image", which can be
 * empty but must exist to trigger this template.
 *
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * You must select the attachment you want by adding a query parameter
 * to the URL, e.g., "?post_id=5" where 5 is the ID of the attachment.
 *
 * The global $post variable contains the post object of the calling page.
 * * The template for displaying the "Tag Gallery" page, which
 * must be defined as a static WordPress "Page" post type.
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

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'single-image' ); ?>
				<?php //comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>