<?php
/**
 * Guides template
 *
 * @package phila-gov
*/

$user_selected_template = phila_get_selected_template();

get_header();

?>

<div id="post-<?php the_ID(); ?>" <?php post_class('guides clearfix'); ?>>
  <?php
    while ( have_posts() ) : the_post();
      include( locate_template( 'partials/guides/header.php' ) ); ?>
  <?php 
    switch($user_selected_template) { 
      case ('guide_landing_page'):
        include( locate_template( 'partials/guides/landing-page.php' ) ); 
        break;
      case('guide_sub_page'):
        include( locate_template( 'partials/guides/subpage.php' ) ); 
        break;
      default:
        include( locate_template( 'partials/guides/subpage.php' ) );
        break; 
    }
  ?>
  <?php endwhile; ?>
  </div><!-- #post-## -->
<?php get_footer(); ?>
