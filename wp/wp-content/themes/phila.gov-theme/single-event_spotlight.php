<?php
/**
 * The template used for displaying event spotlight pages
 *
 * @package phila-gov
*/

get_header();

?>

<div id="post-<?php the_ID(); ?>" <?php post_class('event-spotlight clearfix'); ?>>
  <?php
    while ( have_posts() ) : the_post();
      include( locate_template( 'partials/event-spotlight/header.php' ) );

      get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
      <?php include( locate_template( 'partials/event-spotlight/official-info.php' ) );?>
      
      <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>

      <?php
      include( locate_template( 'partials/event-spotlight/rows.php' ) );

    endwhile;

  ?>
</div><!-- #post-## -->
<?php get_footer(); ?>
