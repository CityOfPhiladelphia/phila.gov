<?php
/**
 * The template used for displaying Document Pages
 *
 * @package phila-gov
 */

get_header();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('document'); ?>>
<?php
  //loop for our regularly scheduled content
  while ( have_posts() ) : the_post();
    get_template_part( 'templates/documents' );
  endwhile;
?>
</article><!-- #post-## -->

<?php get_footer(); ?>
