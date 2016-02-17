<?php
/**
 * The template used for displaying Publication Pages
 *
 * @package phila-gov
 */

get_header();
?>
<?php /*

Allow screen readers / text browsers to skip the navigation menu and
get right to the good stuff. */ ?>

<div class="skip-link screen-reader-text">
	<a href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentyten' ); ?>">
	<?php _e( 'Skip to content', 'twentyten' ); ?></a>
</div>

<article id="post-<?php the_ID(); ?>" <?php post_class('row document'); ?>>
<?php
	//loop for our regularly scheduled content
  while ( have_posts() ) : the_post();
		get_template_part( 'templates/documents' );
  endwhile;
?>
</article><!-- #post-## -->

<?php get_footer(); ?>
