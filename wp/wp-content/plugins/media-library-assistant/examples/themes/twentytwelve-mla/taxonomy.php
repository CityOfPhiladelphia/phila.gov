<?php
/**
 * The template for displaying Custom Taxonomy Archive pages
 *
 * Twenty Twelve already has tag.php for Tag archives and category.php for Category archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Media Library Assistant
 * @subpackage MLA_Child_Theme
 * @version 1.00
 * @since MLA 1.80
 */

get_header(); ?>
<?php
global $wp_query;

$is_media_archive = in_array( $wp_query->query_vars['taxonomy'], array( 'attachment_category', 'attachment_tag' ) );
if ( $is_media_archive ) {
	if ( isset( $_REQUEST['use_mla_gallery'] ) ) {
		$use_mla_gallery = true;
	} else {
		$use_mla_gallery = false;
		$args = array_merge( $wp_query->query_vars, array( 'post_type' => 'attachment', 'post_status' => 'inherit' ) );
		query_posts( $args );
	}
}
?>

	<section id="primary" class="site-content">
		<div id="content" role="main">

		<?php if ( $use_mla_gallery ) : ?>
			<?php get_template_part( 'content', 'mla-gallery' ); ?>
		<?php elseif ( have_posts() ) : ?>
			<header class="archive-header">
				<h1 class="archive-title"><?php
					if ( is_day() ) :
						printf( __( 'Daily Archives: %s', 'twentytwelve' ), '<span>' . get_the_date() . '</span>' );
					elseif ( is_month() ) :
						printf( __( 'Monthly Archives: %s', 'twentytwelve' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'twentytwelve' ) ) . '</span>' );
					elseif ( is_year() ) :
						printf( __( 'Yearly Archives: %s', 'twentytwelve' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'twentytwelve' ) ) . '</span>' );
					elseif ( $is_media_archive ) :
						_e( 'Media Archives', 'mla-child-theme' );
					else :
						_e( 'Archives', 'twentytwelve' );
					endif;
				?></h1>
			</header><!-- .archive-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/* Include the post format-specific template for the content. If you want to
				 * this in a child theme then include a file called called content-___.php
				 * (where ___ is the post format) and that will be used instead.
				 */
				if ( $is_media_archive ) {
					get_template_part( 'content', 'media' );
				} else {
					get_template_part( 'content', get_post_format() );
				}

			endwhile;

			twentytwelve_content_nav( 'nav-below' );
			?>

		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>

		</div><!-- #content -->
	</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>