<?php
/**
 * The template for displaying the "Tosca30 Dropdown" page, which
 * must be defined as a static WordPress "Page" post type.
 *
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Media Library Assistant
 * @subpackage MLA_Child_Theme
 * @version 1.00
 * @since MLA 1.80
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'tosca30-dropdown' ); ?>
				<?php comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>