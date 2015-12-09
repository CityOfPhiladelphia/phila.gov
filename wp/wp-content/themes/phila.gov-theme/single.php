<?php
/**
 * The template for displaying all single posts.
 *
 * @package phila-gov
 */

get_header(); ?>

	<div id="primary" class="content-area row">
		<main id="main" class="site-main small-24 columns" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'partials/content', 'single' ); ?>

			<?php //phila_gov_post_nav(); ?>

		<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->
	

<?php get_footer(); ?>
